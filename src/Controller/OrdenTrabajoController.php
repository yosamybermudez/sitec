<?php

namespace App\Controller;

use App\Entity\ComprobanteOperacion;
use App\Entity\Jornada;
use App\Entity\OperacionContable;
use App\Entity\OrdenTrabajo;
use App\Form\OrdenTrabajoType;
use App\Repository\OperacionContableRepository;
use App\Repository\OrdenTrabajoRepository;
use App\Repository\UsuarioRepository;
use App\Response\PdfResponse;
use App\Twig\Extension\FiltersExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("taller/orden/trabajo")
 */
class OrdenTrabajoController extends AbstractController
{
    /**
     * @Route("/", name="orden_trabajo_index", methods={"GET","POST"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function index(OrdenTrabajoRepository $ordenTrabajoRepository, Request $request): Response
    {
        $hoy = new \DateTime();
        // Filtrar fechas
        if($request->getMethod() === 'POST' && isset($request->request->all()["submit"]))
        {
            $req = $request->request->all();
            $params = '';
            $fecha_inicio = $req['fecha_inicio'] !== '' ? new \DateTime($req['fecha_inicio']) : null;
            $fecha_fin = $req['fecha_fin'] !== '' ? new \DateTime($req['fecha_fin']) : null;
            if($fecha_inicio and $fecha_fin){
                if($fecha_inicio->format('Ymd') !== $fecha_fin->format('Ymd')){
                    $params .= '?inicio=' . $fecha_inicio->format('Ymd') .'&fin=' . $fecha_fin->format('Ymd');
                } else {
                    $params .= '?fecha=' . $fecha_inicio->format('Ymd');
                }
            } else {
                if($fecha_inicio){
                    $params .= '?inicio=' . $fecha_inicio->format('Ymd');
                }
                if($fecha_fin){
                    $params .= '?fin=' . $fecha_fin->format('Ymd');
                }
            }
            return $this->redirect($this->generateUrl('orden_trabajo_index') . $params);
        } else {
            $req = $request->query;
            $fecha_param = $req->get('fecha');
            if(!$fecha_param){
                $fecha_inicio = $req->get('inicio') ? new \DateTime($req->get('inicio')) : null;
                $fecha_fin = $req->get('fin')? new \DateTime($req->get('fin')) : null;
            } else {
                $fecha_inicio = new \DateTime($fecha_param);
                $fecha_fin = new \DateTime($fecha_param);
            }
        }
        //
        $ordenes = $ordenTrabajoRepository->findByDates($fecha_inicio, $fecha_fin);

        $ingreso = 0;
        $gasto = 0;
        $ordenes_tipo = new ArrayCollection();
        $filter = new FiltersExtension();

        foreach ($ordenes as $orden){
            $ingreso += $orden->getOrdenReparacion() ? $orden->getOrdenReparacion()->getIngreso() : 0;
            $gasto += $orden->getOrdenReparacion() ? $orden->getOrdenReparacion()->getGastoMateriales() + $orden->getOrdenReparacion()->getOtrosGastos() : 0 ;
            if($orden->getOrdenReparacion() and $orden->getEstado() !== 'DEC'){
                if($orden->getOrdenReparacion()->getEstadoFinal() === 'R'){
                    $ordenes_tipo['Resuelto'] += 1;
                } else {
                    $ordenes_tipo['No resuelto'] += 1;
                }
            } else {
                if($orden->getEstado() === 'DEC' and $orden->getOrdenReparacion()){
                    $ordenes_tipo[$filter->traducirSiglas($orden->getOrdenReparacion()->getEstadoFinal()) . ' - Decomisado'] += 1;
                } else {
                    $ordenes_tipo[$filter->traducirSiglas($orden->getEstado())] += 1;
                }
            }
        }
        if($fecha_inicio === null and $fecha_fin !== null){
            $fecha = 'hasta el ' . $fecha_fin->format('d/m/Y');
        } elseif($fecha_inicio !== null && $fecha_fin === null){
            $fecha = 'desde el ' . $fecha_inicio->format('d/m/Y');
        } elseif($fecha_inicio === null && $fecha_fin === null){
            $fecha = 'Todas';
        }elseif($fecha_inicio->format('d/m/Y') === $fecha_fin->format('d/m/Y') and $fecha_inicio->format('d/m/Y') !== $hoy->format('d/m/Y')){
            $fecha = $fecha_inicio->format('d/m/Y');
        } elseif($fecha_inicio->format('d/m/Y') === $fecha_fin->format('d/m/Y') and $fecha_inicio->format('d/m/Y') === $hoy->format('d/m/Y')){
            $fecha = 'Hoy';
        } else {
            $fecha = $fecha_inicio->format('d/m/Y') . ' - ' . $fecha_fin->format('d/m/Y');
        }
        if(count($ordenes) > 0){
            $this->addFlash('success', 'Se listan correctamente ' . count($ordenes) . ' órdenes de trabajo');
        }  else {
            $this->addFlash('info', 'No hay órdenes de trabajo para el rango seleccionado');
        }
        return $this->render('orden_trabajo/index.html.twig', [
            'orden_trabajos' => $ordenes,
            'fecha' => $fecha,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'ingreso' => $ingreso,
            'gasto' => $gasto,
            'jornadaCerrada' => $this->jornadaCerrada(false),
            'chart_data' => $ordenes_tipo

        ]);

    }

//    public function index(OrdenTrabajoRepository $ordenTrabajoRepository, Request $request): Response
//    {
//        $form = $this->createForm('App\Form\ZFechaType',null,array('action' => $this->generateUrl('orden_trabajo_filtrar')));
//        $title = 'Todas las órdenes de trabajo';
////        $ordenesTrabajo = $ordenTrabajoRepository->findOrdenesIndex();
//        $ordenesTrabajo = $ordenTrabajoRepository->findBy(array(), array('nroOrden' => 'DESC'));
//        return $this->render('orden_trabajo/index.html.twig', [
//            'orden_trabajos' => $ordenesTrabajo,
//            'title' => $title,
//            'form' => $form->createView(),
//            'jornadaCerrada' => $this->jornadaCerrada(false)
//        ]);
//    }

    /**
     * @Route("/pendientes/tecnicos", name="orden_trabajo_pendientes_tecnicos", methods={"GET", "POST"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function pendientesTecnicos(UsuarioRepository $usuarioRepository): Response
    {
        $tecnicos = $usuarioRepository->findTecnicos();
        return $this->render('orden_trabajo/pendientesTecnicos.html.twig', [
            'tecnicos' => $tecnicos
        ]);
    }

    /**
     * @Route("/filtrar", name="orden_trabajo_filtrar", methods={"GET","POST"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function filtrar(OrdenTrabajoRepository $ordenTrabajoRepository, Request $request): Response
    {
        $form = $this->createForm('App\Form\ZFechaType',null);
        if ($request->getMethod() === 'GET') {
            $anno = $request->query->get('anno');
            $mes = $request->query->get('mes');
            $dia = $request->query->get('dia');
        } else {
            $fecha = explode('-',$request->get('z_fecha')['fechaBuscar']);
            $dia = $fecha[0];
            $mes = $fecha[1];
            $anno = $fecha[2];
            return $this->redirectToRoute('orden_trabajo_filtrar', array('anno' => $anno, 'mes' => $mes, 'dia' => $dia));
        }
        if($anno and  $mes and $dia){
            $str = $anno . "/" . $mes . "/" . $dia;
            $fecha = new \DateTime($str);
            $hoy = new \DateTime();
            $fechaInicio = clone $fecha;
            $fechaFin = $fecha->setTime(23,59,59);
            $ordenesTrabajo = $this->getDoctrine()->getRepository(OrdenTrabajo::class)->findByDates($fechaInicio, $fechaFin);
            if ($fecha->format('d/m/Y') === $hoy->format('d/m/Y')) {
                $title = "Órdenes del hoy";
            } else {
                $title = "Órdenes del " . $fecha->format('d/m/Y');
            }
        } elseif($anno and  $mes) {
            $fecha = new \DateTime($anno . "/" . $mes . "/15");
            $fechaInicioMes = $fecha->modify('first day of this month')->format('d-m-Y');
            $fechaFinMes = $fecha->modify('last day of this month')->format('d-m-Y');
            $ordenesTrabajo = $ordenTrabajoRepository->findByDates($fechaInicioMes, $fechaFinMes);
            $title = "Órdenes del mes " . $mes . "/" . $anno;
        } elseif($anno) {
            $fechaInicioAnno = $anno . "/01/01";
            $fechaFinAnno = $anno . "/12/31";
            $ordenesTrabajo = $ordenTrabajoRepository->findByDates($fechaInicioAnno, $fechaFinAnno);
            $title = "Órdenes del año " . $anno;
        }else {
            return $this->render('orden_trabajo/filtrarPorFecha.html.twig');
        }
        return $this->render('orden_trabajo/index.html.twig', [
            'orden_trabajos' => $ordenesTrabajo,
            'title' => $title,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/filtrar/fecha", name="orden_trabajo_filtrar_fecha", methods={"GET"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function filtrarOrdenesFecha(OrdenTrabajoRepository $ordenTrabajoRepository, Request $request): Response
    {
        $form = $this->createForm('App\Form\ZFechaType',null,array('action' => $this->generateUrl('orden_trabajo_filtrar')));
        return $this->render('orden_trabajo/formFechaSearch.html.twif', [
            'form' => $form->createView(),
            'title' => 'Buscar por fecha'
        ]);
    }

    /**
     * @Route("/reservar", name="orden_trabajo_reservar", methods={"GET"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function registrarReservacion(): Response
    {
        $form = $this->createForm('App\Form\ZFechaType',null,array('action' => $this->generateUrl('orden_trabajo_new')));
        return $this->render('orden_trabajo/formFechaSearch.html.twig', [
            'form' => $form->createView(),
            'title' => 'Especifique la fecha de la reservación',
            'registrar' => true
        ]);
    }

    /**
     * @Route("/new", name="orden_trabajo_new", methods={"GET","POST"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function new(Request $request): Response
    {
        $fechaBuscar = isset($request->request->get('z_fecha')['fechaBuscar']) ? $request->request->get('z_fecha')['fechaBuscar'] : null;
        $fechaBuscarStr = str_replace('/','-',$fechaBuscar);
        $garantia_orden_trabajo_id = $request->query->get('garantia_de');
        $ordenTrabajo = new OrdenTrabajo($this);
        if($garantia_orden_trabajo_id && $garantia_orden_trabajo_id !== ""){
            $ordenPrincipal = $this->getDoctrine()->getRepository(OrdenTrabajo::class)->find($garantia_orden_trabajo_id);
            if($ordenPrincipal->getGarantiaOrdenPrincipal()){
                $this->addFlash('danger', 'No puede hacer una Post-Garantía referenciando los datos de otra Post-Garantía');
                return $this->redirectToRoute('orden_trabajo_show', array('id' => $ordenPrincipal->getId()));
            }
            $fechaFinG = $ordenPrincipal->getFechaSalida()->modify('+'. $ordenPrincipal->getOrdenReparacion()->getDiasGarantia() . 'days');
            if(new \DateTime() > $fechaFinG){
                $this->addFlash('danger', 'Este equipo no tiene garantía');
                return $this->redirectToRoute('orden_trabajo_show', array('id' => $ordenPrincipal->getId()));
            }
            $ordenTrabajo->setGarantiaOrdenPrincipal($ordenPrincipal);
            $ordenTrabajo->setClienteCarneIdentidad($ordenPrincipal->getClienteCarneIdentidad());
            $ordenTrabajo->setClienteNombreCompleto($ordenPrincipal->getClienteNombreCompleto());
            $ordenTrabajo->setClienteTelefonosContacto($ordenPrincipal->getClienteTelefonosContacto());
            $ordenTrabajo->setTecnicoRepara($ordenPrincipal->getTecnicoRepara());
            $ordenTrabajo->setEquipoTipo($ordenPrincipal->getEquipoTipo());
            $ordenTrabajo->setEquipoMarca($ordenPrincipal->getEquipoMarca());
            $ordenTrabajo->setEquipoModelo($ordenPrincipal->getEquipoModelo());
            $ordenTrabajo->setGarantiaOrdenPrincipal($ordenPrincipal);
            $ordenTrabajo->setMotivoVisita('Post-garantía a equipo');
            $ordenTrabajo->setEsReparacion(TRUE);
        } elseif(!$garantia_orden_trabajo_id && $garantia_orden_trabajo_id === "") {
            $this->addFlash('danger', 'No puede crear una orden de garantía para una orden de trabajo que no existe.');
            return $this->redirectToReferer($request);
        }
        $nextNroOrden = $this->getDoctrine()->getRepository(OrdenTrabajo::class)->nextNroOrden($garantia_orden_trabajo_id, $fechaBuscar);
        $ordenTrabajo->setNroOrden($nextNroOrden);

        $fecha = new \DateTime($fechaBuscarStr);
        if($fechaBuscarStr) { $fecha->setTime(7,0,0); }
        $ordenTrabajo->setFechaEntrada($fecha);
        $form = $this->createForm(OrdenTrabajoType::class, $ordenTrabajo);
        if($fechaBuscar){
            $form->remove('tecnicoRepara');
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $hoy = new \DateTime();
                $hoy->setTime(0,0,0);
             $fecha = clone $ordenTrabajo->getFechaEntrada();
                $fecha->setTime(0,0,0);
            if($fecha > $hoy){ // Si la fecha de entrada despues de hoy, entonces
                $ordenTrabajo->setEstado('RES'); // Reservacion
            } else {
                $ordenTrabajo->setEstado('ESP'); // Esperar a ser atendido
            }
            $this->updatedData($ordenTrabajo);
            $entityManager->persist($ordenTrabajo);

            //Evento
                $texto = 'Registro de Orden de Trabajo # ' . $ordenTrabajo->getNroOrden()
                    . ($ordenTrabajo->getEstado() === 'RES' ? ' (reservación para ' . $ordenTrabajo->getFechaEntrada()->format('d/m/Y') . ')' : '');
                $this->registrarEvento('Registro','OrdenTrabajo', $ordenTrabajo->getId(), $texto);
            // Fin evento

            $entityManager->flush();
            $this->successNewElement();
            return $ordenTrabajo->getGarantiaOrdenPrincipal() !== null && $garantia_orden_trabajo_id && $garantia_orden_trabajo_id !== "" ?
                $this->redirectToRoute('dictamen_tecnico_new', array('id' => $ordenTrabajo->getId())) :
                $this->nextAction(
                $request,
                $this->generateUrl('orden_trabajo_new'),
                $this->generateUrl('orden_trabajo_show', array('id' => $ordenTrabajo->getId()))
            );
        }

        return $this->jornadaCerrada($fechaBuscar === null) === true && $fechaBuscar === null ? $this->redirectToReferer($request) : $this->render('orden_trabajo/new.html.twig', [
            'orden_trabajo' => $ordenTrabajo,
            'form' => $form->createView(),
            'multiple' => true,
            'referer' => $request->headers->get('referer'),
            'garantia' => ($garantia_orden_trabajo_id && $garantia_orden_trabajo_id !== "") ? true : false
        ]);
    }


    /**
     * @Route("/{id}", name="orden_trabajo_show", methods={"GET"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function show(OrdenTrabajo $ordenTrabajo): Response
    {
        $deleteForm = $this->createDeleteForm($ordenTrabajo);

        return $this->render('orden_trabajo/show.html.twig', [
            'orden_trabajo' => $ordenTrabajo,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @Route("/facturar/{id}", name="orden_trabajo_facturar", methods={"GET", "POST"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function facturar(OrdenTrabajo $ordenTrabajo, Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('importeEntregado')
            ->add('dejarEnTallerTemporalmente', CheckboxType::class, array(
                'required' => false
            ))
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $hoy = new \DateTime();
            $ingreso = $ordenTrabajo->getOrdenReparacion()->getIngreso();
            $gasto = $ordenTrabajo->getOrdenReparacion()->getGastoMateriales() + $ordenTrabajo->getOrdenReparacion()->getOtrosGastos();
            $nextNroOperacion = $this->getDoctrine()->getRepository(OperacionContable::class)->nextNroOperacion();
            $nextNroComprobante = $this->getDoctrine()->getRepository(ComprobanteOperacion::class)->nextNroComprobante();


            $jornada = $this->getDoctrine()->getRepository(Jornada::class)->findOneBy(array('fecha' => $hoy));

//            //Actualizar el monto recudado del tecnico
            $registroAsistencia = $em->getRepository('App\Entity\RegistroAsistencia')->findOneBy(array('jornada' => $jornada, 'tecnico' => $ordenTrabajo->getOrdenReparacion()->getRevisadoPor()), array('horaEntrada' => 'DESC'));
            $registroAsistencia->setMontoRecaudado($registroAsistencia->getMontoRecaudado() + $ingreso);
            $registroAsistencia->setGastoMateriales($registroAsistencia->getGastoMateriales() + $gasto);
            $this->updatedData($registroAsistencia);
            $em->persist($registroAsistencia);

            // Actualizar el ingreso y el gasto de la jornada
            $jornada->setFondoActual($jornada->getFondoActual() + $ingreso);
            $jornada->setGastoMateriales($jornada->getGastoMateriales() + $gasto);

            $operacionContable = new OperacionContable($this, $nextNroOperacion,'CR', 'Ingreso por orden de trabajo', $ingreso, 'Entrada de efectivo, por la Orden de Trabajo ' . $ordenTrabajo->getNroOrden());
            $comprobanteOperacion = new ComprobanteOperacion($this, $nextNroComprobante,$ordenTrabajo,'Cobro de servicio técnico',$ingreso,$form->get("importeEntregado")->getData());
            $comprobanteOperacion->setGastosAsociados($ordenTrabajo->getOrdenReparacion()->getGastoMateriales() + $ordenTrabajo->getOrdenReparacion()->getOtrosGastos());
            $estadoForm = $form->get("dejarEnTallerTemporalmente")->getData();
            if($estadoForm){
                $ordenTrabajo->setEstado('C');
            } else {
                $ordenTrabajo->setEstado('EAC');
                $ordenTrabajo->setFechaSalida(new \DateTime());
            }

            $ordenTrabajo->setFechaFacturacion(new \DateTime());
            $this->updatedData($jornada);
            $em->persist($jornada);
            $this->updatedData($operacionContable);
            $em->persist($operacionContable);
            //Evento
            $texto = 'Registro de Operacion Contable # ' . $operacionContable->getNroOperacion() . ' asociada a Orden de Trabajo # ' . $ordenTrabajo->getNroOrden();
            $this->registrarEvento('Registro','OperacionContable', $operacionContable->getId(), $texto);
            // Fin evento
            $this->updatedData($comprobanteOperacion);
            $em->persist($comprobanteOperacion);
            //Evento
            $texto = 'Registro de Comprobante de Operación # ' . $comprobanteOperacion->getNroComprobante() . ' asociada a Orden de Trabajo # ' . $ordenTrabajo->getNroOrden();
            $this->registrarEvento('Registro','ComprobanteOperacion', $ordenTrabajo->getId(), $texto);
            // Fin evento
            $this->updatedData($ordenTrabajo);
            $em->persist($ordenTrabajo);
            if($form->get("dejarEnTallerTemporalmente")->getData() == 'C'){
                $this->addFlash('success', 'Se cobró la orden. El equipo se queda en el taller temporalmente. Imprima el modelo de entrega.');
                //Evento
                $texto = 'Facturación de la Orden de Trabajo # ' . $ordenTrabajo->getNroOrden(). '. El equipo se deja en el taller temporalmente';
                $this->registrarEvento('Registro','OrdenTrabajo', $ordenTrabajo->getId(), $texto);
                // Fin evento
            } else {
                $this->addFlash('success', 'Se entregó el equipo al cliente. Imprima el modelo de entrega.');
                //Evento
                $texto = 'Facturación y entrega de Orden de Trabajo # ' . $ordenTrabajo->getNroOrden();
                $this->registrarEvento('Registro','OrdenTrabajo', $ordenTrabajo->getId(), $texto);
                // Fin evento
            }
            $em->flush();
            return $this->redirectToRoute('comprobante_operacion_show', array('id' => $comprobanteOperacion->getId()));
        }
        return $this->render('orden_trabajo/facturar.html.twig', [
            'orden_trabajo' => $ordenTrabajo,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/indemnizar/{id}", name="orden_trabajo_indemnizar", methods={"GET", "POST"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function indemnizar(OrdenTrabajo $ordenTrabajo, Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('enviar', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $nroOperacion = $em->getRepository(OperacionContable::class)->nextNroOperacion();
            $importeADevolver = $ordenTrabajo->getGarantiaOrdenPrincipal()->getOrdenReparacion()->getIngreso();
            $operacionContable = new OperacionContable($this, $nroOperacion,'DB','Devolución de importe al cliente',$importeADevolver,'Salida de efectivo por devolución al cliente. Post-garantía no solucionada. Orden de Trabajo # ' . $ordenTrabajo->getNroOrden());

            $ordenTrabajo->setObservacionesFinales('Devolución de importe por post-garantía no solucionada');
            $ordenTrabajo->setEstado('EAC');
            $ordenTrabajo->setFechaSalida(new \DateTime());
            //Evento
            $texto = 'Devolución de importe por post-garantía no solucionada. Orden de Trabajo # ' . $ordenTrabajo->getNroOrden();
            $this->registrarEvento('Modificación','OrdenTrabajo', $ordenTrabajo->getId(), $texto);
            // Fin evento
            $this->updatedData($operacionContable);
            $em->persist($operacionContable);
            //Evento
            $texto = 'Registro de Operacion Contable # ' . $operacionContable->getNroOperacion() . 'asociada a Orden de Trabajo #' . $ordenTrabajo->getNroOrden(). '. Post-garantía no solucionada. Devolución de importe';
            $this->registrarEvento('Registro','OperacionContable', $operacionContable->getId(), $texto);
            // Fin evento
            $this->updatedData($ordenTrabajo);
            $em->persist($ordenTrabajo);
            $this->addFlash('success', 'Se entregó el equipo al cliente. Se registró la operación de devolución de importe.');
            $em->flush();
            return $this->redirectToRoute('jornada_show_hoy');
        }
        return $this->render('orden_trabajo/indemnizar.html.twig', [
            'orden_trabajo' => $ordenTrabajo,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/cancelar/{id}", name="orden_trabajo_cancelar", methods={"GET"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function cancelar(OrdenTrabajo $ordenTrabajo, Request $request): Response {
        $em = $this->getDoctrine()->getManager();
        $ordenTrabajo->setEstado('CANC');
        $this->updatedData($ordenTrabajo);
        $em->persist($ordenTrabajo);
        //Evento
        $texto = 'Cancelación de Orden de Trabajo # ' . $ordenTrabajo->getNroOrden();
        $this->registrarEvento('Modificación','OrdenTrabajo', $ordenTrabajo->getId(), $texto);
        // Fin evento
        $em->flush();

        $this->addFlash('success', 'La orden seleccionada ha sido cancelada');
        return $this->redirectToReferer($request);
    }

    /**
     * @Route("/dejar_en_taller/{id}", name="orden_trabajo_dejar_en_taller", methods={"GET", "POST"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function dejarEnTaller(OrdenTrabajo $ordenTrabajo, Request $request): Response {
        $em = $this->getDoctrine()->getManager();
        $ordenTrabajo->setEstado('DT');
        $ordenTrabajo->setDejadoEnTaller(TRUE);
        $dictamenTecnico = $ordenTrabajo->getDictamenTecnico();
        $dictamenTecnico->setDejarEnTaller(true);
        $dictamenTecnico->setDictamen('DT');
        $this->updatedData($ordenTrabajo);
        $em->persist($ordenTrabajo);
        //Evento
        $texto = 'Se queda en taller el equipo relacionado con la Orden de Trabajo # ' . $ordenTrabajo->getNroOrden();
        $this->registrarEvento('Modificación','OrdenTrabajo', $ordenTrabajo->getId(), $texto);
        // Fin evento
        $this->updatedData($dictamenTecnico);
        $em->persist($dictamenTecnico);
        $em->flush();
        $this->addFlash('success', 'El equipo se queda en el taller');
        return $this->redirectToReferer($request);
    }

    /**
     * @Route("/{id}/edit", name="orden_trabajo_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function edit(Request $request, OrdenTrabajo $ordenTrabajo): Response
    {
        $ot = clone $ordenTrabajo;
        $form = $this->createForm(OrdenTrabajoType::class, $ordenTrabajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($ordenTrabajo->getEstado() === 'AOT'){
                if($ot->getTecnicoRepara()->getId() === $ordenTrabajo->getTecnicoRepara()->getId()){
                    $this->addFlash('danger', 'El técnico seleccionado solicitó que la orden se asignara a otro');
                    return $this->redirectToRoute('orden_trabajo_edit', array('id' => $ordenTrabajo->getId()));
                }
                $ordenTrabajo->setEstado('ECT');
                $this->updatedData($ordenTrabajo);
                $this->getDoctrine()->getManager()->persist($ordenTrabajo);
            }
            //Evento
            $texto = 'Modificación de la Orden de Trabajo # ' . $ordenTrabajo->getNroOrden();
            $this->registrarEvento('Modificación','OrdenTrabajo', $ordenTrabajo->getId(), $texto);
            // Fin evento
            $this->updatedData($ordenTrabajo);
            $this->getDoctrine()->getManager()->flush();
            $this->successEditElement();
            return $this->redirectToRoute('jornada_show_hoy');
        }

        return $this->jornadaCerrada() ? $this->redirectToReferer($request) : $this->render('orden_trabajo/new.html.twig', [
            'orden_trabajo' => $ordenTrabajo,
            'form' => $form->createView(),
            'referer' => $request->headers->get('referer')
        ]);
    }

    /**
     * @Route("/{id}", name="orden_trabajo_delete", methods={"POST"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function delete(Request $request, OrdenTrabajo $ordenTrabajo): Response
    {
        $form = $this->createDeleteForm($ordenTrabajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ordenTrabajo);
            try{
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al eliminar. Existen elementos dependientes.');
                return $this->redirect($request->headers->get('referer'));
            }
            $this->addFlash('success', 'Elemento eliminado satisfactoriamente.');
        } else {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(OrdenTrabajo::class)->find($request->get('id'));
            $em->remove($entity);
            try{
                //Evento
                $texto = 'Eliminación de la Orden de Trabajo # ' . $ordenTrabajo->getNroOrden();
                $this->registrarEvento('Eliminación','OrdenTrabajo', $ordenTrabajo->getId(), $texto);
                // Fin evento
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al eliminar. Existen elementos dependientes.');
                return $this->redirect($request->headers->get('referer'));
            }
            $this->addFlash('success', 'Elemento eliminado satisfactoriamente.');
        }

        return $this->redirectToRoute('orden_trabajo_index');
    }

    /**
     * Creates a form to delete an entity.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(OrdenTrabajo $ordenTrabajo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('orden_trabajo_delete', array('id' => $ordenTrabajo->getId())))
            ->setMethod('POST')
            ->getForm()
            ;
    }

    /**
 * @Route("/equipos/entregar", name="orden_trabajo_equipo_entregar", methods={"GET"})
 * @IsGranted("ROLE_RECEPCIONISTA")
 */
    public function equiposEntregar(OrdenTrabajoRepository $ordenTrabajoRepository, Request $request): Response
    {
        $ordenesTrabajo = $ordenTrabajoRepository->findBy(array('estado' => 'LE'), array('nroOrden' => 'ASC'));
        return $this->render('orden_trabajo/equiposEntregar.html.twig', [
            'orden_trabajos' => $ordenesTrabajo
        ]);
    }

    /**
     * @Route("/equipos/decomisar", name="orden_trabajo_equipo_decomisar", methods={"GET"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function equiposDecomisar(OrdenTrabajoRepository $ordenTrabajoRepository, Request $request): Response
    {
        $fechaLimite = new \DateTime();
        $fechaLimite = $fechaLimite->modify("- 60 days");
        $ordenesTrabajo = $ordenTrabajoRepository->findTrabajosADecomisar($fechaLimite);
        return $this->render('orden_trabajo/equiposDecomisar.html.twig', [
            'orden_trabajos' => $ordenesTrabajo
        ]);
    }

    /**
     * @Route("/equipos/entregar/{id}", name="orden_trabajo_entregar_equipo", methods={"GET", "POST"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function entregarEquipo(Request $request, OrdenTrabajo $ordenTrabajo): Response
    {
        $form = $this->createFormBuilder()
            ->add('observaciones', TextType::class, array(
                'required' => false
            ))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $ordenTrabajo->setEstado('EAC');
            if($form->get('observaciones')->getData()){
                $ordenTrabajo->setObservaciones($form->get('observaciones')->getData());
            }
            $ordenTrabajo->setFechaSalida(new \DateTime());
            $this->updatedData($ordenTrabajo);
            $em->persist($ordenTrabajo);
            //Evento
            $texto = 'Entrega del equipo asociado a la Orden de Trabajo # ' . $ordenTrabajo->getNroOrden(). ' (' . $ordenTrabajo->getOrdenReparacion()->getEstadoFinal() . ')';
            $this->registrarEvento('Modificación','OrdenTrabajo', $ordenTrabajo->getId(), $texto);
            // Fin evento
            $em->flush();
            $this->addFlash('success', 'Se entregó el equipo al cliente. Imprima el modelo de entrega.');
            return $this->redirectToRoute('jornada_show_hoy');
        }
        return $this->jornadaCerrada() ? $this->redirectToReferer($request) : $this->render('orden_trabajo/entregarEquipo.html.twig', [
            'orden_trabajo' => $ordenTrabajo,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/equipos/modelo/entrega/{id}", name="orden_trabajo_modelo_entrega", methods={"GET", "POST"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function modeloEntregaEquipo(OrdenTrabajo $ordenTrabajo): Response
    {
        $html = $this->renderView('orden_trabajo/modeloEntregaEquipo.html.twig',
            array(
                'orden_trabajo' => $ordenTrabajo
            ));
        $namefile = 'ORDEN DE TRABAJO # ' . $ordenTrabajo->getNroOrden();
        $knpSnappy = new Pdf($this->getParameter('kernel.project_dir') . '/config/wkhtmltopdf/bin/wkhtmltopdf.exe');
        $a = 2;
        return $a === 1 ? $this->render('orden_trabajo/modeloEntregaEquipo.html.twig',
            array(
                'orden_trabajo' => $ordenTrabajo
            )) :
        new PdfResponse(
            $knpSnappy->getOutputFromHtml($html,
                array(
                    'lowquality' => false,
                    'print-media-type' => true,
                    'encoding' => 'utf-8',
                    'enable-local-file-access' => true,
                    'page-size' => 'Letter',
                    'outline-depth' => 1,
                    'orientation' => 'Portrait',
                    'title' => $namefile,
                    'margin-top' => 10
                )
            ),
            $namefile.'.pdf'
        );
    }

    /**
     * @Route("/equipos/notificar/{id}", name="orden_trabajo_notificar_cliente", methods={"GET", "POST"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function notificarCliente(Request $request, OrdenTrabajo $ordenTrabajo): Response
    {
        $em = $this->getDoctrine()->getManager();
//        dd($request->headers->get('referer'));
        $fechaNotificacion = new \DateTime();
        $ordenTrabajo->setFechaNotificacion($fechaNotificacion);
        $ordenTrabajo->setEstado('N');
        $this->updatedData($ordenTrabajo);
        $em->persist($ordenTrabajo);
        //Evento
        $texto = 'Notificación del cliente que inicia la Orden de Trabajo # ' . $ordenTrabajo->getNroOrden();
        $this->registrarEvento('Modificación','OrdenTrabajo', $ordenTrabajo->getId(), $texto);
        // Fin evento
        $em->flush();
        $this->addFlash('success', 'Se ha notificado al cliente. Fecha y hora: ' . $fechaNotificacion->format('d-m-Y h:i:s a'));
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/equipos/decomisar/{id}", name="orden_trabajo_decomisar_equipo", methods={"GET", "POST"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function decomisarEquipo(Request $request, OrdenTrabajo $ordenTrabajo): Response
    {
        $ahora = new \DateTime();
        $diff = $ahora->diff($ordenTrabajo->getFechaEntrada())->days;
        if($request->getMethod() === 'POST') {
            if ($diff >= 60) {
                $ordenTrabajo->setEstado('DEC');
                $ordenTrabajo->setFechaDecomiso(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $this->updatedData($ordenTrabajo);
                $em->persist($ordenTrabajo);
                //Evento
                $texto = 'Decomiso del equipo asociado a la Orden de Trabajo # ' . $ordenTrabajo->getNroOrden() . ' por exceder el tiempo de permanencia en el taller.';
                $this->registrarEvento('Modificación','OrdenTrabajo', $ordenTrabajo->getId(), $texto);
                // Fin evento
                $this->updatedData($ordenTrabajo);
                $em->flush();
                $this->addFlash('success', 'Se ha decomisado el equipo');
                return $this->redirectToReferer($request);
            } else {
                $this->addFlash('danger', 'El equipo lleva menos de 60 días en taller. No es posible decomisarlo');
                return $this->redirectToReferer($request);
            }
        }
        return $this->render('orden_trabajo/decomisarEquipo.html.twig', [
            'orden_trabajo' => $ordenTrabajo
        ]);
    }

    /**
     * @Route("/exportar/{tipo}/rango", name="orden_trabajo_exportar_pdfexcel_rango", methods={"GET", "POST"})
     */
    public function exportarPDFExcel(string $tipo, OrdenTrabajoRepository $ordenTrabajoRepository, Request $request): Response
    {
        $req = $request->query;
        $hoy = new \DateTime();
        $fecha_param = $req->get('fecha');
        if (!$fecha_param) {
            $fecha_inicio = $req->get('inicio') ? new \DateTime($req->get('inicio')) : null;
            $fecha_fin = $req->get('fin') ? new \DateTime($req->get('fin')) : null;
        } else {
            $fecha_inicio = new \DateTime($fecha_param);
            $fecha_fin = new \DateTime($fecha_param);
        }
        //
        $ordenes = $ordenTrabajoRepository->findByDates($fecha_inicio, $fecha_fin);

        $ingreso = 0;
        $gasto = 0;
        foreach ($ordenes as $orden){
            $ingreso += $orden->getOrdenReparacion() ? $orden->getOrdenReparacion()->getIngreso() : 0;
            $gasto += $orden->getOrdenReparacion() ? $orden->getOrdenReparacion()->getGastoMateriales() + $orden->getOrdenReparacion()->getOtrosGastos() : 0 ;
        }
        if ($fecha_inicio === null and $fecha_fin !== null) {
            $fecha = 'hasta el ' . $fecha_fin->format('d/m/Y');
        } elseif ($fecha_inicio !== null && $fecha_fin === null) {
            $fecha = 'desde el ' . $fecha_inicio->format('d/m/Y');
        } elseif ($fecha_inicio === null && $fecha_fin === null) {
            $fecha = 'Todas';
        } elseif ($fecha_inicio->format('d/m/Y') === $fecha_fin->format('d/m/Y') and $fecha_inicio->format('d/m/Y') !== $hoy->format('d/m/Y')) {
            $fecha = $fecha_inicio->format('d/m/Y');
        } elseif ($fecha_inicio->format('d/m/Y') === $fecha_fin->format('d/m/Y') and $fecha_inicio->format('d/m/Y') === $hoy->format('d/m/Y')) {
            $fecha = 'Hoy';
        } else {
            $fecha = $fecha_inicio->format('d/m/Y') . ' - ' . $fecha_fin->format('d/m/Y');
        }

        if ($tipo === 'pdf') {

            $options = [
                'orden_trabajos' => $ordenes,
                'fecha' => $fecha,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'ingreso' => $ingreso,
                'gasto' => $gasto,
                'jornadaCerrada' => $this->jornadaCerrada(false),
            ];

            $renderView = $this->renderView('orden_trabajo/indexPDF.html.twig', $options);
            $render = $this->render('orden_trabajo/indexPDF.html.twig', $options);

            $namefile = strtoupper('Ordenes_Trabajo_Fecha: ' . $fecha);
            $namefile = str_replace(['/', ' - '], ['_', '-'], $namefile);

            return $this->imprimirDocumentoGeneral($renderView, $render, $namefile, true, 'Landscape');
        } elseif ($tipo === 'excel') {
            $filter = new FiltersExtension();
            $data = [];
            $columnNames = ['Fecha', 'No. Orden', 'Datos del cliente', 'Datos del equipo', 'Motivo visita', 'Estado', 'Ingreso', 'Gasto'];
            foreach ($ordenes as $orden) {
                $data[] = [
                    $orden->getFechaEntrada()->format('d/m/Y h:i:s a'),
                    $orden->getNroOrden(),
                    $orden->getDatosCliente(),
                    $orden->getDatosEquipo(),
                    $orden->getMotivoVisita(),
                    $filter->traducirSiglas($orden->getEstado()),
                    $orden->getOrdenReparacion() ? $orden->getOrdenReparacion()->getIngreso() : 0,
                    $orden->getOrdenReparacion() ? $orden->getOrdenReparacion()->getGastoMateriales() + $orden->getOrdenReparacion()->getOtrosGastos() : 0,

                ];
            }
            $namefile = strtoupper('ORDENES_TRABAJO_' . $fecha);
            $namefileR = str_replace(['/', ' - ', ':', ' '], ['', '_', '', '_'], $namefile);
            return $this->exportarExcel($namefileR, 'H', $columnNames, $data, $namefileR);
        } else {
            $this->addFlash('danger', 'No es una ruta válida');
            return $this->redirectToReferer($request);
        }
    }

}