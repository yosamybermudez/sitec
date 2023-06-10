<?php

namespace App\Entity;

use App\Repository\ComprobanteOperacionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @ORM\Table(name="sistema_comprobante_operac")
 * @ORM\Entity(repositoryClass=ComprobanteOperacionRepository::class)
 */
class ComprobanteOperacion
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private $nroComprobante;

    /**
     * @var Collection
     * @ORM\OneToOne(targetEntity="App\Entity\OrdenTrabajo", inversedBy="comprobante")
     */
    private $ordenTrabajo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tipoOperacion;

    /**
     * @ORM\Column(type="float")
     */
    private $importeTotal;

    /**
 * @ORM\Column(type="float")
 */
    private $importePagado;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $gastosAsociados;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":false})
     */
    private $estaConfirmado;

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
    public function __construct(AbstractController $controller, $nroComprobante, $ordenTrabajo, $tipoOperacion, $importeTotal, $importePagado)
    {
        $this->id = md5(uniqid("sisgemp", true));
        $this->createdBy = $controller->getDatabaseUser();
        $this->updatedBy = $controller->getDatabaseUser();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->nroComprobante = $nroComprobante;
        $this->ordenTrabajo = $ordenTrabajo;
        $this->tipoOperacion = $tipoOperacion;
        $this->importeTotal = $importeTotal;
        $this->importePagado = $importePagado;
        $this->estaConfirmado = false;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getNroComprobante(): ?string
    {
        return $this->nroComprobante;
    }

    public function setNroComprobante(string $nroComprobante): self
    {
        $this->nroComprobante = $nroComprobante;

        return $this;
    }

    public function getTipoOperacion(): ?string
    {
        return $this->tipoOperacion;
    }

    public function setTipoOperacion(string $tipoOperacion): self
    {
        $this->tipoOperacion = $tipoOperacion;

        return $this;
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

    public function getImportePagado(): ?float
    {
        return $this->importePagado;
    }

    public function setImportePagado(float $importePagado): self
    {
        $this->importePagado = $importePagado;

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

    public function getEstaConfirmado(): ?bool
    {
        return $this->estaConfirmado;
    }

    public function setEstaConfirmado(bool $estaConfirmado): self
    {
        $this->estaConfirmado = $estaConfirmado;

        return $this;
    }

    public function getGastosAsociados(): ?float
    {
        return $this->gastosAsociados;
    }

    public function setGastosAsociados(?float $gastosAsociados): self
    {
        $this->gastosAsociados = $gastosAsociados;

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
}
