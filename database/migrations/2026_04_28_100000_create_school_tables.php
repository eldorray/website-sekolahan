<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('icon')->default('book');
            $table->text('short_description');
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('ARTIKEL');
            $table->text('excerpt');
            $table->longText('content');
            $table->string('image')->nullable();
            $table->date('published_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('ppdb_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->string('full_name');
            $table->string('nickname')->nullable();
            $table->string('gender');
            $table->string('birthplace');
            $table->date('birthdate');
            $table->string('previous_school')->nullable();
            $table->text('address');
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('parent_phone');
            $table->string('parent_email')->nullable();
            $table->string('grade_target');
            $table->string('status')->default('pending'); // pending, accepted, rejected
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('visit_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->date('visit_date');
            $table->integer('participants')->default(1);
            $table->text('purpose')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_schedules');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('ppdb_registrations');
        Schema::dropIfExists('news');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('settings');
    }
};
