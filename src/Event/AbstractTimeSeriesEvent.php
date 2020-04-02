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
 * `event.action`
 * `event.start`
 * `event.end`
 * `event.outcome`
 */
abstract class AbstractTimeSeriesEvent extends AbstractEventDataContainer
{
    # The path to the root element that contains Serato-specific event data
    private const ROOT_EVENT_ATTR = 'serato.event_data';

    public const SUCCESS = 'success';
    public const FAILURE = 'failure';
    public const UNKNOWN = 'unknown';

    public function __construct()
    {
        parent::__construct();
        $this->setEventStart(new DateTime);
        $this->setEventAction([$this->getEventActionCategory(), $this->getEventActionName()]);
    }

    /**
     * Logs the event to the specified log target
     *
     * @param AbstractEventTarget $target
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
     * @return mixed
     */
    public function setUserId(string $userId)
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
     * @return mixed
     */
    public function setHttpReferrer(string $referrer)
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
     * @return mixed
     */
    public function setClientIp(string $ip)
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
     * @return mixed
     */
    public function setUserAgent(string $ua)
    {
        return $this->setData('user_agent.original', $ua);
    }

    /**
     * Sets the unique event ID.
     *
     * Note: This value also becomes the document ID when injested into Elasticsearch.
     *
     * Sets the following field(s):
     *
     * `event.id`
     *
     * @param string $id
     * @return mixed
     */
    public function setEventId(string $id)
    {
        return $this->setData('event.id', $id);
    }

    /**
     * Returns the event id
     *
     * @return string|null
     */
    public function getEventId(): ?string
    {
        return $this->getData('event.id') === null ? null : (string)$this->getData('event.id');
    }

    /**
     * Sets the event action
     *
     * Sets the following field(s):
     *
     * `event.action`
     *
     * @param array $action
     * @return mixed
     */
    protected function setEventAction(array $action)
    {
        return $this->setData('event.action', $action);
    }

    /**
     * Returns the event action
     *
     * @return null|array
     */
    public function getEventAction(): ?array
    {
        return $this->getData('event.action');
    }

    /**
     * Sets the event start time
     *
     * Sets the following field(s):
     *
     * `event.start`
     *
     * @param DateTime $dt
     * @return mixed
     */
    public function setEventStart(DateTime $dt)
    {
        return $this->setData('event.start', $dt->format(DateTime::ATOM));
    }

    /**
     * Returns the event start
     *
     * @return DateTime|null
     */
    public function getEventStart(): ?DateTime
    {
        return $this->getData('event.start') === null ? null : new DateTime($this->getData('event.start'));
    }

    /**
     * Sets the event end time
     *
     * Sets the following field(s):
     *
     * `event.end`
     *
     * @param DateTime $dt
     * @return mixed
     */
    public function setEventEnd(DateTime $dt)
    {
        return $this->setData('event.end', $dt->format(DateTime::ATOM));
    }

    /**
     * Returns the event end
     *
     * @return DateTime|null
     */
    public function getEventEnd(): ?DateTime
    {
        return $this->getData('event.end') === null ? null : new DateTime($this->getData('event.end'));
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
     * @return mixed
     */
    public function setEventOutcome(string $outcome)
    {
        $this->validateDataValue($outcome, [self::SUCCESS, self::FAILURE, self::UNKNOWN], __METHOD__);
        return $this->setData('event.outcome', $outcome);
    }

    /**
     * Returns the event outcome
     *
     * @return string|null
     */
    public function getEventOutcome(): ?string
    {
        return $this->getData('event.outcome') === null ? null : (string)$this->getData('event.outcome');
    }

    /**
     * Returns data for a specified path under the root event attribute
     *
     * @param null|string $path
     * @return null|mixed
     */
    public function getEventRootData(?string $path = null)
    {
        # FIXME because this is dumb
        if ($path === null) {
            $data = $this->get();
            foreach (explode('.', self::ROOT_EVENT_ATTR) as $i) {
                $data = $data[$i];
            }
            return $data;
        } else {
            return $this->getData(self::ROOT_EVENT_ATTR . '.' . $path);
        }
    }

    /**
     * Sets data under the root event attribute
     *
     * @param string $path
     * @param mixed $item
     * @return mixed
     */
    protected function setEventRootAttributeData(string $path, $item)
    {
        return $this->setData(self::ROOT_EVENT_ATTR . '.' . $path, $item);
    }
}
