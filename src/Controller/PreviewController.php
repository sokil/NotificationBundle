<?php

namespace Sokil\NotificationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PreviewController extends Controller
{
    /**
     * @Route("/preview/email", name="notification_preview_email")
     * @Method({"GET"})
     */
    public function email(Request $request)
    {
        // check access
        if (!$this->isGranted('ROLE_MAIL_MANAGER')) {
            throw $this->createAccessDeniedException();
        }

        $messageType = $request->get('messageType');
        if (!$messageType) {
            throw new \Exception('Message type not specified');
        }

        $emailMessageProvider = $this->get('notification.message_builder');

        $message = $emailMessageProvider->createMessage($messageType, 'email');
        $emailMessageProvider->applyFixture($message);

        return $this->render('NotificationBundle:Preview:email.html.twig', [
            'subject' => $message->getSubject(),
            'body' => str_replace(["\r", "\n"], '', $message->getBody()),
        ]);
    }
}