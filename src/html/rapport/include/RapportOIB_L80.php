<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/16 15:57:02 $
File Versie					: $Revision: 1.11 $

$Log: RapportOIB_L80.php,v $
Revision 1.11  2020/05/16 15:57:02  rvv
*** empty log message ***

Revision 1.10  2020/05/02 15:57:50  rvv
*** empty log message ***

Revision 1.9  2020/03/18 17:44:11  rvv
*** empty log message ***

Revision 1.8  2020/03/15 10:25:30  rvv
*** empty log message ***

Revision 1.7  2020/03/14 18:42:03  rvv
*** empty log message ***

Revision 1.6  2019/05/29 15:45:16  rvv
*** empty log message ***

Revision 1.5  2019/05/08 15:11:07  rvv
*** empty log message ***

Revision 1.4  2019/01/30 16:47:26  rvv
*** empty log message ***

Revision 1.3  2018/12/09 13:00:15  rvv
*** empty log message ***

Revision 1.2  2018/12/08 18:28:30  rvv
*** empty log message ***

Revision 1.1  2018/10/03 15:42:01  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");


class RapportOIB_L80
{
	function RapportOIB_L80($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
    if(count($pdf->portefeuilles)>1)
    {
      $this->consolidatie=true;
      $this->verdeling1='beleggingscategorie';
  
    }
    else
    {
      $this->consolidatie=false;
    }
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Onderverdeling in beleggingscategorieën";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
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
  
  
  function verdelingPerPortefeuille($portefeuille,$rapportageDatum)
  {
    
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q = "SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '" . $beheerder . "'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $kleuren = unserialize($kleuren['grafiek_kleur']);
    $kleuren = $kleuren[$this->pdf->rapport_type];
  

    
    $regels=berekenPortefeuilleWaarde($portefeuille,$rapportageDatum, (substr($rapportageDatum,5,5)=='01-01')?true:false,$this->pdf->portefeuilledata['RapportageValuta'],$rapportageDatum);
    $totaleWaarde=0;
    
    foreach($regels as $regel)
    {
      $totaleWaarde+=$regel['actuelePortefeuilleWaardeEuro'];
    }
    $primaireVerdeling=array();
    $kleurData=array();
    foreach($regels as $regel)
    {
      $data['percentageVanTotaal']=$regel['actuelePortefeuilleWaardeEuro']/$totaleWaarde*100;
      if($regel[$this->verdeling1.'Omschrijving']=='')
      {
        if($regel[$this->verdeling1]<>'')
        {
          if($regel[strtolower($this->verdeling1).'Omschrijving']<>'')
            $regel[$this->verdeling1 . 'Omschrijving'] = $regel[strtolower($this->verdeling1).'Omschrijving'];
          else
            $regel[$this->verdeling1 . 'Omschrijving'] = $regel[$this->verdeling1];
        }
        else
        {
          $regel[$this->verdeling1]='Geen '.$this->verdeling1;
          $regel[$this->verdeling1 . 'Omschrijving'] = 'Geen '.$this->verdeling1;
        }
      }
      //listarray($regel);
      $primaireVerdeling[$regel[$this->verdeling1.'Omschrijving']] +=$data['percentageVanTotaal'];
      if(!isset($kleurData[$regel[$this->verdeling1.'Omschrijving']]))
        $kleurData[$regel[$this->verdeling1.'Omschrijving']]=$kleuren[$regel[$this->verdeling1]];
      $kleurData[$regel[$this->verdeling1.'Omschrijving']]['percentage']+=$data['percentageVanTotaal'];
    }
    arsort($primaireVerdeling);

    return array('verdeling'=>$primaireVerdeling,'kleuren'=>$kleurData);
  }


	function writeRapport()
	{
    
    if($this->consolidatie==true)
    {
      
      $this->pdf->AddPage();
      $hoofdP = $this->verdelingPerPortefeuille($this->portefeuille, $this->rapportageDatum);
      
      //
      // listarray($hoofdP);
      $this->pdf->setXY(30,37);
      $legendLocation=array(90,7+30-(count($hoofdP['verdeling'])*4)/2);
      //   echo count($hoofdP['verdeling']);exit;
      $this->printPie($hoofdP['verdeling'],$hoofdP['kleuren'],$this->portefeuille.' '.date("d-m-Y",db2jul($this->rapportageDatum)),60,50,$legendLocation);
      $this->pdf->wLegend=0;
  
      $maxRows=0;//count($hoofdP['verdeling']);
      $extraY=65;
      $verdelingsData=array();
      foreach($this->pdf->portefeuilles as $portefeuille)
      {
        $verdelingsData[$portefeuille] = $this->verdelingPerPortefeuille($portefeuille, $this->rapportageDatum);
    
        $maxRows=max($maxRows,count($verdelingsData[$portefeuille]['verdeling']));
      }
      if($maxRows>6)
      {
        $this->pdf->addPage();
        $extraY=0;
      }
      
      
      $x=30;
      $n=0;

      foreach($verdelingsData as $portefeuille=>$data)
      {
    /*
        if($n>1)
        {
          $yPos = 120;
        }
        else
          $yPos=37;
    */
        $yPos = 120;
        
       
      //  if($n==2)
      //    $x=30;
        //echo "$portefeuille $x, $yPos <br>\n"; ob_flush();
        $this->pdf->setXY($x, $yPos);
        $data = $this->verdelingPerPortefeuille($portefeuille, $this->rapportageDatum);

        if(min($data['verdeling'])>0)
          $this->printPie($data['verdeling'], $data['kleuren'], $portefeuille . ' ' . date("d-m-Y", db2jul($this->rapportageDatum)), 45, 45);
        else
          BarDiagram($this->pdf,80, 60, $data['verdeling'], '%l (%p)',$data['kleuren'],$portefeuille . ' ' . date("d-m-Y", db2jul($this->rapportageDatum)));//Sector verdeling
        
        $n++;
        $x += 90;
        if($n>5)
          break;
      
      }

    }
    else
    {
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		$this->pdf->AddPage();
    $this->pdf->templateVars['OIBPaginas']=$this->pdf->page;

		$rapportageDatum = $this->rapportageDatum;
		$rapportageDatumVanaf = $this->rapportageDatumVanaf;
	$portefeuille = $this->portefeuille;

	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
					 "FROM TijdelijkeRapportage WHERE ".
					 " rapportageDatum ='".$rapportageDatum."' AND ".
					 " portefeuille = '".$portefeuille."' "
					 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalWaarde = $DB->nextRecord();
	$totaalWaarde = $totaalWaarde['totaal'];

  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
					 "FROM TijdelijkeRapportage WHERE ".
					 " rapportageDatum ='".$rapportageDatumVanaf."' AND ".
					 " portefeuille = '".$portefeuille."' "
					 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalWaardeBegin = $DB->nextRecord();
	$totaalWaardeBegin = $totaalWaardeBegin['totaal'];

	$query = "SELECT
			SUM(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro
			FROM
			TijdelijkeRapportage
			WHERE
			TijdelijkeRapportage.Portefeuille = '".$portefeuille."' AND
			TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' AND
 			TijdelijkeRapportage.Type = 'rekening'
			" .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalLiquiditeiten = $DB->nextRecord();
	$totaalLiquiditeiten = $totaalLiquiditeiten['WaardeEuro'];




	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);


$query="SELECT
if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',TijdelijkeRapportage.beleggingscategorie,'geen')) as categorie,
sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
 if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',Beleggingscategorien.Omschrijving,'geen')) as categorieOmschrijving
FROM TijdelijkeRapportage LEFT JOIN Beleggingscategorien on TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.beleggingscategorie
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
	.$__appvar['TijdelijkeRapportageMaakUniek'].
	" GROUP BY categorie
	ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde, categorie";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	   $data['beleggingscategorieEind']['data'][$cat['categorie']]['waardeEur']=$cat['WaardeEuro'];
	   $data['beleggingscategorieEind']['data'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
	   $data['beleggingscategorieEind']['pieData'][$cat['categorieOmschrijving']]= $cat['WaardeEuro']/$totaalWaarde*100;
	   $data['beleggingscategorieEind']['kleurData'][$cat['categorieOmschrijving']]=$allekleuren['OIB'][$cat['categorie']];
	   $data['beleggingscategorieEind']['kleurData'][$cat['categorieOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
	}

	$query="SELECT
if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',TijdelijkeRapportage.beleggingscategorie,'geen')) as categorie,
sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
 if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',Beleggingscategorien.Omschrijving,'geen')) as categorieOmschrijving
FROM TijdelijkeRapportage LEFT JOIN Beleggingscategorien on TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.beleggingscategorie
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatumVanaf."'"
	.$__appvar['TijdelijkeRapportageMaakUniek'].
	" GROUP BY categorie
	ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde, categorie";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	   $data['beleggingscategorieBegin']['data'][$cat['categorie']]['waardeEur']=$cat['WaardeEuro'];
	   $data['beleggingscategorieBegin']['data'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
	   $data['beleggingscategorieBegin']['pieData'][$cat['categorieOmschrijving']]= $cat['WaardeEuro']/$totaalWaardeBegin*100;
	   $data['beleggingscategorieBegin']['kleurData'][$cat['categorieOmschrijving']]=$allekleuren['OIB'][$cat['categorie']];
	   $data['beleggingscategorieBegin']['kleurData'][$cat['categorieOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaardeBegin*100;
	}


	$query="SELECT
TijdelijkeRapportage.valuta,
Sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
Valutas.Omschrijving
FROM
TijdelijkeRapportage
Inner Join Valutas ON Valutas.Valuta = TijdelijkeRapportage.valuta
WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
	.$__appvar['TijdelijkeRapportageMaakUniek'].
" GROUP BY Valuta
ORDER BY Valutas.afdrukvolgorde";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	   $data['valutaVerdeling']['data'][$cat['valuta']]['waardeEur']=$cat['WaardeEuro'];
	   $data['valutaVerdeling']['data'][$cat['valuta']]['Omschrijving']=$cat['Omschrijving'];
	   $data['valutaVerdeling']['pieData'][$cat['Omschrijving']]= $cat['WaardeEuro']/$totaalWaarde*100;
	   $data['valutaVerdeling']['kleurData'][$cat['Omschrijving']]=$allekleuren['OIV'][$cat['valuta']];
	   $data['valutaVerdeling']['kleurData'][$cat['Omschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
	}


$this->pdf->setXY(30,37);
//$this->pdf->setXY(65,40);
      if(min($data['beleggingscategorieBegin']['pieData'])>0)
         $this->printPie($data['beleggingscategorieBegin']['pieData'],$data['beleggingscategorieBegin']['kleurData'],'Categorieverdeling '.date("d-m-Y",db2jul($rapportageDatumVanaf)),60,50);
      else
        BarDiagram($this->pdf,80, 60, $data['beleggingscategorieBegin']['pieData'], '%l (%p)',$data['beleggingscategorieBegin']['kleurData'],'Categorieverdeling '.date("d-m-Y",db2jul($rapportageDatumVanaf)));
  
      $this->pdf->wLegend=0;
$this->pdf->setXY(120,37);
//$this->pdf->setXY(175,40);
  if(min($data['beleggingscategorieEind']['pieData'])>0)
      $this->printPie($data['beleggingscategorieEind']['pieData'],$data['beleggingscategorieEind']['kleurData'],'Categorieverdeling '.date("d-m-Y",db2jul($rapportageDatum)),60,50);
  else
      BarDiagram($this->pdf,80, 60, $data['beleggingscategorieEind']['pieData'], '%l (%p)',$data['beleggingscategorieEind']['kleurData'],'Categorieverdeling '.date("d-m-Y",db2jul($rapportageDatum)));
$this->pdf->wLegend=0;

$this->pdf->setXY(210,37);
if(min($data['valutaVerdeling']['pieData'])>0)
  $this->printPie($data['valutaVerdeling']['pieData'],$data['valutaVerdeling']['kleurData'],'Valutaverdeling '.date("d-m-Y",db2jul($rapportageDatum)),60,50);
else
  BarDiagram($this->pdf,80, 60, $data['valutaVerdeling']['pieData'], '%l (%p)',$data['valutaVerdeling']['kleurData'],'Valutaverdeling '.date("d-m-Y",db2jul($rapportageDatum)));

foreach ($data as $type=>$typeData)
{
  $n=0;
  foreach ($typeData['data'] as $categorie=>$gegevens)
  {
    if(!is_array($regelData[$n]))
      $regelData[$n]=array('','','','','','','','','','');

		$beginWaarde=false;
    if($type=='beleggingscategorieBegin')
		{
			$offset = 0;
			$beginWaarde=true;
		}
    if($type=='beleggingscategorieEind')
		{
			$offset = 4;
		}
    if($type=='valutaVerdeling')
		{
			$offset = 8;
		}
     $regelData[$n][0]='';
     $regelData[$n][1+$offset]=$gegevens['Omschrijving'];
     $regelData[$n][2+$offset]=$this->formatGetalKoers($gegevens['waardeEur'],0,$beginWaarde);
     $regelData[$n][3+$offset]=$this->formatGetal($data[$type]['kleurData'][$gegevens['Omschrijving']]['percentage'],2).'%';
     $regelData[$n][4+$offset]='';
     $n++;

     $regelTotaal[$type]['waardeEur']+=$gegevens['waardeEur'];
     $regelTotaal[$type]['percentage']+=round($data[$type]['kleurData'][$gegevens['Omschrijving']]['percentage'],2);
  }

}


foreach ($regelData as $regelNr=>$regel)
{
  ksort($regel);
  $regelData[$regelNr]=$regel;
}

$this->pdf->setXY($this->pdf->marge,130);
$this->pdf->SetWidths(array(20, 40,20,15, 15, 40,20,15, 15, 40,20,15));
//$this->pdf->SetWidths(array(45, 40,20,15, 40, 40,20,15, 15));
$this->pdf->SetAligns(array('L', 'L','R','R',  'L',  'L','R','R',  'L',  'L','R','R'));



//
$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
$this->pdf->CellBorders = array();
$this->pdf->ln(2);

$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
foreach ($regelData as $regel)
{
  $this->pdf->row($regel);
}

$this->pdf->underlinePercentage=0.8;
$this->pdf->CellBorders = array('','','TS','TS','','','TS','TS','','','TS','TS');
$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
$this->pdf->row(array('','Totaal '.date("d-m-Y",db2jul($rapportageDatumVanaf)), $this->formatGetalKoers($regelTotaal['beleggingscategorieBegin']['waardeEur'],0,true),$this->formatGetal($regelTotaal['beleggingscategorieBegin']['percentage'],2).'%','',
'Totaal '.date("d-m-Y",db2jul($rapportageDatum)), $this->formatGetalKoers($regelTotaal['beleggingscategorieEind']['waardeEur'],0),$this->formatGetal($regelTotaal['beleggingscategorieEind']['percentage'],2).'%'
,'','Totaal '.date("d-m-Y",db2jul($rapportageDatum)), $this->formatGetalKoers($regelTotaal['valutaVerdeling']['waardeEur'],0),$this->formatGetal($regelTotaal['valutaVerdeling']['percentage'],2).'%'
));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
unset($this->pdf->CellBorders);
    }
	}



	function printPie($pieData,$kleurdata,$title='',$width=100,$height=100,$legendLocation=null)
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
		 // while (list($key, $value) = each($kleurdata))
      foreach($pieData as $omschrijving=>$value)
			{
  			if ($kleurdata[$omschrijving]['R']['value'] == 0 && $kleurdata[$omschrijving]['G']['value']== 0 && $kleurdata[$omschrijving]['B']['value'] == 0)
	  		  $grafiekKleuren[]=$standaardKleuren[$a];
		  	else
			    $grafiekKleuren[] = array($kleurdata[$omschrijving]['R']['value'],$kleurdata[$omschrijving]['G']['value'],$kleurdata[$omschrijving]['B']['value']);
		 
		  	$a++;
			}
		}
		else
		  $grafiekKleuren = $standaardKleuren;

		//while (list($key, $value) = each($pieData))
    foreach($pieData as $key=>$value)
			if ($value < 0)
				$pieData[$key] = -1 * $value;


			//$this->pdf->SetXY(210, $this->pdf->headerStart);
			$y = $this->pdf->getY();
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->pdf->setXY($startX,$y-4);
      //	$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
	
			$this->pdf->Cell(50,4,vertaalTekst($title, $this->pdf->rapport_taal),0,0,"C");
			$this->pdf->setXY($startX,$y);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	
      $this->pdf->setX($startX);
			$this->PieChart($width, $height, $pieData, '%l (%p)', $grafiekKleuren,$legendLocation);
			$hoogte = ($this->pdf->getY() - $y) + 8;
			$this->pdf->setY($y);

			$this->pdf->SetLineWidth($this->pdf->lineWidth);
			$this->pdf->setX($startX);

		//	$this->pdf->Rect($startX,$this->pdf->getY(),$width,$hoogte);

	}

	function PieChart($w, $h, $data, $format, $colors=null,$legendLocation=null)
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

      if(is_array($legendLocation))
      {
        $x1 = $legendLocation[0];
        $x2 = $x1 + $hLegend + $margin;
        $y1 = $legendLocation[1] + ($radius) + $margin;
      }
      else
      {
        $x1 = $XPage;
        $x2 = $x1 + $hLegend + $margin;
        $y1 = $YDiag + ($radius) + $margin;
      }
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