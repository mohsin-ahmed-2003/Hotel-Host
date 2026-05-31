<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Add fields to rooms table
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('booking_type')->default('Instant Booking')->nullable();
            $table->string('cancellation_policy')->default('Flexible')->nullable();
            $table->boolean('custom_cancellation')->default(false);
            $table->integer('free_cancellation_days')->default(0);
            $table->decimal('cancellation_fee', 8, 2)->default(0.00);
            $table->text('selected_rules')->nullable(); // JSON / serialized string of rule IDs
        });

        // 2. Create room_rules table
        Schema::create('room_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_name');
            $table->string('rule_text')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        // 3. Create room_calendars table
        Schema::create('room_calendars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->date('date');
            $table->boolean('is_blocked')->default(true);
            $table->timestamps();
            
            $table->unique(['room_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_calendars');
        Schema::dropIfExists('room_rules');
        
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn([
                'booking_type',
                'cancellation_policy',
                'custom_cancellation',
                'free_cancellation_days',
                'cancellation_fee',
                'selected_rules'
            ]);
        });
    }
};
