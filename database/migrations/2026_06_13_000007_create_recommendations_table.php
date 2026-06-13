<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            // Vers une entreprise partenaire OU un autre membre (pas les deux en même temps)
            $table->foreignId('partner_company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('target_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('need_description');
            $table->enum('status', [
                'pending',
                'examining',
                'transmitted',
                'meeting_obtained',
                'deal_closed',
                'refused',
            ])->default('pending');
            $table->foreignId('examined_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('examined_at')->nullable();
            $table->timestamp('transmitted_at')->nullable();
            $table->text('outcome_notes')->nullable();
            $table->unsignedInteger('estimated_value')->nullable(); // KMF
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};