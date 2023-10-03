<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2015/11/08 16:36:59 $
File Versie					: $Revision: 1.12 $

$Log: Factuur_L39.php,v $
Revision 1.12  2015/11/08 16:36:59  rvv
*** empty log message ***

Revision 1.11  2013/07/10 16:02:14  rvv
*** empty log message ***

Revision 1.10  2013/06/09 18:04:23  rvv
*** empty log message ***

Revision 1.9  2013/04/17 11:38:31  rvv
*** empty log message ***

Revision 1.8  2012/10/23 10:06:05  cvs
*** empty log message ***

Revision 1.7  2012/10/21 12:44:42  rvv
*** empty log message ***

Revision 1.6  2012/10/18 15:17:38  rvv
*** empty log message ***

Revision 1.5  2012/10/17 13:37:56  rvv
*** empty log message ***

Revision 1.4  2012/10/17 09:47:13  rvv
*** empty log message ***

Revision 1.3  2012/10/07 14:58:00  rvv
*** empty log message ***

Revision 1.2  2012/10/02 16:17:58  rvv
*** empty log message ***

Revision 1.1  2012/09/30 11:17:18  rvv
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

$oldRowHeight=$this->pdf->rowHeight;
    $this->pdf->rowHeight = 5;
   	$this->pdf->underlinePercentage=0.8;
    $this->pdf->brief_font='Arial';
    $this->pdf->brief_font='Times';
    $this->pdf->SetFont($this->pdf->brief_font,'',8);
		$this->pdf->rapport_type = "FACTUUR";
		if (count($this->pdf->pages) % 2 && ($this->pdf->selectData['type'] != 'eMail'))
		{
  		$this->pdf->AddPage('L');
      $this->pdf->emailSkipPages[]=$this->pdf->page;
		}
		$this->pdf->AddPage('P');
		//$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$vanaf=db2jul($this->waarden['datumVan']);
		$tot=db2jul($this->waarden['datumTot']);

		if(file_exists($__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Remisier'].".png"))
		  $logo = $__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Remisier'].".png";
		else
		  $logo = $__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Logo'];


    $now=time();
    $nowTien=$now+(3600*24*10);
    $vanDatum=date("j",db2jul($this->waarden['datumVan']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumVan']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumVan']));
    $totDatum=date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot']));
    $huidigeDatum=date("j")." ".vertaalTekst($this->__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y");
    $incassoDatum=date("j",$nowTien)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$nowTien)],$this->pdf->rapport_taal)." ".date("Y",$nowTien);

	 // $logo=$__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Logo'];
		if(is_file($logo))
    {
      global $__appvar;
      if ($__appvar['bedrijf'] == 'TEST')
      {
        $xSize = 60;
        $this->pdf->Image($logo, 10, 5, $xSize);
      }
      else
      {
        $factor = 0.1;
        $xSize = 833 * $factor;
        $this->pdf->Image($logo, 10, 5, $xSize);
      }
    }

//listarray($this->waarden);
//$this->DB = new DB();
//$this->DB->SQL($query);
//	  $crmData = $this->DB->lookupRecord();
	  		$this->pdf->SetWidths(array(15,120,30));

	  $this->pdf->SetAligns(array('R','L','L','L','R'));
    $this->pdf->rowHeight = 5;
	  $this->pdf->SetY(40);
	  $this->pdf->SetFont($this->pdf->brief_font,'B',12);
	  $this->pdf->row(array('',"PERSOONLIJK EN VERTROUWELIJK"));
    $this->pdf->Ln();
	  $this->pdf->SetFont($this->pdf->brief_font,'',10);
		$this->pdf->row(array('',$this->waarden['CRM_naam']));
    if (trim($this->waarden['CRM_naam1']) <> "")  $this->pdf->row(array('',$this->waarden['CRM_naam1']));
    $this->pdf->row(array('',$this->waarden['CRM_verzendAdres']));
    $plaats=$this->waarden['CRM_verzendPc'];
    if($this->waarden['CRM_verzendPlaats'] != '') $plaats.=" ".$this->waarden['CRM_verzendPlaats'];
    $this->pdf->row(array('',$plaats));
    $this->pdf->row(array('',$this->waarden['CRM_verzendLand']));
    $this->pdf->Ln();

$this->pdf->line(15+$this->pdf->marge,$this->pdf->getY(),185,$this->pdf->getY());

$this->pdf->SetY(85);
$this->pdf->SetFont($this->pdf->brief_font,'B',12);
$this->pdf->row(array('',"Kwartaalnota vermogens".strtolower($this->waarden['SoortOvereenkomst'])));

$this->pdf->Ln(3);
$this->pdf->SetFont($this->pdf->brief_font,'',10);
$facturnr=date("Y")." - ".sprintf("%04d",$this->waarden['factuurNummer']);
$this->pdf->row(array('',"Factuurnummer: $facturnr"));

$this->pdf->Ln(3);
$this->pdf->SetAligns(array('R','L','R','R','R'));
$this->pdf->SetFont($this->pdf->brief_font,'B',8);
$this->pdf->row(array('',"Verslagperiode","Nota datum"));
$this->pdf->Ln(3);
$this->pdf->SetFont($this->pdf->brief_font,'',10);
$this->pdf->row(array('',"$vanDatum t/m $totDatum",$huidigeDatum));
$this->pdf->Ln();

$this->pdf->line(15+$this->pdf->marge,$this->pdf->getY(),185,$this->pdf->getY());

$this->pdf->Ln();
$this->pdf->SetFont($this->pdf->brief_font,'B',8);
$this->pdf->row(array('',"Vermogensspecificatie",''));
$this->pdf->SetFont($this->pdf->brief_font,'',10);
$this->pdf->Ln(3);

    $oldPortefeuilleString = strval($this->waarden['portefeuille']);
    $i=1;
	  $puntenAantal=0;
    $portefeuilleString='';
    if(strlen($oldPortefeuilleString)==9)
    {
      $maxPuntenAantal=3;
      $maxTekensPerPunt=2;
    }
    elseif(strlen($oldPortefeuilleString)==6)
    {
      $maxPuntenAantal=1;
      $maxTekensPerPunt=3;
    }
    else
    {
      $portefeuilleString=$oldPortefeuilleString;
    }
    
    if($portefeuilleString == '')
    {
  		for($j=0;$j<strlen($oldPortefeuilleString);$j++)
	  	{
		   if($i>$maxTekensPerPunt && $puntenAantal < $maxPuntenAantal)
		   {
		    $portefeuilleString.='.';
		    $i=1;
		    $puntenAantal ++;
		   }
		   $portefeuilleString.= $oldPortefeuilleString[$j];
		   $i++;
		  }
    }
$this->pdf->SetWidths(array(15,60+60,5,25));    
$this->pdf->row(array('',$this->waarden['depotbankOmschrijving'].' Effectenrekening '.$portefeuilleString,'€',$this->formatGetal($this->waarden['basisRekenvermogen'],0)));
$this->pdf->Ln();
$this->pdf->SetAligns(array('R','L','L','R','R','R'));

$this->pdf->line(15+$this->pdf->marge,$this->pdf->getY(),185,$this->pdf->getY());

$this->pdf->Ln();
$this->pdf->SetWidths(array(15,60,60,5,25));
$this->pdf->SetFont($this->pdf->brief_font,'B',8);
$this->pdf->row(array('',"Kostenspecificatie","Fee per kwartaal",'','Subtotaal'));
$this->pdf->Ln(3);
$this->pdf->SetFont($this->pdf->brief_font,'',10);  

if($this->waarden['BeheerfeePercentageVermogen']==0)
  $this->pdf->row(array('','Vermogens'.strtolower($this->waarden['SoortOvereenkomst']),
  'Vast bedrag','€',
  $this->formatGetal($this->waarden['beheerfeeBetalen'],2))); 
else
  $this->pdf->row(array('','Vermogens'.strtolower($this->waarden['SoortOvereenkomst']),
  $this->formatGetal($this->waarden['BeheerfeePercentageVermogen']/$this->waarden['BeheerfeeAantalFacturen'],3).'%','€',
  $this->formatGetal($this->waarden['beheerfeeBetalen'],2))); 
$this->pdf->Ln(3);
$this->pdf->row(array('','BTW ('.$this->formatGetal($this->waarden['btwTarief'],$round)."%)",'','€',$this->formatGetal($this->waarden['btw'],2)));
$this->pdf->Ln(3);

if($this->waarden['BestandsvergoedingUitkeren'] <> 0) //rvv
{
  $this->pdf->row(array('',"Bestandsvergoedingen"));
  $this->pdf->SetWidths(array(15,120,5,25));
  $this->pdf->SetAligns(array('R','L','R','R','R'));
	$this->pdf->row(array('',"Retournering ontvangen bestandsvergoedingen t/m het ".$this->waarden['kwartaal'].'e kwartaal','€',$this->formatGetal($this->waarden['bestandsvergoeding']*-1,2)));
 	$this->pdf->ln();
}
$this->pdf->SetWidths(array(15,60,60,5,25));
$this->pdf->Ln();

$this->pdf->line(15+$this->pdf->marge,$this->pdf->getY(),185,$this->pdf->getY());

$this->pdf->Ln();

$this->pdf->SetFont($this->pdf->brief_font,'B',10);
$this->pdf->row(array('',"In rekening te brengen",'','€',$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));

$this->pdf->Ln();

$this->pdf->line(15+$this->pdf->marge,$this->pdf->getY(),185,$this->pdf->getY());

$this->pdf->Ln();

$this->pdf->SetWidths(array(15,160));
$this->pdf->SetFont($this->pdf->brief_font,'BI',8);
$this->pdf->row(array('',"Het in rekening te brengen bedrag wordt omstreeks $incassoDatum afgeschreven van uw rekening met rentedatum $totDatum."));
$this->pdf->Ln();
$this->pdf->row(array('',"Voor vragen of nadere toelichting kunt u contact opnemen met Sandra Schoonens, telefoonnummer 070 - 3150 999 of sandra.schoonens@capitael.nl"));


/*
$this->pdf->SetWidths(array(15,140));
$this->pdf->SetWidths(array(25-$this->pdf->marge,40,70));
$facturnr=date("Y")." - ".$this->waarden['factuurNummer'];
$this->pdf->row(array('',"Factuurnummer:",$facturnr));
$this->pdf->ln(1);
$this->pdf->row(array('',"Portefeuillenummer:",$this->waarden['portefeuille']));
$this->pdf->ln(10);
$this->pdf->line(25,$this->pdf->getY()+2,185,$this->pdf->getY()+2);
$this->pdf->ln(10);

//$this->pdf->SetFont($this->pdf->brief_font,'B',11);
$this->pdf->SetWidths(array(15,120,10,30));
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
$this->pdf->ln(20);
$extraDagen=time()+(3*24*3600);
$this->pdf->SetWidths(array(25-$this->pdf->marge,140));

if($crmData['BetalingsinfoMee']==1)
  $this->pdf->row(array('',"Wij verzoeken u het totaalbedrag binnen 14 dagen over te maken op rekeningnummer 21.17.22.448 ten name van Steentjes Vermogensbeheer B.V. te Bussum"));
else
  $this->pdf->row(array('',"Bovenvermeld bedrag wordt automatisch van uw rekening geïncasseerd."));
$this->pdf->ln(40);
$this->pdf->row(array('',"BTW-nummer is 8109.30.134.B.01"));
*/
$this->pdf->AutoPageBreak=false;

$this->pdf->SetWidths(array(55,30,30,30));
$this->pdf->SetAligns(array('L','L','L','L'));
$this->pdf->SetFont($this->pdf->brief_font,'B',7);
 $this->pdf->SetTextColor(175,175,175);
 $this->pdf->SetY(-12);
$this->pdf->row(array('',"Emmapark 9","www.capitael.nl","BTW 851060821B01"));
$this->pdf->SetY(-8);
$this->pdf->row(array('',"2595 ES Den Haag","T +31 (0)70 3150 999","KvK 53890418"));
$this->pdf->AutoPageBreak=true;

 $this->pdf->SetTextColor(0,0,0);
 $this->pdf->geenBasisFooter=true;

    $this->pdf->rowHeight = $oldRowHeight;


    ?>
