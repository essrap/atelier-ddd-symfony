<?php

declare(strict_types=1);

namespace App\Tests\Unit\Booking\Application;

use App\Booking\Application\OpenBookingCalendar;
use App\Booking\Domain\BookingId;
use App\Booking\Domain\BookingRuleBroken;
use App\Booking\Domain\Slot;
use App\Booking\Domain\SlotBooked;
use App\Booking\Infrastructure\InMemoryBookingCalendars;
use App\Shared\Domain\ListingId;
use PHPUnit\Framework\TestCase;

final class OpenBookingCalendarTest extends TestCase
{
    private const string TRAILER = '019a6e1c-3f2a-7b4d-8c5e-6f7a8b9c0d21';
    private const string MALIK = '019a6e1c-5d4e-7f6a-9b8c-7d6e5f4a3b01';
    private const string ANOTHER_RENTER = '019a6e1c-5d4e-7f6a-9b8c-7d6e5f4a3b02';

    public function testOpensTheCalendarOfANewListing(): void
    {
        $calendars = new InMemoryBookingCalendars();
        $open = new OpenBookingCalendar($calendars);

        $open($this->trailer());

        $calendar = $calendars->get($this->trailer());
        $calendar->book($this->booking(self::MALIK), $this->saturday(), $this->now());
        self::assertEquals(
            [new SlotBooked($this->trailer(), $this->booking(self::MALIK), $this->saturday(), $this->now())],
            $calendar->releaseEvents(),
        );
    }

    public function testOpeningTwiceKeepsTheBookings(): void
    {
        $calendars = new InMemoryBookingCalendars();
        $open = new OpenBookingCalendar($calendars);
        $open($this->trailer());
        $calendar = $calendars->get($this->trailer());
        $calendar->book($this->booking(self::MALIK), $this->saturday(), $this->now());
        $calendars->save($calendar);

        $open($this->trailer());

        $this->expectExceptionObject(BookingRuleBroken::slotTaken());

        $calendar = $calendars->get($this->trailer());
        $calendar->book($this->booking(self::ANOTHER_RENTER), $this->saturday(), $this->now());
    }

    private function trailer(): ListingId
    {
        return ListingId::fromString(self::TRAILER);
    }

    private function booking(string $id): BookingId
    {
        return BookingId::fromString($id);
    }

    private function saturday(): Slot
    {
        return Slot::between(
            new \DateTimeImmutable('2026-11-14 09:00'),
            new \DateTimeImmutable('2026-11-14 18:00'),
        );
    }

    private function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2026-11-10 09:30');
    }
}
