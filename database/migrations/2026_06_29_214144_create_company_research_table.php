<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_research', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->longText('website_summary')->nullable();
            $table->string('salesforce_opportunity')->nullable();
            $table->longText('claude_prompt')->nullable();
            $table->longText('generated_email')->nullable();
            $table->timestamps();
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('company_research');
    }
};
