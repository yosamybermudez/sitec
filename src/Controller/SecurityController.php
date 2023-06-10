<?php

namespace App\Controller;

use App\Entity\MyLdap;
use App\Entity\RegistroAccesoSistema;
use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login", methods={"GET","POST"})
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        try{
            $usuarios = $this->getDoctrine()->getRepository(Usuario::class)->findAll();
        } catch (\Exception $e) {
            $usuarios = array();
        }

        try{
            $ldapServers = $this->getDoctrine()->getRepository(MyLdap::class)->findAll();
        } catch (\Exception $e){
            $ldapServers = array();
        }
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return count($usuarios) > 0 ? $this->render(
            '_mdb/mdb_login_2.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
                'ldap_servers' => $ldapServers
            ]) : $this->redirectToRoute('sistema_install')
        ;
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        $em = $this->getDoctrine()->getManager();
        if($this->container->get('security.token_storage')->getToken()){
            $registroAccesoId = $this->container->get('security.token_storage')->getToken()->getAttribute('registro_acceso');
        } else {
            return $this->redirectToRoute('app_login');
        }
        $registroAcceso = $em->getRepository(RegistroAccesoSistema::class)->find($registroAccesoId);
        if($registroAcceso){
            $registroAcceso->setLogoutDate(new \DateTime());
           
            $em->persist($registroAcceso);
            $em->flush();
        }

        $this->get('security.token_storage')->setToken(null);
        return $this->redirectToRoute('app_login');
    }
}
