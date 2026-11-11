<?php

declare(strict_types=1);

namespace App\Payment\Domain;

/**
 * Why the bank refused to hold a deposit, in our words.
 */
enum DepositRefusalReason
{
    case InsufficientFunds;
    case CardExpired;
    case Other;
}
