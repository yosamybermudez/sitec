<?php

namespace App\Controller;

use App\Entity\Jornada;
use App\Entity\MateriaPrima;
use App\Entity\MateriaPrimaMovimiento;
use App\Entity\OrdenReparacion;
use App\Entity\OrdenTrabajo;
use App\Entity\RegistroAsistencia;
use App\Form\MateriaPrimaMovimientoType;
use App\Form\OrdenReparacionType;
use App\Repository\OrdenReparacionRepository;
use App\Twig\Extension\FiltersExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("taller/orden/reparacion")
 */
class OrdenReparacionController extends AbstractController
{
    /**
     * @Route("/", name="orden_reparacion_index", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_TECNICO') or is_granted('ROLE_RECEPCIONISTA')")
     */
    public function index(OrdenReparacionRepository $ordenReparacionRepository, Request $request): Response
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
            return $this->redirect($this->generateUrl('orden_reparacion_index') . $params);
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
        $result = $ordenReparacionRepository->findByDates($fecha_inicio, $fecha_fin);
        $filter = new FiltersExtension();
        $ingreso = 0;
        $gasto = 0;
        $reparaciones_tipo = new ArrayCollection();
        foreach ($result as $item){
            $ingreso += $item->getIngreso();
            $gasto += ($item->getGastoMateriales() + $item->getOtrosGastos());
            $reparaciones_tipo[$filter->traducirSiglas($item->getEstadoFinal())] += 1;

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
        if(count($result) > 0){
            $this->addFlash('success', 'Se listan correctamente ' . count($result) . ' diagnósticos de reparación');
        }  else {
            $this->addFlash('info', 'No hay diagnósticos de reparación para el rango seleccionado');
        }
        return $this->render('orden_reparacion/index.html.twig', [
            'orden_reparacions' => $result,
            'chart_data' => $reparaciones_tipo,
            'fecha' => $fecha,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'ingreso' => $ingreso,
            'gasto' => $gasto,
        ]);
    }


    /**
     * @Route("/trabajos_realizados", name="orden_reparacion_trabajos_realizados", methods={"GET"})
     * @IsGranted("ROLE_TECNICO")
     */
    public function showMisTrabajosRealizados(OrdenReparacionRepository $ordenReparacionRepository): Response
    {

        return $this->render('orden_reparacion/trabajosRealizados.html.twig', [
            'orden_reparacions' => $ordenReparacionRepository->findBy(array('revisadoPor' => $this->getDatabaseUser()->getId()), array('created' => 'ASC')),
        ]);
    }

    /**
     * @Route("/trabajos_realizados/imprimir", name="orden_reparacion_trabajos_realizados_imprimir", methods={"GET"})
     * @IsGranted("ROLE_TECNICO")
     */
    public function showMisTrabajosRealizadosImprimir(OrdenReparacionRepository $ordenReparacionRepository): Response
    {
        $renderView = $this->renderView('orden_reparacion/trabajosRealizadosPDF.html.twig', [
            'orden_reparacions' => $ordenReparacionRepository->findBy(array('revisadoPor' => $this->getDatabaseUser()->getId()), array('created' => 'ASC')),
        ]);

        $render = $this->render('orden_reparacion/trabajosRealizadosPDF.html.twig', [
            'orden_reparacions' => $ordenReparacionRepository->findBy(array('revisadoPor' => $this->getDatabaseUser()->getId()), array('created' => 'ASC')),
        ]);

        return $this->imprimirDocumentoGeneral($renderView, $render, 'REPORTE DE TRABAJOS REALIZADOS - ' . strtoupper($this->getDatabaseUser()->getNombreCompleto()),true);
    }

    /**
     * @Route("/registrar/materiales_usados/{id}", name="orden_reparacion_materiales_usados", methods={"GET", "POST"})
     * @IsGranted("ROLE_TECNICO")
     */
    public function materialesUsados(OrdenReparacion $ordenReparacion, Request $request): Response
    {
        if($request->getMethod() === 'POST'){
            $movMateriasPrimasArray = $request->request->get('orden_reparacion')['movimientosMateriaPrima'];
            $em = $this->getDoctrine()->getManager();
            foreach ($movMateriasPrimasArray as $mmp){
                $materiaPrima = $em->getRepository(MateriaPrima::class)->find($mmp['materiaPrima']);
                $movimiento = new MateriaPrimaMovimiento($this);
                $movimiento->setOrdenreparacion($ordenReparacion);
                $movimiento->setTipo('S');
                $movimiento->setCantidad($mmp['cantidad']);
                $movimiento->setMateriaPrima($materiaPrima);
                $movimiento->setCantidadRestante($materiaPrima->getCantidad() - $mmp['cantidad']);
                $materiaPrima->setCantidad($materiaPrima->getCantidad() - $mmp['cantidad']);
                $this->updatedData($materiaPrima);
                $em->persist($materiaPrima);
                $this->updatedData($movimiento);
                $em->persist($movimiento);
            }
            $em->flush();
            $this->addFlash('success', 'Se registraron los materiales satisfactoriamente');
            return $this->redirectToRoute('dictamen_tecnico_trabajos_pendientes');

        }
        //$mov1 = new MateriaPrimaMovimiento($this);
       // $ordenReparacion->getMovimientosMateriaPrima()->add($mov1);
        $materiasPrimas = $this->getDoctrine()->getRepository(MateriaPrima::class)->findAllEnExistencia();
        $form = $this->createForm(OrdenReparacionType::class, $ordenReparacion);
//        $form->handleRequest($request);
//        if($form->isSubmitted() && $form->isValid()){
//            dd($ordenReparacion);
//        }

        return $this->render('orden_reparacion/materialesUsados.html.twig', [
           // 'orden_reparacions' => $ordenReparacionRepository->findBy(array('createdBy' => $this->getDatabaseUser()->getId()), array('created' => 'DESC')),
            'form' => $form->createView(),
            'orden_reparacion' => $ordenReparacion,
            'cantidad_mp' => count($materiasPrimas)
        ]);
    }

    /**
     * @Route("/new", name="orden_reparacion_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_RECEPCIONISTA') or is_granted('ROLE_TECNICO')")
     */
    public function new(Request $request): Response
    {
        $ordenTrabajoId = $request->query->get('id');
        if($ordenTrabajoId) {
            $entityManager = $this->getDoctrine()->getManager();
            $ordenTrabajo = $this->getDoctrine()->getRepository(OrdenTrabajo::class)->find($ordenTrabajoId);
            $jornada = $this->getDoctrine()->getRepository(Jornada::class)->findOneBy(array('fecha' => new \DateTime()));
            $tecnico = $this->isGranted('ROLE_TECNICO') ? $this->getDatabaseUser() : $ordenTrabajo->getTecnicoRepara();
            $registroAsistencia = $this->getDoctrine()->getRepository(RegistroAsistencia::class)->findOneBy(array('jornada' => $jornada, 'tecnico' => $tecnico),array('horaEntrada' => 'DESC'));
            if($registroAsistencia === null){
                $this->addFlash('danger', 'No puede registrar una reparación de un técnico que no se encuentra en el taller');
                return $this->redirectToReferer($request);
            }
            if($registroAsistencia->getHoraSalida() != null){
                $this->addFlash('danger', 'Se ha registrado una hora de salida para usted (' . $registroAsistencia->getHoraSalida()->format("d-m-A h:i:s a") . '). No puede revisar la orden. Infórmele a la recepcionista que le de entrada en el sistema.');
                return $this->redirect($request->headers->get('referer'));
            }
            if($ordenTrabajo->getOrdenReparacion()){
                $this->addFlash('danger', 'Ya la orden fue revisada por un técnico.');
                return $this->redirectToRoute('orden_reparacion_index');
            }
            /// poner todas las ordenes del tecnico EN COLA PARA EL TECNICO
            /// excepto la que esta abierta en el instante
            $ordenesTrabajo = $this->getDoctrine()->getRepository(OrdenTrabajo::class)->findTrabajosPendientesTecnico($this->getDatabaseUser());
            foreach ($ordenesTrabajo as $obj){
                if($obj->getDictamenTecnico()->getDictamen() === 'NPN' && $obj->getDictamenTecnico()->getDejarEnTaller()){
                    $obj->setEstado('EP');
                } elseif($obj->getDictamenTecnico()->getDictamen() === 'DT'){
                    $obj->setEstado('DT');
                } else {
                    $obj->setEstado('ECT');
                }
                if($obj->getId() === $ordenTrabajoId ){
                    $obj->setEstado('TR');
                }
                $this->updatedData($obj);
                $entityManager->persist($obj);
            }
            $entityManager->flush();
            //
            $ordenReparacion = new OrdenReparacion($this);
            $ordenReparacion->setOrdenTrabajo($ordenTrabajo);
            $ordenReparacion->setCreated(new \DateTime());
            $form = $this->createForm(OrdenReparacionType::class, $ordenReparacion);
            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {
                $movimientos = $ordenReparacion->getMovimientosMateriaPrima();
                $gastoTotal = 0;
                $materialesUsados = "";
                $index = 0;
                foreach ($movimientos as $mov){
                    if($mov->getCantidad() > 0){
                        $gastoTotal += $mov->getCantidad() * $mov->getMateriaPrima()->getPrecio();
                        if (!$mov->getCreatedBy()) {
                            $mov->setCreatedBy($ordenReparacion->getCreatedBy());
                        }
                        if (!$mov->getUpdatedBy()) {
                            $mov->setUpdatedBy($ordenReparacion->getUpdatedBy());
                        }
                        $mov->setOrdenreparacion($ordenReparacion);
                        $mov->setTipo('S');
                        $materiaPrima = $mov->getMateriaPrima();
                        $mov->setCantidadRestante($materiaPrima->getCantidad() - $mov->getCantidad());
                        $materiaPrima->setCantidad($materiaPrima->getCantidad() - $mov->getCantidad());
                        $materialesUsados .= ($index !== 0 ? ', ' : '') . $mov->getCantidad() . " " . $mov->getMateriaPrima()->getUnidadMedida() . " x " . $mov->getMateriaPrima()->getNombrePrecio();

                        $nextNroMovimiento = $this->getDoctrine()->getRepository(MateriaPrimaMovimiento::class)->nextNroMovimiento();
                        $mov->setNroMovimiento($nextNroMovimiento);
                        $this->updatedData($mov);
                        $entityManager->persist($mov);
                        $this->updatedData($materiaPrima);
                        $entityManager->persist($materiaPrima);
                        $index++;
                    } else {
                        $ordenReparacion->removeMovimientosMateriaPrima($mov);
                    }
                }
                $ordenReparacion->setGastoMateriales($gastoTotal);
                $ordenReparacion->setMaterialesUsados($materialesUsados);
                if($this->isGranted('ROLE_TECNICO')){
                    $ordenReparacion->setRevisadoPor($this->getDatabaseUser());
                } else {
                    $ordenReparacion->setRevisadoPor($ordenReparacion->getOrdenTrabajo()->getTecnicoRepara());
                }
                if($ordenReparacion->getCreated() < $ordenTrabajo->getFechaEntrada()){
                    $this->addFlash('danger', 'La fecha de reparación no puede ser antes de la fecha de entrada.');
                    return $this->render('orden_reparacion/new.html.twig', [
                        'orden_reparacion' => $ordenReparacion,
                        'form' => $form->createView(),
                        'multiple' => true,
                        'referer' => $request->headers->get('referer')
                    ]);
                }
                if($ordenReparacion->getEstadoFinal() === 'NR' and $ordenTrabajo->getEsReparacion() ){
                    $ordenTrabajo->setObservacionesFinales($request->get('orden_reparacion')['observacionesFinales']);
                    $ordenReparacion->setDiasGarantia(0);
                } elseif($ordenReparacion->getEstadoFinal() === 'NR' and !$ordenTrabajo->getEsReparacion() ) {
                    $ordenTrabajo->setObservacionesFinales($ordenReparacion->getObservaciones());
                    $ordenReparacion->setDiasGarantia(0);
                } else{
                    $ordenTrabajo->setObservacionesFinales('R');
                    $ordenReparacion->setDiasGarantia($ordenReparacion->getDiasGarantia() ?? 0 );
                }
                $ordenTrabajo->setFechaRevision(new \DateTime());
                $ordenTrabajo->setEstado('LE');
                $ordenTrabajo->setFechaListoEntregar(new \DateTime());
                $this->updatedData($ordenTrabajo);
                $entityManager->persist($ordenTrabajo);
                $this->updatedData($ordenReparacion);
                $entityManager->persist($ordenReparacion);
                //Evento
                $texto = 'Registro de Orden de Reparación asociada a la Orden de Trabajo # ' . $ordenTrabajo->getNroOrden() . ' (' . $ordenReparacion->getEstadoFinal() . ')';
                $this->registrarEvento('Registro','OrdenReparacion', $ordenReparacion->getId(), $texto);
                // Fin evento
                $entityManager->flush();
                $this->successNewElement();
                return $this->isGranted('ROLE_TECNICO') ? $this->redirectToRoute('dictamen_tecnico_trabajos_pendientes') : $this->redirectToRoute('jornada_show_hoy');
            }
            $materiasPrimas = $this->getDoctrine()->getRepository(MateriaPrima::class)->findAllEnExistencia();
            return $this->render('orden_reparacion/new.html.twig', [
                'orden_reparacion' => $ordenReparacion,
                'form' => $form->createView(),
                'referer' => $request->headers->get('referer'),
                'cantidad_mp' => count($materiasPrimas),
                'materiales_existencias' => $materiasPrimas
            ]);
        } else {
            $this->addFlash('danger', 'Ha ocurrido un error');
            return $this->redirectToRoute('dictamen_tecnico_trabajos_pendientes');
        }
    }

    /**
     * @Route("/{id}", name="orden_reparacion_show", methods={"GET"})
     * @Security("is_granted('ROLE_TECNICO') or is_granted('ROLE_RECEPCIONISTA')")
     */
    public function show(OrdenReparacion $ordenReparacion): Response
    {
        $deleteForm = $this->createDeleteForm($ordenReparacion);
        return $this->render('orden_reparacion/show.html.twig', [
            'orden_reparacion' => $ordenReparacion,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="orden_reparacion_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_TECNICO') or is_granted('ROLE_ADMINISTRACION')")
     */
    public function edit(Request $request, OrdenReparacion $ordenReparacion): Response
    {
        if($ordenReparacion->getOrdenTrabajo()->getFechaFacturacion()){
            $this->addFlash('danger','No puede editar esta orden. La orden de trabajo asociada ya ha sido facturada');
            return $this->redirectToRoute('orden_reparacion_index');
        }

        $form = $this->createForm(OrdenReparacionType::class, $ordenReparacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ordenTrabajo = $this->getDoctrine()->getRepository(OrdenTrabajo::class)->find($ordenReparacion->getOrdenTrabajo()->getId());
            if($ordenReparacion->getCreated() < $ordenTrabajo->getFechaEntrada()){
                $this->addFlash('danger', 'La fecha de reparación no puede ser antes de la fecha de entrada.');
                return $this->render('orden_reparacion/new.html.twig', [
                    'orden_reparacion' => $ordenReparacion,
                    'form' => $form->createView(),
                    'multiple' => true,
                    'referer' => $request->headers->get('referer')
                ]);
            }
            $this->updatedData($ordenReparacion);
            //
            $entityManager = $this->getDoctrine()->getManager();
            if($ordenReparacion->getEstadoFinal() == 'NR'){
                $ordenTrabajo->setObservacionesFinales($request->get('orden_reparacion')['observacionesFinales']);
            } else{
                $ordenTrabajo->setObservacionesFinales('R');
                $ordenTrabajo->setFechaDictamen(new \DateTime());
            }

            if($ordenReparacion->getDejarEnTaller() && $ordenReparacion->getEstadoFinal() == 'NR'){
                $ordenTrabajo->setEstado('DT');
            }
            else {
                $ordenTrabajo->setEstado('LE');
                $ordenTrabajo->setFechaDictamen(new \DateTime());
            }
            $this->updatedData($ordenTrabajo);
            $entityManager->persist($ordenTrabajo);
            $this->updatedData($ordenReparacion);
            $entityManager->persist($ordenReparacion);
            $entityManager->flush();
            //
            $this->successEditElement();
            return $this->nextAction(
                $request,
                $this->generateUrl('orden_reparacion_new'),
                $this->generateUrl('orden_reparacion_show', array('id' => $ordenReparacion->getId()))
            );
        }

        $materiasPrimas = $this->getDoctrine()->getRepository(MateriaPrima::class)->findAllEnExistencia();

        return $this->render('orden_reparacion/new.html.twig', [
            'orden_reparacion' => $ordenReparacion,
            'form' => $form->createView(),
            'multiple' => true,
            'referer' => $request->headers->get('referer'),
            'cantidad_mp' => count($materiasPrimas),
            'materiales_existencias' => $materiasPrimas,
        ]);
    }

    /**
     * @Route("/{id}", name="orden_reparacion_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMINISTRADOR_NEGOCIO")
     */
    public function delete(Request $request, OrdenReparacion $ordenReparacion): Response
    {
        if($ordenReparacion->getOrdenTrabajo()->getFechaNotificacion() !== null){
            $this->addFlash('No puede eliminar una orden de reparación. El cliente se ha notificado ya.');
            return $this->redirectToRoute('orden_reparacion_show', array('id' => $ordenReparacion->getId()));
        }
        $form = $this->createDeleteForm($ordenReparacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $ordenTrabajo = $ordenReparacion->getOrdenTrabajo();
            $ordenTrabajo->setObservacionesFinales(null);
            $ordenTrabajo->getFechaRevision(null);
            $ordenTrabajo->setEstado('AT');
            $ordenTrabajo->setFechaListoEntregar('null');
            $em->remove($ordenReparacion);
            try{
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al eliminar. Existen elementos dependientes.');
                return $this->redirect($request->headers->get('referer'));
            }
            $this->addFlash('success', 'Elemento eliminado satisfactoriamente.');
        } else {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(OrdenReparacion::class)->find($request->get('id'));
            $em->remove($entity);
            try{
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al eliminar. Existen elementos dependientes.');
                return $this->redirect($request->headers->get('referer'));
            }
            $this->addFlash('success', 'Elemento eliminado satisfactoriamente.');
        }

        return $this->redirectToRoute('orden_reparacion_index');
    }

    /**
     * Creates a form to delete an entity.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(OrdenReparacion $ordenReparacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('orden_trabajo_delete', array('id' => $ordenReparacion->getId())))
            ->setMethod('POST')
            ->getForm()
            ;
    }

    /**
     * @Route("/exportar/{tipo}/rango", name="orden_reparacion_exportar_pdfexcel_rango", methods={"GET", "POST"})
     */
    public function exportarPDFExcel(string $tipo, OrdenReparacionRepository $ordenReparacionRepository, Request $request): Response
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
        $result = $ordenReparacionRepository->findByDates($fecha_inicio, $fecha_fin);

        $ingreso = 0;
        $gasto = 0;
        foreach ($result as $item){
            $ingreso += $item->getIngreso();
            $gasto += ($item->getGastoMateriales() + $item->getOtrosGastos());
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

        if ($tipo === 'pdf') {

            $options = [
                'orden_reparacions' => $result,
                'fecha' => $fecha,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'ingreso' => $ingreso,
                'gasto' => $gasto,
            ];

            $renderView = $this->renderView('orden_reparacion/indexPDF.html.twig', $options);
            $render = $this->render('orden_reparacion/indexPDF.html.twig', $options);

            $namefile = strtoupper('Diagnosticos_Realizados_Fecha: ' . $fecha);
            $namefile = str_replace(['/', ' - '], ['_', '-'], $namefile);
            $vista_impresion = $request->query->get('vista_impresion');
            $param = ($vista_impresion === null or $vista_impresion === false);
            return $this->imprimirDocumentoGeneral($renderView, $render, $namefile, $param, 'Portrait');
        } elseif ($tipo === 'excel') {
            $data = [];
            $filter = new FiltersExtension();
            $columnNames = ['Fecha de Revisión', 'Orden de Trabajo', 'Revisado por', 'Ingreso', 'Gasto', 'Materiales usados', 'Estado'];
            foreach ($result as $item) {
                $data[] = [
                    $item->getCreated()->format('d/m/Y h:i:s a'),
                    $item->getOrdenTrabajo()->getNroOrden(),
                    $item->getRevisadoPor()->getNombreCompleto(),
                    $item->getIngreso(),
                    $item->getGastoMateriales() + $item->getOtrosGastos(),
                    $item->getMaterialesUsados(),
                    $filter->traducirSiglas($item->getEstadoFinal())
                ];
            }
            $namefile = strtoupper('DIAGNOSTICO_REPARACION_' . $fecha);
            $namefileR = str_replace(['/', ' - ', ':', ' '], ['', '_', '', '_'], $namefile);
            return $this->exportarExcel($namefileR, 'G', $columnNames, $data, $namefileR);
        } else {
            $this->addFlash('danger', 'No es una ruta válida');
            return $this->redirectToReferer($request);
        }
    }
}
