<?php


namespace Birke\Rememberme\Cookie;

/**
 * Classes with this interface are responsible for interacting with the PHP cookie infrastructure
 *
 * @package Birke\Cookie
 */
interface CookieInterface
{

    /**
     * Get the value from the cookie
     * @return string
     */
    public function getValue();

    /**
     * Set the value of the cookie.
     *
     * It is strongly recommended that implementations of this interface extend
     * the expiration date of the cookie whenever a value is set.
     * @param string $value
     */
    public function setValue($value);

    /**
     * Delete the cookie from the users browser during this request
     * and remove all values store din this cookie.
     */
    public function deleteCookie();
}
