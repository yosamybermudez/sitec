<?php

namespace App\Controller;

use App\Entity\Cargo;
use App\Form\CargoType;
use App\Repository\CargoRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sistema/cargo")
 */
class CargoController extends AbstractController
{
    /**
     * @Route("/", name="cargo_index", methods={"GET"})
     * @IsGranted("ROLE_ADMINISTRADOR_NEGOCIO")
     */
    public function index(CargoRepository $cargoRepository): Response
    {
        return $this->render('cargo/index.html.twig', [
            'cargos' => $cargoRepository->findBy(array(), array('nombre' => 'ASC'))
        ]);
    }

    /**
     * @Route("/new", name="cargo_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMINISTRADOR_NEGOCIO")
     */
    public function new(Request $request): Response
    {
        $cargo = new Cargo($this);
        $form = $this->createForm(CargoType::class, $cargo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $this->updatedData($cargo);
            $entityManager->persist($cargo);
            $entityManager->flush();
            $this->successNewElement();
            return $this->nextAction(
                $request,
                $this->generateUrl('cargo_new'),
                $this->generateUrl('cargo_show', array('id' => $cargo->getId()))
            );
        }

        return $this->render('cargo/new.html.twig', [
            'cargo' => $cargo,
            'form' => $form->createView(),
            'multiple' => true,
            'referer' => $request->headers->get('referer')
        ]);
    }

    /**
     * @Route("/{id}", name="cargo_show", methods={"GET"})
     * @IsGranted("ROLE_ADMINISTRADOR_NEGOCIO")
     */
    public function show(Cargo $cargo): Response
    {
        $deleteForm = $this->createDeleteForm($cargo);

        return $this->render('cargo/show.html.twig', [
            'cargo' => $cargo,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="cargo_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMINISTRADOR_NEGOCIO")
     */
    public function edit(Request $request, Cargo $cargo): Response
    {
        $form = $this->createForm(CargoType::class, $cargo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->updatedData($cargo);
            $this->getDoctrine()->getManager()->flush();
            $this->successEditElement();
            return $this->nextAction(
                $request,
                $this->generateUrl('cargo_new'),
                $this->generateUrl('cargo_show', array('id' => $cargo->getId()))
            );
        }

        return $this->render('cargo/new.html.twig', [
            'cargo' => $cargo,
            'form' => $form->createView(),
            'multiple' => true,
            'referer' => $request->headers->get('referer')
        ]);
    }

    /**
     * Deletes a cargo entity.
     *
     * @Route("/{id}", name="cargo_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMINISTRADOR_NEGOCIO")
     */

    public function deleteAction(Request $request, Cargo $cargo)
    {
        $form = $this->createDeleteForm($cargo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cargo);
            try{
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al eliminar. Existen elementos dependientes.');
                return $this->redirect($request->headers->get('referer'));
            }
            $this->addFlash('success', 'Elemento eliminado satisfactoriamente.');
        } else {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(Cargo::class)->find($request->get('id'));
            $em->remove($entity);
            try{
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al eliminar. Existen elementos dependientes.');
                return $this->redirect($request->headers->get('referer'));
            }
            $this->addFlash('success', 'Elemento eliminado satisfactoriamente.');
        }

        return $this->redirectToRoute('cargo_index');
    }

    /**
     * Creates a form to delete an entity.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Cargo $cargo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cargo_delete', array('id' => $cargo->getId())))
            ->setMethod('POST')
            ->getForm()
            ;
    }
}
