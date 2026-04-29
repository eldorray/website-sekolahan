<?php

namespace App\Support;

class ColorPalette
{
    /**
     * Generate 11 brand shades (50-950) from a base hex color (used as 500).
     * Returns array keyed by shade number with hex values.
     */
    public static function fromHex(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
            $hex = '65ad36'; // fallback green-lime
        }

        [$h, $s, $l] = self::hexToHsl($hex);

        // Target lightness for each shade (mirrors Tailwind's perceptual scale)
        $targets = [
            50 => 0.97,
            100 => 0.93,
            200 => 0.85,
            300 => 0.74,
            400 => 0.62,
            500 => $l,        // user color
            600 => max(0.05, $l * 0.80),
            700 => max(0.05, $l * 0.62),
            800 => max(0.05, $l * 0.48),
            900 => max(0.05, $l * 0.38),
            950 => max(0.03, $l * 0.24),
        ];

        // Saturation easing (lighter shades a bit less saturated)
        $satTargets = [
            50 => $s * 0.55,
            100 => $s * 0.70,
            200 => $s * 0.85,
            300 => $s * 0.95,
            400 => $s,
            500 => $s,
            600 => $s,
            700 => $s * 0.95,
            800 => $s * 0.85,
            900 => $s * 0.75,
            950 => $s * 0.65,
        ];

        $palette = [];
        foreach ($targets as $shade => $light) {
            $palette[$shade] = self::hslToHex($h, min(1, $satTargets[$shade]), max(0, min(1, $light)));
        }
        return $palette;
    }

    public static function presets(): array
    {
        return [
            'green-lime' => ['name' => 'Green Lime (default)', 'hex' => '#65ad36'],
            'emerald' => ['name' => 'Emerald', 'hex' => '#10b981'],
            'blue' => ['name' => 'Blue', 'hex' => '#3b82f6'],
            'indigo' => ['name' => 'Indigo', 'hex' => '#6366f1'],
            'violet' => ['name' => 'Violet', 'hex' => '#8b5cf6'],
            'pink' => ['name' => 'Pink', 'hex' => '#ec4899'],
            'red' => ['name' => 'Red', 'hex' => '#ef4444'],
            'orange' => ['name' => 'Orange', 'hex' => '#f97316'],
            'amber' => ['name' => 'Amber', 'hex' => '#f59e0b'],
            'teal' => ['name' => 'Teal', 'hex' => '#14b8a6'],
            'cyan' => ['name' => 'Cyan', 'hex' => '#06b6d4'],
            'slate' => ['name' => 'Slate', 'hex' => '#64748b'],
        ];
    }

    private static function hexToHsl(string $hex): array
    {
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;

        if ($max === $min) {
            return [0, 0, $l];
        }

        $d = $max - $min;
        $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

        $h = match (true) {
            $max === $r => (($g - $b) / $d + ($g < $b ? 6 : 0)),
            $max === $g => (($b - $r) / $d + 2),
            default => (($r - $g) / $d + 4),
        };
        $h /= 6;

        return [$h, $s, $l];
    }

    private static function hslToHex(float $h, float $s, float $l): string
    {
        if ($s === 0.0) {
            $r = $g = $b = $l;
        } else {
            $hue2rgb = function ($p, $q, $t) {
                if ($t < 0) $t += 1;
                if ($t > 1) $t -= 1;
                if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
                if ($t < 1/2) return $q;
                if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
                return $p;
            };
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            $r = $hue2rgb($p, $q, $h + 1/3);
            $g = $hue2rgb($p, $q, $h);
            $b = $hue2rgb($p, $q, $h - 1/3);
        }
        return sprintf('#%02x%02x%02x', round($r * 255), round($g * 255), round($b * 255));
    }
}
