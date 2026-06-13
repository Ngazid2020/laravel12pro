<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('trainer_id')->constrained('users')->cascadeOnDelete();
            $table->text('prerequisites')->nullable();
            $table->enum('format', ['in_person', 'online', 'hybrid'])->default('in_person');
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->enum('price_type', ['free', 'included', 'premium'])->default('included');
            $table->unsignedInteger('price')->nullable(); // KMF, uniquement si premium
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};