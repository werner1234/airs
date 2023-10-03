<?
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.39 $

 		$Log: FactuurHistoriePdf.php,v $
 		Revision 1.39  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.38  2018/01/31 17:20:04  rvv
 		*** empty log message ***
 		
 		Revision 1.37  2016/05/29 14:01:51  rvv
 		*** empty log message ***
 		
 		Revision 1.36  2015/11/02 09:35:00  rvv
 		*** empty log message ***
 		
 		Revision 1.35  2015/11/01 17:21:46  rvv
 		*** empty log message ***
 		
 		Revision 1.34  2014/04/16 15:49:33  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2014/04/05 15:31:04  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2014/03/19 16:34:15  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2013/06/01 16:14:14  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2013/04/22 11:36:24  cvs
 		factuur voorzet AABB
 		
 		Revision 1.29  2013/04/20 16:28:49  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2013/04/17 14:59:43  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2012/05/17 07:01:18  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2012/01/11 12:26:13  rvv
 		*** empty log message ***

 		Revision 1.25  2011/03/13 18:40:35  rvv
 		*** empty log message ***

 		Revision 1.24  2011/03/09 13:18:07  rvv
 		*** empty log message ***

 		Revision 1.23  2011/03/09 12:49:10  rvv
 		*** empty log message ***

 		Revision 1.22  2010/11/24 20:11:25  rvv
 		*** empty log message ***

 		Revision 1.21  2010/11/24 11:57:28  rvv
 		*** empty log message ***


*/

include_once("wwwvars.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_fpdf.php");
include_once("rapport/factuur/PDFFactuur.php");
include_once("rapport/factuur/Factuur.php");


$pdf = new PDFFactuur('P','mm');
$pdf->SetAutoPageBreak(true,15);
//$pdf->pagebreak = 190;
$pdf->underlinePercentage=1;
$pdf->rowHeight=5;
$db=new DB();
$portefeuilles=array();

if(!$_GET['id'])
{
  $cfg=new AE_config();
  $lastPrintDate=$cfg->getData('lastFactuurPrint');
  $now=date("Y-m-d H:i:s");
  if(!isset($_GET['concept']))
    $cfg->addItem('lastFactuurPrint',$now);

  $query  = "SELECT AfdrukSortering,CrmPortefeuilleInformatie FROM (Vermogensbeheerders)
  INNER JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
  WHERE VermogensbeheerdersPerGebruiker.Gebruiker =  '".$_SESSION["USR"]."'";
  $db->SQL($query);
  $afdrukSortering = $db->lookupRecord();

  if($afdrukSortering['CrmPortefeuilleInformatie'] == 1)
	  $join .= " LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille ";
	else
   $join .= " LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client ";

  if($afdrukSortering['AfdrukSortering'] != "")
	{
	  if($afdrukSortering['AfdrukSortering']=='Postcode')
	  {
	    if($afdrukSortering['CrmPortefeuilleInformatie'] == 1)
	      $order = " ORDER BY CRM_naw.verzendPc";
	    else
	      $order = " ORDER BY Clienten.Pc";
	  }
	  else
	  {
	    if($afdrukSortering['AfdrukSortering']=='Portefeuille')
	      $afdrukSortering['AfdrukSortering']='Portefeuilles.Portefeuille';
      $order = " ORDER BY ".$afdrukSortering['AfdrukSortering'];
	  }
	}
	else
	{
	  if($afdrukSortering['CrmPortefeuilleInformatie'] == 1)
	    $order = " ORDER BY CRM_naw.zoekveld"; //rvv nog check
	  else
		  $order = " ORDER BY Clienten.Client "; //rvv nog check
	}
  $join = " LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille ";
	$order = " ORDER BY CRM_naw.zoekveld"; //rvv nog check

	if($_GET['depotbank'] != '' && $_GET['depotbank'] !='alle')
	{
	  $depotbankFilter="AND Portefeuilles.Depotbank='".$_GET['depotbank']."'";
	}

	if($_GET['rapportage'])
	   $query="SELECT Portefeuilles.portefeuille as Portefeuille FROM  Portefeuilles $join WHERE Portefeuilles.BeheerfeeAantalFacturen <> 0 AND MONTH(Portefeuilles.BeheerfeeFacturatieVanaf) = '".$_GET['maand'] ."' AND Portefeuilles.Einddatum > now() $depotbankFilter $order";
	else
	   $query="SELECT FactuurHistorie.id,FactuurHistorie.Portefeuille FROM (FactuurHistorie) Join Portefeuilles ON FactuurHistorie.portefeuille = Portefeuilles.Portefeuille  $join
  WHERE status='1' AND PrintDate = '0000-00-00 00:00:00' $order";

  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    $notas[]=$data['id'];
    $portefeuilles[$data['Portefeuille']]=$data['Portefeuille'];
  }
}
else
  $notas[] = $_GET['id'];


if($_GET['rapportage']==1)
{
  $pdf->AddPage();
  $pdf->SetMargins(20,8);
	$pdf->toonProductieDatum=true;
  foreach ($portefeuilles as $portefeuille)
  {
     $query="SELECT FactuurHistorie.portefeuille,factuurNr,grondslag,omschrijving,factuurDatum,fee,status,Depotbank
	   FROM FactuurHistorie
	   Inner Join Portefeuilles ON FactuurHistorie.portefeuille = Portefeuilles.Portefeuille WHERE Portefeuilles.portefeuille = '$portefeuille' AND
	   factuurDatum > SUBDATE(now(),interval 10 year) ORDER BY factuurDatum";
	   $db->SQL($query);
	   $db->Query();
	   $portRows=array();
	   while($data=$db->nextRecord())
	   {
	     $factuurDatumJul=db2jul($data['factuurDatum']);
	     $voorzet=date("y.",$factuurDatumJul);
      
      
       switch($data['Depotbank'])
  	   {
  	     case "AAB":
  	       $voorzet.='A/';
  		     break;
  	     case "AABB":
  	       $voorzet.='B/';   
  		     break;
  	     default:
           $voorzet.='O/';
  	   }	

       $factuurNr=$voorzet.sprintf('%04d', $data['factuurNr']);

       if($data['grondslag'] <> 0 && $data['fee'] <> 0)
       {
         if($data['status'] == 0)
           $data['fee']=0;
         $portRows[]=array(date('d-m-Y',$factuurDatumJul),$factuurNr,number_format($data['grondslag'],2,',','.'),"€",number_format($data['fee'],2,',','.'),$data['omschrijving']);
       }
	   }
	   if($pdf->GetY()+$pdf->rowHeight*count($portRows)+35 > 280)
       $pdf->addPage();

    if($afdrukSortering['CrmPortefeuilleInformatie'])
     $query="SELECT CRM_naw.naam,CRM_naw.naam1,CRM_naw.verzendPaAanhef,CRM_naw.adres,CRM_naw.pc,CRM_naw.plaats,CRM_naw.land,CRM_naw.portefeuille,Portefeuilles.BeheerfeePercentageVermogen,Portefeuilles.BeheerfeeBedrag,
     DATE_FORMAT(Portefeuilles.Startdatum,'%d-%m-%Y') as Startdatum,Portefeuilles.FactuurMemo
     FROM CRM_naw LEFT JOIN Portefeuilles ON  Portefeuilles.Portefeuille=CRM_naw.portefeuille WHERE CRM_naw.portefeuille='".$portefeuille."'";
    else
     $query="SELECT Clienten.Naam as naam,Clienten.Naam1 as naam1,CRM_naw.verzendPaAanhef, Clienten.Adres as adres, Clienten.pc, Clienten.Woonplaats as plaats, Clienten.land, Portefeuilles.BeheerfeePercentageVermogen,
     Portefeuilles.BeheerfeeBedrag, DATE_FORMAT(Portefeuilles.Startdatum,'%d-%m-%Y') as Startdatum,Portefeuilles.FactuurMemo
     FROM Portefeuilles JOIN Clienten on Portefeuilles.Client=Clienten.Client WHERE Portefeuilles.Portefeuille='".$portefeuille."'";

	  $db->SQL($query);
	  $clientData=$db->lookupRecord();

	  loadLayoutSettings($pdf, $portefeuille);
	  $pdf->rowHeight = 5;
	  $pdf->SetAligns(array('L','L','R','R'));
	  $pdf->SetWidths(array(280));
	  $pdf->SetFont('Arial','',12);

	  $query="SELECT CRM_naw_adressen.naam, CRM_naw_adressen.naam1, CRM_naw_adressen.adres, CRM_naw_adressen.pc, CRM_naw_adressen.plaats,CRM_naw_adressen.land
  	FROM CRM_naw_adressen JOIN CRM_naw ON CRM_naw_adressen.rel_id=CRM_naw.id WHERE portefeuille='".$portefeuille."' AND evenement='factuur'";
  	$db->SQL($query);
  	$crmFactuurData=$db->lookupRecord();
  	if(is_array($crmFactuurData))
  	{
  	  $clientData['naam']=$crmFactuurData['naam'];
  	  $clientData['naam1']=$crmFactuurData['naam1'];
  	}
  	else
  	  $clientData['naam1'].=" ".$clientData['verzendPaAanhef'];

  	 $pdf->ln(2);
     $pdf->Row(array("Portefeuille $portefeuille"));
	   $pdf->Row(array("Factuurhistorie ".$clientData['naam']." ".$clientData['naam1']));
	   $pdf->ln(1);
	   $pdf->Line($pdf->lMargin,$pdf->GetY(),210-$pdf->lMargin,$pdf->GetY());
	   $pdf->ln(1);
	   $pdf->SetFont('Arial','',10);

	   	   $pdf->SetAligns(array('L','L','L','L'));
	   $pdf->SetWidths(array(35,50,15,100));
	   $pdf->Row(array("Datum portefeuille:",$clientData['Startdatum'],'Memo:',$clientData['FactuurMemo']));

	   //FactuurMemo
	   $pdf->SetWidths(array(35,5,25,20));
	   $pdf->SetAligns(array('L','L','R','R'));
	   $pdf->Row(array("Tarief","€",number_format($clientData['BeheerfeeBedrag'],2,',','.'),number_format($clientData['BeheerfeePercentageVermogen'],1,',','.')." %"));

	   $pdf->SetWidths(array(22,22,30,10,30,60));
	   $pdf->SetAligns(array('L','L','R','C','R',"L"));
	   $pdf->SetFont('Arial','B',10);
	   $pdf->ln(4);
	   $pdf->Row(array("Datum","Nummer","Waarde","","Bedrag","Omschrijving factuurafspraken"));

	   $pdf->SetFont('Arial','',10);
     foreach ($portRows as $row)
       $pdf->Row($row);
	   $pdf->ln(30);
  }

  //listarray($portefeuilles);
 // exit;

}
else
{
  
  $velden=array();    
  $query = "desc CRM_naw";
  $db->SQL($query);
  $db->query();
  while($data=$db->nextRecord('num'))
    $velden[]=$data[0];
  if(in_array('FactuurIncasso',$velden))
    $extraVeld='CRM_naw.FactuurIncasso,';

  foreach ($notas as $regelId)
  {
  	$query="SELECT * FROM FactuurHistorie WHERE id= '$regelId'";
   	$db->SQL($query);
	  $data=$db->lookupRecord();

    $portefeuille = $data['portefeuille'];
    
    $query="SELECT $extraVeld Clienten.*,Portefeuilles.BeheerfeeBTW,Portefeuilles.BetalingsinfoMee,Portefeuilles.Depotbank,
    Depotbanken.Omschrijving,
    Vermogensbeheerders.Naam as vermogensbeheerder,
    Vermogensbeheerders.rekening,
    CRM_naw.IBAN  as IBANbetaalRekening,
    Vermogensbeheerders.bank,
    (SELECT SUBSTRING_INDEX(Rekening, 'EUR', 1) FROM Rekeningen WHERE Rekeningen.Portefeuille='".$portefeuille."' AND Memoriaal=0 AND Rekening like '%EUR%' AND Inactief < 1 limit 1) as betaalRekening
    FROM
    Clienten
    Inner Join Portefeuilles ON Clienten.Client = Portefeuilles.Client
    Inner Join Depotbanken ON Depotbanken.Depotbank = Portefeuilles.Depotbank
    Inner Join Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder 
    LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
    WHERE Portefeuilles.Portefeuille='".$portefeuille."'";
	  $db->SQL($query);
  	$cdata=$db->lookupRecord();
    
  	//$query="SELECT naam,naam1,adres,pc,plaats,land,portefeuille FROM CRM_naw WHERE portefeuille='".$portefeuille."'";
  	$query="SELECT CRM_naw.ondernemingsvorm, CRM_naw_adressen.naam, CRM_naw_adressen.naam1, CRM_naw_adressen.adres, CRM_naw_adressen.pc, CRM_naw_adressen.plaats,CRM_naw_adressen.land
  	FROM CRM_naw_adressen JOIN CRM_naw ON CRM_naw_adressen.rel_id=CRM_naw.id WHERE portefeuille='".$portefeuille."' AND evenement='factuur'";
  	$db->SQL($query);
  	$crmData=$db->lookupRecord();
  	if(!is_array($crmData))
  	{
  	  $query="SELECT CRM_naw.ondernemingsvorm, naam, naam1, verzendAdres as adres, verzendPc as pc, verzendPlaats as plaats, verzendLand as land,portefeuille FROM CRM_naw WHERE portefeuille='".$portefeuille."'";
  	  $db->SQL($query);
      $crmData=$db->lookupRecord();
  	}

  	loadLayoutSettings($pdf, $portefeuille);
  	$pdf->SetAligns(array('L','L','L'));
   	$pdf->SetFont('Arial','',11);
  	$pdf->rowHeight = 5;
  	$pdf->AddPage();
  	$pdf->SetMargins(20,8);
    if($cdata['IBANbetaalRekening'] <> '')
    {
      $portefeuilleString=$cdata['IBANbetaalRekening'];
    }
    else
    {
      $oldPortefeuilleString = $cdata['betaalRekening'];
      $i=1;
	    $puntenAantal=0;
	    $portefeuilleString='';
	    for($j=0;$j<strlen($oldPortefeuilleString);$j++)
	    {
	     if($i>2 && $puntenAantal <3)
	     {
	      $portefeuilleString.='.';
	      $i=1;
	      $puntenAantal ++;
	     }
	     $portefeuilleString.= $oldPortefeuilleString[$j];
	     $i++;
	    }
    }
    $adres = $crmData['naam'];
    if(strlen($crmData['naam1']) > 0)
      $adres .= "\n".$crmData['naam1'];
    $adres .= "\n".$crmData['adres'];
    $adres .= "\n".$crmData['pc']." ".$crmData['plaats'];
    $adres .= "\n".$crmData['land'];
    $pdf->ln();
    $pdf->SetY(60);
    $pdf->SetWidths(array(120-$pdf->lMargin,80));
    $pdf->Row(array('',$adres));

    $pdf->SetY(105);
    $pdf->SetAligns(array('L','L','R'));
    $pdf->SetWidths(array(120-$pdf->lMargin,26,24));

    $voorzet=date("y.",db2jul($data['factuurDatum']));

	  switch($cdata['Depotbank'])
	  {
	    case "AAB":
	      $voorzet.='A/';
		    break;
	    case "AABB":
	      $voorzet.='B/';   
		    break;
	    default:
        $voorzet.='O/';
	  }
    	
    $factuurNr=sprintf('%04d', $data['factuurNr']);

    $pdf->Row(array("Datum: ".date("j ",db2jul($data['factuurDatum'])).$__appvar['Maanden'][date("n",db2jul($data['factuurDatum']))].date(" Y",db2jul($data['factuurDatum'])),
                  "Declaratie nr.",$voorzet.$factuurNr));

    $pdf->SetY(130);
    $pdf->SetWidths(array(140-$pdf->lMargin,5,25));
    $pdf->SetAligns(array('L','R','R'));
    $pdf->Row(array($data['omschrijving'],"€",number_format($data['fee'],2,',','.')));
    $pdf->CellBorders=array('','','U');
    $pdf->Row(array("BTW: ".number_format($cdata['BeheerfeeBTW'],1,',','.')."%",'',number_format($data['btw'],2,',','.')));
    $pdf->CellBorders=array('','','UU');
    $pdf->Row(array("Totaal: ","€",number_format($data['totaalIncl'],2,',','.')));
    if($cdata['FactuurIncasso']==1)
    {
      $pdf->SetWidths(array(175));
      $pdf->ln(18);
      $pdf->SetFont('Arial','BI',11);
      $pdf->Row(array("Bovengenoemd bedrag zal binnen enkele weken van uw beleggersrekening worden afgeschreven."));
      $pdf->SetFont('Arial','',11);
    }
    
    $pdf->SetY(200);
    $pdf->SetWidths(array(70-$pdf->lMargin,5,160));
    $pdf->CellBorders=array();
    $pdf->SetAligns(array('L','C','L'));
    if($cdata['BetalingsinfoMee'] > 0)
    {
      $pdf->Row(array($cdata['Omschrijving']));
      $pdf->Row(array("Overboekingsopdracht"));
      $pdf->Row(array(""));
      if($crmData['ondernemingsvorm']=='')
        $pdf->Row(array("Naam",":",$crmData['naam']." ".$crmData['naam1']));
      else
        $pdf->Row(array("Naam",":",$crmData['naam']));  
      $pdf->Row(array("Ten laste van rek.nr.",":",$portefeuilleString));
      $pdf->Row(array("Een bedrag",":","€ ".number_format($data['totaalIncl'],2,',','.')));
      $pdf->Row(array("Ten gunste van rek.nr.",":",$cdata['rekening']));
      $pdf->Row(array("Ten name van",":",$cdata['vermogensbeheerder']));
      $pdf->Row(array("Bij",":",$cdata['bank']));
      $pdf->Row(array("Omschrijving",":","Declaratie: ".$voorzet.$factuurNr));
      $pdf->ln();
      $pdf->Row(array("Met vriendelijke groet,"));
    }

    if(!$_GET['id'] && !isset($_GET['concept']))
    {
      $query="UPDATE FactuurHistorie SET printDate='$now' WHERE id = '$regelId'";
      $db->SQL($query);
      $db->Query();
    }
  }
}
$pdf->Output();
?>
