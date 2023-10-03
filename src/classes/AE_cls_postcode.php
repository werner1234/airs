<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/07/01 13:11:24 $
 		File Versie					: $Revision: 1.14 $

 		$Log: AE_cls_lookup.php,v $



*/
class AE_cls_postcode
{
  var $user;
  var $apiKey = "osagkntDm26eUub804qDx3jIyKTPcGx68GuWbCGL";
  var $postcode;
  var $huisnr;
  var $result;
  var $httpCode;


  function AE_cls_postcode()
  {
    global $USR;
	  $this->user = $USR;
  }

  function fetch($postcode, $huisnr)
  {

    $postcode = preg_replace('/[^A-Za-z0-9]/','',$postcode);
    $huisnr = preg_replace('/[^0-9]/','',$huisnr);


    $this->result = null;
    $url = "https://api.postcodeapi.nu/v3/lookup/$postcode/$huisnr";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'X-Api-Key: '.$this->apiKey,
      'Content-Type: application/json')
    );

    $this->result = curl_exec($ch);
    $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $this->result = str_replace('"number"', '"nummer"', $this->result);
    $this->result = str_replace('"street"', '"straat"', $this->result);
    $this->result = str_replace('"city"', '"plaats"', $this->result);
    $this->result = str_replace('"municipality"', '"gemeente"', $this->result);
    $this->result = str_replace('"province"', '"provincie"', $this->result);

    curl_close($ch);

  }

  function getResult()
  {
    if ($this->httpCode == "200")
    {
      return $this->result;
    }
    else
    {
      return '{"error":"noResults"}';
    }

  }

  function getResultArray()
  {
    return (array) json_decode($this->getResult());
  }







}

