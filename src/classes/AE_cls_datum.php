<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2018/12/19 15:45:01 $
 		File Versie					: $Revision: 1.5 $

 		$Log: AE_cls_datum.php,v $
 		Revision 1.5  2018/12/19 15:45:01  rm
 		6775 Orders via HTML (nominaal + beurs)
 		
 		Revision 1.4  2015/07/22 07:08:07  rm
 		datums omzetten voor oude php
 		
 		Revision 1.3  2015/07/17 12:51:06  rm
 		no message
 		
 		Revision 1.2  2015/07/15 12:28:16  rm
 		ordersV2
 		
 		Revision 1.1  2014/12/10 16:01:02  rm
 		Date class
 		
 		Revision 1.2  2014/04/29 06:24:03  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2014/04/04 13:15:03  cvs
 		update 4-4-2014
 		
*/

include_once("AE_lib2.php3");
Class AE_datum
{
  var $dagnaam    = array("zondag","maandag","dinsdag","woensdag","donderdag","vrijdag","zaterdag");
  var $dagKort    = array("zo","ma","di","wo","do","vr","za");
  
  var $maandnaam  = array("","januari","februari","maart","april","mei","juni",
                             "juli","augustus","september","oktober","november","december");
  var $maandKort  = array("","jan","feb","mrt","apr","mei","jun","jul","aug","sep","okt","nov","dec");

  var $_date = "";
  var $dateSeparator = "-";
  var $formatDateString = "j-n-Y";
  var $formatTimeString = "G:i";
  var $formatDTString   = "j-n-Y G:i";
  
  
  var $dateNotationHuman = array (
    'jjjj'    => 'Y',     //Jaren als 1900-9999
    'jj'      => 'y',     //Jaren als 00-99
    
    'dddd'    => 'l',     //Dagen als zondag-zaterdag
    'ddd'     => 'D',     //Dagen als zo-za
    'dd'      => 'd',     //Dagen als 01-31
    'd'       => 'j',     //Dagen als 1-31
  
    'mmmmm'   => 'F|1',   //Maanden als de eerste letter van de maand
    'mmmm'    => 'F',     //Maanden als januari-december
    'mmm'     => 'M',     //Maanden als jan-dec
    'mm'      => 'm',     //Maanden als 01-12
    'm'       => 'n',     //Maanden als 1-12
  );
  
  var $timeNotationHuman = array (
    'uu'      => 'G',     //Uren als 0-23
    'u'       => 'H',     //Uren als 00-23
  
    'mm'      => 'i|int', //Minuten als 0-59
    'm'       => 'i',     //Minuten als 00-59
  
    'ss'      => 's|int', //Seconden als 0-59
    's'       => 's',     //Seconden als 00-59
  );
  
  function AE_datum($date="", $format="")
  {
    if ($date == "")
     $this->_date = mktime();  //old depreciated over naar dit $this->_date = time(); 
      
    else  
      $this->setDate($date,$format);
    
    
    $this->AEFormatter = new AE_cls_formatter();
  }
  function changeFormatting($human=true)
  {
    if ($human)
    {
      $this->formatDateString = "j-n-Y";
      $this->formatTimeString = "G-i";
      $this->formatDTString   = "j-n-Y G:i";
    }
    else
    {
      $this->formatDateString = "d-m-Y";
      $this->formatTimeString = "H-i";
      $this->formatDTString   = "d-m-Y H:i";
    }
    
    return $this;
     
  }
  function setDate($date="now",$format="db")
  {
    
    if ($date == "now") 
    {
      $date = mktime();
      $format = "";
    }
    
    switch (strtolower($format))
    {
      case "db":
      case "d":
        $dateSplit = explode(" ",$date);
        if ($dateSplit[0] < 2000) // ongeldige datum
        {
          $this->_date = -1;
        }
        else
        {
          $dParts = explode($this->dateSeparator,$dateSplit[0]);
          $tParts = explode(":",$dateSplit[1]);
          $this->_date = mktime((int)$tParts[0],(int)$tParts[1],(int)$tParts[2],$dParts[1],$dParts[2],$dParts[0]);  
        }
        
        break;
      case "form":
      case "f":
        $dateSplit = explode(" ",$date); 
        $tParts = explode(":",$dateSplit[1]);
        $parts = explode($this->dateSeparator,$date);
        $this->_date = mktime((int)$tParts[0],(int)$tParts[1],(int)$tParts[2],$parts[1],$parts[0],$parts[2]);
        break;
      default:
        $this->_date = mktime();    
    }
   return $this; 
    
  }
  
  function convert($to="formDateTime", $from="internal")
  {
   
    if ($from == "internal")
    {
      if ($this->_date > 0 )
      {
        
      
        switch(strtolower($to) )
        {
          case "db":
          case "d":
            $output = date("Y-m-d H:i:s",$this->_date);
            break;
          case "formdate":
          case "date":
            $output = date($this->formatDateString,$this->_date);
            break;
          case "formtime":
          case "time":
            $output = date($this->formatTimeString,$this->_date);
            break;
          default:
            $output = date($this->formatDTString,$this->_date);
            break;  
        }
      }
      else
      {
        $output = "geen datum";
      }
    }
    else
      $output = "no yet";
    
    
    return $output;
  }
  
  function show($format="db")
  {
    echo $this->convert($format);
  }
  
  function formToDb ($date)
  {
    return date('Y-m-d', form2jul($date));
//    return date('Y-m-d', strtotime($date));
  }
  
  function dbToForm ($date)
  {
    return date('d-m-Y', db2jul($date));
//    return date('d-m-Y', strtotime($date));
  }
  
  
  function formatDate ($format, $date) {
    $newDate = $this->__formatFromArray($this->dateNotationHuman, $date, $format);
    return $newDate;
  }
  function formatTime ($format, $time) {
    $newTime = $this->__formatFromArray($this->timeNotationHuman, $time, $format);
    return $newTime;
  }
  
  function __formatFromArray ($formatArray, $date, $newDate) {
    foreach ( $formatArray as $searchNotation => $replaceNotation ) {
      if (strpos($replaceNotation, '|') !== false) {
        $replaceVars = explode('|', $replaceNotation);
        $replaceNotation = $replaceVars[0];
      
      
        $replaceWithDate = $this->translateDate($replaceNotation, strtotime($date));
        if ( is_numeric($replaceVars[1]) ) {
          $replaceWithDate = substr($replaceWithDate, 0, $replaceVars[1]);
        } elseif ($replaceVars[1] === 'int' ) {
          $replaceWithDate = (int) $replaceWithDate;
        }
      
        $newDate = str_replace('{' . $searchNotation . '}', $replaceWithDate,$newDate);
      
      } else {
        $newDate = str_replace( '{' . $searchNotation . '}' ,$this->translateDate($replaceNotation, strtotime($date)),$newDate);
      }
    }
    return $newDate;
  }
  
  
  
  function translateDate ($dateNotation, $strtotime) {
    $returnDate = '';
    switch ($dateNotation) {
      case 'l':
        //Dagen als zondag-zaterdag
        $dayOfWeek = date('N', $strtotime);
        $returnDate = $this->dagnaam[$dayOfWeek];
        break;
      case 'D':
        //Dagen als zo-za
        $dayOfWeek = date('N', $strtotime);
        $returnDate = $this->dagKort[$dayOfWeek];
        break;
      case 'F':
        //Maanden als de eerste letter van de maand
        $month = date('n', $strtotime);
        $returnDate = $this->maandnaam[$month];
        break;
      case 'M':
        //Maanden als jan-dec
        $month = date('n', $strtotime);
        $returnDate = $this->maandKort[$month];
        break;
      default:
        $returnDate = date($dateNotation, $strtotime);
        break;
    }
    
    return $returnDate;
  }
  
} 		

?>