<?php

namespace Sokil\NotificationBundle\MessageBuilder;

use Sokil\NotificationBundle\Exception\InvalidArgumentException;

class BuilderCollection
{
    /**
     * @var Builder[]
     */
    private $collection;

    /**
     * @param string $messageType
     * @param Builder $builder
     * @return $this
     */
    public function addBuilder($messageType, $transportName, Builder $builder)
    {
        $this->collection[$messageType][$transportName] = $builder;
        return $this;
    }

    public function getBuilder($messageType, $transportName)
    {
        if (empty($this->collection[$messageType][$transportName])) {
            throw new InvalidArgumentException(sprintf('Message with type "%s" for transport "%s" not configured', $messageType, $transportName));
        }

        return $this->collection[$messageType][$transportName];
    }
}