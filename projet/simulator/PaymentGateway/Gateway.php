<?php

declare(strict_types=1);

namespace Simulator\PaymentGateway;

/**
 * A simulated payment provider, for the workshop: no account, no network.
 * Its words are not ours: decimal amounts, lowercase currencies, string codes.
 */
final class Gateway
{
    public const string INSUFFICIENT_FUNDS = 'insufficient_funds';
    public const string EXPIRED_CARD = 'expired_card';

    /**
     * @var list<array{operation: string, amount?: string, currency?: string, reference: string}>
     */
    public private(set) array $calls = [];

    private ?string $nextDecline = null;

    public function declineNextWith(string $reason): void
    {
        $this->nextDecline = $reason;
    }

    /**
     * @return array{state: string, reason?: string}
     */
    public function authorize(string $amount, string $currency, string $reference): array
    {
        $this->calls[] = [
            'operation' => 'authorize',
            'amount' => $amount,
            'currency' => $currency,
            'reference' => $reference,
        ];

        if (null === $this->nextDecline) {
            return ['state' => 'authorized'];
        }

        $reason = $this->nextDecline;
        $this->nextDecline = null;

        return ['state' => 'declined', 'reason' => $reason];
    }

    /**
     * @return array{state: string}
     */
    public function void(string $reference): array
    {
        $this->calls[] = ['operation' => 'void', 'reference' => $reference];

        return ['state' => 'voided'];
    }
}
