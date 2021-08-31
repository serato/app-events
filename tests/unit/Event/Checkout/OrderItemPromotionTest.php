<?php

declare(strict_types=1);

namespace Serato\AppEvents\Test\Event\Checkout;

use Serato\AppEvents\Test\AbstractTestCase;
use Serato\AppEvents\Event\Checkout\OrderItemPromotion;

class OrderItemPromotionTest extends AbstractTestCase
{
    public function testSmokeTest(): void
    {
        $event = new OrderItemPromotion();

        $event
            ->setPromotionId('promo-id')
            ->setName('My Promo')
            ->setPromotionCode('25-off-4-eva')
            ->setDiscountPercentage(25.0)
            ->setDiscountFixedAmount(0.00);

        $this->assertTrue(is_array($event->get()));
    }
}
