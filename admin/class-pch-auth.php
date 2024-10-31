<?php
/**
 * @package ProChains\Class
 * @since 2.1.3
 */

defined( 'ABSPATH' ) || exit;

class PCH_Auth {
  public function _cryptoJsAesEncrypt($passphrase, $value){
    $salt = openssl_random_pseudo_bytes(8);
    $salted = '';
    $dx = '';
    while (strlen($salted) < 48) {
        $dx = md5($dx.$passphrase.$salt, true);
        $salted .= $dx;
    }
    $key = substr($salted, 0, 32);
    $iv  = substr($salted, 32,16);
    $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
    $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
    return json_encode($data);
  }
  
  public function _cryptoJsAesDecrypt($passphrase, $jsonString){
    $jsondata = json_decode($jsonString, true);
    $salt = hex2bin($jsondata["s"]);
    $ct = base64_decode($jsondata["ct"]);
    $iv  = hex2bin($jsondata["iv"]);
    $concatedPassphrase = $passphrase.$salt;
    $md5 = array();
    $md5[0] = md5($concatedPassphrase, true);
    $result = $md5[0];
    for ($i = 1; $i < 3; $i++) {
        $md5[$i] = md5($md5[$i - 1].$concatedPassphrase, true);
        $result .= $md5[$i];
    }
    $key = substr($result, 0, 32);
    $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
    return json_decode($data, true);
  }

  public function _generate_token() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $key = "";
    $length = 8;

    $current_time = date('YmdHis', current_time('timestamp', 0));
    $time = date('YmdHis', strtotime($current_time . ' +15 minutes'));
    
    for ($i = 0; $i < $length; $i++) {
      $index = rand(0, strlen($characters) - 1);
      $key .= $characters[$index];
    }

    $result = "";
    $arr_key = str_split($key);
    $arr_time = str_split($time);
    $total_key = $length + strlen($time);
    $position_key = array(2,5,6,8,11,15,17,20);
    
    $j = 0;
    $k = 0;
    for($i=0; $i<$total_key; $i++) {
      if(in_array($i, $position_key) !== false) {
        $result .= $arr_key[$j];
        $j++;
      } else {
        $result .= $arr_time[$k];
        $k++;
      }
    }

    $arr_result = array_reverse(str_split($result));
    $result = join('', $arr_result);
  
    return (new self)->_cryptoJsAesEncrypt('_auth_prochains4wp', $result);
  }

  public function _check_token($token) {
    $token = (new self)->_cryptoJsAesDecrypt('_auth_prochains4wp', $token);
    $arr_token = array_reverse(str_split($token));
    $expiry_token = "";
    $position_key = array(2,5,6,8,11,15,17,20);

    for($i=0; $i<count($arr_token); $i++) {
      if(in_array($i, $position_key) === false) {
        $expiry_token .= $arr_token[$i];
      }
    }

    $current_time = date('YmdHis', current_time('timestamp', 0));
    
    if($expiry_token >= $current_time) {
      return true;
    } else {
      return false;
    }
  }

  public static function generate_token() {
    return (new self)->_generate_token();
  }

  public static function check_token() {
    $token = get_option('_auth_prochains4wp');
    
    return (new self)->_check_token($token);
  }
}