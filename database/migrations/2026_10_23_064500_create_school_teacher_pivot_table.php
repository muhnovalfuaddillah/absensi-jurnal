<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat Tabel Pivot school_teacher
        if (!Schema::hasTable('school_teacher')) {
            Schema::create('school_teacher', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
                $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
                $table->boolean('is_primary')->default(false);
                $table->string('subject')->nullable();
                $table->string('class_duty')->nullable();
                $table->timestamps();

                $table->unique(['school_id', 'teacher_id']);
            });
        }

        // 2. Backfill guru-guru yang sudah ada ke tabel school_teacher
        $teachers = DB::table('teachers')->whereNotNull('school_id')->get();
        foreach ($teachers as $t) {
            $exists = DB::table('school_teacher')
                ->where('school_id', $t->school_id)
                ->where('teacher_id', $t->id)
                ->exists();

            if (!$exists) {
                DB::table('school_teacher')->insert([
                    'school_id' => $t->school_id,
                    'teacher_id' => $t->id,
                    'is_primary' => true,
                    'subject' => $t->subject,
                    'class_duty' => $t->class_duty,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 3. Ubah unique index pada tabel attendances agar guru yang mengajar di 2 sekolah bisa presensi di kedua sekolah pada tanggal yang sama
        if (Schema::hasTable('attendances')) {
            // Pastikan ada index biasa pada teacher_id agar FK tidak memblokir dropUnique
            try {
                Schema::table('attendances', function (Blueprint $table) {
                    $table->index('teacher_id', 'attendances_teacher_id_index');
                });
            } catch (\Exception $e) {}

            Schema::table('attendances', function (Blueprint $table) {
                // Drop index lama jika ada
                try {
                    $table->dropUnique('attendances_teacher_id_date_unique');
                } catch (\Exception $e) {}

                // Tambahkan index komposit unik: teacher_id + school_id + date
                try {
                    $table->unique(['teacher_id', 'school_id', 'date'], 'attendances_teacher_school_date_unique');
                } catch (\Exception $e) {}
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('attendances')) {
            Schema::table('attendances', function (Blueprint $table) {
                try {
                    $table->dropUnique('attendances_teacher_school_date_unique');
                    $table->unique(['teacher_id', 'date'], 'attendances_teacher_id_date_unique');
                } catch (\Exception $e) {}
            });
        }

        Schema::dropIfExists('school_teacher');
    }
};
