<?php

declare(strict_types=1);

namespace App\Tests\Unit\Listing\Infrastructure;

use App\Listing\Domain\Listing;
use App\Listing\Domain\ListingNotFound;
use App\Listing\Domain\ListingStatus;
use App\Listing\Infrastructure\InMemoryListings;
use App\Shared\Domain\Currency;
use App\Shared\Domain\ListingId;
use App\Shared\Domain\Money;
use PHPUnit\Framework\TestCase;

final class InMemoryListingsTest extends TestCase
{
    private const string DRILL = '0199f2a4-6b1e-7c3d-9a8f-2e4b6c8d0f12';
    private const string SANDER = '0199f2a4-6b1e-7c3d-9a8f-2e4b6c8d0f13';

    public function testGivesBackASavedListing(): void
    {
        $listings = new InMemoryListings();
        $listings->save($this->drill());

        $listing = $listings->get($this->id());

        self::assertTrue($listing->id->equals($this->id()));
        self::assertSame('Cordless drill', $listing->title);
    }

    public function testFindsEachListingByItsIdentity(): void
    {
        $listings = new InMemoryListings();
        $listings->save($this->drill());
        $listings->save(Listing::draft(
            ListingId::fromString(self::SANDER),
            'Orbital sander',
            Money::of(1490, Currency::EUR),
            Money::of(9000, Currency::EUR),
        ));

        self::assertSame('Cordless drill', $listings->get($this->id())->title);
        self::assertSame('Orbital sander', $listings->get(ListingId::fromString(self::SANDER))->title);
    }

    public function testRefusesAnUnknownListing(): void
    {
        $listings = new InMemoryListings();

        $this->expectExceptionObject(ListingNotFound::withId($this->id()));

        $listings->get($this->id());
    }

    public function testAChangeIsNotKeptUntilSaved(): void
    {
        $listings = new InMemoryListings();
        $listings->save($this->drill());

        $listings->get($this->id())->publish($this->now());

        self::assertSame(ListingStatus::Draft, $listings->get($this->id())->status);
    }

    public function testAChangeIsKeptOnceSaved(): void
    {
        $listings = new InMemoryListings();
        $listings->save($this->drill());

        $listing = $listings->get($this->id());
        $listing->publish($this->now());
        $listings->save($listing);

        self::assertSame(ListingStatus::Published, $listings->get($this->id())->status);
    }

    public function testAChangeAfterSaveIsNotKeptUntilSavedAgain(): void
    {
        $listings = new InMemoryListings();
        $listing = $this->drill();
        $listings->save($listing);

        $listing->publish($this->now());

        self::assertSame(ListingStatus::Draft, $listings->get($this->id())->status);
    }

    public function testDoesNotKeepTheEvents(): void
    {
        $listings = new InMemoryListings();
        $listing = $this->drill();
        $listing->publish($this->now());
        $listings->save($listing);

        self::assertSame([], $listings->get($this->id())->releaseEvents());
        self::assertCount(1, $listing->releaseEvents());
    }

    private function drill(): Listing
    {
        return Listing::draft(
            $this->id(),
            'Cordless drill',
            Money::of(1990, Currency::EUR),
            Money::of(15000, Currency::EUR),
        );
    }

    private function id(): ListingId
    {
        return ListingId::fromString(self::DRILL);
    }

    private function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2026-11-04 09:30');
    }
}
