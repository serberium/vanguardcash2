<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('unit_price', 10, 2);
            $table->integer('package_size')->default(1); // Ex: 4 unidades por pacote
            $table->integer('stock_quantity')->default(0); // Estoque total em unidades
            $table->integer('low_stock_threshold');
            $table->integer('critical_stock_threshold');
            $table->foreignId('company_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};