<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/08/31 07:47:17 $
 		File Versie					: $Revision: 1.4 $

 		$Log: dd_preview.php,v $
naar RVV 20210120

*/


include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");
include_once("../../classes/AE_cls_Email.php");

require("../../config/checkLoggedIn.php");
error_reporting(0);

if ($_GET["download"] > 0)
{
  if ( ! isset ($__appvar['office365']) ) {
    $mail = new AE_cls_Email();
  } else {
    $mail = new AE_cls_ExchangeOnline();
  }

  $m = $mail->getQueueMsgById($_GET["download"]);
  $file = "emailPreview_".$_GET["download"].".eml";
  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
  header("Content-type: application/force-download");
  header("Content-Transfer-Encoding: Binary");
  header("Content-length: ".strlen($m["rawMail"]));
  header("Content-disposition: attachment; filename=\"".$file."\"");
  echo $m["rawMail"];
  header('Connection: close');

}

/**
 * Parse mime message message part https://pl.wikipedia.org/wiki/Multipurpose_Internet_Mail_Extensions
 * @autor
 * Marcin Łukaszewski hello@breakermind.com
 */
class PhpMimeParser
{
  // Subject mime
  // '=?utf-8?B?'.base64_encode($subject).'?='
  // =?charset?encoding?encoded-text?=
  // =?utf-8?Q?hello?=
  // =?UTF-8?B?4pyIIEJvc3RvbiBhaXJmYXJlIGRlYWxzIC0gd2hpbGUgdGhleSBsYXN0IQ==?=
  var $mime;
  var $MultiParts;
  var $allMessage;
  // Content
  var $mHeader;
  var $mSubject;
  var $mHtml;
  var $mText;
  var $mFiles;
  var $mTo;
  var $mFrom;
  var $mCc;
  var $mBcc;
  var $mInlineList;

  function PhpMimeParser($mimeMessage)
  {
    error_reporting(E_ERROR | E_PARSE | E_STRICT);
    // remove mime message end dot
    $mimeMessage = str_replace("\r\n.\r\n","",$mimeMessage);
    $this->allMessage = $mimeMessage;
    $this->mTo = $this->getEmails('To');
    $this->mFrom = $this->getEmails('From');
    $this->mCc = $this->getEmails('Cc');
    $this->mBcc = $this->getEmails('Bcc');
    // $this->cutEMails($mimeMessage);
    $this->cutSubject($mimeMessage);
    // sut all parts alternative related mixed
    $this->getParts($mimeMessage);
    // get simple body
    $this->getSimpleBody();
    $p = 1;
    foreach ($this->MultiParts as $part) {
      if(!mb_check_encoding($part[1], 'UTF-8')){
        $part = mb_convert_encoding($part[1], "UTF-8", "auto");
        // iconv(mb_detect_encoding($part, mb_detect_order(), true), "UTF-8", $part);
      }
      $this->setMimePart($part);
      switch ($this->getContentType()) {
        case 'text/html':
          # Html content
          if(strpos($part[0],'quoted-printable') > 0){
            $this->mHtml = html_entity_decode(quoted_printable_decode($part[1]));
          }else if(strpos($part[0],'base64') > 0){
            $this->mHtml = html_entity_decode(base64_decode($part[1]));
          }else{
            $this->mHtml = html_entity_decode($part[1]);
          }
          break;
        case 'text/plain':
          # Text content
          if(strpos($part[0],'quoted-printable') > 0){
            $this->mText = quoted_printable_decode($part[1]);
          }else if(strpos($part[0],'base64') > 0){
            $this->mText = base64_decode($part[1]);
          }else{
            $this->mText = $part[1];
          }
          break;
        default:
          # File
          $file = NULL;
          $file['name'] = $this->getFileName();
          if ($this->getFileEncoding() == 'base64') {
            $file['content'] = $this->isFileBase64();
          }else if($this->getFileEncoding() == 'quoted-printable'){
            $file['content'] = $this->isFileQuoted();
          }else{
            $file['content'] = $this->isFile();
          }
          $file['type'] = $this->getFileEncoding();
          $file['inline'] = $this->getInlineID();
          // set list with inline images
          $this->mInlineList['cid:'.$this->getInlineID()] = $this->getFileName();
          // add file
          $this->mFiles[$p] = $file;
          break;
      }
      $p++;
    }
    error_reporting(E_ALL);
  }

  function cutEMails($str){
    preg_match_all('/(?<=((\n)To:)|(^To:))(.*)+?(?=())/', $str, $to);
    // preg_match_all('/To:(.*)/', $str, $to);
    echo "To " . htmlentities($to[0][0]);

    preg_match_all('/(?<=((\n)From:)|(^From:))(.*)+?(?=())/', $str, $from);
    // preg_match_all('/From:(.*)/', $str, $to);
    echo "From " . htmlentities($to[0][0]);

    preg_match_all('/(?<=((\n)Subject:)|(^Subject:))(.*)+?(?=())/', $str, $subject);
    echo "Subject " . $subject[0][0];
  }

  function getEmails($str){
    $emails = array();
    $name = "";
    preg_match_all('/(?<=((\n)'.$str.':)|(^'.$str.':))(.*)+?(?=())/', $this->allMessage, $out);
    // preg_match_all('/To:(.*)/', $this->allMessage, $out);
    // print_r($out);
    if(count($out[0]) != NULL){
      $out = str_ireplace($str.':', '', $out[0][0]);
      $out = str_ireplace('<', '', $out);
      $out = str_ireplace('>', '', $out);
      $out = explode(',', $out);
      $jj = 0;
      foreach ($out as $v) {
        $x = explode(" ",$v);
        // email
        $emails[$jj]['email'] = end($x);
        // name
        for ($i = 0; $i < (count($x)-1); $i++) {
          $name .=  $x[$i] . ' ';
        }
        $emails[$jj]['name'] = $name;
        $jj++;
        $name = "";
      }
    }
    return $emails;
  }

  function cutSubject($str){
    preg_match_all('/(?<=((\n)Subject:)|(^Subject:))(.*)+?(?=())/', $str, $subject);
    // echo "Subject " . $subject[0][0];
    $s = $subject[0][0];
    if(!mb_check_encoding($s, 'UTF-8')){
      $s = mb_convert_encoding($s, "UTF-8", "auto");
      // iconv(mb_detect_encoding($part, mb_detect_order(), true), "UTF-8", $part);
    }
    if (strpos($s, '?Q?') > 0 || strpos($s, '?q?') > 0) {
      $p = explode('?', $s);
      $encoding = $p[1];
      $s = str_ireplace("=?".$encoding."?Q?", "", $s);
      $s = str_replace("?=", "", $s);
      $s = quoted_printable_decode($s);
    }else if (strpos($s, '?B?') > 0 || strpos($s, '?b?') > 0){
      $p = explode('?', $s);
      $encoding = $p[1];
      $s = str_ireplace("=?".$encoding."?B?", "", $s);
      $s = str_replace("?=", "", $s);
      $s = base64_decode($s);
    }
    $this->mSubject = $s;
  }

  function countParts(){
    return count($this->MultiParts);
  }

  function isFile(){
    if (strpos($this->mime[0],'Content-Disposition:') > 0) {
      return trim($this->mime[1]);
    }
    return 0;
  }

  function isFileQuoted(){
    if (strpos($this->mime[0],'Content-Disposition:') > 0) {
      return trim(quoted_printable_decode($this->mime[1]));
    }
    return 0;
  }

  function isFileBase64(){
    if (strpos($this->mime[0],'Content-Disposition:') > 0) {
      return trim(base64_decode($this->mime[1]));
    }
    return 0;
  }

  function setMimePart($mimePart){
    $this->mime = $mimePart;
  }

  function getFileName(){
    preg_match_all('/Content-Disposition:(.*)/', $this->mime[0], $out);
    if (empty($out[0][0])) {
      return 0;
    }
    $str = html_entity_decode($out[0][0]);
    preg_match_all('/(?<=(filename="))(.*)?(?=("))/', $str, $file);
    if (!empty($file[0][0])) {
      return trim($file[0][0]);
    }
    return 0;
    // echo mb_check_encoding($out[0][0], 'UTF-8');		// chr(34)
    // $str = str_replace('Content-Disposition: attachment; filename=', "", $str);
    // return $str = str_replace('"', "", $str);
  }

  function getFileEncoding(){
    preg_match_all('/Content-Transfer-Encoding:(.*)/', $this->mime[0], $out);
    if (empty($out[0][0])) {
      return 0;
    }
    $str = html_entity_decode($out[0][0]);
    $f = explode(":", $str);
    if (!empty($f[1])) {
      return trim($f[1]);
    }
    return 0;
  }

  function getInlineID(){
    preg_match_all('/Content-ID:(.*)/', $this->mime[0], $out);
    if (empty($out[0][0])) {
      return 0;
    }
    $str = html_entity_decode($out[0][0]);
    if ($str != NULL) {
      preg_match_all('/(?<=(<))(.*)?(?=(>))/', $str, $file);
      if (!empty($file[0][0])) {
        return trim($file[0][0]);
      }
    }
    return 0;
  }

  function getContentType(){
    preg_match_all('/Content-Type:(.*)/', $this->mime[0], $out);
    if (empty($out[0][0])) {
      return 0;
    }
    $str = html_entity_decode($out[0][0]);
    if ($str != NULL) {
      preg_match_all('/(?<=(:))(.*)?(?=(;))/', $str, $file);
      if (!empty($file[0][0])) {
        return trim($file[0][0]);
      }
    }
    return 0;
  }

  function getHtmlMsg(){
    foreach ($this->MultiParts as $key => $value) {
      if (strpos($value[0],'text/html') > 0 ) {
        if(strpos($value[0],'quoted-printable') > 0){
          return quoted_printable_decode($value[1]);
        }else if(strpos($value[0],'base64') > 0){
          return base64_decode($value[1]);
        }else{
          $value[1];
        }
      }
    }
    return "";
  }

  function getTextMsg(){
    foreach ($this->MultiParts as $key => $value) {
      if (strpos($value[0],'text/plain') > 0 ) {
        if(strpos($value[0],'quoted-printable') > 0){
          return quoted_printable_decode($value[1]);
        }else if(strpos($value[0],'base64') > 0){
          return base64_decode($value[1]);
        }else{
          $value[1];
        }
      }
    }
    return "";
  }

  function getSimpleBody(){
    if (count($this->MultiParts) == 0) {
      $p = explode("\r\n\r\n", $this->allMessage);
      $this->mHtml = $p[1];
      $this->mText = $this->mHtml;
    }
  }

  function getParts($message){
    preg_match_all('/((?<=(Content-Type: multipart\/mixed; boundary="))(.*)?(?=(")))|((?<=(Content-Type: multipart\/related; boundary="))(.*)?(?=(")))|((?<=(Content-Type: multipart\/alternative; boundary="))(.*)?(?=(")))/', $message, $boundary);

    // echo "<pre>";
    // print_r($boundary);

    $AllPartsUnique = "";
    $j=0;

    foreach ($boundary[0] as $key => $v) {
      if($key >= 0){
        // echo "\n\n\nBoundary " . $v . "\r\n";

        // cut boundary content
        preg_match_all('/(?<=(--'.$v.'))(| |.*|[\s\S]+|\<|\>|\.|\r|\n|\0|@|\w+)?(?=(--'.$v.'--))/', $message, $part);
        // print_r($part);
        $bname = $v;

        foreach ($part[0] as $v) {
          // echo "PART " . $bname . " " . $v . "\r\n";
          $parts = explode("--".$bname, $v);

          // echo "<pre>";
          foreach ($parts as $v) {
            // echo "\r\nSINGLE PART " . $v . "\r\n";
            // $AllPartsUnique[$j] = $v;
            // with html visible on page
            $AllPartsUnique[$j] = htmlentities($v);
            $j++;
          }
        }
      }
    }
    foreach($AllPartsUnique as $key => $one) {
      foreach ($boundary[0] as $find) {
        if(strpos($one, $find) !== false){
          unset($AllPartsUnique[$key]);
        }
      }
    }
    // echo "<pre>";
    // print_r($AllPartsUnique);
    $iii = 0;
    foreach ($AllPartsUnique as $v) {
      $e =  explode("\r\n\r\n", $v);
      $this->MultiParts[$iii] = $e;
      $iii++;
    }
    return $this->MultiParts;
  }

  // get line from Header (To for To: , Bcc for Bcc: ...)
  function getFromHeader($str){
    preg_match_all('/'.$str.':(.*)/', $this->allMessage, $out);
    return htmlentities($out[0][0]);
  }
}

if ( ! isset ($__appvar['office365']) ) {
  $mail = new AE_cls_Email();
} else {
  $mail = new AE_cls_ExchangeOnline();
}

$msg = $mail->getQueueMsgById($_POST["id"]);
//global $__debug;
//$__debug = true;

$raw = explode("\n",$msg["rawMail"]);
foreach ($raw as $regel)
{
  if (stristr($regel, "Return-Path:"))
  {
    break;
  }
}
$rawFrom = explode(":",$regel);
$rawitem = str_replace("<", "", $rawFrom[1]);
$rawitem = trim(str_replace(">", "", $rawitem));


$m = new PhpMimeParser($msg);
$msg = $m->allMessage;


//debug($msg);

$m = $msg["body"];

?>
<style>
  .pk1{
    width: 120px;
  }
  .pk2{

  }
  .pk12{

  }
</style>
<table>
  <tr>
    <td class="pk1">Afzender</td>
    <td class="pk2">:<?=$msg["from"]?></td>
  </tr>
  <tr>
    <td class="pk1">Oorspronkelijke afzender</td>
    <td class="pk2">:<?=$rawitem?></td>
  </tr>
  <tr>
    <td class="pk1">Onderwerp</td>
    <td class="pk2">:<?=$msg["subject"]?></td>
  </tr>
  <tr>
    <td class="pk12" colspan="2">
      <hr/>
    <pre><?=$m?></pre></td>
  </tr>

</table>


