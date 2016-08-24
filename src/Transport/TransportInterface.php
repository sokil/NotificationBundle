<?php

namespace Sokil\NotificationBundle\Transport;

use Sokil\NotificationBundle\Message\MessageInterface;
use Sokil\TaskStockBundle\Provider\TaskNotificationTransportProvider\Task;
use Sokil\TaskStockBundle\Provider\TaskNotificationTransportProvider\User;

interface TransportInterface
{
    /**
     * @param MessageInterface $message
     * @param array $recipients
     * @return mixed
     */
    public function send(
        MessageInterface $message,
        array $recipients
    );
}
