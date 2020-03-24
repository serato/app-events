<?php
declare(strict_types=1);

namespace Serato\AppEvents\Event;

/**
 * ** CheckoutOrderItemDiscount **
 *
 * Captures data attributes related to a checkout order item discount.
 *
 * CheckoutOrderItemDiscount instances must be added to a CheckoutOrderItem instance
 * if they are intended to be sent to a logging backend.
 *
 * Sets the following fields:
 *
 * `id`
 * `name`
 * `promo_code`
 * `discount_percentage`
 * `discount_fixed_amount`
 */
class CheckoutOrderItemPromotion extends AbstractEventData
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'checkout_order_item_promotion';
    }

    /**
     * Sets the promotion ID.
     *
     * Sets the following field(s):
     *
     * `id`
     *
     * @param string $id
     * @return self
     */
    public function setPromotionId(string $id): self
    {
        return $this->setData('id', $id);
    }

    /**
     * Sets the promotion name.
     *
     * Sets the following field(s):
     *
     * `name`
     *
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        return $this->setData('name', $name);
    }

    /**
     * Sets the promotion code.
     *
     * Sets the following field(s):
     *
     * `promo_code`
     *
     * @param string $code
     * @return self
     */
    public function setPromotionCode(string $code): self
    {
        return $this->setData('promo_code', $code);
    }

    /**
     * Sets the percentage discount provided by the promotion.
     *
     * Sets the following field(s):
     *
     * `discount_percentage`
     *
     * @param float $val
     * @return self
     */
    public function setDiscountPercentage(float $val): self
    {
        return $this->setData('discount_percentage', $val);
    }

    /**
     * Sets the fixed amount discount provided by the promotion.
     *
     * Sets the following field(s):
     *
     * `discount_fixed_amount`
     *
     * @param float $val
     * @return self
     */
    public function setDiscountFixedAmount(float $val): self
    {
        return $this->setData('discount_fixed_amount', $val);
    }
}
