<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the Migrations.
     */
    public function up(): void
    {
        Schema::create('plan_subscription_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')
                ->constrained('plan_subscriptions')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('feature_id')
                ->constrained('plan_features')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedInteger('used');
            $table->dateTime('valid_until')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the Migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_subscription_usage');
    }
};
