<?php
/*
    AE-ICT sourcemodule created 24 jan. 2020
    Author              : Chris van Santen
    Filename            : AE_cls_fpdi.php
		standalone PDF tbv handelzeker API call 8314

*/
include_once "fpdi/fpdf.php";
include_once "fpdi/fpdi2.php";
class AE_cls_fpdi extends FPDI2
{
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
