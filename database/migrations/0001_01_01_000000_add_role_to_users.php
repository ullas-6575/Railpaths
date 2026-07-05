<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (Schema::hasColumn('users', 'role')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['passenger', 'station_master', 'admin'])->default('passenger')->after('email');
            $table->unsignedBigInteger('station_id')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['role', 'station_id']);
            });
        }
    }
};