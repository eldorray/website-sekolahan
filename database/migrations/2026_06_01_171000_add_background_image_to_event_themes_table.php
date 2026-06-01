<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_themes', function (Blueprint $table) {
            $table->string('background_image')->nullable()->after('background_color');
        });
    }

    public function down(): void
    {
        Schema::table('event_themes', function (Blueprint $table) {
            $table->dropColumn('background_image');
        });
    }
};
