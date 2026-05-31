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
        Schema::create('book_reservation_batches', function (Blueprint $table) {
            $table->id();

            // Relazione con lo studente che prenota
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Relazione con la scuola
            $table->foreignId('school_id')
                ->constrained('schools')
                ->onDelete('cascade');

            // Status della prenotazione
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'cancelled'])
                ->default('pending');

            // Numero di libri nella prenotazione
            $table->integer('total_items')->default(0);

            // Note opzionali
            $table->text('notes')->nullable();

            // Timestamp di prenotazione e azioni
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // Timestamp
            $table->timestamps();
            $table->softDeletes();

            // Indici
            $table->index('user_id');
            $table->index('school_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_reservation_batches');
    }
};
