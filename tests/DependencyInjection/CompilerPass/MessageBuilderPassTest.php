<?php

namespace Sokil\NotificationBundle\DependencyInjection\CompilerPass;

use Sokil\NotificationBundle\MessageBuilder\Builder;
use Sokil\NotificationBundle\MessageBuilder\BuilderCollection;
use Sokil\NotificationBundle\Stub\Message\SomeMessage;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class MessageBuilderPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage No message builder configured
     */
    public function testProcess_NoMessageBuilderConfigured()
    {
        // transport provider definition
        $messageBuilderCollectionDefinition = new Definition();
        $messageBuilderCollectionDefinition
            ->setClass(BuilderCollection::class)
            ->addTag('notification.message_builder_collection');

        // build container
        $container = new ContainerBuilder();
        $container->setDefinition('notification.message_builder_collection', $messageBuilderCollectionDefinition);

        // compile container
        $container->addCompilerPass(new MessageBuilderPass());
        $container->compile();
    }

    public function testProcess_BuilderCollectionSpecifier_dataProvider()
    {
        return [
            ['default'],
            [null]
        ];
    }

    /**
     * @dataProvider testProcess_BuilderCollectionSpecifier_dataProvider
     */
    public function testProcess_BuilderCollectionSpecified($collection)
    {
        // default message builder collection definition
        $defaultMessageBuilderCollectionDefinition = new Definition();
        $defaultMessageBuilderCollectionDefinition
            ->setClass(BuilderCollection::class)
            ->addTag('notification.message_builder_collection', ['collectionName' => 'default'])
            ->addTag('someOtherTag', ['someOtherParam' => 'someOtherValue']);

        // message builder definition
        $someMessageBuilderDefinition = new Definition();
        $someMessageBuilderDefinition
            ->setClass(Builder::class)
            ->addTag('notification.message_builder', [
                'collectionName'    => $collection,
                'messageType'       => 'someMessage',
                'transport'         => 'email',
            ])
            ->addTag('notification.message_builder', [
                'collectionName'    => $collection,
                'messageType'       => 'someMessage',
                'transport'         => 'sms',
            ])
            ->setArguments([
                SomeMessage::class
            ]);

        // build container
        $container = new ContainerBuilder();
        $container->setDefinition('notification.message_builder_collection', $defaultMessageBuilderCollectionDefinition);
        $container->setDefinition('notification.some_message_builder', $someMessageBuilderDefinition);

        // compile container
        $container->addCompilerPass(new MessageBuilderPass());
        $container->compile();

        // get message builder
        $someEmailMessageBuilder = $container->get('notification.message_builder_collection')->getBuilder('someMessage', 'email');
        $this->assertInstanceOf(Builder::class, $someEmailMessageBuilder);

        $someSmsMessageBuilder = $container->get('notification.message_builder_collection')->getBuilder('someMessage', 'sms');
        $this->assertInstanceOf(Builder::class, $someSmsMessageBuilder);

        // build message
        $emailMessage = $someEmailMessageBuilder->createMessage();
        $this->assertSame('some body', $emailMessage->getBody());

        $smsMessage = $someSmsMessageBuilder->createMessage();
        $this->assertSame('some body', $smsMessage->getBody());
    }

    public function testProcess_CreateUndefinedBuilderCollection()
    {
        // message builder definition
        $someMessageBuilderDefinition = new Definition();
        $someMessageBuilderDefinition
            ->setClass(Builder::class)
            ->addTag('notification.message_builder', [
                'collectionName'    => 'someUnexistedCollectionName',
                'messageType'       => 'someMessage',
                'transport'         => 'email',
            ])
            ->setArguments([
                SomeMessage::class
            ]);

        // build container
        $container = new ContainerBuilder();
        $container->setDefinition('notification.some_message_builder', $someMessageBuilderDefinition);

        // compile container
        $container->addCompilerPass(new MessageBuilderPass());
        $container->compile();

        // test
        $this->assertInstanceOf(
            BuilderCollection::class,
            $container->get('notification.message_builder_collection.someUnexistedCollectionName')
        );

        // test existance of service in list
        $this->assertEquals(
            [
                'someUnexistedCollectionName' => 'notification.message_builder_collection.someUnexistedCollectionName',
            ],
            $container->getParameter('notification.message_builder_collection.list')
        );
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Type of message builder with service id "notification.some_message_builder" not specified
     */
    public function testProcess_BuilderTypeNotSpecifier()
    {
        // default message builder collection definition
        $defaultMessageBuilderCollectionDefinition = new Definition();
        $defaultMessageBuilderCollectionDefinition
            ->setClass(BuilderCollection::class)
            ->addTag('notification.message_builder_collection', ['collectionName' => 'default'])
            ->addTag('someOtherTag', ['someOtherParam' => 'someOtherValue']);

        // message builder definition
        $someMessageBuilderDefinition = new Definition();
        $someMessageBuilderDefinition
            ->setClass(Builder::class)
            ->addTag('notification.message_builder', [
                'collectionName'    => 'default',
                'transport'         => 'email',
            ])
            ->setArguments([
                SomeMessage::class
            ]);

        // build container
        $container = new ContainerBuilder();
        $container->setDefinition('notification.message_builder_collection', $defaultMessageBuilderCollectionDefinition);
        $container->setDefinition('notification.some_message_builder', $someMessageBuilderDefinition);

        // compile container
        $container->addCompilerPass(new MessageBuilderPass());
        $container->compile();
    }

    /**
     * @expectedException \Sokil\NotificationBundle\Exception\MessageBuilderNotFoundException
     * @expectedExceptionMessage Message with type "unexistedMessageType" for transport "email" not configured
     */
    public function testProcess_GetUnexistedBuilder()
    {
        // default message builder collection definition
        $defaultMessageBuilderCollectionDefinition = new Definition();
        $defaultMessageBuilderCollectionDefinition
            ->setClass(BuilderCollection::class)
            ->addTag('notification.message_builder_collection', ['collectionName' => 'default'])
            ->addTag('someOtherTag', ['someOtherParam' => 'someOtherValue']);

        // message builder definition
        $someMessageBuilderDefinition = new Definition();
        $someMessageBuilderDefinition
            ->setClass(Builder::class)
            ->addTag('notification.message_builder', [
                'collectionName'    => 'default',
                'messageType'       => 'someMessage',
                'transport'         => 'email',
            ])
            ->setArguments([
                SomeMessage::class
            ]);

        // build container
        $container = new ContainerBuilder();
        $container->setDefinition('notification.message_builder_collection', $defaultMessageBuilderCollectionDefinition);
        $container->setDefinition('notification.some_message_builder', $someMessageBuilderDefinition);

        // compile container
        $container->addCompilerPass(new MessageBuilderPass());
        $container->compile();

        // get message builder
        $container
            ->get('notification.message_builder_collection')
            ->getBuilder('unexistedMessageType', 'email');
    }
}