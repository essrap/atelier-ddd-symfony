<?php

declare(strict_types=1);

namespace App\Tests\Unit\Booking\Infrastructure;

use App\Booking\Domain\BookingCalendar;
use App\Booking\Domain\BookingCalendarNotFound;
use App\Booking\Domain\BookingId;
use App\Booking\Domain\BookingRuleBroken;
use App\Booking\Domain\Slot;
use App\Booking\Domain\SlotBooked;
use App\Booking\Infrastructure\InMemoryBookingCalendars;
use App\Shared\Domain\ListingId;
use PHPUnit\Framework\TestCase;

final class InMemoryBookingCalendarsTest extends TestCase
{
    private const string DRILL = '0199f2a4-6b1e-7c3d-9a8f-2e4b6c8d0f12';
    private const string SANDER = '0199f2a4-6b1e-7c3d-9a8f-2e4b6c8d0f13';
    private const string NORA = '0199f2b0-1c2d-7e3f-8a9b-0c1d2e3f4a01';
    private const string HUGO = '0199f2b0-1c2d-7e3f-8a9b-0c1d2e3f4a02';

    public function testRefusesAnUnknownCalendar(): void
    {
        $calendars = new InMemoryBookingCalendars();

        $this->expectExceptionObject(BookingCalendarNotFound::forListing($this->drill()));

        $calendars->get($this->drill());
    }

    public function testFindsEachCalendarByItsListing(): void
    {
        $calendars = $this->calendarsWithTheDrill();
        $calendar = $calendars->get($this->drill());
        $calendar->book($this->id(self::NORA), $this->saturday(), $this->now());
        $calendars->save($calendar);
        $calendars->save(BookingCalendar::forListing($this->sander()));

        $sander = $calendars->get($this->sander());
        $sander->book($this->id(self::HUGO), $this->saturday(), $this->now());

        self::assertTrue($sander->listingId->equals($this->sander()));
        self::assertTrue($calendars->get($this->drill())->listingId->equals($this->drill()));
    }

    public function testKeepsASavedBooking(): void
    {
        $calendars = $this->calendarsWithTheDrill();
        $calendar = $calendars->get($this->drill());
        $calendar->book($this->id(self::NORA), $this->saturday(), $this->now());
        $calendars->save($calendar);

        $this->expectExceptionObject(BookingRuleBroken::slotTaken());

        $calendars->get($this->drill())->book($this->id(self::HUGO), $this->saturday(), $this->now());
    }

    public function testAChangeIsNotKeptUntilSaved(): void
    {
        $calendars = $this->calendarsWithTheDrill();
        $calendars->get($this->drill())->book($this->id(self::NORA), $this->saturday(), $this->now());

        $calendar = $calendars->get($this->drill());
        $calendar->book($this->id(self::HUGO), $this->saturday(), $this->now());

        self::assertEquals(
            [new SlotBooked($this->drill(), $this->id(self::HUGO), $this->saturday(), $this->now())],
            $calendar->releaseEvents(),
        );
    }

    public function testAChangeAfterSaveIsNotKeptUntilSavedAgain(): void
    {
        $calendars = new InMemoryBookingCalendars();
        $calendar = BookingCalendar::forListing($this->drill());
        $calendars->save($calendar);

        $calendar->book($this->id(self::NORA), $this->saturday(), $this->now());

        $retrieved = $calendars->get($this->drill());
        $retrieved->book($this->id(self::HUGO), $this->saturday(), $this->now());

        self::assertEquals(
            [new SlotBooked($this->drill(), $this->id(self::HUGO), $this->saturday(), $this->now())],
            $retrieved->releaseEvents(),
        );
    }

    public function testDoesNotKeepTheEvents(): void
    {
        $calendars = $this->calendarsWithTheDrill();
        $calendar = $calendars->get($this->drill());
        $calendar->book($this->id(self::NORA), $this->saturday(), $this->now());
        $calendars->save($calendar);

        self::assertSame([], $calendars->get($this->drill())->releaseEvents());
        self::assertCount(1, $calendar->releaseEvents());
    }

    private function calendarsWithTheDrill(): InMemoryBookingCalendars
    {
        $calendars = new InMemoryBookingCalendars();
        $calendars->save(BookingCalendar::forListing($this->drill()));

        return $calendars;
    }

    private function drill(): ListingId
    {
        return ListingId::fromString(self::DRILL);
    }

    private function sander(): ListingId
    {
        return ListingId::fromString(self::SANDER);
    }

    private function id(string $id): BookingId
    {
        return BookingId::fromString($id);
    }

    private function saturday(): Slot
    {
        return Slot::between(
            new \DateTimeImmutable('2026-11-07 14:00'),
            new \DateTimeImmutable('2026-11-07 18:00'),
        );
    }

    private function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2026-11-04 09:30');
    }
}
