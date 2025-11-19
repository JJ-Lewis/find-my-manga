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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index();
            $table->string('subtitle')->nullable();
            $table->json('authors')->nullable();
            $table->string('isbn_10')->nullable()->index();
            $table->string('isbn_13')->nullable()->index();
            $table->string('publisher')->nullable();
            $table->date('published_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['isbn_10', 'isbn_13']);
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
