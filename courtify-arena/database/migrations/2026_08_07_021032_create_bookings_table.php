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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
        
            // Foreign Keys
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
        
            // nullable() karena tidak semua booking pakai promo
            // nullOnDelete() agar jika promo dihapus admin, data booking tetap aman (hanya promo_id-nya jadi null)
            $table->foreignId('promo_id')->nullable()->constrained('promos')->nullOnDelete();
        
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('rating')->nullable();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->integer('total_price');
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
