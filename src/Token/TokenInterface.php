<?php

namespace Birke\Rememberme\Token;

interface TokenInterface {
    /**
     * Generate a random, 32-byte Token
     * @return string
     */
    public function createToken();
}