<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PDO
 *
 * @author birke
 */
class Rememberme_Storage_PDO extends Rememberme_Storage_DB {
    
  /**
   *
   * @var PDO
   */
  protected $connection;

  public function findTriplet($credential, $token, $persistentToken) {
    // We don't store the binary sha1 values because otherwise we could not use
    // proper XML test data
    $sql = "SELECT IF(SHA1(?) = {$this->tokenColumn}, 1, -1) AS token_match " .
           "FROM {$this->tableName} WHERE {$this->credentialColumn} = ? " .
           "AND {$this->persistentTokenColumn} = SHA1(?) LIMIT 1 ";
    $query = $this->connection->prepare($sql);
    $query->execute(array($token, $credential, $persistentToken));
    $result = $query->fetchColumn();
            
    if(!$result) {
      return self::TRIPLET_NOT_FOUND;
    }
    elseif ($result == 1) {
      return self::TRIPLET_FOUND;
    }
    else {
      return self::TRIPLET_INVALID;
    }
  }

  public function storeTriplet($credential, $token, $persistentToken, $expire=0) {
    $sql = "INSERT INTO {$this->tableName}({$this->credentialColumn}, " .
           "{$this->tokenColumn}, {$this->persistentTokenColumn}, " .
           "{$this->expiresColumn}) VALUES(?, SHA1(?), SHA1(?), ?)";
    $query = $this->connection->prepare($sql);
    $query->execute(array($credential, $token, $persistentToken, date("Y-m-d H:i:s", $expire)));
  }

  public function cleanTriplet($credential, $persistentToken) {
    $sql = "DELETE FROM {$this->tableName} WHERE {$this->credentialColumn} = ? " .
           " AND {$this->persistentTokenColumn} = SHA1(?)";
    $query = $this->connection->prepare($sql);
    $query->execute(array($credential, $persistentToken));
  }

  public function cleanAllTriplets($credential) {
    $sql = "DELETE FROM {$this->tableName} WHERE {$this->credentialColumn} = ? ";
    $query = $this->connection->prepare($sql);
    $query->execute(array($credential));
  }

  public function getConnection() {
    return $this->connection;
  }

  public function setConnection(PDO $connection) {
    $this->connection = $connection;
  }



}
?>
