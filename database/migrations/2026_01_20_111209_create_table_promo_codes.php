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
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->integer('code_type')->default(0)->comment('0=Auto,1=Custom');
            $table->string('code');
            $table->integer('type')->comment('1=%,2=Flat')->nullable();
            $table->integer('code_amount')->nullable();
            $table->date('start_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('min_order_amount')->nullable();
            $table->integer('total_used')->nullable();
            $table->integer('per_user_used')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0=Inactive|1=Active');
            $table->string('applied_to')->nullable()->comment('all_users,selected_users');
            $table->foreignId('created_by')->constrained()->comment('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
