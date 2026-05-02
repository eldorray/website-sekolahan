<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modern School</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up {
            opacity: 0;
            animation: fadeUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        .animate-float-delayed {
            animation: float 7s ease-in-out infinite;
            animation-delay: 2s;
        }
        .animate-float-fast {
            animation: float 5s ease-in-out infinite;
            animation-delay: 1s;
        }
        
        .liquid-glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.05);
        }

        .liquid-glass-dark {
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.05);
        }
        
        .apple-transition {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .bg-main {
            background-image: url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&q=80&w=2000');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="font-sans antialiased text-slate-800 bg-[#F4F6F9]">

    <!-- Main Background for Top Section -->
    <div class="relative min-h-screen">
        <!-- Fixed Background Image -->
        <div class="absolute inset-0 bg-main h-[800px] z-0"></div>
        <div class="absolute inset-0 h-[800px] bg-white/30 backdrop-blur-sm z-0"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-[#F4F6F9]/80 to-[#F4F6F9] h-[800px] z-0"></div>

        <!-- Content Container -->
        <div class="relative z-10 pt-6 px-4 md:px-8 max-w-[1400px] mx-auto">
            
            <!-- Navbar -->
            <div class="animate-fade-up">
                <nav class="liquid-glass rounded-[2rem] px-4 md:px-6 py-3 flex items-center justify-between relative apple-transition hover:shadow-lg">
                    <!-- Logo -->
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#1e3a8a] rounded-xl flex items-center justify-center text-white shadow-sm">
                            <i data-lucide="shield-check" class="w-6 h-6"></i>
                        </div>
                        <div class="hidden sm:block">
                            <h1 class="font-bold text-lg leading-none text-slate-900">Modern School</h1>
                            <p class="text-[10px] text-slate-500 font-medium tracking-wide">Berilmu, Berkarakter, Berprestasi</p>
                        </div>
                    </div>

                    <!-- Links -->
                    <div class="hidden lg:flex items-center gap-2 text-sm font-medium text-slate-600 bg-white/50 rounded-full p-1.5 border border-white/60">
                        <a href="#" class="text-slate-900 bg-white px-5 py-2 rounded-full shadow-sm">Beranda</a>
                        <a href="#" class="px-5 py-2 rounded-full hover:bg-white/60 transition">Tentang Kami</a>
                        <a href="#" class="px-5 py-2 rounded-full hover:bg-white/60 transition">Program</a>
                        <a href="#" class="px-5 py-2 rounded-full hover:bg-white/60 transition">Berita</a>
                        <a href="#" class="px-5 py-2 rounded-full hover:bg-white/60 transition">Guru</a>
                        <a href="#" class="px-5 py-2 rounded-full hover:bg-white/60 transition">Kontak</a>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <button class="w-10 h-10 flex items-center justify-center rounded-full bg-white/80 text-slate-600 hover:bg-white transition border border-white/60 shadow-sm">
                            <i data-lucide="search" class="w-5 h-5"></i>
                        </button>
                        <button class="hidden sm:flex items-center gap-2 bg-white/80 px-4 py-2.5 rounded-full text-sm font-medium text-slate-700 hover:bg-white transition border border-white/60 shadow-sm">
                            <i data-lucide="sun" class="w-4 h-4 text-amber-500 fill-amber-500"></i> Light
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </button>
                        <!-- Mobile Menu Toggle -->
                        <button class="lg:hidden w-10 h-10 flex items-center justify-center rounded-full bg-white/80 text-slate-600 hover:bg-white transition border border-white/60 shadow-sm">
                            <i data-lucide="menu" class="w-5 h-5"></i>
                        </button>
                    </div>
                </nav>
            </div>

            <!-- Hero Section -->
            <div class="mt-6">
                <div class="liquid-glass rounded-[3rem] overflow-hidden relative flex flex-col lg:flex-row shadow-[0_20px_40px_rgb(0,0,0,0.05)] border border-white/80 min-h-[600px]">
                    
                    <!-- Left Content -->
                    <div class="flex-1 relative z-10 flex flex-col justify-center p-8 md:p-16 lg:p-20">
                        <div class="animate-fade-up delay-100 inline-flex items-center gap-2 bg-white/60 backdrop-blur-md text-slate-700 px-5 py-2.5 rounded-full text-sm font-semibold w-fit mb-6 border border-white/60 shadow-sm">
                            Selamat Datang di
                        </div>
                        <h2 class="animate-fade-up delay-200 text-5xl md:text-6xl lg:text-7xl font-bold mb-6 tracking-tight leading-tight">
                            <span class="text-[#4f46e5]">Modern</span> <span class="text-[#9333ea]">School</span>
                        </h2>
                        <p class="animate-fade-up delay-300 text-lg text-slate-600 mb-10 leading-relaxed max-w-lg font-medium">
                            Membentuk generasi unggul yang berilmu, <span class="text-slate-400">berkarakter,</span><br class="hidden md:block"> dan siap menghadapi masa depan.
                        </p>
                        <div class="animate-fade-up delay-400 flex flex-wrap items-center gap-4">
                            <a href="#" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#4f46e5] to-[#6366f1] text-white px-8 py-3.5 rounded-full font-semibold apple-transition hover:scale-105 hover:shadow-[0_10px_20px_rgba(79,70,229,0.3)] w-full sm:w-auto">
                                Tentang Kami <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </a>
                            <a href="#" class="inline-flex items-center justify-center gap-2 bg-white/80 backdrop-blur-md text-slate-800 px-8 py-3.5 rounded-full font-semibold apple-transition hover:scale-105 hover:shadow-lg border border-white/60 w-full sm:w-auto">
                                Lihat Program <i data-lucide="arrow-right" class="w-4 h-4 text-slate-400"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Right Image & Stats -->
                    <div class="flex-1 relative min-h-[400px] lg:min-h-full rounded-b-[3rem] lg:rounded-l-none lg:rounded-r-[3rem] overflow-hidden">
                        <!-- Image -->
                        <div class="absolute inset-0">
                            <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&q=80&w=1000" alt="School Building" class="w-full h-full object-cover" />
                            <div class="absolute inset-0 bg-gradient-to-t lg:bg-gradient-to-r from-white/90 via-transparent to-transparent lg:from-white/90 lg:via-white/20 lg:to-transparent"></div>
                        </div>

                        <!-- Stats Cards Container -->
                        <div class="absolute inset-0 flex flex-col justify-center items-center lg:items-start gap-4 p-8 lg:p-0 lg:-ml-10">
                            
                            <!-- Stat 1 -->
                            <div class="animate-float liquid-glass-dark p-4 rounded-[1.5rem] flex items-center gap-4 w-64 lg:w-72 transform lg:translate-x-10 apple-transition hover:scale-105 hover:bg-white/60">
                                <div class="w-14 h-14 rounded-2xl bg-blue-50/80 backdrop-blur-sm flex items-center justify-center text-blue-600 shadow-inner">
                                    <i data-lucide="trophy" class="w-7 h-7"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-2xl text-slate-800 drop-shadow-sm">1250+</div>
                                    <div class="text-[13px] font-semibold text-slate-600">Siswa Aktif</div>
                                </div>
                            </div>

                            <!-- Stat 2 -->
                            <div class="animate-float-delayed liquid-glass-dark p-4 rounded-[1.5rem] flex items-center gap-4 w-64 lg:w-72 transform lg:-translate-x-4 apple-transition hover:scale-105 hover:bg-white/60">
                                <div class="w-14 h-14 rounded-2xl bg-red-50/80 backdrop-blur-sm flex items-center justify-center text-red-500 shadow-inner">
                                    <i data-lucide="graduation-cap" class="w-7 h-7"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-2xl text-slate-800 drop-shadow-sm">85+</div>
                                    <div class="text-[13px] font-semibold text-slate-600">Guru Profesional</div>
                                </div>
                            </div>

                            <!-- Stat 3 -->
                            <div class="animate-float-fast liquid-glass-dark p-4 rounded-[1.5rem] flex items-center gap-4 w-64 lg:w-72 transform lg:translate-x-10 apple-transition hover:scale-105 hover:bg-white/60">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-50/80 backdrop-blur-sm flex items-center justify-center text-emerald-500 shadow-inner">
                                    <i data-lucide="book-open" class="w-7 h-7"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-2xl text-slate-800 drop-shadow-sm">20+</div>
                                    <div class="text-[13px] font-semibold text-slate-600">Program Unggulan</div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Visi Misi & Program -->
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-8 relative z-20">
                <!-- Visi Misi -->
                <div class="lg:col-span-5 liquid-glass rounded-[2rem] p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                        <h3 class="text-xl font-bold text-slate-900 relative">
                            Visi & Misi
                            <div class="absolute -bottom-2 left-0 w-8 h-1 bg-[#4f46e5] rounded-full"></div>
                        </h3>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Visi -->
                        <div class="bg-white/50 backdrop-blur-sm p-6 rounded-[1.5rem] border border-white/60 shadow-sm apple-transition hover:-translate-y-1 hover:shadow-md">
                            <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-5">
                                <i data-lucide="eye" class="w-5 h-5 fill-blue-600/20 text-blue-600"></i>
                            </div>
                            <h4 class="font-bold text-[15px] mb-2">Visi</h4>
                            <p class="text-[13px] text-slate-600 leading-relaxed font-medium">
                                Menjadi sekolah unggulan yang mencetak generasi berkarakter, berprestasi, dan berwawasan global.
                            </p>
                        </div>
                        <!-- Misi -->
                        <div class="bg-white/50 backdrop-blur-sm p-6 rounded-[1.5rem] border border-white/60 shadow-sm apple-transition hover:-translate-y-1 hover:shadow-md">
                            <div class="w-12 h-12 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center mb-5">
                                <i data-lucide="target" class="w-5 h-5"></i>
                            </div>
                            <h4 class="font-bold text-[15px] mb-2">Misi</h4>
                            <ul class="text-[13px] text-slate-600 leading-relaxed list-disc list-outside ml-4 space-y-1 font-medium">
                                <li>Menyelenggarakan pendidikan berkualitas.</li>
                                <li>Mengembangkan potensi siswa secara optimal.</li>
                                <li>Menanamkan nilai-nilai karakter dalam kehidupan sehari-hari.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Program Sekolah -->
                <div class="lg:col-span-7 liquid-glass rounded-[2rem] p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                        <h3 class="text-xl font-bold text-slate-900 relative">
                            Program Sekolah
                            <div class="absolute -bottom-2 left-0 w-8 h-1 bg-emerald-500 rounded-full"></div>
                        </h3>
                        <a href="#" class="inline-flex items-center gap-2 text-[13px] font-semibold text-[#4f46e5] bg-white/60 backdrop-blur-sm px-4 py-2 rounded-full border border-white/80 hover:bg-white transition shadow-sm self-start sm:self-auto">
                            Lihat Semua <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-white/50 backdrop-blur-sm p-6 rounded-[1.5rem] text-center border border-white/60 shadow-sm apple-transition hover:-translate-y-1 hover:shadow-md group cursor-pointer">
                            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition duration-300 shadow-inner">
                                <i data-lucide="book" class="w-6 h-6"></i>
                            </div>
                            <h4 class="font-bold text-[14px] mb-2">Akademik Unggul</h4>
                            <p class="text-[12px] text-slate-500 leading-relaxed font-medium">Kurikulum berbasis nasional dengan pendekatan modern.</p>
                        </div>
                        <div class="bg-white/50 backdrop-blur-sm p-6 rounded-[1.5rem] text-center border border-white/60 shadow-sm apple-transition hover:-translate-y-1 hover:shadow-md group cursor-pointer">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition duration-300 shadow-inner">
                                <i data-lucide="flask-conical" class="w-6 h-6"></i>
                            </div>
                            <h4 class="font-bold text-[14px] mb-2">Sains & Teknologi</h4>
                            <p class="text-[12px] text-slate-500 leading-relaxed font-medium">Mengembangkan minat dan bakat di bidang sains dan teknologi.</p>
                        </div>
                        <div class="bg-white/50 backdrop-blur-sm p-6 rounded-[1.5rem] text-center border border-white/60 shadow-sm apple-transition hover:-translate-y-1 hover:shadow-md group cursor-pointer">
                            <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-500 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition duration-300 shadow-inner">
                                <i data-lucide="palette" class="w-6 h-6"></i>
                            </div>
                            <h4 class="font-bold text-[14px] mb-2">Seni & Budaya</h4>
                            <p class="text-[12px] text-slate-500 leading-relaxed font-medium">Menumbuhkan kreativitas melalui seni dan budaya.</p>
                        </div>
                        <div class="bg-white/50 backdrop-blur-sm p-6 rounded-[1.5rem] text-center border border-white/60 shadow-sm apple-transition hover:-translate-y-1 hover:shadow-md group cursor-pointer">
                            <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition duration-300 shadow-inner">
                                <i data-lucide="dribbble" class="w-6 h-6"></i>
                            </div>
                            <h4 class="font-bold text-[14px] mb-2">Olahraga</h4>
                            <p class="text-[12px] text-slate-500 leading-relaxed font-medium">Membina kesehatan dan sportivitas siswa.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Berita & CTA -->
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-8 relative z-20">
                <!-- Berita Terbaru -->
                <div class="lg:col-span-7 liquid-glass rounded-[2rem] p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                        <h3 class="text-xl font-bold text-slate-900 relative">
                            Berita Terbaru
                            <div class="absolute -bottom-2 left-0 w-8 h-1 bg-[#4f46e5] rounded-full"></div>
                        </h3>
                        <a href="#" class="inline-flex items-center gap-2 text-[13px] font-semibold text-[#4f46e5] bg-white/60 backdrop-blur-sm px-4 py-2 rounded-full border border-white/80 hover:bg-white transition shadow-sm self-start sm:self-auto">
                            Lihat Semua <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- News 1 -->
                        <div class="bg-white/60 backdrop-blur-sm rounded-[1.5rem] overflow-hidden shadow-sm border border-white/80 group cursor-pointer apple-transition hover:-translate-y-1 hover:shadow-md">
                            <div class="relative h-40 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&q=80&w=600" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Berita 1">
                                <div class="absolute top-3 left-3 bg-[#4f46e5] text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-sm">Kegiatan</div>
                            </div>
                            <div class="p-5">
                                <div class="text-[11px] text-slate-500 font-medium mb-2">20 Mei 2024</div>
                                <h4 class="font-bold text-[14px] mb-4 leading-snug group-hover:text-[#4f46e5] transition">Field Trip Siswa Kelas 8 ke Museum Nasional</h4>
                                <a href="#" class="text-[#4f46e5] text-[13px] font-semibold flex items-center gap-1 hover:gap-2 transition-all">Baca Selengkapnya <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i></a>
                            </div>
                        </div>
                        <!-- News 2 -->
                        <div class="bg-white/60 backdrop-blur-sm rounded-[1.5rem] overflow-hidden shadow-sm border border-white/80 group cursor-pointer apple-transition hover:-translate-y-1 hover:shadow-md">
                            <div class="relative h-40 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&q=80&w=600" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Berita 2">
                                <div class="absolute top-3 left-3 bg-emerald-500 text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-sm">Prestasi</div>
                            </div>
                            <div class="p-5">
                                <div class="text-[11px] text-slate-500 font-medium mb-2">18 Mei 2024</div>
                                <h4 class="font-bold text-[14px] mb-4 leading-snug group-hover:text-[#4f46e5] transition">Tim Sains Modern School Raih Juara 1 Olimpiade</h4>
                                <a href="#" class="text-[#4f46e5] text-[13px] font-semibold flex items-center gap-1 hover:gap-2 transition-all">Baca Selengkapnya <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i></a>
                            </div>
                        </div>
                        <!-- News 3 -->
                        <div class="bg-white/60 backdrop-blur-sm rounded-[1.5rem] overflow-hidden shadow-sm border border-white/80 group cursor-pointer apple-transition hover:-translate-y-1 hover:shadow-md">
                            <div class="relative h-40 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&q=80&w=600" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Berita 3">
                                <div class="absolute top-3 left-3 bg-orange-500 text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-sm">Pengumuman</div>
                            </div>
                            <div class="p-5">
                                <div class="text-[11px] text-slate-500 font-medium mb-2">15 Mei 2024</div>
                                <h4 class="font-bold text-[14px] mb-4 leading-snug group-hover:text-[#4f46e5] transition">Penerimaan Siswa Baru T.A. 2024/2025 Dibuka</h4>
                                <a href="#" class="text-[#4f46e5] text-[13px] font-semibold flex items-center gap-1 hover:gap-2 transition-all">Baca Selengkapnya <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA -->
                <div class="lg:col-span-5">
                    <div class="h-full bg-gradient-to-br from-[#4f46e5] to-[#8b5cf6] rounded-[2rem] p-8 md:p-10 text-white relative overflow-hidden flex flex-col justify-center shadow-lg">
                        <!-- Decor -->
                        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/20 rounded-full blur-2xl"></div>
                        <div class="absolute -left-10 top-10 w-32 h-32 bg-blue-400/30 rounded-full blur-xl"></div>
                        
                        <div class="relative z-10">
                            <h3 class="text-2xl md:text-3xl font-bold mb-4 leading-snug">Ayo Bergabung Bersama Kami!</h3>
                            <p class="text-blue-100 mb-8 text-[14px] md:text-[15px] leading-relaxed max-w-sm">Raih masa depan cerah bersama pendidikan berkualitas di Modern School.</p>
                            <a href="#" class="inline-flex items-center gap-2 bg-white/90 backdrop-blur-md text-[#4f46e5] px-6 py-3 rounded-full font-bold text-sm hover:bg-white transition shadow-lg w-fit apple-transition hover:scale-105">
                                Daftar Sekarang <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </a>
                        </div>
                        <!-- Illustration Placeholder -->
                        <div class="absolute -right-8 -bottom-8 w-48 h-48 md:w-64 md:h-64 opacity-90 pointer-events-none">
                            <img src="https://images.unsplash.com/photo-1532012197267-da84d127e765?auto=format&fit=crop&q=80&w=400" class="w-full h-full object-cover rounded-full mix-blend-overlay opacity-40 blur-[2px]" alt="Books">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guru Profesional -->
            <div class="mt-8 liquid-glass p-8 rounded-[2rem] relative z-20 mb-24">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                    <h3 class="text-xl font-bold text-slate-900 relative">
                        Guru Profesional
                        <div class="absolute -bottom-2 left-0 w-8 h-1 bg-slate-800 rounded-full"></div>
                    </h3>
                    <div class="flex items-center gap-3 self-start sm:self-auto">
                        <a href="#" class="inline-flex items-center gap-2 text-[13px] font-semibold text-[#4f46e5] bg-white/60 backdrop-blur-sm px-4 py-2 rounded-full border border-white/80 hover:bg-white transition shadow-sm">
                            Lihat Semua <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                        <button class="hidden sm:flex w-10 h-10 rounded-full bg-white/60 backdrop-blur-sm border border-white/80 items-center justify-center text-slate-600 hover:bg-white transition shadow-sm">
                            <i data-lucide="chevron-right" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Teacher 1 -->
                    <div class="bg-white/50 backdrop-blur-sm p-4 rounded-[1.5rem] border border-white/60 flex items-center gap-4 hover:shadow-md transition apple-transition hover:-translate-y-1">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" class="w-16 h-16 rounded-[1rem] object-cover shadow-sm" alt="Guru 1">
                        <div>
                            <h4 class="font-bold text-[13px]">Dian Puspita, M.Pd.</h4>
                            <p class="text-[11px] text-slate-500 mb-2 font-medium">Guru Matematika</p>
                            <div class="flex items-center gap-1.5">
                                <a href="#" class="w-6 h-6 rounded-full bg-white/80 flex items-center justify-center text-slate-400 hover:text-[#4f46e5] shadow-sm transition"><i data-lucide="mail" class="w-3 h-3"></i></a>
                                <a href="#" class="w-6 h-6 rounded-full bg-white/80 flex items-center justify-center text-slate-400 hover:text-[#4f46e5] shadow-sm transition"><i data-lucide="linkedin" class="w-3 h-3"></i></a>
                                <a href="#" class="w-6 h-6 rounded-full bg-white/80 flex items-center justify-center text-slate-400 hover:text-pink-600 shadow-sm transition"><i data-lucide="instagram" class="w-3 h-3"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- Teacher 2 -->
                    <div class="bg-white/50 backdrop-blur-sm p-4 rounded-[1.5rem] border border-white/60 flex items-center gap-4 hover:shadow-md transition apple-transition hover:-translate-y-1">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" class="w-16 h-16 rounded-[1rem] object-cover shadow-sm" alt="Guru 2">
                        <div>
                            <h4 class="font-bold text-[13px]">Andi Setiawan, M.Pd.</h4>
                            <p class="text-[11px] text-slate-500 mb-2 font-medium">Guru Fisika</p>
                            <div class="flex items-center gap-1.5">
                                <a href="#" class="w-6 h-6 rounded-full bg-white/80 flex items-center justify-center text-slate-400 hover:text-[#4f46e5] shadow-sm transition"><i data-lucide="mail" class="w-3 h-3"></i></a>
                                <a href="#" class="w-6 h-6 rounded-full bg-white/80 flex items-center justify-center text-slate-400 hover:text-[#4f46e5] shadow-sm transition"><i data-lucide="linkedin" class="w-3 h-3"></i></a>
                                <a href="#" class="w-6 h-6 rounded-full bg-white/80 flex items-center justify-center text-slate-400 hover:text-pink-600 shadow-sm transition"><i data-lucide="instagram" class="w-3 h-3"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- Teacher 3 -->
                    <div class="bg-white/50 backdrop-blur-sm p-4 rounded-[1.5rem] border border-white/60 flex items-center gap-4 hover:shadow-md transition apple-transition hover:-translate-y-1">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" class="w-16 h-16 rounded-[1rem] object-cover shadow-sm" alt="Guru 3">
                        <div>
                            <h4 class="font-bold text-[13px]">Rina Wulandari, M.Pd.</h4>
                            <p class="text-[11px] text-slate-500 mb-2 font-medium">Guru B. Inggris</p>
                            <div class="flex items-center gap-1.5">
                                <a href="#" class="w-6 h-6 rounded-full bg-white/80 flex items-center justify-center text-slate-400 hover:text-[#4f46e5] shadow-sm transition"><i data-lucide="mail" class="w-3 h-3"></i></a>
                                <a href="#" class="w-6 h-6 rounded-full bg-white/80 flex items-center justify-center text-slate-400 hover:text-[#4f46e5] shadow-sm transition"><i data-lucide="linkedin" class="w-3 h-3"></i></a>
                                <a href="#" class="w-6 h-6 rounded-full bg-white/80 flex items-center justify-center text-slate-400 hover:text-pink-600 shadow-sm transition"><i data-lucide="instagram" class="w-3 h-3"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- Teacher 4 -->
                    <div class="bg-white/50 backdrop-blur-sm p-4 rounded-[1.5rem] border border-white/60 flex items-center gap-4 hover:shadow-md transition apple-transition hover:-translate-y-1">
                        <img src="https://randomuser.me/api/portraits/men/45.jpg" class="w-16 h-16 rounded-[1rem] object-cover shadow-sm" alt="Guru 4">
                        <div>
                            <h4 class="font-bold text-[13px]">Budi Santoso, M.Pd.</h4>
                            <p class="text-[11px] text-slate-500 mb-2 font-medium">Guru Informatika</p>
                            <div class="flex items-center gap-1.5">
                                <a href="#" class="w-6 h-6 rounded-full bg-white/80 flex items-center justify-center text-slate-400 hover:text-[#4f46e5] shadow-sm transition"><i data-lucide="mail" class="w-3 h-3"></i></a>
                                <a href="#" class="w-6 h-6 rounded-full bg-white/80 flex items-center justify-center text-slate-400 hover:text-[#4f46e5] shadow-sm transition"><i data-lucide="linkedin" class="w-3 h-3"></i></a>
                                <a href="#" class="w-6 h-6 rounded-full bg-white/80 flex items-center justify-center text-slate-400 hover:text-pink-600 shadow-sm transition"><i data-lucide="instagram" class="w-3 h-3"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer (Inside the container) -->
            <footer class="liquid-glass rounded-t-[3rem] pt-16 pb-8 mt-12 relative z-20">
                <div class="px-8 md:px-12">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10 mb-12">
                        <!-- Branding -->
                        <div class="lg:col-span-4">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-[#1e3a8a] rounded-xl flex items-center justify-center text-white shadow-sm">
                                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h1 class="font-bold text-lg leading-none text-slate-900 mb-1">Modern School</h1>
                                    <p class="text-[10px] text-slate-500 font-medium tracking-wide">Berilmu, Berkarakter, Berprestasi</p>
                                </div>
                            </div>
                            <p class="text-[13px] text-slate-600 leading-relaxed mb-8 max-w-sm font-medium">
                                Modern School berkomitmen memberikan pendidikan berkualitas untuk masa depan yang lebih cerah.
                            </p>
                            <div class="flex items-center gap-3">
                                <a href="#" class="w-9 h-9 rounded-full bg-white/80 border border-white/60 flex items-center justify-center text-slate-600 hover:text-[#4f46e5] hover:bg-white transition shadow-sm"><i data-lucide="facebook" class="w-4 h-4"></i></a>
                                <a href="#" class="w-9 h-9 rounded-full bg-white/80 border border-white/60 flex items-center justify-center text-slate-600 hover:text-pink-600 hover:bg-white transition shadow-sm"><i data-lucide="instagram" class="w-4 h-4"></i></a>
                                <a href="#" class="w-9 h-9 rounded-full bg-white/80 border border-white/60 flex items-center justify-center text-slate-600 hover:text-red-600 hover:bg-white transition shadow-sm"><i data-lucide="youtube" class="w-4 h-4"></i></a>
                                <a href="#" class="w-9 h-9 rounded-full bg-white/80 border border-white/60 flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-white transition shadow-sm">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>
                                </a>
                            </div>
                        </div>

                        <!-- Navigasi -->
                        <div class="lg:col-span-2">
                            <h4 class="font-bold text-slate-900 mb-6 text-[14px]">Navigasi</h4>
                            <ul class="space-y-3 text-[13px] text-slate-600 font-medium">
                                <li><a href="#" class="hover:text-[#4f46e5] transition">Beranda</a></li>
                                <li><a href="#" class="hover:text-[#4f46e5] transition">Tentang Kami</a></li>
                                <li><a href="#" class="hover:text-[#4f46e5] transition">Program</a></li>
                                <li><a href="#" class="hover:text-[#4f46e5] transition">Berita</a></li>
                                <li><a href="#" class="hover:text-[#4f46e5] transition">Guru</a></li>
                                <li><a href="#" class="hover:text-[#4f46e5] transition">Kontak</a></li>
                            </ul>
                        </div>

                        <!-- Program -->
                        <div class="lg:col-span-2">
                            <h4 class="font-bold text-slate-900 mb-6 text-[14px]">Program</h4>
                            <ul class="space-y-3 text-[13px] text-slate-600 font-medium">
                                <li><a href="#" class="hover:text-[#4f46e5] transition">Akademik</a></li>
                                <li><a href="#" class="hover:text-[#4f46e5] transition">Sains & Teknologi</a></li>
                                <li><a href="#" class="hover:text-[#4f46e5] transition">Seni & Budaya</a></li>
                                <li><a href="#" class="hover:text-[#4f46e5] transition">Olahraga</a></li>
                                <li><a href="#" class="hover:text-[#4f46e5] transition">Ekstrakurikuler</a></li>
                            </ul>
                        </div>

                        <!-- Kontak & Map -->
                        <div class="lg:col-span-4">
                            <h4 class="font-bold text-slate-900 mb-6 text-[14px]">Kontak</h4>
                            <ul class="space-y-3 text-[13px] text-slate-600 font-medium mb-6">
                                <li class="flex items-start gap-3">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 mt-0.5"></i>
                                    <span>Jl. Pendidikan No. 1<br>Jakarta, Indonesia</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i data-lucide="phone" class="w-4 h-4 text-slate-400"></i>
                                    <span>(021) 1234 5678</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i data-lucide="mail" class="w-4 h-4 text-slate-400"></i>
                                    <span>info@modernschool.sch.id</span>
                                </li>
                            </ul>
                            <div class="w-full h-24 bg-white/50 rounded-2xl overflow-hidden relative shadow-inner border border-white/60">
                                <img src="https://images.unsplash.com/photo-1524661135-423995f22d0b?auto=format&fit=crop&q=80&w=600" class="w-full h-full object-cover opacity-50" alt="Map">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center animate-pulse">
                                        <div class="w-3 h-3 rounded-full bg-red-500 border-2 border-white shadow-sm"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Footer -->
                    <div class="pt-6 border-t border-white/60 flex flex-col md:flex-row items-center justify-between gap-4 text-[11px] font-medium text-slate-500 relative">
                        <div>&copy; {{ date('Y') }} Modern School. All rights reserved.</div>
                        <div class="flex items-center gap-4">
                            <a href="#" class="hover:text-[#4f46e5] transition">Kebijakan Privasi</a>
                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                            <a href="#" class="hover:text-[#4f46e5] transition">Syarat & Ketentuan</a>
                        </div>
                        <!-- Scroll to top button -->
                        <button class="w-10 h-10 rounded-full bg-[#4f46e5] text-white flex items-center justify-center hover:bg-[#4338ca] transition shadow-lg shadow-indigo-500/30 absolute right-0 -top-5 apple-transition hover:-translate-y-1">
                            <i data-lucide="arrow-up" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>