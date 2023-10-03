<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2019/01/09 15:41:27 $
    File Versie         : $Revision: 1.2 $

    $Log: modw3.php,v $
    Revision 1.2  2019/01/09 15:41:27  rm
    Toevoegen checkboxImage functie, deze mist onder de voorlopige rekeningmutaties.

    Revision 1.1  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie



*/

$HTTP_SERVER_VARS=$_SERVER;

function SelectArray($keuze,$de_array,$keyed=false)
{
  $out = "";
  reset($de_array);
  while (list($key, $value) = each($de_array))
  {
    if ($keyed == false) $V = $value; else $V = $key;

    if ($V == $keuze)
      $out .= "<OPTION VALUE=\"".htmlspecialchars($V)."\" SELECTED>".$value."\n";
    else
      $out .="<OPTION VALUE=\"".htmlspecialchars($V)."\" >".$value."\n";
  }
  return $out;
}


function lclip($str,$lengte)
{
  if (strlen($str) > $lengte)  {   $str = "...".substr($str,-1 * ($lengte-3));  }
  return $str;
}

function rclip($str,$lengte)
{
  if (strlen($str) > $lengte)  {   $str = substr($str,0,$lengte-4)." ...";      }
  return $str;
}

//agenda
function Dag ($indat=0)
{
  global $dagnaam;
  if ($indat == 0) { $indat = time(); }
  return $dagnaam[date('w',$indat)+1];
}

function dbdatum ($indat)
{
  if ($indat == "0000-00-00 00:00:00")
    return "";
  else
    return datum(db2jul($indat));
}

function LDatum($indat=0)
{
  global $maandnaam;
  if ($indat == 0)
  {
    $indat = time();
  }
  return date('j',$indat) ." ". $maandnaam[date('n',$indat)] ." ". date('Y',$indat);
}

function makeButton($name="default", $text=false)
{
  global $ICONS16;
//  echo $ICONS16[$name]['image'];

  $button = "<img src=\"".$ICONS16[$name]['image']."\" width=\"16\" height=\"16\" border=\"0\" alt=\"".$ICONS16[$name]['title']."\" align=\"absmiddle\">";
//  $button = $ICONS16[$name]['image'];
  if ($text)
    $button .= "&nbsp;".$ICONS16[$name]['text'];
  return $button;
}
function Uitnullen($waarde,$lengte=2)
{
  return Str_pad($waarde,$lengte, "0", STR_PAD_LEFT);
}

function Datum($indat=0)
{
  global $maandnaam;
  if ($indat == 0)
  {
    $indat = time();
  }
  return date('j',$indat) .".". date('n',$indat) .".". date('Y',$indat);
}



//agenda
$PHP_SELF=$_SERVER['PHP_SELF'];

if (! isset($PXM_REG_GLOB)) {

  $PXM_REG_GLOB = 1;

  if (! ini_get('register_globals')) {
    foreach (array_merge($_GET, $_POST) as $key => $val) {
      global $$key;
      if(!is_array($val))
        $$key = (get_magic_quotes_gpc()) ? $val : addslashes($val);
    }
  }
  //if (! get_magic_quotes_gpc()) {
 //   foreach ($_POST as $key => $val) $_POST[$key] = addslashes($val);
 //   foreach ($_GET as $key => $val)  $_GET[$key]  = addslashes($val);
 // }
}

function jul2db ($indat=0)
{
  if ($indat == 0)
  {
    $indat = time();
  }
  return date('Y',$indat) ."-". date('m',$indat) ."-". date('d',$indat) ." ".
         date('H',$indat) .":". date('i',$indat) .":". date('s',$indat) ;
}


function db2jul ($dbdate="")
{
  if($dbdate == "")
  {
      return -1;
  }
  else
  {
    $jaar  = intval(substr($dbdate,0,4));
    if($jaar == 0)
      return 0;

    $maand = substr($dbdate,5,2);
    $dag   = substr($dbdate,8,2);
    $uur   = substr($dbdate,11,2);
    $min   = substr($dbdate,14,2);
    $sec   = substr($dbdate,17,2);
  }

  return mktime($uur,$min,$sec,$maand,$dag,$jaar);
}

function session_register($name){
    if(isset($GLOBALS[$name])) $_SESSION[$name] = $GLOBALS[$name];
    $GLOBALS[$name] = $_SESSION[$name];
}

function read_file($filename)
{
  if ($fp = fopen($filename,  "r"))
  {
    $fd = fopen( $filename, "r" );
    $contents = fread( $fd, filesize( $filename ) );
    fclose( $fd );
    return $contents;
  }
  else return 0;
}


function Template($template,$content=array())
{
  global $template_content;
  global $AE_debug;
  if ($AE_debug)
  {
    echo "<BR>DEBUGMODE<HR>";
    while ( list( $key, $val ) = each( $content ) )
    {
      echo "<br>$key -- $val";
    }
    echo "<HR>";
    while ( list( $key, $val ) = each( $template_content ) )
    {
      echo "<br>$key -- $val";
    }
    echo "<HR>";
  }


  if (isset($template_content))
  {
   $content = array_merge ($template_content,$content);
  }
  if ($AE_debug)
  {
    echo "<BR>Merged array DEBUGMODE<HR>";
    while ( list( $key, $val ) = each( $content ) )
    {
      echo "<br>$key -- $val";
    }
    echo "<HR>";
  }
  $message = read_file($template);

  while ( list( $key, $val ) = each( $content ) )
  {
    $message = str_replace( "{".$key."}", $val, $message);
//    if (stristr($message,"{".$key."}"))
//      echo "key $key FOUND";
//    else
//      echo "key $key NOT FOUND";
  }

  $message = eregi_replace( "\{[a-zA-Z0-9_-]+\}", "", $message);
  //$message .= "<HR>DONE<HR>";
  return $message;
}

/**
 * Functie voor het tonen van een checkbox in de list bij rekeningmutaties als afbeelding
 * Wordt vervangen in de nieuwe layout
 * @param int $waarde
 * @return string
 */
function checkboxImage($waarde=0)
{
  global $ICONS16;
  if($waarde==1)
    return makeButton("check");
}

?>
