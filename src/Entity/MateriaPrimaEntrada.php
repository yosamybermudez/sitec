<?php

namespace App\Entity;

use App\Repository\MateriaPrimaEntradaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @ORM\Table(name="invent_materia_prima_entrada")
 * @ORM\Entity(repositoryClass=MateriaPrimaEntradaRepository::class)
 */
class MateriaPrimaEntrada
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @var Usuario
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="realizada_por", referencedColumnName="id")
     * })
     */
    private $realizadaPor;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":false})
     */
    private $estaConfirmado;

    /**
     * @ORM\Column(type="integer")
     */
    private $nroMaterialesComprados;

    /**
     * @ORM\Column(type="float")
     */
    private $importeTotal;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\MateriaPrimaMovimiento", mappedBy="entradaMateriaPrima")
     */
    private $movimientosMateriaPrima;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vendedorNombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vendedorCarneIdentidad;

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
        $this->importeTotal = 0;
        $this->nroMaterialesComprados = 0;
        $this->movimientosMateriaPrima = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getImporteTotal(): ?float
    {
        return $this->importeTotal;
    }

    public function setImporteTotal(float $importeTotal): self
    {
        $this->importeTotal = $importeTotal;

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
            $movimientosMateriaPrima->setEntradaMateriaPrima($this);
        }

        return $this;
    }

    public function removeMovimientosMateriaPrima(MateriaPrimaMovimiento $movimientosMateriaPrima): self
    {
        if ($this->movimientosMateriaPrima->contains($movimientosMateriaPrima)) {
            $this->movimientosMateriaPrima->removeElement($movimientosMateriaPrima);
            // set the owning side to null (unless already changed)
            if ($movimientosMateriaPrima->getEntradaMateriaPrima() === $this) {
                $movimientosMateriaPrima->setEntradaMateriaPrima(null);
            }
        }

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

    public function getRealizadaPor(): ?Usuario
    {
        return $this->realizadaPor;
    }

    public function setRealizadaPor(?Usuario $realizadaPor): self
    {
        $this->realizadaPor = $realizadaPor;

        return $this;
    }

    public function getNroMaterialesComprados(): ?int
    {
        return $this->nroMaterialesComprados;
    }

    public function setNroMaterialesComprados(int $nroMaterialesComprados): self
    {
        $this->nroMaterialesComprados = $nroMaterialesComprados;

        return $this;
    }

    public function getVendedorNombre(): ?string
    {
        return $this->vendedorNombre;
    }

    public function setVendedorNombre(?string $vendedorNombre): self
    {
        $this->vendedorNombre = $vendedorNombre;

        return $this;
    }

    public function getVendedorCarneIdentidad(): ?string
    {
        return $this->vendedorCarneIdentidad;
    }

    public function setVendedorCarneIdentidad(?string $vendedorCarneIdentidad): self
    {
        $this->vendedorCarneIdentidad = $vendedorCarneIdentidad;

        return $this;
    }
}
