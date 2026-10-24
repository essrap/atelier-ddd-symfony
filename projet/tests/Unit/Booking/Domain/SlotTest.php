<?php

declare(strict_types=1);

namespace App\Tests\Unit\Booking\Domain;

use App\Booking\Domain\Slot;
use PHPUnit\Framework\TestCase;

final class SlotTest extends TestCase
{
    public function testRefusesASlotThatEndsBeforeItStarts(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('A slot must end after it starts.');

        $this->slot('14:00', '10:00');
    }

    public function testRefusesAnEmptySlot(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->slot('14:00', '14:00');
    }

    public function testOverlapsASlotThatStartsBeforeItEnds(): void
    {
        self::assertTrue($this->slot('10:00', '14:00')->overlaps($this->slot('13:00', '18:00')));
        self::assertTrue($this->slot('13:00', '18:00')->overlaps($this->slot('10:00', '14:00')));
    }

    public function testOverlapsASlotItContains(): void
    {
        self::assertTrue($this->slot('10:00', '18:00')->overlaps($this->slot('12:00', '13:00')));
    }

    public function testDoesNotOverlapTheNextSlot(): void
    {
        self::assertFalse($this->slot('10:00', '14:00')->overlaps($this->slot('14:00', '18:00')));
        self::assertFalse($this->slot('14:00', '18:00')->overlaps($this->slot('10:00', '14:00')));
    }

    private function slot(string $start, string $end): Slot
    {
        return Slot::between(
            new \DateTimeImmutable('2026-11-07 ' . $start),
            new \DateTimeImmutable('2026-11-07 ' . $end),
        );
    }
}
