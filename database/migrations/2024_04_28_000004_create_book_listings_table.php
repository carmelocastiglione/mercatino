<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Book Listings Table
 *
 * Crea la tabella per le copie specifiche che gli studenti vendono.
 * Le book_listings sono copie specifiche che gli studenti vendono.
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
        // Tabella copie vendute dagli studenti
        Schema::create('book_listings', function (Blueprint $table) {
            $table->id();

            // Relazione con il libro in catalogo
            $table->foreignId('book_id')
                ->constrained('books')
                ->onDelete('cascade');

            // Relazione con l'utente che vende
            $table->foreignId('seller_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Condizione della copia specifica
            $table->enum('condition', ['like-new', 'good', 'fair', 'poor'])
                ->default('good')
                ->comment('Condizione della copia');

            // Prezzo di vendita
            $table->decimal('price', 8, 2);

            // Status della vendita
            $table->enum('status', ['available', 'reserved', 'sold', 'withdrawn', 'reclaim', 'archived'])
                ->default('available');

            // Libro da lasciare a scuola
            $table->boolean('leave')->default(false);

            // Immagini della copia specifica
            $table->json('images')->nullable()->comment('Array di URL immagini della copia');

            // Statistiche
            $table->integer('views')->default(0)->comment('Numero di visualizzazioni');
            $table->integer('favorites')->default(0)->comment('Numero di preferiti');

            // Timestamp
            $table->timestamps();
            $table->softDeletes();

            // Indici
            $table->index('book_id');
            $table->index('seller_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_listings');
    }
};
