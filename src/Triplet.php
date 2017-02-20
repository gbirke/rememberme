<?php

namespace Birke\Rememberme;

/**
 * Domain object for credential, persistent and transient token
 */
class Triplet
{
    private $credential;
    private $persistentToken;
    private $oneTimeToken;

    /**
     * @param string $credential
     * @param string $oneTimeToken
     * @param string $persistentToken
     */
    public function __construct($credential = '', $oneTimeToken = '', $persistentToken = '')
    {
        $this->credential = $credential;
        $this->persistentToken = $persistentToken;
        $this->oneTimeToken = $oneTimeToken;
    }

    /**
     * @param string $tripletString
     * @return Triplet
     */
    public static function fromString($tripletString)
    {
        $parts = explode("|", $tripletString, 3);

        if (count($parts) < 3) {
            return new Triplet();
        }

        return new Triplet($parts[0], $parts[1], $parts[2]);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->getCredential() !== '' && $this->getPersistentToken() !== '' && $this->getOneTimeToken() !== '';
    }

    /**
     * @return string
     */
    public function getCredential()
    {
        return $this->credential;
    }

    /**
     * @return string
     */
    public function getPersistentToken()
    {
        return $this->persistentToken;
    }

    /**
     * @return string
     */
    public function getOneTimeToken()
    {
        return $this->oneTimeToken;
    }

    /**
     * @param string $salt
     * @return string
     */
    public function getSaltedPersistentToken($salt)
    {
        return $this->getPersistentToken().$salt;
    }

    /**
     * @param string $salt
     * @return string
     */
    public function getSaltedOneTimeToken($salt)
    {
        return $this->getOneTimeToken().$salt;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode('|', [$this->getCredential(), $this->getOneTimeToken(), $this->getPersistentToken()]);
    }
}
