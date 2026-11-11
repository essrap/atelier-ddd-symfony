<?php

declare(strict_types=1);

namespace App\Payment\Domain;

use App\Shared\Domain\Money;

/**
 * Holds the deposit of a rental until the equipment comes back, then releases it.
 * Written in our words; whoever does it outside speaks its own.
 */
interface DepositHolder
{
    /**
     * @throws DepositRefused when the bank refuses to hold the amount
     */
    public function hold(DepositId $deposit, Money $amount): void;

    public function release(DepositId $deposit): void;
}
