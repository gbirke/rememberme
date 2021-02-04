<?php

/**
 * @license MIT
 */
namespace Birke\Rememberme\Storage;

/**
 * This abstract class is for storing the credential/token/persistentToken triplets
 *
 * IMPORTANT SECURITY NOTICE: The storage should not store the token values in the clear.
 * Always use a secure hash function!
 */
abstract class AbstractStorage
{
    const TRIPLET_FOUND = 1;
    const TRIPLET_NOT_FOUND = 0;
    const TRIPLET_INVALID = -1;

    /**
     * Return Tri-state value constant
     *
     * @param mixed  $credential      Unique credential (user id, email address, user name)
     * @param string $token           One-Time Token
     * @param string $persistentToken Persistent Token
     *
     * @return int
     */
    abstract public function findTriplet($credential, $token, $persistentToken);

    /**
     * Store the new token for the credential and the persistent token.
     * Create a new storage entry, if the combination of credential and persistent
     * token does not exist.
     *
     * @param mixed  $credential
     * @param string $token
     * @param string $persistentToken
     * @param int    $expire          Timestamp when this triplet will expire
     */
    abstract public function storeTriplet($credential, $token, $persistentToken, $expire);

    /**
     * Replace current token after successful authentication
     * @param mixed  $credential
     * @param string $token
     * @param string $persistentToken
     * @param int    $expire
     */
    abstract public function replaceTriplet($credential, $token, $persistentToken, $expire);

    /**
     * Remove one triplet of the user from the store
     *
     * @abstract
     *
     * @param mixed  $credential
     * @param string $persistentToken
     *
     * @return void
     */
    abstract public function cleanTriplet($credential, $persistentToken);

    /**
     * Remove all triplets of a user, effectively logging him out on all machines
     *
     * @abstract
     *
     * @param mixed $credential
     *
     * @return void
     */
    abstract public function cleanAllTriplets($credential);

    /**
     * Remove all expired triplets of all users.
     *
     * @abstract
     *
     * @param int $expiryTime Timestamp, all tokens before this time will be deleted
     *
     * @return void
     */
    abstract public function cleanExpiredTokens($expiryTime);

    /**
     * @param string $value
     *
     * @return string
     */
    protected function hash($value)
    {
        return sha1($value);
    }
}
