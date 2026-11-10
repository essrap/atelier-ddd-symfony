<?php

declare(strict_types=1);

namespace App\Tests\Unit\Booking\Application;

final class StorageIsDown extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('The storage is down.');
    }
}
