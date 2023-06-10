<?php

namespace App\Controller;

use App\Entity\ComprobanteOperacion;
use App\Form\ComprobanteOperacionType;
use App\Repository\ComprobanteOperacionRepository;
use App\Response\PdfResponse;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/operacion/comprobante")
 */
class ComprobanteOperacionController extends AbstractController
{
    /**
     * @Route("/", name="comprobante_operacion_index", methods={"GET", "POST"})
     */
    public function index(ComprobanteOperacionRepository $comprobanteOperacionRepository, Request $request): Response
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
            return $this->redirect($this->generateUrl('comprobante_operacion_index') . $params);
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
        $comprobantes = $comprobanteOperacionRepository->findByDates($fecha_inicio, $fecha_fin);

        $ingreso = 0;
        $gasto = 0;
        foreach ($comprobantes as $comprobante){
            $ingreso += $comprobante->getImporteTotal();
            $gasto += $comprobante->getGastosAsociados();
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
        if(count($comprobantes) > 0){
            $this->addFlash('success', 'Se listan correctamente ' . count($comprobantes) . ' comprobantes de operaciones');
        }  else {
            $this->addFlash('info', 'No hay comprobantes para el rango seleccionado');
        }
        return $this->render('comprobante_operacion/index.html.twig', [
            'comprobante_operacions' => $comprobantes,
            'fecha' => $fecha,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'ingreso' => $ingreso,
            'gasto' => $gasto,
        ]);
    }

    /**
     * @Route("/exportar/{tipo}/rango", name="comprobante_operacion_exportar_pdfexcel_rango", methods={"GET", "POST"})
     */
    public function exportarPDFExcel(string $tipo, ComprobanteOperacionRepository $comprobanteOperacionRepository, Request $request): Response
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

            $comprobantes = $comprobanteOperacionRepository->findByDates($fecha_inicio, $fecha_fin);

            if(count($comprobantes) === 0){
                $this->addFlash('danger', 'No se puede exportar. No hay comprobantes para el día seleccionado');
                return $this->redirectToReferer($request);
            }
            $ingreso = 0;
            $gasto = 0;
            foreach ($comprobantes as $comprobante){
                $ingreso += $comprobante->getImporteTotal();
                $gasto += $comprobante->getGastosAsociados();
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
                    $renderView = $this->renderView('comprobante_operacion/indexPDF.html.twig', [
                        'comprobante_operacions' => $comprobantes,
                        'fecha' => $fecha,
                        'fecha_inicio' => $fecha_inicio,
                        'fecha_fin' => $fecha_fin,
                        'ingreso' => $ingreso,
                        'gasto' => $gasto,
                    ]);

                    $render = $this->render('comprobante_operacion/indexPDF.html.twig', [
                        'comprobante_operacions' => $comprobantes,
                        'fecha' => $fecha,
                        'fecha_inicio' => $fecha_inicio,
                        'fecha_fin' => $fecha_fin,
                        'ingreso' => $ingreso,
                        'gasto' => $gasto,
                    ]);

                    $namefile = strtoupper('Comprobantes de operaciones_Fecha: ' . $fecha);
                    $namefile = str_replace(['/',' - '],['_', '-'], $namefile);

                    return $this->imprimirDocumentoGeneral($renderView, $render,$namefile, true, 'Portrait');
                } elseif($tipo === 'excel') {
                    $data = [];
                    $columnNames = ['Fecha','Nro.Comprobante','Tipo de operación','Equipo','Importe total', 'Gasto total', 'Técnico'];
                    foreach($comprobantes as $comprobante){
                        $data[] = [
                            $comprobante->getCreated()->format('d/m/Y h:i:s a'),
                            $comprobante->getNroComprobante(),
                            $comprobante->getTipoOperacion(),
                            $comprobante->getOrdenTrabajo()->getDatosEquipo(),
                            $comprobante->getImporteTotal(),
                            $comprobante->getGastosAsociados(),
                            $comprobante->getOrdenTrabajo()->getOrdenReparacion()->getRevisadoPor()->getNombreCompleto()
                        ];
                    }

                    $namefile = strtoupper('Operaciones_' . $fecha);
                    $namefileR = str_replace(['/',' - ',':',' '],['', '_', '', '_'], $namefile);
                    return $this->exportarExcel($namefileR,'G',$columnNames,$data,$namefileR);
                } else {
                    $this->addFlash('danger', 'No es una ruta válida');
                    return $this->redirectToReferer($request);
                }


    }

    /**
     * @Route("/filtrar/hoy", name="comprobante_operacion_jornada_hoy", methods={"GET"})
     */
    public function filtrarHoy(ComprobanteOperacionRepository $comprobanteOperacionRepository): Response
    {
        $hoy = new \DateTime();
        return $this->redirect($this->generateUrl('comprobante_operacion_index') . '?fecha=' . $hoy->format('Ymd'));
    }

    /**
     * @Route("/{id}", name="comprobante_operacion_show", methods={"GET"})
     */
    public function show(ComprobanteOperacion $comprobanteOperacion): Response
    {
        return $this->render('comprobante_operacion/show.html.twig', [
            'comprobante_operacion' => $comprobanteOperacion,
        ]);
    }
}
