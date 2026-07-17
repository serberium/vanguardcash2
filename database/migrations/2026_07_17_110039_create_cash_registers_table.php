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
        Schema::create('cash_registers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('event_id')->constrained();
        $table->foreignId('user_id')->constrained();
        $table->decimal('opening_amount', 10, 2)->default(0);
        $table->decimal('closing_amount', 10, 2)->nullable();
        $table->enum('status', ['aberto', 'fechado'])->default('aberto');
        $table->timestamp('opened_at')->useCurrent();
        $table->timestamp('closed_at')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};
