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
        Schema::table('reclaims', function (Blueprint $table) {
            $table->unsignedBigInteger('buyer_id')->nullable()->after('user_id');
            $table->foreign('buyer_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reclaims', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['buyer_id']);
            $table->dropColumn('buyer_id');
        });
    }
};
