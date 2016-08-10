<?php

namespace Sokil\NotificationBundle\Message;

use Sokil\NotificationBundle\Message\MessageInterface;

interface MessageFixtureInterface
{
    public function apply(MessageInterface $message);
}