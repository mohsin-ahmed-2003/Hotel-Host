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
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->nullable()->constrained()->onDelete('set null');
            $table->string('plan_name');
            $table->decimal('price', 10, 2);
            $table->string('currency')->default('USD');
            $table->integer('duration_days');
            $table->integer('hosting_allowed')->nullable()->comment('Null means unlimited');
            $table->integer('cancellations_allowed')->default(0);
            $table->decimal('cancellation_fee_reduction', 5, 2)->default(0);
            $table->string('payment_type')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('status')->default('pending')->comment('pending, active, expired, failed, cancelled');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
