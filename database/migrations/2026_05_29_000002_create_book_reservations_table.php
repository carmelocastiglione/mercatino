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
        Schema::create('book_reservations', function (Blueprint $table) {
            $table->id();

            // Relazione con il batch di prenotazione
            $table->foreignId('book_reservation_batch_id')
                ->constrained('book_reservation_batches')
                ->onDelete('cascade');

            // Relazione con il libro acquisito da prenotare
            $table->foreignId('book_listing_id')
                ->constrained('book_listings')
                ->onDelete('cascade');

            // Status della singola prenotazione
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'cancelled'])
                ->default('pending');

            // Note opzionali
            $table->text('notes')->nullable();

            // Timestamp di prenotazione e azioni
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            // Timestamp
            $table->timestamps();
            $table->softDeletes();

            // Indici
            $table->index('book_reservation_batch_id');
            $table->index('book_listing_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_reservations');
    }
};
