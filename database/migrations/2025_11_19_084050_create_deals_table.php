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

            $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('pipeline_id')->constrained('pipelines')->cascadeOnDelete();
            $table->foreignId('stage_id')->constrained('stages')->cascadeOnDelete();

            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('primary_contact_id')->nullable()->constrained('contacts')->nullOnDelete();

            $table->string('title');
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('currency', 3)->default('USD');

            $table->string('status')->default('open');
            $table->date('expected_close_date')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->json('extra')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['workspace_id', 'status', 'expected_close_date']);
            $table->index(['workspace_id', 'pipeline_id', 'stage_id']);
            $table->index(['workspace_id', 'owner_id']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
