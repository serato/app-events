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
 * `id`
 * `number`
 * `debtor_code`
 *
 * Additionally, the `OrderInvoice::setInvoiceItems` method takes an array of
 * `\Serato\AppEvents\Event\Checkout\OrderItem` objects and copies their data to:
 *
 * `items`
 *
 *  The same method iterates over the array and calculates values for the following fields:
 *
 * `amounts.base`
 * `amounts.discounts`
 * `amounts.tax`
 * `amounts.total`
 */
class OrderInvoice extends AbstractTimeSeriesCheckoutEvent
{
    public const WEBC001 = 'WEBC001';
    public const WEBC003 = 'WEBC003';
    public const WEBC004 = 'WEBC004';

    /**
     * {@inheritDoc}
     */
    public function getEventActionName(): string
    {
        return 'invoice_created';
    }

    /**
     * Sets the invoice ID.
     *
     * Sets the following field(s):
     *
     * `event.id`
     * `id`
     *
     * @param string $id
     * @return self
     */
    public function setId(string $id): self
    {
        return $this
            ->setEventId($id)
            ->setData('id', $id);
    }

    /**
     * Sets the invoice number.
     *
     * Sets the following field(s):
     *
     * `number`
     *
     * @param string $number
     * @return self
     */
    public function setNumber(string $number): self
    {
        return $this->setData('number', $number);
    }

    /**
     * Sets the debtor code.
     *
     * Sets the following field(s):
     *
     * `debtor_code`
     *
     * @param string $code
     * @return self
     */
    public function setDebtorCode(string $code): self
    {
        $this->validateDataValue($code, [self::WEBC001, self::WEBC003, self::WEBC004], __METHOD__);
        return $this->setData('debtor_code', $code);
    }

    /**
     * Adds an array of `Serato\AppEvents\Event\Checkout\OrderItem` objects to the
     * instance.
     *
     * Also uses the underlying item data to build up the total amounts for the instance.
     *
     * Sets the following field(s):
     *
     * `items`
     * `amounts.base`
     * `amounts.discounts`
     * `amounts.tax`
     * `amounts.total`
     *
     * @param array $items
     * @return self
     */
    public function setInvoiceItems(array $items): self
    {
        $data = [];
        $base = 0;
        $discounts = 0;
        $tax = 0;
        $total = 0;

        foreach ($items as $checkoutOrderItem) {
            if (!is_a($checkoutOrderItem, '\Serato\AppEvents\Event\Checkout\OrderItem')) {
                throw new Exception(
                    'Invalid argument. Items must all be \Serato\AppEvents\Event\Checkout\OrderItem instances'
                );
            }

            $item = $checkoutOrderItem->get();
            # Remove some redundant/meaningless data
            unset($item['id']);
            if (isset($item['promotions'])) {
                unset($item['promotions']);
            }
            if (isset($item['subscription_id'])) {
                unset($item['subscription_id']);
            }
            if (isset($item['sku']['license_term'])) {
                unset($item['sku']['license_term']);
            }

            $data[] = $item;

            $taxRate = $checkoutOrderItem->getTaxRate();
            $qty = $checkoutOrderItem->getQuantity();

            # Reminder: base amount for orders includes applied discounts
            $base       += ($checkoutOrderItem->getAmountsBase() - $checkoutOrderItem->getAmountsDiscounts()) * $qty;
            $discounts  += $checkoutOrderItem->getAmountsDiscounts() * $qty;
            $tax        += $checkoutOrderItem->getAmountsTax() * $qty;
            $total      += $checkoutOrderItem->getAmountsTotal() * $qty;
        }

        return $this
            ->setData('items', $data)
            ->setData('amounts.base', $base)
            ->setData('amounts.discounts', $discounts)
            ->setData('amounts.tax', $tax)
            ->setData('amounts.total', $total);
    }
}
