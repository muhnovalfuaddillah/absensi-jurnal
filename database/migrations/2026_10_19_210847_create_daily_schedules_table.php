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
        Schema::create('daily_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_setting_id')->nullable()->constrained('attendance_settings')->onDelete('cascade');
            $table->unsignedTinyInteger('day_of_week'); // 1 = Senin, 2 = Selasa, ..., 7 = Minggu (ISO-8601)
            $table->string('day_name', 20); // 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'
            $table->boolean('is_active')->default(true); // true = Hari Kerja / Masuk, false = Libur
            $table->time('time_in')->default('07:00:00');
            $table->time('time_in_late_threshold')->default('07:15:00');
            $table->time('time_out')->default('14:30:00');
            $table->string('note')->nullable(); // Catatan khusus (contoh: "Upacara Bendera", "Jumat Berkah / Sholat Jumat")
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_schedules');
    }
};
