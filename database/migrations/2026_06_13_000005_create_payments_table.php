<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Polymorphique : subscription, training_enrollment, event_registration
            $table->nullableMorphs('payable');
            $table->foreignId('subscription_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('method', ['mvola', 'holo_money', 'cash', 'cheque']);
            $table->unsignedInteger('amount'); // KMF
            $table->enum('status', ['pending', 'validated', 'rejected'])->default('pending');
            // MVola / Holo Money
            $table->string('transaction_reference')->nullable();
            $table->string('screenshot_path')->nullable();
            // Chèque
            $table->string('cheque_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->date('cheque_date')->nullable();
            // Admin
            $table->text('notes')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->string('receipt_path')->nullable();
            // Période couverte (cotisation)
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};