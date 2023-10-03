<?
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2008/05/16 08:18:19 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: rapportBrief.php,v $
 		Revision 1.1  2008/05/16 08:18:19  rvv
 		*** empty log message ***
 		
 	
*/
include_once($__appvar["basedir"]."/classes/AE_cls_html2fpdfRapport.php");

class kwartaalBrief extends html2fpdfRapport
{    
	
	function kwartaalBrief($pdf)
	{
	  $this->pdf = &$pdf;
	  $this->html2fpdfRapport('P','mm','A4');
    $this->pdf->rapport_type = "BRIEF";
	}
	
	function template($data,$templateVars)
	{
	  while ( list( $key, $val ) = each( $templateVars ) ) 
		{
			$data = str_replace( "{".$key."}", $val, $data); 
		}
	  $data = eregi_replace( "\{[a-zA-Z0-9_-]+\}", "", $data); 
	 return $data;
	}
	
	function maakBrief($brief='kwartaalBrief',$templateVariabelen = array())
	{
	  $this->pdf->AddPage('P');
    $cfg = new AE_config();
    $briefData = $cfg->getData($brief);
    $briefData = $this->template($briefData,$templateVariabelen);
    $this->WriteHTML($briefData);
    if ($this->pdf->rapport_font)
     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->skipFooter = true; 
	}
}
?>