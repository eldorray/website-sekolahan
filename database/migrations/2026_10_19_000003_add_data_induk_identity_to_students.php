<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('data_induk_source', 10)->nullable();
            $table->unsignedBigInteger('data_induk_id')->nullable();
            $table->unique(['data_induk_source', 'data_induk_id']);
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['data_induk_source', 'data_induk_id']);
            $table->dropColumn(['data_induk_source', 'data_induk_id']);
        });
    }
};
