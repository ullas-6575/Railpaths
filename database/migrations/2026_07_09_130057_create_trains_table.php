<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trains', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('number', 20)->unique();
            $table->string('type')->default('intercity'); // intercity, mail, local
            $table->integer('total_capacity')->default(0);
            $table->json('capacity_by_class')->nullable(); // {"shovan": 200, "snigdha": 80, "ac_chair": 40}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trains');
    }
};