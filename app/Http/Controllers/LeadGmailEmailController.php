<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateCompanyResearchEmailJob;
use App\Models\EmailLog;
use App\Models\Lead;
use App\Services\Gmail\GmailApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

class LeadGmailEmailController extends Controller
{
    public function __invoke(Request $request, Lead $lead, GmailApiService $gmailApiService): JsonResponse|RedirectResponse
    {
        $lead->loadMissing('companyResearch');

        $companyResearch = $lead->companyResearch;

        if (!$companyResearch) {
            return $this->errorResponse($request, $lead, 'No company research was found for this lead. Generate company research first.', 422);
        }

        if (empty($companyResearch->generated_email)) {
            Bus::dispatchSync(new GenerateCompanyResearchEmailJob($companyResearch->id));
            $companyResearch->refresh();
        }

        $generatedEmail = trim((string) $companyResearch->generated_email);

        if ($generatedEmail === '') {
            return $this->errorResponse($request, $lead, 'No generated email body is available for this lead.', 422);
        }

        $emailLog = EmailLog::create([
            'lead_id' => $lead->id,
            'status' => 'pending',
        ]);

        try {
            $result = $gmailApiService->sendLeadEmail(
                $lead,
                $gmailApiService->buildSubject($lead, $generatedEmail),
                $generatedEmail
            );

            $lead->forceFill([
                'email_sent' => true,
                'email_sent_at' => now(),
                'status' => 'sent',
            ])->save();

            $emailLog->update([
                'status' => 'sent',
                'gmail_message_id' => $result['gmail_message_id'] ?? null,
                'sent_at' => now(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email sent successfully through Gmail API.',
                    'data' => [
                        'lead_id' => $lead->id,
                        'recipient_email' => $result['recipient_email'] ?? $lead->email,
                        'gmail_message_id' => $result['gmail_message_id'] ?? null,
                        'thread_id' => $result['thread_id'] ?? null,
                    ],
                ]);
            }

            return redirect()
                ->route('leads.show', $lead)
                ->with('status', 'Email sent successfully through Gmail API.');
        } catch (Throwable $throwable) {
            Log::error('Gmail API email send failed.', [
                'lead_id' => $lead->id,
                'recipient_email' => $lead->email,
                'company_research_id' => $companyResearch->id,
                'exception' => $throwable->getMessage(),
            ]);

            $lead->forceFill([
                'status' => 'failed',
            ])->save();

            $emailLog->update([
                'status' => 'failed',
            ]);

            return $this->errorResponse(
                $request,
                $lead,
                'Failed to send email through Gmail API.',
                500,
                $throwable->getMessage()
            );
        }
    }

    private function errorResponse(Request $request, Lead $lead, string $message, int $statusCode, ?string $error = null): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            $payload = [
                'success' => false,
                'message' => $message,
            ];

            if (!empty($error)) {
                $payload['error'] = $error;
            }

            return response()->json($payload, $statusCode);
        }

        $fullMessage = $message;

        if (!empty($error)) {
            $fullMessage .= ' ' . $error;
        }

        return redirect()
            ->route('leads.show', $lead)
            ->with('error', $fullMessage);
    }
}