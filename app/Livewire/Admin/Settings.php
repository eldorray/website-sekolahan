<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithDeleteConfirm;
use App\Livewire\Concerns\WithNotifications;
use App\Models\EventTheme;
use App\Models\Setting;
use App\Support\ColorPalette;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithDeleteConfirm, WithFileUploads, WithNotifications;

    public array $data = [];

    public string $brandColor = '#65ad36';

    public ?int $eventEditingId = null;

    public string $eventName = '';

    public string $eventStyle = 'default';

    public string $eventMessage = '';

    public string $eventStartDate = '';

    public string $eventEndDate = '';

    public string $eventPrimaryColor = '#65ad36';

    public string $eventSecondaryColor = '#ffffff';

    public string $eventAccentColor = '#f59e0b';

    public string $eventBackgroundColor = '#f8fafc';

    public $eventBackgroundImageFile;

    public ?string $eventExistingBackgroundImage = null;

    public string $eventTextColor = '#0f172a';

    public int $eventPriority = 0;

    public bool $eventRepeatAnnually = false;

    public bool $eventIsActive = true;

    public string $eventSearch = '';

    public $app_logo_file;

    public $school_logo_file;

    public $favicon_file;

    public $hero_image_file;

    public array $fields = [
        'school_name' => 'Nama Sekolah',
        'headmaster_name' => 'Nama Kepala Sekolah',
        'tagline' => 'Tagline',
        'hero_welcome' => 'Hero - Sambutan',
        'hero_title_1' => 'Hero - Judul 1',
        'hero_title_2' => 'Hero - Judul 2',
        'hero_subtitle' => 'Hero - Subjudul',
        'hero_badge' => 'Hero - Badge',
        'stat_students' => 'Statistik - Siswa',
        'stat_teachers' => 'Statistik - Guru',
        'stat_years' => 'Statistik - Tahun',
        'visi' => 'Visi',
        'misi' => 'Misi',
        'about_history' => 'Tentang - Sejarah',
        'address' => 'Alamat',
        'phone' => 'Telepon',
        'email' => 'Email',
        'website' => 'Website',
        'instagram' => 'Instagram URL',
        'facebook' => 'Facebook URL',
        'youtube' => 'Youtube URL',
        'tiktok' => 'TikTok URL',
        'ppdb_year' => 'PPDB - Tahun',
        'footer_about' => 'Footer - Deskripsi',
    ];

    public function mount(): void
    {
        foreach (array_keys($this->fields) as $key) {
            $this->data[$key] = Setting::get($key, '');
        }
        $this->brandColor = Setting::get('brand_color', '#65ad36');
    }

    public function applyPreset(string $hex): void
    {
        $this->brandColor = $hex;
    }

    public function applyEventPreset(string $style): void
    {
        $preset = $this->eventThemePresets()[$style] ?? null;

        if (! $preset) {
            return;
        }

        $this->eventStyle = $style;
        $this->eventName = $preset['name'];
        $this->eventMessage = $preset['message'];
        $this->eventPrimaryColor = $preset['primary_color'];
        $this->eventSecondaryColor = $preset['secondary_color'];
        $this->eventAccentColor = $preset['accent_color'];
        $this->eventBackgroundColor = $preset['background_color'];
        $this->eventTextColor = $preset['text_color'];
    }

    public function save(): void
    {
        $this->validate([
            'app_logo_file' => 'nullable|image|max:2048',
            'school_logo_file' => 'nullable|image|max:2048',
            'favicon_file' => 'nullable|file|mimes:png,ico,jpg,jpeg,svg|max:512',
            'hero_image_file' => 'nullable|image|max:4096',
            'brandColor' => $this->hexColorRule(),
        ]);

        foreach ($this->data as $key => $value) {
            Setting::set($key, (string) $value);
        }

        $hex = '#'.ltrim($this->brandColor, '#');
        Setting::set('brand_color', $hex);

        foreach ([
            'app_logo' => 'app_logo_file',
            'school_logo' => 'school_logo_file',
            'favicon' => 'favicon_file',
            'hero_image' => 'hero_image_file',
        ] as $key => $prop) {
            if ($this->$prop) {
                $old = Setting::get($key);
                if ($old) {
                    Storage::disk('public')->delete($old);
                }
                $path = $this->$prop->store('branding', 'public');
                Setting::set($key, $path);
                $this->$prop = null;
            }
        }

        $this->notifySuccess('Pengaturan berhasil disimpan.');
    }

    public function editEventTheme(int $id): void
    {
        $theme = EventTheme::findOrFail($id);

        $this->eventEditingId = $theme->id;
        $this->eventName = $theme->name;
        $this->eventStyle = $theme->style;
        $this->eventMessage = $theme->message ?? '';
        $this->eventStartDate = $theme->start_date?->format('Y-m-d') ?? '';
        $this->eventEndDate = $theme->end_date?->format('Y-m-d') ?? '';
        $this->eventPrimaryColor = $theme->primary_color;
        $this->eventSecondaryColor = $theme->secondary_color;
        $this->eventAccentColor = $theme->accent_color;
        $this->eventBackgroundColor = $theme->background_color;
        $this->eventBackgroundImageFile = null;
        $this->eventExistingBackgroundImage = $theme->background_image;
        $this->eventTextColor = $theme->text_color;
        $this->eventPriority = $theme->priority;
        $this->eventRepeatAnnually = $theme->repeat_annually;
        $this->eventIsActive = $theme->is_active;
    }

    public function saveEventTheme(): void
    {
        $this->validate($this->eventThemeRules(), [], [
            'eventName' => 'nama tema',
            'eventStyle' => 'gaya tema',
            'eventMessage' => 'pesan tema',
            'eventStartDate' => 'tanggal mulai',
            'eventEndDate' => 'tanggal selesai',
            'eventPrimaryColor' => 'warna utama',
            'eventSecondaryColor' => 'warna sekunder',
            'eventAccentColor' => 'warna aksen',
            'eventBackgroundColor' => 'warna latar',
            'eventBackgroundImageFile' => 'gambar background',
            'eventTextColor' => 'warna teks',
            'eventPriority' => 'prioritas',
        ]);

        if (! $this->eventRepeatAnnually && Carbon::parse($this->eventEndDate)->lt(Carbon::parse($this->eventStartDate))) {
            $this->addError('eventEndDate', 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.');

            return;
        }

        $data = [
            'name' => $this->eventName,
            'style' => $this->eventStyle,
            'message' => $this->eventMessage ?: null,
            'start_date' => $this->eventStartDate,
            'end_date' => $this->eventEndDate,
            'primary_color' => $this->normalizeHex($this->eventPrimaryColor),
            'secondary_color' => $this->normalizeHex($this->eventSecondaryColor),
            'accent_color' => $this->normalizeHex($this->eventAccentColor),
            'background_color' => $this->normalizeHex($this->eventBackgroundColor),
            'text_color' => $this->normalizeHex($this->eventTextColor),
            'priority' => $this->eventPriority,
            'repeat_annually' => $this->eventRepeatAnnually,
            'is_active' => $this->eventIsActive,
        ];

        if ($this->eventBackgroundImageFile) {
            if ($this->eventExistingBackgroundImage) {
                Storage::disk('public')->delete($this->eventExistingBackgroundImage);
            }

            $data['background_image'] = $this->eventBackgroundImageFile->store('event-themes', 'public');
        }

        if ($this->eventEditingId) {
            EventTheme::findOrFail($this->eventEditingId)->update($data);
            $this->notifySuccess('Tema event berhasil diperbarui.');
        } else {
            EventTheme::create($data);
            $this->notifySuccess('Tema event berhasil dibuat.');
        }

        $this->resetEventThemeForm();
    }

    public function delete(int $id): void
    {
        $theme = EventTheme::findOrFail($id);

        if ($theme->background_image) {
            Storage::disk('public')->delete($theme->background_image);
        }

        $theme->delete();

        if ($this->eventEditingId === $id) {
            $this->resetEventThemeForm();
        }

        $this->notifySuccess('Tema event berhasil dihapus.');
    }

    public function resetEventThemeForm(): void
    {
        $this->eventEditingId = null;
        $this->eventName = '';
        $this->eventStyle = 'default';
        $this->eventMessage = '';
        $this->eventStartDate = '';
        $this->eventEndDate = '';
        $this->eventPrimaryColor = '#65ad36';
        $this->eventSecondaryColor = '#ffffff';
        $this->eventAccentColor = '#f59e0b';
        $this->eventBackgroundColor = '#f8fafc';
        $this->eventBackgroundImageFile = null;
        $this->eventExistingBackgroundImage = null;
        $this->eventTextColor = '#0f172a';
        $this->eventPriority = 0;
        $this->eventRepeatAnnually = false;
        $this->eventIsActive = true;
        $this->resetValidation();
    }

    public function removeImage(string $key): void
    {
        $old = Setting::get($key);
        if ($old) {
            Storage::disk('public')->delete($old);
        }
        Setting::set($key, '');
        $this->notifySuccess('Gambar berhasil dihapus.');
    }

    public function removeEventBackgroundImage(): void
    {
        $this->eventBackgroundImageFile = null;

        if ($this->eventEditingId && $this->eventExistingBackgroundImage) {
            Storage::disk('public')->delete($this->eventExistingBackgroundImage);
            EventTheme::findOrFail($this->eventEditingId)->update(['background_image' => null]);
            $this->notifySuccess('Background tema event berhasil dihapus.');
        }

        $this->eventExistingBackgroundImage = null;
    }

    public function render()
    {
        $logos = [
            'app_logo' => Setting::get('app_logo'),
            'school_logo' => Setting::get('school_logo'),
            'favicon' => Setting::get('favicon'),
            'hero_image' => Setting::get('hero_image'),
        ];

        $palette = ColorPalette::fromHex($this->brandColor);
        $presets = ColorPalette::presets();
        $eventPresets = $this->eventThemePresets();
        $eventThemes = EventTheme::query()
            ->when($this->eventSearch, fn ($query) => $query->where('name', 'like', '%'.$this->eventSearch.'%'))
            ->orderByDesc('is_active')
            ->orderByDesc('priority')
            ->orderBy('start_date')
            ->get();

        return view('livewire.admin.settings', compact('eventPresets', 'eventThemes', 'logos', 'palette', 'presets'))
            ->layout('layouts.panel', ['title' => 'Pengaturan']);
    }

    protected function eventThemeRules(): array
    {
        $hex = $this->hexColorRule();

        return [
            'eventName' => 'required|string|max:120',
            'eventStyle' => 'required|string|in:'.implode(',', array_keys($this->eventThemePresets())),
            'eventMessage' => 'nullable|string|max:180',
            'eventStartDate' => 'required|date',
            'eventEndDate' => 'required|date',
            'eventPrimaryColor' => $hex,
            'eventSecondaryColor' => $hex,
            'eventAccentColor' => $hex,
            'eventBackgroundColor' => $hex,
            'eventBackgroundImageFile' => 'nullable|image|max:4096',
            'eventTextColor' => $hex,
            'eventPriority' => 'integer|min:0|max:1000',
            'eventRepeatAnnually' => 'boolean',
            'eventIsActive' => 'boolean',
        ];
    }

    protected function hexColorRule(): array
    {
        return ['required', 'regex:/^#?([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/'];
    }

    protected function eventThemePresets(): array
    {
        return [
            'default' => [
                'label' => 'Umum',
                'name' => 'Tema Event',
                'message' => 'Selamat memperingati hari besar.',
                'primary_color' => '#65ad36',
                'secondary_color' => '#ffffff',
                'accent_color' => '#f59e0b',
                'background_color' => '#f8fafc',
                'text_color' => '#0f172a',
            ],
            'independence' => [
                'label' => '17 Agustus',
                'name' => 'Hari Kemerdekaan RI',
                'message' => 'Dirgahayu Republik Indonesia.',
                'primary_color' => '#dc2626',
                'secondary_color' => '#ffffff',
                'accent_color' => '#facc15',
                'background_color' => '#fff1f2',
                'text_color' => '#7f1d1d',
            ],
            'ramadan' => [
                'label' => 'Ramadhan',
                'name' => 'Ramadhan',
                'message' => 'Marhaban ya Ramadhan.',
                'primary_color' => '#047857',
                'secondary_color' => '#ecfdf5',
                'accent_color' => '#f59e0b',
                'background_color' => '#ecfdf5',
                'text_color' => '#064e3b',
            ],
            'eid-fitr' => [
                'label' => 'Idul Fitri',
                'name' => 'Idul Fitri',
                'message' => 'Taqabbalallahu minna wa minkum.',
                'primary_color' => '#16a34a',
                'secondary_color' => '#f0fdf4',
                'accent_color' => '#facc15',
                'background_color' => '#f0fdf4',
                'text_color' => '#14532d',
            ],
            'eid-adha' => [
                'label' => 'Idul Adha',
                'name' => 'Idul Adha',
                'message' => 'Selamat Hari Raya Idul Adha.',
                'primary_color' => '#b45309',
                'secondary_color' => '#fffbeb',
                'accent_color' => '#15803d',
                'background_color' => '#fffbeb',
                'text_color' => '#78350f',
            ],
            'islamic-new-year' => [
                'label' => '1 Muharam',
                'name' => 'Tahun Baru Islam',
                'message' => 'Selamat Tahun Baru Islam.',
                'primary_color' => '#0f766e',
                'secondary_color' => '#f0fdfa',
                'accent_color' => '#fbbf24',
                'background_color' => '#f0fdfa',
                'text_color' => '#134e4a',
            ],
            'maulid' => [
                'label' => 'Maulid Nabi',
                'name' => 'Maulid Nabi Muhammad SAW',
                'message' => 'Meneladani akhlak Rasulullah SAW.',
                'primary_color' => '#2563eb',
                'secondary_color' => '#eff6ff',
                'accent_color' => '#f59e0b',
                'background_color' => '#eff6ff',
                'text_color' => '#1e3a8a',
            ],
            'isra-miraj' => [
                'label' => 'Isra Miraj',
                'name' => 'Isra Miraj Nabi Muhammad SAW',
                'message' => 'Memaknai perjalanan spiritual dan perintah shalat.',
                'primary_color' => '#4f46e5',
                'secondary_color' => '#eef2ff',
                'accent_color' => '#fbbf24',
                'background_color' => '#eef2ff',
                'text_color' => '#312e81',
            ],
        ];
    }

    protected function normalizeHex(string $hex): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return '#'.strtolower($hex);
    }
}
