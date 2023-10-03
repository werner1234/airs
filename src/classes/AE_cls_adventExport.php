<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/09/25 15:21:52 $
 		File Versie					: $Revision: 1.7 $

 		$Log: AE_cls_adventExport.php,v $
 		Revision 1.7  2017/09/25 15:21:52  cvs
 		call 6203
 		
 		Revision 1.6  2017/09/22 14:11:08  cvs
 		call 6203
 		
 		Revision 1.5  2016/03/16 12:53:16  cvs
 		call 4124
 		
 		Revision 1.4  2014/05/02 08:45:55  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2014/04/04 09:03:41  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2014/02/05 15:29:46  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2013/12/11 10:09:14  cvs
 		*** empty log message ***



*/


class adventExport
{
  var $fieldsPerLine;
  var $lineBuffer;
  var $outputArray1;
  var $outputArray2;
  var $outputArray3;
  var $linefeed;
  var $fieldDelimiter;
  var $filepath;
  var $outputType = "F";
  var $useCombineRows;

  function adventExport()
  {
    $this->fieldsPerLine = 83;
    $this->fieldDelimiter = ",";
    $this->clearBuffer();
    $this->linefeed = "\n";
    $this->outputArray1 = array();
    $this->outputArray2 = array();
    $this->outputArray3 = array();
    $cfg = new AE_config();
    $this->filepath = $cfg->getData("advent_outputDir")."/";
    $this->useCombineRows = false;
  }

  function addField($index, $value)
  {
    $this->lineBuffer[$index] = $value;
  }

  function addValutaFields($rec)
  {
    $fondsValuta   = substr($this->lineBuffer[4],-2);
    $afrekenValuta = substr($this->lineBuffer[12],-2);
    if ($afrekenValuta == "") $afrekenValuta = "eu";
    if ($afrekenValuta == "me")
    {
      $afrekenValuta = $fondsValuta;
      $this->addField(12, $fondsValuta);
      if ($fondsValuta <> "eu")
      {
        $this->addField(14,round(1/$rec["Valutakoers"],7));
        $this->addField(15,round(1/$rec["Valutakoers"],7));
        $this->addField(82,$fondsValuta);
        $this->addField(83,"eu");
      }
    }

    if ( $fondsValuta <> $afrekenValuta 
          OR 
         ($fondsValuta == $afrekenValuta AND $fondsValuta <> "eu") )
    {
      $this->addField(14,round(1/$rec["Valutakoers"],7));
      $this->addField(15,round(1/$rec["Valutakoers"],7));
      $this->addField(82,$fondsValuta);
      $this->addField(83,"eu");
    }

  }

  function addValutaKoersFields($valuta,$valutaKoers)  // niet gebruikt???
  {
    if ($valuta <> "eu")
    {
      $this->addField(14,round(1/$valutaKoers,7));
      $this->addField(15,round(1/$valutaKoers,7));
    }
  }


  function clearBuffer()
  {
    $this->lineBuffer = array();
  }

  function pushBuffer($block="1")
  {
    switch ($block)
    {
      case "1":
        $this->outputArray1[] = $this->lineBuffer;
        break;
      case "2":
        $this->outputArray2[] = $this->lineBuffer;
        break;
      case "3":
        $this->outputArray3[] = $this->lineBuffer;
        break;
    }

    $this->clearBuffer();
  }

  function combineRows($inArray)
  {
    $outArray = array();

    foreach($inArray as $row)
    {
      $key = "";
      for ($x=1; $x < 8; $x++)
      {
        $key .= $row[$x];
      }

      $tempArray[$key]["row"]    = $row;
      $tempArray[$key]["aantal"] += $row[8];

    }

    foreach ($tempArray as $grp)
    {
      $row = $grp["row"];
      $row[8] = $grp["aantal"];
      $outArray[] = $row;
    }

    return $outArray;
  }

  function makeCsv($filePrefix,$block="1")
  {
    $outputStr = "";
    $filename = $this->filepath.$filePrefix."_".date("Ymd-His").".csv";
    switch ($block)
    {
      case "1":
        $blockArray = $this->outputArray1;
        break;
      case "2":
        $blockArray = $this->outputArray2;
        break;
      case "3":
        $blockArray = $this->outputArray3;
        break;
    }

    if ($this->useCombineRows)
    {
      $blockArray = $this->combineRows($blockArray);
    }


    for ($x=0; $x < count($blockArray); $x++)
    {

      $lineArray = $blockArray[$x];
      $line = "";

      for ($l=1; $l <= $this->fieldsPerLine; $l++)
      {
        $value = $lineArray[$l];
        if (!$value == "" AND !isNumeric( trim($value) ) )
          $line .= '"'.$value.'"';
        else
          $line .= $value;  
        //$line .= (!isset($lineArray[$l]))?"":$lineArray[$l];
        $line .= $this->fieldDelimiter;
      }
      $line = substr($line,0, -1);  // laatste delimeter verwijderen
      $outputStr .= $line.$this->linefeed;
    }
    $parts = explode("/",$filename);
    $sfile = $parts[(count($parts)-1)];
    if (strlen($outputStr) > 0)
    {
      if ($this->outputType == "F")
      {
        file_put_contents($filename, $outputStr);
        echo "<li>bestand $sfile weggeschreven</li>";
      }
      else
      {
        header('Content-type: ' . "text/comma-separated-values");
      	header("Content-Length: ".strlen($outputStr));
      	header("Content-Disposition: inline; filename=\"".$filename."\"");
  	    header("Pragma: public");
  	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        echo $outputStr;
        exit;
      }  
    }
    else
    {
      echo "<li>leeg bestand $sfile overgeslagen</li>";
    }
  }


}
?>