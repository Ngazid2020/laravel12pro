<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentoring_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mentee_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('scheduled_at');
            $table->dateTime('held_at')->nullable();
            $table->enum('status', ['scheduled', 'confirmed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            // Double confirmation pour sécuriser le journal de points
            $table->boolean('confirmed_by_mentee')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentoring_sessions');
    }
};
