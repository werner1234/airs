<?php
function Footer_basis_L123($object)
{
    $pdfObject=&$object;

    if ($pdfObject->nextFactuur == true )
    {
      $pdfObject->FooterFACTUUR();
      $pdfObject->nextFactuur = false;
    }
    elseif ($pdfObject->frontPage == true)
    {
       $pdfObject->frontPage = false;
    }
    else
    {
      $pdfObject->SetTextColor($pdfObject->rapport_fontcolor[0],$pdfObject->rapport_fontcolor[1],$pdfObject->rapport_fontcolor[2]);
	    $pdfObject->SetY(-15);
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
	    if($pdfObject->rapport_type <> 'FACTUUR')
	      $pdfObject->MultiCell(240,4,$pdfObject->rapport_voettext,'0','L');
	    $pdfObject->Cell(25,4,$pdfObject->rapport_voettext_rechts,'0','L');
    }

    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }
}

function FooterFRONT_L123($object)
{
   $pdfObject=&$object;


}







?>