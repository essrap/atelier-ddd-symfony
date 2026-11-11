<?php

declare(strict_types=1);

namespace App\Payment\Infrastructure;

use App\Payment\Domain\DepositHolder;
use App\Payment\Domain\DepositId;
use App\Payment\Domain\DepositRefusalReason;
use App\Payment\Domain\DepositRefused;
use App\Shared\Domain\Money;
use Simulator\PaymentGateway\Gateway;

/**
 * The anticorruption layer: our deposit in the gateway's words, and its answers in ours.
 */
final readonly class GatewayDepositHolder implements DepositHolder
{
    public function __construct(
        private Gateway $gateway,
    ) {}

    public function hold(DepositId $deposit, Money $amount): void
    {
        $response = $this->gateway->authorize(
            self::decimal($amount),
            strtolower($amount->currency->value),
            $deposit->toString(),
        );

        $state = $response['state'];
        $reason = self::reason($response['reason'] ?? '');

        match ($state) {
            'authorized' => null,
            'declined' => throw DepositRefused::because($deposit, $reason),
            default => throw new \UnexpectedValueException("Unknown gateway state: {$state}."),
        };
    }

    public function release(DepositId $deposit): void
    {
        $this->gateway->void($deposit->toString());
    }

    private static function decimal(Money $amount): string
    {
        return \sprintf('%d.%02d', intdiv($amount->minorAmount, 100), $amount->minorAmount % 100);
    }

    private static function reason(string $code): DepositRefusalReason
    {
        return match ($code) {
            'insufficient_funds' => DepositRefusalReason::InsufficientFunds,
            'expired_card' => DepositRefusalReason::CardExpired,
            default => DepositRefusalReason::Other,
        };
    }
}
