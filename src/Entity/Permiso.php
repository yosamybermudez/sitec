<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Permiso
 *
 * @ORM\Table(name="sistema_permiso")
 * @ORM\Entity(repositoryClass="App\Repository\PermisoRepository")
 */
class Permiso
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(name="identificador", type="string", length=255, unique=true)
     */
    private $identificador;

    /**
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;


    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Rol", mappedBy="permisos")
     * @ORM\JoinTable(name="relacion_rol_permiso")
     */
    private $rols;

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
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->rols = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getIdentificador(): ?string
    {
        return $this->identificador;
    }

    public function setIdentificador(string $identificador): self
    {
        $this->identificador = $identificador;

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

    /**
     * @return Collection|Rol[]
     */
    public function getRols(): Collection
    {
        return $this->rols;
    }

    public function addRol(Rol $rol): self
    {
        if (!$this->rols->contains($rol)) {
            $this->rols[] = $rol;
            $rol->addPermiso($this);
        }

        return $this;
    }

    public function removeRol(Rol $rol): self
    {
        if ($this->rols->contains($rol)) {
            $this->rols->removeElement($rol);
            $rol->removePermiso($this);
        }

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
