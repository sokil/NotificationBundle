<?php

namespace Sokil\NotificationBundle\Transport;

use Sokil\NotificationBundle\Message\EmailMessageInterface;
use Sokil\NotificationBundle\Message\MessageInterface;

class EmailTransport implements TransportInterface
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    private $senderEmail;

    private $senderName;

    public function __construct(
        \Swift_Mailer $mailer,
        $senderEmail,
        $senderName
    ) {
        $this->mailer = $mailer;
        $this->senderEmail = $senderEmail;
        $this->senderName = $senderName;
    }

    /**
     * @param EmailMessageInterface $message
     * @param array $recipients
     * @throws \Exception
     */
    public function send(
        MessageInterface $message,
        array $recipients
    ) {
        /* @var $mailerMessage \Swift_Message */

        if (!($message instanceof EmailMessageInterface)) {
            throw new \InvalidArgumentException('Message must implament EmailMessageInterface');
        }

        $mailerMessage = $this->mailer->createMessage();
        $mailerMessage
            ->setBcc($recipients)
            ->setSubject($message->getSubject())
            ->setFrom($this->senderEmail, $this->senderName)
            ->setBody($message->getBody(), 'text/html');

        // send
        $this->mailer->send($mailerMessage);
    }
}