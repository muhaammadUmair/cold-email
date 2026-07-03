<?php

namespace App\Jobs;

use App\Models\CompanyResearch;
use App\Services\AiEmailGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Schema;

class GenerateCompanyResearchEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 2;

    public int $timeout = 90;

    public function __construct(public int $companyResearchId)
    {
    }

    public function handle(AiEmailGeneratorService $generator): void
    {
        $companyResearch = CompanyResearch::with('lead')->find($this->companyResearchId);

        if (!$companyResearch) {
            return;
        }

        if (!empty($companyResearch->generated_email)) {
            return;
        }

        $emailBody = $generator->generate($companyResearch);

        if (empty($emailBody)) {
            return;
        }

        $updates = ['generated_email' => $emailBody];

        if (Schema::hasColumn('company_research', 'email_generated_at')) {
            $updates['email_generated_at'] = now();
        }

        $companyResearch->update($updates);
    }
}