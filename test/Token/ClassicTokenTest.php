<?php

/**
 * @license MIT
 */

namespace Birke\Rememberme\Test;

use Birke\Rememberme\Token\ClassicToken;
use PHPUnit\Framework\TestCase;

class ClassicTokenTest extends TestCase
{

    /**
     * @var ClassicToken
     */
    protected $token;

    protected function setUp(): void
    {
        $this->token = new ClassicToken();
    }

    public function testTokenIs32CharsInHexadecimal()
    {
        $this->assertRegExp("/^[\\da-f]{32}$/", $this->token->createToken());
    }
}
