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
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['indoor', 'outdoor']);
            $table->enum('status', ['available', 'maintenance'])->default('available');
            $table->integer('price_per_hour');
            $table->string('location');
            $table->text('description');
            $table->string('image'); // Main image untuk card
        
            $table->timestamps();
            $table->softDeletes(); // Otomatis membuat kolom deleted_at (nullable, timestamp)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
