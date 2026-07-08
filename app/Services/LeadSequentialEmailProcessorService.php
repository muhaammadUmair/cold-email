<?php

namespace App\Services;

use App\Http\Controllers\LeadGmailEmailController;
use App\Models\Lead;
use App\Services\Gmail\GmailApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeadSequentialEmailProcessorService
{
    public function __construct(
        private LeadGmailEmailController $leadGmailEmailController,
        private GmailApiService $gmailApiService,
    ) {
    }

    public function processPendingLeads(bool $onlyNextLead = false): array
    {
        $query = Lead::query()
            ->where('is_sent', false)
            ->orderBy('id');

        $processed = 0;
        $sent = 0;
        $failed = 0;

        if ($onlyNextLead) {
            $lead = $query->first();

            if (!$lead) {
                return [
                    'processed' => 0,
                    'sent' => 0,
                    'failed' => 0,
                    'message' => 'No unsent leads found.',
                ];
            }

            $processed = 1;

            if ($this->sendLeadUsingExistingFunction($lead)) {
                $this->markLeadAsSent($lead);
                $sent = 1;
            } else {
                $failed = 1;
            }

            return [
                'processed' => $processed,
                'sent' => $sent,
                'failed' => $failed,
                'message' => 'Processed the next eligible lead.',
            ];
        }

        foreach ($query->cursor() as $lead) {
            $processed++;

            if ($this->sendLeadUsingExistingFunction($lead)) {
                $this->markLeadAsSent($lead);
                $sent++;
                continue;
            }

            $failed++;
        }

        return [
            'processed' => $processed,
            'sent' => $sent,
            'failed' => $failed,
            'message' => 'Finished sequential processing for unsent leads.',
        ];
    }

    private function sendLeadUsingExistingFunction(Lead $lead): bool
    {
        $request = Request::create('/leads/' . $lead->id . '/send-gmail-email', 'POST');
        $request->headers->set('Accept', 'application/json');

        $response = $this->leadGmailEmailController->__invoke($request, $lead, $this->gmailApiService);

        if (!$response instanceof JsonResponse) {
            Log::warning('Sequential email processor expected JSON response from existing sender.', [
                'lead_id' => $lead->id,
                'response_type' => $response::class,
            ]);

            return false;
        }

        $statusCode = $response->getStatusCode();
        $payload = (array) $response->getData(true);
        $success = $statusCode >= 200
            && $statusCode < 300
            && (($payload['success'] ?? false) === true);

        if (!$success) {
            Log::warning('Sequential email processor failed for lead.', [
                'lead_id' => $lead->id,
                'status_code' => $statusCode,
                'response' => $payload,
            ]);
        }

        return $success;
    }

    private function markLeadAsSent(Lead $lead): void
    {
        $lead->forceFill([
            'is_sent' => true,
            'sending_date' => now(),
        ])->save();
    }
}