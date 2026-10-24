<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain;

use App\Shared\Domain\Currency;
use App\Shared\Domain\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testIsEqualToAnotherMoneyWithTheSameValues(): void
    {
        self::assertTrue(Money::of(1000, Currency::EUR)->equals(Money::of(1000, Currency::EUR)));
    }

    public function testDiffersByAmountOrByCurrency(): void
    {
        $tenEuros = Money::of(1000, Currency::EUR);

        self::assertFalse($tenEuros->equals(Money::of(1001, Currency::EUR)));
        self::assertFalse($tenEuros->equals(Money::of(1000, Currency::CHF)));
    }

    public function testAddsWithoutRoundingErrors(): void
    {
        $threeDays = Money::of(1990, Currency::EUR)->multiply(3);
        $total = $threeDays->add(Money::of(30, Currency::EUR));

        self::assertSame(6000, $total->minorAmount);
    }

    public function testNeverChangesTheOriginal(): void
    {
        $price = Money::of(1990, Currency::EUR);

        $price->add(Money::of(500, Currency::EUR));
        $price->multiply(2);

        self::assertSame(1990, $price->minorAmount);
    }

    public function testAcceptsZero(): void
    {
        self::assertSame(0, Money::of(0, Currency::EUR)->minorAmount);
    }

    public function testRefusesANegativeAmount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('A price cannot be negative.');

        Money::of(-1, Currency::EUR);
    }

    public function testRefusesToMixCurrencies(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot mix currencies.');

        Money::of(1000, Currency::EUR)->add(Money::of(1000, Currency::CHF));
    }
}
