<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/04/26 15:16:49 $
File Versie					: $Revision: 1.22 $

$Log: Factuur_L33.php,v $
Revision 1.22  2017/04/26 15:16:49  rvv
*** empty log message ***

Revision 1.21  2017/04/08 18:21:38  rvv
*** empty log message ***

Revision 1.20  2017/02/01 16:45:45  rvv
*** empty log message ***

Revision 1.19  2016/10/14 07:46:12  rvv
*** empty log message ***

Revision 1.18  2016/10/12 16:27:43  rvv
*** empty log message ***

Revision 1.17  2016/07/13 12:59:08  rvv
*** empty log message ***

Revision 1.16  2015/10/11 17:33:00  rvv
*** empty log message ***

Revision 1.15  2015/10/04 11:51:13  rvv
*** empty log message ***

Revision 1.14  2014/04/05 15:35:00  rvv
*** empty log message ***

Revision 1.13  2012/05/23 15:57:43  rvv
*** empty log message ***

Revision 1.12  2012/04/12 14:57:57  rvv
*** empty log message ***

Revision 1.11  2011/10/09 16:55:25  rvv
*** empty log message ***

Revision 1.10  2011/07/08 11:52:53  cvs
*** empty log message ***

Revision 1.9  2011/07/08 11:18:33  cvs
*** empty log message ***

Revision 1.8  2011/07/08 08:21:27  cvs
*** empty log message ***

Revision 1.7  2011/06/08 18:19:31  rvv
*** empty log message ***

Revision 1.6  2011/05/14 10:51:54  rvv
*** empty log message ***

Revision 1.5  2011/04/12 12:32:23  cvs
*** empty log message ***

Revision 1.4  2011/04/11 18:02:23  rvv
*** empty log message ***

Revision 1.3  2011/04/09 14:36:26  rvv
*** empty log message ***

Revision 1.2  2011/04/03 08:41:58  rvv
*** empty log message ***

Revision 1.1  2011/03/30 20:22:26  rvv
*** empty log message ***

Revision 1.4  2011/01/23 08:55:37  rvv
*** empty log message ***

Revision 1.3  2011/01/12 17:20:35  rvv
*** empty log message ***

Revision 1.2  2011/01/12 16:16:42  rvv
*** empty log message ***

Revision 1.1  2011/01/12 12:28:53  rvv
*** empty log message ***

Revision 1.2  2010/07/21 17:49:59  rvv
*** empty log message ***

Revision 1.1  2010/07/21 17:37:57  rvv
*** empty log message ***


*/

global $__appvar;

/*
$db=new DB();
$query="SELECT Portefeuilles.*, Vermogensbeheerders.* FROM Portefeuilles Join Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder WHERE Portefeuilles.portefeuille='".$this->waarden['portefeuille']."'";
$db->SQL($query);
$portefeuilleData=$db->lookupRecord();
listarray($portefeuilleData);
*/


    $this->pdf->rowHeight = 5;
   	$this->pdf->underlinePercentage=0.8;
    $this->pdf->brief_font='Arial';
    //$this->pdf->brief_font='Times';

    $this->pdf->SetFont($this->pdf->brief_font,'',8);
		$this->pdf->rapport_type = "FACTUUR";

		$this->pdf->AddPage('P');


   // $this->voetFront=base64_decode('iVBORw0KGgoAAAANSUhEUgAABpEAAABOCAMAAAD/286oAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OUQ2ODY3RDFCQTNEMTFFM0JFNTlBRTc1OTM4MjdGRTIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OUQ2ODY3RDJCQTNEMTFFM0JFNTlBRTc1OTM4MjdGRTIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo5RDY4NjdDRkJBM0QxMUUzQkU1OUFFNzU5MzgyN0ZFMiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo5RDY4NjdEMEJBM0QxMUUzQkU1OUFFNzU5MzgyN0ZFMiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PjsFZzIAAAKFUExURYfO6//83GKQ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');

		$vanaf=db2jul($this->waarden['datumVan']);
		$tot=db2jul($this->waarden['datumTot']);

	  $logo=$__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Logo'];
		if(is_file($logo))
		{
      $logoYpos=5;
		  $xSize=19;
	    $this->pdf->Image($this->pdf->rapport_logo,210/2-$xSize/2, $logoYpos, $xSize);
      $imageWidth=150;
   //   $this->pdf->MemImage($this->voetFront,210/2-$imageWidth/2,287,$imageWidth);
		}

$this->DB = new DB();

			  $query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.verzendAdres,
CRM_naw.verzendPc,
CRM_naw.verzendPlaats,
CRM_naw.verzendLand,
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
CRM_naw.enOfRekening
FROM CRM_naw WHERE Portefeuille = '".$this->portefeuille."'  ";

	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();
		$this->pdf->SetWidths(array(25-$this->pdf->marge,140));
	  $this->pdf->SetAligns(array('R','L','L','R','R'));
    $this->pdf->rowHeight = 5;
	  $this->pdf->SetY(42);
	  $this->pdf->SetFont($this->pdf->brief_font,'B',11);
	  $this->pdf->row(array('',""));//Vertrouwelijk
	  $this->pdf->SetFont($this->pdf->brief_font,'',11);
		$this->pdf->row(array('',$crmData['naam']));
    if (trim($crmData['naam1']) <> "")  $this->pdf->row(array('',$crmData['naam1']));
    $this->pdf->row(array('',$crmData['verzendAdres']));
    $plaats=$crmData['verzendPc'];
    if($crmData['verzendPlaats'] != '') $plaats.=" ".$crmData['verzendPlaats'];
    $this->pdf->row(array('',$plaats));
    $this->pdf->row(array('',$crmData['verzendLand']));


$this->pdf->SetY(80);

$this->pdf->row(array('','Amsterdam, '.(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
$this->pdf->SetWidths(array(25-$this->pdf->marge,40,70));
$this->pdf->ln(10);
$this->pdf->row(array('',vertaalTekst("Factuurnummer",$this->pdf->rapport_taal).":",$this->waarden['factuurNummer']));
$this->pdf->row(array('',vertaalTekst("Debiteurnummer",$this->pdf->rapport_taal).":",$this->waarden['debiteurnr']));
$this->pdf->row(array('',vertaalTekst("B.T.W.nummer",$this->pdf->rapport_taal).":",'NL 8088.02.926.B.01'));
$this->pdf->line(25,$this->pdf->getY()+2,185,$this->pdf->getY()+2);
$this->pdf->ln(15);
//$this->pdf->SetFont($this->pdf->brief_font,'B',11);
$this->pdf->SetWidths(array(25-$this->pdf->marge,160));
     $this->pdf->row(array('',vertaalTekst("Vergoeding B.A. van Doorn over de periode",$this->pdf->rapport_taal)." ".date("j",db2jul($this->waarden['datumVan']))." ".vertaalTekst($__appvar["Maanden"][date("n",db2jul($this->waarden['datumVan']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumVan'])).
' t/m '.
		date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot']))));

     $this->pdf->SetFont($this->pdf->brief_font,'',11);
     $this->pdf->ln();

     $onderlijnStart=95-$this->pdf->marge;
     $onderlijnEind=$onderlijnStart+40;
/*
if(substr($this->waarden['datumVan'],5,5)=="01-01")
{
  $offset=-1;
}
else
*/
  $offset=0;
$this->pdf->SetWidths(array(25-$this->pdf->marge,110,10,30,10));
$this->pdf->SetAligns(array('R','L','R','R','L'));
$this->pdf->row(array('',vertaalTekst("Vermogen eind",$this->pdf->rapport_taal)." ".vertaalTekst($__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))-2+$offset],$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['maandsWaarde_1'],2)));
$this->pdf->row(array('',vertaalTekst("Vermogen eind",$this->pdf->rapport_taal)." ".vertaalTekst($__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))-1+$offset],$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['maandsWaarde_2'],2)));
$this->pdf->row(array('',vertaalTekst("Vermogen eind",$this->pdf->rapport_taal)." ".vertaalTekst($__appvar["Maanden"][date("n",  db2jul($this->waarden['datumTot']))+$offset],$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['maandsWaarde_3'],2)));
$this->pdf->SetWidths(array(25-$this->pdf->marge,110,40));
$this->pdf->row(array('','',"-------------------------"));
$this->pdf->SetWidths(array(25-$this->pdf->marge,110,10,30));
$this->pdf->row(array('',vertaalTekst("Gemiddeld vermogen",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['basisRekenvermogen'],2)));//$this->formatGetal(($this->waarden['drieMaandsWaarde_1']+$this->waarden['drieMaandsWaarde_2']+$this->waarden['drieMaandsWaarde_3'])/3
$this->pdf->SetWidths(array(25-$this->pdf->marge,110,40));
$this->pdf->row(array('','',"=============="));
$this->pdf->SetWidths(array(25-$this->pdf->marge,110,10,30));

$this->pdf->ln();

 $percentage=$this->waarden['BeheerfeePercentageVermogen'] / $this->waarden['BeheerfeeAantalFacturen'];

 if(isset( $this->waarden['periodeDagen']['dagen']))
 {
   $dagenTekst= "x ".$this->waarden['periodeDagen']['dagen'].'/'.$this->waarden['periodeDagen']['dagenInJaar']." ".vertaalTekst("dagen",$this->pdf->rapport_taal);
 }
 else
   $dagenTekst='';

$this->pdf->SetWidths(array(25-$this->pdf->marge,110,10,30));
$this->pdf->SetAligns(array('R','L','R','R'));
$this->pdf->row(array('',vertaalTekst("Fee berekening volgens afspraak",$this->pdf->rapport_taal)));
$this->pdf->row(array('',"$dagenTekst"));
$this->pdf->ln();
$this->pdf->ln();
$this->pdf->row(array('',vertaalTekst("Vergoeding",$this->pdf->rapport_taal),'€',$this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));
$this->pdf->row(array('',vertaalTekst("B.T.W.",$this->pdf->rapport_taal).$this->formatGetal($this->waarden['btwTarief'],0)."%",'€',$this->formatGetal($this->waarden['btw'],2)));

//$this->pdf->SetLineStyle(array('dash' => 0.5));
//$this->pdf->line($onderlijnStart,$this->pdf->getY(),$onderlijnEind,$this->pdf->getY());
$this->pdf->SetWidths(array(25-$this->pdf->marge,110,40));
$this->pdf->row(array('','',"-------------------------"));
$this->pdf->SetWidths(array(25-$this->pdf->marge,110,10,30));
$this->pdf->row(array('',vertaalTekst("Totaal",$this->pdf->rapport_taal),'€',$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
$this->pdf->SetWidths(array(25-$this->pdf->marge,110,40));
$this->pdf->row(array('','',"=============="));
$this->pdf->SetWidths(array(25-$this->pdf->marge,110,10,30));
$this->pdf->ln(15);
$this->pdf->SetWidths(array(25-$this->pdf->marge,160));

if($this->waarden['BetalingsinfoMee']==1)
  $this->pdf->row(array('',vertaalTekst("Wij verzoeken u vriendelijk het factuurbedrag binnen 14 dagen aan ons over te maken op rekeningnummer NL53ABNA0527880396 t.n.v. B.A. van Doorn & Comp B.V. bij ABN Amro Bank o.v.v. het factuurnummer.",$this->pdf->rapport_taal)." ".vertaalTekst("Mocht u vragen hebben over dit overzicht dan kunt u contact opnemen met uw beheerder.",$this->pdf->rapport_taal)));
else
  $this->pdf->row(array('',vertaalTekst("Wij zullen het factuurbedrag binnen 14 dagen incasseren van uw rekening bij",$this->pdf->rapport_taal)." ".vertaalTekst($this->waarden['depotbankOmschrijving'],$this->pdf->rapport_taal).".".vertaalTekst("Mocht u vragen hebben over dit overzicht dan kunt u contact opnemen met uw beheerder.",$this->pdf->rapport_taal)));
$this->pdf->ln(15);

 $trigger=$this->pdf->PageBreakTrigger;
 $this->pdf->PageBreakTrigger=$this->pdf->PageBreakTrigger+10;

 $this->pdf->setY(287.8);
 //$this->pdf->SetTextColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
 $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
 $this->pdf->SetTextColor(255,255,255);
 $this->pdf->Rect(30,297-10,210-60,6,'F');
 $this->pdf->SetAligns(array('R','C'));
 $this->pdf->SetFont($this->pdf->brief_font,'',10);
 //$this->pdf->row(array('',"J.J. Viottastraat 35 | 1071 JP  AMSTERDAM | T 020 712 99 99 | www.bavandoorn.nl"));
 $this->pdf->row(array('',$this->pdf->portefeuilledata['VermogensbeheerderAdres']." | ".$this->pdf->portefeuilledata['VermogensbeheerderWoonplaats']." | T ".$this->pdf->portefeuilledata['VermogensbeheerderTelefoon']." | ".str_replace(array('http://','/'),array('',''),$this->pdf->portefeuilledata['VermogensbeheerderWebsite'])));

$this->pdf->PageBreakTrigger=$trigger;

 $this->pdf->SetTextColor(0,0,0);
 $this->pdf->geenBasisFooter=true;


?>