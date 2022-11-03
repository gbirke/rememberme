<?php

/**
 * @license MIT
 */

namespace Birke\Rememberme;

use Birke\Rememberme\Cookie\CookieInterface;
use Birke\Rememberme\Cookie\PHPCookie;
use Birke\Rememberme\Token\DefaultToken;
use Birke\Rememberme\Token\TokenInterface;
use Exception;

/**
 * Authenticate via "remember me" cookie
 */
class Authenticator
{

    /**
     * @var Cookie\CookieInterface
     */
    protected $cookie;

    /**
     * @var Storage\AbstractStorage
     */
    protected $storage;

    /**
     * @var Token\TokenInterface
     */
    protected $tokenGenerator;

    /**
     * Number of seconds in the future tokens in the storage will expire (defaults to 1 week)
     * @var int
     */
    protected $expireTime = 604800;

    /**
     * If the login token was invalid, delete all login tokens of this user
     * @var bool
     */
    protected $cleanStoredTokensOnInvalidResult = true;

    /**
     * Always clean expired tokens of users when login is called.
     *
     * Disabled by default for performance reasons, but useful for
     * hosted systems that can't run periodic scripts.
     *
     * @var bool
     */
    protected $cleanExpiredTokensOnLogin = false;

    /**
     * Additional salt to add more entropy when the tokens are stored as hashes.
     * @var string
     */
    protected $salt = "";

    /**
     * @param Storage\AbstractStorage $storage
     * @param TokenInterface          $tokenGenerator
     * @param Cookie\CookieInterface  $cookie
     */
    public function __construct(Storage\AbstractStorage $storage, TokenInterface $tokenGenerator = null, Cookie\CookieInterface $cookie = null)
    {
        if (is_null($tokenGenerator)) {
            $tokenGenerator = new DefaultToken();
        }
        if (is_null($cookie)) {
            $cookie = new PHPCookie();
        }
        $this->storage = $storage;
        $this->cookie = $cookie;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * Check Credentials from cookie. Returns false if login was not successful, credential string if it was successful
     * @return LoginResult
     *
     * @throws Exception
     */
    public function login()
    {
        $cookieValue = $this->cookie->getValue();

        if (!$cookieValue) {
            return LoginResult::newNoCookieResult();
        }

        $triplet = Triplet::fromString($cookieValue);

        if (!$triplet->isValid()) {
            return LoginResult::newManipulationResult();
        }

        if ($this->cleanExpiredTokensOnLogin) {
            $this->storage->cleanExpiredTokens(time());
        }

        $tripletLookupResult = $this->storage->findTriplet(
            $triplet->getCredential(),
            $triplet->getSaltedOneTimeToken($this->salt),
            $triplet->getSaltedPersistentToken($this->salt)
        );
        switch ($tripletLookupResult) {
            case Storage\AbstractStorage::TRIPLET_FOUND:
                $expire = time() + $this->expireTime;
                $newTriplet = new Triplet($triplet->getCredential(), $this->tokenGenerator->createToken(), $triplet->getPersistentToken());
                $this->storage->replaceTriplet(
                    $newTriplet->getCredential(),
                    $newTriplet->getSaltedOneTimeToken($this->salt),
                    $newTriplet->getSaltedPersistentToken($this->salt),
                    $expire
                );
                $this->cookie->setValue((string) $newTriplet);

                return LoginResult::newSuccessResult($triplet->getCredential());

            case Storage\AbstractStorage::TRIPLET_INVALID:
                $this->cookie->deleteCookie();

                if ($this->cleanStoredTokensOnInvalidResult) {
                    $this->storage->cleanAllTriplets($triplet->getCredential());
                }

                return LoginResult::newManipulationResult();
            default:
                return LoginResult::newExpiredResult();
        }
    }

    /**
     * @param mixed $credential
     *
     * @return $this
     *
     * @throws Exception
     */
    public function createCookie($credential)
    {
        $newToken = $this->tokenGenerator->createToken();
        $newPersistentToken = $this->tokenGenerator->createToken();

        $expire = time() + $this->expireTime;

        $this->storage->storeTriplet($credential, $newToken.$this->salt, $newPersistentToken.$this->salt, $expire);
        $this->cookie->setValue(implode("|", array($credential, $newToken, $newPersistentToken)));

        return $this;
    }

    /**
     * Expire the rememberme cookie, unset $_COOKIE[$this->cookieName] value and
     * remove current login triplet from storage.
     * @return boolean
     */
    public function clearCookie()
    {

        $triplet = Triplet::fromString($this->cookie->getValue());

        $this->cookie->deleteCookie();

        if (!$triplet->isValid()) {
            return false;
        }

        $this->storage->cleanTriplet($triplet->getCredential(), $triplet->getSaltedPersistentToken($this->salt));

        return true;
    }

    /**
     * @param CookieInterface $cookie
     *
     * @return $this
     */
    public function setCookie(CookieInterface $cookie)
    {
        $this->cookie = $cookie;

        return $this;
    }

    /**
     * @return CookieInterface
     */
    public function getCookie()
    {
        return $this->cookie;
    }

    /**
     * @param bool $cleanStoredCookies
     *
     * @return Authenticator
     */
    public function setCleanStoredTokensOnInvalidResult($cleanStoredCookies)
    {
        $this->cleanStoredTokensOnInvalidResult = $cleanStoredCookies;

        return $this;
    }

    /**
     * @return bool
     */
    public function getCleanStoredTokensOnInvalidResult()
    {
        return $this->cleanStoredTokensOnInvalidResult;
    }

    /**
     * Return how many seconds in the future that the cookie will expire
     * @return int
     */
    public function getExpireTime()
    {
        return $this->expireTime;
    }

    /**
     * @param int $expireTime How many seconds in the future the cookie will expire
     *
     *                        Default is 604800 (1 week)
     *
     * @return Authenticator
     */
    public function setExpireTime($expireTime)
    {
        $this->expireTime = $expireTime;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * The salt is additional information that is added to the tokens to make
     * them more unique and secure. The salt is not stored in the cookie and
     * should not be saved in the storage.
     *
     * For example, to bind a token to an IP address use $_SERVER['REMOTE_ADDR'].
     * To bind a token to the browser (user agent), use $_SERVER['HTTP_USER_AGENT].
     * You could also use a long random string that is unique to your application.
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @return boolean
     */
    public function isCleanExpiredTokensOnLogin()
    {
        return $this->cleanExpiredTokensOnLogin;
    }

    /**
     * @param boolean $cleanExpiredTokensOnLogin
     */
    public function setCleanExpiredTokensOnLogin($cleanExpiredTokensOnLogin)
    {
        $this->cleanExpiredTokensOnLogin = $cleanExpiredTokensOnLogin;
    }
}
