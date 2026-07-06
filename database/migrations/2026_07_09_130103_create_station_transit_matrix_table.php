<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('station_transit_matrix', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_station_id')->constrained('stations')->onDelete('cascade');
            $table->foreignId('to_station_id')->constrained('stations')->onDelete('cascade');
            $table->integer('transit_time_hours'); // realistic road/rail travel time
            $table->integer('distance_km')->nullable();
            $table->timestamps();
            
            $table->unique(['from_station_id', 'to_station_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_transit_matrix');
    }
};