<?php

namespace Birke\Rememberme\Token;

/**
 * Common utility class for tokens
 *
 * It can output tokens in different lengths and formats - raw bytes, hexadecimal and base64
 *
 * @package Birke\Rememberme\Token
 */
abstract class AbstractToken implements TokenInterface
{

    const FORMAT_HEX    = 'hex'; // doubles the space needed
    const FORMAT_PLAIN  = 'plain';
    const FORMAT_BASE64 = 'base64'; // space needed * 1.6

    protected $tokenBytes = 16;
    protected $tokenFormat = self::FORMAT_HEX;

    /**
     * @param int    $tokenBytes  How many bytes the token shall contain
     * @param string $tokenFormat How the bytes shall be formatted. Can increase the string returned
     */
    public function __construct($tokenBytes = 16, $tokenFormat = self::FORMAT_HEX)
    {
        if (!in_array($tokenFormat, [self::FORMAT_HEX, self::FORMAT_PLAIN, self::FORMAT_BASE64])) {
            throw new \InvalidArgumentException("Invalid token format");
        }
        $this->tokenBytes = $tokenBytes;
        $this->tokenFormat = $tokenFormat;
    }

    protected function formatBytes($token)
    {
        switch ($this->tokenFormat) {
            case self::FORMAT_HEX:
                return bin2hex($token);
            case self::FORMAT_PLAIN:
                return $token;
            case self::FORMAT_BASE64:
                return base64_encode($token);
        }
    }
}
