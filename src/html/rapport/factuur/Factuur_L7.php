<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/05/11 16:51:23 $
File Versie					: $Revision: 1.14 $

$Log: Factuur_L7.php,v $
Revision 1.14  2019/05/11 16:51:23  rvv
*** empty log message ***

Revision 1.13  2017/10/04 16:09:48  rvv
*** empty log message ***

Revision 1.12  2017/01/16 07:02:45  rvv
*** empty log message ***

Revision 1.11  2017/01/15 11:43:57  rvv
*** empty log message ***

Revision 1.10  2017/01/07 16:23:50  rvv
*** empty log message ***

Revision 1.9  2017/01/05 10:56:40  rvv
*** empty log message ***

Revision 1.8  2017/01/05 06:38:27  rvv
*** empty log message ***

Revision 1.7  2016/10/17 08:24:04  rvv
*** empty log message ***

Revision 1.6  2016/10/12 16:27:43  rvv
*** empty log message ***

Revision 1.5  2016/10/10 06:58:07  rvv
*** empty log message ***

Revision 1.4  2016/10/09 14:42:42  rvv
*** empty log message ***

Revision 1.3  2016/10/07 10:32:07  rvv
*** empty log message ***

Revision 1.2  2016/10/05 16:17:49  rvv
*** empty log message ***

Revision 1.1  2016/10/02 12:39:39  rvv
*** empty log message ***

Revision 1.8  2012/12/12 16:53:11  rvv
*** empty log message ***

Revision 1.7  2011/04/11 19:49:19  rvv
*** empty log message ***

Revision 1.6  2011/04/11 18:02:23  rvv
*** empty log message ***

Revision 1.5  2010/01/20 12:28:47  rvv
*** empty log message ***

Revision 1.4  2009/05/05 12:38:08  cvs
*** empty log message ***

Revision 1.3  2008/10/30 12:10:17  rvv
*** empty log message ***

Revision 1.2  2008/09/26 07:54:41  rvv
*** empty log message ***

Revision 1.1  2008/03/18 09:42:38  rvv
*** empty log message ***

Revision 1.3  2008/01/10 16:27:31  rvv
*** empty log message ***

Revision 1.2  2007/10/04 09:14:51  rvv
*** empty log message ***

Revision 1.1  2007/08/02 14:46:59  rvv
*** empty log message ***



*/


//listarray($this->waarden);



    $this->pdf->marge = 30;
    $this->pdf->rowHeight=4;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);

		$this->pdf->rapport_type = "FACTUUR";
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);

$DB = new DB();
$DB->SQL("SELECT
Vermogensbeheerders.Vermogensbeheerder,
Vermogensbeheerders.Naam,
Vermogensbeheerders.Adres,
Vermogensbeheerders.Woonplaats,
Vermogensbeheerders.Telefoon,
Vermogensbeheerders.Fax,
Vermogensbeheerders.Email,
Vermogensbeheerders.website
FROM
Vermogensbeheerders
WHERE Vermogensbeheerders.Vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'");
$vermData = $DB->lookupRecord();

//factuur 1
		$this->pdf->AddPage('P');
    factuurKop($this->pdf,$vermData);
    $this->pdf->templateVars['FACTUURpaginas']=$this->pdf->page;

		$this->pdf->SetY($this->pdf->getY() +20);
		// start eerste block

		$this->pdf->SetWidths(array(100,80));
		$this->pdf->SetAligns(array("L","L"));

		$kwartaal = ceil(date("n",db2jul($this->waarden['datumTot']))/3);
	//	$kwartaal = ceil(date("n",db2jul('2006-03-20'))/3);

		$kwartalen[1] = 'eerste';
		$kwartalen[2] = 'tweede';
		$kwartalen[3] = 'derde';
		$kwartalen[4] = 'vierde';


$db=new DB();
$query="SELECT debiteurnr FROM CRM_naw WHERE portefeuille='".$this->waarden['portefeuille']."'";
$db->SQL($query);
$crmData=$db->lookupRecord();

$query="SELECT valutaUitsluiten,depotbank FROM Portefeuilles WHERE portefeuille='".$this->waarden['portefeuille']."'";
$db->SQL($query);
$portefeuille=$db->lookupRecord();



		$this->pdf->SetWidths(array(100,80));
		$this->pdf->SetAligns(array("L","L"));
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize+1);
    $this->pdf->row(array('Vertrouwelijk'));
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize+1);
		$this->pdf->row(array($this->waarden['clientNaam']));
		if ($this->waarden['clientNaam1'] !='')
		  $this->pdf->row(array($this->waarden['clientNaam1']));
		$this->pdf->row(array($this->waarden['clientAdres']));
		$plaats='';
		if($this->waarden['clientPostcode'] != '')
		  $plaats .= $this->waarden['clientPostcode']." ";
		$plaats .= $this->waarden['clientWoonplaats'];
		$this->pdf->row(array($plaats));
    if($this->waarden['clientLand'])
      $this->pdf->row(array($this->waarden['clientLand']));
		$this->pdf->SetY($this->pdf->getY() +20);
		$this->pdf->ln();
    $factuurnummer = date('Y').sprintf("%05d",$this->waarden['factuurNummer']);


    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize+1);
		$this->pdf->SetWidths(array(30,100));
		$this->pdf->SetAligns(array("L","L"));
		$tussenruimte =1;
	  $this->pdf->setY(96);
//rapdate		$this->pdf->row(array("Datum:", date("j",db2jul($this->waarden['datumTot']))." ".$this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))]." ".date("Y",db2jul($this->waarden['datumTot']))));
  	$this->pdf->row(array("Datum:", date("j")." ".$this->__appvar["Maanden"][date("n")]." ".date("Y")));
    $this->pdf->ln($tussenruimte);
    $this->pdf->row(array("Debiteur:",$crmData['debiteurnr']));
    $this->pdf->ln($tussenruimte);
    $this->pdf->row(array("Notanummer", $factuurnummer));
		$this->pdf->ln($tussenruimte);


		$this->pdf->SetY($this->pdf->getY() +10);

  $kwartaal=$this->waarden['kwartaal'];
  $rapportJaar=$this->waarden['rapportJaar'];
  if($this->waarden['BeheerfeeFacturatieVooraf']==1)
	{
		if($kwartaal==4)
		{
			$kwartaal = 1;
			$rapportJaar=$this->waarden['rapportJaar']+1;
		}
		else
			$kwartaal++;
	}

$jaarFeeParts = explode('.',$this->waarden['BeheerfeePercentageVermogen']);
$decimaalDeel = $jaarFeeParts[1];
if ($decimaalDeel != 0)
{
	for($i = strlen($decimaalDeel); $i >=0; $i--)
	{
		$decimaal = $decimaalDeel[$i-1];
		if ($decimaal != '0' && !$newDec)
			$newDec = $i;
	}
	if($newDec>4)
		$newDec=4;
	$jaarFeePercentage=$this->formatGetal($this->waarden['BeheerfeePercentageVermogen'],$newDec,",",".");
}
unset($newDec);



	$this->pdf->SetWidths(array(150));
  $this->pdf->row(array("Geachte ".$this->waarden['CRM_verzendAanhef'].","));
  $this->pdf->ln();
  $this->pdf->row(array("Ingevolge de met u gesloten overeenkomst brengen wij u onze fee over het ".$kwartalen[$kwartaal]." kwartaal in rekening."));
	$this->pdf->ln();
  $this->pdf->row(array('Op grond van onze tariefafspraken komt dit neer op een bedrag van:'));
  $this->pdf->ln();

  $this->pdf->SetAligns(array("L","R","R","R","R"));
  if($this->waarden['BeheerfeeBedragVast'] <>0)
  {
		$this->pdf->SetWidths(array(85,20));
		$this->pdf->row(array("","€"));
	  $this->pdf->row(array("Beheerfee ".$kwartalen[$kwartaal]." kwartaal ".$rapportJaar."",$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));
		$this->pdf->row(array("Btw ".$this->formatGetal($this->waarden['btwTarief'],0)."%",$this->formatGetal($this->waarden['btwNor'],2)));
		$this->pdf->CellBorders=array('',array('T','U'));
		$this->pdf->row(array("Totaal",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
    unset($this->pdf->CellBorders);
		$this->pdf->ln();
	}
  else
  {
$this->pdf->SetWidths(array(85,20));
$this->pdf->SetAligns(array("L","R"));
$this->pdf->row(array("","€"));
$this->pdf->row(array("Beheerfee ".$kwartalen[$kwartaal]." kwartaal ".$rapportJaar."",$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));
$this->pdf->row(array("Btw ".$this->formatGetal($this->waarden['btwTarief'],0)."%",$this->formatGetal($this->waarden['btwNor'],2)));
$this->pdf->CellBorders=array('',array('T','U'));
$this->pdf->row(array("Totaal",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
unset($this->pdf->CellBorders);
$this->pdf->ln();
	}

  $this->pdf->SetWidths(array(150));
		$this->pdf->ln(15);
if($this->waarden['BetalingsinfoMee'] == 1)
	$this->pdf->row(array("Zou u zo vriendelijk willen zijn om dit bedrag binnen 14 dagen over te maken op het volgende bankrekeningnummer: IBAN NL93 ABNA 0478 307 535 ter attentie van Wilton Investment Services BV, onder vermelding van het factuurnummer."));
	else
		$this->pdf->row(array("Dit bedrag zal automatisch worden afgeschreven. U hoeft dus zelf geen overboekingsinstructies meer door te geven."));
				$this->pdf->ln();
$this->pdf->row(array("Mocht u vragen of opmerkingen hebben, dan vernemen we dat graag.

Met vriendelijke groet,
Wilton Family Office"));
$this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);

// eind factuur 1




//factuur 2
$this->pdf->AddPage('P');
factuurKop($this->pdf,$vermData);
$this->pdf->SetY($this->pdf->getY() +20);
// start eerste block
$this->pdf->SetWidths(array(100,80));
$this->pdf->SetAligns(array("L","L"));


$this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize+1);
$this->pdf->row(array('Vertrouwelijk'));
$this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize+1);
$this->pdf->row(array($this->waarden['clientNaam']));
if ($this->waarden['clientNaam1'] !='')
	$this->pdf->row(array($this->waarden['clientNaam1']));
$this->pdf->row(array($this->waarden['clientAdres']));
$plaats='';
if($this->waarden['clientPostcode'] != '')
	$plaats .= $this->waarden['clientPostcode']." ";
$plaats .= $this->waarden['clientWoonplaats'];
$this->pdf->row(array($plaats));
if($this->waarden['clientLand'])
	$this->pdf->row(array($this->waarden['clientLand']));
$this->pdf->SetY($this->pdf->getY() +20);
$this->pdf->ln();
$factuurnummer = date('Y').sprintf("%05d",$this->waarden['factuurNummer']);


$this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize+1);
$this->pdf->SetWidths(array(30,100));
$this->pdf->SetAligns(array("L","L"));
$tussenruimte =1;
$this->pdf->setY(96);
//rapdate		$this->pdf->row(array("Datum:", date("j",db2jul($this->waarden['datumTot']))." ".$this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))]." ".date("Y",db2jul($this->waarden['datumTot']))));
$this->pdf->row(array("Datum:", date("j")." ".$this->__appvar["Maanden"][date("n")]." ".date("Y")));
$this->pdf->ln($tussenruimte);
$this->pdf->row(array("Debiteur:",$crmData['debiteurnr']));
$this->pdf->ln($tussenruimte);
$this->pdf->row(array("Notanummer", $factuurnummer));
$this->pdf->ln($tussenruimte);


$this->pdf->SetY($this->pdf->getY() +10);


$this->pdf->SetWidths(array(150));
$this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize+2);
$this->pdf->row(array("Bijlage"));
$this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize+1);
$this->pdf->ln();

$this->pdf->SetAligns(array("L","R","R","R","R"));
if($this->waarden['BeheerfeeBedragVast'] <>0)
{
	$this->pdf->row(array('Op grond van onze tariefafspraken komt dit neer op een bedrag van:'));
	$this->pdf->SetWidths(array(85,20));
	$this->pdf->row(array("","€"));
	$this->pdf->row(array("Beheerfee ".$kwartalen[$kwartaal]." kwartaal ".$rapportJaar."",$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));
	$this->pdf->row(array("Btw ".$this->formatGetal($this->waarden['btwTarief'],0)."%",$this->formatGetal($this->waarden['btwNor'],2)));
	$this->pdf->CellBorders=array('',array('T','U'));
	$this->pdf->row(array("Totaal",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
	unset($this->pdf->CellBorders);
	$this->pdf->ln();
}
else
{
	$txt='';
	if($this->waarden['BeheerfeePercentageVermogen'] <> 0 && $this->waarden['BeheerfeeMinJaarBedrag'] == 0)
		$txt="De hoogte van de fee bedraagt ".$jaarFeePercentage."% per jaar over het vrij belegbare vermogen";
	elseif($this->waarden['BeheerfeePercentageVermogen'] <> 0 && $this->waarden['BeheerfeeMinJaarBedrag'] <> 0)
		$txt="De hoogte van de fee bedraagt ".$jaarFeePercentage."% per jaar over het vrij belegbare vermogen met een ondergrens van €".$this->formatGetal($this->waarden['BeheerfeeMinJaarBedrag'],2)." per jaar";
	if($portefeuille['valutaUitsluiten'] <> 0)
		$txt.=" en exclusief liquide middelen";
	if($txt<>'')
		$txt.=".";
	$this->pdf->row(array($txt));
	$this->pdf->ln();





	$periodeFee=$this->waarden['BeheerfeePercentageVermogen']/$this->waarden['BeheerfeeAantalFacturen'];
	$periodeFeeParts = explode('.',$periodeFee);
	$decimaalDeel = $periodeFeeParts[1];
	if ($decimaalDeel != 0)
	{
		for($i = strlen($decimaalDeel); $i >=0; $i--)
		{
			$decimaal = $decimaalDeel[$i-1];
			if ($decimaal != '0' && !$newDec)
				$newDec = $i;
		}
		if($newDec>4)
			$newDec=4;
		$periodeFee=$this->formatGetal($periodeFee,$newDec,",",".");
	}


	$this->pdf->SetWidths(array(150));
	$periodeTxt="voor de periode van ".date("j",db2jul($this->waarden['datumVan']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumVan']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumVan']))." tot en met ".
		date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot']))." bedraagt ".$periodeFee."%";
	if($this->waarden['BeheerfeeFacturatieVooraf']==0)
		$this->pdf->row(array("De beheerfee ".$periodeTxt));
	$this->pdf->ln();

	$this->pdf->row(array("Vrij belegbaar vermogen per ".date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot'])).":"));
	$this->pdf->ln();

	$this->pdf->SetWidths(array(15,30,30,30));
	$this->pdf->SetAligns(array("L","L","R",'R'));
	$this->pdf->CellBorders=array('','U','');
	$this->pdf->row(array('','Portefeuille','',"€"));
	unset($this->pdf->CellBorders);
	//listarray($this->waarden);
	$this->pdf->SetWidths(array(15,60,30));

	if($this->waarden['BeheerfeeBasisberekening']==0)
	  $verdeling='gemiddeldeWaarde';
	else
    $verdeling='eindWaarde';
	if(count($this->waarden['portefeuilleVerdeling'][$verdeling]) > 0)
	{
		foreach($this->waarden['portefeuilleVerdeling'][$verdeling] as $portefeuille=>$waarde)
		{
			if($portefeuille <> 'totaal')
				$this->pdf->row(array('',$portefeuille,$this->formatGetal($waarde,2) ));
		}
	}
	else
	{
		$this->pdf->row(array('',$this->waarden['portefeuille'],$this->formatGetal($this->waarden['rekenvermogen'],2) ));//$portefeuille['depotbank']
	}
	$this->pdf->CellBorders=array('','',array('T','UU'));
	$this->pdf->row(array('','',$this->formatGetal($this->waarden['rekenvermogen'],2) ));
	unset( $this->pdf->CellBorders);
	$this->pdf->ln();


	$this->pdf->SetWidths(array(85,20));
	$this->pdf->SetAligns(array("L","R"));
	$this->pdf->row(array("","€"));
	$this->pdf->row(array("Beheerfee ".$periodeFee."% van ".$this->formatGetal($this->waarden['rekenvermogen'],2),$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));

}


//eind factuur 2


?>