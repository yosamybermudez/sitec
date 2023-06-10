<?php

namespace App\Entity;

use App\Repository\OrdenReparacionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @ORM\Table(name="operac_orden_reparacion")
 * @ORM\Entity(repositoryClass=OrdenReparacionRepository::class)
 */
class OrdenReparacion
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @var OrdenTrabajo
     * @ORM\OneToOne(targetEntity="App\Entity\OrdenTrabajo", inversedBy="ordenReparacion")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="orden_trabajo_id", referencedColumnName="id")
     * })
     */
    private $ordenTrabajo;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ingreso;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":false})
     */
    private $estaConfirmado;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $estadoFinal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $materialesUsados;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $gastoMateriales;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $otrosMateriales;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $otrosGastos;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observaciones;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $diasGarantia;

    /**
     * @var Usuario
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="revisado_por", referencedColumnName="id")
     * })
     */
    private $revisadoPor;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\MateriaPrimaMovimiento", mappedBy="ordenreparacion")
     */
    private $movimientosMateriaPrima;

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

    public function __construct(AbstractController $controller)
    {
        $this->id = md5(uniqid("sisgemp", true));
        $this->createdBy = $controller->getDatabaseUser();
        $this->updatedBy = $controller->getDatabaseUser();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->estaConfirmado = false;
        $this->gastoMateriales = 0;
//        $this->otrosGastos = 0;
        $this->otrosMateriales = '';
        $this->diasGarantia = 0;
        $this->movimientosMateriaPrima = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getIngreso(): ?float
    {
        return $this->ingreso;
    }

    public function setIngreso(?float $ingreso): self
    {
        $this->ingreso = $ingreso;

        return $this;
    }

    public function getEstadoFinal(): ?string
    {
        return $this->estadoFinal;
    }

    public function setEstadoFinal(string $estadoFinal): self
    {
        $this->estadoFinal = $estadoFinal;

        return $this;
    }

    public function getMaterialesUsados(): ?string
    {
        return $this->materialesUsados;
    }

    public function setMaterialesUsados(?string $materialesUsados): self
    {
        $this->materialesUsados = $materialesUsados;

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

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): self
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    public function getDiasGarantia(): ?int
    {
        return $this->diasGarantia;
    }

    public function setDiasGarantia(?int $diasGarantia): self
    {
        $this->diasGarantia = $diasGarantia;

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

    public function getOrdenTrabajo(): ?OrdenTrabajo
    {
        return $this->ordenTrabajo;
    }

    public function setOrdenTrabajo(?OrdenTrabajo $ordenTrabajo): self
    {
        $this->ordenTrabajo = $ordenTrabajo;

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

    /**
     * @return Collection|MateriaPrimaMovimiento[]
     */
    public function getMovimientosMateriaPrima(): Collection
    {
        return $this->movimientosMateriaPrima;
    }

    public function addMovimientosMateriaPrima(MateriaPrimaMovimiento $movimientosMateriaPrima): self
    {
        if (!$this->movimientosMateriaPrima->contains($movimientosMateriaPrima)) {
            $this->movimientosMateriaPrima[] = $movimientosMateriaPrima;
            $movimientosMateriaPrima->setOrdenreparacion($this);
        }

        return $this;
    }

    public function removeMovimientosMateriaPrima(MateriaPrimaMovimiento $movimientosMateriaPrima): self
    {
        if ($this->movimientosMateriaPrima->contains($movimientosMateriaPrima)) {
            $this->movimientosMateriaPrima->removeElement($movimientosMateriaPrima);
            // set the owning side to null (unless already changed)
            if ($movimientosMateriaPrima->getOrdenreparacion() === $this) {
                $movimientosMateriaPrima->setOrdenreparacion(null);
            }
        }

        return $this;
    }

    public function getRevisadoPor(): ?Usuario
    {
        return $this->revisadoPor;
    }

    public function setRevisadoPor(?Usuario $revisadoPor): self
    {
        $this->revisadoPor = $revisadoPor;

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

    public function getOtrosMateriales(): ?string
    {
        return $this->otrosMateriales;
    }

    public function setOtrosMateriales(?string $otrosMateriales): self
    {
        $this->otrosMateriales = $otrosMateriales;

        return $this;
    }

    public function getOtrosGastos(): ?float
    {
        return $this->otrosGastos;
    }

    public function setOtrosGastos(?float $otrosGastos): self
    {
        $this->otrosGastos = $otrosGastos;

        return $this;
    }

}
