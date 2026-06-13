<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['enrolled', 'attended', 'absent'])->default('enrolled');
            $table->timestamp('attended_at')->nullable();
            $table->unsignedTinyInteger('rating')->nullable(); // 1-5
            $table->text('comment')->nullable();
            $table->unique(['training_session_id', 'user_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_enrollments');
    }
};