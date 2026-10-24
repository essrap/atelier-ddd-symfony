<?php

declare(strict_types=1);

namespace App\Listing\Domain;

/**
 * A business rule of the listing refused the change.
 */
final class ListingRuleBroken extends \DomainException
{
    public static function emptyTitle(): self
    {
        return new self('A listing needs a title.');
    }

    public static function freePrice(): self
    {
        return new self('A listing cannot be rented for free.');
    }

    public static function currencyMismatch(): self
    {
        return new self('The daily price and the deposit must use the same currency.');
    }

    public static function cannotPublish(ListingStatus $status): self
    {
        return new self(\sprintf('Only a draft can be published, this listing is %s.', $status->value));
    }

    public static function alreadyWithdrawn(): self
    {
        return new self('This listing is withdrawn.');
    }
}
