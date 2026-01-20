<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Modules\Common\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public const ARTICLE_1 = 'article_1';
    public const ARTICLE_2 = 'article_2';

    public function load(ObjectManager $manager): void
    {
        $articles = [
            ['SKU-001', 'Ceramic Tile White', '18.500'],
            ['SKU-002', 'Ceramic Tile Gray', '19.200'],
        ];

        foreach ($articles as [$sku, $name, $weight]) {
            $article = new Article();
            $article->setSku($sku);
            $article->setName($name);
            $article->setWeight($weight);

            $manager->persist($article);

            if ($sku === 'SKU-001') {
                $this->addReference(self::ARTICLE_1, $article);
            }

            if ($sku === 'SKU-002') {
                $this->addReference(self::ARTICLE_2, $article);
            }
        }

        $manager->flush();
    }
}
