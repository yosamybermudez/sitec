<?php

namespace App\Controller;

use App\Entity\Jornada;
use App\Repository\OrdenTrabajoRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/principal")
 */
class AppModuleController extends AbstractController
{
    /**
     * @Route("/", name="app_module_index")
     */
    // Index de los módulos
    public function index(Request $request, OrdenTrabajoRepository $ordenTrabajoRepository)
    {
        // Precheck
        $login_url = $this->generateUrl('app_login',array(), UrlGeneratorInterface::ABSOLUTE_URL);
        $referer = $request->headers->get('referer');

        if($referer === $login_url){
            $ordenTrabajoRepository->cancelarReservacionesNoRealizadas();
        }

        //
        $session = $this->get('session');
        $session->set('modulo', 'index');
        $modulos = $this->modulosAutorizados();
        return $this->render('app_module/index.html.twig', [
            'modulos' => $modulos
        ]);
    }

    /**
     * @Route("/taller", name="app_module_taller")
     * @Security("is_granted('ROLE_RECEPCIONISTA') or is_granted('ROLE_TECNICO')")
     */
    public function tallerIndex()
    {
        $texto = 'En este módulo se podrán realizar las actividades relacionadas con la gestión del taller.
        Controlando el flujo de los equipos desde que entran al taller, hasta su salida. 
        Generando las informaciones necesarias para su posterior análisis.';
        $session = $this->get('session');
        $session->set('modulo', 'taller');
        $jornada = $this->getDoctrine()->getRepository(Jornada::class)->findOneBy(array('fecha' => new \DateTime()));
        return $this->render('app_module/modulo.html.twig', [
            'title' => 'Taller',
            'texto' => $texto,
            'jornada' => $jornada
        ]);
    }

    /**
     * @Route("/inventario", name="app_module_inventario")
     */
    public function inventarioIndex()
    {
        $texto = 'En este módulo se podrán realizar las actividades relacionadas con la gestión del inventario de materiales para la reparación de equipos.
        En este módulo, los responsables pordrán dar entrada a las materias primas que se usan para la reparación de los equipos; las cuales tednrán salida mediante
        las órdenes de trabajo realizadas. Los responsables del negocio, podrán visualizar el listado de entradas, los movimientos y las existencias actuales. 
        Los técnicos pueden ver solamente la existencia en almacén.';
        $session = $this->get('session');
        $session->set('modulo', 'inventario');
        return $this->render('app_module/modulo.html.twig', [
            'title' => 'Inventario',
            'texto' => $texto
        ]);
    }

    /**
     * @Route("/sistema", name="app_module_sistema")
     */
    public function sistemaIndex()
    {
        $texto = 'En este módulo se podrán realizar las actividades relacionadas con la configuración del sistema';
        $session = $this->get('session');
        $session->set('modulo', 'sistema');
        return $this->render('app_module/modulo.html.twig', [
            'title' => 'Sistema',
            'texto' => $texto
        ]);
    }

    /**
     * @Route("/sistema/avanzado", name="app_module_sistema_avanzado")
     * @Security("is_granted('ROLE_ADMINISTRADOR_NEGOCIO')")
     */
    public function sistemaAvanzadoIndex()
    {
        $texto = 'No trabaje en este módulo si no sabe lo que está haciendo.';
        $session = $this->get('session');
        $session->set('modulo', 'avanzado');
        return $this->render('app_module/modulo.html.twig', [
            'title' => 'Sistema / Avanzado',
            'texto' => $texto,

        ]);
    }

    /**
     * @Route("/sistema/notificaciones", name="app_module_sistema_notificaciones")
     */
    public function notificaciones(OrdenTrabajoRepository $ordenTrabajoRepository)
    {
        if($this->isGranted('ROLE_TECNICO')){
            $ordenes = $ordenTrabajoRepository->findTrabajosPendientesTecnico($this->getDatabaseUser());
        } else {
            $ordenes = $ordenTrabajoRepository->findTodosTrabajosPendientesEnTaller();
        }
        return $this->render('_mdb/mdb_nav_notifications.html.twig', [
            'ordenes' => $ordenes
        ]);
    }

    private function modulos(){
        /* TALLER */
        $modulos[] = array(
            'nombre' => 'Taller',
            'id' => 'taller',
            'icon' => 'home',
            'roles' => array('ROLE_RECEPCIONISTA', 'ROLE_TECNICO'),
            'enlace_directo' => $this->get('router')->generate('app_module_taller'),
            'enlaces' => array()
        );

        /* INVENTARIOS */
        $modulos[] = array(
            'nombre' => 'Inventarios',
            'id' => 'inventarios',
            'icon' => 'clipboard',
            'roles' => array('ROLE_ADMINISTRACION'),
            'enlace_directo' => $this->get('router')->generate('app_module_inventario'),
            'enlaces' => array()
        );

        /* SISTEMA */
        $modulos[] = array(
            'nombre' => 'Sistema',
            'id' => 'sistema',
            'icon' => 'cog',
            'roles' => array('ROLE_ADMINISTRADOR_NEGOCIO'),
            'enlace_directo' => $this->get('router')->generate('app_module_sistema'),
            'enlaces' => array()
        );
        return $modulos;
    }

    private function modulosAutorizados(){
        $modulos = $this->modulos();
        $cant_modulos = count($modulos);
        for($idM = 0; $idM < $cant_modulos; $idM++) { //MODULO
            $roles = $modulos[$idM]['roles'];
            $modulos[$idM]['mostrar'] = $this->isGrantedRole($roles);
        }
        return $modulos;
    }

    private function isGrantedRole($roles){
        foreach ($roles as $rol){
            if($this->isGranted($rol)){
                return true;
            }
        }
        return false;
    }
}
