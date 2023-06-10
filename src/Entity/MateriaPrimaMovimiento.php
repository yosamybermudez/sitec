<?php

namespace App\Entity;

use App\Repository\MateriaPrimaMovimientoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @ORM\Table(name="invent_materia_prima_movimiento")
 * @ORM\Entity(repositoryClass=MateriaPrimaMovimientoRepository::class)
 */
class MateriaPrimaMovimiento
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $nroMovimiento;

    /**
     * @var MateriaPrima
     * @ORM\ManyToOne(targetEntity="App\Entity\MateriaPrima", inversedBy="movimientosMateriaPrima")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="materiaprima_id", referencedColumnName="id")
     * })
     */
    private $materiaPrima;

    /**
     * @var MateriaPrimaEntrada
     * @ORM\ManyToOne(targetEntity="App\Entity\MateriaPrimaEntrada", inversedBy="movimientosMateriaPrima")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="materiaprimaentrada_id", referencedColumnName="id")
     * })
     */
    private $entradaMateriaPrima;

    /**
     * @var OrdenReparacion
     * @ORM\ManyToOne(targetEntity="App\Entity\OrdenReparacion", inversedBy="movimientosMateriaPrima")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="ordenreparacion_id", referencedColumnName="id")
     * })
     */
    private $ordenreparacion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tipo;


    /**
     * @ORM\Column(type="float")
     */
    private $cantidad;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cantidadRestante;

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
    public function __construct(AbstractController $controller = null)
    {
        $this->id = md5(uniqid("sisgemp", true));
        $this->createdBy = $controller ? $controller->getDatabaseUser() : null;
        $this->updatedBy = $controller ? $controller->getDatabaseUser() : null;
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->estaConfirmado = false;
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

    public function getCantidad(): ?float
    {
        return $this->cantidad;
    }

    public function setCantidad(float $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getMateriaPrima(): ?MateriaPrima
    {
        return $this->materiaPrima;
    }

    public function setMateriaPrima(?MateriaPrima $materiaPrima): self
    {
        $this->materiaPrima = $materiaPrima;

        return $this;
    }

    public function getOrdenreparacion(): ?OrdenReparacion
    {
        return $this->ordenreparacion;
    }

    public function setOrdenreparacion(?OrdenReparacion $ordenreparacion): self
    {
        $this->ordenreparacion = $ordenreparacion;

        return $this;
    }

    public function getNroMovimiento(): ?string
    {
        return $this->nroMovimiento;
    }

    public function setNroMovimiento(string $nroMovimiento): self
    {
        $this->nroMovimiento = $nroMovimiento;

        return $this;
    }

    public function getEntradaMateriaPrima(): ?MateriaPrimaEntrada
    {
        return $this->entradaMateriaPrima;
    }

    public function setEntradaMateriaPrima(?MateriaPrimaEntrada $entradaMateriaPrima): self
    {
        $this->entradaMateriaPrima = $entradaMateriaPrima;

        return $this;
    }

    public function getCantidadRestante(): ?float
    {
        return $this->cantidadRestante;
    }

    public function setCantidadRestante(?float $cantidadRestante): self
    {
        $this->cantidadRestante = $cantidadRestante;

        return $this;
    }

}
