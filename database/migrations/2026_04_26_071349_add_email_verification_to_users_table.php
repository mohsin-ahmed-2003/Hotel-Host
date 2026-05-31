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
            $table->boolean('email_verified')->default(false)->after('email');
            $table->string('email_verify_token', 64)->nullable()->after('email_verified');
            $table->timestamp('email_verified_at')->nullable()->after('email_verify_token');
        });

        // Seed recaptcha settings
        $keys = [
            'recaptcha_enabled'   => '0',
            'recaptcha_site_key'  => '',
            'recaptcha_secret_key'=> '',
        ];
        foreach ($keys as $key => $value) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email_verified', 'email_verify_token', 'email_verified_at']);
        });
    }
};
