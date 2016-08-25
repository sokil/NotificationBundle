<?php

namespace Sokil\NotificationBundle\MessageBuilder;

use Sokil\NotificationBundle\Exception\InvalidArgumentException;
use Sokil\NotificationBundle\Message\MessageInterface;
use Sokil\NotificationBundle\Message\MessageFixtureInterface;

abstract class AbstractBuilder
{
    /**
     * @param string $type
     * @return MessageInterface
     * @throws \Exception
     */
    abstract public function createMessage();

    /**
     * @param MessageInterface $message
     * @return MessageInterface
     * @throws \Exception
     */
    public function applyFixture(MessageInterface $message)
    {
        $fixtureClassName = get_class($message) . 'Fixture';
        if (!class_exists($fixtureClassName)) {
            throw new InvalidArgumentException('Message fixture class not found');
        }

        /** @var MessageFixtureInterface $messageFixture */
        $messageFixture = new $fixtureClassName;
        if (!($messageFixture instanceof MessageFixtureInterface)) {
            throw new InvalidArgumentException('Fixture must implement MessageFixtureInterface');
        }

        $messageFixture->apply($message);

        return $message;
    }
}