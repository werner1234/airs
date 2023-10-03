<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/12/07 19:14:04 $
 		File Versie					: $Revision: 1.4 $

 		$Log: kerstkaart.php,v $
 		Revision 1.4  2011/12/07 19:14:04  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2011/10/12 07:05:43  rvv
 		*** empty log message ***

 		Revision 1.2  2011/10/10 07:02:10  rvv
 		*** empty log message ***

 		Revision 1.1  2011/10/05 17:57:59  rvv
 		*** empty log message ***

 		Revision 1.4  2009/03/14 11:42:06  rvv
 		*** empty log message ***

 		Revision 1.3  2009/01/20 17:46:01  rvv
 		*** empty log message ***

 		Revision 1.2  2008/05/16 08:04:51  rvv
 		*** empty log message ***

 		Revision 1.1  2007/11/27 13:19:18  cvs
 		CRM
 		- verjaardaglijst
 		- velden omzetten van extra velden naar naw
 		- excel van tijdelijke rekening mutaties


*/

include_once("wwwvars.php");
include_once('../classes/excel/Writer.php');
include_once('../classes/AE_cls_xls.php');
require_once("../classes/AE_cls_pdfBase.php");
include_once("../config/ordersVars.php");

function AlteredHeader()
{
  global $pdf;
  $pdf->SetX(10);
  $pdf->SetY(15);
  $pdf->SetFont("Arial","B",16);
  $pdf->MultiCell(190,4, 'Kerstkaart',0,'L');
  $pdf->ln();

}

function AlteredFooter()
{
  global $pdf;
      $pdf->Line(10 ,280, 200,280);
      $pdf->ln();
      $pdf->SetX(10);
      $pdf->SetY(285);
      $pdf->SetFont("Arial","I",10);
      $pdf->Cell(190,4, "pagina ".$pdf->PageNo() ,0,0,"R");
      $pdf->SetX(10);
      $pdf->Cell(190,4, "Datum: ".date('l j F Y h:i') ,0,0,"L");

}

function Row($data)
{
    global $pdf;
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$pdf->NbLines($pdf->widths[$i],$data[$i]));
    $h=$pdf->rowHeight*$nb;
    //Issue a page break first if needed
    if($pdf->AutoPageBreak)
      $pdf->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$pdf->widths[$i];
        $a=isset($pdf->aligns[$i]) ? $pdf->aligns[$i] : 'L';
        //Save the current position
        $x=$pdf->GetX();
        $y=$pdf->GetY();
        //Draw the border
        //$pdf->Rect($x,$y,$w,$h);
        //Print the text
        $lines = $pdf->NbLines($pdf->widths[$i],$data[$i]);
        // fill lines


	      if(is_array($pdf->CellFontColor[$i]))
        {
          $oldColor=$pdf->TextColor;
          $pdf->SetTextColor($pdf->CellFontColor[$i]['r'],$pdf->CellFontColor[$i]['g'],$pdf->CellFontColor[$i]['b']);
        }

        $pdf->MultiCell($w,$pdf->rowHeight,$data[$i],$line,$a,$pdf->fillCell[$i]);
        if($pdf->CellBorders[$i])
        {
          $borders = array();
          if(is_array($pdf->CellBorders[$i]))
            $borders = $pdf->CellBorders[$i];
          else
            $borders[] = $pdf->CellBorders[$i];
          foreach ($borders as $border)
          {
            if(isset($pdf->underlinePercentage) && $pdf->underlinePercentage != 1)
              $shrink = $w-$w*$pdf->underlinePercentage;
            else
              $shrink=0;
            if($border == 'U')
              $pdf->Line($x,$y+$h,$x+$w,$y+$h);
            elseif($border == 'US')
              $pdf->Line($x+$shrink,$y+$h,$x+$w,$y+$h);
            elseif($border == 'SUB')
            {
              $pdf->Line($x+$shrink,$y,$x+$w,$y);
              $pdf->setDash(1,1);
              $pdf->Line($x+$shrink,$y+$h,$x+$w,$y+$h);
              $pdf->setDash();
            }
            elseif($border == 'T')
              $pdf->Line($x,$y,$x+$w,$y);
            elseif($border == 'TS')
              $pdf->Line($x+$shrink,$y,$x+$w,$y);
            elseif($border == 'L')
              $pdf->Line($x,$y,$x,$y+$h);
            elseif($border == 'R')
              $pdf->Line($x+$w,$y,$x+$w,$y+$h);
            elseif($border == 'UU')
            {
              $pdf->Line($x+$shrink,$y+$h,$x+$w,$y+$h);
              $pdf->Line($x+$shrink,$y+$h+1,$x+$w,$y+$h+1);
            }
          }
        }
        if($pdf->CellDot[$i])
        {
            $pdf->Circle($x+$w*.5,$y+$h*.5*.9,$h*.5*.9,0,360,'DF','','');
        }
        if(is_array($pdf->CellFontColor[$i]))
          $pdf->TextColor=$oldColor;
         //Put the position to the right of the cell
        $pdf->SetXY($x+$w,$y);
	    }
	    //Go to the next line
    $pdf->Ln($h);
}

function addAdres($record)
{
  global $pdf;
  if($pdf->GetY() > 250)
    $pdf->AddPage('P');
  $pdf->SetFont('Arial','B',10);
  row(array($record['naam']));
  $pdf->SetFont('Arial','',10);
  row(array($record['naam1']));
  row(array($record['verzendAdres']));
  row(array($record['verzendPc']." ".$record['verzendPlaats']));
  row(array($record['verzendLand']));
  $pdf->ln();
}

$pdf = new PDFbase('P','mm','A4');
$pdf->rowHeight=4;
$pdf->SetAutoPageBreak(true,15);
$pdf->pagebreak = 190;
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);

$pdf->SetTableWidths(array(200,100));
$pdf->SetTableAligns(array("L","L"));

$query = "SELECT
CRM_naw.id,
CRM_naw.zoekveld,
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.verzendAanhef,
CRM_naw.verzendAdres,
CRM_naw.verzendPc,
CRM_naw.verzendPlaats,
CRM_naw.verzendLand
FROM CRM_naw
Inner Join CRM_evenementen ON CRM_naw.id = CRM_evenementen.rel_id
WHERE CRM_evenementen.evenement='kerstkaart' AND CRM_naw.aktief=1
GROUP BY CRM_naw.id
ORDER BY zoekveld";

$db = new DB();
$db2 = new DB();
$db->SQL($query);
$db->Query();

$xlsData=array();
/*
CRM_naw.id,
CRM_naw.zoekveld,
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.verzendAanhef,
CRM_naw.verzendAdres,
CRM_naw.verzendPc,
CRM_naw.verzendPlaats,
CRM_naw.verzendLand
*/
$xlsData[]=array('id','zoekveld','naam','naam1','verzendAdres','verzendPc','verzendPlaats','verzendLand');
while ($hoofdRecord = $db->nextRecord())
{
  $query="SELECT
CRM_naw_adressen.rel_id,
CRM_naw_adressen.naam,
CRM_naw_adressen.naam1,
CRM_naw_adressen.adres,
CRM_naw_adressen.pc,
CRM_naw_adressen.plaats,
CRM_naw_adressen.land
FROM CRM_naw_adressen WHERE CRM_naw_adressen.rel_id='".$hoofdRecord['id']."' AND CRM_naw_adressen.evenement='kerstkaart' ";
  $db2->SQL($query);
  $db2->Query();
  if($db2->records() > 0)
  {
    while($adres=$db2->nextRecord())
    {
      $record=$hoofdRecord;
      $record['naam']=$adres['naam'];
      $record['naam1']=$adres['naam1'];
      $record['verzendAdres']=$adres['adres'];
      $record['verzendPc']=$adres['pc'];
      $record['verzendPlaats']=$adres['plaats'];
      $record['verzendLand']=$adres['land'];
      addAdres($record);
      $xlsData[]=array($record['id'],$record['zoekveld'],$record['naam'],$record['naam1'],$record['verzendAdres'],$record['verzendPc'],$record['verzendPlaats'],$record['verzendLand']);

    }
  }
  else
  {
    addAdres($hoofdRecord);
    $xlsData[]=array($hoofdRecord['id'],$hoofdRecord['zoekveld'],$hoofdRecord['naam'],$hoofdRecord['naam1'],$hoofdRecord['verzendAdres'],$hoofdRecord['verzendPc'],$hoofdRecord['verzendPlaats'],$hoofdRecord['verzendLand']);
  }
}




if($_GET['xls']==1)
{
  $xls = new AE_xls();
  $xls->setData($xlsData);
  $xls->OutputXls("kerstkaart.xls");
  exit;
}
else
{
  $pdf->Output('kerstkaart.pdf','I');
}
?>