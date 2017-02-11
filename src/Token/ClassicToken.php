<?php
/**
 * Created by PhpStorm.
 * User: gbirke
 * Date: 22.07.15
 * Time: 21:26
 */

namespace Birke\Rememberme\Token;

/**
 * Generate an insecure token with the uniqid function.
 *
 * This is only for backwards compatibility with Version 1.
 *
 * @package Birke\Rememberme\Token
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
