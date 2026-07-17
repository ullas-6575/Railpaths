<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('station_logs')) {
            return;
        }

        Schema::create('station_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_master_id')->constrained('users');
            $table->foreignId('station_id')->constrained('stations');
            $table->foreignId('train_id')->constrained('trains');
            $table->foreignId('schedule_id')->constrained('schedules');
            $table->time('scheduled_arrival')->nullable();
            $table->time('actual_arrival')->nullable();
            $table->time('scheduled_departure');
            $table->time('actual_departure')->nullable();
            $table->integer('delay_minutes')->default(0);
            $table->text('remarks')->nullable();
            $table->enum('status', ['on_time', 'delayed', 'cancelled'])->default('on_time');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_logs');
    }
};
