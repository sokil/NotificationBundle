<?php

namespace Sokil\NotificationBundle;

use Sokil\Diff\Renderer;
use Sokil\NotificationBundle\Message\DiffRendererAwareInterface;
use Sokil\NotificationBundle\Message\MessageInterface;
use Sokil\TaskStockBundle\Notification\Message\styring;
use Sokil\NotificationBundle\Message\MessageFixtureInterface;

use Sokil\NotificationBundle\Message\TemplateAwareInterface;
use Sokil\NotificationBundle\Message\TranslatorAwareInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface as TemplatingEngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class MessageBuilder
{
    private $messageClasses = [];

    /**
     * @var TemplatingEngineInterface
     */
    private $templatingEngine;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    private $textDiffRenderer;

    public function __construct(
        TemplatingEngineInterface $templatingEngine,
        TranslatorInterface $translator,
        Renderer $textDiffRenderer
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->translator = $translator;
        $this->textDiffRenderer = $textDiffRenderer;
    }

    public function registerMessageType(
        $messageType,
        $transportType,
        $messageClass
    ) {
        $this->messageClasses[$messageType][$transportType] = $messageClass;
    }

    public function registerMessageTypes(
        array $types
    ) {
        foreach ($types as $messageType => $messageClasses) {
            foreach ($messageClasses as $transportType => $messageClass) {
                $this->registerMessageType($messageType, $transportType, $messageClass);
            }
        }
    }

    /**
     * @param string $messageType
     * @param styring $transportType
     * @return MessageInterface
     * @throws \Exception
     */
    public function createMessage($messageType, $transportType)
    {
        if (empty($this->messageClasses[$messageType][$transportType])) {
            throw new \InvalidArgumentException(sprintf(
                'Message with name %s for transport %s not found',
                $messageType,
                $transportType
            ));
        }

        /** @var MessageInterface $message */
        $messageClassName = $this->messageClasses[$messageType][$transportType];
        $message = new $messageClassName;

        if ($message instanceof TemplateAwareInterface) {
            $message->setTemplateEngine($this->templatingEngine);
        }

        if ($message instanceof TranslatorAwareInterface) {
            $message->setTranslator($this->translator);
        }

        if ($message instanceof DiffRendererAwareInterface) {
            $message->setDiffRenderer($this->textDiffRenderer);
        }

        return $message;
    }

    /**
     * @param MessageInterface $message
     * @return MessageInterface
     * @throws \Exception
     */
    public function applyFixture(MessageInterface $message)
    {
        $fixtureClassName = get_class($message) . 'Fixture';
        if (!class_exists($fixtureClassName)) {
            throw new \InvalidArgumentException('Message fixture class not found');
        }

        /** @var MessageFixtureInterface $messageFixture */
        $messageFixture = new $fixtureClassName;
        if (!($messageFixture instanceof MessageFixtureInterface)) {
            throw new \InvalidArgumentException('Fixture must implement MessageFixtureInterface');
        }

        $messageFixture->apply($message);

        return $message;
    }
}