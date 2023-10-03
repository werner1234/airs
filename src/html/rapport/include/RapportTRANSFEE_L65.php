<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/11 17:30:27 $
File Versie					: $Revision: 1.6 $

$Log: RapportTRANSFEE_L65.php,v $
Revision 1.6  2020/07/11 17:30:27  rvv
*** empty log message ***

Revision 1.5  2020/07/01 14:06:48  rvv
*** empty log message ***

Revision 1.4  2020/06/27 16:23:17  rvv
*** empty log message ***

Revision 1.3  2020/06/20 13:50:28  rvv
*** empty log message ***

Revision 1.2  2020/06/17 15:38:53  rvv
*** empty log message ***

Revision 1.1  2020/06/13 15:10:54  rvv
*** empty log message ***

Revision 1.10  2019/04/03 15:52:48  rvv
*** empty log message ***

Revision 1.9  2019/03/31 12:19:56  rvv
*** empty log message ***

Revision 1.8  2019/02/09 18:40:17  rvv
*** empty log message ***

Revision 1.7  2019/01/09 15:52:19  rvv
*** empty log message ***

Revision 1.6  2018/12/05 16:36:17  rvv
*** empty log message ***

Revision 1.5  2018/12/01 19:51:30  rvv
*** empty log message ***

Revision 1.4  2018/11/17 17:34:53  rvv
*** empty log message ***

Revision 1.3  2018/11/16 16:41:32  rvv
*** empty log message ***

Revision 1.2  2018/10/21 09:42:37  rvv
*** empty log message ***

Revision 1.1  2018/10/20 18:05:20  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportVOLK_L65.php");


class RapportTRANSFEE_L65
{
	function RapportTRANSFEE_L65($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "TRANSFEE";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->pdf->rapport_titel = vertaalTekst("Portefeuilledetails*",$this->pdf->rapport_taal);
	}
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde, $dec, ",", ".");
  }
  
  function getGrafiekdata( $portefeuille='')
  {
    global $__appvar;
    if($portefeuille=='')
    {
      $portefeuilleFilter='';
    }
    else
    {
      $portefeuilleFilter="AND Fondsen.Portefeuille='$portefeuille'";

    }
   
    $DB=new DB();
    $portefeuilles=array();
    $query = "SELECT Fondsen.Portefeuille,
              Portefeuilles.Startdatum,
              Portefeuilles.Einddatum,
              Fondsen.Omschrijving,
              TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,
              FondsenBuitenBeheerfee.layoutNr
              FROM TijdelijkeRapportage
JOIN FondsenBuitenBeheerfee ON TijdelijkeRapportage.fonds = FondsenBuitenBeheerfee.Fonds
JOIN Fondsen ON FondsenBuitenBeheerfee.Fonds = Fondsen.Fonds
JOIN Portefeuilles ON Fondsen.Portefeuille = Portefeuilles.Portefeuille
              WHERE FondsenBuitenBeheerfee.Huisfonds = 1 AND rapportageDatum ='".$this->rapportageDatum."' AND
              TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' $portefeuilleFilter "
      .$__appvar['TijdelijkeRapportageMaakUniek']." ORDER BY Fondsen.Portefeuille";
    $DB->SQL($query);
    $DB->Query();
    while($data = $DB->NextRecord())
    {
      if($data['Portefeuille']<>'')
        $portefeuilles[$data['Portefeuille']]=$data;
    }
  

    $paginas=array();
    foreach($portefeuilles as $portefeuille=>$pdata)
    {
      $rapportageDatum['a'] = date("Y-m-d",$this->pdf->rapport_datumvanaf);
      $rapportageDatum['b'] = date("Y-m-d",$this->pdf->rapport_datum);
    
      if($this->pdf->rapport_datumvanaf < db2jul($pdata['Startdatum']))
        $rapportageDatum['a'] = $pdata['Startdatum'];
    
      if($this->pdf->rapport_datum > db2jul($pdata['Einddatum']))
      {
        echo "<b>Fout: Portefeille '$portefeuille' heeft een einddatum  (".date("d-m-Y",db2jul($pdata['Einddatum'])).")</b>";
        exit;
      }
      if(db2jul($rapportageDatum['a']) > db2jul($rapportageDatum['b']))
      {
        echo "<b>Fout: $portefeuille Van datum kan niet groter zijn dan  T/m datum! </b>";
        exit;
      }
    
    
      $fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum['b'],0,$pdata['RapportageValuta'],$rapportageDatum['a']);
      vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$rapportageDatum['b']);
      $portefeuilleWaarde=0;
      $totaleVerdeling=array();
      $categorieVerdeling=array();
      $categorieVolgorde=array();
      $categorieOmschrijving=array();
      $categorieTotaal=array();

      foreach($fondswaarden['b'] as $fonds)
      {
        if($fonds['type']=='rente')
        {
          $fonds['fondsOmschrijving']=vertaalTekst('Opgelopen rente',$this->pdf->rapport_taal);
          $fonds['beleggingscategorie']='Opgelopen rente';
          $fonds['beleggingscategorieOmschrijving']=vertaalTekst('Opgelopen rente',$this->pdf->rapport_taal);
          $fonds['beleggingscategorieVolgorde']=100;
        }
        elseif($fonds['type']=='rekening')
        {
          $fonds['fondsOmschrijving']=vertaalTekst('Cash positie',$this->pdf->rapport_taal);
        }
      
        $portefeuilleWaarde += $fonds['actuelePortefeuilleWaardeEuro'];
        $totaleVerdeling[$fonds['fondsOmschrijving']] += $fonds['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling[$fonds['beleggingscategorie']][$fonds['fondsOmschrijving']] += $fonds['actuelePortefeuilleWaardeEuro'];
        $categorieTotaal[$fonds['beleggingscategorie']] += $fonds['actuelePortefeuilleWaardeEuro'];
        $categorieVolgorde[$fonds['beleggingscategorieVolgorde']] = $fonds['beleggingscategorie'];
        $categorieOmschrijving[$fonds['beleggingscategorie']] = $fonds['beleggingscategorieOmschrijving'];
      
      }
    
      arsort($totaleVerdeling);
    
      $aandeelVanPortefeuille=$pdata['actuelePortefeuilleWaardeEuro']/$portefeuilleWaarde;
      if($aandeelVanPortefeuille <>0)
      {
        $kop=array();
        $rows=array();
        $totaal=array();
        $grafiekVerdelingen=array();
        $grafiekData=array();
        if($pdata['layoutNr']==1)
        {
        
          $kop = array(array(vertaalTekst('De 20 grootste posities',$this->pdf->rapport_taal)), array('', vertaalTekst('Aandeel fonds',$this->pdf->rapport_taal), vertaalTekst('Waarde',$this->pdf->rapport_taal)));
          $n = 0;
          $line = 0;
          foreach ($totaleVerdeling as $fonds => $waarde)
          {
            if ($n > 20)
            {
              $fonds = vertaalTekst('Overig',$this->pdf->rapport_taal);
            }
            $aandeelFonds = $waarde / $portefeuilleWaarde;
            $waardeFonds = $waarde * $aandeelVanPortefeuille;
            $rows[$line][0] = $fonds;
            $rows[$line][1] += $aandeelFonds;
            $rows[$line][2] += $waardeFonds;
            $totaal['aandeelFonds'] += $aandeelFonds;
            $totaal['waardeFonds'] += $waardeFonds;
            if ($n<21)
              $line++;
            $n++;
          }
          $grafiekVerdelingen=array('OIS'=>'beleggingssector','OIR'=>'regio');
        
        }
        if($pdata['layoutNr']==2)
        {
          ksort($categorieVolgorde);
          $kop = array(array(vertaalTekst('De grootste posities per categorie',$this->pdf->rapport_taal)), array('', vertaalTekst('Aandeel fonds',$this->pdf->rapport_taal), vertaalTekst('Waarde',$this->pdf->rapport_taal)));
        
          $line = 0;
          foreach ($categorieVolgorde as $volgorde => $categorie)
          {
            $n=0;
            if($line>0)
              $line++;
            $rows[$line]=array(vertaalTekst($categorieOmschrijving[$categorie],$this->pdf->rapport_taal),$categorieTotaal[$categorie]/$portefeuilleWaarde,$categorieTotaal[$categorie]*$aandeelVanPortefeuille,'categorieTotaal');
            $line++;
            arsort($categorieVerdeling[$categorie]);
            //listarray($categorieVerdeling[$categorie]);
            foreach($categorieVerdeling[$categorie] as $fonds=>$waarde)
            {
              if ($n>1)
              {
                $fonds = vertaalTekst('Overig',$this->pdf->rapport_taal);
              }
            
              $aandeelFonds = $waarde / $portefeuilleWaarde;
              $waardeFonds = $waarde * $aandeelVanPortefeuille;
              $rows[$line][0] = " - ".$fonds;
              $rows[$line][1] += $aandeelFonds;
              $rows[$line][2] += $waardeFonds;
            
              //echo "$line | $n | $categorie | $fonds | ".round($aandeelFonds,4)." | ".round($rows[$line][1],4)." <br>\n";
            
              $totaal['aandeelFonds'] += $aandeelFonds;
              $totaal['waardeFonds'] += $waardeFonds;
              if ($n<2)
                $line++;
            
              $n++;
            }
          }
          $grafiekVerdelingen=array('OIS'=>'beleggingssector','OIR'=>'regio','OIV'=>'valuta','Rating'=>'rating');
          // listarray($rows);
        }
      
      
        $geenVertaing=array('beleggingssector'=>'Geen sector');
        foreach($grafiekVerdelingen as $kleurShort=> $verdeling)
        {
          if($verdeling=='rating')
          {
            $query = "SELECT 	if(TijdelijkeRapportage.type='rekening','Liquiditeiten',if(ISNULL(Fondsen.rating),'NR',REPLACE(REPLACE(Fondsen.rating,'+',''),'-',''))) AS verdeling,
            if(TijdelijkeRapportage.type='rekening','Liquiditeiten',if(ISNULL(Rating.omschrijving),'Geen rating',REPLACE(REPLACE(Rating.omschrijving,'+',''),'-',''))) AS Omschrijving,
             sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS waarde
          FROM TijdelijkeRapportage LEFT JOIN Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds LEFT JOIN Rating ON Fondsen.rating = Rating.rating
          WHERE TijdelijkeRapportage.portefeuille='" . $portefeuille . "' AND rapportageDatum='" . $rapportageDatum['b'] . "' " . $__appvar['TijdelijkeRapportageMaakUniek'] . "
          GROUP BY verdeling
          ORDER BY Rating.Afdrukvolgorde, verdeling";
          }
          elseif($verdeling=='beleggingssector' || $verdeling=='regio' )
          {
            $query = "SELECT  if(TijdelijkeRapportage.type='rekening','Liquiditeiten',$verdeling) AS verdeling,
             if(TijdelijkeRapportage.type='rekening','Liquiditeiten',".$verdeling."Omschrijving) AS Omschrijving, sum(actuelePortefeuilleWaardeEuro) as waarde
          FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.portefeuille='$portefeuille' AND rapportageDatum='".$rapportageDatum['b']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
          GROUP BY verdeling ORDER BY ".$verdeling."volgorde, $verdeling"; //logscherm( $query);
          }
          else
            $query = "SELECT $verdeling as verdeling, ".$verdeling." as Omschrijving, sum(actuelePortefeuilleWaardeEuro) as waarde
          FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.portefeuille='$portefeuille' AND rapportageDatum='".$rapportageDatum['b']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
          GROUP BY $verdeling ORDER BY ".$verdeling."volgorde, $verdeling";
        
          $DB->SQL($query);
          $DB->Query();
          while($data = $DB->NextRecord())
          {
          
            if($data['verdeling']=='')
            {
              if(isset($geenVertaing[$verdeling]))
                $data['verdeling']=$geenVertaing[$verdeling];
              else
                $data['verdeling'] = 'Geen ' . $verdeling;
              if ($data['Omschrijving'] == '')
              {
                $data['Omschrijving'] = $data['verdeling'];
              }
            }
          
            $kleur=$this->allekleuren[$kleurShort][$data['verdeling']];
            // echo $verdeling." $kleurShort ".$data['verdeling'];listarray($kleur);
            $grafiekData[$verdeling]['data'][$data['verdeling']]['waardeEur']+=$data['waarde'];
            $grafiekData[$verdeling]['data'][$data['verdeling']]['Omschrijving']=$data['Omschrijving'];
            $grafiekData[$verdeling]['pieData'][$data['Omschrijving']]+=$data['waarde']/$portefeuilleWaarde;
            $grafiekData[$verdeling]['kleurData'][$data['Omschrijving']]=$kleur;
            $grafiekData[$verdeling]['kleurData'][$data['Omschrijving']]['percentage']+=$data['waarde']/$portefeuilleWaarde*100;
          
          }
        
        
        
        }
      
      
        $paginas[]=array('omschrijving'=>$pdata['Omschrijving'],'layout'=>$pdata['layoutNr'],'kop'=>$kop,'body'=>$rows,'totaal'=>$totaal,'grafieken'=>$grafiekData);
      }
    }
    return $paginas;
  }
  
  function getKleuren()
  {
    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $this->portefeuilledata = $DB->nextRecord();
  
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $this->allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->allekleuren['Rating']['Liquiditeiten']=$this->allekleuren['OIS']['Liquiditeiten'];
  }
  
	function writeRapport()
	{
		global $__appvar;
    $this->pdf->huis3=true;

    $this->getKleuren();
    $kopBackup=$this->pdf->rapport_koptext;
    
    $paginas=$this->getGrafiekdata();

		foreach($paginas as $paginanr=>$blokData)
    {
      $this->pdf->addPage();
      if($paginanr==0)
      {
        $this->pdf->templateVars[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->page;
        $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->rapport_titel;
      }
      $xwidth = 297 - $this->pdf->marge * 2;
      $xStart = $xwidth / 6 - 90 / 2;
      $n=0;
      $this->toonBlok($xStart + $n * 94, 50, $blokData);
  
      $n=0;
      $xyPos=array(array(110,55),array(110,120),array(210,120),array(210,55));
      $vertalingen=array('beleggingssector'=>'sector');
      foreach($blokData['grafieken'] as $grafiekSoort=>$grafiekData)
      {
        if(isset($vertalingen[$grafiekSoort]))
          $titel=vertaalTekst('Spreiding per '.$vertalingen[$grafiekSoort], $this->pdf->rapport_taal);
        else
          $titel=vertaalTekst('Spreiding per '.$grafiekSoort, $this->pdf->rapport_taal);
        
        $this->pdf->setXY($xyPos[$n][0],$xyPos[$n][1]);
//$this->pdf->setXY(65,40);
        $this->pdf->wLegend = 0;
        $this->printPie($grafiekData['pieData'], $grafiekData['kleurData'], $titel, 35, 35);
        $this->pdf->wLegend = 0;
       // $this->pdf->setXY(120, 37);
//$this->pdf->setXY(175,40);
       // $this->printPie($data['beleggingscategorieEind']['pieData'], $data['beleggingscategorieEind']['kleurData'], vertaalTekst('Categorieverdeling', $this->pdf->rapport_taal) . ' ' . date("d-m-Y", db2jul($rapportageDatum)), 60, 50);
       // $this->pdf->wLegend = 0;
        $n++;
      }
    }
  
    $this->pdf->rapport_koptext=$kopBackup;
		if(isset($this->pdf->huis3))
      unset($this->pdf->huis3);
    
	}
	
	function addKop($x,$blockData)
  {
    $this->pdf->SetXY($x+$this->pdf->marge,34);
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize+2);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
  
    $this->pdf->MultiCell(297-$this->pdf->getX()*2,4,$blockData['omschrijving'],0,'C');
    $this->pdf->SetTextColor(0);
  }
	
	function toonBlok($x,$y,$blockData)
	{
    $this->addKop($x,$blockData);

	  $this->pdf->SetXY($this->pdf->marge,$y);
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);

    $this->pdf->setAligns(array($x,'L','R','R'));
    foreach($blockData['kop'] as $i=>$row)
    {
    	if($i==0)
        $this->pdf->setWidths(array($x,55+15,20));
    	else
      {
        $this->pdf->setWidths(array($x, 60, 15, 20));
      }
    	$this->pdf->row(array('', $row[0], $row[1], $row[2]));
    }
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->setWidths(array($x,60,15,20));
    foreach($blockData['body'] as $row)
    {
      if(isset($row[3]) && $row[3]=='categorieTotaal')
        $this->pdf->SetFont($this->pdf->rapport_font, 'BI', $this->pdf->rapport_fontsize);
      else
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  
      $fondsOmschrijving=$row[0];
      $width=$this->pdf->GetStringWidth($fondsOmschrijving);
      if($width>58)
      {
        for($i=strlen($fondsOmschrijving);$i>0;$i--)
        {
          $newOmschschrijving = substr($row[0], 0, $i).'...';
          $width=$this->pdf->GetStringWidth($newOmschschrijving);
          if($width<58)
          {
            $row[0]=$newOmschschrijving;
            break;
          }
        }
      }
      $this->pdf->row(array('', $row[0], $this->formatGetal($row[1] * 100, 2) . '%', $this->formatGetal($row[2], 0)));
    }
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
    $this->pdf->row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),$this->formatGetal($blockData['totaal']['aandeelFonds']* 100,2).'%',$this->formatGetal($blockData['totaal']['waardeFonds'],0)));
	}
  
  
  function printPie($pieData,$kleurdata,$title='',$width=100,$height=100)
  {
    
    $col1=array(255,0,0); // rood
    $col2=array(0,255,0); // groen
    $col3=array(255,128,0); // oranje
    $col4=array(0,0,255); // blauw
    $col5=array(255,255,0); // geel
    $col6=array(255,0,255); // paars
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
        $pieData[$key] = $value['percentage'];
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
   // $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    
    $this->pdf->Cell(50,4,vertaalTekst($title, $this->pdf->rapport_taal),0,0,"C");
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
    
    $x1 = $XPage + $w +$margin +4 ;
    $x2 = $x1 + $margin/2;
    $y1 = $YDiag - ($radius) ;
    
    for($i=0; $i<$this->pdf->NbVal; $i++)
    {
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
      $l=vertaalTekst($l ,$this->pdf->rapport_taal);
      //$p=sprintf('%.1f',$val/$this->sum*100).'%';
      //$p=sprintf('%.1f',$val).'%';
      $p=$this->formatGetal($val,1).'%';
      $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
      $this->pdf->legends[]=$legend;
      //$this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->pdf->wLegend);
    }
  }

}
?>
