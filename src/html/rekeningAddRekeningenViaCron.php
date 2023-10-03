<?php
/*
    AE-ICT sourcemodule created 05 okt. 2022
    Author              : Chris van Santen
    Filename            : rekeningAddRekeningenViaCron.php


*/

$disable_auth = true;

include_once("wwwvars.php");
include_once('../classes/AE_cls_phpmailer.php');


$USR = "SYS";
$data = array_merge($_GET,$_POST);
$error = "";
$done = "";
$db = new DB();
$db1 = new DB();

$mainHeader   = "";
$dagen = 1;
$query = "
    SELECT 
      * 
    FROM 
      Portefeuilles 
    WHERE 
      add_date >= DATE_SUB(CURDATE(), INTERVAL {$dagen} DAY) AND 
      Depotbank IN ( 'SAXO', 'AAB', 'BIN', 'TGB', 'INT', 'OVE' ) AND 
      consolidatie = 0
";

$portRow = array();
$addRek  = array();
$db->executeQuery($query);
while ($rec = $db->nextRecord())
{


  $p = $rec["Portefeuille"];
  if ($rec["Depotbank"] == "AAB" AND strlen($p) != 9)
  {
    // alleen AAB als porteuille 9 lang is..
    continue;
  }

  $portRow[$p]["portefeuille"] =  $rec["Portefeuille"];
  $portRow[$p]["Vermogensbeheerder"] =  $rec["Vermogensbeheerder"];
  $portRow[$p]["Depotbank"] =  $rec["Depotbank"];
  $portRow[$p]["add_date"] =  $rec["add_date"];
  $portRow[$p]["add_user"] =  $rec["add_user"];


  $query = "
      SELECT 
        * 
      FROM 
        Rekeningen 
      WHERE 
        Portefeuille = '{$rec["Portefeuille"]}'    OR 
            Rekening = '{$rec["Portefeuille"]}MEM' OR
            Rekening = '{$rec["Portefeuille"]}EUR' 

        
        ";

  $db1->executeQuery($query);
  while ($rekRec = $db1->nextRecord())
  {
    if ($rekRec["Valuta"] == "EUR")
    {
      if ($rekRec["Memoriaal"] == 0)
      {
        $portRow[$p]["rekEUR"] = $rekRec["Rekening"]." (portefeuille: ".$rekRec["Portefeuille"].")";
      }
      else
      {
        $portRow[$p]["rekMEM"] = $rekRec["Rekening"]." (portefeuille: ".$rekRec["Portefeuille"].")";
      }
    }
  }

  foreach ($portRow as $port=>$item)
  {
    if (isset($item["rekMEM"]) AND isset($item["rekEUR"]))
    {
      continue;
    }
    $addRek[$port] = $item;
  }
}

//
// FASE 2
//
  $db = new DB();
  $msg = array(
    "Start verwerken ",
    "Automatisch aangemaakte rekeningnrs per ".date("d-m-Y H:i")
  );
  $portArray = array();
  foreach ($addRek as $k=>$v)
  {

    //debug($v,$k);


    $depotbank = $v["Depotbank"];
    $portefeuille = $v["portefeuille"];


      if (!isset($v["rekEUR"]))
      {
        $valuta = "EUR";
        $query = "INSERT INTO Rekeningen SET
            add_user            = '{$USR}'
          , add_date            = NOW()
          , change_user         = '{$USR}'
          , change_date         = NOW()
          , Rekening            = '{$portefeuille}EUR'
          , Portefeuille        = '{$portefeuille}'
          , consolidatie        = 0
          , Inactief            = 0
          , Memoriaal           = 0
          , Depotbank           = '{$depotbank}'
          , Valuta              = 'EUR'
          , Beleggingscategorie = 'Liquiditeiten'

        ";
        if ($db->executeQuery($query))
        {
          $msg[] = "Rekening {$portefeuille}{$valuta} toegevoegd";
        }
        else
        {
          $msg[] = "Mislukt aanmaken rekening {$portefeuille}{$valuta}";
        }
        $portArray[] = $portefeuille;

      }

      if (!isset($v["rekMEM"]))
      {
        $valuta = "MEM";
        $query = "INSERT INTO Rekeningen SET 
            add_user            = '{$USR}'
          , add_date            = NOW()
          , change_user         = '{$USR}'
          , change_date         = NOW()
          , Rekening            = '{$portefeuille}MEM'
          , Portefeuille        = '{$portefeuille}'
          , consolidatie        = 0
          , Inactief            = 0
          , Memoriaal           = 1
          , Depotbank           = '{$depotbank}'
          , Valuta              = 'EUR'
          , Beleggingscategorie = 'Liquiditeiten'

        ";
        if ($db->executeQuery($query))
        {
          $msg[] = "Rekening {$portefeuille}{$valuta} toegevoegd";
        }
        else
        {
          $msg[] = "Mislukt aanmaken rekening {$portefeuille}{$valuta}";
        }
        $portArray[] = $portefeuille;
      }



  }
  
  $portArray = array_unique($portArray);
  if (count($portArray) > 0)
  {
    foreach ($portArray as $portefeuille)
    {
      $msg[] = "Consolidatie controle voor $portefeuille";
      $con = new AIRS_consolidatie();
      $VPs = $con->ophalenVPsViaPortefeuille($portefeuille);
      if ( count($VPs) > 0 )
      {
        $con->bijwerkenConsolidaties($VPs);
      }
    }
  }
  else
  {
    $msg[] = "Geen rekeningen toegevoegd";
  }

  $html = "<ul>";
  foreach ($msg as $item)
  {
    $html .= "<li>{$item}</li>";
  }
  $html .= "</ul>";

$to = "teamairs@useblanco.com";
//$to = "cvs@aeict.nl";

$mailserver = "mailer.aeict.nl";
$mail = new PHPMailer();
$mail->IsSMTP();
$mail->From     = 'noreply@airshost.nl';
$mail->FromName = "Airs HOME";
$mail->Body    = $html;
$mail->AltBody = html_entity_decode(strip_tags($html));
$mail->AddAddress($to,$to);
$mail->AddBCC('cvs@aeict.nl');
$mail->Subject = "Automatisch aangemaakte rekeningnrs per ".date("d-m-Y H:i");
$mail->Host=$mailserver;
if(!$mail->Send())
{

}
else
{
  echo "E-mail verstuurd naar {$to}";
}
