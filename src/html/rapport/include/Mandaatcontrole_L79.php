<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/11/17 09:40:14 $
 		File Versie					: $Revision: 1.10 $

 		$Log: Mandaatcontrole_L79.php,v $
 		Revision 1.10  2019/11/17 09:40:14  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2019/04/17 13:06:41  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2019/04/06 17:11:28  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2019/04/03 15:52:48  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2018/09/19 17:35:08  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/09/16 09:30:21  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2018/09/15 17:45:24  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/09/12 11:41:19  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2018/09/09 16:43:36  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2018/09/08 17:43:29  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.11  2018/04/07 15:24:45  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2018/03/25 10:15:58  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2018/03/18 10:54:29  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2018/03/17 18:47:40  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2018/02/24 18:32:26  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2018/02/17 19:17:53  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/02/14 16:52:34  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2017/10/22 11:11:54  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2017/10/01 14:32:43  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2017/09/13 15:44:03  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/08/19 18:19:17  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2013/10/05 15:57:41  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2012/06/17 13:03:08  rvv
 		*** empty log message ***

 		Revision 1.10  2011/11/05 16:04:41  rvv
 		*** empty log message ***

 		Revision 1.9  2011/09/14 09:26:56  rvv
 		*** empty log message ***

 		Revision 1.8  2011/06/18 15:17:55  rvv
 		*** empty log message ***

 		Revision 1.7  2011/06/02 15:04:19  rvv
 		*** empty log message ***

 		Revision 1.6  2011/04/30 16:27:12  rvv
 		*** empty log message ***

 		Revision 1.5  2010/10/06 16:34:31  rvv
 		*** empty log message ***

 		Revision 1.4  2010/08/25 19:02:17  rvv
 		*** empty log message ***

 		Revision 1.3  2010/08/06 16:32:20  rvv
 		*** empty log message ***

 		Revision 1.2  2010/03/24 17:23:03  rvv
 		*** empty log message ***

 		Revision 1.1  2008/12/03 09:50:18  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once("rapport/Zorgplichtcontrole.php");


class Mandaatcontrole_L79
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Mandaatcontrole_L79( $selectData )
	{

		$this->selectData = $selectData;
		$this->pdf = new PDFRapport('L','mm');
		$this->pdf->rapport_type = "MANDAATCONTROLE";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;


		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->underlinePercentage=0.95;


		$this->pdf->tmdatum = $this->selectData['datumTm'];

		$this->orderby  = " Client ";

		$this->pdf->excelData = array();
		$this->db=new DB();
		if($this->selectData['VermogensbeheerderVan']<>'')
    {
      $query = "SELECT Portefeuille FROM Portefeuilles WHERE Vermogensbeheerder='" . $this->selectData['VermogensbeheerderVan'] . "' AND eindDatum>now() AND consolidatie=0 limit 1";
    }
    else
    {
      $query="SELECT Portefeuille FROM Portefeuilles JOIN VermogensbeheerdersPerBedrijf ON Portefeuilles.Vermogensbeheerder=VermogensbeheerdersPerBedrijf.Vermogensbeheerder WHERE bedrijf='" . $this->selectData['bedrijf'] . "' AND eindDatum>now() AND consolidatie=0 limit 1";
    }
		$this->db->SQL($query);
		$pdata=$this->db->lookupRecord();
		loadLayoutSettings($this->pdf, $pdata['Portefeuille']);
		$this->pdf->rapport_voettext='';
		$this->pdf->rapport_koptext='';
		$this->font='arial';
		$this->fontsize=6;
    
    
    $gebruikteCrmVelden=array('MaxGewichtIndivTitel');
    $query = "DESC CRM_naw";
    $DB=new DB();
    $DB->SQL($query);
    $DB->Query();
    $crmVelden=array();
    while($data=$DB->nextRecord())
    {
      $crmVelden[]=strtolower($data['Field']);
    }
    
    $this->nawSelect='';
    foreach($gebruikteCrmVelden as $veld)
    {
      if(in_array(strtolower($veld),$crmVelden))
      {
        $this->nawSelect.=",CRM_naw.$veld ";
      }
    }

	}

	function printKop($title, $type="default")
	{
		switch($type)
		{
			case "b" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'b';
				break;
			case "bi" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bi';
				break;
			case "i" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'i';
				break;
			default :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = '';
				break;
		}

		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
		$this->pdf->SetFont($font,'',$fontsize);
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


	function mandaatMeting($pdata,$einddatum)
	{
		global $__appvar;
		$portefeuille = $pdata['Portefeuille'];
		$DB=new DB();
		$ZorgplichtCategorien=array();

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
			"FROM TijdelijkeRapportage WHERE ".
			" rapportageDatum ='".$einddatum."' AND ".
			" portefeuille = '".$portefeuille."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
			"FROM TijdelijkeRapportage WHERE ".
			" rapportageDatum ='".$einddatum."' AND ".
			" portefeuille = '".$portefeuille."' AND type='rekening' "
			.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaardeLiq = $DB->nextRecord();
		$totaalWaardeLiq = $totaalWaardeLiq['totaal'];

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
			"FROM TijdelijkeRapportage WHERE ".
			" rapportageDatum ='".$einddatum."' AND ".
			" portefeuille = '".$portefeuille."' AND beleggingscategorie IN('AAND','VA') "
			.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalAANDVA = $DB->nextRecord();
		$totaalAAND = $totaalAANDVA['totaal'];

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
			"FROM TijdelijkeRapportage WHERE ".
			" rapportageDatum ='".$einddatum."' AND ".
			" portefeuille = '".$portefeuille."' AND beleggingscategorie IN('VAR') "
			.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalVAR = $DB->nextRecord();
		$totaalVAR = $totaalVAR['totaal'];

		$query = "SELECT actuelePortefeuilleWaardeEuro AS totaal ".
			"FROM TijdelijkeRapportage JOIN Fondsen ON TijdelijkeRapportage.Fonds=Fondsen.Fonds AND Fondsen.VKM=0 WHERE ".
			" TijdelijkeRapportage.rapportageDatum ='".$einddatum."' AND ".
			" TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND type='fondsen' "
			.$__appvar['TijdelijkeRapportageMaakUniek']." ORDER BY totaal desc limit 1";
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$fondsMax = $DB->nextRecord();
		$fondsMax = $fondsMax['totaal']/$totaalAAND;
    
    $query="SELECT CRM_naw.id ".$this->nawSelect." FROM CRM_naw WHERE CRM_naw.portefeuille='".$portefeuille."'";
    $DB->SQL($query);
    $crmData=$DB->lookupRecord();

		$query = "SELECT Zorgplicht FROM ZorgplichtPerPortefeuille 
                WHERE Portefeuille = '".$portefeuille."' AND Vermogensbeheerder = '".$pdata['Vermogensbeheerder']."' AND vanaf < '$einddatum'
                  ORDER BY extra desc, Zorgplicht";
		$DB->SQL($query);
		$DB->Query();
		while($zpdata = $DB->nextRecord())
		{
			$ZorgplichtCategorien[]=$zpdata['Zorgplicht'];
		}


		foreach($ZorgplichtCategorien as $categorie)
		{
			$query = "SELECT id,Portefeuille,Vermogensbeheerder,Zorgplicht,Minimum,Maximum,add_date,add_user,ZorgplichtPerPortefeuille.maxBedrag,ZorgplichtPerPortefeuille.minBedrag,
ZorgplichtPerPortefeuille.procentueleOpslag,change_date,change_user,Norm,Vanaf,extra FROM ZorgplichtPerPortefeuille 
                  WHERE Portefeuille = '".$portefeuille."' AND Vermogensbeheerder = '".$pdata['Vermogensbeheerder']."' AND Zorgplicht='$categorie'
                  AND vanaf < '$einddatum'
                  ORDER BY vanaf desc limit 1";
			$DB->SQL($query);
			$DB->Query();
			$zpdata = $DB->nextRecord();
			$ZorgplichtPerPortefeuilleCategorien[$categorie]=$zpdata;
			$zpCategorien[$categorie]=array();
		}



		$query = "SELECT Portefeuilles.Risicoklasse,ZorgplichtPerRisicoklasse.Zorgplicht,ZorgplichtPerRisicoklasse.Minimum,ZorgplichtPerRisicoklasse.Maximum,ZorgplichtPerRisicoklasse.Norm
,ZorgplichtPerRisicoklasse.maxBedrag,ZorgplichtPerRisicoklasse.minBedrag,ZorgplichtPerRisicoklasse.procentueleOpslag
                FROM Portefeuilles
                Join ZorgplichtPerRisicoklasse ON Portefeuilles.Risicoklasse = ZorgplichtPerRisicoklasse.Risicoklasse AND
                                                  ZorgplichtPerRisicoklasse.Vermogensbeheerder = '".$pdata['Vermogensbeheerder']."'
                WHERE Portefeuilles.Portefeuille= '".$portefeuille."' ";
		$DB->SQL($query);
		$DB->Query();
		while($zpdata = $DB->nextRecord())
			$zpCategorien[$zpdata['Zorgplicht']]=$zpdata;


		$query= "SELECT
			ifnull(ZorgplichtPerFonds.Zorgplicht, ifnull(ZorgplichtPerBeleggingscategorie.Zorgplicht,  'Overige')) AS Zorgplicht,
	    sum(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro,
	    TijdelijkeRapportage.fondsOmschrijving,
      TijdelijkeRapportage.type,
      TijdelijkeRapportage.Fonds,
      TijdelijkeRapportage.rekening,
      TijdelijkeRapportage.totaalAantal,
      TijdelijkeRapportage.actueleFonds,
      TijdelijkeRapportage.hoofdcategorie
      FROM TijdelijkeRapportage
      LEFT JOIN ZorgplichtPerFonds ON TijdelijkeRapportage.fonds = ZorgplichtPerFonds.Fonds AND ZorgplichtPerFonds.Vermogensbeheerder='".$pdata['Vermogensbeheerder']."'
      LEFT JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$pdata['Vermogensbeheerder']."'
      WHERE rapportageDatum ='".$einddatum."' AND portefeuille = '".$portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
      GROUP BY Zorgplicht,Fonds,Rekening
      ORDER BY Zorgplicht, TijdelijkeRapportage.fondsOmschrijving "; //
		$DB->SQL($query);// echo "<br>\n". $query."<br>\n<br>\n";//exit;
		$DB->Query();
		//$totaalWaarde=0;

		$zorgPlichtData=array();
		while($data = $DB->nextRecord())
		{
			$zorgPlichtData[]=	$data;
		}

		$waardePerZorgplicht=array();
		foreach($zorgPlichtData as $data)
		{

			if($data['Zorgplicht'] == '')
				$data['Zorgplicht']='Leeg';

			$waardePerZorgplicht[$data['Zorgplicht']]+=$data['actuelePortefeuilleWaardeEuro'];
			if(!isset($zpCategorien[$data['Zorgplicht']]))
				$zpCategorien[$data['Zorgplicht']]=array('Minimum'=>0,'Maximum'=>100,'Zorgplicht'=>$data['Zorgplicht'],'fondsGekoppeld'=>true);
		}

		//foreach($ZorgplichtPerPortefeuilleCategorien as $zpdata)
		//	$zpCategorien[$zpdata['Zorgplicht']]=$zpdata;

		$mandaatData=array();
		if(isset($crmData['MaxGewichtIndivTitel']) && $crmData['MaxGewichtIndivTitel']<>'')
      $mandaatData['MaxGewichtIndivTitel']=$crmData['MaxGewichtIndivTitel'];
		else
      $mandaatData['MaxGewichtIndivTitel']=10;
		
		$velden=array('minBedrag','maxBedrag','procentueleOpslag','Minimum','Maximum');
		foreach ($zpCategorien as $zpCat=>$zpdata)
		{
			$catWaarde=$waardePerZorgplicht[$zpCat];
			$mandaatData[$zpCat]['totaalWaarde']=$totaalWaarde;
			$mandaatData[$zpCat]['catWaarde']=$catWaarde;
			$mandaatData[$zpCat]['catProcent']=$catWaarde/$totaalWaarde*100;
			foreach($velden as $veld)
			{
				if (isset($ZorgplichtPerPortefeuilleCategorien[$zpCat][$veld]))
				{
					$mandaatData[$zpCat][$veld] = $ZorgplichtPerPortefeuilleCategorien[$zpCat][$veld];
				}
				else
				{
					$mandaatData[$zpCat][$veld]=$zpdata[$veld];
				}
			}

			$minToegestanecatWaarde=$totaalWaarde*$mandaatData[$zpCat]['Minimum']/100;
			if($mandaatData[$zpCat]['minBedrag']<>0)
				$minToegestanecatWaarde	=$mandaatData[$zpCat]['minBedrag'];

			if($catWaarde > $minToegestanecatWaarde)
				$mandaatData[$zpCat]['bovenMinimum']='Ja';
			else
				$mandaatData[$zpCat]['bovenMinimum']='Nee';
			$mandaatData[$zpCat]['minCategorieBedrag']=$minToegestanecatWaarde;

			$maximumcatWaarde=($totaalWaarde*$mandaatData[$zpCat]['procentueleOpslag']/100)+($totaalWaarde*$mandaatData[$zpCat]['Maximum']/100);//$catWaarde+

			if($mandaatData[$zpCat]['minBedrag']<>0)
				$maximumcatWaarde	= $mandaatData[$zpCat]['minBedrag']+($mandaatData[$zpCat]['minBedrag']*$mandaatData[$zpCat]['procentueleOpslag']/100);
			if($mandaatData[$zpCat]['maxBedrag']<>0)
				$maximumcatWaarde	=$mandaatData[$zpCat]['maxBedrag'];//+($mandaatData[$zpCat]['maxBedrag']*$mandaatData[$zpCat]['procentueleOpslag']/100);


			if($catWaarde<$maximumcatWaarde)
				$mandaatData[$zpCat]['onderMaximum']='Ja';
			else
				$mandaatData[$zpCat]['onderMaximum']='Nee';

			$mandaatData[$zpCat]['maxCategorieBedrag']=$maximumcatWaarde;

		}
		$mandaatData['waardeLiq']=$totaalWaardeLiq;
		$mandaatData['waardeTotaal']=$totaalWaarde;
		$mandaatData['maxFonds']=	$fondsMax;
		$mandaatData['waardeAand']=$totaalAAND;
		$mandaatData['waardeVar']=$totaalVAR;


		return $mandaatData;
	}

	function writeRapport()
	{
		global $__appvar;

		$begindatum = jul2sql($this->selectData['datumVan']);
		$einddatum = jul2sql($this->selectData['datumTm']);

		$this->pdf->__appvar = $__appvar;

		$selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
		$records = $selectie->getRecords();
		$portefeuilles = $selectie->getSelectie();

		if($records <= 0)		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			if($this->progressbar)
				$this->progressbar->hide();
			exit;
		}

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}



		// voor kopjes
		$this->pdf->setWidths(array(30,20,15,15,15,20,15,20,15,15,15,13,22,13,22));
		$this->pdf->setAligns(array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));


	//	$this->pdf->excelData[]=array("Client","Portefeuille","Categorie","Totale Waarde","Categorie waarde","Categorie %",'Minimum bedrag Categorie','Minimum % Categorie',"Maximum Categorie %","% Categorie Toevoeging als Categorie boven Maximum is","Boven Minimum",
	//		"Mimimum Categorie Bedrag Toegestaan","Onder Maximum","Maximum Categorie Bedrag Toegestaan");

		foreach($portefeuilles as $pdata)
		{
			$totalen = array();
			$this->waardeEurTotaalAlles =0;
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}
			$portefeuille = $pdata['Portefeuille'];
			if(substr($einddatum,5,5)=='01-01')
				$beginJaar=true;
			else
				$beginJaar=false;
//listarray($pdata);

			if(isset($pdata['consolidatie']))
			{
				if ($this->selectData['restrictie_alleenConsolidaties'] == 1 && !is_array($pdata['portefeuilles']) && $pdata['consolidatie'] < 1)
					continue;
			}
			elseif($this->selectData['restrictie_alleenConsolidaties']==1 && !is_array($pdata['portefeuilles']))
				continue;

			$fondswaarden =  berekenPortefeuilleWaarde($portefeuille,  $einddatum,$beginJaar,'EUR',$begindatum);

			vulTijdelijkeTabel($fondswaarden ,$portefeuille, $einddatum);
			$meetData=$this->mandaatMeting($pdata,$einddatum);

			$kol4=$meetData['waardeLiq']+$meetData['waardeVar']-$meetData['VAR']['minBedrag'];
			$kol5=$kol4/$meetData['waardeTotaal']*100;
			//logscherm("$kol4=".$meetData['waardeLiq']."+".$meetData['waardeVar']."-".$meetData['VAR']['minBedrag']);
			//logscherm("$kol4+".$meetData['VAR']['minBedrag']."<".$meetData['waardeTotaal']."*".$meetData['VAR']['Maximum']."*.01");//12)
      //listarray($meetData);
			$regelsEerstePagina[]=array($pdata['Client'], //1
//				$pdata['Portefeuille'],//2
				$this->formatGetal($meetData['waardeTotaal'], 0),//3
				$this->formatGetal($meetData['waardeLiq'], 0),//4
				$this->formatGetal($kol5, 1).'%',//Waarde vastrentend %
        $this->formatGetal($meetData['Bedrijfsobl']['catProcent'], 1).'%',//Waarde bedrijfsobligaties %
        $this->formatGetal($meetData['ObligHR']['catProcent'], 1).'%',//Waarde hoogrenderend %
				$this->formatGetal($meetData['VAR']['minBedrag'], 0),//6
				$this->formatGetal($meetData['VAR']['Minimum'], 1).'%',//7
				$this->formatGetal($meetData['VAR']['Maximum'], 1).'%',//8
				$this->formatGetal($meetData['Bedrijfsobl']['Maximum'], 1).'%',//9
        $this->formatGetal($meetData['ObligHR']['Maximum'], 1).'%',//9
				($meetData['waardeLiq']>=$meetData['VAR']['minBedrag'])?'Ja':'Nee',//10
				($kol5>=$meetData['VAR']['Minimum'])?'Ja':'Nee',//11
				($kol5<=$meetData['VAR']['Maximum'])?'Ja':'Nee',//12
        ($meetData['Bedrijfsobl']['catWaarde']<=$kol4)?'Ja':'Nee',
        ($meetData['ObligHR']['catWaarde']<=$kol4)?'Ja':'Nee');//13

//echo "<br>\n". $pdata['Portefeuille']." ".$meetData['DM']['catWaarde']."/".$meetData['waardeAand']."*100>=".$meetData['DM']['Minimum']." && ".$meetData['DM']['catWaarde']."/".$meetData['waardeAand']."*100<=".$meetData['DM']['Maximum']."<br>\n";
//			echo "eerste:". ($meetData['waardeAand']*100>=$meetData['DM']['Minimum'])." tweede:".($meetData['DM']['catWaarde']/$meetData['waardeAand']*100<=$meetData['DM']['Maximum'])." = ".(($meetData['DM']['catWaarde']/$meetData['waardeAand']*100>=$meetData['DM']['Minimum'] && $meetData['DM']['catWaarde']/$meetData['waardeAand']*100<=$meetData['DM']['Maximum'])?'Ja':'Nee')."<br>\n";

      $regelsTweedePagina[]=array($pdata['Client'], //1
				$pdata['Portefeuille'],//2
				$this->formatGetal($meetData['waardeTotaal'], 0),//3
				$this->formatGetal($meetData['waardeAand']/$meetData['waardeTotaal']*100, 1).'%',//4
				$this->formatGetal($meetData['maxFonds']*100, 1).'%',//5
				$this->formatGetal($meetData['DM']['catWaarde']/$meetData['waardeAand']*100, 1).'%',//6
				$this->formatGetal($meetData['EM']['catWaarde']/$meetData['waardeAand']*100, 1).'%',//7
				$this->formatGetal($meetData['Vastgoed']['catWaarde']/$meetData['waardeAand']*100, 1).'%',//8
				$this->formatGetal($meetData['MaxGewichtIndivTitel'], 1).'%',//9
				$meetData['DM']['Minimum']."% - ".$meetData['DM']['Maximum'].'%',//10
				$meetData['EM']['Minimum']."% - ".$meetData['EM']['Maximum'].'%',//11
				$meetData['Vastgoed']['Minimum']."% - ".$meetData['Vastgoed']['Maximum'].'%',//12
				($meetData['maxFonds']*100<=$meetData['MaxGewichtIndivTitel'])?'Ja':'Nee',//13
				($meetData['DM']['catWaarde']/$meetData['waardeAand']*100>=$meetData['DM']['Minimum'] && $meetData['DM']['catWaarde']/$meetData['waardeAand']*100<=$meetData['DM']['Maximum'] || ($meetData['waardeAand']==0 && $meetData['DM']['catWaarde']==0))?'Ja':'Nee',//14
				($meetData['EM']['catWaarde']/$meetData['waardeAand']*100>=$meetData['EM']['Minimum'] && $meetData['EM']['catWaarde']/$meetData['waardeAand']*100<=$meetData['EM']['Maximum'] || ($meetData['waardeAand']==0 && $meetData['EM']['catWaarde']==0))?'Ja':'Nee',//15
				($meetData['Vastgoed']['catWaarde']/$meetData['waardeAand']*100>=$meetData['Vastgoed']['Minimum'] && $meetData['Vastgoed']['catWaarde']/$meetData['waardeAand']*100<=$meetData['Vastgoed']['Maximum'] || ($meetData['waardeAand']==0 && $meetData['DM']['Vastgoed']==0))?'Ja':'Nee');//16


			//		$this->pdf->excelData[] = array('');
			verwijderTijdelijkeTabel($portefeuille, $einddatum);
			
		}

		$this->pdf->AddPage();
		$this->pdf->SetFont($this->font,"B",$this->fontsize+4);
		$this->pdf->setWidths(array(100));
		$this->pdf->row(array("Compliance Check"));
		$this->pdf->SetFont($this->font,"B",$this->fontsize+1);
		$this->pdf->row(array("Data per einde dag ".date("d/m/Y",$this->selectData['datumTm'])));
		$this->pdf->SetFont($this->font,"B",$this->fontsize+2);
		$this->pdf->ln();
		$this->pdf->row(array("Cash compliance"));
		$this->pdf->ln();

		$this->pdf->SetFont($this->font,"B",$this->fontsize);
    $w=16;
    $w2=20;
		$this->pdf->setWidths(array($w2,$w+$w+$w+$w+$w,$w+$w+$w+$w+$w,$w2+$w2+$w2+$w2+$w2));
		$this->pdf->setAligns(array('L','C','C','C'));
		$this->pdf->CellBorders=array('','US','US','US');
		$this->pdf->row(array("","Huidige verdeling","Compliance Regels","Compliance Check"));
		unset($this->pdf->CellBorders);
		$this->pdf->SetFont($this->font,"U",$this->fontsize);
		
		$this->pdf->setWidths(array($w2,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w2,$w2,$w2,$w2,$w2));
		$this->pdf->setAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
		$this->pdf->row(array("Portfolio\nCode",//1
//											"Rekening\nNummer",//2
											"Totale Waarde",//3
											"Waarde\ncash €",//4
											"Waarde vast-\nrentend %",//5
                      "Waarde bedrijfsobl %",//5
                      "Waarde hoogrend %",//5
											"Minimum\nCash Bedrag",//6
											"Minimum\nVastrentend",//7
											"Maximum\nVastrentend",//8
											"Max.bedrijfs-\nobligaties %",//9
                      "Max.hoog-\nrendered %",//10
											"Boven minimum\nCash",//12
											"Boven minimum\nVastrentend",//12
											"Onder maximum\nVastrentend",//13
                      "Onder max\nbedrijfsobligaties",
                      "Onder max\nhoogrenderend"));//14
		$this->pdf->SetFont($this->font,"",$this->fontsize);
    $rood=array('r' => 150, 'g' => 0, 'b' => 0);
		$groen=array('r' => 0, 'g' => 150, 'b' => 0);
		foreach($regelsEerstePagina as $row)
		{
			for($i=11;$i<16;$i++)
			{
				if ($row[$i] == 'Nee'){	$this->pdf->CellFontColor[$i] = $rood;}
				else {$this->pdf->CellFontColor[$i] = $groen;	}
			}
			$this->pdf->row($row);
    }
    unset($this->pdf->CellFontColor);

		$this->pdf->AddPage();
		$this->pdf->SetFont($this->font,"B",$this->fontsize+4);
		$this->pdf->setWidths(array(100));
		$this->pdf->row(array("Compliance Check"));
		$this->pdf->SetFont($this->font,"B",$this->fontsize+1);
		$this->pdf->row(array("Data per einde dag ".date("d/m/Y",$this->selectData['datumTm'])));
		$this->pdf->SetFont($this->font,"B",$this->fontsize+2);
		$this->pdf->ln();
		$this->pdf->row(array("Holdings compliance"));
		$this->pdf->ln();

		$this->pdf->SetFont($this->font,"B",$this->fontsize);
		$this->pdf->setWidths(array(20+20,17+17+18+14+14+16,20+18+18+18,20+18+18+18));
		$this->pdf->setAligns(array('L','C','C','C'));
		$this->pdf->CellBorders=array('','US','US','US');
		$this->pdf->row(array("","Huidige verdeling","Compliance Regels","Compliance Check"));
		unset($this->pdf->CellBorders);
		$this->pdf->SetFont($this->font,"U",$this->fontsize);
		$this->pdf->setWidths(array(20,20,17,17,18,14,14,16,20,18,18,18,20,18,18,18));
		$this->pdf->setAligns(array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
		$this->pdf->row(array("Client","Rekening\nNummer","Totale Waarde","Aandelen\nPlus %","Grootste Ind. Gewicht","DM %","EM %","Vastgoed %",
											"Max.gewicht\nindividuele titel","Bandbreedte\nDM","Bandbreedte\nEM","Bandbreedte\nVastgoed",
											"Max.Gewicht\nIndividuele titel","Bandbreedte\nDM","Bandbreedte\nEM","Bandbreedte\nVastgoed"));
		$this->pdf->SetFont($this->font,"",$this->fontsize);
		foreach($regelsTweedePagina as $row)
		{
			for($i=12;$i<16;$i++)
			{
				if ($row[$i] == 'Nee'){	$this->pdf->CellFontColor[$i] = $rood;}
				else {$this->pdf->CellFontColor[$i] = $groen;	}
			}
			$this->pdf->row($row);
		}

		if($this->progressbar)
			$this->progressbar->hide();
	}
}
?>