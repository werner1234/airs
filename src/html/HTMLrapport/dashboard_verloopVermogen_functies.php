<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/10/27 08:51:29 $
    File Versie         : $Revision: 1.1 $

    $Log: dashboard_verloopVermogen_functies.php,v $
    Revision 1.1  2017/10/27 08:51:29  cvs
    no message

    Revision 1.4  2017/07/21 13:52:56  cvs
    call 5933

    Revision 1.3  2017/06/26 11:37:54  cvs
    no message

    Revision 1.2  2017/06/02 14:21:10  cvs
    no message

    Revision 1.1  2017/04/26 15:06:22  cvs
    call 5816



*/


function getVerloopVermogen($dataset, $apiCall = false)
{
  global $portefeuille, $USR, $jsVV;
//debug($dataset);
  include_once "../../classes/AE_cls_formatter.php";
  $fmt = new AE_cls_formatter(",",".");
  $firstItem = true;
  foreach ($dataset as $rec)
  {
    if ($firstItem)
    {

      $parts = explode("-",$rec["periodeForm"]);
      if ((int) $parts[2] == 1 AND (int) $parts[1] == 1)
      {
        $jul = mktime(1,1,1,$parts[1],$parts[2],$parts[0]) - 42500;
        $d = jul2sql($jul);
      }
      else
      {
        $d = $rec["periodeForm"];
      }

      $jsVV .= "\n{  label: '".$fmt->format("@D{form}",$d)."', y: ".round($rec["waardeBegin"],0).", legendText:'".$fmt->format("@D{form}",$d)."<br/>vermogen :".$fmt->format("@N{.0}",$rec["waardeBegin"])."' },";
      $firstItem = false;
    }
    $jsVV .= "\n{  label: '".$fmt->format("@D{form}",$rec["datum"])."', y: ".round($rec["waardeHuidige"],0).", legendText:'".$fmt->format("@D{form}",$rec["datum"])."<br/>vermogen :".$fmt->format("@N{.0}",$rec["waardeHuidige"])."' },";
  }
  if ($apiCall)
  {

    return $jsVV;
  }

}

function getVerloopVermogenExternalApi($dataset, $apiCall = false)
{

  global $portefeuille, $USR, $jsVV;
//debug($dataset);
  include_once "../../classes/AE_cls_formatter.php";
  $fmt = new AE_cls_formatter(",",".");
  $firstItem = true;
  $verloop = array();
  foreach ($dataset as $rec)
  {
    if ($firstItem)
    {

      $parts = explode("-",$rec["periodeForm"]);
      if ((int) $parts[2] == 1 AND (int) $parts[1] == 1)
      {
        $jul = mktime(1,1,1,$parts[1],$parts[2],$parts[0]) - 42500;
        $d = jul2sql($jul);
      }
      else
      {
        $d = $rec["periodeForm"];
      }

        $verloop[] = array(
          "datum"  => $fmt->format("@D{form}",$d),
          "waarde" => round($rec["waardeHuidige"],2)
        );


      $firstItem = false;
    }

    $verloop[] = array(
      "datum"  => $fmt->format("@D{form}",$rec["datum"]),
      "waarde" => round($rec["waardeHuidige"],2)
    );




  }
  return $verloop;
}

