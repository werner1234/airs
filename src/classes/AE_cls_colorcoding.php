<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2017/08/31 09:40:32 $
 		File Versie					: $Revision: 1.10 $

 		$Log: AE_cls_template.php,v $
*/

class AE_cls_colorcoding
{
  var $baseStyle = "colorBaseStyle";
  var $content = "";
  function AE_cls_colorcoding($inStr="")
  {
    global $USR;
    $this->content = $inStr;

  }

  function setContent($content)
  {
    $this->content = $content;
  }

  function CSSinit()
  {
     return "
     <style>
       .colorBaseStyle{ color: Red; font-weight: bold;} 
     </style>
     ";
  }

  function processFullTextSearch($search, $class="")
  {
    if ($class == "")
    {
      $class = $this->baseStyle;
    }
    $searchArray =  explode(" ",preg_replace("/[^a-zA-Z0-9 ]/", "", $search));

    foreach ($searchArray as $item)
    {
      $this->process($item, $class);
    }

  }

  function process($text, $class="")
  {
    if ($class == "")
    {
      $class = $this->baseStyle;
    }
    $this->content = str_ireplace($text, "<span class='$class'>$text</span>", $this->content);
  }

  function output()
  {
    return $this->content;
  }


  function compare($old, $new)
  {

    $ol = strlen($old);
    $nl = strlen($new);
    $sl = ($ol > $nl)?$ol:$nl;

    $out = "";



    for ($cc=0; $cc < $sl; $cc++)
    {
      $n = ($cc < $nl)?$new[$cc]:"";
      $o = ($cc < $ol)?$old[$cc]:"";


      if ($o == $n)
      {
        $out .= $o;
      }
      else
      {
        $out .= "<span style='color:red; text-decoration: line-through;'>$o</span>";
        $out .= "<span style='color:green; font-weight: bold;'>$n</span>";
      }


    }
    return $out;

  }


}

