<?php

namespace Sokil\NotificationBundle\Stub\Message;

use Sokil\NotificationBundle\Message\MessageFixtureInterface;
use Sokil\NotificationBundle\Message\MessageInterface;

class SomeMessageFixture implements MessageFixtureInterface
{
    public function apply(MessageInterface $message)
    {

    }
}