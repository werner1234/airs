<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/27 16:13:36 $
File Versie					: $Revision: 1.13 $

$Log: RapportMODEL_L42.php,v $
Revision 1.13  2020/05/27 16:13:36  rvv
*** empty log message ***

Revision 1.12  2020/05/02 15:57:50  rvv
*** empty log message ***

Revision 1.11  2020/03/11 16:21:41  rvv
*** empty log message ***

Revision 1.10  2018/04/21 17:56:04  rvv
*** empty log message ***

Revision 1.9  2015/11/22 14:31:46  rvv
*** empty log message ***

Revision 1.8  2015/10/07 19:38:52  rvv
*** empty log message ***

Revision 1.7  2015/05/02 14:57:32  rvv
*** empty log message ***

Revision 1.6  2015/04/04 15:15:15  rvv
*** empty log message ***

Revision 1.5  2014/10/25 14:39:09  rvv
*** empty log message ***

Revision 1.4  2014/08/23 15:45:01  rvv
*** empty log message ***

Revision 1.3  2014/05/25 14:38:33  rvv
*** empty log message ***

Revision 1.2  2013/11/27 16:29:11  rvv
*** empty log message ***

Revision 1.1  2013/11/23 17:23:25  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportMODEL_L42
{
	function RapportMODEL_L42($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
		$this->pdf = &$pdf;
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

   	$this->pdf->rapport_titel = "";//Modelcontrole ".date("j",$this->pdf->rapport_datum)." ".vertaalTekst($__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum);
		if(!is_array($this->pdf->excelData))
		  $this->pdf->excelData 	= array();

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->pdf->underlinePercentage=0.8;
    
    $this->selectData =
		array('percentage' => 0.0,
    'modelcontrole_percentage' => 0.0,
    'modelcontrole_rapport' => 'percentage',
    'modelcontrole_uitvoer' => 'alles',
    'modelcontrole_filter' => 'gekoppeld'
		);
		$this->portefeuille = $portefeuille;
		$this->pdf->excelData 	= array();

		$this->pdf->rapport_type = "MODEL";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;
		$this->pdf->fondsRapport = true;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);

		$this->selectData['datumTm'] = db2jul($rapportageDatum);

		$this->pdf->tmdatum = $this->selectData['datumTm'];
		$this->pdf->rapport_datum = $this->selectData['datumTm'];
		// selectdata ook aan PDF geven
		$this->pdf->selectData = $this->selectData;
		$this->orderData=array();
    
	}

	function formatGetal($waarde, $dec,$procent=false)
	{
    if($procent==true)
      return number_format($waarde,$dec,",",".").'%';
	  if($waarde==0)
      return '';
		return number_format($waarde,$dec,",",".");
	}

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	 	if($waarde==0)
      return '';
      
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


	function printKop($title, $type="default")
	{
	  //if($title == 'Opgelopen rente')
    //  return 0;

		switch($type)
		{
			case "b" :
        $spaties='';
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'b';
			break;
			case "bu" :
        $spaties='';
        $extraX=1.76388;
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bu';
			break;
			case "bi" :
        $spaties='';
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bi';
			break;
			case "u" :
        $spaties='';
        $extraX=2;
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'u';
			break;
			default :
        $spaties='';
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = '';
        $extraX=0;
			break;
		}

		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->pdf->SetX($this->pdf->marge+$extraX);
		$this->pdf->MultiCell(90,4, $spaties.$title, 0, "L");
    $this->pdf->excelData[]=array($title);
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
	}

	function writeRapport()
	{
		global $__appvar;

		$DB = new DB();
 $this->pdf->excelData[]=array('Fonds','Bewaarder','Model Percentage','Werkelijk Percentage','Grootste afwijking','Kopen','Verkopen','Overschrijving waarde EUR','Waarde volgens percentage model','Koers in locale valuta');
		if($this->pdf->ModelSettings['modelcontrole_level'])
		 $this->selectData['modelcontrole_level']=$this->pdf->ModelSettings['modelcontrole_level'];
		else
		  $this->selectData['modelcontrole_level']='fonds';

		if($this->selectData['modelcontrole_level'] != 'fonds')
		{
		  if($this->selectData['modelcontrole_level'] == 'beleggingscategorie')
  	    $query = "SELECT Beleggingscategorie as id,Omschrijving as value FROM Beleggingscategorien";
		  elseif($this->selectData['modelcontrole_level'] == 'beleggingssector')
		    $query = "SELECT Beleggingssector as id,Omschrijving as value FROM Beleggingssectoren";
		  else
		    $query = "SELECT Regio as id,Omschrijving as value FROM Regios";
	    $DB->SQL($query);
      $DB->Query();
	    while($data=$DB->nextRecord())
	      $omschrijving[$data['id']]=$data['value'];
		}
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();
		$query = "SELECT Vermogensbeheerders.VerouderdeKoersDagen , Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM (Portefeuilles, Clienten)  Join Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		$maxDagenOud=$portefeuilledata['VerouderdeKoersDagen'];
    
    
 		$einddatum = jul2sql($this->selectData['datumTm']);

		$jaar = date("Y",$this->selectData['datumTm']);
    
    $query = " SELECT ".
						 " Portefeuilles.Portefeuille, ".
						 " Portefeuilles.Startdatum, ".
						 " Portefeuilles.Client, ".
						 " Portefeuilles.Depotbank, ".
             " Portefeuilles.Risicoklasse, ".
             " Portefeuilles.Vermogensbeheerder, ".
						 " Portefeuilles.ModelPortefeuille, ".
						 " Clienten.Naam  ".
					 " FROM (Portefeuilles, Clienten)  WHERE ".
					 " Portefeuilles.Client = Clienten.Client AND Portefeuille='".$this->portefeuille."'";
		$DB->SQL($query);
		$DB->Query();
		$portefeuille = $DB->nextRecord();
  
//		$this->pdf->AddPage();
    $paginaBeginY=$this->pdf->GetY();
    $this->pdf->rapport_titel = "Modelcontrole ".date("j",$this->pdf->rapport_datum)." ".vertaalTekst($__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum)." - ".$portefeuille['ModelPortefeuille'];

    
	// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) /".$this->pdf->ValutaKoersEind."  AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query); 
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

  	  $this->pdf->selectData['modelcontrole_portefeuille']=$portefeuille['ModelPortefeuille'];
	  	$this->selectData['modelcontrole_portefeuille']=$portefeuille['ModelPortefeuille'];

      $DB3 = new DB();
		  $query="SELECT Fixed, Beleggingscategorie FROM ModelPortefeuilles WHERE Portefeuille='".$this->selectData['modelcontrole_portefeuille']."'";
      $DB3->SQL($query);
	    $DB3->Query();
	    $modelType = $DB3->nextRecord();
      if($modelType['Fixed']==1)
        $portefeuilleData = berekenFixedModelPortefeuille($this->selectData['modelcontrole_portefeuille'],$einddatum);
      elseif($modelType['Fixed']==3)
        $portefeuilleData = berekenMeervoudigeModelPortefeuille($portefeuille['Portefeuille'],$einddatum,$this->selectData['modelcontrole_portefeuille']);
      else
		    $portefeuilleData = berekenPortefeuilleWaarde($this->selectData['modelcontrole_portefeuille'], $einddatum);
  
		  vulTijdelijkeTabel($portefeuilleData,"m".$this->selectData['modelcontrole_portefeuille'],$einddatum);

	    if($modelType['Beleggingscategorie'] <> '')
 	    {
 	      $extraCategorieFilter=" AND TijdelijkeRapportage.Beleggingscategorie='".$modelType['Beleggingscategorie']."' ";
 	    }
		  	  // bereken totaal waarde model
  		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '"."m".$portefeuille['ModelPortefeuille']."' AND type <> 'rente' $extraCategorieFilter "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
		  debugSpecial($query,__FILE__,__LINE__);
		
		  $DB3->SQL($query);
	  	$DB3->Query();
		  $modelwaarde = $DB3->nextRecord();
	  	$modelTotaal = $modelwaarde['totaal'];
      if($modelTotaal==0)
        $modelTotaal=0.001;
      

      $query = "SELECT norm FROM NormPerRisicoprofiel WHERE Risicoklasse='".$portefeuille['Risicoklasse']."'
 	                                                     AND Vermogensbeheerder='".$portefeuille['Vermogensbeheerder']."'
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

			$this->pdf->AddPage();
    $this->pdf->templateVars['MODELPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['MODELPaginas']=$this->pdf->rapport_titel;
			$this->pdf->SetFont("Times","",10);
   
   
			$uitsluitingen=bepaalModelUitsluitingen($this->portefeuille,$einddatum);
      foreach($uitsluitingen['portefeuilleRegels'] as $row)
        $this->pdf->Row($row);

			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
								 " rapportageDatum = '".$einddatum."' AND ".
								 " portefeuille = '".$portefeuille['Portefeuille']."' AND type <> 'rente' "
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

			if($this->selectData['modelcontrole_percentage'] > 0)
			{
				$afwijking = " HAVING ABS(afwijking) > ".$this->selectData['modelcontrole_percentage']." ";
			}

			if($this->selectData['modelcontrole_uitvoer'] == "afwijkingen")
			{
				$afwijking = " HAVING afwijking <> 0 ";
			}

			$query = "SELECT
			SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,model.actuelePortefeuilleWaardeEuro,0))  AS modelWaarde,
		  SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,model.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 AS percentageModel,
  		SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,portef.actuelePortefeuilleWaardeEuro,0)) AS portefeuilleWaarde,
	  	SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,portef.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100 AS percentagePortefeuille,
			(
			  SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,model.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 -
		  	SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,portef.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100
			) AS afwijking,
      TijdelijkeRapportage.".$this->selectData['modelcontrole_level']." as RegelOmschrijving,
			TijdelijkeRapportage.fonds,
      TijdelijkeRapportage.bewaarder,
			TijdelijkeRapportage.fondsOmschrijving,
			TijdelijkeRapportage.actueleFonds,
			TijdelijkeRapportage.actueleValuta,
			TijdelijkeRapportage.fondsEenheid,
      
          if(TijdelijkeRapportage.type='fondsen',1,if(TijdelijkeRapportage.type='rente',2,3)) as hoofdVolgorde,
TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, 
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta, 
TijdelijkeRapportage.historischeWaarde,
TijdelijkeRapportage.beginwaardeLopendeJaar,
TijdelijkeRapportage.totaalAantal,
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.fonds,
TijdelijkeRapportage.actueleValuta,
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.rekening,
TijdelijkeRapportage.beleggingssector,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.hoofdcategorie,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
TijdelijkeRapportage.beleggingssectorOmschrijving,
TijdelijkeRapportage.hoofdcategorieOmschrijving,
TijdelijkeRapportage.beginPortefeuilleWaardeEuro,
TijdelijkeRapportage.beginPortefeuilleWaardeInValuta,
TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
TijdelijkeRapportage.type,
round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd,
if(Fondsen.bucketCode <> '', Fondsen.bucketCode, TijdelijkeRapportage.Fonds) as fakeFonds
			FROM TijdelijkeRapportage
			LEFT JOIN TijdelijkeRapportage AS model ON model.fonds = TijdelijkeRapportage.fonds AND model.portefeuille = \""."m".$this->selectData['modelcontrole_portefeuille']."\" 
           AND model.type IN('fondsen')  AND model.rapportageDatum = '".$einddatum."'"
          .str_replace("TijdelijkeRapportage",'model',$__appvar['TijdelijkeRapportageMaakUniek'])."
			LEFT JOIN TijdelijkeRapportage AS portef ON portef.fonds = TijdelijkeRapportage.fonds AND portef.portefeuille = \"".$portefeuille['Portefeuille']."\" 
           AND portef.type IN('fondsen')  AND portef.rapportageDatum = '".$einddatum."'"
          .str_replace("TijdelijkeRapportage",'portef',$__appvar['TijdelijkeRapportageMaakUniek'])."
      LEFT JOIN Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
			WHERE
			TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND
			(TijdelijkeRapportage.portefeuille = \"".$portefeuille['Portefeuille']."\" OR TijdelijkeRapportage.portefeuille = \""."m".$this->selectData['modelcontrole_portefeuille']."\")  "
			.$__appvar['TijdelijkeRapportageMaakUniek']. 
			" AND TijdelijkeRapportage.type IN('fondsen') GROUP BY fakeFonds ".$afwijking."
			ORDER BY 
hoofdVolgorde,
TijdelijkeRapportage.hoofdsectorVolgorde,
TijdelijkeRapportage.beleggingscategorieVolgorde,
TijdelijkeRapportage.beleggingssectorVolgorde,
TijdelijkeRapportage.fondsOmschrijving";
			debugSpecial($query,__FILE__,__LINE__);
      
  

		$DB->SQL($query); 
		$DB->Query();
    $somVelden=array('actuelePortefeuilleWaardeEuro','beginPortefeuilleWaardeEuro','fondsOngerealiseerd','fondsValutaresultaat','aandeel','dividend','dividendCorrected');
		while($data = $DB->nextRecord())
    {
      
      $aankoopStuks=0;
			  $verkoopStuks=0;

				$aankoopWaarde 	= ((($portefTotaal) / 100) * $data['percentageModel']) - $data['portefeuilleWaarde'];
				$aankoopStuks 	= round(($aankoopWaarde / ($data['actueleFonds'] * $data['actueleValuta']))  / $data['fondsEenheid'],4);
				if($fdata['fondsEenheid'] == '0.01')
		    {
          if($aankoopStuks > 0)
		        $aankoopStuks=floor($aankoopStuks/100)*100;
          else
            $aankoopStuks=ceil($aankoopStuks/100)*100;  
		      $aankoopWaarde 	= ($aankoopStuks * ($fdata['actueleFonds'] * $fdata['actueleValuta'])) * $fdata['fondsEenheid'];
		    }

				if($aankoopStuks < 0)
				{
				  $verkoopStuks = $aankoopStuks * -1;
				  $aankoopStuks = 0;
				}

				if($aankoopStuks > 0)
			    $aankoopStuks=round($aankoopStuks);

	 			if($verkoopStuks > 0)
		    {
		      if(intval($verkoopStuks) == $verkoopStuks )
		        $verkoopStuks = round($verkoopStuks);
	    	}

				$waardeVolgensModel = (($portefTotaal) / 100) * $data['percentageModel'];

				if($this->selectData['modelcontrole_level'] != 'fonds')
				{
				  $data['fondsOmschrijving']=$omschrijving[$data['RegelOmschrijving']];
				  $data['actueleFonds']=0;
				  $aankoopStuks=0;
				  $verkoopStuks=0;
				}
        $data['aankoopStuks']=$aankoopStuks;
        $data['verkoopStuks']=$verkoopStuks;
        $data['aankoopWaarde']=$aankoopWaarde;
        $data['waardeVolgensModel']=$waardeVolgensModel;
        
      $tmp=$this->getDividend($data['fonds']);
      $data['dividend']=$tmp['totaal'];
      $data['dividendCorrected']=$tmp['corrected'];
      if($data['type']=='rekening')
        $data['fondsOmschrijving']=$data['rekening'];
      if($data['beleggingscategorieOmschrijving'] <> '')  
        $categorieOmschrijvingen[$data['beleggingscategorie']]=$data['beleggingscategorieOmschrijving'];
      if($data['beleggingssectorOmschrijving'] <> '')
        $sectorOmschrijvingen[$data['beleggingssector']]=$data['beleggingssectorOmschrijving'];
      $hoofdcategorieOmschrijvingen[$data['hoofdcategorie']]=$data['hoofdcategorieOmschrijving'];
      
     
      if($data['hoofdVolgorde'] == 1)
      {
        $data['fondsOngerealiseerd'] = ($data['actuelePortefeuilleWaardeInValuta'] - $data['beginPortefeuilleWaardeInValuta']) * $data['actueleValuta'];
				$data['fondsValutaresultaat'] = $data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro'] - $data['fondsOngerealiseerd'] ;
        $procentResultaat = (($data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro']) / ($data['beginPortefeuilleWaardeEuro'] /100));
      }
      if($data['hoofdVolgorde'] > 1)
      {
        $data['historischeWaarde']=0;
        $data['beginPortefeuilleWaardeInValuta']=0;
        $data['totaalAantal']=0;
        $data['actueleFonds']=0;
        $data['beginPortefeuilleWaardeEuro']=0;
        if($data['hoofdVolgorde'] == 2)
        {
          $data['beleggingscategorie']='Opgelopen rente';   
          $data['beleggingssector']='Opgelopen rente';
        }
      }
      $aandeel=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde;
      $data['aandeel']=$aandeel*100;
  
      if($data['hoofdcategorie']=='')
        $data['hoofdcategorie']='geen H-cat';
      if($data['beleggingscategorie']=='')
        $data['beleggingscategorie']='geen cat';   
      if($data['beleggingssector']=='')
      {
        if($data['beleggingscategorie']=='Liquiditeiten')
          $data['beleggingssector']='Liquiditeiten'; 
        else
          $data['beleggingssector']='geen sec'; 
      }
      if($data['koersLeeftijd'] > $maxDagenOud && $data['actueleFonds'] <> 0)
			  $data['markering']="*";
		  else
			  $data['markering']="";        
          
      $waarden[$data['type']][$data['hoofdcategorie']][$data['beleggingscategorie']][$data['beleggingssector']][]=$data;
      
      
      foreach($somVelden as $veld)
      {
        $sectorTotalen[$data['hoofdcategorie']][$data['beleggingscategorie']][$data['beleggingssector']][$veld]+=$data[$veld];
        $categorieTotalen[$data['hoofdcategorie']][$data['beleggingscategorie']][$veld]+=$data[$veld];
        $hcatTotalen[$data['hoofdcategorie']][$veld]+=$data[$veld];
        $typeTotalen[$data['type']][$veld]+=$data[$veld];
        $totaal[$veld]+=$data[$veld];
      }
 
    }
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

  $sectorOmschrijvingen['Opgelopen rente']='Opgelopen rente';
  $sectorOmschrijvingen['Liquiditeiten']='Liquiditeiten';
  $regelsPerType=array();
  foreach($waarden as $type=>$hcatData)
  {
 //begin tellen
    $regelsPerType[$type]=0;
    foreach($hcatData as $hcat=>$catData)
    {
      if($type=='fondsen')
        $regelsPerType[$type]++; //$this->printKop($hoofdcategorieOmschrijvingen[$hcat],'b');
      foreach($catData as $cat=>$secData)
      {
        foreach($secData as $sec=>$fondsData)
        {
          $regelsPerType[$type]++; //$this->printKop($sectorOmschrijvingen[$sec],'u');
          foreach($fondsData as $fonds)
          {
            if($fonds['type'] <> 'rente')
              $regelsPerType[$type]++; //$this->printRow($fonds);
          }
          $regelsPerType[$type]++; //$this->printRow($sectorTotalen[$hcat][$cat][$sec],'totaal','',"Totaal ".$sectorOmschrijvingen[$sec]);
          $regelsPerType[$type]++; //$this->pdf->ln();
        }
        //$this->printRow($categorieTotalen[$hcat][$cat],'totaal','b',"Totaal $cat");
      }
      //$this->printRow($hcatTotalen[$hcat],'totaal','bi',"Totaal $hcat");
    }
    if($type=='fondsen')
    {
      $regelsPerType[$type]++; //$this->printRow($typeTotalen[$type],'totaal','b',"Totaal $type");
      $regelsPerType[$type]++; //$this->pdf->ln();
    }
  }
  
  $totaalRegels=0;
  $regelsPerPagina=($this->pdf->PageBreakTrigger-$paginaBeginY)/$this->pdf->rowHeight;
  if(isset($regelsPerType['rente']))
  {
    $regelsPerType['rekening']+=$regelsPerType['rente'];
    unset($regelsPerType['rente']);
  }
  foreach($regelsPerType as $type=>$aantal)
  {
    $totaalRegels+=$aantal;
    $maximumAantalPaginas+=ceil($aantal/$regelsPerPagina);
  }
 
  $minimumAantalPaginas=ceil($totaalRegels/$regelsPerPagina);
  if($minimumAantalPaginas==$maximumAantalPaginas)
    $rekeningNieuwepagina=true;
  else
    $rekeningNieuwepagina=false;  
 //eind tellen
  foreach($waarden as $type=>$hcatData)
  {
    if($type=='rente' && $rekeningNieuwepagina==true)
      $this->pdf->AddPage();
    foreach($hcatData as $hcat=>$catData)
    {
      if($type=='fondsen')
      {
        $this->printKop($hoofdcategorieOmschrijvingen[$hcat],'b');
        $this->pdf->Ln();
      }  
      foreach($catData as $cat=>$secData)
      {
        //$this->printKop($cat,'b');
        foreach($secData as $sec=>$fondsData)
        {
          $this->printKop($sectorOmschrijvingen[$sec],'bu');
          foreach($fondsData as $fonds)
          {
            if($fonds['type'] <> 'rente')
              $this->printRow($fonds);
          }
          $this->printRow($sectorTotalen[$hcat][$cat][$sec],'totaal','b','');
          $this->pdf->ln();
        }
        //$this->printRow($categorieTotalen[$hcat][$cat],'totaal','b',"Totaal $cat");
      }
      //$this->printRow($hcatTotalen[$hcat],'totaal','bi',"Totaal $hcat");
    }
    if($type=='fondsen')
    {
      $this->printRow($typeTotalen[$type],'totaal','b',"Totaal $type");
      $this->pdf->ln();
    }
  }
  $this->printRow($totaal,'totaal','b','Totaal');

  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$query = "SELECT
			SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))  AS modelWaarde,
		  SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 AS percentageModel,
   		SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) AS portefeuilleWaarde,
		  SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100 AS percentagePortefeuille,
			(
			 SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 -
			 SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100
			) AS afwijking,
			TijdelijkeRapportage.fonds,
			TijdelijkeRapportage.fondsOmschrijving,
			TijdelijkeRapportage.actueleFonds,
			TijdelijkeRapportage.actueleValuta,
			TijdelijkeRapportage.fondsEenheid,
      TijdelijkeRapportage.bewaarder
			FROM TijdelijkeRapportage
			WHERE
			TijdelijkeRapportage.type = 'rekening'  AND
			TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND
			(TijdelijkeRapportage.portefeuille = \"".$portefeuille['Portefeuille']."\" OR TijdelijkeRapportage.portefeuille = \""."m".$this->selectData['modelcontrole_portefeuille']."\")  "
			.$__appvar['TijdelijkeRapportageMaakUniek']."
			GROUP BY TijdelijkeRapportage.type ".$afwijking."
			ORDER BY afwijking DESC ";
			debugSpecial($query,__FILE__,__LINE__);

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();

			$totaalRekeningen = array();

      if(count($uitsluitingen['gecorigeerdeRekeningen'])>0)
        array_push($totaalRekeningen,array("Liquiditeiten",'',"Model waarde","Herziene waarde"));
      else
        array_push($totaalRekeningen,array("Liquiditeiten",'',"Model waarde","Huidige waarde"));
      
			$this->pdf->Row(array(""));
      $this->pdf->excelData[]=array("");
			while($fdata = $DB2->nextRecord())
			{
				$aankoopWaarde 	= ((($portefTotaal) / 100) * $fdata['percentageModel']) - $fdata['portefeuilleWaarde'];
				$aankoopStuks 	= ($aankoopWaarde / ($fdata['actueleFonds'] * $fdata['actueleValuta']))  / $fdata['fondsEenheid'];
				$verkoopStuks = 0;
				$waardeVolgensModel = (($portefTotaal) / 100) * $fdata['percentageModel'];

				if ($fdata['portefeuilleWaarde'] != 0)
				{
				   $data = array("Effectenrekening ".$fdata['fondsOmschrijving'],
                      $fdata['bewaarder'],
											$this->formatGetal($fdata['percentageModel'],1),
											$this->formatGetal($fdata['percentagePortefeuille'],1),
											$this->formatGetal($fdata['afwijking'],1),
											$this->formatGetal($aankoopStuks,0),
											$this->formatGetal($verkoopStuks,0),
											$this->formatGetal($aankoopWaarde,2),
											"",
											$this->formatGetal($waardeVolgensModel,2),
											$this->formatGetal($fdata['actueleValuta'],2));
 
				   $this->pdf->Row($data);
           $this->pdf->excelData[]=array("Effectenrekening ".$data['fondsOmschrijving'],
                      $fdata['bewaarder'],
                      round($fdata['percentageModel'],1),
											round($fdata['percentagePortefeuille'],1),
											round($fdata['afwijking'],1),
											round($aankoopStuks,0),
											round($verkoopStuks,0),
											round($aankoopWaarde,2),
											round($waardeVolgensModel,2),
											round($fdata['actueleFonds'],2)); 

				   array_push($totaalRekeningen,array("Effectenrekening ".$fdata['fondsOmschrijving'],$fdata['bewaarder'],
				    								  $this->formatGetal($waardeVolgensModel,2),
				   									  $this->formatGetal($fdata['portefeuilleWaarde'],2)));
				}
			}
			$this->pdf->Row("");
 			foreach($totaalRekeningen as $rekeningRow)
 			{
 			   $this->pdf->Row($rekeningRow);
 			}    
    
    ///
    if(count($uitsluitingen['portefeuilleRegels'])>0)
    {
      $portefeuilleData = berekenPortefeuilleWaarde($this->portefeuille, $einddatum, (substr($einddatum, 5, 5) == '01-01')?true:false, 'EUR', $einddatum);
      vulTijdelijkeTabel($portefeuilleData, $this->portefeuille, $einddatum);
    }
                            
	}


  function printRow($data,$type='',$style='',$omschrijving='')
  {
    switch($style)
		{
			case "b" :
        $spaties='';
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'b';
       	$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
			break;
			case "bi" :
        $spaties='';
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bi';
        $this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
			break;
			case "u" :
        $spaties='';
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'u';
        $this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
			break;
			default :
        $spaties='  ';
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = '';
        $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
			break;
		}
		$this->pdf->SetFont($font,$fonttype,$fontsize);
   
    if($type=='totaal')
    {
       $this->pdf->CellBorders=array('','','','','','','','','','TS','TS','TS','','TS');
       /*
       $this->pdf->row(array($spaties.$omschrijving,'','','','','','','',$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),
                              $this->formatGetal($data['beginPortefeuilleWaardeEuro'],0),
                              $this->formatGetal($data['fondsOngerealiseerd']+$data['fondsValutaresultaat']+$data['dividend'],0),
                              '',
                              $this->formatGetal($data['aandeel'],1,true)
                              )); 
                              */
       unset($this->pdf->CellBorders);
    }
    else
    {
       $omschrijvingWidth=$this->pdf->GetStringWidth($spaties.$data['fondsOmschrijving']);
       if($omschrijvingWidth > 63 )
       {
          $data['fondsOmschrijving']=substr($data['fondsOmschrijving'],0,35).'...';
       }
       if($data['fondsOmschrijving']=='')
         $data['fondsOmschrijving']=$data['fonds'];
       
      if($data['type'] <> 'rekening')
      {
        $rendementInValuta=$this->formatGetal((($data['actueleFonds']/$data['beginwaardeLopendeJaar'])-1)*100,2,true);
        $rendementInEur=$this->formatGetal(((($data['actuelePortefeuilleWaardeEuro']+$data['dividendCorrected'])/$data['beginPortefeuilleWaardeEuro'])-1)*100,2,true);
      }
      else
      {
        $rendementInValuta='';
        $rekeningnummer = preg_replace("/[^0-9]/","", $data['fondsOmschrijving']);    
        if($rekeningnummer <> '')
        { 
          $tmp='';
          for($i=0;$i<strlen($rekeningnummer);$i++)
          {
            $tmp.=$rekeningnummer[$i];
            if($i%2 && strlen($tmp) < 9)
              $tmp.='.';
          }
          $data['fondsOmschrijving']=$tmp;
        }
      }
      
      $stringWidth=$this->pdf->GetStringWidth($spaties.$data['fondsOmschrijving']);
      //echo $this->pdf->widths[0]." ".$data['fondsOmschrijving']." $stringWidth <br>\n";
      if($stringWidth >= $this->pdf->widths[0]-2)
      {
        $omschrijvingRuimte=$this->pdf->widths[0]-$this->pdf->GetStringWidth($spaties.'...')-2;
        for($i=0; $i<strlen($data['fondsOmschrijving']); $i++) 
        {
          $char=$data['fondsOmschrijving'][$i];
          $omschrijvingRuimte-=$this->pdf->GetStringWidth($char);
          if($omschrijvingRuimte<0)
          {
            $newString=substr($data['fondsOmschrijving'],0,$i);
            break;
          }
        } 
        $data['fondsOmschrijving']=$newString.'...';
        
      }
      $this->pdf->excelData[]=array($data['fondsOmschrijving'],
                      $data['bewaarder'],
                      round($data['percentageModel'],1),
											round($data['percentagePortefeuille'],1),
											round($data['afwijking'],1),
											round($data['aankoopStuks'],0),
											round($data['verkoopStuks'],0),
											round($data['aankoopWaarde'],2),
											round($data['waardeVolgensModel'],2),
											round($data['actueleFonds'],2)); 
                            
      $this->pdf->row(array($spaties.$data['fondsOmschrijving'],
                      $data['bewaarder'],
                      $this->formatGetal($data['percentageModel'],1),
											$this->formatGetal($data['percentagePortefeuille'],1),
											$this->formatGetal($data['afwijking'],1),
											$this->formatGetal($data['aankoopStuks'],0),
											$this->formatGetal($data['verkoopStuks'],0),
											$this->formatGetal($data['aankoopWaarde'],2),
											"",
											$this->formatGetal($data['waardeVolgensModel'],2),
											$this->formatGetal($data['actueleFonds'],2))
                              );
    }
    


  }
  
  function getDividend($fonds)
  {
    global $__appvar;
    
    if($fonds=='')
      return 0;
      
     $query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro as actuelePortefeuilleWaardeEuro,
         TijdelijkeRapportage.type,
         TijdelijkeRapportage.totaalAantal
     FROM TijdelijkeRapportage
     WHERE 
       TijdelijkeRapportage.fonds='$fonds' AND
       portefeuille = '".$this->portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek']. "
     GROUP BY rapportageDatum,TijdelijkeRapportage.type";
  
     $DB = new DB();
  	 $DB->SQL($query); 
		 $DB->Query();
     $totaal=0;
     
     while($data = $DB->nextRecord())
     { 
       if($data['type']=='rente')
         $rente[$data['rapportageDatum']]=$data['actuelePortefeuilleWaardeEuro'];
       elseif($data['type']=='fondsen')  
         $aantal[$data['rapportageDatum']]=$data['totaalAantal'];
     }
     
     $totaal+=($rente[$this->rapportageDatum]-$rente[$this->rapportageDatumVanaf]);
     $totaalCorrected=$totaal;
     
     $query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving 
     FROM Rekeningmutaties 
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND 
     Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND 
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' AND 
     Grootboekrekeningen.Opbrengst=1";
		$DB->SQL($query); 
		$DB->Query();
    //echo "$query <br>\n";
    while($data = $DB->nextRecord())
    { 
      $boekdatum=substr($data['Boekdatum'],0,10);
      if(!isset($aantal[$data['Boekdatum']]))
      {
        $fondsAantal=fondsAantalOpdatum($this->portefeuille,$fonds,$data['Boekdatum']);
        $aantal[$boekdatum]=$fondsAantal['totaalAantal'];
      }
      $aandeel=1;
      
      if($aantal[$boekdatum] > $aantal[$this->rapportageDatum])
      {
        $aandeel=$aantal[$this->rapportageDatum]/$aantal[$boekdatum];
      } 
     // echo "$fonds $aandeel  $boekdatum ".$this->rapportageDatum." ".($data['Credit']-$data['Debet'])."<br>\n";
      $totaal+=($data['Credit']-$data['Debet']);
      $totaalCorrected+=(($data['Credit']-$data['Debet'])*$aandeel);
    }
    
    return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
  }
}
?>
