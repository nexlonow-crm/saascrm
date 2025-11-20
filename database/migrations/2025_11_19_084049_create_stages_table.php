<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pipeline_id')->constrained('pipelines')->cascadeOnDelete();

            $table->string('name');                 // e.g. "Qualified", "Proposal Sent"
            $table->unsignedTinyInteger('probability')->nullable(); // 0-100%
            $table->unsignedInteger('position')->default(0);        // sort order

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stages');
    }
};
