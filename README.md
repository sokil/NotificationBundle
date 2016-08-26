# Notification Bundle

[![Build Status](https://travis-ci.org/sokil/NotificationBundle.svg?branch=master)](https://travis-ci.org/sokil/NotificationBundle)

## Installation

Use composer to install dependency:

```
composer.phar require sokil/notification-bundle
```

## Basic usage

## Schema of notification

## Available transports

## Configuring custom transport

## Configuring message type

## Preview

To enable preview, add routing to your `./app/config/routing.yml`:

```yaml
notification:
    resource: "@NotificationBundle/Resources/config/routing.yml"
    prefix: /notification
```

Now preview of mails available at route `/notification/preview`. 
To access this route, you reed to have `ROLE_NOTIFICATION_MAIL_PREVIEW`.
