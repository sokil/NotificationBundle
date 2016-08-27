<?php

namespace Sokil\NotificationBundle\Controller;

use Sokil\NotificationBundle\DependencyInjection\CompilerPass\MessageBuilderPass;
use Sokil\NotificationBundle\DependencyInjection\CompilerPass\TransportProviderPass;
use Sokil\NotificationBundle\MessageBuilder\Builder;
use Sokil\NotificationBundle\MessageBuilder\BuilderCollection;
use Sokil\NotificationBundle\Stub\Symfony\Security\AuthorizationChecker\IsGrantedChecker;
use Sokil\NotificationBundle\Stub\Message\SomeMessage;
use Sokil\NotificationBundle\Stub\Symfony\Templating\Engine;
use Sokil\NotificationBundle\Transport\EmailTransport;
use Sokil\NotificationBundle\TransportProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpFoundation\Request;

class PreviewControllerTest extends \PHPUnit_Framework_TestCase
{
    private function createContainer()
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
            ->addTag('notification.message_builder', [
                'collectionName'    => 'default',
                'messageType'       => 'someMessage',
                'transport'         => 'sms',
            ])
            ->setArguments([
                SomeMessage::class
            ]);

        // email transport provider definition
        $emailTransportDefinition = new Definition();
        $emailTransportDefinition
            ->setClass(EmailTransport::class)
            ->setArguments([
                $this->getMockBuilder('\Swift_Mailer')->disableOriginalConstructor()->getMock(),
                'sender@server.com',
                'senderName',
            ])
            ->addTag('notification.transport', ['transportName' => 'email'])
            ->addTag('someOtherTag', ['someOtherParam' => 'someOtherValue']);

        // transport provider definition
        $transportProviderDefinition = new Definition();
        $transportProviderDefinition->setClass(TransportProvider::class);

        // build container
        $container = new ContainerBuilder();
        $container->setDefinition('notification.message_builder_collection', $defaultMessageBuilderCollectionDefinition);
        $container->setDefinition('notification.some_message_builder', $someMessageBuilderDefinition);
        $container->setDefinition('notification.transport_provider', $transportProviderDefinition);
        $container->setDefinition('notification.transport.email', $emailTransportDefinition);

        // add stubs
        $container->set('security.authorization_checker', new IsGrantedChecker());
        $container->set('templating', new Engine());

        // compile container
        $container->addCompilerPass(new MessageBuilderPass());
        $container->addCompilerPass(new TransportProviderPass());
        $container->compile();

        return $container;
    }

    public function testPreview()
    {
        $controller = new PreviewController();
        $controller->setContainer($this->createContainer());

        $request = new Request([
            'messageType' => 'someMessage',
            'transportName' => 'email',
        ]);

        $response = $controller->preview($request);
    }
}