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
        Schema::table('users', function (Blueprint $table) {
            $table->string('position')->nullable()->change();
            $table->string('company')->nullable()->change();
            $table->string('employees')->nullable()->change();
            $table->string('phone_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('position')->nullable(false)->change();
            $table->string('company')->nullable(false)->change();
            $table->string('employees')->nullable(false)->change();
            $table->string('phone_number')->nullable(false)->change();
        });
    }
};
