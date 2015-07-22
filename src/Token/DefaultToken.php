<?php
/**
 * Created by PhpStorm.
 * User: gbirke
 * Date: 22.07.15
 * Time: 21:06
 */

namespace Birke\Rememberme\Token;


class DefaultToken extends AbstractToken
{
    public function createToken()
    {
        return $this->formatBytes( random_bytes($this->tokenBytes) );
    }
}