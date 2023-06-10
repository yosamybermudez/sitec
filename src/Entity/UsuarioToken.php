<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class UsuarioToken implements UserInterface
{
    /** @var string */
    private $id;

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var string */
    private $salt;

    /** @var string */
    private $roles;

    /** @var string */
    private $displayLastName;

    /** @var string */
    private $datetime;

    /** @var string */
    private $displayName;

    /** @var string */
    private $displayCargo;

    /** @var string */
    private $workerId;

    /** @var string */
    private $email;

    /** @var string */
    private $picture;

    public function __construct($username, $password, array $roles)
    {
        $this->username = $username;
        $this->password = $password;
        $this->salt = '';
        $this->roles = $roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Returns the display name of the authenticated user.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->displayName . ' ' . $this->displayLastName;
    }

    /**
     * Returns the display name of the authenticated user.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set the display name of the authenticated user.
     *
     * @param string $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * Returns the display name of the authenticated user.
     *
     * @return string
     */
    public function getDisplayLastName()
    {
        return $this->displayLastName;
    }

    /**
     * Set the display name of the authenticated user.
     *
     * @param string $displayLastName
     */
    public function setDisplayLastName($displayLastName)
    {
        $this->displayLastName = $displayLastName;
    }

    /**
     * Returns the display name of the authenticated user.
     *
     * @return string
     */
    public function getDisplayCargo()
    {
        return $this->displayCargo;
    }

    /**
     * Set the display name of the authenticated user.
     *
     * @param string $displayCargo
     */
    public function setDisplayCargo($displayCargo)
    {
        $this->displayCargo = $displayCargo;
    }

    /**
     * Returns the  name of the authenticated user.
     *
     * @return string
     */
    public function getAuthenticatedSince()
    {
        return $this->datetime;
    }

    /**
     *
     * @param string $hoy
     */
    public function setAuthenticatedSince($datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * Returns the email address of the authenticated user.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the email address of the authenticated user.
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getWorkerId()
    {
        return $this->workerId;
    }

    public function setWorkerId($workerId)
    {
        $this->workerId = $workerId;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     *
     * @return void
     */
    public function eraseCredentials()
    {
        //return void;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array Role[] The user roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }
}
