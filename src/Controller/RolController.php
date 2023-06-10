<?php

namespace App\Controller;

use App\Entity\Rol;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Rol controller.
 *
 * @Route("sistema/rol")
 */
class RolController extends AbstractController
{
    /**
     * Lists all rol entities.
     *
     * @Route("/", name="rol_index", methods="GET")
     * @IsGranted("ROLE_ADMINISTRADOR_NEGOCIO")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $roles = $em->getRepository('App:Rol')->findBy(array(), array('nombre' => 'ASC'));

        return $this->render('rol/index.html.twig', array(
            'roles' => $roles,
        ));
    }

    /**
     * Creates a new rol entity.
     *
     * @Route("/new", name="rol_new", methods="GET|POST")
     * @IsGranted("ROLE_ADMINISTRADOR_SISTEMA")
     */
    public function newAction(Request $request)
    {
        $rol = new Rol($this);
        $form = $this->createForm('App\Form\RolType', $rol);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->updatedData($rol);
            $em->persist($rol);
            $em->flush();
            $this->successNewElement();
            return $this->nextAction(
                $request,
                $this->generateUrl('rol_new'),
                $this->generateUrl('rol_show', array('id' => $rol->getId()))
            );
        }

        return $this->render('rol/new.html.twig', array(
            'rol' => $rol,
            'form' => $form->createView(),
            'multiple' => true,
            'referer' => $request->headers->get('referer')
        ));
    }

    /**
     * Finds and displays a rol entity.
     *
     * @Route("/{id}", name="rol_show", methods="GET")
     * @IsGranted("ROLE_ADMINISTRADOR_NEGOCIO")
     */
    public function showAction(Rol $rol)
    {
        $deleteForm = $this->createDeleteForm($rol);

        return $this->render('rol/show.html.twig', array(
            'rol' => $rol,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing rol entity.
     *
     * @Route("/{id}/edit", name="rol_edit", methods="GET|POST")
     * @IsGranted("ROLE_ADMINISTRADOR_SISTEMA")
     */
    public function editAction(Request $request, Rol $rol)
    {
        $form = $this->createForm('App\Form\RolType', $rol);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->updatedData($rol);
            $this->getDoctrine()->getManager()->flush();
            $this->successEditElement();
            return $this->nextAction(
                $request,
                $this->generateUrl('rol_new'),
                $this->generateUrl('rol_show', array('id' => $rol->getId()))
            );
        }

        return $this->render('rol/new.html.twig', array(
            'rol' => $rol,
            'form' => $form->createView(),
            'multiple' => true,
            'referer' => $request->headers->get('referer')
        ));
    }

    /**
     * Deletes a rol entity.
     *
     * @Route("/{id}", name="rol_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMINISTRADOR_SISTEMA")
     */
    public function deleteAction(Request $request, Rol $rol)
    {
        $form = $this->createDeleteForm($rol);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($rol);
            try{
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al eliminar. Existen elementos dependientes.');
                return $this->redirect($request->headers->get('referer'));
            }
            $this->addFlash('success', 'Elemento eliminado satisfactoriamente.');
        } else {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(Rol::class)->find($request->get('id'));
            $em->remove($entity);
            try{
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al eliminar. Existen elementos dependientes.');
                return $this->redirect($request->headers->get('referer'));
            }
            $this->addFlash('success', 'Elemento eliminado satisfactoriamente.');
        }

        return $this->redirectToRoute('rol_index');
    }

    /**
     * Creates a form to delete an entity.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Rol $rol)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('rol_delete', array('id' => $rol->getId())))
            ->setMethod('POST')
            ->getForm()
            ;
    }

}
