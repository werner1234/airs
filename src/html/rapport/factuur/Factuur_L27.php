<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/01 17:56:34 $
File Versie					: $Revision: 1.21 $

$Log: Factuur_L27.php,v $
Revision 1.21  2018/08/01 17:56:34  rvv
*** empty log message ***

Revision 1.20  2018/07/29 10:54:35  rvv
*** empty log message ***

Revision 1.19  2018/07/28 16:31:42  rvv
*** empty log message ***

Revision 1.18  2017/10/21 17:31:59  rvv
*** empty log message ***

Revision 1.17  2017/10/18 16:17:30  rvv
*** empty log message ***

Revision 1.16  2017/08/31 05:26:36  rvv
*** empty log message ***

Revision 1.15  2017/08/16 15:59:19  rvv
*** empty log message ***

Revision 1.14  2015/12/27 16:53:44  rvv
*** empty log message ***

Revision 1.13  2013/07/04 15:38:29  rvv
*** empty log message ***

Revision 1.12  2013/03/06 17:00:17  rvv
*** empty log message ***

Revision 1.11  2013/02/06 19:05:24  rvv
*** empty log message ***

Revision 1.10  2012/10/24 16:10:08  rvv
*** empty log message ***

Revision 1.9  2012/10/02 16:17:58  rvv
*** empty log message ***

Revision 1.8  2012/08/08 15:40:13  rvv
*** empty log message ***

Revision 1.7  2012/05/19 10:49:55  rvv
*** empty log message ***

Revision 1.6  2012/01/11 19:16:46  rvv
*** empty log message ***

Revision 1.5  2011/09/14 09:26:56  rvv
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

$fee=round(($this->waarden['BeheerfeePercentageVermogen']/100)*$this->waarden['rekenvermogen']/$this->waarden['BeheerfeeAantalFacturen'],2);
$minBedrag=$this->waarden['FactuurMinimumBedrag'];
if($fee < $minBedrag)
{
  logScherm("Factuur voor " . $this->portefeuille . " afgebroken. (fee $fee <  minBedrag $minBedrag)");
  unset($this->waarden);// = array();
  return false;
}

    $this->pdf->rowHeight = 5;
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

		$vanaf=db2jul($this->waarden['datumVan']);
		$tot=db2jul($this->waarden['datumTot']);

		$kwartalen = array('null','eerste','tweede','derde','vierde');
    $logo=$__appvar['basedir']."/html/rapport/logo/logo_fintessa.jpg";
    if(is_file($logo))
		{
			$this->pdf->Image($logo, 144, 10, 54, 15);

			$this->pdf->SetY(28);
		$this->pdf->SetWidths(array(135,15,50));
	  $this->pdf->SetAligns(array('R','R','L'));
	  $this->pdf->rowHeight = 3.5;
	  $this->pdf->SetFont($this->pdf->brief_font,'',8);
	  $this->pdf->row(array('',"Telefoon","+31 (0)35 543 1450"));
	  $this->pdf->row(array('',"Fax","+31 (0)35 542 6006"));
	  $this->pdf->row(array('',"Adres","Amsterdamsestraatweg 37\n3744 MA Baarn"));
		}

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
		$this->pdf->row(array('',$crmData['verzendAanhef']));
    $this->pdf->row(array('',$crmData['verzendAdres']));
    $plaats=$crmData['verzendPc'];
    if($crmData['verzendPlaats'] != '') $plaats.=" ".$crmData['verzendPlaats'];
    $this->pdf->row(array('',$plaats));
    $this->pdf->row(array('',$crmData['verzendLand']));


$this->pdf->SetY(80);
$this->pdf->SetWidths(array(25-$this->pdf->marge,100,60));
    $this->pdf->row(array('','Factuurdatum: '.(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y"),"Factuurnummer: ".$this->waarden['factuurNummer']));
$this->pdf->ln();
$this->pdf->SetFont($this->pdf->brief_font,'B',11);
$this->pdf->SetWidths(array(25-$this->pdf->marge,160));
     $this->pdf->row(array('',"Beheervergoeding Factuur"));
     $this->pdf->SetFont($this->pdf->brief_font,'',11);
     $this->pdf->ln();

     $portefeuille=$this->waarden['portefeuille'];

//     $query="SELECT Rekening,Valuta,PortefeuilleVoorzet FROM Rekeningen JOIN Portefeuilles ON Rekeningen.Portefeuille=Portefeuilles.Portefeuille WHERE Rekeningen.Portefeuille='".$this->waarden['portefeuille']."' AND Memoriaal=0 AND Inactief=0 AND Valuta='EUR'";
//     $this->DB->SQL($query);
//	   $rekeningData = $this->DB->lookupRecord();
//     $rekening=$rekeningData['PortefeuilleVoorzet'].str_replace($rekeningData['Valuta'],"",$rekeningData['Rekening']);


     $this->pdf->row(array('',"Per ultimo vorig kwartaal was de totale waarde van uw beleggingen in uw portefeuille met depotnummer ".$this->waarden['portefeuille']." bij ".
$this->waarden['depotbankOmschrijving'].", € ". $this->formatGetal($this->waarden['totaalWaarde'],2)."
"));
$this->pdf->ln();

$this->pdf->SetWidths(array(25-$this->pdf->marge,80,30,10,30));
 $this->pdf->row(array('',"Omschrijving:",'','',"Factuurbedrag:"));

/*
 $percentageParts=explode('.',$this->waarden['BeheerfeePercentageVermogenDeelVanJaar']);
 $partLen=strlen($percentageParts[1]);
 if($partLen==3)
   $percentage=$this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'],3);
 elseif($partLen>3)
   $percentage=$this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'],4);
 else
   $percentage=$this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'],2);
*/
$percentage=$this->waarden['BeheerfeePercentageVermogen'] / $this->waarden['BeheerfeeAantalFacturen'];


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


 $this->pdf->row(array('',"Beheervergoeding betreffende het afgelopen kwartaal$dagenTekst.","$percentage %",'€',
                 $this->formatGetal($this->waarden['beheerfeePerPeriode']-$this->waarden['administratieBedrag'],2)));
 if($this->waarden['administratieBedrag'])
   $this->pdf->row(array('',"Administratiekosten","",'€',$this->formatGetal($this->waarden['administratieBedrag'],2)));

$this->pdf->ln();
$this->pdf->ln();
 $this->pdf->row(array('',"","Subtotaal",'€',$this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));
 $this->pdf->row(array('',"","BTW ".$this->formatGetal($this->waarden['btwTarief'],1)."%",'€',$this->formatGetal($this->waarden['btw'],2)));

$this->pdf->row(array('',"","========",'',"========"));
$this->pdf->row(array('',"","Totaal",'€',$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));

$this->pdf->ln();
$this->pdf->SetWidths(array(25-$this->pdf->marge,160));

if($this->waarden['rekeningIBAN'] <>'')
  $rekeningTxt=$this->waarden['rekeningIBAN'];
else
  $rekeningTxt=$this->waarden['rekeningEur'];

  $this->pdf->row(array('',"Dit bedrag wordt binnenkort van uw rekening $rekeningTxt bij ".$this->waarden['depotbankOmschrijving']." onder vermelding van factuurnummer ".$this->waarden['factuurNummer']." afgeschreven."));


if($this->waarden['FactuurMemo'] <> '')
{
  $this->pdf->ln();
  $this->pdf->row(array('', $this->waarden['FactuurMemo']));
}
/*
if($crmData['btwnr'] <> '')
{
  $this->pdf->ln();
  $this->pdf->row(array('',"Uw BTW-nummer is ".$crmData['btwnr'].", BTW verlegd i.v.m. intracommunautaire diensten."));
}
*/
$this->pdf->ln();
    $logo=$__appvar['basedir']."/html/rapport/logo/ondertekening.png";
    if(is_file($logo))
		{
      $factor=0.04;
			$this->pdf->Image($logo, $this->pdf->getX()+20, $this->pdf->getY()+10, 768*$factor, 728*$factor);
		}

 $this->pdf->row(array('',"Met vriendelijke groet,








Ing. Mark W. Sombekke CCO
Directeur Operationele Zaken
Fintessa vermogensbeheer B.V.

"));

/*
$this->pdf->SetTextColor(200,0,0);
 $this->pdf->SetFont($this->pdf->brief_font,'',8);
 $this->pdf->rowHeight = 3.5;
 $this->pdf->SetWidths(array(25-$this->pdf->marge,200));
$this->pdf->row(array('',"Zoals u waarschijnlijk bekend is van overheidswege besloten per 1 oktober 2012 het hoge btw-tarief te verhogen van 19% naar 21%.
Alle Nederlandse bedrijven zijn verplicht om te voldoen aan deze verhoging en moeten vanaf die datum 21% btw afdragen.
Op uw volgende nota zal dan ook met dit nieuwe btw-tarief worden gerekend."));
*/

$this->pdf->SetWidths(array(25-$this->pdf->marge,160));
$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
$this->pdf->rowHeight = 5;
 $trigger=$this->pdf->PageBreakTrigger;
 $this->pdf->PageBreakTrigger=$this->pdf->PageBreakTrigger+10;
 $this->pdf->setY(-15);
 $this->pdf->SetAligns(array('R','C'));
 $this->pdf->SetFont($this->pdf->brief_font,'',8);
 $this->pdf->row(array('',"Inschr.Nr. K.v.K. 32123885  -  Btw-nr. NL818017338B01  -  Bankrelatie: ABN Amro bank  -  Rek. nr.: NL72ABNA0578964813"));
 $this->pdf->PageBreakTrigger=$trigger;

/*
		$this->pdf->SetWidths(array(135,25,30));
	  $this->pdf->SetAligns(array('R','R'));
	  $this->pdf->SetFont($this->pdf->brief_font,'',8);
	  $this->pdf->ln(12);
$this->pdf->row(array('',"Inschr.Nr. K.v.K.","32123885"));
$this->pdf->row(array('',"Btw-nr.","NL818017338B01"));
$this->pdf->row(array('',"Bankrelatie:","ABN Amro bank"));
$this->pdf->row(array('',"Rek. nr.:","57.89.64.813"));
*/


$this->pdf->addPage("P");

		$this->pdf->SetY(50);
		$this->pdf->SetWidths(array(22,150));
	  $this->pdf->SetAligns(array('R','L'));


    $this->pdf->SetTextColor(0,0,0);

    ?>