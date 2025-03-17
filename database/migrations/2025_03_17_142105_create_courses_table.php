<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->integer('duration'); // in minutes
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced']);
            $table->foreignId('subcategory_id')->constrained();
            $table->foreignId('user_id')->constrained()->comment('Mentor ID');
            $table->string('status')->default('draft'); // draft, published, archived
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};