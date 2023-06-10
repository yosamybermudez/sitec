<?php

namespace App\Entity;

use App\Repository\EquipoTipoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @ORM\Table(name="sistema_equipo_tipo")
 * @ORM\Entity(repositoryClass=EquipoTipoRepository::class)
 */
class EquipoTipo
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\Column(name="created", type="datetime" , nullable=true)
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
     *  @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     * })
     */
    private $updatedBy;

    /**
     * @var Usuario
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;

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
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getUpdatedBy(): ?Usuario
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?Usuario $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

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
}
