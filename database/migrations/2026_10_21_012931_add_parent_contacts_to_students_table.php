<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('parent_name', 100)->nullable()->after('gender');
            $table->string('parent_phone', 30)->nullable()->after('parent_name');
            $table->enum('status', ['aktif', 'lulus', 'mutasi', 'keluar'])->default('aktif')->after('parent_phone');
        });

        Schema::table('attendance_settings', function (Blueprint $table) {
            $table->string('wa_gateway_token', 255)->nullable()->after('work_days');
            $table->string('wa_gateway_url', 255)->nullable()->after('wa_gateway_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['parent_name', 'parent_phone', 'status']);
        });

        Schema::table('attendance_settings', function (Blueprint $table) {
            $table->dropColumn(['wa_gateway_token', 'wa_gateway_url']);
        });
    }
};
