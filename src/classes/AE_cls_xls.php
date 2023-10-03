<?php

class AE_xls
{
  function AE_xls($file='')
  {
    include_once(realpath(dirname(__FILE__)).'/excel/Writer.php');
    $this->workbook = new Spreadsheet_Excel_Writer($file);//$filename
    $this->xlsHeader = array ();
    $this->xlsData = array();
    $this->excelOpmaak= array();
    $this->mergeCells = array();
    $this->setColumn = array();
    $this->worksheet = array();
    $this->portrait=false;
  }

  function setData($data)
  {
    $this->xlsData = $data;
  }


  function writetab($name,$data)
  {
    $this->worksheet[$name] =& $this->workbook->addWorksheet($name);
    if($this->portrait==true)
      $this->worksheet[$name]->setPortrait();
    else
      $this->worksheet[$name]->setLandscape();
    $this->xlsData = $data;
    $this->writeData($this->workbook,$this->worksheet[$name]);
  }

function writeData($workbook,$worksheet)
{
  $opmaak=array();
  $this->excelOpmaak['date']=array('setNumFormat'=>'DD-MM-YYYY');
  foreach($this->excelOpmaak as $opmaakSleutel=>$eigenschappen)
  {
    $opmaak[$opmaakSleutel] =& $workbook->addFormat();
    foreach($eigenschappen  as $eigenschap=>$value)
    {
      $opmaak[$opmaakSleutel]->$eigenschap($value);
    }
  }

  for($regel = 0; $regel < count($this->xlsData); $regel++ )
  {
    for($col = 0; $col < count($this->xlsData[$regel]); $col++)
    {
      if (is_array($this->xlsData[$regel][$col]))
      {
        $celOpmaak = $this->xlsData[$regel][$col][1]; //1=opmaak
        $worksheet->write($regel, $col, $this->xlsData[$regel][$col][0],$opmaak[$celOpmaak]);	//0=waarde
      }
      else
      {
        $waarde=$this->xlsData[$regel][$col];
        $worksheet->write($regel, $col, $waarde);
      }
    }
  }

  foreach ($this->mergeCells as $mergeData)
  {
    $worksheet->mergeCells($mergeData[0], $mergeData[1], $mergeData[2], $mergeData[3]);
  }

  foreach ($this->setColumn as $column)
  {
    $worksheet->setColumn($column[0],$column[1],$column[2],$column[3],$column[4],$column[5]);//$firstcol, $lastcol, $width, $format = null, $hidden = 0, $level = 0
  }

}
  function OutputXls($filename='file.xls',$toFile=false,$fileFormat='xls')
	{
    global $__appvar;

    if($toFile==true)
    {
      $file=$filename;
      $filename=false;
    }

    if($fileFormat=='xlsx')
    {
      writeXlsx($this->xlsData,$filename);
    }
    else
    {
	    include_once(realpath(dirname(__FILE__)).'/excel/Writer.php');
		  $this->workbook = new Spreadsheet_Excel_Writer($file);//$filename
      $worksheet =& $this->workbook ->addWorksheet();
      if($this->portrait==true)
        $worksheet->setPortrait();
      else
        $worksheet->setLandscape();
      
       $this->writeData($this->workbook ,$worksheet);

      if($filename)
        $this->workbook->send($filename);
      $this->workbook->close();
    }
	}

}
?>