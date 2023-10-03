<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.3 $

$Log: RapportOIB_L51.php,v $
Revision 1.3  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.2  2014/07/06 12:38:11  rvv
*** empty log message ***

Revision 1.1  2013/09/18 15:23:07  rvv
*** empty log message ***

Revision 1.17  2013/08/24 15:48:47  rvv
*** empty log message ***

Revision 1.16  2013/08/18 12:23:35  rvv
*** empty log message ***

Revision 1.15  2013/08/10 15:48:01  rvv
*** empty log message ***

Revision 1.14  2013/07/28 09:59:15  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");

class RapportOIB_L51
{

	function RapportOIB_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		$this->pdf->rapport_titel = "Participatieverloop";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->jaarGeleden=date("Y-m-d",mktime(0,0,0,date('m',$this->pdf->rapport_datum),date('d',$this->pdf->rapport_datum),date('Y',$this->pdf->rapport_datum)-1));

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
		global $__appvar;
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

		$DB = new DB();
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->kleuren=$allekleuren;
    
		$this->pdf->SetLineWidth($this->pdf->lineWidth);
    $this->pdf->underlinePercentage=0.8;

    $header=array('ultimo','Begin aantal','Begin Koers','Waarde','Stortingen / Onttrekkingen EUR','Waarde Part. ultimo','Aantal Stortingen / Onttrekkingen','Aantal ultimo','Waarde ultimo','Beleggingsresultaat','Rendement Mnd','Rendement Cum');
    
    $widths=array(20,22,22,22,25,25,25,25,25,25,22,22);
    $aligns=array('L','R','R','R','R','R','R','R','R','R','R','R');
    $headerBorders=array('U','U','U','U','U','U','U','U','U','U','U','U');
   
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    
    $this->pdf->setY(25);
    $this->pdf->setWidths($widths);
    $this->pdf->SetAligns($aligns);
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders=$headerBorders;
    $this->pdf->row($header);
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $index=new indexHerberekening();
    $maanden=$index->getMaanden($this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum);
    //listarray($maanden);exit;
    $cumulatief=0;
    $barGraph=array();
    $aanwezigeCategorieen=array();
    
    foreach($maanden as $periode)
    {
      //function berekenPortefeuilleWaarde($portefeuille, $rapportageDatum, $min1dag = false, $rapportageValuta = 'EUR',$rapportageBeginDatum='',$afronding=2,$bewaarders=false)
      $fondsenDatabegin=berekenPortefeuilleWaarde($this->portefeuille,$periode['start'],(substr($periode['start'], 5, 5) == '01-01')?true:false,$this->pdf->portefeuilledata['RapportageValuta'],$periode['start']);
      $fondsenDataEind=berekenPortefeuilleWaarde($this->portefeuille,$periode['stop'],(substr($periode['stop'], 5, 5) == '01-01')?true:false,$this->pdf->portefeuilledata['RapportageValuta'],$periode['start']);
      $fondsenData=array();
      foreach($fondsenDatabegin as $fondsRegel)
      {
        $fondsenData[$fondsRegel['fonds']]['start']=$fondsRegel;
      }
      foreach($fondsenDataEind as $fondsRegel)
      {
        $fondsenData[$fondsRegel['fonds']]['stop']=$fondsRegel;
      }
      $rendement=$index->BerekenMutaties2($periode['start'],$periode['stop'],$this->portefeuille,$this->pdf->portefeuilledata['RapportageValuta']);
      $cumulatief=((1+$cumulatief/100)*(1+$rendement['performance']/100)-1)*100;
      
      foreach($fondsenData as $fondsData)
      {
        $query="SELECT sum(Rekeningmutaties.Bedrag) as Bedrag, sum(Rekeningmutaties.Aantal) as Aantal FROM Rekeningmutaties JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND Rekeningmutaties.Boekdatum>'".$periode['start']."' AND Rekeningmutaties.Boekdatum<='".$periode['stop']."' AND
Rekeningmutaties.Fonds IN('".mysql_real_escape_string($fondsData['start']['fonds'])."','".mysql_real_escape_string($fondsData['stop']['fonds'])."') AND Rekeningmutaties.transactieType IN('A','V','D','L')";
        $DB->SQL($query);//logscherm($query);
        $DB->Query();
        $storting = $DB->LookupRecord();

        $query="SELECT Portefeuille FROM Fondsen WHERE Fonds='".mysql_real_escape_string($fondsData['stop']['fonds'])."'";
        $DB->SQL($query);
        $DB->Query();
        $doorkijk = $DB->LookupRecord();
        if($doorkijk['Portefeuille'] <> '')
        {
          $fondsenDataDoorkijk = berekenPortefeuilleWaarde($doorkijk['Portefeuille'], $periode['stop'], (substr($periode['stop'], 5, 5) == '01-01')?true:false, $this->pdf->portefeuilledata['RapportageValuta'], $periode['start']);
          
          $barGraph['Index'][$periode['stop']]['leeg'] = 0;
          $doorkijkWaarde=0;
          foreach ($fondsenDataDoorkijk as $regel)
          {
            $doorkijkWaarde+=$regel['actuelePortefeuilleWaardeEuro'] ;
          }
          foreach ($fondsenDataDoorkijk as $regel)
          {
            $barGraph['Index'][$periode['stop']][$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'] / $doorkijkWaarde * 100;
            $aanwezigeCategorieen[$regel['beleggingscategorie']]=1;
          }
        }
        //echo $query."<br><br>";
        $this->pdf->Row(array($periode['stop'],
                          $this->formatGetal($fondsData['start']['totaalAantal'],4),
                          $this->formatGetal($fondsData['start']['actueleFonds'],2),
                          $this->formatGetal($fondsData['start']['actuelePortefeuilleWaardeEuro'],2),
                          $this->formatGetal($storting['Bedrag']*-1,2),
                          $this->formatGetal($fondsData['stop']['actueleFonds'],2),
                          $this->formatGetal($storting['Aantal'],4),
                          $this->formatGetal($fondsData['stop']['totaalAantal'],4),
                          $this->formatGetal($fondsData['stop']['actuelePortefeuilleWaardeEuro'],2),
                          $this->formatGetal($fondsData['stop']['actuelePortefeuilleWaardeEuro']-$fondsData['start']['actuelePortefeuilleWaardeEuro']+$storting['Bedrag'],2),
                          $this->formatGetal($rendement['performance'],2)."%",
                          $this->formatGetal($cumulatief,2)."%"
                          )
                        );
      }
//listarray($fondsData);
    }
    
    
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->categorieKleuren=$allekleuren['OIB'];
    
    $q="SELECT
KeuzePerVermogensbeheerder.vermogensbeheerder,
KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.waarde,
Beleggingscategorien.Omschrijving,
Beleggingscategorien.Afdrukvolgorde
FROM
KeuzePerVermogensbeheerder
INNER JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde = Beleggingscategorien.Beleggingscategorie
WHERE
KeuzePerVermogensbeheerder.vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND
KeuzePerVermogensbeheerder.categorie='Beleggingscategorien'
ORDER BY Beleggingscategorien.Afdrukvolgorde"; //WHERE Beleggingscategorie IN('LIQ','ZAK','VAR','Liquiditeiten')
    $DB->SQL($q);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      //$this->categorieVolgorde[$data['Beleggingscategorie']]=$data['Beleggingscategorie'];
      if(isset($aanwezigeCategorieen[$data['waarde']]))
        $this->categorieVolgorde[$data['waarde']]=0;
      
      $this->categorieOmschrijving[$data['waarde']]=vertaalTekst($data['Omschrijving'],$this->pdf->rapport_taal);
    }
    
    for($i=count($barGraph['Index']); $i<12;$i++)
    {
      $barGraph['Index']['vulling'.$i]=array();
    }
    
    $zorgplicht = new Zorgplichtcontrole();
    $zpwaarde=$zorgplicht->zorgplichtMeting($this->pdf->portefeuilledata,$this->rapportageDatum);
    
    $tmp=array();
    foreach ($zpwaarde['conclusie'] as $index=>$regelData)
      $tmp[$regelData[0]]=$regelData;
    
    
    
    $query="SELECT
	Beleggingscategorien.Omschrijving as beleggingscategorieOmschrijving,
	KeuzePerVermogensbeheerder.waarde as beleggingscategorie,
	ZorgplichtPerBeleggingscategorie.Zorgplicht,
	ZorgplichtPerRisicoklasse.Norm
FROM
KeuzePerVermogensbeheerder
JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde=Beleggingscategorien.Beleggingscategorie
LEFT JOIN ZorgplichtPerBeleggingscategorie ON Beleggingscategorien.Beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie
AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT JOIN ZorgplichtPerRisicoklasse ON ZorgplichtPerBeleggingscategorie.Zorgplicht = ZorgplichtPerRisicoklasse.Zorgplicht
AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = ZorgplichtPerRisicoklasse.Vermogensbeheerder
WHERE
KeuzePerVermogensbeheerder.categorie='Beleggingscategorien' AND KeuzePerVermogensbeheerder.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
GROUP BY
	ZorgplichtPerBeleggingscategorie.Zorgplicht
ORDER BY
	KeuzePerVermogensbeheerder.Afdrukvolgorde ";
    $DB->SQL($query);
    $DB->Query();
    $tmp=array();
    while($data=$DB->nextRecord())
    {
      //	$tmp[$data['beleggingscategorie']]=$data['norm'];
      $tmp[$data['beleggingscategorie']]=$zpwaarde['categorien'][$data['Zorgplicht']]['Norm'];
    }
    
    $barGraph['Index']['Beleggingsplan']=$tmp;
    
    if (count($barGraph) > 0)
    {
      $this->pdf->SetXY($this->pdf->marge,102)		;//112
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->Cell(0, 5, vertaalTekst('Vermogensverdeling',$this->pdf->rapport_taal), 0, 1);
      $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
      $this->pdf->SetXY(15,180)		;//112
      $this->VBarDiagram(220, 70, $barGraph['Index']);
    }
    
    
  }
  
  function VBarDiagram($w, $h, $data)
  {
    global $__appvar;
    
    $grafiekPunt = array();
    
    $minVal=0;
    $maxVal=100;
    foreach ($data as $datum=>$waarden)
    {
      if(substr($datum,4,1)=='-')
        $legenda[$datum] = jul2form(db2jul($datum));
      elseif($datum=='Beleggingsplan')
        $legenda[$datum] = vertaalTekst('Beleggingsplan',$this->pdf->rapport_taal);
      else
        $legenda[$datum] = '';
      
      $n=0;
      
      foreach($waarden as $key=>$value)
      {
        if($value <0)
          $minTotal[$datum]+=$value;
        else
          $maxTotal[$datum]+=$value;
      }
      
      
      foreach ($this->categorieVolgorde as $categorie=>$waarde)
      {
        //foreach ($waarden as $categorie=>$waarde)
        //{
        if($categorie=='LIQ')
          $categorie='Liquiditeiten';
        $grafiek[$datum][$categorie]=$waarden[$categorie];
        $grafiekCategorie[$categorie][$datum]=$waarden[$categorie];
        $categorien[$categorie] = $n;
        $categorieId[$n]=$categorie ;
        
        if($maxTotal[$datum] > 100)
          $maxVal=max(array($maxVal,$maxTotal[$datum]));
        if($minTotal[$datum] < 0)
          $minVal=min(array($minVal,$minTotal[$datum]));
        
        if($waarden[$categorie] < 0)
        {
          unset($grafiek[$datum][$categorie]);
          $grafiekNegatief[$datum][$categorie]=$waarden[$categorie];
        }
        else
          $grafiekNegatief[$datum][$categorie]=0;
        
        
        if(!isset($colors[$categorie]))
          $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
        $n++;
      }
    }
    
    
    $numBars = count($legenda);
    $numBars=11;
    
    if($color == null)
    {
      $color=array(155,155,155);
    }
    
    
    if(round($maxVal,0) <= 100)
      $maxVal=100;
    elseif($maxVal < 112.5)
      $maxVal=112.5;
    elseif($maxVal < 125)
      $maxVal=125;
    elseif($maxVal < 150)
      $maxVal=150;
    
    if(round($minVal) >= 0)
      $minVal = 0;
    elseif($minVal > -12.5)
      $minVal=-12.5;
    elseif($minVal > -25)
      $minVal=-25;
    elseif($minVal > -50)
      $minVal=-50;
    
    //echo "$maxVal $minVal ";exit;
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + $margin * 1 ;
    $bGrafiek = ($w - $margin * 1)-45 ; // - legenda
    
    $n=0;
    foreach (array_reverse($this->categorieVolgorde) as $categorie=>$waarde)//
    {
      if(isset($this->categorieOmschrijving[$categorie]) && is_array($grafiekCategorie[$categorie]))
      {
        $this->pdf->Rect($XstartGrafiek+$w+3 , $YstartGrafiek-$hGrafiek+$n*7+2, 2, 2, 'DF',null,$colors[$categorie]);
        $this->pdf->SetXY($XstartGrafiek+$w+6 ,$YstartGrafiek-$hGrafiek+$n*7+1.5 );
        $this->pdf->MultiCell(45, 4,$this->categorieOmschrijving[$categorie],0,'L');
        $n++;
      }
    }
    
    if($minVal < 0)
    {
      $unit = $hGrafiek / (-1 * $minVal + $maxVal) * -1;
      $nulYpos =  $unit * (-1 * $minVal);
    }
    else
    {
      $unit = $hGrafiek / $maxVal * -1;
      $nulYpos =0;
    }
    
    
    $horDiv = 5;
    $horInterval = $hGrafiek / $horDiv;
    $bereik = $hGrafiek/$unit;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    
    $stapgrootte = ceil(abs($bereik)/$horDiv);
    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);
    
    $nulpunt = $YstartGrafiek + $nulYpos;
    $n=0;
    
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
      $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1)." %",0,0,'R');
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)." %",0,0,'R');
      }
      $n++;
      if($n >20)
        break;
    }
    
    
    
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;
    
    $vBar = ($bGrafiek / ($this->pdf->NbVal));
    $bGrafiek = $vBar * ($this->pdf->NbVal);
    $eBaton = ($vBar * 50 / 100);
    
    
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $i=0;
    $this->pdf->SetTextColor(0,0,0);
    foreach ($grafiek as $datum=>$data)
    {
      //echo $datum.' '. count($data)."<br>\n";
      // listarray($data);
      $aantal=count($data);
      $n=1;
      foreach($data as $categorie=>$val)
      {
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
        //Bar
        $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
        $hval = ($val * $unit);
        
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'F',null,$colors[$categorie]);
        $this->pdf->Line($xval,$yval,$xval,$yval+$hval);
        $this->pdf->Line($xval+$lval,$yval,$xval+$lval,$yval+$hval);
        if($aantal==$n)
          $this->pdf->Line($xval,$yval+$hval,$xval+$lval,$yval+$hval);
        
        $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
        
        if(abs($hval) > 3)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
        }
        
        if($legendaPrinted[$datum] != 1)
          $this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);
        
        if($grafiekPunt[$categorie][$datum])
        {
          $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
          if($lastX)
            $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
          $lastX = $xval+.5*$eBaton;
          $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
        }
        $legendaPrinted[$datum] = 1;
        $n++;
      }
      $i++;
    }
    
    $i=0;
    $YstartGrafiekLast=array();
    foreach ($grafiekNegatief as $datum=>$data)
    {
      foreach($data as $categorie=>$val)
      {
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
        //Bar
        $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
        $hval = ($val * $unit);
        
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
        $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
        
        if(abs($hval) > 3)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
        }
        
        
        if($grafiekPunt[$categorie][$datum])
        {
          $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
          if($lastX)
            $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
          $lastX = $xval+.5*$eBaton;
          $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
        }
      }
      $i++;
    }
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }

}
?>