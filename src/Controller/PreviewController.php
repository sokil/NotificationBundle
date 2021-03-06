<?php

namespace Sokil\NotificationBundle\Controller;

use Sokil\NotificationBundle\Exception\MessageBuilderNotFoundException;
use Sokil\NotificationBundle\Exception\MessageFixtureBuilderNotFoundException;
use Sokil\NotificationBundle\Message\EmailMessageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PreviewController extends Controller
{
    /**
     * @Route("/preview", name="notification_preview_email")
     * @Method({"GET"})
     */
    public function preview(Request $request)
    {
        // check access
        if (!$this->isGranted('ROLE_NOTIFICATION_MAIL_PREVIEW')) {
            throw $this->createAccessDeniedException();
        }

        // message type
        $messageType = $request->get('messageType');
        if (!$messageType) {
            throw new BadRequestHttpException('Message type not specified');
        }

        // transport name
        $transportName = $request->get('transportName');
        if (!$transportName) {
            throw new BadRequestHttpException('Transport not specified');
        }

        // get collection name
        $messageBuilderCollectionName = $request->get('collection', 'default');
        if (!$messageBuilderCollectionName) {
            throw new BadRequestHttpException('Collection not specified');
        }

        // get collection
        $messageBuilderCollectionList = $this->getParameter('notification.message_builder_collection.list');
        if (empty($messageBuilderCollectionList[$messageBuilderCollectionName])) {
            throw new BadRequestHttpException(sprintf('Collection with name %s not found', $messageBuilderCollectionName));
        }

        /* @var $messageBuilderCollection \Sokil\NotificationBundle\MessageBuilder\BuilderCollection */
        $messageBuilderCollectionServiceId = $messageBuilderCollectionList[$messageBuilderCollectionName];
        $messageBuilderCollection = $this->get($messageBuilderCollectionServiceId);

        // build message
        try {
            $messageFixtureBuilder = $messageBuilderCollection->getFixtureBuilder($messageType, $transportName);
        } catch (MessageBuilderNotFoundException $e) {
            throw new BadRequestHttpException(sprintf(
                'Message with type "%s" for transport "%s" not configured in  collection "%s"',
                $messageType,
                $transportName,
                $messageBuilderCollectionName
            ));
        } catch (MessageFixtureBuilderNotFoundException $e) {
            throw new BadRequestHttpException(sprintf(
                'Message fixture for builder with type "%s" for transport "%s" not configured in collection "%s"',
                $messageType,
                $transportName,
                $messageBuilderCollectionName
            ));
        }

        $message = $messageFixtureBuilder->createFixture();

        // show email message
        if ($message instanceof EmailMessageInterface) {
            return $this->render('NotificationBundle:Preview:email.html.twig', [
                'subject' => $message->getSubject(),
                'body' => str_replace(["\r", "\n"], '', $message->getBody()),
            ]);
        }

        // show message
        return $this->render('NotificationBundle:Preview:common.html.twig', [
            'body' => str_replace(["\r", "\n"], '', $message->getBody()),
        ]);
    }
}