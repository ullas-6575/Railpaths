<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('train_id')->constrained('trains')->cascadeOnDelete();
            $table->string('seat_number');
            $table->timestamps();
            $table->unique(['train_id', 'seat_number']);
        });

        Schema::create('booking_seat', function (Blueprint $table) {
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('seat_id')->constrained('seats')->cascadeOnDelete();
            $table->primary(['booking_id', 'seat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_seat');
        Schema::dropIfExists('seats');
    }
};
