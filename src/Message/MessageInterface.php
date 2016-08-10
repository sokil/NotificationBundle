<?php

namespace Sokil\NotificationBundle\Message;

interface MessageInterface
{
    public function getSubject();

    public function getBody();
}