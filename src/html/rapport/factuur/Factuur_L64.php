<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/27 16:27:03 $
File Versie					: $Revision: 1.15 $

$Log: Factuur_L64.php,v $
Revision 1.15  2020/06/27 16:27:03  rvv
*** empty log message ***

Revision 1.14  2019/06/26 15:09:39  rvv
*** empty log message ***

Revision 1.13  2019/06/22 16:34:13  rvv
*** empty log message ***

Revision 1.12  2018/07/23 17:30:27  rvv
*** empty log message ***

Revision 1.11  2018/04/12 06:08:03  rvv
*** empty log message ***

Revision 1.10  2018/04/11 15:20:15  rvv
*** empty log message ***

Revision 1.9  2018/03/28 15:46:46  rvv
*** empty log message ***

Revision 1.8  2017/10/21 17:31:59  rvv
*** empty log message ***

Revision 1.7  2017/10/14 17:26:05  rvv
*** empty log message ***

Revision 1.6  2017/04/26 15:16:49  rvv
*** empty log message ***

Revision 1.5  2017/04/12 08:30:57  rvv
*** empty log message ***

Revision 1.4  2016/01/27 17:09:49  rvv
*** empty log message ***

Revision 1.3  2016/01/22 10:56:28  rvv
*** empty log message ***

Revision 1.1  2015/11/29 13:16:01  rvv
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


$this->pdf->rowHeightBackup = $this->pdf->rowHeight;

   	$this->pdf->underlinePercentage=0.8;
    $this->pdf->brief_font='Arial';
    $this->pdf->SetFont($this->pdf->brief_font,'',8);
		$this->pdf->rapport_type = "FACTUUR";

    if($this->pdf->selectData['allInOne']==1)
    {
      $this->pdf->oddEvenCheck[$this->portefeuille]=count($this->pdf->pages);
    }
    else
    {
		  if (count($this->pdf->pages) % 2)
		  {
  	  	$this->pdf->AddPage('L');
		  }
    }


    $this->pdf->oddEvenCheck[$this->portefeuille]=count($this->pdf->pages);

		$this->pdf->AddPage('P');
		$this->pdf->SetFont($this->pdf->rapport_font,'',10);
    $logo=$__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Logo'];
    if(is_file($logo))
    {
      $factor=0.06;
      $xSize=1200*$factor;
      $ySize=377*$factor;
      $logopos=16;//(297/2)-($xSize/2);
      $this->pdf->Image($logo, $logopos, 16, $xSize, $ySize);
    }

		$vanaf=db2jul($this->waarden['datumVan']);
		$tot=db2jul($this->waarden['datumTot']);



$this->DB = new DB();


			  $query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.btwnr,
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
CRM_naw.enOfRekening,
CRM_naw.debiteurnr
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
    if($crmData['naam1']<>'')
      $this->pdf->row(array('',$crmData['naam1']));
    $this->pdf->row(array('',$crmData['verzendAdres']));
    $plaats=$crmData['verzendPc'];
    if($crmData['verzendPlaats'] != '') $plaats.=" ".$crmData['verzendPlaats'];
    $this->pdf->row(array('',$plaats));
    $this->pdf->row(array('',$crmData['verzendLand']));

$this->pdf->SetY(80);
$this->pdf->SetWidths(array(25-$this->pdf->marge,100,60));
    $this->pdf->row(array('','Factuurdatum: '.(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y"),"Factuurnummer: ".$this->waarden['factuurNummer']));
if($this->waarden['btwTarief'] == 0)
{
  $this->pdf->row(array('','', "BTW-nummer: " . $crmData['btwnr']));
}
$this->pdf->ln();
$this->pdf->SetFont($this->pdf->brief_font,'B',11);
$this->pdf->SetWidths(array(25-$this->pdf->marge,160));
     $this->pdf->row(array('',"Factuur"));
     $this->pdf->SetFont($this->pdf->brief_font,'',11);
     $this->pdf->ln();

     $portefeuille=$this->waarden['portefeuille'];

     $query="SELECT Rekening,Valuta,PortefeuilleVoorzet FROM Rekeningen JOIN Portefeuilles ON Rekeningen.Portefeuille=Portefeuilles.Portefeuille WHERE Rekeningen.Portefeuille='".$this->waarden['portefeuille']."' AND Memoriaal=0 AND Inactief=0 AND Valuta='EUR'";
     $this->DB->SQL($query);

	   $rekeningData = $this->DB->lookupRecord();
     $rekening=$rekeningData['PortefeuilleVoorzet'].str_replace($rekeningData['Valuta'],"",$rekeningData['Rekening']);
     $rekening=$this->portefeuille;//$this->waarden['IBAN'];


if($this->waarden['Depotbank']=='GIRO')
  $intro="Per ultimo vorig kwartaal was de totale waarde van uw beleggingen in uw portefeuille bij DeGiro aangehouden girale depot met gebruikersnaam ".$crmData['debiteurnr'].",";
else
  $intro="Per ultimo vorig kwartaal was de totale waarde van uw beleggingen in uw portefeuille met nummer $rekening bij uw depotbank,";
     $this->pdf->row(array('',$intro.
                       " � ". $this->formatGetal($this->waarden['totaalWaarde'],2)."
"));
$this->pdf->ln();

$this->pdf->SetWidths(array(25-$this->pdf->marge,80,35,5,30));
 $this->pdf->row(array('',"Omschrijving:",'','',"Factuurbedrag:"));



 $percentage=$this->waarden['staffelWaarden']['schijvenPerentage']/$this->waarden['BeheerfeeAantalFacturen'];
 //echo " ". $percentage;
 $percentageParts=explode('.',$percentage);
 $partLen=strlen($percentageParts[1]);
 if($partLen==3)
   $percentage=$this->formatGetal($percentage,3);
 elseif($partLen>3)
   $percentage=$this->formatGetal($percentage,4);
 else
   $percentage=$this->formatGetal($percentage,2);

//$percentage=$this->formatGetal($percentage,2);//$this->waarden['BeheerfeePercentageVermogen'] / $this->waarden['BeheerfeeAantalFacturen'];


// listarray($this->waarden);
/*
 if(isset( $this->waarden['periodeDagen']['dagen']))
 {
   $dagenTekst= $this->waarden['periodeDagen']['dagen'].' dagen van in totaal '.$this->waarden['periodeDagen']['dagenInHelePeriode']." dagen.";
 }
 else
 */
 $dagenTekst='';

 if($this->waarden['periodeDagen']['dagenInJaar'] <> '')
    $dagenTekst=" (".$this->waarden['periodeDagen']['dagen']." dagen".")"; //."/".$this->waarden['periodeDagen']['dagenInJaar']." x ".$this->waarden['BeheerfeePercentageVermogen']."%";


 $this->pdf->row(array('',"Vergoeding betreffende het afgelopen kwartaal$dagenTekst.","$percentage %",'�',
                 $this->formatGetal($this->waarden['beheerfeePerPeriode']-$this->waarden['administratieBedrag'],2)));
 if($this->waarden['administratieBedrag'])
   $this->pdf->row(array('',"Administratiekosten","",'�',$this->formatGetal($this->waarden['administratieBedrag'],2)));


if(isset($this->waarden['extraFactuurregels']['regels']))
{
  foreach($this->waarden['extraFactuurregels']['regels'] as $factuurRegel)
  {
    $this->pdf->row(array('',$factuurRegel['omschrijving'],"",'�',$this->formatGetal($factuurRegel['bedrag'],2)));
  }
}
 
 $this->pdf->ln();
$this->pdf->ln();
 $this->pdf->row(array('',"","Subtotaal",'�',$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));

 if($this->waarden['btwTarief']==0)
$this->pdf->row(array('',"","BTW verlegd (0%)",'�',$this->formatGetal($this->waarden['btw'],2)));
else
 $this->pdf->row(array('',"","BTW ".$this->formatGetal($this->waarden['btwTarief'],1)."%",'�',$this->formatGetal($this->waarden['btw'],2)));

$this->pdf->row(array('',"","========",'',"========"));
$this->pdf->row(array('',"","Totaal",'�',$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));

$this->pdf->ln();
$this->pdf->SetWidths(array(25-$this->pdf->marge,160));
if($this->waarden['Depotbank']=='GIRO')
  $this->pdf->row(array('',"Dit bedrag wordt binnenkort van uw girale depot afgeschreven onder vermelding van externe beheerkosten."));
else
  $this->pdf->row(array('',"Dit bedrag wordt binnenkort van uw rekening ".trim($this->waarden['IBAN'])." bij uw depotbank onder vermelding van factuurnummer ".$this->waarden['factuurNummer']." afgeschreven."));

/*
if($crmData['btwnr'] <> '')
{
  $this->pdf->ln();
  $this->pdf->row(array('',"Uw BTW-nummer is ".$crmData['btwnr']."."));
}
*/
$this->pdf->ln();



$this->pdf->SetY(270);
 $this->pdf->SetFont($this->pdf->brief_font,'',9);
 $this->pdf->AutoPageBreak=false;
 $this->pdf->SetWidths(array(25-$this->pdf->marge,200));
 $this->pdf->SetTextColor(71,74,83);
 $spaces=round(($this->pdf->GetStringWidth('BeSmart'))/($this->pdf->CurrentFont['cw'][' ']*$this->pdf->FontSize/1000));
$this->pdf->row(array('',str_repeat(' ',$spaces)." � Oranje Nassaulaan 5 � 1075 AH Amsterdam"));
$this->pdf->SetTextColor(15,176,165);
$this->pdf->Ln($this->pdf->rowHeight*-1);
$this->pdf->row(array('',"BeSmart"));
$this->pdf->SetTextColor(71,74,83);
$this->pdf->row(array('',"020-7608232 � www.besmartib.nl � info@besmartib.nl"));
//$this->pdf->row(array('',"BTW 855174092B01 � KvK 63296683 � IBAN NL49 SNSB 0909 9966 28"));
$spaces1=round(($this->pdf->GetStringWidth('BTW'))/($this->pdf->CurrentFont['cw'][' ']*$this->pdf->FontSize/1000));
$spaces2=round(($this->pdf->GetStringWidth('KvK'))/($this->pdf->CurrentFont['cw'][' ']*$this->pdf->FontSize/1000));
$spaces3=round(($this->pdf->GetStringWidth('IBAN'))/($this->pdf->CurrentFont['cw'][' ']*$this->pdf->FontSize/1000));
$this->pdf->row(array('',str_repeat(' ',$spaces1)." 855174092B01 � ".str_repeat(' ',$spaces2)." 63296683 � ".str_repeat(' ',$spaces3)." NL49 SNSB 0909 9966 28"));
$this->pdf->SetTextColor(15,176,165);
$spaces1=round(($this->pdf->GetStringWidth('855174092B01 � '))/($this->pdf->CurrentFont['cw'][' ']*$this->pdf->FontSize/1000));
$spaces2=round(($this->pdf->GetStringWidth('63296683 �'))/($this->pdf->CurrentFont['cw'][' ']*$this->pdf->FontSize/1000));
$spaces3=round(($this->pdf->GetStringWidth(' NL49 SNSB 0909 9966 28'))/($this->pdf->CurrentFont['cw'][' ']*$this->pdf->FontSize/1000));
$this->pdf->Ln($this->pdf->rowHeight*-1);
$this->pdf->row(array('',"BTW".str_repeat(' ',$spaces1)."KvK".str_repeat(' ',$spaces2)."IBAN".str_repeat(' ',$spaces3).""));
$this->pdf->AutoPageBreak=true;

/*
    $stringWidthVoor=$this->pdf->GetStringWidth('E info@sequoiabeheer.nl - ');
    $stringWidthAchter=$this->pdf->GetStringWidth(' - Rabobank NL66RABO0355054272 - KvK 09112027 - BTW nr. NL.8088.19.008.B01');
    $this->pdf->SetTextColor(0,170,236);
    $spacesVoor=round(($stringWidthVoor)/($this->pdf->CurrentFont['cw'][' ']*$this->pdf->FontSize/1000));
    $spacesAchter=round(($stringWidthAchter)/($this->pdf->CurrentFont['cw'][' ']*$this->pdf->FontSize/1000));
    $this->pdf->Ln(-4);
    $this->pdf->Row(array(str_repeat(' ',$spacesVoor).'www.sequoiabeheer.nl'.str_repeat(' ',$spacesAchter)));

*/


$this->pdf->SetWidths(array(25-$this->pdf->marge,160));
$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);


    $this->pdf->SetTextColor(0,0,0);
$this->pdf->rowHeight=$this->pdf->rowHeightBackup;
?>