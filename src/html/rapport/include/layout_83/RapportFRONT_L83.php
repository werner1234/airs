<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/08/31 13:13:05 $
File Versie					: $Revision: 1.5 $

$Log: RapportFRONT_L83.php,v $
Revision 1.5  2019/08/31 13:13:05  rvv
*** empty log message ***

Revision 1.4  2019/07/13 17:50:20  rvv
*** empty log message ***

Revision 1.3  2019/05/25 16:22:55  rvv
*** empty log message ***

Revision 1.2  2019/04/20 16:59:05  rvv
*** empty log message ***

Revision 1.1  2019/03/02 18:23:01  rvv
*** empty log message ***

Revision 1.10  2019/01/07 09:57:06  rvv
*** empty log message ***

Revision 1.9  2019/01/07 06:26:00  rvv
*** empty log message ***

Revision 1.8  2019/01/06 12:43:52  rvv
*** empty log message ***

Revision 1.7  2019/01/05 18:38:35  rvv
*** empty log message ***

Revision 1.6  2018/09/12 11:41:19  rvv
*** empty log message ***

Revision 1.5  2018/01/04 13:41:18  rvv
*** empty log message ***

Revision 1.4  2018/01/03 14:19:56  rvv
*** empty log message ***

Revision 1.3  2017/12/30 16:38:17  rvv
*** empty log message ***

Revision 1.2  2017/12/28 06:20:02  rvv
*** empty log message ***

Revision 1.1  2017/12/27 18:29:09  rvv
*** empty log message ***

*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFRONT_L83
{
	function RapportFRONT_L83($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
    if(isset($this->pdf->__appvar['consolidatie']))
      $this->consolidatie=true;
    else
      $this->consolidatie=false;
    
    $this->factor=0.025;
    $this->imageSizeX=2050*$this->factor;
    $this->imageSizeY=391*$this->factor;
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
LEFT JOIN  CRM_naw on Portefeuilles.portefeuille=CRM_naw.Portefeuille WHERE CRM_naw.Portefeuille = '".$this->portefeuille."'";;

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
    
    $this->eerstePagina();
    $this->inhoudsPagina();
    

	}
  
  function inhoudsPagina()
  {
    //$this->pdf->frontPage=true;
	  $this->pdf->rapport_type = "INHOUD";
	  $this->pdf->rapport_titel = "Inhoudsopgave";//
	  $this->pdf->addPage('L');
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    if(is_file($this->pdf->rapport_logo))
			 $this->pdf->Image($this->pdf->rapport_logo, 297/2-$this->imageSizeX/2, 7, $this->imageSizeX, $this->imageSizeY);
	  
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
 }
  
  
  function eerstePagina()
  {
    global $__appvar;
		$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');

		if(is_file($this->pdf->rapport_logo))
			 $this->pdf->Image($this->pdf->rapport_logo, 297/2-$this->imageSizeX/2, 7, $this->imageSizeX, $this->imageSizeY);
	  
   	$this->pdf->widthA = array(30,180);
		$this->pdf->alignA = array('L','L','L');
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->SetY(56);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',10);
    if($this->consolidatie)
    {
      $this->pdf->row(array(' ', vertaalTekst('Geconsolideerde vermogensrapprtage', $this->pdf->rapport_taal)));
      $this->pdf->row(array(' ',  $this->pdf->__appvar['consolidatie']['portefeuillenaam1']));
      $this->pdf->row(array(' ',  $this->pdf->__appvar['consolidatie']['portefeuillenaam2']));
    }
    else
    {
      $this->pdf->row(array(' ', vertaalTekst('VERMOGENSRAPPORTAGE', $this->pdf->rapport_taal)));
    }
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'',10);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

    $this->pdf->SetWidths(array(30,40,5,70));
 		$rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
		                                          date("d",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);
		$this->pdf->row(array('',vertaalTekst('Periode',$this->pdf->rapport_taal),':',$rapportagePeriode));
		$this->pdf->ln(3);
    if($this->consolidatie==false)
    {
      $this->pdf->row(array(' ',vertaalTekst('Rekening',$this->pdf->rapport_taal), ':', $this->pdf->portefeuilledata['PortefeuilleVoorzet'] . $this->pdf->portefeuilledata['Portefeuille']));
      $this->pdf->ln(3);
    }
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->ln(8); 
    $this->pdf->SetFont($this->pdf->rapport_font,'B',10);
    $this->pdf->row(array(' ',vertaalTekst('PERSOONLIJK EN VERTROUWELIJK',$this->pdf->rapport_taal)));
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

    $this->pdf->ln(3);
    $this->pdf->SetFont($this->pdf->rapport_font,'',10);
    if($this->consolidatie==false)
    {
       $this->pdf->row(array('', $this->pdf->portefeuilledata['Naam']));
       if ($this->pdf->portefeuilledata['Naam1'] <> '')
       {
         $this->pdf->ln(.5);
         $this->pdf->row(array('', $this->pdf->portefeuilledata['Naam1']));
       }
       if($_POST['anoniem']<>1)
       {
        $this->pdf->ln(.5);
        $this->pdf->row(array('', $this->crmData['verzendAdres']));
        $this->pdf->ln(.5);
        $this->pdf->row(array('', $this->crmData['verzendPlaats']));
        $this->pdf->ln(.5);
        if($this->crmData['verzendLand'] <> 'Nederland')
           $this->pdf->row(array('', $this->crmData['verzendLand']));
       }
       $extraY=0;
    }
    else
    {
      $portefeuilledata=array();
      foreach ($this->pdf->portefeuilles as $portefeuille)
      {
        $query = "SELECT
	            	if(CRM_naw.naam<>'',CRM_naw.naam,Clienten.Naam) as Naam,
                if(CRM_naw.naam<>'',CRM_naw.naam1,Clienten.Naam1) as Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Portefeuilles.PortefeuilleVoorzet,
                Accountmanagers.Naam as accountManager,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
		          FROM
		            Portefeuilles
		            LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
		            LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
		            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
		            LEFT Join CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
		          WHERE
		            Portefeuilles.Portefeuille = '".$portefeuille."'";
    
        $this->DB->SQL($query);
        $this->DB->Query();
        $portefeuilledata[] = $this->DB->nextRecord();
    
      }
      $this->pdf->ln();
      $this->pdf->SetWidths(array(30,25,25,75,75));
      $this->pdf->SetAligns(array('L','L','L','L','L'));
      $this->pdf->row(array('','Portefeuille','Depotbank','Naam'));
  
      foreach ($portefeuilledata as $port)
      {
        $this->pdf->row(array('',$port['PortefeuilleVoorzet'].$port['Portefeuille'],$port['Depotbank'],$port['Naam'],$port['Naam1']));
      }
      $extraY=10;
    }
/*
		$this->pdf->SetY(133+$extraY);
    $this->pdf->SetWidths(array(30,50,70));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal).':',date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(2);
		if($this->consolidatie==false)
    {
       $this->pdf->row(array('', 'Uw vermogensbeheerder:', $this->portefeuilledata['accountManager']));
       $this->pdf->ln(2);
    }
    $this->pdf->row(array('','Telefoon:',$this->portefeuilledata['telefoon']));
    $this->pdf->ln(2);
		$this->pdf->row(array('',''));
*/
    
      if($this->pdf->CurOrientation=='P')
      {
        $voetbeginY=280;
      }
      else
      {
  	    $voetbeginY=185;
      }


      $this->pdf->SetFont($this->pdf->rapport_font,'',10);
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

     // $this->pdf->frontPage = false;
      $this->pdf->AutoPageBreak=false;
      $this->pdf->SetY($voetbeginY);
      $this->pdf->SetWidths(array(30,130,25,50));
      $this->pdf->row(array('',$this->portefeuilledata['Naam'],' ',' '));
      $this->pdf->row(array('', $this->portefeuilledata['Adres'], ' ', ' '));
      $this->pdf->row(array('', $this->portefeuilledata['Woonplaats'], ' ', ''));
      $this->pdf->row(array('', $this->portefeuilledata['telefoon'], ' ', ''));
      $this->pdf->ln(2);
      $this->pdf->AutoPageBreak=true;
   	  $this->pdf->SetY(170);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
  }
  

  
  
}
?>
