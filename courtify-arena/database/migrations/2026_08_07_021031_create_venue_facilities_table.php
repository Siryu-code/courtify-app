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
        Schema::create('venue_facilities', function (Blueprint $table) {
            $table->id();
        
            // Foreign Key ke tabel venues
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
        
            $table->string('name');
            $table->string('icon');
            $table->integer('quantity');
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venue_facilities');
    }
};
