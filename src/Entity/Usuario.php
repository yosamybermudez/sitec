<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Usuario
 *
 * @ORM\Table(name="sistema_usuario")
 * @ORM\Entity(repositoryClass="App\Repository\UsuarioRepository")
 */
class Usuario implements UserInterface, \Serializable
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(name="username", type="string", length=64, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true, nullable=true)
     */
    private $email;

    /********
    * DATOS ESPECIFICOS DEL TRABAJADOR
     */

    /**
     * @ORM\Column(name="nombres", type="string", length=255, nullable=true)
     */
    private $nombres;

    /**
     * @ORM\Column(name="apellidos", type="string", length=255, nullable=true)
     */
    private $apellidos;

    /**
     * @ORM\Column(name="carne_identidad", type="string", length=11, nullable=true)
     */
    private $carneIdentidad;

    /**
     * @ORM\Column(name="direccion", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @ORM\Column(name="municipio", type="string", length=50, nullable=true)
     */
    private $municipio;

    /**
     * @ORM\Column(name="provincia", type="string", length=50, nullable=true)
     */
    private $provincia;

    /**
     * @ORM\Column(name="sexo", type="string", length=20, nullable=true)
     */
    private $sexo;

    /**
     * @ORM\Column(name="fecha_alta", type="datetime", nullable=true)
     */
    private $fechaAlta;

    /**
     * @ORM\Column(name="foto", type="string", length=255, nullable=true)
     */
    private $foto;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\OrdenTrabajo", mappedBy="tecnicoRepara")
     */
    private $ordenesTrabajo;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\RegistroAsistencia", mappedBy="tecnico")
     */
    private $registrosAsistencia;

    /**
     * @var Cargo
     * @ORM\ManyToOne(targetEntity="App\Entity\Cargo", inversedBy="plazasOcupadas")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="cargo", referencedColumnName="id")
     * })
     */

    private $cargo;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Rol", inversedBy="usuarios")
     * @ORM\JoinTable(name="relacion_rol_usuario")
     */
    private $userRoles;


    /**
     * @ORM\Column(name="activo", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(name="created", type="datetime" , nullable=true)
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;


    public function __construct(AbstractController $controller = null)
    {
        $this->id = md5(uniqid("sisgemp", true));
        $this->isActive = true;
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->userRoles = new ArrayCollection();
        $this->jornadas = new ArrayCollection();
        $this->registrosAsistencia = new ArrayCollection();
        $this->ordenesTrabajo = new ArrayCollection();
    }

    public function setRoles(ArrayCollection $roles)
    {
        return $this->userRoles = $roles;
    }



    public function getRolesObjects()
    {
        return $this->userRoles;
    }

    public function addRol(string $rol)
    {
        $this->userRoles->add($rol);
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Usuario
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Usuario
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Usuario
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Usuario
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Usuario
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Usuario
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Collection|Rol[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(Rol $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
        }

        return $this;
    }

    public function removeUserRole(Rol $userRole): self
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
        }

        return $this;
    }

    public function _toArray()
    {
        $array = array();
        $array['username'] = $this->getUsername();
        return $array;
    }

    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Usuario
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getNombres(): ?string
    {
        return $this->nombres;
    }

    public function setNombres(?string $nombres): self
    {
        $this->nombres = $nombres;

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(?string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getCarneIdentidad(): ?string
    {
        return $this->carneIdentidad;
    }

    public function setCarneIdentidad(?string $carneIdentidad): self
    {
        $this->carneIdentidad = $carneIdentidad;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getMunicipio(): ?string
    {
        return $this->municipio;
    }

    public function setMunicipio(?string $municipio): self
    {
        $this->municipio = $municipio;

        return $this;
    }

    public function getProvincia(): ?string
    {
        return $this->provincia;
    }

    public function setProvincia(?string $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    public function getSexo(): ?string
    {
        return $this->sexo;
    }

    public function setSexo(?string $sexo): self
    {
        $this->sexo = $sexo;

        return $this;
    }

    public function getFechaAlta(): ?\DateTimeInterface
    {
        return $this->fechaAlta;
    }

    public function setFechaAlta(?\DateTimeInterface $fechaAlta): self
    {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }

    public function getRoles()
    {
        return ['ROLE_USUARIO_ESTANDAR'];
    }

    public function getNombreCompleto(){
        return $this->nombres . " " . $this->apellidos;
    }

    public function getNombreCompletoCargo(){
        return $this->nombres . " " . $this->apellidos . " (" . $this->cargo->getNombre() . ")";
    }

    public function getNombreCargo(){
        return $this->nombres . " (" . $this->cargo->getNombre() . ")";
    }

    public function getCargo(): ?Cargo
    {
        return $this->cargo;
    }

    public function setCargo(?Cargo $cargo): self
    {
        $this->cargo = $cargo;

        return $this;
    }

    /**
     * @return Collection|Jornada[]
     */
    public function getJornadas(): Collection
    {
        return $this->jornadas;
    }

    public function addRegistrosAsistencium(RegistroAsistencia $registrosAsistencium): self
    {
        if (!$this->registrosAsistencia->contains($registrosAsistencium)) {
            $this->registrosAsistencia[] = $registrosAsistencium;
            $registrosAsistencium->setTecnico($this);
        }

        return $this;
    }

    public function removeRegistrosAsistencium(RegistroAsistencia $registrosAsistencium): self
    {
        if ($this->registrosAsistencia->contains($registrosAsistencium)) {
            $this->registrosAsistencia->removeElement($registrosAsistencium);
            // set the owning side to null (unless already changed)
            if ($registrosAsistencium->getTecnico() === $this) {
                $registrosAsistencium->setTecnico(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OrdenTrabajo[]
     */
    public function getOrdenesTrabajo(): Collection
    {
        return $this->ordenesTrabajo;
    }

    public function addOrdenesTrabajo(OrdenTrabajo $ordenesTrabajo): self
    {
        if (!$this->ordenesTrabajo->contains($ordenesTrabajo)) {
            $this->ordenesTrabajo[] = $ordenesTrabajo;
            $ordenesTrabajo->setTecnicoRepara($this);
        }

        return $this;
    }

    public function removeOrdenesTrabajo(OrdenTrabajo $ordenesTrabajo): self
    {
        if ($this->ordenesTrabajo->contains($ordenesTrabajo)) {
            $this->ordenesTrabajo->removeElement($ordenesTrabajo);
            // set the owning side to null (unless already changed)
            if ($ordenesTrabajo->getTecnicoRepara() === $this) {
                $ordenesTrabajo->setTecnicoRepara(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RegistroAsistencia[]
     */
    public function getRegistrosAsistencia(): Collection
    {
        return $this->registrosAsistencia;
    }

    public function getOrdenesTrabajosPendientes(){
        $ordenes = $this->getOrdenesTrabajo();
        $salida = array();
        foreach ($ordenes as $orden){
            if(in_array($orden->getEstado(), array('ECT', 'TR', 'DT', 'EP'))){
                $salida[] = $orden;
            }
        }
        return $salida;
    }


}
