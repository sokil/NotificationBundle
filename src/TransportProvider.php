<?php

namespace Sokil\NotificationBundle;

use Sokil\NotificationBundle\Transport\TransportInterface;

class TransportProvider
{
    /**
     * @var array
     */
    private $transport;

    /**
     * @param $transportType
     * @param TransportInterface $transport
     * @return $this
     */
    public function setTransport($transportType, TransportInterface $transport)
    {
        $this->transport[$transportType] = $transport;
        return $this;
    }

    /**
     * @param $transportType
     * @return TransportInterface
     * @throws \Exception
     */
    public function getTransport($transportType)
    {
        if (empty($this->transport[$transportType])) {
            throw new InvalidArgumentException(sprintf('Transport %s not found', $transportType));
        }

        return $this->transport[$transportType];
    }
}