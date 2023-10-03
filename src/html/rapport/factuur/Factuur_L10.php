<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/21 16:33:35 $
File Versie					: $Revision: 1.10 $

$Log: Factuur_L10.php,v $
Revision 1.10  2020/03/21 16:33:35  rvv
*** empty log message ***

Revision 1.9  2019/10/19 16:47:57  rvv
*** empty log message ***

Revision 1.8  2019/09/25 15:32:13  rvv
*** empty log message ***

Revision 1.7  2019/07/05 16:39:37  rvv
*** empty log message ***

Revision 1.6  2019/06/26 15:10:43  rvv
*** empty log message ***

Revision 1.5  2019/06/10 11:02:44  rvv
*** empty log message ***

Revision 1.4  2019/06/02 10:03:00  rvv
*** empty log message ***

Revision 1.3  2019/05/18 16:28:09  rvv
*** empty log message ***

Revision 1.2  2019/01/19 13:55:07  rvv
*** empty log message ***

Revision 1.1  2019/01/16 16:27:39  rvv
*** empty log message ***

Revision 1.6  2018/10/10 16:11:45  rvv
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


if(!isset($this->pdf->fonts['opensans']) && file_exists(FPDF_FONTPATH.'OpenSans-Regular.php1'))
{
  $this->pdf->AddFont('opensans','','OpenSans-Regular.php');
  $this->pdf->AddFont('opensans','B','OpenSans-Bold.php');
  $this->pdf->AddFont('opensans','I','OpenSans-Italic.php');
  $this->pdf->AddFont('opensans','BI','OpenSans-BoldItalic.php');
  $font= 'opensans';
}
else
{
  $font= $this->pdf->rapport_font ;
}

   $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight=5;
  $this->pdf->SetFont($font,"",11);
	
		$this->pdf->AddPage('P');
    
    if(is_file($this->pdf->rapport_logo))
		{
      $factor=0.035;
      $xSize=1228*$factor;
      $ySize=899*$factor;
      
      //$logopos=(210-$xSize-($this->pdf->marge+6)*2);
      $logopos=210/2-$xSize/2;
      $this->pdf->Image($this->pdf->rapport_logo, $logopos, 10, $xSize, $ySize);
      //$this->pdf->line($this->pdf->marge+20,30,210-20-$this->pdf->marge,30);
		}

$db=new DB();
$query="SELECT
Vermogensbeheerders.Naam,
Vermogensbeheerders.Adres,
Vermogensbeheerders.Woonplaats,
Vermogensbeheerders.Telefoon,
Vermogensbeheerders.Fax,
Vermogensbeheerders.Email,
Vermogensbeheerders.rekening,
Vermogensbeheerders.bank,
Vermogensbeheerders.website
FROM
Vermogensbeheerders
WHERE Vermogensbeheerders.Vermogensbeheerder='".$this->waarden['Vermogensbeheerder']."'";
$db->SQL($query);
$db->query();
$vermRecord=$db->nextRecord();

//$this->pdf->SetY(8);
//$this->pdf->SetWidths(array(20,180));
//$this->pdf->SetAligns(array("L","L"));
//$this->pdf->row(array('',$vermRecord['Naam']."\n".$vermRecord['Adres']."\n".$vermRecord['Woonplaats']." "));//.' -  - '.$vermRecord['Email'].' - '.$vermRecord['website']

//$this->pdf->ln(12);
$this->pdf->SetY(50);
$this->pdf->SetAligns(array("L","R"));
$this->pdf->SetWidths(array(20,155));
$this->pdf->SetFont($font,"",20);
$this->pdf->row(array('','Factuur'));
// .

$this->pdf->SetFont($font,"",11);


   $this->DB = new DB();
   $query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.adres,
CRM_naw.pc,
CRM_naw.plaats,
CRM_naw.land,
CRM_naw.land,
CRM_naw.verzendAanhef,
Portefeuilles.BetalingsinfoMee
FROM CRM_naw Join Portefeuilles on CRM_naw.Portefeuille= Portefeuilles.portefeuille WHERE CRM_naw.Portefeuille = '".$this->portefeuille."'  ";
	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();


      $query = "SELECT accountmanager,naam
FROM Accountmanagers WHERE accountmanager = '".$this->waarden['Accountmanager']."'  ";
	  $this->DB->SQL($query); //echo $query;
	  $accountmanager = $this->DB->lookupRecord(); 
      // listarray($accountmanager);
		$this->pdf->SetY(55);
		$this->pdf->SetWidths(array(20,100+180));
		$this->pdf->SetAligns(array("L","L","L"));
		$this->pdf->row(array('',$crmData['naam']));
   // $this->pdf->SetFont($font,"",11);
		if ($crmData['naam1'] !='')
		  $this->pdf->row(array('',$crmData['naam1']));
		$this->pdf->row(array('',$crmData['adres']));
		$plaats='';
    $plaats=$crmData['pc'];
    if($crmData['plaats'] != '') $plaats.=" ".$crmData['plaats'];
		$this->pdf->row(array('',$plaats));
    $this->pdf->row(array('',$crmData['land']));
		$this->pdf->SetY(100);
    $this->pdf->SetWidths(array(20,100,80));

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

$kwartaal = ceil(date("n",db2jul($this->waarden['datumTot']))/3);

    $this->pdf->SetAligns(array("L","R"));
    $this->pdf->SetWidths(array(20,155));
    $this->pdf->row(array('',$nu));
    $this->pdf->ln();
    $this->pdf->SetWidths(array(20,155));
    $this->pdf->SetAligns(array("L","L",'R','R'));  
    $this->pdf->row(array('',"Betreft: factuurnummer ".substr($this->waarden['datumTot'],0,4)."-Q".$kwartaal."-".$this->factuurnummer));
    $this->pdf->ln();
    $this->pdf->ln();


$kwartalen[1] = 'eerste';
$kwartalen[2] = 'tweede';
$kwartalen[3] = 'derde';
$kwartalen[4] = 'vierde';
  
$this->pdf->SetWidths(array(20,155));
$this->pdf->row(array('',$crmData['verzendAanhef'].',

Hierbij ontvangt u de uiteenzetting van onze kosten inzake het beheer van de portefeuille over het '.$kwartalen[$this->waarden['kwartaal']].' kwartaal '.date("Y",db2jul($this->waarden['datumTot'])).
	'. Het vermogen hiervoor bedraagt € '.$this->formatGetal($this->waarden['totaalWaarde'],2).'.'));

//listarray($this->waarden);
$this->pdf->SetWidths(array(20,90,10,20));
$this->pdf->ln();
$ystart=$this->pdf->GetY();

$this->pdf->row(array('',"Beheerfee",'€',$this->formatGetal($this->waarden['beheerfeeBetalen']-$this->waarden['administratieBedrag'],2)));
$this->pdf->row(array('',"Kosten toezicht AFM / DNB",'€',$this->formatGetal($this->waarden['administratieBedrag'],2)));
$this->pdf->ln(2);
$this->pdf->CellBorders = array('','','','T');
$this->pdf->row(array('',"Subtotaal exclusief BTW",'€',$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));
unset($this->pdf->CellBorders);
$this->pdf->row(array('',"BTW ".$this->formatGetal($this->waarden['btwTarief'],0)."%:",'€',$this->formatGetal($this->waarden['btw'],2)));
$this->pdf->ln(2);
$this->pdf->CellBorders = array('','','',array('T','UU'));
$this->pdf->row(array('','Totaal inclusief BTW:','€',$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
unset($this->pdf->CellBorders);

$this->pdf->SetFont($font,"",11);
$this->pdf->ln();
$this->pdf->SetWidths(array(20,150));
$this->pdf->row(array('','Wij zullen dit bedrag binnenkort van uw rekening afschrijven.

Indien u nog vragen heeft, kunt u uiteraard contact met ons opnemen.'));
$this->pdf->ln();


$this->pdf->row(array('','Met vriendelijke groet,



Stan J.P. Westerterp, CFA
Managing Partner

Bond Capital Partners B.V.
'));

//
$this->pdf->AutoPageBreak=false;
$this->pdf->rowHeight=$rowHeightBackup;

		$this->pdf->SetWidths(array(210-2*$this->pdf->marge));
		$this->pdf->SetAligns(array("C"));
    $this->pdf->SetY(270);
    $this->pdf->Ln();
    $size=7.5;
	$this->pdf->SetFont($font,"",$size);
  $lineTxt=array();
	$lineTxt[]=array('Bond Capital Partners B.V. '=>'B',
                   'Ginnekenweg 159, 4818 JD Breda '=>'',
                   'T'=>'B',
                   '+31 (0)76 2 00 35 96 '=>'',
                   'E '=>'B',
                   'info@bondcapital.nl '=>'',
                   'W '=>'B',
                   'www.bondcapital.nl'=>'');
  $lineTxt[]=array('KvK nr. '=>'B',
                 '27270229 '=>'',
                 'BTW nr. '=>'B',
                 'NL8136.548.16.B01  '=>'',
                 'ABN AMRO BANK IBAN '=>'B',
                 'IBAN NL52ABNA0564395773 '=>'',
                 'BIC '=>'B',
                 'ABNANL2A'=>'');
  $lineTxt[]=array('BINCK BANK '=>'B',
                 'IBAN NL04BINK0802667775 '=>'',
                 'BIC '=>'B',
                 'BINKNL21'=>'');
	foreach($lineTxt as $line)
  {
    $totalString=implode('',array_keys($line));
    $totalWidth=0;
    foreach($line as $txt=>$style)
    {
      $fontkey = $font . $style;
      $this->pdf->CurrentFont =& $this->pdf->fonts[$fontkey];
      $totalWidth+=$this->pdf->GetStringWidth($txt);
    }
    $eersteX=(210-$totalWidth)/2;
    $this->pdf->setX($eersteX);
    $xPos=$eersteX;
    foreach($line as $txt=>$style)
    {
      $this->pdf->SetFont($font,$style,$size);
      $w=$this->pdf->GetStringWidth($txt);
      $this->pdf->cell($w,$this->pdf->rowHeight,$txt,0,0,'L');
      $xPos+=$w;
    }
    $this->pdf->ln();
  }

  /*
$this->pdf->row(array('Bond Capital Partners Vermogensbeheer, Ginnekenweg 159, 4818 JD Breda T+31 (0)76 2 00 35 96 E info@bondcapital.nl W www.bondcapital.nl
KvK nr. 27270229 BTW nr. NL8136.548.16.B01 ABN AMRO BANK IBAN NL52ABNA0564395773 BIC ABNANL2A
BINKBANK IBAN NL04BINK0802667775 BIC BINKNL21'));
  */
$this->pdf->AutoPageBreak=true;


$this->pdf->SetTextColor(0,0,0);

?>
