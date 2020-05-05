<?php

/**
 * @license MIT
 */

namespace Birke\Rememberme\Token;

/**
 * Generate an insecure token with the uniqid function.
 *
 * This is only for backwards compatibility with Version 1.
 */
class ClassicToken implements TokenInterface
{

    /**
     * Generate a pseudo-random, 32-byte Token
     * @return string
     */
    public function createToken()
    {
        return md5(uniqid(mt_rand(), true));
    }
}
