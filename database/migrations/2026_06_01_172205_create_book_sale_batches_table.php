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
        Schema::create('book_sale_batches', function (Blueprint $table) {
            $table->id();

            // Scuola a cui appartiene il batch
            $table->foreignId('school_id')
                ->constrained('schools')
                ->onDelete('cascade');

            // Staff member che ha creato il batch
            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('restrict');

            // Note opzionali sul batch
            $table->text('notes')->nullable();

            // Timestamp
            $table->timestamps();

            // Indici
            $table->index('school_id');
            $table->index('created_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_sale_batches');
    }
};
