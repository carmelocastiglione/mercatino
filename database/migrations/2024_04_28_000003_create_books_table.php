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
            $table->string('isbn')->nullable();
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
            $table->index('author');
            $table->index('isbn');
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
