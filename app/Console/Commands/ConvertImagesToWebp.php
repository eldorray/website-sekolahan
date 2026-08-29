<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

/**
 * One-off cleanup: uploads made before the WebP switch are still JPEG/PNG.
 * Re-encodes them on the public disk and rewrites every stored reference.
 */
class ConvertImagesToWebp extends Command
{
    protected $signature = 'images:webp {--dry-run : List what would change without touching anything}
                                        {--quality=75 : WebP encoder quality}';

    protected $description = 'Re-encode existing JPEG/PNG uploads to WebP and update paths in the database';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $quality = (int) $this->option('quality');
        $disk = Storage::disk('public');

        $targets = array_values(array_filter(
            $disk->allFiles(),
            fn (string $path) => in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'], true),
        ));

        if (! $targets) {
            $this->info('No JPEG/PNG uploads left to convert.');

            return self::SUCCESS;
        }

        $this->info(count($targets).' file(s) to convert'.($dry ? ' (dry run)' : '').'.');

        $manager = new ImageManager(new Driver);
        $map = [];

        foreach ($targets as $old) {
            $new = preg_replace('/\.[^.]+$/', '.webp', $old);

            if ($disk->exists($new)) {
                $this->warn("skip {$old} — {$new} already exists");

                continue;
            }

            if ($dry) {
                $this->line("  {$old} -> {$new}");
                $map[$old] = $new;

                continue;
            }

            try {
                $encoded = $manager->decodePath($disk->path($old))->scaleDown(width: 1600)
                    ->encode(new WebpEncoder(quality: $quality));
            } catch (\Throwable $e) {
                $this->warn("skip {$old} — {$e->getMessage()}");

                continue;
            }

            $disk->put($new, (string) $encoded);
            $disk->delete($old);
            $map[$old] = $new;
        }

        if (! $map) {
            return self::SUCCESS;
        }

        $rows = $dry ? 0 : $this->rewriteReferences($map);
        $this->info(count($map).' file(s) converted, '.$rows.' database value(s) rewritten.');

        if (! $dry) {
            // Settings are cached forever; drop the stale image paths.
            foreach (DB::table('settings')->pluck('key') as $key) {
                Cache::forget('setting.'.$key);
            }
        }

        return self::SUCCESS;
    }

    /**
     * Replace old paths wherever they are stored — plain columns and rich-text bodies alike.
     *
     * @param  array<string, string>  $map
     */
    private function rewriteReferences(array $map): int
    {
        $textTypes = ['char', 'varchar', 'text', 'tinytext', 'mediumtext', 'longtext', 'string', 'json'];
        $touched = 0;

        foreach (Schema::getTables() as $table) {
            $name = $table['name'];
            if (in_array($name, ['migrations', 'cache', 'cache_locks', 'sessions', 'jobs', 'failed_jobs'], true)) {
                continue;
            }

            foreach (Schema::getColumns($name) as $column) {
                if (! in_array(strtolower($column['type_name']), $textTypes, true)) {
                    continue;
                }

                foreach ($map as $old => $new) {
                    $touched += DB::table($name)
                        ->where($column['name'], 'like', '%'.$old.'%')
                        ->update([$column['name'] => DB::raw('REPLACE('.$column['name'].', '.DB::getPdo()->quote($old).', '.DB::getPdo()->quote($new).')')]);
                }
            }
        }

        return $touched;
    }
}
