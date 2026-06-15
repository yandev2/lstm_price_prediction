<?php

namespace App\Filament\Traits;

trait HasChartColors
{
    protected array $chartColors = [
        '#3B82F6',
        '#EF4444',
        '#10B981',
        '#F59E0B',
        '#8B5CF6',
        '#06B6D4',
        '#EC4899',
        '#84CC16',
    ];

    protected function hexToRgba(string $hex, float $opacity = 0.2): string
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) === 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return "rgba($r, $g, $b, $opacity)";
    }
}
