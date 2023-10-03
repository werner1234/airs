<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2012/05/23 15:57:43 $
File Versie					: $Revision: 1.4 $

$Log: Factuur_L26.php,v $
Revision 1.4  2012/05/23 15:57:43  rvv
*** empty log message ***

Revision 1.3  2012/05/19 10:49:55  rvv
*** empty log message ***

Revision 1.2  2010/07/21 17:49:59  rvv
*** empty log message ***

Revision 1.1  2010/07/21 17:37:57  rvv
*** empty log message ***


*/

global $__appvar;

/*
$db=new DB();
$query="SELECT Portefeuilles.*, Vermogensbeheerders.* FROM Portefeuilles Join Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder WHERE Portefeuilles.portefeuille='".$this->waarden['portefeuille']."'";
$db->SQL($query);
$portefeuilleData=$db->lookupRecord();
listarray($portefeuilleData);
*/
    $this->pdf->rowHeight = 5;
   	$this->pdf->underlinePercentage=0.8;
    $this->pdf->brief_font='Times';
		$this->pdf->rapport_type = "FACTUUR";
		$this->pdf->AddPage('P');
		$this->pdf->SetFont($this->pdf->rapport_font,'',10);

		$vanaf=db2jul($this->waarden['datumVan']);
		$tot=db2jul($this->waarden['datumTot']);

    if(is_file($this->pdf->rapport_logo))
		{
			$this->pdf->Image($this->pdf->rapport_logo, 0, 10, 108, 15);
		}
		$kwartalen = array('null','eerste','tweede','derde','vierde');

    $this->pdf->SetY(20);
	  $this->pdf->SetWidths(array(120,100));
	  $this->pdf->SetAligns(array('R','L'));
    $this->pdf->row(array('','Breda, '.(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y")));
    $this->pdf->ln();
    $this->pdf->row(array('','Liesbeth van Herk'));
    $this->pdf->ln();
    $this->pdf->row(array('','+31 (0)76 820 02 97'));
    $this->pdf->ln();
    $this->pdf->row(array('','1/1'));
    $this->pdf->ln();
    $this->pdf->row(array('','Performance Rapportage '.$kwartalen[$this->waarden['kwartaal']].' kwartaal'));


		$this->pdf->SetY(50);
		$this->pdf->SetWidths(array(22,150));
	  $this->pdf->SetAligns(array('R','L'));

		$this->pdf->row(array('',$this->waarden['clientNaam']));
		if($this->waarden['clientNaam1'] <> '')
		  $this->pdf->row(array('',$this->waarden['clientNaam1']));
		$this->pdf->row(array('',$this->waarden['clientAdres']));
		if($this->waarden['clientPostcode'] != '')
	  	$plaats = $this->waarden['clientPostcode'] . " " .$this->waarden['clientWoonplaats'];
	  else
	  	$plaats = $this->waarden['clientWoonplaats'];
		$this->pdf->row(array('',$plaats));
		$this->pdf->row(array('',$this->waarden['clientLand']));

		$this->pdf->SetY(90);
		$this->pdf->SetAligns(array('R','C'));

		$factuurNr=sprintf("%03d",$this->waarden['factuurNummer']);
		$this->pdf->row(array('',"Factuur Beheerloon: Rekening ".$this->waarden['portefeuille'].", ".$kwartalen[$this->waarden['kwartaal']]." kwartaal ".date("Y",$tot)));
		$this->pdf->row(array('',"Factuurnummer: ".date("Y",$tot).".$factuurNr"));
		$this->pdf->SetAligns(array('R','L'));


		$this->pdf->ln();


		$this->pdf->row(array('',"".$this->waarden['CRM_verzendAanhef'].","));
		$this->pdf->ln();

		$vanafTxt=date("d",$vanaf)." ".vertaalTekst($__appvar["Maanden"][date("n",$vanaf)],$pdf->rapport_taal)." ".date("Y",$vanaf);
		$totTxt=date("d",$tot)." ".vertaalTekst($__appvar["Maanden"][date("n",db2jul($tot))],$pdf->rapport_taal)." ".date("Y",$tot);
		$rapportagePeriode = $vanafTxt.' t/m '.$totTxt;
//listarray($this->waarden);

	$this->pdf->SetAligns(array('R','L'));
	$this->pdf->row(array('','Wij hebben voor u het beheerloon berekend over het '.$kwartalen[$this->waarden['kwartaal']].' kwartaal.' ));
	$this->pdf->ln();
	$this->pdf->row(array('','Het beheerloon bedraagt '.$this->formatGetal($this->waarden['BeheerfeePercentageVermogen'],2).'% per jaar, dat wil zeggen '.$this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'],2).'% voor dit kwartaal van het gemiddelde vermogen dat u in deze periode aanhield op rekening '.$this->waarden['portefeuille'].'.'));
  $this->pdf->ln();
	$this->pdf->row(array('','Het gemiddeld vermogen is berekend door de maandultimo standen te middelen.' ));
	$this->pdf->ln();
	$this->pdf->SetWidths(array(22,80,30,50));
	$this->pdf->SetAligns(array('R','L','R'));
	$this->pdf->row(array('','Vermogen Maandultimo1 ',$this->formatGetal($this->waarden['maandsWaarde_1'],0)." EUR"));
	$this->pdf->row(array('','Vermogen Maandultimo2 ',$this->formatGetal($this->waarden['maandsWaarde_2'],0)." EUR"));
	$this->pdf->row(array('','Vermogen Maandultimo3 ',$this->formatGetal($this->waarden['maandsWaarde_3'],0)." EUR"));
	$this->pdf->CellBorders = array('','','T');
	$this->pdf->row(array('','Gemiddeld vermogen:',$this->formatGetal($this->waarden['maandsGemiddelde'],0)." EUR"));
	$this->pdf->CellBorders = array();
	$this->pdf->ln();
	$this->pdf->SetWidths(array(22,100,30,50));
  $this->pdf->row(array('','Het beheerloon op rekening : '.$this->waarden['portefeuille'].' bedraagt:',$this->formatGetal($this->waarden['beheerfeeBetalen'],2)." EUR"));
  $this->pdf->ln();
  	$this->pdf->CellBorders = array('','','U');
  $this->pdf->row(array('','BTW '.$this->waarden['btwTarief'].'% (BTW nr 821893397B01)',$this->formatGetal($this->waarden['btw'],2)." EUR"));
  $this->pdf->CellBorders = array();
  $this->pdf->ln();
  $this->pdf->row(array('','Totaal Factuur bedrag',$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)." EUR"));
  $this->pdf->ln(12);
  $this->pdf->SetWidths(array(22,150));
	$this->pdf->row(array('',"Dit bedrag zal binnenkort van uw rekening worden afgeschreven."));
	$this->pdf->ln();
	$this->pdf->row(array('',"Als u vragen heeft over deze factuur, horen wij dat graag."));
	$this->pdf->ln(8);
	$this->pdf->row(array('',"Hoogachtend,"));
	$this->pdf->ln(15);
	$this->pdf->SetWidths(array(22,60,60));
   $this->pdf->row(array('','Liesbeth van Herk','Dirk Spaander'));


    $this->pdf->SetTextColor(0,0,0);

    ?>
