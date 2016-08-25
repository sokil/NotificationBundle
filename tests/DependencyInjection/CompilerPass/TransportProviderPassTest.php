<?php

namespace Sokil\NotificationBundle\DependencyInjection\CompilerPass;

use Sokil\NotificationBundle\Transport\EmailTransport;
use Sokil\NotificationBundle\TransportProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class TransportProviderPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage No notification transport configured
     */
    public function testProcess_NoTransportConfigured()
    {
        // transport provider definition
        $transportProviderDefinition = new Definition();
        $transportProviderDefinition->setClass(TransportProvider::class);

        // build container
        $container = new ContainerBuilder();
        $container->setDefinition('notification.transport_provider', $transportProviderDefinition);

        // compile container
        $container->addCompilerPass(new TransportProviderPass());
        $container->compile();
    }

    public function testProcess()
    {
        // email provider definition
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
        $container->setDefinition('notification.transport_provider', $transportProviderDefinition);
        $container->setDefinition('notification.transport.email', $emailTransportDefinition);

        // compile container
        $container->addCompilerPass(new TransportProviderPass());
        $container->compile();

        // test transport exists in provider
        $transportProvider = $container->get('notification.transport_provider');
        $emailTransport = $transportProvider->getTransport('email');

        $this->assertInstanceOf(EmailTransport::class, $emailTransport);
    }
}