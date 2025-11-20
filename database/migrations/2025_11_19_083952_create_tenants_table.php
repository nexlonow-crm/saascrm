<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();

            // Link to account
            $table->foreignId('account_id')
                ->constrained('accounts')
                ->cascadeOnDelete();

            // Core
            $table->string('name');                  // Tenant name (e.g. company/org)
            $table->string('status')->default('active'); // e.g. active, suspended

            // Audit
            $table->timestamps();
            $table->softDeletes();

            // Useful index
            $table->index(['account_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
