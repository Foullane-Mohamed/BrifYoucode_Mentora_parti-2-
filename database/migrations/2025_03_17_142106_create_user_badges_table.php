<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('badge_id')->constrained();
            $table->timestamp('awarded_at');
            $table->timestamps();
            
            // Ensure a user can only have a badge once
            $table->unique(['user_id', 'badge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
    }
};