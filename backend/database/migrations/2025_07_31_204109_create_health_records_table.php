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
        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->date('record_date');
            $table->string('type'); // e.g., 'Vaccination', 'Check-up', 'Medication', 'Surgery'
            $table->text('description')->nullable();
            $table->string('veterinarian')->nullable();
            $table->date('next_due_date')->nullable(); // For vaccinations, follow-ups
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_records');
    }
};
