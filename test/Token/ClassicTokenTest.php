<?php

class ClassicTokenTest extends PHPUnit_Framework_TestCase {

    /**
     * @var \Birke\Rememberme\Token\ClassicToken
     */
    protected $token;

    protected function setUp()
    {
        $this->token = new \Birke\Rememberme\Token\ClassicToken();
    }

    public function testTokenIs32CharsInHexadecimal()
    {
        $this->assertRegExp("/^[\\da-f]{32}$/", $this->token->createToken());
    }
}
