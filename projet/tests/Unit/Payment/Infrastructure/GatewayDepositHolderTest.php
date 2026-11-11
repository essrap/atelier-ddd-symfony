<?php

declare(strict_types=1);

namespace App\Tests\Unit\Payment\Infrastructure;

use App\Payment\Domain\DepositId;
use App\Payment\Domain\DepositRefusalReason;
use App\Payment\Domain\DepositRefused;
use App\Payment\Infrastructure\GatewayDepositHolder;
use App\Shared\Domain\Currency;
use App\Shared\Domain\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Simulator\PaymentGateway\Gateway;

final class GatewayDepositHolderTest extends TestCase
{
    private const string MALIK_DEPOSIT = '0b9c3f5e-6a41-4d2e-9f1a-2c7d8e4b5a60';

    public function testHoldsTheDepositInTheGatewayWords(): void
    {
        $gateway = new Gateway();

        new GatewayDepositHolder($gateway)->hold($this->deposit(), $this->threeHundredEuros());

        self::assertSame([[
            'operation' => 'authorize',
            'amount' => '300.00',
            'currency' => 'eur',
            'reference' => self::MALIK_DEPOSIT,
        ]], $gateway->calls);
    }

    #[DataProvider('amounts')]
    public function testWritesCentsAsDecimals(
        int $cents,
        Currency $currency,
        string $amount,
        string $code,
    ): void {
        $gateway = new Gateway();

        new GatewayDepositHolder($gateway)->hold($this->deposit(), Money::of($cents, $currency));

        self::assertSame($amount, $gateway->calls[0]['amount'] ?? null);
        self::assertSame($code, $gateway->calls[0]['currency'] ?? null);
    }

    /**
     * @return iterable<string, array{int, Currency, string, string}>
     */
    public static function amounts(): iterable
    {
        yield 'five cents' => [5, Currency::EUR, '0.05', 'eur'];
        yield 'cents kept' => [4995, Currency::EUR, '49.95', 'eur'];
        yield 'swiss francs' => [120000, Currency::CHF, '1200.00', 'chf'];
    }

    public function testInsufficientFundsBecomeOurReason(): void
    {
        $this->assertRefusedWith(Gateway::INSUFFICIENT_FUNDS, DepositRefusalReason::InsufficientFunds);
    }

    public function testAnExpiredCardBecomesOurReason(): void
    {
        $this->assertRefusedWith(Gateway::EXPIRED_CARD, DepositRefusalReason::CardExpired);
    }

    public function testAnUnknownCodeNeverEntersTheDomain(): void
    {
        $this->assertRefusedWith('do_not_honor', DepositRefusalReason::Other);
    }

    public function testReleasesTheSameReference(): void
    {
        $gateway = new Gateway();

        new GatewayDepositHolder($gateway)->release($this->deposit());

        self::assertSame([[
            'operation' => 'void',
            'reference' => self::MALIK_DEPOSIT,
        ]], $gateway->calls);
    }

    private function assertRefusedWith(string $code, DepositRefusalReason $expected): void
    {
        $gateway = new Gateway();
        $gateway->declineNextWith($code);

        try {
            new GatewayDepositHolder($gateway)->hold($this->deposit(), $this->threeHundredEuros());
            self::fail('The deposit should have been refused.');
        } catch (DepositRefused $refused) {
            self::assertSame($expected, $refused->reason);
            self::assertTrue($refused->deposit->equals($this->deposit()));
        }
    }

    private function threeHundredEuros(): Money
    {
        return Money::of(30000, Currency::EUR);
    }

    private function deposit(): DepositId
    {
        return DepositId::fromString(self::MALIK_DEPOSIT);
    }
}
