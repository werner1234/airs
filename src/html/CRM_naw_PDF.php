<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2007/10/09 06:27:49 $
 		File Versie					: $Revision: 1.1 $

 		$Log: CRM_naw_PDF.php,v $
 		Revision 1.1  2007/10/09 06:27:49  cvs
 		CRM update DGC
 		


*/


//$AEPDF2=true;
include_once("wwwvars.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once($__appvar["basedir"]."/classes/AE_cls_fpdf.php");
include_once($__appvar["basedir"]."/classes/AE_cls_html2fpdf.php");

if (!$_GET["deb_id"] )
{
  echo "foute aanroep";
  exit();
}


function myTemplate()
{
    global $NAWobject,$__appvar;
    $file = $__appvar['basedir']."/html/CRM_naw_PDFTemplate.html";
      
	  $data = read_file($file);
		// extra formvars
		reset($NAWobject->formVars);
		while ( list( $key, $val ) = each($NAWobject->formVars ) ) 
		{
			$data = str_replace( "{".$key."}", $val, $data); 
		}
		
		// replace data elements
		reset($NAWobject->data['fields']);
		while ( list( $name, $field ) = each( $NAWobject->data[fields] ) ) 
		{ 
			
	  	while ( list( $key, $val ) = each( $field ) ) 
	  	{ 
	    	if(is_string($val))
	    	{
    		  $data = str_replace( "{".$name."_".$key."}", htmlspecialchars($val), $data); 
	    	}	  
	  	}         
 		}
 		
		$data = eregi_replace( "\{[a-zA-Z0-9_-]+\}", "", $data); 
	  return $data;
}

$NAWobject = new Naw();
$NAWobject->getById($_GET["deb_id"]);




class Brief extends HTML2FPDF
{    
  var $legends;
  var $wLegend;
  var $sum;
  var $NbVal;

	var $tablewidths;
	var $marge;
	var $widths;
	var $aligns;
	
	
	
  function Header()
  {
  }
  
	//Page footer
	function Footer()
	{
	}

 
  function maakBrief($pdf)
  {
	 
  }

} 

$pdf = new Brief();

   $pdf->AddPage();
	 
   
   $briefdata = myTemplate();
   $pdf->WriteHTML($briefdata);
   $pdf->Output();
   echo $briefdata;


?>