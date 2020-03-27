<?php
declare(strict_types=1);

namespace Serato\AppEvents\Test\Event\Checkout;

use Serato\AppEvents\Test\AbstractTestCase;
use Serato\AppEvents\Event\Checkout\OrderInvoice;
use Serato\AppEvents\Event\Checkout\OrderItem;
use DateTime;

class OrderInvoiceTest extends AbstractTestCase
{
    public function testSmokeTest(): void
    {
        $orderItem = new OrderItem;
        $orderItem
            ->setOrderItemId("123")
            ->setSkuId("456")
            ->setSkuCode("SKU-456")
            ->setSkuName("My Sku")
            ->setSkuLicenseTerm(OrderItem::PERMANENT)
            ->setSubscriptionId("abcdef")
            ->setQuantity(1)
            ->setTaxCode(OrderItem::TAXCODE_Z)
            ->setTaxRate(0)
            ->setAmountsBase(20)
            ->setAmountsDiscounts(0)
            ->setAmountsTax(0)
            ->setAmountsTotal(20);

        $event = new OrderInvoice;

        $event
            # AbstractEventDataContainer
            ->setAppName('My Web app')
            # AbstractTimeSeriesEvent
            ->setEventId('InvoiceId-123')
            # OrderInvoice
            ->setId('InvoiceId-123')
            ->setDebtorCode(OrderInvoice::WEBC001)
            ->setInvoiceItems([$orderItem]);

        $this->assertTrue(is_array($event->get()));
    }

    // invalid setDebtorCode
}
