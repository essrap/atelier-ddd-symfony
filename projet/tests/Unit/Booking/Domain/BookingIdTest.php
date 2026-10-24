<?php

declare(strict_types=1);

namespace App\Tests\Unit\Booking\Domain;

use App\Booking\Domain\BookingId;
use PHPUnit\Framework\TestCase;

final class BookingIdTest extends TestCase
{
    private const string ID = '0199f2b0-1c2d-7e3f-8a9b-0c1d2e3f4a5b';

    public function testKeepsItsValue(): void
    {
        self::assertSame(self::ID, BookingId::fromString(self::ID)->toString());
    }

    public function testIsEqualToTheSameId(): void
    {
        self::assertTrue(BookingId::fromString(self::ID)->equals(BookingId::fromString(self::ID)));
        self::assertFalse(BookingId::fromString(self::ID)->equals(BookingId::fromString('0199f2b0-1c2d-7e3f-8a9b-0c1d2e3f4a5c')));
    }

    public function testRefusesAnythingButALowercaseUuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('"x0199f2b0-1c2d-7e3f-8a9b-0c1d2e3f4a5b" is not a booking id.');

        BookingId::fromString('x0199f2b0-1c2d-7e3f-8a9b-0c1d2e3f4a5b');
    }

    public function testRefusesTextAfterTheUuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        BookingId::fromString('0199f2b0-1c2d-7e3f-8a9b-0c1d2e3f4a5bx');
    }
}
