<?php

namespace App\Security;

use App\Entity\MyLdap;
use App\Repository\MyLdapRepository;
use App\Repository\UsuarioRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Ldap\Exception\ConnectionException;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\AuthenticatorInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class DatabaseLdapAuthenticator extends AbstractFormLoginAuthenticator implements AuthenticatorInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;

    private $userRepository;
    private $ldapRepository;
    private $router;

    public function __construct(RouterInterface $router, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder, UsuarioRepository $userRepository, MyLdapRepository $ldapRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->ldapRepository = $ldapRepository;
        $this->router = $router;
    }

    public function supports(Request $request)
    {
//
//        dd(self::LOGIN_ROUTE === $request->attributes->get('_route')
//            && $request->isMethod('POST'));
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {

        $credentials = [
            'username' => strtolower($request->request->get('_username')),
            'password' => $request->request->get('_password'),
            'domain' => $request->request->get('_domain'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );
        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
        $domain = $credentials["domain"];
        $userDB = $this->userRepository->findOneBy(array('username' => $credentials["username"]));
        //En la BD tiene que existir un usuario con ese Username
        if (!$userDB) {
            throw new CustomUserMessageAuthenticationException("El usuario no se encuentra registrado en el sistema. Contacte al administrador del sistema.");
        } else {
            if ($domain === 'local') {
                return $userDB;
            } else {
                $ldap = $this->ldapRepository->find($domain);
                $result = $this->checkUser($ldap, $credentials['username']);
                if ($result) {
                    $userLdap = $this->userRepository->findOneBy(array('username' => $credentials["username"]));
                    $userLdap->setPassword($result->getAttribute("userPassword")[0]);
                    return $userLdap;
                }
                throw new CustomUserMessageAuthenticationException("El usuario no se encuentra registrado en el LDAP seleccionar. Intente otro dominio.");
            }
        }
    }

    protected function checkUser(MyLdap $myLdap, string $username)
    {
        $ldap = Ldap::create('ext_ldap', [
            'connection_string' => 'ldap://' . $myLdap->getHost() . ':' . $myLdap->getPort()
        ]);
        $filters = explode(',', $myLdap->getFilter());
        try{
            $ldap->bind($myLdap->getUsername(), $myLdap->getPassword());
        } catch (ConnectionException $e){
            throw new CustomUserMessageAuthenticationException("No se pudo establecer la conexión con el servidor LDAP " . $myLdap->getName() . ".");
        }
        $query = $ldap->query($myLdap->getBase(), ($myLdap->getUserAttr() . '=' . $username), array('filter' => $filters));
        $array = $query->execute()->toArray();

        return $array[0] ?? null;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $domain = $credentials["domain"];
        if ($domain != 'local') {
            $myLdap = $this->ldapRepository->find($domain);
            if (!$this->validCredentials($myLdap, $credentials['username'], $credentials['password'])) {
                throw new CustomUserMessageAuthenticationException("La contraseña es incorrecta.");
            }
        } else {
            if(!$this->passwordEncoder->isPasswordValid($user, $this->getPassword($credentials))){
                throw new CustomUserMessageAuthenticationException("La contraseña es incorrecta.");
            }
        }
        return true;
    }

    protected function validCredentials(MyLdap $myLdap, string $username, string $password): bool
    {
        try {
            $dn = $this->checkUser($myLdap, $username)->getDn();
            $ldap = Ldap::create('ext_ldap', [
                'connection_string' => 'ldap://' . $myLdap->getHost() . ':' . $myLdap->getPort()
            ]);
            $ldap->bind($dn, $password);
        } catch (\Exception $e) {
            throw new CustomUserMessageAuthenticationException("No se pudo establecer la conexión con el servidor LDAP " . $myLdap->getName() . ".");
        }
        return true;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if($this->passwordEncoder->isPasswordValid($token->getUser(), 'sitec')){
            return new RedirectResponse($this->router->generate('usuario_change_password', array('id' => $token->getUser()->getId())));
        }
        return new RedirectResponse($this->router->generate('app_module_index'));
    }

    public function createAuthenticatedToken(UserInterface $user, string $providerKey)
    {
        return new PostAuthenticationGuardToken($user, $providerKey, $user->getRoles());
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
