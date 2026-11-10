<?php

declare(strict_types=1);

namespace App\Tests\Unit\Payment\Domain;

use App\Payment\Domain\CommissionCalculator;
use App\Payment\Domain\PaymentRuleBroken;
use App\Shared\Domain\Currency;
use App\Shared\Domain\Money;
use PHPUnit\Framework\TestCase;

final class CommissionCalculatorTest extends TestCase
{
    public function testTakesTenPercent(): void
    {
        $commission = new CommissionCalculator()->commissionOn($this->euros(5000));

        self::assertTrue($commission->equals($this->euros(500)));
    }

    public function testRoundsHalfACentUp(): void
    {
        $commission = new CommissionCalculator()->commissionOn($this->euros(4995));

        self::assertTrue($commission->equals($this->euros(500)));
    }

    public function testRoundsLessThanHalfACentDown(): void
    {
        $commission = new CommissionCalculator()->commissionOn($this->euros(4994));

        self::assertTrue($commission->equals($this->euros(499)));
    }

    public function testTakesAtLeastOneEuro(): void
    {
        $commission = new CommissionCalculator()->commissionOn($this->euros(500));

        self::assertTrue($commission->equals($this->euros(100)));
    }

    public function testKeepsTheCurrencyOfThePayment(): void
    {
        $commission = new CommissionCalculator()->commissionOn(Money::of(5000, Currency::CHF));

        self::assertTrue($commission->equals(Money::of(500, Currency::CHF)));
    }

    public function testRefusesAPaymentOfOneEuroOrLess(): void
    {
        $this->expectExceptionObject(PaymentRuleBroken::tooSmallForCommission($this->euros(100)));

        new CommissionCalculator()->commissionOn($this->euros(100));
    }

    public function testAcceptsAPaymentJustAboveTheMinimum(): void
    {
        $commission = new CommissionCalculator()->commissionOn($this->euros(101));

        self::assertTrue($commission->equals($this->euros(100)));
    }

    private function euros(int $cents): Money
    {
        return Money::of($cents, Currency::EUR);
    }
}
