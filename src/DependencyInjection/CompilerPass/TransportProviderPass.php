<?php

namespace Sokil\NotificationBundle\DependencyInjection\CompilerPass;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TransportProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // add transports to provider
        $transportProviderDefinition = $container->getDefinition('notification.transport_provider');

        $transportDefinitionList = $container->findTaggedServiceIds('notification.transport');
        if (empty($transportDefinitionList)) {
            throw new InvalidConfigurationException('No notification transport configured');
        }

        foreach ($transportDefinitionList as $transportServiceId => $transportDefinitionTags) {
            foreach ($transportDefinitionTags as $transportDefinitionTag) {
                $transportProviderDefinition->addMethodCall('setTransport', [
                    $transportDefinitionTag['transportName'],
                    $container->getDefinition($transportServiceId)
                ]);
            }
        }
    }
}