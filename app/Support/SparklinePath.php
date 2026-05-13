<?php

namespace App\Support;

class SparklinePath
{
    public static function build(array $points, float $w = 100, float $h = 30, float $pad = 2): string
    {
        $n = count($points);
        if ($n === 0) {
            return '';
        }
        if ($n === 1) {
            $y = $h / 2;
            return sprintf('M 0 %.2f L %.2f %.2f', $y, $w, $y);
        }

        $min = min($points);
        $max = max($points);
        $range = $max - $min;
        $flat = $range <= 0;

        $coords = [];
        foreach ($points as $i => $val) {
            $x = ($i / ($n - 1)) * $w;
            if ($flat) {
                $y = $h / 2;
            } else {
                $norm = ($val - $min) / $range;
                $y = $pad + (1 - $norm) * ($h - 2 * $pad);
            }
            $coords[] = [$x, $y];
        }

        $tension = 6;
        $path = sprintf('M %.2f %.2f', $coords[0][0], $coords[0][1]);
        for ($i = 0; $i < $n - 1; $i++) {
            $p0 = $coords[max(0, $i - 1)];
            $p1 = $coords[$i];
            $p2 = $coords[$i + 1];
            $p3 = $coords[min($n - 1, $i + 2)];
            $cp1x = $p1[0] + ($p2[0] - $p0[0]) / $tension;
            $cp1y = $p1[1] + ($p2[1] - $p0[1]) / $tension;
            $cp2x = $p2[0] - ($p3[0] - $p1[0]) / $tension;
            $cp2y = $p2[1] - ($p3[1] - $p1[1]) / $tension;
            $path .= sprintf(' C %.2f %.2f, %.2f %.2f, %.2f %.2f',
                $cp1x, $cp1y, $cp2x, $cp2y, $p2[0], $p2[1]);
        }

        return $path;
    }
}
