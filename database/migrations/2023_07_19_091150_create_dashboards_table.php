<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('dashboard', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('value'); // Use 'text' instead of 'string' for larger text fields
            $table->string('link_name')->nullable();
            $table->text('link')->nullable(); // Use 'text' instead of 'string' for larger text fields
            // Additional fields if needed

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard');
    }
};
