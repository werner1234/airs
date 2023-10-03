<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/13 15:13:06 $
File Versie					: $Revision: 1.1 $

$Log: RapportRISK_L90.php,v $
Revision 1.1  2020/06/13 15:13:06  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");


class RapportRISK_L90
{
	function RapportRISK_L90($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FACTUUR";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_HSE_geenrentespec=true;
		$this->pdf->rapport_titel =	"";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    
    include($__appvar["basedir"]."/html/rapport/include/layout_90/RapportImages_L90.php");
    
 	}
  
 	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatGetalNegatief($waarde, $dec)
	{
	  if($waarde<0)
      return 'Negatief!';
    else  
 		  return number_format($waarde,$dec,",",".");
	}
  
  function printKop($data)
  {
    $this->pdf->Ln();
   
    if( $this->pdf->GetY() > 275)
      $this->pdf->AddPage('P');
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->GetX(),$this->pdf->GetY(),210-($this->pdf->marge*2),8,'F');
    $this->pdf->SetFillColor(255);
    
    if(isset($data['h']))
      $h=$data['h'];
    else
      $h=5; 
       
    if(isset($data['align']))
      $align=$data['align'];
    else
      $align='C';
      
    if(isset($data['fontsize']))
      $fontsize=$data['fontsize'];
    else
      $fontsize=12;    
      
    if(isset($data['preLn']))
      $this->pdf->Ln($data['preLn']);
    else
      $this->pdf->Ln(2);  
      
    
    if(isset($data['style']))
      $style=$data['style'];
    else
      $style='';  
           
    $this->pdf->SetFont('arial','B',$fontsize);
    if(isset($data['startY']))
      $this->pdf->SetY($data['startY']);
    $this->pdf->SetTextColor(255,255,255);
    $this->pdf->MultiCell(210-$this->pdf->marge*2,$h,$data['txt'],0,$align);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->Ln(2);
    $this->pdf->SetFont('arial','',$fontsize);
    $this->pdf->Ln();
  //  $this->pdf->Rect($this->pdf->GetX(),$this->pdf->GetY(),210-($this->pdf->marge*2),8,'F');
     
  }
  
  function printRegel($data)
  {
    if(isset($data['h']))
      $h=$data['h'];
    else
      $h=5; 
       
    if(isset($data['align']))
      $align=$data['align'];
    else
      $align='L';
      
    if(isset($data['fontsize']))
      $fontsize=$data['fontsize'];
    else
      $fontsize=12;    
      
    if(isset($data['preLn']))
      $this->pdf->Ln($data['preLn']);
        
    if(isset($data['style']))
      $style=$data['style'];
    else
      $style='';  
           
    $this->pdf->SetFont('arial','',$fontsize);
    if(isset($data['startY']))
      $this->pdf->SetY($data['startY']);
    $this->pdf->MultiCell(210-$this->pdf->marge*2,$h,$data['txt'],0,$align);
  }
  
  function printCells($data)
  {
    $x=$this->pdf->getX();
    
    foreach($data['cells'] as $cell)
    {
      if($cell[3]==1)
        $this->pdf->SetFont('arial','u',12);
      else
        $this->pdf->SetFont('arial','',12); 
     // $this->pdf->MultiCell($cell[0],5,$cell[2],0,$cell[1]);  
      $this->pdf->Cell($cell[0],5,$cell[2],0,0,$cell[1]);  
    }
    if($data['rect'])
    {
      $this->pdf->Rect($x+$data['rect'][0],$this->pdf->GetY()+$data['rect'][1], $data['rect'][2], $data['rect'][3] ,$data['rect'][4],$data['rect'][5],$data['rect'][6]); 
    }
    $this->pdf->Ln();
  }
  
  function printBlok($data)
  {
     if(isset($data['h']))
      $h=$data['h'];
    else
      $h=5; 
       
    if(isset($data['align']))
      $align=$data['align'];
    else
      $align='J';
      
    if(isset($data['fontsize']))
      $fontsize=$data['fontsize'];
    else
      $fontsize=12;    
      
       
    if(isset($data['style']))
      $style=$data['style'];
    else
      $style='';  
           
    $this->pdf->SetFont('arial','',$fontsize);
    if(isset($data['startY']))
      $this->pdf->SetY($data['startY']);
        
      
    if(isset($data['preLn']))
      $this->pdf->Ln($data['preLn']);
       
    $this->pdf->MultiCell(210-$this->pdf->marge*2,$h,$data['txt'],0,$align);   
    $this->pdf->ln();
    if(isset($data['postLn']))
      $this->pdf->Ln($data['postLn']);  
  }

  function printAfbeelding($data)
  {
    if(isset($data['hoogte']))
      $afbeeldingHoogte=$data['hoogte'];
    else
      $afbeeldingHoogte=80;
     if( $this->pdf->GetY() > 297-$afbeeldingHoogte)
     {
      $this->pdf->AddPage('P');
     }
    $imgData=base64_decode($this->images[$data['naam']]);
    $xLocatie=210/2-$data['width']/2;
    if(isset($data['startY']))
      $startY=$data['startY'];
    else
      $startY=$this->pdf->GetY()+4;


    $this->pdf->MemImage($imgData,$xLocatie,$startY,$data['width']);
     
    $this->pdf->Ln($afbeeldingHoogte);
    
  }
  function checkMarge($blok)
  {
    if($blok['margeY'])
    {
      if($this->pdf->PageBreakTrigger-$this->pdf->getY() < $blok['margeY'])
        $this->pdf->AddPage('P');
    }
    if($blok['ln'])
    {
      $this->pdf->ln($blok['ln']);
    }
  }
  
  function addDocument($document)
  {
    foreach($document as $blok)
    {
       switch ($blok['type'])
       {
        case 'regel':
          $this->printRegel($blok);
        break;
        case 'kop':
          $this->printKop($blok);
        break;
        case 'par':
          $this->printBlok($blok);
        break;     
        case 'afbeelding':
          $this->printAfbeelding($blok);
        break;  
        case 'nextpage':
          $this->pdf->AddPage('P');
        break;
        case 'cells':
          $this->printCells($blok);
        break;
        case 'check':
          $this->checkMarge($blok);
        break;
       }
    }
  }
  
	function writeRapport()
	{
		global $__appvar;
    $this->pdf->AddPage('P');
    $margeBackup=$this->pdf->marge;
    $this->pdf->marge=25;
    $this->pdf->SetMargins($this->pdf->marge,$this->pdf->marge,true);
    $gebruikteCrmVelden=array('naam','naam1','beleggingsDoelstelling','doelvermogen','doeldatum','Rendementsdoelstelling','RisicobereidheidObj',
    'ProfielVragenlijst','ProfielGekozen','AfwijkingVragenlijst','AfwijkingVragenlijstToel','ScenarioToel','RevisieGesprek',
    'CheckVrijeTekst','UitgangspuntenVrijeTekst','KennisEnErvaring','FinancielePositie','RisicobereidheidSub','RevisieGesprekToel');
    
    $db=new DB();
    $query="DESC CRM_naw";
    $db->SQL($query);
    $db->Query();
    $crmVelden=array();
    while($data=$db->nextRecord())
      $crmVelden[]=strtolower($data['Field']);
    
    $nawSelect='';  
    $nietgevonden=array();
    foreach($gebruikteCrmVelden as $veld)
      if(in_array(strtolower($veld),$crmVelden))
        $nawSelect.=",CRM_naw.$veld ";
      else
        $nietgevonden[]=$veld; 
//        listarray($gebruikteCrmVelden)."<br>\n"; listarray($nietgevonden)."<br>\n";
     if(count($nietgevonden) > 0)
       $this->pdf->MultiCell(190,5,'Niet gevonden crm velden: '.implode(', ',$nietgevonden));
    
    $query="SELECT 
Portefeuilles.Risicoklasse,
laatstePortefeuilleWaarde.laatsteWaarde , laatstePortefeuilleWaarde.change_date as laatsteWaardeDatum $nawSelect
FROM CRM_naw
JOIN Portefeuilles on CRM_naw.portefeuille=Portefeuilles.Portefeuille
LEFT JOIN laatstePortefeuilleWaarde on CRM_naw.portefeuille=laatstePortefeuilleWaarde.Portefeuille
WHERE CRM_naw.portefeuille='".$this->portefeuille."'";


    $db->SQL($query);
    $crmData=$db->lookupRecord();
    $naam=trim($crmData['naam']."\n ".$crmData['naam1']);
    $datum=date("d",$this->pdf->rapport_datum)." ".vertaalTekst($__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum);
   //listarray($crmData);exit;
    
    $document=array();//1
    $document[]=array('type'=>'afbeelding','naam'=>'01front.png','startY'=>50,'width'=>100);
    $document[]=array('type'=>'regel','txt'=>'UITGANGSPUNTEN BELEGGINGSBELEID','fontsize'=>30,'align'=>'C','startY'=>142,'h'=>12,'style'=>'B');
    $document[]=array('type'=>'regel','txt'=>'cliënt:','preLn'=>12,'align'=>'C');
    $document[]=array('type'=>'regel','txt'=>$naam,'align'=>'C');
    $document[]=array('type'=>'regel','txt'=>'datum:','preLn'=>12,'align'=>'C');
    $document[]=array('type'=>'regel','txt'=>$datum,'align'=>'C');  
    $document[]=array('type'=>'nextpage'); 
    //2  
    $document[]=array('type'=>'kop','txt'=>'INLEIDING');
    $document[]=array('type'=>'par','txt'=>'Index Capital hecht er veel waarde aan dat de inrichting van uw beleggingsportefeuille naadloos aansluit bij uw wensen, doelstellingen en persoonlijke situatie.');
    $document[]=array('type'=>'par','txt'=>'Bij de start van de relatie en vervolgens minimaal één keer per jaar checken we daarom alle uitgangspunten die van invloed kunnen zijn op de inrichting van uw beleggingen. En vanzelfsprekend passen wij uw beleggingsportefeuille aan wanneer gewijzigde omstandigheden daar aanleiding toe geven.');
    $document[]=array('type'=>'par','txt'=>'Dit document is een samenvatting van de gegevens waar wij bij het beleggen van uw vermogen vanuit gaan. Het beschrijft de belangrijkste uitgangspunten ten aanzien van uw risicohouding, doelstellingen c.q. beleggingshorizon, kennis en ervaring, inkomen en vermogen.');
    $document[]=array('type'=>'par','txt'=>'Het is belangrijk dat u deze samenvatting goed doorleest. Herkent u zich in hetgeen is geschreven? Dan hoeft u niets te doen. Mocht dat niet het geval zijn, dan is het belangrijk dat u contact met ons opneemt. Zodat wij de gegevens kunnen aanpassen en kunnen borgen dat we u te allen tijde een passende dienstverlening bieden.');
    $document[]=array('type'=>'par','txt'=>'NB Zonder tegenbericht vertrouwen wij erop dat dit document een juiste weergave van de feiten is.');
    $document[]=array('type'=>'par','txt'=>"Heeft u vragen of opmerkingen? Aarzelt u dan niet om contact met ons op te nemen, wij zijn u graag van dienst.\n\nHartelijke groet van\n\nHet team van Index Capital");

		$document[]=array('type'=>'afbeelding','naam'=>'02planning.png','width'=>100);	
    $document[]=array('type'=>'nextpage');   
   // listarray($crmData);
    $document[]=array('type'=>'kop','txt'=>'BELEGGINGSDOELSTELLING');
    $document[]=array('type'=>'par','txt'=>'Het doel dat u heeft met uw beleggingen, kan omschreven worden als: \''.$crmData['beleggingsDoelstelling'].'\'.');
   	$document[]=array('type'=>'kop','txt'=>'BELEGGINGSHORIZON, BEDRAG EN RENDEMENT');
    
    if($crmData['laatsteWaarde']<=0)
      $waarde=$crmData['doelvermogen'];
    else
      $waarde=$crmData['laatsteWaarde'];
     
    //3
    $document[]=array('type'=>'par','txt'=>'Het vermogen dat u via Index Capital gaat beleggen c.q. reeds belegt bedraagt '."\n".'€ '.$this->formatGetal($waarde).'.

Voor het realiseren van uw doelstellingen is het wenselijk en/of noodzakelijk dat dit bedrag in een aantal jaar zal aangroeien tot een doelvermogen. Dit betekent dat uw vermogen - afgezien van fiscaliteit - een gemiddelde netto rendementsdoelstelling kent. Tot slot is het begrip beleggingshorizon belangrijk. Dit is de periode dat u de gelden niet voorzienbaar nodig heeft voor iets anders dan beleggen.

Samenvattend hebben wij het volgende vastgelegd:');

    $document[]=array('type'=>'cells','cells'=>array(array(110,'L','Startvermogen c.q. huidige waarde portefeuille'),array(10,'L',': €'),array(35,'R',$this->formatGetal($waarde))));
    $document[]=array('type'=>'cells','cells'=>array(array(110,'L','Doeljaar'),array(10,'L',': €'),array(35,'R',$crmData['doeldatum'])));
    $document[]=array('type'=>'cells','cells'=>array(array(110,'L','Doelvermogen ca.'),array(10,'L',': €'),array(35,'R',$this->formatGetal($crmData['doelvermogen']))));
    $document[]=array('type'=>'cells','cells'=>array(array(110,'L','Gewenste gem. netto rendementspercentage per jaar ca.'),array(10,'L',': '),array(35,'R',$crmData['Rendementsdoelstelling'].' %')));
    $document[]=array('type'=>'cells','cells'=>array());
    
    //4
   	$document[]=array('type'=>'kop','txt'=>'KENNIS EN ERVARING');
    $document[]=array('type'=>'par','txt'=>'Kennis van en/of ervaring met beleggen is belangrijk, omdat u dan de risico\'s van het beleggen beter kunt inschatten en de kans op een positieve beleggingservaring toeneemt.');
    if($crmData['KennisEnErvaring'] <>'')
    {
      if($crmData['KennisEnErvaring']='Gemiddelde kennis en ervaring')
        $document[]=array('type'=>'par','txt'=>'Uw kennis van en/of ervaring met beleggen kan worden samengevat als \'gemiddeld\'.');
      else
        $document[]=array('type'=>'par','txt'=>$crmData['KennisEnErvaring']);
    }
   	$document[]=array('type'=>'kop','txt'=>'FINANCIËLE POSITIE');
    $document[]=array('type'=>'par','txt'=>'Uw financiële positie (denk aan inkomen en vermogen) bepaalt voor een belangrijk deel uw zogenaamde financiële remweg. Naarmate u meer inkomen geniet en over meer vermogen beschikt, bent u minder gevoelig voor eventuele tussentijdse schommelingen van uw vermogen.   

Op basis van de van u verkregen informatie weten wij c.q. schatten wij in dat uw financiële positie omschreven kan worden als \''.$crmData['FinancielePositie'].'\'.
'); 
 
    $document[]=array('type'=>'kop','txt'=>'SUBJECTIEVE RISICOBEREIDHEID: WELKE RISICO\'S WILT U LOPEN?');
    $document[]=array('type'=>'par','txt'=>'Bij subjectieve risicobereidheid gaat het om de vraag welke risico\'s u maximaal wilt lopen. Ze heeft te maken met de mentale kant van beleggen: hoe reageert u bijvoorbeeld op beursschommelingen: blijft u dan rustig of wordt u nerveus?

Uw objectieve risicohouding kan als volgt worden samengevat: \''.$crmData['RisicobereidheidSub'].'\'.');

if($crmData['RisicobereidheidSub']=='Risicomijdend')//Index Capital optie 1: risicomijdend
$document[]=array('type'=>'par','txt'=>'U snapt dat beleggen gepaard gaat met risico\'s nemen, maar het is uw wens om deze risico\'s zoveel mogelijk te beperken. U voelt zich ongemakkelijk bij een beurs met flinke beursschommelingen. U weet dat rendement en risico met elkaar samen hangen, maar vindt een goede nachtrust belangrijker dan een hoog rendement. Uw risicohouding kan omschreven worden als risicomijdend.');
elseif($crmData['RisicobereidheidSub']=='Neutraal')//Index Capital optie 2: neutraal
$document[]=array('type'=>'par','txt'=>'U snapt dat beleggen gepaard gaat met risico\'s nemen, en vindt het geen probleem om hierbij een gemiddeld risico te lopen. U kijkt niet uit naar een beurs met flinke beursschommelingen, maar u wordt er ook niet heel onrustig van. U weet dat rendement en risico met elkaar samen hangen, en beseft dat schommelingen op de beurs er nu eenmaal bij horen. Uw risicohouding kan omschreven worden als neutraal.');
elseif($crmData['RisicobereidheidSub']=='Risicovoller')//Index Capital optie 3: risicovoller
$document[]=array('type'=>'par','txt'=>'U snapt dat beleggen gepaard gaat met risico\'s nemen, en vindt het geen probleem dat beurskoersen soms flink kunnen schommelen. U wordt hier niet onrustig van, want u weet dat u op langere termijn hiervoor beloond wordt met een gemiddeld hoger rendement. Uw risicohouding kan omschreven worden als risicovoller.');  

//5
    $document[]=array('type'=>'kop','txt'=>'OBJECTIEVE RISICOBEREIDHEID: WELKE RISICO\'S KUNT U LOPEN?');
    $document[]=array('type'=>'par','txt'=>'Bij objectieve risicobereidheid gaat het om de vraag welke risico\'s u maximaal kunt lopen. Met name uw financiële positie, uw beleggingshorizon en doelstellingen zijn hierbij bepalend. Wanneer u bijvoorbeeld financieel zeer onafhankelijk bent, dan kunt u zich meer schommelingen in uw vermogen veroorloven dan wanneer dit niet het geval is. Uw objectieve risicohouding kan als volgt worden samengevat: \''.$crmData['RisicobereidheidSub'].'\'.');

if($crmData['RisicobereidheidObj']=='Risicomijdend')//Index Capital optie 1: risicomijdend
$document[]=array('type'=>'par','txt'=>'Gezien uw financiële positie, uw beleggingshorizon en doelstellingen, kan uw objectieve risicobereidheid samengevat worden als risicomijdend. Een beleggingsprofiel zeer defensief, defensief of matig defensief lijkt het meest passend.');
elseif($crmData['RisicobereidheidObj']=='Neutraal')//Index Capital optie 2: neutraal
$document[]=array('type'=>'par','txt'=>'Gezien uw financiële positie, uw beleggingshorizon en doelstellingen, kan uw objectieve risicobereidheid samengevat worden als neutraal. Een beleggingsprofiel matig defensief of matig offensief lijkt het meest passend.');
elseif($crmData['RisicobereidheidObj']=='Risicovoller')//Index Capital optie 1: risicovoller
$document[]=array('type'=>'par','txt'=>'Gezien uw financiële positie, uw beleggingshorizon en doelstellingen, kan uw objectieve risicobereidheid samengevat worden als risicovoller. Een beleggingsprofiel matig offensief, offensief of zeer offensief lijkt het meest passend.');
//6
    $document[]=array('type'=>'kop','txt'=>'BELEGGINGSPROFIEL');
    $document[]=array('type'=>'par','txt'=>'Op basis van de bij aanvang van de relatie tussen u en Index Capital ingevulde Vragenlijst Beleggen is uw beleggingsprofiel: \''.$crmData['ProfielVragenlijst'].'\'.

Als uiteindelijk beleggingsprofiel is door u gekozen: \''.$crmData['ProfielGekozen'].'\'.');

$afmTekst='De AFM risicowijzer is een afbeelding ter illustratie en ondersteuning van de informatie over een risicoprofiel. Zij geeft de mate van risico aan van de beleggingen in een risicoprofiel. De risicowijzer voor het door u gekozen beleggingsprofiel ziet er als volgt uit:';
$hoogteTweedePlaatje=65;
if($crmData['ProfielGekozen']=='zeer defensief')//Index Capital optie 1: zeer defensief
{
    $document[]=array('type'=>'par','txt'=>'De kenmerken van dit beleggingsprofiel zijn als volgt. Voor u zijn inkomsten uit vermogen en/of het grotendeels beschermen van uw vermogen belangrijk. U streeft naar een iets beter rendement dan u met een spaarrekening zou realiseren. Er wordt niet (of zeer beperkt) belegd in aandelen.  Risico’s wilt u zoveel mogelijk vermijden, maar u beseft dat het vermogen in een jaar in waarde kan dalen. De verdeling van het vermogen over de verschillende beleggingscategorieën ziet er als volgt uit:');  
    $document[]=array('type'=>'afbeelding','naam'=>'03zd.png','width'=>100);  
    $document[]=array('type'=>'par','txt'=>$afmTekst); 
    $document[]=array('type'=>'afbeelding','naam'=>'13zd.png','width'=>100,'hoogte'=>$hoogteTweedePlaatje);
}
elseif($crmData['ProfielGekozen']=='defensief')//Index Capital optie 2: defensief
{        
    $document[]=array('type'=>'par','txt'=>'De kenmerken van dit beleggingsprofiel zijn als volgt. Voor u zijn inkomsten uit vermogen en/of het beschermen van uw vermogen belangrijk. Echter, wetende dat aandelen op de langere termijn een hoger rendement kunnen opleveren, bent u bereid een laag risico met uw vermogen te lopen om op lange termijn enige vermogensgroei te kunnen realiseren. U beseft dat uw vermogen in een jaar in waarde kan dalen. De verdeling van het vermogen over de verschillende beleggingscategorieën ziet er als volgt uit:');  
    $document[]=array('type'=>'afbeelding','naam'=>'04d.png','width'=>100);
    $document[]=array('type'=>'par','txt'=>$afmTekst); 
    $document[]=array('type'=>'afbeelding','naam'=>'14d.png','width'=>100,'hoogte'=>$hoogteTweedePlaatje);
}
elseif($crmData['ProfielGekozen']=='matig defensief')//Index Capital optie 3: matig defensief
{        
    $document[]=array('type'=>'par','txt'=>'De kenmerken van dit beleggingsprofiel zijn als volgt. U bent bereid een laag tot gemiddeld risico met uw vermogen te lopen, maar u belegt nog altijd meer in staatsobligaties (met een AA/AAA rating) en bedrijfsobligaties dan in aandelen. U streeft zowel naar enige vermogensgroei op lange termijn als naar enige inkomsten uit uw vermogen. U beseft dat uw vermogen in enkele achtereenvolgende jaren in waarde kan dalen. De verdeling van het vermogen over de verschillende beleggingscategorieën ziet er als volgt uit:');  
    $document[]=array('type'=>'afbeelding','naam'=>'05md.png','width'=>100);
    $document[]=array('type'=>'par','txt'=>$afmTekst); 
    $document[]=array('type'=>'afbeelding','naam'=>'15md.png','width'=>100,'hoogte'=>$hoogteTweedePlaatje);
}
elseif($crmData['ProfielGekozen']=='matig offensief')//Index Capital optie 4: matig offensief
{           
    $document[]=array('type'=>'par','txt'=>'De kenmerken van dit beleggingsprofiel zijn als volgt. U weet de risico’s van het beleggen in aandelen goed in te schatten en weet welke kansen daar tegenover staan. U streeft voornamelijk naar vermogensgroei op de wat lange termijn en om dit doel te bereiken bent u bereid een gemiddeld risico met uw vermogen te lopen. Daarnaast streeft u naar enige inkomsten uit uw vermogen. U beseft dat uw vermogen in enkele achtereenvolgende jaren in waarde kan dalen. De verdeling van het vermogen over de verschillende beleggingscategorieën ziet er als volgt uit:');  
    $document[]=array('type'=>'afbeelding','naam'=>'06mo.png','width'=>100);
    $document[]=array('type'=>'par','txt'=>$afmTekst); 
    $document[]=array('type'=>'afbeelding','naam'=>'16mo.png','width'=>100,'hoogte'=>$hoogteTweedePlaatje);
}
elseif($crmData['ProfielGekozen']=='offensief')//Index Capital optie 5: offensief
{           
    $document[]=array('type'=>'par','txt'=>'De kenmerken van dit beleggingsprofiel zijn als volgt. U streeft naar vermogensgroei op lange termijn en om dit doel te bereiken bent u bereid een hoog risico met uw vermogen te lopen. Inkomsten uit vermogen spelen voor u een beperkte rol. U beseft dat uw vermogen in enkele achtereenvolgende jaren in waarde kan dalen. De verdeling van het vermogen over de verschillende beleggingscategorieën ziet er als volgt uit:');  
    $document[]=array('type'=>'afbeelding','naam'=>'07o.png','width'=>100);
    $document[]=array('type'=>'par','txt'=>$afmTekst); 
    $document[]=array('type'=>'afbeelding','naam'=>'17o.png','width'=>100,'hoogte'=>$hoogteTweedePlaatje);
}
elseif($crmData['ProfielGekozen']=='zeer offensief')//Index Capital optie 6: zeer offensief
{   
    $document[]=array('type'=>'par','txt'=>'De kenmerken van dit beleggingsprofiel zijn als volgt. U streeft naar vermogensgroei op lange termijn en om dit doel te bereiken bent u bereid een zeer hoog risico met uw vermogen te lopen. Inkomsten uit vermogen spelen voor u geen rol. U beseft dat uw vermogen in enkele achtereenvolgende jaren sterk in waarde kan dalen, maar u bent vol vertrouwen dat daar ook jaren van forse waardestijgingen van het vermogen tegenover staan. De verdeling van het vermogen over de verschillende beleggingscategorieën ziet er als volgt uit:');  
    $document[]=array('type'=>'afbeelding','naam'=>'08zo.png','width'=>100);
    $document[]=array('type'=>'par','txt'=>$afmTekst); 
    $document[]=array('type'=>'afbeelding','naam'=>'18zo.png','width'=>100,'hoogte'=>$hoogteTweedePlaatje);
}    
if($crmData['AfwijkingVragenlijst']==1)
{
  $document[]=array('type'=>'par','txt'=>'U heeft gekozen voor een beleggingsprofiel dat risicovoller is dan het beleggingsprofiel dat - op basis van de puntentelling - voortvloeit uit de Vragenlijst Beleggen. Dit betekent dat u meer risico loopt dan feitelijk passend is bij uw financiële situatie c.q. risicohouding. Dit is nadrukkelijk met u besproken. U bent bereid om een hoger risico op het vermogen te aanvaarden dan uit het beleggingsprofiel is gebleken. U beseft dat het risico dat u uw financiële doelstellingen niet zult behalen daardoor toeneemt.');
  $document[]=array('type'=>'par','txt'=>''.$crmData['AfwijkingVragenlijstToel'].'.');  
}
//9
    $document[]=array('type'=>'kop','txt'=>'REALISATIE VAN UW DOELSTELLINGEN');
    $document[]=array('type'=>'par','txt'=>'Bij de vraag in hoeverre uw doelstellingen op termijn gerealiseerd gaan worden, is het relevant dat de werkelijke rendementen in de praktijk anders zullen zijn dan de  gehanteerde prognoserendementen. Immers, het één is een prognose, het andere is werkelijkheid. Deze afwijkingen kunnen invloed hebben op het al dan niet realiseren van uw doelstellingen. 

Om de mogelijke afwijkingen voor u inzichtelijk te maken, maken wij gebruik van een zogenaamde scenario analyse. In deze analyse worden met behulp van historische data een groot aantal simulaties in kaart gebracht van de ontwikkeling van het vermogen door de tijd heen. 

Hierna volgen de uitgangswaarden voor uw scenario analyse. Zo worden o.a. uw start- en doelkapitaal, uw beleggingshorizon en gegevens voor het door u gekozen beleggingsprofiel vermeld.');

    $DB=new DB();
    $query="SELECT id FROM CRM_naw WHERE portefeuille='".$this->portefeuille."'";
  	$DB->SQL($query);
	  $DB->Query();
		$crmId = $DB->nextRecord();   
    $sc= new scenarioBerekening($crmId['id'],$crmData['Risicoklasse']);

    if($crmData['laatsteWaarde']>0)
    {
      $sc->CRMdata['startvermogen'] = $crmData['laatsteWaarde'];
      $sc->CRMdata['startdatum'] = $crmData['laatsteWaardeDatum'];
    }

    if(!$sc->loadMatrix())
      $sc->createNewMatix(true);
    $this->scenarioKleur=$sc->scenarioKleur;
    $aantalSimulaties=10000;
    $sc->berekenSimulaties(0,$aantalSimulaties);
    $sc->berekenDoelKans();
    $sc->berekenVerdeling();
    $this->startJaar=$sc->CRMdata['startdatum'];

//scenario analyse links boven 
    $cellW1=110;
    $cellW2=10;
    $cellW3=30;
    
   // $document[]=array('type'=>'cells','cells'=>array(array(110,'L','Doeljaar'),array(10,'L',': €'),array(35,'R',$crmData['doeldatum'])));
    $document[]=array('type'=>'check','margeY'=>8*$this->pdf->rowHeight);
    $document[]=array('type'=>'cells','cells'=>array(array($cellW1,'L','Uitgangswaarden',1)));
    $document[]=array('type'=>'cells','cells'=>array(array($cellW1,'L','Beginwaarde'),array(10,'L',': €'),array($cellW3,'R',$this->formatGetal($sc->CRMdata['startvermogen']))));
    $document[]=array('type'=>'cells','cells'=>array(array($cellW1,'L','Doelvermogen'),array(10,'L',': €'),array($cellW3,'R',$this->formatGetal($sc->CRMdata['doelvermogen']))));
    $document[]=array('type'=>'cells','cells'=>array(array($cellW1,'L','Startjaar'),array(10,'L',':'),array($cellW3,'R',substr($sc->CRMdata['startdatum'],0,4))));
    $document[]=array('type'=>'cells','cells'=>array(array($cellW1,'L','Doeljaar'),array(10,'L',':'),array($cellW3,'R',substr($sc->CRMdata['doeldatum'],0,4))));
    $document[]=array('type'=>'cells','cells'=>array(array($cellW1,'L','Gekozen beleggingsprofiel'),array(10,'L',':'),array($cellW3,'R',$sc->CRMdata['gewenstRisicoprofiel'])));
    $document[]=array('type'=>'cells','cells'=>array(array($cellW1,'L','Verwacht rendement'),array(10,'L',':'),array($cellW3,'R',$this->formatGetal(($sc->profieldata['verwachtRendement']-1)*100,1).'%')));
    $document[]=array('type'=>'cells','cells'=>array(array($cellW1,'L','Standaarddeviatie'),array(10,'L',':'),array($cellW3,'R',$this->formatGetal($sc->profieldata['klasseStd']*100,1).'%')));
    $document[]=array('type'=>'cells','cells'=>array());
    //$document[]=array('type'=>'par','txt'=>'Toelichting: u ziet hier een aantal uitgangswaarden, zoals start- en doelkapitaal, uw beleggingshorizon en gegevens over het beleggingsprofiel.'); 
    $document[]=array('type'=>'par','txt'=>'In de grafiek hierna wordt met kleuren getoond hoe de ontwikkeling van het vermogen bij het beleggingsprofiel \''.$sc->CRMdata['gewenstRisicoprofiel'].'\' eruit ziet in verschillende scenario’s, variërend van zeer pessimistisch tot zeer optimistisch. Het scenario ‘normaal’ komt overeen met het prognoserendement dat hoort bij het door u gekozen beleggingsprofiel \''.$sc->CRMdata['gewenstRisicoprofiel'].'\'. In de grafiek wordt met een zwarte punt uw doelvermogen weergegeven.');

    $cellW1=50;
    $cellW2=50;
    $cellW3=50;
//scenario analyse links onder
    $document[]=array('type'=>'check','margeY'=>25);
    $document[]=array('type'=>'cells','cells'=>array(array($cellW1,'L','Scenario '.$sc->CRMdata['gewenstRisicoprofiel']),
                                                     array($cellW2,'R','Kans ongeveer'),
                                                     array($cellW3,'R','Eindvermogen')));
 
    foreach($sc->verwachteWaarden as $scenario=>$eindvermogen)
    {
      $kleur=$this->scenarioKleur[$scenario];
    
      $document[]=array('type'=>'cells','cells'=>array(array($cellW1,'L',$scenario),
                                                       array($cellW2,'R',$this->formatGetal( round((100-$sc->scenarios[$scenario])/5)*5,0).'%'),
                                                       array($cellW3,'R',$this->formatGetalNegatief($eindvermogen))),
                                        'rect'=>array(-2,1, 2, 2 ,'F','',$kleur));
    
    }
   
    $document[]=array('type'=>'check','margeY'=>50);
      $this->addDocument($document);
 
  $document=array();
  $this->pdf->SetX(35);
  $this->LineDiagram(125,50,$sc->scenarioGemiddelde,'',$sc->CRMdata['doelvermogen']);
  $document[]=array('type'=>'check','ln'=>60);

  $document[]=array('type'=>'par','txt'=>'Goed om te weten: de scenario analyse houdt bij haar berekeningen automatisch rekening met de prognoserendementen (korte en lange termijn) die relevant zijn voor uw beleggingshorizon.');
  $document[]=array('type'=>'par','txt'=>'De prognoserendementen zien er per beleggingsprofiel als volgt uit:');
  $document[]=array('type'=>'par','txt'=>'Hierna wordt benoemd hoe groot de kans is dat u het gewenste eindvermogen gaat realiseren, rekening houdend met de nog resterende beleggingshorizon. Naar mate uw beleggingshorizon korter wordt is het van belang dat dit percentage hoger wordt. Vervolgens worden ook de andere - niet door u gekozen - beleggingsprofielen getoond.');

//scenario analyse rechts boven
  $document[]=array('type'=>'check','margeY'=>3*$this->pdf->rowHeight);
  $document[]=array('type'=>'cells','cells'=>array(array($cellW1,'L','Conclusies',1)));
  $document[]=array('type'=>'cells','cells'=>array(array($cellW1,'L','Kans op doelvermogen'),array($cellW2,'R',$this->formatGetal($sc->doelKans,0).'%')));
  $document[]=array('type'=>'cells','cells'=>array(array($cellW1,'L','Gemiddeld eindvermogen'),array($cellW2,'R',"€ ".$this->formatGetalNegatief($sc->verwachteWaarden['Normaal']))));
  $document[]=array('type'=>'cells','cells'=>array());
//scenario analyse rechts onder
      $sc->overigeRisicoklassen();
      $kansData=$sc->berekenKansBijOpgehaaldeRisicoklassen();
      $grafiekData=$kansData['grafiekData'];
      $cellW1=30;
      $cellW2=10;
      $cellW3=30;
      $cellW4=30;
      $cellW5=30;
      $cellW6=30;
      $document[]=array('type'=>'check','margeY'=>(count($kansData['risicoklassen'])+1)*$this->pdf->rowHeight);
      $document[]=array('type'=>'cells','cells'=>array(array($cellW1,'L','Risicoprofiel',1),
                                                       array($cellW2,'C',' '),
                                                       array($cellW3,'R','Kans op doel',1),
                                                       array($cellW4,'R','Pessimistisch',1),
                                                       array($cellW5,'R','Normaal',1),
                                                       array($cellW6,'R','Optimistisch',1)));
      $maxKansTmp=0;
      
      foreach($kansData['risicoklassen'] as $risicoklasse=>$klasseData)
      {
//        $this->pdf->row(array('',);
        
              $document[]=array('type'=>'cells','cells'=>array(array($cellW1,'L',$risicoklasse),
                                                       array($cellW2,'C',"(".$klasseData['risicoklasseData']['afkorting'].")"),
                                                       array($cellW3,'R', $this->formatGetal($klasseData['uitkomstKans']['kans'],0).'%'),
                                                       array($cellW4,'R',$this->formatGetalNegatief($klasseData['uitkomstKans']['scenarioEindwaarden']['Pessimistisch'])),
                                                       array($cellW5,'R',$this->formatGetalNegatief($klasseData['uitkomstKans']['scenarioEindwaarden']['Normaal'])),
                                                       array($cellW6,'R',$this->formatGetalNegatief($klasseData['uitkomstKans']['scenarioEindwaarden']['Optimistisch']))));
                                                       

      }
      $document[]=array('type'=>'cells','cells'=>array());
      $document[]=array('type'=>'par','txt'=>'Tot slot kunt u hierna zien bij welk beleggingsprofiel de kans het grootst is dat u uw doelstellingen gaat realiseren. Dit hoeft overigens niet het huidige door u gekozen beleggingsprofiel te zijn. Er kunnen namelijk diverse redenen zijn waarom u voor het huidige beleggingsprofiel heeft gekozen. Echter, het kan ook zijn dat de uitkomsten van de scenario analyse ertoe leiden dat u een ander beleggingsprofiel overweegt. Alsdan denken wij hierin graag met u mee.');
      $document[]=array('type'=>'check','margeY'=>60);
      $this->addDocument($document);
 
      $document=array();
      $this->scatterplot(130,50,$grafiekData);  
      $this->pdf->SetFont('arial','',12);
      $document[]=array('type'=>'check','ln'=>60);
    




if($crmData['RevisieGesprek']==1)
{
    $document[]=array('type'=>'kop','txt'=>'ONS JAARLIJKSE GESPREK');
    $document[]=array('type'=>'par','txt'=>'Recent hebben wij met elkaar gesproken. Dit document is feitelijk de schriftelijke vastlegging van deze jaarlijkse herijking van alle uitgangspunten die voor ons van belang zijn bij het zorgvuldig beheren van uw vermogen.');  
    if($crmData['RevisieGesprekToel']!='')
      $document[]=array('type'=>'par','txt'=>$crmData['RevisieGesprekToel']);
    else
      $document[]=array('type'=>'par','txt'=>'In ons gesprek gaf u aan dat er geen relevante wijzigingen zijn ten aanzien van uw inkomen, vermogen of persoonlijke situatie, die aanpassing van het beleggingsbeleid door Index Capital noodzakelijk maken. Mochten deze wijzigingen zich in de komende periode voordoen, dan is het belangrijk dat u deze aan ons doorgeeft. Bij voorbaat dank hiervoor!');
}

if($crmData['CheckVrijeTekst']==1)
{
    $document[]=array('type'=>'kop','txt'=>'DIVERSEN');
    $document[]=array('type'=>'par','txt'=>$crmData['UitgangspuntenVrijeTekst']);
}



   $this->addDocument($document);
   $this->pdf->marge=$margeBackup;
   $this->pdf->SetMargins($this->pdf->marge,$this->pdf->marge,true);
   $this->pdf->SetFont('arial','',12);

$totaalPaginas=count($this->pdf->pages);

//$pbBackup=$this->pdf->PageBreakTrigger;
//$this->pdf->PageBreakTrigger=297;
$this->pdf->AutoPageBreak=false;
foreach($this->pdf->pages as $page=>$pageData)
{
 	$this->pdf->page = $page;
  $this->pdf->Ln();
  $this->pdf->SetXY(0,285);
  $this->pdf->SetFont('arial','',12);
  $this->pdf->SetTextColor(0,0,0);
  $this->pdf->SetFillColor(0,0,0);
  $this->pdf->Cell(210,5,'Pagina '.$page.' van '.$totaalPaginas,0,0,'C');
}
$this->pdf->AutoPageBreak=true;
//$this->pdf->PageBreakTrigger=$pbBackup;

}
  
  

function scatterplot($w, $h, $data,$maxStdev=25,$beste)
{
    global $__appvar;
    $color=null; $maxVal=0; $minVal=0; $horDiv=4; $verDiv=4;$jaar=0;

    $minXVal=0; $maxXVal=25; 
    $minYVal=0; $maxYVal=100; 
      
    $XPage = $this->pdf->GetX()+15;
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = $h;//floor($h - $margin * 1);
    $XDiag = $XPage;// + $margin * 1 ;
    $lDiag = $w;//floor($w);

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(0,0,0);
    $this->pdf->SetLineWidth(0.2);

    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    
    $procentWhiteSpace = 0.10;
    $xband=($maxXVal - $minXVal);
    $yband=($maxYVal - $minYVal);
    $stepSize=round($band / $horDiv);
    $stepSize=ceil($stepSize/(pow(10,strlen($stepSize))))*pow(10,strlen($stepSize));
    $maxVal = ceil($maxVal * (1 + ($procentWhiteSpace))/$stepSize)*$stepSize;
    $minVal = floor($minVal * (1 - ($procentWhiteSpace))/$stepSize)*$stepSize;
    
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / $yband;
    $Xunit = $lDiag / $xband;
    $Yunit = $hDiag / $yband *-1;



    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);


    
    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    
    $rood=array(180,50,50);
    $groen=array(50,180,50);
    $steps=100;
    $kleurenStap=array(($rood[0]-$groen[0])/$steps,
                           ($rood[1]-$groen[1])/$steps,
                            ($rood[2]-$groen[2])/$steps); 
  
  
    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    $factor=0.5;
    for($i=0; $i<= $maxYVal; $i+= 10)
    {
      $kleur=array($rood[0]-($i*$kleurenStap[0]),
                   $rood[1]-($i*$kleurenStap[1]),
                   $rood[2]-($i*$kleurenStap[2]));
 
       $kleur2=array(($rood[0]-($i*$kleurenStap[0]))*$factor+100,
                   ($rood[1]-($i*$kleurenStap[1]))*$factor+100,
                   ($rood[2]-($i*$kleurenStap[2]))*$factor+100);
                                  
      if($i < 100)
      {  
        if($maxStdev >0)
        {
          $this->pdf->Rect($XDiag                 , $bodem+$i*$Yunit,$Xunit*$maxStdev    ,$Yunit*10,'F','',$kleur);
          $this->pdf->Rect($XDiag+$Xunit*$maxStdev, $bodem+$i*$Yunit,$w-$Xunit*$maxStdev ,$Yunit*10,'F','',$kleur2);
        }
        else
        {
          $this->pdf->Rect($XDiag, $bodem+$i*$Yunit,$w ,$Yunit*10,'F','',$kleur);
        }
      }
      
      if($maxStdev >0)
      {
        $tekstWidth=($w-$Xunit*$maxStdev);
        if($tekstWidth > 5 && $i==$maxYVal)
        {
          $this->pdf->setXY(($XDiag+$Xunit*$maxStdev),$bodem-$hDiag/2);
          $this->pdf->MultiCell($tekstWidth,2.5,vertaalTekst("Buiten risicotolerantie",$this->pdf->rapport_taal), 0,"C");
        }
      }

      //$this->pdf->Rect($XDiag+($i+1)*$unit-0.5-$xcorrectie, $yval2-0.5, 1, 1 ,'F','',$color);
      $skipNull = true;
      $this->pdf->Line($XDiag, $bodem+$i*$Yunit, $XPage+$w ,$bodem+$i*$Yunit,array('dash' => 1,'color'=>array(0,0,0)));
      
      $this->pdf->setXY($XDiag-20, $bodem+$i*$Yunit);
      $this->pdf->Cell(20,0, $i." %", 0,0, "R");
      //$this->pdf->Text($XDiag-7, $bodem+$i*$Yunit, $i." %");
      $n++;
      if($n >20)
       break;
    }
    $this->pdf->Text($XDiag-7, $bodem+$maxYVal*$Yunit-3, "Kans");
    
    for($i=0; $i<= $maxXVal; $i+= 5)
    {
      $xplot=$XDiag+$i*$Xunit;
      $skipNull = true;
      $this->pdf->Line($xplot, $YDiag, $xplot,$bodem,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($xplot-2, $bodem+3, $i." %");
      $n++;
      if($n >20)
       break;
    }
    $this->pdf->Text($XDiag+$maxXVal/2*$Xunit-8, $bodem+6, vertaalTekst('Standaarddeviatie',$this->pdf->rapport_taal));
    
   $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
     
   foreach($data as $reeks=>$waarden)
   {
     $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
 
     if($this->pdf->portefeuilledata['Layout']==5 && $reeks==$beste['scenario'])
       $this->pdf->MemImage(base64_decode($this->vuurtorenIMG), $XDiag+$waarden['x']*$Xunit-1.0,$bodem+$waarden['y']*$Yunit-8.3,2 );
      
     $this->pdf->Rect($XDiag+$waarden['x']*$Xunit-0.5,$bodem+$waarden['y']*$Yunit-0.5, 1, 1 ,'F','',$color);
     $this->pdf->setXY($XDiag+$waarden['x']*$Xunit-5,$bodem+$waarden['y']*$Yunit+2.5);
     $this->pdf->Cell(10,0,$reeks, 0,0, "C");
   
    }


    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
    $this->pdf->Ln();
    return $beste;
  }
    
function LineDiagram($w, $h, $data,$werkelijkVerloop,$doelVermogen)
  {
    global $__appvar;
    $color=null; $maxVal=0; $minVal=10000000; $horDiv=5; $verDiv=4;$jaar=0;

    $XPage = $this->pdf->GetX()+10;
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage  ;
    $lDiag = $w;

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(116,95,71);
    $this->pdf->SetLineWidth(0.2);

    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

   $aantalPunten=array();
   foreach($data as $reeks=>$waarden)
   {
     $tmp=ceil(max($waarden));
     if($tmp > $maxVal)
       $maxVal = $tmp;
        
     $tmp = floor(min($waarden));
     if($tmp < $minVal)  
       $minVal=$tmp;
       
     foreach($waarden as $index=>$waarde)
      $aantalPunten[$index]=$index;
   }
   
   foreach($werkelijkVerloop as $jaar=>$waarden)
   {
     if($waarden['waarde'] > $maxVal)
       $maxVal = $waarden['waarde'];
       
     if($waarden['waarde'] < $minVal)  
       $minVal=$waarden['waarde'];
   }
   
   if($minVal < 0)
     $minVal=0;
   
   if ($maxVal < 0)
     $maxVal = 1;

    
    $procentWhiteSpace = 0.1;
    $band=($maxVal - $minVal);
    $stepSize=round($band / $horDiv);
    //echo $band;exit;
    
    $stepSize=ceil($stepSize/(pow(10,strlen($stepSize))*5))*pow(10,strlen($stepSize))/5;
    $maxVal = ceil($maxVal * (1 + ($procentWhiteSpace))/$stepSize)*$stepSize;
    $minVal = floor($minVal * (1 - (0.3))/$stepSize)*$stepSize;
 
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / (count($aantalPunten)-1);

    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
    {
      $xpos = $XDiag + $verInterval * $i;
    }

    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
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
      //$this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ."");
      
      $this->pdf->setXY($XDiag-20, $i);
      if($n==0)
        $waarde=$minVal;
      else
        $waarde=0-($n*$stapgrootte);
   
      $this->pdf->Cell(20,0, $this->formatGetal($waarde,0)."", 0,0, "R");
      
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      if($n*$stapgrootte >= $minVal)
      {
        $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        {
        //  $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ."");
           $this->pdf->setXY($XDiag-20, $i);
           $this->pdf->Cell(20,0, $this->formatGetal($n*$stapgrootte,0)."", 0,0, "R");
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
    $lineStyle = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0));
    $circleStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255,255,255));
    
   // $color=array(200,0,0);
   $datumPrinted=array();
   $xcorrectie=$unit;
   $data=array_reverse($data);
   $reeksCount=0;
   $lastReeks=count($data)-1;
   $polly=array();
   $pollyReverse=array();
   foreach($data as $reeks=>$waarden)
   {
     $color=array($this->scenarioKleur[$reeks][0],$this->scenarioKleur[$reeks][1],$this->scenarioKleur[$reeks][2]);
  
    $lines[$reeks]=array();
    $marks[$reeks]=array();

    //$polly[]=$XDiag;
    //$polly[]=$bodem;
   if(count($waarden)> 20)
     $modi=2;
   else
     $modi=1; 
     
    for ($i=0; $i<count($waarden); $i++)
    {
      if($waarden[$i] < 0)
        $waarden[$i]=0;
        
      if(!isset($datumPrinted[$i]))
      {     
        if($i%$modi==0)
          $this->pdf->TextWithRotation($XDiag+($i*$unit)-2,$YDiag+$hDiag+8,$this->startJaar+$i,25);
        $datumPrinted[$i]=1;
      }
      
      $yval2 = $YDiag + (($maxVal-$waarden[$i]) * $waardeCorrectie) ;
      
      if($i==0)
      {
        $yval = $bodem ;
      } 
      else
      {
  
        //$this->pdf->line($XDiag+$i*$unit-$xcorrectie, $yval, $XDiag+($i+1)*$unit-$xcorrectie, $yval2,$lineStyle );
        $lines[$reeks][]=array($XDiag+$i*$unit-$xcorrectie, $yval, $XDiag+($i+1)*$unit-$xcorrectie, $yval2);
        $marks[$reeks][]=array($XDiag+($i+1)*$unit-0.5-$xcorrectie, $yval2-0.5);
        //$this->pdf->Rect($XDiag+($i+1)*$unit-0.5-$xcorrectie, $yval2-0.5, 1, 1 ,'F','',$color);
        if($reeksCount==0)
        {
        $polly[]=$XDiag+$i*$unit-$xcorrectie;
        $polly[]=$yval;
        $polly[]=$XDiag+($i+1)*$unit-$xcorrectie;
        $polly[]=$yval2;
        }
        elseif($reeksCount==$lastReeks)
        {
          $pollyReverse[]=$yval;
          $pollyReverse[]=$XDiag+$i*$unit-$xcorrectie;
          $pollyReverse[]=$yval2;
          $pollyReverse[]=$XDiag+($i+1)*$unit-$xcorrectie;

        }
       
      }
      $yval = $yval2;
    }

    $reeksCount++;
    //$polly[]=$XDiag+$w;
   // $polly[]=$bodem;
   //  $this->pdf->Polygon($polly, 'F', null, $color) ;
    }
    $pollyReverse=array_reverse($pollyReverse);
   // listarray($polly);
    foreach($pollyReverse as $value)
      $polly[]=$value;
   // listarray($polly);
    $this->pdf->Polygon($polly, 'F', null, array(200,200,200)) ;
    
    
    foreach($lines as $reeks=>$lineData)
    {
      $color=array($this->scenarioKleur[$reeks][0],$this->scenarioKleur[$reeks][1],$this->scenarioKleur[$reeks][2]); 
      $lineStyle = array('width' => 0.8, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
      foreach($lineData as $line)
      {
       $this->pdf->line($line[0],$line[1],$line[2],$line[3],$lineStyle);
      }
    }   


      
    foreach($marks as $reeks=>$markData)   
    {
     foreach($markData as $mark) 
     {
       $color=array($this->scenarioKleur[$reeks][0],$this->scenarioKleur[$reeks][1],$this->scenarioKleur[$reeks][2]); 
       $r=0.5;
       $this->pdf->Circle($mark[0]+$r,$mark[1]+$r, $r, 0,360, $style = 'DF', $circleStyle, $color);
     }
    }

    
      


    $yval = $YDiag + (($maxVal-$doelVermogen) * $waardeCorrectie) ;
    $xval=$XDiag+(count($waarden))*$unit-0.5-$xcorrectie+$r;
    $circleStyle = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0));
    $this->pdf->Circle($xval,$yval, $r, 0,360, $style = 'DF', $circleStyle, array(0,0,0));
    
    $this->pdf->Circle($XDiag,$YDiag+$h+10, $r, 0,360, $style = 'DF', $circleStyle, array(0,0,0));
    $this->pdf->TextWithRotation($XDiag+2,$YDiag+$h+10+1,vertaalTekst('Doelvermogen',$this->pdf->rapport_taal),0);
    
    $lineStyle = array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0));
    $i=0;
   foreach($werkelijkVerloop as $jaar=>$waarden)
   {
     $yval2 = $YDiag + (($maxVal-$waarden['waarde']) * $waardeCorrectie) ;
     if($i==0)
     {
       $yval = $bodem ;
     } 
     else
     {
      $this->pdf->line($XDiag+$i*$unit-$xcorrectie, $yval, $XDiag+($i+1)*$unit-$xcorrectie, $yval2,$lineStyle );
     }  
     $yval = $yval2;
     $i++;
   }
   }
}
?>
