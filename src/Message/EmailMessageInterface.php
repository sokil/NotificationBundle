<?php

namespace Sokil\NotificationBundle\Message;

interface EmailMessageInterface extends MessageInterface
{
    public function getSubject();
}