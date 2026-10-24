<?php

declare(strict_types=1);

namespace App\Tests\Unit\Booking\Domain;

use App\Booking\Domain\BookingCalendar;
use App\Booking\Domain\BookingId;
use App\Booking\Domain\BookingRuleBroken;
use App\Booking\Domain\Slot;
use App\Shared\Domain\ListingId;
use PHPUnit\Framework\TestCase;

final class BookingCalendarTest extends TestCase
{
    private const string DRILL = '0199f2a4-6b1e-7c3d-9a8f-2e4b6c8d0f12';
    private const string NORA = '0199f2b0-1c2d-7e3f-8a9b-0c1d2e3f4a01';
    private const string HUGO = '0199f2b0-1c2d-7e3f-8a9b-0c1d2e3f4a02';
    private const string LEA = '0199f2b0-1c2d-7e3f-8a9b-0c1d2e3f4a03';

    public function testBelongsToOneListing(): void
    {
        $calendar = BookingCalendar::forListing(ListingId::fromString(self::DRILL));

        self::assertTrue($calendar->listingId->equals(ListingId::fromString(self::DRILL)));
    }

    public function testRefusesABookingThatOverlapsAnother(): void
    {
        $calendar = $this->calendar();
        $calendar->book($this->id(self::NORA), $this->slot('14:00', '18:00'));

        $this->expectExceptionObject(BookingRuleBroken::slotTaken());

        $calendar->book($this->id(self::HUGO), $this->slot('16:00', '20:00'));
    }

    public function testAcceptsBackToBackBookings(): void
    {
        $calendar = $this->calendar();
        $calendar->book($this->id(self::NORA), $this->slot('10:00', '14:00'));
        $calendar->book($this->id(self::HUGO), $this->slot('14:00', '18:00'));

        $this->expectExceptionObject(BookingRuleBroken::slotTaken());

        $calendar->book($this->id(self::LEA), $this->slot('15:00', '16:00'));
    }

    public function testACancelledBookingFreesItsSlot(): void
    {
        $calendar = $this->calendar();
        $calendar->book($this->id(self::NORA), $this->slot('14:00', '18:00'));
        $calendar->cancel($this->id(self::NORA));
        $calendar->book($this->id(self::HUGO), $this->slot('16:00', '20:00'));

        $this->expectExceptionObject(BookingRuleBroken::slotTaken());

        $calendar->book($this->id(self::LEA), $this->slot('17:00', '19:00'));
    }

    public function testRefusesTheSameBookingTwice(): void
    {
        $calendar = $this->calendar();
        $calendar->book($this->id(self::NORA), $this->slot('10:00', '12:00'));

        $this->expectExceptionObject(BookingRuleBroken::alreadyBooked($this->id(self::NORA)));

        $calendar->book($this->id(self::NORA), $this->slot('14:00', '16:00'));
    }

    public function testCannotCancelAnUnknownBooking(): void
    {
        $this->expectExceptionObject(BookingRuleBroken::unknownBooking($this->id(self::NORA)));

        $this->calendar()->cancel($this->id(self::NORA));
    }

    public function testCannotCancelTwice(): void
    {
        $calendar = $this->calendar();
        $calendar->book($this->id(self::NORA), $this->slot('14:00', '18:00'));
        $calendar->cancel($this->id(self::NORA));

        $this->expectExceptionObject(BookingRuleBroken::alreadyCancelled());

        $calendar->cancel($this->id(self::NORA));
    }

    private function calendar(): BookingCalendar
    {
        return BookingCalendar::forListing(ListingId::fromString(self::DRILL));
    }

    private function id(string $id): BookingId
    {
        return BookingId::fromString($id);
    }

    private function slot(string $start, string $end): Slot
    {
        return Slot::between(
            new \DateTimeImmutable('2026-11-07 ' . $start),
            new \DateTimeImmutable('2026-11-07 ' . $end),
        );
    }
}
