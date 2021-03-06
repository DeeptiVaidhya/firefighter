<?php 
 /*

 * Copyright (c) 2003-2017 BrightOutcome Inc.  All rights reserved.
 * 
 * This software is the confidential and proprietary information of
 * BrightOutcome Inc. ("Confidential Information").  You shall not
 * disclose such Confidential Information and shall use it only
 * in accordance with the terms of the license agreement you
 * entered into with BrightOutcome.
 * 
 * BRIGHTOUTCOME MAKES NO REPRESENTATIONS OR WARRANTIES ABOUT THE
 * SUITABILITY OF THE SOFTWARE, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT 
 * NOT LIMITED TO THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
 * PARTICULAR PURPOSE, OR NON-INFRINGEMENT. BRIGHTOUTCOME SHALL NOT BE LIABLE
 * FOR ANY DAMAGES SUFFERED BY LICENSEE AS A RESULT OF USING, MODIFYING OR
 * DISTRIBUTING THIS SOFTWARE OR ITS DERIVATIVES.
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Bcrypt {
  private $rounds;
  private $salt_prefix;

  /**
   * Bcrypt constructor.
   *
   * @param array $params
   * @throws Exception
   */
  public function __construct($params=array('rounds'=>7, 'salt_prefix'=>'$2y$')) {

    if(CRYPT_BLOWFISH != 1) {
      throw new Exception("bcrypt not supported in this installation. See http://php.net/crypt");
    }

    $this->rounds = $params['rounds'];
    $this->salt_prefix = $params['salt_prefix'];
  }

  public function hash($input) {
    $hash = crypt($input, $this->getSalt());

    if(strlen($hash) > 13) {
      return $hash;
    }

    return false;
  }

  /**
   * @param $input
   * @param $existingHash
   * @return bool
     */
  public function verify($input, $existingHash) {
    $hash = crypt($input, $existingHash);
    return $this->hashEquals($existingHash, $hash);
  }
  
   /**
   * Polyfill for hash_equals()
   * Code mainly taken from hash_equals() compat function of CodeIgniter 3
   *
   * @param  string  $known_string
   * @param  string  $user_string
   * @return  bool
   */
  private function hashEquals($known_string, $user_string)
  {
    // For CI3 or PHP >= 5.6
    if (function_exists('hash_equals')) 
    {
      return hash_equals($known_string, $user_string);
    }
    
    // For CI2 with PHP < 5.6
    // Code from CI3 https://github.com/bcit-ci/CodeIgniter/blob/develop/system/core/compat/hash.php
    if ( ! is_string($known_string))
    {
      trigger_error('hash_equals(): Expected known_string to be a string, '.strtolower(gettype($known_string)).' given', E_USER_WARNING);
      return FALSE;
    }
    elseif ( ! is_string($user_string))
    {
      trigger_error('hash_equals(): Expected user_string to be a string, '.strtolower(gettype($user_string)).' given', E_USER_WARNING);
      return FALSE;
    }
    elseif (($length = strlen($known_string)) !== strlen($user_string))
    {
      return FALSE;
    }

    $diff = 0;
    for ($i = 0; $i < $length; $i++)
    {
      $diff |= ord($known_string[$i]) ^ ord($user_string[$i]);
    }

    return ($diff === 0);
  }

  private function getSalt() {
    $salt = sprintf($this->salt_prefix.'%02d$', $this->rounds);

    $bytes = $this->getRandomBytes(16);

    $salt .= $this->encodeBytes($bytes);

    return $salt;
  }

  private $randomState;


  /**
   * @param $count
   * @return string
     */
  private function getRandomBytes($count) {
    $bytes = '';

    if(function_exists('openssl_random_pseudo_bytes') &&
        (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')) { // OpenSSL slow on Win
      $bytes = openssl_random_pseudo_bytes($count);
    }

    if($bytes === '' && @is_readable('/dev/urandom') &&
       ($hRand = @fopen('/dev/urandom', 'rb')) !== FALSE) {
      $bytes = fread($hRand, $count);
      fclose($hRand);
    }

    if(strlen($bytes) < $count) {
      $bytes = '';

      if($this->randomState === null) {
        $this->randomState = microtime();
        if(function_exists('getmypid')) {
          $this->randomState .= getmypid();
        }
      }

      for($i = 0; $i < $count; $i += 16) {
        $this->randomState = md5(microtime() . $this->randomState);

        if (PHP_VERSION >= '5') {
          $bytes .= md5($this->randomState, true);
        } else {
          $bytes .= pack('H*', md5($this->randomState));
        }
      }

      $bytes = substr($bytes, 0, $count);
    }

    return $bytes;
  }

  /**
   * @param $input
   * @return string
     */
  private function encodeBytes($input) {
    // The following is code from the PHP Password Hashing Framework
    $itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    $output = '';
    $i = 0;
    do {
      $c1 = ord($input[$i++]);
      $output .= $itoa64[$c1 >> 2];
      $c1 = ($c1 & 0x03) << 4;
      if ($i >= 16) {
        $output .= $itoa64[$c1];
        break;
      }

      $c2 = ord($input[$i++]);
      $c1 |= $c2 >> 4;
      $output .= $itoa64[$c1];
      $c1 = ($c2 & 0x0f) << 2;

      $c2 = ord($input[$i++]);
      $c1 |= $c2 >> 6;
      $output .= $itoa64[$c1];
      $output .= $itoa64[$c2 & 0x3f];
    } while (1);

    return $output;
  }
}


/***** End of BCrypt.php ***********/
