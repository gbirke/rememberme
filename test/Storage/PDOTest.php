<?php
/* 
 */

use Birke\Rememberme\Storage\PDOStorage;
use Birke\Rememberme\Storage\StorageInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author birke
 */
class PDOTest extends TestCase {

  private const CREATE_DB = <<<CRDB
CREATE TABLE "tokens" (
	"credential"	TEXT NOT NULL,
	"token"	TEXT NOT NULL,
	"persistent_token"	INTEGER NOT NULL,
	"expires"	TEXT NOT NULL,
	PRIMARY KEY("credential","persistent_token","expires")
) WITHOUT ROWID
CRDB;


  /**
   *
   * @var PDO
   */
  protected $pdo;

  /**
   *
   * @var Birke\Rememberme\Storage\PDOStorage
   */
  protected $storage;

  protected $userid = 'test';
  protected $validToken = "78b1e6d775cec5260001af137a79dbd5";
  protected $validPersistentToken = "0e0530c1430da76495955eb06eb99d95";
  protected $invalidToken = "7ae7c7caa0c7b880cb247bb281d527de";

  // SHA1 hashes of the tokens
  protected $validDBToken = 'e0e6d29addce0fbdd0f845799be7d0395ed087c3';
  protected $validDBPersistentToken = 'd27d330764ef61e99adf5d16f90b95a2a63c209a';
  protected $invalidDBToken = 'ec15fbc40cdff6a2050a1bcbbc1b2196222f13f4';

  protected $expire = "2022-12-21 21:21:00";
  protected $expireTS = 1671657660;

  protected function setUp(): void {
    $this->pdo = new PDO('sqlite::memory:');
    $this->pdo->exec(self::CREATE_DB);
    $this->storage = new PDOStorage(array(
      'connection' => $this->pdo,
      'tableName' => 'tokens',
      'credentialColumn' => 'credential',
      'tokenColumn' => 'token',
      'persistentTokenColumn' => 'persistent_token',
      'expiresColumn' => 'expires'
    ));
  }

  public function testFindTripletReturnsFoundIfDataMatches() {
    $this->insertFixtures();
    $result = $this->storage->findTriplet($this->userid, $this->validToken, $this->validPersistentToken);
    $this->assertEquals(StorageInterface::TRIPLET_FOUND, $result);
  }

  public function testFindTripletReturnsNotFoundIfNoDataMatches() {
    $this->pdo->exec("TRUNCATE tokens");
    $result = $this->storage->findTriplet($this->userid, $this->validToken, $this->validPersistentToken);
    $this->assertEquals(StorageInterface::TRIPLET_NOT_FOUND, $result);
  }

  public function testFindTripletReturnsInvalidTokenIfTokenIsInvalid() {
    $this->insertFixtures();
    $result = $this->storage->findTriplet($this->userid, $this->invalidToken, $this->validPersistentToken);
    $this->assertEquals(StorageInterface::TRIPLET_INVALID, $result);
  }

  public function testStoreTripletSavesValuesIntoDatabase() {
    $this->storage->storeTriplet($this->userid, $this->validToken, $this->validPersistentToken, $this->expireTS);
    $result = $this->pdo->query("SELECT credential,token,persistent_token, expires FROM tokens");
    $row = $result->fetch(PDO::FETCH_NUM);
    $this->assertEquals(array($this->userid, $this->validDBToken, $this->validDBPersistentToken, $this->expire), $row);
    $this->assertFalse($result->fetch());
  }

  public function testCleanTripletRemovesEntryFromDatabase() {
    $this->insertFixtures();
    $this->storage->cleanTriplet($this->userid, $this->validPersistentToken);
    $this->assertEquals(0, $this->pdo->query("SELECT COUNT(*) FROM tokens")->fetchColumn());
  }

  public function testCleanAllTripletsRemovesAllEntriesWithMatchingCredentialsFromDatabase() {
    $this->insertFixtures();
    $this->pdo->exec("INSERT INTO tokens VALUES ('{$this->userid}', 'dummy', 'dummy', NOW())");
    $this->storage->cleanAllTriplets($this->userid);
    $this->assertEquals(0, $this->pdo->query("SELECT COUNT(*) FROM tokens")->fetchColumn());
  }

  private function insertFixtures()
  {
    $this->pdo->exec("INSERT INTO tokens (credential, token, persistent_token, expires) VALUES ('test', 'e0e6d29addce0fbdd0f845799be7d0395ed087c3', 'd27d330764ef61e99adf5d16f90b95a2a63c209a', '2035-12-21 21:21:00')");
  }

}
?>
