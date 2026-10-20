<?php

namespace App\Console\Commands;

use App\Services\SppBilling;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

class GenerateSppInvoices extends Command
{
    protected $signature = 'spp:generate {--period= : Bulan tagihan YYYY-MM} {--student= : ID siswa opsional}';

    protected $description = 'Buat snapshot tagihan SPP bulanan secara idempoten';

    public function handle(SppBilling $billing): int
    {
        $student = $this->option('student');
        if ($student !== null && (! ctype_digit((string) $student) || (int) $student < 1)) {
            $this->error('ID siswa harus berupa bilangan bulat positif.');

            return self::FAILURE;
        }
        try {
            $count = $billing->generate($this->option('period') ?? now()->format('Y-m'), $student !== null ? (int) $student : null);
        } catch (ValidationException $exception) {
            $this->error('Periode atau data tagihan tidak valid. Gunakan YYYY-MM dan periksa biaya siswa.');

            return self::FAILURE;
        }
        $this->info("{$count} tagihan dibuat.");

        return self::SUCCESS;
    }
}
