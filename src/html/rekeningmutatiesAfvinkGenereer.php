<?php
include_once("wwwvars.php");


include_once("../classes/AIRS_rekeningAfvinkHelper.php");

$afh = new AIRS_rekeningAfvinkHelper("VEC");



echo template($__appvar["templateContentHeader"],$content);

?>
<h2>genereren van afvink records</h2>
<br/>
<br/>
Er zijn <?=$afh->genereerAfvinkRecords()?> records aangemaakt.

<br/>
<br/>
<button><a href="<?=$_SESSION["backlink"]?>"> terug naar de afvinklijst </a></button>
  <br/>
  <br/>
  <br/>
  <br/>
<?

if($__debug)
  echo getdebuginfo();
echo template($__appvar["templateRefreshFooter"],$content);






//echo $AETemplate->parseFile('rekeningmutaties/colorCodingLegend.html');


logAccess();
if($__debug) {
	echo getdebuginfo();
}
?>