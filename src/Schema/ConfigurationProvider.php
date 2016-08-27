<?php

namespace Sokil\NotificationBundle\Schema;

use Sokil\NotificationBundle\Exception\NotificationException;

class ConfigurationProvider
{
    private $configurations;

    public function __construct(array $configurations)
    {
        $this->configurations = $configurations;
    }

    /**
     * @return \Generator
     */
    public function getConfigurations()
    {
        foreach ($this->configurations as &$configuration) {
            if (!($configuration instanceof Configuration)) {
                $configuration = new Configuration(
                    $configuration['id'],
                    $configuration['name'],
                    $configuration['recipients']
                );
            }

            yield $configuration;
        }
    }

    /**
     * @param $schemaId
     * @return Configuration
     * @throws NotificationException
     */
    public function getConfiguration($schemaId)
    {
        /* @var $configuration Configuration */
        foreach ($this->getConfigurations() as $configuration) {
            if ($configuration->getId() === $schemaId) {
                return $configuration;
            }
        }

        throw new NotificationException('Wrong schema id');
    }
}