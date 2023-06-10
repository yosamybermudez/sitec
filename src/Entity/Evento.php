<?php

namespace App\Entity;

use App\Repository\EventoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @ORM\Table(name="sistema_evento")
 * @ORM\Entity(repositoryClass=EventoRepository::class)
 */
class Evento
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tipo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $idEntidadEvento;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tipoEntidadEvento;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $texto;

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
    public function __construct(AbstractController $controller)
    {
        $this->id = md5(uniqid("sisgemp", true));
        $this->createdBy = $controller->getDatabaseUser();
        $this->updatedBy = $controller->getDatabaseUser();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->tecnicos = new ArrayCollection();
        $this->registrosAsistencia = new ArrayCollection();
        $this->estaConfirmado = false;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getIdEntidadEvento(): ?string
    {
        return $this->idEntidadEvento;
    }

    public function setIdEntidadEvento(string $idEntidadEvento): self
    {
        $this->idEntidadEvento = $idEntidadEvento;

        return $this;
    }

    public function getTipoEntidadEvento(): ?string
    {
        return $this->tipoEntidadEvento;
    }

    public function setTipoEntidadEvento(string $tipoEntidadEvento): self
    {
        $this->tipoEntidadEvento = $tipoEntidadEvento;

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

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(string $texto): self
    {
        $this->texto = $texto;

        return $this;
    }

}
