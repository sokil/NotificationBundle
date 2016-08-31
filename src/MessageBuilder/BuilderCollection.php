<?php

namespace Sokil\NotificationBundle\MessageBuilder;

use Sokil\NotificationBundle\Exception\MessageBuilderNotFoundException;
use Sokil\NotificationBundle\Exception\MessageFixtureBuilderNotFoundException;
use Sokil\NotificationBundle\Exception\NotificationException;

class BuilderCollection
{
    /**
     * @var Builder[]
     */
    private $collection;

    /**
     * @param string $messageType
     * @param string $transportName
     * @param AbstractBuilder $builder
     * @return $this
     */
    public function addBuilder(
        $messageType,
        $transportName,
        AbstractBuilder $builder
    ) {
        $this->collection[$messageType][$transportName] = $builder;
        return $this;
    }

    /**
     * @param $messageType
     * @param $transportName
     * @return Builder
     * @throws MessageBuilderNotFoundException
     */
    public function getBuilder($messageType, $transportName)
    {
        if (empty($this->collection[$messageType][$transportName])) {
            throw new MessageBuilderNotFoundException(sprintf(
                'Message with type "%s" for transport "%s" not configured',
                $messageType,
                $transportName
            ));
        }

        return $this->collection[$messageType][$transportName];
    }

    /**
     * @param $messageType
     * @param $transportName
     * @return FixtureBuilder
     * @throws NotificationException
     */
    public function getFixtureBuilder($messageType, $transportName)
    {
        $messageBuilder = $this->getBuilder(
            $messageType,
            $transportName
        );

        // get fixture class
        $fixtureBuilderClass = $this->getFixtureClassName($messageBuilder);
        if (!class_exists($fixtureBuilderClass)) {
            throw new MessageFixtureBuilderNotFoundException(
                sprintf(
                    'Fixture builder class not found for message type "%s" for transport "%s"',
                    $messageType,
                    $transportName
                )
            );
        }

        $fixtureBuilder =  new $fixtureBuilderClass($messageBuilder);
        if ($fixtureBuilder instanceof FixtureBuilder) {
            return $fixtureBuilder;
        }

        throw new MessageFixtureBuilderNotFoundException(sprintf(
            'Fixture builder for message builder with type "%s" for transport "%s" not configured',
            $messageType,
            $transportName
        ));
    }

    private function getFixtureClassName(AbstractBuilder $builder)
    {
        $parts = explode('\\', get_class($builder));
        $lastKey = count($parts) - 1;
        $parts[$lastKey] = 'Fixture' . $parts[$lastKey];

        return implode('\\', $parts);
    }
}