<?php

namespace App\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Debug\Exception\ClassNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Usuario controller.
 *
 * @Route("sistema/usuario")
 */
class UsuarioController extends AbstractController
{
    /**
     * Lists all usuario entities.
     *
     * @Route("/estado/{estado}", name="usuario_index", methods="GET")
     * @IsGranted("ROLE_ADMINISTRADOR_NEGOCIO")
     */
    public function indexAction($estado)
    {
        $em = $this->getDoctrine()->getManager();

        if($estado == 'activo'){
            $usuarios = $em->getRepository('App:Usuario')->findBy(array('isActive' => true), array('username' => 'ASC'));
        }elseif ($estado == 'inactivo'){
            $usuarios = $em->getRepository('App:Usuario')->findBy(array('isActive' => false), array('username' => 'ASC'));
        }
        else{
            $this->addFlash('danger', 'No existe esa ruta');
            return $this->redirectToRoute('usuario_index', array('estado' => 'activo'));
        }
        return $this->render('usuario/index.html.twig', array(
            'usuarios' => $usuarios,
            'estado' => $estado
        ));
    }

    /**
     * Creates a new usuario entity.
     * @Route("/new", name="usuario_new", methods="GET|POST")
     * @IsGranted("ROLE_ADMINISTRADOR_NEGOCIO")
     */
    public function newAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $usuario = new Usuario($this);
        $form = $this->createForm('App\Form\UsuarioType', $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $usuario_existe = $em->getRepository("App:Usuario")->findBy(array('username' => $usuario->getUsername()));
            if(count($usuario_existe) == 0){
                $pass = 'sitec';
                $password = $passwordEncoder->encodePassword($usuario, $pass);
                $file = null;
                if($usuario->getFoto()){
                    $file = $usuario->getFoto();
                    $nombre =
                        $usuario->getId();
                    $fileName = $nombre . '.' .$file->guessExtension();
                    $filesDir = $this->getParameter('fotos_trabajadores');
                    $file->move($filesDir, $fileName);
                    $usuario->setFoto($fileName);
                }
                if($usuario->getNombres() === null){
                    $usuario->setNombres($usuario->getUsername());
                }
                $usuario->setPassword($password);
                $this->updatedData($usuario);
                $em->persist($usuario);
                $em->flush();
                $this->successNewElement();
                return $this->nextAction(
                    $request,
                    $this->generateUrl('usuario_new'),
                    $this->generateUrl('usuario_show', array('id' => $usuario->getId()))
                );
            }
            else {
                $this->addFlash('danger', 'Ya existe un usuario con ese identificador');
            }
        }
        return $this->render('usuario/new.html.twig', array(
            'usuario' => $usuario,
            'form' => $form->createView(),
            'multiple' => true,
            'referer' => $request->headers->get('referer')
        ));
    }

    /**
     * Finds and displays a usuario entity.
     * @Route("/{id}", name="usuario_show", methods="GET")
     */
    public function showAction(Usuario $usuario)
    {
        $deleteForm = $this->createDeleteForm($usuario);
        return $this->render('usuario/show.html.twig', array(
            'usuario' => $usuario,
            'delete_form' => $deleteForm->createView()
        ));
    }

    /**
     * Displays a form to edit an existing usuario entity.
     * @IsGranted("ROLE_ADMINISTRADOR_NEGOCIO")
     * @Route("/{id}/edit", name="usuario_edit", methods="GET|POST")

     */
    public function editAction(Request $request, Usuario $usuario, UserPasswordEncoderInterface $passwordEncoder)
    {
        $editForm = $this->createForm('App\Form\UsuarioType', $usuario);
        $editForm->remove('password');
        $editForm->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => false,
            'invalid_message' => 'Las contraseñas no coinciden.',
            'options' => ['attr' => ['class' => 'form-control password-field']],
            'first_options'  => ['label' => 'Contraseña'],
            'second_options' => ['label' => 'Repetir contraseña'],
        ]);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $usuario_old = $em->getUnitOfWork()->getOriginalEntityData($usuario);
            $usuario_unico = $em->getRepository("App:Usuario")->findOneBy(array('username' => $usuario->getUsername()));

            if($usuario_unico != null && $usuario_unico->getUsername() != $usuario_old["username"]){
                $this->addFlash('danger', 'El usuario ' . $usuario_unico->getUsername() . ' ya existe');
                return $this->render('usuario/new.html.twig', array(
                    'usuario' => $usuario,
                    'form' => $editForm->createView()
                ));
            }

            if($usuario->getPassword() != null){
                $password = $passwordEncoder->encodePassword($usuario,$usuario->getPassword());
                $usuario->setPassword($password);
            }else{
                $usuario->setPassword($usuario_old["password"]);
            }
            $file = null;
            if($usuario->getFoto()){
                $file = $usuario->getFoto();
                $nombre =
                    $usuario->getId();
                $fileName = $nombre . '.' .$file->guessExtension();
                $filesDir = $this->getParameter('fotos_trabajadores');
                $file->move($filesDir, $fileName);
                $usuario->setFoto($fileName);
            }
            //
            $this->updatedData($usuario);
            $em->persist($usuario);
            $em->flush();
            $this->successEditElement();
            return $this->nextAction(
                $request,
                $this->generateUrl('usuario_new'),
                $this->generateUrl('usuario_show', array('id' => $usuario->getId()))
            );
        }

        return $this->render('usuario/new.html.twig', array(
            'usuario' => $usuario,
            'form' => $editForm->createView(),
            'multiple' => true,
            'referer' => $request->headers->get('referer')
        ));
    }

    /**
     * Deletes a usuario entity.
     *
     * @Route("/{id}", name="usuario_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMINISTRADOR_NEGOCIO")
     */
    public function deleteAction(Request $request, Usuario $usuario)
    {
        $form = $this->createDeleteForm($usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($usuario);
            try{
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al eliminar. Existen elementos dependientes.');
                return $this->redirect($request->headers->get('referer'));
            }
            $this->addFlash('success', 'Elemento eliminado satisfactoriamente.');
        } else {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(Usuario::class)->find($request->get('id'));
            $em->remove($entity);
            try{
                $em->flush();
            } catch (ForeignKeyConstraintViolationException $e) {
                $this->addFlash('danger', 'Error al eliminar. Existen elementos dependientes.');
                return $this->redirect($request->headers->get('referer'));
            }
            $this->addFlash('success', 'Elemento eliminado satisfactoriamente.');
        }

        return $this->redirectToRoute('usuario_index', array('estado' => 'activo'));
    }

    /**
     * Creates a form to delete an entity.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Usuario $usuario)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('usuario_delete', array('id' => $usuario->getId())))
            ->setMethod('POST')
            ->getForm()
            ;
    }

    public function cantidadAction($estado){
        $em = $this->getDoctrine()->getManager();

        if($estado == 'activo'){
            $usuarios = $em->getRepository('App:Usuario')->findBy(array('isActive' => true));
        }elseif ($estado == 'inactivo'){
            $usuarios = $em->getRepository('App:Usuario')->findBy(array('isActive' => false));
        }else{
            throw new NotFoundHttpException();
        }

        return $this->render('assets_views/showValue.html.twig', array(
            'var' => count($usuarios)
        ));
    }

    /**
     * Activate a usuario entity.
     *
     * @Route("/activate/{id}", name="usuario_activate")
     */
    public function activateAction(Usuario $usuario)
    {
        $em = $this->getDoctrine()->getManager();
        $usuario->setIsActive(true);
        $em->flush();
        $this->addFlash('success', 'Usuario reactivado satisfactoriamente.');
        return $this->redirectToRoute('usuario_index', array('estado' => 'activo'));
    }

    /**
     * Displays a form to edit an existing usuario entity.
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/{id}/cambiar_password_inicial", name="usuario_change_password", methods="GET|POST")

     */
    public function changePassword(Request $request, Usuario $usuario, UserPasswordEncoderInterface $passwordEncoder)
    {
        $session = $this->get('session');
        $session->set('modulo', 'all_denied');
        $form = $this->createFormBuilder()
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => 'Las contraseñas no coinciden.',
                'options' => ['attr' => ['class' => 'form-control password-field']],
                'first_options'  => ['label' => 'Contraseña'],
                'second_options' => ['label' => 'Repetir contraseña'],
            ])->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $password = $passwordEncoder->encodePassword($usuario, $form->get("password")->getData());
            $usuario->setPassword($password);
            $this->updatedData($usuario);
            $em->persist($usuario);
            $em->flush();
            $this->addFlash('success', 'Contraseña actualizada satisfactoriamente');
            return $this->redirectToRoute('app_module_index');
        }

        return $this->render('usuario/changePassword.html.twig', array(
            'usuario' => $usuario,
            'form' => $form->createView(),
        ));
    }
}
