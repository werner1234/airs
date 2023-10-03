<?php
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');
include_once('../classes/BedrijfconsistentieControleClass.php');

session_start();
$_SESSION[NAV] = "";
session_write_close();

$content = array();
$content['javascript'] .=
'
function unsetDefault()
{
  if (document.getElementById(\'check_viaVermogensbeheerders\').checked == true)
  {
    document.getElementById(\'check_viaVermogensbeheerders\').checked  = false;
  }
}
function unsetChecks()
{
  if (document.getElementById(\'check_viaVermogensbeheerders\').checked == true)
  {
    document.getElementById(\'check_categorie\').checked  = false;
    document.getElementById(\'check_sector\').checked  = false;
    document.getElementById(\'check_zorgplichtFonds\').checked  = false;
    document.getElementById(\'check_zorgplichtPortefeuille\').checked  = false;
    document.getElementById(\'check_hoofdcategorie\').checked  = false;
    document.getElementById(\'check_hoofdsector\').checked  = false;
    document.getElementById(\'check_sectorRegio\').checked  = false;
    document.getElementById(\'check_sectorAttributie\').checked  = false;
    document.getElementById(\'check_historischePortefeuilleIndex\').checked  = false;
    document.getElementById(\'check_kruisposten\').checked  = false;
    document.getElementById(\'check_valutaverschillen\').checked  = false;
    document.getElementById(\'check_afmCategorie\').checked  = false;
    document.getElementById(\'check_rekeningATT\').checked  = false;
    document.getElementById(\'check_rekeningCat\').checked  = false;
    document.getElementById(\'check_rekeningDepotbank\').checked  = false;
    document.getElementById(\'check_Beurs\').checked  = false;
    document.getElementById(\'check_BB_Landcodes\').checked  = false;
    document.getElementById(\'check_duurzaamheid\').checked  = false;
  }
}
  ';

echo template($__appvar["templateContentHeader"],$content);

if($posted == true)
{

  if($_GET['check_laatsteUpdate'])
  {
    $db = new DB();
    $query="SELECT LaatsteUpdate FROM Bedrijfsgegevens WHERE Bedrijf='".$_GET['Bedrijf']."'";
    $db->SQL($query);
    $stamp=$db->lookupRecord();
  }

 $controle = new BedrijfConsistentieControle($_GET['Bedrijf'],$stamp['LaatsteUpdate']);


 if ($_GET['check_viaVermogensbeheerders'] == 1)
 {
  $controle->getChecks();
 }
 else
 {
  $controle->setChecks($_GET);
 }

 if ($controle->checkRekeningMutaties() == false)
 {
   echo '<br> Controle afgebroken';
   exit();
 }
 echo '<br> Alle rekeningmutaties zijn verwerkt. ';
 flush();

 $controle->doChecks();

 //echo template($__appvar["templateRefreshFooter"],$content);
}
else
{
	$query = "SELECT Bedrijf FROM Bedrijfsgegevens ORDER BY Bedrijf ";
	$DB = new DB();
	$DB->SQL($query);
	$DB->Query($query);
	while($bedrijf = $DB->NextRecord())
	{
		$options[] = $bedrijf['Bedrijf'];
	}

?>
<form action="BedrijfConsistentieControle.php" method="GET" target="importFrame" name="controleForm">
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="posted" value="true" />
<!-- Name of input element determines name in $_FILES array -->
<b><?= vt('Bedrijf Consistentie controle'); ?></b><br><br>
<?php
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";
?>

<div class="form">
<div class="formblock">
<div class="formlinks"> <?= vt('Bedrijf'); ?>: </div>
<div class="formrechts">
<select name="Bedrijf">
<?=SelectArray("",$options)?>
</select>
</div>
</div>

<div class="form">
<div class="formblock">
<div class="formlinks"><?= vt('Gebruik vermogensbeheerder instellingen'); ?></div>
<div class="formrechts">
<input type="checkbox" name="check_viaVermogensbeheerders" id="check_viaVermogensbeheerders" value="1" checked onclick="unsetChecks();">  <br>
</div>
</div>

<div class="form">
<div class="formblock">
<div class="formlinks"><?= vt('Vanaf laatste update'); ?></div>
<div class="formrechts">
<input type="checkbox" name="check_laatsteUpdate" id="check_laatsteUpdate" checked value="1">
</div>
</div>
<br><br>

<div class="form">
<div class="formblock">
<div class="formlinks"><b><?= vt('Gebruik onderstaande controles'); ?> </b></div>
<div class="formrechts">
</div>
</div>

<div class="form">
<div class="formblock">
<div class="formlinks">check_rekeningmutaties</div>
<div class="formrechts">
<input type="checkbox" name="check_rekeningmutatie" value="1" checked disabled>
<input type="hidden" name="check_rekeningmutaties" id="check_rekeningmutaties" value="1">   <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_categorie </div>
<div class="formrechts">
<input type="checkbox" name="check_categorie" id="check_categorie" value="1" onclick="unsetDefault();" >  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_sector </div>
<div class="formrechts">
<input type="checkbox" name="check_sector" id="check_sector" value="1"  onclick="unsetDefault();">  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_zorgplichtFonds </div>
<div class="formrechts">
<input type="checkbox" name="check_zorgplichtFonds" id="check_zorgplichtFonds" value="1"  onclick="unsetDefault();">  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_zorgplichtPortefeuille </div>
<div class="formrechts">
<input type="checkbox" name="check_zorgplichtPortefeuille" id="check_zorgplichtPortefeuille" value="1"  onclick="unsetDefault();">  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_hoofdcategorie </div>
<div class="formrechts">
<input type="checkbox" name="check_hoofdcategorie" id="check_hoofdcategorie" value="1" >  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_hoofdsector </div>
<div class="formrechts">
<input type="checkbox" name="check_hoofdsector" id="check_hoofdsector" value="1" >  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_sectorRegio</div>
<div class="formrechts">
<input type="checkbox" name="check_sectorRegio" id="check_sectorRegio" value="1" >  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_sectorAttributie </div>
<div class="formrechts">
<input type="checkbox" name="check_sectorAttributie" id="check_sectorAttributie" value="1" >  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_historischeIndex </div>
<div class="formrechts">
<input type="checkbox" name="check_historischePortefeuilleIndex" id="check_historischePortefeuilleIndex" value="1" >  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_kruisposten </div>
<div class="formrechts">
<input type="checkbox" name="check_kruisposten" id="check_kruisposten" value="1" >  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_valutaverschillen </div>
<div class="formrechts">
<input type="checkbox" name="check_valutaverschillen" id="check_valutaverschillen" value="1" >  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_rapport </div>
<div class="formrechts">
<input type="checkbox" name="check_rapport" id="check_rapport" value="1" >  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_afmCategorie </div>
<div class="formrechts">
<input type="checkbox" name="check_afmCategorie" id="check_afmCategorie" value="1" >  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_rekening Attributie </div>
<div class="formrechts">
<input type="checkbox" name="check_rekeningATT" id="check_rekeningATT" value="1" >  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_rekening Categorie </div>
<div class="formrechts">
<input type="checkbox" name="check_rekeningCat" id="check_rekeningCat" value="1" >  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_rekening Depotbank </div>
<div class="formrechts">
<input type="checkbox" name="check_rekeningDepotbankt" id="check_rekeningDepotbank" value="1" >  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_Beurs </div>
<div class="formrechts">
<input type="checkbox" name="check_Beurs" id="check_Beurs" value="1" >  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_BB_Landcodes </div>
<div class="formrechts">
<input type="checkbox" name="check_BB_Landcodes" id="check_BB_Landcodes" value="1" >  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks">check_duurzaamheid </div>
<div class="formrechts">
<input type="checkbox" name="check_duurzaamheid" id="check_duurzaamheid" value="1" >  <br>
</div>
</div>

<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="button" value="Start controle" onClick="document.controleForm.submit();">
&nbsp;&nbsp;&nbsp;&nbsp;
</div>
</div>
</div>

</form>


<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<iframe width="600" height="400" name="importFrame"></iframe>
</div>
</div>

</div>
<?
echo template($__appvar["templateRefreshFooter"],$content);
}
?>