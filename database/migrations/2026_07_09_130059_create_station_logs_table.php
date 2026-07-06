<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('station_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained('stations');
            $table->foreignId('route_id')->constrained('routes');
            $table->foreignId('train_id')->constrained('trains');
            $table->time('scheduled_departure');
            $table->timestamp('actual_departure');
            $table->integer('delay_minutes')->default(0);
            $table->foreignId('logged_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_logs');
    }
};