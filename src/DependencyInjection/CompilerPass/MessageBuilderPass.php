<?php

namespace Sokil\NotificationBundle\DependencyInjection\CompilerPass;

use Sokil\NotificationBundle\MessageBuilder\BuilderCollection;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class MessageBuilderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // get service ids of collections
        $builderCollectionServiceIdList = $this->getMessageBuilderCollectionServiceidList($container);

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
                    // message builder configured with unknown collection name.
                    // Create new collection
                    $messageBuilderCollectionDefinition = new Definition();
                    $messageBuilderCollectionDefinition
                        ->setClass(BuilderCollection::class)
                        ->addTag('notification.message_builder_collection', [
                            'collectionName' => $collectionName,
                        ]);

                    $builderCollectionServiceIdList[$collectionName] = 'notification.message_builder_collection.' . $collectionName;
                    $container->setDefinition(
                        $builderCollectionServiceIdList[$collectionName],
                        $messageBuilderCollectionDefinition
                    );
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

        // store list of collections to parameter
        $container->setParameter('notification.message_builder_collection.list', $builderCollectionServiceIdList);
    }

    /**
     * Get service ids of collections
     */
    private function getMessageBuilderCollectionServiceidList(ContainerBuilder $container)
    {
        $builderCollectionServiceIdList = [];

        $messageBuilderCollectionListDefinition = $container->findTaggedServiceIds('notification.message_builder_collection');
        foreach ($messageBuilderCollectionListDefinition as $builderCollectionServiceId => $builderCollectionDefinitionTags) {
            foreach ($builderCollectionDefinitionTags as $builderCollectionDefinitionTag) {
                $collectionName = empty($builderCollectionDefinitionTag['collectionName'])
                    ? 'default'
                    : $builderCollectionDefinitionTag['collectionName'];

                $builderCollectionServiceIdList[$collectionName] = $builderCollectionServiceId;
            }
        }

        return $builderCollectionServiceIdList;
    }
}