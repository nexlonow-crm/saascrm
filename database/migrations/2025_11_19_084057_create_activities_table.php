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
        Schema::create('activities', function (Blueprint $table) {
        $table->id();

        // Polymorphic association
        $table->morphs('subject'); // subject_id + subject_type (Contact/Company/Deal)

        $table->unsignedBigInteger('account_id');
        $table->unsignedBigInteger('tenant_id');

        $table->unsignedBigInteger('owner_id')->nullable(); // assigned to

        $table->string('type'); // task, call, meeting, email
        $table->string('title');
        $table->text('notes')->nullable();

        $table->dateTime('due_date')->nullable();
        $table->boolean('is_completed')->default(false);

        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
