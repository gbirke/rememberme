<?php

/**
 * @license MIT
 */

namespace Birke\Rememberme\Token;

use Exception;

/**
 * A token generated based on the PHP function random_bytes
 */
class DefaultToken extends AbstractToken
{
    /**
     * @inheritdoc
     *
     * @return string
     *
     * @throws Exception
     */
    public function createToken()
    {
        return $this->formatBytes(random_bytes($this->tokenBytes));
    }
}
