<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/09/11 08:30:02 $
 		File Versie					: $Revision: 1.1 $

 		$Log: RapportFRONTC_L69.php,v $
 		Revision 1.1  2016/09/11 08:30:02  rvv
 		*** empty log message ***
 		
 		
 		

*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFRONTC_L69
{
	function RapportFRONTC_L69($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
		$this->pdf->rapportCounter = count($this->pdf->page);

		$this->DB = new DB();
	}

	function writeRapport()
	{
		global $__appvar;

		foreach ($this->pdf->portefeuilles as $portefeuille)
		{
		  $query = "SELECT
	            	if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam,Clienten.Naam) as Naam,
                if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam1,Clienten.Naam1) as Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Depotbanken.Omschrijving as depotbankOmschrijving,
                Portefeuilles.PortefeuilleVoorzet,
                Accountmanagers.Naam as accountManager,
                 Vermogensbeheerders.Naam as vermogensbeheerderNaam,
                 Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                 Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
		          FROM
		            Portefeuilles
		            LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
                LEFT JOIN Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
		            LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
		            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
		            LEFT Join CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
		          WHERE
		            Portefeuilles.Portefeuille = '".$portefeuille."'";

		  $this->DB->SQL($query);
	  	$this->DB->Query();
      $tmp=$this->DB->nextRecord();
      if($tmp['Portefeuille']=='')
      {
        $tmp['Portefeuille']=$portefeuille;
        $tmp['depotbankOmschrijving']="Niet gevonden? ";
	    }
      $portefeuilledata[] = $tmp; 

		}

		//if($this->pdf->selectData['type'] != 'eMail')
		//  $this->voorBrief();
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
	    $factor=0.031;
		  $xSize=1417*$factor;
		  $ySize=591*$factor;
			$this->pdf->Image($this->pdf->rapport_logo, 230, 180, $xSize, $ySize);
		}
    

		$fontsize = 16; //$this->pdf->rapport_fontsize
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->SetAligns(array('C'));
    $this->pdf->SetWidths(array(297-2*$this->pdf->marge));
		$this->pdf->SetY(45);

		$rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
		                     vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                     date("Y",$this->rapportageDatumVanafJul).
		                     ' - '.
		                     date("d",$this->rapportageDatumJul)." ".
		                     vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                     date("Y",$this->rapportageDatumJul);
    //$this->pdf->row(array('Capital Support'));

    if(isset($this->pdf->__appvar['consolidatie']['portefeuillenaam1']))
      $this->pdf->row(array($this->pdf->__appvar['consolidatie']['portefeuillenaam1']));
    else  
      $this->pdf->row(array($this->pdf->portefeuilledata['Naam']));
    $this->pdf->ln(5);
    //$this->pdf->row(array('Vermogensregierapportage'));
    if(isset($this->pdf->__appvar['consolidatie']['portefeuillenaam2']))
      $this->pdf->row(array($this->pdf->__appvar['consolidatie']['portefeuillenaam2']));
    else 
      $this->pdf->row(array($this->pdf->portefeuilledata['Naam1']));
    $this->pdf->ln(5);
		$this->pdf->row(array($rapportagePeriode));
		$this->pdf->ln(6);

    $this->pdf->SetWidths(array(15,50,150));
    $this->pdf->SetAligns(array('L','L','L'));
     
    if(count($portefeuilledata)>7)
      $ystart=100; 
    else 
      $ystart=150;  
    $this->pdf->SetY($ystart);
    $this->pdf->SetFont($this->pdf->rapport_font,'',11);
    $this->pdf->underline=true;
		$this->pdf->row(array('','Samenstelling portefeuille'));
    $this->pdf->underline=false;
    $offset=0;
    foreach ($portefeuilledata as $index=>$port)
	  {
	   if($index > 0 && $index%15==0)
     {
	     $offset+=90;
       $this->pdf->SetWidths(array(15+$offset,50,150));
       $this->pdf->SetY($ystart+4);
	   }
      $this->pdf->ln(1);
	    $this->pdf->row(array('',$port['PortefeuilleVoorzet'].$port['Portefeuille'],$port['depotbankOmschrijving']));
	  }
        

	$this->pdf->SetY(170);
    $this->pdf->SetWidths(array(223,50));
    $this->pdf->row(array('',''));//Den Haag
    $this->pdf->ln(1);
		$this->pdf->row(array('',date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));

  
	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;
/*
   	$this->pdf->rapport_type = "OIB";
	  $this->pdf->rapport_titel = "";//Inhoudsopgave
	  $this->pdf->addPage('L');
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;
*/
	}
  

}
?>