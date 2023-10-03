<?php

function Footer_basis_L109($object)
{
    $pdfObject=&$object;

    if ($pdfObject->nextFactuur == true )
    {
      $pdfObject->FooterFACTUUR();
      $pdfObject->nextFactuur = false;
    }
 

    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }
    elseif($pdfObject->frontPage == true)
    {
      $pdfObject->frontPage=false;
    }
    elseif($pdfObject->rapport_type == "ORDERP" || $pdfObject->rapport_type == "ORDERL")
    {
      
    }
    else
    {
       // $pdfObject->SetTextColor(255,255,255);
	      $pdfObject->SetY(-10);
	      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
	      $pdfObject->MultiCell(240,4,$pdfObject->rapport_voettext,'0','L');
	      //$pdfObject->MultiCell(250,4,$pdfObject->rapport_voettext_rechts,'0','L');
    }
}

function FooterFRONT_L109($object)
{
   $pdfObject=&$object;


}







?>