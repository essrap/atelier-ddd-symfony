<?php

declare(strict_types=1);

namespace App\Tests\Unit\Booking\Domain;

use App\Booking\Domain\BookingCalendar;
use App\Booking\Domain\BookingCancelled;
use App\Booking\Domain\BookingId;
use App\Booking\Domain\BookingRuleBroken;
use App\Booking\Domain\Slot;
use App\Booking\Domain\SlotBooked;
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
        $calendar->book($this->id(self::NORA), $this->slot('14:00', '18:00'), $this->now());

        $this->expectExceptionObject(BookingRuleBroken::slotTaken());

        $calendar->book($this->id(self::HUGO), $this->slot('16:00', '20:00'), $this->now());
    }

    public function testAcceptsBackToBackBookings(): void
    {
        $calendar = $this->calendar();
        $calendar->book($this->id(self::NORA), $this->slot('10:00', '14:00'), $this->now());
        $calendar->book($this->id(self::HUGO), $this->slot('14:00', '18:00'), $this->now());

        $this->expectExceptionObject(BookingRuleBroken::slotTaken());

        $calendar->book($this->id(self::LEA), $this->slot('15:00', '16:00'), $this->now());
    }

    public function testACancelledBookingFreesItsSlot(): void
    {
        $calendar = $this->calendar();
        $calendar->book($this->id(self::NORA), $this->slot('14:00', '18:00'), $this->now());
        $calendar->cancel($this->id(self::NORA), $this->now());
        $calendar->book($this->id(self::HUGO), $this->slot('16:00', '20:00'), $this->now());

        $this->expectExceptionObject(BookingRuleBroken::slotTaken());

        $calendar->book($this->id(self::LEA), $this->slot('17:00', '19:00'), $this->now());
    }

    public function testRefusesTheSameBookingTwice(): void
    {
        $calendar = $this->calendar();
        $calendar->book($this->id(self::NORA), $this->slot('10:00', '12:00'), $this->now());

        $this->expectExceptionObject(BookingRuleBroken::alreadyBooked($this->id(self::NORA)));

        $calendar->book($this->id(self::NORA), $this->slot('14:00', '16:00'), $this->now());
    }

    public function testCannotCancelAnUnknownBooking(): void
    {
        $this->expectExceptionObject(BookingRuleBroken::unknownBooking($this->id(self::NORA)));

        $this->calendar()->cancel($this->id(self::NORA), $this->now());
    }

    public function testCannotCancelTwice(): void
    {
        $calendar = $this->calendar();
        $calendar->book($this->id(self::NORA), $this->slot('14:00', '18:00'), $this->now());
        $calendar->cancel($this->id(self::NORA), $this->now());

        $this->expectExceptionObject(BookingRuleBroken::alreadyCancelled());

        $calendar->cancel($this->id(self::NORA), $this->now());
    }

    public function testRecordsThatTheSlotIsBooked(): void
    {
        $calendar = $this->calendar();

        $calendar->book($this->id(self::NORA), $this->slot('14:00', '18:00'), $this->now());

        self::assertEquals(
            [new SlotBooked($calendar->listingId, $this->id(self::NORA), $this->slot('14:00', '18:00'), $this->now())],
            $calendar->releaseEvents(),
        );
    }

    public function testRecordsNoEventForARefusedBooking(): void
    {
        $calendar = $this->calendar();
        $calendar->book($this->id(self::NORA), $this->slot('14:00', '18:00'), $this->now());

        try {
            $calendar->book($this->id(self::HUGO), $this->slot('16:00', '20:00'), $this->now());
            self::fail('The overlapping booking should have been refused.');
        } catch (BookingRuleBroken) {
        }

        $events = $calendar->releaseEvents();
        self::assertCount(1, $events);
        self::assertEquals(
            new SlotBooked($calendar->listingId, $this->id(self::NORA), $this->slot('14:00', '18:00'), $this->now()),
            $events[0],
        );
    }

    public function testCancellingTwiceRecordsOneEvent(): void
    {
        $calendar = $this->calendar();
        $calendar->book($this->id(self::NORA), $this->slot('14:00', '18:00'), $this->now());
        $calendar->releaseEvents();

        $calendar->cancel($this->id(self::NORA), $this->now());
        try {
            $calendar->cancel($this->id(self::NORA), $this->now());
            self::fail('The second cancellation should have been refused.');
        } catch (BookingRuleBroken) {
        }

        self::assertEquals(
            [new BookingCancelled($calendar->listingId, $this->id(self::NORA), $this->now())],
            $calendar->releaseEvents(),
        );
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

    private function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2026-11-04 09:30');
    }
}
