<?php

namespace App\Entity;

use App\Repository\OperacionContableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @ORM\Table(name="sistema_operac_contable")
 * @ORM\Entity(repositoryClass=OperacionContableRepository::class)
 */
class OperacionContable
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private $nroOperacion;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $tipoOperacion;

    /**
     * @ORM\Column(type="float")
     */
    private $saldo;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":false})
     */
    private $estaConfirmado;

    /**
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @var Usuario
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;

    /**
     * @var Usuario
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     * })
     */
    private $updatedBy;

    /**
     * Constructor
     */
    public function __construct(AbstractController $controller, $nroOperacion, $tipoOperacion, $codigo, $saldo, $descripcion)
    {
        $this->id = md5(uniqid("sisgemp", true));
        $this->nroOperacion = $nroOperacion;
        $this->createdBy = $controller->getDatabaseUser();
        $this->updatedBy = $controller->getDatabaseUser();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->tipoOperacion = $tipoOperacion;
        $this->codigo = $codigo;
        $this->saldo = $saldo;
        $this->descripcion = $descripcion;
        $this->estaConfirmado = false;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTipoOperacion(): ?string
    {
        return $this->tipoOperacion;
    }

    public function setTipoOperacion(string $tipoOperacion): self
    {
        $this->tipoOperacion = $tipoOperacion;

        return $this;
    }

    public function getSaldo(): ?float
    {
        return $this->saldo;
    }

    public function setSaldo(float $saldo): self
    {
        $this->saldo = $saldo;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(?\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getCreatedBy(): ?Usuario
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Usuario $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?Usuario
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?Usuario $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getEstaConfirmado(): ?bool
    {
        return $this->estaConfirmado;
    }

    public function setEstaConfirmado(bool $estaConfirmado): self
    {
        $this->estaConfirmado = $estaConfirmado;

        return $this;
    }

    public function getNroOperacion(): ?string
    {
        return $this->nroOperacion;
    }

    public function setNroOperacion(string $nroOperacion): self
    {
        $this->nroOperacion = $nroOperacion;

        return $this;
    }
}
