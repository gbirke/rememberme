<?php

use Birke\Rememberme\Token\DefaultToken;

class DefaultTokenTest extends PHPUnit_Framework_TestCase {

    public function testDefaultTokenReturns32CharsInHexadecimal(){
        $token = new DefaultToken();
        $this->assertRegExp("/^[\\da-f]{32}$/", $token->createToken());
    }

    public function testTokenLengthDoublesWhenUsingHexFormat(){
        $token = new DefaultToken(32);
        $this->assertRegExp("/^[\\da-f]{64}$/", $token->createToken());
    }

    public function testTokenLengthIncreasesWhenUsingBase64Format(){
        $token = new DefaultToken(32, DefaultToken::FORMAT_BASE64);
        $this->assertRegExp("/^[\\da-zA-Z=+\\/]{44}$/", $token->createToken());
    }

    public function testTokenLengthIsExactWhenUsingPlainFormat(){
        $token = new DefaultToken(32, DefaultToken::FORMAT_PLAIN);
        $this->assertEquals(32, strlen($token->createToken()));
    }
}
