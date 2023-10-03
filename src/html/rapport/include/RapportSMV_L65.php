<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/11 17:30:27 $
File Versie					: $Revision: 1.3 $

$Log: RapportSMV_L65.php,v $
Revision 1.3  2020/07/11 17:30:27  rvv
*** empty log message ***

Revision 1.2  2020/06/27 16:23:17  rvv
*** empty log message ***

Revision 1.1  2020/06/20 13:50:28  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportVOLK_L65.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportTRANSFEE_L65.php");

class RapportSMV_L65
{
	function RapportSMV_L65($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "SMV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->pdf->rapport_rendementText="Portefeuilledetails";
	}

	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();


		$portefeuilles=array();
		$query = "SELECT Fondsen.Portefeuille,
              Portefeuilles.Startdatum,
              Portefeuilles.Einddatum,
              Fondsen.Omschrijving,
              TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,
              FondsenBuitenBeheerfee.layoutNr
              FROM TijdelijkeRapportage
JOIN FondsenBuitenBeheerfee ON TijdelijkeRapportage.fonds = FondsenBuitenBeheerfee.Fonds
JOIN Fondsen ON FondsenBuitenBeheerfee.Fonds = Fondsen.Fonds
JOIN Portefeuilles ON Fondsen.Portefeuille = Portefeuilles.Portefeuille
              WHERE FondsenBuitenBeheerfee.Huisfonds = 1 AND rapportageDatum ='".$this->rapportageDatum."' AND
              TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek']." ORDER BY Fondsen.Portefeuille";
		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
 	  {
 	  	if($data['Portefeuille']<>'')
		    $portefeuilles[$data['Portefeuille']]=$data;
    }

    $kopBackup=$this->pdf->rapport_koptext;
		
    foreach($portefeuilles as $portefeuille=>$pdata)
    {
      $pdata['Omschrijving']=vertaalTekst($pdata['Omschrijving'],$this->pdf->rapport_taal);
      $rapportageDatum['a'] = date("Y-m-d",$this->pdf->rapport_datumvanaf); 
      $rapportageDatum['b'] = date("Y-m-d",$this->pdf->rapport_datum);

	    if($this->pdf->rapport_datumvanaf < db2jul($pdata['Startdatum']))
	      $rapportageDatum['a'] = $pdata['Startdatum'];
	  
  	  if($this->pdf->rapport_datum > db2jul($pdata['Einddatum']))
  	  {
	    	echo "<b>Fout: Portefeille '$portefeuille' heeft een einddatum  (".date("d-m-Y",db2jul($pdata['Einddatum'])).")</b>";
	  	  exit;
	    }
    	if(db2jul($rapportageDatum['a']) > db2jul($rapportageDatum['b']))
	    {
	    	echo "<b>Fout: '$portefeuille' Van datum kan niet groter zijn dan  T/m datum! </b>";
		    exit;
	    }

      if(substr($rapportageDatum['a'],5,2)==01 && substr($rapportageDatum['a'],8,2)==01)
        $startjaar=true;
      else
        $startjaar=false;

     	$fondswaarden['a'] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum['a'],$startjaar,$pdata['RapportageValuta'],$rapportageDatum['a']);
	    $fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum['b'],0,$pdata['RapportageValuta'],$rapportageDatum['a']);
     	vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$rapportageDatum['a']);
	    vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$rapportageDatum['b']);
      $portefeuilleWaarde=0;
      foreach($fondswaarden['b'] as $fonds)
        $portefeuilleWaarde+=$fonds['actuelePortefeuilleWaardeEuro'];

			$aandeelVanPortefeuille=$pdata['actuelePortefeuilleWaardeEuro']/$portefeuilleWaarde;
			if($aandeelVanPortefeuille <>0)
			{
        
        $rapportagePeriode = date("j",$this->pdf->rapport_datumvanaf)." ".
          vertaalTekst($__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".
          date("Y",$this->pdf->rapport_datumvanaf).
          ' '.vertaalTekst('tot en met',$this->pdf->rapport_taal).' '.
          date("j",$this->pdf->rapport_datum)." ".
          vertaalTekst($__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".
          date("Y",$this->pdf->rapport_datum);
        
				$rapport = new RapportVOLK_L65($this->pdf, $portefeuille, $rapportageDatum['a'], $rapportageDatum['b']);
        $this->pdf->rapport_type='SMV';
				$this->pdf->rapport_titel = vertaalTekst('Portefeuille overzicht',$this->pdf->rapport_taal).' '.$pdata['Omschrijving'].' '.vertaalTekst("verslagperiode",$this->pdf->rapport_taal).' '.$rapportagePeriode.'*';
    
    
				$this->pdf->rapport_koptext = "\n " . $pdata['Omschrijving'] . "\n \n";
        $this->pdf->huisfondsOmschrijving=$pdata['Omschrijving'];
				$rapport->aandeel = $aandeelVanPortefeuille;
        $rapport->PERFblockTonen=false;
        $this->pdf->huisAandeel=$aandeelVanPortefeuille;
				$rapport->writeRapport();
				unset($this->pdf->huisAandeel);
				unset($this->pdf->huisfondsOmschrijving);
				$rapport = new RapportTRANSFEE_L65($this->pdf,$this->portefeuille, $rapportageDatum['a'], $rapportageDatum['b']);
        $this->pdf->rapport_type='HUIS';
        $rapport->getKleuren();
        $this->pdf->huis3=true;
        $grafiekData=$rapport->getGrafiekdata( $portefeuille);
        foreach($grafiekData as $paginanr=>$blokData)
        {
          $this->pdf->rapport_titel = vertaalTekst('Portefeuille overzicht',$this->pdf->rapport_taal).' '.$pdata['Omschrijving'].' '.vertaalTekst("verslagperiode",$this->pdf->rapport_taal).' '.$rapportagePeriode.'*';
  
          $rapport->pdf->addPage();
          if ($paginanr == 0 && !isset($rapport->pdf->templateVars['SMVPaginas']))
          {
            $rapport->pdf->templateVars['SMVPaginas'] = $rapport->pdf->page;
            $rapport->pdf->templateVarsOmschrijving['SMVPaginas'] = $rapport->pdf->rapport_titel;
          }
          $rapport->addKop(0,$blokData);
          $n = 0;
          $xyPos = array(
          	       array(20, 55),  array(130, 55),
                   array(20, 120), array(130, 120));
          $vertalingen=array('beleggingssector'=>'sector');
          foreach ($blokData['grafieken'] as $grafiekSoort => $grafiekData)
          {
  
            if(isset($vertalingen[$grafiekSoort]))
              $titel=vertaalTekst('Spreiding per '.$vertalingen[$grafiekSoort], $this->pdf->rapport_taal);
            else
              $titel=vertaalTekst('Spreiding per '.$grafiekSoort, $this->pdf->rapport_taal);
            
            $rapport->pdf->setXY($xyPos[$n][0], $xyPos[$n][1]);
            $rapport->pdf->wLegend = 0;
            $rapport->printPie($grafiekData['pieData'], $grafiekData['kleurData'], $titel, 35, 35);
            $rapport->pdf->wLegend = 0;
    
            $n++;
          }
        }
        unset($this->pdf->huis3);
			}
    }
    $this->pdf->rapport_koptext=$kopBackup;
    
	}

}
?>