<?php
/*
AE-ICT CODEX source module versie 1.6, 30 maart 2013
Author              : $Author: rvv $
Laatste aanpassing  : $Date: 2017/07/12 15:57:42 $
File Versie         : $Revision: 1.1 $

$Log: categorieFondsKoppelen.php,v $
Revision 1.1  2017/07/12 15:57:42  rvv
*** empty log message ***

*/
include_once("wwwvars.php");
include_once('../classes/BedrijfconsistentieControleClass.php');
$Bedrijf='AEI';
$updateTimeStamp='';//date('Y-m-d h:i:s');
echo template($__appvar["templateContentHeader"],$content);
$controle = new BedrijfConsistentieControle($__appvar["bedrijf"],$updateTimeStamp);
if($_POST)
{
  $controle->updateRecords($_POST);
}

$controle->getChecks();
$result = $controle->doChecks();

if($result == false)
{
  $controle->getFixForm($_SERVER['REQUEST_URI']);
  echo template($__appvar["templateRefreshFooter"],$content);
  exit;
}
echo "<br>- ".vt("einde consistentie controle")."<br>";
echo template($__appvar["templateRefreshFooter"],$content);
