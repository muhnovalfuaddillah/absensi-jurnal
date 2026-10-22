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
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->default('SMK / SMA Negeri Teladan');
            $table->time('time_in')->default('07:00:00');
            $table->time('time_in_late_threshold')->default('07:15:00');
            $table->time('time_out')->default('14:30:00');
            $table->string('work_days')->default('Senin - Jumat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_settings');
    }
};
