<?php

include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFRONTC_L111
{
	function RapportFRONTC_L111($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

    foreach ($this->pdf->portefeuilles as $portefeuille)
    {
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
                            Portefeuilles.Portefeuille = '".$portefeuille."'";

                $this->DB->SQL($query);
                $this->DB->Query();
          $portefeuilledata[] = $this->DB->nextRecord();

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
      $xSize=30;
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
    $this->pdf->row(array('',vertaalTekst('Verslagperiode',$this->pdf->rapport_taal),":",$rapportagePeriode));
		$this->pdf->ln();
    $this->pdf->SetWidths(array(30,120));
		$this->pdf->row(array(' ',vertaalTekst('Geconsolideerde vermogensrapportage',$this->pdf->rapport_taal)));
    $this->pdf->ln();
    $this->pdf->SetWidths(array(30,40,25,120));

   $curYPos = $this->pdf->getY();
   $portefeuilleCounter = 0;
   $kol=0;
    if($_POST['anoniem']!=1)
    {
      foreach($portefeuilledata as $pdata)
      {
  
        if($portefeuilleCounter>1 && $portefeuilleCounter%20==0)
        {
          $kol++;
          $this->pdf->setY($curYPos);
          $this->pdf->SetWidths(array(30+100*$kol,40,25,120));
        }
        
      	if($pdata['selectieveld1'] <> '')
      		$txt=$pdata['selectieveld1'];
      	else
      		$txt=$pdata['Portefeuille'];
      	if($txt=='')
      	  continue;
        $this->pdf->row(array('',$txt,$pdata['Depotbank'],$pdata['Naam']));
        $this->pdf->Ln(1);

/*
        if ( $portefeuilleCounter == 19 ) {
          $this->pdf->setY($curYPos);
          $this->pdf->SetWidths(array(30+100,40,25,120));
        }
        elseif ( $portefeuilleCounter == 39 ) {
          $this->pdf->setY($curYPos);
          $this->pdf->SetWidths(array(30+100+100,40,25,120));
        }
*/
        
        $portefeuilleCounter++;
      }
    }

    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->ln(8); 


    if($this->pdf->getY()<133)
		  $this->pdf->SetY(133);

    if ( $portefeuilleCounter > 20 ) {
      $curPageBreakTrigger = $this->pdf->PageBreakTrigger;
      $this->pdf->PageBreakTrigger +=10;
      $this->pdf->SetY(200);
    }

    $this->pdf->SetWidths(array(30,50,5,120));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
		$this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal).': '.date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));


    if ( $portefeuilleCounter > 20 ) {
      $this->pdf->PageBreakTrigger = $curPageBreakTrigger;
    }


	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;

   /*
    $this->pdf->rapport_type = "INHOUD";
	  $this->pdf->rapport_titel = "Inhoudsopgave";//Inhoudsopgave
	  $this->pdf->addPage('L');
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;
   */
   
	}
}
?>
