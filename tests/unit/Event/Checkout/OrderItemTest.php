<?php

declare(strict_types=1);

namespace Serato\AppEvents\Test\Event\Checkout;

use Serato\AppEvents\Test\AbstractTestCase;
use Serato\AppEvents\Event\Checkout\OrderItem;
use Serato\AppEvents\Event\Checkout\OrderItemPromotion;

class OrderItemTest extends AbstractTestCase
{
    public function testSmokeTest(): void
    {
        $qty = 1;
        $taxRate = 22.5;
        $amountsBase = 10;
        $amountsDiscounts = 0;
        $amountsTax = 2.25;
        $amountsTotal = 12.25;

        $promo = new OrderItemPromotion();
        $promo
            ->setPromotionId('promo-id')
            ->setName('My Promo')
            ->setPromotionCode('25-off-4-eva')
            ->setDiscountPercentage(25.0)
            ->setDiscountFixedAmount(0.00);

        $event = new OrderItem();

        $event
            ->setOrderItemId("123")
            ->setSkuId("456")
            ->setSkuCode("SKU-456")
            ->setSkuName("My Sku")
            ->setSkuLicenseTerm(OrderItem::PERMANENT)
            ->setSubscriptionId("abcdef")
            ->setQuantity($qty)
            ->setTaxCode(OrderItem::TAXCODE_Z)
            ->setTaxRate($taxRate)
            ->setAmountsBase($amountsBase)
            ->setAmountsDiscounts($amountsDiscounts)
            ->setAmountsTax($amountsTax)
            ->setAmountsTotal($amountsTotal)
            ->setPromotions([$promo])
        ;

        $this->assertTrue(is_array($event->get()));

        $this->assertEquals($event->getQuantity(), $qty);
        $this->assertEquals($event->getTaxRate(), $taxRate);
        $this->assertEquals($event->getAmountsBase(), $amountsBase);
        $this->assertEquals($event->getAmountsDiscounts(), $amountsDiscounts);
        $this->assertEquals($event->getAmountsTax(), $amountsTax);
        $this->assertEquals($event->getAmountsTotal(), $amountsTotal);
    }

    /**
     * @expectedException \Exception
     */
    public function testInvaidSetPromotions(): void
    {
        $event = new OrderItem();
        $event->setPromotions([1]);
    }

    /**
     * @expectedException \Serato\AppEvents\Exception\InvalidDataValueException
     */
    public function testInvaidSetSkuLicenseTerm(): void
    {
        $event = new OrderItem();
        $event->setSkuLicenseTerm('WorksForAWeek');
    }

    /**
     * @expectedException \Serato\AppEvents\Exception\InvalidDataValueException
     */
    public function testInvaidSetTaxCode(): void
    {
        $event = new OrderItem();
        $event->setTaxCode('HeapsOfTax');
    }
}
