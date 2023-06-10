<?php

namespace App\Entity;

use App\Repository\MyLdapRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @ORM\Table(name="sistema_ldap")
 * @ORM\Entity(repositoryClass=MyLdapRepository::class)
 */
class MyLdap
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $host;

    /**
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @ORM\Column(type="integer")
     */
    private $port;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $query;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userAttr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $base;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $authenticationMethod;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $encryptionMethod;

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
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(int $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function setQuery(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getFilter(): ?string
    {
        return $this->filter;
    }

    public function setFilter(?string $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    public function getUserAttr(): ?string
    {
        return $this->userAttr;
    }

    public function setUserAttr(?string $userAttr): self
    {
        $this->userAttr = $userAttr;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getBase(): ?string
    {
        return $this->base;
    }

    public function setBase(string $base): self
    {
        $this->base = $base;

        return $this;
    }

    public function getAuthenticationMethod(): ?string
    {
        return $this->authenticationMethod;
    }

    public function setAuthenticationMethod(string $authenticationMethod): self
    {
        $this->authenticationMethod = $authenticationMethod;

        return $this;
    }

    public function getEncryptionMethod(): ?string
    {
        return $this->encryptionMethod;
    }

    public function setEncryptionMethod(string $encryptionMethod): self
    {
        $this->encryptionMethod = $encryptionMethod;

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
}
