<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\OpenAI\Enums\ChatModelEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('presets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('model', ChatModelEnum::values());
            $table->string('prompt_pattern');
            $table->foreignId('collection_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedDouble('temperature')->nullable();
            $table->unsignedDouble('top_p')->nullable();
            $table->unsignedDouble('frequency_penalty')->nullable();
            $table->unsignedDouble('presence_penalty')->nullable();
            $table->unsignedTinyInteger('n')->nullable();
            $table->unsignedSmallInteger('max_tokens')->nullable();
            $table->json('stop')->nullable();
            $table->json('logit_bias')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presets');
    }
};
