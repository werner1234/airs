<?php

/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/03/30 14:46:03 $
    File Versie         : $Revision: 1.2 $

    $Log: AIRS_import_afwijkingen.php,v $
    Revision 1.2  2020/03/30 14:46:03  cvs
    call 8355

    Revision 1.1  2017/03/24 09:34:54  cvs
    call 5731



*/

/*
 * Deze class zoekt afwijkende wensen bij de transactieimport afhankelijk van
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

class AIRS_import_afwijkingen
{
  var $rekeningDetails = array();
  var $depotBank       = "";
  var $disabled;
  var $verbose;


  function AIRS_import_afwijkingen($depotbank)
  {
    $this->depotBank = $depotbank;
    $this->disabled  = false;
    $this->verbose   = false;

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

  function reWrite($sub, $mrRec)
  {

    $functie = $this->refererFunction();

    if ($this->disabled)                                                              // als class uitgeschakeld record onveranderd teruggeven
    {
      return $mrRec;
    }

    $this->getDetailsByRekening($mrRec["Rekening"]);                                  // haal o.a. de VB op bij het reknr
    $rules = $this->getRules($functie,$this->rekeningDetails["Vermogensbeheerder"]);  // haal de rewrite regels op die passen bij depot/VB/functie
    if ($this->verbose)
    {
      $verb = array("regels voor functie: $functie");
    }

    foreach ($rules as $item)                                                         // pas de gevonden regels toe als de conditie waar is
    {
      $r =  "trigger: ".$item["subInFunctie"].", test: ".$item["testVeld"]." --> conditie: ".$item["testConditie"]. ", muteer: ".$item["targetVeld"].", naar inhoud: ".$item["targetWaarde"];
      //debug(array(strtolower($mrRec[$item["testVeld"]]), strtolower($item["testConditie"]), $sub, $item["subInFunctie"]));

      if ($item["testSoort"] == "bevat")
      {
        $testLike   = ( stristr($mrRec[$item["testVeld"]], $item["testConditie"]) !== false );
        if ( $testLike AND strtolower($sub) == strtolower($item["subInFunctie"]))
        {
          $mrRec[$item["targetVeld"]] = $item["targetWaarde"];
          $mrRec["aktie"] = "*".$mrRec["aktie"];
          $r .= " :: TOEGEPAST bevat::";
        }
      }
      else
      {
        $testEq     = ( strtolower($mrRec[$item["testVeld"]]) == strtolower($item["testConditie"]) );
        $testStarts = ( substr(strtolower($mrRec[$item["testVeld"]]),0,strlen($item["testConditie"])) == strtolower($item["testConditie"]) );
        if ( ($testEq OR $testStarts) AND strtolower($sub) == strtolower($item["subInFunctie"]))
        {
          $mrRec[$item["targetVeld"]] = $item["targetWaarde"];
          $mrRec["aktie"] = "*".$mrRec["aktie"];
          $r .= " :: TOEGEPAST begint met::";
        }
      }


      $verb[] = $r;
    }

    if ($this->verbose)
    {
      debug($verb);
    }
    return $mrRec;
  }

  function getRules($functie, $VB)
  {
    $db = new DB();
    $out = array();
    $query = "
      SELECT 
        * 
      FROM 
        importAfwijkingen 
      WHERE 
        depotbank          = '".$this->depotBank."'  AND
        vermogensBeheerder = '".$VB."'               AND
        functie            = '".$functie."'          AND
        actief             = 1
      ORDER BY
        prio
    ";

    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $out[] = $rec;
    }
    return $out;
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
      Rekeningen.Rekening = '".$reknr."'
    ";
    $this->rekeningDetails = $db->lookupRecordByQuery($query);

  }




  function initModule()
  {
    include_once("AE_cls_SQLman.php");

    $tst = new SQLman();
    $tst->tableExist("importAfwijkingen",true);
    $tst->changeField("importAfwijkingen","depotbank",array("Type"=>"varchar(10)","Null"=>false));
    $tst->changeField("importAfwijkingen","vermogensBeheerder",array("Type"=>"varchar(10)","Null"=>false));
    $tst->changeField("importAfwijkingen","actief",array("Type"=>"tinyInt","Null"=>false));
    $tst->changeField("importAfwijkingen","functie",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField("importAfwijkingen","subInFunctie",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField("importAfwijkingen","testVeld",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField("importAfwijkingen","testConditie",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField("importAfwijkingen","targetVeld",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField("importAfwijkingen","targetWaarde",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField("importAfwijkingen","prio",array("Type"=>"int","Null"=>false, "Default" => "DEFAULT 10"));
    $tst->changeField("importAfwijkingen","memo",array("Type"=>"text","Null"=>false));

  }

  
}