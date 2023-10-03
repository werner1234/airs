<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"].'/html/indexBerekening.php');

class RapportOIV_L72
{
	function RapportOIV_L72($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

	}

	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();


		$portefeuilles=array();
		$query = "SELECT Fondsen.Portefeuille,Fondsen.Fonds,
              Portefeuilles.Startdatum,
              Portefeuilles.Einddatum,
              Fondsen.Omschrijving,
              TijdelijkeRapportage.actuelePortefeuilleWaardeEuro
              FROM TijdelijkeRapportage 
              JOIN Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
              INNER JOIN Portefeuilles ON Fondsen.Portefeuille = Portefeuilles.Portefeuille
              WHERE Fondsen.Huisfonds=1 AND rapportageDatum ='".$this->rapportageDatum."' AND 
              TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek']." ORDER BY Fondsen.Portefeuille";
		$DB->SQL($query);  
		$DB->Query();
		while($data = $DB->NextRecord())
 	  {
		  $portefeuilles[$data['Portefeuille']]=$data;
    }

   // $this->pdf->rapport_datumvanaf
   // ; 
    $kopBackup=$this->pdf->rapport_koptext;
    foreach($portefeuilles as $portefeuille=>$pdata)
    {
      $this->addFondsPagina($pdata);
    }
    $this->pdf->rapport_koptext=$kopBackup;
    
	}
  
  
  function formatGetal($waarde, $dec)
  {
    //if($waarde==0)
    //	return '';
    //else
    return number_format($waarde,$dec,",",".");
  }
  
  
  function kopregel($kop,$startKol=1,$resetY=false)
	{
    $starty=$this->pdf->getY();
    $pwidth=$this->pdf->w;
    $width=($pwidth-($this->pdf->marge*3))/2;
    
    if($startKol==1)
      $startX=$this->pdf->marge;
    elseif($startKol==2)
      $startX=$width+$this->pdf->marge*2;
		else
    {
      $startX = $this->pdf->marge;
      $width = $pwidth-$this->pdf->marge*2;
    }
    $this->pdf->setXY($startX,$starty);
		$this->pdf->setFillColor($this->pdf->rapport_kaderkleur[0],$this->pdf->rapport_kaderkleur[1],$this->pdf->rapport_kaderkleur[2]);
		$this->pdf->setTextColor(255,255,255);
		$this->pdf->MultiCell($width,5,$kop,$border=0,$align='L',$fill=1);
    if($resetY==true)
      $this->pdf->setY($starty);
	}
  
  function grijzeregel($data,$startKol,$resetY=false)
  {
    $starty=$this->pdf->getY();
    $pwidth=$this->pdf->w;
    $width=($pwidth-($this->pdf->marge*3))/2;
  
    if($startKol==1)
      $startX=$this->pdf->marge;
    elseif($startKol==2)
      $startX=$width+$this->pdf->marge*2;
    else
		{
      $startX=$this->pdf->marge;
      $width = $pwidth-$this->pdf->marge*2;
		}
    
    $this->pdf->setFillColor(224);
    $this->pdf->setTextColor(0);
    if($starty<>0)
    {
      $this->pdf->setXY($startX,$starty);
    }
    else
    {
      $this->pdf->setX($startX);
    }
    if(is_array($data))
		{
			if(count($data)==2)
      {
        if ($startKol == 1 || $startKol == 2)
        {
          $this->pdf->setWidths(array($width * .7, $width * .3));
        }
        else
        {
          $this->pdf->setWidths(array($width * .3, $width * .7));
        }
        $this->pdf->Row(array($data[0], $data[1]));
      }
      else
			{
				$aantal=count($data);
        $widths=array();
        $aligns=array();
        $fill=array();
				for($i=0;$i<=$aantal;$i++)
				{
          $widths[]=$width/$aantal;
          $aligns[]='C';
          $fill[]=1;
				}
        $this->pdf->setWidths($widths);
        $this->pdf->setAligns($aligns);
        $this->pdf->fillCell=$fill;
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->Row($data);
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			}
		}
		else
    {
      $this->pdf->MultiCell($width, 5, $data, $border = 0, $align = 'L', $fill = 1);
    }
    if($resetY==true)
      $this->pdf->setY($starty);
  }
  
  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=8, $verDiv=4,$periode='maand')
  {
    global $__appvar;
  
    $legendDatum=array_keys($data);
    $data=array_values($data);
    $bereikdata = $data;
    
    
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 5;
    $YDiag = $YPage;
    $hDiag = $h;//floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );
    
   
    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));
    
    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }
    
    if($color == null)
      $color=array(12,37,119);
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
    }
  
  
  
    $minVal = floor(($minVal-1)*0.9);
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
    
    if($maxVal>1000)
    	$deling=100;
    else
    	$deling=1;
    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv/$deling)*$deling;
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
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) );
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
    	if(round($i)<=round($top+$h)  && round($i)>=round($top)  )
      {
        $this->pdf->Line($XDiag, $i, $XPage + $w, $i, array('dash' => 1, 'color' => array(0, 0, 0)));
        if ($skipNull == true)
        {
          $skipNull = false;
        }
        else
        {
          $this->pdf->Text($XDiag - 7, $i, ($n * $stapgrootte) + 0);
        }
      }
      $n++;
      if($n >20)
        break;
    }
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    //$jaren=ceil(count($data)/12);
    for ($i=0; $i<count($data); $i++)
    {

     // if($i%$jaren==0)
     // {
        $this->pdf->TextWithRotation($XDiag + ($i) * $unit + $unit+1, $YDiag + $hDiag + 12, substr($legendDatum[$i],5,2).'-'.substr($legendDatum[$i],0,4), 90);
    //  }
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie);
      if($i<>0)
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      $yval = $yval2;
    }
    
    for ($i=0; $i<count($data)-1; $i++)
    {
      //if($i%$jaren==0)
     // {
        $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
        $this->pdf->line($XDiag + ($i + 1) * $unit, $YDiag + $h, $XDiag + ($i + 1) * $unit, $YDiag + $h + 1, $lineStyle);
     // }
    }

    

    $this->pdf->SetY($YPage+$h+12);
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }
	
	function addFondsPagina($pdata)
  {
 /*
    [Portefeuille] => 882473999
    [Fonds] => Fonds SCM
    [Startdatum] => 2015-08-30 00:00:00
    [Einddatum] => 2037-12-31 00:00:00
    [Omschrijving] => Fonds SCM
    [actuelePortefeuilleWaardeEuro] => 28863.58
  */
 
    $crm=new NAW();
    $crm->getByField('portefeuille',$pdata['Portefeuille']);
    $crmData=array('Oprichting'=>'leeg');
    foreach($crm->data['fields'] as $key=>$cdata)
    {
      if($cdata['value']<>'')
        $crmData[$key]=$cdata['value'];
      else
        $crmData[$key]='leeg';
    }
    
    $query="SELECT if(Clienten.Naam<>'',Clienten.Naam,Clienten.Client) as naam, Depotbanken.Omschrijving, Rekeningen.IBANnr
FROM Portefeuilles
JOIN Clienten ON Portefeuilles.Client=Clienten.Client
JOIN Depotbanken ON Portefeuilles.Depotbank=Depotbanken.Depotbank
JOIN Rekeningen ON Portefeuilles.Portefeuille=Rekeningen.Portefeuille AND Rekeningen.Memoriaal=1
WHERE Portefeuilles.Portefeuille='".$pdata['Portefeuille']."'
ORDER BY Rekeningen.id DESC limit 1";
    $db=new DB();
    $db->SQL($query);
    $details=$db->lookupRecord();
    
    
    $rapJaar=date('Y',$this->pdf->rapport_datum);
    $rapMaand=date('m',$this->pdf->rapport_datum);
    $participatieData=array();
    $participatieData['maandTerug']=date('Y-m-d',mktime(0,0,0,$rapMaand,0,$rapJaar));
    $tmp=bepaalHuisfondsKoers($pdata['Fonds'],$pdata['Portefeuille'],$participatieData['maandTerug']);
    $participatieData['aantalRapDatumMaandTerug']=$tmp['aantal'];
    $tmp=bepaalHuisfondsKoers($pdata['Fonds'],$pdata['Portefeuille'],$this->rapportageDatumVanaf);
    $participatieData['aantalRapStartDatum']=$tmp['aantal'];
    $tmp=bepaalHuisfondsKoers($pdata['Fonds'],$pdata['Portefeuille'],$this->rapportageDatum);

    $participatieData['aantalRapDatum']=$tmp['aantal'];
    $participatieData['koersRapDatum']=$tmp['Koers'];
    $participatieData['waardeRapDatum']=$tmp['aantal']*$tmp['Koers'];
    $query = "SELECT
SUM(if(Rekeningmutaties.Aantal>0,Rekeningmutaties.Aantal,0)) AS totaalAankopen,
SUM(if(Rekeningmutaties.Aantal<0,Rekeningmutaties.Aantal,0)) AS totaalVerkopen
FROM Rekeningmutaties
WHERE Rekeningmutaties.Fonds = '".mysql_real_escape_string($pdata['Fonds'])."' AND
      Rekeningmutaties.GrootboekRekening = 'FONDS' AND
      Rekeningmutaties.Boekdatum > '". $this->rapportageDatumVanaf."' AND
      Rekeningmutaties.Verwerkt = '1' AND
      Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
GROUP BY Rekeningmutaties.Fonds ";
    $db->SQL($query);
    $tmp=$db->lookupRecord();
    $participatieData['totaalAankopen']=$tmp['totaalAankopen'];
    $participatieData['totaalVerkopen']=$tmp['totaalVerkopen'];


    $index = new indexHerberekening();
    $portEinddatum=db2jul($pdata['Einddatum']);
    if($this->pdf->rapport_datum<$portEinddatum)
      $portEinddatum=$this->pdf->rapport_datum;
    $maanden=$index->getMaanden(db2jul($pdata['Startdatum']),$portEinddatum);
  
    
    $koersen=array();
    $rendementen=array();
    $laatsteKoers=0;
    $jarenRendementen=array();
    $jaarMaand='';
    foreach($maanden as $periode)
		{
      $tmp=bepaalHuisfondsKoers($pdata['Fonds'],$pdata['Portefeuille'],$periode['stop']);
      if($tmp['Koers']<>0)
      {
      	$jaar=substr($periode['stop'],0,4);
      	$jaarMaand=substr($periode['stop'],0,7);
        $koersen[$jaarMaand] = $tmp['Koers'];
        $rendementen[$jaarMaand] = ($tmp['Koers']-$laatsteKoers) / $laatsteKoers * 100;
  
        $jarenRendementen[$jaar]=((1+ $jarenRendementen[$jaar]/100)*(1+$rendementen[$jaarMaand]/100)-1)*100;
      }
			$laatsteKoers=$tmp['Koers'];
		}

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->rapport_titel="Intrinsieke waarde ".$pdata['Omschrijving']." per ".date('d-m-Y',db2jul($this->rapportageDatum));
    $this->pdf->addPage('P');

    $this->kopregel('Highlights',1,true);
    $this->kopregel('Uitstaande participaties',2);
    $this->pdf->fillCell=array(1,1,0,1,1);
    $this->pdf->setAligns(array('L','R','C','L','R'));
    $this->grijzeregel(array('',''),1,true);
    $this->grijzeregel(array('Aantal participaties per '.date('d-m-Y',db2jul($participatieData['maandTerug'])),$this->formatGetal($participatieData['aantalRapDatumMaandTerug'],4)),2);
    $this->grijzeregel(array('Intrinsieke waarde per participatie','€ '.$this->formatGetal($participatieData['koersRapDatum'],2)),1,true);
    $this->grijzeregel(array('Mutaties:',''),2);
    $this->grijzeregel(array('Netto waarde fonds:','€ '.$this->formatGetal($participatieData['waardeRapDatum'],0)),1,true);
    $this->grijzeregel(array('Uitgifte',$this->formatGetal($participatieData['totaalAankopen'],4)),2);
    $this->grijzeregel(array('',''),1,true);
    $this->grijzeregel(array('Inkoop:',$this->formatGetal($participatieData['totaalVerkopen'],4)),2);
    $this->grijzeregel(array('Rendement maand:',$this->formatGetal($rendementen[$jaarMaand] ,2).'%'),1,true);
    $this->grijzeregel(array('Aantal participaties per '.date('d-m-Y',db2jul($this->rapportageDatumVanaf)),$this->formatGetal($participatieData['aantalRapStartDatum'],4)),2);
    $this->grijzeregel(array('Rendement YTD::',$this->formatGetal($jarenRendementen[$rapJaar],2).'%'),1,true);
    $this->grijzeregel(array('',''),2);
    
    $this->pdf->ln();
  
    $this->kopregel('Opbouw netto vermogenswaarde',1,true);
    $this->kopregel('Opbouw voorzieningen kosten',2);
    $this->pdf->fillCell=array(1,1,0,1,1);
    $this->pdf->setAligns(array('L','R','C','L','R'));
    $this->grijzeregel(array('Beleggingsportefeuille',''),1,true);
    $this->grijzeregel(array('Bankkosten Insinger Gilissen',''),2);
    $this->grijzeregel(array('Voorschot op opname',''),1,true);
    $this->grijzeregel(array('Participantenadministratie',''),2);
    $this->grijzeregel(array('Stortingen',''),1,true);
    $this->grijzeregel(array('Kosten vermogensbeheer',''),2);
    $this->grijzeregel(array('Voorzieningen kosten',''),1,true);
    $this->grijzeregel(array('',''),2);
    $this->grijzeregel(array('Netto vernogenswaarde',''),1,true);
    $this->grijzeregel(array('Totaal kosten',''),2);
    $this->pdf->ln();
  
    $this->LineDiagram($this->pdf->w-$this->pdf->marge*2,50,$koersen);
  
    $this->pdf->ln();
    $this->kopregel('Waardeontwikkeling per participatie in EUR',0);
    $this->grijzeregel(array('Jaar','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Okt','Nov','Dec',''),0);
  
    $width=$this->pdf->w-$this->pdf->marge*2;
    $widthcell=$width/14;
    $enkelJaren=array_keys($jarenRendementen);
    $maandloop=array('01','02','03','04','05','06','07','08','09','10','11','12');
    foreach (array_reverse($enkelJaren) as $jaar)
		{
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->Cell($widthcell,$this->pdf->rowHeight,$jaar,$border=0,$ln=0,$align='R',$fill=1);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      foreach($maandloop as $maand)
			{
				if(isset($koersen[$jaar.'-'.$maand]))
					$txt=$this->formatGetal($koersen[$jaar.'-'.$maand],0);
				else
					$txt='';
        $this->pdf->Cell($widthcell,$this->pdf->rowHeight,$txt,$border=0,$ln=0,$align='R',$fill=0);
			}
      $this->pdf->ln();
		}
  //  listarray($koersen);
  //  listarray($rendementen);
  //  listarray($jarenRendementen);exit;
  
    $this->pdf->ln();
    $this->kopregel('Historische performance',0);
    $this->grijzeregel(array('Jaar','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Okt','Nov','Dec','YTD'),0);
    foreach (array_reverse($enkelJaren) as $jaar)
    {
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->Cell($widthcell,$this->pdf->rowHeight,$jaar,$border=0,$ln=0,$align='R',$fill=1);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      foreach($maandloop as $maand)
      {
        if(isset($rendementen[$jaar.'-'.$maand]))
          $txt=$this->formatGetal($rendementen[$jaar.'-'.$maand],2).'%';
        else
          $txt='';
        $this->pdf->Cell($widthcell,$this->pdf->rowHeight,$txt,$border=0,$ln=0,$align='R',$fill=0);
      }
      $txt=$this->formatGetal($jarenRendementen[$jaar],2).'%';
      $this->pdf->Cell($widthcell,$this->pdf->rowHeight,$txt,$border=0,$ln=0,$align='R',$fill=1);
      $this->pdf->ln();
    }
    //  lis
  
    $this->pdf->ln();
    $this->pdf->setAligns(array('L','L'));
    $this->kopregel('Fund overview',0);
    $this->grijzeregel(array('Oprichting fonds',$crmData['Oprichting']),0);
    $this->grijzeregel(array('Fondsstructuur',$crmData['ondernemingsvorm']),0);
    $this->grijzeregel(array('Bestuur',$details['naam']),0);
    $this->grijzeregel(array('Custodian',$details['Omschrijving']),0);
    $this->grijzeregel(array('IBAN / rekeningnummer',$details['IBANnr']),0);

  
    $this->pdf->ln();
    $this->pdf->MultiCell($this->pdf->w-$this->pdf->marge*2, 5, "Dit overzicht dient bekeken te worden samen met het performance overzicht uit de beleggingsrapportage.", $border = 0, $align = 'L', $fill = 0);
    
    unset($this->pdf->fillCell);
    
    
    
  }

}
?>
