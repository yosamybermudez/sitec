<?php

namespace App\Controller;

use App\Entity\Jornada;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class DefaultController extends AbstractController
{

    private $roleHierarchy;

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
//        $modulos = $this->funcionalidadesAutorizadas();
////        // replace this example code with whatever you need
////        return $this->render('default/index.html.twig', [
////            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
////            'modulos' => $modulos
////        ]);
        return $this->redirectToRoute('app_module_index');
    }

    public function cargarMenuSuperior()
    {
        $menu_links = array();

        $menu_links[] = array(
            'nombre' => 'Configuración',
            'id' => 'config',
            'icon' => 'cog',
            'enlaces' => array(
             /*   array('usuarios' => 'Usuarios', 'enlace' => $this->get('router')->generate('usuario_index', array('estado' => 'activo')), 'icon' => 'user-solid-circle'),
                array('roles' => 'Roles', 'enlace' => $this->get('router')->generate('rol_index'), 'icon' => 'link'),
//                array('permisos' => 'Permisos', 'enlace' => $this->get('router')->generate('permiso_index'), 'icon' => 'link'),
                array('datos_primarios' => 'Datos primarios', 'enlace' => $this->get('router')->generate('datos_primarios'), 'icon' => 'link')*/
            )
        );

        $menu_links[] = array(
            'nombre' => 'Mi perfil',
            'id' => 'profile',
            'icon' => 'cog',
            'enlaces' => array(
                array('trabajador_data' => 'Datos del trabajador', 'enlace' => $this->get('router')->generate('trabajador_show', array('id' => $this->getUser()->getTrabajador()->getId())), 'icon' => 'user-solid-circle'),
                array('user_data' => 'Datos del usuario', 'enlace' => $this->get('router')->generate('usuario_show', array('id' => $this->getUser()->getUsername())), 'icon' => 'link')
            )
        );
    }

    public function __construct(RoleHierarchyInterface $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    public function cargarModulos()
    {
        $modulos = array();

        /* MODULOS */
        $modulos[] = array(
            'nombre' => 'Módulos',
            'id' => 'modulos',
            'subsistema' => 'todos',
            'icon' => 'view-tile',
            'roles' => array(),
            'enlace_directo' => $this->get('router')->generate('app_module_index'),
            'enlaces' => array()
        );

        /* OPERACIONES */
        $modulos[] = array(
            'nombre' => 'Operaciones',
            'id' => 'operaciones',
            'subsistema' => 'taller',
            'roles' => array(),
            'icon' => 'dashboard',
            'enlaces' => array(
                array('nombre' => 'Registrar reservación', 'roles' => ['ROLE_RECEPCIONISTA'], 'enlace' => $this->get('router')->generate('orden_trabajo_reservar'), 'icon' => 'list-add'),
            )
        );

        /* LISTADOS */
        $modulos[] = array(
            'nombre' => 'Listados',
            'subsistema' => 'taller',
            'id' => 'sistema',
            'roles' => array(),
            'icon' => 'layers',
            'enlaces' => array(
                array('nombre' => 'Mis trabajos pendientes', 'roles' => ['ROLE_TECNICO'], 'enlace' => $this->get('router')->generate('dictamen_tecnico_trabajos_pendientes'), 'icon' => 'search'),
                array('nombre' => 'Trabajos pendientes', 'roles' => ['ROLE_RECEPCIONISTA'], 'enlace' => $this->get('router')->generate('dictamen_tecnico_trabajos_pendientes_todos'), 'icon' => 'chart-pie'),
                array('nombre' => 'Equipos a entregar', 'roles' => ['ROLE_RECEPCIONISTA'], 'enlace' => $this->get('router')->generate('orden_trabajo_equipo_entregar'), 'icon' => 'send'),
                array('nombre' => 'Equipos a decomisar', 'roles' => ['ROLE_RECEPCIONISTA'], 'enlace' => $this->get('router')->generate('orden_trabajo_equipo_decomisar'), 'icon' => 'close-outline'),
                array('nombre' => 'Órdenes de trabajo', 'roles' => ['ROLE_RECEPCIONISTA'], 'enlace' => $this->get('router')->generate('orden_trabajo_index'), 'icon' => 'view-list'),
                array('nombre' => 'Reparaciones realizadas', 'roles' => ['ROLE_RECEPCIONISTA', 'ROLE_TECNICO'], 'enlace' => $this->get('router')->generate('orden_reparacion_index'), 'icon' => 'wrench'),
                array('nombre' => 'Materias primas disponibles', 'roles' => ['ROLE_RECEPCIONISTA', 'ROLE_TECNICO'], 'enlace' => $this->get('router')->generate('materia_prima_existencias'), 'icon' => 'cog'),
                array('nombre' => 'Jornadas', 'roles' => ['ROLE_RECEPCIONISTA', 'ROLE_TECNICO'], 'enlace' => $this->get('router')->generate('jornada_index'), 'icon' => 'layers'),
                array('nombre' => 'Comprobantes de operaciones', 'roles' => ['ROLE_ADMINISTRACION'], 'enlace' => $this->get('router')->generate('comprobante_operacion_index'), 'icon' => 'currency-dollar'),
                array('nombre' => 'Operaciones contables', 'roles' => ['ROLE_ADMINISTRACION'], 'enlace' => $this->get('router')->generate('operacion_contable_index'), 'icon' => 'queue'),
                array('nombre' => 'Dictámenes', 'roles' => ['ROLE_RECEPCIONISTA', 'ROLE_TECNICO'], 'enlace' => $this->get('router')->generate('dictamen_tecnico_index'), 'icon' => 'layers')
            )
        );

        /* SISTEMA */
        $modulos[] = array(
            'nombre' => 'Órdenes de trabajo',
            'subsistema' => 'taller',
            'id' => 'sistema',
            'roles' => array(),
            'icon' => 'layers',
            'enlaces' => array(
                array('nombre' => 'Órdenes de hoy', 'roles' => ['ROLE_RECEPCIONISTA'], 'enlace' => $this->get('router')->generate('orden_trabajo_index', array('fecha' => date('Ymd'))), 'icon' => 'calendar'),
                array('nombre' => 'Órdenes del mes en curso', 'roles' => ['ROLE_RECEPCIONISTA'], 'enlace' => $this->get('router')->generate('orden_trabajo_index', array('inicio' => date('Ym') . '01', 'fin' => date('Ym'). date_modify(new \DateTime(), 'last day of this month')->format('d') )), 'icon' => 'calendar'),
                array('nombre' => 'Órdenes del año en curso', 'roles' => ['ROLE_RECEPCIONISTA'], 'enlace' => $this->get('router')->generate('orden_trabajo_index', array('inicio' => date('Y') . '0101', 'fin' => date('Y') . '1231')), 'icon' => 'calendar'),
                array('nombre' => 'Todas las órdenes', 'roles' => ['ROLE_RECEPCIONISTA'], 'enlace' => $this->get('router')->generate('orden_trabajo_index'), 'icon' => 'box'),
            ));

        /* SISTEMA */
        $modulos[] = array(
            'nombre' => 'Jornadas',
            'subsistema' => 'taller',
            'id' => 'sistema',
            'roles' => array(),
            'icon' => 'layers',
            'enlaces' => array(
//                array('nombre' => 'Jornada de hoy', 'roles' => ['ROLE_RECEPCIONISTA'], 'enlace' => $this->get('router')->generate('orden_trabajo_filtrar', array('anno' => date('Y'), 'mes' => date('m'), 'dia' => date('d') )), 'icon' => 'layers'),
//                array('nombre' => 'Órdenes del mes en curso', 'roles' => ['ROLE_RECEPCIONISTA'], 'enlace' => $this->get('router')->generate('orden_trabajo_index', array('anno' => date('Y'), 'mes' => date('m') )), 'icon' => 'layers'),
//                array('nombre' => 'Órdenes del año en curso', 'roles' => ['ROLE_RECEPCIONISTA'], 'enlace' => $this->get('router')->generate('orden_trabajo_index', array('anno' => date('Y') )), 'icon' => 'layers'),
                array('nombre' => 'Todas las jornadas', 'roles' => ['ROLE_ADMINISTRACION '], 'enlace' => $this->get('router')->generate('jornada_index'), 'icon' => 'layers'),
//                array('nombre' => 'Buscar por fecha', 'roles' => ['ROLE_RECEPCIONISTA'], 'enlace' => $this->get('router')->generate('orden_trabajo_filtrar_fecha'), 'icon' => 'layers'),
            )
        );

        /* ADMINISTRACION */
        $modulos[] = array(
            'nombre' => 'Administración',
            'id' => 'administracion',
            'subsistema' => 'taller',
            'roles' => array(),
            'icon' => 'shield',
            'enlaces' => array(
                array('nombre' => 'Trabajos pendientes', 'roles' => ['ROLE_ADMINISTRACION'], 'enlace' => $this->get('router')->generate('orden_trabajo_pendientes_tecnicos'), 'icon' => 'layers'),
                array('nombre' => 'Jornada de hoy', 'roles' => ['ROLE_ADMINISTRACION'], 'enlace' => $this->get('router')->generate('jornada_show_hoy'), 'icon' => 'layers'),
                array('nombre' => 'Reportes', 'roles' => ['ROLE_ADMINISTRACION'], 'enlace' => $this->get('router')->generate('app_reportes_index'), 'icon' => 'layers'),
            )
        );

        /* INVENTARIO */
        $modulos[] = array(
            'nombre' => 'Inventario',
            'id' => 'inventario',
            'subsistema' => 'inventario',
            'roles' => array(),
            'icon' => 'layers',
            'enlaces' => array(
                array('nombre' => 'Materias primas', 'roles' => ['ROLE_ADMINISTRACION'], 'enlace' => $this->get('router')->generate('materia_prima_index'), 'icon' => 'layers'),
                array('nombre' => 'Entrada de MP', 'roles' => ['ROLE_ADMINISTRACION'], 'enlace' => $this->get('router')->generate('materia_prima_entrada_index'), 'icon' => 'download'),
                array('nombre' => 'Movimientos de MP', 'roles' => ['ROLE_ADMINISTRACION'], 'enlace' => $this->get('router')->generate('materia_prima_movimiento_index'), 'icon' => 'repost'),
            )
        );

        /* SISTEMA */
        $modulos[] = array(
            'nombre' => 'Sistema',
            'id' => 'sistema',
            'subsistema' => 'sistema',
            'roles' => array(),
            'icon' => 'layers',
            'enlaces' => array(
                array('nombre' => 'Usuarios', 'roles' => ['ROLE_ADMINISTRADOR_NEGOCIO'], 'enlace' => $this->get('router')->generate('usuario_index', array('estado' => 'activo')), 'icon' => 'user'),
                array('nombre' => 'Roles', 'roles' => ['ROLE_ADMINISTRADOR_NEGOCIO'], 'enlace' => $this->get('router')->generate('rol_index'), 'icon' => 'user-group'),
                array('nombre' => 'Cargos', 'roles' => ['ROLE_ADMINISTRADOR_NEGOCIO'], 'enlace' => $this->get('router')->generate('cargo_index'), 'icon' => 'user-solid-circle'),
                array('nombre' => 'Tipos de equipos', 'roles' => ['ROLE_ADMINISTRADOR_NEGOCIO'], 'enlace' => $this->get('router')->generate('equipo_tipo_index'), 'icon' => 'list-bullet'),
                array('nombre' => 'Avanzado', 'roles' => ['ROLE_ADMINISTRADOR_NEGOCIO'], 'enlace' => $this->get('router')->generate('app_module_sistema_avanzado'), 'icon' => 'key'),
            )
        );
        $jornada = $this->getDoctrine()->getRepository(Jornada::class)->findOneBy(array('fecha' => new \DateTime()));
        if(!$jornada){
            array_unshift($modulos[array_search('Operaciones', array_column($modulos, 'nombre'))]['enlaces'], array('nombre' => 'Crear jornada para hoy', 'roles' => ['ROLE_RECEPCIONISTA'], 'enlace' => $this->get('router')->generate('jornada_new'), 'icon' => 'folder-outline-add'));
        } else {
            if($jornada->getEstado() === 'Vigente'){
                array_unshift($modulos[array_search('Operaciones', array_column($modulos, 'nombre'))]['enlaces'], array('nombre' => 'Crear orden de trabajo', 'roles' => ['ROLE_RECEPCIONISTA'], 'enlace' => $this->get('router')->generate('orden_trabajo_new'), 'icon' => 'document-add'));
                array_unshift($modulos[array_search('Listados', array_column($modulos, 'nombre'))]['enlaces'], array('nombre' => 'Ver registro del día', 'roles' => ['ROLE_RECEPCIONISTA'], 'enlace' => $this->get('router')->generate('jornada_show', array('id' => $jornada->getId())), 'icon' => 'search'));
            } else {
                array_unshift($modulos[array_search('Listados', array_column($modulos, 'nombre'))]['enlaces'], array('nombre' => 'Ver datos de la jornada', 'roles' => ['ROLE_RECEPCIONISTA'], 'enlace' => $this->get('router')->generate('jornada_show', array('id' => $jornada->getId())), 'icon' => 'folder'));
            }
        }
       return $modulos;

    }

    public function funcionalidadesAutorizadas(){

        $modulos = $this->cargarModulos();
        $cant_modulos = count($modulos);
        for($idM = 1; $idM < $cant_modulos; $idM++){ //MODULO, EMPIEZA EN 2, hay dos enlaces directos
            $cant_modulo_enlaces = count($modulos[$idM]['enlaces']);
            if($cant_modulo_enlaces === 0){
                $modulos[$idM]['mostrar'] = false;
            } else {
                $modulos[$idM]['mostrar'] = true;
                $enlaces = $modulos[$idM]['enlaces'];
                $cant_e = 0;
                $cant_enlaces = count($enlaces);
                for ($idE = 0; $idE < $cant_enlaces; $idE++) { //ENLACE
                    $cant_enlaces_roles = count($modulos[$idM]["enlaces"][$idE]['roles']);
                    if($cant_enlaces_roles === 0) {
                        $modulos[$idM]["enlaces"][$idE]['mostrar'] = false;
                        $modulos[$idM]['mostrar'] = false;
                    } else {
                        $roles = $modulos[$idM]["enlaces"][$idE]['roles'];
                        $modulos[$idM]["enlaces"][$idE]['mostrar'] = $this->isGrantedRole($roles);
                        if(!$this->isGrantedRole($roles)){ $cant_e++; }
                    }
                }
                if(count($enlaces) == $cant_e){
                    $modulos[$idM]['mostrar'] = false;
                }
            }
        }
        $modulos[0]['mostrar'] = true; // para que el enlace de los modulos
//        $modulos[1]['mostrar'] = true; // para que el enlace de la información de la empresa se muestre

        return $modulos;
    }

    public function tieneAcceso($roles){
        $roles_sistema = $this->roleHierarchy->getReachableRoleNames($this->getUser()->getRoles());
        $i = array_intersect($roles_sistema, $roles);

        return !($i === []);
    }

    public function isGrantedRole($roles){
        foreach ($roles as $rol){
            if($this->isGranted($rol)){
                return true;
            }
        }
        return false;
    }

    public function arrayMakerDocumentoGeneral()
    {
        $em = $this->getDoctrine()->getManager();
        $tipo = $em->getRepository("App:EnlaceTipo")->findByIdentificador('documentos_generales');
        $enlaces = $em->getRepository("App:Enlace")->findBy(array('tipo' => $tipo));

        $a = array();
        foreach ($enlaces as $enlace) {
            $a[] = array('nombre' => $enlace->getNombre(), 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('documentogeneral_index', array('tipo' => $enlace->getEnlace())), 'icon' => $enlace->getIcon());
        }

        //if(in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
        $a[] = array('nombre' => 'Añadir tipo de documento', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('enlace_new', array('tipo' => 'documentos_generales')), 'icon' => 'add-outline');
        //}
        return $a;
    }

    /**
     * @Route("/install/", name="configuracion_inicial")
     */
    public function installAction()
    {
        return $this->render('default/install.html.twig');
    }

    /**
     * @Route("/menu-principal", name="main-menu")
     */
    public function mainMenuAction()
    {
        $modulos = $this->funcionalidadesAutorizadas();
        return $this->render('_mdb/mdb_sidebar_menu.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'modulos' => $modulos
        ]);
    }

    /**
     * @Route("/datos_primarios", name="datos_primarios")
     */
    public function datosPrimariosAction()
    {
        $datosPrimarios = $this->cargarDatosPrimarios();
        // replace this example code with whatever you need
        return $this->render('default/datosprimarios.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'datosPrimarios' => $datosPrimarios
        ]);
    }

    public function cargarDatosPrimarios()
    {
        /* DATOS PRIMARIOS */
        $datosPrimarios = array(
            'nombre' => 'Datos primarios',
            'id' => 'datosprimarios',
            'roles' => array(),
            'icon' => 'light-bulb',
            'grupos' => array(
                array(
                    'categoria' => 'Empresa',
                    'enlaces' => array(
                        array('nombre' => 'Áreas', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('area_index'), 'icon' => 'layers',
                            'descripcion' => 'Permite registrar las distintas áreas con la que cuenta la empresa, dígase UEB u otras dependencias. Para cada área se debe especificar área posee un director (o persona responsable), '),
                        array('nombre' => 'Departamentos', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('departamento_index', array('area_id' => 'todas')), 'icon' => 'layers',
                            'descripcion' => 'ggg'),
                        array('nombre' => 'Cargos', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('cargo_index'), 'icon' => 'layers',
                            'descripcion' => 'hhh'),
                        array('nombre' => 'Locales', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('local_index'), 'icon' => 'layers',
                            'descripcion' => 'iii')
                    )
                ),
                array(
                    'categoria' => 'Datos generales',
                    'enlaces' => array(
                        array('nombre' => 'Organismos', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('organismo_index'), 'icon' => 'layers',
                            'descripcion' => 'iii'),
                        array('nombre' => 'OSDEs', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('osde_index'), 'icon' => 'layers',
                            'descripcion' => 'iii')
                    )
                ),
                array(
                    'categoria' => 'Recursos Humanos',
                    'enlaces' => array(
                        array('nombre' => 'Categorías ocupacionales', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('categoriaocupacional_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                        array('nombre' => 'Estado del trabajador', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('trabajadorestado_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                        array('nombre' => 'Grados científicos', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('gradocientifico_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                        array('nombre' => 'Grupos escala', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('grupoescala_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                        array('nombre' => 'Indicador para coeficiente', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('coeficienteindicador_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                        array('nombre' => 'Motivo baja de trabajador', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('motivobajatrabajador_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                        array('nombre' => 'Motivo baja medio', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('motivobajamedio_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                        array('nombre' => 'Niveles educacionales', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('niveleducacional_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                        array('nombre' => 'Subindicador para coeficiente', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('coeficientesubindicador_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                        array('nombre' => 'Tipos de medidas disciplinarias', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('medidadisciplinariatipo_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                    )
                ),
                array(
                    'categoria' => 'Jurídica',
                    'enlaces' => array(
                        array('nombre' => 'Tipos de contratos', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('contratotipo_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                        array('nombre' => 'Categoría de contratos', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('contratocategoria_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                    )
                ),
                array(
                    'categoria' => 'Economía',
                    'enlaces' => array(
                        array('nombre' => 'Bancos', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('banco_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                        array('nombre' => 'Cuentas bancarias', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('cuentabancaria_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                    )
                ),
                array(
                    'categoria' => 'Energía y Transporte',
                    'enlaces' => array(
                        array('nombre' => 'Combustibles', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('combustible_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                    )
                ),
                array(
                    'categoria' => 'Operaciones',
                    'enlaces' => array(
                        array('nombre' => 'Tipos de productos', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('productotipo_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                    )
                ),
                array(
                    'categoria' => 'Informática y Comunicaciones',
                    'enlaces' => array(
                        array('nombre' => 'Plan de datos', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('plandatos_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                        array('nombre' => 'Planes móviles', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('planmovil_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                        array('nombre' => 'Tipos de equipos', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('equipotipo_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                        array('nombre' => 'Categoría de equipos', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('equipocategoria_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                        array('nombre' => 'Tipos de medio tecn.', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('mediotecnologicotipo_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                        array('nombre' => 'Tipos de componentes de medio tecn.', 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate('componentemttipo_index'), 'icon' => 'layers',
                            'descripcion' => ''),
                    )
                )
            )
        );

        return $datosPrimarios;
    }

    /**
     * @Route("/modulo/{id}", name="modulo")
     */
    public function moduloAction($id)
    {
        // replace this example code with whatever you need
        return $this->render('default/module.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'modulo' => $this->returnModule($id)
        ]);
    }

    public function returnModule($id)
    {
        $modulos = $this->cargarModulos();
        $key = $this->searchForId($id, $modulos);
        return $modulos[$key];
    }

    function searchForId($id, $array)
    {
        foreach ($array as $key => $val) {
            if ($val['id'] === $id) {
                return $key;
            }
        }
        return null;
    }

    /**
     * @Route("/error/", name="error")
     */
    public function errorAction(Request $request)
    {
        return $this->render('default/error.html.twig', array(
            'mensaje' => $request->cookies->get('mensaje'),
            'code' => $request->cookies->get('code') ? $request->cookies->get('code') : null,
            'detalle' => $request->cookies->get('detalle') ? $request->cookies->get('detalle') : null
        ));
    }

    /**
     * @Route("/acceso_denegado/", name="acceso_denegado")
     */
    public function accesoDenegadoAction(Request $request)
    {

        return $this->render('default/accesodenegado.html.twig', array(
            'mensaje' => $request->cookies->get('mensaje'),
            'code' => $request->cookies->get('code') ? $request->cookies->get('code') : null
        ));
    }

    public function subArrayMaker($var)
    {
        $em = $this->getDoctrine()->getManager();
        $enlaces = $em->getRepository("App:Enlace")->findAll();

        $a = array();
        foreach ($enlaces as $enlace) {
            $a[] = array('nombre' => $enlace->getNombre(), 'roles' => ['ROLE_USUARIO_ESTANDAR'], 'enlace' => $this->get('router')->generate($enlace->getEnlace()), 'icon' => $enlace->getIcon());
        }

        return $a;

    }

    /**
     * Deletes a multiple entities.
     *
     * @Route("/delete/multiple", name="entity_multipledelete")
     */
    public function multipleDeleteAction(Request $request)
    {
        $class = "App:" .
            $request->cookies->get('controller');
        $arr = $request->get('checkboxDelete');
        $keys = array_keys($arr);
        $no = array();
        foreach ($keys as $k) {
            $em = $this->getDoctrine()->resetManager();
            $entity = $em->getRepository($class)->find($k);
            try {
                $em->remove($entity);
                $em->flush();
            } catch (\Exception $e) {
                $no[] = $entity->getNombre() ? $entity->getNombre() : $entity->getId();
            }
        }
        if (count($no) == count($keys)) {
            $this->addFlash('danger', "Ningún elemento pudo ser eliminado. Existen entidades dependientes.");
        } else
            if (count($no) > 0) {
                $cant = (count($keys) - count($no));
                $this->addFlash('danger', "Fueron eliminados satisfactoriamente " . $cant . " elementos. Los siguientes no pudieron ser eliminados: " . implode(', ', $no)) . ".";
            } else {
                $this->addFlash('success', "Fueron eliminados satisfactoriamente " . count($keys) . " elementos");
            }
        return $this->redirect($request->headers->get('referer'));
    }

}

