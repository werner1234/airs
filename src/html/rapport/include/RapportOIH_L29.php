<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2011/09/14 09:26:56 $
File Versie					: $Revision: 1.2 $

$Log: RapportOIH_L29.php,v $
Revision 1.2  2011/09/14 09:26:56  rvv
*** empty log message ***

Revision 1.1  2011/01/08 14:27:56  rvv
*** empty log message ***

Revision 1.1  2010/08/28 15:16:34  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportHuidigeSamenstellingLayout.php");

class RapportOIH_L29
{
	function RapportOIH_L29($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum, $valuta = 'EUR')
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIH";
		$this->pdf->rapport_start = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_titel = "Portefeuille (in Euro)\nintrinsieke waarde";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if ($VierDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !$newDec)
	       {
	         $newDec = $i;
	       }
	     }
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
	   return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar;
		// rapport settings
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

		$julData=array($this->pdf->rapport_start=>db2jul($this->rapportageDatumVanaf),$this->pdf->rapport_datum=>db2jul($this->rapportageDatum));
		foreach ($this->pdf->lastPOST['selectedFields'] as $datum)
		{
		  $dd = explode($__appvar["date_seperator"],$datum);

	  	if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
	  	{
	    	echo "<b>Fout: ongeldige datum opgegeven!</b>";
			  exit;
	  	}
		  $julData[$datum]=form2jul($datum);
		}
		sort($julData);
		$julData=array_reverse($julData);
		foreach ($julData as $julDate)
		{

		  $this->dbDates[]=substr(jul2db($julDate),0,10);
		}

		$this->pdf->headerWidth=array(55,25,25);
		$this->pdf->headerText=array('',"Aantal\naandelen","Beurskoers");
		$this->pdf->headerUnderline=array('U',"U","U");
		$this->pdf->headerAligns=array('L',"R","R");
		$cellWidth=125/count($this->dbDates);
		foreach ($this->dbDates as $index=>$datum)
		{
		  $this->pdf->headerWidth[]=$cellWidth;
		  $this->pdf->headerText[]=date("d-m-Y",$julData[$index]);
		  $this->pdf->headerUnderline[]='U';
		  $this->pdf->headerAligns[]='R';
		  if(date('d-m',$julData[$index])=='01-01')
		    $startjaar=true;
		  else
		    $startjaar=false;
	    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille, $datum,$startjaar,'EUR',date("Y-01-01",$julData[$index]));
      //verwijderTijdelijkeTabel($this->portefeuille,$datum);
      vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$datum);
		}
		$this->pdf->headerWidth[]=25;
		$this->pdf->headerText[]='boekwaarde';
		$this->pdf->headerUnderline[]='U';
		$this->pdf->headerAligns[]='R';
		$this->pdf->headerWidth[]=25;
		$this->pdf->headerText[]="verschil waarde en boekwaarde";
		$this->pdf->headerUnderline[]='U';
		$this->pdf->headerAligns[]='R';


	//	listarray($this->dbDates);

		$this->pdf->addPage();

		$query="SELECT
TijdelijkeRapportage.totaalAantal,
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.fonds,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.portefeuille,
TijdelijkeRapportage.rapportageDatum,
Beleggingscategorien.Omschrijving as catOmschrijving,
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro,
SUM(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.historischeValutakoers / TijdelijkeRapportage.historischeRapportageValutakoers) as historischeWaarde,
if(Beleggingscategorien.Afdrukvolgorde IS NULL ,100,Beleggingscategorien.Afdrukvolgorde) as Afdrukvolgorde,
TijdelijkeRapportage.`type`
FROM
TijdelijkeRapportage
LEFT Join Beleggingscategorien ON TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie
WHERE
TijdelijkeRapportage.portefeuille='".$this->portefeuille."'
AND TijdelijkeRapportage.rapportageDatum IN('".implode("','",$this->dbDates)."')
GROUP BY TijdelijkeRapportage.fonds,TijdelijkeRapportage.rapportageDatum
ORDER BY Afdrukvolgorde,TijdelijkeRapportage.beleggingscategorie,TijdelijkeRapportage.fonds,TijdelijkeRapportage.rapportageDatum";

		$db=new DB();
		$db->SQL($query);
		$db->Query();
		while($data=$db->nextRecord())
		{
		  if($data['catOmschrijving'] == '')
		    $data['catOmschrijving']='geen';
		  if($data['fonds'] == '')
		    $data['fonds']='geen';
		  if($data['type'] == 'rekening')
		    $data['historischeWaarde']=$data['actuelePortefeuilleWaardeEuro'];


		  $waarden[$data['catOmschrijving']][$data['fonds']][$data['rapportageDatum']]=$data;
		  $catTotalen[$data['catOmschrijving']][$data['rapportageDatum']]['actuelePortefeuilleWaardeEuro']+=$data['actuelePortefeuilleWaardeEuro'];
		  $catTotalen[$data['catOmschrijving']][$data['rapportageDatum']]['historischeWaarde']+=$data['historischeWaarde'];
		  $totalen[$data['rapportageDatum']]['actuelePortefeuilleWaardeEuro']+=$data['actuelePortefeuilleWaardeEuro'];
		  $totalen[$data['rapportageDatum']]['historischeWaarde']+=$data['historischeWaarde'];
		  $fondsen[$data['fonds']]=$data;
		}



		foreach ($waarden as $categorie=>$fondsData)
		{
      $this->pdf->Row(array($categorie));
		  foreach ($fondsData as $fonds=>$datumWaarden)
		  {
		    $rowData=array();
		    if($datumWaarden[$this->dbDates[0]]['totaalAantal'] == 0)
		      $fondsen[$fonds]['totaalAantal']=0;

		    array_push($rowData,$fondsen[$fonds]['fondsOmschrijving']);
		    array_push($rowData,$this->formatGetal($fondsen[$fonds]['totaalAantal'],0));
		    array_push($rowData,$this->formatGetal($fondsen[$fonds]['actueleFonds'],2));
		    foreach ($this->dbDates as $datum)
		    {
          array_push($rowData,$this->formatGetal($datumWaarden[$datum]['actuelePortefeuilleWaardeEuro'],2));
          if($this->dbDates[count($this->dbDates)-1] == $datum)
          {
            array_push($rowData,$this->formatGetal($datumWaarden[$this->dbDates[0]]['historischeWaarde'],2));
            array_push($rowData,$this->formatGetal($datumWaarden[$this->dbDates[0]]['actuelePortefeuilleWaardeEuro']-$datumWaarden[$this->dbDates[0]]['historischeWaarde'],2));
          }
		    }
		    $this->pdf->Row($rowData);
		  }
		  $this->printTotaal($categorie,$catTotalen[$categorie]);
		  $this->pdf->ln();
		}
		$this->printTotaal('in Euro',$totalen);

	//	listarray($waarden);
	//	echo $query;
	//	exit;


	//	exit;


	}

	function printTotaal($omschrijving,$totalen)
	{
	  $rowData=array();
		array_push($rowData,"Totaal $omschrijving");
		array_push($rowData,"");
		array_push($rowData,"");
		foreach ($this->dbDates as $datum)
		{
      array_push($rowData,$this->formatGetal($totalen[$datum]['actuelePortefeuilleWaardeEuro'],2));
      if($this->dbDates[count($this->dbDates)-1] == $datum)
      {
        array_push($rowData,$this->formatGetal($totalen[$this->dbDates[0]]['historischeWaarde'],2));
        array_push($rowData,$this->formatGetal($totalen[$this->dbDates[0]]['actuelePortefeuilleWaardeEuro']-$totalen[$this->dbDates[0]]['historischeWaarde'],2));
      }
		}
		$this->pdf->CellBorders = $this->pdf->headerUnderline;
		$this->pdf->Row($rowData);
		$this->pdf->CellBorders=array();
	}
}
?>