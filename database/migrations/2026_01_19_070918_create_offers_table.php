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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('category');
            $table->foreignId('subcategory_id')->constrained('subcategory');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('price',10,2)->nullable();
            $table->decimal('offer_price',10,2)->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0=Inactive|1=Active');
            $table->string('banner')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
