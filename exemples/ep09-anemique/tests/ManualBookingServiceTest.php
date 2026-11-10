<?php

declare(strict_types=1);

namespace Example\Ep09Anemic\Tests;

use Example\Ep09Anemic\Before\BookingCalendar;
use Example\Ep09Anemic\Before\BookingService;
use Example\Ep09Anemic\Before\ManualBookingService;
use PHPUnit\Framework\TestCase;

final class ManualBookingServiceTest extends TestCase
{
    public function testAnotherServiceCanBypassTheOverlapRule(): void
    {
        $trailer = new BookingCalendar('trailer');
        new BookingService()->book($trailer, 'Malik', $this->at('09:00'), $this->at('18:00'), 4995);

        new ManualBookingService()->add($trailer, 'Neighbour', $this->at('17:00'), $this->at('20:00'), 2000);

        self::assertCount(2, $trailer->getBookings());
    }

    public function testAddsTheBookingAsTyped(): void
    {
        $trailer = new BookingCalendar('trailer');

        new ManualBookingService()->add($trailer, 'Malik', $this->at('09:00'), $this->at('18:00'), 2000);

        self::assertEquals(
            [['renter' => 'Malik', 'start' => $this->at('09:00'), 'end' => $this->at('18:00'), 'commission' => 200]],
            $trailer->getBookings(),
        );
    }

    private function at(string $time): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2026-11-14 ' . $time);
    }
}
