<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/07/05 16:42:29 $
File Versie					: $Revision: 1.6 $

$Log: RapportFRONT_L24.php,v $
Revision 1.6  2019/07/05 16:42:29  rvv
*** empty log message ***

Revision 1.5  2015/10/21 09:29:23  rvv
*** empty log message ***

Revision 1.4  2015/10/21 08:11:50  rvv
*** empty log message ***

Revision 1.3  2015/10/18 13:46:20  rvv
*** empty log message ***

Revision 1.2  2015/10/14 16:12:05  rvv
*** empty log message ***

Revision 1.1  2010/03/31 17:26:12  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L24
{
	function RapportFront_L24($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FRONT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_FRONT_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_FRONT_titel;
		else
			$this->pdf->rapport_titel = "Titel pagina";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul=db2jul($this->rapportageDatum);
		$this->pdf->extraPage =0;
		$this->DB = new DB();

		$this->rapportMaand 	= date("n",$this->rapportageDatumJul);
		$this->rapportDag 		= date("d",$this->rapportageDatumJul);
		$this->rapportJaar 		= date("Y",$this->rapportageDatumJul);

		$this->pdf->brief_font = $this->pdf->rapport_font;

	}



  function FrontPagina()
  {
    global $__appvar;
    $this->pdf->addPage('L');
    
    if(is_file($this->pdf->rapport_logo))
      $this->pdf->Image($this->pdf->rapport_logo, 297/2-43/2, 5, 43);
      
		$query = "SELECT
		            CRM_naw.naam as Naam,
                CRM_naw.naam1 as Naam1,
                CRM_naw.verzendAdres as Adres,
                CRM_naw.verzendPc as Pc,
                CRM_naw.verzendPlaats as Woonplaats,
                CRM_naw.verzendLand as Land,
                Portefeuilles.Portefeuille,
                Portefeuilles.Risicoklasse,
                Accountmanagers.Naam as accountManager,
                 Vermogensbeheerders.Naam as vermogensbeheerderNaam,
                 Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                 Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
		          FROM
		            (Portefeuilles, Clienten , Accountmanagers, Vermogensbeheerders)
		            LEFT JOIN CRM_naw ON Portefeuilles.portefeuille = CRM_naw.Portefeuille
		          WHERE
		            Portefeuilles.Portefeuille = '".$this->portefeuille."' AND
		            Portefeuilles.Client = Clienten.Client AND
                Accountmanagers.Accountmanager = Portefeuilles.Accountmanager AND
                Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder";
		$this->DB->SQL($query);
		$this->DB->Query();
		$portefeuilledata = $this->DB->nextRecord();
    
   	$this->pdf->widthA = array(30,180);
		$this->pdf->alignA = array('L','L','L');

		$fontsize = 10; //$this->pdf->rapport_fontsize
		$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->SetY(48);
		$this->pdf->SetWidths(array(30,120));
		$this->pdf->row(array('',$portefeuilledata['Naam']));
		$this->pdf->ln(2);
    if ($portefeuilledata['Naam1'] != '')
    {
      $this->pdf->row(array('',$portefeuilledata['Naam1']));
      $this->pdf->ln(2);
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    $this->pdf->row(array('',$portefeuilledata['Adres']));
    $this->pdf->ln(2);

    $plaats=$portefeuilledata['Pc'];
    if($portefeuilledata['Woonplaats'] != '') $plaats.=" ".$portefeuilledata['Woonplaats'];
    $this->pdf->row(array('',$plaats));

    if ($portefeuilledata['Land'] != '')
    {
      $this->pdf->ln(2);
      $this->pdf->row(array('',$portefeuilledata['Land']));
    }

    $this->pdf->SetWidths($this->pdf->widthA);

		$this->pdf->SetY(80);

		$rapportagePeriode = vertaalTekst('Verslagperiode',$this->pdf->rapport_taal).' '.date("j",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
		                                          date("j",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);
		$this->pdf->row(array('',$rapportagePeriode));
		$this->pdf->ln(6);

		$oldPortefeuilleString = $portefeuilledata['Portefeuille'];
	  $i=1;
		for($j=0;$j<strlen($oldPortefeuilleString);$j++)
		{
		 if($i>3)
		 {
		  $portefeuilleString.='.';
		  $i=1;
		 }
		 $portefeuilleString.= $oldPortefeuilleString[$j];
		 $i++;
		}

		$rapportageRekening = vertaalTekst('Rapportage rekening',$this->pdf->rapport_taal).' : '.$portefeuilleString;
		$this->pdf->row(array(' ',$rapportageRekening));
    $this->pdf->Ln(6);
		$this->pdf->row(array(' ',vertaalTekst('Risicoprofiel',$this->pdf->rapport_taal).' : '.vertaalTekst($portefeuilledata['Risicoklasse'],$this->pdf->rapport_taal)));

		//$this->pdf->SetY(113);
    $this->pdf->Ln(6);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
		$this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal).': '.date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(2);
		$this->pdf->row(array('',''));

		$this->pdf->SetY(140);

    $explodedName=explode(" ",$portefeuilledata['vermogensbeheerderNaam']);
    foreach ($explodedName as $key=>$word)
      $explodedName[$key]=vertaalTekst($word,$this->pdf->rapport_taal);
		$portefeuilledata['vermogensbeheerderNaam']=implode(" ",$explodedName);

		$this->pdf->row(array('',$portefeuilledata['vermogensbeheerderNaam']));
	  $this->pdf->ln(1);
	  $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderAdres']));
		$this->pdf->ln(1);
	  $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderWoonplaats']));
	  $this->pdf->ln(1);
	  $this->pdf->row(array('',$portefeuilledata['Email']));
	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;    
    
    
  }
  
  function voorBrief()
  {
    global $__appvar;
    $this->pdf->addPage('P');
    $this->pdf->emailSkipPages[]=$this->pdf->page;
    if(is_file($this->pdf->rapport_logo))
      $this->pdf->Image($this->pdf->rapport_logo, 85, 5, 43);
	  $this->pdf->frontPage = true;
	  $this->pdf->SetWidths(array(30,140));
	  $this->pdf->SetAligns(array('R','L'));
    $this->pdf->rowHeight = 5;
    $this->pdf->SetFont($this->pdf->brief_font,'',9);
    $query = "SELECT
		            CRM_naw.naam as Naam,
                CRM_naw.naam1 as Naam1,
                CRM_naw.verzendAdres as Adres,
                CRM_naw.verzendPc as Pc,
                CRM_naw.verzendPlaats as Woonplaats,
                CRM_naw.verzendLand as Land,
                Portefeuilles.Portefeuille,
                Portefeuilles.Risicoklasse,
                Accountmanagers.Naam as accountManager,
                 Vermogensbeheerders.Naam as vermogensbeheerderNaam,
                 Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                 Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
		          FROM
		            (Portefeuilles, Clienten , Accountmanagers, Vermogensbeheerders)
		            LEFT JOIN CRM_naw ON Portefeuilles.portefeuille = CRM_naw.Portefeuille
		          WHERE
		            Portefeuilles.Portefeuille = '".$this->portefeuille."' AND
		            Portefeuilles.Client = Clienten.Client AND
                Accountmanagers.Accountmanager = Portefeuilles.Accountmanager AND
                Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder";
    $this->DB->SQL($query);
    $this->DB->Query();
    $portefeuilledata = $this->DB->nextRecord();
    $this->pdf->SetY(38);
    $this->pdf->row(array('Postadres',$portefeuilledata['vermogensbeheerderAdres'].",".$portefeuilledata['vermogensbeheerderWoonplaats']));
    $this->pdf->SetY(60);
    $this->pdf->SetY(30);
    $this->pdf->SetWidths(array(80,50,50));
    $this->pdf->SetAligns(array('L','R','L'));
    $this->pdf->SetFont($this->pdf->brief_font,'',8);
    $this->pdf->row(array('','','OHV Vermogensmeheer'));// (onderdeel van OHV Vermogensbeheer)
	  $this->pdf->ln(10);
	  $this->pdf->row(array('','Bezoekadres',"Sint Janstraat 67, 4741 AM Hoeven"));
	  $this->pdf->ln(5);
	  $this->pdf->row(array('','Telefoon',"(0165) 50 77 66"));
	  $this->pdf->row(array('','Telefax',"(0165) 50 77 67"));
    $this->pdf->row(array('','E-mail',$portefeuilledata['Email']));
    $this->pdf->ln(5);
    $this->pdf->row(array('','Bankrekening',"NL47RABO00102876924"));
	  $this->pdf->SetAligns(array('R','L'));
	  $this->pdf->SetWidths(array(30,140));


	  $this->pdf->SetFont($this->pdf->brief_font,'',11);
	  $query = "SELECT
                Portefeuilles.Portefeuille,
                Portefeuilles.Risicoklasse,
                Accountmanagers.Naam as accountManager,
                Vermogensbeheerders.Naam as VermogensbeheerderNaam,
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

		$portefeuilledata['Naam']=$this->pdf->portefeuilledata['Naam'];
		$portefeuilledata['Naam1']=$this->pdf->portefeuilledata['Naam1'];
		$portefeuilledata['Adres']=$this->pdf->portefeuilledata['Adres'];
		$portefeuilledata['Woonplaats']=$this->pdf->portefeuilledata['Woonplaats'];
		$portefeuilledata['Land']=$this->pdf->portefeuilledata['Land'];

	  $query = "SELECT verzendAanhef FROM CRM_naw WHERE portefeuille = '".$this->portefeuille."' ";
	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();
    $portefeuilledata['aanhef']=$crmData['verzendAanhef'];

	  $extraDagen = 0; //2
	  $this->pdf->SetY(50);
		$this->pdf->row(array('',$portefeuilledata['Naam']));
    if ($portefeuilledata['Naam1'] != '')
      $this->pdf->row(array('',$portefeuilledata['Naam1']));
    $this->pdf->row(array('',$portefeuilledata['Adres']));
    $this->pdf->row(array('',$portefeuilledata['Woonplaats']));
    $this->pdf->row(array('',$portefeuilledata['Land']));


    $this->pdf->SetY(105);
    $this->pdf->SetFont($this->pdf->brief_font,'',10);
    $this->pdf->row(array('Datum',(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
    $this->pdf->ln(8);
    $this->pdf->row(array('Onderwerp',' Kwartaalrapportage'));
    $this->pdf->SetFont($this->pdf->brief_font,'',11);
    $this->pdf->SetY(140);
    $this->pdf->row(array('',$portefeuilledata['aanhef'].",\n\n"));
    $this->pdf->row(array('',"Bijgaand ontvangt u de kwartaalrapportage per ultimo ".vertaalTekst($__appvar["Maanden"][$this->rapportMaand],$this->pdf->rapport_taal)." ".$this->rapportJaar." betreffende uw effectenportefeuille.

Voor vragen kunt u te allen tijde met ons in contact treden.


Met vriendelijke groet,
"));



    $this->pdf->ln(8);
    $this->pdf->row(array('',$portefeuilledata['VermogensbeheerderNaam']."\n".$portefeuilledata['accountManager']));

    $this->pdf->SetAutoPageBreak(false);
    $this->pdf->SetY(280);
    $this->pdf->SetWidths(array(50,80,50));
    $this->pdf->SetAligns(array('L','R','L'));
    $this->pdf->SetFont($this->pdf->brief_font,'',8);
  
    $this->pdf->SetWidths(array(210-$this->pdf->marge*2));
    $this->pdf->SetAligns(array('C'));
    $this->pdf->row(array($portefeuilledata['VermogensbeheerderNaam']."\nIngeschreven bij de K.v.K. onder nr. 33.248.757"));
    $this->pdf->SetAutoPageBreak(true,20);




  $this->pdf->rowHeight = 4;

   $this->pdf->SetFont($this->pdf->rapport_font,'',11);
	}
  
  
	function writeRapport()
	{
	  global $__appvar;
    if($this->pdf->selectData['type'] != 'eMail')
    {
      $this->voorBrief();
    }
    $this->FrontPagina();
    

  }
  }
?>