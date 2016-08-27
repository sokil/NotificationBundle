<?php

namespace Sokil\NotificationBundle\Stub\Message;

use Sokil\NotificationBundle\Message\EmailMessageInterface;

class SomeMessage implements EmailMessageInterface
{
    public function getSubject()
    {
        return 'some subject';
    }

    public function getBody()
    {
        return 'some body';
    }
}