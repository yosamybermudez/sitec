<?php

namespace App\Entity;

use App\Repository\MateriaPrimaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @ORM\Table(name="invent_materia_prima")
 * @ORM\Entity(repositoryClass=MateriaPrimaRepository::class)
 */
class MateriaPrima
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @var EquipoTipo
     * @ORM\ManyToOne(targetEntity="App\Entity\EquipoTipo")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="tipo_equipo_destino", referencedColumnName="id", nullable=false)
     * })
     */
    private $tipoEquipoDestino;

    /**
     * @ORM\Column(type="float")
     */
    private $cantidad;

    /**
     * @ORM\Column(type="float")
     */
    private $precio;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $unidadMedida;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":false})
     */
    private $estaConfirmado;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\MateriaPrimaMovimiento", mappedBy="materiaPrima")
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
        $this->movimientosMateriaPrima = new ArrayCollection();
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

    public function getNombreCantidad(): ?string
    {
        return $this->nombre . " (" . $this->cantidad . ")";
    }

    public function getNombreCantidadPrecio(): ?string
    {
        return $this->nombre . " - " . $this->cantidad . " " . $this->unidadMedida . " - $ " . number_format($this->getPrecio(),2, '.', '');
    }

    public function getNombrePrecio(): ?string
    {
        return $this->nombre . " - $ " . number_format($this->getPrecio(),2, '.', '');
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

    public function getCantidad(): ?float
    {
        return $this->cantidad;
    }

    public function setCantidad(float $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function setPrecio(float $precio): self
    {
        $this->precio = $precio;

        return $this;
    }

    public function getUnidadMedida(): ?string
    {
        return $this->unidadMedida;
    }

    public function setUnidadMedida(?string $unidadMedida): self
    {
        $this->unidadMedida = $unidadMedida;

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

    public function getTipoEquipoDestino(): ?EquipoTipo
    {
        return $this->tipoEquipoDestino;
    }

    public function setTipoEquipoDestino(?EquipoTipo $tipoEquipoDestino): self
    {
        $this->tipoEquipoDestino = $tipoEquipoDestino;

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
            $movimientosMateriaPrima->setMateriaPrima($this);
        }

        return $this;
    }

    public function removeMovimientosMateriaPrima(MateriaPrimaMovimiento $movimientosMateriaPrima): self
    {
        if ($this->movimientosMateriaPrima->contains($movimientosMateriaPrima)) {
            $this->movimientosMateriaPrima->removeElement($movimientosMateriaPrima);
            // set the owning side to null (unless already changed)
            if ($movimientosMateriaPrima->getMateriaPrima() === $this) {
                $movimientosMateriaPrima->setMateriaPrima(null);
            }
        }

        return $this;
    }
}
