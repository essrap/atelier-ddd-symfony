<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Infrastructure;

use App\Shared\Domain\DomainEvent;
use App\Shared\Infrastructure\InMemoryDomainEventPublisher;
use PHPUnit\Framework\TestCase;

final class InMemoryDomainEventPublisherTest extends TestCase
{
    public function testPublishesNothingUntilAskedTo(): void
    {
        self::assertSame([], new InMemoryDomainEventPublisher()->published());
    }

    public function testKeepsTheEventsInTheOrderTheyWerePublished(): void
    {
        $first = $this->event('2026-11-14 09:00');
        $second = $this->event('2026-11-14 10:00');
        $third = $this->event('2026-11-14 11:00');
        $publisher = new InMemoryDomainEventPublisher();

        $publisher->publish($first, $second);
        $publisher->publish($third);

        self::assertSame([$first, $second, $third], $publisher->published());
    }

    public function testPublishingNoEventChangesNothing(): void
    {
        $publisher = new InMemoryDomainEventPublisher();

        $publisher->publish();

        self::assertSame([], $publisher->published());
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
