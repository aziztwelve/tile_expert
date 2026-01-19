<?php

namespace App\DataFixtures;

use App\Modules\Common\Entity\Article;
use App\Modules\Common\Entity\Country;
use App\Modules\Order\Entity\Order;
use App\Modules\Order\Entity\OrderAddress;
use App\Modules\Order\Entity\OrderCarrier;
use App\Modules\Order\Entity\OrderDelivery;
use App\Modules\Order\Entity\OrderItem;
use App\Modules\Order\Entity\OrderPayment;
use App\Modules\Order\Enum\OrderStatus;
use App\Modules\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $order = new Order();
        $order->setHash(md5(uniqid('', true)));
        $order->setNumber('ORD-0001');
        $order->setUser($this->getReference(UserFixtures::USER_CLIENT, User::class));
        $order->setManager($this->getReference(UserFixtures::USER_MANAGER, User::class));
        $order->setStatus(OrderStatus::NEW);
        $order->setLocale('de');
        $order->setCurrency('EUR');
        $order->setMeasure('m');
        $order->setName('Test order');
        $order->setCreatedAt(new \DateTimeImmutable());

        $item1 = new OrderItem();
        $item1->setOrder($order);
        $item1->setArticle($this->getReference(ArticleFixtures::ARTICLE_1, Article::class));
        $item1->setQuantity('25.000');
        $item1->setUnitPrice('12.50');
        $item1->setWeight('18.500');

        $item2 = new OrderItem();
        $item2->setOrder($order);
        $item2->setArticle($this->getReference(ArticleFixtures::ARTICLE_2, Article::class));
        $item2->setQuantity('10.000');
        $item2->setUnitPrice('14.20');
        $item2->setWeight('19.200');

        $address = new OrderAddress();
        $address->setOrder($order);
        $address->setType('delivery');
        $address->setCountry($this->getReference(CountryFixtures::COUNTRY_DE, Country::class));
        $address->setCity('Berlin');
        $address->setAddress('Alexanderplatz 1');

        $delivery = new OrderDelivery();
        $delivery->setOrder($order);
        $delivery->setType(0);
        $delivery->setPrice('120.00');

        $payment = new OrderPayment();
        $payment->setOrder($order);
        $payment->setPayType(1);
        $payment->setCurRate('1.0000');

        $carrier = new OrderCarrier();
        $carrier->setOrder($order);
        $carrier->setName('DHL');
        $carrier->setContactData('support@dhl.com');

        $manager->persist($order);
        $manager->persist($item1);
        $manager->persist($item2);
        $manager->persist($address);
        $manager->persist($delivery);
        $manager->persist($payment);
        $manager->persist($carrier);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CountryFixtures::class,
            ArticleFixtures::class,
        ];
    }
}
