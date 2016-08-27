<?php

namespace Sokil\NotificationBundle\Stub\Symfony\Security\AuthorizationChecker;

class IsGrantedChecker
{
    public function isGranted($attributes, $object = null)
    {
        return true;
    }
}