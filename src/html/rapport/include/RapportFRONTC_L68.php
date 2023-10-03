<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/10/30 16:46:08 $
File Versie					: $Revision: 1.6 $

$Log: RapportFRONTC_L68.php,v $
Revision 1.6  2019/10/30 16:46:08  rvv
*** empty log message ***

Revision 1.5  2018/12/22 16:15:52  rvv
*** empty log message ***

Revision 1.4  2018/06/13 15:27:10  rvv
*** empty log message ***

Revision 1.3  2016/09/07 15:42:21  rvv
*** empty log message ***

Revision 1.2  2016/06/19 15:22:08  rvv
*** empty log message ***

Revision 1.1  2016/05/15 17:15:00  rvv
*** empty log message ***



*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFrontC_L68
{
	function RapportFrontC_L68($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

		$portefeuilledata=array();
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
                Portefeuilles.ClientVermogensbeheerder,
                Portefeuilles.startdatum,
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
          $portData=$this->DB->nextRecord();
          if(substr($portData['startdatum'],0,10)=='0000-00-00')
						continue;

			    $fondswaarden = berekenPortefeuilleWaarde($portefeuille, $this->rapportageDatum, 0, 'EUR', $this->rapportageDatum);
			    $totaleWaarde = 0;
			    foreach ($fondswaarden as $fondsData)
				    $totaleWaarde += $fondsData['actuelePortefeuilleWaardeEuro'];
			    if($totaleWaarde==0)
						continue;

          $portefeuilledata[] = $portData;
    }

 	$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');


		if(is_file($this->pdf->rapport_logo))
		{

		   $factor=0.045;
		   $xSize=1000*$factor;//$x=885*$factor;
		   $ySize=379*$factor;//$y=849*$factor;
      $logopos=(297)-($xSize)-$this->pdf->marge;
	    $this->pdf->Image($this->pdf->rapport_logo, $logopos, 4, $xSize, $ySize);
		}

   	$this->pdf->widthA = array(30,180);
		$this->pdf->alignA = array('L','L','L');

		$fontsize = 10; //$this->pdf->rapport_fontsize

    


    $this->pdf->SetWidths($this->pdf->widthA);

    $this->pdf->SetY(40);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
    $this->pdf->row(array(' ',vertaalTekst('Geconsolideerde vermogensrapportage',$this->pdf->rapport_taal)));
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

		$rapportagePeriode = date("j",$this->rapportageDatumVanafJul)." ".
		                     vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                     date("Y",$this->rapportageDatumVanafJul).
		                     ' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
		                     date("j",$this->rapportageDatumJul)." ".
		                     vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                     date("Y",$this->rapportageDatumJul);


    $this->pdf->SetWidths(array(30,40,5,120));
    $this->pdf->row(array('',vertaalTekst('Verslagperiode',$this->pdf->rapport_taal),":",$rapportagePeriode));
		$this->pdf->ln();
    $this->pdf->SetWidths(array(30,120));
		$this->pdf->row(array(' ',vertaalTekst('Geconsolideerde vermogensrapportage',$this->pdf->rapport_taal)));
    $this->pdf->ln();
   $this->pdf->SetWidths(array(30,40,25,120));
    if($_POST['anoniem']!=1)
    {
		//	$colls=ceil(count($portefeuilledata)/12);

      foreach($portefeuilledata as $pdata)
      {
				$naamParts=explode(' - ',$pdata['Naam'],2);
        $this->pdf->row(array('',$pdata['Portefeuille'],$pdata['ClientVermogensbeheerder'],$naamParts[1]));
        $this->pdf->Ln(1);
        //if($this->pdf->getY()>180)
        //	$this->pdf->addPage();
      }
    }
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->ln(8); 


    if($this->pdf->getY() < 133)
	  	$this->pdf->SetY(133);
    $this->pdf->SetWidths(array(30,50,5,120));
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
