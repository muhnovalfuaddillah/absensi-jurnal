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
            $table->string('foundation_name')->nullable()->default('Yayasan Barul Ulum')->after('school_name');
            $table->string('npsn', 50)->nullable()->after('foundation_name');
            $table->string('school_phone', 50)->nullable()->after('school_address');
            $table->string('school_email', 100)->nullable()->after('school_phone');
            $table->string('headmaster_name')->nullable()->after('school_email');
            $table->string('headmaster_nip', 50)->nullable()->after('headmaster_name');
            $table->string('website')->nullable()->after('headmaster_nip');
            $table->string('logo_path')->nullable()->default('logo.jpg')->after('website');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_settings', function (Blueprint $table) {
            $table->dropColumn([
                'foundation_name',
                'npsn',
                'school_phone',
                'school_email',
                'headmaster_name',
                'headmaster_nip',
                'website',
                'logo_path',
            ]);
        });
    }
};
