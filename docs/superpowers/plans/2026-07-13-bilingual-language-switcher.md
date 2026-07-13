# Bilingual Language Switcher (ID ⇄ EN) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Let public visitors switch UI chrome between Indonesian (default) and English via a nav toggle beside search, persisted in session.

**Architecture:** Laravel native i18n keyed by the Indonesian source string. A `SetLocale` middleware reads the session locale on every web request (page + Livewire AJAX); a `GET /lang/{locale}` route writes it. All English overrides live in one `lang/en.json`. Static chrome in public blades gets wrapped in `__()`. Dynamic DB content and staff panels are untouched.

**Tech Stack:** Laravel 13, Livewire 4, Blade, Tailwind 4, Pest.

**Spec:** `docs/superpowers/specs/2026-07-13-bilingual-language-switcher-design.md`

---

## File Structure

| File | Responsibility |
| --- | --- |
| `.env`, `.env.example`, `phpunit.xml` | Default locale → `id` |
| `app/Http/Middleware/SetLocale.php` | Apply session locale per request (whitelisted) |
| `bootstrap/app.php` | Register `SetLocale` on web group |
| `routes/web.php` | `GET /lang/{locale}` switch route |
| `lang/en.json` | Indonesian → English string map |
| `resources/views/components/language-switcher.blade.php` | Reusable ID/EN toggle |
| `resources/views/components/layouts/public.blade.php` | Mount switcher (desktop + mobile) + `__()` chrome |
| `resources/views/livewire/public/*.blade.php` | `__()` chrome extraction |
| `tests/Feature/LocaleTest.php` | Middleware + route behavior |

---

## Task 1: Default locale → Indonesian

**Files:**
- Modify: `.env` (lines 7-8), `.env.example` (same keys), `phpunit.xml`

- [ ] **Step 1: Flip `.env` locale**

In `.env` change:
```
APP_LOCALE=id
APP_FALLBACK_LOCALE=id
```
Apply the identical change in `.env.example`.

- [ ] **Step 2: Pin test locale deterministically**

In `phpunit.xml`, inside the `<php>` block (next to the other `<env>` lines ~21-30), add:
```xml
<env name="APP_LOCALE" value="id"/>
<env name="APP_FALLBACK_LOCALE" value="id"/>
```

- [ ] **Step 3: Clear config cache**

Run: `php artisan config:clear`
Expected: `Configuration cache cleared successfully.` (or no error)

- [ ] **Step 4: Commit**

```bash
git add .env.example phpunit.xml
git commit -m "chore(i18n): default app locale to Indonesian"
```
(Note: `.env` is git-ignored; commit only `.env.example` + `phpunit.xml`. Edit local `.env` by hand.)

---

## Task 2: SetLocale middleware (TDD)

**Files:**
- Create: `app/Http/Middleware/SetLocale.php`
- Modify: `bootstrap/app.php:20-23`
- Test: `tests/Feature/LocaleTest.php`

- [ ] **Step 1: Write failing test**

Create `tests/Feature/LocaleTest.php`:
```php
<?php

use function Pest\Laravel\get;
use function Pest\Laravel\withSession;

test('default locale is indonesian', function () {
    get('/')->assertOk();
    expect(app()->getLocale())->toBe('id');
});

test('session locale switches app locale to english', function () {
    withSession(['locale' => 'en'])->get('/')->assertOk();
    expect(app()->getLocale())->toBe('en');
});

test('invalid session locale falls back to default', function () {
    withSession(['locale' => 'zz'])->get('/')->assertOk();
    expect(app()->getLocale())->toBe('id');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=LocaleTest`
Expected: FAIL — the english test asserts `en` but gets `id` (no middleware yet).

- [ ] **Step 3: Create the middleware**

`app/Http/Middleware/SetLocale.php`:
```php
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /** Locales the public site may render in. */
    private const SUPPORTED = ['id', 'en'];

    /**
     * Apply the visitor's chosen locale (session) to the app for this request.
     * Runs on full page loads and Livewire AJAX updates. Invalid/absent →
     * config default. Never reads locale from request input.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        if (in_array($locale, self::SUPPORTED, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
```

- [ ] **Step 4: Register on web group**

In `bootstrap/app.php`, add `SetLocale` to the web append block (before/with `TrackVisitor`):
```php
use App\Http\Middleware\SetLocale;
// ...
$middleware->web(append: [
    SetLocale::class,
    TrackVisitor::class,
]);
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --filter=LocaleTest`
Expected: PASS (3 tests). Requires `/` to render — the home page must return 200.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Middleware/SetLocale.php bootstrap/app.php tests/Feature/LocaleTest.php
git commit -m "feat(i18n): apply session locale via SetLocale middleware"
```

---

## Task 3: Language switch route (TDD)

**Files:**
- Modify: `routes/web.php` (Public section, after line ~30)
- Test: `tests/Feature/LocaleTest.php`

- [ ] **Step 1: Add failing test**

Append to `tests/Feature/LocaleTest.php`:
```php
test('switch route sets session locale and redirects back', function () {
    get('/lang/en', ['referer' => url('/tentang-kami')])
        ->assertRedirect('/tentang-kami')
        ->assertSessionHas('locale', 'en');
});

test('switch route rejects unsupported locale', function () {
    get('/lang/zz')->assertNotFound();
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=LocaleTest`
Expected: FAIL — route `/lang/{locale}` not defined (404 on the redirect test).

- [ ] **Step 3: Add the route**

In `routes/web.php`, in the `// Public` block add:
```php
// Language switch (chrome i18n). Whitelisted locales only.
Route::get('/lang/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['id', 'en'], true), 404);
    session(['locale' => $locale]);

    return back();
})->name('locale.switch');
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=LocaleTest`
Expected: PASS (5 tests).

- [ ] **Step 5: Commit**

```bash
git add routes/web.php tests/Feature/LocaleTest.php
git commit -m "feat(i18n): add /lang/{locale} switch route"
```

---

## Task 4: Language switcher component + nav placement

**Files:**
- Create: `resources/views/components/language-switcher.blade.php`
- Modify: `resources/views/components/layouts/public.blade.php` (desktop cluster ~248-250, mobile panel ~278-286)
- Modify: `lang/en.json` (created here)

- [ ] **Step 1: Create the switcher component**

`resources/views/components/language-switcher.blade.php`:
```blade
@props(['stacked' => false])
@php $current = app()->getLocale(); @endphp
<div {{ $attributes->class([
    'inline-flex items-center rounded-full bg-white/60 border border-white/60 p-1 text-xs font-semibold',
    'w-full justify-center' => $stacked,
]) }}>
    @foreach (['id' => 'ID', 'en' => 'EN'] as $code => $label)
        <a href="{{ route('locale.switch', $code) }}"
            class="px-3 py-1.5 rounded-full transition {{ $current === $code ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}"
            aria-label="{{ __('Ganti bahasa ke :lang', ['lang' => $label]) }}"
            @if ($current === $code) aria-current="true" @endif>
            {{ $label }}
        </a>
    @endforeach
</div>
```

- [ ] **Step 2: Mount in desktop nav (beside search)**

In `resources/views/components/layouts/public.blade.php`, in the right cluster (`<div class="flex items-center gap-3">`, ~line 248), immediately after `<livewire:public.global-search />`:
```blade
<x-language-switcher class="hidden sm:inline-flex" />
```

- [ ] **Step 3: Mount in mobile nav panel**

In the mobile nav `<div class="space-y-1">` (~line 278), after the PPDB link (~line 285), add:
```blade
<div class="mt-3 pt-3 border-t border-white/60">
    <x-language-switcher :stacked="true" />
</div>
```

- [ ] **Step 4: Create `lang/en.json` with the switcher string**

Create `lang/en.json`:
```json
{
    "Ganti bahasa ke :lang": "Switch language to :lang"
}
```

- [ ] **Step 5: Manual verify (no build needed — Blade)**

Run: `php artisan test --filter=LocaleTest` (still green) then eyeball with `php artisan serve` if available: switcher shows `ID | EN` next to search; clicking `EN` highlights EN and stays on the same page.
Expected: LocaleTest PASS; switcher visible.

- [ ] **Step 6: Commit**

```bash
git add resources/views/components/language-switcher.blade.php resources/views/components/layouts/public.blade.php lang/en.json
git commit -m "feat(i18n): language switcher toggle in public nav"
```

---

## Task 5: Extract chrome — public layout (nav + footer)

**Extraction rule (applies to Tasks 5-8):**
- Wrap ONLY static, human-visible Indonesian chrome in `__('<exact phrase>')`.
- NEVER wrap dynamic output (`{{ $news->title }}`, `Setting::get(...)`, dates, numbers).
- Keep the Indonesian phrase verbatim as the key (it is also the `id` rendering).
- For each wrapped phrase, add `"<phrase>": "<english>"` to `lang/en.json`.
- Leave code, classes, and attributes untouched.

**Files:**
- Modify: `resources/views/components/layouts/public.blade.php`
- Modify: `lang/en.json`

- [ ] **Step 1: Wrap the nav link labels**

In the `$links` array (~line 230), wrap each `name`:
```php
$links = [
    ['name' => __('Beranda'), 'route' => 'home'],
    ['name' => __('Tentang Kami'), 'route' => 'about'],
    ['name' => __('Program'), 'route' => 'programs.index'],
    ['name' => __('Berita'), 'route' => 'news.index'],
    ['name' => __('Guru'), 'route' => 'teachers.index'],
    ['name' => __('Kontak'), 'route' => 'contact'],
];
```

- [ ] **Step 2: Wrap remaining layout chrome**

Read the full file and wrap every remaining static Indonesian string in the header, event-theme banner, and footer. Known instances include the footer "Kontak" heading (~line 338) and the event banner label "Tema Hari Besar" (~line 295). The literal `PPDB` before `{{ ...ppdb_year... }}` stays as-is (brand acronym). Sweep the whole file — do not rely on this list being exhaustive.

- [ ] **Step 3: Add English entries to `lang/en.json`**

Merge these (plus any others found in Step 2) into `lang/en.json`:
```json
{
    "Beranda": "Home",
    "Tentang Kami": "About Us",
    "Program": "Programs",
    "Berita": "News",
    "Guru": "Teachers",
    "Kontak": "Contact",
    "Tema Hari Besar": "Special Occasion"
}
```

- [ ] **Step 4: Verify JSON is valid**

Run: `php -r "json_decode(file_get_contents('lang/en.json'), true, 512, JSON_THROW_ON_ERROR); echo 'valid';"`
Expected: `valid`

- [ ] **Step 5: Add a rendering assertion to the test**

Append to `tests/Feature/LocaleTest.php`:
```php
test('nav renders english when locale is en', function () {
    withSession(['locale' => 'en'])->get('/')->assertOk()->assertSee('Home');
});

test('nav renders indonesian by default', function () {
    get('/')->assertOk()->assertSee('Beranda');
});
```

- [ ] **Step 6: Run tests**

Run: `php artisan test --filter=LocaleTest`
Expected: PASS. If the `assertSee('Home')` fails, a nav label was not wrapped or the key/value mismatched in `en.json`.

- [ ] **Step 7: Commit**

```bash
git add resources/views/components/layouts/public.blade.php lang/en.json tests/Feature/LocaleTest.php
git commit -m "feat(i18n): translate public layout chrome"
```

---

## Task 6: Extract chrome — Home + About

**Files:**
- Modify: `resources/views/livewire/public/home.blade.php`, `resources/views/livewire/public/about.blade.php`
- Modify: `lang/en.json`

- [ ] **Step 1: Sweep both files**

Apply the extraction rule (Task 5 header) to `home.blade.php` and `about.blade.php`. Read each fully; wrap static section headings, CTA/button text, labels, and captions in `__()`. Skip anything rendered from `$variables` / `Setting::get`.

- [ ] **Step 2: Append English entries**

For every phrase wrapped in Step 1, add `"<indonesian>": "<english>"` to `lang/en.json`. Reuse existing keys if a phrase already exists (do not duplicate).

- [ ] **Step 3: Validate JSON**

Run: `php -r "json_decode(file_get_contents('lang/en.json'), true, 512, JSON_THROW_ON_ERROR); echo 'valid';"`
Expected: `valid`

- [ ] **Step 4: Smoke test both pages**

Run: `php artisan test --filter=LocaleTest`
Expected: PASS. Then load `/` and `/tentang-kami` under `?`-less session both locales if serving; confirm no raw `__(` leaks and layout intact.

- [ ] **Step 5: Commit**

```bash
git add resources/views/livewire/public/home.blade.php resources/views/livewire/public/about.blade.php lang/en.json
git commit -m "feat(i18n): translate home + about chrome"
```

---

## Task 7: Extract chrome — Programs, News, Teachers, Gallery

**Files:**
- Modify: `resources/views/livewire/public/programs-index.blade.php`, `program-show.blade.php`, `news-index.blade.php`, `news-show.blade.php`, `teachers.blade.php`, `album-show.blade.php`
- Modify: `lang/en.json`

- [ ] **Step 1: Sweep all six files**

Apply the extraction rule (Task 5 header) to each file. Typical chrome here: "Selengkapnya"/"Baca selengkapnya" (read more), "Kembali" (back), "Semua"/category filters, empty-state text ("Belum ada ..."), date/label prefixes like "Dipublikasikan". Wrap those; leave `{{ $program->name }}`, `{{ $news->body }}`, teacher names, photos untouched.

- [ ] **Step 2: Append English entries to `lang/en.json`** (reuse existing keys, validate JSON as in Task 6 Step 3).

- [ ] **Step 3: Test**

Run: `php artisan test --filter=LocaleTest`
Expected: PASS.

- [ ] **Step 4: Commit**

```bash
git add resources/views/livewire/public/programs-index.blade.php resources/views/livewire/public/program-show.blade.php resources/views/livewire/public/news-index.blade.php resources/views/livewire/public/news-show.blade.php resources/views/livewire/public/teachers.blade.php resources/views/livewire/public/album-show.blade.php lang/en.json
git commit -m "feat(i18n): translate programs/news/teachers/gallery chrome"
```

---

## Task 8: Extract chrome — Contact, PPDB, Visit, Search

**Files:**
- Modify: `resources/views/livewire/public/contact.blade.php`, `contact-form.blade.php`, `ppdb.blade.php`, `ppdb-form.blade.php`, `visit-form.blade.php`, `global-search.blade.php`
- Modify: `lang/en.json`

- [ ] **Step 1: Sweep all six files**

Apply the extraction rule (Task 5 header). These are form-heavy: wrap field **labels**, placeholders, helper text, submit-button text, and section headings. Leave `wire:model` names, validation *messages* (Indonesian for now — documented gap), and dynamic values untouched.

- [ ] **Step 2: Append English entries to `lang/en.json`** (reuse existing keys, validate JSON).

- [ ] **Step 3: Test**

Run: `php artisan test --filter=LocaleTest`
Expected: PASS.

- [ ] **Step 4: Commit**

```bash
git add resources/views/livewire/public/contact.blade.php resources/views/livewire/public/contact-form.blade.php resources/views/livewire/public/ppdb.blade.php resources/views/livewire/public/ppdb-form.blade.php resources/views/livewire/public/visit-form.blade.php resources/views/livewire/public/global-search.blade.php lang/en.json
git commit -m "feat(i18n): translate contact/ppdb/visit/search chrome"
```

---

## Task 9: Final verification

- [ ] **Step 1: Lint**

Run: `./vendor/bin/pint`
Expected: no unfixable errors.

- [ ] **Step 2: Static analysis (if used by project)**

Run: `./vendor/bin/phpstan analyse` (skip if not configured)
Expected: no new errors.

- [ ] **Step 3: Full test suite**

Run: `php artisan test`
Expected: all PASS.

- [ ] **Step 4: Grep for leaked/untranslated markers**

Run: `grep -rn "__(''" resources/views/livewire/public resources/views/components/layouts/public.blade.php`
Expected: no empty `__('')` calls.

- [ ] **Step 5: Commit any Pint fixes**

```bash
git add -A
git commit -m "style: pint formatting for i18n changes"
```

---

## Deliberate gaps (per spec — do NOT implement in this plan)

- Livewire form **validation messages** stay Indonesian.
- Persistence is **session**-scoped (no long-lived cookie).
- **DB content** (news/program/about bodies) untranslated.
- Admin / guru / auth panels untranslated.
