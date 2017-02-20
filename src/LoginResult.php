<?php

namespace Birke\Rememberme;

/**
 * Represents the current state of the "Remember me" login.
 *
 * @package Birke\Rememberme
 */
class LoginResult
{
    private $cookieExists;
    private $tripleWasFound;
    private $tripleWasValid;
    private $credential;

    /**
     * @param bool  $cookieExists
     * @param bool  $tripleWasFound
     * @param bool  $tripleWasValid
     * @param mixed $credential
     */
    private function __construct($cookieExists = false, $tripleWasFound = false, $tripleWasValid = false, $credential = null)
    {
        $this->cookieExists = $cookieExists;
        $this->tripleWasFound = $tripleWasFound;
        $this->tripleWasValid = $tripleWasValid;
        $this->credential = $credential;
    }

    /**
     * Create new successful result with credentials
     *
     * @param mixed $credential
     * @return LoginResult
     */
    public static function newSuccessResult($credential)
    {
        // See https://github.com/djoos/Symfony2-coding-standard/issues/54
        // @codingStandardsIgnoreLine
        return new self(true, true, true, $credential);
    }

    /**
     * Create new result that indicates that the tokens might have been manipulated
     *
     * @return LoginResult
     */
    public static function newManipulationResult()
    {
        return new self(true, true, false);
    }

    /**
     * Create new result that indicates the tokens have expired
     *
     * @return LoginResult
     */
    public static function newExpiredResult()
    {
        return new self(true, false, false);
    }

    /**
     * @return LoginResult
     */
    public static function newNoCookieResult()
    {
        return new self(false);
    }

    /**
     * @return bool
     */
    public function cookieExists()
    {
        return $this->cookieExists;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->cookieExists && $this->tripleWasFound && $this->tripleWasValid;
    }

    /**
     * @return bool
     */
    public function hasPossibleManipulation()
    {
        return $this->cookieExists && $this->tripleWasFound && !$this->tripleWasValid;
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        return $this->cookieExists && !$this->tripleWasFound;
    }

    /**
     * @return mixed|null
     */
    public function getCredential()
    {
        return $this->credential;
    }
}
