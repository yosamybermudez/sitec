<?php

namespace App\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Permiso;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * Permiso controller.
 *
 * @Route("sistema/permiso")
 */
class PermisoController extends AbstractController
{
    /**
     * Lists all permiso entities.
     *
     * @Route("/", name="permiso_index", methods="GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $permisos = $em->getRepository('App:Permiso')->findBy(array(),array('identificador' => 'ASC'));

        return $this->render('permiso/index.html.twig', array(
            'permisos' => $permisos,
        ));
    }

    /**
     * Creates a new permiso entity.
     *
     * @Route("/new", name="permiso_new", methods="GET|POST")


     */
    public function newAction(Request $request)
    {
        $referer = $request->headers->get('referer');$permiso = new Permiso($this);
        $form = $this->createForm('App\Form\PermisoType', $permiso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->updatedData($permiso);
            $em->persist($permiso);
            $em->flush();
            $this->successNewElement();
            return $this->nextAction(
                $request,
                $this->generateUrl('permiso_new'),
                $this->generateUrl('permiso_show', array('id' => $permiso->getId()))
            );
        }

        return $this->render('permiso/new.html.twig', array(
            'permiso' => $permiso,
            'form' => $form->createView(),
            'multiple' => true,
            'referer' => $referer
        ));
    }

    /**
     * Finds and displays a permiso entity.
     *
     * @Route("/{id}", name="permiso_show", methods="GET")

     */
    public function showAction(Permiso $permiso)
    {
        $deleteForm = $this->createDeleteForm($permiso);

        return $this->render('permiso/show.html.twig', array(
            'permiso' => $permiso,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing permiso entity.
     *
     * @Route("/{id}/edit", name="permiso_edit", methods="GET|POST")

     */
    public function editAction(Request $request, Permiso $permiso)
    {
        $deleteForm = $this->createDeleteForm($permiso);
        $editForm = $this->createForm('App\Form\PermisoType', $permiso);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('permiso_index');
        }

        return $this->render('permiso/new.html.twig', array(
            'permiso' => $permiso,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a permiso entity.
     *
     * @Route("/{id}", name="permiso_delete", methods={"POST"})

     */
    public function deleteAction(Request $request, Permiso $permiso)
    {
        $form = $this->createDeleteForm($permiso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($permiso);
            try{
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al eliminar. Existen elementos dependientes.');
                return $this->redirect($request->headers->get('referer'));
            }
            $this->addFlash('success', 'Elemento eliminado satisfactoriamente.');
        } else {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(Permiso::class)->find($request->get('id'));
            $em->remove($entity);
            try{
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al eliminar. Existen elementos dependientes.');
                return $this->redirect($request->headers->get('referer'));
            }
            $this->addFlash('success', 'Elemento eliminado satisfactoriamente.');
        }

        return $this->redirectToRoute('permiso_index');
    }

    /**
     * Creates a form to delete a permiso entity.
     *
     * @param Permiso $permiso The permiso entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Permiso $permiso)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('permiso_delete', array('id' => $permiso->getId())))
            ->setMethod('POST')
            ->getForm()
        ;
    }
}
