<?php

// src/EventListener/LoginListener.php

namespace App\Listener;

use App\Entity\RegistroAccesoSistema;
use App\Entity\RegistroAsistencia;
use App\Entity\Usuario;
use App\Entity\UsuarioToken;
use Doctrine\ORM\EntityManagerInterface;
use http\Client\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    private $em;
    private $router;
    private $token;

    public function __construct(EntityManagerInterface $em, Router $router, TokenStorage $tokenStorage)
    {
        $this->em = $em;
        $this->router = $router;
        $this->token = $tokenStorage;
    }


    public function onLogin(InteractiveLoginEvent $event)
    {
        $user = $this->token->getToken()->getUser();
        if ($user) {
            $nomb_usuario = $user->getUsername();
            $userBD = $this->em->getRepository("App:Usuario")->findOneBy(
                array('username' => $nomb_usuario, 'isActive' => true)
            );

            $usuario = $userBD;
            if ($usuario) {
                $roles = $userBD->getRolesObjects()->toArray();
                $sfRoles = array();
                $sfRolesTemp = array();
                foreach ($roles as $r) {
                    if (in_array($r, $sfRolesTemp) === false) {
                        $sfRoles[] = 'ROLE_' . strtoupper($r->getIdentificador());
                        $sfRolesTemp[] = $r;
                    }
                    $registrosAcceso = $this->em->getRepository(RegistroAccesoSistema::class)->findBy(array('logoutDate' => null));
                    foreach ($registrosAcceso as $registroAcceso){
                        $fecha = $registroAcceso->getLoginDate();
                        $fecha->setTime(23,59,59);
                        $registroAcceso->setLogoutDate($fecha);
                        $this->em->persist($registroAcceso);
                    }
                    $registroAcceso = new RegistroAccesoSistema($userBD);
                    $registroAcceso->setRemoteAddr($event->getRequest()->server->get('REMOTE_ADDR'));
                    $registroAcceso->setHttpUserAngent($event->getRequest()->server->get('HTTP_USER_AGENT'));
                    $this->em->persist($registroAcceso);
                    $this->em->flush();
                    $token_user = new UsuarioToken($nomb_usuario, null, $sfRoles);
                    $token_user->setId($usuario->getId());
                    $token_user->setDisplayName($usuario->getNombreCompleto());
                    $token_user->setAuthenticatedSince(new \DateTime());
                    $token_user->setEmail($usuario->getEmail());
                    $token = new UsernamePasswordToken(
                        $token_user,
                        null,
                        'databaseldaptoken',
                        $sfRoles);
                    unset($sfRolesTemp);
                    $token->setAttribute('registro_acceso', $registroAcceso->getId());
                    $this->token->setToken($token);
                }
            } else {
                $this->token->setToken(null);
            }
        } else {
            $this->token->setToken(null);
        }
    }
}