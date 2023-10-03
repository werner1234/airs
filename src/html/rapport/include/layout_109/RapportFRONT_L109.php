<?php

include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L109
{
	function RapportFront_L109($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
    
    $query = "SELECT
                if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam,Clienten.Naam) as Naam,
                if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam1,Clienten.Naam1) as Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Portefeuilles.selectieveld1,
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
                            Portefeuilles.Portefeuille = '".$this->portefeuille."'";
		$this->DB->SQL($query);
		$this->DB->Query();
		$portefeuilledata = $this->DB->nextRecord();


		if($this->pdf->selectData['allInOne']==1 || $this->pdf->selectData['type'] <>'') // backoffice afdruk
		{
		//	$this->pdf->AddPage('P');
		//	$this->pdf->emailSkipPages[]=$this->pdf->page;
		//	$this->voorBrief();
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
			
			$xSize=45;

      $logopos=(297/2)-($xSize/2);
	    $this->pdf->Image($this->pdf->rapport_logo, $this->pdf->w - $xSize -$this->pdf->marge, $this->pdf->h-20, $xSize);
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

    $this->pdf->SetWidths(array(30,40,5,120));
    $this->pdf->row(array('',vertaalTekst('Verslagperiode',$this->pdf->rapport_taal),":",$rapportagePeriode));
		$this->pdf->ln();
    
    if($portefeuilledata['selectieveld1'] <> '')
      $txt=$portefeuilledata['selectieveld1'];
    else
      $txt=$portefeuilledata['Portefeuille'];
    $this->pdf->SetWidths(array(30,40,5,40,25,));
		$this->pdf->row(array(' ',vertaalTekst('Vermogensrapportage',$this->pdf->rapport_taal),':',$txt,$portefeuilledata['Depotbank']));
    $this->pdf->SetWidths(array(30,40,5,120));
    $this->pdf->ln();
    $this->pdf->row(array(' ',vertaalTekst('Mandaat',$this->pdf->rapport_taal),':',vertaalTekst($this->pdf->portefeuilledata['Risicoklasse'],$this->pdf->rapport_taal)));
    $this->pdf->ln();
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->ln(8);
    /*
    $this->pdf->SetWidths(array(30,120));
    $this->pdf->row(array(' ',vertaalTekst('Vermogensrapportage',$this->pdf->rapport_taal)));
    $this->pdf->ln();
    $this->pdf->SetWidths(array(30,40,25,120));

      $this->pdf->row(array('',$txt,$portefeuilledata['Depotbank'],$portefeuilledata['Naam']));
      */
      
      $this->pdf->Ln(1);
    $this->pdf->SetWidths(array(30,40,5,120));

		$this->pdf->SetY(133);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
		$this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal).': '.date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(2);
		$this->pdf->row(array('',''));


	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;

   
    //$this->pdf->rapport_type = "INHOUD";
	  //$this->pdf->rapport_titel = "Inhoudsopgave";//Inhoudsopgave
	  //$this->pdf->addPage('L');
	  //$this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;

	}
}
?>
