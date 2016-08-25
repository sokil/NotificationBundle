<?php

namespace Sokil\NotificationBundle\DependencyInjection\CompilerPass;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MessageBuilderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // get builder collection tags
        $messageBuilderCollectionListDefinition = $container->findTaggedServiceIds('notification.message_builder_collection');
        if (empty ($messageBuilderCollectionListDefinition)) {
            throw new InvalidConfigurationException('No message builder collections configured');
        }

        // get service ids of collections
        $builderCollectionServiceIdList = [];
        foreach ($messageBuilderCollectionListDefinition as $builderCollectionServiceId => $builderCollectionDefinitionTags) {
            foreach ($builderCollectionDefinitionTags as $builderCollectionDefinitionTag) {
                $collectionName = empty($builderCollectionDefinitionTag['collectionName'])
                    ? 'default'
                    : $builderCollectionDefinitionTag['collectionName'];

                $builderCollectionServiceIdList[$collectionName] = $builderCollectionServiceId;
            }
        }

        // get builders
        $messageBuilderListDefinition = $container->findTaggedServiceIds('notification.message_builder');
        if (empty($messageBuilderListDefinition)) {
            throw new InvalidConfigurationException('No message builder configured');
        }

        // add builders to collections
        foreach ($messageBuilderListDefinition as $builderServiceId => $builderDefinitionTags) {
            foreach ($builderDefinitionTags as $builderDefinitionTag) {
                // check existence of builder's type
                if (empty($builderDefinitionTag['messageType'])) {
                    throw new InvalidConfigurationException(sprintf('Type of message builder with service id "%s" not specified', $builderServiceId));
                }

                // check existence of builder's transport
                if (empty($builderDefinitionTag['transport'])) {
                    throw new InvalidConfigurationException(sprintf('Transport for message builder with service id "%s" not specified', $builderServiceId));
                }

                // check builder's collection name
                $collectionName = empty($builderDefinitionTag['collectionName'])
                    ? 'default'
                    : $builderDefinitionTag['collectionName'];

                if (empty($builderCollectionServiceIdList[$collectionName])) {
                    throw new InvalidConfigurationException(sprintf(
                        'Message builder with service id "%s" configured with unexisted collection %s',
                        $builderServiceId,
                        $collectionName
                    ));
                }

                // add builder to collection
                $builderCollectionDefinitionServiceId = $builderCollectionServiceIdList[$collectionName];
                $builderCollectionDefinition = $container->getDefinition($builderCollectionDefinitionServiceId);

                $builderCollectionDefinition->addMethodCall(
                    'addBuilder',
                    [
                        $builderDefinitionTag['messageType'],
                        $builderDefinitionTag['transport'],
                        $container->getDefinition($builderServiceId),
                    ]
                );
            }
        }
    }
}