<?php

namespace App\Handler;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $router;

    private $em;

    private $security;

    public function __construct(RouterInterface $router, EntityManager $entityManager)
    {
        $this->router = $router;
        $this->em = $entityManager;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        return new RedirectResponse($this->router->generate('app_module_index'));
    }
}