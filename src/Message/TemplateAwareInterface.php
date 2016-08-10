<?php

namespace Sokil\NotificationBundle\Message;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

interface TemplateAwareInterface
{
    /**
     * @param EngineInterface $engine
     */
    public function setTemplateEngine(EngineInterface $engine);
}