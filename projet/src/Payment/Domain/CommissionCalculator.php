<?php

declare(strict_types=1);

namespace App\Payment\Domain;

use App\Shared\Domain\Money;

/**
 * The platform's commission on a payment: 10 %, rounded to the nearest cent, at least 1.00 of the same currency.
 * A payment of 1.00 or less is refused: the commission would take all of it, or more.
 */
final class CommissionCalculator
{
    private const int MINIMUM = 100;

    public function commissionOn(Money $paid): Money
    {
        if ($paid->minorAmount <= self::MINIMUM) {
            throw PaymentRuleBroken::tooSmallForCommission($paid);
        }

        return Money::of(max(intdiv($paid->minorAmount + 5, 10), self::MINIMUM), $paid->currency);
    }
}
