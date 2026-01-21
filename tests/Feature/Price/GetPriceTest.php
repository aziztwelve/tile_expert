<?php

declare(strict_types=1);

namespace App\Tests\Feature\Price;

use App\Modules\Pricing\Service\Provider\PriceProviderInterface;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class GetPriceTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();

        $this->client = static::createClient();

        $mock = $this->createMock(PriceProviderInterface::class);
        self::getContainer()->set(PriceProviderInterface::class, $mock);
    }

    #[Test]
    public function it_returns_price_when_all_params_are_valid(): void
    {
        $mock = self::getContainer()->get(PriceProviderInterface::class);

        $mock->method('getPrice')
            ->with('cobsa', 'manual', 'article-123')
            ->willReturn(38.99);

        $this->client->request('GET', '/api/v1/price', [
            'factory' => 'cobsa',
            'collection' => 'manual',
            'article' => 'article-123',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertSame(38.99, $response['price']);
        $this->assertSame('cobsa', $response['factory']);
        $this->assertSame('manual', $response['collection']);
        $this->assertSame('article-123', $response['article']);
    }

    #[Test]
    public function it_returns_404_when_price_is_not_found(): void
    {
        $mock = self::getContainer()->get(PriceProviderInterface::class);

        $mock->method('getPrice')
            ->willReturn(null);

        $this->client->request('GET', '/api/v1/price', [
            'factory' => 'cobsa',
            'collection' => 'manual',
            'article' => 'unknown',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertSame('Price not found for the given parameters', $response['error']);
    }

    #[Test]
    public function it_fails_when_required_parameters_are_missing(): void
    {
        // отсутствует article
        $this->client->request('GET', '/api/v1/price', [
            'factory' => 'cobsa',
            'collection' => 'manual',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    #[Test]
    public function it_fails_when_price_provider_throws_exception(): void
    {
        $mock = self::getContainer()->get(PriceProviderInterface::class);

        $mock->method('getPrice')
            ->willThrowException(new \RuntimeException('External API error'));

        $this->client->request('GET', '/api/v1/price', [
            'factory' => 'cobsa',
            'collection' => 'manual',
            'article' => 'article-123',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertSame('Failed to fetch price', $response['error']);
    }

    #[Test]
    public function it_returns_valid_json_structure(): void
    {
        $mock = self::getContainer()->get(PriceProviderInterface::class);

        $mock->method('getPrice')->willReturn(12.50);

        $this->client->request('GET', '/api/v1/price', [
            'factory' => 'test',
            'collection' => 'col',
            'article' => 'art',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertArrayHasKey('price', $response);
        $this->assertArrayHasKey('factory', $response);
        $this->assertArrayHasKey('collection', $response);
        $this->assertArrayHasKey('article', $response);
    }
}
