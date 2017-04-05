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

        // reconfigure schema definitions
        $container->getDefinition('notification.schema_provider')
            ->replaceArgument(0, $config['schema']);

        // email provider
        $emailTransportFromAddress = null;
        if (!empty($config['transport']['email']['from']['address'])) {
            $emailTransportFromAddress = $config['transport']['email']['from']['address'];
        } else if ($container->hasParameter('notification.from_email.address')) {
            $emailTransportFromAddress = $container->getParameter('notification.from_email.address');
        }

        $emailTransportSenderName = null;
        if (!empty($config['transport']['email']['from']['sender_name'])) {
            $emailTransportSenderName = $config['transport']['email']['from']['sender_name'];
        } else if ($container->hasParameter('notification.from_email.sender_name')) {
            $emailTransportSenderName = $container->getParameter('notification.from_email.sender_name');
        }

        $container->getDefinition('notification.transport.email')
            ->replaceArgument(1, $emailTransportFromAddress)
            ->replaceArgument(2, $emailTransportSenderName);

    }
}
