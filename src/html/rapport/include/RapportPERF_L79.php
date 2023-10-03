<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/02/23 18:32:59 $
File Versie					: $Revision: 1.6 $

$Log: RapportPERF_L79.php,v $
Revision 1.6  2019/02/23 18:32:59  rvv
*** empty log message ***

Revision 1.5  2019/02/06 16:07:12  rvv
*** empty log message ***

Revision 1.4  2019/01/20 12:14:00  rvv
*** empty log message ***

Revision 1.3  2018/12/29 13:57:23  rvv
*** empty log message ***

Revision 1.2  2018/12/15 17:49:14  rvv
*** empty log message ***

Revision 1.1  2018/11/24 19:10:45  rvv
*** empty log message ***

Revision 1.51  2014/11/30 13:18:31  rvv
*** empty log message ***

Revision 1.50  2014/11/19 16:41:56  rvv
*** empty log message ***

Revision 1.49  2014/02/08 17:42:52  rvv
*** empty log message ***

Revision 1.48  2013/10/26 15:42:06  rvv
*** empty log message ***

Revision 1.47  2013/07/17 15:52:10  rvv
*** empty log message ***

Revision 1.46  2012/10/10 13:36:56  cvs
update 10-10-2012

Revision 1.45  2012/10/07 14:56:44  rvv
*** empty log message ***

Revision 1.44  2011/09/14 09:26:56  rvv
*** empty log message ***

Revision 1.43  2010/07/31 16:07:05  rvv
*** empty log message ***

Revision 1.42  2010/06/30 16:10:10  rvv
*** empty log message ***

Revision 1.41  2010/06/09 16:38:21  rvv
*** empty log message ***

Revision 1.40  2009/07/18 14:11:49  rvv
*** empty log message ***

Revision 1.39  2009/03/14 13:24:27  rvv
*** empty log message ***

Revision 1.38  2009/01/20 17:44:09  rvv
*** empty log message ***

Revision 1.37  2008/05/16 08:12:57  rvv
*** empty log message ***

Revision 1.36  2008/03/18 09:30:24  rvv
*** empty log message ***

Revision 1.35  2007/11/16 11:22:27  rvv
*** empty log message ***

Revision 1.34  2007/11/02 12:53:13  rvv
met liquiditeiten

Revision 1.33  2007/10/12 10:06:33  rvv
*** empty log message ***

Revision 1.32  2007/10/10 08:18:40  rvv
*** empty log message ***

Revision 1.31  2007/10/04 12:01:30  rvv
*** empty log message ***

Revision 1.30  2007/08/09 08:58:31  rvv
*** empty log message ***

Revision 1.29  2007/07/10 15:54:49  rvv
AFS update

Revision 1.28  2007/07/05 12:28:39  rvv
*** empty log message ***

Revision 1.27  2007/06/29 11:38:56  rvv
L14 aanpassingen

Revision 1.26  2007/06/05 11:38:25  rvv
*** empty log message ***

Revision 1.25  2007/03/29 10:37:11  rvv
fix performance met jaarovergang

Revision 1.24  2007/03/27 14:58:20  rvv
VreemdeValutaRapportage

Revision 1.23  2006/11/03 11:24:04  rvv
Na user update

Revision 1.22  2006/10/31 12:11:04  rvv
Voor user update

Revision 1.21  2006/08/18 07:33:20  rvv
Toevoeging om het ongerealiseerde koersresultaat uit het vorige jaar mee te nemen.

Revision 1.20  2006/08/10 15:15:42  cvs
*** empty log message ***

Revision 1.19  2006/07/13 18:31:24  cvs
*** empty log message ***

Revision 1.18  2006/03/21 10:13:26  jwellner
*** empty log message ***

Revision 1.17  2006/03/01 07:56:20  jwellner
*** empty log message ***

Revision 1.16  2006/01/13 15:46:51  jwellner
diverse aanpassingen

Revision 1.15  2005/12/19 13:23:27  jwellner
no message

Revision 1.14  2005/11/30 08:37:39  jwellner
layout stuff

Revision 1.13  2005/11/25 09:30:08  jwellner
- verdiept overzicht
- layout

Revision 1.12  2005/11/21 08:39:26  jwellner
layout

Revision 1.11  2005/11/18 15:15:01  jwellner
no message

Revision 1.10  2005/11/11 16:13:50  jwellner
bufix in MUT2 , PERF en Rekenclass

Revision 1.9  2005/11/09 10:21:05  jwellner
no message

Revision 1.8  2005/11/07 10:29:17  jwellner
no message

Revision 1.7  2005/10/04 12:34:41  jwellner
no message

Revision 1.6  2005/09/30 14:05:13  jwellner
- rapport OIH
- rapport MUT2
- Layout 5
- selectieschermen

Revision 1.5  2005/09/29 15:00:18  jwellner
no message

Revision 1.4  2005/09/16 07:32:55  jwellner
aanpassingen rapportage.

Revision 1.3  2005/09/09 11:31:46  jwellner
diverse aanpassingen zie e-mails Theo

Revision 1.2  2005/07/28 15:12:37  jwellner
no message

Revision 1.1  2005/07/15 11:21:00  jwellner
Layout verwijderd, alles samengevoegd in PDFRapport

Revision 1.4  2005/07/12 15:04:20  jwellner
diverse aanpassingen

Revision 1.3  2005/07/12 07:09:50  jwellner
no message

Revision 1.2  2005/07/08 13:52:01  jwellner
no message

Revision 1.1  2005/06/30 08:22:56  jwellner
Rapportage toegevoegd

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportPERF_L79
{

	function RapportPERF_L79($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->fontsize=12;
		$this->pdf->rapport_titel = "4 Vermogensontwikkeling";


		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

   
    if($rapportageDatumVanaf==$rapportageDatum && substr($rapportageDatumVanaf,5,5)=='01-01')
      $this->rapportageDatumVanaf=(substr($rapportageDatumVanaf,0,4)-1).'-12-31';
	}

	function formatGetal($waarde, $dec)
	{
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
  
  function addRegel($type,$data)
  {
   
    if($type=='kop')
    {
      $this->pdf->setFillColor($this->pdf->rapport_kop2_fontcolor['r'],$this->pdf->rapport_kop2_fontcolor['g'],$this->pdf->rapport_kop2_fontcolor['b']);
      
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->fontsize);
      $this->pdf->setTextColor(255);
    }
    elseif($type=='data')
    {
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->fontsize);
      $this->pdf->setFillColor(255);
      $this->pdf->setTextColor(75);
    }
    elseif($type=='sub')
    {
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->fontsize);
      $this->pdf->setFillColor(226,238,241);//$this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
      $this->pdf->setTextColor(75);
    }
    $this->pdf->rect($this->pdf->marge,$this->pdf->getY(),297-$this->pdf->marge*2,$this->pdf->rowHeight,'F');
    $this->pdf->row($data);
  }


	

	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->fontsize);
		$DB = new DB();

		// voor data
		$this->pdf->widthA = array(5,140,50);
		$this->pdf->alignA = array('L','L','R','L','R');

		// voor kopjes
		$this->pdf->widthB = array(5,140,50);
		$this->pdf->alignB = array('L','L','R','L','R');
    
    $this->pdf->setTextColor(0);
		$this->pdf->AddPage();
    
    $this->pdf->rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight=8;

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

		// ***************************** ophalen data voor afdruk ************************ //

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersBegin." ) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
		$totaalWaardeVanaf = $DB->nextRecord();

		$waardeEind				= $totaalWaarde['totaal'];
		$waardeBegin 			 	= $totaalWaardeVanaf['totaal'];
		$waardeMutatie 	   	= $waardeEind - $waardeBegin;
		$stortingen 			 	= getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$onttrekkingen 		 	= getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
		$rendementProcent  	= performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
    

    
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->ln($this->pdf->rowHeight*1.5);


			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);
      $this->addRegel('kop',array("",'Samenvatting',date("Y",db2jul($this->rapportageDatum))));
			$this->addRegel('data',array("",vertaalTekst("Vermogen per",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datumvanaf)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datumvanaf),$this->formatGetal($waardeBegin,0,true),""));
			$this->addRegel('data',array("",vertaalTekst("Vermogen per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)),$this->formatGetal($waardeEind,0),""));
		  $this->addRegel('sub',array("",vertaalTekst("Vermogensmutatie",$this->pdf->rapport_taal),$this->formatGetal($waardeMutatie,0),""));
    $this->addRegel('data',array("",vertaalTekst("Storting",$this->pdf->rapport_taal),$this->formatGetal($stortingen,0),""));
    $this->addRegel('data',array("",vertaalTekst("Onttrekking",$this->pdf->rapport_taal),$this->formatGetal($onttrekkingen,0),""));
    $this->addRegel('sub',array("",vertaalTekst("Resultaat in euro",$this->pdf->rapport_taal),$this->formatGetal($resultaatVerslagperiode,0),""));//.' ('.date("Y",db2jul($this->rapportageDatum)).' t/m '.vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal).')'
    $this->addRegel('sub',array("",vertaalTekst("Rendement in %",$this->pdf->rapport_taal),$this->formatGetal($rendementProcent,2)." %"));
    
    $startJul=db2jul($this->pdf->PortefeuilleStartdatum);
    $yearAgo=date("Y",db2jul($this->rapportageDatum))-1;
   // if($startJul>mktime(0,0,0,1,1,$yearAgo))
    //{
      $historyStart=date('Y-m-d',$startJul);
   // }
    //else
   // {
    //  $historyStart=$yearAgo."-01-01";
   // }
    $historyEnd=$this->rapportageDatum;//$yearAgo."-12-31";
    
    if(db2jul($historyStart) < $this->pdf->rapport_datumvanaf)
    {
  
      $historie = array();
      $waarden['begin'] = berekenPortefeuilleWaarde($this->portefeuille, $historyStart, (substr($historyStart, 5, 5) == '01-01')?true:false);
      foreach ($waarden['begin'] as $tmp)
      {
        $historie['begin'] += $tmp['actuelePortefeuilleWaardeEuro'];
      }
      $waarden['eind'] = berekenPortefeuilleWaarde($this->portefeuille, $historyEnd, (substr($historyEnd, 5, 5) == '01-01')?true:false);
      foreach ($waarden['eind'] as $tmp)
      {
        $historie['eind'] += $tmp['actuelePortefeuilleWaardeEuro'];
      }
      $historie['stortingen'] = getStortingen($this->portefeuille, $historyStart, $historyEnd, $this->pdf->rapportageValuta);
      $historie['onttrekkingen'] = getOnttrekkingen($this->portefeuille, $historyStart, $historyEnd, $this->pdf->rapportageValuta);
      $historie['resultaat'] = $historie['eind'] - $historie['begin'] - $historie['stortingen'] + $historie['onttrekkingen'];
      //listarray($historie);
      $rendementProcent = performanceMeting($this->portefeuille, $historyStart, $historyEnd, $this->pdf->portefeuilledata['PerformanceBerekening'], $this->pdf->rapportageValuta);
      $dagen=(db2jul($historyEnd)-db2jul($historyStart))/86400;
      $geanualiseerdRendement=(pow(1+$rendementProcent/100, (365 / $dagen))-1)*100;
      
      $this->pdf->ln($this->pdf->rowHeight);
      $this->addRegel('kop', array("", 'Historie', 'Sinds start'));
      $this->addRegel('sub', array("", 'Resultaat in euro\'s', $this->formatGetal($historie['resultaat'], 0)));
      $this->addRegel('sub', array("", 'Rendement in %', $this->formatGetal($rendementProcent, 2). " %"));
      $this->addRegel('sub',array("",vertaalTekst("Rendement per jaar sinds start %",$this->pdf->rapport_taal),$this->formatGetal($geanualiseerdRendement,2)." %"));
    }
    $this->pdf->rowHeight=$this->pdf->rowHeightBackup;
    $this->pdf->setTextColor(0);
	}
}
?>