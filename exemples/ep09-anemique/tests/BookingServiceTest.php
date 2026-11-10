<?php

declare(strict_types=1);

namespace Example\Ep09Anemic\Tests;

use Example\Ep09Anemic\Before\BookingCalendar;
use Example\Ep09Anemic\Before\BookingService;
use PHPUnit\Framework\TestCase;

final class BookingServiceTest extends TestCase
{
    public function testBooksAFreeSlotAndTakesTenPercent(): void
    {
        $trailer = new BookingCalendar('trailer');

        new BookingService()->book($trailer, 'Malik', $this->at('09:00'), $this->at('18:00'), 4995);

        self::assertEquals(
            [['renter' => 'Malik', 'start' => $this->at('09:00'), 'end' => $this->at('18:00'), 'commission' => 500]],
            $trailer->getBookings(),
        );
    }

    public function testRoundsLessThanHalfACentDown(): void
    {
        $trailer = new BookingCalendar('trailer');

        new BookingService()->book($trailer, 'Malik', $this->at('09:00'), $this->at('18:00'), 4994);

        self::assertSame(499, $trailer->getBookings()[0]['commission']);
    }

    public function testTakesAtLeastOneEuro(): void
    {
        $trailer = new BookingCalendar('trailer');

        new BookingService()->book($trailer, 'Malik', $this->at('09:00'), $this->at('18:00'), 500);

        self::assertSame(100, $trailer->getBookings()[0]['commission']);
    }

    public function testRefusesAnOverlappingSlot(): void
    {
        $service = new BookingService();
        $trailer = new BookingCalendar('trailer');
        $service->book($trailer, 'Malik', $this->at('09:00'), $this->at('18:00'), 4995);

        $this->expectExceptionObject(new \DomainException('This slot is already booked.'));

        $service->book($trailer, 'Neighbour', $this->at('17:00'), $this->at('20:00'), 2000);
    }

    public function testAcceptsTheSlotsJustBeforeAndJustAfter(): void
    {
        $service = new BookingService();
        $trailer = new BookingCalendar('trailer');
        $service->book($trailer, 'Malik', $this->at('09:00'), $this->at('18:00'), 4995);

        $service->book($trailer, 'Early bird', $this->at('07:00'), $this->at('09:00'), 2000);
        $service->book($trailer, 'Night owl', $this->at('18:00'), $this->at('20:00'), 2000);

        self::assertCount(3, $trailer->getBookings());
    }

    public function testSavesTheCalendarAndSendsTheNews(): void
    {
        $service = new BookingService();
        $trailer = new BookingCalendar('trailer');

        $service->book($trailer, 'Malik', $this->at('09:00'), $this->at('18:00'), 4995);

        self::assertSame([$trailer], $service->saved);
        self::assertSame(['Malik booked your trailer.'], $service->sent);
    }

    private function at(string $time): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2026-11-14 ' . $time);
    }
}
