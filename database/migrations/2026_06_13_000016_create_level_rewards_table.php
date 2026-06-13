<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('level_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_id')->constrained()->cascadeOnDelete();
            $table->enum('type', [
                'badge',
                'premium_training',
                'priority_opportunity',
                'event_invitation',
                'referral_bonus',    // prime fixe unique par filleul validé
                'commission_rate',   // % sur affaires conclues
            ]);
            $table->text('description');
            $table->string('value')->nullable(); // montant KMF ou pourcentage selon le type
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('level_rewards');
    }
};
