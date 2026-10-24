<?php

declare(strict_types=1);

namespace App\Shared\Domain;

/**
 * The identity of a listing: a lowercase UUID, never a bare string.
 * Shared because other contexts refer to a listing by its identity only.
 */
final readonly class ListingId
{
    private function __construct(
        private string $value,
    ) {}

    public static function fromString(string $value): self
    {
        if (1 !== preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $value)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a listing id.', $value));
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
