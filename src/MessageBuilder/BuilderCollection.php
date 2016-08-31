<?php

namespace Sokil\NotificationBundle\MessageBuilder;

use Sokil\NotificationBundle\Exception\MessageBuilderNotFoundException;
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
     * @return AbstractFixtureBuilder
     * @throws NotificationException
     */
    public function getFixtureBuilder($messageType, $transportName)
    {
        $messageBuilder = $this->getBuilder(
            $messageType,
            $transportName
        );

        $fixtureBuilderClass = get_class($messageBuilder) . 'FixtureBuilder';

        $fixtureBuilder =  new $fixtureBuilderClass($this);
        if ($fixtureBuilder instanceof AbstractFixtureBuilder) {
            return $fixtureBuilder;
        }

        throw new NotificationException(sprintf(
            'Fixture for message builder with type "%s" for transport "%s" not configured',
            $messageType,
            $transportName
        ));
    }
}