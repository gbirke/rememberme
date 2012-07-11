<?php

/**
 * This abstract class is for storing the credential/token/persistentToken triplets 
 * 
 * IMPORTANT SECURITY NOTICE: The storage should not store the token values in the clear.
 * Always use a secure hash function!
 */ 
abstract class Rememberme_Storage_Base {
  
  const TRIPLET_FOUND     =  1,
        TRIPLET_NOT_FOUND =  0,
        TRIPLET_INVALID   = -1;

  /**
   * Return Tri-state value constant
   *
   * @param mixed $credential Unique credential (user id, email address, user name)
   * @param string $token One-Time Token
   * @param string $persistentToken Persistent Token
   * @return int
   */
  public abstract function findTriplet($credential, $token, $persistentToken);
  
  /**
   * Store the new token for the credential and the persistent token.
   * Create a new storage entry, if the combination of credential and persistent
   * token does not exist.
   *
   * @param mixed $credential
   * @param string $token
   * @param string $persistentToken
   * @param int $expire Timestamp when this triplet will expire (0=no expiry)
   */
  public abstract function storeTriplet($credential, $token, $persistentToken, $expire=0);


  /**
   * Remove one triplet of the user from the store
   *
   * @abstract
   * @param mixed $credential
   * @param string $persistentToken
   * @return void
   */
  public abstract function cleanTriplet($credential, $persistentToken);

  /**
   * Remove all triplets of a user, effectively logging him out on all machines
   *
   * @abstract
   * @param $credential
   * @return void
   */
  public abstract function cleanAllTriplets($credential);

}
