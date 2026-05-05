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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('category')->onDelete('restrict');
            $table->foreignId('subcategory_id')->constrained('subcategory')->onDelete('restrict');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete(); //brand_id null if deleted
            $table->decimal('price',10,2)->nullable();
            $table->decimal('offer_price',10,2)->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->integer('no_of_pieces_available')->nullable();
            $table->integer('estimated_delivery_time')->nullable()->comment('in days');
            $table->string('sku_number')->nullable();
            $table->string('barcode_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
