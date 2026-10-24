<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain;

use App\Shared\Domain\ListingId;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ListingIdTest extends TestCase
{
    private const string ID = '0199f2a4-6b1e-7c3d-9a8f-2e4b6c8d0f12';

    public function testKeepsItsValue(): void
    {
        self::assertSame(self::ID, ListingId::fromString(self::ID)->toString());
    }

    public function testIsEqualToTheSameId(): void
    {
        self::assertTrue(ListingId::fromString(self::ID)->equals(ListingId::fromString(self::ID)));
        self::assertFalse(ListingId::fromString(self::ID)->equals(ListingId::fromString('0199f2a4-6b1e-7c3d-9a8f-2e4b6c8d0f13')));
    }

    #[DataProvider('notAnId')]
    public function testRefusesAnythingButALowercaseUuid(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('"%s" is not a listing id.', $value));

        ListingId::fromString($value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function notAnId(): iterable
    {
        yield 'empty' => [''];
        yield 'an email' => ['nora@example.com'];
        yield 'uppercase' => ['0199F2A4-6B1E-7C3D-9A8F-2E4B6C8D0F12'];
        yield 'text before' => ['x0199f2a4-6b1e-7c3d-9a8f-2e4b6c8d0f12'];
        yield 'text after' => ['0199f2a4-6b1e-7c3d-9a8f-2e4b6c8d0f12x'];
    }
}
