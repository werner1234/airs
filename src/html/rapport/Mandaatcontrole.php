<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/11/16 17:36:34 $
 		File Versie					: $Revision: 1.14 $

 		$Log: Mandaatcontrole.php,v $
 		Revision 1.14  2019/11/16 17:36:34  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2018/09/08 17:46:11  rvv
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


class Mandaatcontrole
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Mandaatcontrole( $selectData )
	{

		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "mandaatcontrole";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);

		$this->pdf->tmdatum = $this->selectData['datumTm'];

		$this->orderby  = " Client ";

		$this->pdf->excelData = array();
		//rvv
	//	loadLayoutSettings($this->pdf, $this->selectData['portefeuilleVan']); //rvv 29-08-06
		$this->db=new DB();
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
		$this->pdf->setWidths(array(30,25,25,25,25,15,20,15,15,15,13,22,13,22));
		$this->pdf->setAligns(array('L','L','L','R','R','R','R','R','R','R','R','R','R','R'));

		$this->pdf->AddPage();
		$this->pdf->excelData[]=array("Client","Portefeuille","Categorie","Totale Waarde","Categorie waarde","Categorie %",'Minimum bedrag Categorie','Minimum % Categorie',"Maximum Categorie %","% Categorie Toevoeging als Categorie boven Maximum is","Boven Minimum",
			"Mimimum Categorie Bedrag Toegestaan","Onder Maximum","Maximum Categorie Bedrag Toegestaan");

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

			if( $this->selectData['restrictie_alleenConsolidaties']==1 && !(isset($pdata['portefeuilles']) && count($pdata['portefeuilles'])>1) )
			  continue;

			$fondswaarden =  berekenPortefeuilleWaarde($portefeuille,  $einddatum,$beginJaar,'EUR',$begindatum);

			vulTijdelijkeTabel($fondswaarden ,$portefeuille, $einddatum);
			$meetData=$this->mandaatMeting($pdata,$einddatum);

      foreach($meetData as $zorgplicht=>$mandaatData)
			{

        if($this->selectData['mandaat_zorgplichtCategorie']=='' || $this->selectData['mandaat_zorgplichtCategorie']==$zorgplicht)
				{
					$this->pdf->row(array($pdata['Client'], $pdata['Portefeuille'],$zorgplicht,
														$this->formatGetal($mandaatData['totaalWaarde'], 0),
														$this->formatGetal($mandaatData['catWaarde'], 0),
														$this->formatGetal($mandaatData['catProcent'], 2),
														$this->formatGetal($mandaatData['minBedrag'], 0),
														$this->formatGetal($mandaatData['Minimum'], 2),
														$this->formatGetal($mandaatData['Maximum'], 2),
														$this->formatGetal($mandaatData['procentueleOpslag'], 2),
														$mandaatData['bovenMinimum'],
														$this->formatGetal($mandaatData['minCategorieBedrag'],0),
														$mandaatData['onderMaximum'],
														$this->formatGetal($mandaatData['maxCategorieBedrag'], 0)));
					$this->pdf->excelData[]=array($pdata['Client'], $pdata['Portefeuille'],$zorgplicht,
						round($mandaatData['totaalWaarde'], 2),
						round($mandaatData['catWaarde'], 2),
						round($mandaatData['catProcent'], 2),
						round($mandaatData['minBedrag'], 2),
						round($mandaatData['Minimum'], 2),
						round($mandaatData['Maximum'], 2),
						round($mandaatData['procentueleOpslag'], 2),
						$mandaatData['bovenMinimum'],
						round($mandaatData['minCategorieBedrag'],2),
						$mandaatData['onderMaximum'],
						round($mandaatData['maxCategorieBedrag'], 2));
				}
			}
  


	//		$this->pdf->excelData[] = array('');
			verwijderTijdelijkeTabel($portefeuille, $einddatum);
		}
		if($this->progressbar)
			$this->progressbar->hide();
	}
}
?>