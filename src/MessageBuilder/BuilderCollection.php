<?php

namespace Sokil\NotificationBundle\MessageBuilder;

use Sokil\NotificationBundle\Exception\MessageBuilderNotFoundException;

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
}