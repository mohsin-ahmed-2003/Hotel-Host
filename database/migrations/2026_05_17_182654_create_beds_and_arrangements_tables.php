<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Bed Types CRUD table (Admin side)
        Schema::create('room_bed', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable(); // Path to custom uploaded icon/image
            $table->timestamps();
        });

        // 2. Bedrooms bed mapping table
        Schema::create('room_bedroom_beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->integer('bedroom_index'); // e.g. 1 for Bedroom 1, 2 for Bedroom 2
            $table->foreignId('room_bed_id')->constrained('room_bed')->cascadeOnDelete();
            $table->integer('count')->default(0);
            $table->timestamps();
        });

        // 3. Add bedrooms_count to rooms table
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'bedrooms_count')) {
                $table->integer('bedrooms_count')->default(1)->after('accommodation');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('bedrooms_count');
        });
        Schema::dropIfExists('room_bedroom_beds');
        Schema::dropIfExists('room_bed');
    }
};
