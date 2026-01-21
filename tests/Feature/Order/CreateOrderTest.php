<?php

declare(strict_types=1);

namespace App\Tests\Feature\Order;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CreateOrderTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    #[Test]
    public function it_creates_order_with_valid_data(): void
    {
        $payload = [
            'name' => 'Test Order',
            'locale' => 'de',
            'currency' => 'EUR',
            'measure' => 'm',
            'discount_percent' => 5,
            'items' => [
                [
                    'article_id' => 1,
                    'quantity' => '25.000',
                    'unit_price' => '12.50',
                ]
            ],
            'addresses' => [
                [
                    'type' => 'delivery',
                    'country_id' => 1,
                    'city' => 'Berlin',
                    'address' => 'Alexanderplatz 1',
                ]
            ],
        ];

        $this->client->jsonRequest('POST', '/api/v1/orders', $payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertSame('success', $response['status']);
        $this->assertArrayHasKey('id', $response['data']);
        $this->assertEquals('Test Order', $response['data']['name']);
    }

    #[Test]
    public function it_creates_order_with_all_optional_fields(): void
    {
        $payload = [
            'name' => 'Full Order',
            'locale' => 'de',
            'currency' => 'EUR',
            'measure' => 'm',
            'discount_percent' => 10,

            'items' => [
                [
                    'article_id' => 1,
                    'quantity' => '10.000',
                    'unit_price' => '15.00',
                ],
            ],

            'addresses' => [
                [
                    'type' => 'delivery',
                    'country_id' => 1,
                    'city' => 'Munich',
                    'address' => 'Marienplatz 1',
                ],
            ],
        ];

        $this->client->jsonRequest('POST', '/api/v1/orders', $payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertSame('success', $response['status']);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('id', $response['data']);
    }

    #[Test]
    public function it_fails_when_name_is_missing(): void
    {
        $payload = [
            'locale' => 'de',
            'currency' => 'EUR',
            // name отсутствует
        ];

        $this->client->jsonRequest('POST', '/api/v1/orders', $payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('error', $response['status']);
    }

    #[Test]
    public function it_fails_when_items_are_empty(): void
    {
        $payload = [
            'name' => 'Empty Order',
            'locale' => 'de',
            'currency' => 'EUR',
            'measure' => 'm',
            'items' => [], // пустой массив
            'addresses' => [
                ['type' => 'delivery', 'country_id' => 1]
            ],
        ];

        $this->client->jsonRequest('POST', '/api/v1/orders', $payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    #[Test]
    public function it_fails_when_article_does_not_exist(): void
    {
        $payload = [
            'name' => 'Order with invalid article',
            'locale' => 'de',
            'currency' => 'EUR',
            'measure' => 'm',
            'items' => [
                ['article_id' => 99999, 'quantity' => '1.000', 'unit_price' => '10.00']
            ],
            'addresses' => [
                ['type' => 'delivery', 'country_id' => 1]
            ],
        ];

        $this->client->jsonRequest('POST', '/api/v1/orders', $payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertStringContainsString('field is missing', $response['message']);
    }
}
