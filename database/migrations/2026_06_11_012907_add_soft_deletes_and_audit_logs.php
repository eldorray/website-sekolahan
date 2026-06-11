<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Tables that gain recoverable (soft) deletes. */
    protected array $softDeleteTables = [
        'news',
        'programs',
        'ppdb_registrations',
        'contact_messages',
        'brochures',
        'gallery_albums',
        'gallery_photos',
    ];

    public function up(): void
    {
        foreach ($this->softDeleteTables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->softDeletes();
            });
        }

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event'); // created, updated, deleted, restored
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->json('changes')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['auditable_type', 'auditable_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');

        foreach ($this->softDeleteTables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropSoftDeletes();
            });
        }
    }
};
