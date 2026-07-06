<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('route_station', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->foreignId('station_id')->constrained('stations')->onDelete('cascade');
            $table->integer('stop_order');
            $table->time('scheduled_arrival')->nullable();
            $table->time('scheduled_departure');
            $table->integer('distance_km')->nullable();
            $table->timestamps();
            
            $table->unique(['route_id', 'station_id']);
            $table->unique(['route_id', 'stop_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_station');
    }
};