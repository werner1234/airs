<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/07/07 12:24:59 $
File Versie					: $Revision: 1.9 $

$Log: Factuur_L40.php,v $
Revision 1.9  2019/07/07 12:24:59  rvv
*** empty log message ***

Revision 1.8  2015/04/08 15:46:15  rvv
*** empty log message ***

Revision 1.7  2015/04/05 14:18:26  rvv
*** empty log message ***

Revision 1.6  2015/04/04 15:16:43  rvv
*** empty log message ***

Revision 1.5  2013/04/27 16:28:55  rvv
*** empty log message ***

Revision 1.4  2013/03/13 17:01:47  rvv
*** empty log message ***

Revision 1.3  2013/03/09 16:26:30  rvv
*** empty log message ***

Revision 1.2  2012/10/02 16:17:58  rvv
*** empty log message ***

Revision 1.1  2012/09/30 11:17:18  rvv
*** empty log message ***


*/


//listarray($this->waarden);



    $this->pdf->marge = 30;
    $this->pdf->rowHeight=4;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Arial","",11);
    $this->pdf->brief_font='Arial';
		$this->pdf->rapport_type = "FACTUUR";


    global $__appvar;
		$this->pdf->AddPage('P');
    
	  $logo = $__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Logo'];

		if(is_file($logo))
		{
		  $factor=0.035;
		  $xSize=1246*$factor;
		  $ySize=540*$factor;
	    $this->pdf->Image($logo, 85, 5, $xSize, $ySize);
		}


$this->DB = new DB();

			  $query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.adres,
CRM_naw.pc,
CRM_naw.plaats,
CRM_naw.land,
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
    $this->pdf->row(array('',$crmData['adres']));
    $plaats=$crmData['pc'];
    if($crmData['plaats'] != '') $plaats.=" ".$crmData['plaats'];
    $this->pdf->row(array('',$plaats));
    $this->pdf->row(array('',$crmData['land']));


$this->pdf->SetY(110);
		$this->pdf->SetWidths(array(25-$this->pdf->marge,140));
		$this->pdf->SetWidths(array(25-$this->pdf->marge,40,70));
$this->pdf->row(array('','Datum:',(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y")));
$this->pdf->ln(1);
$this->pdf->SetWidths(array(25-$this->pdf->marge,40,70));

$facturnr=date("Y")." - ".sprintf("%04d",$this->waarden['factuurNummer']);
$this->pdf->row(array('',"Factuurnummer:",$facturnr));
$this->pdf->ln(1);
$this->pdf->row(array('',"Portefeuillenummer:",$this->waarden['portefeuille']));
$this->pdf->ln(10);
$this->pdf->line(25,$this->pdf->getY()+2,185,$this->pdf->getY()+2);
$this->pdf->ln(10);



if(strpos(strtolower($this->waarden['SoortOvereenkomst']),'advies') !== false)
 $SoortOvereenkomst="Adviesvergoeding";
else
 $SoortOvereenkomst="Beheervergoeding";
//$this->pdf->SetFont($this->pdf->brief_font,'B',11);
$this->pdf->SetWidths(array(25-$this->pdf->marge,120,10,30));
$this->pdf->SetAligns(array('L','L','C','R'));
$kwartalen=array('1'=>'eerste','2'=>'tweede','3'=>'derde','4'=>'vierde');
$this->pdf->row(array('',"$SoortOvereenkomst over het ".$kwartalen[$this->waarden['kwartaal']]." kwartaal ".date("Y",db2jul($this->waarden['datumTot']))));
$this->pdf->ln();


if($this->waarden['BeheerfeeBedragBuitenFee'] <> 0)
{
  $extraRuimte=4;
  $tmpBtw=explode('.',$this->waarden['btwTarief']);
  $round=1;
  if(count($tmpBtw)==2)
  {
    if($tmpBtw[1]==0)
      $round=0;
    else
      $round=1;
  }
  if($this->waarden['BeheerfeeBedragBuitenFeePortefeuille']<>0)
    $uitgesloten=$this->waarden['BeheerfeeBedragBuitenFeePortefeuille'];
  else
    $uitgesloten=$this->waarden['BeheerfeeBedragBuitenFee'];
/*
  $percentage=$this->waarden['beheerfeePerPeriode']/$this->waarden['rekenvermogen'];
  $oudeFee=$this->waarden['totaalWaarde']*$percentage;
  $this->pdf->SetFont($this->pdf->brief_font,'B',11);
  $this->pdf->row(array('',"Zonder uit te sluiten vermogen"));
  $this->pdf->ln($extraRuimte);
  $this->pdf->SetFont($this->pdf->brief_font,'',11);
  $this->pdf->row(array('',$__appvar["BeheerfeeBasisberekening"][$this->waarden['BeheerfeeBasisberekening']]." ".$kwartalen[$this->waarden['kwartaal']]." kwartaal",'€',$this->formatGetal($this->waarden['totaalWaarde'],2)));
  $this->pdf->ln($extraRuimte);
  $this->pdf->row(array('',"Beheervergoeding",'€',$this->formatGetal($oudeFee,2)));
  $this->pdf->ln($extraRuimte);
  $this->pdf->CellBorders = array('','','','U');
  $this->pdf->row(array('',$this->formatGetal($this->waarden['btwTarief'],$round)."% BTW",'€',$this->formatGetal($oudeFee*$this->waarden['btwTarief']/100,2)));
  $this->pdf->ln($extraRuimte);
  $this->pdf->CellBorders = array();
  $this->pdf->row(array('',"Totaal te betalen",'€',$this->formatGetal($oudeFee*(1+$this->waarden['btwTarief']/100),2)));

  $this->pdf->ln();
  $this->pdf->SetFont($this->pdf->brief_font,'B',11);
  $this->pdf->row(array('',"Inclusief"));
  $this->pdf->ln($extraRuimte);
  $this->pdf->SetFont($this->pdf->brief_font,'',11);
*/
  $this->pdf->row(array('',$__appvar["BeheerfeeBasisberekening"][$this->waarden['BeheerfeeBasisberekening']]." ".$kwartalen[$this->waarden['kwartaal']]." kwartaal",'€',$this->formatGetal($this->waarden['basisRekenvermogen']+$uitgesloten,2)));
  $this->pdf->ln($extraRuimte);
  $this->pdf->CellBorders = array('','','','U');
  $this->pdf->row(array('',"Vermogen buiten beheerfee",'€',$this->formatGetal($uitgesloten,2)));
  $this->pdf->ln($extraRuimte);
  $this->pdf->CellBorders = array();
  $this->pdf->row(array('',"Vermogen waarover beheerfee wordt berekend",'€',$this->formatGetal($this->waarden['rekenvermogenFee'],2)));
  $this->pdf->ln();
  $this->pdf->row(array('',"$SoortOvereenkomst",'€',$this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));
  $this->pdf->ln();
  $this->pdf->CellBorders = array('','','','U');
  $this->pdf->row(array('',$this->formatGetal($this->waarden['btwTarief'],$round)."% BTW",'€',$this->formatGetal($this->waarden['btw'],2)));
  $this->pdf->CellBorders = array();

  $this->pdf->ln($extraRuimte);
  $this->pdf->row(array('',"Totaal te betalen","€",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
  $this->pdf->ln(10);
}
else
{
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
  $this->pdf->row(array('',"$SoortOvereenkomst",'€',$this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));
}
elseif($this->waarden['BeheerfeeMethode'] > 3)
{
  $this->pdf->row(array('',"$SoortOvereenkomst ".$this->waarden['BeheerfeePercentageVermogen']."% per jaar",'€',$this->formatGetal($this->waarden['beheerfeePerPeriode']-$this->waarden['administratieBedrag'],2)));
}
else
{
  $this->pdf->row(array('',"$SoortOvereenkomst",'€',$this->formatGetal($this->waarden['beheerfeePerPeriode']-$this->waarden['administratieBedrag'],2)));
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

$this->pdf->row(array('',$this->formatGetal($this->waarden['btwTarief'],$round)."% BTW",'€',$this->formatGetal($this->waarden['btw'],2)));
$this->pdf->CellBorders = array();
$this->pdf->ln();
$this->pdf->row(array('',"Totaal te betalen","€",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
$this->pdf->ln(10);
$this->pdf->line(25,$this->pdf->getY()+2,185,$this->pdf->getY()+2);
}

$this->pdf->ln(20);
$this->pdf->SetWidths(array(25-$this->pdf->marge,140));
$this->pdf->row(array('',"Dit bedrag wordt binnen enkele dagen van uw rekening geïncasseerd."));
listarray($this->waarden);
  
       $this->pdf->AutoPageBreak=false;
    $this->pdf->SetY(-18);
     $this->pdf->SetTextColor($this->pdf->rapport_kop_bgcolor[r],$this->pdf->rapport_kop_bgcolor[g],$this->pdf->rapport_kop_bgcolor[b]);
    // $this->pdf->SetTextColor(0,88,0);
    $this->pdf->SetFont($this->pdf->brief_font,'',8);
    $this->pdf->SetX(0);
    
    $this->pdf->MultiCell(210,4,"Hazenweg 110 – Postbus 125 – 7550 AC Hengelo – T (074) 248 00 48 
info@groenstate.nl - www.groenstate.nl
IBAN NL42ABNA 054.1674.382 – BIC ABNANL2A – K.v.K. Twente & Salland 06090851 – BTW nr. 807885113B01",0,'C');
/*
    $this->pdf->MultiCell(210,4,
"Hazenweg 110 - Postbus 125 - 7550 AC Hengelo - T (074) 248 00 48 - F (074) 248 00 49 - info@groenstate.nl - www.groenstate.nl
ABN AMRO 54.16.74.382 - IBAN NL42ABNA0541674382 - BTW nr. 807885113B01 - K.v.K. Twente & Salland 06090851",0,'C');
*/
    $this->pdf->AutoPageBreak=true;
    $this->pdf->SetTextColor(0,0,0);



?>