<?php

namespace App\Entity;

use App\Repository\JornadaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @ORM\Table(name="operac_jornada")
 * @ORM\Entity(repositoryClass=JornadaRepository::class)
 */
class Jornada
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\Column(type="float")
     */
    private $fondoInicial;

    /**
     * @ORM\Column(type="float")
     */
    private $fondoActual;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $gastoMateriales;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $estado;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\RegistroAsistencia", mappedBy="jornada")
     */
    private $registrosAsistencia;

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
    public function __construct(AbstractController $controller)
    {
        $this->id = md5(uniqid("sisgemp", true));
        $this->createdBy = $controller->getDatabaseUser();
        $this->updatedBy = $controller->getDatabaseUser();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
//        $this->tecnicos = new ArrayCollection();
        $this->registrosAsistencia = new ArrayCollection();
        $this->estaConfirmado = false;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getFondoInicial(): ?float
    {
        return $this->fondoInicial;
    }

    public function setFondoInicial(float $fondoInicial): self
    {
        $this->fondoInicial = $fondoInicial;

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

    public function getFondoActual(): ?float
    {
        return $this->fondoActual;
    }

    public function setFondoActual(float $fondoActual): self
    {
        $this->fondoActual = $fondoActual;

        return $this;
    }

    /**
     * @return Collection|RegistroAsistencia[]
     */
    public function getRegistrosAsistencia(): Collection
    {
        return $this->registrosAsistencia;
    }

    public function addRegistrosAsistencium(RegistroAsistencia $registrosAsistencium): self
    {
        if (!$this->registrosAsistencia->contains($registrosAsistencium)) {
            $this->registrosAsistencia[] = $registrosAsistencium;
            $registrosAsistencium->setJornada($this);
        }

        return $this;
    }

    public function removeRegistrosAsistencium(RegistroAsistencia $registrosAsistencium): self
    {
        if ($this->registrosAsistencia->contains($registrosAsistencium)) {
            $this->registrosAsistencia->removeElement($registrosAsistencium);
            // set the owning side to null (unless already changed)
            if ($registrosAsistencium->getJornada() === $this) {
                $registrosAsistencium->setJornada(null);
            }
        }

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

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getGastoMateriales(): ?float
    {
        return $this->gastoMateriales;
    }

    public function setGastoMateriales(?float $gastoMateriales): self
    {
        $this->gastoMateriales = $gastoMateriales;

        return $this;
    }

}
