<?php

declare(strict_types=1);

namespace App\Tests\Feature\Search;

use App\Modules\Search\Response\OrderSearchItem;
use App\Modules\Search\Response\OrderSearchResponse;
use App\Modules\Search\Service\ManticoreSearchOrderService;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SearchOrderTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();

        $mock = $this->createMock(ManticoreSearchOrderService::class);
        self::getContainer()->set(ManticoreSearchOrderService::class, $mock);
    }

    private function fakeItem(int $id = 1): OrderSearchItem
    {
        return new OrderSearchItem(
            id: $id,
            hash: 'hash-'.$id,
            status: 'new',
            locale: 'de',
            currency: 'EUR',
            measure: 'm',
            discountPercent: 5,
            managerId: 2,
            createdAt: time(),
            updatedAt: time(),
            itemsCount: 3,
        );
    }

    private function fakeResponse(array $items = [], int $total = 0): OrderSearchResponse
    {
        return new OrderSearchResponse(
            items: $items,
            total: $total,
            took: 2
        );
    }

    #[Test]
    public function it_returns_empty_results_by_default(): void
    {
        $mock = self::getContainer()->get(ManticoreSearchOrderService::class);

        $mock->expects($this->once())
            ->method('advancedSearch')
            ->with(null, null, null, 20, 0)
            ->willReturn($this->fakeResponse());

        $this->client->request('GET', '/api/v1/search/orders');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertSame(0, $response['total']);
        $this->assertSame([], $response['items']);
        $this->assertArrayHasKey('took', $response);
    }

    #[Test]
    public function it_searches_by_query(): void
    {
        $mock = self::getContainer()->get(ManticoreSearchOrderService::class);

        $mock->expects($this->once())
            ->method('advancedSearch')
            ->with('DHL', null, null, 20, 0)
            ->willReturn(
                $this->fakeResponse(
                    items: [$this->fakeItem()],
                    total: 1
                )
            );

        $this->client->request('GET', '/api/v1/search/orders', [
            'q' => 'DHL',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    #[Test]
    public function it_filters_by_status_and_currency(): void
    {
        $mock = self::getContainer()->get(ManticoreSearchOrderService::class);

        $mock->expects($this->once())
            ->method('advancedSearch')
            ->with(null, 'new', 'EUR', 20, 0)
            ->willReturn(
                $this->fakeResponse(
                    items: [$this->fakeItem(1), $this->fakeItem(2)],
                    total: 2
                )
            );

        $this->client->request('GET', '/api/v1/search/orders', [
            'status' => 'new',
            'currency' => 'EUR',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    #[Test]
    public function it_supports_pagination(): void
    {
        $mock = self::getContainer()->get(ManticoreSearchOrderService::class);

        $mock->expects($this->once())
            ->method('advancedSearch')
            ->with(null, null, null, 10, 30)
            ->willReturn(
                $this->fakeResponse(
                    items: [],
                    total: 100
                )
            );

        $this->client->request('GET', '/api/v1/search/orders', [
            'limit' => 10,
            'offset' => 30,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    #[Test]
    public function it_returns_valid_item_structure(): void
    {
        $mock = self::getContainer()->get(ManticoreSearchOrderService::class);

        $mock->method('advancedSearch')
            ->willReturn(
                $this->fakeResponse(
                    items: [$this->fakeItem()],
                    total: 1
                )
            );

        $this->client->request('GET', '/api/v1/search/orders');

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $item = $response['items'][0];

        $this->assertArrayHasKey('id', $item);
        $this->assertArrayHasKey('hash', $item);
        $this->assertArrayHasKey('status', $item);
        $this->assertArrayHasKey('currency', $item);
        $this->assertArrayHasKey('itemsCount', $item);
    }
}
