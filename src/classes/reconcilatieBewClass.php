<?php

/*
  AE-ICT source module
  Author  						: $Author: cvs $
  Laatste aanpassing	: $Date: 2020/06/22 10:08:48 $
  File Versie					: $Revision: 1.7 $

  $Log: reconcilatieBewClass.php,v $
  Revision 1.7  2020/06/22 10:08:48  cvs
  call 8702

  Revision 1.6  2019/04/12 11:51:10  cvs
  call 7663

  Revision 1.5  2019/04/10 12:38:49  cvs
  call 7663

  Revision 1.4  2019/04/03 07:54:04  cvs
  call 7663

  Revision 1.3  2019/03/19 11:44:15  cvs
  call 6837

  Revision 1.2  2019/02/27 15:04:40  cvs
  call 5995

  Revision 1.1  2017/09/20 06:12:53  cvs
  megaupdate

 */

class reconcilatieBewClass
{
  var $user;
  var $server = 1;
  var $testDate = "";
  var $vbArray = array();
  var $depotbank = "";
  var $batch = "";
  var $matchArray = array();
  var $reconRecMatchId = -1;

  function reconcilatieBewClass($depotBank, $testDate)
  {
    global $USR, $__appvar;
    
    //include_once($__appvar["basedir"]."/html/rapport/rapportRekenClass.php");
    $depotBank = ($depotBank == "SNS")?"NIBC":$depotBank;
    $this->user = $USR;
    $this->testDate = $testDate;
    $this->depotbank = $depotBank;
    $this->batch = date("YmdHi");
  }

  function addVB($vb, $p)
  {
    if (trim($vb) == "")
      return;
    $vb = strtoupper($vb);
    if (!in_array($vb, $this->vbArray))
    {
      $this->vbArray[] = $vb;
      echo "<li>".$vb." = ".$p."</li>";
      ob_flush();flush();
    }
  }

  function addRecord($record)
  {
    global $prb;
    $match = array();
    
    if ($record["type"] <> "cash" AND $record["type"] <> "sec" )
    {
      $prb->hide();
      listError(array("Geen geldige recorddata voor recon_addRecord"));
      exit;
    }


    $query = "INSERT INTO tijdelijkeRecon SET ";
    if ($record["type"] == "cash")
    {
      if ($record["depot"] <> "")
      {
        $reknr = $record["rekening"].$record["valuta"];  // vanuit het bankbestand
      }
      else
      {
        $reknr = $record["rekening"];  // AIRS

      }

      if (!$rekRec = $this->getRekening($reknr))
      {
        $airs = 0;
        $bankWaarde = $record["bedrag"];
        $verschil = $record["bedrag"];
        //$rekRec["bank"] = $this->depotbank;  //dbs 3504 depot automatisch vullen bij geen AIRS
        $match[] = "Geen AIRS";

      }
      else
      {

        $portefeuille = $rekRec["Portefeuille"];

        if ($rekRec["Inactief"] <> 1)
        {
          $this->matchArray[] = $rekRec["Rekening"];
          if (!$airsWaarde = $this->getAIRSvaluta($rekRec["Rekening"]))
          {
            $airs = 0;
            $verschil = $record["bedrag"];
            $match[] = "Geen AIRS mutaties";
          }
          else
          {
            $airs = round($airsWaarde, 2);
            $verschil = $record["bedrag"] - $airs;
            $airsGisteren = round($this->getAIRSvaluta($rekRec["Rekening"],true),2);
          }
        }
        else
        {
          $airs = 0;
          $verschil = $record["bedrag"];
          $match[] = "AIRS rekening INAKTIEF";
          $portefeuille = "";
        }
      }

      /////


      if ($record["depot"] == "")
      {

        $bankWaarde = 0;
        $match[] = "Geen bank";
        $verschil = $record["bedrag"] - $airs;
      }
      else
      {
        $bankWaarde = $record["bedrag"];
      }


      if (substr($this->depotbank,0,2) <> substr($rekRec["Depotbank"],0,2) AND $rekRec["bank"] <> "")
      {
        $match[] = "Onbekende Depotbank afwijking ".$this->depotbank." / ".$rekRec["bank"];
        //return false;  // dbs 3316
      }




      $query .= "
       `add_user` = '".$this->user."'  
      ,`add_date` = NOW()
      ,`change_user` = '".$this->user."'
      ,`change_date` = NOW() 
      ,`vermogensbeheerder` = '{$rekRec["Vermogensbeheerder"]}'
      ,`depotbank`=  '{$rekRec["Depotbank"]}'
      ,`portefeuille`=  '".$portefeuille."'
      ,`rekeningnummer` = '".$reknr."'
      ,`client`=  '{$rekRec["Client"]}'
      ,`Einddatum`=  '{$rekRec["Einddatum"]}'
      ,`reconDatum`=  '".$this->testDate."'   
      ,`Accountmanager`=  '{$rekRec["Accountmanager"]}'
      ,`cashPositie`=  1
      ,`fonds`=  ''
      ,`importCode`= '' 
      ,`fondsImportcode`= '' 
      ,`depotbankFondsCode`=  ''
      ,`fileBankCode`=  ''
      ,`isinCode`= ''
      ,`koers` = '{$record["koers"]}'
      ,`koersDatum` = '{$record["koersDatum"]}'
      ,`valuta`=  '{$record["valuta"]}'
      ,`positieBank`= '{$bankWaarde}'
      ,`positieAirs`= '{$airs}'
      ,`positieAirsGisteren`= '{$airsGisteren}'
      ,`verschil`=  '{$verschil}'
      ,`fondsCodeMatch`= '".implode(" / ", $match)."' 
      ,`batch`=  '{$record["batch"]}'";
    }  /////////////////////////////////////////////////////////// CASH END
    else
    {

      if (!$rekRec = $this->getRekening($record["portefeuille"], "P"))
      {
        $rekRec["Portefeuille"] = $record["portefeuille"];
      }


     // if ($oldRec = $this->getReconMatch($record["portefeuille"], $record["bankCode"]))
      if ($oldRec = $this->getReconMatch($rekRec["Portefeuille"], $record["bankCode"]))
      {
        $rowParts = explode("/", $record["batch"]);
        $query = "
        UPDATE tijdelijkeRecon SET 
         `positieBank`= '".($record["aantal"] + $oldRec["positieBank"])."'
        ,`verschil`=  '".($record["aantal"]+ $oldRec["verschil"])."'
        ,`koers` = '{$record["koers"]}'
        ,`koersDatum` = '{$record["koersDatum"]}'
        
        ,`batch`= concat(`batch`, '/".$rowParts[1]."')
        WHERE id = ".$oldRec["id"];
        // ,`depotbankFondsCode` = '".addslashes($record["fBankCode"])."'
      }
      else
      {
        if ($record["valuta"] == "PCT")
        {
          $query = "SELECT * FROM Fondsen WHERE AABCode = '".$record["bankCode"]."' OR ABRCode = '".$record["bankCode"]."'";
          $db1 = new DB();
          if ($fondsRec = $db1->lookupRecordByQuery($query))
          {
            $record["valuta"] = $fondsRec["Valuta"];
          }

        }
        $query = "INSERT INTO tijdelijkeRecon SET ";
        $query .= "
         `add_user` = '".$this->user."'  
        ,`add_date` = NOW()
        ,`change_user` = '".$this->user."'
        ,`change_date` = NOW()
        ,`vermogensbeheerder` = ''
        ,`depotbank`=  '".$this->depotbank."'
        ,`portefeuille`=  '{$rekRec["Portefeuille"]}'
        ,`rekeningnummer` = ''
        ,`Einddatum`=  '{$rekRec["Einddatum"]}'
        ,`reconDatum`=  '".$this->testDate."' 
        ,`Accountmanager`=  '{$rekRec["Accountmanager"]}'
        ,`client`=  ''
        ,`cashPositie`=  0
        ,`fonds`=  '".addslashes($record["fonds"])."'
        ,`importCode`= '' 
        
        ,`depotbankFondsCode`=  '{$record["bankCode"]}'
        ,`isinCode`= '{$record["ISIN"]}'
        ,`valuta`=  '{$record["valuta"]}'
        ,`positieBank`= '{$record["aantal"]}'
        ,`positieAirs`= ''
        ,`verschil`=  '{$record["aantal"]}'
        ,`koers` = '{$record["koers"]}'
        ,`koersDatum` = '{$record["koersDatum"]}'
        ,`fondsImportcode`= '' 
        ,`fondsCodeMatch`= 'Geen AIRS' 
        ,`batch`=  '{$record["batch"]}'";
        $this->addVB($rekRec["Vermogensbeheerder"]);
      }
    }


    $this->addVB($rekRec["Vermogensbeheerder"]);
    $db = new DB();
    $db->executeQuery($query);
  }

  function fillTableFormAIRS()
  {
    global $prb, $bp;
    debug("deze functie uitschakelen!!","FILL table from AIRS");
    return true;
    // deze functie niet gebruiken in de bewaarders recon;
    $bp = array();
    $ddd = 0;
    $datum = $this->testDate;
    $db = new DB();
   echo "<br/>". $query = "SELECT distinct(portefeuille) FROM tijdelijkeRecon WHERE `batch` LIKE '".$this->batch."%' AND add_user = '".$this->user."' ORDER BY portefeuille";
    $db->executeQuery($query);

    $csvRegels = $db->records();
    $pro_multiplier = 100 / $csvRegels;
    $teller = 0;
    while ($portRec = $db->nextRecord())
    {
      $bp[] = $portRec["portefeuille"];
      $teller++;
      $pro_step += $pro_multiplier;
     // $prb->moveStep($pro_step);
     // $prb->setLabelValue('txt1', "AIRS data ophalen voor ".trim($portRec["portefeuille"]).", ".$teller." / ".$csvRegels." records");

      if (trim($portRec["portefeuille"]) == "")
      {
        continue;
      }

      $portefeuille = $portRec["portefeuille"];
      $this->fillPortefeuilleInfo($portefeuille);
      $fondswaarden = array();
      //$fondswaarden =  berekenPortefeuilleWaardeQuick($portefeuille, $datum);
      $fondswaarden = $this->getAirsPortefeuilleWaarde($portefeuille, $datum);


      for ($x = 0; $x < count($fondswaarden); $x++)
      {
        $record = $fondswaarden[$x];
//        $record["portefeuille"] = $portefeuille;
//        $record["portefeuille"] = $record["PtfRecon"];
        if ($record["type"] <> "fondsen")
          continue;

        $this->updateReconTable($record);
      }
      $ddd++;
      //if ($ddd > 10 ) exit();
    }

  }

  function getAirsPortefeuilleWaarde($portefeuille, $datum)
  {

    $db = new DB();

    switch($this->depotbank)
    {
//      case "AAB BE":
//
//        break;
      case "BIN":
        $depotSearch = "(BewaardDoor = 'BIN'  OR BewaardDoor = 'BINB') ";
        break;
      case "CS":
        $depotSearch = "(BewaardDoor = 'CS'  OR BewaardDoor = 'CS AG') ";
        break;
      case "AAB":
      case "AABH":
        $depotSearch = "(BewaardDoor = 'AAB'  OR BewaardDoor = 'TRI' OR BewaardDoor = 'MPN' OR BewaardDoor = 'AABIAM') ";
        break;
      case "NIBC":
      case "SNS":
        $depotSearch = "(BewaardDoor = 'SNS'  OR BewaardDoor = 'NIBC') ";
        break;
      default:
        $depotSearch = "BewaardDoor = '".$this->depotbank."' ";
    }


//    $query = "
//      SELECT
//        Rekeningen.Portefeuille as portefeuille,
//        Rekeningmutaties.Fonds as fonds,
//        SUM(Rekeningmutaties.Aantal) AS totaalAantal
//      FROM
//        Rekeningmutaties
//      JOIN Rekeningen ON
//        Rekeningmutaties.Rekening  = Rekeningen.Rekening
//      JOIN Portefeuilles ON
//        Rekeningen.Portefeuille = Portefeuilles.Portefeuille
//      WHERE
//        Rekeningmutaties.Grootboekrekening = 'FONDS' AND
//        YEAR(Rekeningmutaties.Boekdatum) = '".substr($datum, 0, 4)."' AND
//        Rekeningmutaties.Verwerkt = '1' AND
//        Rekeningmutaties.Boekdatum <= '".$datum."' AND
//        Portefeuilles.Portefeuille='".$portefeuille."' AND
//        $depotSearch
//      GROUP BY
//        Rekeningen.Portefeuille,Rekeningmutaties.Fonds
//      HAVING
//        round(totaalAantal,4) <> 0
//      ORDER BY
//        Rekeningen.Portefeuille,Rekeningmutaties.Fonds; ";

    ///////////////////////////////////////////////////
//    debug($this->vbArray);
//    $query = "
//    SELECT
//      Left(tempPorteuilles.Rekening,length(tempPorteuilles.Rekening)-3)as portefeuille, -- portefeuille
//      tempMutaties.fonds, -- fonds
//      tempMutaties.totaalaantal as totaalAantal -- totaalaantal
//    FROM
//      (
//      SELECT
//      Portefeuilles.Portefeuille,
//      CASE
//
//        WHEN Rekeningmutaties.Bewaarder <> '' THEN
//          Rekeningmutaties.Bewaarder ELSE Rekeningen.Depotbank
//      END AS 'BewaardDoor',
//        Rekeningmutaties.Fonds,
//        sum( Rekeningmutaties.Aantal ) AS 'TotaalAantal',
//        sum( Rekeningmutaties.Bedrag ) AS 'TotaalSaldo'
//      FROM
//        Rekeningmutaties
//        INNER JOIN Rekeningen ON
//          Rekeningmutaties.Rekening = Rekeningen.Rekening
//        INNER JOIN Portefeuilles ON
//          Rekeningen.Portefeuille = Portefeuilles.Portefeuille
//        LEFT JOIN Fondsen ON
//          Rekeningmutaties.Fonds = Fondsen.Fonds
//      WHERE
//        Portefeuilles.Vermogensbeheerder = '".$this->vbArray[0]."'
//        AND YEAR(Rekeningmutaties.Boekdatum) = '".substr($datum,0,4)."'
//        AND Rekeningmutaties.Verwerkt = '1'
//        AND Boekdatum <= '".$datum."'
//        and Grootboekrekening='Fonds'
//      GROUP BY
//        Portefeuilles.Portefeuille,
//        Portefeuilles.Vermogensbeheerder,
//        CASE
//        WHEN Rekeningmutaties.Bewaarder <> '' THEN
//          Rekeningmutaties.Bewaarder ELSE Rekeningen.Depotbank
//        END,
//        Rekeningmutaties.Fonds,
//        Fondsen.ISINcode,
//        Fondsen.Valuta
//        ) tempMutaties
//
//      INNER JOIN (
//      SELECT DISTINCT
//        Rekeningen.Rekening,
//        Rekeningen.Portefeuille,
//        Rekeningen.Depotbank
//      FROM
//        Rekeningen,
//        Portefeuilles
//      WHERE
//        Memoriaal = 1
//        AND Portefeuilles.Portefeuille = Rekeningen.Portefeuille
//        AND Portefeuilles.Vermogensbeheerder = '".$this->vbArray[0]."'
//      ) tempPorteuilles ON tempMutaties.Portefeuille = tempPorteuilles.Portefeuille
//
//    and Left(tempPorteuilles.Rekening,length(tempPorteuilles.Rekening)-3) = '".$portefeuille."' -- van record depotbank
//    and TotaalAantal <> 0
//    and BewaardDoor = '".$this->depotbank."'
//";


    $query = "
    SELECT
      tempPortefeuilles.Portefeuille AS portefeuilleOrg, 
      tempPortefeuilles.PtfRecon AS portefeuille, 
      tempPortefeuilles.Depotbank, 
      tempMutaties.BewaardDoor, 
      tempMutaties.Client AS Client,
      tempMutaties.Accountmanager AS Accountmanager,
      Fonds AS fonds, 
      SUM(TotAantal) AS totaalAantal    
    FROM
    (
      SELECT
        Rekeningmutaties.Rekening,
        Portefeuilles.Portefeuille,
        Portefeuilles.Client AS Client,
        Portefeuilles.Accountmanager AS Accountmanager,
        CASE WHEN Rekeningmutaties.Bewaarder <> '' THEN
          Rekeningmutaties.Bewaarder 
        ELSE 
          Rekeningen.Depotbank
        END AS 'BewaardDoor',
        Rekeningmutaties.Fonds,
        sum( Rekeningmutaties.Aantal ) AS 'TotAantal',
        sum( Rekeningmutaties.Bedrag ) AS 'TotaalSaldo'
      FROM
        Rekeningmutaties
      INNER JOIN Rekeningen ON
        Rekeningmutaties.Rekening = Rekeningen.Rekening
      INNER JOIN Portefeuilles ON
        Rekeningen.Portefeuille = Portefeuilles.Portefeuille
      LEFT JOIN Fondsen ON
        Rekeningmutaties.Fonds = Fondsen.Fonds
      WHERE
        Rekeningen.consolidatie = 0 AND
        YEAR(Rekeningmutaties.Boekdatum) = '".substr($datum,0,4)."' AND 
        Rekeningmutaties.Verwerkt = '1' AND 
        Boekdatum <= '".$datum."' AND 
        (Grootboekrekening='Fonds' OR ( Grootboekrekening='KRUIS' AND Rekeningmutaties.Fonds != '') ) AND 
        Portefeuilles.Portefeuille = '".$portefeuille."'
      GROUP BY
        Rekeningmutaties.Rekening,
        CASE WHEN Rekeningmutaties.Bewaarder <> '' THEN
          Rekeningmutaties.Bewaarder ELSE Rekeningen.Depotbank
        END,
        Rekeningmutaties.Fonds,
        Fondsen.ISINcode,
        Fondsen.Valuta
    ) tempMutaties

    LEFT JOIN ( 
      SELECT distinct
        TT.Portefeuille, 
        TT.Rekening, 
        TT.Depotbank,
        LEFT(TT.Rekening,length(TT.Rekening)-3) as 'PtfRecon'
      FROM 
      Rekeningen RK 


      LEFT JOIN
      (
        SELECT DISTINCT
          Rekeningen.Rekening,
          Rekeningen.Depotbank ,
          Rekeningen.Portefeuille
        FROM
          Rekeningen
        WHERE
          Memoriaal = 1 AND
          Rekeningen.consolidatie = 0 AND
          Rekeningen.Portefeuille = '".$portefeuille."' AND
					Rekeningen.Inactief = 0
        GROUP BY 
          Rekeningen.Depotbank  
        ORDER BY
          Inactief ASC, 
          Rekening ASC
--        LIMIT 1
      ) TT on RK.Portefeuille = TT.Portefeuille and RK.Depotbank =TT.Depotbank
      WHERE
      RK.consolidatie = 0 
  ) tempPortefeuilles ON  
    tempMutaties.Portefeuille = tempPortefeuilles.Portefeuille AND 
    tempMutaties.BewaardDoor = tempPortefeuilles.Depotbank    
  WHERE 
    $depotSearch
    
  GROUP BY
    tempPortefeuilles.PtfRecon, 
    tempPortefeuilles.Depotbank, 
    tempMutaties.BewaardDoor, 
    Fonds
  HAVING    
    totaalAantal <> 0
    ";


//    AND tempMutaties.bewaarddoor = tempPorteuilles.Depotbank
//    and $depotSearch


    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {

      $rec["type"] = "fondsen";
      $out[] = $rec;
      $p = $rec["portefeuille"];
    }

    return $out;
  }

  function getAirsPortefeuilleWaardeDuplicaat($portefeuille, $datum)
  {

    $db = new DB();



    $query = "
      SELECT
        Rekeningen.Portefeuille as portefeuille,
        Rekeningmutaties.Fonds as fonds,
        SUM(Rekeningmutaties.Aantal) AS totaalAantal
      FROM 
        Rekeningmutaties
      JOIN Rekeningen ON  
        Rekeningmutaties.Rekening  = Rekeningen.Rekening
      JOIN Portefeuilles ON 
        Rekeningen.Portefeuille = Portefeuilles.Portefeuille
      WHERE
        Rekeningen.consolidatie = 0 AND
        (Grootboekrekening='Fonds' OR ( Grootboekrekening='KRUIS' AND Rekeningmutaties.Fonds != '') ) AND
        YEAR(Rekeningmutaties.Boekdatum) = '".substr($datum, 0, 4)."' AND
        Rekeningmutaties.Verwerkt = '1' AND
        Rekeningmutaties.Boekdatum <= '".$datum."' AND 
        Portefeuilles.Portefeuille='".$portefeuille."' 
      GROUP BY 
        Rekeningen.Portefeuille,Rekeningmutaties.Fonds
      HAVING 
        round(totaalAantal,4) <> 0
      ORDER BY 
        Rekeningen.Portefeuille,Rekeningmutaties.Fonds; ";

    $db->executeQuery($query);
    $stukken = array();
    while ($rec = $db->nextRecord())
    {
      $stukken[$rec["fonds"]] += $rec["totaalAantal"];
    }
    return $stukken;
  }

  function checksumPortefeuille($testArray)
  {
    return md5(serialize($testArray));
  }

  function updateReconTable($record)
  {

    $portefeuille = $record["portefeuille"];
    $fondRec = $this->getFonds($record["fonds"]);

    $db = new DB();


    if ($reconRec = $this->findReconRow($portefeuille, $fondRec))
    {
      if ($reconRec["match"] == "bankcode afwijking")
      {
        $query = -1;
      }
      else
      {
        $match2 = "";
        if ($record["portefeuille"] != $record["portefeuilleOrg"] AND trim($record["portefeuilleOrg"]) != "")
        {
          $match2 = " afw ptf (org: ".$record["portefeuilleOrg"].") ";
        }
        $query = "UPDATE tijdelijkeRecon SET ";
        $query .= "
       `positieAirs`= '{$record["totaalAantal"]}'
      ,`verschil`=  '" . ($reconRec["positieBank"] - $record["totaalAantal"]) . "'
      ,`fondsCodeMatch`= '" .$match2. $reconRec["match"] . "'  
      ,`fonds` = '" . addslashes($fondRec["Fonds"]) . "'  
      ,`client`=  '" . $record["Client"] . "' 
      ,`Accountmanager`=  '{$record["Accountmanager"]}'
      ,`fondsImportcode`= '{$fondRec["FondsImportCode"]}'";
        //  ,`depotbankFondsCode` = '".addslashes($record["fBankCode"])."'
        $query .= "WHERE id = " . $reconRec["id"];
      }


    }
    else
    {

      $portRec = $this->getPortefeuille("Portefeuille = '" . $portefeuille . "'");
      $query = "INSERT INTO tijdelijkeRecon SET ";
      $query .= "
       `add_user` = '" . $this->user . "'  
      ,`add_date` = NOW()
      ,`change_user` = '" . $this->user . "'
      ,`change_date` = NOW()
      ,`portefeuille`=  '{$record["portefeuille"]}'
      ,`rekeningnummer` = ''
      ,`cashPositie`=  0
      ,`fonds`=  '" . addslashes($fondRec["Fonds"]) . "'
      ,`importCode`= '' 
      ,`fondsImportcode`= '{$fondRec["FondsImportCode"]}' 
      ,`Einddatum`=  '{$portRec["Einddatum"]}'
      ,`reconDatum`=  '" . $this->testDate . "'       
      ,`Accountmanager`=  '{$record["Accountmanager"]}'
      ,`depotbankFondsCode`=  '{$record["bankCode"]}'
      ,`isinCode`= '{$fondRec["ISINCode"]}'
      ,`valuta`=  '{$fondRec["Valuta"]}'
      ,`positieBank`= '0'
      ,`positieAirs`= '{$record["totaalAantal"]}'
      ,`verschil`=  '" . -1 * $record["totaalAantal"] . "'  
      ,`fondsCodeMatch`= 'Geen bank (".$record["portefeuilleOrg"].") xx'
      ,`vermogensbeheerder` = '{$portRec["Vermogensbeheerder"]}'
      ,`depotbank`=  '{$portRec["Depotbank"]}'
      ,`client`=  '{$record["Client"]}'
      ,`batch`=  '{$record["batch"]}'";
    }

    if ($query != -1)
    {
      $db->executeQuery($query);
    }

  }

  function findReconRow($portefeuille, $fondsRec)
  {
    $db = new DB();
    switch (strtolower($this->depotbank))
    {
      case "pic":
        $bankcode = $fondsRec["PICcode"];
        $matchText = "via PictectCode";
        break;
      case "lom":
        $bankcode = $fondsRec["LOMcode"];
        $matchText = "via LOMcode";
        break;
      case "giro":
        $bankcode = $fondsRec["giroCode"];
        $matchText = "via DeGiroCode";
        break;
      case "kas":
        if ($fondsRec["fondssoort"] == "OPT")
        {
          $bankcode = $fondsRec["Fonds"];
        }
        else
        {
          $bankcode = $fondsRec["kasbankCode"];
        }
        $matchText = "via Kasbankcode";
        break;
      case "tgb":
        $bankcode = $fondsRec["stroeveCode"];
        $matchText = "via StroeveCode";
        break;
      case "fvl":
        $bankcode = $fondsRec["FVLCode"];
        $matchText = "via FVLCode";
        break;
      case "nibc":
      case "sns":
        $bankcode = $fondsRec["snsSecCode"];
        $matchText = "via NIBC/SNSCode";
        break;
      case "aab":
      case "tri":
      case "aabh":
      case "aabiam":
        $bankcode = $fondsRec["AABCode"];
        $matchText = "via AABCode";
        break;
      case "aab be":
        $bankcode = $fondsRec["aabbeCode"];
        $matchText = "via AABBECode";
        break;
      case "jb":
        $bankcode = $fondsRec["JBcode"];
        $matchText = "via JBcode";
        break;
      case "bil":
        $bankcode = $fondsRec["BILcode"];
        $matchText = "via BILcode";
        break;
      case "binb":
      case "bin":
        $bankcode = $fondsRec["binckCode"];
        $matchText = "via BinckCode";
        break;
      case "ubp":
        $bankcode = $fondsRec["UBPcode"];
        $matchText = "via UBPCode";
        break;
      case "ubs":
        $bankcode = $fondsRec["UBScode"];
        $matchText = "via UBSCode";
        break;
      case "cs":
      case "cs ag":
        $bankcode = "";  // nog geen bankcode bekend..
    }

    if ($bankcode <> "")
    {
      $query = "SELECT * FROM tijdelijkeRecon WHERE add_user = '".$this->user."' AND depotbankFondsCode = '".$bankcode."' AND portefeuille = '".$portefeuille."'";
      if ($reconRec = $db->lookupRecordByQuery($query))
      {
        $reconRec["match"] = $matchText;
        return $reconRec;
      }
      else
      {
//        $reconRec["match"] = "bankcode afwijking";
//        return $reconRec;
        return false;

      }
    }
    else
    {
      $query = "SELECT * FROM tijdelijkeRecon WHERE add_user = '".$this->user."' AND isinCode = '".$fondsRec["ISINCode"]."' AND valuta = '".$fondsRec["Valuta"]."' AND portefeuille = '".$portefeuille."'";

      if ($fondsRec["ISINCode"] <> "")
      {
        if ($reconRec = $db->lookupRecordByQuery($query))
        {
          $reconRec["match"] = "via ISIN/valuta";
          return $reconRec;
        }
      }
    }

    return false;
  }

  function getReconMatch($portefeuille, $bankCode)
  {
    $db = new DB();
    $query = "SELECT * FROM tijdelijkeRecon WHERE add_user = '".$this->user."' AND portefeuille = '".$portefeuille."' AND depotbankFondsCode = '".$bankCode."' ";

    if ($reconRec = $db->lookupRecordByQuery($query))
    {
      return $reconRec;
    }
    else
    {
      return false;
    }
  }

  function getAirsRekNr($rekeningNr = "-1")
  {

    switch ($this->depotbank)
    {
//      case "AAB BE":
//        break;
      case "CS":
        $depotSearch = "(Rekeningen.Depotbank = 'CS'  OR Rekeningen.Depotbank = 'CS AG') ";
        break;
      case "NIBC":
      case "SNS":
        $depotSearch = "(Rekeningen.Depotbank = 'SNS'  OR Rekeningen.Depotbank = 'NIBC') ";
        break;
      case "AAB":
      case "AABH":
        $depotSearch = "(Rekeningen.Depotbank = 'AAB'  OR Rekeningen.Depotbank = 'TRI' OR Rekeningen.Depotbank = 'AABIAM' )";
        break;
      default:
       $depotSearch = "Rekeningen.Depotbank = '".$this->depotbank."' ";
    }


    $db = new DB();
    $query = "SELECT * FROM Rekeningen WHERE `RekeningDepotbank` = '".$rekeningNr."' AND $depotSearch ";

    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rec["Rekening"];
    }
    else
    {
      $query = "SELECT * FROM Rekeningen WHERE `Rekening` = '".$rekeningNr."' AND $depotSearch ";

      if ($rec = $db->lookupRecordByQuery($query))
      {
        return $rec["Rekening"];
      }
      else
      {
        return false;
      }
    }
  }

  function getAirsPortefeuilleNr($portefeuilleNr = "-1")
  {
    $depot = $this->depotbank;
    $db = new DB();

    switch ($this->depotbank)
    {
//      case "AAB BE":
//        break;
      case "BIN":
        $depotSearch = "(Portefeuilles.Depotbank = 'BIN'  OR Portefeuilles.Depotbank = 'BINB') ";
        break;
      case "NIBC":
      case "SNS":
        $depotSearch = "(Portefeuilles.Depotbank = 'SNS'  OR Portefeuilles.Depotbank = 'NIBC') ";
        break;
      case "CS":
        $depotSearch = "(Portefeuilles.Depotbank = 'CS'  OR Portefeuilles.Depotbank = 'CS AG') ";
        break;
      case "AAB":
      case "AABH":
        $depotSearch = "(Portefeuilles.Depotbank = 'AAB'  OR Portefeuilles.Depotbank = 'TRI' OR Portefeuilles.Depotbank = 'AABIAM')  ";
        break;
      default:
        $depotSearch = "Portefeuilles.Depotbank = '".$this->depotbank."' ";
    }


    $query = "SELECT * FROM Portefeuilles WHERE `PortefeuilleDepotbank` = '".$portefeuilleNr."' AND Portefeuilles.consolidatie = 0 AND ".$depotSearch;
    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rec["Portefeuille"];
    }
    else
    {
      $query = "SELECT * FROM Portefeuilles WHERE `Portefeuille` = '".$portefeuilleNr."' AND Portefeuilles.consolidatie = 0 AND  ".$depotSearch;
      if ($rec = $db->lookupRecordByQuery($query))
      {
        return $portefeuilleNr;
      }
      else
      {
        return false;
      }
    }
  }

  function getRekening($nr, $type="R")
  {
    if ($type == "R")
    {
      if (!$reknr = $this->getAirsRekNr($nr) )
      {
        return false;
      }
      //$search = "Rekening = '".$reknr."'  AND Rekeningen.Depotbank = '".$this->depotbank."' ";

      switch($this->depotbank)
      {
//        case "AAB BE":
//          break;
        case "BIN":
          $search = "Rekening = '".$reknr."'  AND (Rekeningen.Depotbank = 'BIN'  OR Rekeningen.Depotbank = 'BINB') ";
          break;
        case "CS":
          $search = "Rekening = '".$reknr."'  AND (Rekeningen.Depotbank = 'CS'  OR Rekeningen.Depotbank = 'CS AG') ";
          break;
        case "NIBC":
        case "SNS":
          $search = "Rekening = '".$reknr."'  AND (Rekeningen.Depotbank = 'SNS'  OR Rekeningen.Depotbank = 'NIBC') ";
          break;
        case "AAB":
        case "AABH":
          $search = "Rekening = '".$reknr."'  AND (Rekeningen.Depotbank = 'AAB'  OR Rekeningen.Depotbank = 'TRI' OR Rekeningen.Depotbank = 'AABIAM') ";
          break;
        default:
          $search = "Rekening = '".$reknr."'  AND Rekeningen.Depotbank = '".$this->depotbank."' ";
      }

    }
    else // P
    {

      switch($this->depotbank)
      {
//        case "AAB BE":
//        break;
        case "BIN":
          $depotSearch = "(Portefeuilles.Depotbank = 'BIN'  OR Portefeuilles.Depotbank = 'BINB') ";
          break;
        case "CS":
          $depotSearch = "(Portefeuilles.Depotbank = 'CS'  OR Portefeuilles.Depotbank = 'CS AG') ";
          break;
        case "NIBC":
        case "SNS":
          $depotSearch = "(Portefeuilles.Depotbank = 'SNS'  OR Portefeuilles.Depotbank = 'NIBC') ";
          break;
        case "AAB":
        case "AABH":
          $depotSearch = "(Portefeuilles.Depotbank = 'AAB'  OR Portefeuilles.Depotbank = 'TRI' OR Portefeuilles.Depotbank = 'AABIAM' ) ";
          break;
        default:
          $depotSearch = "Portefeuilles.Depotbank = '".$this->depotbank."' ";
      }

      $reknr = $this->getAirsPortefeuilleNr($nr);
      $search = "Portefeuilles.Portefeuille = '".$reknr."'  AND ".$depotSearch ;
    }

    $db = new DB($this->server);
    $q = "SELECT
            Rekeningen.*,
            Portefeuilles.Depotbank as bank,
            Portefeuilles.SoortOvereenkomst,
            Portefeuilles.InternDepot AS portDepot,
            Portefeuilles.Client,
            Portefeuilles.Einddatum,
            Portefeuilles.Accountmanager,
            Portefeuilles.Vermogensbeheerder
          FROM
            Rekeningen
          LEFT JOIN Portefeuilles ON
            Rekeningen.Portefeuille = Portefeuilles.Portefeuille
          WHERE
            Rekeningen.consolidatie = 0 AND
            $search";



    if ($rec = $db->lookupRecordByQuery($q))
    {
      if (trim($rec["typeRekening"]) == "")
        $rec["typeRekening"] = "Ontbreekt";
      return $rec;
    }
    else
      return false;
  }

  function getPortefeuille($search, $part = "all")
  {
     if (trim($search) == "")
    {
      return false;
    }
    $db = new DB($this->server);
    $q = "SELECT * FROM Portefeuilles WHERE $search";

    if ($rec = $db->lookupRecordByQuery($q))
    {
      if ($part == "all")
        return $rec;
      else
        return $rec[$part];
    }
    else
      return false;
  }

  function getPortefeuilleContent($portefeuille)
  {
    $db = new DB();
    $query = "SELECT * FROM Portefeuilles WHERE  Portefeuille = '".$portefeuilleInCsv."' AND Portefeuilles.consolidatie = 0 AND Einddatum > NOW()";
  }

  function getFonds($fonds, $part = "all")
  {
    $db = new DB($this->server);
    $q = "SELECT * FROM Fondsen WHERE Fonds='$fonds' ";
    if ($rec = $db->lookupRecordByQuery($q))
    {
      if ($part == "all")
        return $rec;
      else
        return $rec[$part];
    }
    else
      return false;
  }

  function getAirsPortefeuilles()
  {
    global $bp;
    $db = new DB();
    $datum = $this->testDate;
//    debug($this);
    switch($this->depotbank)
    {


//      case "AAB BE":
//        break;
      case "BIN":
        $depotSearch = "(BewaardDoor = 'BIN'  OR BewaardDoor = 'BINB') ";
        break;
      case "CS":
        $depotSearch = "(BewaardDoor = 'CS'  OR BewaardDoor = 'CS AG') ";
        break;
      case "NIBC":
      case "SNS":
        $depotSearch = "(BewaardDoor = 'SNS'  OR BewaardDoor = 'NIBC') ";
        break;
      case "AAB":
      case "AABH":
        $depotSearch = "(BewaardDoor = 'AAB'  OR BewaardDoor = 'TRI' OR BewaardDoor = 'MPN' OR BewaardDoor = 'AABIAM') ";
        break;
      default:
        $depotSearch = "BewaardDoor = '".$this->depotbank."' ";
    }

//    echo $query = "
//    SELECT
//      distinct(Portefeuille)
//    FROM
//      Portefeuilles
//    WHERE
//      Portefeuilles.Vermogensbeheerder IN ( '".implode("','", $this->vbArray)."' ) AND
//
//      Portefeuilles.Einddatum > NOW() ";

//    $query = "
//    SELECT
//      distinct tempMutaties.Portefeuille
//
//      FROM
// (
//      SELECT
//        Portefeuilles.Portefeuille,
//        CASE
//          WHEN Rekeningmutaties.Bewaarder <> '' THEN
//            Rekeningmutaties.Bewaarder ELSE Rekeningen.Depotbank
//        END AS 'BewaardDoor',
//        Rekeningmutaties.Fonds,
//        sum( Rekeningmutaties.Aantal ) AS 'TotaalAantal',
//        sum( Rekeningmutaties.Bedrag ) AS 'TotaalSaldo'
//      FROM
//        Rekeningmutaties
//        INNER JOIN Rekeningen ON
//          Rekeningmutaties.Rekening = Rekeningen.Rekening
//        INNER JOIN Portefeuilles ON
//          Rekeningen.Portefeuille = Portefeuilles.Portefeuille
//        LEFT JOIN Fondsen ON
//          Rekeningmutaties.Fonds = Fondsen.Fonds
//      WHERE
//        YEAR(Rekeningmutaties.Boekdatum) = '".substr($datum,0,4)."'
//        AND Rekeningmutaties.Verwerkt = '1'
//        AND Boekdatum <= '$datum'
//        and Grootboekrekening='Fonds'
//      GROUP BY
//        Portefeuilles.Portefeuille,
//        Portefeuilles.Vermogensbeheerder,
//        CASE
//        WHEN Rekeningmutaties.Bewaarder <> '' THEN
//          Rekeningmutaties.Bewaarder ELSE Rekeningen.Depotbank
//        END,
//        Rekeningmutaties.Fonds,
//        Fondsen.ISINcode,
//        Fondsen.Valuta
//        ) tempMutaties
//      WHERE
//        BewaardDoor = '".$this->depotbank."'
//
//    ";

    $query = "
    SELECT
      distinct(tempMutaties.Portefeuille) AS Portefeuille, 
      tempMutaties.BewaardDoor 
    FROM
    (
      SELECT
        Rekeningmutaties.Rekening,
        Portefeuilles.Portefeuille,
        CASE WHEN Rekeningmutaties.Bewaarder <> '' THEN
          Rekeningmutaties.Bewaarder 
        ELSE 
          Rekeningen.Depotbank
        END AS 'BewaardDoor',
        Rekeningmutaties.Fonds,
        sum( Rekeningmutaties.Aantal ) AS 'TotAantal',
        sum( Rekeningmutaties.Bedrag ) AS 'TotaalSaldo'
      FROM
        Rekeningmutaties
      INNER JOIN Rekeningen ON
        Rekeningmutaties.Rekening = Rekeningen.Rekening
      INNER JOIN Portefeuilles ON
        Rekeningen.Portefeuille = Portefeuilles.Portefeuille
      LEFT JOIN Fondsen ON
        Rekeningmutaties.Fonds = Fondsen.Fonds
      WHERE
        Rekeningen.consolidatie = 0 AND
        YEAR(Rekeningmutaties.Boekdatum) = '".substr($datum,0,4)."' AND 
        Rekeningmutaties.Verwerkt = '1' AND 
        Boekdatum <= '".$datum."' AND 
        (Grootboekrekening='Fonds' OR ( Grootboekrekening='KRUIS' AND Rekeningmutaties.Fonds != '') ) AND
        Portefeuilles.Vermogensbeheerder IN ('".implode("','",$this->vbArray)."')
      GROUP BY
        Rekeningmutaties.Rekening,
        CASE WHEN Rekeningmutaties.Bewaarder <> '' THEN
          Rekeningmutaties.Bewaarder ELSE Rekeningen.Depotbank
        END,
        Rekeningmutaties.Fonds,
        Fondsen.ISINcode,
        Fondsen.Valuta
    ) tempMutaties

  WHERE
    $depotSearch 
    
  ";

    $db->executeQuery($query);
    while ($portRec = $db->nextRecord())
    {
//      if (in_array($portRec["Portefeuille"], $bp))
//      {
//        $pSkip[] = $portRec["Portefeuille"];
//      }
//      else
//      {
        $pAirs[] = $portRec["Portefeuille"];
//      }
    }
//    debug("gevonden pAirs: ".count($pAirs));
//    debug($pAirs);
    for ($y=0; $y < count($pAirs); $y++)
    {
      echo "X.";
      $portefeuille = $pAirs[$y];

      $this->fillPortefeuilleInfo($portefeuille, true);   // recon record aanmaken met AIRS data
      $fondswaarden = $this->getAirsPortefeuilleWaarde($portefeuille, $datum);

      for ($x = 0; $x < count($fondswaarden); $x++)
      {

        $record = $fondswaarden[$x];
        $record["portefeuilleOrg"] = $portefeuille;
        if ($record["type"] <> "fondsen")
          continue;

        $this->updateReconTable($record);
      }
    }


  }

  function getAirsCashRekeningen()
  {
    global $prb;

    $db = new DB();
    switch($this->depotbank)
    {
//      case "AAB BE":
//        break;
      case "BIN":
        $depotSearch = " (Rekeningen.Depotbank = 'BIN' OR Rekeningen.Depotbank = 'BINB') ";
        break;
      case "CS":
        $depotSearch = " (Rekeningen.Depotbank = 'CS' OR Rekeningen.Depotbank = 'CS AG') ";
        break;
      case "NIBC":
      case "SNS":
        $depotSearch = "(Rekeningen.Depotbank = 'SNS'  OR Rekeningen.Depotbank = 'NIBC') ";
        break;
      case "AAB":
      case "AABH":
        $depotSearch = "(Rekeningen.Depotbank = 'AAB'  OR Rekeningen.Depotbank = 'AABIAM' OR Rekeningen.Depotbank = 'TRI') ";
        break;
      default:
        $depotSearch = "Rekeningen.Depotbank = '".$this->depotbank."' ";
    }

  echo  $query = "
    SELECT 
      Portefeuilles.*,
      Rekeningen.Rekening as Rekening
    FROM 
      Portefeuilles 
    LEFT JOIN 
      Rekeningen ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
    WHERE  
      Portefeuilles.consolidatie = 0 AND
      Portefeuilles.Vermogensbeheerder IN ( '".implode("','", $this->vbArray)."' ) AND 
      $depotSearch AND
      Portefeuilles.Einddatum > NOW() AND
      Rekeningen.Rekening <> '' AND
      Rekeningen.Memoriaal = 0 AND
      Rekeningen.Inactief = 0 ";

    echo " <li> geselecteerde vermogenbeheerders: '".implode("','", $this->vbArray)."' </li>";
    ob_flush();flush();

    $db->executeQuery($query);
    $csvRegels = $db->records();
    $pro_multiplier = 100 / $csvRegels;
    $teller = 0;

    while ($portRec = $db->nextRecord())
    {
   //   debug($portRec);
      $record = array("depot" => "","batch"=>$this->batch."/000");  // reset $record per ingelezen regel
      $teller++;
      $pro_step += $pro_multiplier;
      //$prb->moveStep($pro_step);
      //$prb->setLabelValue('txt1', "AIRS bankrekeningen ophalen voor ".trim($portRec["Rekening"]).", ".$teller." / ".$csvRegels." records");


      if (in_array($portRec["Rekening"], $this->matchArray))
        continue;
      $airsRekeningen[] = $portRec["Rekening"];
    }
    for ($x = 0; $x < count($airsRekeningen); $x++)
    {
      $record["type"] = "cash";
      if ($rekRec = $this->getRekening($airsRekeningen[$x]) )
      {


        $record["portefeuille"] = $rekRec["Portefeuille"];
        $record["rekening"] = $rekRec["Rekening"];
        $record["Einddatum"] = $rekRec["Einddatum"];
        $record["Accountmanager"] = $rekRec["Accountmanager"];
        $record["bedrag"] = "AIRS";
        $record["valuta"] = $rekRec["Valuta"];
        $record["depot"] = "";  // force AIRS
        $this->addRecord($record);
      }

    }
    //$prb->setLabelValue('txt1', "Klaar met inlezen");
    //$prb->hide();


    return count($airsRekeningen);
  }

  function fillVB()
  {
    $vb = ($this->vbArray[0] <> "")?$this->vbArray[0]:"???";

    $q = "UPDATE tijdelijkeRecon SET `vermogensbeheerder` = '".$vb."' WHERE  `vermogensbeheerder` = '' AND add_user = '".$this->user."'";
    $db = new DB();
    $db->executeQuery($q);
  }

  function getAIRSvaluta($rekeningnr, $gisteren = false)
  {


    $datum = $this->testDate;
    $tmpDB = New DB();

    if ($gisteren)
    {
      $qExtra = "Rekeningmutaties.boekdatum <= DATE_SUB('".$datum."',INTERVAL 1 DAY) ";
    }
    else
    {
      $qExtra = "Rekeningmutaties.boekdatum <= '".$datum."' ";
    }

    switch ($this->depotbank)
    {
//      case "AAB BE":
//        break;
      case "BIN":
        $depotSearch = " (Rekeningen.Depotbank = 'BIN' OR Rekeningen.Depotbank = 'BINB') ";
        break;
      case "NIBC":
      case "SNS":
        $depotSearch = " (Rekeningen.Depotbank = 'SNS'  OR Rekeningen.Depotbank = 'NIBC') ";
        break;
      case "CS":
        $depotSearch = " (Rekeningen.Depotbank = 'CS' OR Rekeningen.Depotbank = 'CS AG') ";
        break;
      case "AAB":
      case "AABH":
        $depotSearch = "(Rekeningen.Depotbank = 'AAB'  OR Rekeningen.Depotbank = 'AABIAM' OR Rekeningen.Depotbank = 'TRI') ";
        break;
      default:
        $depotSearch = "Rekeningen.Depotbank = '".$this->depotbank."' ";
    }

   $query = "
    SELECT 
      Rekeningen.Valuta, 
      SUM(Rekeningmutaties.Bedrag) as totaal,
      Rekeningmutaties.Rekening
    FROM 
      Rekeningmutaties, Rekeningen
    WHERE
      Rekeningen.consolidatie = 0 AND
    	Rekeningmutaties.Rekening = Rekeningen.Rekening AND
    	Rekeningmutaties.boekdatum >= '".substr($datum, 0, 4)."' AND
      Rekeningmutaties.Rekening = '".$rekeningnr."' AND
      $depotSearch AND
    	$qExtra
    GROUP BY 
      Rekeningen.Valuta
    ORDER BY 
      Rekeningen.Valuta";
    if ($data = $tmpDB->lookupRecordByQuery($query))
    {

      return $data["totaal"];
    }
    else
    {
      return false;
    }
  }

  function getAIRSvalutaWaardeDuplicaat($rekeningnr, $datum)
  {
    
    $tmpDB = New DB();

    $qExtra = "Rekeningmutaties.boekdatum <= '".$datum."' ";


   $query = "
    SELECT 
      Rekeningen.Valuta, 
      SUM(Rekeningmutaties.Bedrag) as totaal,
      Rekeningmutaties.Rekening
    FROM 
      Rekeningmutaties, Rekeningen
    WHERE
      Rekeningen.consolidatie = 0 AND
    	Rekeningmutaties.Rekening = Rekeningen.Rekening AND
    	Rekeningmutaties.boekdatum >= '".substr($datum, 0, 4)."' AND
      Rekeningmutaties.Rekening = '".$rekeningnr."' AND
    	$qExtra
    GROUP BY 
      Rekeningen.Valuta
    ORDER BY 
      Rekeningen.Valuta";

    if ($data = $tmpDB->lookupRecordByQuery($query))
    {
      return round($data[totaal],2);
    }
    else
    {
      return false;
    }
  }
  
  function fillPortefeuilleInfo($portefeuille, $insertRecord=false)
  {
    $db = new DB();
    if (!$portRec = $this->getPortefeuille("Portefeuille = '".$portefeuille."'"))
    {
        $q = ",`fondsCodeMatch`= 'Geen AIRS'";
    }
    else
    {
      if ($portRec["Depotbank"] <> $this->depotbank )
      {
        $q = ",`fondsCodeMatch`= 'Port. Depotbank afwijking'";
      }
      else
      {
        $q = "";
      }
    }
    
    
    if ($portRec["Depotbank"] == "BINB" OR $this->depotbank == "BIN")  // call 3528
    {
      $q = "";
    }
    
    if ($portRec["Depotbank"] == "CS AG" OR $this->depotbank == "CS")
    {
      $q = "";
    }

    if ($portRec["Depotbank"] == "TRI" OR $portRec["Depotbank"] == "AABIAM" OR $this->depotbank == "AAB" )
    {
      $q = "";
    }

    if ($portRec["Depotbank"] == "NIBC" OR $this->depotbank == "SNS")
    {
      $q = "";
    }

    if ($insertRecord)     
    {
      $q = ",`fondsCodeMatch`= 'Geen BANK2'";
      $query = " 
        INSERT INTO tijdelijkeRecon SET     
          `add_date` = NOW(),
          `add_user` = '".$this->user."' ,";
          
      $where = "";

    }
    else
    {
      $query = " UPDATE tijdelijkeRecon SET      ";
      $where = " WHERE portefeuille = '".$portefeuille."'";
    }
    $query .= "`vermogensbeheerder` = '{$portRec["Vermogensbeheerder"]}'
              ,`client`=  '{$portRec["Client"]}'
              ,`Einddatum`=  '{$portRec["Einddatum"]}'
              ,`reconDatum`=  '".$this->testDate."' 
              ,`Accountmanager`=  '{$portRec["Accountmanager"]}'
              ,`portefeuille`=  '{$portefeuille}'
              $q  
              $where      ";
    
              
    

    $db->executeQuery($query);
  }

  function kasbankDateToDb($kasdate)
  {
    return "20".substr($kasdate, 0, 2)."-".substr($kasdate, 2, 2)."-".substr($kasdate, 4, 2);
  }

  function bewaarders()
  {
    $db = new DB();
    $query = "
    SELECT
      Portefeuilles.Portefeuille,
      Portefeuilles.Vermogensbeheerder,
      Portefeuilles.Depotbank AS 'DepotPort',
      Rekeningen.Depotbank AS 'DepotRekening',
      Rekeningmutaties.Bewaarder,
      CASE
        WHEN Rekeningmutaties.Bewaarder <> '' THEN
          Rekeningmutaties.Bewaarder 
        ELSE 
          Rekeningen.Depotbank 
      END AS 'BewaardDoor',
      Rekeningen.Rekening,
      Rekeningmutaties.Fonds,
      Fondsen.ISINcode,
      Fondsen.Valuta,
      sum( Rekeningmutaties.Aantal ) AS 'TotaalAantal',
      sum( Rekeningmutaties.Bedrag ) AS 'TotaalSaldo' 
    FROM
      Rekeningmutaties
      INNER JOIN Rekeningen ON 
        Rekeningmutaties.Rekening = Rekeningen.Rekening
      INNER JOIN Portefeuilles ON 
        Rekeningen.Portefeuille = Portefeuilles.Portefeuille
      LEFT JOIN Fondsen ON 
        Rekeningmutaties.Fonds = Fondsen.Fonds 
    WHERE
      Rekeningen.consolidatie = 0 AND
      Portefeuilles.Vermogensbeheerder = '".$this->vbArray[0]."'  AND 
      Boekdatum >= '".date("Y")."-01-01'  
    GROUP BY
      Portefeuilles.Portefeuille,
      Portefeuilles.Vermogensbeheerder,
      Portefeuilles.Depotbank,
      Rekeningen.Depotbank,
      Rekeningmutaties.Bewaarder,
      CASE
        WHEN Rekeningmutaties.Bewaarder <> '' THEN
          Rekeningmutaties.Bewaarder 
        ELSE 
          Rekeningen.Depotbank 
      END,
      Rekeningen.Rekening,
      Rekeningmutaties.Fonds,
      Fondsen.ISINcode,
      Fondsen.Valuta
    ";

  }

}
?>