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
        Schema::create('product_sides', function (Blueprint $table) {
            $table->id();
            $table->integer('pos')->nullable();
            $table->string('name');
            $table->text('descrip')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedBigInteger('product_category_id')->nullable();
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
        Schema::dropIfExists('product_sides');
    }
};
