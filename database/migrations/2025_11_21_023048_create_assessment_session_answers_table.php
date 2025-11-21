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
        Schema::create('assessment_session_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('answer_option_id')->nullable();
            $table->text('answer_text')->nullable();
            $table->integer('score_value')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('session_id')->references('id')->on('assessment_sessions')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreign('question_id')->references('id')->on('assessment_questions')
                ->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('answer_option_id')->references('id')->on('assessment_answer_options')
                ->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_session_answers');
    }
};