<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/07/25 15:37:41 $
 		File Versie					: $Revision: 1.5 $

 		$Log: PDFRapport_headers_L103.php,v $
 		Revision 1.5  2020/07/25 15:37:41  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2020/07/15 16:39:40  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2020/07/11 17:31:18  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2020/07/08 15:26:28  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2020/07/01 16:22:28  rvv
 		*** empty log message ***
 		

*/

function Header_basis_L103($object)
{
    $pdfObject = &$object;

    if ($pdfObject->rapport_type == "BRIEF")
    {
      $pdfObject->HeaderFACTUUR();
    }
    elseif ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->HeaderFACTUUR();
    }
    elseif ($pdfObject->rapport_type == "FRONT")
    {
	  	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    }
    else
    {
   		if(is_file($pdfObject->rapport_logo))
  		{
	      $pdfObject->Image($pdfObject->rapport_logo, $pdfObject->marge, 15, $pdfObject->logoXsize);
	  	}

		  $pdfObject->SetY(30);
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		  $pdfObject->SetTextColor($pdfObject->rapport_fontcolor[0],$pdfObject->rapport_fontcolor[1],$pdfObject->rapport_fontcolor[2]);
   
      $pdfObject->Cell(200,$pdfObject->rowHeight,$pdfObject->portefeuilledata['VermogensbeheerderNaam'],0,1,'L',0);
      $pdfObject->Cell(200,$pdfObject->rowHeight,$pdfObject->portefeuilledata['VermogensbeheerderAdres'],0,1,'L',0);
      $pdfObject->Cell(200,$pdfObject->rowHeight,$pdfObject->portefeuilledata['VermogensbeheerderWoonplaats'],0,1,'L',0);
      $pdfObject->Cell(200,$pdfObject->rowHeight,vertaalTekst_L103('The Netherlands',$pdfObject->rapport_taal),0,1,'L',0);
  
      $pdfObject->SetY(50);
      $pdfObject->Cell(200,$pdfObject->rowHeight,$pdfObject->portefeuilledata['Naam'],0,1,'L',0);
      if($pdfObject->portefeuilledata['Naam1']<>'')
        $pdfObject->Cell(200,$pdfObject->rowHeight,$pdfObject->portefeuilledata['Naam1'],0,1,'L',0);
      $pdfObject->Cell(200,$pdfObject->rowHeight,$pdfObject->portefeuilledata['Adres'],0,1,'L',0);
      $pdfObject->Cell(200,$pdfObject->rowHeight,trim($pdfObject->portefeuilledata['pc'].' '.$pdfObject->portefeuilledata['Woonplaats']),0,1,'L',0);
      $pdfObject->Cell(200,$pdfObject->rowHeight,
                       vertaalTekst_l103(
                         (isset($pdfObject->isoLanden[$pdfObject->portefeuilledata['Land']])?$pdfObject->isoLanden[$pdfObject->portefeuilledata['Land']]:$pdfObject->portefeuilledata['Land'])
                         ,$pdfObject->rapport_taal),0,1,'L',0);
      
 
    }
  $pdfObject->rapport_portefeuilleLast = $pdfObject->rapport_portefeuille;
  
}

function vertaalTekst_l103($tekst,$taal)
{
  global $teksten;
  if($taal <> 1)
  {
    if(isset($teksten[$tekst][$taal]))
      return $teksten[$tekst][$taal];
    $zoekTekst = str_replace("\n","",$tekst);
    $DB = new DB();
    $DB->SQL("SELECT Vertaling FROM Vertalingen WHERE Term = '".mysql_escape_string($zoekTekst)."' AND Taal = '".$taal."' LIMIT 1");
    $DB->Query();
    $data = $DB->nextRecord();
    $vertaling = str_replace('<enter>',"\n",$data['Vertaling']);
    if($vertaling=='')
        $vertaling = $tekst;
    $teksten[$tekst][$taal]=$vertaling;
  }
  else
  {
    $vertaling = $tekst;
  }
  
  return $vertaling;
}

function HeaderHUIS_L103($object)
{
    $pdfObject = &$object;
    $beginY=25;
    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[0],$pdfObject->rapport_kop_bgcolor[1],$pdfObject->rapport_kop_bgcolor[2]);
  
    $pdfObject->portefeuilledata['periodStart']=date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst(date("M",$pdfObject->rapport_datumvanaf),$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf);
    $pdfObject->portefeuilledata['periodEnd']=date("j",$pdfObject->rapport_datum)." ".vertaalTekst(date("M",$pdfObject->rapport_datum),$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum);
  
    if($pdfObject->subPortefeuille<>'')
    {
      $velden=array('periodStart'=>'Period Start','periodEnd'=>'Period Ended','Naam'=>'Client Name','Portefeuille'=>'Account Number');//,'Memo'=>'Goal Name');
      $data=$pdfObject->portefeuilledata;
      $data['Portefeuille']=$pdfObject->subPortefeuille;
    }
    else
    {
      $velden=array('periodStart'=>'Period Start','periodEnd'=>'Period Ended','Naam'=>'Client Name','Client'=>'Client Number');//,'AccountmanagerNaam'=>'Your Portfolio Manager');//,'VermogensbeheerderEmail'=>'Email');
      $data=$pdfObject->portefeuilledata;
    }
    $backup=array();
    $backup['widths']=$pdfObject->widths;
    $backup['aligns']=$pdfObject->aligns;
    $pdfObject->rect(90,$beginY,50,$pdfObject->rowHeight*count($velden)*2,'F');
    $pdfObject->setWidths(array(90-$pdfObject->marge,50,58));
    $pdfObject->setAligns(array('L','L','R'));
    
    $pdfObject->setY($beginY+$pdfObject->rowHeight/2);
    $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
    foreach($velden as $veld=>$beschrijving)
    {
      $pdfObject->Row(array('', vertaalTekst_l103($beschrijving,$pdfObject->rapport_taal)));
      $pdfObject->ln();
    }
    $pdfObject->setY($beginY+$pdfObject->rowHeight/2);
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    foreach($velden as $veld=>$beschrijving)
    {
      $pdfObject->Row(array('', '',$data[$veld]));
      $pdfObject->ln();
    }
    $pdfObject->widths=$backup['widths'];
    $pdfObject->aligns=$backup['aligns'];
    $pdfObject->setY(68);
 // listarray($pdfObject->portefeuilledata);
}
  

?>