<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/09/30 07:54:15 $
 		File Versie					: $Revision: 1.3 $

 		$Log: ordersPDF_L8.php,v $
 		Revision 1.3  2015/09/30 07:54:15  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/07/27 11:29:40  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/12/24 16:34:06  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2011/12/21 19:18:08  rvv
 		*** empty log message ***

*/

include_once("wwwvars.php");
require_once("../classes/AE_cls_pdfBase.php");
include_once("../config/ordersVars.php");
include_once("rapport/rapportRekenClass.php");


if (!$_GET["orderid"] )
{
  echo "foute aanroep";
  exit();
}
  $ordermoduleAccess=GetModuleAccess("ORDER");

  $db = new DB();
  $db2 = new DB();
  $query = "SELECT * FROM Vermogensbeheerders Inner Join VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR' limit 1";
  $db->SQL($query);
  $beheerderRec = $db->lookupRecord();
  if($ordermoduleAccess==2)
    $query = "SELECT sum(aantal) as totaal FROM OrderRegelsV2 WHERE orderid='".$_GET["orderid"]."' ";
  else
    $query = "SELECT sum(aantal) as totaal FROM OrderRegels WHERE orderid='".$_GET["orderid"]."' ";
  $db->SQL($query);
  $tmp = $db->lookupRecord();
  if($ordermoduleAccess==2)
    $query = "SELECT * FROM OrdersV2 WHERE id='".$_GET["orderid"]."' ";
  else
    $query = "SELECT * FROM Orders WHERE orderid='".$_GET["orderid"]."' ";
  $db->SQL($query);
  $orderRec = $db->lookupRecord();
  $query = "SELECT * FROM Fondsen WHERE fonds='".$orderRec["fonds"]."' ";
  $db->SQL($query);
  $fondsenRec = $db->lookupRecord();
  $query = "SELECT * FROM Fondskoersen WHERE fonds='".$orderRec["fonds"]."' Order by datum desc limit 1 ";
  $db->SQL($query);
  $fondsKoersRec = $db->lookupRecord();

  if($ordermoduleAccess==2)
    $query = "SELECT * FROM OrderUitvoeringV2 WHERE orderid='".$_GET["orderid"]."'  ";
  else  
    $query = "SELECT * FROM OrderUitvoering WHERE orderid='".$_GET["orderid"]."'  ";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    $data['valutaKoers']=getValutaKoers($fondsenRec['Valuta'],$data['uitvoeringsDatum']);
    $uitvoeringWaarde +=$data['uitvoeringsAantal']*$data['uitvoeringsPrijs'];
    $uitvoeringWaardeEur += $data['uitvoeringsAantal']*$data['uitvoeringsPrijs']*$data['valutaKoers'];
    $uitvoeringAantal +=$data['uitvoeringsAantal'];
    $uitvoering[]=$data;
  }
  if($uitvoeringAantal <> 0)
  {
    $gemiddeldePrijsValuta=$uitvoeringWaarde/$uitvoeringAantal;
    $gemiddeldePrijsEur=$uitvoeringWaardeEur/$uitvoeringAantal;
  }
  else
  {
    $gemiddeldePrijsValuta=0;
    $gemiddeldePrijsEur=0;
  }

  if($ordermoduleAccess==2)
    $query="SELECT id as orderid FROM OrdersV2 WHERE batchId='".$orderRec['batchId']."' AND batchId > 0 ORDER BY id";
  else
    $query="SELECT orderid FROM Orders WHERE batchId='".$orderRec['batchId']."' AND batchId > 0 ORDER BY id";
  $db->SQL($query);
  if($db->QRecords($query) > 1)
  {

   if($ordermoduleAccess==2)
     $query= "SELECT OrdersV2.id, OrdersV2.giraleOrder, OrdersV2.orderid, OrdersV2.fondsOmschrijving, OrdersV2.ISINcode as fondsCode, 
     OrderRegelsV2.aantal, OrdersV2.transactieSoort, OrdersV2.transactieType,
    OrderRegelsV2.client,OrderRegelsV2.portefeuille,OrderRegelsV2.rekening as rekeningnr,
    OrderRegelsV2.valuta, OrdersV2.Memo, Fondsen.Valuta as fondsValuta
    FROM (Orders, OrderRegels)
    LEFT Join Fondsen ON Orders.fonds = Fondsen.Fonds
     WHERE OrderRegels.orderid = Orders.id AND Orders.batchId='".$orderRec['batchId']."' ORDER BY OrderRegelsV2.add_date";
   else
     $query= "SELECT Orders.id, Orders.giraleOrder, Orders.orderid, Orders.fondsOmschrijving, Orders.fondsCode, OrderRegels.aantal, Orders.transactieSoort, Orders.transactieType,
    OrderRegels.client,OrderRegels.portefeuille,OrderRegels.rekeningnr,OrderRegels.valuta, Orders.Memo, Fondsen.Valuta as fondsValuta
    FROM (Orders, OrderRegels)
    LEFT Join Fondsen ON Orders.fonds = Fondsen.Fonds
     WHERE OrderRegels.orderid = Orders.orderid AND Orders.batchId='".$orderRec['batchId']."' ORDER BY OrderRegels.add_date";

  $db->SQL($query);
  $db->query();
  $orderIds=array();
  while ($row = $db->nextRecord())
  {
    $orderIds[$row['orderid']]=$row['orderid'];
    $orderRegels[]=$row;

    if($client == '')
    {
      $client=$row['client'];
      $portefeuille=$row['portefeuille'];
      $rekeningnr=$row['rekeningnr'].$row['valuta'];
    }
    else
    {
      if($client != $row['client'])
      {
        echo "Verschillende clienten bij deze batchId?";
        exit;
      }
    }
  }
  }

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if ($VierDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];

	       if ($decimaal != '0' && !$newDec)
	       {
	         $newDec = $i;
	       }
	     }
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
	  {
	   return number_format($waarde,$dec,",",".");
	  }
	}


function AlteredHeader()
{
  global $beheerderRec, $pdf;
  $adres  = $beheerderRec["Adres"] ."\n";
  $adres .= $beheerderRec["Woonplaats"] ."\n";
  $adres .= "(T) ".$beheerderRec["Telefoon"] ."\n";
  $adres .= "(F) ".$beheerderRec["Fax"] ."\n";
  $pdf->SetY(10);
  $pdf->SetFont("Arial","b",12);
  $pdf->SetX(100);
  //$pdf->Cell(100,4, $beheerderRec["Naam"] ,0,0,"R");

  if (file_exists('rapport/logo/'.$beheerderRec["Logo"]) && filetype('rapport/logo/'.$beheerderRec["Logo"]) != 'dir' )
    $pdf->Image('rapport/logo/'.$beheerderRec["Logo"],200,4,88);

  $pdf->SetX(10);
  $pdf->SetY(17);
  $pdf->SetFont("Arial","",10);
  $pdf->MultiCell(275,4, $adres,0,'R');
  $pdf->ln();
  $pdf->tMargin = $pdf->GetY()+4;
}


  if(count($orderIds)>1)
  {
  //  ,$row[portefeuille],$row[client]

  $kop[] = array("field" =>"Client"     ,"value" =>$client);
  $kop[] = array("field" =>"Portefeuille"    ,"value" =>$portefeuille);
  $kop[] = array("field" =>"Rekeningnummer"    ,"value" =>$rekeningnr);

  $pdf = new PDFbase('L','mm','A4');
  $pdf->SetAutoPageBreak(true,15);
  $pdf->pagebreak = 190;
  $pdf->AliasNbPages();
  $pdf->AddPage();
  $pdf->SetFont('Arial','',10);
  $pdf->SetTableWidths(array(45,100));
  $pdf->SetTableAligns(array("L","L"));
  for ($x=0;$x < count($kop);$x++)
    $pdf->AddTableRow(array($kop[$x]['field'],$kop[$x]['value']));

  $pdf->SetTableWidths(array("10",'20',"90","30","20","25","30","40"));
  $pdf->SetTableAligns(array("R","L","L","R","L","R"));
  $pdf->ln();


  $pdf->AddTableRow(array("Pos",'Kenmerk',"Fonds omschrijving","ISIN-code","Valuta","Aantal","Transactiesoort","Transactietype"));
  $pdf->line(10,$pdf->GetY()+2,280,$pdf->GetY()+2);
  $pdf->SetY($pdf->GetY()+5);
//listarray($orderRegels);
 $positie=1;
 foreach ($orderRegels as $row)
 {
    $aantal = formatAantal($row["aantal"],0,true);
    if($row['giraleOrder']==1)
      $aantal='€'.$aantal;

   $row['transactieType']=$__ORDERvar["transactieType"][$row['transactieType']];
   $row['transactieSoort']=$__ORDERvar["transactieSoort"][$row['transactieSoort']];
   $pdf->AddTableRow(array($positie,$row['orderid'],$row['fondsOmschrijving'],$row['fondsCode'],$row['fondsValuta'],$aantal,$row['transactieSoort'],$row['transactieType']));
   if($row['Memo'] <> '')
   {
     $backupW=$pdf->widths;
     $backupA=$pdf->aligns;
     $pdf->SetTableWidths(array(190));
     $pdf->SetTableAligns(array("L"));
     $pdf->AddTableRow(array($row['Memo']));
     $pdf->SetTableWidths($backupW);
     $pdf->SetTableAligns($backupA);
   }

   $positie++;
 }

  $pdf->addBodyText();
  $pdf->Output();
  }
  else
  {
  $ordermoduleAccess=GetModuleAccess("ORDER");
  if($ordermoduleAccess==2)
    $query = "SELECT id as orderid,ISINcode as fondsCode, OrdersV2.* FROM OrdersV2 WHERE id='".$_GET["orderid"]."' ";
  else
    $query = "SELECT * FROM Orders WHERE orderid='".$_GET["orderid"]."' ";
  $db->SQL($query);
  $orderRec = $db->lookupRecord();


  if ($orderRec["transactieType"] == "L" or
      $orderRec["transactieType"] == "SL")
  {
    $trType = '   (koers '.$orderRec["koersLimiet"].')';

  }
  if ($tmp["totaal"] <> $orderRec["aantal"])
  {
    if($ordermoduleAccess==1)
    {
    $verschil=$tmp["totaal"] - $orderRec["aantal"];
    if ($verschil == intval($verschil))
      $verschil = number_format($verschil,0,",",".");
    else
      $verschil = number_format($verschil,4,",",".");
    $verschilTxt = " >>> LET OP verschil = $verschil";
    }
    else
    {
      $query = "SELECT sum(aantal) as aantal FROM OrderRegels WHERE id='".$_GET["orderid"]."' ";
      $db->SQL($query); 
      $test = $db->lookupRecord();
      $orderRec["aantal"]=$test['aantal'];
    }  
  }

  if ($orderRec["tijdsSoort"] == "DAT")
  {
    $looptijd = "  (".$orderRec["tijdsLimiet"].")";
  }

  $kop[] = array("field" =>"Order kenmerk"    ,"value" =>$orderRec["orderid"]);
  $kop[] = array("field" =>"Fonds"            ,"value" =>$orderRec["fondsOmschrijving"]);
  $kop[] = array("field" =>"ISIN"             ,"value" =>$orderRec["fondsCode"]);
  $kop[] = array("field" =>"Laatste koers"    ,"value" =>date("d-m-Y",db2jul($fondsKoersRec["Datum"]))." ".formatGetal($fondsKoersRec["Koers"],3));

  $aantal = formatAantal($orderRec["aantal"],0,true);
  if($orderRec["giraleOrder"]==1)
    $aantal="€ ".$aantal;

  $kop[] = array("field" =>"Aantal"           ,"value" =>$aantal.$verschilTxt);
  if($beheerderRec['OrderLoggingOpNota']==1)
    $kop[] = array("field" =>"Fondsvaluta"      ,"value" =>$fondsenRec['Valuta']);
  $kop[] = array("field" =>"TransactieType"   ,"value" =>$__ORDERvar["transactieType"][$orderRec["transactieType"]].$trType);
  $kop[] = array("field" =>"TransactieSoort"  ,"value" =>$__ORDERvar["transactieSoort"][$orderRec["transactieSoort"]]);
  $kop[] = array("field" =>"Looptijd"         ,"value" =>$__ORDERvar["tijdsSoort"][$orderRec["tijdsSoort"]].$looptijd);


$pdf = new PDFbase('L','mm','A4');
$pdf->SetAutoPageBreak(true,10);
$pdf->pagebreak = 190;
$pdf->AliasNbPages();
$pdf->AddPage();
//$pdf->Body("test");
$pdf->SetFont('Arial','',10);

$pdf->SetTableWidths(array(45,100));
$pdf->SetTableAligns(array("L","L"));

for ($x=0;$x < count($kop);$x++)
{
   $pdf->AddTableRow(array($kop[$x]['field'],$kop[$x]['value']));
}

$pdf->SetTableWidths(array("15","30","5","30","80","40","35","35"));
$pdf->SetTableAligns(array("R","R","C","L","L","L","R","R"));

  if($ordermoduleAccess==2)
    $query = "SELECT OrderRegelsV2.rekening as rekeningnr, OrderRegelsV2.* FROM OrderRegelsV2 WHERE orderid='".$_GET["orderid"]."' ORDER BY positie";
  else
    $query = "SELECT * FROM OrderRegels WHERE orderid='".$_GET["orderid"]."' ORDER BY positie";

$db->SQL($query);
$db->query();
$pdf->SetY($pdf->GetY()+10);

  if($beheerderRec['OrderLoggingOpNota']==1)
  {
     if($gemiddeldePrijsValuta == 0)
       $geschat="Geschat orderbedrag";
     else
       $geschat='Orderbedrag';

     if(strtolower($fondsenRec['Valuta']) <> 'eur')
       $geschatEur="Geschat orderbedrag";
     else
       $geschatEur=$geschat;

      if($fondsenRec['Lossingsdatum'] <> '0000-00-00')
        $renteOpmerking="Geschatte orderbedrag exclusief opgelopen rente.";
      else
        $renteOpmerking='';

     $pdf->AddTableRow(array("Pos","Aantal"," ","Portefeuille","Client","Rekeningnr",$geschatEur." eur",$geschat.' valuta'));
     $pdf->line(10,$pdf->GetY()+5,280,$pdf->GetY()+5);
     $pdf->SetY($pdf->GetY()+8);
  }
  else
  {
    $pdf->AddTableRow(array("Pos","Aantal"," ","Portefeuille","Client","Rekeningnr"));
    $pdf->line(10,$pdf->GetY()+2,280,$pdf->GetY()+2);
    $pdf->SetY($pdf->GetY()+5);
  }

while ($row = $db->nextRecord())
{

  $aantal = formatAantal($row["aantal"],0,true);
  if($orderRec["giraleOrder"]==1)
    $aantal="€ ".$aantal;

  if($fondsenRec['Fondseenheid'] <> 0)
    $eenheid=$fondsenRec['Fondseenheid'];
  else
    $eenheid=1;

  if($gemiddeldePrijsEur <> 0)
    $bedrag=number_format($row["aantal"]*$gemiddeldePrijsEur*$eenheid,2,",",".");
  else
    $bedrag=number_format($row['brutoBedrag'],2,",",".");

  if($gemiddeldePrijsValuta <> 0)
    $bedragValuta=number_format($row["aantal"]*$gemiddeldePrijsValuta*$eenheid,2,",",".");
  else
    $bedragValuta=number_format($row['brutoBedragValuta'],2,",",".");


  if($beheerderRec['OrderLoggingOpNota']==1)
    $pdf->AddTableRow(array($row['positie'],$aantal,"",$row['portefeuille'],$row['client'],$row['rekeningnr'].$row['valuta'],$bedrag,$bedragValuta));
  else
    $pdf->AddTableRow(array($row['positie'],$aantal,"",$row['portefeuille'],$row['client'],$row['rekeningnr'].$row['valuta']));
}

if(count($uitvoering) > 0)
{
  $pdf->ln(8);
  if(strtolower($fondsenRec['Valuta']) <> 'eur')
    $valutaKoersHeader="Geschatte valutakoers";
  else
    $valutaKoersHeader='Valutakoers';
   $pdf->AddTableRow(array('','Uitvoeringen','','Datum','Aantal','Koers',$valutaKoersHeader));
  foreach ($uitvoering as $regelData)
  {
    $pdf->AddTableRow(array('','','',date("d-m-Y",db2jul($regelData['uitvoeringsDatum'])),formatAantal($regelData['uitvoeringsAantal'],0,true),number_format($regelData['uitvoeringsPrijs'],2,",","."),number_format($regelData['valutaKoers'],4,",",".")));
   // listarray($regelData);
  }
}
if($beheerderRec['OrderLoggingOpNota']==1)
{
  $pdf->SetY(140);
  $pdf->MultiCell(200,4,$renteOpmerking);

  if($orderRec['memo'] <> '')
    $pdf->MultiCell(200,4,$orderRec['memo']);

  //$renteOpmerking
  $logregels=explode("\n",$orderRec['status']);
  $logregels=array_reverse($logregels);

  $pdf->AddTableRow(array(''));
  $pdf->SetTableWidths(array(60,120,50));
  $pdf->SetTableAligns(array("L","L","L","L"));
  foreach ($logregels as $regel)
  {
    if($regel <> '')
    {
      $pos=strpos($regel," ");
      $pdf->AddTableRow(array(substr($regel,0,$pos),str_replace("laatsteStatus","Status",substr($regel,$pos))));
    }
  }
  $pdf->AddTableRow(array(''));
  $pdf->AddTableRow(array("Printinformatie:",$USR,date('d-m-Y'),date("h:i")));
}

$pdf->addBodyText();

$pdf->Output();
  }

?>