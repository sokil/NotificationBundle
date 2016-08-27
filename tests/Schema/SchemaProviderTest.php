<?php

namespace Sokil\NotificationBundle\Schema;

use Sokil\NotificationBundle\DependencyInjection\NotificationExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SchemaProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadAppConfiguration()
    {
        $config = [
            0 => [
                'schema' => [
                    [
                        'id' => 42,
                        'name' => "SomeName",
                        'recipients' => [
                            'email' => [
                                'someGroup1',
                                'someGroup2',
                                'user1@server.com',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $container = new ContainerBuilder();

        $extension = new NotificationExtension();
        $extension->load($config, $container);

        $this->assertSame(
            'SomeName',
            $container->get('notification.schema_provider')->getConfiguration(42)->getName()
        );
    }
}