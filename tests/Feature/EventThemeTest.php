<?php

use App\Livewire\Admin\Settings as AdminSettings;
use App\Models\EventTheme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('highest priority active event theme is selected for a date', function () {
    EventTheme::query()->delete();

    EventTheme::create([
        'name' => 'Low Priority',
        'style' => 'default',
        'message' => null,
        'start_date' => '2026-08-17',
        'end_date' => '2026-08-17',
        'primary_color' => '#65ad36',
        'secondary_color' => '#ffffff',
        'accent_color' => '#f59e0b',
        'background_color' => '#f8fafc',
        'text_color' => '#0f172a',
        'priority' => 10,
        'repeat_annually' => false,
        'is_active' => true,
    ]);

    EventTheme::create([
        'name' => 'High Priority',
        'style' => 'independence',
        'message' => null,
        'start_date' => '2026-08-17',
        'end_date' => '2026-08-17',
        'primary_color' => '#dc2626',
        'secondary_color' => '#ffffff',
        'accent_color' => '#facc15',
        'background_color' => '#fff1f2',
        'text_color' => '#7f1d1d',
        'priority' => 90,
        'repeat_annually' => false,
        'is_active' => true,
    ]);

    expect(EventTheme::activeFor('2026-08-17')?->name)->toBe('High Priority');
});

test('annual event themes can cross the end of the year', function () {
    EventTheme::query()->delete();

    $theme = EventTheme::create([
        'name' => 'Year End',
        'style' => 'default',
        'message' => null,
        'start_date' => '2026-12-30',
        'end_date' => '2027-01-02',
        'primary_color' => '#65ad36',
        'secondary_color' => '#ffffff',
        'accent_color' => '#f59e0b',
        'background_color' => '#f8fafc',
        'text_color' => '#0f172a',
        'priority' => 10,
        'repeat_annually' => true,
        'is_active' => true,
    ]);

    expect($theme->isActiveOn('2028-01-01'))->toBeTrue()
        ->and($theme->isActiveOn('2028-02-01'))->toBeFalse();
});

test('admin settings page renders event theme management', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.settings'))
        ->assertOk()
        ->assertSee('Tema Event Hari Besar');
});

test('admin can save event theme with strict hex color validation', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin);
    Storage::fake('public');

    Livewire::test(AdminSettings::class)
        ->set('eventName', 'Tema Test')
        ->set('eventStyle', 'independence')
        ->set('eventMessage', 'Pesan tema test.')
        ->set('eventStartDate', '2026-08-17')
        ->set('eventEndDate', '2026-08-17')
        ->set('eventPrimaryColor', '#dc2626')
        ->set('eventSecondaryColor', '#ffffff')
        ->set('eventAccentColor', '#facc15')
        ->set('eventBackgroundColor', '#fff1f2')
        ->set('eventTextColor', '#7f1d1d')
        ->set('eventPriority', 99)
        ->set('eventRepeatAnnually', true)
        ->set('eventIsActive', true)
        ->set('eventBackgroundImageFile', UploadedFile::fake()->image('tema.jpg', 1600, 900))
        ->call('saveEventTheme')
        ->assertHasNoErrors();

    $theme = EventTheme::where('name', 'Tema Test')->first();

    expect($theme)->not->toBeNull()
        ->and($theme->background_image)->not->toBeNull();

    Storage::disk('public')->assertExists($theme->background_image);
});
