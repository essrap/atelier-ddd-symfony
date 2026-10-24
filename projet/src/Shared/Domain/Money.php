<?php

declare(strict_types=1);

namespace App\Shared\Domain;

/**
 * An amount of money, in minor units: 4.92 EUR is 492.
 * Integers are exact; floats are not.
 */
final readonly class Money
{
    private function __construct(
        public int $minorAmount,
        public Currency $currency,
    ) {}

    public static function of(int $minorAmount, Currency $currency): self
    {
        if ($minorAmount < 0) {
            throw new \InvalidArgumentException('A price cannot be negative.');
        }

        return new self($minorAmount, $currency);
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->minorAmount + $other->minorAmount, $this->currency);
    }

    public function multiply(int $factor): self
    {
        return self::of($this->minorAmount * $factor, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->minorAmount === $other->minorAmount
            && $this->currency === $other->currency;
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Cannot mix currencies.');
        }
    }
}
