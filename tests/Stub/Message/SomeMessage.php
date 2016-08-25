<?php

namespace Sokil\NotificationBundle\Stub\Message;

use \Sokil\NotificationBundle\Message\MessageInterface;

class SomeMessage implements MessageInterface
{
    public function getBody()
    {
        return 'some body';
    }
}