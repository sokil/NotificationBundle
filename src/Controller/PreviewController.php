<?php

namespace Sokil\NotificationBundle\Controller;

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

        $messageBuilderCollectionServiceId = $messageBuilderCollectionList[$messageBuilderCollectionName];
        $messageBuilderCollection = $this->get($messageBuilderCollectionServiceId);

        // build message
        $messageBuilder = $messageBuilderCollection
            ->getBuilder($messageType, $transportName);

        $message = $messageBuilder->createMessage();
        $messageBuilder->applyFixture($message);

        // show message
        if ($transportName === 'email') {
            return $this->render('NotificationBundle:Preview:email.html.twig', [
                'subject' => $message->getSubject(),
                'body' => str_replace(["\r", "\n"], '', $message->getBody()),
            ]);
        } else {
            return $this->render('NotificationBundle:Preview:common.html.twig', [
                'body' => str_replace(["\r", "\n"], '', $message->getBody()),
            ]);
        }
    }
}