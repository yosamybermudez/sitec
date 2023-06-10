<?php

namespace App\Controller;

use App\Entity\Cargo;
use App\Entity\EquipoTipo;
use App\Entity\Rol;
use App\Entity\Usuario;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\SchemaTool;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("sistema")
 */
class AdvancedController extends AbstractController
{

    /**
     * @Route("/mostrar-rutas", name="sistema_mostrar_rutas", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMINISTRADOR_SISTEMA")
     */
    public function mostraRutas(){
        $em = $this->getDoctrine()->getManager();
        $schemaTool = new SchemaTool($em);
        $metadatas = $em->getMetadataFactory()->getAllMetadata();
        $sqls = $schemaTool->getCreateSchemaSql($metadatas);

        $router = $this->container->get('router');
        $collection = $router->getRouteCollection();
        $allRoutes = $collection->all();
        $routes = new ArrayCollection();
        /** @var $params \Symfony\Component\Routing\Route */
        foreach ($allRoutes as $route => $params)
        {
            $r = new ArrayCollection();
           $r->set('path' , $params->getPath());
           $r->set('method' , $params->getMethods());
            $explode = explode('::', $params->getDefault('_controller'));
           $r->set('controller' , str_replace("App\\Controller\\","",$explode[0]));
           $metodo = $explode[1] ?? "";
           $r->set('controller_method' , $metodo);
           $routes->set($route, $r);
        }
        return $this->render('advanced/routes.html.twig', array(
            'routes' => $routes
        ));
    }


    /**
     * @Route("/reset", name="sistema_reset", methods={"GET", "POST"})
     */
    public function reset(KernelInterface $kernel){
//        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $output = new BufferedOutput();

        $options = array('command' => 'doctrine:schema:drop', '--force' => true);
        $input = new ArrayInput($options);
        $application->run($input, $output);

        $options = array('command' => 'doctrine:schema:create');
        $input = new ArrayInput($options);
        $application->run($input, $output);

        $content = $output->fetch();
//        return new Response($content);
        $this->addFlash('success', 'Sistema reiniciado');
        return $this->redirectToRoute('logout');

    }

    /**
     * @Route("/clean-cache", name="sistema_clean_cache", methods={"GET", "POST"})
     */
    public function cleanCache(KernelInterface $kernel, Request $request)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $output = new BufferedOutput();

        $options = array('command' => 'cache:clear');
        $input = new ArrayInput($options);

        $application->run($input, $output);
        try {
        } catch (\Exception $e){
            $this->addFlash('danger', 'Ocurrió un error');
            return $this->redirectToReferer($request);
        }

        $content = $output->fetch();
//        return new Response($content);
        $this->addFlash('success', 'Caché limpiada correctamente');
        return $this->redirectToReferer($request);

    }


    /**
     * @Route("/instalar", name="sistema_install", methods={"GET", "POST"})
     */
    public function instalar(Request $request, UserPasswordEncoderInterface $passwordEncoder, KernelInterface $kernel)
    {
        if($request->getMethod() === 'POST'){
            if($request->request->get('_login') !== null)
            {
                //SITECpassword123.
                $passwordMD5 = 'bff99672c9a361d1681a30340860c0a6';
                $user = $request->request->get('_username');
                $password = $request->request->get('_password');

                if(md5($password) === $passwordMD5 and $user === 'developer'){
                    //Ejecucion de consultas a la Base de datos
                    return $this->render('advanced/install.html.twig', array(
                        'start' => true
                    ));
                } else {
                    return $this->render('advanced/install.html.twig', array(
                        'error' => 'Credenciales incorrectas'
                    ));
                }
            }

            if($request->request->get('_start') !== null){
                $application = new Application($kernel);
                $application->setAutoExit(false);

                $output = new BufferedOutput();

                $options = array('command' => 'doctrine:database:create');
                $input = new ArrayInput($options);

                $application->run($input, $output);
                try {
                } catch (\Exception $e){
                    $this->addFlash('danger', 'Ocurrió un error');
                    return $this->redirectToReferer($request);
                }

                $application = new Application($kernel);
                $application->setAutoExit(false);

                $output = new BufferedOutput();

                $options = array('command' => 'doctrine:schema:create');
                $input = new ArrayInput($options);

                $application->run($input, $output);
                try {
                } catch (\Exception $e){
                    $this->addFlash('danger', 'Ocurrió un error');
                    return $this->redirectToReferer($request);
                }

                $em = $this->getDoctrine()->getManager();

                $adminPassword = $request->request->get('_admin_password');
                $usuario = new Usuario();
                $usuario->setUsername('admin');
                $adminPassword = $passwordEncoder->encodePassword($usuario,$adminPassword);
                $usuario->setPassword($adminPassword);
                $usuario->setNombres('Administrador');
                $usuario->setApellidos('Sistema');
                $usuario->setIsActive(true);
                $usuario->setEmail('admin@taller.cu');
                $usuario->setEmail('admin@taller.cu');
                $em->persist($usuario);

                $rol1 = new Rol();
                $rol1->setNombre('Administrador del sistema');
                $rol1->setIdentificador('administrador_sistema');
                $rol1->setDescripcion('Acceso pleno a todas las funcionalidades del sistema');
                $rol1->setRango(0);
                $rol1->addUsuario($usuario);
                $em->persist($rol1);

                $rol2 = new Rol();
                $rol2->setNombre('Administrador del negocio');
                $rol2->setIdentificador('administrador_negocio');
                $rol2->setDescripcion('Acceso pleno a todas las funcionalidades del negocio');
                $rol2->setRango(1);
                $em->persist($rol2);

                $rol3 = new Rol();
                $rol3->setNombre('Administración');
                $rol3->setIdentificador('administracion');
                $rol3->setDescripcion('Control y supervisión de las tareas relacionadas con el negocio.');
                $rol3->setRango(2);
                $em->persist($rol3);

                $rol4 = new Rol();
                $rol4->setNombre('Técnico');
                $rol4->setIdentificador('tecnico');
                $rol4->setDescripcion('Registra las operaciones de revisión de los medios tecnológicos que se reciben en el taller.');
                $rol4->setRango(3);
                $em->persist($rol4);

                $rol5 = new Rol();
                $rol5->setNombre('Recepcionista');
                $rol5->setIdentificador('recepcionista');
                $rol5->setDescripcion('Registra las órdenes de trabajo para la reparación de los medios tecnológicos que se reciben en el taller.');
                $rol5->setRango(4);
                $em->persist($rol5);

                $cargo1 = new Cargo($this);
                $cargo1->setNombre('Jefe de taller');
                $cargo1->setCreatedBy($usuario);
                $cargo1->setUpdatedBy($usuario);
                $em->persist($cargo1);

                $cargo2 = new Cargo($this);
                $cargo2->setNombre('Administrador(a)');
                $cargo2->setCreatedBy($usuario);
                $cargo2->setUpdatedBy($usuario);
                $em->persist($cargo2);

                $cargo2 = new Cargo($this);
                $cargo2->setNombre('Técnico general');
                $cargo2->setCreatedBy($usuario);
                $cargo2->setUpdatedBy($usuario);
                $em->persist($cargo2);

                $cargo3 = new Cargo($this);
                $cargo3->setNombre('Recepcionista');
                $cargo3->setCreatedBy($usuario);
                $cargo3->setUpdatedBy($usuario);
                $em->persist($cargo3);

                $equipo = new EquipoTipo($this);
                $equipo->setNombre('Televisor');
                $equipo->setCreatedBy($usuario);
                $equipo->setUpdatedBy($usuario);
                $em->persist($equipo);

                $em->flush();

                $this->addFlash('success', 'Sistema inicializado correctamente. Inicie sesión con el usuario admin. Registre los trabajadores y los tipos de equipo que se reparan.');
                return $this->redirectToRoute('app_login');
            }

        }
        return $this->render('advanced/install.html.twig');
    }


}
