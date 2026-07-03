<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateCompanyResearchEmailJob;
use App\Models\CompanyResearch;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class WebsiteResearchController extends Controller
{
    public function createCompanyResearch(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => ['nullable', 'integer', 'exists:leads,id'],
            'email' => ['nullable', 'email'],
            'website' => ['nullable', 'url'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'salesforce_opportunity' => ['nullable', 'string', 'max:255'],
            'claude_prompt' => ['nullable', 'string'],
            'generated_email' => ['nullable', 'string'],
        ]);

        if (empty($validated['lead_id']) && empty($validated['email'])) {
            return response()->json([
                'success' => false,
                'message' => 'Provide lead_id or email.'
            ], 422);
        }

        $lead = $this->resolveLead($validated);

        $website = $validated['website']
            ?? $lead->website
            ?? $this->websiteFromEmail($lead->email);

        if (empty($website)) {
            return response()->json([
                'success' => false,
                'message' => 'Website is required. Pass website in request or ensure lead has website/email domain.'
            ], 422);
        }

        $website = $this->normalizeUrl($website);

        try {
            $homeResponse = $this->fetchPage($website);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to load website home page.',
                'error' => $e->getMessage(),
            ], 422);
        }

        $homeContent = $this->extractReadableText($homeResponse->body());
        $discovered = $this->discoverKeywordPages($website, $homeResponse->body());

        $aboutUrl = $this->firstUrlByBucket($discovered, ['about']);
        $careerUrl = $this->firstUrlByBucket($discovered, ['career']);
        $newsUrl = $this->firstUrlByBucket($discovered, ['news']);

        $aboutContent = $this->contentFromUrl($aboutUrl);
        $careerContent = $this->contentFromUrl($careerUrl);
        $newsContent = $this->contentFromUrl($newsUrl);

        $payload = [
            'lead_id' => $lead->id,
            'website_summary' => $homeContent,
            'salesforce_opportunity' => $validated['salesforce_opportunity'] ?? null,
            'claude_prompt' => $validated['claude_prompt'] ?? $this->defaultClaudePrompt($lead, $website, $homeContent),
            'generated_email' => $validated['generated_email'] ?? null,
            'home_url' => $website,
            'home_content' => $homeContent,
            'about_url' => $aboutUrl,
            'about_content' => $aboutContent,
            'career_url' => $careerUrl,
            'career_content' => $careerContent,
            'news_url' => $newsUrl,
            'news_content' => $newsContent,
        ];

        $safePayload = $this->filterExistingCompanyResearchColumns($payload);
        $companyResearch = CompanyResearch::create($safePayload);
        GenerateCompanyResearchEmailJob::dispatch($companyResearch->id);

        return response()->json([
            'success' => true,
            'message' => 'Company research created successfully.',
            'data' => [
                'id' => $companyResearch->id,
                'lead_id' => $companyResearch->lead_id,
                'home_url' => $safePayload['home_url'] ?? null,
                'about_url' => $safePayload['about_url'] ?? null,
                'career_url' => $safePayload['career_url'] ?? null,
                'news_url' => $safePayload['news_url'] ?? null,
            ],
        ]);
    }

    public function discoverPages(Request $request)
    {
        $validated = $request->validate([
            'website' => ['required', 'url'],
        ]);

        $website = $this->normalizeUrl($validated['website']);

        $response = $this->fetchPage($website);

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to load website.'
            ]);
        }

        $pages = array_values($this->discoverKeywordPages($website, $response->body()));

        usort($pages, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return response()->json(array_values($pages));
    }

    private function resolveLead(array $validated): Lead
    {
        if (!empty($validated['lead_id'])) {
            return Lead::findOrFail($validated['lead_id']);
        }

        $email = strtolower(trim($validated['email']));
        $lead = Lead::where('email', $email)->first();

        if ($lead) {
            return $lead;
        }

        $local = strstr($email, '@', true) ?: 'lead';
        $parts = preg_split('/[._-]+/', $local) ?: [];
        $firstName = $validated['first_name'] ?? ucfirst($parts[0] ?? 'Lead');
        $lastName = $validated['last_name'] ?? ucfirst($parts[1] ?? 'Contact');

        return Lead::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'website' => $validated['website'] ?? $this->websiteFromEmail($email),
        ]);
    }

    private function websiteFromEmail(?string $email): ?string
    {
        if (empty($email) || !str_contains($email, '@')) {
            return null;
        }

        $domain = substr(strrchr($email, '@'), 1);
        if (empty($domain)) {
            return null;
        }

        return 'https://' . $domain;
    }

    private function normalizeUrl(string $url): string
    {
        $trimmed = trim($url);

        if (!preg_match('/^https?:\/\//i', $trimmed)) {
            $trimmed = 'https://' . ltrim($trimmed, '/');
        }

        return rtrim($trimmed, '/');
    }

    private function fetchPage(string $url)
    {
        return Http::timeout(20)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0'
            ])
            ->get($url);
    }

    private function discoverKeywordPages(string $website, string $html): array
    {
        $crawler = new Crawler($html);
        $baseHost = parse_url($website, PHP_URL_HOST);

        $keywords = [
            'about' => ['about', 'company', 'our-story', 'story', 'who-we-are'],
            'career' => ['careers', 'career', 'jobs', 'join-us', 'work-with-us'],
            'news' => ['news', 'blog', 'press', 'updates', 'insights'],
            'general' => ['services', 'solutions', 'products', 'customers', 'case-study'],
        ];

        $pages = [];

        $crawler->filter('a')->each(function (Crawler $node) use (&$pages, $website, $baseHost, $keywords) {
            $hrefAttr = $node->attr('href');

            if (!$hrefAttr) {
                return;
            }

            $href = trim($hrefAttr);

            if (
                str_starts_with($href, '#') ||
                str_starts_with($href, 'mailto:') ||
                str_starts_with($href, 'javascript:')
            ) {
                return;
            }

            if (!str_starts_with($href, 'http')) {
                $href = rtrim($website, '/') . '/' . ltrim($href, '/');
            }

            $host = parse_url($href, PHP_URL_HOST);
            if ($host !== $baseHost) {
                return;
            }

            $title = trim($node->text());
            $score = 0;
            $bucket = 'general';

            foreach ($keywords as $name => $list) {
                foreach ($list as $keyword) {
                    if (stripos($href, $keyword) !== false) {
                        $score += 10;
                        $bucket = $name;
                    }

                    if (stripos($title, $keyword) !== false) {
                        $score += 5;
                        $bucket = $name;
                    }
                }
            }

            if ($score > 0) {
                $pages[$href] = [
                    'url' => $href,
                    'title' => $title,
                    'score' => $score,
                    'bucket' => $bucket,
                ];
            }
        });

        return $pages;
    }

    private function firstUrlByBucket(array $pages, array $buckets): ?string
    {
        $filtered = array_filter($pages, function (array $page) use ($buckets) {
            return in_array($page['bucket'] ?? '', $buckets, true);
        });

        if (empty($filtered)) {
            return null;
        }

        usort($filtered, function ($a, $b) {
            return ($b['score'] ?? 0) <=> ($a['score'] ?? 0);
        });

        return $filtered[0]['url'] ?? null;
    }

    private function contentFromUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        try {
            $response = $this->fetchPage($url);
            if (!$response->successful()) {
                return null;
            }

            return $this->extractReadableText($response->body());
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function extractReadableText(string $html): string
    {
        $crawler = new Crawler($html);

        $crawler->filter('script, style, noscript, svg')->each(function (Crawler $node) {
            foreach ($node as $domNode) {
                if ($domNode->parentNode) {
                    $domNode->parentNode->removeChild($domNode);
                }
            }
        });

        $text = trim($crawler->filter('body')->count() ? $crawler->filter('body')->text(' ') : strip_tags($html));
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;

        return Str::limit($text, 60000, '');
    }

    private function defaultClaudePrompt(Lead $lead, string $website, string $homeContent): string
    {
        $summary = Str::limit($homeContent, 1200);

        return "Create a personalized cold email for {$lead->first_name} {$lead->last_name} using this company website {$website}. Website summary: {$summary}";
    }

    private function filterExistingCompanyResearchColumns(array $payload): array
    {
        $filtered = [];

        foreach ($payload as $column => $value) {
            if (Schema::hasColumn('company_research', $column)) {
                $filtered[$column] = $value;
            }
        }

        return $filtered;
    }
}