<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/02/09 10:12:26 $
File Versie					: $Revision: 1.8 $

$Log: Factuur_L48.php,v $
Revision 1.8  2020/02/09 10:12:26  rvv
*** empty log message ***

Revision 1.7  2020/01/11 14:36:57  rvv
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


    if(file_exists(FPDF_FONTPATH.'helvetica.php'))
    {
  	    if(!isset($this->pdf->fonts['helvetica']))
	      {
		      $this->pdf->AddFont('helvetica','','helvetica.php');
          $this->pdf->AddFont('helvetica','I','helveticai.php');
		      $this->pdf->AddFont('helvetica','B','helveticab.php');
		      $this->pdf->AddFont('helvetica','BI','helveticabi.php');
	      }
        $font='helvetica';
	  }
    $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight=5;

	
		$this->pdf->AddPage('P');
    
    if(is_file($this->pdf->rapport_logo))
		{
	   $factor=0.055;
	   $xSize=1000*$factor;//$x=885*$factor;
	   $ySize=620*$factor;//$y=849*$factor;
     $xStart=(210)/2-($xSize/2);
     $this->pdf->Image($this->pdf->rapport_logo, $xStart, 25, $xSize, $ySize);
		}

		$this->pdf->SetWidths(array(210-2*$this->pdf->marge));
		$this->pdf->SetAligns(array("C"));
    $this->pdf->SetY(85);
    $this->pdf->Ln();
	$this->pdf->SetFont($font,"B",11);
$this->pdf->row(array('FACTUUR'));
	$this->pdf->SetFont($font,"",11);

   $this->DB = new DB();
   $query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.adres,
CRM_naw.pc,
CRM_naw.plaats,
CRM_naw.land,
CRM_naw.btwnr,
Portefeuilles.BetalingsinfoMee
FROM CRM_naw Join Portefeuilles on CRM_naw.Portefeuille= Portefeuilles.portefeuille WHERE CRM_naw.Portefeuille = '".$this->portefeuille."'  ";
	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();

   /*
   $crmData['naam']='naam naam naam naam naam naam naam naam naam';
   $crmData['naam1']='naam1';
   $crmData['adres']='adres';
   $crmData['pc']='pc';
   $crmData['land']='land';
   */

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

      $query = "SELECT accountmanager,naam
FROM Accountmanagers WHERE accountmanager = '".$this->waarden['Accountmanager']."'  ";
	  $this->DB->SQL($query); //echo $query;
	  $accountmanager = $this->DB->lookupRecord();


      // listarray($accountmanager);
		$this->pdf->SetY(100);
		$this->pdf->SetWidths(array(20,100+180));
		$this->pdf->SetAligns(array("L","L","L"));
		$this->pdf->row(array('',$crmData['naam']));
   // $this->pdf->SetFont($font,"",11);
		if ($crmData['naam1'] !='')
		  $this->pdf->row(array('',$crmData['naam1']));
		if($crmData['adres']<>'')
		  $this->pdf->row(array('',$crmData['adres']));
		$plaats='';
    $plaats=$crmData['pc'];
    if($crmData['plaats'] != '') $plaats.=" ".$crmData['plaats'];
    if(trim($plaats) <> '')
		  $this->pdf->row(array('',$plaats));
    if($crmData['land']<>'')
      $this->pdf->row(array('',$crmData['land']));
    if($this->waarden['btwTarief']==0 && $this->waarden['afwijkendeOmzetsoort']<>'' && $crmData['btwnr']<>'')
		{
    	$this->pdf->ln(1);
      $this->pdf->row(array('', $crmData['btwnr']));
    }
$this->pdf->SetY(135);
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
 
    $factuurKwartaalJul=db2jul($this->waarden['datumTot'])+(3600*25);
    $factuurKwartaal=ceil(date("n",$factuurKwartaalJul)/3);
    
    $factuurnr=$factuurKwartaal."e kwartaal IC ".date("Y",$factuurKwartaalJul)."-".sprintf("%04d",$this->waarden['factuurNummer']);

    $this->pdf->SetWidths(array(20,60,10,100));
    $this->pdf->SetAligns(array("L","L",'R','L'));
    $this->pdf->row(array('',"Datum",":",$nu));
    $this->pdf->ln();
    //$this->pdf->SetWidths(array(20,60,10,60));
    //$this->pdf->SetAligns(array("L","L",'R','L'));
    $this->pdf->row(array('',"Factuurnummer",":",$factuurnr));
    $this->pdf->ln();
    $this->pdf->ln();
$this->pdf->SetAligns(array("L","L",'R','R'));
  
$this->pdf->SetWidths(array(20,150));
$this->pdf->row(array('','Geachte relatie,

Onderstaand bedrag is c.q. wordt een dezer dagen automatisch geïncasseerd van uw rekening. Het te incasseren bedrag is als volgt opgebouwd:
'));  

$this->pdf->SetWidths(array(20,70,10,20));  
$this->pdf->ln();
$ystart=$this->pdf->GetY();

if($this->waarden['portefeuilledata']['afrekenvalutaKosten']=='USD')
{
  $valutaTeken='$';
}
else
{
  $valutaTeken='€';
}


$this->pdf->row(array('',"fee Index Capital",'',$this->formatGetal($this->waarden['beheerfeeBetalen']-$this->waarden['administratieBedrag'],2)));
$this->pdf->row(array('',"bijdrage in toezichtskosten AFM / DNB",'',$this->formatGetal($this->waarden['administratieBedrag'],2)));
$this->pdf->ln(2);
$this->pdf->CellBorders = array('','','','T');
$this->pdf->row(array('',"",'',$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));
unset($this->pdf->CellBorders);
if($this->waarden['btwTarief']==0 && $this->waarden['afwijkendeOmzetsoort']<>'')
{
  $this->pdf->row(array('', "BTW Verlegd", '', $this->formatGetal($this->waarden['btw'], 2)));
}
else
{
  $this->pdf->row(array('', "btw " . $this->formatGetal($this->waarden['btwTarief'], 0) . "%", '', $this->formatGetal($this->waarden['btw'], 2)));
}
$this->pdf->ln(2);
$this->pdf->CellBorders = array('','','','T');
$this->pdf->row(array('','totaal','',$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
unset($this->pdf->CellBorders);
$this->pdf->SetY($ystart);
$this->pdf->SetFont('times',"",11);
$this->pdf->row(array('','',$valutaTeken));
$this->pdf->row(array('','',$valutaTeken));
$this->pdf->ln(2);
$this->pdf->row(array('','',$valutaTeken));
$this->pdf->row(array('','',$valutaTeken));
$this->pdf->ln(2);
$this->pdf->row(array('','',$valutaTeken));
$this->pdf->SetFont($font,"",11);
$this->pdf->ln();
$this->pdf->SetWidths(array(20,150));
$this->pdf->row(array('','Mocht u nog vragen hebben, aarzelt u dan niet om contact met mij op te nemen; ik ben u graag van dienst.'));
$this->pdf->ln();


$this->pdf->row(array('','Met vriendelijke groet,

'.$accountmanager['naam'].''));

//
$this->pdf->SetTextColor(191,191,191);
		$this->pdf->SetWidths(array(210-2*$this->pdf->marge));
		$this->pdf->SetAligns(array("C"));
    $this->pdf->SetY(260);
    $this->pdf->Ln();
	$this->pdf->SetFont($font,"",8);
$this->pdf->row(array($vermRecord['Adres'].' - '.$vermRecord['Woonplaats'].' - '.$vermRecord['Telefoon'].' - '.$vermRecord['Email'].' - '.$vermRecord['website'].'
KvK 53956141 – BTW NL851377816B01 – Rabobank 16.88.58.754 – IBAN NL09RABO0168858754 – BIC RABONL2U'));

$this->pdf->SetTextColor(0,0,0);
$this->pdf->rowHeight=$rowHeightBackup;
?>
