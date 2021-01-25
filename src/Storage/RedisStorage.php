<?php

/**
 * @license MIT
 */

namespace Birke\Rememberme\Storage;

/**
 * Redis-Based Storage
 *
 * @author Michaël Thieulin
 */
class RedisStorage extends AbstractStorage
{
    /**
     * @var Predis\Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $keyPrefix = 'rememberme';


    /**
     * @param Predis\Client $client
     * @param string        $keyPrefix
     */
    public function __construct(Predis\Client $client, $keyPrefix = 'rememberme')
    {
        $this->client = $client;
        $this->keyPrefix = $keyPrefix;
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
        // Hash the tokens, because they can contain a salt and can be accessed in redis
        $persistentToken = $this->hash($persistentToken);
        $token = $this->hash($token);
        $key = $this->getKeyname($credential, $persistentToken);

        if ($this->client->exists($key) === 0) {
            return self::TRIPLET_NOT_FOUND;
        }

        $redisToken = trim($this->client->get($key));

        if ($redisToken === $token) {
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
    public function storeTriplet($credential, $token, $persistentToken, $expire = 0)
    {
        // Hash the tokens, because they can contain a salt and can be accessed in redis
        $persistentToken = $this->hash($persistentToken);
        $token = $this->hash($token);
        $key = $this->getKeyname($credential, $persistentToken);
        $this->client->set($key, $token);

        if ($expire > 0) {
            $this->client->expireat($key, $expire);
        }

        return $this;
    }

    /**
     * Replace current token after successful authentication
     * @param mixed  $credential
     * @param string $token
     * @param string $persistentToken
     * @param int    $expire
     */
    public function replaceTriplet($credential, $token, $persistentToken, $expire = 0)
    {
        $this->cleanTriplet($credential, $persistentToken);
        $this->storeTriplet($credential, $token, $persistentToken, $expire);
    }

    /**
     * @param mixed  $credential
     * @param string $persistentToken
     */
    public function cleanTriplet($credential, $persistentToken)
    {
        $persistentToken = $this->hash($persistentToken);
        $key = $this->getKeyname($credential, $persistentToken);

        if ($this->client->exists($key) === 1) {
            $this->client->del($key);
        }
    }

    /**
     * @param mixed $credential
     */
    public function cleanAllTriplets($credential)
    {
        foreach ($this->client->keys($this->keyPrefix.':'.$credential.':*') as $key) {
            $this->client->del($key);
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
        // Redis will automatically delete the key after the timeout has expired.
    }

    /**
     * @param string $credential
     * @param string $persistentToken
     *
     * @return string
     */
    protected function getKeyname($credential, $persistentToken)
    {
        return $this->keyPrefix.':'.$credential.':'.$persistentToken;
    }
}
