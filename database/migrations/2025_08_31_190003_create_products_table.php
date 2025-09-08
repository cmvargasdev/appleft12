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
            $table->integer('pos')->nullable();
            $table->string('name');
            $table->text('descrip')->nullable();
            $table->text('detail')->nullable(); // Ej: Slices/Porc Mini:4 Med:6 Fam:8 Extra:12
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedBigInteger('product_category_id')->nullable();
            $table->boolean('has_variants')->default(false);
            $table->boolean('status')->default(true);
            $table->string('image')->nullable();
            // Claves foráneas
            $table->foreign('product_category_id')->references('id')->on('product_categories')->onDelete('set null');
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
