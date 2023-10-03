<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/09/27 15:59:35 $
File Versie					: $Revision: 1.19 $

$Log: Factuur_L38.php,v $
Revision 1.19  2017/09/27 15:59:35  rvv
*** empty log message ***

Revision 1.18  2017/03/29 15:58:50  rvv
*** empty log message ***

Revision 1.17  2016/10/29 17:17:02  rvv
*** empty log message ***

Revision 1.16  2016/10/05 16:17:49  rvv
*** empty log message ***

Revision 1.15  2016/09/07 15:43:46  rvv
*** empty log message ***

Revision 1.14  2015/11/08 16:36:59  rvv
*** empty log message ***

Revision 1.13  2014/03/19 16:40:41  rvv
*** empty log message ***

Revision 1.12  2014/02/09 11:01:07  rvv
*** empty log message ***

Revision 1.11  2014/01/09 12:47:06  rvv
*** empty log message ***

Revision 1.10  2013/11/02 17:04:50  rvv
*** empty log message ***

Revision 1.9  2013/04/10 10:12:39  rvv
*** empty log message ***

Revision 1.8  2013/04/03 14:57:26  rvv
*** empty log message ***

Revision 1.7  2012/08/08 15:40:13  rvv
*** empty log message ***

Revision 1.6  2012/06/09 13:44:46  rvv
*** empty log message ***

Revision 1.5  2012/06/03 09:55:37  rvv
*** empty log message ***

Revision 1.4  2012/05/30 16:03:02  rvv
*** empty log message ***

Revision 1.3  2012/05/27 08:32:21  rvv
*** empty log message ***

Revision 1.2  2012/05/23 17:40:22  rvv
*** empty log message ***

Revision 1.1  2012/05/23 15:57:43  rvv
*** empty log message ***

Revision 1.12  2012/04/12 14:57:57  rvv
*** empty log message ***


*/

global $__appvar;

    $this->pdf->rowHeight = 5;
   	$this->pdf->underlinePercentage=0.8;
    $this->pdf->brief_font='Arial';
    $this->pdf->brief_font='Times';
    $this->pdf->SetFont($this->pdf->brief_font,'',8);
		$this->pdf->rapport_type = "FACTUUR";
/*
		if (count($this->pdf->pages) % 2 && ($this->pdf->selectData['type'] != 'eMail'))
		{
  		$this->pdf->AddPage('L');
      $this->pdf->row(array('pagina:',$this->pdf->page));
      logscherm("pagina factuur : ".$this->pdf->page)." toegevoegd.";
		  $this->pdf->emailSkipPages[]=$this->pdf->page;

		}
*/

		$this->pdf->AddPage('P');
    $this->pdf->oddPageReportStart[$this->portefeuille][$this->pdf->rapport_type]=$this->pdf->page;
		//$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$vanaf=db2jul($this->waarden['datumVan']);
		$tot=db2jul($this->waarden['datumTot']);

		if(file_exists($__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Remisier'].".png"))
		  $logo = $__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Remisier'].".png";
		else
		  $logo = $__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Logo'];


	 // $logo=$__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Logo'];
		if(is_file($logo))
		{
      $factor=0.08;
		  $xSize=1500*$factor; 
		  $ySize=372*$factor;
	    $this->pdf->Image($logo, 45, 10, $xSize, $ySize);
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
CRM_naw.enOfRekening,
Portefeuilles.BetalingsinfoMee
FROM CRM_naw Join Portefeuilles on CRM_naw.Portefeuille= Portefeuilles.portefeuille WHERE CRM_naw.Portefeuille = '".$this->portefeuille."'  ";

	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();
	  		$this->pdf->SetWidths(array(25-$this->pdf->marge,140));

	  $this->pdf->SetAligns(array('R','L','L','R','R'));
    $this->pdf->rowHeight = 4;
	  $this->pdf->SetY(72);
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


$this->pdf->SetY(110);
		$this->pdf->SetWidths(array(25-$this->pdf->marge,140));
$this->pdf->row(array('','Bussum, '.(date("j"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y")));
$this->pdf->ln(1);
$this->pdf->SetWidths(array(25-$this->pdf->marge,40,70));
$facturnr=date("Y")." - ".$this->waarden['factuurNummer'];
$this->pdf->row(array('',"Factuurnummer:",$facturnr));
$this->pdf->ln(1);
$this->pdf->row(array('',"Portefeuillenummer:",$this->waarden['portefeuille']));
$this->pdf->ln(10);
$this->pdf->line(25,$this->pdf->getY()+2,185,$this->pdf->getY()+2);
$this->pdf->ln(10);

//$this->pdf->SetFont($this->pdf->brief_font,'B',11);
$this->pdf->SetWidths(array(25-$this->pdf->marge,120,10,30));
$this->pdf->SetAligns(array('L','L','C','R'));
$kwartalen=array('1'=>'eerste','2'=>'tweede','3'=>'derde','4'=>'vierde');
$this->pdf->row(array('',$this->waarden['SoortOvereenkomst']."vergoeding over het ".$kwartalen[$this->waarden['kwartaal']]." kwartaal ".date("Y",db2jul($this->waarden['datumTot']))));
$this->pdf->ln();

$bijlage=false;
if($this->waarden['BeheerfeeBasisberekening']=='5')  // basisRekenvermogen
{
  $this->pdf->row(array('',"Gemiddelde vermogen ".$kwartalen[$this->waarden['kwartaal']]." kwartaal",'€',$this->formatGetal($this->waarden['basisRekenvermogen'],2)));
  $this->pdf->ln();
  $this->pdf->row(array('',"Zie bijlage voor berekening gemiddeld vermogen."));
  $this->pdf->ln();
  $bijlage=true;
}
else
{
  $this->pdf->row(array('',$__appvar["BeheerfeeBasisberekening"][$this->waarden['BeheerfeeBasisberekening']]." ".$kwartalen[$this->waarden['kwartaal']]." kwartaal",'€',$this->formatGetal($this->waarden['basisRekenvermogen'],2)));
  $this->pdf->ln();
}

if($this->waarden['BeheerfeeMethode'] == 1 || $this->waarden['BeheerfeeMethode'] == 2)
{
  $this->pdf->row(array('',$this->waarden['SoortOvereenkomst']."vergoeding conform staffel",'€',$this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));
}
else
{
  $this->pdf->row(array('',$this->waarden['SoortOvereenkomst']."vergoeding ".$this->waarden['BeheerfeePercentageVermogen']."% per jaar",'€',$this->formatGetal($this->waarden['beheerfeePerPeriode']-$this->waarden['administratieBedrag'],2)));
}
$this->pdf->ln();



if($this->waarden['administratieBedrag'] <> 0)
{
  $this->pdf->row(array('',"Administratie-vergoeding",'€',$this->formatGetal($this->waarden['administratieBedrag'],2)));
  $this->pdf->ln();
}


$this->pdf->CellBorders = array('','','','U');

$tmpBtw=explode('.',$this->waarden['btwTarief']);
$round=1;
if(count($tmpBtw)==2)
{
  if($tmpBtw[1]==0)
    $round=0;
  else
    $round=1;
}



$this->pdf->row(array('',$this->formatGetal($this->waarden['btwTarief'],$round)." % BTW",'€',$this->formatGetal($this->waarden['btw'],2)));
$this->pdf->CellBorders = array();
$this->pdf->ln();
$this->pdf->row(array('',"Totaal te betalen","€",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
$this->pdf->ln(10);
$this->pdf->line(25,$this->pdf->getY()+2,185,$this->pdf->getY()+2);


if($this->waarden['btwTarief']==0)
{
  $this->pdf->ln(8);
  $this->pdf->row(array('',"Deze dienst is vrijgesteld van BTW."));
  $this->pdf->ln(4);
}
else
  $this->pdf->ln(20);
$extraDagen=time()+(3*24*3600);
$this->pdf->SetWidths(array(25-$this->pdf->marge,160));

if($crmData['BetalingsinfoMee']==1)
{
//  $this->pdf->row(array('',"Wij verzoeken u het totaalbedrag binnen 14 dagen over te maken op rekeningnummer 21.17.22.448 ten name van Steentjes Vermogensbeheer B.V. te Bussum"));
  $this->pdf->row(array('',"Wij verzoeken u vriendelijk het totaalbedrag over te maken op het rekeningnummer\nNL26ABNA0516690671 ten name van Steentjes Vermogensbeheer BV te Bussum"));
  $this->pdf->ln(32);
}
else
{
  $this->pdf->row(array('',"Bovenvermeld bedrag wordt automatisch van uw rekening geïncasseerd."));
  $this->pdf->ln(40);
}
$this->pdf->row(array('',"BTW-nummer is 8109.30.134.B.01"));

if($this->pdf->portefeuilledata['Remisier']=='Tostrams')
{
  $this->pdf->rowHeight = 3.6;
  $trigger=$this->pdf->PageBreakTrigger;
  $this->pdf->PageBreakTrigger=$this->pdf->PageBreakTrigger+10;
  $this->pdf->setY(273);
  $this->pdf->SetAligns(array('R','C'));
  $this->pdf->SetFont($this->pdf->brief_font,'I',8);
  $this->pdf->SetWidths(array(0,195));
  $this->pdf->row(array('',"\n\nTostrams Vermogensbeheer is een handelsnaam van Steentjes Vermogensbeheer\nPostbus 1359, 1400 BJ Bussum\nvermogensbeheer@tostrams.nl "));
  $this->pdf->SetFont($this->pdf->brief_font,'BI',8);
  $this->pdf->PageBreakTrigger=$trigger;
}
else
{
  //$this->pdf->Rect(20,280,4,12,'F','F',array(140,94,44));
  //$this->pdf->Rect(185,280,4,12,'F','F',array(140,94,44));
  $this->pdf->rowHeight = 3.6;
  $trigger=$this->pdf->PageBreakTrigger;
  $this->pdf->PageBreakTrigger=$this->pdf->PageBreakTrigger+10;
  $this->pdf->setY(273);
  $this->pdf->SetAligns(array('R','C'));
  $this->pdf->SetFont($this->pdf->brief_font,'',8);
  $this->pdf->SetWidths(array(0,195));
  $this->pdf->row(array('',"Vergunninghouder AFM\nZwarteweg 10, Postbus 1359, 1400 BJ Gooise Meren\n tel: 035-692 17 00\nABN AMRO.IBAN NL26 ABNA 0516 690 671, K.v.K. 20078649"));
  $this->pdf->SetFont($this->pdf->brief_font,'B',8);
  $this->pdf->row(array('',"www.steentjes-vermogensbeheer.nl"));
  
  $this->pdf->PageBreakTrigger=$trigger;
}

if($bijlage==true)
{



  $this->pdf->addPage('P');
  $this->pdf->SetFont($this->pdf->brief_font,'',11);
  $this->pdf->SetWidths(array(25-$this->pdf->marge,120,10,30));
$this->pdf->SetAligns(array('L','L','C','R'));
  $this->pdf->ln(40);
  $this->pdf->row(array('',"Bijlage factuur $facturnr"));
  $this->pdf->SetWidths(array(25-$this->pdf->marge,50,10,30,10));
  $this->pdf->SetAligns(array('R','L','R','R','L'));
  $this->pdf->ln(40);
$somWaarden=0;
$noemer=0;
    for($x=1;$x<5;$x++)
    {
       $this->pdf->row(array('',"".$__appvar["Maanden"][date("n",$this->waarden['maandsData_'.$x])],"€",$this->formatGetal($this->waarden['maandsWaarde_'.$x],2)));
       $somWaarden+=$this->waarden['maandsWaarde_'.$x];
       if(round($this->waarden['maandsWaarde_'.$x],2) != 0.00)
         $noemer+=1;
    }

$this->pdf->SetWidths(array(25-$this->pdf->marge,50,40));
$this->pdf->row(array('','',"-------------------------"));

  $this->pdf->SetWidths(array(25-$this->pdf->marge,50,10,30,10,10,15));
  $this->pdf->SetAligns(array('R','L','R','R','C','C','C','C'));
$this->pdf->row(array('',"","€",$this->formatGetal($somWaarden,2)," : ",$noemer," is ","€ ".$this->formatGetal($somWaarden/$noemer,2)));

}
else
{
  $this->pdf->addPage('P');
  $this->pdf->emailSkipPages[]=$this->pdf->page;
}
 $this->pdf->SetTextColor(0,0,0);
 $this->pdf->geenBasisFooter=true;




    ?>
