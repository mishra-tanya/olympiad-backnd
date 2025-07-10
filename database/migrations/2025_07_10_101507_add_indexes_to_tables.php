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
        Schema::table('testsquestions', function (Blueprint $table) {
            $table->index(['class_id', 'goal_id', 'test_id']);
        });

        Schema::table('goalstests', function (Blueprint $table) {
            $table->index(['class_id', 'goal_id']);
        });

        Schema::table('goals', function (Blueprint $table) {
            $table->index(['class_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testsquestions', function (Blueprint $table) {
            $table->dropIndex(['class_id', 'goal_id', 'test_id']);
        });

        Schema::table('goalstests', function (Blueprint $table) {
            $table->dropIndex(['class_id', 'goal_id']);
        });

        Schema::table('goals', function (Blueprint $table) {
            $table->dropIndex(['class_name', 'goal_name']);
        });
    }
};
