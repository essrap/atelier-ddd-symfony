<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain;

use App\Shared\Domain\DomainEvent;
use App\Shared\Domain\RecordsEvents;
use PHPUnit\Framework\TestCase;

final class RecordsEventsTest extends TestCase
{
    public function testReleasesTheEventsInTheOrderTheyWereRecorded(): void
    {
        $first = $this->event('2026-11-07 09:00');
        $second = $this->event('2026-11-07 10:00');
        $aggregate = new class {
            use RecordsEvents;

            public function happen(DomainEvent $event): void
            {
                $this->record($event);
            }
        };

        $aggregate->happen($first);
        $aggregate->happen($second);

        self::assertSame([$first, $second], $aggregate->releaseEvents());
    }

    public function testReleasesEachEventOnlyOnce(): void
    {
        $aggregate = new class {
            use RecordsEvents;

            public function happen(DomainEvent $event): void
            {
                $this->record($event);
            }
        };
        $aggregate->happen($this->event('2026-11-07 09:00'));

        $aggregate->releaseEvents();

        self::assertSame([], $aggregate->releaseEvents());
    }

    private function event(string $occurredOn): DomainEvent
    {
        return new readonly class (new \DateTimeImmutable($occurredOn)) implements DomainEvent {
            public function __construct(
                public \DateTimeImmutable $occurredOn,
            ) {}
        };
    }
}
