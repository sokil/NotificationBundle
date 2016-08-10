<?php

namespace Sokil\NotificationBundle\Schema;

use Symfony\Component\Serializer\Annotation\Groups;

class Configuration
{
    /**
     * @var int id of schema
     */
    private $id;

    /**
     * @var string name of schema
     */
    private $name;

    /**
     * @var array list of recipients
     */
    private $recipients;

    public function __construct($id, $name, array $recipients)
    {
        $this->id = $id;
        $this->name = $name;
        $this->recipients = $recipients;
    }

    /**
     * @return int
     * @Groups({"select"})
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     * @Groups({"select"})
     */
    public function getName()
    {
        return $this->name;
    }

    public function getRecipients()
    {
        return $this->recipients;
    }

    public function getRecipientsByTransport($transport)
    {
        if (empty($this->recipients[$transport])) {
            return [];
        }

        return $this->recipients[$transport];
    }
}