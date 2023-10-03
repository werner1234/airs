<?php

include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFRONT_L112
{
	function RapportFRONT_L112($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
    
    $portefeuillesdata=array();
    foreach ($this->pdf->portefeuilles as $portefeuille)
    {
      $query = "SELECT
                if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam,Clienten.Naam) as Naam,
                if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam1,Clienten.Naam1) as Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Depotbanken.Omschrijving as DepotbankOmschrijving,
                Portefeuilles.PortefeuilleVoorzet,
                Accountmanagers.Naam as accountManager,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
                          FROM
                            Portefeuilles
                            LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
                            LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
                            LEFT JOIN Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
                            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
                            LEFT Join CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
                          WHERE
                            Portefeuilles.Portefeuille = '".$portefeuille."'";
      
      $this->DB->SQL($query);
      $this->DB->Query();
      $portefeuillesdata[] = $this->DB->nextRecord();
      
    }


		$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');


		if(is_file($this->pdf->rapport_logo))
		{
			
			$xSize=45;

      $logopos=(297/2)-($xSize/2);
	    $this->pdf->Image($this->pdf->rapport_logo, $logopos, 4, $xSize);
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
    $this->pdf->row(array('',vertaalTekst('Productiedatum',$this->pdf->rapport_taal),':',date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
    $this->pdf->ln();
    $this->pdf->row(array('',vertaalTekst('Verslagperiode',$this->pdf->rapport_taal),":",$rapportagePeriode));
		$this->pdf->ln();
  
		if(count($portefeuillesdata)>0)
    {
      $this->pdf->SetWidths(array(30, 120));
      $this->pdf->row(array(' ', vertaalTekst('Geconsolideerde vermogensrapportage', $this->pdf->rapport_taal)));
      $this->pdf->ln();
      $this->pdf->SetWidths(array(30, 40, 40, 120));
      if ($_POST['anoniem'] != 1)
      {
        foreach ($portefeuillesdata as $pdata)
        {
          $this->pdf->row(array('', $pdata['Portefeuille'], $pdata['DepotbankOmschrijving'], $pdata['Naam']));
          $this->pdf->Ln(1);
        }
      }
    }
    else
    {
      if ($portefeuilledata['naam'] <> '')
      {
        $txt = $portefeuilledata['naam'];
      }
      else
      {
        $txt = $portefeuilledata['Portefeuille'];
      }
      $this->pdf->SetWidths(array(30, 40, 5, 40, 25,));
      if ($_POST['anoniem'] != 1)
        $this->pdf->row(array(' ', vertaalTekst('Vermogensrapportage', $this->pdf->rapport_taal), ':', $txt, $portefeuilledata['Depotbank']));
    }
    
	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;

   
    $this->pdf->rapport_type = "INHOUD";
	  $this->pdf->rapport_titel = "Inhoudsopgave";//Inhoudsopgave
	  $this->pdf->addPage('L');
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;

	}
}
?>
