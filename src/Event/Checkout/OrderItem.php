<?php

declare(strict_types=1);

namespace Serato\AppEvents\Event\Checkout;

use Serato\AppEvents\Event\AbstractDataContainer;
use Exception;

/**
 * ** OrderItem **
 *
 * Captures data attributes related to a checkout order item.
 *
 * OrderItem instances must be added to an Order instance
 * if they are intended to be sent to a logging backend.
 *
 * Sets the following fields:
 *
 * `id`
 * `quantity`
 * `tax_code`
 * `tax_rate`
 * `amounts.base`
 * `amounts.discounts`
 * `amounts.tax`
 * `amounts.total`
 * `sku.id`
 * `sku.sku`
 * `sku.name`
 * `sku.license_term`
 * `subscription_id`
 *
 * Additionally, the `OrderItem::setPromotions` method takes an array of
 * `\Serato\AppEvents\Event\Checkout\OrderItemPromotion` objects and copies their data to:
 *
 * `promotions`
 */
class OrderItem extends AbstractDataContainer
{
    public const TAXCODE_Z = 'Z';
    public const TAXCODE_V = 'V';

    public const PERMANENT = 'permanent';
    public const SUBSCRIPTION = 'subscription';
    public const TIMELIMITED = 'timelimited';

    public function __construct()
    {
        $this
            ->setQuantity(1)
            ->setTaxRate(0)
            ->setAmountsBase(0)
            ->setAmountsDiscounts(0)
            ->setAmountsTax(0)
            ->setAmountsTotal(0);
    }

    /**
     * Sets the order item ID.
     *
     * Sets the following field(s):
     *
     * `id`
     *
     * @param string $id
     * @return self
     */
    public function setOrderItemId(string $id): self
    {
        return $this->setData('id', $id);
    }

    /**
     * Sets the order item SKU ID.
     *
     * Note: this is commonly referred to as product type ID in other systems.
     *
     * Sets the following field(s):
     *
     * `sku.id`
     *
     * @param string $id
     * @return self
     */
    public function setSkuId(string $id): self
    {
        return $this->setData('sku.id', $id);
    }

    /**
     * Sets the order item SKU code.
     *
     * Sets the following field(s):
     *
     * `sku.sku`
     *
     * @param string $code
     * @return self
     */
    public function setSkuCode(string $code): self
    {
        return $this->setData('sku.sku', $code);
    }

    /**
     * Sets the order item SKU name.
     *
     * Sets the following field(s):
     *
     * `sku.name`
     *
     * @param string $name
     * @return self
     */
    public function setSkuName(string $name): self
    {
        return $this->setData('sku.name', $name);
    }

    /**
     * Sets the order item SKU license term.
     *
     * Sets the following field(s):
     *
     * `sku.license_term`
     *
     * @param string $term
     * @return self
     */
    public function setSkuLicenseTerm(string $term): self
    {
        $this->validateDataValue($term, [self::PERMANENT, self::SUBSCRIPTION, self::TIMELIMITED], __METHOD__);
        return $this->setData('sku.license_term', $term);
    }

    /**
     * Sets the order item subscription ID.
     *
     * This is the ID of subscription to which the order item is related.
     *
     * Sets the following field(s):
     *
     * `subscription_id`
     *
     * @param string $id
     * @return self
     */
    public function setSubscriptionId(string $id): self
    {
        return $this->setData('subscription_id', $id);
    }

    /**
     * Sets the order item quantity.
     *
     * Sets the following field(s):
     *
     * `quantity`
     *
     * @param int $qty
     * @return self
     */
    public function setQuantity(int $qty): self
    {
        return $this->setData('quantity', $qty);
    }

    /**
     * Returns the order item quantity.
     *
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->getData('quantity') === null ? null : (int)$this->getData('quantity');
    }

    /**
     * Sets the tax code.
     *
     * Sets the following field(s):
     *
     * `tax_code`
     *
     * @param string $code
     * @return self
     */
    public function setTaxCode(string $code): self
    {
        $this->validateDataValue($code, [self::TAXCODE_Z, self::TAXCODE_V], __METHOD__);
        return $this->setData('tax_code', $code);
    }

    /**
     * Sets the order item tax rate.
     *
     * Sets the following field(s):
     *
     * `tax_rate`
     *
     * @param float $rate
     * @return self
     */
    public function setTaxRate(float $rate): self
    {
        return $this->setData('tax_rate', $rate);
    }

    /**
     * Returns the order item tax rate.
     *
     * @return float|null
     */
    public function getTaxRate(): ?float
    {
        return $this->getData('tax_rate') === null ? null : (float)$this->getData('tax_rate');
    }

    /**
     * Sets the base amount of a single SKU within the order item line.
     *
     * Sets the following field(s):
     *
     * `amounts.base`
     *
     * @param float $amount
     * @return self
     */
    public function setAmountsBase(float $amount): self
    {
        return $this->setData('amounts.base', $amount);
    }

    /**
     * Returns the base amount of a single SKU within the order item line.
     *
     * @return float|null
     */
    public function getAmountsBase(): ?float
    {
        return $this->getData('amounts.base') === null ? null : (float)$this->getData('amounts.base');
    }

    /**
     * Sets the amount of all discounts applied to a single SKU within the order item line.
     *
     * Sets the following field(s):
     *
     * `amounts.discounts`
     *
     * @param float $amount
     * @return self
     */
    public function setAmountsDiscounts(float $amount): self
    {
        return $this->setData('amounts.discounts', $amount);
    }

    /**
     * Returns the amount of all discounts applied to a single SKU within the order item line.
     *
     * @return float|null
     */
    public function getAmountsDiscounts(): ?float
    {
        return $this->getData('amounts.discounts') === null ? null : (float)$this->getData('amounts.discounts');
    }

    /**
     * Sets the tax amount of a single SKU within the order item line.
     *
     * Sets the following field(s):
     *
     * `amounts.tax`
     *
     * @param float $amount
     * @return self
     */
    public function setAmountsTax(float $amount): self
    {
        return $this->setData('amounts.tax', $amount);
    }

    /**
     * Returns the tax amount of a single SKU within the order item line.
     *
     * @return float|null
     */
    public function getAmountsTax(): ?float
    {
        return $this->getData('amounts.tax') === null ? null : (float)$this->getData('amounts.tax');
    }

    /**
     * Sets the total amount of a single SKU within the order item line.
     *
     * Sets the following field(s):
     *
     * `amounts.total`
     *
     * @param float $amount
     * @return self
     */
    public function setAmountsTotal(float $amount): self
    {
        return $this->setData('amounts.total', $amount);
    }

    /**
     * Returns the total amount of a single SKU within the order item line.
     *
     * @return float|null
     */
    public function getAmountsTotal(): ?float
    {
        return $this->getData('amounts.total') === null ? null : (float)$this->getData('amounts.total');
    }

    /**
     * Adds an array of `Serato\AppEvents\Event\Checkout\OrderItemPromotion` objects to the
     * instance.
     *
     * Sets the following field(s):
     *
     * `promotions`
     *
     * @param array $promotions
     * @return self
     * @throws Exception
     */
    public function setPromotions(array $promotions): self
    {
        $items = [];
        foreach ($promotions as $checkoutOrderItemPromotion) {
            if (!is_a($checkoutOrderItemPromotion, '\Serato\AppEvents\Event\Checkout\OrderItemPromotion')) {
                throw new Exception(
                    'Invalid argument. Items must all be \Serato\AppEvents\Event\Checkout\OrderItemPromotion instances'
                );
            }
            $items[] = $checkoutOrderItemPromotion->get();
        }
        return $this->setData('promotions', $items);
    }
}
