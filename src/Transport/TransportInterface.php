<?php

namespace Sokil\NotificationBundle\Transport;

use Sokil\NotificationBundle\Message\MessageInterface;
use Sokil\TaskStockBundle\Provider\TaskNotificationTransportProvider\Task;
use Sokil\TaskStockBundle\Provider\TaskNotificationTransportProvider\User;

interface TransportInterface
{
    /**
     * @param Task $task
     * @param User $user
     * @param array $changes array of \Sokil\TaskStockBundle\Dto\ChangedValue instances
     * @param array $recipients
     * @return mixed
     */
    public function send(
        MessageInterface $message,
        array $recipients
    );
}