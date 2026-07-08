<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Services\Gmail\GmailApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class LeadAutomationFlowController extends Controller
{
    public function processNextUnsentLead(Request $request, LeadGmailEmailController $leadGmailEmailController, GmailApiService $gmailApiService): JsonResponse
    {
        if (!Schema::hasColumn('leads', 'is_sent') || !Schema::hasColumn('leads', 'sending_date')) {
            return response()->json([
                'success' => false,
                'message' => 'Required columns is_sent and sending_date are missing on leads table.',
            ], 422);
        }

        $lead = Lead::query()
            ->where('is_sent', false)
            ->orderBy('id')
            ->first();

        if (!$lead) {
            return response()->json([
                'success' => true,
                'message' => 'No unsent leads found.',
            ]);
        }

        $internalRequest = Request::create('/leads/' . $lead->id . '/send-gmail-email', 'POST');
        $internalRequest->headers->set('Accept', 'application/json');

        $response = $leadGmailEmailController->__invoke($internalRequest, $lead, $gmailApiService);

        if (!$response instanceof JsonResponse) {
            return response()->json([
                'success' => false,
                'message' => 'Unexpected response from existing email send function.',
                'lead_id' => $lead->id,
            ], 500);
        }

        $payload = (array) $response->getData(true);
        $statusCode = $response->getStatusCode();
        $emailSent = $statusCode >= 200
            && $statusCode < 300
            && (($payload['success'] ?? false) === true);

        if ($emailSent) {
            $lead->forceFill([
                'is_sent' => true,
                'sending_date' => now(),
            ])->save();
        }

        return response()->json([
            'success' => $emailSent,
            'message' => $emailSent
                ? 'Processed next unsent lead and updated is_sent/sending_date.'
                : 'Email send failed for next unsent lead.',
            'lead_id' => $lead->id,
            'email_result' => $payload,
        ], $emailSent ? 200 : 500);
    }

    public function processNextPendingCompanyResearch(Request $request, WebsiteResearchController $websiteResearchController): JsonResponse
    {
        if (!Schema::hasColumn('leads', 'company_research_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Required column company_research_id is missing on leads table.',
            ], 422);
        }

        $lead = Lead::query()
            ->whereNull('company_research_id')
            ->orderBy('id')
            ->first();

        if (!$lead) {
            return response()->json([
                'success' => true,
                'message' => 'No leads found with null company_research_id.',
            ]);
        }

        $internalRequest = Request::create('/external/company-research/create', 'POST', [
            'lead_id' => $lead->id,
        ]);
        $internalRequest->headers->set('Accept', 'application/json');

        $response = $websiteResearchController->createCompanyResearch($internalRequest);

        if (!$response instanceof JsonResponse) {
            return response()->json([
                'success' => false,
                'message' => 'Unexpected response from existing company research function.',
                'lead_id' => $lead->id,
            ], 500);
        }

        $payload = (array) $response->getData(true);
        $statusCode = $response->getStatusCode();
        $researchProcessed = $statusCode >= 200
            && $statusCode < 300
            && (($payload['success'] ?? false) === true);

        return response()->json([
            'success' => $researchProcessed,
            'message' => $researchProcessed
                ? 'Processed next lead for company research.'
                : 'Company research failed for next pending lead.',
            'lead_id' => $lead->id,
            'research_result' => $payload,
        ], $researchProcessed ? 200 : 500);
    }
}