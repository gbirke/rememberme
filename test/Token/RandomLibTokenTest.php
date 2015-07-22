<?php

use Birke\Rememberme\Token\RandomLibToken;

class RandomLibTokenTest extends PHPUnit_Framework_TestCase {

    public function testRandomLibTokenReturns32CharsInHexadecimal(){
        $token = new RandomLibToken();
        $this->assertRegExp("/^[\\da-f]{32}$/", $token->createToken());
    }

    public function testHexFormatReturnsRightCharacters(){
        $token = new RandomLibToken(32);
        $this->assertRegExp("/^[\\da-f]{32}$/", $token->createToken());
    }

    public function testBase64FormatReturnsRightCharacters(){
        $token = new RandomLibToken(32, RandomLibToken::FORMAT_BASE64);
        $this->assertRegExp("/^[\\da-zA-Z+=\\/]{32}$/", $token->createToken());
    }

    public function testPlainFormatReturnsBase64Characters(){
        $token = new RandomLibToken(32, RandomLibToken::FORMAT_PLAIN);
        $this->assertRegExp("/^[\\da-zA-Z+=\\/]{32}$/", $token->createToken());
    }
}
