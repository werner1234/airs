<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/01/12 17:08:31 $
File Versie					: $Revision: 1.2 $

$Log: RapportFRONT_L80.php,v $
Revision 1.2  2019/01/12 17:08:31  rvv
*** empty log message ***

Revision 1.1  2018/10/03 15:42:01  rvv
*** empty log message ***



*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L80
{
	function RapportFront_L80($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
			$factor=0.02;
			$xSize=1518*$factor;
			$ySize=1008*$factor;

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

		$this->pdf->row(array('',$crmData['naam']));
		if (trim($crmData['naam1']) <> "")
			$this->pdf->row(array('',$crmData['naam1']));
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
		$this->pdf->PageBreakTrigger=$this->pdf->PageBreakTrigger+10;

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
    
    
    $query="SHOW COLUMNS FROM CRM_naw like 'Mandaat'";
    if($this->DB->Qrecords($query)>0)
    {
      $crmVeld='CRM_naw.Mandaat,';
      $crmJoin='LEFT JOIN CRM_naw ON CRM_naw.Portefeuille=Portefeuilles.Portefeuille';
    }
    else
		{
      $crmVeld='';
      $crmJoin='';
		}
  
		$query = "SELECT
		            Clienten.Naam,
                Clienten.Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                $crmVeld
                Portefeuilles.Portefeuille,
                Accountmanagers.Naam as accountManager,
                 Vermogensbeheerders.Naam as vermogensbeheerderNaam,
                 Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                 Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
		          FROM
		            (Portefeuilles, Clienten , Accountmanagers, Vermogensbeheerders)
		            LEFT JOIN CRM_naw ON CRM_naw.Portefeuille=Portefeuilles.Portefeuille
		          WHERE
		            Portefeuilles.Portefeuille = '".$this->portefeuille."' AND
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
      
      $factor=0.02;
      $xSize=1518*$factor;
      $ySize=1008*$factor;
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
    $this->pdf->ln(1);
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

    $this->pdf->SetWidths(array(30,40,5,180));
    $this->pdf->row(array('',vertaalTekst('Verslagperiode',$this->pdf->rapport_taal),":",$rapportagePeriode));
		$this->pdf->ln();
		$this->pdf->row(array(' ',vertaalTekst('Vermogensrapportage',$this->pdf->rapport_taal),':',formatPortefeuille($this->pdf->portefeuilledata['Portefeuille'])));
    $this->pdf->ln();
    $this->pdf->row(array(' ',vertaalTekst('Mandaat',$this->pdf->rapport_taal),':',vertaalTekst($portefeuilledata['Mandaat'],$this->pdf->rapport_taal)));
    $this->pdf->ln();
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->ln(8); 



		$this->pdf->SetY(133+24);
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
