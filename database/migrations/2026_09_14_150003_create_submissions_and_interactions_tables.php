<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exercise_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->nullable()->constrained()->nullOnDelete();
            $table->string('image_path')->nullable();
            $table->text('answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->json('ai_feedback')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();

            $table->index(['child_id', 'submitted_at']);
        });

        Schema::create('ai_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // homework_check, qa_chat
            $table->text('input_summary')->nullable();
            $table->longText('ai_response')->nullable();
            $table->string('model_used');
            $table->unsignedInteger('input_tokens')->default(0);
            $table->unsignedInteger('output_tokens')->default(0);
            $table->decimal('estimated_cost_usd', 10, 6)->default(0);
            $table->string('status')->default('success'); // success, failed
            $table->unsignedInteger('latency_ms')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['child_id', 'created_at']);
        });

        Schema::create('child_topic_mastery', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('mastery_score')->default(0); // 0 - 100
            $table->timestamps();

            $table->unique(['child_id', 'topic_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_topic_mastery');
        Schema::dropIfExists('ai_interactions');
        Schema::dropIfExists('exercise_submissions');
    }
};
