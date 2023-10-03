<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/04 15:59:25 $
File Versie					: $Revision: 1.22 $

$Log: RapportFRONT_L67.php,v $
Revision 1.22  2020/07/04 15:59:25  rvv
*** empty log message ***

Revision 1.21  2020/06/24 13:02:41  rvv
*** empty log message ***

Revision 1.20  2019/12/14 17:46:24  rvv
*** empty log message ***

Revision 1.19  2019/11/06 16:11:20  rvv
*** empty log message ***

Revision 1.18  2019/02/03 13:43:54  rvv
*** empty log message ***

Revision 1.17  2018/10/31 17:23:34  rvv
*** empty log message ***

Revision 1.16  2018/10/29 06:34:35  rvv
*** empty log message ***

Revision 1.15  2018/10/27 16:49:57  rvv
*** empty log message ***

Revision 1.14  2018/10/23 05:41:47  rvv
*** empty log message ***

Revision 1.13  2018/09/02 12:02:03  rvv
*** empty log message ***

Revision 1.12  2018/09/01 16:53:24  rvv
*** empty log message ***

Revision 1.11  2018/07/18 11:31:31  rvv
*** empty log message ***

Revision 1.10  2017/11/18 18:58:17  rvv
*** empty log message ***

Revision 1.9  2017/11/15 17:03:35  rvv
*** empty log message ***

Revision 1.8  2017/04/21 14:26:14  cvs
bij brief witdhA van 180 naar 155

Revision 1.7  2017/04/21 12:52:35  cvs
handtekening aangepast

Revision 1.6  2017/03/08 16:53:15  rvv
*** empty log message ***

Revision 1.5  2017/02/19 09:21:57  rvv
*** empty log message ***

Revision 1.4  2016/04/10 15:48:34  rvv
*** empty log message ***

Revision 1.3  2016/04/03 10:58:02  rvv
*** empty log message ***

Revision 1.2  2016/03/27 17:31:28  rvv
*** empty log message ***

Revision 1.1  2016/03/06 18:17:00  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFRONT_L67
{
	function RapportFRONT_L67($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FRONT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);


		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul=db2jul($this->rapportageDatum);
		$this->pdf->rapportCounter = count($this->pdf->page);

		$this->DB = new DB();

	}



	function writeRapport()
	{
		global $__appvar;


			  $query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.verzendAdres,
CRM_naw.verzendPc,
CRM_naw.verzendPlaats,
CRM_naw.verzendLand,
CRM_naw.verzendAanhef,
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
Portefeuilles.BetalingsinfoMee
FROM Portefeuilles  
LEFT JOIN  CRM_naw on Portefeuilles.portefeuille=CRM_naw.Portefeuille WHERE CRM_naw.Portefeuille = '".$this->portefeuille."'  ";

	  $this->DB->SQL($query);
	  $this->crmData = $this->DB->lookupRecord();
    
		$query = "SELECT
                  Accountmanagers.Naam as accountManager,
                  Gebruikers.emailAdres as emailAdres,
                  Vermogensbeheerders.telefoon,
                  Vermogensbeheerders.email,
                  Vermogensbeheerders.Naam,
                  Vermogensbeheerders.Adres,
                  Vermogensbeheerders.Woonplaats
		          FROM
		            Portefeuilles
                LEFT JOIN Accountmanagers ON Accountmanagers.Accountmanager = Portefeuilles.Accountmanager 
                LEFT JOIN Gebruikers ON Accountmanagers.Accountmanager=Gebruikers.Accountmanager
                JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
		          WHERE
		            Portefeuilles.Portefeuille = '".$this->portefeuille."' ";
		$this->DB->SQL($query);
		$this->DB->Query();
		$this->portefeuilledata = $this->DB->nextRecord();
    if($this->crmData['verzendPc']<>'')
      $this->crmData['verzendPlaats']=$this->crmData['verzendPc'].' '.$this->crmData['verzendPlaats'];
  
   
	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	//  $this->pdf->frontPage = true;
    $this->factor=0.06;
    $this->imageSizeX=961*$this->factor;//$x=885*$factor;
    $this->imageSizeY=331*$this->factor;//$x=885*$factor;
	//	$ySize=182*$this->factor;//$y=849*$factor;

    if($this->pdf->selectData['allInOne']==1 || ($this->pdf->selectData['backoffice']==1  && $this->pdf->selectData['type']=='pdf'))
    {
      $this->briefPagina();
    }
    $this->eerstePagina();
    $this->tweedePagina();
    
    $this->inhoudsPagina();
    

	}
  
  function inhoudsPagina()
  {
    
    //$this->pdf->Line($this->pdf->marge,25,297-$this->pdf->marge,25);
    $this->pdf->frontPage=true;
	  $this->pdf->rapport_type = "FRONT";
	  $this->pdf->rapport_titel = "";//Inhoudsopgave
	  $this->pdf->addPage('L');
    if(is_file($this->pdf->rapport_logo))
			 $this->pdf->Image($this->pdf->rapport_logo, 297/2-$this->imageSizeX/2, 5, $this->imageSizeX, $this->imageSizeY);
	  
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;
    //$this->pdf->SetDrawColor($this->pdf->rapport_paars[0],$this->pdf->rapport_paars[1],$this->pdf->rapport_paars[2]);
    //$this->pdf->Line($this->pdf->marge,190+$lijnY,297-$this->pdf->marge,190+$lijnY);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
 }
  
  
  function eerstePagina()
  {
    global $__appvar;
		$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');

		if(is_file($this->pdf->rapport_logo))
			 $this->pdf->Image($this->pdf->rapport_logo, 297/2-$this->imageSizeX/2, 5, $this->imageSizeX, $this->imageSizeY);
	  
    //$this->pdf->Line($this->pdf->marge,25,297-$this->pdf->marge,25);
   	$this->pdf->widthA = array(30,180);
		$this->pdf->alignA = array('L','L','L');
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->SetY(56);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(' ',vertaalTekst('VERMOGENSRAPPORTAGE',$this->pdf->rapport_taal)));
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		//$fontsize = 10; //$this->pdf->rapport_fontsize
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0);


    $this->pdf->SetWidths(array(30,40,5,70));
 		$rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
		                                          date("d",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);
		$this->pdf->row(array('',vertaalTekst('Voor de periode',$this->pdf->rapport_taal),':',$rapportagePeriode));
		$this->pdf->ln(3);
 
    $this->pdf->row(array(' ',vertaalTekst('Voor rekening',$this->pdf->rapport_taal),':',$this->pdf->portefeuilledata['PortefeuilleVoorzet'].$this->pdf->portefeuilledata['Portefeuille']));
		$this->pdf->ln(3);
    

    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->ln(8); 
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(' ',vertaalTekst('PERSOONLIJK EN VERTROUWELIJK',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam']));
    if($this->pdf->portefeuilledata['Naam1'] <> '')
    {
      $this->pdf->ln(.5);
      $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam1']));
    }
    $this->pdf->ln(.5);
    $this->pdf->row(array('',$this->crmData['verzendAdres']));
    $this->pdf->ln(.5);
    $this->pdf->row(array('',$this->crmData['verzendPlaats']));
    $this->pdf->ln(.5);
    $this->pdf->row(array('',$this->crmData['verzendLand']));

		$this->pdf->SetY(133);
    $this->pdf->SetWidths(array(30,40,5,70));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',vertaalTekst('Uw profiel',$this->pdf->rapport_taal),':',$this->pdf->portefeuilledata['Risicoklasse']));
    $this->pdf->ln(2);
		$this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal),':',date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(2);
    $this->pdf->row(array('',vertaalTekst('Uw vermogensbeheerder',$this->pdf->rapport_taal),':',$this->portefeuilledata['accountManager']));
    $this->pdf->ln(2);
    $this->pdf->row(array('',vertaalTekst('Telefoon',$this->pdf->rapport_taal),':',$this->portefeuilledata['telefoon']));
    $this->pdf->ln(2);
    $this->pdf->row(array('',vertaalTekst('Email',$this->pdf->rapport_taal),':',$this->portefeuilledata['emailAdres']));
    $this->pdf->ln(2);
		$this->pdf->row(array('',''));
    
    
      if($this->pdf->CurOrientation=='P')
      {
        $voetbeginY=285;
        $pageWidth=210;
      }
      else
      {
  	    $voetbeginY=190;
        $pageWidth=297;
      }

                  
      //$this->pdf->SetDrawColor($this->pdf->rapport_paars[0],$this->pdf->rapport_paars[1],$this->pdf->rapport_paars[2]);
      //$this->pdf->Line($this->pdf->marge,$voetbeginY+$lijnY,$pageWidth-$this->pdf->marge,$voetbeginY+$lijnY);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

    
      $this->pdf->frontPage = false;
      $this->pdf->AutoPageBreak=false;
      $this->pdf->SetY($voetbeginY);
      $this->pdf->SetWidths(array(30,30,25,50));
      $this->pdf->row(array('',$this->portefeuilledata['Naam'],' ',' '));
      $this->pdf->row(array('',$this->portefeuilledata['Adres'],' ',' '));
      $this->pdf->row(array('',$this->portefeuilledata['Woonplaats'],' ',''));
      $this->pdf->ln(2);
      $this->pdf->AutoPageBreak=true;
   	  $this->pdf->SetY(170);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-2);
    
  }
  
  function tweedePagina()
  {
    global $__appvar;
		$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');

		if(is_file($this->pdf->rapport_logo))
		{
			 $this->pdf->Image($this->pdf->rapport_logo, 297/2-$this->imageSizeX/2, 5, $this->imageSizeX, $this->imageSizeY);
		}
    
    //$this->pdf->Line($this->pdf->marge,25,297-$this->pdf->marge,25);
   	$this->pdf->widthA = array(30,125);
		$this->pdf->alignA = array('L','L','L');
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->SetY(40);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    if($this->pdf->rapport_taal==1)
      $this->pdf->row(array(' ',vertaalTekst('Disclaimer and legal notice',$this->pdf->rapport_taal)));
    else
      $this->pdf->row(array(' ',vertaalTekst('Disclaimer en juridische kennisgeving',$this->pdf->rapport_taal)));
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    if($this->pdf->rapport_taal==1)
    {
      $this->pdf->row(array(' ', 'The information presented in this investment portfolio statement may not be used for tax purposes. Only the end-of-year tax statement should be used for that purpose.
      
This portfolio statement is strictly personal and aims to provide a factual overview of your investment portfolio. If you spot any errors or omissions in this report, please notify your dedicated contact at Fair Capital Partners as soon as possible. The positions in this portfolio have been valued at the price known to us on the date this report was prepared. We can therefore not guarantee – especially not for non-listed securities – that they reflect current prices. Investing comes with risks. Past performance is no guarantee of future results. You may derive no rights from the contents of this report.

The information in this publication is protected by intellectual property rights, including but not limited to copyrights, trademark rights and data base rights. All rights reserved, Fair Capital Partners.

No part of this publication may be reproduced, stored in automated databases, published or used for any other purpose, whether commercial or non-commercial, in any form or by any means, without the prior written consent of Fair Capital Partners.
'));
    }
    else
    {
      $this->pdf->row(array(' ', 'De gegevens uit deze vermogensrapportage zijn niet bedoeld voor fiscale doeleinden. Hiertoe dient u uitsluitend het binnenkort te ontvangen fiscale jaaroverzicht te gebruiken.
Deze rapportage betreffende uw beleggingsportefeuille is strikt persoonlijk en beoogt een feitelijke weergave te geven van uw beleggingsportefeuille. Bij constatering van een onjuistheid of onvolledigheid in deze rapportage, verzoeken wij u vriendelijk uw contactpersoon bij uw vermogensbeheerder/-adviseur zo spoedig mogelijk in kennis te stellen. De posities van de portefeuille worden gewaardeerd tegen de bij ons laatst bekende koersen op de datum van opmaak van de rapportage. Met name van niet-beursgenoteerde effecten is daarom niet altijd de actualiteit te garanderen. Beleggingen zijn omgeven met risico. In het verleden behaalde rendementen vormen derhalve geen garantie voor toekomstige resultaten. U kunt geen rechten ontlenen aan de inhoud van deze rapportage.

De in deze publicatie weergegeven informatie is beschermd door middel van rechten van intellectuele eigendom waaronder mede doch niet uitsluitend begrepen Auteursrechten, Merkrechten en Databankrechten. Al deze rechten worden voorbehouden door Fair Capital Partners. Niets uit deze publicatie mag worden verveelvoudigd, opgeslagen in een geautomatiseerd gegevensbestand, openbaargemaakt, of voor al dan niet commerciële doeleinden worden gebruikt, in enigerlei vorm of op enige andere manier, zonder voorafgaande schriftelijke toestemming van Fair Capital Partners.'));
    }
    
      if($this->pdf->CurOrientation=='P')
      {
        $voetbeginY=285;
        $pageWidth=210;
      }
      else
      {
  	    $voetbeginY=190;
        $pageWidth=297;
      }

      //$this->pdf->SetDrawColor($this->pdf->rapport_paars[0],$this->pdf->rapport_paars[1],$this->pdf->rapport_paars[2]);
      //$this->pdf->Line($this->pdf->marge,$voetbeginY+$lijnY,$pageWidth-$this->pdf->marge,$voetbeginY+$lijnY);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

    
    
  }
  
  function briefPagina()
  {
    global $__appvar;
		$this->pdf->frontPage = true;
    $this->pdf->AddPage('P');
    if($this->pdf->selectData['allInOne']==1)
    {
      $this->pdf->oddEvenCheck[$this->portefeuille]=count($this->pdf->pages);
    }
    $this->pdf->emailSkipPages[]=$this->pdf->page;
    $this->brief_font=$this->pdf->rapport_font;
    $this->brief_fontSize=$this->pdf->rapport_fontsize;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
   	$this->pdf->widthA = array(20,155);
		$this->pdf->alignA = array('L','L','L');
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    
		if(is_file($this->pdf->rapport_logo))
			 $this->pdf->Image($this->pdf->rapport_logo, 210-16-$this->imageSizeX*1.4, 16, $this->imageSizeX*1.4);
    
   $query = "SELECT
		            Clienten.Naam,
                Clienten.Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Clienten.Land,
                Portefeuilles.Portefeuille,
                Portefeuilles.Risicoklasse,
                Accountmanagers.Naam as accountManager,
                Accountmanagers.Handtekening as handtekening,
                Accountmanagers.Titel as titel,
                Vermogensbeheerders.Naam as VermogensbeheerderNaam,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email,
                tweedeAanspreekpunt.naam as tweedeAanspreekpunt,
                tweedeAanspreekpunt.Handtekening as tweedeHandtekening
		          FROM
		            (Portefeuilles, Clienten , Accountmanagers, Vermogensbeheerders)
                LEFT JOIN Accountmanagers as tweedeAanspreekpunt ON Portefeuilles.tweedeAanspreekpunt= tweedeAanspreekpunt.Accountmanager
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
        
    if(isset($this->pdf->extraAdres['naam']))
      $portefeuilledata['Naam']=$this->pdf->extraAdres['naam'];
    if(isset($this->pdf->extraAdres['naam1']))
      $portefeuilledata['Naam1']=$this->pdf->extraAdres['naam1'];
    if(isset($this->pdf->extraAdres['adres']))
      $portefeuilledata['Adres']=$this->pdf->extraAdres['adres'];
    if(isset($this->pdf->extraAdres['pc']))
      $portefeuilledata['Woonplaats']=$this->pdf->extraAdres['pc'].' '.$this->pdf->extraAdres['plaats'];           
    if(isset($this->pdf->extraAdres['land']))
      $portefeuilledata['Land']=$this->pdf->extraAdres['land'];  

	  $query = "SELECT verzendAanhef FROM CRM_naw WHERE portefeuille = '".$this->portefeuille."' ";
	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();
    $portefeuilledata['aanhef']=$crmData['verzendAanhef'];

	  $extraDagen = 0; //2
	  $this->pdf->SetY(50);
	 // $this->pdf->SetFont($this->brief_font,'B',$this->brief_fontSize);
	 // $this->pdf->row(array('',"Vertrouwelijk"));
	 // $this->pdf->SetFont($this->brief_font,'',$this->brief_fontSize);
		$this->pdf->row(array('',$portefeuilledata['Naam']));
    if ($portefeuilledata['Naam1'] != '')
      $this->pdf->row(array('',$portefeuilledata['Naam1']));
    $this->pdf->row(array('',$portefeuilledata['Adres']));
    $this->pdf->row(array('',$portefeuilledata['Woonplaats']));
    $this->pdf->row(array('',$portefeuilledata['Land']));


    $this->pdf->SetY(105);
    //$this->pdf->SetFont($this->brief_font,'',$this->brief_fontSize);
    $this->pdf->row(array('','Muiden, '.(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
    $this->pdf->ln(8);

    $rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' t/m '.
		                                          date("d",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);

    //$this->pdf->row(array('','Betreft: Rapportage over de periode '.$rapportagePeriode));
    $this->pdf->SetFont($this->pdf->brief_font,'',$this->pdf->brief_fontSize);
    //$this->pdf->SetY(140);
    $this->pdf->row(array('',$portefeuilledata['aanhef'].",\n\n"));

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
		  }

    $this->pdf->ln();
    $this->pdf->row(array('',$briefData));

//    if($portefeuilledata['handtekening'] <> '')
//      $this->pdf->MemImage(base64_decode($portefeuilledata['handtekening']), $this->pdf->getX()+20, $this->pdf->getY(), 60);
//    if($portefeuilledata['tweedeHandtekening'] <> '')
//      $this->pdf->MemImage(base64_decode($portefeuilledata['tweedeHandtekening']), $this->pdf->getX()+95, $this->pdf->getY(), 60);
    $this->pdf->ln(14);
    $this->pdf->SetWidths(array(20,75,75));
    $this->pdf->row(array('',"Met vriendelijke groet,\n\n\n\n\n".$portefeuilledata['accountManager']."\nFair Capital Partners","")); //
   // $this->pdf->row(array('',$portefeuilledata['titel']));
    //$portefeuilledata['accountManager'] $portefeuilledata['handtekening'] $portefeuilledata['titel']

    
      if($this->pdf->CurOrientation=='P')
      {
        $voetbeginY=280;
        $pageWidth=210;
      }
      else
      {
  	    $voetbeginY=185;
        $pageWidth=297;
      }
  
  
    $this->pdf->AutoPageBreak=false;
    $this->pdf->SetY($voetbeginY);
    $this->pdf->SetWidths(array(30,180));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-1);
    $this->pdf->row(array('','Herengracht 33      1398 AB Muiden (NL)      tel. +31(0)294 745 300      vermogensbeheer@faircapitalpartners.nl'));
    $this->pdf->row(array('','www.faircapitalpartners.nl      KvK 32060125      BTW NL8818.964.B.01'));
    $this->pdf->AutoPageBreak=true;

      //$this->pdf->SetDrawColor($this->pdf->rapport_paars[0],$this->pdf->rapport_paars[1],$this->pdf->rapport_paars[2]);
      //$this->pdf->Line($this->pdf->marge,$voetbeginY+$lijnY,$pageWidth-$this->pdf->marge,$voetbeginY+$lijnY);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
    $this->pdf->AddPage('P');
    $this->pdf->emailSkipPages[]=$this->pdf->page;
    
    
  }
  
}
?>
