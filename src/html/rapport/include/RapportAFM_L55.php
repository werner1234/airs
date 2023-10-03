<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/04/07 15:21:44 $
File Versie					: $Revision: 1.8 $

$Log: RapportAFM_L55.php,v $
Revision 1.8  2018/04/07 15:21:44  rvv
*** empty log message ***

Revision 1.7  2018/02/21 17:15:09  rvv
*** empty log message ***

Revision 1.6  2018/02/18 14:58:36  rvv
*** empty log message ***

Revision 1.5  2016/12/17 18:57:35  rvv
*** empty log message ***

Revision 1.4  2016/03/02 16:59:05  rvv
*** empty log message ***

Revision 1.3  2014/08/09 15:06:36  rvv
*** empty log message ***

Revision 1.2  2014/07/02 15:56:02  rvv
*** empty log message ***

Revision 1.1  2014/06/08 15:27:58  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportAFM_L55
{
	function RapportAFM_L55($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
		$this->pdf = &$pdf;
    
    $this->pdf->rapport_type = "AFM";
   	
  	$this->pdf->rapport_datum = db2jul($rapportageDatum);
		if($this->pdf->rapport_OIB_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_AFM_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling in AFM categorieën";// per ".date('d-m-Y',$this->pdf->rapport_datum);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
		$this->tweedePerformanceStart=$this->rapportageDatumVanaf;
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


	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}

	function printTotaal($title, $totaalA, $procent, $grandtotaal)
	{
	  return $totaalA;
		

		
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

		if(($this->pdf->GetY() + 12) >= $this->pdf->pagebreak) {
			$this->pdf->AddPage();
			$this->pdf->ln();
		}
		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->pdf->SetX($this->pdf->marge);
		$y = $this->pdf->getY();


		if($this->pdf->rapport_layout == 14)
		{
		  $this->pdf->MultiCell($this->pdf->widthB[0]+$this->pdf->widthB[1],4, $title, 0, "L");
		}
		else
		{
		  $this->pdf->MultiCell($this->pdf->widthB[0],4, $title, 0, "L");
		}

	  $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
		$this->pdf->SetY($y);
	}


	function tweedeStart()
	{
		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
		if(db2jul($this->pdf->PortefeuilleStartdatum) == db2jul($this->rapportageDatumVanaf))
		{
			$this->tweedePerformanceStart = substr($this->pdf->PortefeuilleStartdatum,0,10);
		}
		else
		{
			if(db2jul($this->pdf->PortefeuilleStartdatum) <  db2jul(($RapStartJaar-1).'-'.substr($this->rapportageDatum,5,5)))
			{
				$dagMaand=substr($this->rapportageDatum,5,5);
				if($dagMaand=='12-31')
					$this->tweedePerformanceStart=date('Y-m-d',db2jul(($RapStartJaar-1).'-'.substr($this->rapportageDatum,5,5))+3600*24);
				else
					$this->tweedePerformanceStart=($RapStartJaar-1).'-'.substr($this->rapportageDatum,5,5);
			}
			elseif(db2jul($this->pdf->PortefeuilleStartdatum) >  db2jul("$RapStartJaar-01-01"))
			{
				$this->tweedePerformanceStart=substr($this->pdf->PortefeuilleStartdatum,0,10);
			}
			else
			{
				$this->tweedePerformanceStart = "$RapStartJaar-01-01";
			}
		}

	}


	function addAFMGrafiek()
	{
		global $__appvar;
		include_once('../../indexBerekening.php');
		$index=new indexHerberekening();
		$this->tweedeStart();
		$perioden=$index->getMaanden(db2jul($this->tweedePerformanceStart),db2jul($this->rapportageDatum));
		$db=new DB();
		foreach($perioden as $periode)
		{
			$query = "SELECT * FROM TijdelijkeRapportage 
                 WHERE add_date > (now() - interval 10 minute) AND portefeuille='" . $this->portefeuille . "' AND rapportageDatum='" . $periode['stop'] . "'
                 " . $__appvar['TijdelijkeRapportageMaakUniek'];
			if ($db->QRecords($query) == 0)
			{
				if (substr($periode['stop'], 5, 5) == '01-01')
				{
					$startJaar = true;
				}
				else
				{
					$startJaar = false;
				}
				vulTijdelijkeTabel(berekenPortefeuilleWaarde( $this->portefeuille, $periode['stop'], $startJaar), $this->portefeuille, $periode['stop']);
			}
			$afm = AFMstd($this->portefeuille, $periode['stop']);
			$grafiekData['totaal']['datum'][]=$periode['stop'];// date("M y",db2jul($periode['stop']));
			$grafiekData['totaal']['portefeuille'][]= $afm['std'];
		}
		$grafiekData['totaal']['titel']='AFM Standaarddeviatie';
		//$grafiekData['afm']['titel']='AFM Standaarddeviatie portefeuille';

		//$grafiekData['totaal']['legenda']=array('Portefeuille','Benchmark','AFM');


	//	$afmKleur=array(30,130,50);

		$this->pdf->setXY(20,120);
		$this->LineDiagram(120, 55, $grafiekData['totaal'],array(array(140,178,209)),0,0,6,5,1);//50



	}


	function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
	{
		global $__appvar;


		$legendDatum= $data['datum'];
		$legendaItems= $data['legenda'];
		$titel=$data['titel'];
		$data = $data['portefeuille'];
		$bereikdata =   $data;

		$XPage = $this->pdf->GetX();
		$YPage = $this->pdf->GetY();
		$margin = 0;
		$YDiag = $YPage + $margin;
		$hDiag = floor($h - $margin * 1);
		$XDiag = $XPage + $margin * 1 ;
		$lDiag = floor($w - $w/12 );


		$this->pdf->setXY($XPage,$YPage-3);
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->Cell($w,0,$titel,0,0,'L');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));


		if(is_array($color[0]))
		{
			$color1= $color[1];
			$color = $color[0];
		}

		$this->pdf->Rect($XPage, $YPage, $w, $h,'FD','',$this->pdf->grafiekAchtergrondKleur);

		if($color == null)
			$color=array(140,178,209);
		$this->pdf->SetLineWidth(0.2);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetFillColor($color[0],$color[1],$color[2]);

		if ($maxVal == 0)
		{
			$maxVal = ceil(max($bereikdata));
			if ($maxVal < 0)
				$maxVal = 1;
		}
		if ($minVal == 0)
		{
			$minVal = floor(min($bereikdata));
		//	if ($minVal > 0)
		//		$minVal =-1;
		}

		$minVal = floor(($minVal-1) * 1.1);
		$maxVal = ceil(($maxVal+1) * 1.1);
		$legendYstep = ($maxVal - $minVal) / $horDiv;
		$verInterval = ($lDiag / $verDiv);
		$horInterval = ($hDiag / $horDiv);
		$waardeCorrectie = $hDiag / ($maxVal - $minVal);
		$aantalData=count($data);
		$unit = $lDiag / $aantalData;

		if($jaar && count($data)<12)
			$unit = $lDiag / 12;

		for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
		{
			$xpos = $XDiag + $verInterval * $i;
		}

		$this->pdf->SetFont($this->pdf->rapport_font, '', 6);
		$this->pdf->SetTextColor(0,0,0);
		$this->pdf->SetDrawColor(0,0,0);

		$stapgrootte = round(abs($maxVal - $minVal)/$horDiv);
		$unith = $hDiag / (-1 * $minVal + $maxVal);

		$top = $YPage;
		$bodem = $YDiag+$hDiag;
		$absUnit =abs($unith);

		$nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
		$n=0;
		for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
		{
			$skipNull = true;
			$this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
			$this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
			$n++;
			if($n >20)
				break;
		}

		$n=0;
		for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
		{
			if($i < $bodem)
			{
				$this->pdf->Line($XDiag, $i, $XPage + $w, $i, array('dash' => 1, 'color' => array(0, 0, 0)));
				if ($skipNull == true)
				{
					$skipNull = false;
				}
				else
				{
					$this->pdf->Text($XDiag - 7, $i, ($n * $stapgrootte) + 0 . " %");
				}
			}
			$n++;
			if($n >20)
				break;
		}

		//datum onder grafiek
		/*
    $datumStart = db2jul($legendDatum[0]);
    $datumStart = vertaalTekst($__appvar["Maanden"][date("n",$datumStart)],$pdf->rapport_taal).' '.date("Y",$datumStart);
    $datumStop  =  db2jul($legendDatum[count($legendDatum)-1])+86400;
    $datumStop  = vertaalTekst($__appvar["Maanden"][date("n",$datumStop)],$pdf->rapport_taal).' '.date("Y",$datumStop);
    $ypos = $YDiag + $hDiag + $margin*2;
    $xpos = $XDiag;
    $this->pdf->Text($xpos, $ypos,$datumStart);
    $xpos = $XPage+$w - $this->pdf->GetStringWidth($datumStop);
    $this->pdf->Text($xpos, $ypos,$datumStop);
*/
		$yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
		$lineStyle = array('width' => 1.0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
		//listarray($data);
		// $color=array(200,0,0);

		$printLabel=array();
		for ($i=0; $i<count($data); $i++)
		{
			$extrax=($unit*0.1*-1);
			if($i <> 0)
				$extrax1=($unit*0.1*-1);

			$maand=date("n",db2jul($legendDatum[$i]));
			if($aantalData <= 13 || $maand==3 || $maand==6 || $maand==9 || $maand==12)
			{
				$this->pdf->TextWithRotation($XDiag+($i)*$unit-3+$unit,$YDiag+$hDiag+8,vertaalTekst($__appvar["Maanden"][$maand],$this->pdf->rapport_taal) ,25);
				$printLabel[$i]=1;
			}
			$yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
			if($i<> 0)
			{
				$this->pdf->line($XDiag + $i * $unit + $extrax1, $yval, $XDiag + ($i + 1) * $unit + $extrax, $yval2, $lineStyle);
			}
				$this->pdf->Rect($XDiag + ($i + 1) * $unit - 0.5 + $extrax, $yval2 - 0.5, 1, 1, 'F', '', $color);
				$this->pdf->Circle($XDiag + ($i + 1) * $unit + $extrax, $yval2, 1, 0, 360, 'F', '', $color);

			$yval = $yval2;
		}


		for ($i=0; $i<count($data); $i++)
		{
			$extrax=($unit*0.1*-1);
			if($i <> 0)
				$extrax1=($unit*0.1*-1);
			$yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
			$this->pdf->SetFont($this->pdf->rapport_font, '', 9);
			if($data[$i] <> 0 && $printLabel[$i])
				$this->pdf->Text($XDiag+($i+1)*$unit-1+$extrax,$yval2-2.5,$this->formatGetal($data[$i],1));
			$this->pdf->SetFont($this->pdf->rapport_font, '', 6);

		}

		if(is_array($data1))
		{
			// listarray($data1);
			$yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
			$lineStyle = array('width' => 1.0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color1);
			for ($i=0; $i<count($data1); $i++)
			{
				$yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
				$this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
				$this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color1);

				$this->pdf->SetFont($this->pdf->rapport_font, '', 9);
				if($data1[$i] <> 0 && $printLabel[$i])
					$this->pdf->Text($XDiag+($i+1)*$unit-1+$extrax,$yval2-2.5,$this->formatGetal($data1[$i],1));
				$this->pdf->SetFont($this->pdf->rapport_font, '', 6);

				$yval = $yval2;
			}
		}
		$this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.1, ));
		$this->pdf->SetFillColor(0,0,0);
	}

	function LineDiagram_old($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4)
	{
		global $__appvar;

		$legendDatum= $data['datum'];
		$legendaItems= $data['legenda'];
		$titel=$data['titel'];
		$data = $data['portefeuille'];

		$bereikdata =   $data;

		$XPage = $this->pdf->GetX();
		$YPage = $this->pdf->GetY();
		$margin = 2;
		$YDiag = $YPage + $margin;
		$hDiag = floor($h - $margin * 1);
		$XDiag = $XPage + $margin * 1 ;
		$lDiag = floor($w - $margin * 1 );

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->Cell($w,0,$titel,0,0,'L');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

		$this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

		if(is_array($color[0]))
		{
			$color2= $color[2];
			$color1= $color[1];
			$color = $color[0];
		}

		if($color == null)
			$color=array(155,155,155);
		$this->pdf->SetLineWidth(0.2);


		$this->pdf->SetFillColor($color[0],$color[1],$color[2]);

		if ($maxVal == 0)
		{
			$maxVal = ceil(max($bereikdata));
		}
		if ($minVal == 0)
		{
			$minVal = floor(min($bereikdata));
		}

		$minVal = floor(($minVal-1) * 1.1);
		if($minVal > 0)
			$minVal=0;
		$maxVal = ceil(($maxVal+1) * 1.1);
		$legendYstep = ($maxVal - $minVal) / $horDiv;
		$verInterval = ($lDiag / $verDiv);
		$horInterval = ($hDiag / $horDiv);
		$waardeCorrectie = $hDiag / ($maxVal - $minVal);
		$unit = $lDiag / count($data);

		for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
			$xpos = $XDiag + $verInterval * $i;

		$this->pdf->SetFont($this->pdf->rapport_font, '', 8);
		$this->pdf->SetTextColor(0,0,0);
		$this->pdf->SetDrawColor(0,0,0);

		$stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
		$unith = $hDiag / (-1 * $minVal + $maxVal);

		$top = $YPage;
		$bodem = $YDiag+$hDiag;
		$absUnit =abs($unith);

		$nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
		$n=0;
		if($titel=='Sharpe-ratio')
			$yAs='';
		else
			$yAs=' %';
		for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
		{
			$skipNull = true;
			$this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
			//$this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) .$yAs);

			$this->pdf->setXY($XDiag-11, $i-2);
			$this->pdf->cell(10,4,0-($n*$stapgrootte) .$yAs,0,1,'R');
			$n++;
			if($n >20)
				break;
		}

		$n=0;

		for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
		{
			$this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
			if($skipNull == true)
				$skipNull = false;
			else
			{
				$this->pdf->setXY($XDiag-11, $i-2);
				$this->pdf->cell(10,4,(($n*$stapgrootte) + 0) . $yAs,0,1,'R');

				// $this->pdf->Text($XDiag - 7, $i, ($n * $stapgrootte) + 0 . $yAs);
			}
			$n++;
			if($n >20)
				break;
		}
		$yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
		$lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
		$jaren=ceil(count($data)/12);
		for ($i=0; $i<count($data); $i++)
		{
			if($i%$jaren==0)
				$this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
			$yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;

			if ($i>0)
			{
				$this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
			}

			$yval = $yval2;
		}

		if(is_array($data1))
		{
			$yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
			$lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color1);

			for ($i=0; $i<count($data1); $i++)
			{
				$yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;

				if ($i>0)
				{
					$this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
				}
				$yval = $yval2;
			}
		}

		if(is_array($data2))
		{
			$yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
			$lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color2);

			for ($i=0; $i<count($data2); $i++)
			{
				$yval2 = $YDiag + (($maxVal-$data2[$i]) * $waardeCorrectie) ;

				if ($i>0)
				{
					$this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
				}
				$yval = $yval2;
			}
		}

		$this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.2,'cap' => 'butt'));
		$step=5;
		foreach ($legendaItems as $index=>$item)
		{
			if($index==0)
				$kleur=$color;
			elseif($index==1)
				$kleur=$color1;
			else
				$kleur=$color2;

			$this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
			$this->pdf->Rect($XPage+$step, $YPage+$h+10, 3, 3, 'DF','',$kleur);
			$this->pdf->SetXY($XPage+3+$step,$YPage+$h+10);
			$this->pdf->Cell(0,3,$item);
			$step+=($w/3);
		}
		$this->pdf->SetDrawColor(0,0,0);
		$this->pdf->SetFillColor(0,0,0);
	}

	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

		// voor data
		$this->pdf->widthB = array(80,35,25);
		$this->pdf->alignB = array('L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = $this->pdf->widthB;
		$this->pdf->alignA = $this->pdf->alignB;

		$this->pdf->AddPage();
    $this->pdf->templateVars['AFMPaginas']=$this->pdf->page;
    
    $ybegin=$this->pdf->getY();
    $this->pdf->SetY($ybegin+7);

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
		$totaalWaarde = $totaalWaarde[totaal];

		$actueleWaardePortefeuille = 0;

		$query = "SELECT TijdelijkeRapportage.afmCategorieOmschrijving as Omschrijving, ".
			" TijdelijkeRapportage.valutaOmschrijving AS ValutaOmschrijving, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta, TijdelijkeRapportage.afmCategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
			" FROM TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.afmCategorie".
			" ORDER BY TijdelijkeRapportage.afmCategorie asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
    $n=0;
		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);


			// print totaal op hele categorie.
			if($lastCategorie <> $categorien['Omschrijving'] && !empty($lastCategorie) )
			{

				// voor Pie
				$this->pdf->pieData[vertaalTekst($lastCategorie,$this->pdf->rapport_taal)] = $percentageVanTotaal;
				$grafiekCategorien[$lastCat]=$percentageVanTotaal; //toevoeging voor kleuren.
      
			}


			$lastCategorie = $categorien[Omschrijving];

			$percentageVanTotaal = $categorien[subtotaalactueel] / ($totaalWaarde/100);

			// print valutaomschrijving appart ivm met apparte fontkleur
			$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
			$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
			$this->pdf->setX($this->pdf->marge);
			$this->pdf->setX($this->pdf->marge);

			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			// print categorie footers

$n=fillLine($this->pdf,$n);
				$this->pdf->row(array($categorien['Omschrijving'],
											$this->formatGetal($categorien['subtotaalactueel'],2),
											$this->formatGetal($percentageVanTotaal,1).""));
			



			// totaal op categorie tellen
			$totaalinvaluta += $categorien['subtotaalactueelvaluta'];
			$totaalactueel += $categorien['subtotaalactueel'];
      $lastCat       = $categorien['afmCategorie'];
			$lastCategorie = $categorien['Omschrijving'];
      $lastsubtotaalactueel =$categorien['subtotaalactueel'];
		}

		// totaal voor de laatste categorie


		
		$actueleWaardePortefeuille += $this->printTotaal("", $totaalactueel, $percentageVanTotaal);
		// voor Pie
		$this->pdf->pieData[vertaalTekst($lastCategorie,$this->pdf->rapport_taal)] = $percentageVanTotaal;
    $percentageVanTotaal = $lastsubtotaalactueel / ($totaalWaarde/100);
		$grafiekCategorien[$lastCat]=$percentageVanTotaal; //toevoeging voor kleuren.

		// print grandtotaal
		$this->pdf->ln();


		$this->pdf->setX($this->pdf->marge);

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor[r],$this->pdf->rapport_totaal_omschr_fontcolor[g],$this->pdf->rapport_totaal_omschr_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);







		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}
    unset($this->pdf->fillCell);
    $this->pdf->Row(array(vertaalTekst('Totale actuele waarde portefeuille',$this->pdf->rapport_taal),$this->formatGetalKoers($actueleWaardePortefeuille,$this->pdf->rapport_OIB_decimaal),$this->formatGetal(100,1)." %"));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);
		$kleuren = $kleuren['AFM'];
		//listarray($kleuren);
		$q = "SELECT afmCategorie, omschrijving FROM afmCategorien";
		$DB->SQL($q);
		$DB->Query();
		$kleurdata = array();

		$dbBeleggingscategorien = array();
		$dbBeleggingscategorien['Opgelopen Rente']='Opgelopen Rente'; //Voorkomen dat Opgelopen rente leeg blijft wanneer vermogensbeheerder kleuren niet geset.

		while($categorie = $DB->NextRecord())
			$dbBeleggingscategorien[$categorie['afmCategorie']] = $categorie['omschrijving'];


    foreach ($grafiekCategorien as $cat=>$percentage)
    {
      $groep=$dbBeleggingscategorien[$cat];
      $groep=	vertaalTekst($groep,$this->pdf->rapport_taal);
      $kleurdata[$groep]['kleur'] = $kleuren[$cat];
      $kleurdata[$groep]['percentage'] = $percentage;
    }

    $afm=AFMstd($this->portefeuille,$this->rapportageDatum,$this->pdf->debug);

    $this->pdf->ln(8);
    $this->pdf->SetWidths(array(80,35,25));
    $this->pdf->Row(array(vertaalTekst('AFM-standaarddeviatie',$this->pdf->rapport_taal)." per ".date('d-m-Y',$this->pdf->rapport_datum),'',$this->formatGetal($afm['std'],2)." %"));

    if($this->pdf->debug)
      listarray($afm);

		//		if($this->pdf->rapport_layout == 1 || $this->pdf->rapport_layout == 12)
		//		  $this->pdf->pieData[strtoupper(vertaalTekst($lastCategorie,$this->pdf->rapport_taal))] = $percentageVanTotaal;
		//		else z
	//	$this->pdf->printPie($this->pdf->pieData,$kleurdata);
    $this->pdf->SetXY(160,$ybegin+5);
    $this->BarDiagram(100, 60, $this->pdf->pieData, '',$kleurdata,0,10);//%l (%p)
    $this->addAFMGrafiek();

	}
  
  
    function BarDiagram($w, $h, $data, $format, $colorArray=null, $maxVal=0, $nbDiv=4)
  {

      $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
      $this->pdf->SetLegends($data,$format);


      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YDiag = $YPage;
      $hDiag = floor($h);
      $XDiag = $XPage;
      $lDiag = floor($w);
      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0) {
          $maxVal = max($data)*1.1;
      }
      if ($minVal == 0) {
          $minVal = min($data)*1.1;
      }
      $minVal=0;
      $offset=$minVal;
      $valIndRepere = ceil(($maxVal-$minVal) / $nbDiv);
      $bandBreedte = $valIndRepere * $nbDiv;
      $lRepere = floor($lDiag / $nbDiv);
      $unit = $lDiag / $bandBreedte;
      $hDiag=$this->pdf->NbVal*5;
      $hBar = floor($hDiag / ($this->pdf->NbVal + 1));
      $hDiag = $hBar * ($this->pdf->NbVal + 1);
      $eBaton = floor($hBar * 80 / 100);
      $legendaStep=$unit;
      if($bandBreedte/$legendaStep > $nbDiv)
        $legendaStep=$legendaStep*5;
      if($bandBreedte/$legendaStep > $nbDiv)
        $legendaStep=$legendaStep*2;
      if($bandBreedte/$legendaStep > $nbDiv)
        $legendaStep=$legendaStep/2*5;
      $valIndRepere=round($valIndRepere/$unit/5)*5;


      $this->pdf->SetLineWidth(0.2);
      $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

      $nullijn=$XDiag - ($offset * $unit);

      $i=0;
      $nbDiv=10;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 5);
      if(round($legendaStep,5) <> 0.0)
      {
        for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,1),0,0,'C');
        }

        for($x=$nullijn;$x<($XDiag+$lDiag); $x=$x+$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,1),0,0,'C');
        }
      }
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $i=0;

      //$this->pdf->SetXY(0, $YDiag);
      //$this->pdf->Cell($nullijn, $hval-4, 'Onderwogen',0,0,'R');
      //$this->pdf->SetXY($nullijn, $YDiag);
      //$this->pdf->Cell(60, $hval-4, 'Overwogen',0,0,'L');
      $this->pdf->SetXY($XDiag, $YDiag);
      $this->pdf->Cell($lDiag, $hval-4, 'AFM verdeling',0,0,'C');
      foreach($data as $key=>$val)
      {
          $this->pdf->SetFillColor($colorArray[$key]['kleur']['R']['value'],$colorArray[$key]['kleur']['G']['value'],$colorArray[$key]['kleur']['B']['value']);
          //Bar
          $xval = $nullijn;
          $lval = ($val * $unit);
          $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
          $hval = $eBaton;
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
          //Legend
          $this->pdf->SetXY(0, $yval);
          $this->pdf->Cell(75 , $hval, $this->pdf->legends[$i],0,0,'R');
          $i++;
      }

      //Scales
      $minPos=($minVal * $unit);
      $maxPos=($maxVal * $unit);

      $unit=($maxPos-$minPos)/$nbDiv;
     // echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";

      for ($i = $nullijn+$XDiag; $i <= $maxVal; $i=$i+$unit)
      {
          $xpos = $XDiag +  $i;
          $this->pdf->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
          $val = $i * $valIndRepere;
          $xpos = $XDiag +  $i - $this->pdf->GetStringWidth($val) / 2;
          $ypos = $YDiag + $hDiag - $margin;
          $this->pdf->Text($xpos, $ypos, $val);
      }
  }
}
?>