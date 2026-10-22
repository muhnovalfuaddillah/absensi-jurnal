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
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('nuptk', 50)->nullable()->after('nip');
            $table->string('employment_status', 50)->default('Guru Tetap')->after('subject');
            $table->string('education', 50)->nullable()->after('employment_status');
            $table->string('class_duty', 100)->nullable()->after('education');
            $table->text('address')->nullable()->after('phone');
            $table->string('birth_place', 100)->nullable()->after('address');
            $table->date('birth_date')->nullable()->after('birth_place');
            $table->string('photo')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn([
                'nuptk',
                'employment_status',
                'education',
                'class_duty',
                'address',
                'birth_place',
                'birth_date',
                'photo',
            ]);
        });
    }
};
