<?php

declare(strict_types=1);

namespace App\Payment\Domain;

use App\Shared\Domain\Money;

/**
 * A business rule of the payment refused the operation.
 */
final class PaymentRuleBroken extends \DomainException
{
    public static function tooSmallForCommission(Money $paid): self
    {
        return new self(\sprintf(
            'A payment of %d %s cents is too small to take the commission.',
            $paid->minorAmount,
            $paid->currency->value,
        ));
    }
}
