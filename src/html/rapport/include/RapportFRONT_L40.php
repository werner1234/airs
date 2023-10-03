<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/13 15:36:21 $
File Versie					: $Revision: 1.19 $

$Log: RapportFRONT_L40.php,v $
Revision 1.19  2020/05/13 15:36:21  rvv
*** empty log message ***

Revision 1.18  2020/02/22 18:46:19  rvv
*** empty log message ***

Revision 1.17  2018/12/08 18:28:30  rvv
*** empty log message ***

Revision 1.16  2018/12/05 16:36:17  rvv
*** empty log message ***

Revision 1.15  2017/04/05 15:39:45  rvv
*** empty log message ***

Revision 1.14  2016/01/17 18:10:27  rvv
*** empty log message ***

Revision 1.13  2013/07/15 17:06:38  rvv
*** empty log message ***

Revision 1.12  2013/03/09 16:22:24  rvv
*** empty log message ***

Revision 1.11  2013/01/23 16:45:37  rvv
*** empty log message ***

Revision 1.10  2013/01/20 13:27:16  rvv
*** empty log message ***

Revision 1.9  2012/12/30 14:27:11  rvv
*** empty log message ***

Revision 1.8  2012/12/22 15:34:10  rvv
*** empty log message ***

Revision 1.7  2012/12/08 14:48:08  rvv
*** empty log message ***

Revision 1.6  2012/11/01 10:09:29  rvv
*** empty log message ***

Revision 1.5  2012/10/24 16:09:36  rvv
*** empty log message ***

Revision 1.4  2012/10/17 15:55:14  rvv
*** empty log message ***

Revision 1.3  2012/10/07 14:57:17  rvv
*** empty log message ***

Revision 1.2  2012/10/02 16:17:32  rvv
*** empty log message ***

Revision 1.1  2012/09/30 11:18:17  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L40
{
	function RapportFront_L40($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FRONT";
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
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

	function voorBrief()
	{
	  global $__appvar;

	//	if (count($this->pdf->pages) % 2)
  //		$this->pdf->AddPage($this->pdf->CurOrientation);

		$this->pdf->AddPage('P');
  	$this->pdf->frontPage = true;
    $rowheightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight=4.5;


   	$this->pdf->underlinePercentage=0.8;

    $this->pdf->SetFont($this->pdf->rapport_font,'',8);

		if(file_exists($__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Remisier'].".png"))
		  $logo = $__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Remisier'].".png";
		else
		  $logo = $__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Logo'];
    

    
    
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor[r],$this->pdf->rapport_kop_bgcolor[g],$this->pdf->rapport_kop_bgcolor[b]);
		$this->pdf->Rect(0, 25, 210, 19 , 'F');
    $this->pdf->SetFillColor(255);
    
    $extraX=125;
    $this->pdf->Ellipse(40+$extraX, 12, 30, 22,0, 180 , 360, 'F');
    //$this->Ellipse($x0, $y0, $r, 0, 0, $astart, $afinish, $style, $line_style, $fill_color, $nSeg);
    
   // $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[r],$this->pdf->rapport_kop_fontcolor[g],$this->pdf->rapport_kop_fontcolor[b]);
//0,78,58
    if(is_file($logo))
    {
      $factor=0.0295;
      $xSize=1246*$factor;
      $ySize=540*$factor;
      
      $xSize=1581*$factor;
      $ySize=694*$factor;
      
      //$this->pdf->MemImage($groenLogo,85,5,$xSize,$ySize);
      $this->pdf->Image($logo, 16.5+$extraX, 5, $xSize, $ySize);
    }
    
    $this->pdf->SetTextColor(0);

//$this->pdf->ImageEps($__appvar['basedir']."/html/rapport/logo/test.eps", 30, 20, 150, 0);


			  $query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.adres,
CRM_naw.pc,
CRM_naw.plaats,
CRM_naw.land,
CRM_naw.verzendAanhef,
CRM_naw.ondernemingsvorm,
CRM_naw.titel,
CRM_naw.voorletters,
CRM_naw.tussenvoegsel,
CRM_naw.achternaam,
CRM_naw.achtervoegsel,
CRM_naw.part_naam,
CRM_naw.part_voorvoegsel,
CRM_naw.part_titel,
CRM_naw.part_voorletters,
CRM_naw.part_tussenvoegsel,
CRM_naw.part_achternaam,
CRM_naw.part_achtervoegsel,
CRM_naw.enOfRekening,
CRM_naw.verzendAdres,
CRM_naw.verzendPc,
CRM_naw.verzendPlaats,
CRM_naw.verzendLand,
Portefeuilles.BetalingsinfoMee,
acc.naam as Accountmanager,
acc.titel as AccountmanagerTitel,
acc.titel2 as AccountmanagerTitel2,
acc.Handtekening as Handtekening,
acc2.naam as AccountmanagerTwee,
acc2.titel as AccountmanagerTweeTitel,
acc2.titel2 as AccountmanagerTweeTitel2,
acc2.Handtekening as tweedeHandtekening
FROM 
Portefeuilles
LEFT Join CRM_naw on Portefeuilles.portefeuille =CRM_naw.Portefeuille
LEFT JOIN Accountmanagers as acc ON Portefeuilles.Accountmanager = acc.Accountmanager 
LEFT JOIN Accountmanagers as acc2 ON Portefeuilles.Tweedeaanspreekpunt = acc2.Accountmanager 
WHERE Portefeuilles.portefeuille = '".$this->portefeuille."'  ";

	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();
	 	$this->pdf->SetWidths(array(25,140));
    if(trim($crmData['verzendAdres'])<>'')
    {
      $crmData['adres']=$crmData['verzendAdres'];
      $crmData['pc']=$crmData['verzendPc'];
      $crmData['plaats']=$crmData['verzendPlaats'];
      $crmData['land']=$crmData['verzendLand'];
    }
	  $this->pdf->SetAligns(array('R','L','L','R','R'));

	  $this->pdf->SetY(43);
	  $this->pdf->SetFont($this->pdf->rapport_font,'B',11);
	  $this->pdf->row(array('',""));//Vertrouwelijk
	  $this->pdf->SetFont($this->pdf->rapport_font,'',11);
		$this->pdf->row(array('',$crmData['naam']));
    if (trim($crmData['naam1']) <> "")  $this->pdf->row(array('',$crmData['naam1']));
    $this->pdf->row(array('',$crmData['adres']));
    $plaats=$crmData['pc'];
    if($crmData['plaats'] != '') $plaats.=" ".$crmData['plaats'];
    $this->pdf->row(array('',$plaats));
    $this->pdf->row(array('',$crmData['land']));

$this->pdf->SetTextColor(0,0,0);

$this->pdf->SetY(130);
$this->pdf->SetWidths(array(25,140));
$this->pdf->SetFont($this->pdf->rapport_font,'B',11);
$this->pdf->row(array('','Vermogensrapportage'));
$this->pdf->SetFont($this->pdf->rapport_font,'',11);
$this->pdf->SetY(140);
//$this->pdf->row(array('','Datum: '.(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y")));
//$this->pdf->ln(1);
$this->pdf->SetWidths(array(25,40,70));


//$this->pdf->SetFont($this->pdf->brief_font,'B',11);
$this->pdf->SetWidths(array(25,160,10,30));
$this->pdf->SetAligns(array('L','L','C','R'));

$this->pdf->ln(8);


$txt=$crmData['verzendAanhef'].",

Bijgaand ontvangt u een rapportage van uw vermogen zoals wij die in onze administratie hebben opgenomen.

Graag willen wij u op de mogelijkheid wijzen dat u deze rapportages (beveiligd) per e-mail kunt ontvangen. Veel mensen kiezen inmiddels voor deze mogelijkheid vanwege het gemak of om papier te sparen. Mocht u hier ook gebruik van willen maken, dan kunt u dit doorgeven via info@groenstate.nl of via ondergetekende.

Indien u vragen heeft over deze rapportage dan zijn wij u graag van dienst.


Met vriendelijke groeten,
Groenstate Vermogensbeheer B.V.
";

$this->pdf->row(array('',$txt));

$this->pdf->SetWidths(array(25,75,75,30));
$this->pdf->SetAligns(array('L','L','L','R'));
$this->pdf->ln(5);
//$this->pdf->row(array('',$crmData['Accountmanager'],$crmData['AccountmanagerTwee']));

if($crmData['Handtekening'] <> '')
  $this->pdf->MemImage(base64_decode($crmData['Handtekening']), $this->pdf->getX()+20, $this->pdf->getY(), 60);
if($crmData['tweedeHandtekening'] <> '')
  $this->pdf->MemImage(base64_decode($crmData['tweedeHandtekening']), $this->pdf->getX()+95, $this->pdf->getY(), 60);
$this->pdf->ln(22);
$this->pdf->SetWidths(array(25,75,75));
$this->pdf->row(array('',$crmData['Accountmanager'],$crmData['AccountmanagerTwee'])); //
$this->pdf->SetFont($this->pdf->rapport_font,'',9);
$this->pdf->row(array('',$crmData['AccountmanagerTitel'],$crmData['AccountmanagerTweeTitel']));
$this->pdf->row(array('',$crmData['AccountmanagerTitel2'],$crmData['AccountmanagerTweeTitel2']));
$this->pdf->SetFont($this->pdf->rapport_font,'',11);

 $this->pdf->SetTextColor($this->pdf->rapport_kop_bgcolor[r],$this->pdf->rapport_kop_bgcolor[g],$this->pdf->rapport_kop_bgcolor[b]);
 $this->pdf->geenBasisFooter=true;
 
 $this->pdf->rowHeight=$rowheightBackup;
 
     $this->pdf->AutoPageBreak=false;
    $this->pdf->SetY(-14);
    $this->pdf->SetFont($this->pdf->rapport_font,'',8);
    $this->pdf->SetX(0);
    $this->pdf->MultiCell(210,4,
"Hazenweg 110 - Postbus 125 - 7550 AC Hengelo - T (074) 248 00 48 - F (074) 248 00 49 - info@groenstate.nl - www.groenstate.nl
ABN AMRO 54.16.74.382 - IBAN NL42ABNA0541674382 - BTW nr. 807885113B01 - K.v.K. Twente & Salland 06090851",0,'C');
    $this->pdf->AutoPageBreak=true;
    $this->pdf->SetTextColor(0,0,0);
	}


	function writeRapport()
	{
		global $__appvar;

		  $this->voorBrief();
   
    

   // $this->pdf->AddPage();
	 // $this->pdf->frontPage = true;

	}
}
?>
