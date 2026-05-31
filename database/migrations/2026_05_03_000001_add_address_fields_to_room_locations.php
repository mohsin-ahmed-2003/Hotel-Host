<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_locations', function (Blueprint $table) {
            $table->string('city')->nullable()->after('location_name');
            $table->string('state')->nullable()->after('city');
            $table->string('country')->nullable()->after('state');
            $table->string('zip_code')->nullable()->after('country');
        });
    }

    public function down(): void
    {
        Schema::table('room_locations', function (Blueprint $table) {
            $table->dropColumn(['city', 'state', 'country', 'zip_code']);
        });
    }
};
