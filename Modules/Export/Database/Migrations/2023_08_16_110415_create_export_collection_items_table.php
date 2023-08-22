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
        Schema::create('export_collection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('export_id')
                ->constrained('exports')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->json('data');
            $table->json('completions');
            $table->json('translations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_collection_items');
    }
};
