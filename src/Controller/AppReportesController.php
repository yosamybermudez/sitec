<?php

namespace App\Controller;

use App\Entity\ComprobanteOperacion;
use App\Entity\OrdenReparacion;
use App\Entity\OrdenTrabajo;
use App\Repository\ComprobanteOperacionRepository;
use App\Response\PdfResponse;
use Knp\Snappy\Pdf;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reportes")
 */
class AppReportesController extends AbstractController
{
    /**
     * @Route("/", name="app_reportes_index")
     */
    public function index()
    {
        $enlaces = array();

        $enlaces[] = array(
            'nombre' => 'Reporte diario',
            'enlace_directo' => $this->generateUrl('app_reportes_rangos', array('tipo' => 'diario')),
            'icon' => 'news-paper',
        );
        $enlaces[] = array(
            'nombre' => 'Reporte semanal',
            'enlace_directo' => $this->generateUrl('app_reportes_rangos', array('tipo' => 'semanal')),
            'icon' => 'news-paper',
        );
        $enlaces[] = array(
            'nombre' => 'Reporte mensual',
            'enlace_directo' => $this->generateUrl('app_reportes_rangos', array('tipo' => 'mensual')),
            'icon' => 'news-paper',
        );
        $enlaces[] = array(
            'nombre' => 'Reporte anual',
            'enlace_directo' => $this->generateUrl('app_reportes_rangos', array('tipo' => 'anual')),
            'icon' => 'news-paper',
        );
         return $this->render('app_reportes/index.html.twig', [
             'enlaces' => $enlaces
        ]);
    }

    public function datosReportes(string $tipo, string $queryDate = null){
        $fecha = new \DateTime($queryDate);
        switch ($tipo){
            case 'diario' :
                $start = clone $fecha;
                $end = clone $fecha;
                $start->setTime(0,0,0);
                $end->setTime(23,59,59,999999);
                break;
            case 'semanal' :
                $start = clone $fecha;
                $end = clone $fecha;
                $start->modify('this week');
                $end->modify('this week + 6 days');
                $start->setTime(0,0,0);
                $end->setTime(23,59,59,999999);
                break;
            case 'mensual' :
                $start = clone $fecha;
                $end = clone $fecha;
                $start->modify('first day of this month');
                $end->modify('last day of this month');
                $start->setTime(0,0,0);
                $end->setTime(23,59,59,999999);
                break;
            case 'anual' :
                $start = clone $fecha;
                $end = clone $fecha;
                $start->modify('first day of January');
                $end->modify('last day of December');
                $start->setTime(0,0,0);
                $end->setTime(23,59,59,999999);
                break;
            default: $start = null; $end = null; break;
        }


        if($start === null and $end === null){
            $data = null;
        } else {
            $ordenTrabajoRepository = $this->getDoctrine()->getRepository(OrdenTrabajo::class);
            $ordenesTrabajoRangoTodas = $ordenTrabajoRepository->findByTodasLasFechas($start, $end);

            $comprobanteOperacionRepository = $this->getDoctrine()->getRepository(ComprobanteOperacion::class);
            $totalIngresosPorFecha = $comprobanteOperacionRepository->findByFechasTotalIngreso($start,$end);

            $ordenReparacionRepository = $this->getDoctrine()->getRepository(OrdenReparacion::class);
            $totalGastosPorFecha = $ordenReparacionRepository->findByFechasTotalGastos($start,$end);

            $ordenesTrabajoRango['fechaEntrada'] = $ordenTrabajoRepository->findByFechaEntrada($start,$end);
            $ordenesTrabajoRango['fechaDictamen'] = $ordenTrabajoRepository->findByFechaDictamen($start,$end);
            $ordenesTrabajoRango['fechaSalida'] = $ordenTrabajoRepository->findByFechaSalida($start, $end);
            $ordenesTrabajoRango['fechaListoEntregar'] = $ordenTrabajoRepository->findByFechaListoEntregar($start, $end);
            $ordenesTrabajoRango['fechaNotificacion'] = $ordenTrabajoRepository->findByFechaNotificacion($start, $end);
            $ordenesTrabajoRango['fechaFacturacion'] = $ordenTrabajoRepository->findByFechaFacturacion($start, $end);
            $ordenesTrabajoRango['fechaDecomiso'] = $ordenTrabajoRepository->findByFechaDecomiso($start, $end);

            $data = array();
            $data['ordenesTrabajoRangoTodas'] = $ordenesTrabajoRangoTodas;
            $data['ordenesTrabajoRango'] = $ordenesTrabajoRango;
            $data['totalIngresos'] = $totalIngresosPorFecha;
            $data['totalGastos'] = $totalGastosPorFecha;
            $data['start'] = $start;
            $data['end'] = $end;
        }
        return $data;

    }
    /**
    * @Route("/{tipo}", name="app_reportes_rangos", methods={"GET", "POST"})
     */
    public function reporteRango(string $tipo, Request $request)
    {
        if($request->getMethod() === 'POST'){
            $queryDate = $request->request->get('fecha_submit');
        } else {
            $queryDate = $request->query->get('fecha');
        }

        $data = $this->datosReportes($tipo,$queryDate);
        if($data === null){
            return $this->redirectToRoute('app_reportes_rangos', array('tipo' => 'diario'));
        }
        $ordenesTrabajoRango = $data['ordenesTrabajoRango'];
        $ordenesTrabajoRangoTodas = $data['ordenesTrabajoRangoTodas'];


        return $this->render('app_reportes/reporte.html.twig', [
            'ordenesTrabajoRango' => $ordenesTrabajoRango,
            'ordenesTrabajoRangoTodas' => $ordenesTrabajoRangoTodas,
            'tipo' => $tipo,
            'start' => $data['start'],
            'end' => $data['end'],
            'ingresos' => $data['totalIngresos'],
            'gastos' => $data['totalGastos'],
            'fecha' => $queryDate
        ]);
    }

    /**
     * @Route("/{tipo}/imprimir", name="app_reportes_rangos_imprimir")
     */
    public function  reporteRangoImprimir(Request $request, string $tipo){

        // logica
        $queryDate = $request->query->get('fecha');
        $data = $this->datosReportes($tipo,$queryDate);
        $ordenesTrabajoRango = $data['ordenesTrabajoRango'];
        $ordenesTrabajoRangoTodas = $data['ordenesTrabajoRangoTodas'];

        //exportacion

        $html = $this->renderView('app_reportes/reportePDF.html.twig',
            array(
                'ordenesTrabajoRango' => $ordenesTrabajoRango,
                'ordenesTrabajoRangoTodas' => $ordenesTrabajoRangoTodas,
                'tipo' => $tipo,
                'start' => $data['start'],
                'end' => $data['end'],
                'ingresos' => $data['totalIngresos'],
                'gastos' => $data['totalGastos'],
                'fecha' => $queryDate
            ));
        $namefile = 'Reporte de trabajo ' . $tipo . '_' . $data['start']->format('Y-m-d') . '_' . $data['end']->format('Y-m-d');
        $knpSnappy = new Pdf($this->getParameter('kernel.project_dir') . '/config/wkhtmltopdf/bin/wkhtmltopdf.exe');
        $a = 2;
        return $a === 1 ? $this->render('orden_trabajo/modeloEntregaEquipo.html.twig',
            array(

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
                        'orientation' => 'Landscape',
                        'title' => $namefile,
                        'footer-left' => 'Impreso por ' . ($this->getDatabaseUser()->getNombreCompleto() && $this->getDatabaseUser()->getNombreCompleto() != ' ' ? $this->getDatabaseUser()->getNombreCompleto() : $this->getDatabaseUser()->getUsername()),
                        'footer-right' => 'Pag. [page] de [toPage]',
                        'footer-font-size' => 8,
                        'margin-top' => 10,
                        'margin-bottom' => 20
                    )
                ),
                $namefile.'.pdf'
            );
    }
}
