<?php
declare(strict_types=1);

namespace Serato\AppEvents\Event\Checkout;

use Exception;
use DateTime;

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
 * `<ROOT ATTR>.user.organization_name`
 * `<ROOT ATTR>.user.first_name`
 * `<ROOT ATTR>.user.last_name`
 * `<ROOT ATTR>.user.billing_address.address_1`
 * `<ROOT ATTR>.user.billing_address.address_2`
 * `<ROOT ATTR>.user.billing_address.city_name`
 * `<ROOT ATTR>.user.billing_address.region_name`
 * `<ROOT ATTR>.user.billing_address.postcode`
 * `<ROOT ATTR>.user.billing_address.country_iso_code`
 * `<ROOT ATTR>.tax_rate`
 *
 * The `CheckoutOrder::setOrderItems` method takes an array of
 * `\Serato\AppEvents\Event\Checkout\OrderItem` objects and copies their data to:
 *
 * `<ROOT ATTR>.items`
 *
 *  The same method iterates over the array and calculates values for the following fields:
 *
 * `<ROOT ATTR>.amounts.base`
 * `<ROOT ATTR>.amounts.discounts`
 * `<ROOT ATTR>.amounts.tax`
 * `<ROOT ATTR>.amounts.total`
 *
 * The `Checkout::setOrderInvoices` method takes an array of
 * `\Serato\AppEvents\Event\Checkout\OrderInvoice` objects and copies their data to:
 *
 * `<ROOT ATTR>.invoices`
 */
class Order extends AbstractTimeSeriesCheckoutEvent
{
    public function __construct()
    {
        parent::__construct();
        $this
            ->setEventRootAttributeData('amounts.base', 0)
            ->setEventRootAttributeData('amounts.discounts', 0)
            ->setEventRootAttributeData('amounts.tax', 0)
            ->setEventRootAttributeData('amounts.total', 0);
    }

    /**
     * {@inheritDoc}
     */
    public function getEventActionName(): string
    {
        return 'order_created';
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
            ->setEventRootAttributeData('id', $id);
    }

    /**
     * Sets whether or not the order was created for a user interaction.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.interactive`
     *
     * @param bool $i
     * @return self
     */
    public function setOrderInteractive(bool $i): self
    {
        return $this->setEventRootAttributeData('interactive', $i);
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
        return $this->setEventRootAttributeData('cart_id', $id);
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
        return $this->setEventRootAttributeData('user.id', $id);
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
        return $this->setEventRootAttributeData('user.email_address', $emailAddress);
    }

    /**
     * Sets the order user organization name.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.user.organization_name`
     *
     * @param string $name
     * @return self
     */
    public function setUserOrganizationName(string $name): self
    {
        return $this->setEventRootAttributeData('user.organization_name', $name);
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
        return $this->setEventRootAttributeData('user.first_name', $firstName);
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
        return $this->setEventRootAttributeData('user.last_name', $lastName);
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
        return $this->setEventRootAttributeData('user.billing_address.address_1', $address1);
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
        return $this->setEventRootAttributeData('user.billing_address.address_2', $address2);
    }

    /**
     * Sets the order user billing address city.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.user.billing_address.city_name`
     *
     * @param string $city
     * @return self
     */
    public function setUserBillingAddressCity(string $city): self
    {
        return $this->setEventRootAttributeData('user.billing_address.city_name', $city);
    }

    /**
     * Sets the order user billing address region_name.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.user.billing_address.region_name`
     *
     * @param string $region
     * @return self
     */
    public function setUserBillingAddressRegion(string $region): self
    {
        return $this->setEventRootAttributeData('user.billing_address.region_name', $region);
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
        return $this->setEventRootAttributeData('user.billing_address.postcode', $postcode);
    }

    /**
     * Sets the order user billing address country code.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.user.billing_address.country_iso_code`
     *
     * @param string $countryCode
     * @return self
     */
    public function setUserBillingAddressCountryCode(string $countryCode): self
    {
        return $this->setEventRootAttributeData('user.billing_address.country_iso_code', $countryCode);
    }

    /**
     * Sets the order tax rate.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.tax_rate`
     *
     * @param float $rate
     * @return self
     */
    public function setTaxRate(float $rate): self
    {
        return $this->setEventRootAttributeData('tax_rate', $rate);
    }

    /**
     * Adds an array of `Serato\AppEvents\Event\Checkout\OrderItem` objects to the
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
            ->setEventRootAttributeData('items', $data)
            ->setEventRootAttributeData('amounts.base', $base)
            ->setEventRootAttributeData('amounts.discounts', $discounts)
            ->setEventRootAttributeData('amounts.tax', $tax)
            ->setEventRootAttributeData('amounts.total', $total);
    }

    /**
     * Adds an array of `Serato\AppEvents\Event\Checkout\OrderInvoice` objects to the
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
            if (!is_a($invoice, '\Serato\AppEvents\Event\Checkout\OrderInvoice')) {
                throw new Exception(
                    'Invalid argument. Items must all be \Serato\AppEvents\Event\Checkout\OrderInvoice instances'
                );
            }
            $data[] = array_merge(
                $invoice->getEventRootData(),
                $invoice->getEventStart() === null ? [] :
                    ['created_at' => $invoice->getEventStart()->format(DateTime::ATOM)]
            );
        }
        return $this->setEventRootAttributeData('invoices', $data);
    }
}
