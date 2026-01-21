<?php

declare(strict_types=1);

namespace App\Tests\Feature\Order;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class GetOrderTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    #[Test]
    public function it_returns_order_by_id(): void
    {
        // предполагается, что заказ с ID=1 существует в тестовой БД
        $this->client->request('GET', '/api/v1/orders/1');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertSame(1, $response['id']);
        $this->assertArrayHasKey('number', $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('created_at', $response);
    }

    #[Test]
    public function it_returns_404_when_order_does_not_exist(): void
    {
        $this->client->request('GET', '/api/v1/orders/999999');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertSame('Order not found', $response['error']);
    }

    #[Test]
    public function it_returns_items_and_addresses(): void
    {
        $this->client->request('GET', '/api/v1/orders/1');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertArrayHasKey('items', $response);
        $this->assertIsArray($response['items']);
        $this->assertNotEmpty($response['items']);

        $item = $response['items'][0];
        $this->assertArrayHasKey('article', $item);
        $this->assertArrayHasKey('quantity', $item);

        $this->assertArrayHasKey('addresses', $response);
        $this->assertIsArray($response['addresses']);
    }

    #[Test]
    public function it_returns_optional_blocks_when_present(): void
    {
        $this->client->request('GET', '/api/v1/orders/1');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        // optional блоки могут быть null или массивом — оба варианта валидны
        $this->assertArrayHasKey('manager', $response);
        $this->assertArrayHasKey('delivery', $response);
        $this->assertArrayHasKey('payment', $response);
        $this->assertArrayHasKey('carrier', $response);
    }
}
