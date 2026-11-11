<?php

declare(strict_types=1);

namespace App\Tests\Unit\Payment\Domain;

use App\Payment\Domain\DepositId;
use PHPUnit\Framework\TestCase;

final class DepositIdTest extends TestCase
{
    private const string ID = '0199f2b0-1c2d-7e3f-8a9b-0c1d2e3f4a5b';

    public function testKeepsItsValue(): void
    {
        self::assertSame(self::ID, DepositId::fromString(self::ID)->toString());
    }

    public function testIsEqualToTheSameId(): void
    {
        self::assertTrue(DepositId::fromString(self::ID)->equals(DepositId::fromString(self::ID)));
        self::assertFalse(DepositId::fromString(self::ID)->equals(DepositId::fromString('0199f2b0-1c2d-7e3f-8a9b-0c1d2e3f4a5c')));
    }

    public function testRefusesAnythingButALowercaseUuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('"x0199f2b0-1c2d-7e3f-8a9b-0c1d2e3f4a5b" is not a deposit id.');

        DepositId::fromString('x0199f2b0-1c2d-7e3f-8a9b-0c1d2e3f4a5b');
    }

    public function testRefusesTextAfterTheUuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DepositId::fromString('0199f2b0-1c2d-7e3f-8a9b-0c1d2e3f4a5bx');
    }
}
