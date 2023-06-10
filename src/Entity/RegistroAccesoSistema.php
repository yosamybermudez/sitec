<?php

namespace App\Entity;

use App\Repository\RegistroAccesoSistemaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @ORM\Table(name="registro_acceso_sistema")
 * @ORM\Entity(repositoryClass=RegistroAccesoSistemaRepository::class)
 */
class RegistroAccesoSistema
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
     *  @ORM\JoinColumn(name="usuario", referencedColumnName="id")
     * })
     */
    private $usuario;

    /**
     * @ORM\Column(name="login_date", type="datetime", nullable=false)
     */
    private $loginDate;

    /**
     * @ORM\Column(name="logout_date", type="datetime", nullable=true)
     */
    private $logoutDate;

    /**
     * @ORM\Column(name="remote_addr", type="string", length=255, nullable=true)
     */
    private $remoteAddr;

    /**
     * @ORM\Column(name="http_user_agent", type="string", length=255, nullable=true)
     */
    private $httpUserAngent;

    /**
     * Constructor
     */
    public function __construct(Usuario $usuario)
    {
        $this->id = md5(uniqid("sisgemp", true));
        $this->usuario = $usuario;
        $this->loginDate = new \DateTime();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLoginDate(): ?\DateTime
    {
        return $this->loginDate;
    }

    public function setLoginDate(\DateTime $loginDate): self
    {
        $this->loginDate = $loginDate;

        return $this;
    }

    public function getLogoutDate(): ?\DateTime
    {
        return $this->logoutDate;
    }

    public function setLogoutDate(\DateTime $logoutDate): self
    {
        $this->logoutDate = $logoutDate;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getRemoteAddr(): ?string
    {
        return $this->remoteAddr;
    }

    public function setRemoteAddr(?string $remoteAddr): self
    {
        $this->remoteAddr = $remoteAddr;

        return $this;
    }

    public function getHttpUserAngent(): ?string
    {
        return $this->httpUserAngent;
    }

    public function setHttpUserAngent(?string $httpUserAngent): self
    {
        $this->httpUserAngent = $httpUserAngent;

        return $this;
    }
}
