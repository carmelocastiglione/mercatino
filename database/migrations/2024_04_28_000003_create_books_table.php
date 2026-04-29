<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Books Table
 *
 * Crea la tabella per i libri nel catalogo e per le copie vendute dagli studenti.
 * I libri sono generici (catalogo importato).
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
        // Tabella catalogo libri generici
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            // Informazioni di base del libro
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('isbn')->nullable()->unique();
            $table->text('description')->nullable();

            // Informazioni scolastiche
            $table->string('subject')->comment('Materia del libro (es: Matematica, Italiano)')->nullable();
            $table->string('school_class')->comment('Classe a cui è destinato il libro (es: 1ª, 2ª, 3ª)')->nullable();

            // Prezzo di copertina
            $table->decimal('original_price', 8, 2)->nullable()->comment('Prezzo di copertina originale');

            // Immagine di copertina
            $table->string('cover_image')->nullable()->comment('Immagine di copertina principale');

            // Timestamp
            $table->timestamps();
            $table->softDeletes();

            // Indici
            $table->fullText('title', 'description');
        });

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
            $table->enum('status', ['available', 'reserved', 'sold', 'archived'])
                ->default('available');

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
        Schema::dropIfExists('books');
    }
};
