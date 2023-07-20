<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('welcome_data', function (Blueprint $table) {
            $table->id();
            $table->string('block_name');
            $table->string('title');
            $table->text('value');
            $table->string('link_name')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('welcome_data');
    }
};
