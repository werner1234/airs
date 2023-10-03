<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.9 $

$Log:
*/
class PDFOptieOverzicht extends FPDF
{
  var $legends;
  var $wLegend;
  var $sum;
  var $NbVal;
  var $rowHeight = 4;

	var $tablewidths;
	var $marge;
	var $widths;
	var $aligns;

  function Header()
  {
		switch ($this->rapport_type)
		{
			case "optieExpiratieLijst" :
				$this->SetFont("Times","b",16);
  			$this->SetX($this->marge);
				$this->Cell(200,4, vertaalTekst("Optie expiratie lijst ", $this->rapport_taal).vertaalTekst($this->__appvar["Maanden"][$this->OptieExpMaand],$this->taal)." ".$this->OptieExpJaar);
				$this->SetX(250);
				$this->SetFont("Times","",10);
				$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Rapportagedatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
				$this->ln();
				$this->SetFont("Times","b",10);
				$this->SetWidths(array(10,20,30,60,50,25,20,20,30));
				$this->SetAligns(array("R","L","L","L","L","R","R","R","R"));
				$this->row(array("","Portefeuille","Client","Naam","Fonds","Fonds koers","Aantal","Koers","Waarde in Euro"));
				$this->Line($this->marge ,$this->GetY(), $this->marge + 265,$this->GetY());
				$this->SetFont("Times","",10);
				
			break;
			case "OptieGeschrevenPositie" :
				$this->SetFont("Times","b",16);
  			$this->SetX($this->marge);
				$this->Cell(200,4, vertaalTekst("Geschreven call posities", $this->rapport_taal) ,0,0,"L");
				$this->SetX(250);
				$this->SetFont("Times","",10);
				$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Rapportagedatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
				$this->ln();
				$this->SetFont("Times","b",10);
				$this->SetWidths(array(25,40,50,25,10,50,15,35));
				$this->SetAligns(array("L","L","L","R","L","L","R","R"));
				$this->row(array("Portefeuille", "Client", "Fonds", "Aantal","", "Optie", "Aantal", "% geschreven calls"));
				$this->Line($this->marge ,$this->GetY(), $this->marge + 265,$this->GetY());
				$this->SetFont("Times","",10);
			break;
			case "OptieOngedektePositie" :
				$this->SetFont("Times","b",16);
  			$this->SetX($this->marge);
				$this->Cell(200,4, vertaalTekst("Ongedekte positie", $this->rapport_taal) ,0,0,"L");
				$this->SetX(250);
				$this->SetFont("Times","",10);
				$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Rapportagedatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
				$this->ln();
				$this->SetFont("Times","b",10);
				$this->SetWidths(array(25,40,50,25,10,50,15,35));
				$this->SetAligns(array("L","L","L","R","L","L","R","R"));
				$this->row(array("Portefeuille", "Client", "Fonds", "Aantal","", "Optie", "Aantal", "% geschreven calls"));
				$this->Line($this->marge ,$this->GetY(), $this->marge + 265,$this->GetY());
				$this->SetFont("Times","",10);
			break;	
			case "OptieVrijePositie" :
				$this->SetFont("Times","b",16);
  				$this->SetX($this->marge);
				$this->Cell(200,4, vertaalTekst("Vrije Positie", $this->rapport_taal). " ". $this->Fonds ,0,0,"L");
				$this->SetX(250);
				$this->SetFont("Times","",10);
				$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Rapportagedatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
				$this->ln();
				$this->SetFont("Times","b",10);
				$this->SetX(175);
				$this->Cell(40,4, "Geschreven Positie" ,0,0,"L");
				$this->SetX(225);
				$this->Cell(40,4, "Vrije Positie" ,0,0,"L");
				$this->ln();
				$this->SetWidths(array(25,40,25,10,50,25,25,25,25));
				$this->SetAligns(array("L","L","R","L","R","R","R","R","R"));
				$this->row(array("Portefeuille", "Client", "Aantal","", "Optie", "Absoluut","Percentage","Absoluut","percentage"));
				$this->Line($this->marge ,$this->GetY(), $this->marge + 265,$this->GetY());
				$this->SetFont("Times","",10);
			break;		
			case "OptieLiquideRuimte" :
				$this->SetFont("Times","b",16);
  			$this->SetX($this->marge);
        if($this->soort=='OptiePutExposure')
          $this->Cell(200,4, vertaalTekst("Overzicht put-exposure", $this->rapport_taal) ,0,0,"L");
        else
		  		$this->Cell(200,4, vertaalTekst("Liquide ruimte geschreven puts", $this->rapport_taal) ,0,0,"L");
				$this->SetX(250);
				$this->SetFont("Times","",10);
				$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Rapportagedatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
				$this->ln();
				$this->SetFont("Times","b",10);
				$this->SetWidths(array(25,40,50,25,10,50,15,25,25));
				$this->SetAligns(array("L","L","L","R","L","L","R","R","R"));
				$this->row(array("Portefeuille", "Client", "Fonds/Rekening", "Aantal","", "Optie", "Aantal", "Uitgaven EUR", "Waarde EUR"));
				$this->Line($this->marge ,$this->GetY(), $this->marge + 265,$this->GetY());
				$this->SetFont("Times","",10);
			break;				


			default :
			break;
		}
  }

	//Page footer
	function Footer()
	{
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
	{
		if($fp = fopen($filename,"w+"))
		{
			$csvdata = generateCSV($this->excelData);
			fwrite($fp,$csvdata);
			fclose($fp);
		}
		else 
		{
			echo "Fout: kan niet schrijven naar ".$filename;
		}
		
	}
  
 	function OutputXls($filename,$type="S",$fileFormat)
	{
		global $__appvar;
		if($fileFormat=='xlsx')
		{
			writeXlsx($this->excelData,$filename);
		}
		else
		{
			include_once('../classes/excel/Writer.php');

			$workbook = new Spreadsheet_Excel_Writer($filename);
			$worksheet =& $workbook->addWorksheet();

			$this->excelOpmaak['date'] = array('setNumFormat' => 'DD-MM-YYYY');
			while (list($opmaakSleutel, $eigenschappen) = each($this->excelOpmaak))
			{
				$opmaak[$opmaakSleutel] =& $workbook->addFormat();
				while (list($eigenschap, $value) = each($eigenschappen))
				{
					$opmaak[$opmaakSleutel]->$eigenschap($value);
				}

			}

			for ($regel = 0; $regel < count($this->excelData); $regel++)
			{
				for ($col = 0; $col < count($this->excelData[$regel]); $col++)
				{
					if (is_array($this->excelData[$regel][$col]))
					{
						//$opmaak[$opmaakSleutel]
						$celOpmaak = $this->excelData[$regel][$col][1]; //1=opmaak
						$worksheet->write($regel, $col, $this->excelData[$regel][$col][0], $opmaak[$celOpmaak]);  //0=waarde
					}
					else
					{
						$waarde = $this->excelData[$regel][$col];
						$worksheet->write($regel, $col, $waarde);
					}
				}
			}

			$workbook->close();
		}
	}

 	function fillXlsSheet($worksheet)
	{
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
		       $worksheet->write($regel, $col, $this->excelData[$regel][$col]);	
		     }
		   }
	   }
	} 	

}
?>
