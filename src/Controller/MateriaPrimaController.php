<?php

namespace App\Controller;

use App\Entity\MateriaPrima;
use App\Entity\MateriaPrimaEntrada;
use App\Entity\MateriaPrimaMovimiento;
use App\Form\MateriaPrimaType;
use App\Repository\MateriaPrimaRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/materia/prima")
 */
class MateriaPrimaController extends AbstractController
{
    /**
     * @Route("/", name="materia_prima_index", methods={"GET"})
     */
    public function index(MateriaPrimaRepository $materiaPrimaRepository): Response
    {
        return $this->render('materia_prima/index.html.twig', [
            'materia_primas' => $materiaPrimaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/listar/existencias", name="materia_prima_existencias", methods={"GET"})
     */
    public function indexExistencias(MateriaPrimaRepository $materiaPrimaRepository): Response
    {
        return $this->render('materia_prima/index.html.twig', [
            'materia_primas' => $materiaPrimaRepository->findAllEnExistencia(),
            'existencias' => true
        ]);
    }

    /**
     * @Route("/exportar/existencias", name="materia_prima_exportar_existencias", methods={"GET"})
     */
    public function exportarExistencias(MateriaPrimaRepository $materiaPrimaRepository): Response
    {
        $title = 'Materias primas en existencia';
        $lastColumn = 'F';
        $columnNames = ['Nombre', 'Descripción', 'U/M', 'Cantidad', 'Precio', 'Equipo destino'];
        $materiasPrimas = $materiaPrimaRepository->findAllEnExistencia();
        $data = [];
        foreach ($materiasPrimas as $materiaPrima){
            $data[] = [
                $materiaPrima->getNombre(),
                $materiaPrima->getDescripcion(),
                $materiaPrima->getUnidadMedida(),
                $materiaPrima->getCantidad(),
               $materiaPrima->getPrecio(),
                $materiaPrima->getTipoEquipoDestino()->getNombre(),
            ];
        }
        $now = new \DateTime();
        $namefile = 'MATERIAS PRIMAS DISPONIBLES - ' . $now->format('YmdHis');
        return $this->exportarExcel($title,$lastColumn, $columnNames, $data, $namefile);
    }

    /**
     * @Route("/imprimir/existencias", name="materia_prima_imprimir_existencias", methods={"GET"})
     */
    public function imprimirExistencias(Request $request, MateriaPrimaRepository $materiaPrimaRepository): Response {
        $renderView = $this->renderView('materia_prima/indexPDF.html.twig', [
            'materia_primas' => $materiaPrimaRepository->findAllEnExistencia(),
            'existencias' => true
        ]);

        $render = $this->render('materia_prima/indexPDF.html.twig', [
            'materia_primas' => $materiaPrimaRepository->findAllEnExistencia(),
            'existencias' => true
        ]);
        $now = new \DateTime();
        $namefile = 'MATERIAS PRIMAS DISPONIBLES - ' . $now->format('YmdHis');
        return $this->imprimirDocumentoGeneral($renderView, $render, $namefile, true );
    }

    /**
     * @Route("/new", name="materia_prima_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMINISTRACION")
     */
    public function new(Request $request): Response
    {
        return $this->redirectToRoute('materia_prima_entrada_new');
//        $materiaPrima = new MateriaPrima($this);
//        $form = $this->createForm(MateriaPrimaType::class, $materiaPrima);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager = $this->getDoctrine()->getManager();
//
//            $movimiento = new MateriaPrimaMovimiento($this);
//            $nextNroMovimiento = $this->getDoctrine()->getRepository(MateriaPrimaMovimiento::class)->nextNroMovimiento();
//            $movimiento->setNroMovimiento($nextNroMovimiento);
//            $movimiento->setTipo('E');
//            $movimiento->setCantidad($materiaPrima->getCantidad());
//            $movimiento->setMateriaPrima($materiaPrima);
//
//            $this->updatedData($materiaPrima);
//            $entityManager->persist($materiaPrima);
//
//            //Evento
//            $texto = 'Registro de Materia Prima ' . $materiaPrima->getNombreCantidadPrecio();
//            $this->registrarEvento('Registro','MateriaPrima', $materiaPrima->getId(), $texto);
//            // Fin evento
//
//            $this->updatedData($movimiento);
//            $entityManager->persist($movimiento);
//
//            //Evento
//            $texto = 'Registro de Movimiento de Materia Prima ' . $materiaPrima->getNombreCantidadPrecio();
//            $this->registrarEvento('Registro','MateriaPrimaMovimiento', $movimiento->getId(), $texto);
//            // Fin evento
//
//            $entityManager->flush();
//            $this->successNewElement();
//
//            return $this->redirectToRoute('materia_prima_index');
//        }
//
//        return $this->render('materia_prima/new.html.twig', [
//            'materia_prima' => $materiaPrima,
//            'form' => $form->createView(),
//        ]);
    }

    /**
     * @Route("/{id}", name="materia_prima_show", methods={"GET"})
     */
    public function show(MateriaPrima $materiaPrima): Response
    {
        $deleteForm = $this->createDeleteForm($materiaPrima);

        return $this->render('materia_prima/show.html.twig', [
            'materia_prima' => $materiaPrima,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="materia_prima_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMINISTRADOR_NEGOCIO")
     */
    public function edit(Request $request, MateriaPrima $materiaPrima): Response
    {
        $form = $this->createForm(MateriaPrimaType::class, $materiaPrima);
        $form->remove('cantidad');
        $form->remove('precio');
        $form->remove('unidadMedida');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->updatedData($materiaPrima);
            $this->getDoctrine()->getManager()->flush();
            $this->successEditElement();
            return $this->redirectToRoute('materia_prima_index');
        }

        return $this->render('materia_prima/new.html.twig', [
            'materia_prima' => $materiaPrima,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a cargo entity.
     *
     * @Route("/{id}", name="materia_prima_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMINISTRADOR_NEGOCIO")
     */

    public function deleteAction(Request $request, MateriaPrima $materiaPrima)
    {
        $form = $this->createDeleteForm($materiaPrima);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($materiaPrima);
            try{
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al eliminar. Existen elementos dependientes.');
                return $this->redirect($request->headers->get('referer'));
            }
            $this->addFlash('success', 'Elemento eliminado satisfactoriamente.');
        } else {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(MateriaPrima::class)->find($request->get('id'));
            $em->remove($entity);
            try{
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al eliminar. Existen elementos dependientes.');
                return $this->redirect($request->headers->get('referer'));
            }
            $this->addFlash('success', 'Elemento eliminado satisfactoriamente.');
        }

        return $this->redirectToRoute('materia_prima_index');
    }

    /**
     * Creates a form to delete an entity.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MateriaPrima $materiaPrima)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('materia_prima_delete', array('id' => $materiaPrima->getId())))
            ->setMethod('POST')
            ->getForm()
            ;
    }

    public function precioMateriaPrima(MateriaPrima $materiaPrima): Response
    {
        return $this->render('default/renderNumber.html.twig', [
            'number' => $materiaPrima->getPrecio(),
        ]);
    }
}
