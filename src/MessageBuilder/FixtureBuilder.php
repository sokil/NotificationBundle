<?php

namespace Sokil\NotificationBundle\MessageBuilder;

use Sokil\NotificationBundle\Message\MessageInterface;

class FixtureBuilder
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
    public function createFixture()
    {
        return $this->messageBuilder->createMessage();
    }
}