<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\Program;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@school.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '021-1234-5678',
            'position' => 'Administrator',
        ]);

        // Guru
        $teachers = [
            ['name' => 'Ust. Ahmad Fathoni', 'position' => 'Kepala Sekolah', 'email' => 'fathoni@school.id'],
            ['name' => 'Usth. Siti Aisyah', 'position' => 'Wakil Kurikulum', 'email' => 'aisyah@school.id'],
            ['name' => 'Ust. M. Rizky', 'position' => 'Guru Tahfidz', 'email' => 'rizky@school.id'],
            ['name' => 'Usth. Nabila Hasanah', 'position' => 'Guru Bahasa Inggris', 'email' => 'nabila@school.id'],
            ['name' => 'Ust. Hamzah', 'position' => 'Guru Akademik', 'email' => 'hamzah@school.id'],
            ['name' => 'Usth. Khadijah', 'position' => 'Guru Karakter', 'email' => 'khadijah@school.id'],
        ];

        foreach ($teachers as $i => $t) {
            User::create([
                'name' => $t['name'],
                'email' => $t['email'],
                'password' => Hash::make('password'),
                'role' => 'guru',
                'position' => $t['position'],
                'bio' => 'Pendidik berpengalaman yang berdedikasi dalam mengembangkan potensi siswa.',
                'instagram' => 'https://instagram.com/',
                'facebook' => 'https://facebook.com/',
            ]);
        }

        // Programs
        $programs = [
            ['title' => 'Akademik Unggulan', 'icon' => 'book', 'short_description' => 'Kurikulum nasional dengan pendekatan pembelajaran modern.'],
            ['title' => 'Tahfidz & Al-Qur\'an', 'icon' => 'book-open', 'short_description' => 'Program tahfidz dengan metode efektif dan pembinaan rutin.'],
            ['title' => 'Bahasa Asing', 'icon' => 'globe', 'short_description' => 'Penguasaan bahasa Inggris & Arab untuk kesiapan global.'],
            ['title' => 'Pengembangan Karakter', 'icon' => 'users', 'short_description' => 'Pembinaan adab, disiplin, kepemimpinan dan kepedulian sosial.'],
        ];
        foreach ($programs as $i => $p) {
            Program::create([
                'title' => $p['title'],
                'slug' => Str::slug($p['title']),
                'icon' => $p['icon'],
                'short_description' => $p['short_description'],
                'description' => '<p>'.$p['short_description'].' Program ini dirancang untuk mendukung perkembangan siswa secara holistik dengan pendekatan yang menyeluruh — akademis, spiritual, dan karakter.</p><p>Kami menggabungkan kurikulum modern dengan nilai-nilai Islam untuk membentuk generasi yang unggul.</p>',
                'order' => $i,
            ]);
        }

        // News
        $news = [
            ['title' => 'Study Tour ke Museum Sains, Siswa Belajar dengan Seru', 'category' => 'KEGIATAN', 'date' => '2026-04-20'],
            ['title' => 'Alifia Raih Juara 1 Olimpiade Matematika Tingkat Kota', 'category' => 'PRESTASI', 'date' => '2026-04-15'],
            ['title' => 'Pentingnya Membiasakan Literasi Sejak Dini', 'category' => 'ARTIKEL', 'date' => '2026-04-10'],
            ['title' => 'Program Tahfidz: Mencetak Generasi Qur\'ani', 'category' => 'ARTIKEL', 'date' => '2026-04-05'],
            ['title' => 'Pekan Olahraga & Seni Alifia 2026', 'category' => 'KEGIATAN', 'date' => '2026-03-28'],
        ];
        foreach ($news as $n) {
            News::create([
                'user_id' => $admin->id,
                'title' => $n['title'],
                'slug' => Str::slug($n['title']),
                'category' => $n['category'],
                'excerpt' => 'Belajar tidak harus selalu di kelas, siswa kelas 5 mengeksplorasi dunia sains secara langsung dan penuh keseruan.',
                'content' => '<p>Belajar tidak harus selalu di kelas. Siswa-siswi Alifia Modern School berkesempatan untuk mengeksplorasi dunia sains secara langsung melalui kegiatan study tour yang seru dan edukatif.</p><p>Kegiatan ini bertujuan untuk meningkatkan minat belajar siswa serta memberikan pengalaman langsung tentang berbagai konsep sains yang mereka pelajari di kelas.</p>',
                'published_at' => $n['date'],
                'is_published' => true,
            ]);
        }

        // Settings
        $settings = [
            'school_name' => 'Modern School',
            'tagline' => 'Modern School',
            'hero_title_1' => ' Modern',
            'hero_title_2' => 'School',
            'hero_subtitle' => 'Membentuk generasi berakhlak mulia, cerdas, dan berwawasan global melalui pendidikan berkualitas.',
            'hero_welcome' => 'Selamat Datang di',
            'hero_badge' => 'Sekolah Modern untuk Generasi Hebat',
            'stat_students' => '1.250+',
            'stat_teachers' => '80+',
            'stat_years' => '10+',
            'address' => 'Jl. Pendidikan No. 10, Kota Depok',
            'phone' => '(021) 1234-5678',
            'email' => 'info@modern.sch.id',
            'website' => 'www.modern.sch.id',
            'instagram' => 'https://instagram.com/modern',
            'facebook' => 'https://facebook.com/modern',
            'youtube' => 'https://youtube.com/@modern',
            'tiktok' => 'https://tiktok.com/@modern',
            'visi' => 'Menjadi lembaga pendidikan Islam modern terdepan dalam membentuk generasi unggul yang berakhlak mulia, cerdas, dan berwawasan global.',
            'misi' => "Menyelenggarakan pendidikan berkualitas\nMembentuk karakter Islami\nMengembangkan potensi siswa\nMendorong inovasi dan teknologi",
            'about_history' => ' Modern School berdiri sejak tahun 2014 dengan visi menjadi lembaga pendidikan Islam terdepan. Selama lebih dari satu dekade kami telah meluluskan ribuan siswa berprestasi yang siap menghadapi tantangan global dengan landasan iman, ilmu, dan akhlak mulia.',
            'about_image' => '',
            'ppdb_year' => '2026',
            'ppdb_open' => '1',
            'brand_color' => '#65ad36',
            'footer_about' => 'Sekolah Islam modern yang berkomitmen membentuk generasi unggul berlandaskan iman, ilmu, dan akhlak mulia.',
        ];
        foreach ($settings as $k => $v) {
            Setting::create(['key' => $k, 'value' => $v]);
        }
    }
}
