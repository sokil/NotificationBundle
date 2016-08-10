<?php

namespace Sokil\NotificationBundle\Message;

use Symfony\Component\Translation\TranslatorInterface;

interface TranslatorAwareInterface
{
    public function setTranslator(TranslatorInterface $translator);

    public function getTranslator();
}