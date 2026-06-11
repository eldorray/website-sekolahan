<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin:create makes an active admin user', function () {
    $this->artisan('admin:create', [
        '--name' => 'Kepala Sekolah',
        '--email' => 'kepala@sekolah.sch.id',
        '--password' => 'super-secret-123',
    ])->assertExitCode(0);

    $user = User::firstWhere('email', 'kepala@sekolah.sch.id');

    expect($user)->not->toBeNull()
        ->and($user->role)->toBe('admin')
        ->and($user->is_active)->toBeTrue()
        ->and($user->password)->not->toBe('super-secret-123'); // hashed
});

test('admin:create rejects a short password', function () {
    $this->artisan('admin:create', [
        '--name' => 'X',
        '--email' => 'x@sekolah.sch.id',
        '--password' => 'short',
    ])->assertExitCode(1);

    expect(User::where('email', 'x@sekolah.sch.id')->exists())->toBeFalse();
});

test('admin:create rejects a duplicate email', function () {
    User::factory()->create(['email' => 'dup@sekolah.sch.id']);

    $this->artisan('admin:create', [
        '--name' => 'Dup',
        '--email' => 'dup@sekolah.sch.id',
        '--password' => 'super-secret-123',
    ])->assertExitCode(1);
});
