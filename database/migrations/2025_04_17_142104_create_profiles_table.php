<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('photo')->nullable();
            $table->text('biography')->nullable();
            $table->json('skills')->nullable();
            $table->string('specialty')->nullable(); // For mentors
            $table->string('experience_level')->nullable(); // For mentors
            $table->integer('level')->nullable(); // For students
            $table->integer('badges_count')->default(0); // For students
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};