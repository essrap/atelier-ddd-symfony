<?php

declare(strict_types=1);

namespace App\Payment\Domain;

/**
 * The bank refused to hold the deposit. Not one of our rules: the bank decided.
 */
final class DepositRefused extends \DomainException
{
    private function __construct(
        public readonly DepositId $deposit,
        public readonly DepositRefusalReason $reason,
    ) {
        parent::__construct(\sprintf(
            'The deposit %s was refused: %s.',
            $deposit->toString(),
            $reason->name,
        ));
    }

    public static function because(DepositId $deposit, DepositRefusalReason $reason): self
    {
        return new self($deposit, $reason);
    }
}
