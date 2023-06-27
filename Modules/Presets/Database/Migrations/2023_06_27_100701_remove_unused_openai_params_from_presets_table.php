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
            $table->dropColumn(['n', 'max_tokens', 'stop', 'logit_bias']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presets', function (Blueprint $table) {
            $table->unsignedTinyInteger('n')->after('presence_penalty')->nullable();
            $table->unsignedSmallInteger('max_tokens')->after('n')->nullable();
            $table->json('stop')->after('max_tokens')->nullable();
            $table->json('logit_bias')->after('stop')->nullable();
        });
    }
};
