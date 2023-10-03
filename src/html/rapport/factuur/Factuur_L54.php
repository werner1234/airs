<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/16 17:10:40 $
File Versie					: $Revision: 1.19 $

$Log: Factuur_L54.php,v $
Revision 1.19  2019/11/16 17:10:40  rvv
*** empty log message ***

Revision 1.18  2019/10/19 16:47:57  rvv
*** empty log message ***

Revision 1.17  2019/04/17 11:21:04  rvv
*** empty log message ***

Revision 1.16  2018/07/11 16:15:22  rvv
*** empty log message ***

Revision 1.15  2018/07/07 17:34:05  rvv
*** empty log message ***

Revision 1.14  2018/04/21 17:57:30  rvv
*** empty log message ***

Revision 1.13  2017/09/09 18:15:36  rvv
*** empty log message ***

Revision 1.12  2017/04/23 12:50:36  rvv
*** empty log message ***

Revision 1.11  2016/07/16 15:15:15  rvv
*** empty log message ***

Revision 1.10  2016/01/18 20:31:28  rvv
*** empty log message ***

Revision 1.9  2016/01/18 19:34:52  rvv
*** empty log message ***

Revision 1.8  2016/01/18 19:15:18  rvv
*** empty log message ***

Revision 1.7  2016/01/18 06:57:40  rvv
*** empty log message ***

Revision 1.6  2016/01/17 18:17:14  rvv
*** empty log message ***

Revision 1.5  2015/01/24 19:53:41  rvv
*** empty log message ***

Revision 1.4  2014/10/19 08:53:58  rvv
*** empty log message ***

Revision 1.3  2014/10/15 16:07:30  rvv
*** empty log message ***

Revision 1.2  2014/10/08 15:44:12  rvv
*** empty log message ***

Revision 1.1  2014/04/10 06:02:04  rvv
*** empty log message ***

Revision 1.5  2013/12/23 16:43:32  rvv
*** empty log message ***

Revision 1.4  2013/07/19 07:11:17  rvv
*** empty log message ***

Revision 1.3  2013/07/18 17:46:37  rvv
*** empty log message ***

Revision 1.2  2013/07/17 08:13:15  rvv
*** empty log message ***

Revision 1.1  2013/06/15 15:55:44  rvv
*** empty log message ***

Revision 1.3  2013/04/27 16:28:55  rvv
*** empty log message ***

*/


    global $__appvar;
		$this->pdf->rapport_type = "FACTUUR";
    

    $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight=5;

	
		$this->pdf->AddPage('P');
  $altijdHAV=true;


    if(is_file($this->pdf->rapport_logo))
		{
      $toekomstLogo=$__appvar["basedir"]."/html/rapport/logo/toekomstbeleggen.png";
      if($this->pdf->portefeuilledata['SoortOvereenkomst']=='Toekomstbeleggen' && file_exists($toekomstLogo))
      {
        $factor=0.04;
        $xSize=1605*$factor;//$x=885*$factor;
        $ySize=203*$factor;//$y=849*$factor;
        $logo = $toekomstLogo;
        $xpos = 135;
      }
      else
      {
        $logo=$this->pdf->rapport_logo;
        $factor=0.035;
        if ($altijdHAV || $this->pdf->portefeuilledata['Vermogensbeheerder'] == 'HAV')
        {
          $factor=0.042;
          $xSize=1612*$factor;//$x=885*$factor;
          $ySize=666*$factor;//$y=849*$factor;

        //  $xSize = 2509 * $factor;//$x=885*$factor;
            $xpos = 13;//140 - (2509 - 1605) * $factor;
        }
        else
        {
         $xSize = 1605 * $factor;
         $xpos = 140;
        }//$x=885*$factor;
        $ySize = 611 * $factor;//$y=849*$factor;
       }
			 $this->pdf->Image($logo,$xpos , 13, $xSize, $ySize);
      // 92,109,119
      // $this->pdf->Rect(0,280,210,19,"F",'',array(92,109,119));
		}
    $font='times';
    $this->pdf->SetY(40);
    $kopFontSize=7;
    $this->pdf->SetTextColor(123,131,136);
    $this->pdf->SetWidths(array(155,50));
    $this->pdf->SetAligns(array("L","L","L"));
    $this->pdf->SetFont($font,"B",$kopFontSize);
    $this->pdf->row(array('','Bezoekadres:'));
    $this->pdf->SetFont($font,"",$kopFontSize);
    $this->pdf->row(array('','Ramgatseweg 7A'));
    $this->pdf->row(array('','4941 VN Raamsdonksveer'));
    $this->pdf->Ln(2);
    $this->pdf->SetFont($font,"B",$kopFontSize);
    $this->pdf->row(array('','Postadres:'));
    $this->pdf->SetFont($font,"",$kopFontSize);
    $this->pdf->row(array('','Postbus 298'));
    $this->pdf->row(array('','4940 AG Raamsdonksveer'));
    $this->pdf->Ln(2);
    $this->pdf->SetWidths(array(154,1,4+50));
    $this->pdf->SetFont($font,"B",$kopFontSize);
    $telStatY=$this->pdf->GetY();

    if (!$altijdHAV && $this->pdf->portefeuilledata['Vermogensbeheerder'] <> 'HAV')
    {
      $this->pdf->SetWidths(array(155,4,50));
      $this->pdf->row(array('', 'T'));
      $this->pdf->row(array('', 'F'));
      $this->pdf->row(array('', 'E'));
      $this->pdf->row(array('', 'I'));
    }
    $this->pdf->SetY($telStatY);
    $this->pdf->SetFont($font,"",$kopFontSize);
    if ($altijdHAV || $this->pdf->portefeuilledata['Vermogensbeheerder'] == 'HAV') //tel
      $this->pdf->row(array('','',' +31-088 - 0081333'));
    else
      $this->pdf->row(array('','','+31 (0)88 008 13 33'));

    if (!$altijdHAV && $this->pdf->portefeuilledata['Vermogensbeheerder'] <> 'HAV') //fax
      $this->pdf->row(array('','','+31 (0)88 008 13 34'));

    if ($this->pdf->portefeuilledata['SoortOvereenkomst']=='Toekomstbeleggen')
    {
      $this->pdf->row(array('', '', 'info@toekomstbeleggen.nl'));
      $this->pdf->row(array('', '', 'www.toekomstbeleggen.nl'));
    }
    elseif ($altijdHAV || $this->pdf->portefeuilledata['Vermogensbeheerder'] == 'HAV')
    {
      $this->pdf->row(array('', '', 'info@comfortvermogensbeheer.nl'));
      $this->pdf->row(array('', '', 'www.comfortvermogensbeheer.nl'));
    }
    else
    {
      $this->pdf->row(array('', '', 'info@svdhvermogensbeheer.nl'));
      $this->pdf->row(array('', '', 'www.svdhvermogensbeheer.nl'));
    }
    $this->pdf->SetTextColor(0);
    


    $this->pdf->SetY(85);
  	$this->pdf->SetFont($font,"",11);

   $this->DB = new DB();


    $query="SELECT
Accountmanagers.Accountmanager,
Accountmanagers.Naam,
Accountmanagers.Titel,
IFNULL(Gebruikers.emailAdres,'schraauwers@svdhvermogensbeheer.nl') as email
FROM
Accountmanagers
LEFT JOIN Gebruikers ON Accountmanagers.Accountmanager = Gebruikers.Accountmanager 
WHERE Accountmanagers.accountmanager = '".$this->waarden['Accountmanager']."'";
	  $this->DB->SQL($query); //echo $query;
	  $accountmanager = $this->DB->lookupRecord(); 
      // listarray($accountmanager);

$db=new DB();
$query="SELECT id,highwatermark,portfoliowaarde,datum FROM feehistorie WHERE portefeuille='".$this->portefeuille."' AND datum < '".$this->waarden['datumTot']."' ORDER BY datum desc limit 1 ";
$db->SQL($query);
$db->Query();
$oldHighWater=$db->lookupRecord();


    
		$this->pdf->SetY(55);
		$this->pdf->SetWidths(array(25,100+180));
		$this->pdf->SetAligns(array("L","L","L"));
		$this->pdf->row(array('',$this->waarden['CRM_naam']));
		if ($this->waarden['CRM_naam1'] !='')
		  $this->pdf->row(array('',$this->waarden['CRM_naam1']));
		$this->pdf->row(array('',$this->waarden['CRM_verzendAdres']));
		$plaats='';
    $plaats=$this->waarden['CRM_verzendPc'];
    if($this->waarden['CRM_verzendPlaats']!= '') 
      $plaats.=" ".$this->waarden['CRM_verzendPlaats'];
		$this->pdf->row(array('',$plaats));
    $this->pdf->row(array('',$this->waarden['CRM_verzendLand']));
		
    $this->pdf->SetWidths(array(25,100,80));

  //  $this->pdf->SetFont($font,"B",10);
  //  $this->pdf->row(array('',"Factuurnummer: ".sprintf("%06d",$this->waarden['factuurNummer'])));
//    $this->pdf->SetFont($font,"",10);
 //   $this->pdf->ln(6);
    
    $vanjul=db2jul($this->waarden['datumVan']);
		if(substr($this->waarden['datumVan'],5,5) != '01-01')
		  $vanjul+=86400;
   	$vanDatum=date("j",$vanjul)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$vanjul)],$this->pdf->rapport_taal)." ".date("Y",$vanjul);
    $totDatum=date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot']));
    $nu=date("j")." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y");
 

    $this->pdf->SetWidths(array(25,100));
    $this->pdf->SetAligns(array("L","L",'R','L'));
    $this->pdf->SetY(100);
    $telStatY=$this->pdf->GetY();
    $this->pdf->SetFont($font,"",8);
    $this->pdf->row(array('Behandeld door'));
    $this->pdf->row(array('E-mail'));
    $this->pdf->row(array("Datum"));
    $this->pdf->row(array('Onderwerp'));
    $this->pdf->row(array("Factuurnummer",$this->factuurnummer));
    $this->pdf->SetY($telStatY);   
    $this->pdf->row(array('',$accountmanager['Naam']));
    $this->pdf->row(array('',$accountmanager['email']));
    $this->pdf->row(array('',$nu));
    $this->pdf->row(array('',"Factuur vermogensbeheer"));
    $this->pdf->row(array("",$this->factuurnummer));
    $this->pdf->SetY(140);
        $this->pdf->SetFont($font,"",10);
$this->pdf->SetWidths(array(25,150));
$this->pdf->row(array('',$this->waarden['CRM_verzendAanhef'].'


Hierbij zenden wij u onze nota inzake vermogensbeheer.
'));  

$this->pdf->SetAligns(array("L","L",'R','R'));
$this->pdf->SetWidths(array(25,95,10,20));  


$this->pdf->CellBorders = array('','U','','U');
$this->pdf->ln();
$this->pdf->row(array('',"Omschrijving",'','Bedrag'));
unset($this->pdf->CellBorders);
$this->pdf->ln(1);
//listarray($this->waarden['basisRekenvermogen']);


if ($altijdHAV || $this->pdf->portefeuilledata['Vermogensbeheerder'] == 'HAV')
{
  $this->pdf->row(array('', "Beheerfee over het " . $this->waarden['kwartaal'] . "e kwartaal " . substr($this->waarden['datumVan'], 0, 4), '€', $this->formatGetal($this->waarden['beheerfeeBetalen'] - $this->waarden['administratieBedrag'] - $this->waarden['performancefee'], 2)));
}
else
{
  $this->pdf->row(array('', "Advies-/beheerloon over het " . $this->waarden['kwartaal'] . "e kwartaal " . substr($this->waarden['datumVan'], 0, 4), '€', $this->formatGetal($this->waarden['beheerfeeBetalen'] - $this->waarden['administratieBedrag'] - $this->waarden['performancefee'], 2)));
}

if($this->waarden['BeheerfeeMethode']==3)
{
  if ($altijdHAV || $this->pdf->portefeuilledata['Vermogensbeheerder'] == 'HAV')
  {
    $tmp=explode('.',round($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'],6));
    $decimalen=strlen($tmp[1]);
    if($decimalen<2)
      $decimalen=2;
    $this->pdf->row(array('', $this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'], $decimalen) . "% x € " . $this->formatGetal($this->waarden['basisRekenvermogen'], 2)));
  }
}

/*
   [highwatermark] => Array
        (
            [hoogsteWaarde] => 507718.59
            [performanceFeePercentage] => 10
            [rendementTbvFee] => 9718.59
            [periodeRendement] => 237
        )


 */

$this->pdf->ln();
if($oldHighWater['highwatermark'] <> 0  &&
  ($this->waarden['portefeuilledata']['BeheerfeePerformancefeeJaarlijks']==0 ||
    ($this->waarden['portefeuilledata']['BeheerfeePerformancefeeJaarlijks']==1 && substr($this->waarden['datumTot'],5,5)=='12-31')
  ))
{
  $this->pdf->row(array('','De vorige high watermark bedraagt € '.$this->formatGetal($oldHighWater['highwatermark'],2)));
  $this->pdf->ln();
  //$this->pdf->row(array('','De high watermark bedraagt € '.$this->formatGetal($this->waarden['basisRekenvermogen']-$this->waarden['performancefeeRekenbedrag'],2)));
}
if($this->waarden['performancefee'] <> 0 && $this->waarden['highwatermark']['periodeRendement'] <> 0)
  $this->pdf->row(array('',"Performance fee over afgelopen periode € ".
  $this->formatGetal($this->waarden['highwatermark']['periodeRendement'],2)."x".$this->waarden['highwatermark']['performanceFeePercentage']."%",'€',$this->formatGetal($this->waarden['highwatermark']['periodeRendement']*$this->waarden['highwatermark']['performanceFeePercentage']/100,2)));
if($this->waarden['administratieBedrag'] <> 0)
  $this->pdf->row(array('',"Kosten toezichthouders",'€',$this->formatGetal($this->waarden['administratieBedrag'],2)));
$this->pdf->ln();
$this->pdf->row(array('',"BTW percentage ".$this->formatGetal($this->waarden['btwTarief'],0)."%",'€',$this->formatGetal($this->waarden['btw'],2)));
$this->pdf->ln(2);
$this->pdf->CellBorders = array('','T','','T');
$this->pdf->row(array('',' ',' ',' '));
unset($this->pdf->CellBorders);
$this->pdf->row(array('','Totaal','€',$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
$this->pdf->ln(2);
$this->pdf->row(array('',' ',' ','---------------'));

$this->pdf->SetWidths(array(25,150));
if($this->waarden['BetalingsinfoMee']==1)
  $this->pdf->row(array('','Wij verzoeken u bovenstaand bedrag binnen 30 dagen na dagtekening aan ons te voldoen onder vermelding van het factuurnummer op rekeningnummer NL50ABNA0563007060 ten name van Schraauwers Van den Hout & Partners Vermogensbeheer B.V. te Raamsdonksveer'));
else
  $this->pdf->row(array('','Het factuurbedrag wordt binnen enkele dagen van uw rekening '.$this->waarden['rekeningEur'].' geïncasseerd.'));

if(round($this->waarden['highwatermark']['hoogsteWaarde']) > 0 &&
                          ($this->waarden['portefeuilledata']['BeheerfeePerformancefeeJaarlijks']==0 ||
                              ($this->waarden['portefeuilledata']['BeheerfeePerformancefeeJaarlijks']==1 && substr($this->waarden['datumTot'],5,5)=='12-31')
                          )
  )
{
  $this->pdf->row(array('', 'De nieuwe high watermark is vastgesteld op € ' . $this->formatGetal($this->waarden['highwatermark']['hoogsteWaarde'], 2)));
}
$this->pdf->ln();

$this->pdf->row(array('','Met vriendelijke groet,

'.$accountmanager['Naam'].'
'.$accountmanager['Titel']));

//
$this->pdf->SetTextColor(123,131,136);
		$this->pdf->SetWidths(array(210-2*$this->pdf->marge));
		$this->pdf->SetAligns(array("C"));
    $this->pdf->SetY(260);
    $this->pdf->Ln();
	$this->pdf->SetFont($font,"",8);
/*

else
*/
if ($this->pdf->portefeuilledata['SoortOvereenkomst']=='Toekomstbeleggen')
{
  $this->pdf->row(array('Toekomstbeleggen.nl
KvK nr. 53897099 | BTW nr. 8510.63.664.B.01'));
}
elseif ($altijdHAV || $this->pdf->portefeuilledata['Vermogensbeheerder'] == 'HAV')
{
  $this->pdf->row(array('Comfort Vermogensbeheer
KvK nr. 53897099 | BTW nr. 8510.63.664.B.01'));
}
/*
{
  $this->pdf->row(array('Schraauwers Van den Hout & Partners Vermogensbeheer B.V. ABN AMRO nr. 56.30.07.060 | IBAN nr. NL50ABNA0563007060 
KvK nr. 20073497 | BTW nr. 0061.35.602.B.01'));
}
*/
$this->pdf->SetTextColor(0,0,0);
$this->pdf->rowHeight=$rowHeightBackup;
?>
