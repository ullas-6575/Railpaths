<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('train_id')->constrained('trains')->cascadeOnDelete();
            $table->foreignId('route_id')->constrained('routes')->cascadeOnDelete();
            $table->foreignId('station_id')->constrained('stations')->cascadeOnDelete();
            $table->date('date');
            $table->time('arrival_time')->nullable();
            $table->time('departure_time')->nullable();
            $table->timestamps();
            $table->unique(['train_id', 'station_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
