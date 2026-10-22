<?php

use App\Models\SchoolClass;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->string('qr_code_token', 64)->nullable()->unique()->after('name');
        });

        // Generate token awal untuk seluruh kelas yang sudah ada
        $classes = SchoolClass::all();
        foreach ($classes as $c) {
            $c->update([
                'qr_code_token' => 'CLS-' . strtoupper(Str::slug($c->name)) . '-' . strtoupper(Str::random(8)),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn('qr_code_token');
        });
    }
};
