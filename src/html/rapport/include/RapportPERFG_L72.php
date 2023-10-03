<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/10/23 13:34:01 $
 		File Versie					: $Revision: 1.10 $

 		$Log: RapportPERFG_L72.php,v $
 		Revision 1.10  2019/10/23 13:34:01  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2019/08/07 12:32:54  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2019/03/27 16:20:18  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2019/03/10 14:08:16  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2019/03/06 16:13:44  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/10/03 15:42:01  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2018/08/04 11:54:53  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/07/01 07:29:40  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2018/06/30 17:43:55  rvv
 		*** empty log message ***
 		


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");

class RapportPERFG_L72
{


	function RapportPERFG_L72($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{

		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
 
  }


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar;


		$query="SELECT 
CRM_naw.naam,
CRM_naw.naam1,
Portefeuilles.Client,
Portefeuilles.Portefeuille,
Portefeuilles.SoortOvereenkomst,
Portefeuilles.Accountmanager,
Portefeuilles.Modelportefeuille,
Accountmanagers.naam as AccountmanagerNaam,
CRM_naw.Rapportagetenaamstelling,
CRM_naw.email,
CRM_naw.wachtwoord,
Portefeuilles.BeheerfeePercentageVermogen,
Portefeuilles.BeheerfeeBedrag,
Portefeuilles.BeheerfeeSchijvenTarief,
Portefeuilles.BeheerfeeStaffel1,
Portefeuilles.BeheerfeeStaffelPercentage1,
Portefeuilles.BeheerfeeStaffel2,
Portefeuilles.BeheerfeeStaffelPercentage2,
Portefeuilles.BeheerfeeStaffel3,
Portefeuilles.BeheerfeeStaffelPercentage3,
Portefeuilles.BeheerfeeStaffel4,
Portefeuilles.BeheerfeeStaffelPercentage4,
Portefeuilles.BeheerfeeStaffel5,
Portefeuilles.BeheerfeeStaffelPercentage5,
Portefeuilles.BetalingsinfoMee,
Portefeuilles.afwijkendeOmzetsoort,
CRM_naw.verzendAanhef,
CRM_naw.verzendPaAanhef,
CRM_naw.verzendAdres,	
CRM_naw.verzendPc, 
CRM_naw.verzendPlaats,
CRM_naw.verzendLand,
CRM_naw.CtrAandAmer,
CRM_naw.CtrAlternatives,
CRM_naw.CtrAandEur,
CRM_naw.CtrCommodities,
CRM_naw.CtrAandEMKT,
CRM_naw.CtrAandOG,
CRM_naw.CtrAandPac,
CRM_naw.CtrOpties,
CRM_naw.CtrAandWer,
CRM_naw.CtrVastrEur,
CRM_naw.CtrDebestaden,
CRM_naw.CtrVastrInt,
CRM_naw.CtrValutaTermAff,
CRM_naw.CtrVastrAlt,
CRM_naw.CtrVreemdeValuta,
CRM_naw.Ptf_ClientRiskTolerance,
CRM_naw.beleggingsHorizon,
CRM_naw.beleggingsDoelstelling,
CRM_naw.Ptf_ClientHorizon,
CRM_naw.Ptf_RiskSRRI,
Portefeuilles.Risicoklasse,
Portefeuilles.Taal,
Portefeuilles.RapportageValuta,
CRM_naw.Kapitaalverlies,
CRM_naw.kvknr,
CRM_naw.LEInr,
CRM_naw.btwnr,
CRM_naw.Vestigingsland,
CRM_naw.PUP,
CRM_naw.PEP,
CRM_naw.profielInsiderRegeling,
CRM_naw.profielOverigeBeperkingen,
CRM_naw.ondernemingsvorm,
CRM_naw.Ptf_ClientTypeRetail,
CRM_naw.Ptf_ExpertiseBasic,
CRM_naw.Ptf_CapitalLossNone,
CRM_naw.Ptf_ClientTypeProfessional,
CRM_naw.Ptf_ExpertiseInformed,
CRM_naw.Ptf_CapitalLossLimited,
CRM_naw.Ptf_ClientTypeEligibleCounterparty,
CRM_naw.Ptf_ExpertiseAdvanced,
CRM_naw.Ptf_CapitalLossTotal,
CRM_naw.Landproblematiek
FROM Portefeuilles 
JOIN Accountmanagers ON Portefeuilles.Accountmanager=Accountmanagers.Accountmanager
LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.Portefeuille
WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
$db=new DB();
		$db->SQL($query);
		$crm=$db->lookupRecord();

		$width=210;
		$base=$width-$this->pdf->marge*2;
		$cell=$base/4;
    $cellZes=$base/6;


		$this->pdf->AddPage('P');


		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		$this->pdf->SetWidths(array($cell,$cell,$cell,$cell));
		$this->pdf->SetAligns(array('L','L','L','L'));
		$this->pdf->row(array('Naam:',$crm['naam']));
		$this->pdf->row(array('',$crm['naam1']));
		$this->pdf->row(array('Clientcode',$crm['Client'],'Portefeuille nr',$crm['Portefeuille']));
		$this->pdf->row(array('Overeenkomst',$crm['SoortOvereenkomst'],'acc.man.',$crm['AccountmanagerNaam']));
		$this->pdf->row(array('Rapportage tenaamstelling',$crm['Rapportagetenaamstelling']));
		//$this->pdf->row(array('Emailadres',$crm['email']));
		//$this->pdf->row(array('Wachtwoord',$crm['wachtwoord']));
		$this->pdf->ln();
		$this->pdf->line($this->pdf->marge,$this->pdf->getY(),210-$this->pdf->marge,$this->pdf->getY());
		$this->pdf->ln();
		$this->pdf->row(array('Fee:',$this->formatGetal($crm['BeheerfeePercentageVermogen'],3),'bedrag:',$this->formatGetal($crm['BeheerfeeBedrag'],2)));
		$this->pdf->row(array('Staffel?',($crm['BeheerfeeSchijvenTarief']==1)?'Ja':'Nee','Waarde t/m :','Percentage'));
		$this->pdf->row(array('','',$this->formatGetal($crm['BeheerfeeStaffel1'],2),$this->formatGetal($crm['BeheerfeeStaffelPercentage1'],3)));
		$this->pdf->row(array('','',$this->formatGetal($crm['BeheerfeeStaffel2'],2),$this->formatGetal($crm['BeheerfeeStaffelPercentage2'],3)));
		$this->pdf->row(array('Betaling via overboeking:',($crm['BetalingsinfoMee']==1)?'Ja':'Nee',$this->formatGetal($crm['BeheerfeeStaffel3'],2),$this->formatGetal($crm['BeheerfeeStaffelPercentage3'],3)));
		$this->pdf->row(array('','',$this->formatGetal($crm['BeheerfeeStaffel4'],2),$this->formatGetal($crm['BeheerfeeStaffelPercentage4'],3)));
		$this->pdf->row(array('','',$this->formatGetal($crm['BeheerfeeStaffel5'],2),$this->formatGetal($crm['BeheerfeeStaffelPercentage5'],3)));
		$this->pdf->ln();
		$this->pdf->row(array('NAW factuur',$crm['verzendAanhef']));
		$this->pdf->row(array('',$crm['verzendPaAanhef']));
		$this->pdf->row(array('',$crm['verzendAdres']));
		$this->pdf->row(array('',$crm['verzendPc'].' '.$crm['verzendPlaats']));
		$this->pdf->row(array('',$crm['verzendLand']));
		$this->pdf->ln();
		$this->pdf->line($this->pdf->marge,$this->pdf->getY(),210-$this->pdf->marge,$this->pdf->getY());
		$this->pdf->ln();

		$query="SELECT ZorgplichtPerPortefeuille.Vanaf FROM ZorgplichtPerPortefeuille WHERE ZorgplichtPerPortefeuille.Portefeuille='".$this->portefeuille."'
		AND ( ZorgplichtPerPortefeuille.Vanaf < '".$this->rapportageDatum."'  OR ZorgplichtPerPortefeuille.Vanaf='0000-00-00') ORDER BY ZorgplichtPerPortefeuille.Vanaf desc limit 1";
    $db->SQL($query);
    $db->query();
    $datum=$db->nextRecord();
    
$query="SELECT
ZorgplichtPerPortefeuille.Zorgplicht,
Zorgplichtcategorien.Omschrijving,
ZorgplichtPerPortefeuille.Portefeuille,
ZorgplichtPerPortefeuille.Minimum,
ZorgplichtPerPortefeuille.Maximum
FROM
ZorgplichtPerPortefeuille
INNER JOIN Zorgplichtcategorien ON Zorgplichtcategorien.Vermogensbeheerder = ZorgplichtPerPortefeuille.Vermogensbeheerder AND Zorgplichtcategorien.Zorgplicht = ZorgplichtPerPortefeuille.Zorgplicht
WHERE ZorgplichtPerPortefeuille.Portefeuille='".$this->portefeuille."' AND vanaf='".$datum['Vanaf']."'
ORDER BY ZorgplichtPerPortefeuille.Zorgplicht";
		$db->SQL($query);
		$db->query();
		$zorg=array();
		while($tmp=$db->nextRecord())
		{
			$zorg[$tmp['Zorgplicht']]=$tmp;
		}

		$zorgplicht = new Zorgplichtcontrole();
		$pdata=$this->pdf->portefeuilledata;
		if($pdata['Portefeuille']=='000000')
			$pdata['Portefeuille']=$pdata['PortefeuilleOrigineel'];

		$backup=backupTijdelijkeTabel($pdata['Portefeuille'],$this->rapportageDatum);
		$doorkijkData=bepaaldFondsWaardenVerdiept($this->pdf,$pdata['Portefeuille'],$this->rapportageDatum);
		vulTijdelijkeTabel($doorkijkData,$pdata['Portefeuille'],$this->rapportageDatum);
		//$pdata['Portefeuille']='d_'.$pdata['Portefeuille'];

		$zpwaarde=$zorgplicht->zorgplichtMeting($pdata,$this->rapportageDatum); //listarray($zpwaarde);
		vulTijdelijkeTabel($backup,$pdata['Portefeuille'],$this->rapportageDatum);

		$this->pdf->SetAligns(array('L','R','R','R','L'));
		$this->pdf->SetWidths(array($cell/2,$cell/2,$cell/2,$cell/2,$cell));
		$this->pdf->row(array('Bandbreedtes','Min','Max','Werkelijk'));
		foreach($zorg as $cat=>$regel)
		{
			if($zpwaarde['conclusieDetail'][$cat]['percentage']>$regel['Maximum'] || $zpwaarde['conclusieDetail'][$cat]['percentage']<$regel['Minimum'])
			{
				if($zpwaarde['conclusieDetail'][$cat]['percentage']>$regel['Maximum'])
					$verschil=$zpwaarde['conclusieDetail'][$cat]['percentage']-$regel['Maximum'];
				else
					$verschil=$zpwaarde['conclusieDetail'][$cat]['percentage']-$regel['Minimum'];
				$detail = 'Voldoet niet. (' .$this->formatGetal($verschil,2).')';
			}
			else
				$detail='';
			$this->pdf->row(array($regel['Omschrijving'],$this->formatGetal($regel['Minimum'],2),$this->formatGetal($regel['Maximum'],2),$this->formatGetal($zpwaarde['conclusieDetail'][$cat]['percentage'],2),$detail));
		}
		$this->pdf->SetAligns(array('L','L','L','L','L'));
		$this->pdf->SetWidths(array($cell,$cell,$cell,$cell));

		$this->pdf->ln();
		$this->pdf->line($this->pdf->marge,$this->pdf->getY(),210-$this->pdf->marge,$this->pdf->getY());
		$this->pdf->ln();
		$this->pdf->row(array('Aandelen Amerika',$crm['CtrAandAmer'],'Alternatives',$crm['CtrAlternatives']));
		$this->pdf->row(array('Aandelen Europa',$crm['CtrAandEur'],'Commodities',$crm['CtrCommodities']));
		$this->pdf->row(array('Aandelen Emerging Markets',$crm['CtrAandEMKT'],'Onroerend goed',$crm['CtrAandOG']));
		$this->pdf->row(array('Aandelen Pacific Rim',$crm['CtrAandPac'],'Opties',$crm['CtrOpties']));
		$this->pdf->row(array('Aandelen Wereld',$crm['CtrAandWer']));
		$this->pdf->ln();
		$this->pdf->row(array('Vastrentend Euro Hedged',$crm['CtrVastrEur'],'Debetstanden',$crm['CtrDebestaden']));
		$this->pdf->row(array('Vastrentend Non Hedged',$crm['CtrVastrInt'],'Valuta termijn aff.',$crm['CtrValutaTermAff']));
		$this->pdf->row(array('Vastrentend Alternatief	',$crm['CtrVastrAlt'],'Vreemde Valuta',$crm['CtrVreemdeValuta']));
		$this->pdf->ln();
		$this->pdf->row(array('Beleggingshorizon',$crm['Ptf_ClientHorizon'],'Risico Profiel',$crm['Risicoklasse']));
		$this->pdf->row(array('Doelstelling',$crm['beleggingsDoelstelling'],'Model Portefeuille',$crm['Modelportefeuille'])); //,'Risico Tolerantie',round($crm['Ptf_RiskSRRI'])
		$this->pdf->row(array('Taal',$__appvar["TaalOptions"][$crm['Taal']],'Valuta',$crm['RapportageValuta']));//'Vermogen om verlies op te vangen',$crm['Kapitaalverlies']
		$this->pdf->row(array('Landen problematiek',$crm['Landproblematiek']));
    $this->pdf->ln();
    $this->pdf->SetAligns(array('L','L','L','L','L','L','L'));
    $this->pdf->SetWidths(array($cellZes*(1.7),$cellZes*(.5),$cellZes*(1.2),$cellZes*(.7),$cellZes*(1.4),$cellZes*(.5)));
    $this->pdf->row(array('Niet-Professioneel (retail client)',$crm['Ptf_ClientTypeRetail'],'Basiskennis',$crm['Ptf_ExpertiseBasic'],'Geen Kapitaalverlies',$crm['Ptf_CapitalLossNone']));
    $this->pdf->row(array('Professioneel',$crm['Ptf_ClientTypeProfessional'],'Uitgebreide kennis',$crm['Ptf_ExpertiseInformed'],'Beperkt Kapitaalverlies',$crm['Ptf_CapitalLossLimited']));
    $this->pdf->row(array('In aanmerking komende tegenpartij',$crm['Ptf_ClientTypeEligibleCounterparty'],'Geavanceerde kennis',$crm['Ptf_ExpertiseAdvanced'],'Geen Kapitaalgarantie',$crm['Ptf_CapitalLossTotal']));
    $this->pdf->SetAligns(array('L','L','L','L','L'));
    $this->pdf->SetWidths(array($cell,$cell,$cell,$cell));
    
		$this->pdf->ln();
		$this->pdf->line($this->pdf->marge,$this->pdf->getY(),210-$this->pdf->marge,$this->pdf->getY());
		$this->pdf->ln();
		if($crm['ondernemingsvorm']<>'Persoon')
		{
			$this->pdf->row(array('Lei Nummer', $crm['LEInr'], 'KVK', $crm['kvknr']));
			$this->pdf->row(array('', '', 'Vestigingsland', $crm['Vestigingsland']));
			$this->pdf->ln();
			$this->pdf->line($this->pdf->marge, $this->pdf->getY(), 210 - $this->pdf->marge, $this->pdf->getY());
			$this->pdf->ln();
		}
		$this->pdf->row(array('PEP',($crm['PEP']==1)?'Ja':'Nee', 'Insider',( empty($crm['profielInsiderRegeling']))?'Nee':$crm['profielInsiderRegeling']));//'PUP',($crm['PUP']==1)?'Ja':'Nee',
		$this->pdf->SetWidths(array($cell,$cell+$cell+$cell));
		$this->pdf->row(array('Overige Beperkingen', ( empty($crm['profielOverigeBeperkingen']) ? 'N.v.t':$crm['profielOverigeBeperkingen']) ));
//		$this->pdf->row(array('Insider',$crm['profielInsiderRegeling']));
	}
}
?>