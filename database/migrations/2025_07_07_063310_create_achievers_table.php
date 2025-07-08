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
        Schema::create('achievers', function (Blueprint $table) {
            $table->id();
            $table->date('week_ending');
            $table->string('student_name');
            $table->string('student_school');
            $table->string('student_grade');
            $table->string('school_name');
            $table->string('school_location');
            $table->string('school_logo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievers');
    }
};
