<?php

declare(strict_types=1);

namespace App\Listing\Domain;

use App\Shared\Domain\ListingId;
use App\Shared\Domain\Money;

/**
 * Equipment offered for rent. An entity: it keeps its identity while its price and status change.
 * Everyone can read its state; only its own methods can change it.
 */
final class Listing
{
    private function __construct(
        public readonly ListingId $id,
        public private(set) string $title,
        public private(set) Money $dailyPrice,
        public private(set) Money $deposit,
        public private(set) ListingStatus $status,
    ) {}

    public static function draft(ListingId $id, string $title, Money $dailyPrice, Money $deposit): self
    {
        if ('' === trim($title)) {
            throw ListingRuleBroken::emptyTitle();
        }
        self::assertPriceFits($dailyPrice, $deposit);

        return new self($id, trim($title), $dailyPrice, $deposit, ListingStatus::Draft);
    }

    public function publish(): void
    {
        if (ListingStatus::Draft !== $this->status) {
            throw ListingRuleBroken::cannotPublish($this->status);
        }

        $this->status = ListingStatus::Published;
    }

    public function withdraw(): void
    {
        $this->assertNotWithdrawn();

        $this->status = ListingStatus::Withdrawn;
    }

    public function changeDailyPrice(Money $dailyPrice): void
    {
        $this->assertNotWithdrawn();
        self::assertPriceFits($dailyPrice, $this->deposit);

        $this->dailyPrice = $dailyPrice;
    }

    private function assertNotWithdrawn(): void
    {
        if (ListingStatus::Withdrawn === $this->status) {
            throw ListingRuleBroken::alreadyWithdrawn();
        }
    }

    private static function assertPriceFits(Money $dailyPrice, Money $deposit): void
    {
        if (0 === $dailyPrice->minorAmount) {
            throw ListingRuleBroken::freePrice();
        }
        if ($dailyPrice->currency !== $deposit->currency) {
            throw ListingRuleBroken::currencyMismatch();
        }
    }
}
