<?php

namespace App\Controller;
use App\Entity\OperacionContable;
use App\Repository\OperacionContableRepository;
use App\Repository\OrdenTrabajoRepository;
use App\Twig\Extension\FiltersExtension;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/operacion/contable")
 */
class OperacionContableController extends AbstractController
{
    /**
     * @Route("/", name="operacion_contable_index", methods={"GET", "POST"})
     */
    public function index(Request $request, OperacionContableRepository $operacionContableRepository): Response
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
            return $this->redirect($this->generateUrl('operacion_contable_index') . $params);
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
        $operaciones = $operacionContableRepository->findByDates($fecha_inicio, $fecha_fin);

        $ingreso = 0;
        $gasto = 0;
        foreach ($operaciones as $operacion){
            $ingreso += $operacion->getTipoOperacion() === 'CR' ? $operacion->getSaldo() : 0;
            $gasto += $operacion->getTipoOperacion() === 'DB' ? $operacion->getSaldo() : 0;
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
        if(count($operaciones) > 0){
            $this->addFlash('success', 'Se listan correctamente ' . count($operaciones) . ' operaciones contables');
        }  else {
            $this->addFlash('info', 'No hay operaciones contables para el rango seleccionado');
        }
        return $this->render('operacion_contable/index.html.twig', [
            'operacion_contables' => $operaciones,
            'fecha' => $fecha,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'ingreso' => $ingreso,
            'gasto' => $gasto,
        ]);
    }

    /**
     * @Route("/exportar/{tipo}/rango", name="operacion_contable_exportar_pdfexcel_rango", methods={"GET", "POST"})
     */
    public function exportarPDFExcel(string $tipo, OperacionContableRepository $operacionContableRepository, Request $request): Response
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
        $operaciones = $operacionContableRepository->findByDates($fecha_inicio, $fecha_fin);

        $ingreso = 0;
        $gasto = 0;
        foreach ($operaciones as $operacion) {
            $ingreso += $operacion->getTipoOperacion() === 'CR' ? $operacion->getSaldo() : 0;
            $gasto += $operacion->getTipoOperacion() === 'DB' ? $operacion->getSaldo() : 0;
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
                'operacion_contables' => $operaciones,
                'fecha' => $fecha,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'ingreso' => $ingreso,
                'gasto' => $gasto
            ];

            $renderView = $this->renderView('operacion_contable/indexPDF.html.twig', $options);
            $render = $this->render('operacion_contable/indexPDF.html.twig', $options);

            $namefile = strtoupper('Operaciones_Contables_Fecha: ' . $fecha);
            $namefile = str_replace(['/', ' - '], ['_', '-'], $namefile);

            return $this->imprimirDocumentoGeneral($renderView, $render, $namefile, true, 'Portrait');
        } elseif ($tipo === 'excel') {
            $data = [];
            $columnNames = ['Fecha', 'Tipo operación', 'Código', 'Descripción', 'Saldo Entrada', 'Saldo Salida'];
            foreach ($operaciones as $operacion) {
                $data[] = [
                    $operacion->getCreated()->format('d/m/Y h:i:s a'),
                    $operacion->getTipoOperacion(),
                    $operacion->getCodigo(),
                    $operacion->getDescripcion(),
                    $operacion->getTipoOperacion() === 'CR' ? $operacion->getSaldo() : 0,
                    $operacion->getTipoOperacion() === 'DB' ? $operacion->getSaldo() : 0
                ];
            }
            $namefile = strtoupper('OPERACIONES_CONTABLES_' . $fecha);
            $namefileR = str_replace(['/', ' - ', ':', ' '], ['', '_', '', '_'], $namefile);
            return $this->exportarExcel($namefileR, 'F', $columnNames, $data, $namefileR);
        } else {
            $this->addFlash('danger', 'No es una ruta válida');
            return $this->redirectToReferer($request);
        }
    }
//    /**
//     * @Route("/filtrar", name="operacion_contable_filtrar", methods={"GET","POST"})
//     * @IsGranted("ROLE_RECEPCIONISTA")
//     */
//    public function filtrar(OrdenTrabajoRepository $ordenTrabajoRepository, Request $request): Response
//    {
//        $form = $this->createForm('App\Form\ZFechaType',null);
//        if ($request->getMethod() === 'GET') {
//            $anno = $request->query->get('anno');
//            $mes = $request->query->get('mes');
//            $dia = $request->query->get('dia');
//        } else {
//            $fecha = explode('/',$request->get('z_fecha')['fecha']);
//            $dia = $fecha[0];
//            $mes = $fecha[1];
//            $anno = $fecha[2];
//            return $this->redirectToRoute('orden_trabajo_filtrar', array('anno' => $anno, 'mes' => $mes, 'dia' => $dia));
//        }
//        if($anno and  $mes and $dia){
//            $str = $anno . "/" . $mes . "/" . $dia;
//            $fecha = new \DateTime($str);
//            $hoy = new \DateTime();
//            $fechaInicio = clone $fecha;
//            $fechaFin = $fecha->setTime(23,59,59);
//            $ordenesTrabajo = $this->getDoctrine()->getRepository(OrdenTrabajo::class)->findByDates($fechaInicio, $fechaFin);
//            if ($fecha->format('d/m/Y') === $hoy->format('d/m/Y')) {
//                $title = "Órdenes del hoy";
//            } else {
//                $title = "Órdenes del " . $fecha->format('d/m/Y');
//            }
//        } elseif($anno and  $mes) {
//            $fecha = new \DateTime($anno . "/" . $mes . "/15");
//            $fechaInicioMes = $fecha->modify('first day of this month')->format('d-m-Y');
//            $fechaFinMes = $fecha->modify('last day of this month')->format('d-m-Y');
//            $ordenesTrabajo = $ordenTrabajoRepository->findByDates($fechaInicioMes, $fechaFinMes);
//            $title = "Órdenes del mes " . $mes . "/" . $anno;
//        } elseif($anno) {
//            $fechaInicioAnno = $anno . "/01/01";
//            $fechaFinAnno = $anno . "/12/31";
//            $ordenesTrabajo = $ordenTrabajoRepository->findByDates($fechaInicioAnno, $fechaFinAnno);
//            $title = "Órdenes del año " . $anno;
//        }else {
//            return $this->render('orden_trabajo/filtrarPorFecha.html.twig');
//        }
//        return $this->render('orden_trabajo/index.html.twig', [
//            'orden_trabajos' => $ordenesTrabajo,
//            'title' => $title,
//            'form' => $form->createView()
//        ]);
//    }
//
//    /**
//     * @Route("/filtrar/fecha", name="orden_trabajo_filtrar_fecha", methods={"GET"})
//     * @IsGranted("ROLE_RECEPCIONISTA")
//     */
//    public function filtrarOrdenesFecha(OperacionContableRepository $operacionContableRepository): Response
//    {
//        $form = $this->createForm('App\Form\ZFechaType',null,array('action' => $this->generateUrl('operacion_contable_filtrar')));
//        return $this->render('orden_trabajo/filtrarOrdenesFecha.html.twig', [
//            'form' => $form->createView(),
//        ]);
//    }
//
//    /**
//     * @Route("/filtar/hoy", name="operacion_contable_filtar_hoy", methods={"GET"})
//     */
//    public function filtrarHoy(OperacionContableRepository $operacionContableRepository): Response
//    {
//        $date = new \DateTime();
//        $inicio = clone $date->setTime(0,0,0);
//        $fin = clone $date->setTime(23,59,59);
//        return $this->render('operacion_contable/index.html.twig', [
//            'operacion_contables' => $operacionContableRepository->findByDates($inicio,$fin),
//            'fecha' => $date
//        ]);
//    }
}
