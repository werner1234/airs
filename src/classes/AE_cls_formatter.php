<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/02/01 12:43:52 $
 		File Versie					: $Revision: 1.7 $

 		$Log: AE_cls_formatter.php,v $
 		Revision 1.7  2018/02/01 12:43:52  cvs
 		update naar airsV2
 		
 		Revision 1.6  2016/09/28 14:09:38  rm
 		format comments
 		
 		Revision 1.5  2016/09/19 09:02:47  cvs
 		toevoegen
 		
 		Revision 1.4  2016/08/29 08:19:05  cvs
 		spelfout in september
 		
 		Revision 1.3  2016/07/22 07:54:31  cvs
 		no message
 		
 		Revision 1.2  2016/04/22 07:41:43  cvs
 		datum met "form" picture uitgebreidt {form} geeft dd-mm-yyyy
 		
 		Revision 1.1  2016/03/30 09:57:26  cvs
 		eerste commit
 		

*/
  
  
  
  /*****************************
   * 
   * formateringen
   * 
   * @S{45}                                 string 45 lang
   * @S{-45}                                string toon laatste 45 tekens
   * @S...{-3}                              string toon ...ter   (input = letter)
   * @S{3}...                               string toon let...   (input = letter)
   * 
   * @N{.2}                                 nummer met 2 decimalen, dus 10.000,25
   * @N{.0}                                 nummer zonder decimalen
   * @N{.2B}                                nummer met 2 decimalen, als 0.00 dan lege string teruggeven
   * @N{.2R}                                nummer met 2 decimalen zonder opmaak, dus 10000.25
   * @N{F}                                  Nummer weergeven in de werkelijke waarde
   * @N{.5T2}                               Nummer weergeven met een min/max decimalen
   * 
   * @D{d}-{m}-{Y}                          geef datum terug als 02-03-2016
   * @D{form}                               geef datum terug als 02-03-2016
   * @D{H}:{i}:{s}                          geef tijd terug `ii` zijn minuten als 10:15:53
   * @D{d}-{m}-{Y}, de tijd is {H}:{i} uur  geef datum en tijd terug als 02-03-2016, de tid is 10:15:53 uur
   * 
   * de opmaak van de PHP functie wordt gebruikt..
   *
   *
  $fmt = new AE_cls_formatter(",",".");
  echo "<li>";
  echo $fmt->format("@N{F}", "123456.123456");
  echo "<li>";
  echo $fmt->format("@N{F}", "123456.456");
  $t = array(1.12341241, 1.0012456, 1.10300000000, 2.120000, 2.234512, 2.1230123013);
  foreach ($t as $item)
  {
  echo "<hr/> $item = ".$fmt->format("@N{.5T2}", $item);
  }
  */



class AE_cls_formatter
{
  var $decimalSeperator  = ".";
  var $thousendSeperator = ",";
  var $maandNaam     = array("","januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december");
  var $maandNaamKort = array("","jan","feb","mrt","apr","mei","jun","jul","aug","sep","okt","nov","dec");
  var $dagNaam       = array("","maandag","dinsdag","woensdag","donderdag","vrijdag","zaterdag","zondag");
  var $dagNaamKort   = array("","ma","di","wo","do","vr","za","zo");
  var $output=null;
  
  function AE_cls_formatter($dec=".",$thou=",")
  {
    $this->decimalSeperator  = $dec;
    $this->thousendSeperator = $thou;
  }

  /**
   * formatteer tekst/getallen of datums
   * @param $format
   * @param $value
   *
   * @return null|string
   */
  function format($format, $value)
  {
     $this->output = null;
     switch (strtoupper(substr($format,0,2))) 
     {
       case "@S":    // string
         $this->formatString($format,$value);
         break;
       case "@N":    // numbers
         $this->formatNumber($format,$value);
         break;
       case "@D":    // date and time
         $this->formatDate($format,$value);
         break;
       case "@B":    // boolean or checkbox
         $this->formatBoolean($format,$value);
         break;
       case "@C":
         $this->output = ($value == 1)?"<i class='fa fa-check-square-o' aria-hidden='true'></i>":"<i class='fa fa-square-o' aria-hidden='true'></i>";
         break;
       default:
         $this->output = "format `".$format."` niet herkend! ";
     }
     return $this->output;
  }

  /**
   * @param $format formaat uitvoer b.v. @S{23}
   * @param $value  tekst
   */
  function formatString($format,$value)
  {
    $template = substr($format,2);
    $splitParts  = explode("{",$template);
    $splitParts2 = explode("}",$splitParts[1]);
    $format = trim($splitParts2[0]);
    $template = $splitParts[0]."{}".$splitParts2[1];
    if (!is_numeric($format))
    {  $this->output = $value;    }
    else
    {
      if ($format < 1) 
      {  $value = substr($value,$format);    }
      else
      {  $value = substr($value,0,$format);  }
      
      $this->output = str_replace("{}", $value, trim($template));
    }
  }
  /**
   * @param $format formaat uitvoer b.v. @N{.2}
   * @param $value  waarde
   */
  function formatNumber($format,$value)
  {
    $blank = false;
    $raw   = false;
    $formatted = false;
    $trailClip = false;
  
    $template = substr($format,2);
    $splitParts  = explode("{",$template);
    $splitParts2 = explode("}",$splitParts[1]);
    $format = trim($splitParts2[0]);
    $template = $splitParts[0]."{}".$splitParts2[1];
    
    if (stristr($format,"B"))
    {
      $blank = true;
      $format = str_ireplace("b", "", $format);
    }
    if (stristr($format,"F"))
    {
      $formatted = true;
      $format = str_ireplace("f", "", $format);
      $split = explode(".", $value);
      $dec = strlen($split[1]);
    }
    if (stristr($format,"R"))
    {
      $raw = true;
      $format = str_ireplace("r", "", $format);
    }
    if (stristr($format,"T"))
    {
      $split = explode("T", $format);
      $tc = $split[1];
      $format = $split[0];
      $trailClip = true;
    }

    if (substr(trim($format),0,1) == "." AND is_numeric(substr(trim($format),1)) )
    {
      $dec = substr(trim($format),1);
    }
    else
    {
       $this->output = "NUMBER format `".$format."` niet herkend! "; 
    }
    if ($value == 0 AND $blank)
    {
      $this->output="";
      return;
    }
    if ($raw)
    {
      $value = number_format($value,$dec,".","");
    }
    else if ($formatted)
    {
      $value = number_format((double)$value,$dec,$this->decimalSeperator,$this->thousendSeperator);
    }
    else if ($trailClip)
    {
      $split = explode(".", (double)$value);
      $decLength = strlen($split[1]);
      $trimLength = ($decLength < $tc)?$tc:$decLength;
      $dec = ($trimLength < $dec)?$trimLength:$dec;
      $value = number_format($value,$dec,$this->decimalSeperator,$this->thousendSeperator);
    }
    else
    {
      $value = number_format($value,$dec,$this->decimalSeperator,$this->thousendSeperator);  
    }

    $this->output = str_replace("{}", $value, trim($template));
  }
  
  /**
   * @param $format formaat uitvoer b.v. @D{d}-{m}-{Y}
   * @param $value  Datum in SQL formaat
   */
  function formatDate($format, $value)
  {
    $date["Y"]    = substr($value,0,4);
    $date["m"]    = substr($value,5,2);
    $date["d"]    = substr($value,8,2);
    $date["H"]    = substr($value,11,2);
    $date["i"]    = substr($value,14,2);
    $date["s"]    = substr($value,17,2);
    $date["jul"]  = mktime($date["H"],$date["i"],$date["s"],$date["m"],$date["d"],$date["Y"]);
    $date["now"]  = mktime();


    $template = substr($format,2);
    $splitParts  = explode("{",$template);
    $template = $splitParts[0];
    $template = "";
    $idx = 0;
    foreach ($splitParts as $part)
    {
      $parts2 = explode("}",$part);
      if (count($parts2) == 2)
      {
        $key[$idx] = $parts2[0];
        $template .= '{'.$idx.'}'.$parts2[1];
        $idx++;
      }
      else
      {
        $template .=$part;
      }
     
    }
    foreach ($key as $k=>$v)
    {
      $jul = (strtoupper($value) == "NOW")?$date["now"]:$date["jul"];
      
      switch ($v)
      {
        case "form":
          $val[$k] = date("d-m-Y", $jul);
          break;
        case "D":
          $val[$k] = $this->dagNaamKort[date("w", $jul)];
          break;
        case "l":
          $val[$k] = $this->dagNaam[date("w", $jul)];
          break;
        case "F":
          $val[$k] = $this->maandNaam[date("n", $jul)];
          break;
        case "M":
          $val[$k] = $this->maandNaamKort[date("n", $jul)];
          break;
        default :
          $val[$k] = date("$v", $jul);
      }    
      $template = str_replace("{".$k."}", $val[$k], $template);
    }
    $this->output = $template;
  }
  
  function formatBoolean($format,$value)
  {
    $this->output = ($value <> 0)?"waar":"onwaar";
  }

}