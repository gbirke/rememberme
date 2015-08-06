<?php

// Mock PHP function
namespace Birke\Rememberme\Cookie {
    function setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly)
    {
        return true;
    }
}

namespace {

    class PHPCookieTest extends PHPUnit_Framework_TestCase
    {
        public function testDefaultValues()
        {
            $cookie = new \Birke\Rememberme\Cookie\PHPCookie();

            $this->assertEquals('', $cookie->getPath());
            $this->assertEquals('', $cookie->getDomain());
            $this->assertFalse($cookie->getSecure());
            $this->assertTrue($cookie->getHttpOnly());
        }

        public function testSetters()
        {
            $cookie = new \Birke\Rememberme\Cookie\PHPCookie();

            $cookie->setName("SECURE_REMEMBER");
            $this->assertEquals("SECURE_REMEMBER", $cookie->getName());

            $cookie->setPath('/test');
            $this->assertEquals('/test', $cookie->getPath());

            $cookie->setDomain('www.foo.com');
            $this->assertEquals('www.foo.com', $cookie->getDomain());

            $cookie->setSecure(true);
            $this->assertTrue($cookie->getSecure());

            $cookie->setHttpOnly(false);
            $this->assertFalse($cookie->getHttpOnly());
        }

        public function testSetValueSetsSuperglobal()
        {
            unset($_COOKIE["SET_TEST_1"]);
            $cookie = new \Birke\Rememberme\Cookie\PHPCookie("SET_TEST_1");
            $cookie->setValue("testvalue");
            $this->assertEquals("testvalue", $_COOKIE["SET_TEST_1"]);
        }

        public function testGetValueReturnsSuperglobal(){
            $_COOKIE["GET_TEST_1"] = "testvalue";
            $cookie = new \Birke\Rememberme\Cookie\PHPCookie("GET_TEST_1");
            $this->assertEquals("testvalue", $cookie->getValue());
        }
    }
}