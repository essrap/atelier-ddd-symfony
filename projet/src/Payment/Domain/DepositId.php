<?php

declare(strict_types=1);

namespace App\Payment\Domain;

/**
 * The identity of a deposit: a lowercase UUID. Only the Payment context uses it.
 */
final readonly class DepositId
{
    private function __construct(
        private string $value,
    ) {}

    public static function fromString(string $value): self
    {
        if (1 !== preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $value)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a deposit id.', $value));
        }

        return new self($value);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
