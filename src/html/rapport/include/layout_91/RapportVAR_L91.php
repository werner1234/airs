<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/layout_91/RapportFRONT_L91.php");
include_once("rapport/include/layout_91/RapportEND_L91.php");
include_once("rapport/include/layout_91/RapportPERFG_L91.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");

class RapportVAR_L91
{
	function RapportVAR_L91($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VAR";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->pdf->rapport_rendementText="Rendement over verslagperiode";
    $this->check =  base64_decode('iVBORw0KGgoAAAANSUhEUgAAADQAAAAzCAMAAADvo9thAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjIzRDI2NzQyQ0U3QzExRUFBQTUyQkZGMUEzMDAxRkI3IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjIzRDI2NzQzQ0U3QzExRUFBQTUyQkZGMUEzMDAxRkI3Ij4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MjNEMjY3NDBDRTdDMTFFQUFBNTJCRkYxQTMwMDFGQjciIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MjNEMjY3NDFDRTdDMTFFQUFBNTJCRkYxQTMwMDFGQjciLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5caeO6AAAADFBMVEWbp8AnQngAIGD///8gq/pNAAAAbElEQVR42uzWMQ6AMAxDUSe+/50JoJZOqA4Sk70xfD06tUDuL3gPkXoFRapNKbCzxTo/xq++Lx7rkrai5Vy6VJYuZaIjwZIlS5YsWfpDEi7qKamD+o74ILEV6VZFRCciWxE6kWiNCCGMhwADAMizGIXhL/REAAAAAElFTkSuQmCC');
    $this->checkPng=base64_decode('iVBORw0KGgoAAAANSUhEUgAAAC0AAAA1CAMAAADiQZJeAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjNCMjBERDg2Q0U3NjExRUE4NzM3ODU3RUM2QkI2RUFGIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjNCMjBERDg3Q0U3NjExRUE4NzM3ODU3RUM2QkI2RUFGIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6M0IyMEREODRDRTc2MTFFQTg3Mzc4NTdFQzZCQjZFQUYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6M0IyMEREODVDRTc2MTFFQTg3Mzc4NTdFQzZCQjZFQUYiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5O95YdAAAAMFBMVEX0+vSWz5JUsUzn9Oas2ajF5MLX7NV4wXJFqzxuvWc9pzRht1qEx3/+/v46pjH////UjrOAAAACYUlEQVR42rSWy6LrIAhFUfBRleT///YCxuZRk5PJzaADs4qIG3ZgnTwcXODZC5itUc3ewTsayC/Lkhu+oWPLiz7lE/mZZkYLXHKRX++Q+ZZmQPpo2Jywb+ApAk9oITG4ainICVlPanwLEWFsAYwxxhCIWi2dTUFDcGy+L9TkKAShUEK6Wr3vBzOWkLfdwsbbuq+1RaABar61BdzzZMntc3hdHLg8SNkwXgsKMbhUc9+iJKVLTwxhftuox3Itb3QmmIOH2kL0cl9Gh/Xv5z/Ty3saO13e0xXaE32QoNBF6IdMkOir8R77IW90XspwzvuWRhVFxncVNHhpcKCTajDTTB7WPe0rySB0U7rQ3u4EAxbdlR022oH9jqUku2kVGJKK2B2GhObgTFtpLNXFcMZ2hdeuVasj7wEUR03jDK96izIAJJ4f672TkrbXBYaPVhPWVBYfvqp3fmu8M7yiBmVYzyXsQ1APdC6oFGNpMn2CNtxBPYr/wHrIhYQGPWY8tCBVT9f5ahVFobnpvDs3+U9Th2qVkx2DTYrnntdEnM5BZknFB356otWPWWOSyechtI0/VSNsQvd0P384StZZfQvsr3LRKd7hbEKwzW1U60XJ5fFDHt4s0WqhslWFzeHDS9i7ys9xLfVoCzik5h1PbFl1M8wQxrHTr6S/IqsBLp6mF7DkTzzr1FrZD3j3S8Z0dlS5OTKXqnttDwfbLEgEKKISswt1c9obLx6O6iJGMvss528JuHwRdIfMyQ+n5YevgrOjJgJ+/OIQR22WT/GNfrQAE7cLlMRqw0Q3cGOocfrinwADADDacgmRnW/iAAAAAElFTkSuQmCC');
    
    $this->fontsize=$this->pdf->rapport_fontsize+1;//12;
	}
	
	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
	
	function getCRMdata()
  {
    $gebruikteCrmVelden=array(
      'naam',
      'naam1',
      'beleggingsDoelstelling',
      'doelvermogen',
      'doeldatum',
      'gewenstRisicoprofiel',
      'SamenvattingFinPositie',
      'SamenvattingErvaring',
      'SamenvattingUitgangspunten',
      'BeleggingsHorizon',
      'SamenvattingRisicohouding');
  
    $db = new DB();
    $query = "DESC CRM_naw";
    $db->SQL($query);
    $db->Query();
    $crmVelden=array();
    while($data=$db->nextRecord())
    {
      $crmVelden[]=strtolower($data['Field']);
    }
  
    $nawSelect='';
    $nietgevonden=array();
    foreach($gebruikteCrmVelden as $veld)
    {
      if(in_array(strtolower($veld),$crmVelden))
      {
        $nawSelect.=",CRM_naw.$veld ";
      }
      else
      {
        $nietgevonden[]=$veld;
      }
    }

    $query="
      SELECT
        Portefeuilles.Risicoklasse,
        Portefeuilles.Vermogensbeheerder,
        laatstePortefeuilleWaarde.laatsteWaarde
        $nawSelect
      FROM
        CRM_naw
      JOIN Portefeuilles on
        CRM_naw.portefeuille=Portefeuilles.Portefeuille
      LEFT JOIN laatstePortefeuilleWaarde on
        CRM_naw.portefeuille=laatstePortefeuilleWaarde.Portefeuille
      WHERE
        CRM_naw.portefeuille='".$this->portefeuille."'";
    
    $db->SQL($query);
    $crmData=$db->lookupRecord();
    return $crmData;
  }
	
	
	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
		$this->rowHeightBackup=$this->pdf->rowHeight;
		$this->rowHeightHigh=6;
		
		$rapport = new RapportFRONT_L91($this->pdf, $this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
    $rapport->title='Vermogenskompas';
		$rapport->writeRapport();
    
    $crmData=$this->getCRMdata();
		$this->inleiding($crmData);
    
    $scenario=$this->scenarioBerekening();
    $this->samenvatting($crmData);
    $this->scenarioanalyse($scenario);

		//$this->risicoMeter();
    
    $this->toelichting();
    
		$this->pdf->rowHeight=$this->rowHeightBackup;
		$rapport = new RapportEND_L91($this->pdf, $this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
    $rapport->zonderBegrippen=true;
		$rapport->writeRapport();
    $this->pdf->rowHeight=$this->rowHeightBackup;
	}
  
  
  function toelichting()
  {
    $this->pdf->rapport_titel='Toelichting';
    $this->pdf->addPage();
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->setWidths(array( ($this->pdf->w-$this->pdf->marge*2-10)/2,10,($this->pdf->w-$this->pdf->marge*2-10)/2) );
    $this->pdf->setAligns(array('L','C','L'));
  
    $kol1="Standaarddeviatie wordt gebruikt als een maatstaf voor de risicograad van beleggingen. Het geeft de mate van afwijking van een gemiddelde weer. Risico bij beleggen is te omschrijven als de kans dat het werkelijke rendement afwijkt van het verwachte rendement. Dit kan dus zowel een lager als een hoger rendement betekenen.

Een hogere standaarddeviatie geeft aan dat sprake is van een hoger risico, aangezien de afwijkingen van het gemiddelde in het verleden groter waren. Ander woorden voor standaarddeviatie zijn volatiliteit en beweeglijkheid. De rendementen van aandelen schommelen meer dan die van obligaties. Dit komt tot uitdrukking in het verschil in standaarddeviatie.

De standaarddeviatie van obligaties is doorgaans lager dan die van aandelen. Naarmate de rendementen in het verleden meer schommelden, is de standaarddeviatie hoger en dat geldt daarmee ook voor het risico. De standaarddeviatie wordt berekend met behulp van historische rendementen.";
  
    $kol2="De toekomst is niet te voorspellen. Wel kunnen we met een scenarioanalyse de haalbaarheid van uw toekomstige wensen en doelen op termijn, gecombineerd met een (voor u) acceptabel beleggingsrisico inzichtelijk maken.
    
Op basis van historische gegevens, macro-economische kennis en verwachtingen worden 10.000 economische scenario's doorgerekend om te komen tot een prognose van de mogelijke ontwikkelingen van uw belegd vermogen.
    
In lijn met het gekozen risicoprofiel wordt met een haalbaarheidspercentage een verwacht eindvermogen in een normaal scenario aan u gepresenteerd. Een prognose blijft uiteraard een verwachting, zo is er de kans dat de daadwerkelijke ontwikkeling van uw vermogen slechter of beter is dan de verwachte eindwaarde. Of slechter dan een pessimistische of beter dan een optimistische markt.
    
Een scenarioanalyse helpt u om samen met ons te bepalen of de genomen risico's aanvaardbaar zijn en stelt ons in staat om uw verwachtingen te managen.";
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->fontsize);
    $this->pdf->Row(array('Toelichting standaarddeviatie','','Scenarioanalyse'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->fontsize);
    $this->pdf->Row(array($kol1,'',$kol2));
    
  
  }
	
	function inleiding($crmData)
	{
	  $this->pdf->rapport_titel='Inleiding';
	  $this->pdf->addPage();
    $this->pdf->rowHeight=$this->rowHeightHigh;
	  $kol1='
Bij het nemen van financiële beslissingen is het van belang een optimale afstemming te vinden tussen uw huidige en gewenste situatie én uw mogelijkheden. Uw persoonlijke doelen en wensen spelen hierbij een belangrijke rol.

In bijgaand document vindt u een overzicht van uw uitgangspunten, doelstellingen en beleggingshorizon zoals deze destijds met u zijn besproken en zijn vastgelegd. Daarnaast zijn uw kennis, ervaring en risicobereidheid de basis geweest voor het huidige afgesproken risicoprofiel in het vermogensbeheer contract.

In het kader van passendheid en geschiktheid vindt u tevens de huidige verdeling van uw vermogen over de vermogenscategorieën Zakelijke waarden, Vastrentende waarden en Liquiditeiten.

Wij bevestigen dat uw portefeuille in overeenstemming is met onze vastlegging van uw beleggingsdoelstelling, beleggingshorizon, kennis- en ervaringsniveau, risicobereidheid en verliescapaciteit (uw cliëntprofiel).

Mogelijk zijn er wijzigingen opgetreden in uw financiële situatie, doelstellingen, wensen, beleggingshorizon of risicohouding. Graag stemmen wij met u af of deze vastgelegde gegevens nog steeds aansluiten bij uw huidige situatie en wensen en of uw huidige beleggingsportefeuille nog steeds het meest passend voor u is.';
    
    $kol2='
Sequoia Vermogensbeheer kiest voor een rustige hand van beleggen en een gestage groei.

Beleggen kan een goede manier zijn om uw doelen te bereiken. Maar dat vraagt wel om een zorgvuldige aanpak, met een lange termijn focus.

Hierbij hoort onder andere een actueel inzicht in uw financiële positie en de vermogensontwikkeling van het beheerde vermogen over de afgelopen jaren. Op deze manier krijgt u inzicht in de kansen en mogelijkheden van de realisatie van uw toekomstige financiële doelen.

Beleggen is nooit zonder risico, het nemen van beleggingsrisico’s loont vooral op de lange termijn. Maar met een breed gespreide beleggingsportefeuille worden die risico’s beperkt en bieden hiermee een gerede kans op het halen van uw financiële doelen.

Graag bespreken wij de inhoud van dit document met u in een volgend gesprek. Wilt u eerder hierover van gedachten te wisselen, dan horen wij dat natuurlijk graag.';
	  $this->pdf->setWidths(array( ($this->pdf->w-$this->pdf->marge*2-10)/2,10,($this->pdf->w-$this->pdf->marge*2-10)/2) );
	  $this->pdf->setAligns(array('L','C','L'));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->fontsize);
	  $this->pdf->Row(array($kol1,'',$kol2));
    
    $this->pdf->rapport_titel='Samenvatting van uw risicoprofiel';
		$this->pdf->addPage();

   	$this->pdf->SetAligns(array('L', 'L','L'));
    
    $teksten=array(array('Financiële positie','Bij de bepaling van uw financiële positie wordt zowel naar uw vermogen als uw huidige en toekomstige inkomsten en uitgaven gekeken.'),
      array('Beleggingshorizon','Op welke termijn wilt u uw doelen bereiken. Het nemen van risico’s loont vooral op de lange termijn. Alleen zo kunnen de jaren met hoge rendementen de jaren met lage of negatieve rendementen overtreffen. Het is een voorwaarde om uw beleggingsdoel te bereiken.'),
      array('Doelstelling','Welk doel heeft u met uw beleggingen, wat wilt u bereiken met uw beleggingsportefeuille? Beleggen is immers een middel en geen doel op zich.'),
      array('Ervaringen met beleggen','Het is van belang dat u inzicht heeft in de tussentijdse risico’s die beleggen met zich mee kan brengen, en dat u ook weet en begrijpt waarin wordt belegd.'),
      array('Risicohouding',"Met het begrip ‘risico’ bedoelen wij de jaarlijkse koersbewegingen van uw beleggingen, de plussen en de minnen. Een andere woord voor koersbewegingen is ‘volatiliteit’. Hierdoor loopt u de kans dat u uw belegde vermogen voor een deel of helemaal verliest.\n\nAls u belegt, loopt u altijd een risico. Afhankelijk van waarin u belegt, kan dat risico kleiner of groter zijn. Ook belangrijk daarbij is wat u met uw beleggingen wilt bereiken. Wilt u de kans op een hogere opbrengst, omdat u dan eerder uw vermogensdoelen denkt te kunnen realiseren, dan zal het bijbehorende risico ook hoger zijn.\n\nEr zijn twee soorten risico’s:\n\nHet risico dat u kunt lopen: dit wordt bepaald door uw financiële situatie en valt voor de korte en de  lange  termijn uit te rekenen en te plannen.\n\nHet risico dat u wilt lopen: dit wordt bepaald door uw houding ten opzichte van risico’s bij beleggingen. Wat vindt u aanvaardbaar? Hoe voelt u zich bij heftige koersbewegingen?"),

    );
    
    $teksten2=array(
      array('Financiële positie','Voor u is het volgende vastgelegd: '."\n".$crmData['SamenvattingFinPositie']),
      array('Beleggingshorizon','Voor u is het volgende vastgelegd: '."\n".$crmData['BeleggingsHorizon']),
      array('Doelstelling','Voor u is het volgende vastgelegd: '."\n".$crmData['beleggingsDoelstelling']),
      array('Ervaringen met beleggen','Voor u is het volgende vastgelegd: '."\n".$crmData['SamenvattingErvaring']),
      array('Risicohouding','Voor u is het volgende vastgelegd: '."\n".$crmData['SamenvattingRisicohouding'],
      
        )
    );
    
    foreach ($teksten2 as $index=>$tekst)
    {
      if(strlen(trim($tekst[1]))>34)
      {
        $teksten[$index][1].="\n\n".$tekst[1];
      }
    }
		//listarray($teksten);
    $n=0;
    $this->pdf->setWidths(array( ($this->pdf->w-$this->pdf->marge*2-10)/2,10,($this->pdf->w-$this->pdf->marge*2-10)/2) );
    $this->pdf->setAligns(array('L','C','L'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->fontsize);

    $n=1;
    $aantal=count($teksten);
    
    $kol1Tekens=1;
    $kol2Tekens=1;
    $yNaRow=0;
    $yVoorRow=0;
    $blok=0;
		foreach ($teksten as $index=>$tekstParts)
		{
      if($index%2==0)
      {
        $kol1 = $tekstParts;
      }
      else
      {
        $kol2 = $tekstParts;
      }
      
      if($index==4 && !isset($teksten[5]))
      {
        $verhouding=$kol1Tekens/$kol2Tekens;
        $yVerschil=$yNaRow-$yVoorRow;
        $yOmhoog=round(($yVerschil*(1-$verhouding))*4)/4;
        $this->pdf->setY($this->pdf->getY()-$yOmhoog);
        $this->pdf->ln();
      }
      
      if( ($n>0 && $n%2==0) || $index==$aantal-1)
      {
        $kol1Tekens=strlen($kol1[1]);
        $kol2Tekens=strlen($kol2[1]);
        
        if( ($kol1Tekens>500 || $kol2Tekens>500|| $this->pdf->getY()>180) && $blok>0 && $this->pdf->getY()>$this->pdf->h/2)
        {
          $this->pdf->addPage();
          $blok=0;
        }
        if(isset($kol1[0]))
          $this->pdf->memImage($this->check,$this->pdf->getX()+2,$this->pdf->getY(),4);
        if(isset($kol2[0]))
          $this->pdf->memImage($this->check,$this->pdf->getX()+($this->pdf->w-$this->pdf->marge*2-10)/2+12,$this->pdf->getY(),4);
       
        $this->pdf->Row(array('        '.$kol1[0],'','        '.$kol2[0]));
        $yVoorRow=$this->pdf->getY();
        $this->pdf->Row(array($kol1[1],'',$kol2[1]));
        $yNaRow=$this->pdf->getY();
        $this->pdf->ln();
        
        $n=0;
        $kol1=array();
        $kol2=array();
        $blok++;
      }
      $n++;

      /*
		  
			if($tekstParts[0] == 'Beleggingshorizon')
				$this->pdf->addPage();
			$this->pdf->SetWidths(array(8,270));

			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->fontsize);
			$this->pdf->row(array('',$tekstParts[0]));
			$this->pdf->ln(3);
			$this->pdf->SetWidths(array(270));
			$this->pdf->SetFont($this->pdf->rapport_font, '',$this->fontsize);
			$this->pdf->row(array($tekstParts[1]));
			$this->pdf->ln(20);
      */
		}
		
	}
	
	function samenvatting($crmData)
	{
		$this->pdf->rapport_titel='Samenvatting';
		$this->pdf->addPage();
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->SetWidths(array(150));
		$this->pdf->SetFont($this->pdf->rapport_font, '',$this->fontsize);
		$this->pdf->setAligns(array('L'));
    
    
    $DB=new DB();
    $query="
      SELECT
        ZorgplichtPerRisicoklasse.Zorgplicht,
        Minimum,
        Maximum,
        Zorgplichtcategorien.Omschrijving
      FROM
        ZorgplichtPerRisicoklasse
      INNER JOIN Zorgplichtcategorien ON
        ZorgplichtPerRisicoklasse.Vermogensbeheerder = Zorgplichtcategorien.Vermogensbeheerder AND ZorgplichtPerRisicoklasse.Zorgplicht = Zorgplichtcategorien.Zorgplicht
      WHERE
        ZorgplichtPerRisicoklasse.Risicoklasse='".$crmData['Risicoklasse']."' AND
        ZorgplichtPerRisicoklasse.Vermogensbeheerder='".$crmData['Vermogensbeheerder']."'";
    $DB->SQL($query);
    $DB->Query();
    $zorgplicht=array();
    $profielTxt='Dit risicoprofiel kent een bandbreedte voor de ';//'Bij dit profiel wordt een bandbreedte gehanteerd voor';
    $n=0;
    while($data = $DB->nextRecord())
    {
      if($n>0)
        $profielTxt.=" en voor de";
      $zorgplicht[$data['Zorgplicht']]=$data;
      $profielTxt.=" ".strtolower($data['Omschrijving'])." van ".$data['Minimum']."%-".$data['Maximum']."%";
      $n++;
    }
    $profielTxt.=".\nDe huidige invulling is als volgt:\n";
		
		$startY=$this->pdf->getY();
		$this->pdf->row(array('Op basis van de inventarisatie is gekozen voor een portefeuilleprofiel '.$crmData['Risicoklasse'].'. '.$profielTxt));
		$this->pdf->ln();
		$this->printRisico();
		$this->pdf->ln();
		$this->pdf->SetWidths(array(150));
		$this->pdf->SetFont($this->pdf->rapport_font, '',$this->fontsize);
		$this->pdf->setAligns(array('L'));
    $profielData=$this->scenarioProfieldata;
		$this->pdf->row(array('Het lange termijn verwachte rendement van dit profiel bedraagt '.$this->formatGetal(($profielData['verwachtRendement']-1)*100,1).'% met een hierbij behorende standaarddeviatie van '.$this->formatGetal($profielData['klasseStd']*100,1).'%.'));
    
    $this->printPERFG($startY);
    
    
	}
	
	function scenarioBerekening()
	{
		global $__appvar;
    $DB=new DB();
    
    $query="
      SELECT
        CRM_naw.id,
        CRM_naw.id,
        Portefeuilles.Risicoklasse,
        Portefeuilles.Vermogensbeheerder,
        laatstePortefeuilleWaarde.laatsteWaarde
      FROM
        CRM_naw
      JOIN Portefeuilles on
        CRM_naw.portefeuille=Portefeuilles.Portefeuille
      LEFT JOIN laatstePortefeuilleWaarde on
        CRM_naw.portefeuille=laatstePortefeuilleWaarde.Portefeuille
      WHERE
        CRM_naw.portefeuille='".$this->portefeuille."'";
    
    $DB->SQL($query);
    $crmData=$DB->lookupRecord();
    
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
    

    $scFirst= new scenarioBerekening($crmData['id'],$crmData['Risicoklasse']);
    
    if($scFirst->CRMdata['doelvermogen']==0 && $this->gewenstRisicoprofiel=='')
    {
      $scFirst= new scenarioBerekening($crmData['id'],$this->gewenstRisicoprofiel);
      
      //if($this->pdf->lastPOST['scenario_portefeuilleWaardeGebruik']==1 )
      //{
        $scFirst->CRMdata['startvermogen']=$totaalWaarde;
        $scFirst->CRMdata['startdatum']=$this->rapportageDatum;
      //}
      /*
      if (!$sc->loadMatrix())
        $sc->createNewMatix(true);
      $sc->overigeRisicoklassen();
      $kansData=$sc->berekenKansBijOpgehaaldeRisicoklassen();
     // listarray($kansData);
      if(count($kansData['beste'])>0)
      {
        $besteProfiel = $kansData['beste'];
      }
      $sc= new scenarioBerekening($crmId['id'],$besteProfiel['risicoklasse']);
      */
      if (!$scFirst->loadMatrix())
        $scFirst->createNewMatix(true);
      $scFirst->berekenSimulaties(0, 10000);
      $scFirst->berekenDoelKans();
      $scFirst->berekenVerdeling();
      $doelvermogen=$scFirst->verwachteWaarden['Normaal'];
      $scFirst= new scenarioBerekening($crmData['id'],$this->gewenstRisicoprofiel);
      $scFirst->CRMdata['doelvermogen']=$doelvermogen;
    }
    
    $scFirst->CRMdata['startvermogen']=$totaalWaarde;
    $scFirst->CRMdata['startdatum']=$this->rapportageDatum;
    
    if (!$scFirst->loadMatrix())
      $scFirst->createNewMatix(true);

    $scFirst->overigeRisicoklassen();
    
    /*

*/
   
    
    $sc= new scenarioBerekening($crmData['id'],$crmData['Risicoklasse']);
    $sc->CRMdata['startvermogen']=$totaalWaarde;
    $sc->CRMdata['startdatum']=$this->rapportageDatum;
    if($sc->CRMdata['doelvermogen']==0 && $doelvermogen<>0)
      $sc->CRMdata['doelvermogen']=$doelvermogen;
    
    if (!$sc->loadMatrix())
      $sc->createNewMatix(true);
    $sc->berekenSimulaties(0, 10000);
    $sc->berekenDoelKans();
    $sc->berekenVerdeling();
    
    
    $profielData=$sc->profieldata;
    $this->scenarioProfieldata=$profielData;
    return array($scFirst,$sc);
  }

  
  function scenarioPagina2($scFirst,$sc)
  {
  
    $ystart=30;
    $kansData=$scFirst->berekenKansBijOpgehaaldeRisicoklassen();
  
    $grafiekData=$kansData['grafiekData'];
    if(count($kansData['beste'])>0)
    {
      $besteProfiel=$kansData['beste'];
    }
    else
    {
      $besteProfiel=$kansData['maxKans'];
    }
  
    $this->pdf->setXY(160,130-5);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(115,0,vertaalTekst('Kans op behalen doelstelling bij diverse profielen',$this->pdf->rapport_taal),0,0,'C');
    $this->pdf->setXY(170,130);
    $this->scatterplot(115,50,$grafiekData,$scFirst->profieldata['maximaalRisicoprofielStdev'],$besteProfiel);
    //$this->pdf->setXY(168,108);
    $this->pdf->setXY($this->pdf->marge,$ystart);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->setDrawColor($this->pdf->rapportLineColor[0],$this->pdf->rapportLineColor[1],$this->pdf->rapportLineColor[2]);
    $this->pdf->Cell(160,0,'',0,0,'L');
    $this->pdf->Cell(115,0,vertaalTekst('Kans op behalen doelstelling bij diverse profielen',$this->pdf->rapport_taal),0,0,'L');
    $this->pdf->ln(6);
    $widths=$this->pdf->widths;
    $this->pdf->widthB = array(160,22,20,23,23,23);
    $this->pdf->alignB = array('L','L','R','R','R','R','R');
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->CellBorders = array('',array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'));
  
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',
                      vertaalTekst('Risicoprofiel',$this->pdf->rapport_taal),
                      vertaalTekst('Kans op doel',$this->pdf->rapport_taal),
                      vertaalTekst('Pessimistisch',$this->pdf->rapport_taal),
                      vertaalTekst('Normaal',$this->pdf->rapport_taal),
                      vertaalTekst('Optimistisch',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
    foreach($kansData['risicoklassen'] as $risicoklasse=>$klasseData)
    {
    
      $this->pdf->row(array('',vertaalTekst($risicoklasse,$this->pdf->rapport_taal),
                        $this->formatGetal($klasseData['uitkomstKans']['kans'],0).'%',
                        $this->formatGetalNegatief($klasseData['uitkomstKans']['scenarioEindwaarden']['Pessimistisch']),
                        $this->formatGetalNegatief($klasseData['uitkomstKans']['scenarioEindwaarden']['Normaal']),
                        $this->formatGetalNegatief($klasseData['uitkomstKans']['scenarioEindwaarden']['Optimistisch'])));
    
    }
    $this->pdf->CellBorders = array('',array('T'),array('T'),array('T'),array('T'),array('T'));
    $this->pdf->row(array('','','','','',''));
    unset($this->pdf->CellBorders);
    $this->pdf->SetWidths($widths);
    
    
    
    
    
    $this->pdf->widthA = array(40,30,20);
    $this->pdf->alignA = array('L','R','R');
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    $this->pdf->setXY($this->pdf->marge,$ystart);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array(array('T','U'),array('T','U'));
    $this->pdf->row(array(vertaalTekst('Uitgangswaarden',$this->pdf->rapport_taal),''));
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst('Beginwaarde',$this->pdf->rapport_taal),"€ ".$this->formatGetal($sc->CRMdata['startvermogen'])));
    $this->pdf->row(array(vertaalTekst('Scenario-vermogen',$this->pdf->rapport_taal),"€ ".$this->formatGetal($sc->CRMdata['doelvermogen'])));
    $this->pdf->row(array(vertaalTekst('Startjaar',$this->pdf->rapport_taal),substr($sc->CRMdata['startdatum'],0,4)));
    $this->pdf->row(array(vertaalTekst('Doeljaar',$this->pdf->rapport_taal),substr($sc->CRMdata['doeldatum'],0,4)));
    $this->pdf->row(array(vertaalTekst('Berekend profiel',$this->pdf->rapport_taal),vertaalTekst($sc->CRMdata['gewenstRisicoprofiel'],$this->pdf->rapport_taal)));
    $this->pdf->row(array(vertaalTekst('Maximaal risicoprofiel',$this->pdf->rapport_taal),vertaalTekst($sc->CRMdata['maximaalRisicoprofiel'],$this->pdf->rapport_taal)));
    $this->pdf->row(array(vertaalTekst('Verwacht rendement',$this->pdf->rapport_taal),$this->formatGetal(($sc->profieldata['verwachtRendement']-1)*100,1).'%'));
    $this->pdf->CellBorders = array(array('U'),array('U'));
    $this->pdf->row(array(vertaalTekst('Standaarddeviatie',$this->pdf->rapport_taal),$this->formatGetal($sc->profieldata['klasseStd']*100,1).'%'));
    unset($this->pdf->CellBorders);
    $uitgangsWaardenY=$this->pdf->getY();
  
  
    $indexatie=false;
    foreach($sc->cashflowText as $bedragData)
      if($bedragData[2] <> '')
        $indexatie=true;
  
    if($indexatie)
      $this->pdf->widthB = array(75,20,25,15);
    else
      $this->pdf->widthB = array(100,20,25,2);
    $this->pdf->alignB = array('L','L','R','R','L');
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
 //   $this->pdf->setXY($this->pdf->marge,$ystart);
    $this->pdf->CellBorders = array('',array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'));
    //$this->pdf->row(array('',vertaalTekst('Cashflow',$this->pdf->rapport_taal)));
    $this->pdf->setXY($this->pdf->marge,$ystart);
    if($indexatie)
      $this->pdf->row(array('',vertaalTekst('Cashflow',$this->pdf->rapport_taal),
                        vertaalTekst('Bedrag in',$this->pdf->rapport_taal).' €',
                        vertaalTekst('Index%',$this->pdf->rapport_taal)));
    else
      $this->pdf->row(array('',vertaalTekst('Cashflow',$this->pdf->rapport_taal),
                        vertaalTekst('Bedrag in',$this->pdf->rapport_taal).' €'));
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($sc->cashflowText as $bedragData)
    {
      $this->pdf->row(array('',$bedragData[0],$this->formatGetal($bedragData[1],0),$bedragData[2]));
    }
    $this->pdf->CellBorders = array('','T','T','T');
    $this->pdf->row(array('','','',''));
    unset($this->pdf->CellBorders);
  
    //$this->pdf->setXY($this->pdf->marge,$ystart);
    $this->pdf->setXY($this->pdf->marge,$uitgangsWaardenY+5);
    $this->pdf->widthB = array(30,30,30);
    $this->pdf->alignB = array('L','R','R');
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array(array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'));
    $this->pdf->row(array(vertaalTekst('Scenario',$this->pdf->rapport_taal).' '.vertaalTekst($sc->CRMdata['gewenstRisicoprofiel'],$this->pdf->rapport_taal),
                      vertaalTekst('Kans op eindvermogen',$this->pdf->rapport_taal),
                      vertaalTekst('Bedrag eindvermogen',$this->pdf->rapport_taal)));
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($sc->verwachteWaarden as $scenario=>$eindvermogen)
    {
      //$kleur=$this->scenarioKleur[$scenario];
      //$this->pdf->Rect($this->pdf->getX()+145-3,$this->pdf->GetY()+1, 2, 2 ,'F','',$kleur);
      $this->pdf->row(array(vertaalTekst($scenario,$this->pdf->rapport_taal),$this->formatGetal( round((100-$sc->scenarios[$scenario])/5)*5,0).'%',$this->formatGetalNegatief($eindvermogen)));
    }
    $this->pdf->CellBorders = array('T','T','T');
    $this->pdf->row(array('','',''));
    unset($this->pdf->CellBorders);
  
    $beginWaarde=0;
    $reeks=array();
    foreach($sc->scenarioGemiddelde as $scenario=>$jaarData)
    {
      if($scenario=='Pessimistisch'||$scenario=='Normaal'||$scenario=='Optimistisch')
      {
        foreach ($jaarData as $jaar => $waarde)
        {
          if ($jaar == 0)
          {
            $beginWaarde = $waarde;
          }
          $reeks[$scenario][$jaar] = $waarde / $beginWaarde * 100;
        }
      }
    }
    $this->scenarioKleur=$sc->scenarioKleur;
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->setXY($this->pdf->marge+15,135);
    $this->pdf->SetWidths(array(125));
    $this->pdf->SetAligns(array('C'));
    $this->pdf->row(array(vertaalTekst('Scenarioanalyse',$this->pdf->rapport_taal)));//.' '.vertaalTekst($sc->CRMdata['gewenstRisicoprofiel'],$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setXY($this->pdf->marge+15,140);
    $this->startJaar=$sc->CRMdata['startdatum'];
    $this->LineDiagram(125,40,$sc->scenarioGemiddelde,'',$sc->CRMdata['doelvermogen']);
   // $this->LineDiagram(140,40, $reeks);
 //   listarray($reeks);
    
  }
	
	function scenarioanalyse($scenario)
	{
	  
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->rapport_titel='Scenarioanalyse';
		/*
		$this->pdf->addPage();
		$this->pdf->SetWidths(array(150));
		$this->pdf->SetFont($this->pdf->rapport_font, '',$this->fontsize);
		$this->pdf->setAligns(array('L'));
		$this->pdf->row(array('De hele dag door nemen mensen beslissingen; op het werk, thuis of waar dan ook. De ene beslissing wordt genomen zonder erbij na te denken, terwijl de andere beslissing denkwerk vereist, vergelijking en analyse. De scenarioanalyse is een beslissingsmethode, die ons helpt om de juiste beslissing te nemen en er vooraf over na te denken welk effect het genomen besluit zal bewerkstelligen. Door de verschillende uitkomsten onder de loep te nemen, kan de beste oplossing weloverwogen gekozen worden'));
    $this->pdf->ln();
		$this->pdf->SetFont($this->pdf->rapport_font, 'B',$this->fontsize);
		$this->pdf->row(array('Inzicht en overzicht in risico en rendement'));
		$this->pdf->SetFont($this->pdf->rapport_font, '',$this->fontsize);
		$this->pdf->row(array('De scenarioanalyse geeft inzicht in risico, rendement en de haalbaarheid van financiële doelen. Hoe zullen activa zich ontwikkelen in verschillende en vaak vluchtige economische omstandigheden? Welke stappen moeten worden genomen om een financieel plan realistisch te maken? Om de meest realistische resultaten te bereiken, kan men ook rekening houden met inflatierisico, valutarisico\'s en verschillende beleggingsstrategieën. U krijgt inzicht in het effect van hun investeringsbeslissingen, veranderingen in hun financiële situatie en marktontwikkelingen. De essentie van een scenarioanalyse is om te komen tot uiteenlopende toekomst uitkomsten. De methode berekent het effect van een combinatie van mogelijke vermogensontwikkelingen. Door een neutraal, best-case en een worst-case scenario te berekenen is het mogelijk de bandbreedte van de verwachte vermogensontwikkeling te bepalen.'));
    $this->pdf->ln();
    $this->pdf->row(array('De portefeuille wordt bijna geheel in zakelijke waarden belegd, zodat het risicoprofiel van dit Vermogensplan hoog is. Normaal gesproken wordt 0% in obligaties belegd. Indien bijvoorbeeld de onzekerheid in de aandelenmarkt oploopt, kan dit wel gedaan worden tot maximaal 10%. Het accent in dit Vermogensplan ligt op groei van de waarde. De kans op negatieve rendementen in een slechter beleggingsjaar wordt geaccepteerd en de beleggingshorizon is in het algemeen zeker 12 jaar. U heeft gekozen voor vermogensplan geel. De bandbreedte voor de vastrentende waarden is 30% - 50%, voor de zakelijke waarden is de bandbreedte is 50% - 70%. '));
*/

	
		$this->pdf->addPage();
	  $this->scenarioPagina2($scenario[0],$scenario[1]);
		$this->pdf->SetFont($this->pdf->rapport_font, '',$this->fontsize);
	}
	
	function risicoMeter()
  {
  
    $this->pdf->rapport_titel = "Risicometer - Veel gestelde vragen over de kenmerken van de Risicometer Beleggen";
    $this->pdf->AddPage();
    $this->pdf->SetWidths(array(6,250));
    $this->pdf->SetAligns(array('L', 'L','L'));
    $teksten=array('Voor wie is de Risicometer Beleggen ontwikkeld?'=>'Voor consumenten die beleggen of overwegen dit te doen.',
                   'Waarom is de Risicometer Beleggen ontwikkeld?'=>'Voor de belegger is het lastig om de verschillende risicoprofielen (bijvoorbeeld een defensief of offensief profiel) van de verschillende aanbieders met elkaar te vergelijken op risico. Elke aanbieder heeft immers haar eigen systematiek van risicoprofielen en haar eigen uitleg daarbij. De Risicometer Beleggen brengt hier verandering in. Deze moet beleggers helpen bij het onderling vergelijken van risicoprofielen',
                   'Door wie is de Risicometer Beleggen ontwikkeld?'=>'De Risicometer Beleggen is een gezamenlijk initiatief van een aantal aanbieders vertegenwoordigd in de Nederlandse Vereniging van Banken (NVB).',
                   'Hoe meet de Risicometer Beleggen het risico?'=>'Het meten van het risico vindt plaats op basis van volatiliteit. Dit is de mate waarin de waarde van een portefeuille van beleggingen schommelt. Volatiliteit is een veel gebruikte maatstaf voor risico. De Risicometer Beleggen geeft een indicatie van de mate van volatiliteit op een schaal van 1 tot 7, van een beleggingsportefeuille die past bij een bepaald risicoprofiel.',
                   'Hoe wordt de volatiliteit berekend?'=>'Berekening vindt plaats op basis van door de VBA Beleggingsprofessionals aangeleverde historische gegevens. Daarmee is de Risicometer Beleggen een schatting van de volatiliteit op basis van historische gegevens. Elk jaar wordt bekeken of aanpassing noodzakelijk is.',
                   'Wat ‘doet’ de Risicometer Beleggen niet?'=>'De Risicometer Beleggen is niet zondermeer toepasbaar op werkelijke klantportefeuilles. De Risicometer Beleggen meet niet alle vormen van risico. Zo wordt geen rekening gehouden met het kredietrisico en het liquiditeitsrisico.',
                   'Betekent de introductie van de Risicometer Beleggen ook dat de aanbieders in het vervolg de volatiliteit van de individuele klantportefeuille op basis van de Risicometer gaan bewaken?'=>'Nee, de Risicometer Beleggen is niet gericht op individuele klantportefeuilles. De werkelijke volatiliteit van een individuele klantportefeuille kan dan ook hiervan afwijken.',
                   'Is het aanbieden van de Risicometer Beleggen verplicht?'=>'Aanbieders zijn niet verplicht om de Risicometer Beleggen te gebruiken.');
  
    $this->pdf->rowHeight=$this->rowHeightBackup;
    foreach ($teksten as $kop=>$tekst)
    {
      $this->pdf->memImage($this->check,$this->pdf->getX()+2,$this->pdf->getY(),3);
      $this->pdf->SetFont($this->pdf->rapport_font, 'I', $this->pdf->rapport_fontsize);
      $this->pdf->row(array('',$kop));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->row(array('',$tekst));
      $this->pdf->ln();
    }
    $this->pdf->rowHeight=$this->rowHeightHigh;
  }
	
	function printRisico($viaVar=false)
	{
		
    $query = "SELECT  Portefeuille,Risicoklasse, Client,Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille = '" . $this->portefeuille . "' ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $portefeuilledata = $DB->nextRecord();
    
    $zorgplicht=new Zorgplichtcontrole();
    $zpwaarde=$zorgplicht->zorgplichtMeting($portefeuilledata,$this->rapportageDatum );
    $this->pdf->setDrawColor($this->pdf->rapportLineColor[0],$this->pdf->rapportLineColor[1],$this->pdf->rapportLineColor[2]);
    //listarray($zpwaarde);
    $this->pdf->SetWidths(array(10,40,35,35));
    $this->pdf->SetAligns(array('C','L', 'R', 'R'));
    $this->pdf->CellBorders = array('','T','T','T');
    $this->pdf->row(array('','','',''));
    $this->pdf->ln(-4);
    $this->pdf->CellBorders =array();
    
    $this->pdf->row(array('','Mandaat controle','% actueel',''));
    $this->pdf->ln(-4);
    $this->pdf->CellBorders = array('','U','U','U');
    $this->pdf->row(array('','','',''));
    $this->pdf->CellBorders =array();
    $this->pdf->ln(1);
    foreach($zpwaarde['conclusieDetail'] as $categorie=>$details)
    {
      $this->pdf->row(array('',$categorie,$this->formatGetal($details['percentage'],0).'%',''));
      if($zpwaarde['voldoet'] =='Ja')
        $this->pdf->memImage($this->checkPng,$this->pdf->getX()+100,$this->pdf->getY()-$this->pdf->rowHeight,4);
      $this->pdf->ln(1);
    }
    $this->pdf->CellBorders = array('','T','T','T');
    $this->pdf->row(array('','','',''));
    unset($this->pdf->CellBorders);
	}
	
	function printPERFG($startY)
	{
    
    $perfg=new RapportPERFG_L91($this->pdf, $this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
    
    $perfgData=$perfg->gatherData('VAR');
    $color=array($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $aantalJaren=count($perfgData['grafiekData']['jaren']['datum']);
    

    $grafiekData=$perfgData['grafiekData'];
    $indexTabel=$perfgData['indexTabel'];
    
      $this->pdf->CellBorders=array();
      $this->pdf->setY($startY);
      $color=array($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
//$this->pdf->CellBorders = array(array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'));
      $this->pdf->CellBorders = array('','U','U','U','U','U','U','U','U');
      $this->pdf->setWidths(array(155, 17, 18, 18, 18, 18, 18, 18));
      $this->pdf->setAligns(array('C','L','R','R','R','R','R','R','R'));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      if(count($grafiekData['jaren']['datum'])>0)
        $this->pdf->Row(array('','Periode', 'Begin', 'Stortingen', 'Onttrekking', 'Resultaat', 'Eind','Per jaar'));

//$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      unset($this->pdf->CellBorders);
      $som=array();
      $aantal=count($grafiekData['jaren']['datum']);
      $begin=0;
      /*
      if($aantal>4)
      {
        $begin=$aantal-5;
      }
    */
    for($i=$begin;$i<$aantalJaren;$i++)
    {
      $jaarData=$perfgData['grafiekData']['jaren']['waarde'][$i];
      $indexTabel=$perfgData['indexTabel'];
      $datum=$perfgData['grafiekData']['jaren']['datum'][$i];
      $this->pdf->Row(array('',
                        $datum,
                        $this->formatGetal($jaarData['waardeBegin'],0),
                        $this->formatGetal($jaarData['stortingen'],0),
                        $this->formatGetal($jaarData['onttrekkingen'],0),
                        $this->formatGetal($jaarData['waardeHuidige']-$jaarData['waardeBegin']+$jaarData['onttrekkingen']-$jaarData['stortingen'],0),
                        $this->formatGetal($jaarData['waardeHuidige'],0),
                        $this->formatGetal($indexTabel[$datum]['portefeuille']['jaar']-100,1)."%"
                      ));
      $som['stortingen']+=$grafiekData['jaren']['waarde'][$i]['stortingen'];
      $som['onttrekkingen']+=$grafiekData['jaren']['waarde'][$i]['onttrekkingen'];
    }
    /*
      foreach($grafiekData['jaren']['datum'] as $i=>$datum)
      {
        //if($i==$aantal)
        //  $this->pdf->CellBorders = array('','','US','US','','','','');
        
        $this->pdf->Row(array('',$datum,
                          $this->formatGetal($grafiekData['jaren']['waarde'][$i]['waardeBegin'],0,false),
                          $this->formatGetal($grafiekData['jaren']['waarde'][$i]['stortingen'],0,false),
                          $this->formatGetal($grafiekData['jaren']['waarde'][$i]['onttrekkingen'],0,false),
                          $this->formatGetal($grafiekData['jaren']['waarde'][$i]['waardeHuidige']-$grafiekData['jaren']['waarde'][$i]['waardeBegin']+$grafiekData['jaren']['waarde'][$i]['onttrekkingen']-$grafiekData['jaren']['waarde'][$i]['stortingen'],0,false),
                          $this->formatGetal($grafiekData['jaren']['waarde'][$i]['waardeHuidige'],0,false),
                          $this->formatGetal($indexTabel[$datum]['portefeuille']['jaar']-100,1)."%",
                          $this->formatGetal($indexTabel[$datum]['portefeuille']['cumulatief']-100,1)."%"
                        ));

      }
      */
     // $this->pdf->ln();
      $this->pdf->CellBorders = array('','','','UU','UU');
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->Row(array('','','',$this->formatGetal($som['stortingen'],0,true),$this->formatGetal($som['onttrekkingen'],0,true),'','','',''));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->CellBorders = array('','U','U','U','U','U','U','U','U');
      $this->pdf->Row(array('','','','','','','','',''));
      $this->pdf->CellBorders = array();
      
      if(count($grafiekData['kwartalen']['datum'])>0)
      {
//$this->pdf->CellBorders = array(array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'));
        $this->pdf->CellBorders = array('','U', 'U', 'U', 'U', 'U', 'U', 'U', 'U');
        $this->pdf->setWidths(array(155, 13, 18, 18, 20, 17, 18, 15, 17));
        $this->pdf->setAligns(array('C','L', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
//$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
        $this->pdf->Row(array('','periode', 'begin', 'stortingen', 'onttrekking', 'resultaat', 'eind', 'per kwartaal', 'cumulatief'));
        unset($this->pdf->CellBorders);
//$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
        foreach ($grafiekData['kwartalen']['datum'] as $i => $datum)
        {
          $this->pdf->Row(array('',$datum,
                            $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['waardeBegin'], 0),
                            $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['stortingen'], 0),
                            $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['onttrekkingen'], 0),
                            $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['waardeHuidige'] - $grafiekData['kwartalen']['waarde'][$i]['waardeBegin'] + $grafiekData['kwartalen']['waarde'][$i]['onttrekkingen'] - $grafiekData['kwartalen']['waarde'][$i]['stortingen'], 0),
                            $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['waardeHuidige'], 0),
                            $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['performance'], 1) . "%",
                            $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['index'] - 100, 1) . "%"));
        }
        $this->pdf->CellBorders = array();
      }
////

      $this->pdf->setWidths(array(155+5, 120));
      $this->pdf->ln(10);
    $this->pdf->SetFont($this->pdf->rapport_font, '',$this->fontsize);
      $this->pdf->Row(array('','Werkelijke ontwikkeling van uw beleggingsportefeuille'));
      $this->pdf->setXY(168,$this->pdf->getY()+52);
      if(count($grafiekData['jaren']['datum'])>0)
        $perfg->VBarDiagram(120,50,$grafiekData['jaren'],'',$color);
    
	}
  function formatGetalNegatief($waarde, $dec)
  {
    if($waarde<0)
      return 'Negatief!';
    else
      return number_format($waarde,$dec,",",".");
  }
  
  
  
  function LineDiagram($w, $h, $data,$werkelijkVerloop,$doelVermogen)
  {
    global $__appvar;
    $color=null; $maxVal=0; $minVal=10000000; $horDiv=5; $verDiv=4;$jaar=0;
    
    $XPage = $this->pdf->GetX();
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
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
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
    
    
    
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }
  function LineDiagramVerdeling($w, $h, $data,$werkelijkVerloop,$doelVermogen)
  {
    global $__appvar;
    $color=null; $maxVal=0; $minVal=0; $horDiv=5; $verDiv=4;
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage  ;
    $lDiag = $w;
    
    if(is_array($color[0]))
    {
      $color = $color[0];
    }
    
    if($color == null)
      $color=array(116,95,71);
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    
    $aantalPunten=array();
    $reeksen=array();
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
      
      $reeksen[$reeks]=$reeks;
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
  
    $maxVal=ceil($maxVal/50)*50;
    $procentWhiteSpace = 0.1;
    $band=($maxVal - $minVal);
    $stepSize=round($band / $horDiv);
    //echo $band;exit;
    
    //$stepSize=ceil($stepSize/(pow(10,strlen($stepSize))*5))*pow(10,strlen($stepSize))/5;
   // $maxVal = ceil($maxVal * (1 + ($procentWhiteSpace))/$stepSize)*$stepSize;
   // $minVal = floor($minVal * (1 - (0.3))/$stepSize)*$stepSize;
    
   
    $verInterval = ($lDiag / $verDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / (count($aantalPunten)-1);
    
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
    $this->pdf->TextWithRotation($XDiag-7,$YDiag+$h/2+4,'Waarde',90);
    
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      //$this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ."");
      
      $this->pdf->setXY($XDiag-5, $i);
      if($n==0)
        $waarde=$minVal;
      else
        $waarde=0-($n*$stapgrootte);
      
      $this->pdf->Cell(5,0, $this->formatGetal($waarde,0)."", 0,0, "R");
      
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
          $this->pdf->setXY($XDiag-5, $i);
          $this->pdf->Cell(5,0, $this->formatGetal($n*$stapgrootte,0)."", 0,0, "R");
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
            $this->pdf->TextWithRotation($XDiag+($i*$unit)-2,$YDiag+$hDiag+3,$this->startJaar+$i,0);
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
    }
    
    
    foreach($lines as $reeks=>$lineData)
    {
      $color=array($this->scenarioKleur[$reeks][0],$this->scenarioKleur[$reeks][1],$this->scenarioKleur[$reeks][2]);
      $lineStyle = array('width' => 0.8, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
      foreach($lineData as $line)
      {
        $this->pdf->line($line[0],$line[1],$line[2],$line[3],$lineStyle);
      }
    }
  
    $this->pdf->TextWithRotation($XDiag+$w/2-5,$YDiag+$h+6,'Jaren',0);
    
    $i=0;
    
    $cellW=30;
    $extraX=$w/2 - $cellW*count($reeksen)/2 ;
    foreach($reeksen as $reeks)
    {
      $color=array($this->scenarioKleur[$reeks][0],$this->scenarioKleur[$reeks][1],$this->scenarioKleur[$reeks][2]);
  
      $this->pdf->setXY($XDiag+$i*$cellW+$extraX,$YDiag+$h+10);
      $this->pdf->Cell($cellW,0, $reeks, 0,0, "L");
      $this->pdf->Rect($XDiag+$i*$cellW-3+$extraX, $YDiag+$h+9,2 ,2,'F','',$color);
      $i++;
    }
    
    
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }
  
  function scatterplot($w, $h, $data,$maxStdev=25,$beste)
  {
    global $__appvar;
    $color=null; $maxVal=0; $minVal=0; $horDiv=4; $verDiv=4;$jaar=0;
    
    $minXVal=0; $maxXVal=25;
    $minYVal=0; $maxYVal=100;
    
    $XPage = $this->pdf->GetX();
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
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
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
    return $beste;
  }

}
?>