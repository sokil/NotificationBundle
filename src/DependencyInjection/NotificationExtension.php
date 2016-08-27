<?php

namespace Sokil\NotificationBundle\DependencyInjection;

use Sokil\Diff\Renderer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class NotificationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // prepare config
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // load services
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // reconfigure some definitions
        $container->getDefinition('notification.schema_provider')
            ->replaceArgument(0, $config['schema']);


    }
}
