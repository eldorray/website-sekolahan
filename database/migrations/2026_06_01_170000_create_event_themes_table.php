<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('style')->default('default');
            $table->string('message')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('primary_color', 7)->default('#65ad36');
            $table->string('secondary_color', 7)->default('#ffffff');
            $table->string('accent_color', 7)->default('#f59e0b');
            $table->string('background_color', 7)->default('#f8fafc');
            $table->string('text_color', 7)->default('#0f172a');
            $table->integer('priority')->default(0);
            $table->boolean('repeat_annually')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'start_date', 'end_date']);
        });

        $now = now();

        DB::table('event_themes')->insert([
            [
                'name' => 'Isra Miraj Nabi Muhammad SAW',
                'style' => 'isra-miraj',
                'message' => 'Memaknai perjalanan spiritual dan perintah shalat.',
                'start_date' => '2026-01-16',
                'end_date' => '2026-01-16',
                'primary_color' => '#4f46e5',
                'secondary_color' => '#eef2ff',
                'accent_color' => '#fbbf24',
                'background_color' => '#eef2ff',
                'text_color' => '#312e81',
                'priority' => 70,
                'repeat_annually' => false,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Ramadhan',
                'style' => 'ramadan',
                'message' => 'Marhaban ya Ramadhan.',
                'start_date' => '2026-02-19',
                'end_date' => '2026-03-20',
                'primary_color' => '#047857',
                'secondary_color' => '#ecfdf5',
                'accent_color' => '#f59e0b',
                'background_color' => '#ecfdf5',
                'text_color' => '#064e3b',
                'priority' => 80,
                'repeat_annually' => false,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Idul Fitri',
                'style' => 'eid-fitr',
                'message' => 'Taqabbalallahu minna wa minkum.',
                'start_date' => '2026-03-21',
                'end_date' => '2026-03-24',
                'primary_color' => '#16a34a',
                'secondary_color' => '#f0fdf4',
                'accent_color' => '#facc15',
                'background_color' => '#f0fdf4',
                'text_color' => '#14532d',
                'priority' => 100,
                'repeat_annually' => false,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Hari Pendidikan Nasional',
                'style' => 'default',
                'message' => 'Bergerak bersama untuk pendidikan yang lebih baik.',
                'start_date' => '2026-05-02',
                'end_date' => '2026-05-02',
                'primary_color' => '#2563eb',
                'secondary_color' => '#eff6ff',
                'accent_color' => '#f59e0b',
                'background_color' => '#eff6ff',
                'text_color' => '#1e3a8a',
                'priority' => 50,
                'repeat_annually' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Idul Adha',
                'style' => 'eid-adha',
                'message' => 'Selamat Hari Raya Idul Adha.',
                'start_date' => '2026-05-27',
                'end_date' => '2026-05-28',
                'primary_color' => '#b45309',
                'secondary_color' => '#fffbeb',
                'accent_color' => '#15803d',
                'background_color' => '#fffbeb',
                'text_color' => '#78350f',
                'priority' => 95,
                'repeat_annually' => false,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Tahun Baru Islam',
                'style' => 'islamic-new-year',
                'message' => 'Selamat Tahun Baru Islam.',
                'start_date' => '2026-06-16',
                'end_date' => '2026-06-16',
                'primary_color' => '#0f766e',
                'secondary_color' => '#f0fdfa',
                'accent_color' => '#fbbf24',
                'background_color' => '#f0fdfa',
                'text_color' => '#134e4a',
                'priority' => 70,
                'repeat_annually' => false,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Hari Kemerdekaan RI',
                'style' => 'independence',
                'message' => 'Dirgahayu Republik Indonesia.',
                'start_date' => '2026-08-17',
                'end_date' => '2026-08-17',
                'primary_color' => '#dc2626',
                'secondary_color' => '#ffffff',
                'accent_color' => '#facc15',
                'background_color' => '#fff1f2',
                'text_color' => '#7f1d1d',
                'priority' => 90,
                'repeat_annually' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Maulid Nabi Muhammad SAW',
                'style' => 'maulid',
                'message' => 'Meneladani akhlak Rasulullah SAW.',
                'start_date' => '2026-08-25',
                'end_date' => '2026-08-25',
                'primary_color' => '#2563eb',
                'secondary_color' => '#eff6ff',
                'accent_color' => '#f59e0b',
                'background_color' => '#eff6ff',
                'text_color' => '#1e3a8a',
                'priority' => 70,
                'repeat_annually' => false,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Hari Santri Nasional',
                'style' => 'islamic-new-year',
                'message' => 'Menjaga semangat ilmu, adab, dan pengabdian.',
                'start_date' => '2026-10-22',
                'end_date' => '2026-10-22',
                'primary_color' => '#0f766e',
                'secondary_color' => '#f0fdfa',
                'accent_color' => '#fbbf24',
                'background_color' => '#f0fdfa',
                'text_color' => '#134e4a',
                'priority' => 50,
                'repeat_annually' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Hari Guru Nasional',
                'style' => 'default',
                'message' => 'Terima kasih guru atas ilmu dan keteladanan.',
                'start_date' => '2026-11-25',
                'end_date' => '2026-11-25',
                'primary_color' => '#7c3aed',
                'secondary_color' => '#f5f3ff',
                'accent_color' => '#f59e0b',
                'background_color' => '#f5f3ff',
                'text_color' => '#4c1d95',
                'priority' => 50,
                'repeat_annually' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('event_themes');
    }
};
