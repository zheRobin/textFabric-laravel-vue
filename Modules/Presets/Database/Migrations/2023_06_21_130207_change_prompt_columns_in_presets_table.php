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
        Schema::table('presets', function (Blueprint $table) {
            $table->renameColumn('prompt_pattern', 'system_prompt');
        });

        Schema::table('presets', function (Blueprint $table) {
            $table->string('user_prompt')->after('system_prompt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presets', function (Blueprint $table) {
            $table->renameColumn('system_prompt', 'prompt_pattern');
            $table->dropColumn('user_prompt');
        });
    }
};
