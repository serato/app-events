<?php
declare(strict_types=1);

namespace Serato\AppEvents\Event;

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
 * `<ROOT ATTR>.id`
 * `<ROOT ATTR>.interactive`
 * `<ROOT ATTR>.cart_id`
 * `<ROOT ATTR>.user.id`
 * `<ROOT ATTR>.user.email_address`
 * `<ROOT ATTR>.user.first_name`
 * `<ROOT ATTR>.user.last_name`
 * `<ROOT ATTR>.user.billing_address.address_1`
 * `<ROOT ATTR>.user.billing_address.address_2`
 * `<ROOT ATTR>.user.billing_address.city`
 * `<ROOT ATTR>.user.billing_address.state`
 * `<ROOT ATTR>.user.billing_address.postcode`
 * `<ROOT ATTR>.user.billing_address.country_code`
 * `<ROOT ATTR>.tax_rate`
 * `<ROOT ATTR>.payment.provider`
 * `<ROOT ATTR>.payment.transaction_reference`
 * `<ROOT ATTR>.payment.payment_instrument.type`
 * `<ROOT ATTR>.payment.payment_instrument.name`
 * `<ROOT ATTR>.payment.payment_instrument.transaction_reference`
 *
 * Additionally, the `CheckoutOrder::setOrderItems` method takes an array of `\Serato\AppEvents\Event\CheckoutOrderItem`
 * objects and copies their data to:
 *
 * `<ROOT ATTR>.items`
 *
 *  The same method iterates over the array and calculates values for the following fields:
 *
 * `<ROOT ATTR>.amounts.base`
 * `<ROOT ATTR>.amounts.discounts`
 * `<ROOT ATTR>.amounts.tax`
 * `<ROOT ATTR>.amounts.total`
 */
class CheckoutOrder extends AbstractTimeSeriesEvent
{
    public const BRAINTREE = 'braintree';
    public const CREDITCARD = 'creditcard';
    public const PAYPAL_ACCOUNT = 'paypal_account';
    public const EVENT_CATEGORY = 'checkout';
    public const EVENT_ACTION = 'order_created';

    public function __construct()
    {
        parent::__construct();

        $this
            ->setEventCategory(self::EVENT_CATEGORY)
            ->setEventAction(self::EVENT_ACTION)
            # For now, the only supported payment gateway is Braintree
            ->setPaymentGateway(self::BRAINTREE)
            ->setData(self::ROOT_ATTR . '.amounts.base', 0)
            ->setData(self::ROOT_ATTR . '.amounts.discounts', 0)
            ->setData(self::ROOT_ATTR . '.amounts.tax', 0)
            ->setData(self::ROOT_ATTR . '.amounts.total', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'checkout_order';
    }

    /**
     * Sets the order ID.
     *
     * Sets the following field(s):
     *
     * `event.id`
     * `<ROOT ATTR>.id`
     *
     * @param string $id
     * @return self
     */
    public function setOrderId(string $id): self
    {
        return $this
            ->setEventId($id)
            ->setData(self::ROOT_ATTR . '.id', $id);
    }

    /**
     * Sets whether or not the order was created for a user interaction.
     *
     * Sets the following field(s):
     *
     * `event.provider`
     * `<ROOT ATTR>.interactive`
     *
     * @param string $i
     * @return self
     */
    public function setOrderInteractive(bool $i): self
    {
        return $this
            ->setEventProvider($i ? 'user' : 'webhook')
            ->setData(self::ROOT_ATTR . '.interactive', $i);
    }

    /**
     * Sets the cart id.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.cart_id`
     *
     * @param string $id
     * @return self
     */
    public function setCartId(string $id): self
    {
        return $this->setData(self::ROOT_ATTR . '.cart_id', $id);
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
     * `<ROOT ATTR>.user.id`
     *
     * @param string $id
     * @return self
     */
    public function setOrderUserId(string $id): self
    {
        return $this->setData(self::ROOT_ATTR . '.user.id', $id);
    }

    /**
     * Sets the order user email address.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.user.email_address`
     *
     * @param string $emailAddress
     * @return self
     */
    public function setUserEmailAddress(string $emailAddress): self
    {
        return $this->setData(self::ROOT_ATTR . '.user.email_address', $emailAddress);
    }

    /**
     * Sets the order user first name.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.user.first_name`
     *
     * @param string $firstName
     * @return self
     */
    public function setUserFirstName(string $firstName): self
    {
        return $this->setData(self::ROOT_ATTR . '.user.first_name', $firstName);
    }

    /**
     * Sets the order user last name.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.user.last_name`
     *
     * @param string $lastName
     * @return self
     */
    public function setUserLastName(string $lastName): self
    {
        return $this->setData(self::ROOT_ATTR . '.user.last_name', $lastName);
    }

    /**
     * Sets the order user billing address 1.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.user.billing_address.address_1`
     *
     * @param string $address1
     * @return self
     */
    public function setUserBillingAddress1(string $address1): self
    {
        return $this->setData(self::ROOT_ATTR . '.user.billing_address.address_1', $address1);
    }

    /**
     * Sets the order user billing address 2.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.user.billing_address.address_2`
     *
     * @param string $address2
     * @return self
     */
    public function setUserBillingAddress2(string $address2): self
    {
        return $this->setData(self::ROOT_ATTR . '.user.billing_address.address_2', $address2);
    }

    /**
     * Sets the order user billing address city.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.user.billing_address.city`
     *
     * @param string $city
     * @return self
     */
    public function setUserBillingAddressCity(string $city): self
    {
        return $this->setData(self::ROOT_ATTR . '.user.billing_address.city', $city);
    }

    /**
     * Sets the order user billing address state.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.user.billing_address.state`
     *
     * @param string $state
     * @return self
     */
    public function setUserBillingAddressState(string $state): self
    {
        return $this->setData(self::ROOT_ATTR . '.user.billing_address.state', $state);
    }

    /**
     * Sets the order user billing address postcode.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.user.billing_address.postcode`
     *
     * @param string $postcode
     * @return self
     */
    public function setUserBillingAddressPostcode(string $postcode): self
    {
        return $this->setData(self::ROOT_ATTR . '.user.billing_address.postcode', $postcode);
    }

    /**
     * Sets the order user billing address country code.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.user.billing_address.country_code`
     *
     * @param string $countryCode
     * @return self
     */
    public function setUserBillingAddressCountryCode(string $countryCode): self
    {
        return $this->setData(self::ROOT_ATTR . '.user.billing_address.country_code', $countryCode);
    }

    /**
     * Sets the order tax rate.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.tax_rate`
     *
     * @param string $rate
     * @return self
     */
    public function setTaxRate(float $rate): self
    {
        return $this->setData(self::ROOT_ATTR . '.tax_rate', $rate);
    }

    /**
     * Sets the order payment gateway.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.payment.gateway`
     *
     * @param string $gateway
     * @return self
     */
    public function setPaymentGateway(string $gateway): self
    {
        $this->validateDataValue($gateway, [self::BRAINTREE], __METHOD__);
        return $this->setData(self::ROOT_ATTR . '.payment.gateway', $gateway);
    }

    /**
     * Sets the order payment gateway transaction reference.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.payment.gateway_transaction_reference`
     *
     * @param string $ref
     * @return self
     */
    public function setPaymentGatewayTransactionReference(string $ref): self
    {
        return $this->setData(self::ROOT_ATTR . '.payment.gateway_transaction_reference', $ref);
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
        return $this->setData(self::ROOT_ATTR . '.payment.payment_instrument.type', $type);
    }

    public function setPaymentInstrumentName(string $name): self
    {
        return $this->setData(self::ROOT_ATTR . '.payment.payment_instrument.name', $name);
    }
    
    public function setPaymentInstrumentTransactionReference(string $ref): self
    {
        return $this->setData(self::ROOT_ATTR . '.payment.payment_instrument.transaction_reference', $ref);
    }

    /**
     * Adds an array of `Serato\AppEvents\Event\CheckoutOrderItem` objects to the
     * instance.
     *
     * Also uses the underlying item data to build up the total amounts for the instance.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.items`
     * `<ROOT ATTR>.amounts.base`
     * `<ROOT ATTR>.amounts.discounts`
     * `<ROOT ATTR>.amounts.tax`
     * `<ROOT ATTR>.amounts.total`
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
            if (!is_a($checkoutOrderItem, '\Serato\AppEvents\Event\CheckoutOrderItem')) {
                throw new Exception(
                    'Invalid argument. Items must all be \Serato\AppEvents\Event\CheckoutOrderItem instances'
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
            ->setData(self::ROOT_ATTR . '.items', $data)
            ->setData(self::ROOT_ATTR . '.amounts.base', $base)
            ->setData(self::ROOT_ATTR . '.amounts.discounts', $discounts)
            ->setData(self::ROOT_ATTR . '.amounts.tax', $tax)
            ->setData(self::ROOT_ATTR . '.amounts.total', $total);
    }

    /**
     * Adds an array of `Serato\AppEvents\Event\CheckoutOrderInvoice` objects to the
     * instance.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.invoices`
     *
     * @param array $orderInvoices
     * @return self
     */
    public function setOrderInvoices(array $orderInvoices): self
    {
        $data = [];
        foreach ($orderInvoices as $invoice) {
            if (!is_a($invoice, '\Serato\AppEvents\Event\CheckoutOrderInvoice')) {
                throw new Exception(
                    'Invalid argument. Items must all be \Serato\AppEvents\Event\CheckoutOrderInvoice instances'
                );
            }
            $data[] = $invoice->get();
        }
        return $this->setData(self::ROOT_ATTR . '.invoices', $data);
    }
}
