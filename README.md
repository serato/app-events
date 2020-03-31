# Application Events

A PHP library for delivering application event data to external consumers.

## Installation

To include this library in a PHP project add the following line to the project's `composer.json` file
in the `require` section:

```json
{
  "require": {
    "serato/app-events": "^1.0.0"
  }
}
```
See [Packagist](https://packagist.org/packages/serato/app-events) for a list of all available versions.

## Events

Each instrumented event is encapsulated in a model containing set methods for populating the event data.
The currently implemented events are:

* [Checkout Order completion](./src/Event/Checkout)
* [Digital Asset download](./src/Event/DigitalAsset)
* [Sera.to redirects](./src/Event/SeraTo)
* [Software license authorization](./src/Event/SoftwareLicense)

### Basic usage

Simply create an event model instance and populate it:

```php
use Serato\AppEvents\Event\SeraTo\Redirect;

$event = new Redirect;
$event
    ->setHttpReferrer('http://serato.com/dj')
    ->setClientIp('24.30.52.126')
    ->setUserAgent(
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 ' .
        '(KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36'
    )
    ->setRedirectId('id-123')
    ->setRedirectName('Manage Subscription')
    ->setRedirectGroup('Serato DJ (app)')
    ->setRedirectShortUrl('sera.to/-b6ap')
    ->setRedirectDestinationUrl('https://account.serato.com/#/subscriptions');
```

# Event target

Event targets are destinations for event data. Currently only Elasticsearch is supported (via Filebeat).

### Basic usage

Create the event target instance and pass it an event instance via the `sendEvent` method.

```php
use Serato\AppEvents\EventTarget\Filebeat;
use Serato\AppEvents\Event\SeraTo\Redirect;

$event = new Redirect;
# Populate $event...

# Create the event target instance.
# Application name is important. ALL events will be tagged with the application name.
$eventTarget = new Filebeat('My App Name', '/path/to/log/file.log');
# Send the event to the target
$eventTarget->sendEvent($event);
```
