<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adiwiyata_assessments', function (Blueprint $table) {
            $table->id();
            // Path folder di Drive, mis. "2026_... / 3. Dokumen ... / 24. Jumlah ...".
            // Terpanjang di snapshot saat ini 203 karakter.
            $table->string('folder_key')->unique();
            $table->string('status', 10);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adiwiyata_assessments');
    }
};
