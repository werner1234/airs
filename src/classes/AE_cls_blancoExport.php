<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/06/10 13:39:39 $
 		File Versie					: $Revision: 1.3 $

 		$Log: AE_cls_blancoExport.php,v $
 		Revision 1.3  2020/06/10 13:39:39  cvs
 		call 8517
 		
 		Revision 1.2  2020/05/04 11:45:05  cvs
 		call 8517
 		
 		Revision 1.1  2020/04/24 06:37:35  cvs
 		call 8517
 		
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


class blancoExport
{
  var $fieldsPerLine;
  var $lineBuffer;
  var $outputArray;
  var $linefeed;
  var $fieldDelimiter;
  var $filepath;
  var $outputType = "F";
  var $blancoGrootBoek = array(
    "BUY",
    "SELL",
    "DEPOSIT",
    "WITHDRAW",
    "CASH DIVIDEND",
    "RFOP",
    "DFOP",
    "IIT",
    "ICT"
  );
  // $grootboekMapping
  //  "airsgb" => ('gb-','gb+','Perf. Impact')
  var $grootboekMapping = array(
    "BEH"     => array("WITHDRAW","DEPOSIT","TRUE"),
    "BEW"     => array("WITHDRAW","DEPOSIT","TRUE"),
    "DIV"     => array("CASH DIVIDEND","CASH DIVIDEND","TRUE"),
    "DIVBE"   => array("CASH DIVIDEND","CASH DIVIDEND","TRUE"),
    "FONDS-A" => array("BUY","BUY","TRUE"),
    "FONDS-V" => array("SELL","SELL","TRUE"),
    "FONDS-D" => array("RFOP","RFOP","TRUE"),
    "FONDS-L" => array("DFOP","DFOP","TRUE"),
//    "FONDS-B" => array("",""),
    "KNBA"    => array("WITHDRAW","WITHDRAW","TRUE"),
    "KOBU"    => array("WITHDRAW","WITHDRAW","TRUE"),
    "ONTTR"   => array("WITHDRAW","WITHDRAW","FALSE"),
    "RENME"   => array("WITHDRAW","WITHDRAW","TRUE"),
    "RENOB"   => array("CASH DIVIDEND","CASH DIVIDEND","TRUE"),
    "RENTE"   => array("WITHDRAW","DEPOSIT","TRUE"),
    "STORT"   => array("DEPOSIT","DEPOSIT","FALSE"),
    "ROER"    => array("WITHDRAW","DEPOSIT","TRUE"),
    "TOB"     => array("WITHDRAW","DEPOSIT","TRUE"),
    "BTLBR"   => array("WITHDRAW","DEPOSIT","TRUE"),
    "VALK"    => array("WITHDRAW","DEPOSIT","TRUE"),
    "FDFEE"   => array("WITHDRAW","DEPOSIT","TRUE"),
    "UITK"    => array("CASH DIVIDEND","CASH DIVIDEND"),
  );

  var $fieldHeader =array(
    "Product Reference Name",
    "Party Reference Value",
    "Counter Party Reference Value",
    "No of Units",
    "Unit Price",
    "Transaction Date",
    "Settlement Date",
    "Instrument Reference Name",
    "Instrument Reference Value",
    "ISIN",
    "Currency",
    "Performance Impact",
    "Type",
    "Exchange Rate",
    "Amount",
    "Accrued Interest",
    "External Execution Id",
    "Reverse reference",
    "Settlement Net Amount",
    "Settlement Vat",
    "Settlement Exchange Tax",
    "Settlement Stamp Duty",
    "Settlement Withholding Tax",
    "Settlement External Costs",
    "Settlement Commission",
    "Comments"
  );

  function blancoExport()
  {

    $this->fieldsPerLine = count($this->fieldHeader);
    $this->fieldDelimiter = ",";
    $this->clearBuffer();
    $this->linefeed = "\n";
    $this->outputArray = array();

    foreach($this->fieldHeader as $val)
    {
      $this->addField($val, $val);
    }
    $this->pushBuffer();
    $cfg = new AE_config();
    $this->filepath = getcwd()."/output/";
    $this->useCombineRows = false;

  }

  function num($input, $dec=2)
  {
    return round($input, $dec);
  }
  function blancoDate($in)
  {
    $s = explode("-",substr($in,0,10));
    return $s[2]."/".$s[1]."/".$s[0];
  }

  function mapGrootBoek($gb, $bedrag)
  {
    $indx = ($bedrag < 0 )?0:1;
    return array(
      "gb" =>$this->grootboekMapping[$gb][$indx],
      "pi" =>$this->grootboekMapping[$gb][2],
    );
  }

  function getValue($index)
  {
    $fldCol = array_search($index, $this->fieldHeader);
    return $this->lineBuffer[$fldCol];
  }

  function addField($index, $value, $format="")
  {
    $value = (is_null($value))?"":$value;
    $fldCol = array_search($index, $this->fieldHeader);
    $fType = explode("@", $format);
    switch (strtoupper($fType[0]))
    {
      case "N":
        $dec = ($fType[1])?$fType[1]:2;

        $out = round($value, $dec);
        break;
      case "D":
        $out = $this->blancoDate($value);
        break;
      default:
        $out = $value;
    }
    $this->lineBuffer[$fldCol] = $out;

  }

  function clearBuffer()
  {
    $this->lineBuffer = array();
    $this->lineBuffer = array_fill (0,$this->fieldsPerLine,"");
  }

  function pushBuffer()
  {
    $this->outputArray[] = $this->lineBuffer;
    $this->clearBuffer();
  }

  function makeCsv($filePrefix)
  {
    $outputStr = "";
    $filename = $this->filepath.$filePrefix."_".date("Ymd-His").".csv";
//    debug($this->outputArray);
    for ($x=0; $x < count($this->outputArray); $x++)
    {
      $lineArray = $this->outputArray[$x];
//      debug($lineArray);
      $line = "";

      for ($l=0; $l < $this->fieldsPerLine; $l++)
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
    debug($filename);
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
