<?php
/* 	
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/01/04 13:03:09 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: AE_cls_crypt.php,v $
 		Revision 1.1  2017/01/04 13:03:09  cvs
 		call 5542, uitrol WWB en TGC
 		

 		 		
 	
*/
class AE_crypt
{
  var $iv_len = 16;
  var $key;
	
  function AE_crypt() 
  {
    global $USR;
	  $this->user = $USR;
    
    $this->key = "abcd3";
  }
	
  function setKey($key)
  {
    $this->key = $key;
  }

  
  function get_rnd_iv()
  {
    $tel = $this->iv_len;
    $iv = '';
    while ($tel-- > 0) 
    {
      $iv .= chr(mt_rand() & 0xff);
    }
    return $iv;
  }

  function md5_encrypt($plain_text)
  {
    $password = $this->key ;
    $iv_len = $this->iv_len;    
    $plain_text .= "\x13";
    $n = strlen($plain_text);
    if ($n % 16) $plain_text .= str_repeat("\0", 16 - ($n % 16));
    $i = 0;
    $enc_text = $this->get_rnd_iv($iv_len);
    $iv = substr($password ^ $enc_text, 0, 512);
    while ($i < $n) 
    {
      $block = substr($plain_text, $i, 16) ^ pack('H*', md5($iv));
      $enc_text .= $block;
      $iv = substr($block . $iv, 0, 512) ^ $password;
      $i += 16;
    }
    return base64_encode($enc_text);
  }

  function md5_decrypt($enc_text)
  {
    $password = $this->key ;
    $iv_len = $this->iv_len;    
    $enc_text = base64_decode($enc_text);
    $n = strlen($enc_text);
    $i = $iv_len;
    $plain_text = '';
    $iv = substr($password ^ substr($enc_text, 0, $iv_len), 0, 512);
    while ($i < $n) 
    {
      $block = substr($enc_text, $i, 16);
      $plain_text .= $block ^ pack('H*', md5($iv));
      $iv = substr($block . $iv, 0, 512) ^ $password;
      $i += 16;
    }
    return preg_replace('/\\x13\\x00*$/', '', $plain_text);
  }
  
  
}




?>