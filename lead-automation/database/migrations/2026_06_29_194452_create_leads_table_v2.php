<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('first_name');
            $table->string('last_name');
            $table->string('title')->nullable();
            $table->string('company')->nullable();
            $table->string('company_name_for_emails')->nullable();
            $table->string('email')->unique();
            $table->string('email_status')->nullable();
            $table->string('primary_email_source')->nullable();
            $table->string('email_verification_source')->nullable();
            $table->string('email_confidence')->nullable();
            $table->string('email_catch_all_status')->nullable();
            $table->timestamp('email_last_verified_at')->nullable();

            // Seniority & Department
            $table->string('seniority')->nullable();
            $table->string('departments')->nullable();
            $table->string('sub_departments')->nullable();

            // Contact Owner
            $table->string('contact_owner')->nullable();
            $table->string('account_owner')->nullable();

            // Phone Numbers
            $table->string('work_direct_phone')->nullable();
            $table->string('home_phone')->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('corporate_phone')->nullable();
            $table->string('other_phone')->nullable();
            $table->boolean('do_not_call')->default(false);

            // Stage & Lists
            $table->string('stage')->nullable();
            $table->string('lists')->nullable();
            $table->timestamp('last_contacted_at')->nullable();

            // Company Info
            $table->integer('employees')->nullable();
            $table->string('industry')->nullable();
            $table->text('keywords')->nullable();

            // URLs
            $table->string('linkedin_url')->nullable();
            $table->string('website')->nullable();
            $table->string('company_linkedin_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();

            // Location
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('company_address')->nullable();
            $table->string('company_city')->nullable();
            $table->string('company_state')->nullable();
            $table->string('company_country')->nullable();
            $table->string('company_phone')->nullable();

            // Technologies & Funding
            $table->text('technologies')->nullable();
            $table->string('annual_revenue')->nullable();
            $table->string('total_funding')->nullable();
            $table->string('latest_funding')->nullable();
            $table->string('latest_funding_amount')->nullable();
            $table->string('last_raised_at')->nullable();
            $table->string('subsidiary_of')->nullable();
            $table->string('subsidiary_of_org_id')->nullable();

            // Email Tracking
            $table->boolean('email_sent')->default(false);
            $table->boolean('email_open')->default(false);
            $table->boolean('email_bounced')->default(false);
            $table->boolean('replied')->default(false);
            $table->boolean('demoed')->default(false);

            // Other
            $table->string('retail_locations')->nullable();
            $table->string('sic_codes')->nullable();
            $table->string('naics_codes')->nullable();

            // Apollo IDs
            $table->string('apollo_id')->nullable();
            $table->string('apollo_account_id')->nullable();
            $table->string('apollo_record_id')->nullable();

            // Secondary Email
            $table->string('secondary_email')->nullable();
            $table->string('secondary_email_source')->nullable();
            $table->string('secondary_email_status')->nullable();
            $table->string('secondary_email_verification_source')->nullable();

            // Tertiary Email
            $table->string('tertiary_email')->nullable();
            $table->string('tertiary_email_source')->nullable();
            $table->string('tertiary_email_status')->nullable();
            $table->string('tertiary_email_verification_source')->nullable();

            // Qualify
            $table->string('qualify_contact')->nullable();

            // App Status
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->timestamp('email_sent_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};