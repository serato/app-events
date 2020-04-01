<?php
declare(strict_types=1);

namespace Serato\AppEvents\Test\Event\Checkout;

use Serato\AppEvents\Test\AbstractTestCase;
use Serato\AppEvents\Event\Checkout\Order;
use Serato\AppEvents\Event\Checkout\OrderInvoice;
use Serato\AppEvents\Event\Checkout\OrderItem;
use Serato\AppEvents\Event\Checkout\OrderItemPromotion;
use DateTime;

class OrderTest extends AbstractTestCase
{
    public function testSmokeTest(): void
    {
        $orderItems = [$this->getOrderItem()];

        $event = new Order;

        $event
            # AbstractTimeSeriesEvent
            ->setUserId('user-123')
            ->setClientIp('24.30.52.126')
            ->setUserAgent(
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 ' .
                '(KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36'
            )
            ->setEventStart(new DateTime)
            ->setEventEnd(new DateTime)
            # Order
            ->setOrderId('order-123')
            ->setOrderInteractive(true)
            ->setCartId('cart-456')
            ->setOrderUserId('user-123')
            ->setUserEmailAddress('test@email.net')
            ->setUserOrganizationName('Mee Corp')
            ->setUserFirstName('Me')
            ->setUserLastName('Too')
            ->setUserBillingAddress1('123 My Street')
            ->setUserBillingAddress2('Suburbia')
            ->setUserBillingAddressCity('Los Angeles')
            ->setUserBillingAddressRegion('California')
            ->setUserBillingAddressPostcode('90210')
            ->setUserBillingAddressCountryCode('US')
            ->setTaxRate(0.0)
            ->setPaymentGateway(Order::BRAINTREE)
            ->setPaymentGatewayTransactionReference('ref-abcdef')
            ->setPaymentInstrumentType(Order::CREDITCARD)
            ->setPaymentInstrumentName('Visa 0122')
            ->setPaymentInstrumentTransactionReference('ref-12345')
            ->setOrderItems($orderItems)
            ->setOrderInvoices([$this->getOrderInvoice($orderItems)])
        ;

        $this->assertTrue(is_array($event->get()));
        $this->assertEquals([$event->getEventActionCategory(), $event->getEventActionName()], $event->getEventAction());
    }

    /**
     * @expectedException \Exception
     */
    public function testInvaidSetOrderItems(): void
    {
        $event = new Order;
        $event->setOrderItems([1]);
    }

    /**
     * @expectedException \Exception
     */
    public function testInvaidSetOrderInvoices(): void
    {
        $event = new Order;
        $event->setOrderInvoices([1]);
    }

    /**
     * @expectedException \Serato\AppEvents\Exception\InvalidDataValueException
     */
    public function testInvalidPaymentGateway(): void
    {
        $event = new Order;
        $event->setPaymentGateway('DEFO-INVALID');
    }

    /**
     * @expectedException \Serato\AppEvents\Exception\InvalidDataValueException
     */
    public function testInvalidPaymentInstrumentType(): void
    {
        $event = new Order;
        $event->setPaymentInstrumentType('DEFO-INVALID');
    }

    protected function getOrderItem(): OrderItem
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
        return $orderItem;
    }
    
    protected function getOrderInvoice(array $orderItems): OrderInvoice
    {
        $inv = new OrderInvoice;
        $inv
            ->setEventId('InvoiceId-123')
            ->setId('InvoiceId-123')
            ->setDebtorCode(OrderInvoice::WEBC001)
            ->setInvoiceItems($orderItems);
        return $inv;
    }
}
