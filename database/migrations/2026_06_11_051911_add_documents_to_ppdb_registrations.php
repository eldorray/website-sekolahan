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
        Schema::table('ppdb_registrations', function (Blueprint $table) {
            // Relative paths on the private "local" disk (not publicly served).
            $table->string('kk_file')->nullable()->after('grade_target');
            $table->string('birth_certificate_file')->nullable()->after('kk_file');
        });
    }

    public function down(): void
    {
        Schema::table('ppdb_registrations', function (Blueprint $table) {
            $table->dropColumn(['kk_file', 'birth_certificate_file']);
        });
    }
};
