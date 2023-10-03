<?php
/*
    AE-ICT sourcemodule created 04 jun. 2021
    Author              : Chris van Santen
    Filename            : api_documentlinks.php


*/

$__debug = true;
global $logArray;

$portefeuille = sanatizePortefeuille($__ses["data"]["portefeuille"]);




if (count($error) == 0)
{


  $blob = vulPortaalApi($portefeuille);

//  ad($blob);
//  if ($blob == 0 )
//  {
//    $error[] = "Deze functie is niet geactiveerd";
//  }
//  else
  {
    $blob     = base64_encode($blob);
    $md5      = md5($blob);
    $filename = "dagRapport_{$portefeuille}_".date("Y-m-d");
    $mimeType = "application/pdf";

    $output[] = array(
      "filename"      => $filename,
      "portefeuille"  => $portefeuille,
      "mimetype"      => $mimeType,
      "blob"          => $blob,
      "hash"          => $md5
    );
    $logArray[] = array(
      "filename"      => $filename,
      "portefeuille"  => $portefeuille,
      "mimetype"      => $mimeType,
      "hash"          => $md5
    );
  }
}





