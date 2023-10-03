<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/11/25 09:30:52 $
 		File Versie					: $Revision: 1.3 $

 		$Log: facmod_factuurAfdrukkenPDF.php,v $
 		Revision 1.3  2019/11/25 09:30:52  cvs
 		call 7675
*/

include_once("wwwvars.php");

define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
require_once("../classes/AE_cls_pfdi.php");

if (!facmodAccess())
{
  return false;
}

//function vt($in) // dummy vertaal routine
//{
//  return $in;
//}

function unslash($arr)
{
  foreach ($arr as $k=>$v)
  {
    $out[$k] = str_replace("'", "`", $v);
  }
  return $out;
}

function nf($bedrag)
{
  global $__facmod;
  //return $bedrag;
  return number_format($bedrag,2,$__facmod["decimaalSeperator"],$__facmod["promilleSeperator"]);
}

$data = array_merge($_GET,$_POST);
$DB = new DB();
$fmt = new AE_cls_formatter();
$cfg = new AE_config();

$pdf = new PDFbase('P','mm','A4');

$pdf->setSourceFile('facmod/factuurpapier.pdf');
//$pdf->SetAutoPageBreak(true,$cfg->getData("bodyRegels"));

$bottomMargin = $cfg->getData("bodyRegels");
if ($bottomMargin < 0 OR $bottomMargin >= 90 )  $bottomMargin = 65;

$pdf->SetAutoPageBreak(true,65);
$pdf->AliasNbPages();



$factuurinfo["factuurBriefpapier"] = $data["factuurBriefpapier"];
$factuurinfo["email_factuur"] = trim($data["email_factuur"]);

function AlteredFooter()
{
  global $pdf,$factuurinfo,$transportText, $leftMargin;
  global $_SESSION;
  if ($transportText <> "")
  {
    $pdf->SetXY(10,250);
    $pdf->MultiCell(190,4, $transportText.($pdf->PageNo()+1) ,0,"R");
  }

  $pdf->SetX($leftMargin);
  $pdf->SetY($factuurinfo["voetnootY"]);
  $pdf->SetFont('Arial','',8);
  $pdf->MultiCell(180,4, $factuurinfo["voetnoot"] ,0,"L");
  $pdf->SetFont('Arial','',10);
}


function AlteredHeader()
{
  global $__appvar;
  global $factuurinfo;
  global $pdf;
  global $Y;
  global $bottomMargin;
  global $DB;
  global $cfg;
  global $leftMargin;
  global $rightMargin;
  global $boxTop;
  global $boxBottom;
  global $_SESSION;
  $leftMargin      = 10;
  $rightMargin     = 200;
  $boxTop          = 80;
  $boxBottom       = 100;




  $tplIdx = $pdf->importPage(1);
  $pdf->useTemplate($tplIdx);
  $pdf->SetY($factuurinfo["adres_y"]);
  $pdf->SetX($factuurinfo["adres_x"]);
  $pdf->addBodyText($factuurinfo["naam"]);

  if ($factuurinfo["tav"] <> "")
  {
    $pdf->SetX($factuurinfo["adres_x"]);
    $pdf->addBodyText($factuurinfo["tav"]);
  }

  $pdf->SetX($factuurinfo["adres_x"]);
  $pdf->addBodyText($factuurinfo["straat"]);

  $pdf->SetX($factuurinfo["adres_x"]);
  $pdf->addBodyText($factuurinfo["plaats"]);
  
  if ($factuurinfo["email_factuur"] <> "")
  {
    $pdf->SetX($factuurinfo["adres_x"]);
    $pdf->addBodyText("via Email:".$factuurinfo["email_factuur"]);
  }


  /*
  * vaste teksten op de factuur
  */
  $boxWidth  = $rightMargin-$leftMargin;
  $boxHeight = $boxBottom-$boxTop;

  $pdf->SetXY($leftMargin,$boxTop-7);
  $pdf->SetFont("Arial","b",14);
  $pdf->Cell($boxWidth,9,$factuurinfo["factuur"],0,0,"R");
  $pdf->SetFont("Arial","",10);

  //$pdf->rect($leftMargin, $boxTop, $boxWidth, $boxHeight);

  $pdf->SetY($boxTop);
  $pdf->SetX($leftMargin);

  $text="\nFactuurnummer\nFactuurdatum";
  if ($factuurinfo["debnr"] <> "")
  {
    $text .= "\nDebiteurnummer";
  }
  $pdf->MultiCell(35,4,$text,0,"L");

  $pdf->SetY($boxTop);
  $pdf->SetX($leftMargin+40);

  $text="\n: ".$factuurinfo["voorzet"].$factuurinfo["factuurnr"]."\n: ".$factuurinfo["factuurdatum"];
  if ($factuurinfo["debnr"] <> "")
  {
    $text .= "\n: ".$factuurinfo["debnr"];
  }


  $pdf->MultiCell(45,4,$text,0,"L");

  $pdf->SetY($boxTop);
  $pdf->SetX($leftMargin+100);

  $btwtxt = "";
  $btwval = "";
  if ($factuurinfo["btwnr"] != "")
  {
    $btwtxt = vt("uw BTW nummer");
    $btwval = ": ".$factuurinfo["btwnr"];
  }

  $text="\n".$btwtxt;
  $pdf->MultiCell(35,4,$text,0,"L");

  $pdf->SetY($boxTop);
  $pdf->SetX($leftMargin+130);

  $text="\n".": ".$btwval;
  $pdf->MultiCell(45,4,$text,0,"L");

  if ($factuurinfo["incasso"] == 1)
  {
    $pdf->SetY($boxBottom - 6);
    $pdf->SetX($leftMargin);
    $pdf->Cell($boxWidth,4,$factuurinfo["incassotxt"],0,0,"C");
  }

  /*
  * lijnen tekenen op de factuur
  */
  $pdf->SetTableWidths(array("12","13","114","22","8" ,"22"));
  $pdf->SetTableAligns(array("R" ,"L" ,"L"  ,"R" ,"C" ,"R") );
  $colX =              array(  0 ,12  ,35   ,149 ,171 ,180 ,$rightMargin);

  $Y = $boxBottom + 10;
  $LinesYstart = $Y-5;
  $LinesYstop  = 232;
  $pdf->Line($colX[1], $Y           ,$colX[6], $Y);
  $pdf->Line($colX[2], $LinesYstart ,$colX[2], $LinesYstop);
  $pdf->Line($colX[3], $LinesYstart ,$colX[3], $LinesYstop);
  $pdf->Line($colX[4], $LinesYstart ,$colX[4], $LinesYstop);
  $pdf->Line($colX[5], $LinesYstart ,$colX[5], $LinesYstop);
  //$pdf->Line($rightMargin ,$LinesYstart ,$rightMargin ,$LinesYstop);
  $LineWitdh = $pdf->LineWidth;
  $pdf->SetLineWidth(0.3);
  $pdf->Line($colX[2],$LinesYstop,$colX[6],$LinesYstop);
  $pdf->Line($colX[5],$LinesYstop-1,$colX[6],$LinesYstop-1);
  $pdf->setLineWidth(0.2);
  $pdf->SetY($Y-5);
  $pdf->AddTableRow(array("aantal","","omschrijving","stuksprijs ","btw","totaal "));
  $pdf->SetY($Y+1);
}

if ($data["action"] == "copyRun")
{
  if (!is_numeric($data["startInterval"]) or !is_numeric($data["stopInterval"]))
  {
    echo "Interval bevat ongeldige waarden";
    exit;
  }
  for ($x=$data["startInterval"];$x <= $data["stopInterval"];$x++)
  {

    makeInvoice($x);
  }
}
else
  makeInvoice($data["invoiceNr"]);

  $filename ="factuur_".makeFileName($factuurinfo["factuurnr"]).".pdf";
  $blob = $pdf->Output("","S");
  if ($kopieFactuur)
  {
    $_closeWindow = "window.open('','_self','');window.close()";
  }
  else
  {
    //$_returnUrl = "nawEdit.php?action=edit&id=".$data["deb_id"]."&useSavedUrl=1";
    if ($cfg->getData("factuurPDFMethode") <> 1)
      $_closeWindow = "window.opener.location = '".$_SESSION["pdfReturnUrl"]."'; window.close()";
    else
      $_closeWindow = "window.open('','_self','');window.close()";
  }
//echo "$_closeWindow";
Header('Content-Type: application/pdf');
Header("Cache-Control:  maxage=1");
Header("Pragma: public");
Header('Content-Length: '.strlen($blob));
Header('Content-disposition: inline; filename='.$filename);
echo $blob;
exit;




function makeInvoice($invoiceNr=0)
{
  global $fmt;
  global $data;
  global $pdf;
  global $DB;
  global $cfg;
  global $__appvar;
  global $factuurinfo;
  global $kopieFactuur;
  global $_SESSION;
  global $prjRec;
  global $nawRecord;
  global $USR;
  global $__factuur;
  $factuurinfo = array();

  //listarray($data);
//  debug($data);

  $data["invoiceNr"] = $invoiceNr;
  $factuurinfo["factuur"] = vt("FACTUUR");
  $factuurinfo["email_factuur"] = trim($data["email_factuur"]);
  if ($data["invoiceNr"] < 1)
  {
    $db = new DB();

    $kopieFactuur = false;
    $query = "SELECT * FROM CRM_naw WHERE id='".$data["deb_id"]."'";

    if (!$nawRecord = $db->lookupRecordByQuery($query))
    {
      echo "<br>Foutmelding: Geen debiteur info gevonden!";
      exit;
    }
    //listarray($nawRecord);
    $dateParts = explode($__appvar["date_seperator"],$data["invoiceDate"]);
    if (!checkdate($dateParts[1],$dateParts[0],$dateParts[2]))
    {
      echo "<br>Foutmelding: De opgegeven factuurdatum is geen geldige datum (".$data["invoiceDate"].")";
      exit;
    }
    $invoiceDate = $dateParts[2]."-".substr("00".$dateParts[1],-2)."-".substr("00".$dateParts[0],-2);
    if ($data["vervalDatum"] <> "")
    {
      $vdateParts = explode($__appvar["date_seperator"],$data["vervalDatum"]);
      $vervaldatum = $vdateParts[2]."-".substr("00".$vdateParts[1],-2)."-".substr("00".$vdateParts[0],-2);
    }
    else
      $vervaldatum = "";



    $query = "
  SELECT
    *
  FROM
    facmod_factuurregels
  WHERE
    rel_id=".$data["deb_id"]." AND
    wachtstand = 0           AND
    facnr < 1
  ORDER BY
    volgnr  ";

    $DB->SQL($query);
    $DB->Query();
    if ($DB->records() < 1)
    {
      echo "<br>Foutmelding: Geen factuurregels gevonden die factureerbaar zijn!";
      exit;
    }

    if ($cfg->isLocked("factuurnummer") AND $data["conceptInvoice"] <> "true")
    {
      echo "<br>Factureren is op dit moment niet mogelijk. Probeer a.u.b. het over enkele seconden nogmaals.";
      exit;
    }

    if ($data["conceptInvoice"] == "true")
    {
      $proef = vt("PROFORMA");
      $factuurinfo["factuurnr"]    = "-- proef -- ";
      $proef = "PROEF ";
    }
    else
    {
      $cfg->setLock("factuurnummer");
      $factuurinfo["factuurnr"]    = $cfg->getData("factuurnummer");
      $proef = "";
    }

    $factuurinfo["factuurTaal"]  = "nl";

    $factuurinfo["factuur"]      = $proef."FACTUUR";
    $factuurinfo["debnr"]        = $nawRecord["debiteurnr"];
    if ($nawRecord["incasso"] == 1)
    {
      $factuurinfo["incassotxt"]   = $nawRecord["incassotxt"].vt(" van uw bankrekening").$nawRecord["rekeningnr"];
      $factuurinfo["factuur"]      = $proef."FACTUUR VIA INCASSO";
    }
    if ($_SESSION["taal"] <> "en")
      $factuurinfo["voetnoot"]     = $cfg->getData("voetnoot");
    else
      $factuurinfo["voetnoot"]     = $cfg->getData("voetnoot_uk");
    $factuurinfo["voetnootY"]    = $cfg->getData("voetnootY");
    $factuurinfo["korting"]      = $nawRecord["factuurkorting"];
    $factuurinfo["voorzet"]      = $cfg->getData("factuurvoorzet");

    //$factuurinfo["factuurdatum"] = ldatum(db2jul($invoiceDate));
    $factuurinfo["factuurdatum"] = $fmt->format("@D{form}",$invoiceDate);
    $factuurinfo["vervaldatum"] = ldatum(db2jul($vervaldatum));


    $adres["naam"]   = $_POST["facNaam"];
    $adres["tav"]    = $_POST["facTav"];
    $adres["straat"] = $_POST["facStraat"];
    $adres["plaats"] = $_POST["facPlaats"];
    $adres["land"]   = $_POST["facLand"];
    $adres["btwnr"]   = $_POST["facBTWnr"];
    $adres = unslash($adres);
    $factuurinfo["factuurAdresSerial"] = serialize($adres);
    
  }
  else   // we gaan een kopie factuur printen
  {

    $kopieFactuur = true;
//    $factuurinfo["factuurBriefpapier"] = $cfg->getData("kopiefactuurBriefpapier");
    $factuurinfo["factuurBriefpapier"] = 0;
    $proef = $data["kopietxt"]." ";
    $query = "SELECT * FROM facmod_factuurbeheer WHERE facnr='".$data["invoiceNr"]."'";
    $DB->SQL($query);
    if (!$invoiceRec = $DB->lookupRecord())
    {
      echo "<br>Foutmelding: Geen factuurinfo voor kopiefactuur gevonden (".$data["invoiceNr"].")";
      exit;
    }
    $_SESSION["taal"] = $invoiceRec["factuurTaal"] <> "en"?"nl":"en";

    $DB->SQL("SELECT * FROM CRM_naw WHERE id=".$invoiceRec["rel_id"]);
    if (!$nawRecord = $DB->lookupRecord())
    {
      echo "<br>Foutmelding: Geen debiteur voor kopiefactuur gevonden!";
      exit;
    }
    
    
    $adres = unserialize($invoiceRec["factuurAdres"]);
    
    if (!isset($adres["naam"]))   // backwards comp als ernog geen factuuradres is opgeslagen
    {
      $adres["naam"]   = $nawRecord["naam"];
      $adres["tav"]    = $nawRecord["tav"];
      $adres["straat"] = $nawRecord["adres"];
      $adres["plaats"] = $nawRecord["pc"]."  ".$nawRecord["plaats"]."  ".$nawRecord["land"];
      //$adres["land"]   = $nawRecord["n_land"];
      $adres["btwnr"]  = $nawRecord["btwnr"];

      $adres = unslash($adres);
    }

    if ($invoiceRec["vervaldatum"] <> "" AND $invoiceRec["vervaldatum"] <> "0000-00-00")
    {
      $vervaldatum =  ldatum(db2jul($invoiceRec["vervaldatum"]));
    }
    else
    {
      $vervaldatum = "";
    }

    $invoiceDate = $invoiceRec["datum"];
    $factuurinfo["betaaldatum"]  = $invoiceRec["betaaldatum"];
    $factuurinfo["incassotxt"]   = $invoiceRec["incassotxt"];
    $factuurinfo["voetnoot"]     = $invoiceRec["voetnoot"];
    $factuurinfo["factuurTaal"]  = $invoiceRec["factuurTaal"];
    $factuurinfo["voetnootY"]    = $cfg->getData("voetnootY");
    $factuurinfo["korting"]      = $invoiceRec["korting"];
    $factuurinfo["factuur"]      = $proef.$factuurinfo["factuur"];
    if ($invoiceRec["incasso"] == 1)
    {
      $factuurinfo["incassotxt"]   = $invoiceRec["incassotxt"]." van uw bankrekening: ".$invoiceRec["rekeningnr"];
      $factuurinfo["factuur"]      = $proef."FACTUUR VIA INCASSO";
    }
//    $factuurinfo["factuurdatum"] = ldatum(db2jul($invoiceDate));
    $factuurinfo["factuurdatum"] = db2form($invoiceDate);
    $factuurinfo["factuurnr"]    = $invoiceRec["facnr"];
    $factuurinfo["vervaldatum"] = $vervaldatum;
    if ($_GET["nocopy"] == "1")
    {
      $factuurinfo["voorzet"]      = $invoiceRec["voorzet"];
    }
    else
    {
      $factuurinfo["voorzet"]      = "*".$invoiceRec["voorzet"];
    }


    if ($_SESSION["taal"] <> "en")
      $factuurinfo["voetnoot"]     = $cfg->getData("voetnoot");
    else
      $factuurinfo["voetnoot"]     = $cfg->getData("voetnoot_uk");
    $query = "
  SELECT
    *
  FROM
    facmod_factuurregels
  WHERE
    facnr = ".$data["invoiceNr"]."
  ORDER BY
    volgnr  ";
    $DB->SQL($query);
    $DB->Query();

  }

  //$pdf->Body("test");

  /*
  *  globale instellingen
  */

  $totalen = array();                               // in deze array worden totalen bijgehouden
  //$factuurinfo = array();                           // in deze array worden header en footer info bijgehouden



  $factuurinfo["page"]         = $pdf->PageNo() ;
  $factuurinfo["adres_x"]      = $cfg->getData("adres_x");
  $factuurinfo["adres_y"]      = $cfg->getData("adres_y");
//  $factuurinfo["briefhoofd"]   = $cfg->getData("factuurBriefpapier");
  $factuurinfo["naam"]         = $adres["naam"];
  $factuurinfo["tav"]          = $adres["tav"];
  $factuurinfo["straat"]       = $adres["straat"] ;
  $factuurinfo["plaats"]       = $adres["plaats"];
  $factuurinfo["btwnr"]        = $adres["btwnr"];
  $factuurinfo["debnr"]        = $nawRecord["debiteurnr"];
  $pdf->SetFont('Arial','',10);

  //$leftMargin      = 10;
  //$rightMargin     = 200;
  //$boxTop          = 80;
  //$boxBottom       = 100;
  /*
  * Adres op factuur
  */


  /*
  * factuur body afdrukken
  */

  $pdf->AddPage();

  $transportText = vt("Transport naar pagina");
  while ($row = $DB->nextRecord())
  {
    // onderdruk dat er 0 afgedrukt wordt
    if($row["aantal"]      == 0)
    {
      $row["aantal"] = "";
      $row["eenheid"] = "";
    }

    $row["stuksprijs"] = ($row["stuksprijs"]  == 0 OR $row["stuksprijs"]  == 1)?"":nf($row["stuksprijs"]);

    if($row["totaal_excl"] == 0)
    {
      $exBedrag = "";
      $row["btw"] = "";
      $row["totaal_incl"] = "";
    }
    else
    {
      $exBedrag = nf($row["totaal_excl"]);
    }






//listarray($row);

    $pdf->AddTableRow(array($row["aantal"],$row["eenheid"],$row["txt"],$row["stuksprijs"],$row["btw"],$exBedrag));
    switch ($row["btw"])
    {
      case "H":
      $totalen["excl_H"] += $row["totaal_excl"];
      $factuurinfo["btwHoog"] = $row["btw_per"];
      break;
      case "L":
      $totalen["excl_L"] += $row["totaal_excl"];
      $factuurinfo["btwLaag"] = $row["btw_per"];
      break;
      case "0":
      $totalen["excl_0"] += $row["totaal_excl"];
      break;
      case "VL":
      $totalen["excl_VL"] += $row["totaal_excl"];
      break;
      case "VH":
      $totalen["excl_VH"] += $row["totaal_excl"];
      break;
    }
  }
  unset($transportText);
  /*
  *  totalen op factuur
  */
  $pdf->SetAutoPageBreak(false);
  $y = 228;
  $x = $leftMargin+35;
  $pdf->SetXY($x,$y);



  $kortingbedrag = 0;
  if ($factuurinfo["korting"] <> 0)
  {
    $kortingbedrag = round(-1 * ($totalen["excl_H"] * ($factuurinfo["korting"]/100)),2);
    if ($kortingbedrag <> 0)  // als kortingbedrag 0 dan niet afdrukken
    {
      $txt = $factuurinfo["korting"]."% factuurkorting over ".fBedrag($totalen["excl_H"]);
      $pdf->Cell(0,0,fBedrag($kortingbedrag),0,0,"R");
      $pdf->Cell(-52,0,$txt,0,0,"R");
      $totalen["excl_H"] += $kortingbedrag;
    }
  }
//  if ($factuurinfo["betaaldatum"] <> "" AND $factuurinfo["betaaldatum"] <> "0000-00-00")
//  {
//    $txt = vt("Uiterste betaaldatum").db2form($factuurinfo["betaaldatum"]);
//    $pdf->Cell(70,0,$txt,0,0,"L");
//  }

  $y += 10;
  $btwBedragHoog = round($totalen["excl_H"] * ($factuurinfo['btwHoog']/100),2);
  $btwBedragLaag = round($totalen["excl_L"] * ($factuurinfo['btwLaag']/100),2);
  $eindtotaal =
  $totalen["excl_H"]  +
  $totalen["excl_L"]  +
  $totalen["excl_0"]  +
  $totalen["excl_VH"] +
  $totalen["excl_VL"] +
  $btwBedragHoog      +
  $btwBedragLaag      +
  $kortingbedrag;
  $eindtotaalFactuur = nf($eindtotaal);
  if($totalen["excl_H"] <> 0) 
  {
    $txt = vt("subtotaal excl")." ".$factuurinfo['btwHoog'].vt("% BTW").vt(" €");
    $pdf->setXY($x,$y);
    $pdf->Cell(0,0,nf($totalen["excl_H"]),0,0,"R");
    $pdf->Cell(-30,0,$txt,0,0,"R");
    $y+=5;

    $txt = $factuurinfo['btwHoog'].vt("% BTW").vt(" €");
    $pdf->setXY($x,$y);
    $pdf->Cell(0,0,nf($btwBedragHoog),0,0,"R");
    $pdf->Cell(-30,0,$txt,0,0,"R");
    $y+=5;
  }

  if($totalen["excl_L"] <> 0)
  {
    $txt = vt("subtotaal excl")." ".$factuurinfo['btwLaag'].vt("% BTW").vt(" €");
    $pdf->setXY($x,$y);
    $pdf->Cell(0,0,nf($totalen["excl_L"]),0,0,"R");
    $pdf->Cell(-30,0,$txt,0,0,"R");
    $y+=5;

    $txt = $factuurinfo['btwLaag'].vt("% BTW").vt(" €");
    $pdf->setXY($x,$y);
    $pdf->Cell(0,0,nf($btwBedragLaag),0,0,"R");
    $pdf->Cell(-30,0,$txt,0,0,"R");
    $y+=5;
  }

  if($totalen["excl_0"] <> 0)
  {
    $txt = vt("subtotaal BTW vrij").vt(" €");
    $pdf->setXY($x,$y);
    $pdf->Cell(0,0,nf($totalen["excl_0"]),0,0,"R");
    $pdf->Cell(-30,0,$txt,0,0,"R");
    $y+=5;
  }

  if($totalen["excl_VH"] <> 0)
  {
    $txt = vt("subtotaal BTW verlegd Hoog").vt(" €");
    $pdf->setXY($x,$y);
    $pdf->Cell(0,0,nf($totalen["excl_VH"]),0,0,"R");
    $pdf->Cell(-30,0,$txt,0,0,"R");
    $y+=5;
  }
  if($totalen["excl_VL"] <> 0)
  {
    $txt = vt("subtotaal BTW verlegd Laag").vt(" €");
    $pdf->setXY($x,$y);
    $pdf->Cell(0,0,nf($totalen["excl_VL"]),0,0,"R");
    $pdf->Cell(-30,0,$txt,0,0,"R");
    $y += 5;
  }

  $pdf->Line(170,$y-1,200,$y-1);
  $y += 0.5;
  $pdf->Line(170,$y-1,200,$y-1);

  $y += 2;
  if ($eindtotaal < 0)
  $txt = vt("CREDIT NOTA te ontvangen").vt(" €");
  else
  {
    if ($nawRecord["incasso"] == 1)
    $txt = vt("Totaal incassobedrag").vt(" €");
    else
    $txt = vt("Te betalen bedrag").vt(" €");
  }

  $pdf->setXY($x,$y);
  $pdf->Cell(0,0,$eindtotaalFactuur,0,0,"R");
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(-30,0,$txt,0,0,"R");

  if($nawRecord["betalingstermijn"] != '0')
    $betalingstermijn = $nawRecord["betalingstermijn"];
  else
    $betalingstermijn = $cfg->getData('betalingstermijn');

  if ($betalingstermijn == '0')
    $betalingstermijn = '30';


  if (!$data["invoiceNr"] AND $data["conceptInvoice"] <> "true")
  {

    // een nieuwe factuur dus factuurbeheer en factuurregels muteren
    $query  = "INSERT INTO facmod_factuurbeheer SET ";
    $query .= "  voorzet           = '".$factuurinfo["voorzet"]."' ";
    $query .= ", facnr             = '".$factuurinfo["factuurnr"]."' ";
    $query .= ", datum             = '".$invoiceDate."' ";
    $query .= ", rel_id            = '".$nawRecord["id"]."' ";
    $query .= ", status            = 'G' ";
    $query .= ", firmanaam         = '".$factuurinfo["naam"]."' ";
    $query .= ", bedrag_ex_h       = '".$totalen["excl_H"]."' ";
    $query .= ", btw_h             = '". $btwBedragHoog."' ";
    $query .= ", bedrag_ex_l       = '".$totalen["excl_L"]."' ";
    $query .= ", bedrag_0          = '".$totalen["excl_0"]."' ";
    $query .= ", btw_l             = '". $btwBedragLaag."' ";
    $query .= ", bedrag_vh         = '".$totalen["excl_VH"]."' ";
    $query .= ", bedrag_vl         = '".$totalen["excl_VL"]."' ";
    $query .= ", bedrag_incl       = '".$eindtotaal."' ";
    $query .= ", door              = '".$USR."' ";
    $query .= ", voetnoot          = '".$factuurinfo["voetnoot"]."' ";
    $query .= ", incasso           = '".$nawRecord["incasso"]."' ";
    $query .= ", incassotxt        = '".$nawRecord["incassotxt"]."' ";
    $query .= ", korting           = '".$factuurinfo["korting"]."' ";
    $query .= ", rekeningnr        = '".$nawRecord["rekeningnr"]."' ";
    $query .= ", btwPercentageLaag = '".$cfg->getData("btw_L")."' ";
    $query .= ", btwPercentageHoog = '".$cfg->getData("btw_H")."' ";
    $query .= ", betalingstermijn  = '".$betalingstermijn."' ";
    $query .= ", email_factuur     = '".$factuurinfo["email_factuur"]."' ";
    $query .= ", betaal_datum      = '".$factuurinfo["betaaldatum"]."' ";
    $query .= ", factuurTaal       = '".$factuurinfo["factuurTaal"]."' ";
    $query .= ", factuurAdres      = '".$factuurinfo["factuurAdresSerial"]."' ";
    $query .= ", prj_id            = '".$data["prj_id"]."' ";
    $query .= ", omzetgroep        = '".$data["omzetgroep"]."' ";
    $query .= ", add_date          = NOW() ";
    $query .= ", add_user          = '".$USR."' ";
    $query .= ", change_date       = NOW() ";
    $query .= ", change_user       = '".$USR."' ";
    $DB->SQL($query);

    if ($DB->Query())   // factuurbeheer is gelukt
    {
      $query  = "UPDATE facmod_factuurregels SET";
      $query .= "  facnr = '".$factuurinfo["factuurnr"]."' ";
      $query .= ", change_date = NOW() ";
      $query .= ", change_user = '".$USR."' ";
      $query .= "WHERE  rel_id=".$data["deb_id"]." AND wachtstand = 0 AND facnr < 1";
      $DB->SQL($query);
      $DB->Query();
      $cfg->putData("factuurnummer",($factuurinfo["factuurnr"]+1));
      $cfg->releaseLock("factuurnummer");

      if ($factuurinfo["email_factuur"] <> "")
      {

        mailInvoice();
      }
    
    }

  }

}

function mailInvoice()
{
  global $pdf, $factuurinfo, $nawRecord, $USR, $__debug, $__factuur ;
  $db = new DB();

  $file = "facturen/".$__factuur["pdfVoorzet"]."-".$factuurinfo["factuurnr"].".pdf";
  $factuurnummer = $factuurinfo["factuurnr"];
  file_put_contents($file, $pdf->Output("","S"));

  $emailTo = $factuurinfo["email_factuur"];
  
  $body = $_POST["email_template"];
  $body = str_replace("{factuurnummer}", $factuurnummer, $body);
  
  /////////////////////////////////////

  include_once('../classes/AE_cls_phpmailer.php');
  $mail = new PHPMailer();
  $mail->IsSMTP();
  $mail->IsHTML(true);
  $mail->Host = "mailer.aeict.nl"; // SMTP server<br>
  $mail->AddAddress( $emailTo );
  $mail->FromName = $__factuur["mailFromFullname"];
  $mail->From = $__factuur["mailFrom"];
  $mail->Sender = $__factuur["mailFrom"];

  if (count($__factuur["mailBCC"]) > 0)
  {
    foreach ($__factuur["mailBCC"] as $bcc)
    {
      if (trim($bcc) != "")
      {
        $mail->addCustomHeader("BCC: $bcc");
        $mail->AddBCC($bcc);
      }

    }
  }
  if ($__factuur["debug"])
  {
    $mail->AddBCC("cvs@aeict.nl");
  }

  $mail->Subject = str_replace("{factuurnummer}", $factuurnummer, $__factuur["subject"] );
  $mail->AddAttachment($file);
  //digifacLog($nawRecord["id"],$factuurnummer, "Factuur verstuurd als bijlage naar ".$emailTo);
  $mail->AltBody  = $body;
  $body = nl2br($body);
  $mail->Body = $body;
//  debug($mail);
  $mail->Send();

  $query = "
  UPDATE facmod_factuurbeheer SET
    change_date = NOW(),
    change_user = '{$USR}',
    email_datum = NOW(),
    factuurEmailLog = '\n".date("d-m-Y H:i")." :: Factuur per E-mail verzonden aan {$emailTo}"."'
  WHERE facnr = '$factuurnummer'";
  $db->executeQuery($query);

  if (!$__factuur["keepPdf"] )
  {
    unlink($file);
  }
  
}
