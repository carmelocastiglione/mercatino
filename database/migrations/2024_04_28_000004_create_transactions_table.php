<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Transactions Table
 *
 * Crea la tabella per tracciare le transazioni tra acquirenti e venditori.
 * Registra ogni vendita di libri per statistiche e audit trail.
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Relazioni con utenti e libro
            $table->foreignId('buyer_id')
                ->constrained('users')
                ->onDelete('restrict');

            $table->foreignId('seller_id')
                ->constrained('users')
                ->onDelete('restrict');

            $table->foreignId('book_id')
                ->constrained('books')
                ->onDelete('restrict');

            // Informazioni finanziarie
            $table->decimal('price', 8, 2)->comment('Prezzo pagato');
            $table->decimal('seller_earnings', 8, 2)->comment('Quanto riceve il venditore');
            $table->decimal('platform_fee', 8, 2)->default(0)->comment('Commissione piattaforma');

            // Metodi di pagamento e consegna
            $table->enum('payment_method', [
                'paypal',
                'stripe',
                'credit_card',
                'bank_transfer',
                'satispay',
                'cash'
            ])->comment('Metodo di pagamento');

            $table->enum('delivery_method', [
                'school_meeting',
                'postal',
                'pickup_point',
                'courier'
            ])->default('school_meeting')->comment('Metodo di consegna');

            // Status della transazione
            $table->enum('status', [
                'pending',
                'paid',
                'shipped',
                'delivered',
                'completed',
                'cancelled',
                'refunded'
            ])->default('pending');

            // Note e rating
            $table->text('notes')->nullable();
            $table->integer('buyer_rating')->nullable()->comment('Rating del venditore (1-5)');
            $table->text('buyer_review')->nullable();
            $table->integer('seller_rating')->nullable()->comment('Rating dell\'acquirente (1-5)');
            $table->text('seller_review')->nullable();

            // Tracking
            $table->string('tracking_number')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Timestamp
            $table->timestamps();

            // Indici
            $table->index('buyer_id');
            $table->index('seller_id');
            $table->index('book_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
