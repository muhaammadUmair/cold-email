<?php

namespace App\Services\Gmail;

use App\Models\Lead;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class GmailApiService
{
    private const TOKEN_CACHE_KEY = 'gmail_api_access_token';

    public function sendLeadEmail(Lead $lead, string $subject, string $body): array
    {
        $recipientEmail = trim((string) $lead->email);

        if ($recipientEmail === '') {
            throw new RuntimeException('The lead does not have a recipient email address.');
        }

        $senderEmail = $this->resolveSenderEmail();
        $senderName = $this->resolveSenderName();
        $rawMessage = $this->buildRawMessage($senderName, $senderEmail, $recipientEmail, $subject, $body);

        $response = Http::timeout($this->timeout())
            ->withToken($this->getAccessToken())
            ->post('https://gmail.googleapis.com/gmail/v1/users/me/messages/send', [
                'raw' => $rawMessage,
            ]);

        if (!$response->successful()) {
            throw new RuntimeException($this->buildApiFailureMessage($response, 'send email'));
        }

        return [
            'gmail_message_id' => data_get($response->json(), 'id'),
            'thread_id' => data_get($response->json(), 'threadId'),
            'label_ids' => data_get($response->json(), 'labelIds', []),
            'sender_email' => $senderEmail,
            'recipient_email' => $recipientEmail,
        ];
    }

    public function buildSubject(Lead $lead, string $body): string
    {
        $normalizedBody = trim((string) preg_replace('/\s+/', ' ', strip_tags($body)));
        $subject = $this->subjectFromBody($normalizedBody);

        if ($subject !== '') {
            return $subject;
        }

        $company = trim((string) ($lead->company_name_for_emails ?: $lead->company ?: ''));

        if ($company !== '') {
            return 'Quick question about ' . $company;
        }

        return (string) config('services.gmail.subject', 'Quick question');
    }

    private function subjectFromBody(string $body): string
    {
        if ($body === '') {
            return '';
        }

        $withoutGreeting = preg_replace('/^(hi|hello|hey)\b[^\n.!?]*[\n.!?]\s*/i', '', $body) ?? $body;
        $withoutSignoff = preg_replace('/\b(best|regards|thanks|thank you|sincerely)\b[\s\S]*$/i', '', $withoutGreeting) ?? $withoutGreeting;

        $candidate = trim((string) preg_split('/(?<=[.!?])\s+/', $withoutSignoff, 2)[0]);

        if ($candidate === '') {
            return '';
        }

        $candidate = preg_replace('/[^\pL\pN\s\-,:]/u', '', $candidate) ?? $candidate;
        $candidate = trim((string) preg_replace('/\s+/', ' ', $candidate));

        if ($candidate === '') {
            return '';
        }

        $words = preg_split('/\s+/', $candidate) ?: [];

        if (count($words) < 3) {
            return '';
        }

        if (count($words) > 10) {
            $candidate = implode(' ', array_slice($words, 0, 10));
        }

        return Str::limit($candidate, 72, '');
    }

    private function resolveSenderName(): string
    {
        $senderName = trim((string) config('services.gmail.sender_name', ''));

        if ($senderName === '') {
            return (string) config('app.name', 'Laravel');
        }

        return $senderName;
    }

    private function resolveSenderEmail(): string
    {
        $configuredSenderEmail = trim((string) config('services.gmail.sender_email', ''));

        if ($configuredSenderEmail !== '') {
            return $configuredSenderEmail;
        }

        $accessToken = $this->getAccessToken();

        $response = Http::timeout($this->timeout())
            ->withToken($accessToken)
            ->get('https://gmail.googleapis.com/gmail/v1/users/me/profile');

        if (!$response->successful()) {
            throw new RuntimeException($this->buildApiFailureMessage($response, 'resolve the authenticated Gmail account'));
        }

        $emailAddress = trim((string) data_get($response->json(), 'emailAddress', ''));

        if ($emailAddress === '') {
            throw new RuntimeException('Unable to determine the authenticated Gmail sender address.');
        }

        return $emailAddress;
    }

    private function getAccessToken(): string
    {
        return Cache::remember($this->tokenCacheKey(), $this->tokenCacheSeconds(), function () {
            return $this->refreshAccessToken();
        });
    }

    private function refreshAccessToken(): string
    {
        $clientId = trim((string) config('services.gmail.client_id', ''));
        $clientSecret = trim((string) config('services.gmail.client_secret', ''));
        $refreshToken = trim((string) config('services.gmail.refresh_token', ''));

        if ($clientId === '' || $clientSecret === '' || $refreshToken === '') {
            throw new RuntimeException('Gmail OAuth credentials are not configured.');
        }

        $response = Http::asForm()
            ->timeout($this->timeout())
            ->post('https://oauth2.googleapis.com/token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
            ]);

        if (!$response->successful()) {
            throw new RuntimeException($this->buildApiFailureMessage($response, 'refresh the Gmail access token'));
        }

        $accessToken = trim((string) data_get($response->json(), 'access_token', ''));

        if ($accessToken === '') {
            throw new RuntimeException('Gmail access token response did not contain an access token.');
        }

        return $accessToken;
    }

    private function buildRawMessage(string $senderName, string $senderEmail, string $recipientEmail, string $subject, string $body): string
    {
        $subject = $this->sanitizeHeaderValue($subject);
        $senderName = $this->sanitizeHeaderValue($senderName);
        $senderEmail = $this->sanitizeHeaderValue($senderEmail);
        $recipientEmail = $this->sanitizeHeaderValue($recipientEmail);

        $headers = [
            'From: ' . $senderName . ' <' . $senderEmail . '>',
            'To: ' . $recipientEmail,
            'Subject: ' . $subject,
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
        ];

        $message = implode("\r\n", $headers) . "\r\n\r\n" . $body;

        return $this->base64UrlEncode($message);
    }

    private function sanitizeHeaderValue(string $value): string
    {
        return trim(str_replace(["\r", "\n"], '', $value));
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function timeout(): int
    {
        return max(1, (int) config('services.gmail.timeout', 30));
    }

    private function tokenCacheKey(): string
    {
        return self::TOKEN_CACHE_KEY;
    }

    private function tokenCacheSeconds(): int
    {
        return max(60, $this->timeout() * 60);
    }

    private function buildApiFailureMessage(Response $response, string $action): string
    {
        $body = trim((string) $response->body());

        if ($body === '') {
            $body = 'No response body returned.';
        }

        return 'Unable to ' . $action . '. Gmail API returned HTTP ' . $response->status() . ': ' . Str::limit($body, 1000, '');
    }
}