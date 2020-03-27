<?php
declare(strict_types=1);

namespace Serato\AppEvents\Event\Checkout;

use Exception;

/**
 * ** CheckoutOrder **
 *
 * Captures data attributes related to a completed checkout order and allows the
 * data to to be send to a logging target.
 *
 * Completed orders may be the result of a user interactive checkout process
 * or may be the result of an external recurring billing event.
 *
 * Sets the following fields:
 *
 * `<ROOT ATTR>.checkout_order.id`
 * `<ROOT ATTR>.checkout_order.interactive`
 * `<ROOT ATTR>.checkout_order.cart_id`
 * `<ROOT ATTR>.checkout_order.user.id`
 * `<ROOT ATTR>.checkout_order.user.email_address`
 * `<ROOT ATTR>.checkout_order.user.first_name`
 * `<ROOT ATTR>.checkout_order.user.last_name`
 * `<ROOT ATTR>.checkout_order.user.billing_address.address_1`
 * `<ROOT ATTR>.checkout_order.user.billing_address.address_2`
 * `<ROOT ATTR>.checkout_order.user.billing_address.city_name`
 * `<ROOT ATTR>.checkout_order.user.billing_address.region_name`
 * `<ROOT ATTR>.checkout_order.user.billing_address.postcode`
 * `<ROOT ATTR>.checkout_order.user.billing_address.country_iso_code`
 * `<ROOT ATTR>.checkout_order.tax_rate`
 * `<ROOT ATTR>.checkout_order.payment.provider`
 * `<ROOT ATTR>.checkout_order.payment.transaction_reference`
 * `<ROOT ATTR>.checkout_order.payment.payment_instrument.type`
 * `<ROOT ATTR>.checkout_order.payment.payment_instrument.name`
 * `<ROOT ATTR>.checkout_order.payment.payment_instrument.transaction_reference`
 *
 * Additionally, the `CheckoutOrder::setOrderItems` method takes an array of
 * `\Serato\AppEvents\Event\Checkout\OrderItem` objects and copies their data to:
 *
 * `<ROOT ATTR>.checkout_order.items`
 *
 *  The same method iterates over the array and calculates values for the following fields:
 *
 * `<ROOT ATTR>.checkout_order.amounts.base`
 * `<ROOT ATTR>.checkout_order.amounts.discounts`
 * `<ROOT ATTR>.checkout_order.amounts.tax`
 * `<ROOT ATTR>.checkout_order.amounts.total`
 */
class Order extends AbstractTimeSeriesCheckoutEvent
{
    public const BRAINTREE = 'braintree';
    public const CREDITCARD = 'creditcard';
    public const PAYPAL_ACCOUNT = 'paypal_account';

    public function __construct()
    {
        parent::__construct();
        $this
            # For now, the only supported payment gateway is Braintree
            ->setPaymentGateway(self::BRAINTREE)
            ->setData($this->getEventDataRootAttribute() . '.amounts.base', 0)
            ->setData($this->getEventDataRootAttribute() . '.amounts.discounts', 0)
            ->setData($this->getEventDataRootAttribute() . '.amounts.tax', 0)
            ->setData($this->getEventDataRootAttribute() . '.amounts.total', 0);
    }

    /**
     * {@inheritDoc}
     */
    public function getEventAction(): string
    {
        return 'order_created';
    }

    /**
     * Sets the order ID.
     *
     * Sets the following field(s):
     *
     * `event.id`
     * `<ROOT ATTR>.checkout_order.id`
     *
     * @param string $id
     * @return self
     */
    public function setOrderId(string $id): self
    {
        return $this
            ->setEventId($id)
            ->setData($this->getEventDataRootAttribute() . '.id', $id);
    }

    /**
     * Sets whether or not the order was created for a user interaction.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.checkout_order.interactive`
     *
     * @param string $i
     * @return self
     */
    public function setOrderInteractive(bool $i): self
    {
        return $this
            ->setData($this->getEventDataRootAttribute() . '.interactive', $i);
    }

    /**
     * Sets the cart id.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.checkout_order.cart_id`
     *
     * @param string $id
     * @return self
     */
    public function setCartId(string $id): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.cart_id', $id);
    }

    /**
     * Sets the order user ID.
     *
     * This is the user who is charged for the order, not the user who created the order.
     * The user who created the order is set by `self::setUserId` and may be ommitted
     * in the case of non-interactive orders (eg. orders for recurring billing charges.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.checkout_order.user.id`
     *
     * @param string $id
     * @return self
     */
    public function setOrderUserId(string $id): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.user.id', $id);
    }

    /**
     * Sets the order user email address.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.checkout_order.user.email_address`
     *
     * @param string $emailAddress
     * @return self
     */
    public function setUserEmailAddress(string $emailAddress): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.user.email_address', $emailAddress);
    }

    /**
     * Sets the order user first name.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.checkout_order.user.first_name`
     *
     * @param string $firstName
     * @return self
     */
    public function setUserFirstName(string $firstName): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.user.first_name', $firstName);
    }

    /**
     * Sets the order user last name.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.checkout_order.user.last_name`
     *
     * @param string $lastName
     * @return self
     */
    public function setUserLastName(string $lastName): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.user.last_name', $lastName);
    }

    /**
     * Sets the order user billing address 1.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.checkout_order.user.billing_address.address_1`
     *
     * @param string $address1
     * @return self
     */
    public function setUserBillingAddress1(string $address1): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.user.billing_address.address_1', $address1);
    }

    /**
     * Sets the order user billing address 2.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.checkout_order.user.billing_address.address_2`
     *
     * @param string $address2
     * @return self
     */
    public function setUserBillingAddress2(string $address2): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.user.billing_address.address_2', $address2);
    }

    /**
     * Sets the order user billing address city.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.checkout_order.user.billing_address.city_name`
     *
     * @param string $city
     * @return self
     */
    public function setUserBillingAddressCity(string $city): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.user.billing_address.city_name', $city);
    }

    /**
     * Sets the order user billing address region_name.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.checkout_order.user.billing_address.region_name`
     *
     * @param string $region
     * @return self
     */
    public function setUserBillingAddressRegion(string $region): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.user.billing_address.region_name', $region);
    }

    /**
     * Sets the order user billing address postcode.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.checkout_order.user.billing_address.postcode`
     *
     * @param string $postcode
     * @return self
     */
    public function setUserBillingAddressPostcode(string $postcode): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.user.billing_address.postcode', $postcode);
    }

    /**
     * Sets the order user billing address country code.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.checkout_order.user.billing_address.country_iso_code`
     *
     * @param string $countryCode
     * @return self
     */
    public function setUserBillingAddressCountryCode(string $countryCode): self
    {
        return $this->setData(
            $this->getEventDataRootAttribute() . '.user.billing_address.country_iso_code',
            $countryCode
        );
    }

    /**
     * Sets the order tax rate.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.checkout_order.tax_rate`
     *
     * @param string $rate
     * @return self
     */
    public function setTaxRate(float $rate): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.tax_rate', $rate);
    }

    /**
     * Sets the order payment gateway.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.checkout_order.payment.gateway`
     *
     * @param string $gateway
     * @return self
     */
    public function setPaymentGateway(string $gateway): self
    {
        $this->validateDataValue($gateway, [self::BRAINTREE], __METHOD__);
        return $this->setData($this->getEventDataRootAttribute() . '.payment.gateway', $gateway);
    }

    /**
     * Sets the order payment gateway transaction reference.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.checkout_order.payment.gateway_transaction_reference`
     *
     * @param string $ref
     * @return self
     */
    public function setPaymentGatewayTransactionReference(string $ref): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.payment.gateway_transaction_reference', $ref);
    }

    /**
     * Sets the payment instrument type.
     *
     * One of `creditcard` or `paypal_account`
     *
     * @param string $type
     * @return self
     */
    public function setPaymentInstrumentType(string $type): self
    {
        $this->validateDataValue($type, [self::CREDITCARD, self::PAYPAL_ACCOUNT], __METHOD__);
        return $this->setData($this->getEventDataRootAttribute() . '.payment.payment_instrument.type', $type);
    }

    public function setPaymentInstrumentName(string $name): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.payment.payment_instrument.name', $name);
    }
    
    public function setPaymentInstrumentTransactionReference(string $ref): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.payment.payment_instrument.transaction_reference', $ref);
    }

    /**
     * Adds an array of `Serato\AppEvents\Event\Checkout\OrderItem` objects to the
     * instance.
     *
     * Also uses the underlying item data to build up the total amounts for the instance.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.checkout_order.items`
     * `<ROOT ATTR>.checkout_order.amounts.base`
     * `<ROOT ATTR>.checkout_order.amounts.discounts`
     * `<ROOT ATTR>.checkout_order.amounts.tax`
     * `<ROOT ATTR>.checkout_order.amounts.total`
     *
     * @param array $orderItems
     * @return self
     */
    public function setOrderItems(array $orderItems): self
    {
        $data = [];
        $base = 0;
        $discounts = 0;
        $tax = 0;
        $total = 0;

        foreach ($orderItems as $checkoutOrderItem) {
            if (!is_a($checkoutOrderItem, '\Serato\AppEvents\Event\Checkout\OrderItem')) {
                throw new Exception(
                    'Invalid argument. Items must all be \Serato\AppEvents\Event\Checkout\OrderItem instances'
                );
            }

            $data[] = $checkoutOrderItem->get();

            $taxRate = $checkoutOrderItem->getTaxRate();
            $qty = $checkoutOrderItem->getQuantity();

            # Reminder: base amount for orders includes applied discounts
            $base       += ($checkoutOrderItem->getAmountsBase() - $checkoutOrderItem->getAmountsDiscounts()) * $qty;
            $discounts  += $checkoutOrderItem->getAmountsDiscounts() * $qty;
            $tax        += $checkoutOrderItem->getAmountsTax() * $qty;
            $total      += $checkoutOrderItem->getAmountsTotal() * $qty;
        }

        return $this
            ->setData($this->getEventDataRootAttribute() . '.items', $data)
            ->setData($this->getEventDataRootAttribute() . '.amounts.base', $base)
            ->setData($this->getEventDataRootAttribute() . '.amounts.discounts', $discounts)
            ->setData($this->getEventDataRootAttribute() . '.amounts.tax', $tax)
            ->setData($this->getEventDataRootAttribute() . '.amounts.total', $total);
    }

    /**
     * Adds an array of `Serato\AppEvents\Event\Checkout\OrderInvoice` objects to the
     * instance.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.checkout_order.invoices`
     *
     * @param array $orderInvoices
     * @return self
     */
    public function setOrderInvoices(array $orderInvoices): self
    {
        $data = [];
        foreach ($orderInvoices as $invoice) {
            if (!is_a($invoice, '\Serato\AppEvents\Event\Checkout\OrderInvoice')) {
                throw new Exception(
                    'Invalid argument. Items must all be \Serato\AppEvents\Event\Checkout\OrderInvoice instances'
                );
            }
            $data[] = $invoice->get();
        }
        return $this->setData($this->getEventDataRootAttribute() . '.invoices', $data);
    }
}
