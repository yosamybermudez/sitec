<?php

namespace App\Entity;

use App\Repository\DictamenTecnicoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @ORM\Table(name="operac_dictamen_tecnico")
 * @ORM\Entity(repositoryClass=DictamenTecnicoRepository::class)
 */
class DictamenTecnico
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true, options={"default":0})
     */
    private $precio;

    /**
     * @ORM\Column(type="boolean")
     */
    private $dejarEnTaller;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dictamen;

    /**
     * @var OrdenTrabajo
     * @ORM\OneToOne(targetEntity="App\Entity\OrdenTrabajo", inversedBy="dictamenTecnico")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="orden_trabajo_id", referencedColumnName="id")
     * })
     */
    private $ordenTrabajo;

    /**
     * @var Usuario
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="dictaminado_por", referencedColumnName="id")
     * })
     */
    private $dictaminadoPor;

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

    public function __construct(AbstractController $controller)
    {
        $this->id = md5(uniqid("sisgemp", true));
        $this->createdBy = $controller->getDatabaseUser();
        $this->updatedBy = $controller->getDatabaseUser();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->estaConfirmado = false;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function setPrecio(?float $precio): self
    {
        $this->precio = $precio;

        return $this;
    }

    public function getDejarEnTaller(): ?bool
    {
        return $this->dejarEnTaller;
    }

    public function setDejarEnTaller(bool $dejarEnTaller): self
    {
        $this->dejarEnTaller = $dejarEnTaller;

        return $this;
    }

    public function getDictamen(): ?string
    {
        return $this->dictamen;
    }

    public function setDictamen(string $dictamen): self
    {
        $this->dictamen = $dictamen;

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

    public function getEstaConfirmado(): ?bool
    {
        return $this->estaConfirmado;
    }

    public function setEstaConfirmado(bool $estaConfirmado): self
    {
        $this->estaConfirmado = $estaConfirmado;

        return $this;
    }

    public function getDictaminadoPor(): ?Usuario
    {
        return $this->dictaminadoPor;
    }

    public function setDictaminadoPor(?Usuario $dictaminadoPor): self
    {
        $this->dictaminadoPor = $dictaminadoPor;

        return $this;
    }

}
