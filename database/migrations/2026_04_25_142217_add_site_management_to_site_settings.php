<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $keys = [
            'site_name'       => 'Hotel Host',
            'site_logo'       => '',
            'site_favicon'    => '',
            'mail_email_logo' => '',
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
        DB::table('site_settings')->whereIn('key', [
            'site_name', 'site_logo', 'site_favicon', 'mail_email_logo'
        ])->delete();
    }
};
