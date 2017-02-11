<?php

namespace Birke\Rememberme\Token;

use RandomLib\Factory;
use RandomLib\Generator;

/**
 * A token class that uses ircmaxell/random-lib to generate secure random tokens
 *
 * @package Birke\Rememberme\Token
 */
class RandomLibToken extends AbstractToken
{
    /**
     * @var Generator
     */
    protected $generator;

    protected $formatMap;

    /**
     * @param int            $tokenBytes
     * @param string         $tokenFormat
     * @param Generator|null $generator
     */
    public function __construct($tokenBytes = 32, $tokenFormat = self::FORMAT_HEX, Generator $generator = null)
    {
        parent::__construct($tokenBytes, $tokenFormat);
        if (is_null($generator)) {
            $factory = new Factory();
            $this->generator = $factory->getMediumStrengthGenerator();
        } else {
            $this->generator = $generator;
        }
        $this->formatMap = [
            self::FORMAT_HEX => Generator::CHAR_LOWER_HEX,
            self::FORMAT_PLAIN => Generator::CHAR_BASE64,
            self::FORMAT_BASE64 => Generator::CHAR_BASE64,
        ];
    }

    /**
     * Generate a random, 32-byte Token
     * @return string
     */
    public function createToken()
    {
        return $this->generator->generateString($this->tokenBytes, $this->formatMap[$this->tokenFormat]);
    }
}
