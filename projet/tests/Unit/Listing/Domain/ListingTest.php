<?php

declare(strict_types=1);

namespace App\Tests\Unit\Listing\Domain;

use App\Listing\Domain\Listing;
use App\Listing\Domain\ListingPublished;
use App\Listing\Domain\ListingRuleBroken;
use App\Listing\Domain\ListingStatus;
use App\Shared\Domain\Currency;
use App\Shared\Domain\ListingId;
use App\Shared\Domain\Money;
use PHPUnit\Framework\TestCase;

final class ListingTest extends TestCase
{
    public function testIsBornAsADraft(): void
    {
        $listing = $this->drill();

        self::assertSame(ListingStatus::Draft, $listing->status);
        self::assertSame('Cordless drill', $listing->title);
    }

    public function testTwoListingsWithTheSameValuesAreStillTwoListings(): void
    {
        $first = $this->drill('0199f2a4-6b1e-7c3d-9a8f-2e4b6c8d0f12');
        $second = $this->drill('0199f2a4-6b1e-7c3d-9a8f-2e4b6c8d0f13');

        self::assertFalse($first->id->equals($second->id));
    }

    public function testRefusesAnEmptyTitle(): void
    {
        $this->expectExceptionObject(ListingRuleBroken::emptyTitle());

        Listing::draft($this->id(), '   ', $this->euros(1990), $this->euros(15000));
    }

    public function testRefusesAFreeListing(): void
    {
        $this->expectExceptionObject(ListingRuleBroken::freePrice());

        Listing::draft($this->id(), 'Cordless drill', $this->euros(0), $this->euros(15000));
    }

    public function testRefusesAPriceAndADepositInTwoCurrencies(): void
    {
        $this->expectExceptionObject(ListingRuleBroken::currencyMismatch());

        Listing::draft($this->id(), 'Cordless drill', $this->euros(1990), Money::of(15000, Currency::CHF));
    }

    public function testPublishesADraft(): void
    {
        $listing = $this->drill();

        $listing->publish($this->now());

        self::assertSame(ListingStatus::Published, $listing->status);
    }

    public function testRecordsThatItIsPublished(): void
    {
        $listing = $this->drill();

        $listing->publish($this->now());

        self::assertEquals([new ListingPublished($listing->id, $this->now())], $listing->releaseEvents());
    }

    public function testReleasesThePublicationOnlyOnce(): void
    {
        $listing = $this->drill();
        $listing->publish($this->now());

        $listing->releaseEvents();

        self::assertSame([], $listing->releaseEvents());
    }

    public function testRecordsNoEventForARefusedPublication(): void
    {
        $listing = $this->drill();
        $listing->publish($this->now());
        $listing->withdraw();
        $listing->releaseEvents();

        try {
            $listing->publish($this->now());
            self::fail('Publishing a withdrawn listing should have been refused.');
        } catch (ListingRuleBroken) {
        }

        self::assertSame([], $listing->releaseEvents());
    }

    public function testCannotPublishTwice(): void
    {
        $listing = $this->drill();
        $listing->publish($this->now());

        $this->expectExceptionObject(ListingRuleBroken::cannotPublish(ListingStatus::Published));

        $listing->publish($this->now());
    }

    public function testCannotRepublishAWithdrawnListing(): void
    {
        $listing = $this->drill();
        $listing->publish($this->now());
        $listing->withdraw();

        $this->expectExceptionObject(ListingRuleBroken::cannotPublish(ListingStatus::Withdrawn));

        $listing->publish($this->now());
    }

    public function testWithdrawsADraftOrAPublishedListing(): void
    {
        $draft = $this->drill();
        $published = $this->drill();
        $published->publish($this->now());

        $draft->withdraw();
        $published->withdraw();

        self::assertSame(ListingStatus::Withdrawn, $draft->status);
        self::assertSame(ListingStatus::Withdrawn, $published->status);
    }

    public function testCannotWithdrawTwice(): void
    {
        $listing = $this->drill();
        $listing->withdraw();

        $this->expectExceptionObject(ListingRuleBroken::alreadyWithdrawn());

        $listing->withdraw();
    }

    public function testChangesTheDailyPrice(): void
    {
        $listing = $this->drill();

        $listing->changeDailyPrice($this->euros(2490));

        self::assertTrue($listing->dailyPrice->equals($this->euros(2490)));
    }

    public function testCannotChangeThePriceOfAWithdrawnListing(): void
    {
        $listing = $this->drill();
        $listing->withdraw();

        $this->expectExceptionObject(ListingRuleBroken::alreadyWithdrawn());

        $listing->changeDailyPrice($this->euros(2490));
    }

    public function testKeepsTheRulesWhenThePriceChanges(): void
    {
        $listing = $this->drill();

        $this->expectExceptionObject(ListingRuleBroken::currencyMismatch());

        $listing->changeDailyPrice(Money::of(2490, Currency::CHF));
    }

    public function testCannotBecomeFree(): void
    {
        $listing = $this->drill();

        $this->expectExceptionObject(ListingRuleBroken::freePrice());

        $listing->changeDailyPrice($this->euros(0));
    }

    private function drill(string $id = '0199f2a4-6b1e-7c3d-9a8f-2e4b6c8d0f12'): Listing
    {
        return Listing::draft($this->id($id), ' Cordless drill ', $this->euros(1990), $this->euros(15000));
    }

    private function id(string $id = '0199f2a4-6b1e-7c3d-9a8f-2e4b6c8d0f12'): ListingId
    {
        return ListingId::fromString($id);
    }

    private function euros(int $cents): Money
    {
        return Money::of($cents, Currency::EUR);
    }

    private function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2026-11-04 09:30');
    }
}
