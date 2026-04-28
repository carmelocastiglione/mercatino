<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Books Table
 *
 * Crea la tabella per i libri nel mercatino.
 * Contiene tutte le informazioni relative ai libri in vendita.
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
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            // Relazione con l'utente che vende il libro
            $table->foreignId('seller_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Informazioni di base del libro
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('isbn')->nullable()->unique();
            $table->text('description')->nullable();

            // Informazioni scolastiche
            $table->string('subject')->comment('Materia del libro (es: Matematica, Italiano)');
            $table->string('school_class')->comment('Classe a cui è destinato il libro (es: 1ª, 2ª, 3ª)');

            // Stato e prezzo
            $table->enum('condition', ['like-new', 'good', 'fair', 'poor'])
                ->default('good')
                ->comment('Condizione del libro');

            $table->decimal('price', 8, 2);
            $table->decimal('original_price', 8, 2)->nullable()->comment('Prezzo di copertina originale');

            // Status della vendita
            $table->enum('status', ['available', 'reserved', 'sold', 'archived'])
                ->default('available');

            // Immagini
            $table->json('images')->nullable()->comment('Array di URL immagini');
            $table->string('cover_image')->nullable()->comment('Immagine di copertina principale');

            // Statistiche
            $table->integer('views')->default(0)->comment('Numero di visualizzazioni');
            $table->integer('favorites')->default(0)->comment('Numero di preferiti');

            // Timestamp
            $table->timestamps();
            $table->softDeletes();

            // Indici
            $table->index('seller_id');
            $table->index('subject');
            $table->index('school_class');
            $table->index('status');
            $table->fullText('title', 'description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
