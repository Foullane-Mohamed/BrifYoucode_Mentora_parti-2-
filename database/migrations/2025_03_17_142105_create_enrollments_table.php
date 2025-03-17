<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->comment('Student ID');
            $table->foreignId('course_id')->constrained();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->integer('progress')->default(0); // percentage
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Ensure a student can only enroll once in a course
            $table->unique(['user_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};