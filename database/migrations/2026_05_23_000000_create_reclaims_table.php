<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Reclaims Table
 *
 * Crea la tabella per tracciare i ritiri di libri non venduti.
 * Uno staff member ritira un libro che il venditore non ha più interesse a vendere.
 *
 * @return void
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reclaims', function (Blueprint $table) {
            $table->id();

            // Relazione con l'utente (venditore)
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Relazione con il libro listing ritirato
            $table->foreignId('book_listing_id')
                ->constrained('book_listings')
                ->onDelete('cascade');

            // Note sul ritiro
            $table->text('notes')->nullable()->comment('Note aggiuntive sul ritiro');

            // Timestamp
            $table->timestamps();

            // Indici
            $table->index('user_id');
            $table->index('book_listing_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reclaims');
    }
};
