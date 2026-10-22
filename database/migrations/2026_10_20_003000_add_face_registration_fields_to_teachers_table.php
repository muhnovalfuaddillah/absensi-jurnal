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
            if (!Schema::hasColumn('teachers', 'face_registered_at')) {
                $table->timestamp('face_registered_at')->nullable()->after('photo');
            }
            if (!Schema::hasColumn('teachers', 'face_data')) {
                $table->longText('face_data')->nullable()->after('face_registered_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            if (Schema::hasColumn('teachers', 'face_registered_at')) {
                $table->dropColumn('face_registered_at');
            }
            if (Schema::hasColumn('teachers', 'face_data')) {
                $table->dropColumn('face_data');
            }
        });
    }
};
