<?php

namespace App\Entity;

use App\Repository\OrdenTrabajoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @ORM\Table(name="operac_orden_trabajo")
 * @ORM\Entity(repositoryClass=OrdenTrabajoRepository::class)
 */
class OrdenTrabajo
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=false, unique=true)
     */
    private $nroOrden;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $clienteNombreCompleto;

    /**
     * @ORM\Column(type="string", length=11, nullable=false)
     */
    private $clienteCarneIdentidad;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $clienteTelefonosContacto;



    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $equipoMarca;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $estado;

    /**
     * @var EquipoTipo
     * @ORM\ManyToOne(targetEntity="App\Entity\EquipoTipo")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(referencedColumnName="id")
     * })
     */
    private $equipoTipo;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $equipoModelo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $equipoSerie;

    /**
     * @var Usuario
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario", inversedBy="ordenesTrabajo")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(referencedColumnName="id")
     * })
     */
    private $tecnicoRepara;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $fechaEntrada;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaDictamen;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaFacturacion;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaRevision;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaListoEntregar;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaNotificacion;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaDecomiso;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaSalida;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observaciones;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observacionesFinales;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $motivoVisita;

    /**
     * @var Collection
     * @ORM\OneToOne(targetEntity="App\Entity\OrdenReparacion", mappedBy="ordenTrabajo")
     */
    private $ordenReparacion;

    /**
     * @var Collection
     * @ORM\OneToOne(targetEntity="App\Entity\ComprobanteOperacion", mappedBy="ordenTrabajo")
     */
    private $comprobante;

    /**
     * @var Collection
     * @ORM\OneToOne(targetEntity="App\Entity\DictamenTecnico", mappedBy="ordenTrabajo")
     */
    private $dictamenTecnico;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":false})
     */
    private $esReparacion;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $dejadoEnTaller;

    /**
     * @ORM\Column(type="boolean", nullable=false)
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
     * @var OrdenTrabajo
     * @ORM\ManyToOne(targetEntity="App\Entity\OrdenTrabajo", inversedBy="garantiasAsociadas")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="garantia_orden_principal", referencedColumnName="id")
     * })
     */
    private $garantiaOrdenPrincipal;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\OrdenTrabajo", mappedBy="garantiaOrdenPrincipal")
     */
    private $garantiasAsociadas;

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
        $this->dejadoEnTaller = false;
        $this->garantiasAsociadas = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getNroOrden(): ?string
    {
        return $this->nroOrden;
    }

    public function setNroOrden(string $nroOrden): self
    {
        $this->nroOrden = $nroOrden;

        return $this;
    }

    public function getClienteNombreCompleto(): ?string
    {
        return $this->clienteNombreCompleto;
    }

    public function setClienteNombreCompleto(string $clienteNombreCompleto): self
    {
        $this->clienteNombreCompleto = $clienteNombreCompleto;

        return $this;
    }

    public function getClienteCarneIdentidad(): ?string
    {
        return $this->clienteCarneIdentidad;
    }

    public function setClienteCarneIdentidad(string $clienteCarneIdentidad): self
    {
        $this->clienteCarneIdentidad = $clienteCarneIdentidad;

        return $this;
    }

    public function getClienteTelefonosContacto(): ?string
    {
        return $this->clienteTelefonosContacto;
    }

    public function setClienteTelefonosContacto(?string $clienteTelefonosContacto): self
    {
        $this->clienteTelefonosContacto = $clienteTelefonosContacto;

        return $this;
    }

    public function getEquipoMarca(): ?string
    {
        return $this->equipoMarca;
    }

    public function setEquipoMarca(string $equipoMarca): self
    {
        $this->equipoMarca = $equipoMarca;

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

    public function getEquipoModelo(): ?string
    {
        return $this->equipoModelo;
    }

    public function setEquipoModelo(string $equipoModelo): self
    {
        $this->equipoModelo = $equipoModelo;

        return $this;
    }

    public function getFechaEntrada(): ?\DateTimeInterface
    {
        return $this->fechaEntrada;
    }

    public function setFechaEntrada(\DateTimeInterface $fechaEntrada): self
    {
        $this->fechaEntrada = $fechaEntrada;

        return $this;
    }

    public function getFechaDictamen(): ?\DateTimeInterface
    {
        return $this->fechaDictamen;
    }

    public function setFechaDictamen(?\DateTimeInterface $fechaDictamen): self
    {
        $this->fechaDictamen = $fechaDictamen;

        return $this;
    }

    public function getFechaFacturacion(): ?\DateTimeInterface
    {
        return $this->fechaFacturacion;
    }

    public function setFechaFacturacion(?\DateTimeInterface $fechaFacturacion): self
    {
        $this->fechaFacturacion = $fechaFacturacion;

        return $this;
    }

    public function getFechaRevision(): ?\DateTimeInterface
    {
        return $this->fechaRevision;
    }

    public function setFechaRevision(?\DateTimeInterface $fechaRevision): self
    {
        $this->fechaRevision = $fechaRevision;

        return $this;
    }

    public function getFechaListoEntregar(): ?\DateTimeInterface
    {
        return $this->fechaListoEntregar;
    }

    public function setFechaListoEntregar(?\DateTimeInterface $fechaListoEntregar): self
    {
        $this->fechaListoEntregar = $fechaListoEntregar;

        return $this;
    }

    public function getFechaNotificacion(): ?\DateTimeInterface
    {
        return $this->fechaNotificacion;
    }

    public function setFechaNotificacion(?\DateTimeInterface $fechaNotificacion): self
    {
        $this->fechaNotificacion = $fechaNotificacion;

        return $this;
    }

    public function getFechaSalida(): ?\DateTimeInterface
    {
        return $this->fechaSalida;
    }

    public function setFechaSalida(?\DateTimeInterface $fechaSalida): self
    {
        $this->fechaSalida = $fechaSalida;

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

    public function getObservacionesFinales(): ?string
    {
        return $this->observacionesFinales;
    }

    public function setObservacionesFinales(?string $observacionesFinales): self
    {
        $this->observacionesFinales = $observacionesFinales;

        return $this;
    }

    public function getMotivoVisita(): ?string
    {
        return $this->motivoVisita;
    }

    public function setMotivoVisita(string $motivoVisita): self
    {
        $this->motivoVisita = $motivoVisita;

        return $this;
    }

    public function getEsReparacion(): ?bool
    {
        return $this->esReparacion;
    }

    public function setEsReparacion(bool $esReparacion): self
    {
        $this->esReparacion = $esReparacion;

        return $this;
    }

    public function getDejadoEnTaller(): ?bool
    {
        return $this->dejadoEnTaller;
    }

    public function setDejadoEnTaller(bool $dejadoEnTaller): self
    {
        $this->dejadoEnTaller = $dejadoEnTaller;

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

    public function getEquipoTipo(): ?EquipoTipo
    {
        return $this->equipoTipo;
    }

    public function setEquipoTipo(?EquipoTipo $equipoTipo): self
    {
        $this->equipoTipo = $equipoTipo;

        return $this;
    }

    public function getTecnicoRepara(): ?Usuario
    {
        return $this->tecnicoRepara;
    }

    public function setTecnicoRepara(?Usuario $tecnicoRepara): self
    {
        $this->tecnicoRepara = $tecnicoRepara;

        return $this;
    }

    public function getOrdenReparacion(): ?OrdenReparacion
    {
        return $this->ordenReparacion;
    }

    public function setOrdenReparacion(?OrdenReparacion $ordenReparacion): self
    {
        $this->ordenReparacion = $ordenReparacion;

        // set (or unset) the owning side of the relation if necessary
        $newOrdenTrabajo = null === $ordenReparacion ? null : $this;
        if ($ordenReparacion->getOrdenTrabajo() !== $newOrdenTrabajo) {
            $ordenReparacion->setOrdenTrabajo($newOrdenTrabajo);
        }

        return $this;
    }

    public function getDictamenTecnico(): ?DictamenTecnico
    {
        return $this->dictamenTecnico;
    }

    public function setDictamenTecnico(?DictamenTecnico $dictamenTecnico): self
    {
        $this->dictamenTecnico = $dictamenTecnico;

        // set (or unset) the owning side of the relation if necessary
        $newOrdenTrabajo = null === $dictamenTecnico ? null : $this;
        if ($dictamenTecnico->getOrdenTrabajo() !== $newOrdenTrabajo) {
            $dictamenTecnico->setOrdenTrabajo($newOrdenTrabajo);
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

    public function getGarantiaOrdenPrincipal(): ?self
    {
        return $this->garantiaOrdenPrincipal;
    }

    public function setGarantiaOrdenPrincipal(?self $garantiaOrdenPrincipal): self
    {
        $this->garantiaOrdenPrincipal = $garantiaOrdenPrincipal;

        return $this;
    }

    /**
     * @return Collection|OrdenTrabajo[]
     */
    public function getGarantiasAsociadas(): Collection
    {
        return $this->garantiasAsociadas;
    }

    public function addGarantiasAsociada(OrdenTrabajo $garantiasAsociada): self
    {
        if (!$this->garantiasAsociadas->contains($garantiasAsociada)) {
            $this->garantiasAsociadas[] = $garantiasAsociada;
            $garantiasAsociada->setGarantiaOrdenPrincipal($this);
        }

        return $this;
    }

    public function removeGarantiasAsociada(OrdenTrabajo $garantiasAsociada): self
    {
        if ($this->garantiasAsociadas->contains($garantiasAsociada)) {
            $this->garantiasAsociadas->removeElement($garantiasAsociada);
            // set the owning side to null (unless already changed)
            if ($garantiasAsociada->getGarantiaOrdenPrincipal() === $this) {
                $garantiasAsociada->setGarantiaOrdenPrincipal(null);
            }
        }

        return $this;
    }

    public function getEquipoSerie(): ?string
    {
        return $this->equipoSerie;
    }

    public function setEquipoSerie(?string $equipoSerie): self
    {
        $this->equipoSerie = $equipoSerie;

        return $this;
    }

    public function getEstadoFinalOrdenTrabajo(): ?string
    {
        $garantias = $this->getGarantiasAsociadas()->toArray();



        if(count($garantias) > 0){
            $garantia = $garantias[count($garantias)-1];
            return $this->getObservacionesFinalesConvertedAux($garantia->getObservacionesFinales());
        }
        return $this->getObservacionesFinalesConvertedAux($this->getObservacionesFinales());
    }

    public function getObservacionesFinalesConvertedAux($observaciones): string{
        return ($observaciones === null or $observaciones !== 'R') ? 'NR' : 'R';
    }

    public function getObservacionesFinalesConverted(): string{
        return ($this->getObservacionesFinales() === null or $this->getObservacionesFinales() !== 'R') ? 'NR' : 'R';
    }

    public function getComprobante(): ?ComprobanteOperacion
    {
        return $this->comprobante;
    }

    public function setComprobante(?ComprobanteOperacion $comprobante): self
    {
        $this->comprobante = $comprobante;

        // set (or unset) the owning side of the relation if necessary
        $newOrdenTrabajo = null === $comprobante ? null : $this;
        if ($comprobante->getOrdenTrabajo() !== $newOrdenTrabajo) {
            $comprobante->setOrdenTrabajo($newOrdenTrabajo);
        }

        return $this;
    }

    public function getFechaDecomiso(): ?\DateTimeInterface
    {
        return $this->fechaDecomiso;
    }

    public function setFechaDecomiso(?\DateTimeInterface $fechaDecomiso): self
    {
        $this->fechaDecomiso = $fechaDecomiso;

        return $this;
    }

    public function getDatosEquipo(){
        return $this->getEquipoTipo()->getNombre() . " " . $this->getEquipoMarca() . " " . $this->getEquipoModelo() . ($this->getEquipoSerie() !== null ? " con S/N: " . $this->getEquipoSerie() : "");
    }

    public function getDatosCliente(){
        return $this->getClienteNombreCompleto() . " / " . $this->getClienteCarneIdentidad() . " / " . $this->getClienteTelefonosContacto();
    }

}
