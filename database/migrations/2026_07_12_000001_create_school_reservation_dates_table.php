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
        Schema::create('school_reservation_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->dateTime('scheduled_date');
            $table->string('label')->nullable()->comment('Es: Ritiro prenotazioni mattina, Ritiro prenotazioni pomeriggio');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indice per query rapide
            $table->index(['school_id', 'is_active']);
            $table->index('scheduled_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_reservation_dates');
    }
};
