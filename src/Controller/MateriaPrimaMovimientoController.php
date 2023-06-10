<?php

namespace App\Controller;

use App\Entity\MateriaPrimaMovimiento;
use App\Form\EntradaMateriaPrimaType;
use App\Form\MateriaPrimaMovimientoType;
use App\Repository\MateriaPrimaMovimientoRepository;
use App\Repository\MateriaPrimaRepository;
use App\Response\PdfResponse;
use App\Twig\Extension\FiltersExtension;
use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/movimiento/materia/prima")
 */
class MateriaPrimaMovimientoController extends AbstractController
{
    /**
     * @Route("/", name="materia_prima_movimiento_index", methods={"GET", "POST"})
     */
    public function index(MateriaPrimaMovimientoRepository $materiaPrimaMovimientoRepository, Request $request): Response
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
            return $this->redirect($this->generateUrl('materia_prima_movimiento_index') . $params);
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
        $result = $materiaPrimaMovimientoRepository->findByDates($fecha_inicio, $fecha_fin);

        $array = array();
        foreach ($result as $elem){
            $movimientos = $materiaPrimaMovimientoRepository->findBy(array('nroMovimiento' => $elem[1]));
            foreach ($movimientos as $item){
                $array[$elem[1]][] = $item;
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
        if(count($result) > 0){
            $this->addFlash('success', 'Se listan correctamente ' . count($result) . ' movimientos de materias primas');
        }  else {
            $this->addFlash('info', 'No hay movimientos de materias primas para el rango seleccionado');
        }
        return $this->render('materia_prima_movimiento/index.html.twig', [
            'materia_prima_movimientos' => $array,
            'fecha' => $fecha,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ]);
    }

    /**
     * @Route("/exportar/{tipo}/rango", name="materia_prima_movimiento_exportar_pdfexcel_rango", methods={"GET", "POST"})
     */
    public function exportarPDFExcel(string $tipo, MateriaPrimaMovimientoRepository $materiaPrimaMovimientoRepository, Request $request): Response
    {
        $req = $request->query;
        $fecha_param = $req->get('fecha');
        if(!$fecha_param){
            $fecha_inicio = $req->get('inicio') ? new \DateTime($req->get('inicio')) : null;
            $fecha_fin = $req->get('fin')? new \DateTime($req->get('fin')) : null;
        } else {
            $fecha_inicio = new \DateTime($fecha_param);
            $fecha_fin = new \DateTime($fecha_param);
        }
    //
        $result = $materiaPrimaMovimientoRepository->findByDates($fecha_inicio, $fecha_fin);

        $array = array();
        foreach ($result as $elem){
            $movimientos = $materiaPrimaMovimientoRepository->findBy(array('nroMovimiento' => $elem[1]));
            foreach ($movimientos as $item){
                $array[$elem[1]][] = $item;
            }
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
                'materia_prima_movimientos' => $array,
                'fecha' => $fecha,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
            ];

            $renderView = $this->renderView('materia_prima_movimiento/indexPDF.html.twig', $options);
            $render = $this->render('materia_prima_movimiento/indexPDF.html.twig', $options);

            $namefile = strtoupper('Movimientos de materias primas_Fecha: ' . $fecha);
            $namefile = str_replace(['/',' - '],['_', '-'], $namefile);

            return $this->imprimirDocumentoGeneral($renderView, $render,$namefile, true, 'Portrait');
        } elseif($tipo === 'excel') {
            $filters = new FiltersExtension();
            $data = [];
            $columnNames = ['Fecha','Nro.Movimiento', 'Materia prima', 'Cantidad','Tipo de movimiento','Motivo'];
            foreach($array as $key => $item){
                $a = false;
                foreach ($item as $mov){
                    $data[] = [
                        $mov->getCreated()->format('d/m/Y h:i:s a'),
                        $a === false ? $key : null,
                        $mov->getMateriaPrima()->getNombre(),
                        $mov->getCantidad(),
                        $filters->traducirSiglas($mov->getTipo()),
                        $a === false ? $mov->getOrdenReparacion() !== null ? 'Orden de trabajo' : 'Entrada de MP' : null
                    ];
                    $a = true;
                }
            }

            $namefile = strtoupper('MP_MOVIMIENTOS_' . $fecha);
            $namefileR = str_replace(['/',' - ',':',' '],['', '_', '', '_'], $namefile);
            return $this->exportarExcel($namefileR,'F', $columnNames, $data, $namefileR);
        } else {
            $this->addFlash('danger', 'No es una ruta válida');
            return $this->redirectToReferer($request);
        }
    }

    /**
     * @Route("/new", name="materia_prima_movimiento_new", methods={"GET","POST"})
     */
//    public function new(Request $request): Response
//    {
//        $materiaPrimaMovimiento = new MateriaPrimaMovimiento();
//        $form = $this->createForm(MateriaPrimaMovimientoType::class, $materiaPrimaMovimiento);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $nextNroMovimiento = $this->getDoctrine()->getRepository(MateriaPrimaMovimiento::class)->nextNroMovimiento();
//            $materiaPrimaMovimiento->setNroMovimiento($nextNroMovimiento);
//
//            $entityManager = $this->getDoctrine()->getManager();
//            $this->updatedData($materiaPrimaMovimiento);
//            $entityManager->persist($materiaPrimaMovimiento);
//
//            //Evento
//            $texto = 'Registro de Movimiento de Materia Prima ' . $materiaPrimaMovimiento->getMateriaPrima()->getNombreCantidadPrecio();
//            $this->registrarEvento('Registro','MateriaPrimaMovimiento', $materiaPrimaMovimiento->getId(), $texto);
//            // Fin evento
//
//            $entityManager->flush();
//
//            return $this->redirectToRoute('materia_prima_movimiento_index');
//        }
//
//        return $this->render('materia_prima_movimiento/new.html.twig', [
//            'materia_prima_movimiento' => $materiaPrimaMovimiento,
//            'form' => $form->createView(),
//        ]);
//    }

    /**
     * @Route("/{id}", name="materia_prima_movimiento_show", methods={"GET"})
     */
    public function show(MateriaPrimaMovimiento $materiaPrimaMovimiento, MateriaPrimaMovimientoRepository $materiaPrimaMovimientoRepository): Response
    {
        $nroMovimiento = $materiaPrimaMovimiento->getNroMovimiento();

        $movimientos = $materiaPrimaMovimientoRepository->findByNroMovimiento($nroMovimiento);

        return $this->render('materia_prima_movimiento/show.html.twig', [
            'materia_prima_movimiento' => $movimientos,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="materia_prima_movimiento_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MateriaPrimaMovimiento $materiaPrimaMovimiento): Response
    {
//        $form = $this->createForm(MateriaPrimaMovimientoType::class, $materiaPrimaMovimiento);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            //Evento
//            $texto = 'Modificación de la Orden de Trabajo # ' . $ordenTrabajo->getNroOrden();
//            $this->registrarEvento('Modificación','OrdenTrabajo', $ordenTrabajo->getId(), $texto);
//            // Fin evento
//            $this->getDoctrine()->getManager()->flush();
//
//            return $this->redirectToRoute('materia_prima_movimiento_index');
//        }
//
//        return $this->render('materia_prima_movimiento/edit.html.twig', [
//            'materia_prima_movimiento' => $materiaPrimaMovimiento,
//            'form' => $form->createView(),
//        ]);
        $this->denyEditElement();
        return $this->redirectToRoute('materia_prima_movimiento_index');
    }

    /**
     * @Route("/{id}", name="materia_prima_movimiento_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMINISTRADOR_NEGOCIO")
     */
    public function delete(Request $request, MateriaPrimaMovimiento $materiaPrimaMovimiento): Response
    {
        if($materiaPrimaMovimiento->getEstaConfirmado()){
            $this->addFlash('No puede deshacer el movimiento. Ya se ha confirmado.');
            return $this->redirectToRoute('materia_prima_movimiento_show', array('id' => $materiaPrimaMovimiento->getId()));
        }
        $form = $this->createDeleteForm($materiaPrimaMovimiento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //
            $em = $this->getDoctrine()->getManager();
            // Aqui hay que incluir una logica, para si se elimina un movimiento. Hay que revertir para atras.
            $em->remove($materiaPrimaMovimiento);
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
    private function createDeleteForm(MateriaPrimaMovimiento $materiaPrimaMovimiento)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('materia_prima_movimiento_delete', array('id' => $materiaPrimaMovimiento->getId())))
            ->setMethod('POST')
            ->getForm()
            ;
    }

    /**
     * @Route("/{id}/imprimir", name="materia_prima_movimiento_imprimir", methods={"GET"})
     */
    public function imprimir(MateriaPrimaMovimiento $materiaPrimaMovimiento, MateriaPrimaMovimientoRepository $materiaPrimaMovimientoRepository): Response {

        $nroMovimiento = $materiaPrimaMovimiento->getNroMovimiento();

        $movimientos = $materiaPrimaMovimientoRepository->findByNroMovimiento($nroMovimiento);

        $render = $this->render('materia_prima_movimiento/show.html.twig', [
            'materia_prima_movimiento' => $movimientos,
        ]);

        $renderView = $this->renderView('materia_prima_movimiento/showPDF.html.twig', [
            'materia_prima_movimiento' => $movimientos,
        ]);

        $namefile = 'Movimiento de materia prima - ' . $movimientos[0]->getNroMovimiento();

        return $this->imprimirDocumentoGeneral($renderView, $render, strtoupper($namefile), true, 'Portrait');
    }
}
