<?php

use Birke\Rememberme\Cookie\CookieInterface;
use Birke\Rememberme\Storage\StorageInterface;

class RemembermeTest extends PHPUnit_Framework_TestCase
{
  /**
   * @var Birke\Rememberme\Authenticator
   */
  protected $rememberme;

  /**
   * Default user id, used as credential information to check
   */
  protected $userid = 1;

  protected $validToken = "78b1e6d775cec5260001af137a79dbd5";

  protected $validPersistentToken = "0e0530c1430da76495955eb06eb99d95";

  protected $invalidToken = "7ae7c7caa0c7b880cb247bb281d527de";

  protected $cookie;

  protected $storage;

  function setUp() {
    $this->storage = $this->getMockBuilder(StorageInterface::class)->getMock();
    $this->rememberme = new Birke\Rememberme\Authenticator($this->storage);

    $this->cookie = $this->getMockBuilder(CookieInterface::class)
	    ->setMethods(array("setValue", "getValue", "deleteCookie"))
	    ->getMock();

    $this->rememberme->setCookie($this->cookie);

    $_COOKIE = array();
  }

  /* Basic cases */

  public function testNoCookieExists()
  {
      $this->assertFalse($this->rememberme->login()->isSuccess());
      $this->assertFalse($this->rememberme->login()->cookieExists());
  }

  public function testReturnFalseIfCookieIsInvalid()
  {
      $this->cookie->method("getValue")->willReturn("DUMMY");
      $this->assertFalse($this->rememberme->login()->isSuccess());
      $this->assertTrue($this->rememberme->login()->hasPossibleManipulation());
      $this->cookie->method("getValue")->willReturn($this->userid."|a");
      $this->assertFalse($this->rememberme->login()->isSuccess());
      $this->assertTrue($this->rememberme->login()->hasPossibleManipulation());
  }

  public function testLoginTriesToFindTripletWithValuesFromCookie() {
      $this->cookie->method("getValue")->willReturn( implode("|", array(
          $this->userid,
          $this->validToken,
          $this->validPersistentToken
    ))) ;
    $this->storage->expects($this->once())
      ->method("findTriplet")
      ->with($this->equalTo($this->userid), $this->equalTo($this->validToken), $this->equalTo($this->validPersistentToken));
    $this->rememberme->login();
  }

  /* Success cases */

  public function testSuccessIsTrueIfTripletIsFound() {
      $this->cookie->method("getValue")->willReturn( implode("|", array(
          $this->userid,
          $this->validToken,
          $this->validPersistentToken
      )));

    $this->storage->expects($this->once())
      ->method("findTriplet")
      ->will($this->returnValue(Birke\Rememberme\Storage\StorageInterface::TRIPLET_FOUND));
    $this->assertTrue($this->rememberme->login()->isSuccess());
  }

  public function testCredentialsAreInResultIfTripletIsFound() {
        $this->cookie->method("getValue")->willReturn( implode("|", array(
            $this->userid,
            $this->validToken,
            $this->validPersistentToken
        )));

        $this->storage->expects($this->once())
            ->method("findTriplet")
            ->will($this->returnValue(Birke\Rememberme\Storage\StorageInterface::TRIPLET_FOUND));
        $this->assertEquals($this->userid, $this->rememberme->login()->getCredential());
   }

  public function testStoreNewTripletInCookieIfTripletIsFound() {
    $oldcookieValue = implode("|", array(
      $this->userid, $this->validToken, $this->validPersistentToken));
    $this->cookie->method("getValue")->willReturn($oldcookieValue);
    $this->storage->expects($this->once())
      ->method("findTriplet")
      ->will($this->returnValue(Birke\Rememberme\Storage\StorageInterface::TRIPLET_FOUND));
    $this->cookie->expects($this->once())
      ->method("setValue")
      ->with(
        $this->logicalAnd(
          $this->matchesRegularExpression('/^'.$this->userid.'\|[a-f0-9]{32,}\|'.$this->validPersistentToken.'$/'),
          $this->logicalNot($this->equalTo($oldcookieValue))
        )
      );
    $this->rememberme->login();
  }

  public function testReplaceTripletInStorageIfTripletIsFound() {
      $this->cookie->method("getValue")->willReturn( implode("|", array(
          $this->userid,
          $this->validToken,
          $this->validPersistentToken
    )));
    $this->storage->expects($this->once())
      ->method("findTriplet")
      ->will($this->returnValue(Birke\Rememberme\Storage\StorageInterface::TRIPLET_FOUND));
    $this->storage->expects($this->once())
      ->method("replaceTriplet")
      ->with(
        $this->equalTo($this->userid),
        $this->logicalAnd(
          $this->matchesRegularExpression('/^[a-f0-9]{32,}$/'),
          $this->logicalNot($this->equalTo($this->validToken))
        ),
        $this->equalTo($this->validPersistentToken)
        );
    $this->rememberme->login();
  }

  public function testCookieContainsUserIDAndHexTokensIfTripletIsFound()
  {
      $this->cookie->method("getValue")->willReturn( implode("|", array(
          $this->userid,
          $this->validToken,
          $this->validPersistentToken
      )));
    $this->storage->expects($this->once())
      ->method("findTriplet")
      ->will($this->returnValue(Birke\Rememberme\Storage\StorageInterface::TRIPLET_FOUND));
    $this->cookie->expects($this->once())
      ->method("setValue")
      ->with(
          $this->matchesRegularExpression('/^'.$this->userid.'\|[a-f0-9]{32,}\|[a-f0-9]{32,}$/')
        );
    $this->rememberme->login();
  }

  public function testCookieContainsNewTokenIfTripletIsFound()
  {
      $oldcookieValue = implode("|", array(
        $this->userid, $this->validToken, $this->validPersistentToken));
      $this->cookie->method("getValue")->willReturn( $oldcookieValue );
    $this->storage->expects($this->once())
      ->method("findTriplet")
      ->will($this->returnValue(Birke\Rememberme\Storage\StorageInterface::TRIPLET_FOUND));
    $this->cookie->expects($this->once())
      ->method("setValue")
      ->with(
          $this->logicalAnd(
            $this->matchesRegularExpression('/^'.$this->userid.'\|[a-f0-9]{32,}\|'.$this->validPersistentToken.'$/'),
            $this->logicalNot($this->equalTo($oldcookieValue))
          )
        );
    $this->rememberme->login();
  }

  /* Failure Cases */

  public function testResultIndicatesExpiredWhenTripletIsNotFound() {
      $this->cookie->method("getValue")->willReturn( implode("|", array(
      $this->userid, $this->validToken, $this->validPersistentToken)));
      $this->storage->expects($this->once())
          ->method("findTriplet")
          ->will($this->returnValue(Birke\Rememberme\Storage\StorageInterface::TRIPLET_NOT_FOUND));

      $result = $this->rememberme->login();

      $this->assertFalse($result->isSuccess());
      $this->assertTrue($result->isExpired());
  }

  public function testResultIndicatesManipulationIfTripletIsInvalid() {
      $this->cookie->method("getValue")->willReturn( implode("|", array(
      $this->userid, $this->invalidToken, $this->validPersistentToken)));
      $this->storage->expects($this->once())
          ->method("findTriplet")
          ->will($this->returnValue(Birke\Rememberme\Storage\StorageInterface::TRIPLET_INVALID));

      $result = $this->rememberme->login();

      $this->assertFalse($result->isSuccess());
      $this->assertTrue($result->hasPossibleManipulation());
  }

  public function testCookieIsExpiredIfTripletIsInvalid() {
      $this->cookie->method("getValue")->willReturn( implode("|", array(
      $this->userid, $this->invalidToken, $this->validPersistentToken)));
      $this->storage->expects($this->once())
          ->method("findTriplet")
          ->will($this->returnValue(Birke\Rememberme\Storage\StorageInterface::TRIPLET_INVALID));
      $this->cookie->expects($this->once())
          ->method("deleteCookie");
      $this->rememberme->login();
  }

  public function testAllStoredTokensAreClearedIfTripletIsInvalid() {
      $this->cookie->method("getValue")->willReturn( implode("|", array(
      $this->userid, $this->invalidToken, $this->validPersistentToken)));
    $this->storage->expects($this->any())
      ->method("findTriplet")
      ->will($this->returnValue(Birke\Rememberme\Storage\StorageInterface::TRIPLET_INVALID));
    $this->storage->expects($this->once())
      ->method("cleanAllTriplets")
      ->with($this->equalTo($this->userid));
    $this->rememberme->setCleanStoredTokensOnInvalidResult(true);
    $this->rememberme->login();
    $this->rememberme->setCleanStoredTokensOnInvalidResult(false);
    $this->rememberme->login();
  }

  /* Salting test */

  public function testSaltIsAddedToTokensOnLogin() {
    $salt = "Mozilla Firefox 4.0";
      $this->cookie->method("getValue")->willReturn( implode("|", array(
      $this->userid, $this->validToken, $this->validPersistentToken)));
    $this->storage->expects($this->once())
      ->method("findTriplet")
      ->with($this->equalTo($this->userid), $this->equalTo($this->validToken.$salt), $this->equalTo($this->validPersistentToken.$salt))
      ->will($this->returnValue(Birke\Rememberme\Storage\StorageInterface::TRIPLET_FOUND));
    $this->storage->expects($this->once())
      ->method("replaceTriplet")
      ->with(
        $this->equalTo($this->userid),
        $this->matchesRegularExpression('/^[a-f0-9]{32,}'.preg_quote($salt)."$/"),
        $this->equalTo($this->validPersistentToken.$salt)
    );
    $this->rememberme->setSalt($salt);
    $this->rememberme->login();
  }

  public function testSaltIsAddedToTokensOnCreateCookie() {
    $salt = "Mozilla Firefox 4.0";
    $testExpr = '/^[a-f0-9]{32,}'.preg_quote($salt).'$/';
    $this->storage->expects($this->once())
      ->method("storeTriplet")
      ->with(
        $this->equalTo($this->userid),
        $this->matchesRegularExpression($testExpr),
        $this->matchesRegularExpression($testExpr)
    );
    $this->rememberme->setSalt($salt);
    $this->rememberme->createCookie($this->userid);
  }

  public function testSaltIsAddedToTokensOnClearCookie() {
    $salt = "Mozilla Firefox 4.0";
      $this->cookie->method("getValue")->willReturn( implode("|", array(
      $this->userid, $this->validToken, $this->validPersistentToken)));
    $this->storage->expects($this->once())
      ->method("cleanTriplet")
      ->with(
        $this->equalTo($this->userid),
        $this->equalTo($this->validPersistentToken.$salt)
    );
    $this->rememberme->setSalt($salt);
    $this->rememberme->clearCookie(true);
  }

  /* Other functions */

  public function testCreateCookieCreatesCookieAndStoresTriplets() {
    $now = time();
    $this->cookie->expects($this->once())
      ->method("setValue")
      ->with(
        $this->matchesRegularExpression('/^'.$this->userid.'\|[a-f0-9]{32,}\|[a-f0-9]{32,}$/')
      );
    $testExpr = '/^[a-f0-9]{32,}$/';
    $this->storage->expects($this->once())
      ->method("storeTriplet")
      ->with(
        $this->equalTo($this->userid),
        $this->matchesRegularExpression($testExpr),
        $this->matchesRegularExpression($testExpr)
      );
    $this->rememberme->createCookie($this->userid);
  }

  public function testClearCookieExpiresCookieAndDeletesTriplet() {
      $this->cookie->method("getValue")->willReturn( implode("|", array(
      $this->userid, $this->validToken, $this->validPersistentToken)));
    $now = time();
    $this->cookie->expects($this->once())
      ->method("deleteCookie");
    $this->storage->expects($this->once())
      ->method("cleanTriplet")
      ->with(
        $this->equalTo($this->userid),
        $this->equalTo($this->validPersistentToken)
      );
    $this->rememberme->clearCookie(true);
  }
}
