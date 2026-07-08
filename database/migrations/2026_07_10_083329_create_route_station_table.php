<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('route_station', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained()->onDelete('cascade');
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->integer('stop_order')->default(0); // 1, 2, 3... for drag-drop ordering
            $table->time('arrival_time')->nullable(); // arrival at this station
            $table->time('departure_time')->nullable(); // departure from this station
            $table->integer('distance_from_source')->default(0); // km
            $table->timestamps();

            $table->unique(['route_id', 'station_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_station');
    }
};