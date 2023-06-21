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
            $table->foreignId('input_language_id')
                ->after('logit_bias')
                ->nullable()
                ->constrained('languages')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('output_language_id')
                ->after('input_language_id')
                ->nullable()
                ->constrained('languages')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('input_language_id');
            $table->dropConstrainedForeignId('output_language_id');
        });
    }
};
