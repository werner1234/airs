<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/10/06 17:20:57 $
File Versie					: $Revision: 1.7 $

$Log: RapportWaardeprognose_L76.php,v $
Revision 1.7  2018/10/06 17:20:57  rvv
*** empty log message ***

Revision 1.6  2018/09/27 08:00:22  rvv
*** empty log message ***

Revision 1.5  2018/09/27 06:53:41  rvv
*** empty log message ***

Revision 1.4  2018/09/26 15:53:28  rvv
*** empty log message ***

Revision 1.3  2018/09/22 17:12:17  rvv
*** empty log message ***

Revision 1.2  2018/09/20 08:30:41  rvv
*** empty log message ***

Revision 1.1  2018/09/19 17:35:08  rvv
*** empty log message ***

Revision 1.7  2018/02/21 17:12:31  rvv
*** empty log message ***

Revision 1.6  2018/02/14 16:52:34  rvv
*** empty log message ***

Revision 1.5  2018/02/10 18:08:00  rvv
*** empty log message ***

Revision 1.4  2018/02/08 08:01:02  rvv
*** empty log message ***

Revision 1.3  2018/02/07 17:15:36  rvv
*** empty log message ***

Revision 1.2  2018/02/03 18:52:57  rvv
*** empty log message ***

Revision 1.1  2018/01/14 12:39:01  rvv
*** empty log message ***

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

class RapportWaardeprognose_L76
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function RapportWaardeprognose_L76( $selectData )
	{
		global $__appvar,$USR;
		$this->selectData = $selectData;
		$this->pdf = new PDFRapport('L','mm');
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 270;
		$this->pdf->__appvar = $__appvar;
		$this->pdf->rapport_type = "waardeprognose";

		$this->pdf->excelData 	= array();
		$this->pdf->tmdatum = $this->selectData['datumTm'];
		$this->pdf->rapport_datum = $this->selectData['datumTm'];// db2jul($rapportageDatum);
		$this->pdf->selectData=$selectData;
		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);

		if($selectData['PortefeuilleVan']<>'')
    {
      loadLayoutSettings($this->pdf, $selectData['PortefeuilleVan']);
      $this->vermogensbeheerder=$selectData['VermogensbeheerderVan'];
    }
    else
    {
			$db=new DB();
			$query="SELECT Portefeuilles.Portefeuille,Portefeuilles.Vermogensbeheerder FROM Portefeuilles INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".mysql_real_escape_string($USR)."' JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker WHERE 1 AND (Portefeuilles.beperktToegankelijk = '0' OR Gebruikers.beperkingOpheffen = '1' ) AND Portefeuilles.consolidatie<2 limit 1";
			$db->SQL($query);
			$portefeuille=$db->lookupRecord();
      $this->vermogensbeheerder=$portefeuille['Vermogensbeheerder'];
			loadLayoutSettings($this->pdf,$portefeuille['Portefeuille']);
    }

		$this->pdf->portefeuilledata=array();
		$this->pdf->rapport_portefeuille 	='';
		$this->pdf->rapport_portefeuilleVoorzet 	= '';
		$this->pdf->rapport_portefeuilleFormat 	= '';
		$this->pdf->rapport_client 				= '';
		$this->pdf->rapport_clientVermogensbeheerder 				= '';
		$this->pdf->rapport_clientVermogensbeheerderReal= '';
		$this->pdf->rapport_risicoklasse 	= '';
		$this->pdf->rapport_risicoprofiel 	= '';
		$this->pdf->rapport_depotbank 		= '';
		$this->pdf->rapport_depotbankOmschrijving 		= '';
		$this->pdf->rapport_naam1 				= '';
		$this->pdf->rapport_naam2 				= '';
		$this->pdf->rapport_accountmanager		= '';
		$this->pdf->underlinePercentage=0.8;

		$this->orderby = " Portefeuilles.ClientVermogensbeheerder ";

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{

    $this->waardeprognose_naam=$this->pdf->selectData['waardeprognose_naam'];
	  $this->waardeprognose_risicoklasse=$this->pdf->selectData['waardeprognose_risicoklasse'];
	  $this->waardeprognose_bedrag = $this->selectData['waardeprognose_bedrag'];
    if($this->waardeprognose_naam <> '' || $this->waardeprognose_bedrag <> '')
 	  {
			$this->pdf->addPage();
			$pdata=array('Vermogensbeheerder'=>$this->vermogensbeheerder);
			$this->langeTermijngrafiek($pdata);
	  }
		else
		{
			//
			$selectie = new portefeuilleSelectie($this->selectData, $this->orderby);
			$records = $selectie->getRecords();
			$portefeuilles = $selectie->getSelectie(false);

			if ($records <= 0)
			{
				echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
				$this->progressbar->hide();
				exit;
			}

			if ($this->progressbar)
			{
				$this->progressbar->moveStep(0);
				$pro_step = 0;
				$pro_multiplier = 100 / $records ;
			}

			$rapportageDatum['b'] = jul2sql($this->selectData['datumTm']);

			foreach ($portefeuilles as $pdata)
			{
				if ($this->progressbar)
				{
					$pro_step += $pro_multiplier;
					$this->progressbar->moveStep($pro_step);
					logScherm("Portefeuille: " . $pdata['Portefeuille'] . " (Vullen tijdelijke rapportage)");
				}
				$startjaar=(substr($rapportageDatum['b'],5,5)=='01-01')?true:false;
				$waardeEur=0;
				$fondswaarden = berekenPortefeuilleWaarde($pdata['Portefeuille'] , $rapportageDatum['b'], $startjaar, 'EUR', $rapportageDatum['b']);
				foreach($fondswaarden as $item)
				{
				//	listarray($fondswaarden);
					$waardeEur += $item['actuelePortefeuilleWaardeEuro'];
				}

				$this->waardeprognose_naam = $pdata['Naam1'];

				if($this->pdf->selectData['waardeprognose_risicoklasse']<>'')
					$this->waardeprognose_risicoklasse=$this->pdf->selectData['waardeprognose_risicoklasse'];
				else
				  $this->waardeprognose_risicoklasse = $pdata['Risicoklasse'];

				$this->waardeprognose_bedrag = $waardeEur;
				$this->pdf->addPage();
				$this->langeTermijngrafiek($pdata);
			}
		}
		if($this->progressbar)
			$this->progressbar->hide();
	}



	function langeTermijngrafiek($pdata)
	{
		global $__appvar;
		$db=new DB();

		$this->pdf->rapport_jaar=date('Y',$this->pdf->rapport_datum );
		$startDag=date('d-m',$this->pdf->rapport_datum );

		if($this->selectData['waardeprognose_eindjaar'] > $this->pdf->rapport_jaar)
			$doelJaar=$this->selectData['waardeprognose_eindjaar'];
		else
			$doelJaar=$this->pdf->rapport_jaar+10;

		$query="SELECT Risicoklassen.verwachtRendement,Risicoklassen.verwachtKostenPercentage  FROM Risicoklassen WHERE Risicoklassen.Risicoklasse ='".$this->waardeprognose_risicoklasse."' AND Vermogensbeheerder='".$pdata['Vermogensbeheerder']."'";
		$db->SQL($query);
		$db->query();
		$data=$db->nextRecord();
		if($data['verwachtRendement'] <> 0 )
			$rendement=$data['verwachtRendement'];
		else
			$rendement=0;

		$kostenVelden=array('Kosten dienstverlening'=>array('waardeprognose_kosten_beheer'=>'Beheervergoeding','waardeprognose_kosten_bank'=>'Bank- en toezichtkosten'),
												'Kosten financiële instrumenten'=>array('waardeprognose_kosten_indirect'=>'Kosten beleggingsfondsen/ETF\'s','waardeprognose_kosten_transactie'=>'Transctiekosten'));
		//));

		$kostenPercentage=0;
		$kostenBedrag=0;
		$kostenGesplitst=array();

		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$beginwaarde = $this->waardeprognose_bedrag;
		$this->pdf->setWidths(array(80,30));
		$this->pdf->setAligns(array('L','R'));
		$this->pdf->ln();
		$this->pdf->row(array('Uitgangspunten portefeuille'));
		$this->pdf->ln();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		if($this->waardeprognose_naam<>'')
		  $this->pdf->row(array('Naam',$this->waardeprognose_naam));
		$this->pdf->row(array('Profiel',$this->waardeprognose_risicoklasse));
		$this->pdf->row(array('Verwacht netto-rendement (o.b.v. historische gegevens)',$this->formatGetal($rendement,2).' %'));
		$this->pdf->row(array('Beheerd vermogen',$this->formatGetal($beginwaarde,0)));
		$this->pdf->row(array('Doeljaar',$doelJaar));


		$this->pdf->setWidths(array(80+30));
		$this->pdf->setAligns(array('L','R','R'));
		$this->pdf->ln();
    $this->pdf->ln();
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('Overzicht van de te verwachten kosten voor het aankomende jaar'));

		$this->pdf->ln();
		$this->pdf->setWidths(array(50,30,30));
		$this->pdf->row(array('','%','Bedrag'));

		foreach($kostenVelden as $kopje=>$velden)
		{
			$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
			$this->pdf->row(array($kopje));
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			foreach($velden as $veld=>$omschrijving)
			{
		  	if ($this->selectData[$veld] <> 0)
		  	{
					$bedrag=$this->selectData[$veld] * $beginwaarde * 0.01;
		  		$this->pdf->row(array($omschrijving, $this->formatGetal($this->selectData[$veld], 2) . ' %', $this->formatGetal($bedrag, 0)));
		  		$kostenPercentage += $this->selectData[$veld];
					$kostenBedrag += $bedrag;
		  		$kostenGesplitst[$omschrijving] = $this->selectData[$veld];
		  	}
		  }
			$this->pdf->ln();
		}

		if($kostenPercentage == 0 && $data['verwachtKostenPercentage'] <> 0 )
		{
			$kostenPercentage = $data['verwachtKostenPercentage'];
			$kostenGesplitst['Gehanteerd kostenpercentage']=$data['verwachtKostenPercentage'];
	//		$this->pdf->row(array('Kostenpercentage',$data['verwachtKostenPercentage']));
		}

		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders=array('',array('TS','UU'),array('TS','UU'));

		$this->pdf->row(array('Totale kosten op jaarbasis', $this->formatGetal($kostenPercentage, 2) . ' %', $this->formatGetal($kostenBedrag, 0)));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->excelData[]=array('doelJaar','rendement','beginwaarde','kostenPercentage');
		$this->pdf->excelData[]=array($doelJaar,round($rendement,2),round($beginwaarde,2),round($kostenPercentage,4));
		$this->pdf->excelData[]=array('jaar','waardeNaKosten','cumulatieveKosten','waardeZonderKosten');

		$kosten=0;
		$grafiekWaarden=array();
		$n=0;
		for($i=$this->pdf->rapport_jaar; $i<=$doelJaar; $i++)
		{
			$jaren=$i-$this->pdf->rapport_jaar;
			$nieuweWaarde=$beginwaarde*pow(1+($rendement/100),$jaren);
			if($n>0)
			  $kosten+=$nieuweWaarde*($kostenPercentage/100);

			$grafiekWaarden['waardeNaKosten'][$n]=$nieuweWaarde;
			$grafiekWaarden['cumulatieveKosten'][$n]=$kosten;
			$grafiekWaarden['waardeZonderKosten'][$n]=$nieuweWaarde+$kosten;
			$grafiekWaarden['datum'][$n]=$i;
			$grafiekWaarden['datumVolledig'][$n]=$startDag.'-'.$i;
			$this->pdf->excelData[]=array($startDag.'-'.$i,round($nieuweWaarde,2),round($kosten,2),round($nieuweWaarde+$kosten,2));
			$n++;
		}
		$aantalJaren=$n;
		$grafiekWaarden['legenda']=array('Waardeontwikkeling zonder kosten','Waardeontwikkeling na kosten','Cumulatieve kosten');

		$grafiekWaarden['titel']="Voorbeeldillustratie waardeontwikkeling portefeuille";
		$waardeZonderKostenKleur=array(100,100,200);
		$waardeNaKostenKleur=array(100,200,100);
		$cumulatieveKostenKleur=array(200,100,100);

		if($this->pdf->getY()+70>$this->pdf->pagebreak)
			$this->pdf->addPage();

		$this->pdf->setXY(150,38);
		$this->LineDiagram(120, 55, $grafiekWaarden,array($waardeZonderKostenKleur,$waardeNaKostenKleur,$cumulatieveKostenKleur),0,0,4,4,false);//50
/*
		$this->pdf->setWidths(array(22,30,25,30));
		$this->pdf->setAligns(array('L','R','R','R'));
    $this->pdf->setXY($this->pdf->marge,100);

		$this->pdf->row(array('Datum','Waarde incl Kosten','Kosten cum.','Waarde excl. Kosten'));

		for($n=0;$n<$aantalJaren;$n++)
			$this->pdf->row(array($grafiekWaarden['datumVolledig'][$n],
												    $this->formatGetal($grafiekWaarden['waardeNaKosten'][$n],0),
												    $this->formatGetal($grafiekWaarden['cumulatieveKosten'][$n],0),
												    $this->formatGetal($grafiekWaarden['waardeZonderKosten'][$n],0))
			               );
*/

		//foreach($this->pdf->excelData as $row)
		//	$this->pdf->row($row);

//		listarray($grafiekWaarden);
//		echo "$doelJaar $rendement $beginwaarde";
		//	listarray( $this->vkmWaarde);
		//	exit;
	}


	function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$vanafBegin=false)
	{
		global $__appvar;

		$legendDatum= $data['datum'];
		$legendaItems= $data['legenda'];
		$titel=$data['titel'];
		$data1 = $data['waardeNaKosten'];
		$data2 = $data['cumulatieveKosten'];
		$data = $data['waardeZonderKosten'];


		if(count($data1)>0)
			$bereikdata = array_merge($data,$data1);
		else
			$bereikdata =   $data;

		if(count($data2)>0)
			$bereikdata = array_merge($bereikdata,$data2);

		$XPage = $this->pdf->GetX();
		$YPage = $this->pdf->GetY()+6;
		$margin = 0;
		$YDiag = $YPage + $margin;
		$hDiag = floor($h - $margin * 1);
		$XDiag = $XPage + $margin * 1 ;
		$lDiag = floor($w - $margin * 1 );

		//	$this->pdf->setY($Ypage-3);
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->Cell($w,0,$titel,0,0,'L');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

		$this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

		if(is_array($color[0]))
		{
			$color1= $color[1];
			$color2= $color[2];
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


		//	echo $maxVal;exit;

		$minVal = floor(($minVal-1) * 1.1);
		if($minVal > 0)
			$minVal=0;
		$maxVal = ceil(($maxVal+1) * 1.1);

		//	$maxVal=round($maxVal,floor(log10($maxVal))*-1+1);

		$significance=floor(log10($maxVal));
		$significance=pow(10,$significance);
		$maxVal=	ceil($maxVal/$significance)*$significance;

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
		for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
		{
			$skipNull = true;
			$this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
			$this->pdf->setXY($XDiag-7, $i);
			$this->pdf->Cell(7 , 4 , "€ ". 0-round($n*$stapgrootte/100)*100 , 0, 1, "R");

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
				$this->pdf->setXY($XDiag-7, $i);
				$this->pdf->Cell(7 , 4 , "€ " .(round($n * $stapgrootte/100)*100 + 0) , 0, 1, "R");

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

			if ($i>0 || $vanafBegin==true)
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

				if ($i>0 || $vanafBegin==true)
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

				if ($i>0 || $vanafBegin==true)
				{
					$this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
				}
				$yval = $yval2;
			}
		}

		$this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.2,'cap' => 'butt'));
		$step=5;
		$aantal=count($legendaItems);
		foreach ($legendaItems as $index=>$item)
		{
			if($index==0)
				$kleur=$color;
			elseif($index==1)
				$kleur=$color1;
			else
				$kleur=$color2;
			$this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
			$this->pdf->Rect($XPage+5 , $bodem+$step+10, 3, 3, 'DF','',$kleur);
			$this->pdf->SetXY($XPage+3+5,$bodem+$step+10);
			$this->pdf->Cell(0,3,$item);

			$step+=6;
		}
		$this->pdf->SetDrawColor(0,0,0);
		$this->pdf->SetFillColor(0,0,0);
	}


}
?>