<?php
/**
 * Created by PhpStorm.
 * User: SONY
 * Date: 2/08/17
 * Time: 15:30
 */

namespace App\Listener;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Debug\Exception\ClassNotFoundException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class ExceptionListener
{
    /**
     * Holds Symfony2 router
     *
     * @var Router
     */
    protected $router;

    /**
     * @param Router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }


    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $url = $this->router->generate('acceso_denegado');
        $response = new RedirectResponse($url);
        $response->headers->clearCookie('code');
        $response->headers->clearCookie('mensaje');
        $response->headers->clearCookie('detalle');
        if ($exception instanceof NotFoundHttpException) {
            $response->headers->setCookie(new Cookie('mensaje', 'Enlace no existente'));
            $response->headers->setCookie(new Cookie('code', $exception->getMessage()));
            $response->headers->setCookie(new Cookie('detalle', 'El enlace al que desea acceder no existe. Evite la modificación de la URL.'));
        } else if ($exception instanceof RouteNotFoundException) {
            $response->headers->setCookie(new Cookie('mensaje', 'Ruta no encontrada'));
            $response->headers->setCookie(new Cookie('code', $exception->getMessage()));
            $response->headers->setCookie(new Cookie('detalle', 'La ruta a la que desea acceder no existe.'));
        } else if ($exception instanceof ClassNotFoundException) {
            $detalle = "";
            $response->headers->setCookie(new Cookie('mensaje', $exception->getMessage()));
            $response->headers->setCookie(new Cookie('code', $exception));
            $response->headers->setCookie(new Cookie('detalle', $detalle));
        } else {
            $detalle = "";
            $response->headers->setCookie(new Cookie('mensaje', "Algo ha salido mal :("));
            $response->headers->setCookie(new Cookie('code', $exception->getMessage()));
            $response->headers->setCookie(new Cookie('detalle', $detalle));
        }

        $event->setResponse($response);
    }

    private function reformatForeignKeyException(string $cadena)
    {

    }
}