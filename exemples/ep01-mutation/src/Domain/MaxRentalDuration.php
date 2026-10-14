<?php

declare(strict_types=1);

namespace Example\Ep01Mutation\Domain;

/**
 * Throwaway rule for episode 1: a rental lasts from 1 to 30 days.
 * It only exists to show unit tests and mutation testing; it is not part of the Tooloc model.
 */
final readonly class MaxRentalDuration
{
    public const int MIN_DAYS = 1;
    public const int MAX_DAYS = 30;

    public function allows(int $days): bool
    {
        return $days >= self::MIN_DAYS && $days <= self::MAX_DAYS;
    }
}
