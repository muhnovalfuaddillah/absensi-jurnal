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
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'attendance_method')) {
                $table->string('attendance_method', 50)->default('face_scan')->after('status');
            }
            if (!Schema::hasColumn('attendances', 'photo_in')) {
                $table->string('photo_in')->nullable()->after('attendance_method');
            }
            if (!Schema::hasColumn('attendances', 'photo_out')) {
                $table->string('photo_out')->nullable()->after('photo_in');
            }
            if (!Schema::hasColumn('attendances', 'latitude_in')) {
                $table->decimal('latitude_in', 10, 8)->nullable()->after('photo_out');
            }
            if (!Schema::hasColumn('attendances', 'longitude_in')) {
                $table->decimal('longitude_in', 11, 8)->nullable()->after('latitude_in');
            }
            if (!Schema::hasColumn('attendances', 'latitude_out')) {
                $table->decimal('latitude_out', 10, 8)->nullable()->after('longitude_in');
            }
            if (!Schema::hasColumn('attendances', 'longitude_out')) {
                $table->decimal('longitude_out', 11, 8)->nullable()->after('latitude_out');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $cols = ['attendance_method', 'photo_in', 'photo_out', 'latitude_in', 'longitude_in', 'latitude_out', 'longitude_out'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('attendances', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
