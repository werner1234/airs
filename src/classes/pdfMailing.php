<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/03/29 15:59:35 $
 		File Versie					: $Revision: 1.5 $

 		$Log: pdfMailing.php,v $
 		Revision 1.5  2017/03/29 15:59:35  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/07/02 16:01:36  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/02/05 15:54:21  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/07/10 10:28:07  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/07/06 15:57:35  rvv
 		*** empty log message ***
 		
 		

*/

define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_fpdf.php");
include_once('../classes/fpdi/fpdi.php');

class pdfMailing
{
	function pdfMailing($crmId,$template)
	{
	  global $__appvar,$USR;
    $db=new DB();
    $query="SELECT * FROM CRM_naw LEFT JOIN Portefeuilles ON CRM_naw.portefeuille=Portefeuilles.Portefeuille WHERE CRM_naw.id='$crmId'";
    $db->SQL($query);
    $crmData=$db->lookupRecord();
    $crmData=$this->getAllFields($crmData);
    
    $dir = realpath(dirname(__FILE__))."/../html/PDF_templates/";
    $templatePath=$dir.$template;
    
    
    $query="SELECT * FROM pdfTemplateAfbeelding WHERE templateFile='$template'";
    $db->SQL($query);
    $db->Query();
    $templateImgData=array();
    while($data=$db->nextRecord())
    {
      $templateImgData[$data['pagina']][]=$data;
      
    }
    
    $query="SELECT * FROM pdfTemplateText WHERE templateFile='$template'";
    $db->SQL($query);
    $db->Query();
    $usedFont=array();
    $templateTxtData=array();
    while($data=$db->nextRecord())
    {
      $templateTxtData[$data['pagina']][]=$data;
      $usedFont[$data['fontName']]=$data['fontName'];
    }

    
    
    $this->pdf =  new FPDI();
    foreach($usedFont as $font)
    {
      if($font=='palatino')
      {
        if(file_exists(FPDF_FONTPATH.'pala.php'))
        {
          if(!isset($this->pdf->fonts['palatino']))
	        {
	          $this->pdf->AddFont('palatino','','pala.php');
	          $this->pdf->AddFont('palatino','B','palab.php');
	          $this->pdf->AddFont('palatino','I','palai.php');
	          $this->pdf->AddFont('palatino','BI','palabi.php');
	         }
         }
       }
     }
      
      
    
    
    $pagecount = $this->pdf->setSourceFile($templatePath);
    for($n=1; $n<=$pagecount; $n++)
    {
      $tplidx = $this->pdf->importPage($n);
      $this->pdf->addPage();
      $this->pdf->useTemplate($tplidx);
      
      
      if(is_array($templateImgData[$n]))
      {
        foreach($templateImgData[$n] as $data)
        { 
          $img=$crmData[$data['image']];
          if($img <> '')
            $this->pdf->MemImage(base64_decode($img),$data['x'],$data['y'], $data['imageWidth']);
        }
      }
      
      if(is_array($templateTxtData[$n]))
      {
        foreach($templateTxtData[$n] as $data)
        {
          $data['tekst']= $this->templateText($data['tekst'],$crmData);
          $this->pdf->SetFont($data['fontName'],$data['fontStyle'],$data['fontSize']);
          $this->pdf->SetXY($data['x'],$data['y']);
          $this->pdf->multiCell($data['lineWidth'],$data['lineHeight'],$data['tekst'],$data['lineBorder'],$data['lineAlign']);
        }
      }
   
   }
   
   
   
 }
 
 function putPdf($filePath,$type)
 {
   if($type=='S')
     return $this->pdf->Output($filePath,'S');
   else
     $this->pdf->Output($filePath,$type);
 }



  function templateText($template,$data)
  { 
    foreach($data as $key=>$value)
    {
      $template=str_replace('{'.$key.'}',$value,$template);
      
    }
    $lines=explode("\n",$template);
    foreach($lines as $index=>$line)
    {
      $strippedLine=trim($line);
      if(strpos($strippedLine,'leegNietTonen}') == 1)
        unset($lines[$index]);
    }
    $template=implode("\n",$lines);
    $template=str_replace('{leegNietTonen}','',$template);
    return $template;
  }

	function getAllFields($keyValue)
	{
	  $db=new DB();
	  $data=array();
	  global $__appvar,$USR;
    $velden=array('Vermogensbeheerder','Client','Depotbank','Accountmanager','tweedeAanspreekpunt','Remisier','RapportageValuta','accountEigenaar');
    foreach($velden as $veld)
      $keyValue['*'.$veld]='';
	  if($keyValue['Vermogensbeheerder'])
	  {
	    $query="SELECT Naam as `*Vermogensbeheerder` FROM Vermogensbeheerders WHERE Vermogensbeheerder='".$keyValue['Vermogensbeheerder']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Vermogensbeheerder']=$data['*Vermogensbeheerder'];
	  }
	  if($keyValue['Client'])
	  {
	    $query="SELECT Naam as `*Client` FROM Clienten WHERE Client='".$keyValue['Client']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Client']=$data['*Client'];
	  }
	  if($keyValue['Depotbank'])
	  {
	    $query="SELECT Omschrijving as `*Depotbank` FROM Depotbanken WHERE Depotbank='".$keyValue['Depotbank']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Depotbank']=$data['*Depotbank'];
	  }
	  if($keyValue['custodian'])
	  {
	    $query="SELECT Omschrijving as `*custodian` FROM Depotbanken WHERE Depotbank='".$keyValue['custodian']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
      $keyValue['*custodian']=$data['*custodian'];
	  }    
	  if($keyValue['Accountmanager'])
	  {
	    $query="SELECT Naam as `*Accountmanager` , Handtekening as `handtekening` FROM Accountmanagers WHERE Accountmanager='".$keyValue['Accountmanager']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Accountmanager']=$data['*Accountmanager'];
      $keyValue['handtekening']=$data['handtekening'];
	  }
	  if($keyValue['tweedeAanspreekpunt'])
	  {
	    $query="SELECT Naam as `*tweedeAanspreekpunt` , Handtekening as `tweedeHandtekening` FROM Accountmanagers WHERE Accountmanager='".$keyValue['tweedeAanspreekpunt']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*tweedeAanspreekpunt']=$data['*tweedeAanspreekpunt'];
      $keyValue['tweedeHandtekening']=$data['tweedeHandtekening'];
	  }
	  if($keyValue['Remisier'])
	  {
	    $query="SELECT Naam as `*Remisier` FROM Remisiers WHERE Remisier='".$keyValue['Remisier']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Remisier']=$data['*Remisier'];
	  }
 	  if($keyValue['accountEigenaar'])
	  {
	    $query="SELECT Naam as `*accountEigenaar` FROM Gebruikers WHERE Gebruiker='".$keyValue['accountEigenaar']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*accountEigenaar']=$data['*accountEigenaar'];
	  }    
	  if($keyValue['RapportageValuta'])
	  {
	    $query="SELECT Omschrijving as `*RapportageValuta` FROM Valutas WHERE Valuta='".$keyValue['RapportageValuta']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*RapportageValuta']=$data['*RapportageValuta'];
	  }
	  $keyValue['huidigeDatum']=date("j")." ".$__appvar["Maanden"][date("n")]." ".date("Y");
	  $keyValue['huidigeGebruiker']=$USR;
    
  	$query="SELECT Naam,titel FROM Gebruikers WHERE Gebruiker='".$USR."'";
	  $db->SQL($query);
	  $data=$db->lookupRecord();
	  $keyValue['GebruikerNaam']=$data['Naam'];  
    $keyValue['GebruikerTitel']=$data['titel'];  
  
	  return $keyValue;
	}
}
?>