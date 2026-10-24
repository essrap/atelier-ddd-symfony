<?php

declare(strict_types=1);

namespace App\Booking\Domain;

/**
 * A dated period, from start (included) to end (excluded):
 * a slot ending at 2 pm and one starting at 2 pm do not overlap.
 */
final readonly class Slot
{
    private function __construct(
        public \DateTimeImmutable $start,
        public \DateTimeImmutable $end,
    ) {}

    public static function between(\DateTimeImmutable $start, \DateTimeImmutable $end): self
    {
        if ($end <= $start) {
            throw new \InvalidArgumentException('A slot must end after it starts.');
        }

        return new self($start, $end);
    }

    public function overlaps(self $other): bool
    {
        return $this->start < $other->end && $other->start < $this->end;
    }
}
