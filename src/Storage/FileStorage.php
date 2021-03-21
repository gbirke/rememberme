<?php

/**
 * @license MIT
 */

namespace Birke\Rememberme\Storage;

/**
 * File-Based Storage
 */
class FileStorage extends AbstractStorage
{
    /**
     * @var string
     */
    protected $path = "";

    /**
     * @var string
     */
    protected $suffix = ".txt";

    /**
     * @param string $path
     * @param string $suffix
     */
    public function __construct($path = "", $suffix = ".txt")
    {
        $this->path = $path;
        $this->suffix = $suffix;
    }

    /**
     * @param mixed  $credential
     * @param string $token
     * @param string $persistentToken
     *
     * @return int
     */
    public function findTriplet($credential, $token, $persistentToken)
    {
        // Hash the tokens, because they can contain a salt and can be accessed in the file system
        $persistentToken = $this->hash($persistentToken);
        $token = $this->hash($token);
        $fn = $this->getFilename($credential, $persistentToken);

        if (!file_exists($fn)) {
            return self::TRIPLET_NOT_FOUND;
        }

        $fileToken = trim(file_get_contents($fn));

        if ($fileToken === $token) {
            return self::TRIPLET_FOUND;
        }

        return self::TRIPLET_INVALID;
    }

    /**
     * @param mixed  $credential
     * @param string $token
     * @param string $persistentToken
     * @param int    $expire
     *
     * @return $this
     */
    public function storeTriplet($credential, $token, $persistentToken, $expire)
    {
        // Hash the tokens, because they can contain a salt and can be accessed in the file system
        $persistentToken = $this->hash($persistentToken);
        $token = $this->hash($token);
        $fn = $this->getFilename($credential, $persistentToken);
        file_put_contents($fn, $token);

        return $this;
    }

    /**
     * @param mixed  $credential
     * @param string $persistentToken
     */
    public function cleanTriplet($credential, $persistentToken)
    {
        $persistentToken = $this->hash($persistentToken);
        $fn = $this->getFilename($credential, $persistentToken);

        if (file_exists($fn)) {
            unlink($fn);
        }
    }

    /**
     * Replace current token after successful authentication
     * @param mixed  $credential
     * @param string $token
     * @param string $persistentToken
     * @param int    $expire
     */
    public function replaceTriplet($credential, $token, $persistentToken, $expire)
    {
        $this->cleanTriplet($credential, $persistentToken);
        $this->storeTriplet($credential, $token, $persistentToken, $expire);
    }

    /**
     * @param mixed $credential
     */
    public function cleanAllTriplets($credential)
    {
        foreach (glob($this->path.DIRECTORY_SEPARATOR.$credential.".*".$this->suffix) as $file) {
            unlink($file);
        }
    }

    /**
     * Remove all expired triplets of all users.
     *
     * @param int $expiryTime Timestamp, all tokens before this time will be deleted
     *
     * @return void
     */
    public function cleanExpiredTokens($expiryTime)
    {
        foreach (glob($this->path.DIRECTORY_SEPARATOR."*".$this->suffix) as $file) {
            if (filemtime($file) < $expiryTime) {
                unlink($file);
            }
        }
    }

    /**
     * @param $credential
     * @param $persistentToken
     *
     * @return string
     */
    protected function getFilename($credential, $persistentToken)
    {
        return $this->path.DIRECTORY_SEPARATOR.$credential.".".$persistentToken.$this->suffix;
    }
}
