<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/04/06 17:11:28 $
File Versie					: $Revision: 1.16 $

$Log: RapportFRONT_L56.php,v $
Revision 1.16  2019/04/06 17:11:28  rvv
*** empty log message ***

Revision 1.15  2018/10/15 09:03:04  rvv
*** empty log message ***

Revision 1.14  2018/10/13 17:18:13  rvv
*** empty log message ***

Revision 1.13  2018/10/03 15:43:35  rvv
*** empty log message ***

Revision 1.12  2018/07/18 15:46:07  rvv
*** empty log message ***

Revision 1.11  2017/12/13 17:03:53  rvv
*** empty log message ***

Revision 1.10  2017/07/19 19:29:27  rvv
*** empty log message ***

Revision 1.9  2017/06/24 16:31:57  rvv
*** empty log message ***

Revision 1.8  2016/02/03 17:00:18  rvv
*** empty log message ***

Revision 1.7  2015/12/31 06:42:46  rvv
*** empty log message ***

Revision 1.6  2015/12/23 16:24:41  rvv
*** empty log message ***

Revision 1.5  2015/12/19 14:27:30  rvv
*** empty log message ***

Revision 1.4  2015/05/31 10:15:24  rvv
*** empty log message ***

Revision 1.3  2015/03/01 14:08:16  rvv
*** empty log message ***

Revision 1.2  2015/02/18 17:09:13  rvv
*** empty log message ***

Revision 1.1  2015/02/15 10:26:57  rvv
*** empty log message ***

Revision 1.9  2014/01/22 17:01:30  rvv
*** empty log message ***

Revision 1.8  2014/01/18 17:27:23  rvv
*** empty log message ***

Revision 1.7  2013/07/10 16:01:24  rvv
*** empty log message ***

Revision 1.6  2013/06/09 18:01:53  rvv
*** empty log message ***

Revision 1.5  2012/10/24 15:45:39  rvv
*** empty log message ***

Revision 1.4  2012/10/17 09:16:53  rvv
*** empty log message ***

Revision 1.3  2012/10/07 14:57:17  rvv
*** empty log message ***

Revision 1.2  2012/09/23 08:51:44  rvv
*** empty log message ***

Revision 1.1  2012/06/17 13:04:11  rvv
*** empty log message ***

Revision 1.2  2012/06/09 13:43:40  rvv
*** empty log message ***

Revision 1.1  2012/05/27 08:33:11  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L56
{
	function RapportFront_L56($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FRONT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_OIS_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_FRONT_titel;
		else
			$this->pdf->rapport_titel = "Titel pagina";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul=db2jul($this->rapportageDatum);
		$this->pdf->rapportCounter = count($this->pdf->page);

		$this->DB = new DB();

    
  }

	function voorBrief()
	{
		global $__appvar;
		$fontsize=8;


		$logo=$__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Logo'];
		if(is_file($logo))
		{
			$factor=0.06;
			$xSize=669*$factor;
			$ySize=177*$factor;

			$this->pdf->Image($logo, 20, 20, $xSize, $ySize);
		}
		$font='Arial';
		$this->pdf->brief_font='Arial';

		$this->pdf->setY(20);
		$this->pdf->SetFont($font,'',8);
		$kop="Petram & Co. N.V. 
Maliesingel 27 
3581 BH Utrecht

T +31 (0)85 485 85 70 
E info@petram-co.com 
www.petram-co.com
 ";

		$this->pdf->SetTextColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
		$this->pdf->SetWidths(array(140,50));
		$this->pdf->SetAligns(array("L","L","L"));
		$this->pdf->row(array('',$kop));
		$this->pdf->SetWidths(array(140,10,50));
		$this->pdf->row(array('','IBAN','NL57 ABNA 0579 1779 98 '));
		$this->pdf->row(array('','BIC','ABNANL2A'));
		$this->pdf->row(array('','KVK','34140171'));
		$this->pdf->row(array('','BTW','NL810449390B01'));

		$this->pdf->SetY(80);
		$this->pdf->SetTextColor(0);

		$this->pdf->SetFont($font,"",$fontsize);

		$this->DB = new DB();

		$query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.verzendAdres,
CRM_naw.verzendPc,
CRM_naw.verzendPlaats,
CRM_naw.verzendLand,
CRM_naw.verzendAanhef,
CRM_naw.verzendPaAanhef,
CRM_naw.ondernemingsvorm,
CRM_naw.titel,
CRM_naw.voorletters,
CRM_naw.tussenvoegsel,
CRM_naw.achternaam,
CRM_naw.achtervoegsel,
CRM_naw.part_naam,
CRM_naw.part_voorvoegsel,
CRM_naw.part_titel,
CRM_naw.part_voorletters,
CRM_naw.part_tussenvoegsel,
CRM_naw.part_achternaam,
CRM_naw.part_achtervoegsel,
CRM_naw.enOfRekening,
CRM_naw.btwnr
FROM CRM_naw WHERE Portefeuille = '".$this->portefeuille."'  ";

		$this->DB->SQL($query);
		$crmData = $this->DB->lookupRecord();
		$extraMarge=60;
		$this->pdf->SetWidths(array($extraMarge-$this->pdf->marge,135));
		$this->pdf->SetAligns(array('R','L','L','L','R'));
		$this->pdf->rowHeight = 5;
		$this->pdf->SetY(75);
		$this->pdf->SetFont($this->pdf->brief_font,'',$fontsize);
    
    $naam2=getExtraCrmNaam($this->portefeuille);
		
		$this->pdf->row(array('',$crmData['naam']));
		if (trim($crmData['naam1']) <> "")
			$this->pdf->row(array('',$crmData['naam1']));
    if (trim($naam2) <> "")
      $this->pdf->row(array('',$naam2));
		$this->pdf->row(array('',$crmData['verzendAdres']));
		$plaats=$crmData['verzendPc'];
		if($crmData['verzendPlaats'] != '') $plaats.=" ".$crmData['verzendPlaats'];
		$this->pdf->row(array('',$plaats));
		$this->pdf->row(array('',$crmData['verzendLand']));



		$beginDatumTxt=date("j",db2jul($this->waarden['datumVan']))." ".$__appvar["Maanden"][date("n",db2jul($this->waarden['datumVan']))]." ".date("Y",db2jul($this->waarden['datumVan']));
		$eindDatumTxt=date("j",db2jul($this->waarden['datumTot']))." ".$__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))]." ".date("Y",db2jul($this->waarden['datumTot']));
		$this->pdf->SetY(110);
		$this->pdf->row(array('',"Utrecht, ".(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->SetY(130);
		$this->pdf->SetWidths(array($extraMarge-$this->pdf->marge,125));
		$this->pdf->row(array('',"Betreft: Rapportage"));
		$this->pdf->ln();
		$this->pdf->row(array('',$crmData['verzendPaAanhef'].','));

		$this->pdf->SetY(150);
		$txt='';
		if($this->pdf->portefeuilledata['txtKoppeling'] !='')
		{
			$koppeling = stripslashes($this->pdf->portefeuilledata[$this->pdf->portefeuilledata['txtKoppeling']]);
			$koppeling = stripslashes($koppeling);
			$query = "SELECT * FROM custom_txt WHERE
  type = '".$this->pdf->portefeuilledata['txtKoppeling']."' AND
  field = '".$this->pdf->rapport_type."_".$koppeling."' AND
  Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'  ";
			$this->DB->SQL($query);
			$txtData = $this->DB->lookupRecord();
			$titel = $txtData['title'];
			$briefData = html_entity_decode(strip_tags($txtData['txt']));
			$txt = $briefData;//."\n".$this->pdf->portefeuilledata['AccountmanagerNaam'];
		}
		$this->pdf->SetWidths(array($extraMarge-$this->pdf->marge,125));
		$this->pdf->SetAligns(array('R','L','R','R','L'));
		$this->pdf->row(array('',$txt));
		$this->pdf->ln();
		$this->pdf->SetWidths(array($extraMarge-$this->pdf->marge,85,15,35));
		$this->pdf->SetAligns(array('R','L','R','R'));

		$voet="Petram & Co. N.V. is geregistreerd bij de Autoriteit Financiële Markten (AFM) en staat onder toezicht van de AFM en 
De Nederlandse Bank (DNB). Petram & Co. N.V. staat ingeschreven in de registers van de Stichting DSI.";
		$trigger=$this->pdf->PageBreakTrigger;
		$this->pdf->PageBreakTrigger=$this->pdf->PageBreakTrigger+20;

		$this->pdf->setY(280);
		$this->pdf->SetAligns(array('R','L'));
		$this->pdf->SetFont($this->pdf->brief_font,'i',6);

		$this->pdf->rowHeight = 3;
		$this->pdf->SetTextColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
		$this->pdf->row(array('',$voet));
		$this->pdf->PageBreakTrigger=$trigger;

		$this->pdf->SetTextColor(0,0,0);
		$this->pdf->geenBasisFooter=true;


	}



	function writeRapport()
	{
		global $__appvar;
		$rowHeight=$this->pdf->rowHeight;

		$query = "SELECT
		            Clienten.Naam,
                Clienten.Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Accountmanagers.Naam as accountManager,
                 Vermogensbeheerders.Naam as vermogensbeheerderNaam,
                 Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                 Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
		          FROM
		            Portefeuilles, Clienten , Accountmanagers, Vermogensbeheerders
		          WHERE
		            Portefeuille = '".$this->portefeuille."' AND
		            Portefeuilles.Client = Clienten.Client AND
                Accountmanagers.Accountmanager = Portefeuilles.Accountmanager AND
                Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder";
		$this->DB->SQL($query);
		$this->DB->Query();
		$portefeuilledata = $this->DB->nextRecord();


		//
//listarray($this->pdf->selectData);
		if($this->pdf->selectData['periode']=='Kwartaalrapportage')//$this->pdf->selectData['allInOne']==1 || $this->pdf->selectData['type'] <>'') // backoffice afdruk
		{
			$this->pdf->AddPage('P');
			$this->pdf->emailSkipPages[]=$this->pdf->page;
			$this->voorBrief();
		//	listarray($this->pdf->selectData);
		}
		$this->pdf->rowHeight=$rowHeight;
		//if($this->pdf->selectData['type'] != 'eMail')
		//
   //background

		///if ((count($this->pdf->pages) % 2))
		//{
		//  $this->pdf->frontPage=true;
  	//	$this->pdf->AddPage($this->pdf->CurOrientation);
		//}
		$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');


		if(is_file($this->pdf->rapport_logo))
		{

	    $factor=0.055;
      $xSize=669*$factor;
		  $ySize=206*$factor;
      $logopos=(297/2)-($xSize/2);
	    $this->pdf->Image($this->pdf->rapport_logo, $logopos, 4, $xSize, $ySize);
		}

   	$this->pdf->widthA = array(30,180);
		$this->pdf->alignA = array('L','L','L');

		$fontsize = 10; //$this->pdf->rapport_fontsize

    


    $this->pdf->SetWidths($this->pdf->widthA);

    $this->pdf->SetY(40);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
    $this->pdf->row(array(' ',vertaalTekst('PERSOONLIJK EN VERTROUWELIJK',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam']));
    if($this->pdf->portefeuilledata['Naam1'] <> '')
    {
      $this->pdf->ln(1);
      $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam1']));
    }
    
    $naam2=getExtraCrmNaam($this->portefeuille);
    if ($naam2 <> "")
    {
      $this->pdf->ln(1);
      $this->pdf->row(array('', $naam2));
    }
    
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Adres']));
    $this->pdf->ln(1);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Woonplaats']));
    
		$this->pdf->SetY(75);

		$rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
		                     vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                     date("Y",$this->rapportageDatumVanafJul).
		                     ' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
		                     date("d",$this->rapportageDatumJul)." ".
		                     vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                     date("Y",$this->rapportageDatumJul);

    $this->pdf->SetWidths(array(30,40,5,120));
    $this->pdf->row(array('',vertaalTekst('Verslagperiode',$this->pdf->rapport_taal),":",$rapportagePeriode));
		$this->pdf->ln();
		$this->pdf->row(array(' ',vertaalTekst('Vermogensrapportage',$this->pdf->rapport_taal),':',formatPortefeuille($this->pdf->portefeuilledata['Portefeuille'])));
    $this->pdf->ln();
    $this->pdf->row(array(' ',vertaalTekst('Mandaat',$this->pdf->rapport_taal),':',vertaalTekst($this->pdf->portefeuilledata['Risicoklasse'],$this->pdf->rapport_taal)));
    $this->pdf->ln();
    $this->pdf->row(array(' ',vertaalTekst('Depotbank',$this->pdf->rapport_taal),':',vertaalTekst($this->pdf->portefeuilledata['DepotbankOmschrijving'],$this->pdf->rapport_taal)));
    $this->pdf->ln();
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->ln(8); 



		$this->pdf->SetY(133);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
		$this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal).': '.date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(2);
		$this->pdf->row(array('',''));


	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;

   
    $this->pdf->rapport_type = "INHOUD";
	  $this->pdf->rapport_titel = "Inhoudsopgave";//Inhoudsopgave
	  $this->pdf->addPage('L');
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;

	}
}
?>
