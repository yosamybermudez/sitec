<?php

namespace App\Controller;

use App\Entity\ComprobanteOperacion;
use App\Entity\DictamenTecnico;
use App\Entity\Evento;
use App\Entity\Jornada;
use App\Entity\OperacionContable;
use App\Entity\OrdenReparacion;
use App\Entity\OrdenTrabajo;
use App\Entity\RegistroAsistencia;
use App\Entity\Usuario;
use App\Form\JornadaType;
use App\Repository\JornadaRepository;
use App\Twig\Extension\FiltersExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("taller/jornada")
 */
class JornadaController extends AbstractController
{
    /**
     * @Route("/", name="jornada_index", methods={"GET", "POST"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function index(JornadaRepository $jornadaRepository, Request $request): Response
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
            return $this->redirect($this->generateUrl('jornada_index') . $params);
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
        $result = $jornadaRepository->findByDates($fecha_inicio, $fecha_fin);

        $ingreso = 0;
        $gasto = 0;
     //   $ordenes_tipo = new ArrayCollection();

        foreach ($result as $item){
            $ingreso += $item->getFondoActual() - $item->getFondoInicial();
            $gasto += $item->getGastoMateriales();
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
            $this->addFlash('success', 'Se listan correctamente ' . count($result) . ' jornadas');
        }  else {
            $this->addFlash('info', 'No hay jornadas para el rango seleccionado');
        }
        return $this->render('jornada/index.html.twig', [
            'jornadas' => $result,
            'fecha' => $fecha,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'ingreso' => $ingreso,
            'gasto' => $gasto
        ]);
    }

    /**
     * @Route("/new", name="jornada_new", methods={"GET","POST"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function new(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $fecha = new \DateTime();
        $fecha->setTime(0,0,0);
        $jornada = $this->getDoctrine()->getRepository(Jornada::class)->findOneBy(array('fecha' => $fecha));

        if($jornada !== null){
            $this->addFlash('danger', 'Ya existe una jornada en el día señalado');
            return $this->redirectToRoute('jornada_show', array('id' => $jornada->getId()));
        }

        //Cerrar las jornadas anteriores que se quedaron abiertas
        $jornadas = $entityManager->getRepository(Jornada::class)->findBy(array('estado' => 'Vigente'));
        $registrosAsistencia = $entityManager->getRepository(RegistroAsistencia::class)->findBy(array('horaSalida' => null));
        foreach ($jornadas as $jornada){
            $jornada->setEstado('Cerrada');
            $this->updatedData($jornada);
            $entityManager->persist($jornada);
        }

        //Los trabajos creados en fechas pasadas que tengan los siguientes estados, deben ponerse en estado DEJADO EN TALLER (DT)
        $ordenesPendientesJornada = $entityManager->getRepository(OrdenTrabajo::class)->findTodosTrabajosPendientesEnTaller();
        foreach ($ordenesPendientesJornada as $item){
            if(in_array($item->getEstado(), array('ECT', 'TR'))){
                $item->setEstado('DT');
                $item->setDejadoEnTaller(true);
            }
            if($item->getEstado() === 'LE'){
                $item->setDejadoEnTaller(true);
            }
            if($item->getEstado() === 'ESP') { //Estos trabajos no han entrado, solo habian sido registrados en el sistema.
                $item->setEstado('CANC'); // Por eso se cancelan estas ordenes.
                $item->setFechaSalida(new \DateTime());
                $item->setDejadoEnTaller(false);
            }
            $this->updatedData($item);
            $entityManager->persist($item);
        }
        //Ponerle fecha de salida a los registros de asistencia que no se han cerrado. Se le pone la ultima hora del dia de entrada
        foreach ($registrosAsistencia as $registroAsistencia){
            $fechaJornada = $registroAsistencia->getJornada()->getFecha();
            $fechaJornada->setTime(23,59,59);
            $registroAsistencia->setHoraSalida($fechaJornada);
            $this->updatedData($registroAsistencia);
            $entityManager->persist($registroAsistencia);
        }
        // Crear la nueva jornada
        $jornada = new Jornada($this);
        $jornada->setGastoMateriales(0);
        $jornada->setEstado('Vigente');
        $jornada->setFecha(new \DateTime());
        $form = $this->createForm(JornadaType::class, $jornada);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tecnicos = $form->get('tecnicos')->getData();
            foreach ($tecnicos as $tecnico){
                $hora = new \DateTime();
                $registroAsistencia = new RegistroAsistencia($this);
                $registroAsistencia->setTecnico($tecnico);
                $registroAsistencia->setHoraEntrada($hora->setTime(8,0,0));
                $registroAsistencia->setJornada($jornada);
                $this->updatedData($registroAsistencia);
                $entityManager->persist($registroAsistencia);
            }
            $existe = $this->getDoctrine()->getRepository(Jornada::class)->findBy(array('fecha' => $jornada->getFecha()));
            if(!$existe){

                $jornada->setFondoActual($jornada->getFondoInicial());
                $nextNroOperacion = $this->getDoctrine()->getRepository(OperacionContable::class)->nextNroOperacion();
                $operacionContable = new OperacionContable($this, $nextNroOperacion,'CR', 'Depósito en caja',$jornada->getFondoInicial(),'Depósito en caja para inicio de jornada laboral');
                $this->updatedData($operacionContable);
                $entityManager->persist($operacionContable);
                $this->updatedData($jornada);
                $entityManager->persist($jornada);
                //Evento
                $numero = number_format($jornada->getFondoInicial(),2,'.','');
                $texto = 'Registro de la jornada para el día en curso. Fondo en caja: $ ' . $numero;
                $this->registrarEvento('Registro','Jornada', $jornada->getId(), $texto);
                // Fin evento
                $entityManager->flush();
                $this->successNewElement();
                return $this->redirectToRoute('jornada_show', array('id' => $jornada->getId()));
            } else {
                $this->addFlash('danger', 'Ya existe una jornada en el día señalado');
            }
        }

        return $this->render('jornada/new.html.twig', [
            'jornada' => $jornada,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="jornada_show", methods={"GET"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function show(Jornada $jornada, Request $request, JornadaRepository $jornadaRepository): Response
    {
        $jornadas= $jornadaRepository->findBy(array(),array('fecha' => 'ASC'));
        $a = array_search($jornada,$jornadas);
        $j['anterior'] = $jornadas[$a - 1] ?? null;
        $j['siguiente'] = $jornadas[$a + 1] ?? null;
        $fakeClosed = $request->query->get('fake_closed') ? true : false;

        $fechaInicio = clone $jornada->getFecha();
        $fechaFin = $jornada->getFecha()->setTime(23,59,59);
        $ordenes = $this->getDoctrine()->getRepository(OrdenTrabajo::class)->findTrabajosMostrarJornada($fechaInicio, $fechaFin);
        $eventos = $this->getDoctrine()->getRepository(Evento::class)->findEventosMostrarJornada($fechaInicio, $fechaFin);
        if($jornada->getEstado() === 'Vigente' and $fakeClosed === false){ // Abierta
            $registrosAsistencia = $this->getDoctrine()->getRepository(RegistroAsistencia::class)
                ->findBy(array('jornada' => $jornada, 'horaSalida' => null));
            $tecnicos = $this->getDoctrine()->getRepository(Usuario::class)->findTecnicos();
            $tecnicosEnTaller = [];
            foreach ($registrosAsistencia as $registro){
                $tecnicosEnTaller[] = $registro->getTecnico();
            }
            $tecnicosFuera = [];
            foreach ($tecnicos as $tecnico){
                if(!in_array($tecnico,$tecnicosEnTaller)){
                    $tecnicosFuera[] = $tecnico;
                }
            }
            $deleteForm = $this->createDeleteForm($jornada);
            return $this->render('jornada/showVigente.html.twig', [
                'jornada' => $jornada,
                'delete_form' => $deleteForm->createView(),
                'orden_trabajos' => $ordenes,
                'tecicosFuera' => $tecnicosFuera,
                'eventos' => $eventos
            ]);
        } else { // Cerrada
            $gastoMateriales = $jornada->getGastoMateriales();
            $dictamenes = $this->getDoctrine()->getRepository(DictamenTecnico::class)->findByDates($fechaInicio, $fechaFin);
            $revisiones = $this->getDoctrine()->getRepository(OrdenReparacion::class)->findByDates($fechaInicio, $fechaFin);
            $salidas = $this->getDoctrine()->getRepository(OrdenTrabajo::class)->findSalidasFecha($fechaInicio, $fechaFin);
            $reparaciones = $this->getDoctrine()->getRepository(OrdenReparacion::class)->findByDates($fechaInicio, $fechaFin, true);
            $equiposPendientes = $this->getDoctrine()->getRepository(OrdenTrabajo::class)->findTrabajosPendientesEnTaller($fechaFin);
            $registrosAsistencia = $this->getDoctrine()->getRepository(RegistroAsistencia::class)
                ->findBy(array('jornada' => $jornada));
            $fechaInicio = clone $jornada->getFecha()->setTime(0,0,0);
            $fechaFin = clone $jornada->getFecha()->setTime(23,59,59);
            $comprobantes = $this->getDoctrine()->getRepository(ComprobanteOperacion::class)->findByDates($fechaInicio, $fechaFin);
            $operaciones_contables = $this->getDoctrine()->getRepository(OperacionContable::class)->findByDates($fechaInicio, $fechaFin);
            $gananciaTecnicos = array();
            foreach ($registrosAsistencia as $registro){
                if(array_key_exists($registro->getTecnico()->getNombreCompleto(), $gananciaTecnicos)){
                    $gananciaTecnicos[$registro->getTecnico()->getNombreCompleto()] += ['total' => $registro->getMontoRecaudado() - $registro->getGastoMateriales(), 'ingreso' => $registro->getMontoRecaudado(), 'gasto' => $registro->getGastoMateriales()];
                } else {
                    $gananciaTecnicos[$registro->getTecnico()->getNombreCompleto()] = ['total' => $registro->getMontoRecaudado() - $registro->getGastoMateriales(), 'ingreso' => $registro->getMontoRecaudado(), 'gasto' => $registro->getGastoMateriales()];
                }

            }
            $trabajosTecnicos = array();
            foreach ($revisiones as $elem){
                $trabajosTecnicos[$elem->getRevisadoPor()->getNombreCompleto()][] = $elem;
            }

            $array = array();
            foreach ($ordenes as $elem){
                $array[$elem->getNroOrden()]['registrado'] = $elem->getFechaEntrada();
            }
            foreach ($salidas as $elem){
                $orden = $elem;
                $array[$orden->getNroOrden()]['salida'] = $orden->getFechaSalida();
            }
            foreach ($dictamenes as $elem){
                $orden = $elem->getOrdenTrabajo();
                $array[$orden->getNroOrden()]['dictaminado'] = $orden->getFechaDictamen();
            }
            foreach ($revisiones as $elem){
                $orden = $elem->getOrdenTrabajo();
//                $orden = new OrdenTrabajo($this);
                $array[$orden->getNroOrden()]['revisado'] = $orden->getFechaRevision();
            }
            foreach ($reparaciones as $elem){
                $orden = $elem->getOrdenTrabajo();
                $array[$orden->getNroOrden()]['reparado'] = $orden->getFechaRevision();
            }
            foreach ($equiposPendientes as $elem){
                $array[$elem->getNroOrden()]['pendiente'] = true;
            }

            dump($array);

            return $this->render('jornada/showCerrada.html.twig', [
                'fake_closed' => $fakeClosed,
                'jornada' => $jornada,
                'orden_trabajos' => $ordenes,
                'dictamenes' => $dictamenes,
                'revisiones' => $revisiones,
                'reparaciones' => $reparaciones,
                'equiposPendientes' => $equiposPendientes,
                'gananciaTecnicos' => $gananciaTecnicos,
                'gastoMateriales' => $gastoMateriales,
                'trabajos_tecnicos' => $trabajosTecnicos,
                'comprobantes' => $comprobantes,
                'operacion_contables' => $operaciones_contables,
                'salidas' => $salidas,
                'array' => $array,
                'jornadas' => $j
            ]);
        }
    }

    /**
     * @Route("/imprimir/{id}", name="jornada_imprimir", methods={"GET"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function imprimir(Jornada $jornada, Request $request): Response
    {
        $fechaInicio = clone $jornada->getFecha();
        $fechaFin = clone $jornada->getFecha()->setTime(23,59,59);

        $ordenes = $this->getDoctrine()->getRepository(OrdenTrabajo::class)->findTrabajosMostrarJornada($fechaInicio, $fechaFin);
        $gastoMateriales = $jornada->getGastoMateriales();
        $dictamenes = $this->getDoctrine()->getRepository(DictamenTecnico::class)->findByDates($fechaInicio, $fechaFin);
        $revisiones = $this->getDoctrine()->getRepository(OrdenReparacion::class)->findByDates($fechaInicio, $fechaFin);
        $salidas = $this->getDoctrine()->getRepository(OrdenTrabajo::class)->findSalidasFecha($fechaInicio, $fechaFin);
        $reparaciones = $this->getDoctrine()->getRepository(OrdenReparacion::class)->findByDates($fechaInicio, $fechaFin, true);
        $equiposPendientes = $this->getDoctrine()->getRepository(OrdenTrabajo::class)->findTrabajosPendientesEnTaller($fechaInicio, $fechaFin);
        $comprobantes = $this->getDoctrine()->getRepository(ComprobanteOperacion::class)->findByDates($fechaInicio, $fechaFin);
        $operaciones_contables = $this->getDoctrine()->getRepository(OperacionContable::class)->findByDates($fechaInicio, $fechaFin);
        $registrosAsistencia = $this->getDoctrine()->getRepository(RegistroAsistencia::class)
            ->findBy(array('jornada' => $jornada));
        $gananciaTecnicos = array();

        foreach ($registrosAsistencia as $registro){
            if(array_key_exists($registro->getTecnico()->getNombreCompleto(), $gananciaTecnicos)){
                $gananciaTecnicos[$registro->getTecnico()->getNombreCompleto()] += ['total' => $registro->getMontoRecaudado() - $registro->getGastoMateriales(), 'ingreso' => $registro->getMontoRecaudado(), 'gasto' => $registro->getGastoMateriales()];
            } else {
                $gananciaTecnicos[$registro->getTecnico()->getNombreCompleto()] = ['total' => $registro->getMontoRecaudado() - $registro->getGastoMateriales(), 'ingreso' => $registro->getMontoRecaudado(), 'gasto' => $registro->getGastoMateriales()];
            }

        }
        $trabajosTecnicos = array();
        foreach ($revisiones as $elem){
            $trabajosTecnicos[$elem->getRevisadoPor()->getNombreCompleto()][] = $elem;
        }

        $array = array();
        foreach ($ordenes as $elem){
            $array[$elem->getNroOrden()]['registrado'] = $elem->getFechaEntrada();
        }
        foreach ($dictamenes as $elem){
            $orden = $elem->getOrdenTrabajo();
            $array[$orden->getNroOrden()]['dictaminado'] = $orden->getFechaDictamen();
        }
        foreach ($revisiones as $elem){
            $orden = $elem->getOrdenTrabajo();
//                $orden = new OrdenTrabajo($this);
            $array[$orden->getNroOrden()]['revisado'] = $orden->getFechaRevision();
        }
        foreach ($reparaciones as $elem){
            $orden = $elem->getOrdenTrabajo();
//                $orden = new OrdenTrabajo($this);
            $array[$orden->getNroOrden()]['reparado'] = $orden->getFechaRevision();
        }
        foreach ($equiposPendientes as $elem){
            $array[$elem->getNroOrden()]['pendiente'] = $elem->getFechaRevision();
        }
        $options = ['jornada' => $jornada,
            'orden_trabajos' => $ordenes,
            'dictamenes' => $dictamenes,
            'revisiones' => $revisiones,
            'reparaciones' => $reparaciones,
            'equiposPendientes' => $equiposPendientes,
            'gananciaTecnicos' => $gananciaTecnicos,
            'gastoMateriales' => $gastoMateriales,
            'trabajos_tecnicos' => $trabajosTecnicos,
            'comprobantes' => $comprobantes,
            'salidas' => $salidas,
            'operacion_contables' => $operaciones_contables,
            'array' => $array];
        $renderView = $this->renderView('jornada/showCerradaPDF.html.twig', $options);
        $render = $this->render('jornada/showCerradaPDF.html.twig', $options);

        $namefile = 'REPORTE DE JORNADA - ' . $jornada->getFecha()->format('Ymd');

        return $this->imprimirDocumentoGeneral($renderView, $render,$namefile,true);
    }

    /**
     * @Route("/entrada/trabajador/{id}", name="jornada_entrada_trabajador", methods={"GET"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function entradaTrabajador(Usuario $usuario): Response
    {
        $jornada = $this->getDoctrine()->getRepository(Jornada::class)->findOneBy(array('fecha' => new \DateTime()));

        $registroAsistencia = new RegistroAsistencia($this);
        $registroAsistencia->setJornada($jornada);
        $registroAsistencia->setTecnico($usuario);
        $registroAsistencia->setHoraEntrada(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $this->updatedData($registroAsistencia);
        $em->persist($registroAsistencia);
        $em->flush();
        $this->addFlash('success', 'Confirmación de entrada. Trabajador: ' . $usuario->getNombreCompleto() . '. Hora de entrada: ' . $registroAsistencia->getHoraEntrada()->format('h:i:s a'));
        return $this->redirectToRoute('jornada_show', array('id' => $jornada->getId(), 'info' => 3));
    }

    /**
     * @Route("/salida/trabajador/{id}", name="jornada_salida_trabajador", methods={"GET"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function salidaTrabajador(Usuario $usuario): Response
    {
        $ordenTrabajo = $this->getDoctrine()->getRepository(OrdenTrabajo::class)
                             ->findBy(array('estado' => 'AT', 'tecnicoRepara' => $usuario));
        $jornada = $this->getDoctrine()->getRepository(Jornada::class)->findOneBy(array('fecha' => new \DateTime()));
        if(count($ordenTrabajo) !== 0){
            $this->addFlash('danger','El trabajador no puede registrar la salida mientras tenga equipos en espera de su revisión');
            return $this->redirectToRoute('jornada_show', array('id' => $jornada->getId(), 'info' => true));
        }
        //Devuelve el registro de asistencia al cual se le pondra Fecha y hora de salida
        $registroAsistencia = $this->getDoctrine()->getRepository(RegistroAsistencia::class)->findOneBy(array('tecnico' => $usuario, 'jornada' => $jornada), array('horaEntrada' => 'DESC'));
        $registroAsistencia->setHoraSalida(new \DateTime());
        $em = $this->getDoctrine()->getManager();
        $this->updatedData($registroAsistencia);
        $em->persist($registroAsistencia);
        $em->flush();
        $this->addFlash('success', 'Confirmación de salida. Trabajador: ' . $usuario->getNombreCompleto() . '. Hora de salida: ' . $registroAsistencia->getHoraSalida()->format('h:i:s a'));
        return $this->redirectToRoute('jornada_show', array('id' => $jornada->getId(), 'info' => true));
    }

    /**
     * @Route("/fecha/hoy", name="jornada_show_hoy", methods={"GET"})
     * @IsGranted("ROLE_RECEPCIONISTA")
     */
    public function showHoy(): Response
    {
        $jornada = $this->getDoctrine()->getRepository(Jornada::class)->findOneBy(array('fecha' => new \DateTime()));
        if($jornada === null){
            $this->addFlash('danger', 'No se ha creado la jornada del día de hoy');
            return $this->redirectToRoute('app_module_taller');
        } else {
            return $this->redirectToRoute('jornada_show', array('id' => $jornada->getId()));
        }

    }

    /**
     * @Route("/{id}/cerrar", name="jornada_cerrar", methods={"GET"})
     * @IsGranted("ROLE_ADMINISTRACION")
     */
    public function cerrar(Jornada $jornada, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $fechaInicio = clone $jornada->getFecha();
        $fechaFin = $jornada->getFecha()->setTime(23,59,59);
        $ordenesPendientesJornada = $em->getRepository(OrdenTrabajo::class)->findTrabajosPendientesJornada($fechaInicio, $fechaFin);
        if(count($ordenesPendientesJornada) != 0){
            $this->addFlash('danger', 'No se puede cerrar la jornada. Todavía tiene órdenes de trabajo pendientes a procesar');
            return $this->render('jornada/cierreForzado.html.twig', [
                'orden_trabajos' => $ordenesPendientesJornada,
                'jornada' => $jornada,
                'referer' => $request->headers->get('referer')
            ]);
        }
//        $jornada = $this->getDoctrine()->getRepository(Jornada::class)->findOneBy(array('fecha' => new \DateTime()));
        if($jornada === null){
            $this->addFlash('danger', 'No se ha creado la jornada del día de hoy');
        } else {
            $horaSalida = new \DateTime();
            $registrosAsistencia = $em->getRepository(RegistroAsistencia::class)->findBy(array('horaSalida' => null));
            foreach ($registrosAsistencia as $registroAsistencia){
                if($jornada === $registroAsistencia->getJornada()){
                    $registroAsistencia->setHoraSalida($horaSalida);
                } else {
                    $fechaJornada = $registroAsistencia->getJornada()->getFecha();
                    $fechaJornada->setTime(23,59,59);
                    $registroAsistencia->setHoraSalida($fechaJornada);
                }
                $this->updatedData($registroAsistencia);
                $em->persist($registroAsistencia);

            }
            $jornada->setEstado('Cerrada');
            $this->updatedData($jornada);
            $em->persist($jornada);
            $em->flush();
            $this->addFlash('success', 'Se ha cerrado correctamente la jornada de hoy');
        }
        return $this->redirectToRoute('app_module_taller');
    }

    /**
     * @Route("/{id}/cerrar/forzado", name="jornada_cerrar_forzado", methods={"GET"})
     * @IsGranted("ROLE_ADMINISTRACION")
     */
    public function cerrarFozado(Jornada $jornada, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $fechaInicio = clone $jornada->getFecha();
        $fechaFin = $jornada->getFecha()->setTime(23,59,59);
        $ordenesPendientesJornada = $em->getRepository(OrdenTrabajo::class)->findTrabajosPendientesJornada($fechaInicio, $fechaFin);
        foreach ($ordenesPendientesJornada as $item){
            if(in_array($item->getEstado(), array('ECT', 'TR'))){
                $item->setEstado('DT');
                $item->setDejadoEnTaller(true);
            }
            if($item->getEstado() === 'LE'){
                $item->setDejadoEnTaller(true);
            }
            if($item->getEstado() === 'ESP'){ //Estos trabajos no han entrado, solo habian sido registrados en el sistema.
                $item->setEstado('CANC'); // Por eso se cancelan estas ordenes.
                $item->setFechaSalida(new \DateTime());
                $item->setDejadoEnTaller(false);
            }
            $this->updatedData($item);
            $em->persist($item);
        }
        if($jornada === null){
            $this->addFlash('danger', 'No se ha creado la jornada del día de hoy');

        } else {

            $horaSalida = new \DateTime();
            $registrosAsistencia = $em->getRepository(RegistroAsistencia::class)->findBy(array('jornada' => $jornada, 'horaSalida' => null));
            foreach ($registrosAsistencia as $registroAsistencia){
                $registroAsistencia->setHoraSalida($horaSalida);
                $this->updatedData($registroAsistencia);
                $em->persist($registroAsistencia);
            }
            $jornada->setEstado('Cerrada');
            $this->updatedData($jornada);
            $em->persist($jornada);
            $em->flush();
            $this->addFlash('success', 'Se ha cerrado correctamente la jornada de hoy');
        }
        return $this->redirectToRoute('app_module_taller');
    }

    /**
     * @Route("/{id}/edit", name="jornada_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMINISTRADOR_NEGOCIO")
     */
    public function edit(Request $request, Jornada $jornada): Response
    {
        $form = $this->createForm(JornadaType::class, $jornada);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->updatedData($jornada);
            $em->persist($jornada);
            $em->flush();
            $this->successEditElement();
            return $this->redirectToRoute('jornada_show', array('id' => $jornada->getId()));
        }

        return $this->render('jornada/new.html.twig', [
            'jornada' => $jornada,
            'form' => $form->createView(),
            'referer' => $request->headers->get('referer')
        ]);
    }

    /**
     * @Route("/{id}", name="jornada_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMINISTRADOR_NEGOCIO")
     */
    public function delete(Request $request, Jornada $jornada): Response
    {
        $form = $this->createDeleteForm($jornada);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($jornada);
            try{
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al eliminar. Existen elementos dependientes.');
                return $this->redirect($request->headers->get('referer'));
            }
            $this->successDeleteElement();
        } else {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(Jornada::class)->find($request->get('id'));
            $em->remove($entity);
            try{
                $em->flush();
            } catch (\Exception $e) {
                $this->failedDeleteElement();
                return $this->redirect($request->headers->get('referer'));
            }
            $this->successDeleteElement();
        }

        return $this->redirectToRoute('app_module_taller');
    }

    /**
     * Creates a form to delete an entity.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Jornada $jornada)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('jornada_delete', array('id' => $jornada->getId())))
            ->setMethod('POST')
            ->getForm()
            ;
    }

    /**
     * @Route("/exportar/{tipo}/rango", name="jornada_exportar_pdfexcel_rango", methods={"GET", "POST"})
     */
    public function exportarPDFExcel(string $tipo, JornadaRepository $jornadaRepository, Request $request): Response
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

        $result = $jornadaRepository->findByDates($fecha_inicio, $fecha_fin);

        $ingreso = 0;
        $gasto = 0;
        //   $ordenes_tipo = new ArrayCollection();

        foreach ($result as $item){
            $ingreso += $item->getFondoActual() - $item->getFondoInicial();
            $gasto += $item->getGastoMateriales();
        }

        if(count($result) === 0){
            $this->addFlash('danger', 'No se puede exportar. No hay jornadas para el día seleccionado');
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

            $options = [
                'jornadas' => $result,
                'fecha' => $fecha,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'ingreso' => $ingreso,
                'gasto' => $gasto
            ];

            $renderView = $this->renderView('jornada/indexPDF.html.twig', $options);
            $render = $this->render('jornada/indexPDF.html.twig', $options);

            $namefile = strtoupper('Jornadas_Fecha: ' . $fecha);
            $namefile = str_replace(['/',' - '],['_', '-'], $namefile);

            return $this->imprimirDocumentoGeneral($renderView, $render,$namefile, false, 'Portrait');
        } elseif($tipo === 'excel') {
            $data = [];
            $columnNames = ['Fecha','Fondo inicial','Fondo actual', 'Ingreso','Gasto materiales','Ganancia'];
            foreach($result as $item){
                $data[] = [
                    $item->getFecha()->format('d/m/Y h:i:s a'),
                    $item->getFondoInicial(),
                    $item->getFondoActual(),
                    $item->getFondoActual() - $item->getFondoInicial(),
                    $item->getGastoMateriales(),
                    $item->getFondoActual() - $item->getFondoInicial() - $item->getGastoMateriales()
                ];
            }

            $namefile = strtoupper('Dictamenes_' . $fecha);
            $namefileR = str_replace(['/',' - ',':',' '],['', '_', '', '_'], $namefile);
            return $this->exportarExcel($namefileR,'F',$columnNames,$data,$namefileR);
        } else {
            $this->addFlash('danger', 'No es una ruta válida');
            return $this->redirectToReferer($request);
        }


    }
}
