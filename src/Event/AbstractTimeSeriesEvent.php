<?php
declare(strict_types=1);

namespace Serato\AppEvents\Event;

use DateTime;
use Serato\AppEvents\EventTarget\AbstractEventTarget;

/**
 * ** AbstractTimeSeriesEvent **
 *
 * Abstract class that all time series events inherit from.
 *
 * Includes setters for commonly used fields.
 *
 * Sets the following fields:
 *
 * `labels.application`
 * `labels.env`
 * `client.user.id`
 * `http.request.referrer`
 * `user_agent.original`
 * `event.id`
 * `event.category`
 * `event.action`
 * `event.start`
 * `event.end`
 * `event.outcome`
 */
abstract class AbstractTimeSeriesEvent extends AbstractEventData
{
    protected const ROOT_ATTR = 'serato_event';

    public const SUCCESS = 'success';
    public const FAILURE = 'failure';
    public const INCOMPLETE = 'incomplete';

    public function __construct()
    {
        $this->setEventStart(new DateTime);
    }

    /**
     * Logs the event to the specified log target
     *
     * @param AbstractEventTarget $logger
     * @return void
     */
    public function send(AbstractEventTarget $target): void
    {
        $target->sendEvent($this);
    }

    /**
     * Sets the user ID
     *
     * Sets the following field(s):
     *
     * `client.user.id`
     *
     * @param string $userId
     * @return self
     */
    public function setUserId(string $userId): self
    {
        return $this->setData('client.user.id', $userId);
    }

    /**
     * Sets the HTTP referrer
     *
     * Sets the following field(s):
     *
     * `http.request.referrer`
     *
     * @param string $referrer
     * @return self
     */
    public function setHttpReferrer(string $referrer): self
    {
        return $this->setData('http.request.referrer', $referrer);
    }

    /**
     * Sets the client IP address
     *
     * Sets the following field(s):
     *
     * `client.ip`
     *
     * @param string $ip
     * @return self
     */
    public function setClientIp(string $ip): self
    {
        return $this->setData('client.ip', $ip);
    }

    /**
     * Sets the User Agent
     *
     * Sets the following field(s):
     *
     * `user_agent.original`
     *
     * @param string $ua
     * @return self
     */
    public function setUserAgent(string $ua): self
    {
        return $this->setData('user_agent.original', $ua);
    }

    /**
     * Sets the unique event ID.
     *
     * Note: This value also becomes the documented ID when injested into Elasticsearch.
     *
     * Sets the following field(s):
     *
     * `event.id`
     *
     * @param string $id
     * @return self
     */
    public function setEventId(string $id): self
    {
        return $this->setData('event.id', $id);
    }

    /**
     * Sets the event category.
     *
     * Sets the following field(s):
     *
     * `event.category`
     *
     * @param string $category
     * @return self
     */
    public function setEventCategory(string $category): self
    {
        return $this->setData('event.category', $category);
    }

    /**
     * Sets the event action
     *
     * Sets the following field(s):
     *
     * `event.action`
     *
     * @param string $action
     * @return self
     */
    public function setEventAction(string $action): self
    {
        return $this->setData('event.action', $action);
    }

    /**
     * Sets the event start time
     *
     * Sets the following field(s):
     *
     * `event.start`
     *
     * @param DateTime $dt
     * @return self
     */
    public function setEventStart(DateTime $dt): self
    {
        return $this->setData('event.start', $dt->format(DateTime::ATOM));
    }

    /**
     * Sets the event end time
     *
     * Sets the following field(s):
     *
     * `event.end`
     *
     * @param DateTime $dt
     * @return self
     */
    public function setEventEnd(DateTime $dt): self
    {
        return $this->setData('event.end', $dt->format(DateTime::ATOM));
    }

    /**
     * Sets the event outcome.
     *
     * Valid values are defined as class constants `self::SUCCESS`, `self::FAILURE` and `self::INCOMPLETE`
     *
     * Sets the following field(s):
     *
     * `event.outcome`
     *
     * @param string $outcome
     * @return self
     */
    public function setEventOutcome(string $outcome): self
    {
        $this->validateDataValue($outcome, [self::SUCCESS, self::FAILURE, self::INCOMPLETE], __METHOD__);
        return $this->setData('event.outcome', $outcome);
    }

    /**
     * Sets the event provider.
     *
     * Sets the following field(s):
     *
     * `event.provider`
     *
     * @param string $provider
     * @return self
     */
    public function setEventProvider(string $provider): self
    {
        return $this->setData('event.provider', $provider);
    }
}