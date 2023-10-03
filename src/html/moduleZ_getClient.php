<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/02/06 09:05:18 $
    File Versie         : $Revision: 1.4 $

    $Log: moduleZ_getClient.php,v $
    Revision 1.4  2019/02/06 09:05:18  cvs
    call 7463

    Revision 1.3  2018/09/07 10:12:34  cvs
    commit voor robert call 6989

    Revision 1.2  2018/07/02 07:50:29  cvs
    call 6709

    Revision 1.1  2018/06/18 06:59:57  cvs
    update naar VRY omgeving

    Revision 1.1  2018/05/25 09:34:52  cvs
    25-5-2018


*/

include_once("../config/local_vars.php");
include_once("../config/applicatie_functies.php");
include_once("../classes/AE_cls_mysql.php");
error_reporting(E_ERROR);

$zoek = $_GET["term"];

$db = new DB();

if ($_GET["mode"] == "2")
{
  $query = "

SELECT
  *
FROM
  CRM_naw
WHERE 
  ( naam LIKE '%".$zoek."%' OR   
    zoekveld LIKE '%".$zoek."%' ) AND  
    externID = ''
ORDER BY 
  naam
LIMIT 50

";


  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    $d = explode("-", $rec["geboortedatum"]);
    $gbd = ( $d[0] != "0000")?$d[2]."-".$d[1]."-".$d[0]:"";

    $d = explode("-", $rec["IdGeldigTot"]);
    $lgd = ( $d[0] != "0000")?$d[2]."-".$d[1]."-".$d[0]:"";

    $adr = explode(" ",$rec["adres"]);
    $hnr = array_pop($adr);
    $adr = implode(" ",$adr);

    $output[] = array(
      "label"           => $rec["naam"]." | ".$rec["externID"],
      "value"           => $rec["naam"],
      "naam"            => $rec["naam"],
      "achternaam"      => $rec["achternaam"],
      "voorletters"     => $rec["voorletters"],
      "tussenvoegsel"   => $rec["tussenvoegsel"],
      "geboortedatum"   => $gbd,
      "nationaliteit"   => ($rec["nationaliteit"] == "")?"NL":$rec["nationaliteit"],
      "geslacht"        => $rec["geslacht"],
      "BSN"             => $rec["BSN"],
      "legitimatie"     => $rec["legitimatie"],
      "IdGeldigTot"     => $lgd,
      "nummerID"        => $rec["nummerID"],
      "adres"           => $adr,
      "hnr"             => $hnr,
      "pc"              => $rec["pc"],
      "plaats"          => $rec["plaats"],
      "land"            => ($rec["land"] == "")?"NL":$rec["land"],
      "tel1"            => $rec["tel1"],
      "tel1_oms"        => $rec["tel1_oms"],
      "tel2"            => $rec["tel2"],
      "tel2_oms"        => $rec["tel2_oms"],
      "email"           => $rec["email"],

    );
  }

  echo json_encode($output);
}
else
{
  $query = "

SELECT
  naam,
  zoekveld,
  externID
FROM
  CRM_naw
WHERE 
  ( naam LIKE '%".$zoek."%' OR   
    zoekveld LIKE '%".$zoek."%'  OR  
    externID LIKE '%".$zoek."%'   )  AND
    externID <> ''
ORDER BY 
  naam
LIMIT 50

";


  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {

    $output[] = array(
      "label"         => utf8_encode($rec["naam"])." | ".$rec["externID"],
      "value"         => utf8_encode($rec["naam"]),
      "naam"          => utf8_encode($rec["naam"]),
      "externID"      => $rec["externID"]);
  }

  echo json_encode($output);

}
