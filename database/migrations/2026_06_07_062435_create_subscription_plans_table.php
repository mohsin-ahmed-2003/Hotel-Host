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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->string('currency')->default('USD');
            $table->integer('duration_days')->default(30);
            $table->integer('hosting_allowed')->nullable()->comment('Null means unlimited');
            $table->integer('cancellations_allowed')->default(0)->comment('No. of host cancellation allowed');
            $table->decimal('cancellation_fee_reduction', 5, 2)->default(0)->comment('Percentage reduction');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
