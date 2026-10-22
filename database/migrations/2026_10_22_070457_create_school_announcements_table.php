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
        Schema::create('school_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->default('MAKLUMAT'); // PENTING, MAKLUMAT, SURAT EDARAN, AGENDA, INFO
            $table->text('content');
            $table->date('date')->nullable();
            $table->string('letter_number')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('external_link')->nullable();
            $table->string('badge_color')->default('amber'); // amber, emerald, blue, rose, purple
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_announcements');
    }
};
