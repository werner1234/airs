<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.10 $

$Log: RapportOIH_L33.php,v $
Revision 1.10  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.9  2017/05/28 09:57:56  rvv
*** empty log message ***

Revision 1.8  2017/05/24 15:56:56  rvv
*** empty log message ***

Revision 1.7  2016/10/14 07:46:50  rvv
*** empty log message ***

Revision 1.6  2014/11/23 14:13:22  rvv
*** empty log message ***

Revision 1.5  2012/08/29 17:01:36  rvv
*** empty log message ***

Revision 1.4  2012/07/18 15:21:19  rvv
*** empty log message ***

Revision 1.3  2012/07/14 13:20:23  rvv
*** empty log message ***

Revision 1.2  2012/07/11 15:50:19  rvv
*** empty log message ***

Revision 1.1  2012/07/08 19:29:46  rvv
*** empty log message ***

Revision 1.24  2012/06/30 14:42:50  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once("rapport/rapportATTberekening.php");

class RapportOIH_L33
{
	function RapportOIH_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
	  $this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	  $this->db=new DB();
	}

	function formatGetal($waarde, $dec)
	{
	  if($waarde==0)
	    return '';
	  else
  		return number_format($waarde,$dec,",",".");
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }



	function writeRapport()
	{
		if(isset($this->pdf->__appvar['consolidatie']))
		{

	  $this->pdf->rapport_type = "OIH";
		$this->pdf->rapport_titel = "Vermogensoverzicht (met beginkoers)";
		$this->vastWhere='';

		$DB=new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) /".$this->pdf->ValutaKoersEind."  AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];




		  		    $query = "SELECT
	            	if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam,Clienten.Naam) as Naam,
                if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam1,Clienten.Naam1) as Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Portefeuilles.PortefeuilleVoorzet,
                Portefeuilles.kleurcode,
                Portefeuilles.clientVermogensbeheerder,
                Accountmanagers.Naam as accountManager,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email,
                Depotbanken.Omschrijving as depotbankOmschrijving
		          FROM
		            Portefeuilles
		            LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
		            LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
		            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
		            LEFT Join CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
		            Join Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
		          WHERE
		            Portefeuilles.Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."')
		            ORDER BY depotbankOmschrijving,Portefeuilles.Portefeuille";
		    $DB->SQL($query);
	    	$DB->Query();
	      while($tmp = $DB->nextRecord())
	        $portefeuilledata[$tmp['Portefeuille']]=$tmp;

      $clientGevuld=false;
      foreach ($portefeuilledata as $portefeuille=>$pdata)
      {
        if($pdata['clientVermogensbeheerder'] <> '')
					$clientGevuld=true;
        if(substr($this->rapportageDatum,5,5)=='01-01')
          $startjaar=true;
        else
          $startjaar=false;

        $waarden=berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum,$startjaar,$this->rapportageDatumVanaf);
        foreach ($waarden as $waarde)
        {
          $portefeuilleWaarden[$portefeuille]+=$waarde['actuelePortefeuilleWaardeEuro']/$this->pdf->ValutaKoersEind;
          $valutaWaarden[$waarde['valuta']]+=$waarde['actuelePortefeuilleWaardeEuro']/$this->pdf->ValutaKoersEind;
                //$depotbankWaarden[$tmp['Depotbank']]+=$waarde['actuelePortefeuilleWaardeEuro']/$this->pdf->ValutaKoersEind;
         // $depotbankOmschrijving[$tmp['Depotbank']]=$tmp['depotbankOmschrijving'];
        }
      }


      if(!is_array($this->pdf->grafiekKleuren))
	    {
	      $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
	    	$DB->SQL($q);
  	  	$DB->Query();
    		$kleuren = $DB->LookupRecord();
    		$kleuren = unserialize($kleuren['grafiek_kleur']);
    		$this->pdf->grafiekKleuren=$kleuren;
	    }

	    $valutaKleuren=array();
	    foreach ($valutaWaarden as $valuta=>$waarde)
	    {
	      $valutaAandeel[$valuta]=$waarde/$totaalWaarde*100;
	      $valutaKleuren[]=array($this->pdf->grafiekKleuren['OIV'][$valuta]['R']['value'],$this->pdf->grafiekKleuren['OIV'][$valuta]['G']['value'],$this->pdf->grafiekKleuren['OIV'][$valuta]['B']['value']);
	    }


      $portefeuilleKleur=array();
      foreach ($portefeuilleWaarden as $portefeuille=>$waarde)
      {
        if(unserialize($portefeuilledata[$portefeuille]['kleurcode']))
          $kleur=unserialize($portefeuilledata[$portefeuille]['kleurcode']);
        else
          $kleur=array(rand(0,255),rand(0,255),rand(0,255));

        $portefeuilleAandeel[$portefeuilledata[$portefeuille]['depotbankOmschrijving']." ".$portefeuille]=$waarde/$totaalWaarde*100;
        $portefeuilleKleur[]=$kleur;
      }

      /*
      $depotbankKleuren=array();
      foreach ($depotbankWaarden as $depotbank=>$waarde)
      {
        $depotbankAandeel[$depotbankOmschrijving[$depotbank]]=$waarde/$totaalWaarde*100;
        $depotbankKleuren[]=array($this->pdf->grafiekKleuren['DEP'][$depotbank]['R']['value'],$this->pdf->grafiekKleuren['DEP'][$depotbank]['G']['value'],$this->pdf->grafiekKleuren['DEP'][$depotbank]['B']['value']);
      }
      */



 	  $this->pdf->rapport_titel = vertaalTekst("Waardeverdeling portefeuilles",$this->pdf->rapport_taal);
      $this->pdf->addPage();
      $this->pdf->setXY($this->pdf->marge,60);
      $this->pdf->SetAligns(array('L','L','L','R','R','L'));
			if($clientGevuld==true)
				$Vermogensbeheerder='  Vermogensbeheerder';
			else
				$Vermogensbeheerder='';
      $this->pdf->SetWidths(array(80,40,40,25,25,40));
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		  $this->pdf->row(array(vertaalTekst('Naam',$this->pdf->rapport_taal),vertaalTekst('Depotbank',$this->pdf->rapport_taal),vertaalTekst('Rekening',$this->pdf->rapport_taal),vertaalTekst('Waarde',$this->pdf->rapport_taal),vertaalTekst('in %',$this->pdf->rapport_taal),vertaalTekst($Vermogensbeheerder,$this->pdf->rapport_taal)));
		  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		  $totalen=array();
		  foreach ($portefeuilleWaarden as $portefeuille=>$waarde)
		  {
		     $grafieknaam=$portefeuilledata[$portefeuille]['depotbankOmschrijving']." ".$portefeuille;
		     $this->pdf->row(array(
		     $portefeuilledata[$portefeuille]['Naam'],
		     $portefeuilledata[$portefeuille]['depotbankOmschrijving'],
         $portefeuille,
         $this->formatGetal($waarde,0),
         $this->formatGetal($portefeuilleAandeel[$grafieknaam],1),
				 '  '.$portefeuilledata[$portefeuille]['clientVermogensbeheerder'],));
		     $totalen['waarde']+=$waarde;
		     $totalen['aandeel']+=$portefeuilleAandeel[$grafieknaam];
		 	}
		 	$this->pdf->underlinePercentage=0.8;
		 	$this->pdf->CellBorders=array('','','','TS','TS');
		 	$this->pdf->row(array('','',vertaalTekst('Totaal',$this->pdf->rapport_taal),$this->formatGetal($totalen['waarde'],0),$this->formatGetal($totalen['aandeel'],1)));
      $this->pdf->CellBorders=array();


      $this->pdf->setY(110);
      $this->pdf->SetAligns(array('C','C'));
      $this->pdf->SetWidths(array(140,140));
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+4);
      $this->pdf->row(array(vertaalTekst("Verdeling over depot's",$this->pdf->rapport_taal),vertaalTekst("Verdeling over valuta's",$this->pdf->rapport_taal)));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		 	$this->pdf->setXY(20,120);

		 	//"Verdeling over depot's"
		  PieChart($this->pdf,65, 65, $portefeuilleAandeel, '%l (%p)',$portefeuilleKleur);
      $this->pdf->setXY(160,120);
      //"Verdeling over valuta's"
      PieChart($this->pdf,65, 65, $valutaAandeel, '%l (%p)',$valutaKleuren);
      //$this->pdf->PieChart(50, 50,$depotbankAandeel, '%l (%p)',$kleuren);

		}

	}

}
?>