<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/11 16:21:41 $
File Versie					: $Revision: 1.5 $

$Log: RapportOIH_L40.php,v $
Revision 1.5  2020/03/11 16:21:41  rvv
*** empty log message ***

Revision 1.4  2019/02/13 16:42:08  rvv
*** empty log message ***

Revision 1.3  2019/02/09 18:40:17  rvv
*** empty log message ***

Revision 1.2  2019/02/06 16:07:12  rvv
*** empty log message ***

Revision 1.1  2019/02/03 13:43:54  rvv
*** empty log message ***

Revision 1.26  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.25  2017/01/21 17:48:04  rvv
*** empty log message ***

Revision 1.24  2017/01/18 17:02:28  rvv
*** empty log message ***

Revision 1.23  2015/06/13 13:16:01  rvv
*** empty log message ***

Revision 1.22  2015/04/04 15:15:15  rvv
*** empty log message ***

Revision 1.21  2013/12/21 18:31:54  rvv
*** empty log message ***

Revision 1.20  2013/12/14 17:16:30  rvv
*** empty log message ***

Revision 1.19  2013/12/07 17:51:24  rvv
*** empty log message ***

Revision 1.18  2013/11/02 17:04:05  rvv
*** empty log message ***

Revision 1.17  2013/07/15 17:06:38  rvv
*** empty log message ***

Revision 1.16  2012/12/08 14:48:08  rvv
*** empty log message ***

Revision 1.15  2012/12/05 16:45:29  rvv
*** empty log message ***

Revision 1.14  2012/11/21 16:29:06  rvv
*** empty log message ***

Revision 1.13  2012/11/14 16:48:28  rvv
*** empty log message ***

Revision 1.12  2012/11/07 17:07:29  rvv
*** empty log message ***

Revision 1.11  2012/11/03 18:14:13  rvv
*** empty log message ***

Revision 1.10  2012/11/01 14:40:05  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportHuidigeSamenstellingLayout.php");

class RapportOIH_L40
{
	function RapportOIH_L40($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum, $valuta = 'EUR')
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIH";
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Huidige samenstelling effectenportefeuille";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->verdeling='beleggingssector';
    $this->pdf->tweedeDeel=false;
    $this->pdf->underlinePercentage=0.8;
    $this->modelDataPrinted=array();
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec,$extraTeken='')
	{
	  if(round($waarde,2) <> 0)
	  	return number_format($waarde,$dec,",",".").$extraTeken;
	}

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if(round($waarde,2) == 0)
      return '';
	  elseif ($VierDecimalenZonderNullen)
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

	function printTotaal($title, $type,$fontStyle='')
	{
	  if($type=='hoofdcategorie')
	  {
	    $space='';
      $this->pdf->SetFillColor(0,78,58); 
      $this->pdf->SetTextColor(255,255,255);
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
      $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
      $this->pdf->row(array(""," ",' ','','','','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
      unset($this->pdf->fillCell);
      $extraln=1;
	  }
	  if($type=='beleggingscategorie')
	  {
	    $space='  ';
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
	  }
	  if($type=='verdeling')
	  {
	    $space='    ';
      if($this->pdf->GetY() < 50 || $this->pdf->GetY() > 190)
        $this->pdf->CellBorders=$this->subtotaalCatBorders;
      else  
        $this->pdf->CellBorders=$this->subtotaalVerBorders;
	  }
    if($type=='alles')
	  {
	    $space='';
      $this->pdf->SetFillColor(0,78,58); 
      $this->pdf->SetTextColor(255,255,255);
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
      $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
      $this->pdf->row(array(""," ",' ','','','','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
      unset($this->pdf->fillCell);
      $extraln=1;
      $this->totalen[$type]['beginPortefeuilleWaardeEuro']=0;
      $this->totalen[$type]['eurResultaat']=0;
      $this->totalen[$type]['procentResultaat']=0;
      $title="actuele vermogen";
	  }
    if($title=='Liquiditeiten')
      $this->totalen[$type]['eurResultaat']=0;


      
      $this->pdf->SetFont($this->pdf->rapport_font,$fontStyle,$this->pdf->rapport_fontsize);
  	  $this->pdf->SetX($this->pdf->marge);
		  $this->pdf->Cell(150,4, $space.'Totaal '.$title, 0, "L");
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		  $this->pdf->setX($this->pdf->marge);
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
	  	$this->pdf->row(array("","",'','','',	//$this->formatGetal($this->totalen[$type]['beginPortefeuilleWaardeInValuta'],$this->pdf->rapport_decimaal),
													
                        $this->formatGetal($this->totalen[$type]['portefeuilleWaarde'],$this->pdf->rapport_decimaal),'',
                          '',
													'',//$this->formatGetal($this->totalen[$type]['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_decimaal),
													
                          '',
                          ''));
    
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders); 
    if($extraln==1) 
      $this->pdf->Ln();                  
		$this->totalen[$type]=array();
    $this->totalenRente[$type]=array();
	}

	function printKop($title, $type, $fontStyle="")
	{
	  $fill=0;
	  if($type=='hoofdcategorie')
	  {
	    $space='';
      
      if($this->pdf->GetY() > 185)
        $this->pdf->addPage();
      
      $this->pdf->SetFillColor(0,78,58); 
      $this->pdf->SetTextColor(255,255,255);
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
      $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
      $this->pdf->row(array(""," ",' ','','','','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
      unset($this->pdf->fillCell);
	  }
	  if($type=='beleggingscategorie')
	  {
	    $space='  ';
      if($this->pdf->GetY() > 190)
        $this->pdf->addPage();
      $this->pdf->SetFillColor(200,200,200);
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
      $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
      $this->pdf->row(array(""," ",' ','','','','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
      unset($this->pdf->fillCell);
	  }
	  if($type=='verdeling')
	  {
	   	//   echo $title." ".$this->pdf->GetY()."<br>\n";
	    $space='    ';
      if($this->pdf->GetY() < 50 || $this->pdf->GetY() > 190 )
        $this->pdf->CellBorders=$this->kopVerBorders;
      else
        $this->pdf->CellBorders=$this->subtotaalFondsBorders;
      $this->pdf->row(array("","",'','','','','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
	  }
		$this->pdf->SetFont($this->pdf->rapport_font,$fontStyle,$this->pdf->rapport_fontsize);
		
		$this->pdf->SetX($this->pdf->marge);
    $width=array_sum($this->pdf->widthB);
		$this->pdf->MultiCell($width,4, $space.$title, 0, "L",$fill);
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

	}

	function writeRapport()
	{

	  # LOOP over H-CAT/CAT/(regio of sector)
	  # eerst fonds dan optie tonen.
	  # rapportagedatum +365 dagen is kortlopende
	  # P 229002
		global $__appvar;
    $this->selectData['modelcontrole_level']='fonds';
    
		// rapport settings
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Risicoklasse, Portefeuilles.ModelPortefeuille, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
    
    $this->pdf->selectData['modelcontrole_portefeuille']=$this->portefeuilledata['ModelPortefeuille'];
    $this->selectData['modelcontrole_portefeuille']=$this->portefeuilledata['ModelPortefeuille'];
    $einddatum=$this->rapportageDatum;
    
    $DB3 = new DB();
    $query="SELECT Fixed, Beleggingscategorie FROM ModelPortefeuilles WHERE Portefeuille='".$this->selectData['modelcontrole_portefeuille']."'";
    $DB3->SQL($query);
    $DB3->Query();
    $modelType = $DB3->nextRecord();
    if($modelType['Fixed']==1)
      $portefeuilleData = berekenFixedModelPortefeuille($this->selectData['modelcontrole_portefeuille'],$einddatum);
    elseif($modelType['Fixed']==3)
      $portefeuilleData = berekenMeervoudigeModelPortefeuille($this->portefeuille,$einddatum,$this->selectData['modelcontrole_portefeuille']);
    else
      $portefeuilleData = berekenPortefeuilleWaarde($this->selectData['modelcontrole_portefeuille'], $einddatum);
    
    //listarray($portefeuilleData);exit;
    vulTijdelijkeTabel($portefeuilleData,"m".$this->selectData['modelcontrole_portefeuille'],$einddatum);
    
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$einddatum."' AND ".
      " portefeuille = '"."m".$this->portefeuilledata ['ModelPortefeuille']."' AND type <> 'rente' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    
    $DB3->SQL($query);
    $DB3->Query();
    $modelwaarde = $DB3->nextRecord();
    $modelTotaal = $modelwaarde['totaal'];
    
    if($this->selectData['modelcontrole_portefeuille']=='' || $modelTotaal==0)
    {
      logScherm("Modelvergelijking voor portefeuille ".$this->portefeuille." gestopt, geen waarden voor modelportefeuille '".$this->selectData['modelcontrole_portefeuille']."' gevonden.",true);
      logScherm("",true);
      return 1;
    }
    
    
    
    $query = "SELECT norm FROM NormPerRisicoprofiel WHERE Risicoklasse='".$this->portefeuilledata['Risicoklasse']."'
 	                                                     AND Vermogensbeheerder='".$this->portefeuilledata['Vermogensbeheerder']."'
 	                                                     AND Beleggingscategorie='".$modelType['Beleggingscategorie']."'";
    $DB3->SQL($query);
    $DB3->Query();
    $norm = $DB3->nextRecord();
    
    if($this->selectData["modelcontrole_rapport"] == "vastbedrag")
    {
      $portefeuille = array();
    }
    // set pdf vars
    $this->pdf->naamOmschrijving = $portefeuille['Naam'];
    $this->pdf->clientOmschrijving = $portefeuille['Client']." / ".$portefeuille['Portefeuille']." / ".$portefeuille['Depotbank'];
    

    
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum = '".$einddatum."' AND ".
      " portefeuille = '".$this->portefeuille."' AND type <> 'rente' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB3 = new DB();
    $DB3->SQL($query);
    $DB3->Query();
    $portefwaarde = $DB3->nextRecord();
    
    if($norm['norm'] <> '')
      $portefwaarde['totaal']=$portefwaarde['totaal']*($norm['norm']/100);
    
    $portefTotaal = $portefwaarde['totaal'];
    
    if($this->selectData["modelcontrole_rapport"] == "vastbedrag")
    {
      $portefTotaal = $this->selectData["modelcontrole_vastbedrag"];
    }
    
    $afwijking='';
    if($this->selectData['modelcontrole_percentage'] > 0)
    {
      $afwijking = " HAVING ABS(afwijking) > ".$this->selectData['modelcontrole_percentage']." ";
    }
    
    if($this->selectData['modelcontrole_uitvoer'] == "afwijkingen")
    {
      $afwijking = " HAVING afwijking <> 0 ";
    }
    

    $this->pdf->widthB = array(10,65,20,20,30,30,20,20,30,17.5,17.5);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R');
    
    $this->subtotaalCatBorders=array(array('T','L','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U','R'));
    $this->subtotaalVerBorders=array(array('L','U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U','R'));
    $this->kopVerBorders=array(array('L','T'),array('T'),array('T'),array('T'),array('T'),array('T'),array('T'),array('T'),array('T'),array('T'),array('T','R'));
    $this->subtotaalFondsBorders=array(array('L'),'','','','','','','','','',array('R'));


		$this->pdf->AddPage();




		  $beginQuery = $this->pdf->ValutaKoersBegin;

		$DB2 = new DB();

		$verdeling=$this->verdeling;
  	$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
    
    if($verdeling=='Fonds')
     $verdelingVolgorde="TijdelijkeRapportage.".$verdeling."Omschrijving"; 
    else
     $verdelingVolgorde="TijdelijkeRapportage.".$verdeling."Volgorde";
    
    $subquery = "SELECT
			SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,model.actuelePortefeuilleWaardeEuro,0))  AS modelWaarde,
		  SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,model.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 AS percentageModel,
  		SUM(IF(TijdelijkeRapportage.portefeuille ='".$this->portefeuille."' ,portef.actuelePortefeuilleWaardeEuro,0)) AS portefeuilleWaarde,
	  	SUM(IF(TijdelijkeRapportage.portefeuille ='".$this->portefeuille."' ,portef.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100 AS percentagePortefeuille,
			(
			  SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,model.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 -
		  	SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,portef.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100
			) AS afwijking,
      TijdelijkeRapportage.".$this->selectData['modelcontrole_level']." as RegelOmschrijving,
			TijdelijkeRapportage.fonds,
			TijdelijkeRapportage.fondsOmschrijving,
			TijdelijkeRapportage.actueleFonds,
			TijdelijkeRapportage.actueleValuta,
			TijdelijkeRapportage.fondsEenheid,
			Fondsen.ISINCode,
			Fondsen.fondssoort,
			TijdelijkeRapportage.valuta,
			TijdelijkeRapportage.type,
			TijdelijkeRapportage.beleggingscategorie,
			
			TijdelijkeRapportage.beleggingscategorie,
			 TijdelijkeRapportage.valuta,
			 TijdelijkeRapportage.fondsOmschrijving,
       TijdelijkeRapportage.Fonds,
			 TijdelijkeRapportage.actueleValuta,
			 portef.totaalAantal,
			 TijdelijkeRapportage.beginwaardeLopendeJaar,
			 TijdelijkeRapportage.beginPortefeuilleWaardeInValuta,
			 TijdelijkeRapportage.beginPortefeuilleWaardeEuro / $beginQuery as beginPortefeuilleWaardeEuro,
			 TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,
			 TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			 round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd,
			 TijdelijkeRapportage.hoofdsector,
       TijdelijkeRapportage.hoofdcategorieOmschrijving,
       TijdelijkeRapportage.beleggingscategorie,
       TijdelijkeRapportage.beleggingscategorieOmschrijving,
       TijdelijkeRapportage.beleggingscategorieVolgorde,
       TijdelijkeRapportage.fondspaar,
       TijdelijkeRapportage.type,
       TijdelijkeRapportage.".$verdeling." as verdeling,
       TijdelijkeRapportage.".$verdeling."Omschrijving as verdelingOmschrijving,
       Fondsen.OptieBovenliggendFonds,
       if(Fondsen.OptieBovenliggendFonds='',TijdelijkeRapportage.Fonds ,Fondsen.OptieBovenliggendFonds) as onderliggendFonds
       ,TijdelijkeRapportage.Lossingsdatum,
       TijdelijkeRapportage.rekening

			FROM TijdelijkeRapportage
			 JOIN Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
			LEFT JOIN TijdelijkeRapportage AS model ON model.fonds = TijdelijkeRapportage.fonds AND model.portefeuille = \""."m".$this->selectData['modelcontrole_portefeuille']."\"
           AND model.type = 'fondsen'  AND model.rapportageDatum = '".$einddatum."'"
      .str_replace("TijdelijkeRapportage",'model',$__appvar['TijdelijkeRapportageMaakUniek'])."
			LEFT JOIN TijdelijkeRapportage AS portef ON portef.fonds = TijdelijkeRapportage.fonds AND portef.portefeuille = \"".$this->portefeuille."\"
           AND portef.type = 'fondsen'  AND portef.rapportageDatum = '".$einddatum."'"
      .str_replace("TijdelijkeRapportage",'portef',$__appvar['TijdelijkeRapportageMaakUniek'])."
			WHERE
			TijdelijkeRapportage.type = 'fondsen' AND
			TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND
			(TijdelijkeRapportage.portefeuille = \"".$this->portefeuille."\" OR TijdelijkeRapportage.portefeuille = \""."m".$this->selectData['modelcontrole_portefeuille']."\")  "
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " GROUP BY TijdelijkeRapportage.".$this->selectData['modelcontrole_level']." ".$afwijking."
			ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde,TijdelijkeRapportage.beleggingscategorieVolgorde, ".$verdelingVolgorde.",
         TijdelijkeRapportage.Lossingsdatum,
          onderliggendFonds,TijdelijkeRapportage.fondspaar,
           TijdelijkeRapportage.Lossingsdatum, Fondsen.OptieBovenliggendFonds,
         TijdelijkeRapportage.type,TijdelijkeRapportage.fondsOmschrijving asc";
    debugSpecial($subquery,__FILE__,__LINE__);
//echo $subquery;exit;
    

			$DB2->SQL($subquery);
			$DB2->Query();
			$somVelden=array('portefeuilleWaarde');
			$vedelingen=array('hoofdcategorie'=>'bi','beleggingscategorie'=>'b','verdeling'=>'','alles'=>'');
			$omschrijvingVelden=array('hoofdcategorieOmschrijving'=>'hoofdcategorie','beleggingscategorieOmschrijving'=>'beleggingscategorie','verdelingOmschrijving'=>$verdeling);
			$regels=array();
      //$regels[]=array('hoofdcategorieOmschrijving'=>'hoofdcategorieOmschrijving','hoofdsector'=>'hoofdsector','verdeling'=>'verdeling','beleggingscategorie'=>'beleggingscategorie','fondsOmschrijving'=>'fondsOmschrijving','verdelingOmschrijving'=>'verdelingOmschrijving');
      while($subdata = $DB2->NextRecord())
			{
			   $regels[]=$subdata;
      }
    
    
    $subquery = "SELECT
			SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))  AS modelWaarde,
		  SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 AS percentageModel,
   		SUM(IF(TijdelijkeRapportage.portefeuille ='".$this->portefeuille."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) AS portefeuilleWaarde,
		  SUM(IF(TijdelijkeRapportage.portefeuille ='".$this->portefeuille."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100 AS percentagePortefeuille,
			(
			 SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 -
			 SUM(IF(TijdelijkeRapportage.portefeuille ='".$this->portefeuille."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100
			) AS afwijking,
			TijdelijkeRapportage.fonds,
			TijdelijkeRapportage.fondsOmschrijving,
			TijdelijkeRapportage.actueleFonds,
			TijdelijkeRapportage.actueleValuta,
			TijdelijkeRapportage.fondsEenheid,
			TijdelijkeRapportage.valuta,
			 TijdelijkeRapportage.hoofdsector,
       TijdelijkeRapportage.hoofdcategorieOmschrijving,
       TijdelijkeRapportage.beleggingscategorie,
       TijdelijkeRapportage.beleggingscategorieOmschrijving,
       TijdelijkeRapportage.beleggingscategorieVolgorde,
       TijdelijkeRapportage.fondspaar,
       TijdelijkeRapportage.type,
       TijdelijkeRapportage.".$verdeling." as verdeling,
       TijdelijkeRapportage.".$verdeling."Omschrijving as verdelingOmschrijving
			FROM TijdelijkeRapportage
			WHERE
			TijdelijkeRapportage.type = 'rekening'  AND
			TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND
			(TijdelijkeRapportage.portefeuille = \"".$this->portefeuille."\" OR TijdelijkeRapportage.portefeuille = \""."m".$this->selectData['modelcontrole_portefeuille']."\")  "
      .$__appvar['TijdelijkeRapportageMaakUniek']."
			GROUP BY TijdelijkeRapportage.type".$afwijking."
			ORDER BY afwijking DESC ";
    debugSpecial($query,__FILE__,__LINE__);
    $DB2->SQL($subquery);
    $DB2->Query();
    while($subdata = $DB2->NextRecord())
    {
      $regels[]=$subdata;
    }
      //listarray($regels);
      //exit;
      foreach($regels as $subdata)
      {
  
        $aankoopStuks=0;
        $verkoopStuks=0;
  
        $aankoopWaarde 	= ((($portefTotaal) / 100) * $subdata['percentageModel']) - $subdata['portefeuilleWaarde'];
        $aankoopStuks 	= round(($aankoopWaarde / ($subdata['actueleFonds'] * $subdata['actueleValuta']))  / $subdata['fondsEenheid'],4);
        if($subdata['fondsEenheid'] == '0.01')
        {
          if($aankoopStuks > 0)
            $aankoopStuks=floor($aankoopStuks/100)*100;
          else
            $aankoopStuks=ceil($aankoopStuks/100)*100;
        }
  
  
        $waardeVolgensModel = (($portefTotaal) / 100) * $subdata['percentageModel'];
  
        if($this->selectData['modelcontrole_level'] != 'fonds')
        {
          $subdata['fondsOmschrijving']=$omschrijving[$subdata['RegelOmschrijving']];
          $subdata['actueleFonds']=0;
          $aankoopStuks=0;
          $verkoopStuks=0;
        }
  
        $aankoopStuks=round($aankoopStuks,0);
        $verkoopNaarNul=false;
        if($aankoopStuks < 0)
        {
          if(round($subdata['percentageModel'],1)==0)
          {
            $verkoopStuks = $subdata['totaalAantal'];
            $verkoopNaarNul=true;
          }
          else
            $verkoopStuks = $aankoopStuks * -1;
          $aankoopStuks = 0;
        }
  
  
        $geschatOrderbedrag 	= (($verkoopStuks-$aankoopStuks) * ($subdata['actueleFonds'] * $subdata['actueleValuta'])) * $subdata['fondsEenheid'];
        
        
        //////
        if($subdata['type']=='rekening' && $_POST['anoniem']!=1)
          $subdata['fondsOmschrijving'].=" ".ereg_replace("[^0-9]","",$subdata['rekening']);
       
        if($subdata['beleggingscategorie']=='AAND')
        {
          foreach ($omschrijvingVelden as $veldNaam=>$omschrijving)
	 	        if($subdata[$veldNaam]=='' )
              $subdata[$veldNaam] ="Geen $omschrijving";
        }
          
			  foreach (array_reverse($vedelingen,true) as $type=>$weergave)
			  {
			    if($lastVerdeling[$type] <> $subdata[$type.'Omschrijving'] && isset($this->totalen[$type]))
          {
            if($this->pdf->modelLayout==true && $addModel==true && $type==$modelCategorieType)
            {
              $addModel=false;
              $this->printTotaal('missingModel','verdeling');
            }
			      $this->printTotaal($lastVerdeling[$type],$type,$weergave);
          }
			  }

			  foreach ($vedelingen as $type=>$weergave)
  			  if($subdata[$type.'Omschrijving'] <> '' && $lastVerdeling[$type] <> $subdata[$type.'Omschrijving'])
          {
            if($this->pdf->modelLayout==true && $this->modelCategorie == $subdata[$type])
            {
              $addModel=true;
              $modelCategorieType=$type;
            }
           
	  		  	$this->printKop(vertaalTekst($subdata[$type.'Omschrijving'],$this->pdf->rapport_taal),$type, $weergave);
          }
   
        if($subdata['type']=='rente')
        {
          $subdata['fondsOmschrijving']='lopende rente';
        }
          
       // echo $this->pdf->GetY()." ".$subdata['fondsOmschrijving']." <br>\n";
        if($this->pdf->GetY() > 194)  
          $this->printKop(vertaalTekst($subdata['beleggingscategorieOmschrijving'],$this->pdf->rapport_taal),'beleggingscategorie', 'b');

        if($this->pdf->GetY() > 190)
          $this->pdf->CellBorders=$this->subtotaalVerBorders;
        else
          $this->pdf->CellBorders=$this->subtotaalFondsBorders; 
          
				$this->pdf->setX($this->pdf->marge);
        
  			$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving']);//." |".$subdata['fondspaar'] 
				$this->pdf->setX($this->pdf->marge);
        $eurResultaat=$subdata['portefeuilleWaarde'] - $subdata['beginPortefeuilleWaardeEuro'];
       	$procentResultaat = ($eurResultaat / (abs($subdata['beginPortefeuilleWaardeEuro']) /100));
         
          
        if($subdata['type']=='rekening')
          $eurResultaat='';
  
  
     
        if($subdata['fondssoort']=='STOCKDIV')
        {
          $verkoopStuks='';
 
        }
        
        if($this->pdf->modelLayout==true)
            $this->pdf->row(array("",'',$this->formatGetal($subdata['portefeuilleWaarde'],$this->pdf->rapport_decimaal),"","","","","","","",""));
        else
        {          
          if($subdata['type']=='rente')
          {
          	$this->pdf->row(array("","","","","","","","",
													$this->formatGetal($subdata['portefeuilleWaarde'],$this->pdf->rapport_decimaal),'',''));
          }
          else
			  	  $this->pdf->row(array("",
													"",
                          $subdata['valuta'],
                          $this->formatAantal($subdata['totaalAantal'],0,true),
													$this->formatGetal($subdata['actueleFonds'],2),
													$this->formatGetal($subdata['portefeuilleWaarde'],$this->pdf->rapport_decimaal),
                              $this->formatGetal($subdata['percentagePortefeuille'],2),
                              $this->formatGetal($subdata['percentageModel'],2),

                              $this->formatGetal($aankoopWaarde,2),
                              $this->formatAantal($aankoopStuks,0,true),
                              $this->formatAantal($verkoopStuks,0,true)));
        }                  
        unset($this->pdf->CellBorders);


      foreach ($vedelingen as $type=>$weergave)
      {
        $lastVerdeling[$type]=$subdata[$type.'Omschrijving'];
        foreach ($somVelden as $veld)
        {
          $this->totalen[$type][$veld]+=$subdata[$veld];
          if($subdata['type']=='rente')
            $this->totalenRente[$type][$veld]+=$subdata[$veld];
        }
//
        $this->totalen[$type]['eurResultaat']=($this->totalen[$type]['portefeuilleWaarde'] - $this->totalen[$type]['beginPortefeuilleWaardeEuro']) - $this->totalenRente[$type]['portefeuilleWaarde']  ;
    	  $this->totalen[$type]['procentResultaat'] = ($this->totalen[$type]['eurResultaat'] / (abs($this->totalen[$type]['beginPortefeuilleWaardeEuro']) /100));
      }
      
  //    listarray($subdata['fondsOmschrijving']);
//listarray($this->totalenRente);   

		}

	  foreach (array_reverse($vedelingen,true) as $type=>$weergave)
		{
		  if(isset($this->totalen[$type]) && $type <> 'alles' && $lastVerdeling[$type]<> '')
		     $this->printTotaal($lastVerdeling[$type],$type,$weergave);
		}

   	// print grandtotaal
		$this->pdf->ln();
		$this->printTotaal('','alles','B');
    $this->pdf->SetWidths(array(200));
    //$this->pdf->row(array("* Resultaat is exclusief lopende rente"));
    $this->pdf->MultiCell(200,$this->pdf->rowHeight,"* Resultaat is exclusief lopende rente",0,'L');


$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}
}
?>
