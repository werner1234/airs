<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/04/28 18:36:15 $
File Versie					: $Revision: 1.3 $

$Log: RapportFRONT_L76.php,v $
Revision 1.3  2018/04/28 18:36:15  rvv
*** empty log message ***

Revision 1.2  2018/04/22 09:30:29  rvv
*** empty log message ***

Revision 1.1  2018/04/18 16:18:39  rvv
*** empty log message ***

Revision 1.7  2015/03/14 17:01:49  rvv
*** empty log message ***

Revision 1.6  2013/10/12 15:54:06  rvv
*** empty log message ***

Revision 1.5  2013/07/31 15:45:41  rvv
*** empty log message ***

Revision 1.4  2013/07/12 07:09:01  rvv
*** empty log message ***

Revision 1.3  2013/04/20 16:34:57  rvv
*** empty log message ***

Revision 1.2  2013/04/17 15:59:22  rvv
*** empty log message ***

Revision 1.1  2013/03/27 17:02:38  rvv
*** empty log message ***

Revision 1.2  2013/03/17 10:58:29  rvv
*** empty log message ***

*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L76
{
	function RapportFront_L76($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
	  $crmData = $this->DB->lookupRecord();
    
		$query = "SELECT
                  Accountmanagers.Naam as accountManager,
                  Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                  Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats
		          FROM
		            Portefeuilles
                LEFT JOIN Accountmanagers ON Accountmanagers.Accountmanager = Portefeuilles.Accountmanager 
                JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
		          WHERE
		            Portefeuilles.Portefeuille = '".$this->portefeuille."' ";
		$this->DB->SQL($query);
		$this->DB->Query();
		$portefeuilledata = $this->DB->nextRecord();
    if($crmData['verzendPc']<>'')
      $crmData['verzendPlaats']=$crmData['verzendPc'].' '.$crmData['verzendPlaats'];

		$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');
    

		if(is_file($this->pdf->rapport_logo))
		{
		   $factor=0.06;
		   $xSize=983*$factor;//$x=885*$factor;
		   $ySize=217*$factor;//$y=849*$factor;
			 $this->pdf->Image($this->pdf->rapport_logo, 229, 5, $xSize, $ySize);
     //  $this->pdf->SetFillColor(255,255,255);
     //  $this->pdf->Rect(229,18,$xSize,5,"F");
       
		}
    
    $this->pdf->SetDrawColor($this->pdf->rapport_oranje[0],$this->pdf->rapport_oranje[1],$this->pdf->rapport_oranje[2]);
    $this->pdf->Line($this->pdf->marge,25,297-$this->pdf->marge,25);
    
    $this->pdf->SetTextColor($this->pdf->rapport_blauw[0],$this->pdf->rapport_blauw[1],$this->pdf->rapport_blauw[2]);
   	$this->pdf->widthA = array(30,180);
		$this->pdf->alignA = array('L','L','L');
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->SetY(56);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(' ',vertaalTekst('VERMOGENSRAPPORTAGE',$this->pdf->rapport_taal)));
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$fontsize = 10; //$this->pdf->rapport_fontsize
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
 
    $this->pdf->row(array(' ','Voor rekening',':',$this->pdf->portefeuilledata['PortefeuilleVoorzet'].$this->pdf->portefeuilledata['Portefeuille']));
		$this->pdf->ln(3);
    

    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->ln(8); 
    $this->pdf->SetTextColor($this->pdf->rapport_blauw[0],$this->pdf->rapport_blauw[1],$this->pdf->rapport_blauw[2]);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(' ','PERSOONLIJK EN VERTROUWELIJK'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam']));
    if($this->pdf->portefeuilledata['Naam1'] <> '')
    {
      $this->pdf->ln(.5);
      $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam1']));
    }
    $this->pdf->ln(.5);
    $this->pdf->row(array('',$crmData['verzendAdres']));
    $this->pdf->ln(.5);
    $this->pdf->row(array('',$crmData['verzendPlaats']));
    $this->pdf->ln(.5);
    $this->pdf->row(array('',$crmData['verzendLand']));

		$this->pdf->SetY(133);
    $this->pdf->SetWidths(array(30,45,70));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Uw overeenkomst:',$this->pdf->portefeuilledata['SoortOvereenkomst']));
    $this->pdf->ln(2);
    $this->pdf->row(array('','Uw profiel:',$this->pdf->portefeuilledata['Risicoklasse']));
    $this->pdf->ln(2);
		$this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal).':',date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(2);
    $this->pdf->row(array('','Uw relatiebeheerder:',$portefeuilledata['accountManager']));
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
      
      $this->pdf->SetDrawColor($this->pdf->rapport_oranje[0],$this->pdf->rapport_oranje[1],$this->pdf->rapport_oranje[2]);
      $this->pdf->Line($this->pdf->marge,$voetbeginY,$pageWidth-$this->pdf->marge,$voetbeginY);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

    
      $this->pdf->frontPage = false;
    $this->pdf->AutoPageBreak=false;
    $this->pdf->SetY(195);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize-2);
    $this->pdf->Cell(297,5,$portefeuilledata['vermogensbeheerderAdres'].', '.$portefeuilledata['vermogensbeheerderWoonplaats'].' - www.ambassadorvermogensbeheer.nl - info@ambassadorvermogensbeheer.nl - 035-2031035',0,1,'C');
    $this->pdf->Cell(297,5,'IBAN: NL59 ABNA 0516 0106 89 - KvK: 28087987 - BTW: NL8092.88.722 B01',0,1,'C');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    $this->pdf->AutoPageBreak=true;


		$this->pdf->SetY(170);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-2);



    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->frontPage = true;


    $this->pdf->rapport_type = "INHOUD";
    $this->pdf->rapport_titel = "Inhoudsopgave";//Inhoudsopgave
    $this->pdf->addPage('L');
    $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;
    $this->pdf->SetDrawColor(0);

	}
}
?>
