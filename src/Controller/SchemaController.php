<?php

namespace Sokil\NotificationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SchemaController extends Controller
{
    /**
     * @Route("/schemas", name="notification_schema_list", requirements={"id": "\d+"})
     * @Method({"GET"})
     */
    public function listAction(Request $request)
    {
        // check access
        if (!$this->isGranted('ROLE_NOTIFICATION_SCHEMA_MANAGER')) {
            throw $this->createAccessDeniedException();
        }

        // get list
        $list = $this->get('notification.schema_provider')->getConfigurations();

        // response
        return new Response($this->get('serializer')->serialize(
            $list,
            'json',
            ['groups' => ['select']]
        ));
    }
}