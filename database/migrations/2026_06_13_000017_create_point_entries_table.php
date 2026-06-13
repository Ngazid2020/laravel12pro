<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Journal immuable : jamais de UPDATE, uniquement INSERT
        Schema::create('point_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('source', [
                'training_attended',
                'training_animated',
                'recommendation_closed',
                'mentoring_session',
                'contribution',
                'referral',   // plafonné au 1er niveau uniquement
                'manual',     // ajustement admin
            ]);
            $table->unsignedInteger('points');
            // Lien vers l'activité source (TrainingEnrollment, Recommendation, MentoringSession…)
            $table->nullableMorphs('pointable');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            // Pas de updated_at : ce journal est immuable
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_entries');
    }
};
