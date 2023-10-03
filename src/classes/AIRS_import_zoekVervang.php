<?php

/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/10/30 13:34:48 $
    File Versie         : $Revision: 1.4 $

    $Log: AIRS_import_zoekVervang.php,v $
    Revision 1.4  2019/10/30 13:34:48  cvs
    call 8214

    Revision 1.3  2019/08/28 09:50:48  cvs
    rekeningnr voorloop 0 onafhankelijk gemaakt

    Revision 1.2  2018/03/21 15:22:30  cvs
    call 6313

    Revision 1.1  2017/03/24 09:34:54  cvs
    call 5731



*/

/*
 * Deze class zoekt teksten en vervangt deze tijdens import
 *
 *   depotbank                BINCK
 *   VB                       ERC
 *   functie                  do_UITK
 *   subInFunctie             Kosten
 *   veld                     grootboek
 *   conditie                 KOST
 *   gewenste veld            grootboek
 *   gewenste uitvoer         BEW
 *
 */

class AIRS_import_zoekVervang
{
  var $rekeningDetails = array();
  var $depotBank       = "";
  var $disabled;
  var $verbose;
  var $VB = "";
  var $ruleSet;


  function AIRS_import_zoekVervang($depotbank)
  {
    $this->depotBank = $depotbank;
    $this->disabled  = false;
    $this->verbose   = false;
    $this->getRules();

  }

  function setVerbose()
  {
    $this->verbose   = true;
  }

  function disable()
  {
    $this->disabled  = true;
  }

  function debugTrace(){
    $trace = debug_backtrace();
    return $trace;
  }

  function refererFunction(){
    $trace = debug_backtrace();
    return $trace[2]['function'];
  }

  function reWrite($input, $reknr="")
  {

    if ($this->disabled)                                                              // als class uitgeschakeld record onveranderd teruggeven
    {
      return $input;
    }

    $this->VB = "";
    if ($reknr <> "")
    {
      $rekRec = $this->getDetailsByRekening($reknr);
      if ($rekRec["Vermogensbeheerder"] <> "")
      {
        $this->VB = $rekRec["Vermogensbeheerder"];
        $this->getRules();
      }
    }

    if ($this->VB == "")                                                              // als VB nog steeds leeg dan stoppen
    {
      return false;
    }

    if ($this->verbose)
    {
      $verbose = array("regels voor depot/vb: ".$this->depotBank." / ".$this->VB);
    }
    $text = $input;
//    debug($this->ruleSet, $input);
    foreach ($this->ruleSet as $item)                                                         // pas de gevonden regels toe als de conditie waar is
    {
      $r =  "zoek: ".$item["zoek"].", vervang: ".$item["vervang"]." input: ".$input;

      if (stristr($input, $item["zoek"]) <> "")
      {
        switch ($item["type"])
        {
          case "a":
            $text = $item["vervang"];
            break;
          case "l":

            $s = preg_split("/".$item["zoek"]."/i", $text);
            $text = $item["vervang"].$s[1];
            break;
          case "r":
            $s = preg_split("/".$item["zoek"]."/i", $text);
            $text = $s[0].$item["vervang"];
            break;
          default:
            $text = str_ireplace($item["zoek"], $item["vervang"], $text);
        }

        if ($this->verbose)
        {
          $r = "::FOUND ".$r;
          debug($r);
        }

      }


    }

    return $text;
  }

  function getRules()
  {
    $db = new DB();
    $out = array();
    $query = "
      SELECT 
        * 
      FROM 
        importZoekVervang 
      WHERE 
        depotbank          = '".$this->depotBank."'  AND
        vermogensBeheerder = '".$this->VB."'         AND
        actief             = 1
      ORDER BY
        prio
    ";

    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $out[] = array(
        "zoek"    => $rec["zoek"],
        "vervang" => $rec["vervang"],
        "type"    => $rec["typeVervang"],
      );
    }
    $this->ruleSet = $out;
    return;
  }

  function getDetailsByRekening($reknr)
  {
    $db = new DB();
    $query = "
    SELECT
      Portefeuilles.Vermogensbeheerder,
      Portefeuilles.Portefeuille,
      Rekeningen.Rekening,
      Rekeningen.Valuta
    FROM
      Portefeuilles
    INNER JOIN Rekeningen ON 
      Portefeuilles.Portefeuille = Rekeningen.Portefeuille
    WHERE 
      Rekeningen.Rekening LIKE '%".$reknr."'
    ";
    return $db->lookupRecordByQuery($query);

  }


  function initModule()
  {
    include_once("AE_cls_SQLman.php");

    $tst = new SQLman();
    $tst->tableExist("importZoekVervang",true);
    $tst->changeField("importZoekVervang","depotbank",array("Type"=>"varchar(10)","Null"=>false));
    $tst->changeField("importZoekVervang","vermogensBeheerder",array("Type"=>"varchar(10)","Null"=>false));
    $tst->changeField("importZoekVervang","actief",array("Type"=>"tinyInt","Null"=>false));
    $tst->changeField("importZoekVervang","zoek",array("Type"=>"varchar(100)","Null"=>false));
    $tst->changeField("importZoekVervang","vervang",array("Type"=>"varchar(100)","Null"=>false));
    $tst->changeField("importZoekVervang","typeVervang",array("Type"=>"varchar(20)","Null"=>false));
    $tst->changeField("importZoekVervang","veldAanduiding",array("Type"=>"text","Null"=>false));
    $tst->changeField("importZoekVervang","prio",array("Type"=>"int","Null"=>false));
    $tst->changeField("importZoekVervang","memo",array("Type"=>"text","Null"=>false));

  }

  
}