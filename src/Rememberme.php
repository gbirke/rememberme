<?php

class Rememberme {
  
  protected $cookieName = "PHP_REMEMBERME";
  
  /**
   * @ var Rememberme_Cookie
   */
  protected $cookie;
  
  /**
   * @var Rememberme_Storage_Base
   */
  protected $storage;

  /**
   * @var int Number of seconds in the future the cookie and storage will expire
   */
  protected $expireTime =  604800; // 1 week

  /**
   * If the return from the storage was Rememberme_Storage_Base::TRIPLET_INVALID,
   * this is set to true
   *
   * @var bool
   */
  protected $lastLoginTokenWasInvalid = false;

  protected $cleanStoredTokensOnInvalidResult = true;

  protected $salt = "";
  
  public function __construct(Rememberme_Storage_Base $storage) {
    $this->storage = $storage;
    $this->cookie = new Rememberme_Cookie();
  }

  /**
   * Check Credentials from cookie
   * @param  $credential
   * @return bool
   */
  public function login($credential) {

    $cookieValues = $this->getCookieValues();
    if(!$cookieValues) {
      return false;
    }
    $loginWasSuccessful = false;
    switch($this->storage->findTriplet($cookieValues[0], $cookieValues[1].$this->salt, $cookieValues[2].$this->salt)) {
      case Rememberme_Storage_Base::TRIPLET_FOUND:
        $expire = time() + $this->expireTime;
        $newToken = $this->createToken();
        $this->storage->storeTriplet($credential, $newToken.$this->salt, $cookieValues[2].$this->salt, $expire);
        $this->cookie->setCookie($this->cookieName, implode("|", array($credential,$newToken, $cookieValues[2])), $expire);
        $loginWasSuccessful = true; 
        break;
      case Rememberme_Storage_Base::TRIPLET_INVALID:
        $this->cookie->setCookie($this->cookieName, "", time() - $this->expireTime);
        $this->lastLoginTokenWasInvalid = true;
        if($this->cleanStoredTokensOnInvalidResult) {
          $this->storage->cleanAllTriplets($credential);
        }
        break;
    }
    return $loginWasSuccessful;
  }

  public function cookieIsValid($credential) {
    $cookieValues = $this->getCookieValues();
    if(!$cookieValues) {
      return false;
    }
    $state = $this->storage->findTriplet($cookieValues[0], $cookieValues[1].$this->salt, $cookieValues[2].$this->salt);
    return $state == Rememberme_Storage_Base::TRIPLET_FOUND;
  }

  public function createCookie($credential) {
    $newToken = $this->createToken();
    $newPersistentToken = $this->createToken();
    $expire = time() + $this->expireTime;
    $this->storage->storeTriplet($credential, $newToken.$this->salt, $newPersistentToken.$this->salt, $expire);
    $this->cookie->setCookie($this->cookieName, implode("|", array($credential,$newToken, $newPersistentToken)), $expire);
    return $this;
  }

  public function clearCookie($credential) {
    if(empty($_COOKIE[$this->cookieName]))
      return false;
    $cookieValues = explode("|", $_COOKIE[$this->cookieName], 3);
    $this->cookie->setCookie($this->cookieName, "", time() - $this->expireTime);

    if(count($cookieValues) < 3) {
      return false;
    }
    $this->storage->cleanTriplet($credential, $cookieValues[2].$this->salt);
    return true;
  }
  
  public function getCookieName() {
    return $this->cookieName;
  }

  public function setCookieName($name) {
    $this->cookieName = $name;
    return $this;
  }
  
  public function setCookie(Rememberme_Cookie $cookie) {
    $this->cookie = $cookie;
    return $this;
  }

  public function loginTokenWasInvalid() {
    return $this->lastLoginTokenWasInvalid;
  }

  public function getCookie() {
    return $this->cookie;
  }

  public function setCleanStoredTokensOnInvalidResult($state) {
    $this->cleanStoredTokensOnInvalidResult = $state;
    return $this;
  }

  public function getCleanStoredTokensOnInvalidResult($state) {
    return $this->cleanStoredTokensOnInvalidResult;
  }

  /**
   * Create a pseudo-random token.
   *
   * The token is pseudo-random. If you need better security, read from /dev/urandom
   */
  protected function createToken() {
      return md5(uniqid(mt_rand(), true));
  }

  protected function getCookieValues() {
    // Cookie was not sent with incoming request
    if(empty($_COOKIE[$this->cookieName])) {
      return array();
    }
    $cookieValues = explode("|", $_COOKIE[$this->cookieName], 3);

    if(count($cookieValues) < 3) {
      return array();
    }
    
    return $cookieValues;
  }

  /**
   * Return how many seconds in the future that the cookie will expire
   * @return int
   */
  public function getExpireTime() {
    return $this->expireTime;
  }

  /**
   * @param int $expireTime How many seconds in the future the cookie will expire
   *
   * Default is 604800 (1 week)
   *
   * @return Rememberme
   */
  public function setExpireTime($expireTime) {
    $this->expireTime = $expireTime;
    return $this;
  }

  /**
   *
   * @return string
   */
  public function getSalt() {
    return $this->salt;
  }

  /**
   * The salt is additional information that is added to the tokens to make
   * them more unqiue and secure. The salt is not stored in the cookie and
   * should not saved in the storage.
   *
   * For example, to bind a token to an IP address use $_SERVER['REMOTE_ADDR'].
   * To bind a token to the browser (user agent), use $_SERVER['HTTP_USER_AGENT].
   * You could also use a long random string that is uniqe to your application.
   * @param string $salt
   */
  public function setSalt($salt) {
    $this->salt = $salt;
  }

}


