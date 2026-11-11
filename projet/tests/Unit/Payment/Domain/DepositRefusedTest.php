<?php

declare(strict_types=1);

namespace App\Tests\Unit\Payment\Domain;

use App\Payment\Domain\DepositId;
use App\Payment\Domain\DepositRefusalReason;
use App\Payment\Domain\DepositRefused;
use PHPUnit\Framework\TestCase;

final class DepositRefusedTest extends TestCase
{
    public function testSaysWhichDepositAndWhy(): void
    {
        $refused = DepositRefused::because(
            DepositId::fromString('0b9c3f5e-6a41-4d2e-9f1a-2c7d8e4b5a60'),
            DepositRefusalReason::InsufficientFunds,
        );

        self::assertSame(
            'The deposit 0b9c3f5e-6a41-4d2e-9f1a-2c7d8e4b5a60 was refused: InsufficientFunds.',
            $refused->getMessage(),
        );
    }
}
