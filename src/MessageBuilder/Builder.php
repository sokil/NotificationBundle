<?php

namespace Sokil\NotificationBundle\MessageBuilder;

use Sokil\NotificationBundle\Exception\NotificationException;
use Sokil\NotificationBundle\Message\MessageInterface;

class Builder extends AbstractBuilder
{
    private $className;

    public function __construct($className)
    {
        $this->className = $className;
    }

    public function createMessage()
    {
        $message = new $this->className;
        if (!($message instanceof MessageInterface)) {
            throw new NotificationException(sprintf('Message class %s must implement MessageInterface', $this->className));
        }

        return $message;
    }
}