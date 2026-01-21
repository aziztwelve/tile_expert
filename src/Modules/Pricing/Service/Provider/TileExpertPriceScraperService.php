<?php

declare(strict_types=1);

namespace App\Modules\Pricing\Service\Provider;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class TileExpertPriceScraperService implements PriceProviderInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $tileExpertBaseUrl,
    ) {}

    public function getPrice(string $factory, string $collection, string $article): ?float
    {
        $url = sprintf(
            '%s/%s/%s/a/%s',
            rtrim($this->tileExpertBaseUrl, '/'),
            $factory,
            $collection,
            $article
        );

        $response = $this->httpClient->request('GET', $url, [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept-Language' => 'en-US,en;q=0.9',
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $html = $response->getContent();
        $crawler = new Crawler($html);

        $jsonNode = $crawler->filter('script[data-js-react-on-rails-store="appStore"]');
        if ($jsonNode->count() === 0) {
            return null;
        }

        $data = json_decode($jsonNode->text(), true, 512, JSON_THROW_ON_ERROR);

        $elementId = $data['slider']['elementId'] ?? null;
        if (!$elementId) {
            return null;
        }

        $element = $data['slider']['elements'][$elementId] ?? null;

        if (!$element) {
            return null;
        }

        if (isset($element['priceUSD'])) {
            return (float) $element['priceUSD'];
        }

        return null;
    }
}
