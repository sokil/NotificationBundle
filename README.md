# Notification Bundle

[![Build Status](https://travis-ci.org/sokil/NotificationBundle.svg?branch=master)](https://travis-ci.org/sokil/NotificationBundle)

## Installation

Use composer to install dependency:

```
composer.phar require sokil/notification-bundle
```

## Creating messages

### Message builder

First we need to create message builder, which optain some dependencies from container and build message instances.
It must extend `\Sokil\NotificationBundle\MessageBuilder\AbstractBuilder` class:

```php
<?php

namespace Acme\Notification\Message;

use \Sokil\NotificationBundle\MessageBuilder\AbstractBuilder;

class SomeMessageBuilder extends AbstractBuilder
{
    
}

```

This builder must be registered as service in container and tagged by `notification.message_builder`:

```yaml
acme.notification.message_builder.some:
    class: Acme\Notification\Message\SomeMessageBuilder
    tags:
        - {name: 'notification.message_builder', messageType: 'someMessage', transport: 'email'}
```

This service will build messages with type `someMessages` for transport `email`. One
message may be used for different transports. In this case just add another tag:

```yaml
acme.notification.message_builder.some:
    class: Acme\Notification\Message\SomeMessageBuilder
    tags:
        - {name: 'notification.message_builder', messageType: 'someMessage', transport: 'email'}
        - {name: 'notification.message_builder', messageType: 'someMessage', transport: 'sms'}
```

### Message builder collection

Collection holds number of different messages. Collection used to group 
messages. It must extends class `Sokil\NotificationBundle\MessageBuilder\BuilderCollection`.
To register new collection, define new service:

```yaml
acme.notification.message_builder_collection.some:
    class: Sokil\NotificationBundle\MessageBuilder\BuilderCollection
    tags:
      - {name: 'notification.message_builder_collection', collectionName: 'some'}
```

There is already collection with name `default`, defined as service `notification.message_builder_collection`.

To add message builder to collection, set `collectionName` attribute of builder's `notification.message_builder` tag:

```yaml
acme.notification.message_builder.some:
    class: Acme\Notification\Message\SomeMessageBuilder
    tags:
        - {name: 'notification.message_builder', messageType: 'someMessage', transport: 'email', collectionName, 'some'}
        - {name: 'notification.message_builder', messageType: 'someMessage', transport: 'sms'}
```

If `collectionName` not specified, builder registered in `default` collection.

To get builder from collection:

```php
<?php
$someSmsMessageBuilder = $container
    ->get('acme.notification.message_builder_collection.some')
    ->getBuilder('someMessage', 'email');
```

## Schema of notification

## Available transports

## Configuring custom transport

## Preview

To enable preview, add routing to your `./app/config/routing.yml`:

```yaml
notification:
    resource: "@NotificationBundle/Resources/config/routing.yml"
    prefix: /notification
```

Now preview of mails available at route `/notification/preview`. 
To access this route, you reed to have `ROLE_NOTIFICATION_MAIL_PREVIEW`.
