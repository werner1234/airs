<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/11 16:20:55 $
File Versie					: $Revision: 1.21 $

$Log: Modelrapport.php,v $
Revision 1.21  2020/03/11 16:20:55  rvv
*** empty log message ***

Revision 1.20  2019/02/21 08:10:19  rvv
*** empty log message ***

Revision 1.19  2019/02/20 16:48:30  rvv
*** empty log message ***

Revision 1.18  2018/09/23 17:14:23  cvs
call 7175

Revision 1.17  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.16  2017/10/28 17:59:16  rvv
*** empty log message ***

Revision 1.15  2015/10/29 13:14:03  rvv
*** empty log message ***

Revision 1.14  2015/10/07 19:38:13  rvv
*** empty log message ***

Revision 1.13  2014/08/23 15:41:31  rvv
*** empty log message ***

Revision 1.12  2013/09/01 13:32:39  rvv
*** empty log message ***

Revision 1.11  2011/09/14 09:26:56  rvv
*** empty log message ***

Revision 1.10  2011/02/24 17:43:53  rvv
*** empty log message ***

Revision 1.9  2008/06/30 07:58:44  rvv
*** empty log message ***

Revision 1.8  2007/08/02 14:46:01  rvv
*** empty log message ***

Revision 1.7  2006/11/03 11:24:04  rvv
Na user update

Revision 1.6  2006/10/31 12:01:55  rvv
Voor user update

Revision 1.5  2006/01/25 10:29:32  jwellner
bugfix Modelcontrole

Revision 1.4  2006/01/23 14:13:43  jwellner
no message

Revision 1.3  2005/12/09 13:28:51  jwellner
bugfix managementoverzicht

Revision 1.2  2005/12/09 12:16:51  jwellner
ajax lib toegevoegd.

Revision 1.1  2005/12/08 13:57:05  jwellner
Modelcontrole rapport

Revision 1.3  2005/11/07 10:29:17  jwellner
no message

Revision 1.2  2005/10/21 08:08:56  jwellner
lock file bij complete database updates

Revision 1.1  2005/10/20 07:15:23  jwellner
no message

*/

define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_fpdf.php");
include_once("rapportRekenClass.php");

class Modelrapport
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Modelrapport( $selectData )
	{

		$this->selectData = $selectData;
		$this->pdf->excelData 	= array();

		$this->pdf = new PDFRapport('P','mm');
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 270;
		$this->pdf->__appvar = $this->__appvar;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);

		$this->pdf->tmdatum = $this->selectData['datumTm'];
		// selectdata ook aan PDF geven
		$this->pdf->selectData = $this->selectData;

		$this->orderby = " Portefeuilles.ClientVermogensbeheerder ";
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function printKop($title)
	{
		$this->pdf->SetFont("Times", "bi", 10);
		$this->pdf->Cell(100 , 4 , $title , 0, 1, "L");
		$this->pdf->SetFont("Times", "", 10);

		$this->pdf->excelData[] = array($title);
	}
  
  

	function calculate()
	{
		global $__appvar;
		$this->pdf->__appvar = $this->__appvar;
		$einddatum = jul2sql($this->selectData['datumTm']);
		$jaar = date("Y",$this->selectData['datumTm']);
    $portefTotaal = $this->selectData["mutatieportefeuille_vastbedrag"];
    
		if(count($this->selectData['selectedModelportefeuilles']) > 0 )
    {
      $alleFondsen=array();
      $nieuweWaarde=0;
      $eurRekeningKey='';
      foreach($this->selectData['selectedModelportefeuilles'] as $portefeuilleData)
      {
        $portefeuilleSplit=explode('|',$portefeuilleData);
        $percentage=$portefeuilleSplit[0]/100;
        $portefeuille=$portefeuilleSplit[1];
        $DB3 = new DB();
        $query = "SELECT Fixed FROM ModelPortefeuilles WHERE Portefeuille='" . $this->selectData['mutatieportefeuille_portefeuille'] . "'";
        $DB3->SQL($query);
        $DB3->Query();
        $modelType = $DB3->nextRecord();
        if ($modelType['Fixed'] == 1)
        {
          $portefeuilleData = berekenFixedModelPortefeuille($portefeuille, $einddatum);
        }
        elseif ($modelType['Fixed'] == 3)
        {
          $portefeuilleData = berekenMeervoudigeModelPortefeuille($portefeuille, $einddatum,$this->selectData['mutatieportefeuille_portefeuille']);
        }
        else
        {
          $portefeuilleData = berekenPortefeuilleWaarde($portefeuille, $einddatum);
        }
        $modelTotaal=0;
        foreach($portefeuilleData as $instrument)
        {
          $modelTotaal+=$instrument['actuelePortefeuilleWaardeEuro'];
        }

        $eurRekeningKey='';
        foreach($portefeuilleData as $instrument)
        {
          $key = '|' . $instrument['type'] . '|' . $instrument['fonds'] . '|' . $instrument['rekening'] . '|';
          if($instrument['type']=='fondsen')
          {
            $instrument['totaalAantal'] = round(($portefTotaal / $modelTotaal) * $instrument['totaalAantal']*$percentage);
 
            if(isset($instrument['FondsEenheid']))
            {
              $instrument['fondsEenheid']=$instrument['FondsEenheid'];
              unset($instrument['FondsEenheid']);
            }
            $waardeValuta=$instrument['totaalAantal'] * $instrument['fondsEenheid'] * $instrument['actueleFonds'];
            $waardeEur=$waardeValuta * $instrument['actueleValuta'];
            if (!isset($alleFondsen[$key]))
            {
              $alleFondsen[$key] = $instrument;
              
              $alleFondsen[$key]['actuelePortefeuilleWaardeEuro'] = $waardeEur;
              $alleFondsen[$key]['actuelePortefeuilleWaardeInValuta'] = $waardeValuta;
            }
            else
            {
              $alleFondsen[$key]['actuelePortefeuilleWaardeEuro'] += $waardeEur;
              $alleFondsen[$key]['actuelePortefeuilleWaardeInValuta'] += $waardeValuta;
            }
            $nieuweWaarde+=$waardeEur;
          }
          else
          {
            $instrument['actuelePortefeuilleWaardeEuro'] = ($portefTotaal / $modelTotaal) * $instrument['actuelePortefeuilleWaardeEuro']*$percentage;
            $nieuweWaarde+=$instrument['actuelePortefeuilleWaardeEuro'];
            $instrument['actuelePortefeuilleWaardeInValuta'] = ($portefTotaal / $modelTotaal) * $instrument['actuelePortefeuilleWaardeInValuta']*$percentage;
            if($instrument['actuelePortefeuilleWaardeInValuta']==0 && $instrument['valuta']=='EUR')
              $instrument['actuelePortefeuilleWaardeInValuta']=$instrument['actuelePortefeuilleWaardeEuro'];
            if (!isset($alleFondsen[$key]))
            {
              $alleFondsen[$key] = $instrument;
            }
            else
            {
              $alleFondsen[$key]['actuelePortefeuilleWaardeEuro'] += $instrument['actuelePortefeuilleWaardeEuro'];
              $alleFondsen[$key]['actuelePortefeuilleWaardeInValuta'] += $instrument['actuelePortefeuilleWaardeInValuta'];
            }
            if($instrument['type']=='rekening' && $instrument['valuta']=='EUR')
            {
              $eurRekeningKey=$key;
            }
          }
        }
      }
      $waardeCorrectie=$portefTotaal-$nieuweWaarde;
      if($eurRekeningKey=='' && $waardeCorrectie<>0)
      {
        logscherm("Geen EUR rekening gevonden voor correctie van $waardeCorrectie .");exit;
      }
      
      if($eurRekeningKey<>'' && $waardeCorrectie<>0)
      {
        $alleFondsen[$eurRekeningKey]['actuelePortefeuilleWaardeEuro'] +=$waardeCorrectie;
        $alleFondsen[$eurRekeningKey]['actuelePortefeuilleWaardeInValuta'] +=$waardeCorrectie;
      }
   // listarray($alleFondsen);
      
      vulTijdelijkeTabel(  array_values($alleFondsen), $this->selectData['mutatieportefeuille_portefeuille'], $einddatum);
    }
    else
    {
      // selectie scherm.
      $extraquery = " AND Portefeuilles.Portefeuille = '" . $this->selectData['modelcontrole_portefeuille'] . "' ";
  
      // selecteer alleen portefeuilles waar het fonds voorkomt!
      $q = " SELECT " .
        " Portefeuilles.Portefeuille, " .
        " Portefeuilles.Startdatum, " .
        " Portefeuilles.Client, " .
        " Portefeuilles.Depotbank, " .
        " Clienten.Naam  " .
        " FROM (Portefeuilles, Clienten)  WHERE " .
        " Portefeuilles.Client = Clienten.Client " . $extraquery .
        " ORDER BY " . $this->orderby;
  
      $DB = new DB();
      $DB->SQL($q);
      $DB->Query();
  
      $records = $DB->records();
  
      if ($this->progressbar)
      {
        $this->progressbar->moveStep(0);
        $pro_step = 0;
        $pro_multiplier = 100 / $records;
      }
  
      // zet modelportefeuille in tijdelijke tabel.
      $DB3 = new DB();
      $query = "SELECT Fixed FROM ModelPortefeuilles WHERE Portefeuille='" . $this->selectData['mutatieportefeuille_portefeuille'] . "'";
      $DB3->SQL($query);
      $DB3->Query();
      $modelType = $DB3->nextRecord();
      if ($modelType['Fixed'] == 1)
      {
        $portefeuilleData = berekenFixedModelPortefeuille($this->pdf->selectData['mutatieportefeuille_portefeuille'], $einddatum);
      }
      elseif ($modelType['Fixed'] == 3)
      {
        $portefeuilleData = berekenMeervoudigeModelPortefeuille($this->pdf->selectData['mutatieportefeuille_portefeuille'], $einddatum,$this->selectData['mutatieportefeuille_portefeuille']);
      }
      else
      {
        $portefeuilleData = berekenPortefeuilleWaarde($this->pdf->selectData['mutatieportefeuille_portefeuille'], $einddatum);
      }
  

      //verwijderTijdelijkeTabel($this->selectData['mutatieportefeuille_portefeuille'],$einddatum);
      vulTijdelijkeTabel($portefeuilleData, $this->selectData['mutatieportefeuille_portefeuille'], $einddatum);
  
      // bereken totaal waarde model
      $query = "SELECT IFNULL(SUM(actuelePortefeuilleWaardeEuro),0) AS totaal FROM TijdelijkeRapportage WHERE " .
        " rapportageDatum ='" . $einddatum . "' AND " .
        " portefeuille = '" . $this->selectData['mutatieportefeuille_portefeuille'] . "' "
        . $__appvar['TijdelijkeRapportageMaakUniek'];
      debugSpecial($query, __FILE__, __LINE__);
  
      $DB3->SQL($query);
      $DB3->Query();
      $modelwaarde = $DB3->nextRecord();
      $modelTotaal = $modelwaarde['totaal'];
  
      $portefTotaal = $this->selectData["mutatieportefeuille_vastbedrag"];
  
      // DELETE FROM Tijdelijke tabel where <> 'fondsen';
      //$query = "DELETE FROM TijdelijkeRapportage WHERE ".
      //				 " rapportageDatum ='".$einddatum."' AND ".
      //				 " portefeuille = '".$this->selectData[modelcontrole_portefeuille]."' AND type <> 'fondsen'";
  
      $DB3->SQL($query);
      $DB3->Query();
  
      // loopje over TijdelijkeRapportage en SET waarden op $modelTotaal / $portefTotaal * waarde/aantal
      $query = "SELECT * FROM TijdelijkeRapportage WHERE " .
        " rapportageDatum ='" . $einddatum . "' AND " .
        " portefeuille = '" . $this->selectData['mutatieportefeuille_portefeuille'] . "' "
        . $__appvar['TijdelijkeRapportageMaakUniek'];
      debugSpecial($query, __FILE__, __LINE__);
      $DB3->SQL($query);
      $DB3->Query();
      $rekeningId = 0;
      while ($data = $DB3->nextRecord())
      {
        $totaalAantal = round(($portefTotaal / $modelTotaal) * $data['totaalAantal']);
    
        if ($data['type'] == "fondsen")
        {
          $update = "UPDATE TijdelijkeRapportage SET " .
            "totaalAantal = '" . $totaalAantal . "',  " .
            "actuelePortefeuilleWaardeInValuta = (" . $totaalAantal . " * fondsEenheid * actueleFonds),  " .
            "actuelePortefeuilleWaardeEuro = (" . $totaalAantal . " * fondsEenheid * actueleFonds * actueleValuta) " .
            "WHERE id = '" . $data[id] . "'";
        }
        else
        {
          if ($data['type'] == "rekening" && $data['valuta'] == 'EUR')
          {
            $rekeningId = $data['id'];
          }
          $update = "UPDATE TijdelijkeRapportage SET " .
            "actuelePortefeuilleWaardeInValuta = (" . ($portefTotaal / $modelTotaal) . " * actuelePortefeuilleWaardeInValuta),  " .
            "actuelePortefeuilleWaardeEuro = (" . ($portefTotaal / $modelTotaal) . " * actuelePortefeuilleWaardeEuro) " .
            "WHERE id = '" . $data['id'] . "'";
        }
        $DB->SQL($update);
        $DB->Query();
      }
  
      if ($rekeningId <> 0)
      {
        $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ,
       SUM(IF(TijdelijkeRapportage.type='rekening',actuelePortefeuilleWaardeEuro,0)) AS totaalLiq 
        FROM TijdelijkeRapportage WHERE " .
          " rapportageDatum ='" . $einddatum . "' AND " .
          " portefeuille = '" . $this->selectData['mutatieportefeuille_portefeuille'] . "' "
          . $__appvar['TijdelijkeRapportageMaakUniek'];
        debugSpecial($query, __FILE__, __LINE__);
    
        $DB3->SQL($query);
        $DB3->Query();
        $nieuweWaarde = $DB3->nextRecord();
    
        $rekeningCorrectie = $portefTotaal - $nieuweWaarde['totaal'];
        if ($nieuweWaarde['totaalLiq'] + rekeningCorrectie > 0)
        {
          $query = "UPDATE TijdelijkeRapportage SET
        actuelePortefeuilleWaardeInValuta=actuelePortefeuilleWaardeInValuta+$rekeningCorrectie ,
        actuelePortefeuilleWaardeEuro=actuelePortefeuilleWaardeEuro+$rekeningCorrectie 
        WHERE id=$rekeningId ";
          $DB3->SQL($query);
          $DB3->Query();
        }
      }
    }
		// do rapportage stuff...

		loadLayoutSettings($this->pdf, $this->selectData['mutatieportefeuille_portefeuille']);
  
    if(is_file($__appvar["basedir"].'/html/rapport/include/RapportMOD_L'.$this->pdf->portefeuilledata['Layout'].'.php'))
    {
      include_once($__appvar["basedir"].'/html/rapport/include/RapportMOD_L'.$this->pdf->portefeuilledata['Layout'].'.php');
      $rapportclass='RapportMOD_L'.$this->pdf->portefeuilledata['Layout'];
 	  	$rapport = new $rapportclass($this->pdf, $this->selectData['mutatieportefeuille_portefeuille'], $einddatum);
	  	$rapport->writeRapport();    
    }
    elseif(is_file($__appvar["basedir"].'/html/rapport/include/layout_'.$this->pdf->portefeuilledata['Layout'].'/RapportMOD_L'.$this->pdf->portefeuilledata['Layout'].'.php'))
    {
      include_once($__appvar["basedir"].'/html/rapport/include/layout_'.$this->pdf->portefeuilledata['Layout'].'/RapportMOD_L'.$this->pdf->portefeuilledata['Layout'].'.php');
      $rapportclass='RapportMOD_L'.$this->pdf->portefeuilledata['Layout'];
      $rapport = new $rapportclass($this->pdf, $this->selectData['mutatieportefeuille_portefeuille'], $einddatum);
      $rapport->writeRapport();
    }
    else
    {
	  	$rapport = new RapportMOD($this->pdf, $this->selectData['mutatieportefeuille_portefeuille'], $einddatum);
	  	$rapport->writeRapport();
    }
		verwijderTijdelijkeTabel($this->selectData['mutatieportefeuille_portefeuille'],$einddatum);
		if($this->progressbar)
			$this->progressbar->hide();

	}

	function writeRapport()
	{
		$this->calculate();
	}

	function OutputCSV($filename, $type)
	{
		if($fp = fopen($filename,"w+"))
		{
			$exceldata = generateCSV($this->pdf->excelData);
			fwrite($fp,$exceldata);
			fclose($fp);
		}
		else
		{
			echo "Fout: kan niet schrijven naar ".$filename;
		}

	}


}
?>