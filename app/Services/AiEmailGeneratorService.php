<?php

namespace App\Services;

use App\Models\CompanyResearch;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiEmailGeneratorService
{
    private const DEFAULT_SENDER_NAME = 'Code Aligned';

    public function generate(CompanyResearch $companyResearch): ?string
    {
        $provider = (string) config('services.ai.provider', 'gemini');
        $apiKey = $provider === 'gemini'
            ? (string) config('services.gemini.api_key')
            : (string) config('services.openai.api_key');

        if ($apiKey === '') {
            return null;
        }

        $context = $this->buildCompactContext($companyResearch);

        if ($provider === 'gemini') {
            return $this->generateWithGemini($context, $apiKey);
        }

        return $this->generateWithOpenAi($context, $apiKey);
    }

    private function generateWithOpenAi(string $context, string $apiKey): ?string
    {
        $senderName = self::DEFAULT_SENDER_NAME;

        $response = Http::timeout((int) config('services.openai.timeout', 30))
            ->withToken($apiKey)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('services.openai.model', 'gpt-4o-mini'),
                'temperature' => (float) config('services.openai.temperature', 0.4),
                'max_tokens' => (int) config('services.openai.max_output_tokens', 420),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You write concise, personalized B2B cold emails. Keep the email human, specific, and under 160 words. Return only the email body, no markdown, no subject line. Never include placeholders like [Your Name], [Company Name], or bracket tokens. End the email with the sender name "' . $senderName . '".',
                    ],
                    [
                        'role' => 'user',
                        'content' => $context,
                    ],
                ],
            ]);

        if (!$response->successful()) {
            return null;
        }

        $content = trim((string) data_get($response->json(), 'choices.0.message.content', ''));

        return $content !== '' ? $this->finalizeGeneratedEmail($content, $senderName) : null;
    }

    private function generateWithGemini(string $context, string $apiKey): ?string
    {
        $senderName = self::DEFAULT_SENDER_NAME;
        $model = (string) config('services.gemini.model', 'gemini-1.5-flash');
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent';

        $response = Http::timeout((int) config('services.gemini.timeout', 30))
            ->post($url . '?key=' . urlencode($apiKey), [
                'generationConfig' => [
                    'temperature' => (float) config('services.gemini.temperature', 0.4),
                    'maxOutputTokens' => (int) config('services.gemini.max_output_tokens', 420),
                ],
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => 'You write concise, personalized B2B cold emails. Keep the email human, specific, and under 160 words. Return only the email body, no markdown, no subject line. Never include placeholders like [Your Name], [Company Name], or bracket tokens. End the email with the sender name "' . $senderName . '".',
                            ],
                            [
                                'text' => $context,
                            ],
                        ],
                    ],
                ],
            ]);

        if (!$response->successful()) {
            return null;
        }

        $content = trim((string) data_get($response->json(), 'candidates.0.content.parts.0.text', ''));

        return $content !== '' ? $this->finalizeGeneratedEmail($content, $senderName) : null;
    }

    private function finalizeGeneratedEmail(string $content, string $senderName): string
    {
        $clean = trim($content);

        $clean = preg_replace('/\[(your name|company name|your company)\]/i', $senderName, $clean) ?? $clean;
        $clean = preg_replace('/\[.*?\]/', '', $clean) ?? $clean;
        $clean = trim(preg_replace('/\n{3,}/', "\n\n", $clean) ?? $clean);

        if (!preg_match('/\b' . preg_quote($senderName, '/') . '\b/i', $clean)) {
            $clean .= "\n\nBest,\n" . $senderName;
        }

        return $clean;
    }

    private function buildCompactContext(CompanyResearch $companyResearch): string
    {
        $lead = $companyResearch->lead;

        $payload = [
            'lead_name' => trim((string) ($lead?->first_name . ' ' . $lead?->last_name)),
            'lead_title' => (string) ($lead->title ?? ''),
            'company_name' => (string) ($lead->company ?? ''),
            'website' => (string) ($companyResearch->home_url ?? $lead->website ?? ''),
            'salesforce_opportunity' => (string) ($companyResearch->salesforce_opportunity ?? ''),
            'home_summary' => $this->compactText((string) ($companyResearch->home_content ?? ''), 850),
            'about_summary' => $this->compactText((string) ($companyResearch->about_content ?? ''), 850),
            'career_summary' => $this->compactText((string) ($companyResearch->career_content ?? ''), 550),
            'news_summary' => $this->compactText((string) ($companyResearch->news_content ?? ''), 550),
            'website_summary' => $this->compactText((string) ($companyResearch->website_summary ?? ''), 650),
            'goal' => 'Write one personalized cold outreach email with a clear value hook and soft CTA for a short call.',
        ];

        return json_encode($payload, JSON_UNESCAPED_SLASHES) ?: '';
    }

    private function compactText(string $text, int $maxChars): string
    {
        $clean = trim(preg_replace('/\s+/', ' ', strip_tags($text)) ?? '');

        if ($clean === '') {
            return '';
        }

        return Str::limit($clean, $maxChars, '');
    }
}