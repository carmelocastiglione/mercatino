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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        // Add school_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('role')->constrained('schools')->onDelete('set null');
        });

        // Add school_id to books table
        Schema::table('books', function (Blueprint $table) {
            $table->foreignId('school_id')->after('id')->constrained('schools')->onDelete('cascade');
            $table->unique(['isbn', 'school_id'])->comment('ISBN unique per scuola');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['school_id']);
            $table->dropColumn('school_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['school_id']);
            $table->dropColumn('school_id');
        });

        Schema::dropIfExists('schools');
    }
};
