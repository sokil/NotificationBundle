<?php

namespace Sokil\NotificationBundle;

use Sokil\NotificationBundle\DependencyInjection\CompilerPass\TransportProviderPass;
use Sokil\NotificationBundle\DependencyInjection\CompilerPass\MessageBuilderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NotificationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new TransportProviderPass());
        $container->addCompilerPass(new MessageBuilderPass());
    }
}
