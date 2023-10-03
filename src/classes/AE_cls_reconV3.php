<?php
/*
    AE-ICT sourcemodule created 22 mei 2019
    Author              : Chris van Santen
    Filename            : AE_cls_reconV3Class.php

    $Log: AE_cls_reconV3.php,v $
    Revision 1.26  2020/07/01 12:54:10  cvs
    call 7937

    Revision 1.25  2020/07/01 12:16:10  cvs
    call 7937

    Revision 1.24  2020/06/08 09:23:09  cvs
    call 7937

    Revision 1.23  2020/03/20 12:47:03  cvs
    call 8452

    Revision 1.22  2020/03/20 11:09:21  cvs
    call 8451

    Revision 1.21  2020/03/20 10:16:40  cvs
    call 8447

    Revision 1.20  2020/03/18 15:39:41  cvs
    call 8449

    Revision 1.19  2020/03/11 16:08:34  cvs
    zonder call

    Revision 1.18  2020/03/02 15:47:04  cvs
    no message

    Revision 1.17  2020/02/27 08:52:04  cvs
    call 8433

    Revision 1.16  2019/12/06 10:44:26  cvs
    call 7937

    Revision 1.15  2019/12/02 08:25:24  cvs
    call 7937 encoder probleem?

    Revision 1.14  2019/11/29 13:18:09  cvs
    call 7937

*/

//aetodo: opruimen batch na verwerking

//aetodo: ??

class AE_cls_reconV3
{
  var $user;
  var $reconDate = "";
  var $vb = array();   // vermogensbeheerders in bankfile
  var $vbQueryString = "";
  var $depotbank = "";
  var $batch = "";
  var $matchArray = array();
  var $unmatchArray = array();
  var $bankPile = array();
  var $bankPortefeuilles = array();
  var $bankRekeningNrs= array();
  var $airsPile = array();
  var $airsPortefeuilles = array();
  var $airsRekeningNrs= array();
  var $airsCashPile = array();
  var $portefeuilleVB = array();
  var $fondsPile = array();
  var $noFondsPile = array();
  var $depotWherePORT = "";
  var $depotWhereREK = "";
  var $depotFondsCodeField = "";
  var $trPile = array();
  var $cashPosYesterday = Array();

  var $dbReadserver = 1;

  function AE_cls_reconV3($depotBank, $vb, $reconDate)
  {
    global $USR, $__appvar;

    $this->user = $USR;
    $this->reconDate = $reconDate;
    $this->depotbank = $depotBank;
    $this->vb = $vb;
    $this->vbQueryString = " ('".implode("','",$vb)."') ";
    $this->batch = date("YmdHi")."_".rand(11111,99999);
    $this->initModule();
    $this->truncateTables();

    $this->depotWhereREK = "Rekeningen.Depotbank = '".$this->depotbank."' ";
    $this->depotWherePORT = "Portefeuilles.Depotbank = '".$this->depotbank."' ";
    switch (strtoupper($this->depotbank))
    {
      case "AAB":
        $this->depotWhereREK = "( Rekeningen.Depotbank = 'AAB'  OR Rekeningen.Depotbank = 'AABIAM' ) ";
        $this->depotWherePORT = "( Portefeuilles.Depotbank = 'AAB'  OR Portefeuilles.Depotbank = 'AABIAM' ) ";
        $this->depotFondsCodeField = "AABCode";
        break;
      case "BIN":
        $this->depotWhereREK = "( Rekeningen.Depotbank = 'BIN' OR  Rekeningen.Depotbank = 'BINB' ) ";
        $this->depotWherePORT = "( Portefeuilles.Depotbank = 'BIN' OR  Portefeuilles.Depotbank = 'BINB' ) ";
        $this->depotFondsCodeField = "binckCode";
        break;
      case "BINS":
        $this->depotWhereREK = "( Rekeningen.Depotbank = 'BINS'  ) ";
        $this->depotWherePORT = "( Portefeuilles.Depotbank = 'BINS' ) ";
        $this->depotFondsCodeField = "binckCode";
        break;
      case "SAXO":
        $this->depotWhereREK = "( Rekeningen.Depotbank = 'SAXO' OR  Rekeningen.Depotbank = 'SAXOB' ) ";
        $this->depotWherePORT = "( Portefeuilles.Depotbank = 'SAXO' OR  Portefeuilles.Depotbank = 'SAXOB' ) ";
        $this->depotFondsCodeField = "SAXOcode";
        break;
      case "FSE":
        $this->depotFondsCodeField = "bucketCode";
        break;
      case "FVL":
        $this->depotFondsCodeField = "FVLCode";
        break;
      case "FVLC":
        $this->depotFondsCodeField = "bucketCode";
        break;
      case "GIRO":
        $this->depotFondsCodeField = "giroCode";
        break;
      case "HHB":
        $this->depotFondsCodeField = "HHBcode";
        break;
      case "HSBC":
        $this->depotFondsCodeField = "HSBCcode";
        break;
      case "IB":
        $this->depotFondsCodeField = "IBcode";
        break;
      case "ING":
        $this->depotFondsCodeField = "INGCode";
        break;
      case "JUL BAER":
        $this->depotFondsCodeField = "JBcode";
        break;
      case "LOM":
        $this->depotFondsCodeField = "LomCode";
        break;
      case "LYNX":
        $this->depotFondsCodeField = "LYNXcode";
        break;
      case "OPT":
        $this->depotFondsCodeField = "optCode";
        break;
      case "PIC":
        $this->depotFondsCodeField = "PICcode";
        break;
      case "TGB":
        $this->depotFondsCodeField = "stroeveCode";
        break;
      case "UBP":
        $this->depotFondsCodeField = "UBPcode";
        break;
      case "RABO":
        $this->depotFondsCodeField = "raboCode";
        break;

      case "GMS":
        $this->depotFondsCodeField = "GScode";
      case "VP":
        $this->depotFondsCodeField = "VPcode";
      default:
        break;

    }

//    debug($this,"init class");

  }

  function changeDbReadServer($id)
  {
    $this->dbReadserver = $id;
  }

  function addToBankPile($data)
  {
    $bankItem = array();
    $bankItem["batch"]        = $this->batch;
    $bankItem["eigenaar"]     = $this->depotbank;
    $bankItem["portefeuille"] = $data["portefeuille"];

    $bankItem["valuta"]       = $data["valuta"];
    $bankItem["koers"]        = $data["koers"];
    $bankItem["memo"]         = $data["memo"];
    if ($data["isPositie"])
    {
      // onderstaande regel stond uit, 20-3-2020 weer aangezet
     // $this->bankPortefeuilles[] = $bankItem["portefeuille"];
      $bankItem["isPositie"]    = $data["isPositie"];
      $bankItem["ISIN"]         = $data["ISIN"];
      $bankItem["bankCode"]     = $data["bankCode"];
      $bankItem["aantal"]       = $data["aantal"];
      $bankItem["bankFonds"]    = str_replace("'","`",$data["fonds"]);
    }
    else
    {
      $this->bankRekeningNrs[] = $bankItem["portefeuille"].strtoupper($bankItem["valuta"]);
      $bankItem["isPositie"]    = false;   // cash
      $bankItem["aantal"]       = $data["bedrag"];
    }
    $this->bankPile[] = $bankItem;
  }

  function addToTrPile($record)
  {
    $now = date("Y-m-d H:i:s");
    $static = array(
      "add_user"            => $this->user,
      "add_date"            => $now,
      "change_user"         => $this->user,
      "change_date"         => $now,
      "vermogensbeheerder"  => $this->vb,
      "depotbank"           => $this->depotbank,
      "batch"               => $this->batch,
      "reconDatum"          => $this->reconDate,
      );

      $this->trPile[] = array_merge($static, $record);
  }

  function trPileToDb()
  {
    global $USR;
    $this->addToReconLog("trPileToDb -> start");
    $bankPortefeuillesRaw = array_unique($this->bankPortefeuilles);
    foreach ($bankPortefeuillesRaw as $b)
    {
      $p = explode("|", $b);
      if (!in_array($p[0], $bankPortefeuilles))
      {
        $bankPortefeuilles[] = $p[0];
      }


    }
    //debug($bankPortefeuilles);
    $bPortefeuilles = array();
    $dbR = new DB($this->dbReadserver);
    $db  = new DB();
    $db1 = new DB();

//    $query = "
//      SELECT
//        `Portefeuille`,
//        `Vermogensbeheerder`,
//        `Accountmanager`,
//        `Einddatum`
//      FROM
//        `Portefeuilles`
//      WHERE
//      `Portefeuille` IN ('".implode("','", $bankPortefeuilles)."')
//      ";
//    $db->executeQuery($query);
//    while ($rec = $db->nextRecord())
//    {
//      $bPortefeuilles[$rec["Portefeuille"]] = array(
//        "Vermogensbeheerder" => $rec["Vermogensbeheerder"],
//        "Accountmanager" => $rec["Accountmanager"],
//        "Einddatum" => $rec["Einddatum"],
//      );
//    }
//    debug($query);
//    debug($bPortefeuilles);

    $rows = array();
    $chunkCount = 0;
    $chunkIndex = 0;
    $chunkMaxSize = 10000;
    $queryStart = "
    INSERT INTO `tijdelijkeRecon` 
        (  `add_user`
          ,`add_date`
          ,`change_user`
          ,`change_date`
          ,`vermogensbeheerder`
          ,`depotbank`
          ,`batch` 
          ,`reconDatum` 
          ,`portefeuille` 
          ,`rekeningnummer` 
          ,`client` 
          ,`cashPositie` 
          ,`fonds` 
          ,`importCode` 
          ,`depotbankFondsCode` 
          ,`isinCode` 
          ,`valuta` 
          ,`positieBank` 
          ,`positieAirs` 
          ,`verschil` 
          ,`fondsCodeMatch` 
          ,`Einddatum` 
          ,`Accountmanager` 
          ,`positieAirsGisteren` 
          ,`koers` 
          ,`koersDatum` 
          ,`fondsImportcode` 
          ,`fileBankCode` 
          ,`opmerking` 
          ,`portefeuilleAirs`

       )
    VALUES
    ";


    foreach($this->trPile as $a)
    {
      if ($a["cashPositie"] == 1)
      {
        $a["positieAirsGisteren"] = (float)$this->cashPosYesterday[$a["rekeningnummer"]];
      }


      $chunkCount++;
      if ($chunkCount > $chunkMaxSize)
      {
        $chunkCount = 0;
        $chunkIndex++;
      }

      if ($a["vermogensbeheerder"] == "--" AND $bPortefeuilles[$a["portefeuille"]]["Vermogensbeheerder"] != "")
      {
        $a["vermogensbeheerder"] = $bPortefeuilles[$a["portefeuille"]]["Vermogensbeheerder"];
//        debug($a["vermogensbeheerder"],"nw port");
      }
      if ($a["Accountmanager"] == "--" AND $bPortefeuilles[$a["portefeuille"]]["Accountmanager"] != "")
      {
        $a["Accountmanager"] = $bPortefeuilles[$a["portefeuille"]]["Accountmanager"];
      }
      if ($a["Einddatum"] == "" AND $bPortefeuilles[$a["portefeuille"]]["Einddatum"] != "")
      {
        $a["Einddatum"] = $bPortefeuilles[$a["portefeuille"]]["Einddatum"];
      }
      $batch = $a["batch"];
      $rows[$chunkIndex][] = "(
      '{$a["add_user"]}', 
      '{$a["add_date"]}', 
      '{$a["change_user"]}', 
      '{$a["change_date"]}', 
      '{$a["vermogensbeheerder"]}', 
      '{$a["depotbank"]}', 
      '{$a["batch"]}', 
      '{$a["reconDatum"]}', 
      '{$a["portefeuille"]}', 
      '{$a["rekeningnummer"]}', 
      '{$a["client"]}', 
      '{$a["cashPositie"]}', 
      '{$a["fonds"]}', 
      '{$a["importCode"]}', 
      '{$a["depotbankFondsCode"]}', 
      '{$a["isinCode"]}', 
      '{$a["valuta"]}', 
      '{$a["positieBank"]}', 
      '{$a["positieAirs"]}', 
      '{$a["verschil"]}', 
      '{$a["fondsCodeMatch"]}', 
      '{$a["Einddatum"]}', 
      '{$a["Accountmanager"]}', 
      '{$a["positieAirsGisteren"]}', 
      '{$a["koers"]}', 
      '{$a["koersDatum"]}', 
      '{$a["fondsImportcode"]}', 
      '{$a["fileBankCode"]}', 
      '{$a["opmerking"]}',
      '{$a["afwPorteuille"]}'
      )";

    }
    for ($x=0; $x < count($rows); $x++)
    {
      $query = $queryStart;
      $query .= implode(",\n", $rows[$x]);
      $db->executeQuery($query);

    }
    $this->addToReconLog("trPileToDb -> klaar met vullen tijdelijkeRecon");

    $rows = array();  // destroy $rows

    // verrijken van recontabel velden met bekende AIRS gegevens
    // stukken
    $query = "
      SELECT 
        `tijdelijkeRecon`.id,
        `Portefeuilles`.`Client`,
        `Portefeuilles`.`Portefeuille`,
        `Portefeuilles`.`Vermogensbeheerder`,
        `Portefeuilles`.`Accountmanager`,
        `Portefeuilles`.`Einddatum`
      FROM 
        `tijdelijkeRecon` 
      JOIN `Portefeuilles` ON 
        (  `Portefeuilles`.`Portefeuille` = `tijdelijkeRecon`.`portefeuille` OR 
           `Portefeuilles`.`PortefeuilleDepotbank` = `tijdelijkeRecon`.`portefeuille`) AND
       {$this->depotWherePORT} 
      WHERE  
        `tijdelijkeRecon`.`batch` = '$batch' AND
        `tijdelijkeRecon`.`vermogensbeheerder` = '--'
    ";
    $dbR->executeQuery($query);

    while ($rec = $dbR->nextRecord())
    {

      $query = "
        UPDATE 
          `tijdelijkeRecon` 
        SET
          `client`              = '{$rec["Client"]}',
          `Einddatum`           = '{$rec["Einddatum"]}', 
          `Accountmanager`      = '{$rec["Accountmanager"]}',
          `vermogensbeheerder`  = '{$rec["Vermogensbeheerder"]}'
        WHERE 
          `id` = ".$rec["id"];

      $db1->executeQuery($query);
    }

    // verrijken van recontabel velden met bekende AIRS gegevens
    // geld
    $query = "
      SELECT 
        `tijdelijkeRecon`.id,
        `Portefeuilles`.`Client`,
        `Portefeuilles`.`Portefeuille`,
        `Portefeuilles`.`Vermogensbeheerder`,
        `Portefeuilles`.`Accountmanager`,
        `Portefeuilles`.`Einddatum`
      FROM 
        `tijdelijkeRecon` 
			JOIN Rekeningen  ON
        ( `Rekeningen`.`Rekening` = `tijdelijkeRecon`.`rekeningnummer` ) AND
          {$this->depotWhereREK}  AND
				   Rekeningen.consolidatie = 0
					 
      JOIN `Portefeuilles` ON 
          Rekeningen.Portefeuille = Portefeuilles.Portefeuille
      WHERE  
        `tijdelijkeRecon`.`batch` = '{$batch}' AND
        `tijdelijkeRecon`.`vermogensbeheerder` = '--' 
				    
    ";

    $dbR->executeQuery($query);
//    debug($query);
    while ($rec = $dbR->nextRecord())
    {

      $query = "
        UPDATE 
          `tijdelijkeRecon` 
        SET
          `client`              = '{$rec["Client"]}',
          `Einddatum`           = '{$rec["Einddatum"]}', 
          `Accountmanager`      = '{$rec["Accountmanager"]}',
          `vermogensbeheerder`  = '{$rec["Vermogensbeheerder"]}',
          `portefeuille`        = '{$rec["Portefeuille"]}' 
        WHERE 
          `id` = ".$rec["id"];

      $db1->executeQuery($query);
    }
    $where =  str_replace("Rekeningen","rekDepot", $this->depotWhereREK);
    $query = "
      SELECT 
        `tijdelijkeRecon`.id,
        `Portefeuilles`.`Client`,
        `Portefeuilles`.`Portefeuille`,
        `Portefeuilles`.`Vermogensbeheerder`,
        `Portefeuilles`.`Accountmanager`,
        `Portefeuilles`.`Einddatum`
      FROM 
        `tijdelijkeRecon` 
			JOIN (
			    SELECT * FROM Rekeningen WHERE consolidatie = 0 and RekeningDepotbank <> '') rekDepot ON
        ( rekDepot.`RekeningDepotbank` = `tijdelijkeRecon`.`rekeningnummer` AND
          $where )  
					 
      JOIN `Portefeuilles` ON 
          rekDepot.Portefeuille = Portefeuilles.Portefeuille
      WHERE  
        `tijdelijkeRecon`.`batch` = '{$batch}' AND
        `tijdelijkeRecon`.`vermogensbeheerder` = '--' 
				    
    ";

    $dbR->executeQuery($query);
//    debug($query);
    while ($rec = $dbR->nextRecord())
    {

      $query = "
        UPDATE 
          `tijdelijkeRecon` 
        SET
          `client`              = '{$rec["Client"]}',
          `Einddatum`           = '{$rec["Einddatum"]}', 
          `Accountmanager`      = '{$rec["Accountmanager"]}',
          `vermogensbeheerder`  = '{$rec["Vermogensbeheerder"]}',
          `portefeuille`        = '{$rec["Portefeuille"]}' 
        WHERE 
          `id` = ".$rec["id"];

      $db1->executeQuery($query);
    }
    $this->addToReconLog("trPileToDb -> klaar met verrijken data");

    $cfg = new AE_config();
    $field = "reconV3-import-status_".$USR;
    $cfg->putData($field, "import done");
  }

  function matchPositions($showAll=false)
  {
    $templateRow = "
    <tr>
      <td class='{tdCls}'>{c1}</td>
      <td class='{tdCls}'>{c2}</td>
      <td class='{tdCls}'>{c3}</td>
      <td class='{tdCls}'>{c4}</td>
      <td class='{tdCls}'>{c8}</td>
      <td class='{tdCls} ar'>{c5}</td>
      <td class='{tdCls} ar'>{c6}</td>
      <td class='{tdCls} ar {vCls}'>{c7}</td>
      <td class='{tdCls} '>{match}</td>
    </tr>
    ";

    $tmpl = new AE_template();
    $fmt = new AE_cls_formatter(",", ".");

    $tmpl->loadTemplateFromString($templateRow, "row");

    $data = array(
      "tdCls" => "tdH td1 ",
      "c1" => "portefeuille",
      "c2" => "reknr",
      "c3" => "client",
      "c4" => "fondscode",
      "c5" => "bank",
      "c6" => "airs",
      "c7" => "verschil",
      "c8" => "vermogensbeheerder",
      "match" => "match",
    );

    $out = "<table class>";
    $out .= $tmpl->parseBlock("row", $data);
    /////////////////////////////////////////////////
    // match op stukken

    $query = "
    SELECT
     'matched'as m,
			reconV3_bankPile.portefeuille as bPortefeuille,
      SUM(reconV3_bankPile.aantal) as bAantal,
      reconV3_bankPile.valuta as bValuta,
			reconV3_bankPile.airsCode as bAirsCode,
      reconV3_bankPile.bankCode as bFondscode,
      reconV3_bankPile.ISIN ,
      reconV3_bankPile.match,
      reconV3_airsPile.client as aClient,
      reconV3_airsPile.einddatum,
      reconV3_airsPile.accountmanager,
      reconV3_airsPile.fondsImportcode,
      reconV3_airsPile.portefeuille as aPortefeuille,
      reconV3_airsPile.valuta as aValuta,
      reconV3_airsPile.aantal as aAantal,
      reconV3_airsPile.airsCode as aAirsCode,
      reconV3_airsPile.vermogensbeheerder as vermogensbeheerder,
      reconV3_airsPile.afwPortefeuille as aAfwPorteuille,
      ROUND(reconV3_bankPile.aantal - reconV3_airsPile.aantal,8) as verschilMetBank,
      reconV3_airsPile.isPositie
    FROM
      reconV3_airsPile
    INNER JOIN reconV3_bankPile ON
        reconV3_bankPile.portefeuille = reconV3_airsPile.portefeuille AND
        reconV3_airsPile.batch = reconV3_bankPile.batch
    WHERE
      reconV3_bankPile.airsCode = reconV3_airsPile.airsCode AND
	    reconV3_airsPile.isPositie = 1  AND
      reconV3_airsPile.batch = '{$this->batch}'
    GROUP BY 
      reconV3_bankPile.portefeuille, 
      reconV3_bankPile.bankCode
    ";
//    debug($query);
    $db = new DB();
    $db->executeQuery($query);

    while ($rec = $db->nextRecord())
    {
      $verschil = $rec["bAantal"] - $rec["aAantal"];
      $this->addToTrPile(array(
        'portefeuille' => $rec["aPortefeuille"],
        'rekeningnummer' => $rec[""],
        'client' => $rec["aClient"],
        'cashPositie' => ($rec["isPositie"]) ? 0 : 1,
        'fonds' => $rec["aAirsCode"],
        'importCode' => $rec[""],
        'depotbankFondsCode' => $rec["bFondscode"],
        'isinCode' => $rec["ISIN"],
        'valuta' => $rec["bValuta"],
        'positieBank' => $rec["bAantal"],
        'positieAirs' => $rec["aAantal"],
        'verschil' => $verschil,
        'fondsCodeMatch' => $rec["match"],
        'Einddatum' => $rec["einddatum"],
        'Accountmanager' => $rec["accountmanager"],
        'positieAirsGisteren' => 0,
        'koers' => $rec[""],
        'koersDatum' => $rec[""],
        'fondsImportcode' => $rec["fondsImportcode"],
        'fileBankCode' => $rec["bFondscode"],
        'vermogensbeheerder' => $rec["vermogensbeheerder"],
        'opmerking' => $rec[""],
        'afwPorteuille' => $rec["aAfwPorteuille"]
      ));

      if ($showAll OR $rec["verschilMetBank"] != 0)
      {
        $data = array(
          "tdCls" => "td1 ",
          "c1" => $rec["aPortefeuille"],
          "c2" => $rec[""],
          "c3" => $rec["aClient"],
          "c4" => $rec["aAirsCode"],
          "c5" => $rec["bAantal"],
          "c6" => $rec["aAantal"],
          "c7" => $verschil,
          "c8" => $rec["vermogensbeheerder"],
          "vCls" => ($verschil != 0) ? "rood" : "",
          "match" => "Bank en AIRS",
        );
        $out .= $tmpl->parseBlock("row", $data);
      }
    }
//    debug($this->trPile);
//    $bPorts = array();
//    $aPorts = array();
//    debug($this->unmatchArray);
//    foreach ($this->unmatchArray["port"] as $k => $v)
//    {
//      $be = explode("|",$v);
//      debug($v, $k);
//      if ($v['bank'] != "")
//      {
//        $bPorts[] = $k;
//      }
//      if ($v['airs'] != "")
//      {
//        $aPorts[] = $k;
//      }
//    }
//debug($bPorts);
    //if (count($bPorts) > 0)
    {

    /////////////////////////////////////////////////
    // match stukken alleen BANK
      $query = "
    SELECT
		  'alleen Bank' as m,
      reconV3_bankPile.portefeuille as bPortefeuille,
      ROUND(reconV3_bankPile.aantal,8) as bAantal,
      reconV3_bankPile.valuta as bValuta,
      reconV3_bankPile.bankCode as bFondscode,
			reconV3_bankPile.airsCode as bAirsCode,
      reconV3_bankPile.ISIN ,
      reconV3_bankPile.match,
      reconV3_bankPile.bankFonds,
      reconV3_airsPile.client as aClient,
      reconV3_airsPile.einddatum,
      reconV3_airsPile.accountmanager,
      reconV3_airsPile.fondsImportcode,
      reconV3_airsPile.portefeuille as aPortefeuille,
      reconV3_airsPile.valuta as aValuta,
      reconV3_airsPile.aantal as aAantal,
      reconV3_airsPile.airsCode as aAirsCode,
      reconV3_airsPile.vermogensbeheerder as vermogensbeheerder,
      reconV3_airsPile.afwPortefeuille as aAfwPorteuille,
      reconV3_airsPile.isPositie
    FROM
      reconV3_bankPile
    left JOIN
      reconV3_airsPile ON
      reconV3_airsPile.portefeuille = reconV3_bankPile.portefeuille AND
      reconV3_airsPile.batch = reconV3_bankPile.batch AND
			reconV3_airsPile.airsCode  = reconV3_bankPile.airsCode AND
			reconV3_airsPile.isPositie = reconV3_bankPile.isPositie
    WHERE

	    reconV3_bankPile.isPositie = 1  AND
      reconV3_bankPile.batch = '{$this->batch}' AND 
			reconV3_airsPile.client is null 
    ";
//      debug($query);
      $db = new DB();
      $db->executeQuery($query);

      while ($rec = $db->nextRecord())
      {
        if ($rec["portefeuille"] == "244026")
        {
          debug($rec,"nextrecord");
        }

        $this->addToTrPile(array(
          'portefeuille'        => $rec["bPortefeuille"],
          'cashPositie'         => 0,
          'fonds'               => $rec["bankFonds"],
          'importCode'          => $rec[""],
          'depotbankFondsCode'  => $rec["bFondscode"],
          'isinCode'            => $rec["ISIN"],
          'valuta'              => $rec["bValuta"],
          'positieBank'         => $rec["bAantal"],
          'vermogensbeheerder'  => "--",
          'Accountmanager'      => "--",
          'verschil'            => $rec["bAantal"],
          'fondsCodeMatch'      => "alleen bank",
          'afwPorteuille'       => $rec["aAfwPorteuille"],
//          'koers' => $rec[""],
//          'koersDatum' => $rec[""],
          'fileBankCode'        => mysql_real_escape_string($rec["bFondscode"]),
        ));

        $data = array(
          "tdCls" => "td1 ",
          "c1" => $rec["bPortefeuille"],
          "c2" => "",
          "c3" => "",
          "c4" => $rec["bFondscode"],
          "c5" => $rec["bAantal"],
          "c6" => 0,
          "c7" => $rec["bAantal"],
          "c8" => $rec["vermogensbeheerder"],
          "vCls" => "rood",
          "match" => "alleen bank",
        );
        $out .= $tmpl->parseBlock("row", $data);
      }

    }
//    if (count($aPorts) > 0)
    {
      /////////////////////////////////////////////////
      // match stukken alleen AIRS
      $query = "
    SELECT
     'allen AIRS'as m,
      reconV3_bankPile.portefeuille as bPortefeuille,
      reconV3_bankPile.aantal as bAantal,
      reconV3_bankPile.valuta as bValuta,
			reconV3_bankPile.airsCode as bAirsCode,
      reconV3_bankPile.bankCode as bFondscode,
      reconV3_airsPile.ISIN ,
      reconV3_bankPile.match,
      reconV3_airsPile.client as aClient,
      reconV3_airsPile.einddatum,
      reconV3_airsPile.accountmanager,
      reconV3_airsPile.fondsImportcode,
      reconV3_airsPile.portefeuille as aPortefeuille,
      reconV3_airsPile.valuta as aValuta,
      ROUND(reconV3_airsPile.aantal,8) as aAantal,
      reconV3_airsPile.airsCode as aAirsCode,
      reconV3_airsPile.vermogensbeheerder as vermogensbeheerder,
      reconV3_airsPile.afwPortefeuille as aAfwPorteuille,
      reconV3_airsPile.isPositie
    FROM
      reconV3_airsPile
    left JOIN
      reconV3_bankPile ON
      reconV3_bankPile.portefeuille = reconV3_airsPile.portefeuille AND
      reconV3_airsPile.batch = reconV3_bankPile.batch AND
			reconV3_bankPile.airsCode = reconV3_airsPile.airsCode AND
			reconV3_airsPile.isPositie = reconV3_bankPile.isPositie

    WHERE

	    reconV3_airsPile.isPositie = 1  AND
      reconV3_airsPile.batch = '{$this->batch}' AND
			reconV3_bankPile.valuta is null 
      ";
//          debug($query);
      $db = new DB();
      $db->executeQuery($query);

      while ($rec = $db->nextRecord())
      {
//debug($rec);
        $this->addToTrPile(array(
          'portefeuille'        => $rec["aPortefeuille"],
          'client'              => $rec["aClient"],
          'cashPositie'         => 0,
          'fonds'               => $rec["aAirsCode"],
          'importCode'          => $rec["fondsImportcode"],
          'isinCode'            => $rec["ISIN"],
          'valuta'              => $rec["aValuta"],
          'positieBank'         => 0,
          'positieAirs'         => $rec["aAantal"],
          'verschil'            => $rec["aAantal"] * -1,
          'fondsCodeMatch'      => "alleen AIRS",
          'Einddatum'           => $rec["einddatum"],
          'Accountmanager'      => $rec["accountmanager"],
          'positieAirsGisteren' => 0,
          'fondsImportcode'     => $rec["fondsImportcode"],
          'vermogensbeheerder'  => $rec["vermogensbeheerder"],
          'afwPorteuille'       => $rec["aAfwPorteuille"]
        ));

        $data = array(
          "tdCls" => "td1 ",
          "c1" => $rec["aPortefeuille"],
          "c2" => $rec[""],
          "c3" => $rec["aClient"],
          "c4" => $rec["aAirsCode"],
          "c5" => 0,
          "c6" => $rec["aAantal"],
          "c7" => $rec["aAantal"],
          "c8" => $rec["vermogensbeheerder"],
          "vCls" => "rood",
          "match" => "alleen AIRS",
        );
        $out .= $tmpl->parseBlock("row", $data);
      }
    }

    /////////////////////////////////////////////////
    // match geld

    $query = " SELECT
      CONCAT(reconV3_bankPile.portefeuille,reconV3_bankPile.valuta) as bRekening,
      reconV3_bankPile.portefeuille as bPortefeuille,
      reconV3_bankPile.aantal as bAantal,
      reconV3_bankPile.valuta as bValuta,
      reconV3_airsPile.portefeuille as aPortefeuille,
      reconV3_airsPile.client as aClient,
      reconV3_airsPile.rekening as aRekening,
      reconV3_airsPile.einddatum,
      reconV3_airsPile.accountmanager,
      reconV3_airsPile.valuta as aValuta,
      reconV3_airsPile.totaal as aTotaal,
      reconV3_airsPile.vermogensbeheerder,
      ROUND(reconV3_bankPile.aantal - reconV3_airsPile.totaal,2) as verschilMetBank,
      reconV3_airsPile.isPositie
    FROM
      reconV3_airsPile
    INNER JOIN
      reconV3_bankPile ON
          CONCAT(reconV3_bankPile.portefeuille,reconV3_bankPile.valuta) = reconV3_airsPile.rekening AND
					reconV3_airsPile.isPositie = reconV3_bankPile.isPositie AND 
					reconV3_airsPile.batch = reconV3_bankPile.batch 
    WHERE
 	    reconV3_airsPile.batch = '{$this->batch}' AND
	    reconV3_airsPile.isPositie = 0 ";
//    debug($query);
    $db = new DB();
    $db->executeQuery($query);

    while ($rec = $db->nextRecord()) {

      $this->addToTrPile(array(
        'portefeuille' => $rec["aPortefeuille"],
        'afwPorteuille' => $rec["aPortefeuille"],
        'rekeningnummer' => $rec["bRekening"],
        'client' => $rec["aClient"],
        'cashPositie' => 1,
        'valuta' => $rec["bValuta"],
        'positieBank' => $rec["bAantal"],
        'positieAirs' => $rec["aTotaal"],
        'verschil' => $rec["verschilMetBank"],
        'fondsCodeMatch' => $rec["match"],
        'Einddatum' => $rec["einddatum"],
        'Accountmanager' => $rec["accountmanager"],
        'vermogensbeheerder' => $rec["vermogensbeheerder"],
        'fondsCodeMatch'      => "op rekening",
        'positieAirsGisteren' => 0,
      ));

      if ($showAll OR $rec["verschilMetBank"] != 0)
      {
        $data = array(
          "tdCls" => "td1 ",
          "c1" => "",
          "c2" => $rec["aRekening"],
          "c3" => $rec["aClient"],
          "c4" => "",
          "c5" => $fmt->format("@N{.2}", $rec["bAantal"]),
          "c6" => $fmt->format("@N{.2}", $rec["aTotaal"]),
          "c7" => $fmt->format("@N{.2}", $rec["verschilMetBank"]),
          "c8" => $rec["vermogensbeheerder"],
          "vCls" => ($rec["verschilMetBank"] != 0) ? "rood" : "",
          "match" => "Bank en AIRS",
        );
        $out .= $tmpl->parseBlock("row", $data);
      }
    }

/////////////////////////////////////////////
    $bReknrs = array();
    $aReknrs = array();

    foreach ($this->unmatchArray["reknr"] as $k => $v) {
      if ($v == "bank") {
        $bReknrs[] = $k;
      } else {
        $aReknrs[] = $k;
      }
    }

    if (count($bReknrs) > 0)
    {
      /////////////////////////////////////////////////
      // match geld alleen BANK
      $query = "
        SELECT
          CONCAT(reconV3_bankPile.portefeuille,reconV3_bankPile.valuta) as bRekening,
          reconV3_bankPile.portefeuille,
          ROUND(reconV3_bankPile.aantal,2) as bAantal,
          reconV3_bankPile.valuta as bValuta,
          reconV3_bankPile.isPositie,
          Rekeningen.Rekening as airsRekening
        FROM
          reconV3_bankPile
        LEFT JOIN
          reconV3_airsPile ON
          reconV3_airsPile.rekening = CONCAT(reconV3_bankPile.portefeuille,reconV3_bankPile.valuta) AND
          reconV3_airsPile.batch = reconV3_bankPile.batch AND
					reconV3_airsPile.isPositie = reconV3_bankPile.isPositie
        LEFT JOIN 
          Rekeningen ON Rekeningen.Rekening = CONCAT(reconV3_bankPile.portefeuille,reconV3_bankPile.valuta) AND
					Rekeningen.Depotbank = '{$this->depotbank}'
        WHERE
          reconV3_bankPile.isPositie = 0 AND
          reconV3_bankPile.batch = '{$this->batch}' AND
          reconV3_airsPile.client is null AND
					CONCAT(reconV3_bankPile.portefeuille,reconV3_bankPile.valuta) <> ''
        GROUP BY
					bRekening  
    ";
      //debug($query);

      $db = new DB();
      $db->executeQuery($query);
      $bankrek = array();
      while ($rec = $db->nextRecord())
      {
        $match = ($rec["bRekening"] != $rec["airsRekening"])?"alleen bank":"alleen bank/(verv.) airs";

        $this->addToTrPile(array(
          'portefeuille' => $rec["portefeuille"],
          'afwPorteuille' => $rec["portefeuille"],
          'rekeningnummer' => $rec["bRekening"],
          'cashPositie' => 1,
          'valuta' => $rec["bValuta"],
          'positieBank' => $rec["bAantal"],
          'positieAirs' => 0,
          'verschil' => $rec["bAantal"],
          'fondsCodeMatch' => $match,
          'positieAirsGisteren' => 0,
          'vermogensbeheerder'  => "--",
          'Accountmanager'      => "--",
        ));

        $data = array(
          "tdCls" => "td1 ",
          "c1" => $rec["portefeuille"],
          "c2" => $rec["bRekening"],
          "c3" => "",
          "c4" => "",
          "c5" => $fmt->format("@N{.2}", $rec["bAantal"]),
          "c6" => $fmt->format("@N{.2}"),
          "c7" => $fmt->format("@N{.2}", $rec["bAantal"]),
          "vCls" => "rood",
          "match" => "alleen bank",
        );
        $out .= $tmpl->parseBlock("row", $data);
      }


    }
    if (count($aReknrs) > 0)
    {

      /////////////////////////////////////////////////
      // match geld alleen AIRS
      $query = "
        SELECT
          reconV3_airsPile.portefeuille,
          reconV3_airsPile.client as aClient,
          reconV3_airsPile.rekening as aRekening,
          reconV3_airsPile.valuta as aValuta,
          ROUND(reconV3_airsPile.totaal,2) as aTotaal,
          reconV3_airsPile.isPositie,
          reconV3_airsPile.accountmanager,
          reconV3_airsPile.einddatum,
          reconV3_airsPile.vermogensbeheerder
        FROM
          reconV3_airsPile
        LEFT JOIN
          reconV3_bankPile ON
          reconV3_airsPile.rekening = CONCAT(reconV3_bankPile.portefeuille,reconV3_bankPile.valuta) AND
          reconV3_airsPile.batch = reconV3_bankPile.batch AND
    			reconV3_airsPile.isPositie = reconV3_bankPile.isPositie
        WHERE
          reconV3_airsPile.isPositie = 0 AND
          reconV3_airsPile.batch = '{$this->batch}' AND
          reconV3_bankPile.valuta is null
          
    ";
//      debug($query);
      $db = new DB();
      $db->executeQuery($query);

      while ($rec = $db->nextRecord())
      {

        $this->addToTrPile(array(
          'portefeuille' => $rec["portefeuille"],
          'afwPorteuille' => $rec["portefeuille"],
          'client' => $rec["aClient"],
          'rekeningnummer' => $rec["aRekening"],
          'cashPositie' => 1,
          'valuta' => $rec["aValuta"],
          'positieBank' => 0,
          'positieAirs' => $rec["aTotaal"],
          'verschil' => $rec["aTotaal"] * -1,
          'Einddatum' => $rec["einddatum"],
          'vermogensbeheerder' => $rec["vermogensbeheerder"],
          'Accountmanager'      => $rec["accountmanager"],
          'fondsCodeMatch' => "alleen AIRS",
          'positieAirsGisteren' => 0,
        ));
        $data = array(
          "tdCls" => "td1 ",
          "c1" => $rec["portefeuille"],
          "c2" => $rec["aRekening"],
          "c3" => $rec["aClient"],
          "c4" => "",
          "c5" => $fmt->format("@N{.2}"),
          "c6" => $fmt->format("@N{.2}", $rec["aTotaal"]),
          "c7" => $fmt->format("@N{.2}", $rec["aTotaal"]),
          "c8" => $rec["vermogensbeheerder"],
          "vCls" => "rood",
          "match" => "alleen AIRS",
        );

        $out .= $tmpl->parseBlock("row", $data);
      }

    }
/////////////////////////////////////////////


    $out .= "</table>";
    return $out;
  }


  function bankPileToDB()
  {

    $db = new DB();
    $rows = array();
    $date = date("Y-m-d H:i:s");
    $query = "
    INSERT INTO `reconV3_bankPile` 
        (add_user,add_date,batch,eigenaar,portefeuille,bankCode,ISIN,valuta,aantal,koers,isPositie,memo,bankFonds )
    VALUES
    ";
//    debug($this->bankPile);
//    debug($this->getBankPortefeuilles());


    foreach($this->bankPile as $b)
    {
      $rows[] = "('{$this->user}', '{$date}', '{$b["batch"]}', '{$b["eigenaar"]}', '{$b["portefeuille"]}', '{$b["bankCode"]}', '{$b["ISIN"]}', '{$b["valuta"]}', '{$b["aantal"]}', '{$b["koers"]}', '{$b["isPositie"]}', '".mysql_real_escape_string($b["memo"])."','{$b["bankFonds"]}')";
    }

    $query .= implode(",\n", $rows);
    $rows = array();
    $db->executeQuery($query);
    //debug($query);
    $query = "";

  }

  function airsPileToDB()
  {
    $cashPile = array();
    $this->airsPile = array();
    $this->getAirsPostions();

    $db = new DB();
    $rows = array();
    $date = date("Y-m-d H:i:s");
    $query = "
    INSERT INTO `reconV3_airsPile` 
        (add_user,add_date,batch,eigenaar,portefeuille,vermogensbeheerder,client,afwPortefeuille,airsCode,aantal,isPositie,einddatum,accountmanager,portDepotbank,fondsImportcode,ISIN,valuta )
    VALUES
    ";

    foreach($this->airsPile as $a)
    {
      $rows[] = "('{$this->user}', '{$date}', '{$a["batch"]}', '{$a["eigenaar"]}', '{$a["portefeuille"]}', '{$a["vermogensbeheerder"]}', '{$a["client"]}','{$a["orgPortefeuille"]}', '{$a["airsCode"]}', '{$a["aantal"]}', '{$a["isPositie"]}', '{$a["einddatum"]}', '{$a["accountmanager"]}', '{$a["portDepotbank"]}', '{$a["fondsImportcode"]}', '{$a["ISIN"]}', '{$a["valuta"]}')";
    }

    $query .= implode(",\n", $rows);
//    debug($query);
    $rows = array();
    $db->executeQuery($query);

    $this->getAirsCash();
    $rows = array();
    $date = date("Y-m-d H:i:s");
    $query = "
    INSERT INTO `reconV3_airsPile` 
        (add_user,add_date,isPositie,batch,client,portefeuille,portDepotbank,depotbank,rekening,afwRekening,vermogensbeheerder,valuta,totaal,einddatum )
    VALUES
    ";

    foreach($this->airsCashPile as $a)
    {
      $rows[] = "('{$this->user}', '{$date}', 0, '{$a["batch"]}', '{$a["client"]}', '{$a["portefeuille"]}', '{$a["portDepotbank"]}', '{$a["depotbank"]}', '{$a["rekening"]}', '{$a["afwRekening"]}', '{$a["vermogensbeheerder"]}', '{$a["valuta"]}', '{$a["totaal"]}', '{$a["einddatum"]}')";
    }
    $query .= implode(",\n", $rows);
    $rows = array();
    $db->executeQuery($query);

  }

  function getAirsPortefeuilles($theArray=true)
  {
    $this->airsPortefeuilles = array_unique($this->airsPortefeuilles);
    return ($theArray)?$this->airsPortefeuilles:count($this->airsPortefeuilles);
  }

  function getAirsReknrs($theArray=true)
  {
    $this->airsRekeningNrs = array_unique($this->airsRekeningNrs);
    return ($theArray)?$this->airsRekeningNrs:count($this->airsRekeningNrs);
  }

  function getBankPortefeuilles($theArray=true)
  {
    $this->bankPortefeuilles = array_unique($this->bankPortefeuilles);
    return ($theArray)?$this->bankPortefeuilles:count($this->bankPortefeuilles);
  }

  function getBankReknrs($theArray=true)
  {
    $this->bankRekeningNrs = array_unique($this->bankRekeningNrs);
    return ($theArray)?$this->bankRekeningNrs:count($this->bankRekeningNrs);
  }

  function findUnmatched()
  {
    $aPortStack = $this->getAirsPortefeuilles();  // ontdubbel en sorteer
    $bPortStack = $this->getBankPortefeuilles();
//    debug($aPortStack);
//    debug($bPortStack);
    foreach ( $bPortStack as $bPort)
    {
//      debug($bPort);
      if (!in_array($bPort, $aPortStack))
      {
        $bp = explode("|",$bPort);
//        debug($bp);
        $this->unmatchArray["port"][$bp[0]]["bank"] = "{$bp[1]}";
      }
    }
    foreach ( $aPortStack as $aPort)
    {
      if (!in_array($aPort, $bPortStack))
      {
        $ap = explode("|",$aPort);
        $this->unmatchArray["port"][$ap[0]]["airs"] = "{$ap[1]}";
      }
    }
//debug($this->unmatchArray);
    $aReknrStack = $this->getAirsReknrs();
    $bReknrStack = $this->getBankReknrs();
    foreach ( $bReknrStack as $bReknr)
    {
      if (!in_array($bReknr, $aReknrStack))
      {
        $this->unmatchArray["reknr"][$bReknr] = "bank";
      }
    }
    foreach ( $aReknrStack as $aReknr)
    {

      if (!in_array($aReknr, $bReknrStack))
      {
        $this->unmatchArray["reknr"][$aReknr] = "airs";
      }
    }


  }

  function matchCashPositions($showAll=false)
  {
    $templateRow = "
    <tr>
      <td class='{tdCls}'>{c1}</td>
      <td class='{tdCls}'>{c2}</td>
      <td class='{tdCls}'>{c3}</td>
      <td class='{tdCls}'>{c4}</td>
      <td class='{tdCls}'>{c8}</td>
      <td class='{tdCls} ar'>{c5}</td>
      <td class='{tdCls} ar'>{c6}</td>
      <td class='{tdCls} ar {vCls}'>{c7}</td>
      <td class='{tdCls} '>{match}</td>
    </tr>
    ";

    $tmpl = new AE_template();
    $fmt = new AE_cls_formatter(",", ".");

    $tmpl->loadTemplateFromString($templateRow, "row");

    $data = array(
      "tdCls" => "tdH td1 ",
      "c1" => "portefeuille",
      "c2" => "reknr",
      "c3" => "client",
      "c4" => "fondscode",
      "c5" => "bank",
      "c6" => "airs",
      "c7" => "verschil",
      "c8" => "vermogensbeheerder",
      "match" => "match",
    );

    $out = "<table class>";
    $out .= $tmpl->parseBlock("row", $data);

    /////////////////////////////////////////////////
    // match geld

    $query = " SELECT
      CONCAT(reconV3_bankPile.portefeuille,reconV3_bankPile.valuta) as bRekening,
      reconV3_bankPile.portefeuille as bPortefeuille,
      reconV3_bankPile.aantal as bAantal,
      reconV3_bankPile.valuta as bValuta,
      reconV3_airsPile.portefeuille as aPortefeuille,
      reconV3_airsPile.client as aClient,
      reconV3_airsPile.rekening as aRekening,
      reconV3_airsPile.einddatum,
      reconV3_airsPile.accountmanager,
      reconV3_airsPile.valuta as aValuta,
      reconV3_airsPile.totaal as aTotaal,
      reconV3_airsPile.vermogensbeheerder,
      ROUND(reconV3_bankPile.aantal - reconV3_airsPile.totaal,2) as verschilMetBank,
      reconV3_airsPile.isPositie
    FROM
      reconV3_airsPile
    INNER JOIN
      reconV3_bankPile ON
          CONCAT(reconV3_bankPile.portefeuille,reconV3_bankPile.valuta) = reconV3_airsPile.rekening AND
					reconV3_airsPile.isPositie = reconV3_bankPile.isPositie AND 
					reconV3_airsPile.batch = reconV3_bankPile.batch 
    WHERE
 	    reconV3_airsPile.batch = '{$this->batch}' AND
	    reconV3_airsPile.isPositie = 0 ";
//    debug($query);
    $db = new DB();
    $db->executeQuery($query);

    while ($rec = $db->nextRecord()) {

      $this->addToTrPile(array(
                           'portefeuille' => $rec["aPortefeuille"],
                           'afwPorteuille' => $rec["aPortefeuille"],
                           'rekeningnummer' => $rec["bRekening"],
                           'client' => $rec["aClient"],
                           'cashPositie' => 1,
                           'valuta' => $rec["bValuta"],
                           'positieBank' => $rec["bAantal"],
                           'positieAirs' => $rec["aTotaal"],
                           'verschil' => $rec["verschilMetBank"],
                           'fondsCodeMatch' => $rec["match"],
                           'Einddatum' => $rec["einddatum"],
                           'Accountmanager' => $rec["accountmanager"],
                           'vermogensbeheerder' => $rec["vermogensbeheerder"],
                           'fondsCodeMatch'      => "op rekening",
                           'positieAirsGisteren' => 0,
                         ));

      if ($showAll OR $rec["verschilMetBank"] != 0)
      {
        $data = array(
          "tdCls" => "td1 ",
          "c1" => "",
          "c2" => $rec["aRekening"],
          "c3" => $rec["aClient"],
          "c4" => "",
          "c5" => $fmt->format("@N{.2}", $rec["bAantal"]),
          "c6" => $fmt->format("@N{.2}", $rec["aTotaal"]),
          "c7" => $fmt->format("@N{.2}", $rec["verschilMetBank"]),
          "c8" => $rec["vermogensbeheerder"],
          "vCls" => ($rec["verschilMetBank"] != 0) ? "rood" : "",
          "match" => "Bank en AIRS",
        );
        $out .= $tmpl->parseBlock("row", $data);
      }
    }

/////////////////////////////////////////////
    $bReknrs = array();
    $aReknrs = array();

    foreach ($this->unmatchArray["reknr"] as $k => $v) {
      if ($v == "bank") {
        $bReknrs[] = $k;
      } else {
        $aReknrs[] = $k;
      }
    }

    if (count($bReknrs) > 0)
    {
      /////////////////////////////////////////////////
      // match geld alleen BANK
      $query = "
        SELECT
          CONCAT(reconV3_bankPile.portefeuille,reconV3_bankPile.valuta) as bRekening,
          reconV3_bankPile.portefeuille,
          ROUND(reconV3_bankPile.aantal,2) as bAantal,
          reconV3_bankPile.valuta as bValuta,
          reconV3_bankPile.isPositie,
          Rekeningen.Rekening as airsRekening
        FROM
          reconV3_bankPile
        LEFT JOIN
          reconV3_airsPile ON
          reconV3_airsPile.rekening = CONCAT(reconV3_bankPile.portefeuille,reconV3_bankPile.valuta) AND
          reconV3_airsPile.batch = reconV3_bankPile.batch AND
					reconV3_airsPile.isPositie = reconV3_bankPile.isPositie
        LEFT JOIN 
          Rekeningen ON Rekeningen.Rekening = CONCAT(reconV3_bankPile.portefeuille,reconV3_bankPile.valuta) AND
					Rekeningen.Depotbank = '{$this->depotbank}'
        WHERE
          reconV3_bankPile.isPositie = 0 AND
          reconV3_bankPile.batch = '{$this->batch}' AND
          reconV3_airsPile.client is null AND
					CONCAT(reconV3_bankPile.portefeuille,reconV3_bankPile.valuta) <> ''
        GROUP BY
					bRekening  
    ";
      //debug($query);

      $db = new DB();
      $db->executeQuery($query);
      $bankrek = array();
      while ($rec = $db->nextRecord())
      {
        $match = ($rec["bRekening"] != $rec["airsRekening"])?"alleen bank":"alleen bank/(verv.) airs";

        $this->addToTrPile(array(
                             'portefeuille' => $rec["portefeuille"],
                             'afwPorteuille' => $rec["portefeuille"],
                             'rekeningnummer' => $rec["bRekening"],
                             'cashPositie' => 1,
                             'valuta' => $rec["bValuta"],
                             'positieBank' => $rec["bAantal"],
                             'positieAirs' => 0,
                             'verschil' => $rec["bAantal"],
                             'fondsCodeMatch' => $match,
                             'positieAirsGisteren' => 0,
                             'vermogensbeheerder'  => "--",
                             'Accountmanager'      => "--",
                           ));

        $data = array(
          "tdCls" => "td1 ",
          "c1" => $rec["portefeuille"],
          "c2" => $rec["bRekening"],
          "c3" => "",
          "c4" => "",
          "c5" => $fmt->format("@N{.2}", $rec["bAantal"]),
          "c6" => $fmt->format("@N{.2}"),
          "c7" => $fmt->format("@N{.2}", $rec["bAantal"]),
          "vCls" => "rood",
          "match" => "alleen bank",
        );
        $out .= $tmpl->parseBlock("row", $data);
      }


    }
    if (count($aReknrs) > 0)
    {

      /////////////////////////////////////////////////
      // match geld alleen AIRS
      $query = "
        SELECT
          reconV3_airsPile.portefeuille,
          reconV3_airsPile.client as aClient,
          reconV3_airsPile.rekening as aRekening,
          reconV3_airsPile.valuta as aValuta,
          ROUND(reconV3_airsPile.totaal,2) as aTotaal,
          reconV3_airsPile.isPositie,
          reconV3_airsPile.accountmanager,
          reconV3_airsPile.einddatum,
          reconV3_airsPile.vermogensbeheerder
        FROM
          reconV3_airsPile
        LEFT JOIN
          reconV3_bankPile ON
          reconV3_airsPile.rekening = CONCAT(reconV3_bankPile.portefeuille,reconV3_bankPile.valuta) AND
          reconV3_airsPile.batch = reconV3_bankPile.batch AND
    			reconV3_airsPile.isPositie = reconV3_bankPile.isPositie
        WHERE
          reconV3_airsPile.isPositie = 0 AND
          reconV3_airsPile.batch = '{$this->batch}' AND
          reconV3_bankPile.valuta is null
          
    ";
//      debug($query);
      $db = new DB();
      $db->executeQuery($query);

      while ($rec = $db->nextRecord())
      {

        $this->addToTrPile(array(
                             'portefeuille' => $rec["portefeuille"],
                             'afwPorteuille' => $rec["portefeuille"],
                             'client' => $rec["aClient"],
                             'rekeningnummer' => $rec["aRekening"],
                             'cashPositie' => 1,
                             'valuta' => $rec["aValuta"],
                             'positieBank' => 0,
                             'positieAirs' => $rec["aTotaal"],
                             'verschil' => $rec["aTotaal"] * -1,
                             'Einddatum' => $rec["einddatum"],
                             'vermogensbeheerder' => $rec["vermogensbeheerder"],
                             'Accountmanager'      => $rec["accountmanager"],
                             'fondsCodeMatch' => "alleen AIRS",
                             'positieAirsGisteren' => 0,
                           ));
        $data = array(
          "tdCls" => "td1 ",
          "c1" => $rec["portefeuille"],
          "c2" => $rec["aRekening"],
          "c3" => $rec["aClient"],
          "c4" => "",
          "c5" => $fmt->format("@N{.2}"),
          "c6" => $fmt->format("@N{.2}", $rec["aTotaal"]),
          "c7" => $fmt->format("@N{.2}", $rec["aTotaal"]),
          "c8" => $rec["vermogensbeheerder"],
          "vCls" => "rood",
          "match" => "alleen AIRS",
        );

        $out .= $tmpl->parseBlock("row", $data);
      }

    }
/////////////////////////////////////////////


    $out .= "</table>";
    return $out;
  }



  function getAirsCash()
  {
    $db = new DB($this->dbReadserver);

    $order = "ORDER BY Rekeningen.Portefeuille, Rekeningmutaties.Rekening, Rekeningen.Valuta";
    $query = "
    SELECT 
      Rekeningen.Portefeuille as portefeuille,
			Portefeuilles.Depotbank as portDepotbank,
      Case When 
          Rekeningen.RekeningDepotbank <> '' 
      then 
          Rekeningen.RekeningDepotbank 
      else
          Rekeningen.Rekening  
      end as `rekening`,
      Rekeningen.Rekening as orgRekening,     
      Portefeuilles.Client as client,
      Portefeuilles.Einddatum as einddatum,
      Portefeuilles.Accountmanager as accountmanager,
      Portefeuilles.Depotbank as portDepotbank,
			Rekeningen.Depotbank as depotbank,
      Rekeningmutaties.Rekening as orgRekening,
			Rekeningen.RekeningDepotbank as afwRekening,
			Portefeuilles.Vermogensbeheerder as vermogensbeheerder,
      Rekeningen.Valuta as valuta,
      Rekeningen.Inactief as inactief,
      ROUND(SUM(Rekeningmutaties.Bedrag),2) as totaal
    FROM 
      Rekeningmutaties, Rekeningen
		LEFT JOIN
		  Portefeuilles ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
    WHERE
    	Rekeningmutaties.Rekening = Rekeningen.Rekening AND
    	Rekeningmutaties.boekdatum >= '".substr($this->reconDate,0,4)."-01-01' AND
    	Rekeningmutaties.boekdatum <= '{$this->reconDate}' AND
      Rekeningen.Memoriaal = 0 AND
      Rekeningen.consolidatie = 0 AND
			Portefeuilles.Vermogensbeheerder IN {$this->vbQueryString} AND 
      {$this->depotWhereREK}
    GROUP BY 
      Portefeuilles.Vermogensbeheerder,
			Portefeuilles.Depotbank,
			Rekeningen.Depotbank,
      Rekeningen.Valuta, 
      rekening
    HAVING 
			(
			  (
			    Rekeningen.Inactief = 0 AND
			    Portefeuilles.Einddatum > NOW()  
			  )
			  OR 
			  (
			    totaal != 0
			  ) 
			) 
			
    
      ";
//debug($query, "getAirsCash()");
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      if (substr($rec["rekening"], -3) != "MEM")
      {
        $this->airsRekeningNrs[] = $rec["rekening"];
      }


      $rec["isPositie"] = false;
      $rec["batch"] = $this->batch;
      $rec["eigenaar"] = $this->depotbank;
      $this->airsCashPile[] = $rec;

    }

      /// positie gisteren
//      $cashPosYesterday
    $dParts = explode("-", $this->reconDate);
    $yesterday = date("Y-m-d",mktime(1,0,0,$dParts[1],$dParts[2],$dParts[0]) - 86400);

    $query = "
      SELECT
        Rekeningmutaties.Rekening as rekening,
        ROUND(SUM(Rekeningmutaties.Bedrag),2) as totaal
      FROM
        Rekeningmutaties, Rekeningen
      LEFT JOIN
        Portefeuilles ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
      WHERE
        Rekeningmutaties.Rekening = Rekeningen.Rekening AND
        Rekeningmutaties.boekdatum >= '".substr($yesterday,0,4)."-01-01' AND
        Rekeningmutaties.boekdatum <= '{$yesterday}' AND
  
        Rekeningen.consolidatie = 0 AND
        Portefeuilles.Vermogensbeheerder IN {$this->vbQueryString} AND
        {$this->depotWhereREK}
      GROUP BY
        Rekeningmutaties.Rekening
  
        ";
//debug($query, "yesteradag");
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $this->cashPosYesterday[$rec["rekening"]] = $rec["totaal"];
    }
//    debug($this->cashPosYesterday);
  }


  function findUnmatchedCash()
  {
    $aReknrStack = $this->getAirsReknrs();
    $bReknrStack = $this->getBankReknrs();
    foreach ( $bReknrStack as $bReknr)
    {
      if (!in_array($bReknr, $aReknrStack))
      {
        $this->unmatchArray["reknr"][$bReknr] = "bank";
      }
    }
    foreach ( $aReknrStack as $aReknr)
    {

      if (!in_array($aReknr, $bReknrStack))
      {
        $this->unmatchArray["reknr"][$aReknr] = "airs";
      }
    }
  }

  function getAirsPostions()
  {
    $db = new DB($this->dbReadserver);
    $order =  "ORDER BY Rekeningen.Portefeuille,Rekeningmutaties.Fonds";
    $query = "
     SELECT
        Rekeningen.Portefeuille as orgPortefeuille,
      Case When 
          Portefeuilles.PortefeuilleDepotbank <> '' 
      then 
          Portefeuilles.PortefeuilleDepotbank 
      else
          Rekeningen.Portefeuille  
      end as `portefeuille`,
        Portefeuilles.Einddatum as einddatum,
        Portefeuilles.Accountmanager as accountmanager,
        Portefeuilles.Depotbank as portDepotbank,
        Portefeuilles.Vermogensbeheerder as vermogensbeheerder,
        Portefeuilles.Client as client,
        Rekeningmutaties.Fonds as airsCode,
        Fondsen.FondsImportCode as fondsImportcode,
        Fondsen.ISINCode as ISIN,
        Fondsen.Valuta as valuta,
        ROUND(SUM(Rekeningmutaties.Aantal),8) AS aantal
      FROM 
        Rekeningmutaties
      JOIN Rekeningen ON  
        Rekeningmutaties.Rekening  = Rekeningen.Rekening AND Rekeningen.consolidatie = '0'
      JOIN Portefeuilles ON 
        Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie = '0'
      JOIN Fondsen ON
        Rekeningmutaties.Fonds  = Fondsen.Fonds
      WHERE
        Rekeningmutaties.Grootboekrekening = 'FONDS' AND
        YEAR(Rekeningmutaties.Boekdatum) = '".substr($this->reconDate,0,4)."' AND
        Rekeningmutaties.Verwerkt = '1' AND
        Rekeningmutaties.Boekdatum <= '{$this->reconDate}' AND 
        Portefeuilles.Vermogensbeheerder IN {$this->vbQueryString} AND
        {$this->depotWherePORT}
      GROUP BY 
        portefeuille,Rekeningmutaties.Fonds
      HAVING 
        round(aantal,4) <> 0
    ";
//debug($query, "getAirsPostions()");
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $this->portefeuilleVB[$rec["portefeuille"]] = $rec["vermogensbeheerder"];
      $this->airsPortefeuilles[] = $rec["portefeuille"]."|".$rec["airsCode"];
      $rec["isPositie"]    = true;
      $rec["batch"]        = $this->batch;
      $rec["eigenaar"]     = $this->depotbank;
      $this->airsPile[] = $rec;
    }



  }

  function getAirsFondsForBankPile()
  {
    $db = new DB($this->dbReadserver);    // main loop
    $dbl = new DB($this->dbReadserver);   // lookup
    $dbu = new DB();   // update
    $fondsRec = false; //
    $query = "
      SELECT
        `bankCode`, 
        `ISIN`, 
        `valuta`
      FROM 
        `reconV3_bankPile` 
      WHERE
        `batch` = '{$this->batch}' AND
        `isPositie` = 1 AND 
        `airsCode` = ''
      GROUP BY
        `bankCode`
      ORDER BY 
        `bankCode`
    ";

    $db->executeQuery($query);



    while ($rec = $db->nextRecord())
    {

      if ($this->depotFondsCodeField == "AABCode")   // AAB heeft twee bankcode velden
      {
        $searchString = "`AABCode` = '".trim($rec["bankCode"])."' OR `ABRCode` = '".trim($rec["bankCode"])."'";
      }
      else
      {
        $searchString = "`".$this->depotFondsCodeField."` = '".trim($rec["bankCode"])."'";
      }

      $match = "bankCode";
      $query = " 
        SELECT 
          `Fonds`,
           `".$this->depotFondsCodeField."`,
           `ISINCode`,
           `Valuta`    
        FROM 
          `Fondsen` 
        WHERE 
          {$searchString} 

      ";
//      debug($query);
      if (!$fondsRec = $dbl->lookupRecordByQuery($query))
      {

        $split = explode("|", $rec["ISIN"]);

        if ($split[0] == "isOptie")
        {
          $match = "optieCode";
          $query = " 
        SELECT 
          `Fonds`,
           `".$this->depotFondsCodeField."` as bankFondsCode,
           `ISINCode`,
           `Valuta`    
        FROM 
          `Fondsen` 
        WHERE 
          (`Fonds` LIKE '".trim($split[1])."' AND `Valuta` = '".trim($rec["valuta"])."') AND
           `".$this->depotFondsCodeField."` = ''

          ";
        }
        else
        {
          $match = "ISIN/val";
          $query = " 
        SELECT 
          `Fonds`,
           `".$this->depotFondsCodeField."` as bankFondsCode,
           `ISINCode`,
           `Valuta`    
        FROM 
          `Fondsen` 
        WHERE 
          (`ISINCode` = '".trim($rec["ISIN"])."' AND `Valuta` = '".trim($rec["valuta"])."') AND
           `ISINCode` != '' AND
           `".$this->depotFondsCodeField."` = ''

          ";
//          debug($query);
        }

        if (!$fondsRec = $dbl->lookupRecordByQuery($query))
        {
          $match = "geen match";
        }
      }

      if ($fondsRec)
      {
        $query = "
          UPDATE 
            `reconV3_bankPile` 
          SET 
            `change_date` = NOW(),  
            `airsCode` = '".mysql_real_escape_string($fondsRec["Fonds"])."',
            `Airs_bankCode` = '{$fondsRec["bankFondsCode"]}', 
            `Airs_ISIN` = '{$fondsRec["ISINCode"]}', 
            `Airs_valuta` = '{$fondsRec["Valuta"]}', 
            `match` = '{$match}'
          WHERE
            `bankCode` = '".trim($rec["bankCode"])."'
            ";
        $dbu->executeQuery($query);
        $rec["match"]         = $match;
        $rec["airsCode"]      = $fondsRec["Fonds"];
        $rec["Airs_bankCode"] = $fondsRec["bankFondsCode"];
        $rec["Airs_ISIN"]     = $fondsRec["ISINCode"];
        $rec["Airs_valuta"]   = $fondsRec["Valuta"];
        $this->fondsPile[$fondsRec["Fonds"]] = $rec;
      }
      else
      {
        $this->noFondsPile[] = $rec;
      }
    }

    $query = "SELECT * FROM `reconV3_bankPile` WHERE `batch` = '".$this->batch."' ";
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $this->bankPortefeuilles[] = $rec["portefeuille"]."|".$rec["airsCode"];
    }

  }

  function bankCodesNotInAirs()
  {
    if (count($this->noFondsPile) != 0)
    {
      $out = "<table><tr><td class='td1 tdH'>bankCode</td><td class='td1 tdH'>ISIN</td><td class='td1 tdH'>valuta</td></tr>";
      foreach ($this->noFondsPile as $noFondsRow)
      {
        $out .= "<tr><td class='td1'>{$noFondsRow["bankCode"]}</td><td class='td2'>{$noFondsRow["ISIN"]}</td><td class='td3'>{$noFondsRow["valuta"]}</td></tr>";
      }
      $out .= "</table>";
    }
    else
    {
      $out = "<h2>Geen onbekende bankcode in deze batch</h2>";
    }
    return $out;
  }

  function bankCodesInAirs()
  {
    if (count($this->fondsPile) != 0)
    {

      $out = "<table><tr><td class='td1 tdH'>Airs fondscode</td><td class='td1 tdH'>bankCode</td><td class='td1 tdH'>ISIN</td><td class='td1 tdH'>valuta</td>
        <td class='td1 tdH'>A bankCode</td><td class='td1 tdH'>A ISIN</td><td class='td1 tdH'>A valuta</td><td class='td1 tdH'>match</td>
    </tr>";
      foreach ($this->fondsPile as $airs=>$fondsRow)
      {
        $out .= "<tr><td class='td1'>{$airs}</td><td class='td1'>{$fondsRow["bankCode"]}</td><td class='td2'>{$fondsRow["ISIN"]}</td><td class='td3'>{$fondsRow["valuta"]}</td>
<td class='td1'>{$fondsRow["Airs_bankCode"]}</td><td class='td2'>{$fondsRow["Airs_ISIN"]}</td><td class='td3'>{$fondsRow["Airs_valuta"]}</td><td class='td3'>{$fondsRow["match"]}</td>
</tr>";
      }
      $out .= "</table>";
    }
    else
    {
      $out = "<h2>Geen bekende bankcode in deze batch</h2>";
    }
    return $out;
  }

  function truncateTables()
  {
    global $USR;
    $db = new DB();
    $query = "DELETE FROM `reconV3_bankPile` WHERE `add_user` = '$USR' ";
    $db->executeQuery($query);
    $query = "DELETE FROM `reconV3_airsPile` WHERE `add_user` = '$USR' ";
    $db->executeQuery($query);
  }


  function addToReconLog($oms)
  {
    global $USR;
    $db = new DB();
    $bt = debug_backtrace();
    $query = "
      INSERT INTO `reconV3Log` SET 
        add_user = '$USR',
        change_user = '$USR',
        add_date = NOW(),
        change_date = NOW(),
        stamp = NOW(),
        batch = '".$this->batch."',
        location = '".basename($bt[0]["file"]).":".$bt[0]["line"]."',
        omschrijving = '$oms'
        
      ";
    $db->executeQuery($query);
  }


  function getAirsCashOnly($einddatum = "")
  {
    $db  = new DB($this->dbReadserver);
    $dbU = new DB();

    if ($einddatum == "")
    {
      $einddatum = $this->reconDate;
    }

    $vbs = "AND VermogensbeheerdersPerBedrijf.Bedrijf = '".$this->vb."'";
    if (is_array($this->vb) )
    {
      $vbs = "AND VermogensbeheerdersPerBedrijf.Bedrijf IN ('".implode("','",$this->vb)."') ";
    }


    $extraquery = " AND Portefeuilles.Vermogensbeheerder =  VermogensbeheerdersPerBedrijf.Vermogensbeheerder
                     AND VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
                     AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille 
                     {$vbs}";


    $extraTable = ', VermogensbeheerdersPerBedrijf ';



    if($_POST['depositoTonen']==1)
      $depositoFilter='';
    else
      $depositoFilter='Rekeningen.Deposito = 0 AND';

    $query = "
	  SELECT
      Rekeningen.Portefeuille as portefeuille,
      Portefeuilles.Depotbank as portDepotbank,
			Rekeningen.Depotbank as depotbank,
      Rekeningen.Valuta as valuta,
      round(SUM(Rekeningmutaties.Bedrag),2) as totaal,
      Portefeuilles.Client as client,
      Portefeuilles.Einddatum as einddatum,
      Portefeuilles.Accountmanager as accountmanager,
      Portefeuilles.Vermogensbeheerder as vermogensbeheerder,
      Rekeningen.Inactief as inactief,
      Rekeningen.Rekening as rekening
    FROM
      (Rekeningmutaties, Rekeningen {$extraTable} )
	    Inner Join Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
      $extraJoin
    WHERE
      Rekeningmutaties.Rekening = Rekeningen.Rekening AND
      Rekeningen.Memoriaal = 0 AND
      Rekeningen.Inactief = 0 AND
      Rekeningen.Depotbank = '{$this->depotbank}' AND
      $depositoFilter
      Rekeningmutaties.boekdatum >= '".date("Y")."-01-01' AND
      Rekeningmutaties.boekdatum <=  '$einddatum'  $extraquery
    GROUP BY
      Portefeuilles.Portefeuille,
      Rekeningmutaties.Rekening
    ORDER BY
      Rekeningen.Portefeuille";
//debug($query);
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      if ($rec["rekeningValuta"] != "MEM")
      {
        $this->airsRekeningNrs[] = $rec["rekening"];
      }


      $rec["isPositie"] = false;
      $rec["batch"] = $this->batch;
      $rec["eigenaar"] = $this->depotbank;
      $this->airsCashPile[] = $rec;
    }

    $rows = array();
    $date = date("Y-m-d H:i:s");
    $query = "
    INSERT INTO `reconV3_airsPile` 
        (add_user,add_date,isPositie,batch,client,portefeuille,portDepotbank,depotbank,rekening,afwRekening,vermogensbeheerder,valuta,totaal,einddatum )
    VALUES
    ";

    foreach($this->airsCashPile as $a)
    {
      $rows[] = "('{$this->user}', '{$date}', 0, '{$a["batch"]}', '{$a["client"]}', '{$a["portefeuille"]}', '{$a["portDepotbank"]}', '{$a["depotbank"]}', '{$a["rekening"]}', '{$a["afwRekening"]}', '{$a["vermogensbeheerder"]}', '{$a["valuta"]}', '{$a["totaal"]}', '{$a["einddatum"]}')";
    }
    $query .= implode(",\n", $rows);
    $rows = array();
    $dbU->executeQuery($query);
  }

  function initModule()
  {
    $tst = new SQLman();
    $tst->tableExist("reconV3_bankPile",true);
    $tst->changeField("reconV3_bankPile","batch",array("Type"=>" varchar(30)","Null"=>false));
    $tst->changeField("reconV3_bankPile","eigenaar",array("Type"=>" varchar(30)","Null"=>false));  // weg
//    $tst->changeField("reconV3_bankPile","depotbank",array("Type"=>" varchar(35)","Null"=>false));  //nw
    $tst->changeField("reconV3_bankPile","portefeuille",array("Type"=>" varchar(65)","Null"=>false));
    $tst->changeField("reconV3_bankPile","bankCode",array("Type"=>" varchar(26)","Null"=>false));
    $tst->changeField("reconV3_bankPile","airsCode",array("Type"=>" varchar(45)","Null"=>false));
    $tst->changeField("reconV3_bankPile","ISIN",array("Type"=>" varchar(25)","Null"=>false));
    $tst->changeField("reconV3_bankPile","valuta",array("Type"=>" varchar(10)","Null"=>false));
    $tst->changeField("reconV3_bankPile","Airs_bankCode",array("Type"=>" varchar(45)","Null"=>false));
    $tst->changeField("reconV3_bankPile","Airs_ISIN",array("Type"=>" varchar(25)","Null"=>false));
    $tst->changeField("reconV3_bankPile","Airs_valuta",array("Type"=>" varchar(10)","Null"=>false));
    $tst->changeField("reconV3_bankPile","aantal",array("Type"=>" double","Null"=>false));
    $tst->changeField("reconV3_bankPile","koers",array("Type"=>" double","Null"=>false));
    $tst->changeField("reconV3_bankPile","isPositie",array("Type"=>" tinyint","Null"=>false));
    $tst->changeField("reconV3_bankPile","memo",array("Type"=>" varchar(255)","Null"=>false));
    $tst->changeField("reconV3_bankPile","reconDatum",array("Type"=>" date","Null"=>false));  // nw
    $tst->changeField("reconV3_bankPile","koersDatum",array("Type"=>" date","Null"=>false));  //nw
    $tst->changeField("reconV3_bankPile","match",array("Type"=>" varchar(15)","Null"=>false));
    $tst->changeField("reconV3_bankPile","bankFonds",array("Type"=>" varchar(50)","Null"=>false));

    $tst->tableExist("reconV3_airsPile",true);
    $tst->changeField("reconV3_airsPile","batch",array("Type"=>" varchar(30)","Null"=>false));
    $tst->changeField("reconV3_airsPile","eigenaar",array("Type"=>" varchar(30)","Null"=>false)); // weg
    $tst->changeField("reconV3_airsPile","vermogensbeheerder",array("Type"=>" varchar(35)","Null"=>false)); //nw
    $tst->changeField("reconV3_airsPile","client",array("Type"=>" varchar(50)","Null"=>false)); //nw
    $tst->changeField("reconV3_airsPile","portefeuille",array("Type"=>" varchar(65)","Null"=>false));
    $tst->changeField("reconV3_airsPile","afwPortefeuille",array("Type"=>" varchar(35)","Null"=>false));
    $tst->changeField("reconV3_airsPile","bankCode",array("Type"=>" varchar(26)","Null"=>false));
    $tst->changeField("reconV3_airsPile","airsCode",array("Type"=>" varchar(45)","Null"=>false));
    $tst->changeField("reconV3_airsPile","ISIN",array("Type"=>" varchar(25)","Null"=>false));
    $tst->changeField("reconV3_airsPile","valuta",array("Type"=>" varchar(10)","Null"=>false));
    $tst->changeField("reconV3_airsPile","koers",array("Type"=>" varchar(10)","Null"=>false));
    $tst->changeField("reconV3_airsPile","aantal",array("Type"=>" double","Null"=>false));
    $tst->changeField("reconV3_airsPile","totaal",array("Type"=>" double","Null"=>false));
    $tst->changeField("reconV3_airsPile","isPositie",array("Type"=>" tinyint","Null"=>false));
    $tst->changeField("reconV3_airsPile","memo",array("Type"=>" varchar(255)","Null"=>false));
    $tst->changeField("reconV3_airsPile","portDepotbank",array("Type"=>" varchar(15)","Null"=>false));
    $tst->changeField("reconV3_airsPile","depotbank",array("Type"=>" varchar(15)","Null"=>false));
    $tst->changeField("reconV3_airsPile","Rekening",array("Type"=>" varchar(35)","Null"=>false));
    $tst->changeField("reconV3_airsPile","afwRekening",array("Type"=>" varchar(35)","Null"=>false));
    $tst->changeField("reconV3_airsPile","einddatum",array("Type"=>" date","Null"=>false));
    $tst->changeField("reconV3_airsPile","accountmanager",array("Type"=>" varchar(15)","Null"=>false));
    $tst->changeField("reconV3_airsPile","fondsImportcode",array("Type"=>" varchar(35)","Null"=>false));
//    $tst->changeField("reconV3_airsPile","reconDatum",array("Type"=>" date","Null"=>false));  // nw
    
    $tst->changeField("tijdelijkeRecon", "portefeuilleAirs",array("Type"=>" varchar(35)","Null"=>false));
  }

}
