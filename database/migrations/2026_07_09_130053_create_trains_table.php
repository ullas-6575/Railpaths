<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trains', function (Blueprint $table) {
            $table->id();
            $table->string('train_number')->unique(); // e.g., "RAJ123"
            $table->string('name'); // e.g., "Rajdhani Express"
            $table->enum('type', ['express', 'superfast', 'passenger', 'local'])->default('express');
            $table->integer('total_seats')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trains');
    }
};