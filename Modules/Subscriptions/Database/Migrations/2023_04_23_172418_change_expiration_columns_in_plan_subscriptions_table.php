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
        Schema::table('plan_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['trial_ends_at']);
            $table->boolean('on_trial')->after('starts_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_subscriptions', function (Blueprint $table) {
            $table->dateTime('trial_ends_at')->nullable()->after('starts_at');
            $table->dropColumn('on_trial');
        });
    }
};
