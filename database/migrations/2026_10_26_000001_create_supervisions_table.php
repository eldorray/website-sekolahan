<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supervisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('revision')->default(0);
            $table->foreignId('teacher_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('supervisor_id')->constrained('users')->restrictOnDelete();
            $table->dateTime('scheduled_at')->index();
            $table->string('school_year', 9);
            $table->string('semester', 1);
            $table->string('class_name', 60);
            $table->string('subject', 120);
            $table->string('status')->default('dijadwalkan');
            $table->string('instrument_version');
            $table->json('instrument');
            $table->json('observations');
            $table->text('context')->nullable();
            $table->text('pre_observation')->nullable();
            $table->text('reflection')->nullable();
            $table->text('follow_up')->nullable();
            $table->date('follow_up_due')->nullable();
            $table->text('follow_up_evidence')->nullable();
            $table->text('verification')->nullable();
            $table->text('report')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervisions');
    }
};
