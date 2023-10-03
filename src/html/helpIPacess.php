<?php
include_once("wwwvars.php");

session_start();
$_SESSION['submenu'] = "";
$_SESSION['NAV'] = "";
session_write_close();
$fileName = ".htaccess";
$content = array();
echo template($__appvar["templateContentHeader"],$content);

?>

<style>
  .ipTable{
    width: 800px;
    border:1px #999 solid;
    margin: 0;
    padding: 0;
  }
  .ipTable .c2{
    border-left: #999 2px solid;
  }
  .ipTable td{
    padding: 5px 10px;
  }
  .ipTable thead td {
    background: rgba(20,60,90,1);
    color: white;
    padding: 5px 10px;
  }
  tr:nth-child(even) {background: #EEE}
  tr:nth-child(odd) {background: #FFF}
</style>
<h1><?= vt('IP adressen die toegang hebben tot de applicatie'); ?></h1>
<?

if (!file_exists($fileName))
{
  echo vt("Geen informatie beschikbaar");

}
else
{

?>
<table class='ipTable'>
  <thead>
  <td><?= vt('IP adres'); ?></td>
  <td><?= vt('DNS naam'); ?></td>
  <td><?= vt('Memo'); ?></td>
  </thead>
<?
  $data = file($fileName);
  for ($x=0; $x < count($data); $x++)
  {
    $item = $data[$x];
    if (stristr($item,"allow from"))
    {
      $msgRow = $data[$x-1];
      $msg = (substr($msgRow,0,4) == "#msg")?trim(substr($msgRow,4)):"";

      $p = explode ("allow from",$item);
      $dns = (strstr($p[1],"/"))?$p[1]:gethostbyaddr(trim($p[1]));

      echo "\n<tr><td class='c1'>{$p[1]}</td><td class='c2'>{$dns}</td><td class='c2'>{$msg}</td></tr>";

    }
  }

  echo "</table>";
}


echo template($__appvar["templateRefreshFooter"],$content);
?>