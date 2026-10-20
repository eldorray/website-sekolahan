<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spp_payments', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->string('proof_path')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->index(['status', 'confirmed_at']);
        });
    }

    public function down(): void
    {
        Schema::table('spp_payments', function (Blueprint $table) {
            $table->dropIndex(['status', 'confirmed_at']);
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['description', 'proof_path', 'confirmed_at', 'reviewed_at', 'review_notes']);
        });
    }
};
