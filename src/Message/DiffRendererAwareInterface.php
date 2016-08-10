<?php

namespace Sokil\NotificationBundle\Message;

use Sokil\Diff\Renderer;

interface DiffRendererAwareInterface
{
    public function setDiffRenderer(Renderer $renderer);
}