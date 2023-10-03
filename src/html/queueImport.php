<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/08/13 09:40:47 $
 		File Versie					: $Revision: 1.17 $

 		$Log: queueImport.php,v $
 		Revision 1.17  2017/08/13 09:40:47  rvv
 		*** empty log message ***



*/
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');

session_start();
$_SESSION[NAV] = "";
session_write_close();

if($__appvar["multipleDB"])
{
  $dbnaam=explode('_',$_SESSION["dbName"]);
  $bedrijf = $dbnaam[1];
}
else
  $bedrijf = $__appvar['bedrijf'];
  
$DB=new DB();
$query="
SELECT 
  max(check_module_portefeuilleWaarde) as portefeuilleWaarde,  
  max(autoPortaalVulling) as portaal
FROM
  Vermogensbeheerders";
$DB->SQL($query);
$DB->Query();
$vermogensbeheerder=$DB->NextRecord();

$DB = new DB(2);
$q = "SELECT * FROM updates WHERE Bedrijf = '$bedrijf' AND complete = '2' GROUP BY exportId ORDER BY add_date ASC";
$DB->SQL($q);
$DB->Query();
$bezig=$DB->records();
$bezigData=$DB->NextRecord();

$q = "SELECT * FROM updates WHERE Bedrijf = '$bedrijf' AND complete = '0' GROUP BY exportId ORDER BY add_date ASC";
$DB->SQL($q);
$DB->Query();
$aantal = $DB->Records();
$herrekeningPortaal=0;
$herrekeningPortefeuille=0;
$correctieUpdate=0;
if($aantal > 0)
{
	while($data = $DB->NextRecord())
	{
	  if($data['type']=='dagelijks' || $data['type']=='zaterdag')
    {
      $herrekeningPortaal=1;
    } 
	  if($data['type']=='dagelijks' || $data['type']=='vanafLaatste' || $data['type']=='zaterdag')
    {
      $herrekeningPortefeuille=1;
    } 
	  if($data['type']=='correctie' && $data['filesize'] > 500000)
    {
      $correctieUpdate=1;
    } 
		$options .= "<input type=\"hidden\" value=\"".$data['exportId']."\" name=\"exportId_".$data['id']."\"> ".$data['exportId']." ( ".$data['filesize']." bytes) <br>";
	}
}
else
{

}
$melding='';
if($correctieUpdate==1)
{
  $melding .= vt("Correctieupdate gevonden").".";
}

if($herrekeningPortefeuille==1 && $vermogensbeheerder['portefeuilleWaarde']==1)
{
  $melding .= vt("Laatste portefeuillewaarde herrekening geactiveerd").".\\n";
}

if($herrekeningPortaal==1 && $vermogensbeheerder['portaal']==1)
{
  $melding .= vt("Portaal vulling geactiveerd").".\\n";
}

if($melding <> '')
{
  $melding .= vt("Het handmatig ophalen van deze update kan vele minuten duren").".\\n".
              vt("Tijdens het ophalen van de update kunt u Airs niet gebruiken").".\\n".
              vt("Wilt u doorgaan?");
}

  

$content = array();
$content['javascript']="
function submitCheck()
{
";
if($melding <> '')
  $content['javascript'].="  var verder=confirm(\"$melding\");\n";
else
  $content['javascript'].="  var verder=true\n";  
  $content['javascript'].="
  if(verder==true)
  {
    document.selectForm.submit();
  }
}";
echo template($__appvar["templateContentHeader"],$content);
?>

<form action="queueImportData.php" method="POST" name="selectForm" >
<input type="hidden" name="posted" value="true" />

<b><?=vt("Importeren data van Queue")?></b> <br> <?=vt("Huidige versie")?> <?=$PRG_VERSION?> (<?=$PRG_RELEASE?>)<br><br>
<?php
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";
?>
<div class="form">
<div class="formblock">
<div class="formlinks"> <?=vt("Bedrijf")?></div>
<div class="formrechts">
<b><?=$bedrijf?></b>
</div>
</div>

<div class="form">
<div class="formblock">
<div class="formlinks"> <?=vt("Update")?></div>
<div class="formrechts">
<?php
if($bezig>0)
{
  echo vt("Update")." ".$bezigData['exportId']." ".vt("wordt momenteel verwerkt. Laatste update om")." ".$bezigData['change_date'].".";
  if((time()-db2jul($bezigData['change_date'])) > 3600*4)
  {
    echo vt("De update is al langer dan 4 uur bezig.")." ".
         vt("De update is mogelijk afgebroken en dient door Airs in de queue weer op status 0 gezet te worden.");
  }

}
elseif($aantal >0)
{
  echo $options;
}
else
{
?>
  <?=vt("geen updates aanwezig")?>
<?
}
?>
</div>
</div>

<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<br><br>
<br><br>
<?php
if($aantal >0 && $bezig<1)
{
?>
  <div class="buttonDiv" onclick="javascript:submitCheck();"> >> <?=vt("start update")?> </div><br>
<?php
}
?>
</div>
</div>

</form>

</div>
<?
echo template($__appvar["templateRefreshFooter"],$content);
?>