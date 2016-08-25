<?php

namespace Sokil\NotificationBundle;

use Sokil\NotificationBundle\Exception\InvalidArgumentException;
use Sokil\NotificationBundle\Transport\TransportInterface;

class TransportProvider
{
    /**
     * @var TransportInterface[]
     */
    private $transport;

    /**
     * Set transport
     *
     * @param $transportName
     * @param TransportInterface $transport
     * @return $this
     */
    public function setTransport($transportName, TransportInterface $transport)
    {
        $this->transport[$transportName] = $transport;
        return $this;
    }

    /**
     * Get transport by name
     *
     * @param $transportName
     * @return TransportInterface
     * @throws InvalidArgumentException
     */
    public function getTransport($transportName)
    {
        if (empty($this->transport[$transportName])) {
            throw new InvalidArgumentException(sprintf('Transport %s not found', $transportName));
        }

        return $this->transport[$transportName];
    }
}