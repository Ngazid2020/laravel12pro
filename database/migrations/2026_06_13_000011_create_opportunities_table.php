<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('published_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('partner_company_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['tender', 'mission', 'internship', 'funding', 'contest']);
            $table->string('sector')->nullable();
            $table->json('target_skills')->nullable();
            $table->date('deadline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};