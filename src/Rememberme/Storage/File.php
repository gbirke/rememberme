<?php
/**
 *
 *
 */

namespace Birke\Rememberme\Storage;
/**
 * File-Based Storage
 */
class File implements StorageInterface {

  protected $path="";

  protected $suffix = ".txt";

  public function __construct($path="", $suffix = ".txt") {
    $this->path = $path;
    $this->suffix = $suffix;
  }

  public function findTriplet($credential, $token, $persistentToken) {
    // Hash the tokens, because they can contain a salt and can be accessed in the file system
    $persistentToken = sha1($persistentToken);
    $token = sha1($token);
    $fn = $this->getFilename($credential, $persistentToken);
    if(!file_exists($fn)) {
      return self::TRIPLET_NOT_FOUND;
    }
    $fileToken = trim(file_get_contents($fn));
    if($fileToken == $token) {
      return self::TRIPLET_FOUND;
    }
    else {
      return self::TRIPLET_INVALID;
    }
  }

  public function storeTriplet($credential, $token, $persistentToken, $expire=0) {
    // Hash the tokens, because they can contain a salt and can be accessed in the file system
    $persistentToken = sha1($persistentToken);
    $token = sha1($token);
    $fn = $this->getFilename($credential, $persistentToken);
    file_put_contents($fn, $token);
    return $this;
  }

  public function cleanTriplet($credential, $persistentToken) {
    $persistentToken = sha1($persistentToken);
    $fn = $this->getFilename($credential, $persistentToken);
    if(file_exists($fn)) {
      unlink($fn);
    }
  }

  public function cleanAllTriplets($credential) {
    foreach(glob($this->path . DIRECTORY_SEPARATOR . $credential . ".*"  . $this->suffix) as $file) {
      unlink($file);
    }
  }

  protected function getFilename($credential, $persistentToken) {
    return $this->path . DIRECTORY_SEPARATOR . $credential . "." . $persistentToken . $this->suffix;
  }


}
