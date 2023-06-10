<?php

namespace App\Controller;

use App\Entity\EquipoTipo;
use App\Entity\MateriaPrima;
use App\Entity\MateriaPrimaEntrada;
use App\Entity\MateriaPrimaMovimiento;
use App\Form\MateriaPrimaEntradaType;
use App\Repository\MateriaPrimaEntradaRepository;
use App\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/materia/prima/movimiento/entrada")
 */
class MateriaPrimaEntradaController extends AbstractController
{
    /**
     * @Route("/", name="materia_prima_entrada_index", methods={"GET", "POST"})
     */
    public function index(MateriaPrimaEntradaRepository $materiaPrimaEntradaRepository, Request $request): Response
    {
        $hoy = new \DateTime();
        // Filtrar fechas
        if($request->getMethod() === 'POST' && isset($request->request->all()["submit"]))
        {
            $req = $request->request->all();
            $params = '';
            $fecha_inicio = $req['fecha_inicio'] !== '' ? new \DateTime($req['fecha_inicio']) : null;
            $fecha_fin = $req['fecha_fin'] !== '' ? new \DateTime($req['fecha_fin']) : null;
            if($fecha_inicio && $fecha_fin){
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
            return $this->redirect($this->generateUrl('materia_prima_entrada_index') . $params);
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
        $entradas = $materiaPrimaEntradaRepository->findByDates($fecha_inicio, $fecha_fin);

        $cantidades = 0;
        $gasto = 0;
        foreach ($entradas as $index){
            $cantidades += count($index->getMovimientosMateriaPrima());
            $gasto += $index->getImporteTotal();
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
        if(count($entradas) > 0){
            $this->addFlash('success', 'Se listan correctamente ' . count($entradas) . ' entradas de materias primas');
        }  else {
            $this->addFlash('info', 'No hay entradas de materias primas para el rango seleccionado');
        }
        return $this->render('materia_prima_entrada/index.html.twig', [
            'materia_prima_entradas' => $entradas,
            'fecha' => $fecha,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'cantidades' => $cantidades,
            'gasto' => $gasto
        ]);
    }

    /**
     * @Route("/exportar/{tipo}/rango", name="materia_prima_entrada_exportar_pdfexcel_rango", methods={"GET", "POST"})
     */
    public function exportarPDFExcel(string $tipo, MateriaPrimaEntradaRepository $materiaPrimaEntradaRepository, Request $request): Response
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

        $result = $materiaPrimaEntradaRepository->findByDates($fecha_inicio, $fecha_fin);

        if(count($result) === 0){
            $this->addFlash('danger', 'No se puede exportar. No hay entradas de materias primsa para el día seleccionado');
            return $this->redirectToReferer($request);
        }
        $cantidades = 0;
        $gasto = 0;
        foreach ($result as $item){
            $cantidades += $item->getNroMaterialesComprados();
            $gasto += $item->getImporteTotal();
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
                'materia_prima_entradas' => $result,
                'fecha' => $fecha,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'cantidades' => $cantidades,
                'gasto' => $gasto,
            ];

            $renderView = $this->renderView('materia_prima_entrada/indexPDF.html.twig', $options);
            $render = $this->render('materia_prima_entrada/indexPDF.html.twig', $options);

            $namefile = strtoupper('Entradas de materias primas_Fecha: ' . $fecha);
            $namefile = str_replace(['/',' - '],['_', '-'], $namefile);

            return $this->imprimirDocumentoGeneral($renderView, $render,$namefile, true, 'Portrait');
        } elseif($tipo === 'excel') {
            $data = [];
            $columnNames = ['Fecha','Nro.Movimiento','Realizada por','Movimientos realizados','Importe total'];
            foreach($result as $item){
                $data[] = [
                    $item->getCreated()->format('d/m/Y h:i:s a'),
                    $item->getMovimientosMateriaPrima()[0]->getNroMovimiento(),
                    $item->getRealizadaPor()->getNombreCompleto(),
                    count($item->getMovimientosMateriaPrima()),
                    $item->getImporteTotal()
                ];
            }

            $namefile = strtoupper('MP_Entradas_' . $fecha);
            $namefileR = str_replace(['/',' - ',':',' '],['', '_', '', '_'], $namefile);
            return $this->exportarExcel($namefileR,'E',$columnNames,$data,$namefileR);
        } else {
            $this->addFlash('danger', 'No es una ruta válida');
            return $this->redirectToReferer($request);
        }
    }

    /**
     * @Route("/new", name="materia_prima_entrada_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $materiaPrimaEntrada = new MateriaPrimaEntrada($this);
        $form = $this->createForm(MateriaPrimaEntradaType::class, $materiaPrimaEntrada);
        $form->handleRequest($request);
        $materialesRegistrados = $entityManager->getRepository(MateriaPrima::class)->findBy(array(), array('nombre' => 'ASC'));
        $tiposEquipo = $entityManager->getRepository(EquipoTipo::class)->findAll();
        if ($form->isSubmitted() && $form->isValid()) {
            $nextNroMovimiento = $this->getDoctrine()->getRepository(MateriaPrimaMovimiento::class)->nextNroMovimiento();
            //Materias primas registradas
            $materiasPrimasRegistradas = $request->request->get('materiaprima_registrada');
            $nroMaterialesComprados = 0;
            if ($materiasPrimasRegistradas) {
                foreach ($materiasPrimasRegistradas as $mpr) {
                    $movimiento = new MateriaPrimaMovimiento($this);
                    $materiaPrima = $entityManager->getRepository(MateriaPrima::class)->find($mpr['materiaprima']);
                    $movimiento->setCantidadRestante($materiaPrima->getCantidad() + $mpr['cantidad']);
                    $materiaPrima->setCantidad($materiaPrima->getCantidad() + $mpr['cantidad']);
                    $movimiento->setNroMovimiento($nextNroMovimiento);
                    $movimiento->setEntradaMateriaPrima($materiaPrimaEntrada);
                    $movimiento->setTipo('E');
                    $movimiento->setCantidad($mpr['cantidad']);
                    $movimiento->setMateriaPrima($materiaPrima);
                    $nroMaterialesComprados += $mpr['cantidad'];
                    $materiaPrimaEntrada->setImporteTotal($materiaPrimaEntrada->getImporteTotal() + ($mpr['cantidad'] * $materiaPrima->getPrecio()));
                    $this->updatedData($materiaPrima);
                    $entityManager->persist($materiaPrima);
                    $this->updatedData($movimiento);
                    $entityManager->persist($movimiento);
                }
            }

            //Materias primas no registradas
            $materiasPrimasNoRegistradas = $request->request->get('materiaprima');
            if ($materiasPrimasNoRegistradas) {
                foreach ($materiasPrimasNoRegistradas as $mpnr) {
                    $materiaPrima = new MateriaPrima($this);
                    $materiaPrima->setNombre($mpnr['nombre']);
                    $materiaPrima->setCantidad($mpnr['cantidad']);
                    $materiaPrima->setUnidadMedida($mpnr['unidadmedida']);
                    $materiaPrima->setPrecio($mpnr['precio']);
                    $tipoEquipoDestino = $entityManager->getRepository(EquipoTipo::class)->find($mpnr['tipoequipo']);
                    $materiaPrima->setTipoEquipoDestino($tipoEquipoDestino);

                    $movimiento = new MateriaPrimaMovimiento($this);
                    $movimiento->setNroMovimiento($nextNroMovimiento);
                    $movimiento->setEntradaMateriaPrima($materiaPrimaEntrada);
                    $movimiento->setTipo('E');
                    $movimiento->setCantidad($mpnr['cantidad']);
                    $movimiento->setCantidadRestante($mpnr['cantidad']);
                    $movimiento->setMateriaPrima($materiaPrima);
                    $nroMaterialesComprados += $mpnr['cantidad'];
                    $materiaPrimaEntrada->setImporteTotal($materiaPrimaEntrada->getImporteTotal() + ($mpnr['cantidad'] * $mpnr['precio']));
                    $this->updatedData($materiaPrima);
                    $entityManager->persist($materiaPrima);
                    $this->updatedData($movimiento);
                    $entityManager->persist($movimiento);
                }
            }
            $materiaPrimaEntrada->setNroMaterialesComprados($nroMaterialesComprados);
            $this->updatedData($materiaPrimaEntrada);
            $entityManager->persist($materiaPrimaEntrada);
            $this->successNewElement();
            $entityManager->flush();

            return $this->redirectToRoute('materia_prima_entrada_show', array('id' => $materiaPrimaEntrada->getId()));
        }
        return $this->render('materia_prima_entrada/new.html.twig', [
            'materia_prima_entrada' => $materiaPrimaEntrada,
            'form' => $form->createView(),
            'tipos_equipo' => $tiposEquipo,
            'materiales_registrados' => $materialesRegistrados,

        ]);
    }

    /**
     * @Route("/{id}", name="materia_prima_entrada_show", methods={"GET"})
     */
    public function show(MateriaPrimaEntrada $materiaPrimaEntrada): Response
    {
        return $this->render('materia_prima_entrada/show.html.twig', [
            'materia_prima_entrada' => $materiaPrimaEntrada,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="materia_prima_entrada_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MateriaPrimaEntrada $materiaPrimaEntrada): Response
    {
        $form = $this->createForm(MateriaPrimaEntradaType::class, $materiaPrimaEntrada);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('materia_prima_entrada_index');
        }

        return $this->render('materia_prima_entrada/new.html.twig', [
            'materia_prima_entrada' => $materiaPrimaEntrada,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/imprimir", name="materia_prima_entrada_imprimir", methods={"GET"})
     */
    public function imprimir(Request $request, MateriaPrimaEntrada $materiaPrimaEntrada): Response {
        $html = $this->renderView('materia_prima_entrada/showPDF.html.twig',
            array(
                'materia_prima_entrada' => $materiaPrimaEntrada
            ));
        $namefile = 'ENTRADA DE MATERIAS PRIMA ' . $materiaPrimaEntrada->getMovimientosMateriaPrima()[0]->getNroMovimiento();
        $knpSnappy = new Pdf($this->getParameter('kernel.project_dir') . '/config/wkhtmltopdf/bin/wkhtmltopdf.exe');
        $a = 1;
        return $a === 1 ?
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
                        'footer-left' => 'Impreso por ' . ($this->getDatabaseUser()->getNombreCompleto() && $this->getDatabaseUser()->getNombreCompleto() != ' ' ? $this->getDatabaseUser()->getNombreCompleto() : $this->getDatabaseUser()->getUsername()),
                        'footer-right' => 'Pag. [page] de [toPage]',
                        'footer-font-size' => 8,
                        'margin-top' => 10,
                        'margin-bottom' => 20
                    )
                ),
                $namefile.'.pdf'
            ) :  $this->render('materia_prima_entrada/showPDF.html.twig',
                array(
                    'materia_prima_entrada' => $materiaPrimaEntrada
                )) ;
    }


    /**
     * @Route("/{id}", name="materia_prima_entrada_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MateriaPrimaEntrada $materiaPrimaEntrada): Response
    {
        if ($this->isCsrfTokenValid('delete'.$materiaPrimaEntrada->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($materiaPrimaEntrada);
            $entityManager->flush();
        }

        return $this->redirectToRoute('materia_prima_entrada_index');
    }
}
