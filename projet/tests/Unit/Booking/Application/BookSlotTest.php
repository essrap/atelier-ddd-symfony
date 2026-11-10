<?php

declare(strict_types=1);

namespace App\Tests\Unit\Booking\Application;

use App\Booking\Application\BookSlot;
use App\Booking\Domain\BookingCalendar;
use App\Booking\Domain\BookingId;
use App\Booking\Domain\BookingRuleBroken;
use App\Booking\Domain\Slot;
use App\Booking\Domain\SlotBooked;
use App\Booking\Infrastructure\InMemoryBookingCalendars;
use App\Shared\Domain\ListingId;
use App\Shared\Infrastructure\InMemoryDomainEventPublisher;
use PHPUnit\Framework\TestCase;

final class BookSlotTest extends TestCase
{
    private const string TRAILER = '019a6e1c-3f2a-7b4d-8c5e-6f7a8b9c0d21';
    private const string MALIK = '019a6e1c-5d4e-7f6a-9b8c-7d6e5f4a3b01';
    private const string ANOTHER_RENTER = '019a6e1c-5d4e-7f6a-9b8c-7d6e5f4a3b02';

    public function testPublishesTheSlotBooked(): void
    {
        $publisher = new InMemoryDomainEventPublisher();
        $bookSlot = new BookSlot($this->calendarsWithTheTrailer(), $publisher);

        $bookSlot($this->trailer(), $this->maliksBooking(), $this->saturday(), $this->now());

        self::assertEquals(
            [new SlotBooked($this->trailer(), $this->maliksBooking(), $this->saturday(), $this->now())],
            $publisher->published(),
        );
    }

    public function testSavesTheBooking(): void
    {
        $calendars = $this->calendarsWithTheTrailer();
        $bookSlot = new BookSlot($calendars, new InMemoryDomainEventPublisher());

        $bookSlot($this->trailer(), $this->maliksBooking(), $this->saturday(), $this->now());

        $this->expectExceptionObject(BookingRuleBroken::slotTaken());

        $calendar = $calendars->get($this->trailer());
        $calendar->book($this->anotherBooking(), $this->saturday(), $this->now());
    }

    public function testNothingIsPublishedWhenTheBookingIsRefused(): void
    {
        $calendars = $this->calendarsWithTheTrailer();
        $calendar = $calendars->get($this->trailer());
        $calendar->book($this->maliksBooking(), $this->saturday(), $this->now());
        $calendars->save($calendar);
        $publisher = new InMemoryDomainEventPublisher();
        $bookSlot = new BookSlot($calendars, $publisher);

        try {
            $bookSlot($this->trailer(), $this->anotherBooking(), $this->saturday(), $this->now());
            self::fail('The overlapping booking should have been refused.');
        } catch (BookingRuleBroken) {
        }

        self::assertSame([], $publisher->published());
    }

    public function testNothingIsPublishedWhenTheSaveFails(): void
    {
        $publisher = new InMemoryDomainEventPublisher();
        $bookSlot = new BookSlot(new BookingCalendarsWithStorageDown(), $publisher);

        try {
            $bookSlot($this->trailer(), $this->maliksBooking(), $this->saturday(), $this->now());
            self::fail('The save should have failed.');
        } catch (StorageIsDown) {
        }

        self::assertSame([], $publisher->published());
    }

    private function calendarsWithTheTrailer(): InMemoryBookingCalendars
    {
        $calendars = new InMemoryBookingCalendars();
        $calendars->save(BookingCalendar::forListing($this->trailer()));

        return $calendars;
    }

    private function trailer(): ListingId
    {
        return ListingId::fromString(self::TRAILER);
    }

    private function maliksBooking(): BookingId
    {
        return BookingId::fromString(self::MALIK);
    }

    private function anotherBooking(): BookingId
    {
        return BookingId::fromString(self::ANOTHER_RENTER);
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
