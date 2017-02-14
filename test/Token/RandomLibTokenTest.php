<?php

use Birke\Rememberme\Token\RandomLibToken;

class RandomLibTokenTest extends PHPUnit_Framework_TestCase {

    protected function setUp()
    {
        if (!class_exists('RandomLib\Factory')) {
            $this->markTestSkipped(
                'The RandomLib library is not available.'
            );
        }

        // TODO: Remove this check when RandomLib works with PHP >= 7.1 again
        if (version_compare(PHP_VERSION, '7.1', '>=')) {
            $this->markTestSkipped(
                'RandomLib library is not compatible with PHP PHP >= 7.1.'
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
