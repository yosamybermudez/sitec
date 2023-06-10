<?php


namespace App\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class DatabaseGlobalsExtension extends AbstractExtension implements GlobalsInterface
{

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getGlobals(): array
    {
        return [

        ];
    }
}