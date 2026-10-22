<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 0. Perluas tipe data kolom role pada tabel users agar mendukung 'superadmin'
        if (Schema::hasColumn('users', 'role')) {
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'guru'");
        }

        // 1. Buat Tabel Schools (Sekolah)
        if (!Schema::hasTable('schools')) {
            Schema::create('schools', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('npsn', 50)->nullable();
                $table->string('foundation_name')->nullable();
                $table->text('address')->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('email')->nullable();
                $table->string('headmaster_name')->nullable();
                $table->string('headmaster_nip', 50)->nullable();
                $table->string('website')->nullable();
                $table->string('logo_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Tambah Kolom school_id ke tabel-tabel terkait
        $tablesToUpdate = [
            'users' => true,
            'teachers' => true,
            'classes' => true,
            'subjects' => true,
            'students' => true,
            'attendances' => true,
            'lesson_schedules' => true,
            'teaching_journals' => true,
            'school_announcements' => true,
            'attendance_settings' => true,
        ];

        foreach ($tablesToUpdate as $tableName => $enabled) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'school_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->unsignedBigInteger('school_id')->nullable()->after('id')->index();
                });
            }
        }

        // 3. Migrasikan data yang sudah ada menjadi Sekolah Pertama (Default School)
        $existingSetting = DB::table('attendance_settings')->first();
        $schoolName = $existingSetting->school_name ?? 'MTS SYAFIIYAH';
        $foundationName = $existingSetting->foundation_name ?? 'Yayasan Barul Ulum';
        $npsn = $existingSetting->npsn ?? '20567890';
        $address = $existingSetting->school_address ?? 'Besuk Probolinggo';
        $phone = $existingSetting->school_phone ?? '081931447338';
        $email = $existingSetting->school_email ?? 'admin@sekolah.sch.id';
        $headmasterName = $existingSetting->headmaster_name ?? 'Muh Noval Fuaddillah';
        $headmasterNip = $existingSetting->headmaster_nip ?? '-';
        $website = $existingSetting->website ?? null;
        $logoPath = $existingSetting->logo_path ?? null;

        $existingSchool = DB::table('schools')->first();
        if (!$existingSchool) {
            $schoolId = DB::table('schools')->insertGetId([
                'name' => $schoolName,
                'slug' => Str::slug($schoolName) ?: 'sekolah-utama',
                'npsn' => $npsn,
                'foundation_name' => $foundationName,
                'address' => $address,
                'phone' => $phone,
                'email' => $email,
                'headmaster_name' => $headmasterName,
                'headmaster_nip' => $headmasterNip,
                'website' => $website,
                'logo_path' => $logoPath,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $schoolId = $existingSchool->id;
        }

        // 4. Backfill school_id ke seluruh data yang ada
        foreach (array_keys($tablesToUpdate) as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'school_id')) {
                DB::table($tableName)->whereNull('school_id')->update(['school_id' => $schoolId]);
            }
        }

        // 5. Buat Akun Superadmin jika belum ada
        $superadminUser = DB::table('users')->where('role', 'superadmin')->orWhere('username', 'superadmin')->first();
        if (!$superadminUser) {
            DB::table('users')->insert([
                'name' => 'Super Administrator (Pusat)',
                'username' => 'superadmin',
                'email' => 'superadmin@aplikasi.com',
                'password' => Hash::make('superadmin123'),
                'role' => 'superadmin',
                'school_id' => null, // Superadmin tidak terikat satu sekolah khusus
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Pastikan role-nya adalah superadmin
            DB::table('users')->where('id', $superadminUser->id)->update([
                'role' => 'superadmin',
                'school_id' => null,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablesToUpdate = [
            'users',
            'teachers',
            'classes',
            'subjects',
            'students',
            'attendances',
            'lesson_schedules',
            'teaching_journals',
            'school_announcements',
            'attendance_settings',
        ];

        foreach ($tablesToUpdate as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'school_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('school_id');
                });
            }
        }

        Schema::dropIfExists('schools');
    }
};
