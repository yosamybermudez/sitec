<?php

namespace App\Entity;

use App\Repository\RegistroAsistenciaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @ORM\Table(name="registro_asistencia")
 * @ORM\Entity(repositoryClass=RegistroAsistenciaRepository::class)
 */
class RegistroAsistencia
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @var Usuario
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario", inversedBy="registrosAsistencia")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="tecnico", referencedColumnName="id")
     * })
     */
    private $tecnico;

    /**
     * @var Usuario
     * @ORM\ManyToOne(targetEntity="App\Entity\Jornada", inversedBy="registrosAsistencia")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="jornada", referencedColumnName="id")
     * })
     */
    private $jornada;

    /**
     * @ORM\Column(type="datetime")
     */
    private $horaEntrada;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $horaSalida;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $montoRecaudado;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $gastoMateriales;

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
        $this->estaConfirmado = false;
        $this->montoRecaudado = 0;
        $this->gastoMateriales = 0;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMontoRecaudado(): ?float
    {
        return $this->montoRecaudado;
    }

    public function setMontoRecaudado(?float $montoRecaudado): self
    {
        $this->montoRecaudado = $montoRecaudado;

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

    public function getTecnico(): ?Usuario
    {
        return $this->tecnico;
    }

    public function setTecnico(?Usuario $tecnico): self
    {
        $this->tecnico = $tecnico;

        return $this;
    }

    public function getHoraEntrada(): ?\DateTimeInterface
    {
        return $this->horaEntrada;
    }

    public function setHoraEntrada(\DateTimeInterface $horaEntrada): self
    {
        $this->horaEntrada = $horaEntrada;

        return $this;
    }

    public function getHoraSalida(): ?\DateTimeInterface
    {
        return $this->horaSalida;
    }

    public function setHoraSalida(?\DateTimeInterface $horaSalida): self
    {
        $this->horaSalida = $horaSalida;

        return $this;
    }

    public function getJornada(): ?Jornada
    {
        return $this->jornada;
    }

    public function setJornada(?Jornada $jornada): self
    {
        $this->jornada = $jornada;

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
