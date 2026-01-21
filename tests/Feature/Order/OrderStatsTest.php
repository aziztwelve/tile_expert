<?php

declare(strict_types=1);

namespace App\Tests\Feature\Order;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderStatsTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    #[Test]
    public function it_returns_stats_grouped_by_month(): void
    {
        $this->client->request('GET', '/api/v1/orders/stats', [
            'group' => 'month',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertArrayHasKey('meta', $response);
        $this->assertArrayHasKey('data', $response);

        $this->assertSame('month', $response['meta']['group']);
    }

    #[Test]
    public function it_supports_pagination(): void
    {
        $this->client->request('GET', '/api/v1/orders/stats', [
            'group' => 'day',
            'page' => 2,
            'limit' => 5,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertSame(2, $response['meta']['page']);
        $this->assertSame(5, $response['meta']['limit']);
        $this->assertArrayHasKey('total_pages', $response['meta']);
    }

    #[Test]
    public function it_fails_when_group_by_is_invalid(): void
    {
        $this->client->request('GET', '/api/v1/orders/stats', [
            'group' => 'hour',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertSame('error', $response['status']);
        $this->assertStringContainsString('not a valid choice', $response['message']);

    }
}
