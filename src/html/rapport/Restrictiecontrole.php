<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/06/13 15:14:32 $
 		File Versie					: $Revision: 1.10 $

 		$Log: Restrictiecontrole.php,v $
 		Revision 1.10  2020/06/13 15:14:32  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2020/05/31 16:26:06  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2020/05/30 15:28:01  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2019/11/16 17:36:34  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2018/09/19 17:31:53  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/09/05 15:50:43  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.3  2018/03/12 06:37:01  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2018/03/11 10:52:28  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2018/02/17 19:17:53  rvv
 		*** empty log message ***
 		
 	

*/

include_once("rapportRekenClass.php");
include_once("rapport/Zorgplichtcontrole.php");


class Restrictiecontrole
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Restrictiecontrole( $selectData )
	{

		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "restrictiecontrole";
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
		loadLayoutSettings($this->pdf, $this->selectData['portefeuilleVan']); //rvv 29-08-06
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

	function getUitsluitingen($vermogensbeheerder,$portefeuille,$datum,$koppelVertaling)
	{
		$query="SELECT
contractueleUitsluitingen.categoriesoort,
contractueleUitsluitingen.fonds,
contractueleUitsluitingen.portefeuille,
contractueleUitsluitingen.vermogensbeheerder,
contractueleUitsluitingen.categorie,
contractueleUitsluitingen.vanaf
FROM
contractueleUitsluitingen
WHERE ((Vermogensbeheerder='".$vermogensbeheerder."' AND Portefeuille='')  OR Portefeuille='".$portefeuille."' ) 
AND vanaf < '".$datum."'
AND (einddatum='0000-00-00' OR einddatum > '".$datum."')";
		$this->db->SQL($query);
		$this->db->Query();
		$uitgeslotenFondsen=array();
		$uitegeslotenCategorien=array();

		while($data = $this->db->nextRecord())
		{
			if($data['fonds']<>'')
				$uitgeslotenFondsen[$data['fonds']]=$data['fonds'];
			if($data['categoriesoort']<>'')
				$uitegeslotenCategorien[$koppelVertaling[$data['categoriesoort']]][$data['categorie']]=$data['categorie'];
		}
		return array('categorien'=>$uitegeslotenCategorien,'fondsen'=>$uitgeslotenFondsen);
	}

	function writeRapport()
	{
		global $__appvar;

		$koppelVertaling=array('Beleggingscategorien'=>'beleggingscategorie',
													 'Beleggingssectoren'=>'beleggingssector',
													 'Fondssoort'=>'fondssoort',
													 'Regios'=>'regio',
													 'afmCategorien'=>'afmCategorie',
													 'Valuta'=>'valuta',
													 'Rating'=>'rating',
													 'Zorgplichtcategorien'=>'zorgplicht',
													 'Hoofdcategorien'=>'hoofdcategorie');

		$begindatum = jul2sql($this->selectData['datumVan']);
		$einddatum = jul2sql($this->selectData['datumTm']);


		$this->pdf->__appvar = $__appvar;

		$selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();


		if($records <= 0)		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
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
		$this->pdf->setWidths(array(65   ,20,20,30,30,5,20,30,30,30));
		$this->pdf->setAligns(array('L','R','R','R','R','R','R','R','R','R'));


		$this->pdf->excelData[]=array("Client","Naam","Portefeuille","Accountmanager",'Soort overeenkomst','Risicoprofiel',"Depotbank","beleggingscategorie","Fonds","Aantal","Per stuk in valuta","Portefeuille in valuta","Portefeuille in EUR",
			"Per stuk in valuta","Portefeuille in valuta", "Portefeuille in EUR",'conclusie','detail','Overige beperkingen');

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
			$fondswaarden =  berekenPortefeuilleWaarde($portefeuille,  $einddatum,$beginJaar,'EUR',$begindatum);
			vulTijdelijkeTabel($fondswaarden ,$portefeuille, $einddatum);
			$uitsluitingen=$this->getUitsluitingen($pdata['Vermogensbeheerder'], $portefeuille,$einddatum,$koppelVertaling );


			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.beleggingscategorieOmschrijving, TijdelijkeRapportage.valuta,TijdelijkeRapportage.type, 
			if(TijdelijkeRapportage.type='fondsen',1,if(TijdelijkeRapportage.type='rente',2,3)) as volgorde, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeEuro  as beginPortefeuilleWaardeEuro,
			TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, 
			TijdelijkeRapportage.beleggingscategorie, 
			TijdelijkeRapportage.hoofdcategorie, 
			TijdelijkeRapportage.beleggingssector, 
			Fondsen.fondssoort, 
			TijdelijkeRapportage.regio, 
			TijdelijkeRapportage.afmCategorie, 
			Fondsen.rating, 
			TijdelijkeRapportage.Fonds,
			CRM_naw.profielOverigeBeperkingen,
			if(ZorgplichtPerFonds.Zorgplicht <> null ,ZorgplichtPerFonds.Zorgplicht,ZorgplichtPerBeleggingscategorie.Zorgplicht) as zorgplicht,
			TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd ".
				" FROM TijdelijkeRapportage 
			  LEFT JOIN Fondsen on TijdelijkeRapportage.Fonds=Fondsen.Fonds 
			  LEFT JOIN ZorgplichtPerFonds ON TijdelijkeRapportage.fonds = ZorgplichtPerFonds.Fonds AND ZorgplichtPerFonds.Vermogensbeheerder='".$pdata['Vermogensbeheerder']."' 
			  LEFT JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$pdata['Vermogensbeheerder']."' 
			  LEFT JOIN CRM_naw ON TijdelijkeRapportage.portefeuille=CRM_naw.portefeuille
			  WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$einddatum."' ".
				$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY volgorde ,TijdelijkeRapportage.beleggingscategorieVolgorde asc,  TijdelijkeRapportage.valutaVolgorde asc, 
			TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($subquery,__FILE__,__LINE__);

			$this->db->SQL($subquery);
			$this->db->Query();
			$lastCategorie='';
			$lastType='';


			$buffer=array();
      $toevoegen=false;
      $profielOverigeBeperkingen='';
			while($subdata = $this->db->NextRecord())
      {
        $conclusie = 'voldoet';
        $detail = '';
        if (isset($uitsluitingen['fondsen'][$subdata['Fonds']]))
        {
          $conclusie = 'voldoet niet';
          $detail .= ",Uitgesloten fonds";
        }
        foreach ($koppelVertaling as $check)
        {
          if (isset($uitsluitingen['categorien'][$check][$subdata[$check]]))
          {
            $conclusie = 'voldoet niet';
            $detail .= ",Uitgesloten in $check";
          }
        }
        $subdata['conclusie']=$conclusie;
        $subdata['detail']=$detail;
        $buffer[]=$subdata;
  
        if($this->selectData['restrictie_uitvoer']=='alles' || ($this->selectData['restrictie_uitvoer']=='afwijkingen' && $detail<>'') || ($this->selectData['restrictie_uitvoer']=='afwijkingenEnBeperkingen' && ($detail<>'' || $subdata['profielOverigeBeperkingen']<>''))  )
        {
          $toevoegen=true;
        }
        if($subdata['profielOverigeBeperkingen']<>'')
          $profielOverigeBeperkingen=$subdata['profielOverigeBeperkingen'];
      }
      
      
      if($toevoegen==true)
      {
        $this->pdf->portefeuille = $pdata['Portefeuille'];
        $this->pdf->rapport_kop = $pdata['Portefeuille'] . " - " . $pdata['Client'] . " - " . $pdata['Naam'];
        $this->pdf->AddPage();
  
        if($profielOverigeBeperkingen<>'')
        {
          //$this->pdf->ln();
          $y=$this->pdf->getY();
          $this->pdf->setY(20);
          $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
          $this->pdf->row(array('Overige beperkingen'));
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
          $this->pdf->multicell(200, 4, $profielOverigeBeperkingen);
          $this->pdf->excelData[]=array($pdata['Client'],
            $pdata['Naam'],
            $pdata['Portefeuille'],
            $pdata['Accountmanager'],
            $pdata['SoortOvereenkomst'],
            $pdata['Risicoklasse'],
            $pdata['Depotbank'],
            '','','',
            '',
            '',
            '',
            '',
            '',
            '',
            '','',$profielOverigeBeperkingen);
          $this->pdf->setY($y);
        }
        
      foreach($buffer as $subdata)
      {
        $conclusie=$subdata['conclusie'];
        $detail=$subdata['detail'];
        if($lastCategorie=='' || $lastCategorie <> $subdata['beleggingscategorieOmschrijving'])
					  $this->printKop(vertaalTekst($subdata['beleggingscategorieOmschrijving'],$this->pdf->rapport_taal),$this->pdf->rapport_kop3_fontstyle);
				$lastCategorie=$subdata['beleggingscategorieOmschrijving'];

				if($lastType<>$subdata['type'] && $subdata['type']=='rente')
				{
					$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),$this->pdf->rapport_kop3_fontstyle);
				}
				elseif($lastType<>$subdata['type'] && $subdata['type']=='rekening')
				{
					//	$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),$this->pdf->rapport_kop3_fontstyle);
				}
				$lastType=$subdata['type'];


				if($this->selectData['restrictie_uitvoer']=='alles' || ($this->selectData['restrictie_uitvoer']=='afwijkingen' && $detail<>'') || ($this->selectData['restrictie_uitvoer']=='afwijkingenEnBeperkingen' && ($detail<>'' || $subdata['profielOverigeBeperkingen']<>'')) )
				{
				  if($subdata['type']=='fondsen')
			  	{
				  	$this->pdf->row(array($subdata['fondsOmschrijving'],
														$this->formatAantal($subdata['totaalAantal'], 0, $this->pdf->rapport_HSE_aantalVierDecimaal),
														$this->formatGetal($subdata['beginwaardeLopendeJaar'], 2),
														$this->formatGetal($subdata['beginPortefeuilleWaardeInValuta'], $this->pdf->rapport_decimaal),
														$this->formatGetal($subdata['beginPortefeuilleWaardeEuro'], $this->pdf->rapport_decimaal, true),
														"",
														$this->formatGetal($subdata['actueleFonds'], 2),
														$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'], $this->pdf->rapport_decimaal),
														$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_decimaal),
														$conclusie));
				  	$this->pdf->excelData[]=array($pdata['Client'],
						$pdata['Naam'],
						$pdata['Portefeuille'],
						$pdata['Accountmanager'],
						$pdata['SoortOvereenkomst'],
						$pdata['Risicoklasse'],
						$pdata['Depotbank'],
						$subdata['beleggingscategorieOmschrijving'],$subdata['fondsOmschrijving'],round($subdata['totaalAantal']),
						round($subdata['beginwaardeLopendeJaar'], 4),
						round($subdata['beginPortefeuilleWaardeInValuta'], $this->pdf->rapport_decimaal),
						round($subdata['beginPortefeuilleWaardeEuro'], $this->pdf->rapport_decimaal, true),
						round($subdata['actueleFonds'], 4),
						round($subdata['actuelePortefeuilleWaardeInValuta'], $this->pdf->rapport_decimaal),
						round($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_decimaal),
						$conclusie,$detail,$subdata['profielOverigeBeperkingen']);

				  }
			  	elseif($subdata['type']=='rente' || $subdata['type']=='rekening')
				  {
				  	$this->pdf->row(array($subdata['fondsOmschrijving'], "", "", "", "", "", "",
														$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'], $this->pdf->rapport_decimaal),
														$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_decimaal),
														$conclusie));
					  $this->pdf->excelData[]=array($pdata['Client'],
						$pdata['Naam'],
						$pdata['Portefeuille'],
						$pdata['Accountmanager'],
						$pdata['SoortOvereenkomst'],
						$pdata['Risicoklasse'],
						$pdata['Depotbank'],$subdata['beleggingscategorieOmschrijving'],$subdata['fondsOmschrijving'], "", "", "", "", "",
						round($subdata['actuelePortefeuilleWaardeInValuta'], $this->pdf->rapport_decimaal),
						round($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_decimaal),
						$conclusie,$detail);
				  }
				}
				$valutaWaarden[$subdata['valuta']] = $subdata['actueleValuta'];
				$valutaOmschrijving[$subdata['valuta']] = $subdata['ValutaOmschrijving'];

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