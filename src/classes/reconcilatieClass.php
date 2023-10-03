<?php

/*
  AE-ICT source module
  Author  						: $Author: cvs $
  Laatste aanpassing	: $Date: 2020/05/11 14:45:12 $
  File Versie					: $Revision: 1.47 $

  $Log: reconcilatieClass.php,v $
  Revision 1.47  2020/05/11 14:45:12  cvs
  call 8621

  Revision 1.46  2020/05/11 14:43:41  cvs
  call 8621

  Revision 1.45  2020/03/09 13:37:47  cvs
  call 8464

  Revision 1.44  2020/02/05 13:57:33  cvs
  call 8264

  Revision 1.43  2020/01/14 13:22:29  cvs
  call 8293

  Revision 1.42  2019/10/09 09:59:47  cvs
  call 8025

  Revision 1.41  2019/10/07 07:59:43  cvs
  call 8024

  Revision 1.40  2019/08/27 08:26:03  cvs
  calls 8025, 7605, 7829

  Revision 1.39  2019/03/22 15:48:56  cvs
  x

  Revision 1.38  2019/01/20 12:28:10  rvv
  *** empty log message ***

  Revision 1.37  2019/01/18 15:17:39  cvs
  call 7048

  Revision 1.36  2019/01/18 08:23:42  cvs
  call 7347

  Revision 1.35  2018/12/10 14:12:28  cvs
  consolidatie aanpassingen

  Revision 1.34  2018/09/23 11:51:46  cvs
  call 4982

  Revision 1.33  2018/08/22 13:57:50  cvs
  call 5932

  Revision 1.32  2018/06/20 06:23:09  cvs
  call 3517

  Revision 1.31  2018/06/18 14:41:18  cvs
  call 3517

  Revision 1.30  2018/05/30 13:12:21  cvs
  call 6908

  Revision 1.29  2018/04/30 13:54:52  cvs
  call 5923

  Revision 1.28  2017/09/20 06:12:53  cvs
  megaupdate

  Revision 1.27  2017/06/26 14:31:26  cvs
  test1

  Revision 1.26  2017/06/26 14:25:34  cvs

  Revision 1.23  2017/06/02 11:51:23  cvs
  kasbank optiefonds

  Revision 1.22  2016/10/21 14:10:05  cvs
  call 5321

  Revision 1.21  2016/09/21 08:30:42  cvs
  call 5200

 */

class reconcilatieClass
{
  var $user;
  var $server = 1;
  var $testDate = "";
  var $vbArray = array();   // vermogensbeheerders in bankfile
  var $depotbank = "";
  var $batch = "";
  var $matchArray = array();
  var $batchverwerking = false;
  var $AirsVerwerkingIntern = true;  // in sommige batchverwerkingen AIRS deel apart aanroepen (false state)
  var $excludedDepots = array(
    "AAB",
    "AAB BE",
    "AABA",
    "BIL",
    "BIN",
    "CS",
    "GIRO",
    "FVL",
    "ING",
    "INT",
    "JUL BAER",
    "KAS",
    "LOM",
    "LYNX",
    "MDZ",
    "MPF",
    "OPT",
    "RABO",
    "BTC",
    "PIC",
    "TGB",
    "UBP",
    "UBS",
  );
  function reconcilatieClass($depotBank, $testDate)
  {
    global $USR, $__appvar;
    
    //include_once($__appvar["basedir"]."/html/rapport/rapportRekenClass.php");
    $depotBank = ($depotBank == "SNS")?"NIBC":$depotBank;
    $this->user = $USR;
    $this->testDate = $testDate;
    $this->depotbank = $depotBank;
    $this->batch = date("YmdHi");
  }

  /**
   * voegt vermogensbeheerder toe als nog niet in vbArray
   *
   * @param "vb" vermogensbeheerder
   */
  function addVB($vb, $p)
  {
    if (trim($vb) == "")
      return;
    $vb = strtoupper($vb);
    if (!in_array($vb, $this->vbArray))
    {
      $this->vbArray[] = $vb;
//      echo "<li>".$vb." = ".$p."</li>";
      ob_flush();flush();
    }
//    debug($this->vbArray);
  }



  /**
   * record toevoegen aan tijdelijke tabel
   *
   * @param "record" array met cash of sec data
   */
  function addRecord($record)
  {

    global $prb;
    $match = array();
    $record["batch"] = $this->batch;

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
//debug($this->getRekening($reknr)," getrek: $reknr");
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
//debug($record);
//debug($rekRec, $this->depotbank);
      
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
      ,`fileBankCode`=  '{$record["fileBankCode"]}'
      ,`isinCode`= ''
      ,`koers` = '{$record["koers"]}'
      ,`koersDatum` = '{$record["koersDatum"]}'
      ,`valuta`=  '{$record["valuta"]}'
      ,`positieBank`= '{$bankWaarde}'
      ,`positieAirs`= '{$airs}'
      ,`positieAirsGisteren`= '{$airsGisteren}'
      ,`verschil`=  '{$verschil}'
      ,`fondsCodeMatch`= '".implode(" / ", $match)."' 
      ,`batch`=  '{$this->batch}'";

    }  /////////////////////////////////////////////////////////// CASH END
    else
    {  /// sec
      
      if (!$rekRec = $this->getRekening($record["portefeuille"], "P"))
      {
        $rekRec["Portefeuille"] = $record["portefeuille"];
      }
      

     // debug($rekRec, " pnr");
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

        if ($this->depotbank == "IND") // call 10318
        {
          $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$record["ISIN"]."' AND Valuta = 'EUR'";
          $db1 = new DB();
          if ($fondsRec = $db1->lookupRecordByQuery($query))
          {
            $record["fonds"] = $fondsRec["Fonds"];
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
        ,`fileBankCode`=  '{$record["fileBankCode"]}'
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
        ,`batch`=  '{$this->batch}'";
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
    $bp = array();
    $ddd = 0;
    $datum = $this->testDate;
    $db = new DB();
    $query = "SELECT distinct(portefeuille) FROM tijdelijkeRecon WHERE `batch` LIKE '".$this->batch."%' AND add_user = '".$this->user."' ORDER BY portefeuille";
//    debug($query, "fillTableFormAIRS");
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
//      debug($portefeuille, $datum);
      $fondswaarden = $this->getAirsPortefeuilleWaarde($portefeuille, $datum);
//debug($fondswaarden);

      for ($x = 0; $x < count($fondswaarden); $x++)
      {
        $record = $fondswaarden[$x];
        $record["portefeuille"] = $portefeuille;
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
        $depotSearch = "(Portefeuilles.Depotbank = 'BIN'  OR Portefeuilles.Depotbank = 'BINB') ";
        break;
      case "SAXO":
        $depotSearch = "(Portefeuilles.Depotbank = 'SAXO'  OR Portefeuilles.Depotbank = 'SAXOB') ";
        break;
      case "SAR":
        $depotSearch = "(Portefeuilles.Depotbank = 'SAR'  OR Portefeuilles.Depotbank = 'SARCH') ";
        break;
      case "CS":
        $depotSearch = "(Portefeuilles.Depotbank = 'CS'  OR Portefeuilles.Depotbank = 'CS AG') ";
        break;
      case "AAB":
        $depotSearch = "(Portefeuilles.Depotbank = 'AAB'  OR Portefeuilles.Depotbank = 'AABIAM') ";
        break;
      case "NIBC":
      case "SNS":
        $depotSearch = "(Portefeuilles.Depotbank = 'SNS'  OR Portefeuilles.Depotbank = 'NIBC') ";
        break;
      default:
        $depotSearch = "Portefeuilles.Depotbank = '".$this->depotbank."' ";
    }
    
    
    $query = "
      SELECT
        Rekeningen.Portefeuille as portefeuille,
        Rekeningmutaties.Fonds as fonds,
        SUM(Rekeningmutaties.Aantal) AS totaalAantal
      FROM 
        Rekeningmutaties
      JOIN Rekeningen ON  
        Rekeningmutaties.Rekening  = Rekeningen.Rekening AND Rekeningen.consolidatie = '0'
      JOIN Portefeuilles ON 
        Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie = '0'
      WHERE
        Rekeningmutaties.Grootboekrekening = 'FONDS' AND
        YEAR(Rekeningmutaties.Boekdatum) = '".substr($datum, 0, 4)."' AND
        Rekeningmutaties.Verwerkt = '1' AND
        Rekeningmutaties.Boekdatum <= '".$datum."' AND 
        Portefeuilles.Portefeuille='".$portefeuille."' AND
        $depotSearch 
      GROUP BY 
        Rekeningen.Portefeuille,Rekeningmutaties.Fonds
      HAVING 
        round(totaalAantal,4) <> 0
      ORDER BY 
        Rekeningen.Portefeuille,Rekeningmutaties.Fonds; ";

    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $rec["type"] = "fondsen";
      $out[] = $rec;
      $p = $rec["portefeuille"];
    }
    return $out;
  }

  function getAirsPortefeuilleWaardeOverige($portefeuille)  // zonder depotbank aanduiding call 3517
  {
    $datum = $this->testDate;
    $db = new DB();

    $depotSearch = "Portefeuilles.Depotbank = '".$this->depotbank."' ";



    $query = "
      SELECT
        Rekeningen.Portefeuille as portefeuille,
        Rekeningmutaties.Fonds as fonds,
        SUM(Rekeningmutaties.Aantal) AS totaalAantal
      FROM 
        Rekeningmutaties
      JOIN Rekeningen ON  
        Rekeningmutaties.Rekening  = Rekeningen.Rekening AND Rekeningen.consolidatie = 0
      JOIN Portefeuilles ON 
        Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie = '0'
      WHERE
        Rekeningmutaties.Grootboekrekening = 'FONDS' AND
        YEAR(Rekeningmutaties.Boekdatum) = '".substr($datum, 0, 4)."' AND
        Rekeningmutaties.Verwerkt = '1' AND
        Rekeningmutaties.Boekdatum <= '".$datum."' AND 
        Portefeuilles.Portefeuille='".$portefeuille."' AND
        $depotSearch 
      GROUP BY 
        Rekeningen.Portefeuille,Rekeningmutaties.Fonds
      HAVING 
        round(totaalAantal,4) <> 0
      ORDER BY 
        Rekeningen.Portefeuille,Rekeningmutaties.Fonds; ";

    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $rec["type"] = "fondsen";
      $rec["depotbank"] = $this->depotbank;
      $out[] = $rec;
      $p = $rec["portefeuille"];
    }
    return $out;
  }

  
  function getAirsPortefeuilleWaardeDuplicaat($portefeuille, $datum)
  {

    $db = new DB();
    $portTotaal = 0;

    
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
        Rekeningmutaties.Grootboekrekening = 'FONDS' AND
        YEAR(Rekeningmutaties.Boekdatum) = '".substr($datum, 0, 4)."' AND
        Rekeningmutaties.Verwerkt = '1' AND
        Rekeningmutaties.Boekdatum <= '".$datum."' AND 
        Portefeuilles.consolidatie = '0' AND
        Portefeuilles.Portefeuille='".$portefeuille."' 
      GROUP BY 
        Rekeningen.Portefeuille,Rekeningmutaties.Fonds
      HAVING 
        round(totaalAantal,4) <> 0
      ORDER BY 
        Rekeningen.Portefeuille,Rekeningmutaties.Fonds; ";

    $db->executeQuery($query);
    $stukken = array();
//    $stukken["query"] = $query;
    while ($rec = $db->nextRecord())
    {
      $stukken[$rec["fonds"]] += round($rec["totaalAantal"],4);
      $portTotaal += round($rec["totaalAantal"],4);
    }
    $stukken["portefeuilleTotaal"] = $portTotaal;
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

      $query = "UPDATE tijdelijkeRecon SET ";
      $query .= "
       `positieAirs`= '{$record["totaalAantal"]}'
      ,`verschil`=  '".($reconRec["positieBank"] - $record["totaalAantal"])."'
      ,`fondsCodeMatch`= '".$reconRec["match"]."'  
      ,`fonds` = '".addslashes($fondRec["Fonds"])."'  
        
      ,`fondsImportcode`= '{$fondRec["FondsImportCode"]}'";
      //  ,`depotbankFondsCode` = '".addslashes($record["fBankCode"])."'

      $query .= "WHERE id = ".$reconRec["id"];
    }
    else
    {
      $portRec = $this->getPortefeuille("Portefeuille = '".$portefeuille."'");
      $query = "INSERT INTO tijdelijkeRecon SET ";
      $query .= "
       `add_user` = '".$this->user."'  
      ,`add_date` = NOW()
      ,`change_user` = '".$this->user."'
      ,`change_date` = NOW()
      ,`portefeuille`=  '{$record["portefeuille"]}'
      ,`rekeningnummer` = ''
      ,`cashPositie`=  0
      ,`fonds`=  '".addslashes($fondRec["Fonds"])."'
      ,`importCode`= '' 
      ,`fondsImportcode`= '{$fondRec["FondsImportCode"]}' 
      ,`Einddatum`=  '{$portRec["Einddatum"]}'
      ,`reconDatum`=  '".$this->testDate."'       
      ,`Accountmanager`=  '{$portRec["Accountmanager"]}'
      ,`depotbankFondsCode`=  '{$record["bankCode"]}'
      ,`isinCode`= '{$fondRec["ISINCode"]}'
      ,`valuta`=  '{$fondRec["Valuta"]}'
      ,`positieBank`= '0'
      ,`positieAirs`= '{$record["totaalAantal"]}'
      ,`verschil`=  '".-1 * $record["totaalAantal"]."'  
      ,`fondsCodeMatch`= 'Geen bank'
      ,`vermogensbeheerder` = '{$portRec["Vermogensbeheerder"]}'
      ,`depotbank`=  '{$portRec["Depotbank"]}'
      ,`client`=  '{$portRec["Client"]}'
      ,`batch`=  '{$this->batch}'";
    }

    $db->executeQuery($query);
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
      case "ing":
        $bankcode = $fondsRec["INGCode"];
        $matchText = "via INGCode";
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
      case "lynx":
        $bankcode = $fondsRec["LYNXcode"];
        $matchText = "via LynxCode";
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
      case "aabiam":
      case "aabh":
      case "aaba":
        $bankcode = $fondsRec["AABCode"];
        $matchText = "via AABCode";
        break;
      case "aab be":
        $bankcode = $fondsRec["aabbeCode"];
        $matchText = "via AABBECode";
        break;
      case "jul baer":
        $bankcode = $fondsRec["JBcode"];
        $matchText = "via JBcode";
        break;
      case "jblux":
        $bankcode = $fondsRec["JBLuxcode"];
        $matchText = "via JBLuxcode";
        break;
      case "hhb":
        $bankcode = $fondsRec["HHBcode"];
        $matchText = "via HHBcode";
        break;
      case "hsbc":
        $bankcode = $fondsRec["HSBCcode"];
        $matchText = "via HSBCcode";
        break;
      case "bgl":
        $bankcode = $fondsRec["BNPBGLcode"];
        $matchText = "via BNPBGLcode";
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
      case "caw":
        $bankcode = $fondsRec["CAWcode"];
        $matchText = "via CAWcode";
        break;
      case "rabo":
        $bankcode = $fondsRec["raboCode"];
        $matchText = "via raboCode";
        break;
      case "ubs":
        $bankcode = $fondsRec["UBScode"];
        $matchText = "via UBSCode";
        break;
      case "ubsl":
        $bankcode = $fondsRec["UBSLcode"];
        $matchText = "via UBSLcode";
        break;
      case "kbc":
        $bankcode = $fondsRec["KBCcode"];
        $matchText = "via KBCcode";
        break;
      case "sar":
      case "sarch":
        $bankcode = $fondsRec["Sarasincode"];
        $matchText = "via Sarasincode";
        break;
      case "knox":
        $bankcode = $fondsRec["KNOXcode"];
        $matchText = "via KNOXcode";
        break;
      case "dil":
        $bankcode = $fondsRec["Dierickscode"];
        $matchText = "via Dierickscode";
        break;
      case "saxo":
      case "saxob":
        $bankcode = $fondsRec["SAXOcode"];
        $matchText = "via SAXOcode";
        break;
      case "cs":
      case "cs ag":
      case "mdz":
      case "ind":

        $bankcode = "";  // nog geen bankcode bekend..

    }

    if ($this->batchverwerking)
    {
      $extraWhere = " AND batch = '".$this->batch."' ";
    }
    else
    {
      $extraWhere = "";
    }

    if ($bankcode <> "")
    {
      $query = "SELECT * FROM tijdelijkeRecon WHERE add_user = '".$this->user."' $extraWhere AND depotbankFondsCode = '".$bankcode."' AND portefeuille = '".$portefeuille."'";

      if ($reconRec = $db->lookupRecordByQuery($query))
      {
        $reconRec["match"] = $matchText;
        return $reconRec;
      }
    }
    else
    {
      $query = "SELECT * FROM tijdelijkeRecon WHERE add_user = '".$this->user."' $extraWhere AND isinCode = '".$fondsRec["ISINCode"]."' AND valuta = '".$fondsRec["Valuta"]."' AND portefeuille = '".$portefeuille."'";

      if ($fondsRec["ISINCode"] <> "")
      {
//        debug($query);
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

    if ($this->batchverwerking)
    {
      $extraWhere = " AND batch = '".$this->batch."' ";
    }
    else
    {
      $extraWhere = "";
    }

    $query = "SELECT * FROM tijdelijkeRecon WHERE add_user = '".$this->user."' $extraWhere AND portefeuille = '".$portefeuille."' AND depotbankFondsCode = '".$bankCode."' ";
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
      case "BIN":
        $depotSearch = "(Rekeningen.Depotbank = 'BIN'  OR Rekeningen.Depotbank = 'BINB') ";
        break;
      case "SAXO":
        $depotSearch = "(Rekeningen.Depotbank = 'SAXO'  OR Rekeningen.Depotbank = 'SAXOB') ";
        break;
      case "SAR":
        $depotSearch = "(Rekeningen.Depotbank = 'SAR'  OR Rekeningen.Depotbank = 'SARCH') ";
        break;
      case "CS":
        $depotSearch = "(Rekeningen.Depotbank = 'CS'  OR Rekeningen.Depotbank = 'CS AG') ";
        break;
      case "NIBC":
      case "SNS":
        $depotSearch = "(Rekeningen.Depotbank = 'SNS'  OR Rekeningen.Depotbank = 'NIBC') ";
        break;
      case "AAB":
        $depotSearch = "(Rekeningen.Depotbank = 'AAB'  OR Rekeningen.Depotbank = 'AABIAM') ";
        break;
      default:
       $depotSearch = "Rekeningen.Depotbank = '".$this->depotbank."' ";
    }
    
    
    $db = new DB();
    $query = "SELECT * FROM Rekeningen WHERE `RekeningDepotbank` = '".$rekeningNr."' AND $depotSearch  AND consolidatie = 0 ";
    //debug($query," P1");
    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rec["Rekening"];
    }
    else
    {
      $query = "SELECT * FROM Rekeningen WHERE `Rekening` = '".$rekeningNr."' AND $depotSearch AND consolidatie = 0";
      //debug($query," P2");
      if ($rec = $db->lookupRecordByQuery($query))
      {
        return $rekeningNr;
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
      case "SAXO":
        $depotSearch = "(Portefeuilles.Depotbank = 'SAXO'  OR Portefeuilles.Depotbank = 'SAXOB') ";
        break;
      case "SAR":
        $depotSearch = "(Portefeuilles.Depotbank = 'SAR'  OR Portefeuilles.Depotbank = 'SARCH') ";
        break;
      case "NIBC":
      case "SNS":
        $depotSearch = "(Portefeuilles.Depotbank = 'SNS'  OR Portefeuilles.Depotbank = 'NIBC') ";
        break;
      case "CS":
        $depotSearch = "(Portefeuilles.Depotbank = 'CS'  OR Portefeuilles.Depotbank = 'CS AG') ";
        break;
      case "AAB":
        $depotSearch = "(Portefeuilles.Depotbank = 'AAB'  OR Portefeuilles.Depotbank = 'AABIAM') ";
        break;
      default:
        $depotSearch = "Portefeuilles.Depotbank = '".$this->depotbank."' ";
    }
    $depotSearch .= " AND consolidatie = 0 ";
    
    $query = "SELECT * FROM Portefeuilles WHERE `PortefeuilleDepotbank` = '".$portefeuilleNr."' AND ".$depotSearch." AND consolidatie = 0";
    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rec["Portefeuille"];
    }
    else
    {
      $query = "SELECT * FROM Portefeuilles WHERE `Portefeuille` = '".$portefeuilleNr."' AND ".$depotSearch ." AND consolidatie = 0";
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
  
  /**
   * Haal dagboek gegevens op
   *
   * @param "search" where clause
   * @param "part" defineer output
   */
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
        case "SAXO":
          $search = "Rekening = '".$reknr."'  AND (Rekeningen.Depotbank = 'SAXO'  OR Rekeningen.Depotbank = 'SAXOB') ";
          break;
        case "SAR":
          $search = "Rekening = '".$reknr."'  AND (Rekeningen.Depotbank = 'SAR'  OR Rekeningen.Depotbank = 'SARCH') ";
          break;
        case "CS":
          $search = "Rekening = '".$reknr."'  AND (Rekeningen.Depotbank = 'CS'  OR Rekeningen.Depotbank = 'CS AG') ";
          break;
        case "NIBC":
        case "SNS":
          $search = "Rekening = '".$reknr."'  AND (Rekeningen.Depotbank = 'SNS'  OR Rekeningen.Depotbank = 'NIBC') ";
          break;
        case "AAB":
          $search = "Rekening = '".$reknr."'  AND (Rekeningen.Depotbank = 'AAB'  OR Rekeningen.Depotbank = 'AABIAM') ";
          break;        
        default:
          $search = "Rekening = '".$reknr."'  AND Rekeningen.Depotbank = '".$this->depotbank."' ";
      }
      $search .= " AND Rekeningen.consolidatie = 0 ";
      
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
        case "SAXO":
          $depotSearch = "(Portefeuilles.Depotbank = 'SAXO'  OR Portefeuilles.Depotbank = 'SAXOB') ";
          break;
        case "SAR":
          $depotSearch = "(Portefeuilles.Depotbank = 'SAR'  OR Portefeuilles.Depotbank = 'SARCH') ";
          break;
        case "CS":
          $depotSearch = "(Portefeuilles.Depotbank = 'CS'  OR Portefeuilles.Depotbank = 'CS AG') ";
          break;
        case "NIBC":
        case "SNS":
          $depotSearch = "(Portefeuilles.Depotbank = 'SNS'  OR Portefeuilles.Depotbank = 'NIBC') ";
          break;
        case "AAB":
          $depotSearch = "(Portefeuilles.Depotbank = 'AAB'  OR Portefeuilles.Depotbank = 'AABIAM') ";
          break;
        default:
          $depotSearch = "Portefeuilles.Depotbank = '".$this->depotbank."' ";
      }
      
      $reknr = $this->getAirsPortefeuilleNr($nr);
      $search = "Portefeuilles.Portefeuille = '".$reknr."' AND Portefeuilles.consolidatie = 0  AND ".$depotSearch ;
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
            $search";
              
    
//    debug($q);
    //echo $q."<hr/>";
      
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
    $q = "SELECT * FROM Portefeuilles WHERE $search AND consolidatie = 0 ";

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
    $query = "SELECT * FROM Portefeuilles WHERE  Portefeuille = '".$portefeuilleInCsv."' AND Einddatum > NOW() AND consolidatie = 0";
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
    
    switch($this->depotbank)
    {
//      case "AAB BE": 
//        break;
      case "BIN":
        $depotSearch = "(Portefeuilles.Depotbank = 'BIN'  OR Portefeuilles.Depotbank = 'BINB') ";
        break;
      case "SAXO":
        $depotSearch = "(Portefeuilles.Depotbank = 'SAXO'  OR Portefeuilles.Depotbank = 'SAXOB') ";
        break;
      case "SAR":
        $depotSearch = "(Portefeuilles.Depotbank = 'SAR'  OR Portefeuilles.Depotbank = 'SARCH') ";
        break;
      case "CS":
        $depotSearch = "(Portefeuilles.Depotbank = 'CS'  OR Portefeuilles.Depotbank = 'CS AG') ";
        break;
      case "NIBC":
      case "SNS":
        $depotSearch = "(Portefeuilles.Depotbank = 'SNS'  OR Portefeuilles.Depotbank = 'NIBC') ";
        break;
      case "AAB":
        $depotSearch = "(Portefeuilles.Depotbank = 'AAB'  OR Portefeuilles.Depotbank = 'AABIAM') ";
        break;
      default:
        $depotSearch = "Portefeuilles.Depotbank = '".$this->depotbank."' ";
    }
    
    echo $query = "
    SELECT 
      distinct(Portefeuille) 
    FROM 
      Portefeuilles 
    WHERE  
      Portefeuilles.Vermogensbeheerder IN ( '".implode("','", $this->vbArray)."' ) AND 
      $depotSearch AND
      Portefeuilles.Einddatum > NOW() AND
      Portefeuilles.consolidatie = 0 ";
//    debug($query);
//    debug($bp,"BP vulling");
    $db->executeQuery($query);
    while ($portRec = $db->nextRecord())
    {
      if (in_array($portRec["Portefeuille"], $bp))
      {
        $pSkip[] = $portRec["Portefeuille"];
      }
      else
      {
        $pAirs[] = $portRec["Portefeuille"];
      }  
    }
//    debug($pAirs, "pairs");
//    debug($pSkip, "skip");
    for ($y=0; $y < count($pAirs); $y++)
    {
      echo "X.";
      $portefeuille = $pAirs[$y];
      
      $this->fillPortefeuilleInfo($portefeuille, true);   // recon record aanmaken met AIRS data
      $fondswaarden = $this->getAirsPortefeuilleWaarde($portefeuille, $datum);

      for ($x = 0; $x < count($fondswaarden); $x++)
      {
        $record = $fondswaarden[$x];
        $record["portefeuille"] = $portefeuille;
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
      case "SAXO":
        $depotSearch = " (Rekeningen.Depotbank = 'SAXO' OR Rekeningen.Depotbank = 'SAXOB') ";
        break;
      case "SAR":
        $depotSearch = " (Rekeningen.Depotbank = 'SAR' OR Rekeningen.Depotbank = 'SARCH') ";
        break;
      case "CS":
        $depotSearch = " (Rekeningen.Depotbank = 'CS' OR Rekeningen.Depotbank = 'CS AG') ";
        break;
      case "NIBC":
      case "SNS":
        $depotSearch = "(Rekeningen.Depotbank = 'SNS'  OR Rekeningen.Depotbank = 'NIBC') ";
        break;      
      case "AAB":
        $depotSearch = "(Rekeningen.Depotbank = 'AAB'  OR Rekeningen.Depotbank = 'AABIAM') ";
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
      Rekeningen ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Rekeningen.consolidatie = 0
    WHERE  
      Portefeuilles.Vermogensbeheerder IN ( '".implode("','", $this->vbArray)."' ) AND 
      $depotSearch AND
      Portefeuilles.Einddatum > NOW() AND
      Rekeningen.Rekening <> '' AND
      Rekeningen.Memoriaal = 0 AND
      Portefeuilles.consolidatie = 0 AND
      Rekeningen.Inactief = 0 ";
//debug($query);
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

  /**
   * reconcilatieClass::getAIRSvaluta()
   * 
   * Haal het saldo van het rekeningnummer op
   * 
   * geeft false als er geen rekeningmutaties gevonden zijn.
   */
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
      case "SAXO":
        $depotSearch = " (Rekeningen.Depotbank = 'SAXO' OR Rekeningen.Depotbank = 'SAXOB') ";
        break;
      case "SAR":
        $depotSearch = " (Rekeningen.Depotbank = 'SAR' OR Rekeningen.Depotbank = 'SARCH') ";
        break;
      case "NIBC":
      case "SNS":
        $depotSearch = " (Rekeningen.Depotbank = 'SNS'  OR Rekeningen.Depotbank = 'NIBC') ";
        break;
      case "CS":
        $depotSearch = " (Rekeningen.Depotbank = 'CS' OR Rekeningen.Depotbank = 'CS AG') ";
        break;
      case "AAB":
        $depotSearch = "(Rekeningen.Depotbank = 'AAB'  OR Rekeningen.Depotbank = 'AABIAM') ";
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
    	Rekeningmutaties.Rekening = Rekeningen.Rekening AND
    	Rekeningmutaties.boekdatum >= '".substr($datum, 0, 4)."' AND
      Rekeningmutaties.Rekening = '".$rekeningnr."' AND
      Rekeningen.consolidatie = 0 AND
      $depotSearch AND
    	$qExtra
    GROUP BY 
      Rekeningen.Valuta
    ORDER BY 
      Rekeningen.Valuta";
//debug($query);
    if ($data = $tmpDB->lookupRecordByQuery($query))
    {
      return $data[totaal];
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
      if (strtoupper($portRec["Depotbank"]) <> $this->depotbank )
      {
        $q = ",`fondsCodeMatch`= 'Port. Depotbank afwijking'";
      }
      else
      {
        $q = "";
      }
    }
    
    
    if ($portRec["Depotbank"] == "BINB" AND $this->depotbank == "BIN")  // call 3528
    {
      $q = "";
    }
    if ($portRec["Depotbank"] == "SAXOB" AND $this->depotbank == "SAXO")
    {
      $q = "";
    }
    if ($portRec["Depotbank"] == "SARCH" AND $this->depotbank == "SAR")
    {
      $q = "";
    }
    if ($portRec["Depotbank"] == "CS AG" AND $this->depotbank == "CS")  
    {
      $q = "";
    }

    if ($portRec["Depotbank"] == "AABIAM" AND $this->depotbank == "AAB")  
    {
      $q = "";
    }

    if ($portRec["Depotbank"] == "NIBC" AND $this->depotbank == "SNS")
    {
      $q = "";
    }

    if ($insertRecord)     
    {
      $q = ",`fondsCodeMatch`= 'Geen BANK'";
      $query = " 
        INSERT INTO tijdelijkeRecon SET     
          `add_date` = NOW(),
          `add_user` = '".$this->user."' ,
          `batch`=  '{$this->batch}', ";
          
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

}
?>