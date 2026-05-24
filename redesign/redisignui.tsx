import React, { useState, useEffect, useRef } from 'react';
import { 
  Sparkles, 
  BookOpen, 
  Users, 
  ArrowRight, 
  ChevronRight, 
  Search, 
  Calendar, 
  MapPin, 
  Phone, 
  Clock, 
  ExternalLink, 
  Menu, 
  X, 
  CheckCircle, 
  Award, 
  Compass,
  GraduationCap,
  Target,
  Send,
  ArrowUpRight,
  ArrowLeft,
  ChevronDown,
  Globe,
  Monitor,
  Check,
  HelpCircle
} from 'lucide-react';

// --- KOMPONEN PENGAMAN ANIMASI SCROLL (REVEAL ON SCROLL) ---
function RevealOnScroll({ children, delay = 0, className = "" }) {
  const [isVisible, setIsVisible] = useState(false);
  const ref = useRef(null);

  useEffect(() => {
    // Pengaman jika browser tidak mendukung IntersectionObserver
    if (typeof window === 'undefined' || !window.IntersectionObserver) {
      setIsVisible(true);
      return;
    }

    const currentRef = ref.current;
    if (!currentRef) {
      setIsVisible(true);
      return;
    }

    try {
      const observer = new IntersectionObserver(
        ([entry]) => {
          if (entry && entry.isIntersecting) {
            setIsVisible(true);
            observer.unobserve(currentRef);
          }
        },
        { 
          threshold: 0.05,
          rootMargin: "0px 0px -80px 0px"
        }
      );
      
      observer.observe(currentRef);
      
      return () => {
        try {
          if (currentRef) observer.unobserve(currentRef);
        } catch (e) {
          // Abaikan error unobserve saat unmount
        }
        observer.disconnect();
      };
    } catch (err) {
      // Fallback langsung tampil jika terjadi error inisialisasi
      setIsVisible(true);
    }
  }, []);

  return (
    <div
      ref={ref}
      className={`transition-all duration-[1000ms] cubic-bezier(0.16, 1, 0.3, 1) ${
        isVisible ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 translate-y-12 scale-[0.98]'
      } ${className}`}
      style={{ transitionDelay: `${delay}ms`, willChange: 'transform, opacity' }}
    >
      {children}
    </div>
  );
}

export default function App() {
  const [activeSection, setActiveSection] = useState('home');
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [scrollY, setScrollY] = useState(0);
  const [mousePos, setMousePos] = useState({ x: 0, y: 0 });
  
  // State Navigasi Halaman Detail
  const [activeDetailPage, setActiveDetailPage] = useState(null);

  // State Tab Konten di halaman About
  const [activeAboutTab, setActiveAboutTab] = useState('visi');

  // State Filter & Pencarian Berita
  const [blogFilter, setBlogFilter] = useState('Semua');
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedBlog, setSelectedBlog] = useState(null);

  // State Filter Galeri & Lightbox
  const [galleryFilter, setGalleryFilter] = useState('Semua');
  const [activeImage, setActiveImage] = useState(null);

  // State Formulir Kontak
  const [formData, setFormData] = useState({ name: '', email: '', subject: '', message: '' });
  const [focusedField, setFocusedField] = useState(null);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitStatus, setSubmitStatus] = useState(null);

  // State Akordeon FAQ
  const [openFaq, setOpenFaq] = useState(null);

  useEffect(() => {
    const handleScroll = () => {
      setScrollY(window.scrollY);
      
      const sections = ['home', 'about', 'blog', 'gallery', 'contact'];
      const scrollPosition = window.scrollY + 200;
      
      for (const section of sections) {
        const el = document.getElementById(section);
        if (el) {
          const top = el.offsetTop;
          const height = el.offsetHeight;
          if (scrollPosition >= top && scrollPosition < top + height) {
            setActiveSection(section);
            break;
          }
        }
      }
    };

    const handleMouseMove = (e) => {
      const { clientX, clientY } = e;
      const width = window.innerWidth || 1000;
      const height = window.innerHeight || 800;
      const x = (clientX / width) - 0.5;
      const y = (clientY / height) - 0.5;
      setMousePos({ x, y });
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('mousemove', handleMouseMove, { passive: true });
    
    return () => {
      window.removeEventListener('scroll', handleScroll);
      window.removeEventListener('mousemove', handleMouseMove);
    };
  }, []);

  const scrollToSection = (id) => {
    setIsMenuOpen(false);
    setActiveDetailPage(null); 
    const element = document.getElementById(id);
    if (element) {
      window.scrollTo({
        top: element.offsetTop - 80,
        behavior: 'smooth'
      });
      setActiveSection(id);
    }
  };

  const blogPosts = [
    {
      id: 1,
      title: "Siswa SMA Google School Sabet Medali Emas Olimpiade Sains Nasional",
      category: "Prestasi",
      date: "20 Mei 2026",
      author: "Admin Humas",
      image: "https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&q=80&w=600",
      excerpt: "Tim robotik SMA Google School berhasil mengembangkan purwarupa pendeteksi dini bencana alam yang meraih penghargaan utama.",
      content: "Selamat kepada tim robotik sekolah yang berhasil mengharumkan nama sekolah di tingkat nasional. Melalui inovasi sistem sensor berbasis loT, para siswa berhasil memukau juri dalam ajang OSN 2026. Kepala sekolah mengungkapkan rasa bangga yang luar biasa atas kegigihan para siswa serta bimbingan intensif dari para guru mentor. Langkah berikutnya adalah mendaftarkan paten untuk purwarupa ini agar dapat diproduksi secara massal guna membantu mitigasi bencana di daerah rawan."
    },
    {
      id: 2,
      title: "Penerapan Pembelajaran Interaktif Berbasis AI dan AR di Kelas",
      category: "Akademik",
      date: "14 Mei 2026",
      author: "Dr. Amara (Kurikulum)",
      image: "https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&q=80&w=600",
      excerpt: "Kini pembelajaran biologi dan fisika semakin seru dengan kehadiran visualisasi Augmented Reality 3D.",
      content: "Mulai semester ini, SMA Google School resmi mengintegrasikan sistem pembelajaran berbasis Artificial Intelligence (AI) dan Augmented Reality (AR) di dalam ruang kelas. Para siswa dapat berinteraksi langsung dengan model organ dalam manusia atau galaksi bima sakti secara tiga dimensi. Metode ini terbukti meningkatkan pemahaman materi hingga 45% dan mendorong antusiasme riset mandiri di kalangan siswa."
    },
    {
      id: 3,
      title: "Keseruan Festival Seni Budaya Tahunan 'Google Art Fest 2026'",
      category: "Acara",
      date: "05 Mei 2026",
      author: "OSIS Team",
      image: "https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?auto=format&fit=crop&q=80&w=600",
      excerpt: "Pameran seni rupa, teater kolosal, hingga penampilan band indie sekolah memeriahkan suasana pekan kreasi.",
      content: "Festival seni tahunan kembali digelar dengan meriah. Mengusung tema 'Budaya Nusantara di Era Digital', festival kali ini menyatukan pagelaran tari tradisional dengan iringan musik digital modern dan pameran lukisan virtual reality (VR). Acara ini juga dihadiri oleh ratusan wali murid dan masyarakat umum yang sangat antusias menikmati berbagai kuliner tradisional nusantara hasil olahan kreasi kewirausahaan siswa."
    },
    {
      id: 4,
      title: "Pemberdayaan Karakter Pemimpin lewat Program Camp Kepemimpinan",
      category: "Karakter",
      date: "28 April 2026",
      author: "Bapak Rahmat, S.Pd",
      image: "https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&q=80&w=600",
      excerpt: "Siswa kelas X mengikuti pelatihan pengembangan kepribadian, ketangkasan, dan kerja sama tim.",
      content: "Selama tiga hari penuh, siswa kelas X mengikuti kegiatan Camp Kepemimpinan di kawasan alam terbuka. Kegiatan dirancang untuk melatih mental kepemimpinan, kepedulian sosial, serta kemampuan memecahkan masalah kompleks secara berkelompok. Program wajib tahunan ini diharapkan melahirkan lulusan berkarakter unggul yang tidak hanya hebat secara akademis, namun juga memiliki integritas tinggi dan kepedulian sosial nyata."
    }
  ];

  const galleryItems = [
    { id: 1, title: "Lab Komputer Modern", category: "Fasilitas", image: "https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&q=80&w=600" },
    { id: 2, title: "Siswa Berdiskusi di Taman", category: "Aktivitas", image: "https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&q=80&w=600" },
    { id: 3, title: "Perpustakaan Digital", category: "Fasilitas", image: "https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&q=80&w=600" },
    { id: 4, title: "Tim Basket Juara Wilayah", category: "Prestasi", image: "https://images.unsplash.com/photo-1546519638-68e109498ffc?auto=format&fit=crop&q=80&w=600" },
    { id: 5, title: "Kelas Interaktif Nyaman", category: "Fasilitas", image: "https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?auto=format&fit=crop&q=80&w=600" },
    { id: 6, title: "Penelitian Kimia Organik", category: "Aktivitas", image: "https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&q=80&w=600" }
  ];

  const filteredBlogs = blogPosts.filter(post => {
    const matchesCategory = blogFilter === 'Semua' || post.category === blogFilter;
    const matchesSearch = post.title.toLowerCase().includes(searchQuery.toLowerCase()) || 
                          post.excerpt.toLowerCase().includes(searchQuery.toLowerCase());
    return matchesCategory && matchesSearch;
  });

  const filteredGallery = galleryItems.filter(item => {
    return galleryFilter === 'Semua' || item.category === galleryFilter;
  });

  useEffect(() => {
    if (activeDetailPage || selectedBlog || activeImage) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = 'unset';
    }
    return () => {
      document.body.style.overflow = 'unset';
    };
  }, [activeDetailPage, selectedBlog, activeImage]);

  const handleContactSubmit = (e) => {
    e.preventDefault();
    if (!formData.name || !formData.email || !formData.message) return;
    
    setIsSubmitting(true);
    setTimeout(() => {
      setIsSubmitting(false);
      setSubmitStatus('success');
      setFormData({ name: '', email: '', subject: '', message: '' });
      setTimeout(() => setSubmitStatus(null), 5000);
    }, 1500);
  };

  return (
    <div className="min-h-screen bg-[#F8F9FA] text-[#202124] font-sans antialiased selection:bg-blue-100 selection:text-blue-900 overflow-x-hidden relative">
      
      {/* --- CSS GLOBAL UNTUK ANIMASI HALUS --- */}
      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        
        body {
          font-family: 'Plus Jakarta Sans', sans-serif;
          scroll-behavior: smooth;
        }

        .google-shadow-sm {
          box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3), 0 1px 3px 1px rgba(60,64,67,0.15);
        }

        .google-shadow-md {
          box-shadow: 0 1px 3px 0 rgba(60,64,67,0.3), 0 4px 8px 3px rgba(60,64,67,0.15);
        }

        .google-shadow-lg {
          box-shadow: 0 4px 4px 0 rgba(60,64,67,0.30), 0 8px 12px 6px rgba(60,64,67,0.15);
        }

        .fluid-bounce {
          transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .will-change-transform {
          will-change: transform, opacity;
        }

        @keyframes float-slower {
          0%, 100% { transform: translate3d(0, 0, 0) scale(1) rotate(0deg); }
          50% { transform: translate3d(0, -20px, 0) scale(1.08) rotate(3deg); }
        }

        @keyframes float-faster {
          0%, 100% { transform: translate3d(0, 0, 0) scale(1) rotate(0deg); }
          50% { transform: translate3d(0, 18px, 0) scale(0.92) rotate(-6deg); }
        }

        .animate-float-1 {
          animation: float-slower 9s ease-in-out infinite;
        }

        .animate-float-2 {
          animation: float-faster 7s ease-in-out infinite;
        }

        .active-tab-glow {
          box-shadow: 0 0 20px rgba(59, 130, 246, 0.25);
        }

        @keyframes slide-up {
          from { transform: translate3d(0, 80px, 0); opacity: 0; }
          to { transform: translate3d(0, 0, 0); opacity: 1; }
        }
        .animate-slideUp {
          animation: slide-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
      `}</style>

      {/* --- HEADER & NAVIGASI --- */}
      <header className="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-[#E8EAED] transition-all duration-300">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between h-20">
            {/* Logo */}
            <div className="flex items-center space-x-3 cursor-pointer" onClick={() => scrollToSection('home')}>
              <div className="w-11 h-11 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-sm shadow-blue-500/20 transform hover:scale-105 duration-300">
                <GraduationCap className="w-6 h-6 text-white" />
              </div>
              <div>
                <span className="text-xl font-bold tracking-tight text-[#202124]">
                  Google<span className="text-blue-600">School</span>
                </span>
                <p className="text-[10px] text-gray-500 font-medium tracking-wider uppercase">Future Academy</p>
              </div>
            </div>

            {/* Desktop Navigation */}
            <nav className="hidden md:flex items-center space-x-1 relative bg-gray-100 p-1.5 rounded-full border border-gray-200/50">
              {[
                { id: 'home', label: 'Home' },
                { id: 'about', label: 'About' },
                { id: 'blog', label: 'Blog' },
                { id: 'gallery', label: 'Gallery' },
                { id: 'contact', label: 'Contact Us' }
              ].map((item) => {
                const isActive = activeSection === item.id;
                return (
                  <button
                    key={item.id}
                    onClick={() => scrollToSection(item.id)}
                    className={`relative px-5 py-2 text-sm font-medium rounded-full transition-all duration-300 ease-out ${
                      isActive 
                        ? 'text-blue-600 bg-white shadow-sm font-semibold' 
                        : 'text-gray-600 hover:text-gray-900 hover:bg-gray-200/50'
                    }`}
                  >
                    {item.label}
                  </button>
                );
              })}
            </nav>

            {/* CTA Button */}
            <div className="hidden md:flex items-center space-x-4">
              <button 
                onClick={() => setActiveDetailPage('detail-pendaftaran')}
                className="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-full text-sm fluid-bounce hover:shadow-lg hover:shadow-blue-500/25 flex items-center space-x-2"
              >
                <span>Daftar Sekarang</span>
                <ArrowRight className="w-4 h-4" />
              </button>
            </div>

            {/* Mobile Hamburger Menu */}
            <div className="md:hidden">
              <button
                onClick={() => setIsMenuOpen(!isMenuOpen)}
                className="p-2.5 rounded-full hover:bg-gray-100 transition duration-200 focus:outline-none"
              >
                {isMenuOpen ? <X className="w-6 h-6 text-gray-700" /> : <Menu className="w-6 h-6 text-gray-700" />}
              </button>
            </div>
          </div>
        </div>

        {/* Mobile Dropdown Menu */}
        <div className={`md:hidden absolute top-20 left-0 w-full bg-white border-b border-gray-200 shadow-xl transition-all duration-300 ease-in-out ${
          isMenuOpen ? 'max-h-screen opacity-100 overflow-y-auto py-6' : 'max-h-0 opacity-0 overflow-hidden py-0'
        }`}>
          <div className="px-4 space-y-2">
            {[
              { id: 'home', label: 'Home' },
              { id: 'about', label: 'About Us' },
              { id: 'blog', label: 'Blog & Kabar' },
              { id: 'gallery', label: 'Galeri Kegiatan' },
              { id: 'contact', label: 'Contact Us' }
            ].map((item) => (
              <button
                key={item.id}
                onClick={() => scrollToSection(item.id)}
                className={`w-full text-left px-5 py-3.5 rounded-2xl text-base font-semibold transition duration-200 flex items-center justify-between ${
                  activeSection === item.id 
                    ? 'bg-blue-50 text-blue-600' 
                    : 'text-gray-700 hover:bg-gray-50'
                }`}
              >
                <span>{item.label}</span>
                <ChevronRight className={`w-4 h-4 transition duration-200 ${activeSection === item.id ? 'text-blue-600 translate-x-1' : 'text-gray-400'}`} />
              </button>
            ))}
            <div className="pt-4 px-4">
              <button 
                onClick={() => {
                  setIsMenuOpen(false);
                  setActiveDetailPage('detail-pendaftaran');
                }}
                className="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-2xl flex items-center justify-center space-x-2 shadow-md"
              >
                <span>Daftar Sekarang</span>
                <ArrowRight className="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </header>

      {/* --- SECTION 1: HOME (HERO) DENGAN PARALLAX TERISOLASI --- */}
      <section id="home" className="relative min-h-[calc(100vh-80px)] flex items-center py-12 overflow-hidden">
        
        {/* PARALLAX LAYER KHUSUS HERO */}
        <div className="absolute inset-0 z-0 overflow-hidden pointer-events-none">
          {/* Blob Biru */}
          <div 
            className="absolute top-[8%] left-[2%] w-[450px] h-[450px] bg-blue-200/25 rounded-full filter blur-[120px] will-change-transform transition-transform duration-100 ease-out"
            style={{ transform: `translate3d(0, ${scrollY * 0.15}px, 0)` }}
          />
          {/* Blob Kuning */}
          <div 
            className="absolute top-[28%] right-[5%] w-[400px] h-[400px] bg-amber-200/20 rounded-full filter blur-[110px] will-change-transform transition-transform duration-100 ease-out"
            style={{ transform: `translate3d(0, ${scrollY * 0.1}px, 0)` }}
          />
          {/* Cincin Geometris Biru */}
          <div 
            className="absolute top-[12%] right-[15%] w-24 h-24 border-8 border-blue-400/20 rounded-full will-change-transform transition-transform duration-100 ease-out hidden lg:block"
            style={{ transform: `translate3d(0, ${scrollY * -0.15}px, 0) rotate(${scrollY * 0.05}deg)` }}
          />
          {/* Segitiga Kuning */}
          <svg 
            className="absolute top-[45%] left-[8%] w-16 h-16 text-amber-400/20 fill-current will-change-transform transition-transform duration-100 ease-out hidden lg:block"
            style={{ transform: `translate3d(0, ${scrollY * 0.08}px, 0) rotate(${scrollY * -0.03}deg)` }}
            viewBox="0 0 100 100"
          >
            <polygon points="50,15 90,85 10,85" />
          </svg>
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            {/* Sisi Kiri: Teks & CTA */}
            <div className="lg:col-span-7 space-y-8 text-center lg:text-left">
              <RevealOnScroll delay={100} className="inline-flex items-center space-x-2 bg-blue-50 border border-blue-200/60 px-4 py-1.5 rounded-full text-blue-700 text-xs sm:text-sm font-semibold tracking-wide">
                <Sparkles className="w-4 h-4 text-blue-600 animate-pulse" />
                <span>Penerimaan Siswa Baru TA 2026/2027 Telah Dibuka</span>
              </RevealOnScroll>

              <RevealOnScroll delay={250}>
                <h1 className="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-[#202124] leading-[1.1] sm:leading-tight">
                  Membentuk <span className="text-blue-600 relative inline-block">Masa Depan<span className="absolute bottom-2 left-0 w-full h-3 bg-blue-100/85 -z-10 rounded-full"></span></span> yang Inovatif, Cerdik & Berkarakter
                </h1>
              </RevealOnScroll>

              <RevealOnScroll delay={400}>
                <p className="text-lg text-gray-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-light">
                  Selamat datang di Google School. Kami menghadirkan pendidikan akademis bertaraf internasional dengan ekosistem digital cerdas, kurikulum adaptif, dan pembinaan karakter berbasis nilai luhur.
                </p>
              </RevealOnScroll>

              <RevealOnScroll delay={550} className="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                <button 
                  onClick={() => setActiveDetailPage('detail-pendaftaran')}
                  className="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-4 rounded-full text-base fluid-bounce hover:shadow-xl hover:shadow-blue-500/20 transform hover:-translate-y-0.5"
                >
                  Mulai Pendaftaran
                </button>
                <button 
                  onClick={() => scrollToSection('about')}
                  className="w-full sm:w-auto bg-white hover:bg-gray-50 text-gray-700 font-semibold px-8 py-4 rounded-full text-base border border-gray-200/85 shadow-sm fluid-bounce flex items-center justify-center space-x-2 transform hover:-translate-y-0.5"
                >
                  <span>Eksplorasi Profil</span>
                  <ChevronRight className="w-4 h-4 text-gray-400" />
                </button>
              </RevealOnScroll>

              {/* Lencana Prestasi */}
              <RevealOnScroll delay={700} className="pt-8 border-t border-gray-200/60 grid grid-cols-3 gap-4 max-w-md mx-auto lg:mx-0">
                <div className="flex items-center space-x-2">
                  <div className="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                    <Award className="w-5 h-5" />
                  </div>
                  <div>
                    <h4 className="text-sm font-bold">Akreditasi</h4>
                    <p className="text-xs text-gray-500">Unggul (A)</p>
                  </div>
                </div>
                <div className="flex items-center space-x-2">
                  <div className="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <Users className="w-5 h-5" />
                  </div>
                  <div>
                    <h4 className="text-sm font-bold">1200+</h4>
                    <p className="text-xs text-gray-500">Alumni Sukses</p>
                  </div>
                </div>
                <div className="flex items-center space-x-2">
                  <div className="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-500">
                    <Compass className="w-5 h-5" />
                  </div>
                  <div>
                    <h4 className="text-sm font-bold">15+</h4>
                    <p className="text-xs text-gray-500">Ekskul Unggulan</p>
                  </div>
                </div>
              </RevealOnScroll>
            </div>

            {/* Sisi Kanan: Grafik Mockup dengan Interaksi Mouse Kursor */}
            <div className="lg:col-span-5 relative">
              <RevealOnScroll delay={350}>
                <div 
                  className="relative mx-auto max-w-md lg:max-w-none transition-all duration-200 ease-out"
                  style={{ 
                    transform: `translate3d(${mousePos.x * 25}px, ${mousePos.y * 25}px, 0) translate3d(0, ${scrollY * -0.05}px, 0) rotate(${scrollY * 0.003}deg)`,
                    willChange: 'transform'
                  }}
                >
                  {/* Kartu Utama */}
                  <div className="bg-white rounded-[32px] p-6 google-shadow-lg border border-gray-100 transform rotate-1 hover:rotate-0 transition-all duration-500 relative z-20">
                    <div className="relative rounded-2xl overflow-hidden aspect-square shadow-inner">
                      <img 
                        src="https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&q=80&w=600" 
                        alt="Aktivitas Belajar Sekolah" 
                        className="w-full h-full object-cover"
                      />
                      {/* Glassmorphism Badge */}
                      <div className="absolute bottom-4 left-4 right-4 bg-white/85 backdrop-blur-md rounded-xl p-4 flex items-center justify-between border border-white/20">
                        <div>
                          <p className="text-xs font-semibold text-blue-600 uppercase tracking-widest">Kolaborasi Aktif</p>
                          <h4 className="text-sm font-bold text-gray-900 mt-0.5">Siswa Mengembangkan Proyek IoT</h4>
                        </div>
                        <div className="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white cursor-pointer" onClick={() => setActiveDetailPage('detail-kurikulum')}>
                          <ArrowUpRight className="w-4 h-4" />
                        </div>
                      </div>
                    </div>

                    {/* Panel Statistik */}
                    <div className="mt-5 grid grid-cols-2 gap-4">
                      <div className="bg-blue-50/60 p-4 rounded-2xl border border-blue-100">
                        <span className="text-2xl font-black text-blue-600">98%</span>
                        <p className="text-xs font-semibold text-gray-700 mt-1">Lulusan di PTN & Universitas Global</p>
                      </div>
                      <div className="bg-emerald-50/60 p-4 rounded-2xl border border-emerald-100">
                        <span className="text-2xl font-black text-emerald-600">100%</span>
                        <p className="text-xs font-semibold text-gray-700 mt-1">Fasilitas Smart Classroom & Laboratorium</p>
                      </div>
                    </div>
                  </div>

                  {/* Dekorasi Floating Parallax (Gerakan Berlawanan) */}
                  <div 
                    className="absolute top-10 -right-6 w-32 h-32 bg-amber-100/80 border border-amber-200 rounded-[24px] -z-10 animate-float-1 flex flex-col justify-center items-center shadow-lg shadow-amber-500/5 transition-transform duration-200 ease-out"
                    style={{ transform: `translate3d(${mousePos.x * -35}px, ${mousePos.y * -35}px, 0) translate3d(0, ${scrollY * 0.08}px, 0)` }}
                  >
                    <Sparkles className="w-8 h-8 text-amber-500 mb-1" />
                    <span className="text-xs font-bold text-amber-800">Kreatif</span>
                  </div>
                  
                  <div 
                    className="absolute -bottom-8 -left-8 w-40 h-40 bg-red-50/90 border border-red-100 rounded-[28px] -z-10 animate-float-2 flex flex-col justify-center items-center p-4 shadow-lg shadow-red-500/5 transition-transform duration-200 ease-out"
                    style={{ transform: `translate3d(${mousePos.x * -45}px, ${mousePos.y * -45}px, 0) translate3d(0, ${scrollY * -0.08}px, 0)` }}
                  >
                    <div className="flex -space-x-2 mb-2">
                      <div className="w-8 h-8 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-[10px] text-white font-bold">1</div>
                      <div className="w-8 h-8 rounded-full bg-amber-500 border-2 border-white flex items-center justify-center text-[10px] text-white font-bold">2</div>
                      <div className="w-8 h-8 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center text-[10px] text-white font-bold">3</div>
                    </div>
                    <span className="text-xs font-bold text-red-800">Ekskul Juara</span>
                  </div>
                </div>
              </RevealOnScroll>
            </div>

          </div>
        </div>
      </section>

      {/* --- SECTION 2: ABOUT US (TENTANG KAMI) --- */}
      <section id="about" className="py-24 bg-white/40 border-y border-gray-100 relative overflow-hidden">
        
        {/* PARALLAX LAYER KHUSUS ABOUT */}
        <div className="absolute inset-0 z-0 overflow-hidden pointer-events-none">
          <div 
            className="absolute top-[10%] -left-[5%] w-[500px] h-[500px] bg-emerald-100/30 rounded-full filter blur-[130px] will-change-transform transition-transform duration-100 ease-out"
            style={{ transform: `translate3d(0, ${(scrollY - 600) * 0.1}px, 0)` }}
          />
          <div 
            className="absolute top-[40%] right-[3%] w-16 h-36 bg-emerald-400/10 rounded-full will-change-transform transition-transform duration-100 ease-out hidden lg:block"
            style={{ transform: `translate3d(0, ${(scrollY - 800) * -0.12}px, 0) rotate(${scrollY * 0.04}deg)` }}
          />
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          
          <RevealOnScroll className="text-center max-w-3xl mx-auto mb-16 space-y-4">
            <span className="text-xs font-black uppercase tracking-widest text-blue-600 bg-blue-50 px-3.5 py-1.5 rounded-full">
              Pilar Pendidikan Kami
            </span>
            <h2 className="text-3xl sm:text-4xl font-extrabold tracking-tight text-[#202124]">
              Membangun Fondasi Masa Depan yang Kokoh
            </h2>
            <p className="text-base text-gray-500">
              Google School didirikan untuk menjadi pionir pendidikan terintegrasi teknologi, tanpa melupakan penanaman moral yang luhur dan kebebasan bereksplorasi.
            </p>
          </RevealOnScroll>

          {/* Tiga Pilar Utama */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-20">
            {/* Pilar 1 */}
            <RevealOnScroll delay={100}>
              <div className="bg-white/80 backdrop-blur-sm h-full rounded-[28px] p-8 border border-gray-100 hover:border-blue-100 fluid-bounce hover:shadow-xl hover:-translate-y-2.5 group transition-transform duration-300 ease-out">
                <div className="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                  <BookOpen className="w-7 h-7" />
                </div>
                <h3 className="text-xl font-bold text-gray-900 mb-3">Kurikulum Unggul & Global</h3>
                <p className="text-sm text-gray-600 leading-relaxed font-light mb-4">
                  Kurikulum adaptif berbasis pemecahan masalah (Problem Based Learning) yang diintegrasikan dengan standar literasi global terkini.
                </p>
                <button 
                  onClick={() => setActiveDetailPage('detail-kurikulum')}
                  className="text-xs font-bold text-blue-600 flex items-center space-x-1 group-hover:text-blue-700 mt-2"
                >
                  <span>Lihat Detil Kurikulum</span>
                  <ChevronRight className="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" />
                </button>
              </div>
            </RevealOnScroll>

            {/* Pilar 2 */}
            <RevealOnScroll delay={250}>
              <div className="bg-white/80 backdrop-blur-sm h-full rounded-[28px] p-8 border border-gray-100 hover:border-emerald-100 fluid-bounce hover:shadow-xl hover:-translate-y-2.5 group transition-transform duration-300 ease-out">
                <div className="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                  <Users className="w-7 h-7" />
                </div>
                <h3 className="text-xl font-bold text-gray-900 mb-3">Kolaborasi & Kreativitas</h3>
                <p className="text-sm text-gray-600 leading-relaxed font-light mb-4">
                  Kami melatih para siswa untuk terbiasa bekerja sama secara dinamis lintas disiplin guna mematangkan empati sosial dan pola pikir solutif.
                </p>
                <button 
                  onClick={() => setActiveDetailPage('detail-fasilitas')}
                  className="text-xs font-bold text-emerald-600 flex items-center space-x-1 group-hover:text-emerald-700 mt-2"
                >
                  <span>Lihat Detil Fasilitas</span>
                  <ChevronRight className="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" />
                </button>
              </div>
            </RevealOnScroll>

            {/* Pilar 3 */}
            <RevealOnScroll delay={400}>
              <div className="bg-white/80 backdrop-blur-sm h-full rounded-[28px] p-8 border border-gray-100 hover:border-amber-100 fluid-bounce hover:shadow-xl hover:-translate-y-2.5 group transition-transform duration-300 ease-out">
                <div className="w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mb-6 group-hover:bg-amber-400 group-hover:text-white transition-colors duration-300">
                  <Award className="w-7 h-7" />
                </div>
                <h3 className="text-xl font-bold text-gray-900 mb-3">Karakter & Kepemimpinan</h3>
                <p className="text-sm text-gray-600 leading-relaxed font-light mb-4">
                  Pendidikan moral intensif melalui proyek kepemimpinan dan pengabdian masyarakat, membentuk jiwa pemimpin yang beretika.
                </p>
                <button 
                  onClick={() => setActiveDetailPage('detail-pendaftaran')}
                  className="text-xs font-bold text-amber-600 flex items-center space-x-1 group-hover:text-amber-700 mt-2"
                >
                  <span>Lihat Info Beasiswa</span>
                  <ChevronRight className="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" />
                </button>
              </div>
            </RevealOnScroll>
          </div>

          {/* Hub Visi, Misi & Sejarah (Interaktif) */}
          <RevealOnScroll delay={200}>
            <div className="bg-white/95 backdrop-blur-md rounded-[32px] p-6 sm:p-10 border border-gray-200/60 google-shadow-sm">
              <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                
                {/* Selektor Tab */}
                <div className="lg:col-span-4 flex flex-col space-y-3">
                  <h3 className="text-2xl font-black text-gray-900 mb-2">Profil Inti Sekolah</h3>
                  {[
                    { id: 'visi', label: 'Visi Sekolah', icon: Target, desc: 'Tujuan akhir & impian bersama' },
                    { id: 'misi', label: 'Misi Utama', icon: Compass, desc: 'Langkah strategis berkelanjutan' },
                    { id: 'sejarah', label: 'Sejarah Singkat', icon: Clock, desc: 'Perjalanan panjang inovasi' }
                  ].map((tab) => {
                    const IconComp = tab.icon;
                    const isActive = activeAboutTab === tab.id;
                    return (
                      <button
                        key={tab.id}
                        onClick={() => setActiveAboutTab(tab.id)}
                        className={`text-left p-4 rounded-2xl border transition-all duration-300 flex items-start space-x-4 ${
                          isActive 
                            ? 'bg-blue-600 border-blue-600 text-white active-tab-glow' 
                            : 'bg-white border-gray-200/60 hover:border-gray-300 text-gray-700'
                        }`}
                      >
                        <div className={`p-2 rounded-xl ${isActive ? 'bg-white/15 text-white' : 'bg-blue-50 text-blue-600'}`}>
                          <IconComp className="w-5 h-5" />
                        </div>
                        <div>
                          <h4 className="font-bold text-sm sm:text-base">{tab.label}</h4>
                          <p className={`text-xs mt-0.5 ${isActive ? 'text-white/85' : 'text-gray-400'}`}>{tab.desc}</p>
                        </div>
                      </button>
                    );
                  })}
                </div>

                {/* Panel Konten Tab Dinamis */}
                <div className="lg:col-span-8 bg-white p-6 sm:p-8 rounded-3xl border border-gray-200/50 min-h-[250px] flex flex-col justify-center transition-all duration-300">
                  {activeAboutTab === 'visi' && (
                    <div className="space-y-4 animate-slideUp">
                      <span className="text-xs font-bold text-blue-600 uppercase tracking-widest bg-blue-50 px-2.5 py-1 rounded">Masa Depan Cerah</span>
                      <h3 className="text-2xl font-bold text-gray-900">Menjadi Mercusuar Pendidikan Global</h3>
                      <p className="text-gray-600 leading-relaxed font-light">
                        "Menjadi lembaga pendidikan unggulan di Asia Tenggara yang mampu melahirkan agen perubahan dunia, unggul dalam sains, berdaya cipta dalam teknologi, berwawasan internasional, dengan landasan akhlak mulia."
                      </p>
                      <ul className="grid grid-cols-2 gap-3 pt-2">
                        <li className="flex items-center space-x-2 text-sm text-gray-700">
                          <CheckCircle className="w-4 h-4 text-emerald-500" />
                          <span>Pemberdayaan Teknologi</span>
                        </li>
                        <li className="flex items-center space-x-2 text-sm text-gray-700">
                          <CheckCircle className="w-4 h-4 text-emerald-500" />
                          <span>Integritas Kebangsaan</span>
                        </li>
                      </ul>
                    </div>
                  )}

                  {activeAboutTab === 'misi' && (
                    <div className="space-y-4 animate-slideUp">
                      <span className="text-xs font-bold text-emerald-600 uppercase tracking-widest bg-emerald-50 px-2.5 py-1 rounded">Rencana Aksi</span>
                      <h3 className="text-2xl font-bold text-gray-900">Empat Pilar Pelaksanaan Pendidikan</h3>
                      <div className="space-y-3">
                        <div className="flex items-start space-x-3">
                          <div className="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs mt-0.5">1</div>
                          <p className="text-sm text-gray-600"><span className="font-bold text-gray-800">Menyelenggarakan KBM</span> yang inovatif melalui integrasi penuh teknologi AI dan riset mandiri.</p>
                        </div>
                        <div className="flex items-start space-x-3">
                          <div className="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs mt-0.5">2</div>
                          <p className="text-sm text-gray-600"><span className="font-bold text-gray-800">Menyediakan Ekosistem</span> kolaboratif inklusif bagi seluruh elemen akademis demi potensi maksimal siswa.</p>
                        </div>
                        <div className="flex items-start space-x-3">
                          <div className="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs mt-0.5">3</div>
                          <p className="text-sm text-gray-600"><span className="font-bold text-gray-800">Menanamkan Nilai Akhlak</span> melalui keterlibatan aktif sosial kemasyarakatan yang berdampak.</p>
                        </div>
                      </div>
                    </div>
                  )}

                  {activeAboutTab === 'sejarah' && (
                    <div className="space-y-4 animate-slideUp">
                      <span className="text-xs font-bold text-amber-600 uppercase tracking-widest bg-amber-50 px-2.5 py-1 rounded">Sejak 2018</span>
                      <h3 className="text-2xl font-bold text-gray-900">Inovasi yang Tak Pernah Berhenti</h3>
                      <p className="text-gray-600 leading-relaxed font-light text-sm">
                        Didirikan pada tahun 2018 sebagai sekolah percontohan digital, Google School berawal dari ruang kelas interaktif kecil berkapasitas 60 siswa. Selaras dengan komitmen tinggi terhadap kualitas, sekolah tumbuh pesat. 
                      </p>
                      <p className="text-gray-600 leading-relaxed font-light text-sm">
                        Pada tahun 2022, sekolah diakui oleh Kementerian Pendidikan sebagai 'Sekolah Ekosistem Digital Terbaik'. Hari ini, Google School mengayomi lebih dari seribu siswa dengan jaringan alumni global yang tersebar di lima benua.
                      </p>
                    </div>
                  )}
                </div>

              </div>
            </div>
          </RevealOnScroll>

        </div>
      </section>

      {/* --- SECTION 3: BLOG (KABAR SEKOLAH) --- */}
      <section id="blog" className="py-24 bg-[#F8F9FA]/40 relative overflow-hidden">
        
        {/* PARALLAX LAYER KHUSUS BLOG */}
        <div className="absolute inset-0 z-0 overflow-hidden pointer-events-none">
          <div 
            className="absolute top-[20%] -right-[10%] w-[550px] h-[550px] bg-red-100/15 rounded-full filter blur-[140px] will-change-transform transition-transform duration-100 ease-out"
            style={{ transform: `translate3d(0, ${(scrollY - 1500) * 0.08}px, 0)` }}
          />
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          
          <RevealOnScroll className="flex flex-col md:flex-row items-start md:items-end justify-between mb-12 gap-6">
            <div className="space-y-3">
              <span className="text-xs font-black uppercase tracking-widest text-blue-600 bg-blue-50 px-3.5 py-1.5 rounded-full">
                Kabar & Inspirasi
              </span>
              <h2 className="text-3xl sm:text-4xl font-extrabold tracking-tight text-[#202124]">
                Update Terbaru Google School
              </h2>
              <p className="text-gray-500 text-sm sm:text-base max-w-xl">
                Temukan kabar prestasi, liputan agenda sekolah, gagasan akademik, dan cerita menarik dari komunitas kami.
              </p>
            </div>

            {/* Pencarian */}
            <div className="relative w-full md:w-80">
              <input
                type="text"
                placeholder="Cari kabar atau prestasi..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full pl-11 pr-5 py-3 rounded-full border border-gray-200 bg-white shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition text-sm text-[#202124]"
              />
              <Search className="w-5 h-5 text-gray-400 absolute left-4 top-3.5" />
            </div>
          </RevealOnScroll>

          {/* Chips Filter */}
          <RevealOnScroll delay={100} className="flex flex-wrap gap-2.5 mb-8">
            {['Semua', 'Akademik', 'Prestasi', 'Acara', 'Karakter'].map((category) => {
              const isActive = blogFilter === category;
              return (
                <button
                  key={category}
                  onClick={() => setBlogFilter(category)}
                  className={`px-5 py-2 rounded-full text-xs font-semibold border transition-all duration-300 ${
                    isActive
                      ? 'bg-blue-600 border-blue-600 text-white shadow-md'
                      : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50'
                  }`}
                >
                  {category}
                </button>
              );
            })}
          </RevealOnScroll>

          {/* Grid Kartu Berita */}
          {filteredBlogs.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              {filteredBlogs.map((post, index) => (
                <RevealOnScroll key={post.id} delay={index * 150} className="h-full">
                  <article 
                    className="bg-white rounded-3xl border border-gray-200/50 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col h-full group cursor-pointer"
                    onClick={() => setSelectedBlog(post)}
                  >
                    <div className="relative aspect-[4/3] overflow-hidden bg-gray-100 shrink-0">
                      <img 
                        src={post.image} 
                        alt={post.title} 
                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                      />
                      <span className="absolute top-4 left-4 bg-white/95 backdrop-blur-sm text-xs font-bold text-blue-600 px-3 py-1 rounded-full shadow-sm">
                        {post.category}
                      </span>
                    </div>

                    <div className="p-6 flex flex-col flex-grow space-y-3">
                      <div className="flex items-center space-x-2 text-xs text-gray-400 font-medium">
                        <span>{post.date}</span>
                        <span>•</span>
                        <span>{post.author}</span>
                      </div>
                      <h3 className="font-bold text-base text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2 leading-snug">
                        {post.title}
                      </h3>
                      <p className="text-xs text-gray-500 line-clamp-3 leading-relaxed font-light">
                        {post.excerpt}
                      </p>
                      <div className="pt-3 mt-auto flex items-center text-xs font-bold text-blue-600 group-hover:translate-x-1.5 transition-transform duration-200">
                        <span>Baca Selengkapnya</span>
                        <ChevronRight className="w-4 h-4 ml-0.5" />
                      </div>
                    </div>
                  </article>
                </RevealOnScroll>
              ))}
            </div>
          ) : (
            <div className="bg-white rounded-3xl p-12 text-center border border-gray-100 max-w-md mx-auto">
              <BookOpen className="w-12 h-12 text-gray-300 mx-auto mb-4" />
              <h3 className="text-lg font-bold text-gray-900 mb-1">Artikel Tidak Ditemukan</h3>
              <p className="text-sm text-gray-500">Silakan gunakan kata kunci pencarian lain.</p>
            </div>
          )}

        </div>
      </section>

      {/* --- SECTION 4: GALLERY (GALERI KEGIATAN) --- */}
      <section id="gallery" className="py-24 bg-white border-b border-gray-100 relative overflow-hidden">
        
        {/* PARALLAX LAYER KHUSUS GALERI */}
        <div className="absolute inset-0 z-0 overflow-hidden pointer-events-none">
          <div 
            className="absolute top-[15%] left-[2%] w-28 h-28 border-[12px] border-red-400/10 rounded-full will-change-transform transition-transform duration-100 ease-out hidden lg:block"
            style={{ transform: `translate3d(0, ${(scrollY - 2200) * 0.12}px, 0) rotate(${scrollY * -0.06}deg)` }}
          />
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          
          <RevealOnScroll className="text-center max-w-3xl mx-auto mb-16 space-y-4">
            <span className="text-xs font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3.5 py-1.5 rounded-full">
              Dokumentasi Visual
            </span>
            <h2 className="text-3xl sm:text-4xl font-extrabold tracking-tight text-[#202124]">
              Galeri Kehidupan Kampus
            </h2>
            <p className="text-base text-gray-500">
              Intip setiap senyum kemeriahan kelas, kolaborasi penelitian di laboratorium, serta kejuaraan bergengsi siswa kami.
            </p>

            <div className="flex flex-wrap justify-center gap-2 pt-4">
              {['Semua', 'Fasilitas', 'Aktivitas', 'Prestasi'].map((cat) => {
                const isActive = galleryFilter === cat;
                return (
                  <button
                    key={cat}
                    onClick={() => setGalleryFilter(cat)}
                    className={`px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-300 ${
                      isActive
                        ? 'bg-[#202124] text-white'
                        : 'bg-gray-100 hover:bg-gray-200 text-gray-600'
                    }`}
                  >
                    {cat}
                  </button>
                );
              })}
            </div>
          </RevealOnScroll>

          {/* Grid Gambar Galeri */}
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredGallery.map((item, idx) => (
              <RevealOnScroll key={item.id} delay={idx * 100}>
                <div 
                  onClick={() => setActiveImage(item)}
                  className="group relative rounded-[28px] overflow-hidden aspect-[4/3] bg-gray-100 border border-gray-100 cursor-pointer shadow-sm hover:shadow-xl transition-all duration-300"
                >
                  <img 
                    src={item.image} 
                    alt={item.title} 
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                  />
                  
                  {/* Overlay Detil */}
                  <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-6">
                    <span className="text-xs font-bold text-emerald-400 uppercase tracking-widest">
                      {item.category}
                    </span>
                    <h4 className="text-white text-lg font-bold mt-1">
                      {item.title}
                    </h4>
                    <div className="mt-3 inline-flex items-center space-x-1.5 text-xs text-white/95 bg-white/10 backdrop-blur-sm py-1.5 px-3 rounded-full w-max border border-white/10">
                      <span>Lihat Foto</span>
                      <ExternalLink className="w-3.5 h-3.5" />
                    </div>
                  </div>
                </div>
              </RevealOnScroll>
            ))}
          </div>

          <RevealOnScroll delay={200} className="mt-12 text-center">
            <button 
              onClick={() => setActiveDetailPage('detail-fasilitas')}
              className="bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold px-8 py-3.5 rounded-full text-sm inline-flex items-center space-x-2 transition duration-300 shadow-sm"
            >
              <span>Jelajahi Semua Fasilitas</span>
              <ArrowRight className="w-4 h-4" />
            </button>
          </RevealOnScroll>

        </div>
      </section>

      {/* --- SECTION 5: CONTACT US (HUBUNGI KAMI) --- */}
      <section id="contact" className="py-24 bg-[#F8F9FA]/60 relative overflow-hidden">
        
        {/* PARALLAX LAYER KHUSUS KONTAK */}
        <div className="absolute inset-0 z-0 overflow-hidden pointer-events-none">
          <div 
            className="absolute top-[20%] -left-[2%] w-[450px] h-[450px] bg-blue-100/20 rounded-full filter blur-[120px] will-change-transform transition-transform duration-100 ease-out"
            style={{ transform: `translate3d(0, ${(scrollY - 2800) * 0.1}px, 0)` }}
          />
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-stretch">
            
            {/* Kolom Informasi Kontak */}
            <div className="lg:col-span-5 flex flex-col justify-between space-y-8">
              <RevealOnScroll className="space-y-4">
                <span className="text-xs font-black uppercase tracking-widest text-blue-600 bg-blue-50 px-3.5 py-1.5 rounded-full">
                  Hubungi Kami
                </span>
                <h2 className="text-3xl sm:text-4xl font-extrabold tracking-tight text-[#202124]">
                  Siap Membantu Anda
                </h2>
                <p className="text-gray-500 leading-relaxed font-light">
                  Ada pertanyaan mengenai tata cara pendaftaran, beasiswa, kurikulum, atau peninjauan fasilitas? Silakan hubungi pusat informasi kami yang ramah dan siap membantu.
                </p>
              </RevealOnScroll>

              <div className="space-y-4">
                <RevealOnScroll delay={100}>
                  <div className="bg-white/95 backdrop-blur-sm p-5 rounded-2xl border border-gray-200/50 flex items-start space-x-4 shadow-sm">
                    <div className="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                      <MapPin className="w-5 h-5" />
                    </div>
                    <div>
                      <h4 className="font-bold text-sm text-gray-900">Alamat Sekolah</h4>
                      <p className="text-xs text-gray-500 mt-1">Jl. Boulevard Edukasi No. 101, BSD City, Tangerang, Banten</p>
                    </div>
                  </div>
                </RevealOnScroll>

                <RevealOnScroll delay={200}>
                  <div className="bg-white/95 backdrop-blur-sm p-5 rounded-2xl border border-gray-200/50 flex items-start space-x-4 shadow-sm">
                    <div className="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                      <Phone className="w-5 h-5" />
                    </div>
                    <div>
                      <h4 className="font-bold text-sm text-gray-900">Kontak Telepon & WhatsApp</h4>
                      <p className="text-xs text-gray-500 mt-1">+62 812-3456-7890 (Adm. Pendaftaran)</p>
                    </div>
                  </div>
                </RevealOnScroll>

                <RevealOnScroll delay={300}>
                  <div className="bg-white/95 backdrop-blur-sm p-5 rounded-2xl border border-gray-200/50 flex items-start space-x-4 shadow-sm">
                    <div className="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                      <Clock className="w-5 h-5" />
                    </div>
                    <div>
                      <h4 className="font-bold text-sm text-gray-900">Jam Operasional Layanan</h4>
                      <p className="text-xs text-gray-500 mt-1">Senin - Jumat: 08.00 - 15.00 WIB (Sabtu dengan Janji)</p>
                    </div>
                  </div>
                </RevealOnScroll>
              </div>

              {/* Banner Navigasi FAQ */}
              <RevealOnScroll delay={400} className="bg-gradient-to-br from-blue-100 to-indigo-100 border border-blue-200 rounded-[24px] h-36 flex flex-col justify-center items-center relative overflow-hidden p-6 text-center cursor-pointer group" onClick={() => setActiveDetailPage('detail-faq')}>
                <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-20 w-48 h-48 bg-blue-400 rounded-full blur-2xl"></div>
                <Compass className="w-8 h-8 text-blue-600 mb-2 relative z-10 group-hover:rotate-12 transition-transform duration-300" />
                <h4 className="font-bold text-sm text-blue-900 relative z-10 flex items-center justify-center space-x-1">
                  <span>Ada Pertanyaan Umum?</span>
                  <ArrowRight className="w-4 h-4 transition-transform group-hover:translate-x-1" />
                </h4>
                <p className="text-xs text-blue-700/80 mt-1 relative z-10">Buka halaman Tanya Jawab (FAQ) & Alur Pendaftaran.</p>
              </RevealOnScroll>

            </div>

            {/* Kolom Kanan: Formulir Kontak Interaktif */}
            <div className="lg:col-span-7">
              <RevealOnScroll delay={200} className="bg-white/95 backdrop-blur-sm rounded-[32px] p-6 sm:p-10 border border-gray-200/60 google-shadow-md">
                <h3 className="text-xl font-bold text-gray-900 mb-2">Kirim Pesan Langsung</h3>
                <p className="text-xs text-gray-400 mb-8">Konsultan pendidikan kami akan merespons dalam waktu 1x24 jam operasional.</p>

                <form onSubmit={handleContactSubmit} className="space-y-6">
                  {/* Input Nama */}
                  <div className="relative">
                    <input
                      type="text"
                      id="name"
                      required
                      value={formData.name}
                      onFocus={() => setFocusedField('name')}
                      onBlur={() => setFocusedField(null)}
                      onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                      className={`w-full px-5 py-4 rounded-2xl border text-sm transition-all duration-300 outline-none ${
                        focusedField === 'name' || formData.name
                          ? 'pt-6 pb-2 border-blue-600 ring-1 ring-blue-500'
                          : 'border-gray-200 hover:border-gray-300 bg-white'
                      }`}
                    />
                    <label 
                      htmlFor="name" 
                      className={`absolute left-5 transition-all duration-300 pointer-events-none text-xs ${
                        focusedField === 'name' || formData.name 
                          ? 'top-2 text-blue-600 font-bold' 
                          : 'top-4 text-gray-400 text-sm'
                      }`}
                    >
                      Nama Lengkap Anda
                    </label>
                  </div>

                  {/* Input Email */}
                  <div className="relative">
                    <input
                      type="email"
                      id="email"
                      required
                      value={formData.email}
                      onFocus={() => setFocusedField('email')}
                      onBlur={() => setFocusedField(null)}
                      onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                      className={`w-full px-5 py-4 rounded-2xl border text-sm transition-all duration-300 outline-none ${
                        focusedField === 'email' || formData.email
                          ? 'pt-6 pb-2 border-blue-600 ring-1 ring-blue-500'
                          : 'border-gray-200 hover:border-gray-300 bg-white'
                      }`}
                    />
                    <label 
                      htmlFor="email" 
                      className={`absolute left-5 transition-all duration-300 pointer-events-none text-xs ${
                        focusedField === 'email' || formData.email 
                          ? 'top-2 text-blue-600 font-bold' 
                          : 'top-4 text-gray-400 text-sm'
                      }`}
                    >
                      Alamat Email Aktif
                    </label>
                  </div>

                  {/* Input Perihal */}
                  <div className="relative">
                    <input
                      type="text"
                      id="subject"
                      value={formData.subject}
                      onFocus={() => setFocusedField('subject')}
                      onBlur={() => setFocusedField(null)}
                      onChange={(e) => setFormData({ ...formData, subject: e.target.value })}
                      className={`w-full px-5 py-4 rounded-2xl border text-sm transition-all duration-300 outline-none ${
                        focusedField === 'subject' || formData.subject
                          ? 'pt-6 pb-2 border-blue-600 ring-1 ring-blue-500'
                          : 'border-gray-200 hover:border-gray-300 bg-white'
                      }`}
                    />
                    <label 
                      htmlFor="subject" 
                      className={`absolute left-5 transition-all duration-300 pointer-events-none text-xs ${
                        focusedField === 'subject' || formData.subject 
                          ? 'top-2 text-blue-600 font-bold' 
                          : 'top-4 text-gray-400 text-sm'
                      }`}
                    >
                      Perihal / Subjek Pertanyaan (Opsional)
                    </label>
                  </div>

                  {/* Input Pesan */}
                  <div className="relative">
                    <textarea
                      id="message"
                      required
                      rows="4"
                      value={formData.message}
                      onFocus={() => setFocusedField('message')}
                      onBlur={() => setFocusedField(null)}
                      onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                      className={`w-full px-5 py-4 rounded-2xl border text-sm transition-all duration-300 outline-none resize-none ${
                        focusedField === 'message' || formData.message
                          ? 'pt-6 pb-2 border-blue-600 ring-1 ring-blue-500'
                          : 'border-gray-200 hover:border-gray-300 bg-white'
                      }`}
                    ></textarea>
                    <label 
                      htmlFor="message" 
                      className={`absolute left-5 transition-all duration-300 pointer-events-none text-xs ${
                        focusedField === 'message' || formData.message 
                          ? 'top-2 text-blue-600 font-bold' 
                          : 'top-4 text-gray-400 text-sm'
                      }`}
                    >
                      Tulis Detail Pesan Anda...
                    </label>
                  </div>

                  {/* Tombol Kirim */}
                  <button
                    type="submit"
                    disabled={isSubmitting}
                    className="w-full bg-[#202124] hover:bg-black text-white font-bold py-4 rounded-2xl transition-all duration-300 flex items-center justify-center space-x-2 shadow-lg"
                  >
                    {isSubmitting ? (
                      <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    ) : (
                      <>
                        <span>Kirim Pesan Ke Sekolah</span>
                        <Send className="w-4 h-4" />
                      </>
                    )}
                  </button>

                  {/* Banner Notifikasi Sukses */}
                  {submitStatus === 'success' && (
                    <div className="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center space-x-3 text-emerald-800 animate-slideUp">
                      <CheckCircle className="w-5 h-5 text-emerald-600 shrink-0" />
                      <div className="text-xs">
                        <p className="font-bold">Pesan Terkirim dengan Sukses!</p>
                        <p className="mt-0.5">Terima kasih atas pesan Anda, admin kami akan segera membalas lewat e-mail.</p>
                      </div>
                    </div>
                  )}
                </form>
              </RevealOnScroll>
            </div>

          </div>
        </div>
      </section>

      {/* --- FOOTER --- */}
      <footer className="bg-[#202124] text-[#E8EAED] pt-16 pb-8 border-t border-gray-800 relative z-10">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 md:grid-cols-12 gap-10 mb-12">
            
            {/* Profil Singkat */}
            <div className="md:col-span-5 space-y-4">
              <div className="flex items-center space-x-3">
                <div className="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold">
                  <GraduationCap className="w-5 h-5" />
                </div>
                <span className="text-xl font-bold tracking-tight text-white">
                  Google<span className="text-blue-500">School</span>
                </span>
              </div>
              <p className="text-sm text-gray-400 max-w-sm leading-relaxed font-light">
                Menyelenggarakan pendidikan komprehensif berteknologi tinggi demi lahirnya generasi visioner yang memimpin dengan keteladanan akhlak.
              </p>
            </div>

            {/* Navigasi Peta */}
            <div className="md:col-span-3 space-y-4">
              <h4 className="text-white text-sm font-bold tracking-widest uppercase">Navigasi Peta</h4>
              <ul className="space-y-2 text-sm text-gray-400">
                <li><button onClick={() => scrollToSection('home')} className="hover:text-white transition">Halaman Utama (Home)</button></li>
                <li><button onClick={() => scrollToSection('about')} className="hover:text-white transition">Tentang Kami (About)</button></li>
                <li><button onClick={() => scrollToSection('blog')} className="hover:text-white transition">Kabar Sekolah (Blog)</button></li>
                <li><button onClick={() => scrollToSection('gallery')} className="hover:text-white transition">Galeri Kegiatan (Gallery)</button></li>
                <li><button onClick={() => scrollToSection('contact')} className="hover:text-white transition">Kontak Layanan (Contact)</button></li>
              </ul>
            </div>

            {/* Newsletter */}
            <div className="md:col-span-4 space-y-4">
              <h4 className="text-white text-sm font-bold tracking-widest uppercase">Newsletter Hub</h4>
              <p className="text-sm text-gray-400 font-light">Dapatkan rangkuman buletin mingguan kegiatan dan artikel kurikulum sekolah.</p>
              <div className="flex space-x-2">
                <input
                  type="email"
                  placeholder="E-mail anda..."
                  className="bg-gray-800 text-white rounded-xl px-4 py-2.5 text-xs w-full focus:outline-none focus:ring-1 focus:ring-blue-500 border border-gray-700"
                />
                <button className="bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-4 text-xs font-bold transition">
                  Gabung
                </button>
              </div>
            </div>

          </div>

          <div className="pt-8 border-t border-gray-800 text-center flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-4">
            <p>© 2026 Google School Future Academy. Seluruh Hak Cipta Dilindungi.</p>
            <div className="flex space-x-4">
              <a href="#" className="hover:text-gray-400 transition">Kebijakan Privasi</a>
              <span>•</span>
              <a href="#" className="hover:text-gray-400 transition">Ketentuan Layanan</a>
            </div>
          </div>
        </div>
      </footer>

      {/* --- MODAL DETAIL (SLIDE UP FULLSCREEN) --- */}

      {/* 1. DETAIL PENDAFTARAN & BEASISWA */}
      {activeDetailPage === 'detail-pendaftaran' && (
        <div className="fixed inset-0 z-50 bg-[#F8F9FA] overflow-y-auto animate-slideUp">
          <div className="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
            <div className="flex items-center justify-between pb-8 border-b border-gray-200">
              <button 
                onClick={() => setActiveDetailPage(null)}
                className="inline-flex items-center space-x-2 text-sm font-bold text-gray-600 hover:text-gray-900 bg-white px-5 py-2.5 rounded-full border border-gray-200 shadow-sm transition"
              >
                <ArrowLeft className="w-4 h-4" />
                <span>Kembali ke Beranda</span>
              </button>
              <span className="text-xs font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-3 py-1.5 rounded-full">
                Penerimaan 2026/2027
              </span>
            </div>

            <div className="mt-12 space-y-12">
              <div className="space-y-4">
                <h1 className="text-3xl sm:text-4xl font-extrabold text-[#202124]">Panduan Pendaftaran & Program Beasiswa</h1>
                <p className="text-gray-600 font-light leading-relaxed">Selamat datang di gerbang masa depan. Temukan informasi lengkap mengenai alur seleksi masuk, persyaratan dokumen, hingga skema beasiswa prestasi penuh (full scholarship).</p>
              </div>

              {/* Timeline Alur Masuk */}
              <div className="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/60 shadow-sm space-y-8">
                <h2 className="text-xl font-bold text-gray-900 flex items-center space-x-2">
                  <Calendar className="w-5 h-5 text-blue-600" />
                  <span>Alur Gelombang Penerimaan</span>
                </h2>
                
                <div className="relative border-l-2 border-blue-100 ml-4 space-y-8">
                  <div className="relative pl-6">
                    <div className="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-blue-600 border-4 border-white shadow"></div>
                    <span className="text-xs font-bold text-blue-600">GELOMBANG 1 (SEKARANG BUKA)</span>
                    <h3 className="font-bold text-base text-gray-900 mt-1">Pendaftaran & Pengunggahan Berkas</h3>
                    <p className="text-xs text-gray-500 mt-1">Batas Akhir: 30 Juni 2026. Persyaratan mencakup nilai rapor semester 1-5, surat rekomendasi guru, dan sertifikat prestasi (opsional).</p>
                  </div>
                  
                  <div className="relative pl-6">
                    <div className="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-blue-400 border-4 border-white shadow"></div>
                    <span className="text-xs font-bold text-blue-400">TAHAPAN 2</span>
                    <h3 className="font-bold text-base text-gray-900 mt-1">Ujian Saringan Masuk & Wawancara</h3>
                    <p className="text-xs text-gray-500 mt-1">Tanggal: 5-7 Juli 2026. Tes kognitif, literasi digital dasar, logika matematika, serta wawancara bakat minat dalam Bahasa Inggris.</p>
                  </div>

                  <div className="relative pl-6">
                    <div className="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-gray-300 border-4 border-white shadow"></div>
                    <span className="text-xs font-bold text-gray-400">TAHAPAN 3</span>
                    <h3 className="font-bold text-base text-gray-900 mt-1">Pengumuman Kelulusan Akhir</h3>
                    <p className="text-xs text-gray-500 mt-1">Tanggal: 15 Juli 2026. Hasil diumumkan secara online melalui portal resmi pendaftaran siswa baru.</p>
                  </div>
                </div>
              </div>

              {/* Pilihan Program Beasiswa */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-3xl p-8 space-y-4 shadow-lg shadow-blue-500/10">
                  <Award className="w-8 h-8 text-amber-300" />
                  <h3 className="text-xl font-bold">Beasiswa Cendekia Unggul</h3>
                  <p className="text-sm text-blue-50/90 leading-relaxed font-light">Beasiswa penuh biaya pendidikan (100% SPP & Pembangunan) khusus bagi siswa berprestasi di bidang sains, olimpiade matematika, atau kejuaraan olahraga nasional.</p>
                  <ul className="text-xs space-y-2 pt-2">
                    <li className="flex items-center space-x-2"><Check className="w-4 h-4 text-amber-300" /> <span>Bebas biaya pendaftaran</span></li>
                    <li className="flex items-center space-x-2"><Check className="w-4 h-4 text-amber-300" /> <span>Pembinaan khusus olimpiade internasional</span></li>
                  </ul>
                </div>

                <div className="bg-white text-gray-900 rounded-3xl p-8 space-y-4 border border-gray-200 shadow-sm">
                  <Sparkles className="w-8 h-8 text-blue-600" />
                  <h3 className="text-xl font-bold text-gray-900">Beasiswa Inovator Digital</h3>
                  <p className="text-sm text-gray-600 leading-relaxed font-light">Skema bantuan khusus untuk calon siswa yang menunjukkan karya digital kreatif (programming, robotik, desain grafis, atau pembuatan konten edukasi).</p>
                  <ul className="text-xs space-y-2 pt-2 text-gray-500">
                    <li className="flex items-center space-x-2"><Check className="w-4 h-4 text-emerald-500" /> <span>Bantuan subsidi SPP hingga 75%</span></li>
                    <li className="flex items-center space-x-2"><Check className="w-4 h-4 text-emerald-500" /> <span>Akses fasilitas eksklusif lab inovasi</span></li>
                  </ul>
                </div>
              </div>

              {/* Box Aksi Kontak Admisi */}
              <div className="p-8 bg-gray-100 rounded-3xl text-center space-y-4">
                <h3 className="font-bold text-lg">Siap Melangkah Bersama Kami?</h3>
                <p className="text-sm text-gray-500 max-w-md mx-auto">Hubungi kami melalui nomor resmi untuk mengunduh brosur PDF fisik atau menjadwalkan kunjungan.</p>
                <div className="flex flex-col sm:flex-row justify-center gap-3">
                  <a href="https://wa.me/6281234567890" target="_blank" rel="noreferrer" className="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-full text-sm font-bold shadow-md shadow-emerald-600/10 inline-flex items-center justify-center space-x-2">
                    <Phone className="w-4 h-4" />
                    <span>Hubungi Panitia (WA)</span>
                  </a>
                  <button onClick={() => { setActiveDetailPage(null); scrollToSection('contact'); }} className="bg-gray-900 hover:bg-black text-white px-6 py-3 rounded-full text-sm font-bold inline-flex items-center justify-center space-x-2">
                    <span>Isi Formulir Pertanyaan</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* 2. DETAIL KURIKULUM */}
      {activeDetailPage === 'detail-kurikulum' && (
        <div className="fixed inset-0 z-50 bg-[#F8F9FA] overflow-y-auto animate-slideUp">
          <div className="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
            <div className="flex items-center justify-between pb-8 border-b border-gray-200">
              <button 
                onClick={() => setActiveDetailPage(null)}
                className="inline-flex items-center space-x-2 text-sm font-bold text-gray-600 hover:text-gray-900 bg-white px-5 py-2.5 rounded-full border border-gray-200 shadow-sm transition"
              >
                <ArrowLeft className="w-4 h-4" />
                <span>Kembali ke Beranda</span>
              </button>
              <span className="text-xs font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full">
                Sains & Inovasi
              </span>
            </div>

            <div className="mt-12 space-y-12">
              <div className="space-y-4">
                <h1 className="text-3xl sm:text-4xl font-extrabold text-[#202124]">Kurikulum & Metode Pengajaran Futuristik</h1>
                <p className="text-gray-600 font-light leading-relaxed">SMA Google School menerapkan ekosistem kurikulum adaptif yang tidak hanya mementingkan hafalan tekstual, melainkan penguasaan konsep holistik, kecerdasan digital, dan pemikiran analitik tingkat tinggi.</p>
              </div>

              {/* Kurikulum Grid */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="bg-white border border-gray-200 rounded-3xl p-6 space-y-4">
                  <div className="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">1</div>
                  <h3 className="font-bold text-lg">AI & Digital Literacy</h3>
                  <p className="text-xs text-gray-500 leading-relaxed font-light">Materi pemrograman dasar, pengenalan kecerdasan buatan, visualisasi data, dan etika berinteraksi di dunia maya sebagai mata pelajaran inti.</p>
                </div>

                <div className="bg-white border border-gray-200 rounded-3xl p-6 space-y-4">
                  <div className="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">2</div>
                  <h3 className="font-bold text-lg">Project-Based Learning</h3>
                  <p className="text-xs text-gray-500 leading-relaxed font-light">Siswa belajar memecahkan masalah lokal secara nyata di masyarakat melalui riset ilmiah mandiri yang dipresentasikan di akhir semester.</p>
                </div>

                <div className="bg-white border border-gray-200 rounded-3xl p-6 space-y-4">
                  <div className="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">3</div>
                  <h3 className="font-bold text-lg">Bilingual Immersion</h3>
                  <p className="text-xs text-gray-500 leading-relaxed font-light">Penyampaian materi pembelajaran eksakta (Sains, Teknologi, dan Matematika) menggunakan Bahasa Inggris interaktif bertaraf internasional.</p>
                </div>
              </div>

              {/* Peminatan Studi */}
              <div className="bg-white rounded-3xl p-6 sm:p-10 border border-gray-200/60 shadow-sm space-y-6">
                <h3 className="text-xl font-bold">Dua Peminatan Unggulan Kami</h3>
                
                <div className="grid grid-cols-1 md:grid-cols-2 gap-8 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                  <div className="space-y-3 pr-0 md:pr-4">
                    <div className="inline-flex items-center space-x-2 text-blue-600 font-bold text-sm">
                      <Monitor className="w-4 h-4" />
                      <span>Rekayasa Teknologi & Robotik (Sains)</span>
                    </div>
                    <p className="text-xs text-gray-500 leading-relaxed font-light">Fokus pada pendalaman matematika terapan, coding (Python & C++), fisika mekanika, sistem loT, serta kompetisi sains nasional & internasional.</p>
                  </div>

                  <div className="space-y-3 pt-6 md:pt-0 pl-0 md:pl-8">
                    <div className="inline-flex items-center space-x-2 text-emerald-600 font-bold text-sm">
                      <Globe className="w-4 h-4" />
                      <span>Global Leader & Sosiopreneur (Sosial)</span>
                    </div>
                    <p className="text-xs text-gray-500 leading-relaxed font-light">Fokus pada pembinaan kemampuan bahasa asing, debat internasional (Model United Nations), kewirausahaan digital sosial, dan riset sosiologi budaya.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* 3. DETAIL FASILITAS */}
      {activeDetailPage === 'detail-fasilitas' && (
        <div className="fixed inset-0 z-50 bg-[#F8F9FA] overflow-y-auto animate-slideUp">
          <div className="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
            <div className="flex items-center justify-between pb-8 border-b border-gray-200">
              <button 
                onClick={() => setActiveDetailPage(null)}
                className="inline-flex items-center space-x-2 text-sm font-bold text-gray-600 hover:text-gray-900 bg-white px-5 py-2.5 rounded-full border border-gray-200 shadow-sm transition"
              >
                <ArrowLeft className="w-4 h-4" />
                <span>Kembali ke Beranda</span>
              </button>
              <span className="text-xs font-bold uppercase tracking-wider text-amber-600 bg-amber-50 px-3 py-1.5 rounded-full">
                Ekosistem Nyaman
              </span>
            </div>

            <div className="mt-12 space-y-12">
              <div className="space-y-4">
                <h1 className="text-3xl sm:text-4xl font-extrabold text-[#202124]">Fasilitas Kampus Pintar (Smart Campus)</h1>
                <p className="text-gray-600 font-light leading-relaxed">Kami meyakini lingkungan belajar yang modern, bersih, dan berfasilitas lengkap sangat mendukung penyerapan potensi maksimal siswa.</p>
              </div>

              {/* Grid Dokumentasi Fasilitas */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div className="bg-white rounded-3xl overflow-hidden border border-gray-200 shadow-sm">
                  <div className="aspect-video w-full bg-gray-100 relative">
                    <img src="https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&q=80&w=400" alt="Lab Komputer" className="w-full h-full object-cover" />
                  </div>
                  <div className="p-6 space-y-2">
                    <h3 className="font-bold text-base">Laboratorium Komputer & AI</h3>
                    <p className="text-xs text-gray-500 font-light leading-relaxed">Dilengkapi dengan PC berspesifikasi tinggi untuk rendering 3D, coding, dan pengembangan kecerdasan buatan, serta koneksi internet fiber optik simetris berkecepatan tinggi.</p>
                  </div>
                </div>

                <div className="bg-white rounded-3xl overflow-hidden border border-gray-200 shadow-sm">
                  <div className="aspect-video w-full bg-gray-100 relative">
                    <img src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&q=80&w=400" alt="Perpustakaan Digital" className="w-full h-full object-cover" />
                  </div>
                  <div className="p-6 space-y-2">
                    <h3 className="font-bold text-base">Perpustakaan Digital & Cafe Belajar</h3>
                    <p className="text-xs text-gray-500 font-light leading-relaxed">Menggabungkan kenyamanan ruang baca fisik ber-AC dengan e-book tablet untuk mengakses jutaan referensi jurnal ilmiah nasional dan global gratis.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* 4. DETAIL FAQ (PERTANYAAN UMUM) */}
      {activeDetailPage === 'detail-faq' && (
        <div className="fixed inset-0 z-50 bg-[#F8F9FA] overflow-y-auto animate-slideUp">
          <div className="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
            <div className="flex items-center justify-between pb-8 border-b border-gray-200">
              <button 
                onClick={() => setActiveDetailPage(null)}
                className="inline-flex items-center space-x-2 text-sm font-bold text-gray-600 hover:text-gray-900 bg-white px-5 py-2.5 rounded-full border border-gray-200 shadow-sm transition"
              >
                <ArrowLeft className="w-4 h-4" />
                <span>Kembali ke Beranda</span>
              </button>
              <span className="text-xs font-bold uppercase tracking-wider text-purple-600 bg-purple-50 px-3 py-1.5 rounded-full">
                Pusat Bantuan
              </span>
            </div>

            <div className="mt-12 space-y-12">
              <div className="space-y-4">
                <h1 className="text-3xl sm:text-4xl font-extrabold text-[#202124]">Pertanyaan yang Sering Diajukan (FAQ)</h1>
                <p className="text-gray-600 font-light leading-relaxed">Berikut adalah kompilasi jawaban ringkas dan jelas atas pertanyaan umum yang diajukan oleh calon siswa maupun orang tua wali.</p>
              </div>

              {/* Daftar Akordeon FAQ */}
              <div className="space-y-4">
                {[
                  {
                    q: "Apakah Google School berafiliasi langsung dengan korporasi Google?",
                    a: "Google School merupakan sekolah akademis independen di bawah naungan Yayasan Masa Depan Inovatif. Namun, kami bermitra secara optimal menggunakan teknologi ekosistem Google Workspace for Education (Chromebook, Google Classroom) dan kurikulum teknologi kami dikembangkan selaras dengan keahlian industri digital global."
                  },
                  {
                    q: "Bagaimana sistem akreditasi kurikulum sekolah?",
                    a: "Kami telah terakreditasi Unggul (A) secara nasional oleh BAN-S/M, serta mengadopsi standar internasional Cambridge untuk penyampaian sains dan matematika, sehingga lulusan kami memiliki peluang lolos tinggi baik di PTN unggulan maupun di universitas global."
                  },
                  {
                    q: "Apakah ada asrama/boarding untuk siswa di luar daerah?",
                    a: "Saat ini kami fokus pada sekolah harian (Day School). Namun, kami memiliki jaringan kemitraan eksklusif terpercaya dengan penyedia asrama (kos/homestay) yang sangat aman dan terawasi berkala di sekitar kawasan BSD City."
                  },
                  {
                    q: "Bagaimana sistem pembayaran biaya pendidikan?",
                    a: "Biaya SPP bulanan serta sumbangan pembangunan dapat diangsur secara fleksibel per semester atau tahunan. Kami juga memiliki ragam pilihan beasiswa penuh dan subsidi biaya bagi siswa yang berpotensi tinggi."
                  }
                ].map((faq, idx) => {
                  const isOpen = openFaq === idx;
                  return (
                    <div 
                      key={idx}
                      className="bg-white rounded-2xl border border-gray-200/60 overflow-hidden transition-all duration-300 shadow-sm"
                    >
                      <button 
                        onClick={() => setOpenFaq(isOpen ? null : idx)}
                        className="w-full text-left p-6 flex justify-between items-center hover:bg-gray-50/50 focus:outline-none"
                      >
                        <span className="font-bold text-sm sm:text-base text-gray-900 pr-4">{faq.q}</span>
                        <ChevronDown className={`w-5 h-5 text-gray-400 transition-transform duration-300 shrink-0 ${isOpen ? 'rotate-180 text-blue-600' : ''}`} />
                      </button>
                      
                      <div className={`transition-all duration-300 ease-in-out ${isOpen ? 'max-h-60 border-t border-gray-100 p-6 opacity-100' : 'max-h-0 overflow-hidden opacity-0 py-0'}`}>
                        <p className="text-xs sm:text-sm text-gray-500 leading-relaxed font-light">{faq.a}</p>
                      </div>
                    </div>
                  );
                })}
              </div>

              {/* Tombol Konsultasi Tambahan */}
              <div className="bg-white rounded-3xl p-8 border border-gray-200/60 text-center space-y-4">
                <HelpCircle className="w-10 h-10 text-blue-600 mx-auto" />
                <h3 className="text-lg font-bold">Masih Belum Menemukan Jawaban?</h3>
                <p className="text-xs text-gray-500 max-w-md mx-auto">Kami menyediakan konsultasi langsung tatap muka atau panggilan video interaktif dengan konsultan pendidikan kami untuk kenyamanan Anda.</p>
                <button onClick={() => { setActiveDetailPage(null); scrollToSection('contact'); }} className="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-full text-xs">
                  Hubungi Kontak Sekarang
                </button>
              </div>

            </div>
          </div>
        </div>
      )}

      {/* --- LIGHTBOX DETAIL FOTO GALERI --- */}
      {activeImage && (
        <div className="fixed inset-0 z-50 bg-black/95 flex items-center justify-center p-4 transition-opacity duration-300">
          <button 
            onClick={() => setActiveImage(null)}
            className="absolute top-6 right-6 p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition focus:outline-none"
          >
            <X className="w-6 h-6" />
          </button>
          
          <div className="max-w-4xl w-full text-center space-y-4">
            <div className="relative rounded-3xl overflow-hidden shadow-2xl bg-gray-950 aspect-[4/3] max-h-[80vh] mx-auto">
              <img 
                src={activeImage.image} 
                alt={activeImage.title} 
                className="w-full h-full object-contain mx-auto"
              />
            </div>
            <div>
              <span className="text-xs font-bold text-emerald-400 uppercase tracking-widest">{activeImage.category}</span>
              <h3 className="text-white text-xl font-bold mt-1">{activeImage.title}</h3>
            </div>
          </div>
        </div>
      )}

      {/* --- MODAL DETAIL BACAAN BERITA/BLOG --- */}
      {selectedBlog && (
        <div className="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
          <div className="bg-white rounded-[32px] max-w-2xl w-full overflow-hidden shadow-2xl border border-gray-100 max-h-[90vh] flex flex-col animate-slideUp">
            
            <div className="relative aspect-video w-full bg-gray-100 shrink-0">
              <img src={selectedBlog.image} alt={selectedBlog.title} className="w-full h-full object-cover" />
              <button
                onClick={() => setSelectedBlog(null)}
                className="absolute top-4 right-4 p-2.5 rounded-full bg-white/90 text-gray-700 hover:bg-white shadow focus:outline-none"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            <div className="p-8 overflow-y-auto space-y-4">
              <div className="flex items-center space-x-2.5 text-xs text-blue-600 font-bold">
                <span className="bg-blue-50 px-3 py-1 rounded-full uppercase tracking-wider">{selectedBlog.category}</span>
                <span className="text-gray-300">•</span>
                <span className="text-gray-500 font-normal">{selectedBlog.date}</span>
                <span className="text-gray-300">•</span>
                <span className="text-gray-500 font-normal">Oleh {selectedBlog.author}</span>
              </div>
              
              <h3 className="text-xl sm:text-2xl font-black text-gray-900 leading-snug">
                {selectedBlog.title}
              </h3>
              
              <p className="text-sm text-gray-700 leading-relaxed font-normal whitespace-pre-wrap">
                {selectedBlog.content}
              </p>

              <div className="pt-6 border-t border-gray-100 flex items-center justify-between">
                <span className="text-xs text-gray-400">Butuh info lebih lanjut? Hubungi sekretariat kami.</span>
                <button
                  onClick={() => setSelectedBlog(null)}
                  className="bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold px-5 py-2.5 rounded-full transition"
                >
                  Tutup Bacaan
                </button>
              </div>
            </div>

          </div>
        </div>
      )}

    </div>
  );
}