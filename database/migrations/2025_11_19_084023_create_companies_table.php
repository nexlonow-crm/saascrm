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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('workspace_id')
                ->constrained('workspaces')
                ->cascadeOnDelete();

            $table->foreignId('owner_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('name');
            $table->string('domain')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('industry')->nullable();
            $table->string('size')->nullable();

            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();

            $table->json('extra')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['workspace_id', 'name']);
            $table->index(['workspace_id', 'owner_id']);
            $table->index(['workspace_id', 'domain']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
