<?php

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

    $this->pdf->templateVars['FACTUURpaginas']=$this->pdf->page;


if (is_file($this->pdf->rapport_logo))
{
  $this->pdf->Image($this->pdf->rapport_logo, 16, 8, $this->pdf->logoXsize);
}


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
  	$this->pdf->row(array("Factuurdatum:", date("j")." ".$this->__appvar["Maanden"][date("n")]." ".date("Y")));
    $this->pdf->ln($tussenruimte);
    $this->pdf->row(array("Notanummer", $factuurnummer));
		$this->pdf->ln($tussenruimte);
    $this->pdf->row(array("Portefeuille:",$this->waarden['portefeuille']));
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
$jaarFeePercentage=$this->formatGetal($this->waarden['BeheerfeePercentageVermogen'],0,",",".");
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
  //$this->pdf->row(array("Geachte ".$this->waarden['CRM_verzendAanhef'].","));
  //$this->pdf->ln();

$datumJul=db2jul($this->waarden['datumTot']);
$this->pdf->SetWidths(array(85,10,25));
$this->pdf->SetAligns(array("L","R","R"));
$this->pdf->row(array("Waarde portefeuille per ultimo ".$this->__appvar["Maanden"][date("n",$datumJul)]." ".date("Y",$datumJul),"",$this->formatGetal($this->waarden['rekenvermogen'],2)));
$this->pdf->SetFont('times',"",$this->pdf->rapport_fontsize+1);
$this->pdf->ln($this->pdf->rowHeight*-1);
$this->pdf->row(array('',chr(128)));
	$this->pdf->ln();
  
  $this->pdf->SetAligns(array("L","R","R","R","R"));
  /*
  if($this->waarden['BeheerfeeBedragVast'] <>0)
  {
		$this->pdf->SetWidths(array(85,20));
		$this->pdf->row(array("",".chr(128)."));
	  $this->pdf->row(array("Beheerfee ".$kwartalen[$kwartaal]." kwartaal ".$rapportJaar."",$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));
		$this->pdf->row(array("Btw ".$this->formatGetal($this->waarden['btwTarief'],0)."%",$this->formatGetal($this->waarden['btwNor'],2)));
		$this->pdf->CellBorders=array('',array('T','U'));
		$this->pdf->row(array("Totaal",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
    unset($this->pdf->CellBorders);
		$this->pdf->ln();
	}
  */
 // else
 // {
$this->pdf->SetWidths(array(85,10,25));
$this->pdf->SetAligns(array("L","R","R"));
$this->pdf->row(array(""));
$this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize+1);
$this->pdf->row(array("Beheervergoeding $jaarFeePercentage% per jaar",'',$this->formatGetal($this->waarden['beheerfeePerPeriode']-$this->waarden['administratieBedrag'],2)));
$this->pdf->SetFont('times',"",$this->pdf->rapport_fontsize+1);
$this->pdf->ln($this->pdf->rowHeight*-1);
$this->pdf->row(array('',chr(128)));
$this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize+1);

if(round($this->waarden['administratieBedrag'],2)<>0)
  $this->pdf->row(array("Toezichthouders kosten","-",$this->formatGetal($this->waarden['administratieBedrag'],2)));
foreach($this->waarden['extraFactuurregels']['regels'] as $regel)
{
  $this->pdf->row(array($regel['omschrijving'],"-",$this->formatGetal($regel['bedrag'],2)));
}
$this->pdf->CellBorders=array('','',array('T'));
$this->pdf->row(array("","",$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));
$this->pdf->SetFont('times',"",$this->pdf->rapport_fontsize+1);
$this->pdf->ln($this->pdf->rowHeight*-1);
$this->pdf->row(array('',chr(128)));
$this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize+1);

unset($this->pdf->CellBorders);
$this->pdf->ln();
$this->pdf->row(array("BTW ".$this->formatGetal($this->waarden['btwTarief'],0)."%","-",$this->formatGetal($this->waarden['btw'],2)));
$this->pdf->CellBorders=array('','',array('T','U'));
$this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize+1);
$this->pdf->row(array("Totaal","",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
$this->pdf->SetFont('times',"B",$this->pdf->rapport_fontsize+1);
$this->pdf->ln($this->pdf->rowHeight*-1);
$this->pdf->row(array('',chr(128)));
$this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize+1);

unset($this->pdf->CellBorders);
$this->pdf->ln();
//	}

//listarray($this->waarden);

$this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize+1);
  $this->pdf->SetWidths(array(150));
		$this->pdf->ln(15);
//if($this->waarden['BetalingsinfoMee'] == 1)
//	$this->pdf->row(array("Zou u zo vriendelijk willen zijn om dit bedrag binnen 14 dagen over te maken op het volgende bankrekeningnummer: IBAN NL93 ABNA 0478 307 535 ter attentie van Wilton Investment Services BV, onder vermelding van het factuurnummer."));
//	else
		$this->pdf->row(array("Dit bedrag wordt via automatische incasso van uw rekening afgeschreven."));
//				$this->pdf->ln();
//				$this->pdf->row(array("Mocht u vragen of opmerkingen hebben, dan vernemen we dat graag.
//
//Met vriendelijke groet,
//Wilton Family Office"));
//$this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);

$query = "SELECT
Vermogensbeheerders.Vermogensbeheerder,
Vermogensbeheerders.Naam,
Vermogensbeheerders.Adres,
Vermogensbeheerders.Woonplaats,
Vermogensbeheerders.Telefoon,
Vermogensbeheerders.Fax,
Vermogensbeheerders.Email,
Vermogensbeheerders.rekening,
Vermogensbeheerders.bank,
Vermogensbeheerders.website
FROM
Vermogensbeheerders
WHERE
Vermogensbeheerders.Vermogensbeheerder = '".$this->waarden['Vermogensbeheerder']."'";
$db->SQL($query);
$vermData = $db->lookupRecord();
$this->pdf->AutoPageBreak=false;
$this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
  $this->pdf->SetTextColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
  $this->pdf->SetWidths(array($this->pdf->w-$this->pdf->marge*2));
  $this->pdf->SetAligns(array("C"));
  $this->pdf->SetY(280);
  $this->pdf->row(array($vermData['Adres'].", ".$vermData['Woonplaats']." | T".$vermData['Telefoon']."\nKvK Limburg 14065375 | Btw NR NL 8090.25.590B01"));
$this->pdf->AutoPageBreak=true;
$this->pdf->SetTextColor(0,0,0);


?>