<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_active')->default(1)->after('role');
            $table->string('login_type', 20)->default('form')->after('is_active'); // form|google|facebook|apple
            $table->string('social_id', 255)->nullable()->after('login_type');
            $table->string('social_provider', 20)->nullable()->after('social_id');
        });

        // Seed social login settings
        $settings = [
            'google_login_enabled'    => '0',
            'google_client_id'        => '',
            'google_client_secret'    => '',
            'facebook_login_enabled'  => '0',
            'facebook_client_id'      => '',
            'facebook_client_secret'  => '',
            'apple_login_enabled'     => '0',
            'apple_client_id'         => '',
            'apple_client_secret'     => '',
        ];

        foreach ($settings as $key => $value) {
            DB::table('site_settings')->updateOrInsert(['key' => $key], ['value' => $value]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'login_type', 'social_id', 'social_provider']);
        });
    }
};
