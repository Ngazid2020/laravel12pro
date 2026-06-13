<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Arbre d'affiliation : mentor_id → users.id (auto-référencé via users)
            $table->foreignId('mentor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('company_name')->nullable();
            $table->string('project_name')->nullable();
            $table->string('sector')->nullable();
            $table->string('city')->nullable();
            $table->text('bio')->nullable();
            $table->json('skills_offered')->nullable();
            $table->json('needs_expressed')->nullable();
            $table->json('social_links')->nullable();  // { linkedin, facebook, website, ... }
            $table->string('referral_code', 20)->unique()->nullable();
            $table->enum('membership_status', ['candidate', 'active', 'suspended', 'excluded', 'alumni'])->default('candidate');
            $table->date('membership_expires_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_profiles');
    }
};