<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create
        {--name= : Nama admin}
        {--email= : Email admin}
        {--password= : Password (jika kosong akan ditanyakan)}';

    protected $description = 'Buat akun admin baru secara interaktif (pengganti kredensial default).';

    public function handle(): int
    {
        $name = $this->option('name') ?: text('Nama admin', required: true);
        $email = $this->option('email') ?: text('Email admin', required: true);
        $plainPassword = $this->option('password') ?: password('Password (min. 8 karakter)', required: true);

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $plainPassword],
            [
                'name' => 'required|string|max:160',
                'email' => 'required|email|max:160|unique:users,email',
                'password' => 'required|string|min:8',
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($plainPassword),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->info("Admin '{$user->email}' berhasil dibuat.");

        return self::SUCCESS;
    }
}
