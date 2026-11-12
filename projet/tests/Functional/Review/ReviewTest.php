<?php

declare(strict_types=1);

namespace App\Tests\Functional\Review;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ReviewTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        self::getContainer()->get(Connection::class)->executeStatement('TRUNCATE review');
    }

    public function testLeavesAReview(): void
    {
        $this->post(['rentalReference' => 'rental-1', 'rating' => 4, 'comment' => 'Perfect, thanks']);

        self::assertResponseStatusCodeSame(201);
    }

    public function testRefusesSevenStars(): void
    {
        $this->post(['rentalReference' => 'rental-1', 'rating' => 7]);

        self::assertResponseStatusCodeSame(422);
        self::assertStringContainsString('"rating"', (string) $this->client->getResponse()->getContent());
    }

    public function testRefusesACommentLongerThanFiveHundredCharacters(): void
    {
        $this->post(['rentalReference' => 'rental-1', 'rating' => 3, 'comment' => str_repeat('a', 501)]);

        self::assertResponseStatusCodeSame(422);
        self::assertStringContainsString('"comment"', (string) $this->client->getResponse()->getContent());
    }

    public function testRefusesAReviewWithoutAValidRental(): void
    {
        $this->post(['rating' => 3]);

        self::assertResponseStatusCodeSame(422);
        self::assertStringContainsString('"rentalReference"', (string) $this->client->getResponse()->getContent());

        $this->post(['rentalReference' => str_repeat('r', 65), 'rating' => 3]);

        self::assertResponseStatusCodeSame(422);
        self::assertStringContainsString('"rentalReference"', (string) $this->client->getResponse()->getContent());
    }

    public function testListsTheReviewsOfOneRental(): void
    {
        $this->post(['rentalReference' => 'rental-1', 'rating' => 4, 'comment' => 'Perfect, thanks']);
        $this->post(['rentalReference' => 'rental-2', 'rating' => 2]);
        $this->post(['rentalReference' => 'rental-1', 'rating' => 5]);

        $this->client->request('GET', '/reviews?rental=rental-1');

        self::assertResponseIsSuccessful();
        self::assertJsonStringEqualsJsonString(
            '[{"rating":4,"comment":"Perfect, thanks"},{"rating":5,"comment":null}]',
            (string) $this->client->getResponse()->getContent(),
        );
    }

    /**
     * @param array<string, int|string> $payload
     */
    private function post(array $payload): void
    {
        $this->client->request(
            'POST',
            '/reviews',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload, \JSON_THROW_ON_ERROR),
        );
    }
}
