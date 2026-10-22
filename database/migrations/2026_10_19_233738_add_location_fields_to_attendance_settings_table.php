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
        Schema::table('attendance_settings', function (Blueprint $table) {
            $table->text('school_address')->nullable()->after('school_name');
            $table->decimal('latitude', 10, 8)->default(-7.53420000)->after('school_address');
            $table->decimal('longitude', 11, 8)->default(112.23510000)->after('latitude');
            $table->integer('radius_meters')->default(120)->after('longitude');
            $table->boolean('is_geofence_active')->default(true)->after('radius_meters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_settings', function (Blueprint $table) {
            $table->dropColumn([
                'school_address',
                'latitude',
                'longitude',
                'radius_meters',
                'is_geofence_active',
            ]);
        });
    }
};
