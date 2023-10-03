<?php
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');

if($__appvar['master'] == false)
  exit;

$updateSoort=array('zaterdag'=>'Zaterdag update');

session_start();
$_SESSION['NAV'] = "";
session_write_close();

$DB = new DB();
$DB->SQL("SELECT * FROM Bedrijfsgegevens WHERE zaterdagExport=1 ORDER BY Bedrijf");
$DB->Query();

$bedrijven = array();

while($bedrijfdata = $DB->NextRecord())
{
	$bedrijven[] = $bedrijfdata['Bedrijf'];
}



//$content = array();
$content['javascript'].='


function checkAll(optie)
{
  var theForm = document.selectForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == \'checkbox\' && theForm[z].name.substr(0,8) == \'bedrijf_\')
   {
      if(optie == -1)
      {
        if(theForm[z].checked == true)
          theForm[z].checked=false;
        else
          theForm[z].checked=true;  
      }
      else
      {
        theForm[z].checked = optie;
      }
   }
  }
}



';


echo template($__appvar["templateContentHeader"],$content);
?>

<form action="queueZaterdagExportData.php" method="POST" name='selectForm' id='selectForm' >
<input type="hidden" name="posted" value="true" />

<b><?= vt('Export data'); ?></b><br><br>
<?php
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";
?>
  <div id="wrapper" style="overflow:hidden;">
    <div class="buttonDiv" style="width:150px;float:left;" onclick="checkAll(1);">&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> <?= vt('Alles selecteren'); ?></div>
    <div class="buttonDiv" style="width:150px;float:left;" onclick="checkAll(0);">&nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon' /> <?= vt('Niets selecteren'); ?></div>
    <div class="buttonDiv" style="width:160px;float:left;" onclick="checkAll(-1);">&nbsp;&nbsp;<img src='icon/16/replace2.png' class='simbisIcon' /> <?= vt('Selectie omkeren'); ?></div>
  </div>
<br>
  <div class="form">
<div class="formblock">
<div class="formlinks"> <?= vt('Bedrijven'); ?></div>
<div class="formrechts">
<?
foreach($bedrijven as $bedrijf)
{
  echo "<input type='checkbox' name='bedrijf_".$bedrijf."' value='1' checked> $bedrijf <br>\n";
}
?>
</div>
</div>


<div class="form">
<div class="formblock">
<div class="formlinks"> <?= vt('Soort update'); ?></div>
<div class="formrechts">
<select id="updateSoort" name="updateSoort" onchange="javascript:updateSoortChanged();">
<?=SelectArray("",$updateSoort,true)?>
</select>
</div>
</div>

<div class="form" id="tabelSelectie" style="display: none;">
<div class="formblock">
<div class="formlinks"> <?= vt('Tabel'); ?></div>
<div class="formrechts">
<select name="tabel">
</select>
</div>
</div>
</div>

<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="radio" name="exportType" id="radioQueueExport" value="queue" checked> <?= vt('Naar Queue'); ?>
</div>
</div>

<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="submit" value="Exporteren" >
</div>
</div>
</form>

</div>

<?


echo template($__appvar["templateRefreshFooter"],$content);
?>