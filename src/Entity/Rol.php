<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Rol
 *
 * @ORM\Table(name="sistema_rol")
 * @ORM\Entity(repositoryClass="App\Repository\RolRepository")
 */
class Rol
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(name="nombre", type="string", length=255, unique=true)
     */
    private $nombre;

    /**
     * @ORM\Column(name="identificador", type="string", length=255, unique=true)
     */
    private $identificador;

    /**
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\Column(name="rango", type="integer", nullable=true)
     */
    private $rango;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Usuario", mappedBy="userRoles")
     * @ORM\JoinTable(name="relacion_rol_usuario")
     */
    private $usuarios;


    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Permiso", inversedBy="rols")
     * @ORM\JoinTable(name="relacion_rol_permiso")
     */
    private $permisos;

    /**
     * @ORM\Column(name="created", type="datetime" , nullable=true)
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * Constructor
     */
    public function __construct(AbstractController $controller = null)
    {
        $this->id = md5(uniqid("sisgemp", true));
        $this->permisos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->usuarios = new ArrayCollection();
    }


    /**
     * Get role
     */
    public function getRole() : string
    {
        return 'ROLE_' . strtoupper($this->identificador);
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getRango(): ?int
    {
        return $this->rango;
    }

    public function setRango(?int $rango): self
    {
        $this->rango = $rango;

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
     * @return Collection|Usuario[]
     */
    public function getUsuarios(): Collection
    {
        return $this->usuarios;
    }

    public function addUsuario(Usuario $usuario): self
    {
        if (!$this->usuarios->contains($usuario)) {
            $this->usuarios[] = $usuario;
            $usuario->addUserRole($this);
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        if ($this->usuarios->contains($usuario)) {
            $this->usuarios->removeElement($usuario);
            $usuario->removeUserRole($this);
        }

        return $this;
    }

    /**
     * @return Collection|Permiso[]
     */
    public function getPermisos(): Collection
    {
        return $this->permisos;
    }

    public function addPermiso(Permiso $permiso): self
    {
        if (!$this->permisos->contains($permiso)) {
            $this->permisos[] = $permiso;
        }

        return $this;
    }

    public function removePermiso(Permiso $permiso): self
    {
        if ($this->permisos->contains($permiso)) {
            $this->permisos->removeElement($permiso);
        }

        return $this;
    }
}
