<?php

namespace App\Controller;

use App\Entity\EquipoTipo;
use App\Entity\Rol;
use App\Form\EquipoTipoType;
use App\Repository\EquipoTipoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("sistema/equipo/tipo")
 */
class EquipoTipoController extends AbstractController
{
    /**
     * @Route("/", name="equipo_tipo_index", methods={"GET"})
     */
    public function index(EquipoTipoRepository $equipoTipoRepository): Response
    {
        return $this->render('equipo_tipo/index.html.twig', [
            'equipo_tipos' => $equipoTipoRepository->findBy(array(), array('nombre' => 'ASC')),
        ]);
    }

    /**
     * @Route("/new", name="equipo_tipo_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $equipoTipo = new EquipoTipo($this);
        $form = $this->createForm(EquipoTipoType::class, $equipoTipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $this->updatedData($equipoTipo);
            $entityManager->persist($equipoTipo);
            $entityManager->flush();
            $this->successNewElement();
            return $this->nextAction(
                $request,
                $this->generateUrl('equipo_tipo_new'),
                $this->generateUrl('equipo_tipo_show', array('id' => $equipoTipo->getId()))
            );
        }

        return $this->render('equipo_tipo/new.html.twig', [
            'equipo_tipo' => $equipoTipo,
            'form' => $form->createView(),
            'multiple' => true,
            'referer' => $request->headers->get('referer')
        ]);
    }

    /**
     * @Route("/{id}", name="equipo_tipo_show", methods={"GET"})
     */
    public function show(EquipoTipo $equipoTipo): Response
    {
        $deleteForm = $this->createDeleteForm($equipoTipo);

        return $this->render('equipo_tipo/show.html.twig', [
            'equipo_tipo' => $equipoTipo,
            'delete_form' => $deleteForm->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="equipo_tipo_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EquipoTipo $equipoTipo): Response
    {
        $form = $this->createForm(EquipoTipoType::class, $equipoTipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->updatedData($equipoTipo);
            $this->getDoctrine()->getManager()->flush();
            $this->successEditElement();
            return $this->nextAction(
                $request,
                $this->generateUrl('equipo_tipo_new'),
                $this->generateUrl('equipo_tipo_show', array('id' => $equipoTipo->getId()))
            );
        }

        return $this->render('equipo_tipo/new.html.twig', [
            'equipo_tipo' => $equipoTipo,
            'form' => $form->createView(),
            'multiple' => true,
            'referer' => $request->headers->get('referer')
        ]);
    }

    /**
     * @Route("/{id}", name="equipo_tipo_delete", methods={"POST"})
     */
    public function delete(Request $request, EquipoTipo $equipoTipo): Response
    {
        $form = $this->createDeleteForm($equipoTipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($equipoTipo);
            try{
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al eliminar. Existen elementos dependientes.');
                return $this->redirect($request->headers->get('referer'));
            }
            $this->addFlash('success', 'Elemento eliminado satisfactoriamente.');
        } else {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(EquipoTipo::class)->find($request->get('id'));
            $em->remove($entity);
            try{
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al eliminar. Existen elementos dependientes.');
                return $this->redirect($request->headers->get('referer'));
            }
            $this->addFlash('success', 'Elemento eliminado satisfactoriamente.');
        }

        return $this->redirectToRoute('equipo_tipo_index');
    }

    /**
     * Creates a form to delete an entity.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(EquipoTipo $equipoTipo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('equipo_tipo_delete', array('id' => $equipoTipo->getId())))
            ->setMethod('POST')
            ->getForm()
            ;
    }
}
