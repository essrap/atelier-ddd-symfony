<?php

declare(strict_types=1);

namespace Example\Ep01Mutation\Tests;

use Example\Ep01Mutation\Domain\MaxRentalDuration;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MaxRentalDurationTest extends TestCase
{
    #[DataProvider('allowedDurations')]
    public function testItAllowsADurationWithinTheLimits(int $days): void
    {
        self::assertTrue((new MaxRentalDuration())->allows($days));
    }

    #[DataProvider('refusedDurations')]
    public function testItRefusesADurationOutsideTheLimits(int $days): void
    {
        self::assertFalse((new MaxRentalDuration())->allows($days));
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function allowedDurations(): iterable
    {
        yield 'shortest rental' => [1];
        yield 'two weeks' => [14];
        yield 'longest rental' => [30];
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function refusedDurations(): iterable
    {
        yield 'no day at all' => [0];
        yield 'one day too long' => [31];
    }
}
