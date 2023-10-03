<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/06/10 16:00:44 $
 		File Versie					: $Revision: 1.7 $

 		$Log: RapportFrontC.php,v $
 		Revision 1.7  2015/06/10 16:00:44  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/01/08 16:51:48  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2011/12/04 12:56:32  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2010/10/31 15:42:53  rvv
 		*** empty log message ***

 		Revision 1.3  2010/10/27 16:20:25  rvv
 		*** empty log message ***

 		Revision 1.2  2010/05/30 12:45:04  rvv
 		*** empty log message ***

 		Revision 1.1  2009/01/10 16:01:47  rvv
 		*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFrontC
{
	function RapportFrontC($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

		$this->DB = new DB();

	}


	function writeRapport()
	{
		global $__appvar;
			$this->pdf->frontPage = true;

		foreach ($this->pdf->portefeuilles as $portefeuille)
		{
		$query = "SELECT
	            	if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam,Clienten.Naam) as Naam,
                if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam1,Clienten.Naam1) as Naam1,
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

 if($this->pdf->rapport_layout == 16)
 {
   $this->pdf->AddPage();
   $background = $__appvar['basedir']."/html/rapport/logo/background.jpg";
   if(file_exists($background))
    $this->pdf->Image($background, 0, 0, 300, 210);

		if(is_file($this->pdf->rapport_logo))
			 $this->pdf->Image($this->pdf->rapport_logo, 185, 5, 101, 12);


 }
 elseif($this->pdf->rapport_layout == 14)
 {
  $this->pdf->AddPage('P');
  $this->pdf->widthA = array(20,120);
	$this->pdf->alignA = array('L','L');
	$this->pdf->SetAligns($this->pdf->alignA);
	if(is_file($this->pdf->rapport_logo))
 	  $this->pdf->Image($this->pdf->rapport_logo, 132, 10, 65, 20);
 	$this->pdf->frontPage = true;
	}
	elseif($this->pdf->rapport_layout == 17)
	{
	  $this->pdf->AddPage();
    $imagefile = $__appvar['basedir']."/html/rapport/logo/front.jpg";
	  if(is_file($imagefile))
		{
		  $breedte = 175;
		  $hoogte = $breedte/(650/550);
		  $x = 288 - $breedte;
			$this->pdf->Image($imagefile, $x , 38, $breedte, $hoogte);
		}
		$this->pdf->SetXY(200,190);
  	$this->pdf->AutoPageBreak = false;
  	$logoFile = $this->pdf->rapport_logo;
		if(is_file($logoFile))
		{
			  $this->pdf->Image($this->pdf->rapport_logo, 242, 191, 45, 10);
		}
	 $this->pdf->AutoPageBreak  = true;
	}
	elseif($this->pdf->rapport_layout == 18)
 {
  $this->pdf->AddPage();
 	$this->pdf->widthA = array(20,120);
	$this->pdf->alignA = array('L','L');
  $this->pdf->SetWidths($this->pdf->widthA);
 	$this->pdf->SetAligns($this->pdf->alignA);
	if(is_file($this->pdf->rapport_logo))
	  $this->pdf->Image($this->pdf->rapport_logo, 220, 5, 65, 20);

	}
  elseif($this->pdf->rapport_layout == 12)
	{
	  $this->pdf->AddPage();
	  if(file_exists($this->pdf->rapport_logo))
		  $this->pdf->Image($this->pdf->rapport_logo, 130 -33, 5, 108, 15);
    
    
    $this->pdf->SetWidths(array(50,50,75+75));
   	$this->pdf->SetAligns(array('L','L','L','L'));
    $this->pdf->setY(50);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	$this->pdf->row(array('','Portefeuille','Naam',''));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	  foreach ($portefeuilledata as $port)
	  {
	   $this->pdf->row(array('',$port['PortefeuilleVoorzet'].$port['Portefeuille'],$port['Naam']." ".$port['Naam1']));
	  }
    if($this->pdf->GetY() < 80)
  	  $this->pdf->SetY(80);
    else
       $this->pdf->ln();
       
    $rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' '.vertaalTekst('tot en met',$this->pdf->rapport_taal).' '.
		                                          date("d",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);
		$this->pdf->row(array('',vertaalTekst('Verslagperiode',$this->pdf->rapport_taal).":",$rapportagePeriode));  
    $addedPortefeuilleInfo=true;

  }
  else
  {
    $this->pdf->AddPage();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setY(50);
	  $this->pdf->SetWidths(array(25,25,25,35,75,75));
 	  $this->pdf->SetAligns(array('L','L','L','L','L'));
	  $this->pdf->row(array('','Portefeuille','Depotbank','Accountmanager','Naam'));

	  foreach ($portefeuilledata as $port)
	  {
	   $this->pdf->row(array('',$port['PortefeuilleVoorzet'].$port['Portefeuille'],$port['Depotbank'],$port['Accountmanager'],$port['Naam'],$port['Naam1']));
	  }
	  $addedPortefeuilleInfo=true;
  }

  if(!isset($addedPortefeuilleInfo))
  {
    $this->pdf->setY(50);
    $this->pdf->SetWidths(array(50,50,75,75));
   	$this->pdf->SetAligns(array('L','L','L','L'));
  	$this->pdf->row(array('','Portefeuille','Naam','Naam1'));
	  foreach ($portefeuilledata as $port)
	  {
	   $this->pdf->row(array('',$port['PortefeuilleVoorzet'].$port['Portefeuille'],$port['Naam'],$port['Naam1']));
	  }
  }


	}
}
?>