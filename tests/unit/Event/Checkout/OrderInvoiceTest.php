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
            # AbstractTimeSeriesEvent
            ->setEventId('InvoiceId-123')
            # OrderInvoice
            ->setId('123')
            ->setNumber('InvoiceId-123')
            ->setDebtorCode(OrderInvoice::WEBC001)
            ->setPaymentGateway(OrderInvoice::BRAINTREE)
            ->setPaymentGatewayTransactionReference('ref-abcdef')
            ->setPaymentInstrumentType(OrderInvoice::CREDITCARD)
            ->setPaymentInstrumentName('Visa 0122')
            ->setPaymentInstrumentTransactionReference('ref-12345')

            ->setInvoiceItems([$orderItem]);

        $this->assertTrue(is_array($event->get()));
        $this->assertEquals([$event->getEventActionCategory(), $event->getEventActionName()], $event->getEventAction());
    }

    /**
     * @expectedException \Serato\AppEvents\Exception\InvalidDataValueException
     */
    public function testInvalidPaymentGateway(): void
    {
        $event = new OrderInvoice;
        $event->setPaymentGateway('DEFO-INVALID');
    }

    /**
     * @expectedException \Serato\AppEvents\Exception\InvalidDataValueException
     */
    public function testInvalidPaymentInstrumentType(): void
    {
        $event = new OrderInvoice;
        $event->setPaymentInstrumentType('DEFO-INVALID');
    }

    /**
     * @expectedException \Exception
     */
    public function testInvaidSetInvoiceItems(): void
    {
        $event = new OrderInvoice;
        $event->setInvoiceItems([1]);
    }

    /**
     * @expectedException \Serato\AppEvents\Exception\InvalidDataValueException
     */
    public function testInvalidDebtorCode(): void
    {
        $event = new OrderInvoice;
        $event->setDebtorCode('DEFO-INVALID');
    }
}
