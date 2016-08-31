<?php

namespace Sokil\NotificationBundle\MessageBuilder;

use Sokil\NotificationBundle\Message\MessageInterface;

abstract class AbstractFixtureBuilder
{
    /**
     * @var AbstractBuilder
     */
    private $messageBuilder;

    public function __construct(AbstractBuilder $messageBuilder)
    {
        $this->messageBuilder = $messageBuilder;
    }

    /**
     * @return MessageInterface
     * @throws \Exception
     */
    abstract public function createFixture();
}