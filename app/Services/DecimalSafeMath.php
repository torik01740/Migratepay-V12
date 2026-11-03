<?php

namespace App\Services;

use InvalidArgumentException;

class DecimalSafeMath
{
    /**
     * Canonicalize any numeric representation into an exact decimal string
     * to the specified scale using strict BCMath string-based arithmetic.
     * Zero PHP native float casting or number_format((float)...) allowed.
     */
    public static function canonicalize(mixed $value, int $scale = 2): string
    {
        if ($value === null || $value === '') {
            return static::formatZero($scale);
        }

        $str = trim((string)$value);

        // Handle scientific notation via pure string expansion if encountered
        if (stripos($str, 'e') !== false) {
            $str = static::expandScientificNotation($str);
        }

        // Validate that string is a valid numeric pattern
        if (!preg_match('/^-?\d+(\.\d+)?$/', $str)) {
            throw new InvalidArgumentException("Invalid numeric string for decimal arithmetic: '{$value}'");
        }

        return static::roundHalfUp($str, $scale);
    }

    /**
     * Add two decimal strings using BCMath.
     */
    public static function add(string $a, string $b, int $scale = 2): string
    {
        $a = static::canonicalize($a, $scale + 4);
        $b = static::canonicalize($b, $scale + 4);
        $result = bcadd($a, $b, $scale + 4);
        return static::roundHalfUp($result, $scale);
    }

    /**
     * Subtract two decimal strings using BCMath.
     */
    public static function sub(string $a, string $b, int $scale = 2): string
    {
        $a = static::canonicalize($a, $scale + 4);
        $b = static::canonicalize($b, $scale + 4);
        $result = bcsub($a, $b, $scale + 4);
        return static::roundHalfUp($result, $scale);
    }

    /**
     * Multiply two decimal strings using BCMath.
     */
    public static function mul(string $a, string $b, int $scale = 2): string
    {
        $a = static::canonicalize($a, $scale + 4);
        $b = static::canonicalize($b, $scale + 4);
        $result = bcmul($a, $b, $scale + 4);
        return static::roundHalfUp($result, $scale);
    }

    /**
     * Divide two decimal strings using BCMath.
     */
    public static function div(string $a, string $b, int $scale = 2): string
    {
        $bCanonical = static::canonicalize($b, $scale + 4);
        if (bccomp($bCanonical, '0', $scale + 4) === 0) {
            throw new InvalidArgumentException("Division by zero in DecimalSafeMath.");
        }
        $a = static::canonicalize($a, $scale + 4);
        $result = bcdiv($a, $bCanonical, $scale + 4);
        return static::roundHalfUp($result, $scale);
    }

    /**
     * Compare two decimal strings using BCMath.
     * Returns: 0 if equal, 1 if a > b, -1 if a < b.
     */
    public static function comp(string $a, string $b, int $scale = 2): int
    {
        $a = static::canonicalize($a, $scale);
        $b = static::canonicalize($b, $scale);
        return bccomp($a, $b, $scale);
    }

    /**
     * Strict ROUND_HALF_UP implementation via string and BCMath.
     * Guaranteed zero float casts.
     */
    public static function roundHalfUp(string $value, int $scale = 2): string
    {
        $isNegative = str_starts_with($value, '-');
        $unsignedVal = ltrim($value, '-');

        // Split integer and fraction
        $parts = explode('.', $unsignedVal);
        $intPart = $parts[0] === '' ? '0' : $parts[0];
        $fracPart = $parts[1] ?? '';

        if (strlen($fracPart) <= $scale) {
            $formattedFrac = str_pad($fracPart, $scale, '0');
            $res = ($intPart === '0' && $formattedFrac === str_repeat('0', $scale))
                ? "0.{$formattedFrac}"
                : ($isNegative ? "-{$intPart}.{$formattedFrac}" : "{$intPart}.{$formattedFrac}");
            return $res;
        }

        // Determine if rounding up is needed
        $cutoffDigit = (int)$fracPart[$scale];
        $keptFrac = substr($fracPart, 0, $scale);
        $base = "{$intPart}.{$keptFrac}";

        if ($cutoffDigit >= 5) {
            // Add 1 at the lowest scale position
            $increment = '0.' . str_repeat('0', max(0, $scale - 1)) . '1';
            $base = bcadd($base, $increment, $scale);
        }

        // Ensure scale is exactly padded
        $resParts = explode('.', $base);
        $resInt = $resParts[0];
        $resFrac = str_pad($resParts[1] ?? '', $scale, '0');

        if ($resInt === '0' && $resFrac === str_repeat('0', $scale)) {
            return "0.{$resFrac}";
        }

        return $isNegative ? "-{$resInt}.{$resFrac}" : "{$resInt}.{$resFrac}";
    }

    /**
     * Helper to format zero with exact scale decimals.
     */
    protected static function formatZero(int $scale): string
    {
        return $scale > 0 ? '0.' . str_repeat('0', $scale) : '0';
    }

    /**
     * Expand scientific notation (e.g. 1.2e-4) using pure string operations.
     */
    protected static function expandScientificNotation(string $str): string
    {
        $parts = preg_split('/[eE]/', $str);
        $mantissa = $parts[0];
        $exponent = (int)$parts[1];

        $isNegative = str_starts_with($mantissa, '-');
        $mantissa = ltrim($mantissa, '-');

        $mParts = explode('.', $mantissa);
        $intPart = $mParts[0];
        $fracPart = $mParts[1] ?? '';
        $digits = $intPart . $fracPart;
        $currentFracLen = strlen($fracPart);

        $newFracLen = $currentFracLen - $exponent;

        if ($newFracLen <= 0) {
            $res = $digits . str_repeat('0', abs($newFracLen));
        } else {
            $digitsLen = strlen($digits);
            if ($newFracLen >= $digitsLen) {
                $res = '0.' . str_repeat('0', $newFracLen - $digitsLen) . $digits;
            } else {
                $cut = $digitsLen - $newFracLen;
                $res = substr($digits, 0, $cut) . '.' . substr($digits, $cut);
            }
        }

        return $isNegative ? "-{$res}" : $res;
    }
}
