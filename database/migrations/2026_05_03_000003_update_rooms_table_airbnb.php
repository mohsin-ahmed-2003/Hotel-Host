<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Check if columns exist before adding (for idempotency)
            if (!Schema::hasColumn('rooms', 'title')) {
                $table->string('title')->nullable()->after('name');
            }
            if (!Schema::hasColumn('rooms', 'price')) {
                $table->decimal('price', 10, 2)->nullable()->after('accommodation');
            }
            if (Schema::hasColumn('rooms', 'status')) {
                $table->string('status')->nullable()->change(); // Allow changing to enum later or just use string
            } else {
                $table->string('status')->default('pending');
            }
            
            // Airbnb specific fields for the multi-step form
            if (!Schema::hasColumn('rooms', 'address')) {
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('country')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['title', 'price', 'address', 'city', 'state', 'country']);
        });
    }
};
