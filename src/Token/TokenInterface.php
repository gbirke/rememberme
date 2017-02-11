<?php

namespace Birke\Rememberme\Token;

/**
 * Interface for creating random tokens
 */
interface TokenInterface
{
    /**
     * Generate a random, 32-byte Token
     * @return string
     */
    public function createToken();
}
