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
        Schema::create('assessment_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('session_code', 36)->unique();
            $table->integer('total_score')->default(0);
            $table->enum('risk_level', ['low','medium','high'])->nullable();
            $table->json('summary_json')->nullable();
            $table->string('client_user_agent', 255)->nullable();
            $table->timestamp('started_at')->useCurrent();
            $table->dateTime('completed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_sessions');
    }
};