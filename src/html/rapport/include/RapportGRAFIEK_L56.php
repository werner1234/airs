<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/05/11 16:48:39 $
File Versie					: $Revision: 1.3 $

$Log: RapportGRAFIEK_L56.php,v $
Revision 1.3  2019/05/11 16:48:39  rvv
*** empty log message ***

Revision 1.2  2016/05/28 14:21:20  rvv
*** empty log message ***

Revision 1.1  2016/05/25 14:15:31  rvv
*** empty log message ***

Revision 1.6  2015/04/01 16:00:45  rvv
*** empty log message ***

Revision 1.5  2015/03/11 17:13:49  rvv
*** empty log message ***

Revision 1.4  2015/03/01 14:08:16  rvv
*** empty log message ***

Revision 1.3  2015/02/18 17:09:13  rvv
*** empty log message ***

Revision 1.2  2015/02/15 10:35:15  rvv
*** empty log message ***

Revision 1.1  2015/02/15 10:26:57  rvv
*** empty log message ***

Revision 1.6  2014/10/29 16:47:19  rvv
*** empty log message ***

Revision 1.5  2011/02/10 19:56:35  rvv
*** empty log message ***

Revision 1.4  2011/01/08 14:27:56  rvv
*** empty log message ***

Revision 1.3  2010/12/22 18:45:30  rvv
*** empty log message ***

Revision 1.2  2010/12/19 13:05:15  rvv
*** empty log message ***

Revision 1.1  2010/07/04 15:24:39  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportGRAFIEK_L56
{
	function RapportGRAFIEK_L56($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "GRAFIEK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Onderverdeling in beleggingscategorieën";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
		$this->portefeuilleNamen=array();
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }


	function writeRapport()
	{
     $this->verdeling();
  }

	function getPortefeuilleNaam($portefeuille)
	{
		if(isset($this->portefeuilleNamen[$portefeuille]))
			return $this->portefeuilleNamen[$portefeuille];

		$db=new DB();
		$query="SELECT CRM_naw.zoekveld,Portefeuilles.Client, if(Portefeuilles.Depotbank='TGB','IGB',Portefeuilles.Depotbank) as Depotbank FROM Portefeuilles LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.portefeuille WHERE Portefeuilles.Portefeuille='$portefeuille'";
		$db->sql($query);
		$tmp=$db->lookupRecord();
		if($tmp['zoekveld']<>'')
			$naam=$tmp['zoekveld']." / ".$tmp['Depotbank'];
		else
			$naam=$tmp['Client']." / ".$tmp['Depotbank'];

		$this->portefeuilleNamen[$portefeuille]=$naam;
		return $naam;
	}

	function verdeling()
	{
		global $__appvar;
		$DB=new DB();


		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
			"FROM TijdelijkeRapportage WHERE ".
			" rapportageDatum ='".$this->rapportageDatum."' AND ".
			" portefeuille = '".$this->portefeuille."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		$geconsolideerdeWaarde=array('totaal'=>$data['totaal']);
		$geconsolideerdeWaarden=array();
		$query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving, TijdelijkeRapportage.beleggingscategorieVolgorde, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta, TijdelijkeRapportage.beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS actuelePortefeuilleWaardeEuro ".
			" FROM TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.beleggingscategorie".
			" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
			if($categorien['beleggingscategorie']=='')
			{
				$categorien['beleggingscategorie']='GeenCategorie';
				$categorien['Omschrijving']='Geen categorie';
			}
			$geconsolideerdeWaarde[$categorien['beleggingscategorie']]+=$categorien['actuelePortefeuilleWaardeEuro'];
  	}




		if(!is_array($this->pdf->grafiekKleuren))
		{
			$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
			$DB->SQL($q);
			$DB->Query();
			$kleuren = $DB->LookupRecord();
			$kleuren = unserialize($kleuren['grafiek_kleur']);
			$this->pdf->grafiekKleuren=$kleuren;
		}

		$totaalWaarde=0;
    if(is_array($this->pdf->__appvar['consolidatie']))//if(is_array($this->pdf->portefeuilles))
		{
			foreach($this->pdf->portefeuilles as $portefeuille)
			{
				$gegevens=berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum);
        $portefeuilleWaarden[$portefeuille]['belCatWaarde']=array();
				foreach($gegevens as $waarde)
				{
					$portefeuilleWaarden[$portefeuille]['belCatWaarde'][$waarde['beleggingscategorie']]+=$waarde['actuelePortefeuilleWaardeEuro'];
					$portefeuilleWaarden[$portefeuille]['totaleWaarde']+=$waarde['actuelePortefeuilleWaardeEuro'];
					$categorieVolgorde[$waarde['beleggingscategorieVolgorde']]=$waarde['beleggingscategorie'];
					$categorieOmschrijving[$waarde['beleggingscategorie']]=$waarde['beleggingscategorieOmschrijving'];
					$totaalWaarde+=$waarde['actuelePortefeuilleWaardeEuro'];
				}
			}
			foreach($portefeuilleWaarden as $portefeuille=>$waarden)
			{
				foreach($waarden['belCatWaarde'] as $categorie=>$waardeEur)
				{
					$portefeuilleNaam=$this->getPortefeuilleNaam($portefeuille);
					$percentage=($waardeEur/$waarden['totaleWaarde']);
					$portefeuilleWaarden[$portefeuille]['belCatPercentage'][$categorieOmschrijving[$categorie]]=$percentage*100;
					$portefeuilleWaarden[$portefeuille]['belCatKleuren'][$categorieOmschrijving[$categorie]]=$this->pdf->grafiekKleuren['OIB'][$categorie];
					$portefeuilleWaarden[$portefeuille]['totalePercentage']+=$percentage*100;
					$geconsolideerdeWaarden[$categorie]['portefeuilles'][$portefeuilleNaam]+=($waardeEur/$geconsolideerdeWaarde[$categorie]*100);
          $DB->SQL("SELECT kleurcode FROM Portefeuilles WHERE portefeuille='".$portefeuille."'");
          $kleur=$DB->lookupRecord();
          $tmp=unserialize($kleur['kleurcode']);
          $geconsolideerdeWaarden[$categorie]['kleur'][$portefeuilleNaam]=array('R'=>array('value'=>$tmp[0]),'G'=>array('value'=>$tmp[1]),'B'=>array('value'=>$tmp[2]));
				
				}
			}
		}
		else
		{
		  #Alleen uitvoer voor consolidaties.
      return '';
			// haal totaalwaarde op om % te berekenen
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				"FROM TijdelijkeRapportage WHERE ".
				" rapportageDatum ='".$this->rapportageDatum."' AND ".
				" portefeuille = '".$this->portefeuille."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);
			$DB->SQL($query);
			$DB->Query();
			$totaalWaarde = $DB->nextRecord();
			$totaalWaarde = $totaalWaarde['totaal'];
			$portefeuilleWaarden[$this->portefeuille]['totaleWaarde']=$totaalWaarde;

			$query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving, TijdelijkeRapportage.beleggingscategorieVolgorde, ".
				" TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta, TijdelijkeRapportage.beleggingscategorie, ".
				" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS actuelePortefeuilleWaardeEuro ".
				" FROM TijdelijkeRapportage ".
				" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" GROUP BY TijdelijkeRapportage.beleggingscategorie".
				" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc";
			debugSpecial($query,__FILE__,__LINE__);

			$DB->SQL($query);
			$DB->Query();

			while($categorien = $DB->NextRecord())
			{
				if($categorien['beleggingscategorie']=='')
				{
					$categorien['beleggingscategorie']='GeenCategorie';
					$categorien['Omschrijving']='Geen categorie';
				}
				$categorieOmschrijving[$categorien['beleggingscategorie']]=$categorien['Omschrijving'];
				$categorieVolgorde[$categorien['beleggingscategorieVolgorde']]=$categorien['beleggingscategorie'];
				$portefeuilleWaarden[$this->portefeuille]['belCatWaarde'][$categorien['beleggingscategorie']]+=$categorien['actuelePortefeuilleWaardeEuro'];
				$percentage=($categorien['actuelePortefeuilleWaardeEuro']/$totaalWaarde);
				$portefeuilleWaarden[$this->portefeuille]['belCatPercentage'][$categorien['Omschrijving']]=$percentage*100;
				$portefeuilleWaarden[$this->portefeuille]['belCatKleuren'][$categorieOmschrijving[$categorien['beleggingscategorie']]]=$this->pdf->grafiekKleuren['OIB'][$categorien['beleggingscategorie']];
				$portefeuilleWaarden[$this->portefeuille]['totalePercentage']+=$percentage*100;

			}
		}
    
    $this->pdf->rapport_titel = "Asset allocatie per rekening";
    $this->pdf->addPage();
    $this->pdf->templateVars['GRAFIEK1Paginas']=$this->pdf->page;

	//	listarray($portefeuilleWaarden);
//listarray($geconsolideerdeWaarden);
		$n=0;
		foreach($portefeuilleWaarden as $portefeuille=>$waarden)
		{
      if($n==4)
			{
				$n==0;
				$this->pdf->addPage();
			}

			if($n==1 || $n==3)
				$x=160;
			else
				$x=20;

			if($n==0 || $n==1)
				$y=40;
			else
				$y=120;

			$this->pdf->setXY($x,$y);
			$this->printPie($waarden['belCatPercentage'], $waarden['belCatKleuren'], $this->getPortefeuilleNaam($portefeuille), 60, 50);
			$n++;
		}

		$this->pdf->rapport_titel = "Rekeningverdeling per asset class";
		$this->pdf->addPage();
		$this->pdf->templateVars['GRAFIEK2Paginas']=$this->pdf->page;


		$n=0;
		foreach($geconsolideerdeWaarden as $categorie=>$waarden)
		{
			if($n==4)
			{
				$n==0;
				$this->pdf->addPage();
			}
			if($n==1 || $n==3)
				$x=160;
		  else
				$x=20;

			if($n==0 || $n==1)
				$y=40;
			else
				$y=120;

			$this->pdf->setXY($x,$y);
			$this->printPie($geconsolideerdeWaarden[$categorie]['portefeuilles'],$geconsolideerdeWaarden[$categorie]['kleur'], $categorieOmschrijving[$categorie].' verdeling ', 60, 50);
			$n++;
		}


	}



	function printPie($pieData,$kleurdata,$title='',$width=100,$height=100)
	{

	  $col1=array(189,149,91); // rood
	  $col2=array(103,103,103); // groen
	  $col3=array(35,45,104); // oranje
	  $col4=array(210,211,212); // blauw
	  $col5=array(189,149,91); // geel
	  $col6=array(103,103,103); // paars
	  $col7=array(128,128,128); // grijs
	  $col8=array(128,64,64); // bruin
	  $col9=array(255,255,255); // wit
	  $col0=array(0,0,0); //zwart










	  $standaardKleuren=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
    // standaardkleuren vervangen voor eigen kleuren.
    $startX=$this->pdf->GetX();
		if(isset($kleurdata))
		{
		  $grafiekKleuren = array();
		  $a=0;
		  while (list($key, $value) = each($kleurdata))
			{
  			if ($value['R']['value'] == 0 && $value['G']['value'] == 0 && $value['B']['value'] == 0)
	  		  $grafiekKleuren[]=$standaardKleuren[$a];
		  	else
			    $grafiekKleuren[] = array($value['R']['value'],$value['G']['value'],$value['B']['value']);
		  //	$pieData[$key] = $value['percentage'];
		  	$a++;
			}
		}
		else
		  $grafiekKleuren = $standaardKleuren;

		while (list($key, $value) = each($pieData))
			if ($value < 0)
				$pieData[$key] = -1 * $value;

			//$this->pdf->SetXY(210, $this->pdf->headerStart);
			$y = $this->pdf->getY();
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->pdf->setXY($startX,$y-4);
      	$this->pdf->SetTextColor(0);
	
			$this->pdf->Cell(90,4,vertaalTekst($title, $this->pdf->rapport_taal),0,0,"C");
			$this->pdf->setXY($startX,$y);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	
      $this->pdf->setX($startX);
			$this->PieChart($width, $height, $pieData, '%l (%p)', $grafiekKleuren);
			$hoogte = ($this->pdf->getY() - $y) + 8;
			$this->pdf->setY($y);

			$this->pdf->SetLineWidth($this->pdf->lineWidth);
			$this->pdf->setX($startX);

		//	$this->pdf->Rect($startX,$this->pdf->getY(),$width,$hoogte);

	}

	function PieChart($w, $h, $data, $format, $colors=null)
  {

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 4;
      $hLegend = 2;
      $radius = min($w - $margin * 4 - $hLegend - $this->pdf->wLegend, $h - $margin * 2);
      $radius=min($w,$h);

      $radius = floor($radius / 2);
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;
      if($colors == null) {
          for($i = 0;$i < $this->pdf->NbVal; $i++) {
              $gray = $i * intval(255 / $this->pdf->NbVal);
              $colors[$i] = array($gray,$gray,$gray);
          }
      }

      //Sectors
      $this->pdf->SetLineWidth(0.2);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;
      $aantal=count($data);
      foreach($data as $val)
      {
        $angle = floor(($val * 360) / doubleval($this->pdf->sum));

        if ($angle != 0)
        {
          $angleEnd = $angleStart + $angle;

          $avgAngle=($angleStart+$angleEnd)/360*M_PI;
          $factor=1.5;

          if($i==($aantal-1))
            $angleEnd=360;

        //  echo " $angle $angleStart + $angleEnd = ".(($angleStart+$angleEnd)/2)." ".$this->pdf->legends[$i]." | cos:".cos($avgAngle)." | sin:".sin($avgAngle)."  <br>\n";
              $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
              $this->pdf->Sector($XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor), $radius, $angleStart, $angleEnd);
              $angleStart += $angle;
          }
          $i++;
      }
   //   if ($angleEnd != 360) {
    //      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    //  }

      //Legends
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      $x1 = $XPage + $w ;
      $x2 = $x1  + $hLegend + $margin;
      $y1 = $YDiag + ($radius) + $margin - $h;

      for($i=0; $i<$this->pdf->NbVal; $i++) {
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
          $y1+=$hLegend + 2;
      }

  }

    function SetLegends($data, $format)
  {
      $this->pdf->legends=array();
      $this->pdf->wLegend=0;

      $this->pdf->sum=array_sum($data);

      $this->pdf->NbVal=count($data);
      foreach($data as $l=>$val)
      {
          //$p=sprintf('%.1f',$val/$this->sum*100).'%';
          $p=sprintf('%.1f',$val).'%';
          $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
          $this->pdf->legends[]=$legend;
          $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->pdf->wLegend);
      }
  }

}
?>