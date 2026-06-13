<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('min_points')->default(0);
            // Conditions mixtes (points seuls ne suffisent pas)
            $table->unsignedSmallInteger('required_trainings')->default(0);
            $table->unsignedSmallInteger('required_months')->default(0); // ancienneté
            $table->boolean('grants_mentor_status')->default(false);
            $table->text('description')->nullable();
            $table->string('badge_color', 20)->nullable(); // ex. "gold", "#FFD700"
            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
