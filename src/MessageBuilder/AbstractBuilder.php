<?php

namespace Sokil\NotificationBundle\MessageBuilder;

use Sokil\NotificationBundle\Exception\InvalidArgumentException;
use Sokil\NotificationBundle\Message\MessageInterface;
use Sokil\NotificationBundle\Message\MessageFixtureInterface;

abstract class AbstractBuilder
{
    /**
     * @param string $type
     * @return MessageInterface
     * @throws \Exception
     */
    abstract public function createMessage();
}