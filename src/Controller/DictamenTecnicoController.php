<?php

namespace App\Controller;

use App\Entity\DictamenTecnico;
use App\Entity\Jornada;
use App\Entity\OrdenReparacion;
use App\Entity\OrdenTrabajo;
use App\Entity\Usuario;
use App\Form\DictamenTecnicoType;
use App\Repository\DictamenTecnicoRepository;
use App\Repository\OrdenTrabajoRepository;
use App\Twig\Extension\FiltersExtension;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("taller/dictamen_tecnico")
 */
class DictamenTecnicoController extends AbstractController
{
    /**
     * @Route("/", name="dictamen_tecnico_index", methods={"GET", "POST"})
     */
    public function index(DictamenTecnicoRepository $dictamenTecnicoRepository, Request $request): Response
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
            return $this->redirect($this->generateUrl('dictamen_tecnico_index') . $params);
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
        $result = $dictamenTecnicoRepository->findByDates($fecha_inicio, $fecha_fin);

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
            $this->addFlash('success', 'Se listan correctamente ' . count($result) . ' dictámenes ');
        }  else {
            $this->addFlash('info', 'No hay dictámenes para el rango seleccionado');
        }
        return $this->render('dictamen_tecnico/index.html.twig', [
            'dictamen_tecnicos' => $result,
            'fecha' => $fecha,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ]);
    }

    /**
     * @Route("/new", name="dictamen_tecnico_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ordenTrabajoId = $request->query->get('id');
        if($ordenTrabajoId) {
            $dictamenTecBD = $entityManager->getRepository(DictamenTecnico::class)->findOneBy(array('ordenTrabajo' => $ordenTrabajoId));
            $dictamenTecnico = $dictamenTecBD ?: new DictamenTecnico($this);
            $ordenTrabajo = $this->getDoctrine()->getRepository(OrdenTrabajo::class)->find($ordenTrabajoId);
            // Para aquellos equipos que no se va a realizar un reparacion como tal. Ej> en los moviles, instalar aplicaciones.
//            if(!$ordenTrabajo->getEsReparacion()){
//                $dictamenTecnico->setDictamen('RA');
//                $dictamenTecnico->setDejarEnTaller(false);
//                $dictamenTecnico->setOrdenTrabajo($ordenTrabajo);
//                $dictamenTecnico->setPrecio(0);
//                $ordenTrabajo->setFechaDictamen(new \DateTime());
//                $this->updatedData($ordenTrabajo);
//                $entityManager->persist($dictamenTecnico);
//                $this->updatedData($ordenTrabajo);
//                $entityManager->persist($ordenTrabajo);
//                $entityManager->flush();
//                $this->addFlash('success', 'Se generó un dictamen para el trabajo a realizar');
//                return $this->redirectToRoute('orden_reparacion_new', array('id' => $ordenTrabajo->getId()));
//            }
            $dictamenTecnico->setOrdenTrabajo($ordenTrabajo);
            $form = $this->createForm(DictamenTecnicoType::class, $dictamenTecnico);
            $form->add('tecnicoRepara', EntityType::class, [
                'class' => Usuario::class,
                'label' => 'Técnico asignado',
                'choice_label' => 'nombreCargo',
                'placeholder' => 'Seleccione',
                'required' => true,
                'mapped' => false,
                'query_builder' => function ($er) {
                    $date = new \DateTime();
                    $date->format('Y-m-d');
                    return $er->createQueryBuilder('d')
                        ->innerJoin('d.cargo', 'c')
                        ->innerJoin('d.registrosAsistencia', 'r')
                        ->innerJoin('r.jornada', 'j')
                        ->where("c.nombre LIKE '%Técnico%'")
                        ->orWhere("c.nombre LIKE '%Tecnico%'")
                        ->andWhere("j.fecha = :date")
                        ->andWhere("r.horaSalida is null")
                        ->setParameter('date', $date)
                        ->orderBy('d.nombres', 'ASC');
                }
            ]);
            if(!$ordenTrabajo->getEsReparacion()) {
                $form->remove('dictamen');
                $form->add('dictamen', ChoiceType::class, array(
                    'choices' => [
                        'No lo puedo realizar' => 'NPR',
                        'No se encuentran los materiales que se necesitan' => 'NPN',
                        'Dejar en taller' => 'DT',
                        'Se revisará ahora' => 'RA',
                        'Asignar a otro técnico' => 'AOT',
                        'El cliente se fue' => 'CF',
                    ],
                    'placeholder' => 'Seleccione',
                    'required' => true
                ))
                ;
                $form->get('dictamen')->setData('RA');
            }
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $tecnicoRepara = isset($request->request->get('dictamen_tecnico')['tecnicoRepara']) ? $request->request->get('dictamen_tecnico')['tecnicoRepara'] : null ;
                if($tecnicoRepara != null){
                    $tecnicoRepara = $this->getDoctrine()->getRepository(Usuario::class)->find($tecnicoRepara);
                    $ordenTrabajo->setTecnicoRepara($tecnicoRepara);
                }

                $dictamen = $dictamenTecnico->getDictamen();
                $ordenTrabajo->setDejadoEnTaller(FALSE);
                if($dictamen === 'NPR' || ($dictamen === 'NPN' && !$dictamenTecnico->getDejarEnTaller())){
                    $ordenTrabajo->setEstado('NRT'); //No se realizara el trabajo al cliente
                    $ordenTrabajo->setFechaSalida(new \DateTime());
                    $ordenTrabajo->setObservacionesFinales($dictamen === 'NPR' ? 'Ninguno de los técnicos disponibles puede reparar el equipo.' : 'No existe la pieza que se necesita para la reparación. El cliente se debe llevar el equipo.');
                } elseif($dictamen === 'NPN' && $dictamenTecnico->getDejarEnTaller()){
                    $ordenTrabajo->setEstado('EP'); //Esta en el taller. En Espera de la pieza.
                    $ordenTrabajo->setDejadoEnTaller(TRUE);
                } elseif($dictamen === 'AOT'){ //Asignar a otro tecnico
                    $ordenTrabajo->setEstado('AOT');
                } elseif($dictamen === 'RA') {
                    $ordenTrabajo->setEstado('ECT'); //En cola para el tecnico
                } elseif($dictamen === 'CF'){
                    $ordenTrabajo->setEstado('CF'); //El cliente se fue.
                    $ordenTrabajo->setFechaSalida(new \DateTime());
                    $ordenTrabajo->setObservacionesFinales('El cliente se fue. No pudo esperar');
                } elseif($dictamen === 'DT'){
                    $dictamenTecnico->setDejarEnTaller(TRUE);
                    $ordenTrabajo->setEstado('DT'); //Se decide dejar en el taller.
                    $ordenTrabajo->setDejadoEnTaller(TRUE);
                }
                if($dictamen !== 'CF') {
                    $ordenTrabajo->setFechaDictamen(new \DateTime());
                    $ordenTrabajo->setDictamenTecnico($dictamenTecnico);
                }
                // Precio del dictamen: Actualmente no se cobra por dictaminar
                $dictamenTecnico->setPrecio(0);

                //Persona que dictamina
                $dictaminadoPor = $this->isGranted('ROLE_RECEPCIONISTA') ? $ordenTrabajo->getTecnicoRepara() : $this->getDatabaseUser();
                $dictamenTecnico->setDictaminadoPor($dictaminadoPor);

                $this->updatedData($ordenTrabajo);
                $entityManager->persist($ordenTrabajo);

                $this->updatedData($dictamenTecnico);
                $entityManager->persist($dictamenTecnico);
                //Evento
                $extension = new FiltersExtension();
                $texto = 'Registro de Dictamen Técnico asociado a la Orden de Trabajo # ' . $ordenTrabajo->getNroOrden() . ' (' . $extension->traducirSiglas($ordenTrabajo->getEstado()) . ')';
                $this->registrarEvento('Registro','DictamenTecnico', $dictamenTecnico->getId(), $texto);
                // Fin evento

                $entityManager->flush();
                $this->successNewElement();
                if($this->isGranted('ROLE_RECEPCIONISTA')){
                    return $this->redirectToRoute('jornada_show_hoy');
                }
                if($dictamen === 'RA'){
                    return $this->redirectToRoute('orden_reparacion_new', array('id' => $ordenTrabajoId));
                }
                return $this->redirectToRoute('dictamen_tecnico_trabajos_pendientes');
            }

            return $this->render('dictamen_tecnico/new.html.twig', [
                'dictamen_tecnico' => $dictamenTecnico,
                'form' => $form->createView(),
            ]);
        } else {
            $this->addFlash('danger', 'Ha ocurrido un error');
            return $this->redirectToRoute('dictamen_tecnico_trabajos_pendientes');
        }
    }

    /**
     * @Route("/exportar/{tipo}/rango", name="dictamen_tecnico_exportar_pdfexcel_rango", methods={"GET", "POST"})
     */
    public function exportarPDFExcel(string $tipo, DictamenTecnicoRepository $dictamenTecnicoRepository, Request $request): Response
    {

        $req = $request->query;
        $fecha_param = $req->get('fecha');
        if(!$fecha_param){
            $fecha_inicio = $req->get('inicio');
            $fecha_fin = $req->get('fin');
        } else {
            $fecha_inicio = $fecha_param;
            $fecha_fin = $fecha_param;
        }

        $fecha_inicio = $fecha_inicio !== null ? new \DateTime($fecha_inicio) : null;
        $fecha_fin = $fecha_fin !== null ? new \DateTime($fecha_fin) : null;

        $result = $dictamenTecnicoRepository->findByDates($fecha_inicio, $fecha_fin);

        if(count($result) === 0){
            $this->addFlash('danger', 'No se puede exportar. No hay dictámenes para el día seleccionado');
            return $this->redirectToReferer($request);
        }

        if($fecha_inicio === null and $fecha_fin !== null){
            $fecha = 'hasta el ' . $fecha_fin->format('d/m/Y');
        } elseif($fecha_inicio !== null and $fecha_fin === null){
            $fecha = 'desde el ' . $fecha_inicio->format('d/m/Y');
        } elseif($fecha_inicio === null and $fecha_fin === null){
            $fecha = 'Todas';
        } elseif($fecha_inicio->format('d/m/Y') === $fecha_fin->format('d/m/Y') ){
            $fecha = $fecha_inicio->format('d/m/Y');
        } elseif($fecha_inicio->format('d/m/Y') === $fecha_fin->format('d/m/Y')){
            $fecha = $fecha_inicio->format('d/m/Y');
        } else {
            $fecha = $fecha_inicio->format('d/m/Y') . ' - ' . $fecha_fin->format('d/m/Y');
        }

        if($tipo === 'pdf'){
            $renderView = $this->renderView('dictamen_tecnico/indexPDF.html.twig', [
                'dictamen_tecnicos' => $result,
                'fecha' => $fecha,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
            ]);

            $render = $this->render('dictamen_tecnico/indexPDF.html.twig', [
                'dictamen_tecnicos' => $result,
                'fecha' => $fecha,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
            ]);

            $namefile = strtoupper('Dictamenes tecnicos_Fecha: ' . $fecha);
            $namefile = str_replace(['/',' - '],['_', '-'], $namefile);

            return $this->imprimirDocumentoGeneral($renderView, $render,$namefile, true, 'Portrait');
        } elseif($tipo === 'excel') {
            $data = [];
            $columnNames = ['Fecha','Registrado','Dictaminado por','Equipo','Dictamen'];
            $filters = new FiltersExtension();
            foreach($result as $item){
                $data[] = [
                    $item->getCreated() ? $item->getCreated()->format('d/m/Y h:i:s a') : null,
                    $item->getCreatedBy() ? $item->getCreatedBy()->getNombreCompleto() : null,
                    $item->getOrdenTrabajo() ? $item->getOrdenTrabajo()->getTecnicoRepara()->getNombreCompleto() : null,
                    $item->getOrdenTrabajo()->getDatosEquipo(),
                    $item->getDictamen() !== 'RA' ? $filters->traducirSiglas($item->getDictamen()) : 'Entra a revisión'
                ];
            }

            $namefile = strtoupper('Dictamenes_' . $fecha);
            $namefileR = str_replace(['/',' - ',':',' '],['', '_', '', '_'], $namefile);
            return $this->exportarExcel($namefileR,'E',$columnNames,$data,$namefileR);
        } else {
            $this->addFlash('danger', 'No es una ruta válida');
            return $this->redirectToReferer($request);
        }


    }

    /**
     * @Route("/trabajos_pendientes", name="dictamen_tecnico_trabajos_pendientes", methods={"GET"})
     * @IsGranted("ROLE_TECNICO")
     */
    public function showMisTrabajosPendientes(OrdenTrabajoRepository $ordenTrabajoRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $fecha = new \DateTime();
        $fecha->format('d-m-Y');
        $jornada = $entityManager->getRepository(Jornada::class)->findByDate($fecha);
        if($jornada->getEstado() === 'Cerrada'){
            $this->addFlash('danger', 'La jornada actual ya está cerrada. No se puede realizar ninguna acción');
            return $this->redirectToRoute('homepage');
        }
        $ordenesTrabajo = $this->getDoctrine()->getRepository(OrdenTrabajo::class)->findTrabajosPendientesTecnico($this->getDatabaseUser());
        foreach ($ordenesTrabajo as $obj){
            if($obj->getDictamenTecnico()->getDictamen() === 'NPN' && $obj->getDictamenTecnico()->getDejarEnTaller()){
                $obj->setEstado('EP'); //En espera de piezas
            } elseif($obj->getDictamenTecnico()->getDictamen() === 'DT'){
                $obj->setEstado('DT'); //Dejado en taller
            } else {
                $obj->setEstado('ECT'); //En cola para el tecnico
            }
            $this->updatedData($obj);
            $entityManager->persist($obj);
            $entityManager->flush();
        }
        return $this->render('orden_reparacion/trabajosPendientesCards.html.twig', [
            'orden_trabajos' => $ordenTrabajoRepository->findTrabajosPendientesTecnico($this->getDatabaseUser())
        ]);
    }

    /**
     * @Route("/trabajos_pendientes/todos", name="dictamen_tecnico_trabajos_pendientes_todos", methods={"GET"})
     */
    public function showTrabajosPendientesTodos(OrdenTrabajoRepository $ordenTrabajoRepository): Response
    {
        return $this->render('orden_reparacion/trabajosPendientes.html.twig', [
            'orden_trabajos' => $ordenTrabajoRepository->findTodosTrabajosPendientesEnTaller(),
        ]);
    }

    /**
     * @Route("/{id}", name="dictamen_tecnico_show", methods={"GET"})
     */
    public function show(DictamenTecnico $dictamenTecnico): Response
    {
        return $this->render('dictamen_tecnico/show.html.twig', [
            'dictamen_tecnico' => $dictamenTecnico,
        ]);
    }

}
