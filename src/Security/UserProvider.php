<?php

namespace App\Security;

use App\Entity\UsuarioToken;
use App\Repository\UsuarioRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{

    private $userRepository;

    public function __construct(UsuarioRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    /**
     * Symfony calls this method if you use features like switch_user
     * or remember_me.
     *
     * If you're not using these features, you do not need to implement
     * this method.
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        // Load a User object from your data source or throw UsernameNotFoundException.
        // The $username argument may not actually be a username:
        // it is whatever value is being returned by the getUsername()
        // method in your User class.
        return UserInterface::class;
    }

    /**
     * Refreshes the user after being reloaded from the session.
     *
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     *
     * If your firewall is "stateless: true" (for a pure API), this
     * method is not called.
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof UsuarioToken) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $user;
//        dd($user);
//        if (!$user instanceof Usuario) {
//            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
//        }
//        $db_user = $this->userRepository->findOneBy(['username' => $user->getUsername()]);
//        foreach ($db_user->getRoles() as $r) {
//            $rol = 'ROLE_' . strtoupper($r->getIdentificador());
//            $user->addRol($rol);
//        }
//        return $user;

        // Return a User object after making sure its data is "fresh".
        // Or throw a UsernameNotFoundException if the user no longer exists.

    }

    /**
     * Tells Symfony to use this provider for this User class.
     */
    public function supportsClass($class)
    {
        return UsuarioToken::class === $class;
    }

    /**
     * Upgrades the encoded password of a user, typically for using a better hash algorithm.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        // TODO: when encoded passwords are in use, this method should:
        // 1. persist the new password in the user storage
        // 2. update the $user object with $user->setPassword($newEncodedPassword);
    }
}
