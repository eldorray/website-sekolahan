<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithNotifications;
use App\Models\Setting;
use App\Support\ColorPalette;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithFileUploads, WithNotifications;

    public array $data = [];

    public string $brandColor = '#65ad36';

    public $app_logo_file;
    public $school_logo_file;
    public $favicon_file;
    public $hero_image_file;

    public array $fields = [
        'school_name' => 'Nama Sekolah',
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

    public function save(): void
    {
        $this->validate([
            'app_logo_file' => 'nullable|image|max:2048',
            'school_logo_file' => 'nullable|image|max:2048',
            'favicon_file' => 'nullable|file|mimes:png,ico,jpg,jpeg,svg|max:512',
            'hero_image_file' => 'nullable|image|max:4096',
            'brandColor' => 'required|regex:/^#?[a-fA-F0-9]{3,6}$/',
        ]);

        foreach ($this->data as $key => $value) {
            Setting::set($key, (string) $value);
        }

        $hex = '#' . ltrim($this->brandColor, '#');
        Setting::set('brand_color', $hex);

        foreach ([
            'app_logo' => 'app_logo_file',
            'school_logo' => 'school_logo_file',
            'favicon' => 'favicon_file',
            'hero_image' => 'hero_image_file',
        ] as $key => $prop) {
            if ($this->$prop) {
                $old = Setting::get($key);
                if ($old) Storage::disk('public')->delete($old);
                $path = $this->$prop->store('branding', 'public');
                Setting::set($key, $path);
                $this->$prop = null;
            }
        }

        $this->notifySuccess('Pengaturan berhasil disimpan.');
    }

    public function removeImage(string $key): void
    {
        $old = Setting::get($key);
        if ($old) Storage::disk('public')->delete($old);
        Setting::set($key, '');
        $this->notifySuccess('Gambar berhasil dihapus.');
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

        return view('livewire.admin.settings', compact('logos', 'palette', 'presets'))
            ->layout('layouts.panel', ['title' => 'Pengaturan']);
    }
}
