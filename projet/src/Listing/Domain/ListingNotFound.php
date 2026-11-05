<?php

declare(strict_types=1);

namespace App\Listing\Domain;

use App\Shared\Domain\ListingId;

/**
 * No listing was saved with this identity.
 */
final class ListingNotFound extends \DomainException
{
    public static function withId(ListingId $id): self
    {
        return new self(\sprintf('Listing %s does not exist.', $id->toString()));
    }
}
