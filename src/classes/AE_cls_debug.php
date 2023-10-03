<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/05/12 11:13:40 $
 		File Versie					: $Revision: 1.5 $

 		$Log: AE_cls_config.php,v $

*/
class AE_cls_debug
{
  var $user;
  var $disabled = false;
  var $data = "";
  function AE_cls_debug()
  {
    global $USR, $__debug;
	  $this->user = $USR;
	  if (!$__debug)
    {
      $this->disabled = true;
    }

  }

  function enable()
  {
    $this->disabled = false;
  }

  function setData($data)
  {
    if (gettype($data) == "array")
    {
      $this->data = $data;
    }
    else
    {
      $this->data = array($data);
    }

  }


  function filter($filter)
  {
    $out = array();
    foreach ($this->data as $k=>$v)
    {
      if (stristr($k, $filter) or stristr($v, $filter))
      {
        $out[$k] = $v;
      }
    }
    $this->show($out,"filtered: $filter");

  }

  function show($de_array,$title="")
  {

    if ($this->disabled)
    {
      return;
    }

    $output = "
    
    <fieldset>
      
    ";
      $data = stripslashes(var_export($de_array,true));  // slashes uit uitvoer verwijderen
      if (!is_array($de_array) AND !is_object($de_array) )
      {
        $start = substr($data, 0,1);
        $end = substr($data, -1);
        if ( $start == '"' || $start == '\'')
        {
          $data  = substr($data,1,-1);  // quotes om strings verwijderen
        }
      }

      $output = "\n<div style='background:#D2B48C; padding:3px;  color:#000; font-family:monospace; line-height: 1rem'><span style='background:#333; color:white;'> Backtrace&nbsp;:</span>&nbsp;";
      $bt = debug_backtrace();

            $output .= "&raquo;&raquo; ".$bt[0]["file"].":".$bt[0]["line"]."\n";
      $output .= "\n<br/><span style='background:#333; color:white;'>VAR type &nbsp;:</span>&nbsp;";
      $output .= "&raquo;&raquo; ".gettype($de_array)."\n";
      $output .= "<div style='background:#8B4513; padding:3px; margin-bottom:0; color:white; font-weight: bold; font-size:16px; font-family:monospace; '>$title</div>";
      $output .= "<pre style='background:#FAEBD7; color: #333; padding:3px; margin-top:0; border: 1px solid maroon; font-family:monospace; '>\n";
      $output .= $data;
      $output .= "</pre></fieldset></div>\n";
      echo $output;

  }

  function showSys($sysVar="", $title)
  {
    $toShow = ($sysVar != "")?$sysVar:$_SERVER;
    $this->show($toShow, $title);
  }

}

