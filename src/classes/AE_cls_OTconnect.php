<?php
/*
    AE-ICT sourcemodule created 15 jun. 2020
    Author              : Chris van Santen
    Filename            : AE_cls_OTconnect.php

    $Log: AE_cls_OTconnect.php,v $
    Revision 1.5  2020/06/22 07:49:36  cvs
    call 8380

    Revision 1.4  2020/06/22 07:31:18  cvs
    call 8380

    Revision 1.3  2020/06/15 13:25:50  cvs
    call 8380

*/

class AE_cls_OTconnect
{
  var $user;
  var $module;
  var $bedrijf;
  var $apiKey;
  var $apiUrl;
  var $urls = array(
    "PROD" => array("blancoQueue" => "https://blancoAirsApi.airshost.nl/"),
    "TEST" => array("blancoQueue" => "http://chris.php53.2018.aeict.net/AIRS_OTserver/apiBlancoAIRS/"),
  );
  var $actions = array(
    "blancoQueue" => "pollqueue"
  );
  var $PROD = true;
  var $error = array();
  var $errorFlag = false;
  var $lastResult;
  var $lastHttpCode;
  var $postData  = array();
  var $basePostData;
  var $headerData  = array();
  var $baseHeaderData;
  var $action;
  function AE_cls_OTconnect($module="blancoQueue")
  {
    global $USR, $__appvar;

    $this->PROD    = (substr($_SERVER["SERVER_ADDR"],0,12) != "192.168.222.")?"PROD":"TEST";
    $this->user    = $USR;
    $this->bedrijf = $__appvar["bedrijf"];
    if ($this->urls[$this->PROD][$module] == "")
    {
      echo "OT module niet gevonden!";
      exit;
    }
    $this->apiUrl = $this->urls[$this->PROD][$module];
    $this->apiKey = $__appvar["OTkeys"][$module];
    $this->clearPostData();
    $this->clearHeaderData();

    $this->baseHeaderData = array(
      'Content-Type: application/json',
      'apiKey: '.$this->apiKey

    );
    $this->setAction($this->actions[$module]);

  }

  function checkForErrors()
  {
    if (count($this->error) > 0)
    {
      foreach ($this->error as $err)
      {
        echo "<li>$err";
      }
      return true;
    }
    else
    {
      return false;
    }

  }

  function setError($txt)               {    $this->error[] = $txt;  }
  function setAction($action)           {    $this->action = array("action: ".$action); }
  function clearPostData()              {    $this->postData = array();   }
  function addPostData($key, $value)    {    $this->postData[$key] = $value;  }
  function clearHeaderData()            {    $this->headerData = array();  }
  function addHeaderData($key, $value)  {    $this->headerData[] = "{$key}: {$value}";  }
  function showObject()                 {    debug($this);  }
  function getHttpCode()                {    return $this->lastHttpCode;   }

  function getRequest()
  {

    $json = new AE_Json();

    $this->errorFlag = false;
    $header  = array_merge($this->baseHeaderData, $this->action, $this->headerData);
    $cSession = curl_init();

    curl_setopt($cSession, CURLOPT_URL, $this->apiUrl);
    curl_setopt($cSession, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cSession, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($cSession, CURLOPT_HTTPHEADER, $header);
    curl_setopt($cSession, CURLOPT_GET, true);
    curl_setopt($cSession, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($cSession, CURLOPT_SSL_VERIFYHOST, 0);

    $this->lastResult = curl_exec($cSession);
    $this->lastHttpCode = curl_getinfo($cSession, CURLINFO_HTTP_CODE);
    $res = (array) $json->json_decode($this->lastResult);

    if (count($res["errors"]) > 0)
    {
      $this->error = $res["errors"];
      $this->errorFlag = true;
    }

    curl_close($cSession);
  }

  function postRequest()
  {
    $json = new AE_Json();

    $payload = $json->json_encode(array_merge($this->basePostData, $this->postData));
    $header  = array_merge($this->baseHeaderData, $this->action, $this->headerData);

    $cSession = curl_init();
    curl_setopt($cSession, CURLOPT_URL, $this->apiUrl);
    curl_setopt($cSession, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cSession, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($cSession, CURLOPT_HTTPHEADER, $header);
    curl_setopt($cSession, CURLOPT_POST, true);
    curl_setopt($cSession, CURLOPT_POSTFIELDS, $payload);

    $this->lastResult = curl_exec($cSession);
    $this->lastHttpCode = curl_getinfo($cSession, CURLINFO_HTTP_CODE);

    curl_close($cSession);
  }

  function getResult($asArray=true)
  {
    $json = new AE_Json();
    return $asArray?(array)$json->json_decode($this->lastResult):$this->lastResult;
  }




  function apiCall($postArray)
  {
    global $__api, $_SESSION;
    session_start();

    if ($_SESSION["apiOffline"])
    {
      return false;
    }
    $postArray[] = "apiKey=".$__api["key"];

//  $ch = curl_init();
//  curl_setopt($ch, CURLOPT_URL, $__api["url"]);
//  curl_setopt($ch, CURLOPT_TIMEOUT, $__api["timeout"]);
//  curl_setopt($ch, CURLOPT_BUFFERSIZE, 1024000);
//  curl_setopt($ch, CURLOPT_POST, 1);
//  curl_setopt($ch, CURLOPT_POSTFIELDS,implode("&",$postArray));
//
//  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//
//  $output  = curl_exec($ch);
    $url = $__api["url"]."index.php?".implode("&",$postArray);
    $url = str_replace(" ","%20", $url);
//debug($url);
    $output = file_get_contents($url);
//print_r($output);
    $output  = json_decodeAE($output);


    $_SESSION["apiurl"] = $url;

//  $curlError = curl_errno($ch);
//
//  if ($curlError > 0)
//  {
//    $output = array("errors"=>array("server offline"));
//  }

    return (array)$output;
  }
}
