<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/01/02 16:18:56 $
 		File Versie					: $Revision: 1.4 $

 		$Log: RapportKERNV_L30.php,v $
 		Revision 1.4  2019/01/02 16:18:56  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/12/22 16:15:52  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2018/12/14 16:43:21  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2018/12/12 16:19:08  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2018/08/18 12:40:15  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.19  2016/05/28 14:21:20  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2015/01/31 20:02:46  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2014/11/30 13:19:33  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2014/11/23 14:13:22  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2014/10/22 15:50:27  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2013/03/20 11:07:25  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2012/12/22 15:34:10  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2012/11/25 13:16:31  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2012/09/05 18:19:11  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2012/08/11 13:17:53  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2012/08/08 15:37:42  rvv
 		*** empty log message ***

 		Revision 1.8  2012/04/08 08:13:18  rvv
 		*** empty log message ***

 		Revision 1.7  2012/03/08 08:15:34  rvv
 		*** empty log message ***

 		Revision 1.6  2012/03/08 07:58:38  rvv
 		*** empty log message ***

 		Revision 1.5  2012/01/04 16:28:38  rvv
 		*** empty log message ***

 		Revision 1.4  2011/12/21 19:19:33  rvv
 		*** empty log message ***

 		Revision 1.3  2011/12/14 18:59:12  rvv
 		*** empty log message ***

 		Revision 1.2  2011/10/26 17:33:24  rvv
 		*** empty log message ***

 		Revision 1.1  2011/10/23 13:34:45  rvv
 		*** empty log message ***

 		Revision 1.7  2011/09/14 09:26:56  rvv
 		*** empty log message ***

 		Revision 1.6  2011/09/03 14:30:20  rvv
 		*** empty log message ***

 		Revision 1.5  2011/07/03 06:42:47  rvv
 		*** empty log message ***

 		Revision 1.4  2011/06/15 16:14:39  rvv
 		*** empty log message ***

 		Revision 1.3  2011/06/13 14:41:56  rvv
 		*** empty log message ***

 		Revision 1.2  2011/06/02 15:05:05  rvv
 		*** empty log message ***

 		Revision 1.1  2011/05/29 06:38:42  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/PDFOverzicht.php");

//ini_set('max_execution_time',60);
class RapportKERNV_L30
{
	function RapportKERNV_L30($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "\n \n \n \nGeschiktheidsverklaring";

		$this->pdf->rapport_titel2='';

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";

		$this->perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar;

		$this->pdf->addPage();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln(8);
    $this->printZorg();
    $this->pdf->setY(105);
    
    $this->printText();

	}
	
	function printText()
	{
    $db=new DB();
    $query="SELECT verzendAanhef,voorletters,tussenvoegsel,achternaam, part_voorletters,part_tussenvoegsel,part_achternaam,enOfRekening FROM CRM_naw WHERE Portefeuille='".$this->portefeuille."'";
    $db->SQL($query);
    $crm=$db->lookupRecord();
    
    $naam=$crm['verzendAanhef'];
    if($naam=='')
    	$naam=$this->pdf->rapport_naam1;
    
    $handtekeningTxt1=trim(trim($crm['voorletters'].' '.$crm['tussenvoegsel']).' '.$crm['achternaam']);
    if($crm['enOfRekening']==1)
      $handtekeningTxt2=trim(trim($crm['part_voorletters'].' '.$crm['part_tussenvoegsel']).' '.$crm['part_achternaam']);
    else
      $handtekeningTxt2='';
    //$handtekeningTxt1='A';$handtekeningTxt2='B';
    if($handtekeningTxt1<>'')
    {
      $handtekening1 = 'Handtekening ' . $handtekeningTxt1;
      $handtekening1puntten='...';
    }
    else
    {
      $handtekening1 = '';
      $handtekening1puntten='';
    }
    if($handtekeningTxt2<>'')
    {
      $handtekening2 = 'Handtekening ' . $handtekeningTxt2;
      $handtekening2puntten='...';
    }
    else
    {
      $handtekening2 = '';
      $handtekening2puntten='';
    }
		$txt="$naam

Het gekozen risicoprofiel van uw portefeuille is het resultaat van informatie verkregen van u over:
- uw kennis en ervaring op beleggingsgebied;
- uw financiële situatie (eventueel van uw b.v.);
- de doelstelling voor de beheerportefeuille;
- uw risicobereidheid.

Eerder in de afgelopen periode hebben wij met u gesproken over deze algemene uitgangspunten voor het vermogensbeheer en uw doelstellingen voor de beleggingen. Er is toen ook vastgesteld dat er zich geen (relevante) wijzigingen hebben voorgedaan in uw persoonlijke situatie die van invloed zijn op uw financiële situatie en risicobereidheid. (Neemt u contact op wanneer dat wel het geval is).

Op basis van de bij ons bekend informatie hebben wij vastgesteld dat (i) het genoemde risicoprofiel geschikt is voor u en (ii) dat de beleggingen in uw portefeuille binnen de overeengekomen bandbreedtes van de vermogensverdeling van het het risicoprofiel vallen en derhalve geschikt zijn.

Wij verzoeken u vriendelijk ten blijke van akkoord deze pagina te ondertekenen en aan ons te retourneren (dit mag via email).
";
    
    
    $this->pdf->SetAligns(array('L'));
    $this->pdf->SetWidths(array(280));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array($txt));
    
    $this->pdf->SetWidths(array(120,120));
    $this->pdf->ln();
    $this->pdf->row(array($handtekening1,$handtekening2));
    $this->pdf->ln();
    $this->pdf->ln();
    $this->pdf->row(array($handtekening1puntten,$handtekening2puntten));
    
    
	}




  function printZorg()
  {
    global $__appvar;
        $DB= new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$query="SELECT Zorgplicht,Omschrijving FROM Zorgplichtcategorien WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
		$DB->SQL($query);
		$DB->Query();
		while($zorgp = $DB->nextRecord())
		{
		  $zorgplichtcategorien[$zorgp['Zorgplicht']]=$zorgp['Omschrijving'];
		}
		$zorgplichtcategorien['Overige']='Vastrentende waarden';

		$this->totaalWaarde=$totaalWaarde;
		if(!$this->totaalWaarde)
      $this->totaalWaarde=1;
    $query= "SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->totaalWaarde." as percentage,ZorgplichtPerBeleggingscategorie.Zorgplicht,
    TijdelijkeRapportage.beleggingscategorie,TijdelijkeRapportage.beleggingscategorie
    FROM TijdelijkeRapportage
    INNER JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='CAS'
    WHERE TijdelijkeRapportage.Portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
    .$__appvar['TijdelijkeRapportageMaakUniek']."
    GROUP By ZorgplichtPerBeleggingscategorie.Zorgplicht";
    $DB->SQL($query);
		$DB->Query();
    $categorieWaarden=array();
		while($data= $DB->nextRecord())
		{

		  $categorieWaarden[$data['Zorgplicht']]+=$data['percentage']*100;
		}
#listarray($categorieWaarden);

  	$zorgplicht = new Zorgplichtcontrole();
    $tmp=$this->pdf->portefeuilledata;
    $tmp['Portefeuille']=$this->portefeuille;
  	$zpwaarde=$zorgplicht->zorgplichtMeting($tmp,$this->rapportageDatum);

    $tmp=array();
    foreach ($zpwaarde['conclusie'] as $index=>$regelData)
      $tmp[$regelData[0]]=$regelData;

    krsort($tmp);

    $this->pdf->SetAligns(array('L','R','R','R','R'));
   	$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),130,51.3);
  	$this->pdf->SetWidths(array(50,20,20,20,20));
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('Risicoprofiel'."\n".$this->pdf->portefeuilledata['Risicoklasse'],'Minimaal','Maximaal',"Werkelijke\nverdeling","Risico\ngewogen"));
    	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetAligns(array('L','R','R','R','R'));
  	foreach ($tmp as $index=>$regelData)
        {
          //echo $regelData[0]." ";
  	  $this->pdf->row(array($zorgplichtcategorien[$regelData[0]],$zpwaarde['categorien'][$regelData[0]]['Minimum']."%",$zpwaarde['categorien'][$regelData[0]]['Maximum']."%",$this->formatGetal($categorieWaarden[$regelData[0]],1)."%",$regelData[2]."%"));
        }
    $this->pdf->ln();

    $db=new DB();
    $query="SELECT Portefeuilles.OptieToestaan,Portefeuilles.Memo FROM Portefeuilles WHERE Portefeuille='".$this->portefeuille."'";
    $db->SQL($query);
    $optie=$db->lookupRecord();
    if($optie['OptieToestaan']==1)
      $optie['OptieToestaan']='toegestaan';
    else
      $optie['OptieToestaan']='niet toegestaan';

    $query="SELECT profielVastgoed,Memo FROM CRM_naw WHERE Portefeuille='".$this->portefeuille."'";
    $db->SQL($query);
    $crm=$db->lookupRecord();
    if($crm['profielVastgoed']=='J')
      $crm['profielVastgoed']='toegestaan';
    else
      $crm['profielVastgoed']='niet toegestaan';

    //listarray($this->pdf->portefeuilledata);
  	  //ZorgplichtPerRisicoklasse
  	// SELECT ZorgplichtPerRisicoklasse.id, ZorgplichtPerRisicoklasse.Vermogensbeheerder, ZorgplichtPerRisicoklasse.Zorgplicht, ZorgplichtPerRisicoklasse.Risicoklasse, ZorgplichtPerRisicoklasse.Minimum, ZorgplichtPerRisicoklasse.Maximum FROM (ZorgplichtPerRisicoklasse) WHERE
  	$this->pdf->SetWidths(array(50,80));
  	$this->pdf->SetAligns(array('L','L','L','L'));
  	//$this->pdf->row(array('Risicoklasse',$this->pdf->portefeuilledata['Risicoklasse']));
  	$this->pdf->row(array('Opties',$optie['OptieToestaan']));
  	$this->pdf->row(array('Vastgoed',$crm['profielVastgoed']));
  	$this->pdf->row(array('Bijzonderheden',$optie['Memo']));
  }



}
?>
