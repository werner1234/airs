<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/01/17 18:17:14 $
File Versie					: $Revision: 1.9 $

$Log:
*/

include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class PDFFactuur extends FPDF
{
  var $legends;
  var $wLegend;
  var $sum;
  var $NbVal;

	var $tablewidths;
	var $marge;
	var $widths;
	var $aligns;
	var $rowHeight=4;


  function Header()
  {
  	if ($this->rapport_layout == 5 && is_file($this->rapport_factuurHeader))
			$this->Image($this->rapport_factuurHeader, 0, 10, 210, 34);
  }

	//Page footer
	function Footer()
	{
	  global $__appvar;

	  if ($this->rapport_layout == 5 && is_file($this->rapport_factuurFooter))
	  {
			  $this->Image($this->rapport_factuurFooter, 5, 255, 200, 37);
	  }
	  if(isset($this->toonProductieDatum))
	  {
	    $this->SetY(290);
	    $this->Line($this->lMargin,$this->GetY()-1,210-$this->lMargin,$this->GetY()-1);
	    $this->SetFont('Arial','',8);
	    $dagen=array('zondag','maandag','dinsdag','woensdag','donderdag','vrijdag','zaterdag');
      $this->Cell(100,4,"Datum: ".$dagen[date('w')].date(" d ").$__appvar['Maanden'][date("n")].date(" Y H:i:s"));
	  }

	}


	function SetWidths($w)
	{
	    //Set the array of column widths
	    $this->widths=$w;
	}

	function SetAligns($a)
	{
	    //Set the array of column alignments
	    $this->aligns=$a;
	}

function Row($data)
	{
	    //Calculate the height of the row
	    $nb=0;
	    for($i=0;$i<count($data);$i++)
	        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
	    $h=$this->rowHeight*$nb;

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

	        $this->MultiCell($w,$this->rowHeight,$data[$i],$line,$a,$this->fillCell[$i]);

	        if($this->CellBorders[$i])
	        {
	          $borders = array();
	          if(is_array($this->CellBorders[$i]))
	            $borders = $this->CellBorders[$i];
	          else
	            $borders[] = $this->CellBorders[$i];

	          foreach ($borders as $border)
	          {
	            if($border == 'U')
	              $this->Line($x,$y+$h,$x+$w,$y+$h);
	            elseif($border == 'T')
	              $this->Line($x,$y,$x+$w,$y);
	            elseif($border == 'L')
	              $this->Line($x,$y,$x,$y+$h);
	            elseif($border == 'R')
	              $this->Line($x+$w,$y,$x+$w,$y+$h);
	            elseif($border == 'UU')
	            {
	              $shink = $w-$w*$this->underlinePercentage;
	              $this->Line($x+$shink,$y+$h,$x+$w,$y+$h);
	              $this->Line($x+$shink,$y+$h+1,$x+$w,$y+$h+1);
	            }
	          }
	        }
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
	    $cw=&$this->CurrentFont['cw'];
	    if($w==0)
	        $w=$this->w-$this->rMargin-$this->x;
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
	        $c=$s[$i];
	        if($c=="\n")
	        {
	            $i++;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	            continue;
	        }
	        if($c==' ')
	            $sep=$i;
	        $l+=$cw[$c];
	        if($l>$wmax)
	        {
	            if($sep==-1)
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
	{echo 'output csv';
    if ($this->nullenOnderdrukken == 1)
    {
	   $nulldata = array();//Loop over array om nullen te bepalen.
	   for($regel = 1; $regel < count($this->excelData); $regel++ )
	   {
		    for($col = 0; $col < count($this->excelData[$regel]); $col++)
		    {
			    if ($this->excelData[$regel][$col] != '0' && $this->excelData[$regel][$col] != '')
			    {
				    $nulldata[$col]="1";
			    }
		    }
	   }
	   $dataZonderNul = array();//Kopie van array maken zonder de nullen
	   for($regel = 0; $regel < count($this->excelData); $regel++ )
	   {
		   for($col = 0; $col < count($this->excelData[$regel]); $col++)
		   {
			   if ($nulldata[$col] == "1")
			   {
			   $dataZonderNul[$regel][]=$this->excelData[$regel][$col];
			   }
		   }
	   }
	   $this->excelData = $dataZonderNul;
    }


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

	function OutputXls($filename,$type="S")
	{
		$workbook = new Spreadsheet_Excel_Writer($filename);
    $worksheet =& $workbook->addWorksheet();
    $this->excelOpmaak['date']=array('setNumFormat'=>'DD-MM-YYYY');
    while(list($opmaakSleutel,$eigenschappen)=each($this->excelOpmaak))
    {
        $opmaak[$opmaakSleutel] =& $workbook->addFormat();
        while(list($eigenschap,$value)=each($eigenschappen))
        {
          $opmaak[$opmaakSleutel]->$eigenschap($value);
        }

    }
	   for($regel = 0; $regel < count($this->excelData); $regel++ )
	   {
		   for($col = 0; $col < count($this->excelData[$regel]); $col++)
		   {
		     if (is_array($this->excelData[$regel][$col]))
		     {
		       //$opmaak[$opmaakSleutel]
		       $celOpmaak = $this->excelData[$regel][$col][1]; //1=opmaak
		       $worksheet->write($regel, $col, $this->excelData[$regel][$col][0],$opmaak[$celOpmaak]);	//0=waarde
		     }
		     else
		     {
		       $waarde=$this->excelData[$regel][$col];
		       $worksheet->write($regel, $col, $waarde);
		     }
		   }
	   }

//$worksheet->write(3, 0, '11');
//$worksheet->write(4, 0, '21');
//$worksheet->write(6, 0, '=SUM(A4,A5)');
//$worksheet->writeFormula(5,0, "=SUM(A4,A5)");


	   /*
$format_bold =& $workbook->addFormat();
$format_bold->setBold();

$format_title =& $workbook->addFormat();
$format_title->setBold();
$format_title->setBgColor('yellow');
$format_title->setPattern(1);
$format_title->setFgColor('blue');

$worksheet =& $workbook->addWorksheet();
$worksheet->write(0, 0, "Quarterly Profits for Dotcom.Com", $format_title);
// While we are at it, why not throw some more numbers around
$worksheet->write(1, 0, "Quarter", $format_bold);
$worksheet->write(1, 1, "Profit", $format_bold);
$worksheet->write(2, 0, "Q1");
$worksheet->write(2, 1, 0);
$worksheet->write(3, 0, "Q2");
$worksheet->write(3, 1, 0);


$worksheet =& $workbook->addWorksheet('My first worksheet');

$worksheet->write(0, 0, 'Name');
$worksheet->write(0, 1, 'Age');
$worksheet->write(1, 0, 'John Smith');
$worksheet->write(1, 1, 30);
$worksheet->write(2, 0, 'Johann Schmidt');
$worksheet->write(2, 1, 31);
$worksheet->write(3, 0, 'Juan Herrera');
$worksheet->write(3, 1, 32);
*/


// Let's send the file
$workbook->close();


	}
}
?>
