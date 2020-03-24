<?php
declare(strict_types=1);

namespace Serato\AppEvents\Event\Checkout;

use Serato\AppEvents\Event\AbstractTimeSeriesEvent;

/**
 * ** AbstractTimeSeriesEvent **
 *
 * Abstract class that all Checkout time series events inherit from.
 */
abstract class AbstractTimeSeriesCheckoutEvent extends AbstractTimeSeriesEvent
{
    /**
     * {@inheritDoc}
     */
    public function getEventCategory(): string
    {
        return 'checkout';
    }
}
