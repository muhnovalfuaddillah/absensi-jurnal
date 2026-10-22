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
        Schema::table('teaching_journals', function (Blueprint $table) {
            $table->boolean('is_substitute')->default(false)->after('teacher_id');
            $table->foreignId('substituted_teacher_id')->nullable()->after('is_substitute')->constrained('teachers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teaching_journals', function (Blueprint $table) {
            $table->dropForeign(['substituted_teacher_id']);
            $table->dropColumn(['is_substitute', 'substituted_teacher_id']);
        });
    }
};
