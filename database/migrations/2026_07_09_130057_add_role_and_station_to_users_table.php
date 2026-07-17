<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->unique()->after('email');
            $table->enum('role', ['passenger', 'station_master', 'admin'])->default('passenger')->after('phone');
            $table->foreignId('station_id')->nullable()->after('role')->constrained('stations')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role']);
            $table->dropConstrainedForeignId('station_id');
        });
    }
};
