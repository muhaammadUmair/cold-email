<?php

namespace App\Services;

use App\Http\Controllers\WebsiteResearchController;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class LeadSequentialCompanyResearchProcessorService
{
    public function __construct(private WebsiteResearchController $websiteResearchController)
    {
    }

    public function processNextLead(): array
    {
        if (!Schema::hasColumn('leads', 'company_research_id')) {
            return [
                'processed' => 0,
                'success' => false,
                'message' => 'The leads table does not have company_research_id column.',
            ];
        }

        $lead = Lead::query()
            ->whereNull('company_research_id')
            ->orderBy('id')
            ->first();

        if (!$lead) {
            return [
                'processed' => 0,
                'success' => true,
                'message' => 'No leads pending company research.',
            ];
        }

        $request = Request::create('/external/company-research/create', 'POST', [
            'lead_id' => $lead->id,
        ]);
        $request->headers->set('Accept', 'application/json');

        $response = $this->websiteResearchController->createCompanyResearch($request);

        if (!$response instanceof JsonResponse) {
            Log::warning('Company research processor expected JsonResponse.', [
                'lead_id' => $lead->id,
                'response_type' => $response::class,
            ]);

            return [
                'processed' => 1,
                'success' => false,
                'lead_id' => $lead->id,
                'message' => 'Unexpected response type from company research function.',
            ];
        }

        $payload = (array) $response->getData(true);
        $statusCode = $response->getStatusCode();
        $success = $statusCode >= 200
            && $statusCode < 300
            && (($payload['success'] ?? false) === true);

        if (!$success) {
            Log::warning('Company research processor failed for lead.', [
                'lead_id' => $lead->id,
                'status_code' => $statusCode,
                'response' => $payload,
            ]);
        }

        return [
            'processed' => 1,
            'success' => $success,
            'lead_id' => $lead->id,
            'message' => (string) ($payload['message'] ?? 'Company research processing completed.'),
        ];
    }
}