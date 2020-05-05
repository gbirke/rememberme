<?php

/**
 * @license MIT
 */

use Birke\Rememberme\Token\RandomLibToken;
use PHPUnit\Framework\TestCase;

class RandomLibTokenTest extends TestCase {

    protected function setUp(): void
    {
        if (!class_exists('RandomLib\Factory')) {
            $this->markTestSkipped(
                'The RandomLib library is not available.'
            );
        }
    }

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
