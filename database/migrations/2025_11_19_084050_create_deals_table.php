<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();

            // Ownership / tenancy
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();

            // Pipeline & stage
            $table->foreignId('pipeline_id')->constrained('pipelines')->cascadeOnDelete();
            $table->foreignId('stage_id')->constrained('stages')->cascadeOnDelete();

            // Relations
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('primary_contact_id')->nullable()->constrained('contacts')->nullOnDelete();

            // Core fields
            $table->string('title');                   // "Website redesign for ACME"
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('currency', 3)->default('USD');

            // Status & dates
            $table->string('status')->default('open'); // open, won, lost
            $table->date('expected_close_date')->nullable();
            $table->timestamp('closed_at')->nullable();

            // Flexible extra data (JSON)
            $table->json('extra')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['account_id', 'tenant_id']);
            $table->index(['status', 'expected_close_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
