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
        Schema::create('retailer_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retailer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->string('sku')->nullable();
            $table->string('url')->nullable();
            $table->bigInteger('price_cents')->nullable();
            $table->string('currency', 8)->nullable();
            $table->boolean('in_stock')->default(false);
            $table->integer('stock_qty')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();

            $table->index(['book_id', 'retailer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retailer_products');
    }
};
