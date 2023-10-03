<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/11/23 14:02:27 $
 		File Versie					: $Revision: 1.6 $
 		
 		$Log: AE_cls_pdfBase.php,v $
 		Revision 1.6  2014/11/23 14:02:27  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/11/13 07:09:56  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/11/05 16:49:01  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/08/30 16:52:42  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2006/01/05 16:00:09  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2005/12/16 14:43:09  jwellner
 		classes aangepast
 		
 		Revision 1.6  2005/12/01 13:27:49  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2005/11/23 19:24:35  cvs
 		*** empty log message ***
 		
 	
*/
require_once("AE_cls_html2fpdf.php");

class PDFbase extends HTML2FPDF
{
  var $legends;
  var $wLegend;
  var $sum;
  var $NbVal;

	var $tablewidths;
	var $marge = 15;
	var $widths;
	var $aligns;
	var $mutlipageHeader = true;
	var $supressFooter   = false;
	
    
  function Header()
  {
    if (function_exists("AlteredHeader"))
	    AlteredHeader();
	  else  
	  {
      if (!$this->mutlipageHeader AND $this->PageNo() > 1)
        return;
  
      global $__appvar;
      if($__appvar["bedrijf"]=='HOME')
      {
		  $this->SetY(10);
		  $this->SetFont("Arial","b",12);
      $this->SetX(100);
		  $this->Cell(100,4, 'Asset Information & Registration Services B.V.' ,0,0,"R");
      if(is_file('../html/rapport/logo/AIRS_logo.jpg'))
		    $this->Image('../html/rapport/logo/AIRS_logo.jpg',10,8,33);
		  $this->SetX(10);
		  $this->SetY(15);
		  $this->SetFont("Arial","",10);
		  $this->MultiCell(190,4,"Prins Johan Frisoplaats 5\n4196 AC   TRICHT\nT. + 31 (0)  345 - 57 01 67\nE. info@airs.nl",0,'R');
		  $this->ln();
		  $this->tMargin = $this->GetY()+4;
      }
      else
      {
      $db=new DB();
      $query="SELECT
Vermogensbeheerders.Vermogensbeheerder,
Vermogensbeheerders.Naam,
Vermogensbeheerders.Adres,
Vermogensbeheerders.Woonplaats,
Vermogensbeheerders.Telefoon,
Vermogensbeheerders.Fax,
Vermogensbeheerders.Email,
Vermogensbeheerders.Logo
FROM
Vermogensbeheerders limit 1";
      $db->SQL($query);
      $data=$db->lookupRecord();

      
		  $this->SetY(10);
		  $this->SetFont("Arial","b",12);
      $this->SetX(100);
		  $this->Cell(100,4, $data['Naam'] ,0,0,"R");
      if(is_file('../html/logo/'.$data['logo']))
		    $this->Image('../html/logo/'.$data['logo'],10,8,33);
		  $this->SetX(10);
		  $this->SetY(15);
		  $this->SetFont("Arial","",10);
		  $this->MultiCell(190,4, $data['Adres']."\n".$data['Woonplaats']."\n".$data['Telefoon'],0,'R');
		  $this->ln();
		  $this->tMargin = $this->GetY()+4;
      }
	  }  
  }
  
	//Page footer
	function Footer()
	{
	  if (function_exists("AlteredFooter"))
	    AlteredFooter();
	  else  
	  {
      $this->Line(10 ,284, 200,284);
      $this->ln();
      $this->SetX(10);
      $this->SetY(287);
      $this->Cell(190,4, "pagina ".$this->PageNo() ,0,0,"R");
	  }  
	}
  
	function addBodyText($message="",$align="L")
	{
	  $alignArray = array("L","R","C","J");
	  if (!in_array(strtoupper($align),$alignArray))
	    $align = "L";  // reset align to Left if invalid value passed
		$this->MultiCell(0,4,$message,0,$align);
	}
    
	function SetTableWidths($w)
	{
	    //Set the array of column widths
	    $this->widths=$w;
	}
	
	function SetTableAligns($a)
	{
	    //Set the array of column alignments
	    $this->aligns=$a;
	}
	
	function AddTableRow($data)
	{
	    //Calculate the height of the row
	    $nb=0;
	    for($i=0; $i < count($data) ;$i++)
	    {
        $nb = max($nb, $this->NbLines($this->widths[$i],$data[$i]));
	    }  
	    $h=4*$nb;
	    //Issue a page break first if needed
	    $this->CheckPageBreak($h);
	    //Draw the cells of the row
	    for($i=0;$i<count($data);$i++)
	    {
	        $w=$this->widths[$i];
	        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

	        //Save the current position
	        $x=$this->GetX();
	        $y=$this->GetY();
	        //Draw the border
	        //$this->Rect($x,$y,$w,$h);
	        //Print the text
	        $lines = $this->NbLines($this->widths[$i],$data[$i]);
	        // fill lines

	        $this->MultiCell($w,4,$data[$i],$line,$a);
	        //Put the position to the right of the cell
	        $this->SetXY($x+$w,$y);
	    }
	    //Go to the next line
	    $this->Ln($h);
	}
	
	function CheckPageBreak($h)
	{
	    //If the height h would cause an overflow, add a new page immediately
	    if($this->GetY()+$h>$this->PageBreakTrigger)
	        $this->AddPage($this->CurOrientation);
	}
	
	function NbLines($w,$txt)
	{
	    //Computes the number of lines a MultiCell of width w will take
	    $cw = &$this->CurrentFont['cw'];
	    if ($w == 0)
	      $w = $this->w-$this->rMargin-$this->x;
	      
	    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	    $s=str_replace("\r",'',$txt);
	    $nb=strlen($s);
	    if($nb>0 and $s[$nb-1]=="\n")
	        $nb--;
	    $sep=-1;
	    $i=0;
	    $j=0;
	    $l=0;
	    $nl=1;
	    while($i<$nb)
	    {
	      $c = $s[$i];
	      if ( $c=="\n" )
	      {
	        $i++;
	        $sep = -1;
	        $j = $i;
	        $l = 0;
	        $nl++;
	        continue;
	      }
	      if($c==' ')
	        $sep=$i;

	      $l +=$cw[$c];
	      if ($l>$wmax)
	      {
	        if ($sep == -1)
	        {
	          if($i==$j)
	            $i++;
	        }
	        else
	          $i=$sep+1;
	        $sep=-1;
	        $j=$i;
	        $l=0;
	        $nl++;
	      }
	      else
	        $i++;
	    }
	    return $nl;
	}
	
  function setDash($black=false,$white=false)
  {
      if($black and $white)
          $s=sprintf('[%.3f %.3f] 0 d',$black*$this->k,$white*$this->k);
      else
          $s='[] 0 d';
      $this->_out($s);
  }
    
  
	function OutputCSV($filename, $type)
	{
		if($fp = fopen($filename,"w+"))
		{
			$exceldata = generateCSV($this->excelData);
			fwrite($fp,$exceldata);
			fclose($fp);
		}
		else 
		{
			echo "Fout: kan niet schrijven naar ".$filename;
		}
	}
		
}

?>