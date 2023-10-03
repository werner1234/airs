<?php


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportEND_L126
{
	function RapportEND_L126($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "END";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Begrippen en verklaringen";
    $this->DB=new DB();
		$this->portefeuille = $portefeuille;
		$this->zonderBegrippen = false;
    $this->check=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADQAAAAzCAMAAADvo9thAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjIzRDI2NzQyQ0U3QzExRUFBQTUyQkZGMUEzMDAxRkI3IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjIzRDI2NzQzQ0U3QzExRUFBQTUyQkZGMUEzMDAxRkI3Ij4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MjNEMjY3NDBDRTdDMTFFQUFBNTJCRkYxQTMwMDFGQjciIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MjNEMjY3NDFDRTdDMTFFQUFBNTJCRkYxQTMwMDFGQjciLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5caeO6AAAADFBMVEWbp8AnQngAIGD///8gq/pNAAAAbElEQVR42uzWMQ6AMAxDUSe+/50JoJZOqA4Sk70xfD06tUDuL3gPkXoFRapNKbCzxTo/xq++Lx7rkrai5Vy6VJYuZaIjwZIlS5YsWfpDEi7qKamD+o74ILEV6VZFRCciWxE6kWiNCCGMhwADAMizGIXhL/REAAAAAElFTkSuQmCC');
	}

	


	function writeRapport()
	{

    if($this->zonderBegrippen==false)
    {
      $this->pdf->AddPage();
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
      $this->pdf->SetWidths(array(120, 25, 120));
      $this->pdf->SetAligns(array('L', 'L', 'L'));
      $ystart = $this->pdf->getY();
      $teksten = array('Asset Allocatie'       => 'De verdeling binnen een beleggingsportefeuille. Bijvoorbeeld de verdeling over de vermogenscategorieën zakelijke waarden, vastrentende waarden en liquiditeiten.',
                       'Zakelijke waarden:'    => 'Zakelijke waarden is een verzamelnaam voor beleggingen in aandelen in ontwikkelde en opkomende markten, beleggingen in onroerend goed en grondstoffen. Deze beleggingen worden ook wel aangeduid als risicodragend.',
                       'Vastrentende waarden:' => 'Met vastrentende waarden worden obligaties (staatsobligaties, bedrijfsobligaties of andersoortige) bedoeld.',
                       'Koersen'               => 'De beleggingen in de portefeuille zijn gewaardeerd op basis van slotkoersen van de laatste handelsdag van de verslagperiode.',
                       'Dividend'              => 'Dividend is winstuitkering van een onderneming aan haar aandeelhouders.',
                       'Dividendbelasting'     => 'Nederland heft 15% belasting over het uitgekeerde dividend. Dit bedrag wordt automatisch ingehouden voordat het bedrijf het geld uitkeert. Dit bedrag wordt verrekend met de inkomstenbelasting.',
                       'Valuta'                => 'De geldsoort van een land, de munteenheid. Bijvoorbeeld: dollar, yen, euro, pond,kroon.');
      foreach ($teksten as $kop => $tekst)
      {
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
        $this->pdf->row(array($kop));
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
        $this->pdf->row(array($tekst));
        $this->pdf->ln();
      }
  
      $this->pdf->setY($ystart);
      $teksten = array('Standaarddeviatie'           => 'Standaarddeviatie wordt gebruikt als een maatstaf voor de risicograad van beleggingen. Het geeft de mate van afwijking van een gemiddelde weer. Risico bij beleggen is te omschrijven als de kans dat het werkelijke rendement afwijkt van het verwachte rendement. Dit kan dus zowel een lager als een hoger rendement betekenen. Een hogere standaarddeviatie geeft aan dat sprake is van een hoger risico, aangezien de afwijkingen van het gemiddelde in het verleden groter waren. Ander woorden voor standaarddeviatie zijn volatiliteit en beweeglijkheid. De rendementen van aandelen schommelen meer dan die van obligaties. Dit komt tot uitdrukking in het verschil in standaarddeviatie. De standaarddeviatie van obligaties is doorgaans lager dan die van aandelen. Naarmate de rendementen in het verleden meer schommelden, is de standaarddeviatie hoger en dat geldt daarmee ook voor het risico. De standaarddeviatie wordt berekend met behulp van historische rendementen.',
                       'Maximum Drawdown'            => 'Maximum Drawdown is de grootste procentuele daling vanaf een historische piek van de portefeuille tot de laagste waarde na die piek van de portefeuille. De `Maximum Drawdown` wordt uitgedrukt als percentage van het koersniveau waarop de grootste daling begon',
                       'Verwacht rendement per jaar' => 'Dit is het gemiddelde te verwachten rendement binnen het gekozen beleggingsprofiel, waarbij wordt uitgegaan van een beleggingspreriode van 10 jaar',
                       'Historisch risico per jaar'  => 'Hiermee wordt bedoeld de gemiddelde standaarddeviatie per jaar, gemeten over de afgelopen 10 jaar',
                       'Resultaat verslagperiode'    => 'Het nettorendement is de waardeontwikkeling van uw vermogen in de getoonde verslagperiode. Dit resultaat bestaat uit koerswinst en/of verlies op uw beleggingen, het resultaat op de valuta’s en alle rente en dividendinkomsten. Wij geven een overzicht van het resultaat van de beleggingen. Hierbij maken we onderscheid tussen de ongerealiseerde koerswinsten en de koersresultaten die echt zijn behaald bij aan en verkopen. Hierbij is rekening gehouden met stortingen en/of onttrekkingen aan uw vermogen en de gemaakte kosten.',
                       'Valutadatum'                 => 'De datum waarop de vergoeding of de betaling van rente over een bedrag ingaat.');
      foreach ($teksten as $kop => $tekst)
      {
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
        $this->pdf->row(array('', '', $kop));
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
        $this->pdf->row(array('', '', $tekst));
        $this->pdf->ln();
      }
    }
    
    $this->pdf->SetWidths(array(6,250));
    $this->pdf->SetAligns(array('L', 'L','L'));
    

    $this->pdf->rapport_type = "END";
    $this->pdf->rapport_titel = "";
	  $this->pdf->addPage('L');
    if(is_file($this->pdf->rapport_logo))
    {
          $this->pdf->Image($this->pdf->rapport_logo, $this->pdf->marge+5, 75, $this->pdf->logoXsize*1.4);
    }
    $query="SELECT Vermogensbeheerders.naam,Vermogensbeheerders.adres,
Vermogensbeheerders.woonplaats,
Vermogensbeheerders.telefoon,
Vermogensbeheerders.email,
Vermogensbeheerders.website FROM Vermogensbeheerders WHERE Vermogensbeheerders.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $this->DB->SQL($query);
    $verm = $this->DB->lookupRecord();
    $this->pdf->setY(105);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize+2);
    foreach($verm as $key=>$value)
    {
      $this->pdf->ln(1);
      if($key=='telefoon')
        $this->pdf->row(array('','Tel. '.$value));
      elseif($key=='email')
        $this->pdf->row(array('','E-mail: '.$value));
			elseif($key=='website')
      {
      	$this->pdf->setX(30);
        $this->pdf->SetTextColor($this->pdf->rapportLineColor[0],$this->pdf->rapportLineColor[1],$this->pdf->rapportLineColor[2]);
      	$this->pdf->cell(100,4,$value);
        $this->pdf->setX($this->pdf->marge);
        $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
        $this->pdf->row(array('', 'Website:'));
        
      }
      else
        $this->pdf->row(array('', $value));
  
      if($key=='woonplaats')
        $this->pdf->ln();
      
    }
    $this->pdf->AutoPageBreak=false;
    $this->pdf->setY(180);
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
    $this->pdf->row(array('', 'Disclaimer:'));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->row(array('', 'Deze Sequoia Vermogensrapportage is samengesteld door Sequoia Vermogensbeheer B.V. Er kunnen aan deze publicatie geen rechten worden ontleend.
Sequoia staat niet in voor de juistheid en volledigheid van informatie en aanvaardt daarvoor geen aansprakelijkheid.'));
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
    $this->pdf->row(array('', 'Copyright © '.date('Y').', Sequoia Vermogensbeheer B.V.'));
    $this->pdf->AutoPageBreak=true;
  }
}
?>
