<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('property_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('space_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('accommodation')->nullable(); // e.g., '1', '15+'
            $table->string('location_name')->nullable();
            $table->string('video_type')->nullable(); // 'link' or 'video'
            $table->string('video_link')->nullable();
            $table->string('video_path')->nullable();
            $table->string('status')->default('draft'); // 'draft', 'published'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
