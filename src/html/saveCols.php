<?
include_once("wwwvars.php");
session_start();
$colOrder=array();
$colOrder[]='edit';
foreach ($_SESSION[$_SESSION['tableSettings']['currentTable']]['fixedFields'] as $key=>$value)
  $colOrder[]=$key;
foreach ($_SESSION[$_SESSION['tableSettings']['currentTable']]['fields'] as $key=>$value)
{
  if($value==1)
    $colOrder[]=$key;
}
$cfg=new AE_config();
$cfg->addItem($USR.'_'.$_SESSION['tableSettings']['currentTable'],serialize($_SESSION[$_SESSION['tableSettings']['currentTable']]));
logIt($USR);
$_SESSION[$_SESSION['tableSettings']['currentTable']]['widths'][$colOrder[$_GET['col']]]=$_GET['width'];
session_write_close();
?>