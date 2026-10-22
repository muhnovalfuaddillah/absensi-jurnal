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
        // 1. Tabel Kelas (Classes)
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50); // e.g. 'VII-A', 'VII-B', 'VIII-A'
            $table->string('grade_level', 20)->default('7'); // e.g. '7', '8', '9' atau 'X', 'XI'
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->string('academic_year', 20)->default('2025/2026');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel Mata Pelajaran (Subjects)
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 3. Tabel Siswa (Students)
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->string('nis', 30)->nullable();
            $table->string('nisn', 30)->nullable();
            $table->string('name', 150);
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Tabel Jadwal Pelajaran (Lesson Schedules)
        Schema::create('lesson_schedules', function (Blueprint $table) {
            $table->id();
            $table->enum('day', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']);
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room', 50)->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // 5. Tabel Jurnal Mengajar Guru (Teaching Journals)
        Schema::create('teaching_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('lesson_schedule_id')->nullable()->constrained('lesson_schedules')->nullOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('materi', 255); // Materi Pokok / Topik
            $table->text('kegiatan'); // Uraian Aktivitas Pembelajaran
            $table->text('catatan')->nullable(); // Catatan Kelas / Hambatan
            $table->string('attachment')->nullable(); // Foto dokumentasi
            $table->enum('status', ['terlaksana', 'tugas', 'daring', 'tidak_terlaksana'])->default('terlaksana');
            $table->timestamps();
        });

        // 6. Tabel Absensi Siswa (Student Attendances)
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_journal_id')->nullable()->constrained('teaching_journals')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpa'])->default('hadir');
            $table->string('notes')->nullable();
            $table->timestamps();

            // Indeks untuk query pencarian cepat & integritas
            $table->index(['teaching_journal_id', 'student_id']);
            $table->index(['class_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
        Schema::dropIfExists('teaching_journals');
        Schema::dropIfExists('lesson_schedules');
        Schema::dropIfExists('students');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('classes');
    }
};
