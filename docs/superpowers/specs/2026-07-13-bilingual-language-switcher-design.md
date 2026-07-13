# Bilingual Language Switcher (ID ⇄ EN) — Design

**Date:** 2026-07-13
**Status:** Approved (pending spec review)

## Goal

Public website visitors can switch the UI language between Indonesian (default)
and English. Phase 1 ships **English** as the second language.

## Scope decisions (confirmed with owner)

- **What translates:** UI *chrome* only — nav, buttons, labels, footer, static
  section headings on public pages. Dynamic DB content (News, Program, About
  body text) stays Indonesian by design.
- **Persistence:** browser **session** (Laravel session), same URLs. No `/en/`
  URL prefix.
- **Switcher placement:** compact `ID | EN` toggle in the public nav, right
  beside the search (`<livewire:public.global-search />`), before the dark-mode
  toggle. Mirrored in the mobile nav panel.
- **Out of scope:** admin / guru / auth panels (staff-only, stay Indonesian).

## Approach — Laravel native i18n, keyed by Indonesian source string

No new dependency, no DB/schema change.

### 1. Locale files

- App default locale → `id` (the source language). Change `.env` +
  `.env.example`: `APP_LOCALE=id`, `APP_FALLBACK_LOCALE=id`.
- One translation file: `lang/en.json`, mapping Indonesian → English:
  ```json
  { "Beranda": "Home", "Tentang Kami": "About Us", "Program": "Programs",
    "Berita": "News", "Guru": "Teachers", "Kontak": "Contact" }
  ```
- No `id.json` needed. When locale is `id`, or a key is missing from
  `en.json`, `__('Beranda')` returns the key itself — the Indonesian text.
  Graceful fallback for free.

### 2. `SetLocale` middleware

- `App\Http\Middleware\SetLocale`.
- Reads `session('locale', config('app.locale'))`, whitelisted to `['id','en']`
  (anything else → default `id`), calls `app()->setLocale($locale)`.
- Registered on the `web` group via `bootstrap/app.php` `web(append: [...])`,
  so it runs after `StartSession` (needed to read the session) and applies to
  full page loads *and* Livewire AJAX requests — `__()` stays correct after
  `wire:navigate` and component updates.

### 3. Switch route

- `GET /lang/{locale}` → `name('locale.switch')`, plain closure (no Livewire).
- Validate `$locale` ∈ `['id','en']`; on match `session(['locale' => $locale])`;
  `redirect()->back()`.

### 4. Switcher UI

- Blade component `resources/views/components/language-switcher.blade.php`
  (`<x-language-switcher />`).
- Two links → `route('locale.switch','id')` / `route('locale.switch','en')`,
  active locale highlighted (`app()->getLocale()`), styled to match the
  liquid-glass pill nav.
- Placed at [public.blade.php:249](../../../resources/views/components/layouts/public.blade.php)
  next to search; a stacked variant added inside the mobile nav panel.

### 5. String extraction (the long pole)

Wrap hardcoded Indonesian chrome in `__()` and add each string to `lang/en.json`:

- Public layout: `components/layouts/public.blade.php` — nav `$links` names,
  PPDB button, footer headings/links, event-theme label.
- 14 public Livewire views under `resources/views/livewire/public/`: home,
  about, programs-index, program-show, news-index, news-show, teachers,
  album-show, contact, contact-form, ppdb, ppdb-form, visit-form,
  global-search.

Rules:
- Only *static* chrome. Never wrap dynamic DB output (`{{ $news->title }}`).
- Keep the Indonesian phrase verbatim as the `__()` key.
- Mechanical + parallelizable per file during implementation.

### 6. Testing

Feature test(s):
- Visiting `/` with `session(['locale'=>'en'])` renders "Home"; default session
  renders "Beranda".
- `GET /lang/en` sets `session('locale') === 'en'` and redirects back.
- `GET /lang/xx` (invalid) does not set an invalid locale.

## Deliberate gaps (ponytail — add later if needed)

- **Livewire form validation messages** stay Indonesian. Add `lang/en/validation.php`
  + attribute names only if the English form experience matters.
- **Persistence** is session-scoped (owner's choice). Upgrade to a long-lived
  cookie for "remember across visits."
- **DB content** untranslated by design (chrome-only scope).

## Files touched

| File | Change |
| --- | --- |
| `.env`, `.env.example` | `APP_LOCALE=id`, `APP_FALLBACK_LOCALE=id` |
| `app/Http/Middleware/SetLocale.php` | new middleware |
| `bootstrap/app.php` | append `SetLocale` to web group |
| `routes/web.php` | `GET /lang/{locale}` switch route |
| `lang/en.json` | new — Indonesian→English map |
| `resources/views/components/language-switcher.blade.php` | new switcher component |
| `resources/views/components/layouts/public.blade.php` | place switcher (desktop + mobile) + `__()` chrome |
| `resources/views/livewire/public/*.blade.php` | `__()` chrome extraction |
| `tests/Feature/LocaleTest.php` | new feature test |
