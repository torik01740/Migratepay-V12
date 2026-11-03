<?php

namespace Tests\Unit;

use App\Services\DecimalSafeMath;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DecimalSafeMathTest extends TestCase
{
    public function test_canonicalize_positive_values(): void
    {
        $this->assertEquals('1000.00', DecimalSafeMath::canonicalize('1000'));
        $this->assertEquals('1000.50', DecimalSafeMath::canonicalize('1000.5'));
        $this->assertEquals('1000.55', DecimalSafeMath::canonicalize('1000.55'));
    }

    public function test_canonicalize_half_up_rounding(): void
    {
        // 0.005 rounds up to 0.01
        $this->assertEquals('1000.56', DecimalSafeMath::canonicalize('1000.555'));
        // 0.004 rounds down to 0.00
        $this->assertEquals('1000.55', DecimalSafeMath::canonicalize('1000.554'));
        $this->assertEquals('0.01', DecimalSafeMath::canonicalize('0.005'));
        $this->assertEquals('0.00', DecimalSafeMath::canonicalize('0.004'));
    }

    public function test_canonicalize_negative_values(): void
    {
        $this->assertEquals('-500.00', DecimalSafeMath::canonicalize('-500'));
        $this->assertEquals('-500.56', DecimalSafeMath::canonicalize('-500.555'));
        $this->assertEquals('-500.55', DecimalSafeMath::canonicalize('-500.554'));
    }

    public function test_canonicalize_zero_and_empty(): void
    {
        $this->assertEquals('0.00', DecimalSafeMath::canonicalize('0'));
        $this->assertEquals('0.00', DecimalSafeMath::canonicalize(''));
        $this->assertEquals('0.00', DecimalSafeMath::canonicalize(null));
    }

    public function test_canonicalize_large_values(): void
    {
        $this->assertEquals('999999999999.99', DecimalSafeMath::canonicalize('999999999999.99'));
    }

    public function test_canonicalize_rejects_invalid_strings(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DecimalSafeMath::canonicalize('not-a-number');
    }

    public function test_bcmath_addition(): void
    {
        $result = DecimalSafeMath::add('14037.00', '12240.00');
        $this->assertEquals('26277.00', $result);
    }

    public function test_bcmath_subtraction(): void
    {
        $result = DecimalSafeMath::sub('28500.00', '2223.00');
        $this->assertEquals('26277.00', $result);
    }

    public function test_zero_variance_comparison(): void
    {
        $custodialObligations = '26277.00';
        $netCapitalPosition = '26277.00';
        $variance = DecimalSafeMath::sub($custodialObligations, $netCapitalPosition);

        $this->assertEquals('0.00', $variance);
        $this->assertEquals(0, DecimalSafeMath::comp($variance, '0.00'));
    }

    public function test_division_by_zero_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DecimalSafeMath::div('100.00', '0.00');
    }
}
