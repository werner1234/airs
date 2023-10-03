<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/04/18 17:04:33 $
 		File Versie					: $Revision: 1.26 $

 		$Log: rapportBackofficeClientSamenvatting.php,v $
 		Revision 1.26  2020/04/18 17:04:33  rvv
 		*** empty log message ***

*/
include_once("../classes/portefeuilleSelectieClass.php");
include_once("../classes/backofficeAfdrukkenClass.php");
include_once("../classes/templateEmail.php");
session_start();


$query = "SELECT check_module_CRM FROM Vermogensbeheerders LIMIT 1";
$DB = new DB();
$DB->SQL($query);
$DB->Query();
$rdata = $DB->nextRecord();

//$_SESSION['submenu'] = New Submenu();
//$_SESSION['submenu']->addItem("CRM rapportage instellingen","CRM_rapportageInstelling.php",array('target'=>'_blank'));

if ($_GET['selectie'])
  $_SESSION['selectieMethode'] = $_GET['selectie'];
if($_SESSION['selectieMethode'] == 'portefeuille')
  $selectiePortefeuille = 'checked';
elseif($_SESSION['selectieMethode'] == 'vink')
  $selectieVink = 'checked';
else
  $selectieAlles = 'checked';

echo template($__appvar["templateContentHeader"],$content);

?>
<script type="text/javascript">
function setRapportTypes()
{
	document.selectForm.rapport_types.value = "";
	var tel =0;
 	for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			document.selectForm.rapport_types.value = document.selectForm.rapport_types.value + '|' + parent.frames['submenu'].document.selectForm.rapport_type[i].value;
 			tel++;
 		}
 	}
}
<?
if($rapportSelectie[$rdata['layout'].'_b'])
  echo $rapportSelectie[$rdata['layout'].'_b'];
else
  echo $rapportSelectie['default_b'];
?>


function print()
{
  document.selectForm.action = "rapportBackofficeClientAfdrukken.php";
	document.selectForm.target = "generateFrame";
	document.selectForm.type.value="pdf";
	document.selectForm.save.value="0";
	document.selectForm.submit();
	document.selectForm.target = "";
	document.selectForm.action = "";
}


function saveasfile()
{
  document.selectForm.action = "rapportBackofficeClientAfdrukken.php";
	document.selectForm.target = "generateFrame";
	document.selectForm.type.value="pdf";
	document.selectForm.save.value="1";
	document.selectForm.submit();
	document.selectForm.target = "";
	document.selectForm.action = "";
}

function exportData()
{
  document.selectForm.action = "rapportBackofficeClientAfdrukken.php";
	document.selectForm.target = "generateFrame";
	document.selectForm.type.value="export";
	document.selectForm.save.value="0";
	document.selectForm.submit();
	document.selectForm.target = "";
	document.selectForm.action = "";
}

function eMail()
{
  var answer=confirm("<?=vt("Rapportages in de eMail wachtrij plaatsen?")?>");
  if (answer)
  {
    document.selectForm.action = "rapportBackofficeClientAfdrukken.php";
  	document.selectForm.target = "generateFrame";
  	document.selectForm.type.value="eMail";
	  document.selectForm.save.value="0";
	  document.selectForm.submit();
	  document.selectForm.target = "";
	  document.selectForm.action = "";
  }
}

function rapportagePortaal()
{
  var answer=confirm("<?=vt("Rapportages in de rapportage portaal wachtrij plaatsen?")?>");
  if (answer)
  {
    document.selectForm.action = "rapportBackofficeClientAfdrukken.php";
  	document.selectForm.target = "generateFrame";
  	document.selectForm.type.value="portaal";
	  document.selectForm.save.value="0";
	  document.selectForm.submit();
	  document.selectForm.target = "";
	  document.selectForm.action = "";
  }
}


function eDossier()
{
  var answer=confirm("<?=vt("Rapportages aan digitaal dosier toevoegen?")?>")
  if (answer)
  {
    document.selectForm.action = "rapportBackofficeClientAfdrukken.php";
  	document.selectForm.target = "generateFrame";
  	document.selectForm.type.value="eDossier";
  	document.selectForm.save.value="0";
  	document.selectForm.submit();
 	  document.selectForm.target = "";
	  document.selectForm.action = "";
  }
}

function rapportageFactuur()
{
  document.selectForm.action = "rapportBackofficeClientAfdrukken.php";
	document.selectForm.target = "generateFrame";
	document.selectForm.type.value="alleenFactuur";
	document.selectForm.save.value="0";
	document.selectForm.submit();
	document.selectForm.target = "";
	document.selectForm.action = "";
 
  
}

function saveSettings()
{
	document.selectForm.target = "";//generateFrame
	document.selectForm.submit();
}

function checktest(box)
{
  if(box.checked)
  {
    document.selectForm.testrun.value=1;
  }
  else
  {
    document.selectForm.testrun.value=0;
  }
}

</script>

<br><br>
<div class="tabbuttonRow">
<?
$opmaakStyle='tabbuttonInActive';
$selectieStyle='tabbuttonInActive';
$samenvattingStyle='tabbuttonInActive';
$productieStyle='tabbuttonInActive'; 
if($_SESSION['backofficeSelectie']['stap'] == 'opmaak')
{
  $opmaakStyle='tabbuttonActive';
  $include='rapportBackofficeKwartaalopmaak.php';
}
elseif($_SESSION['backofficeSelectie']['stap'] == 'samenvatting')
   $samenvattingStyle='tabbuttonActive';
elseif($_SESSION['backofficeSelectie']['stap'] == 'productie')
   $productieStyle='tabbuttonActive'; 
else
  $selectieStyle='tabbuttonActive';


?>
	<input type="button" class="<?=$selectieStyle?>" onclick="document.selectForm.stap.value='selectie';saveSettings();" id="tabbutton0" value="<?=vt("Selectie")?>">
	<input type="button" class="<?=$opmaakStyle?>" onclick="document.selectForm.stap.value='opmaak';saveSettings();"  id="tabbutton1" value="<?=vt("Opmaak")?>">
	<input type="button" class="<?=$samenvattingStyle?>" onclick="document.selectForm.stap.value='samenvatting';saveSettings();"  id="tabbutton1" value="<?=vt("Samenvatting")?>">
	<input type="button" class="<?=$productieStyle?>" onclick="document.selectForm.stap.value='productie';saveSettings();"  id="tabbutton3" value="<?=vt("Productie")?>">
<!--
		<input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportBackofficeMaandSelectie.php';"  id="tabbutton1" value="Maandrapportage">
   <<input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportBackofficeKwartaalSelectie.php';" id="tabbutton2" value="Kwartaalrapportage">
	-->
</div>
<br>

<form method="POST" name="selectForm">
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="stap" value="" />
<input type="hidden" name="type" value="" />
<input type="hidden" name="save" value="" />
<input type="hidden" name="exportRap" value="" />
<input type="hidden" name="testrun" value="" />


<table border="0">
<tr>
<td width="540" valign="top">
<fieldset id="Selectie" >
<?
$selectieData=$_SESSION['backofficeSelectie'];
$selectieData['datumVan'] =form2jul($selectieData['datumVan']);
$selectieData['datumTm']=form2jul($selectieData['datumTm']);

//$selectie=new portefeuilleSelectie($selectieData,'',true);
$selectie=new portefeuilleSelectie($selectieData,'',array('CrmClientNaam','CRM_naw.wachtwoord','CRM_naw.email','Vermogensbeheerders.CrmPortefeuilleInformatie','CRM_naw.rapportageVinkSelectie'));//,'CRM_naw.rapportageVinkSelectie'
$afdruk=new backofficeAfdrukken($selectieData);
$aantal=$selectie->getRecords();
$portefeuilles=$selectie->getSelectie();
$afdruk->portefeuilles=$portefeuilles;


$afdruk->selectie['type']='eMail';
foreach ($portefeuilles as $portefeuille=>$pdata)
{
  if($selectieData['CRM_rapport_vink']==1)
  {
    $crmInstelling=unserialize($pdata['rapportageVinkSelectie']);
    $afdruk->getCrmRapport($portefeuille);
    $rapporten=$afdruk->rapport_type;
    if(count($rapporten) > 0)
    {
      $emailPortefeuilles[]=$portefeuille;
      if($pdata['CrmClientNaam'] && strlen($pdata['wachtwoord']) < 6)
        $geenKoppeling['wachtwoord'][]="$portefeuille";
      if($pdata['CrmClientNaam'] && $pdata['email']=='')
       $geenKoppeling['Email'][]="$portefeuille";
    }
    if($pdata['CrmClientNaam'] && $pdata['CRM_nawID']=='')
        $geenKoppelingAlgemeen['Crm'][]="$portefeuille";
  }
  else
  {
    $emailPortefeuilles[]=$portefeuille;
    if($pdata['CrmClientNaam'] && $pdata['CRM_nawID']=='')
      $geenKoppelingAlgemeen['Crm'][]="$portefeuille";
    if($pdata['CrmClientNaam'] && strlen($pdata['wachtwoord']) < 6)
      $geenKoppeling['wachtwoord'][]="$portefeuille";
    if($pdata['CrmClientNaam'] && $pdata['email']=='')
     $geenKoppeling['Email'][]="$portefeuille";
  }
}

$template=new templateEmail($_SESSION['backofficeSelectie']['email'],$_SESSION['backofficeSelectie']['onderwerp']);
echo vt("De huidige selectie bevat")." {$aantal} ".vt("portefeuilles").".<br><br>\n";
echo '<br><table border=1>';
foreach ($geenKoppelingAlgemeen as $type=>$data)
  echo "<tr><td>".vt("Missende")." $type ".vt("koppelingen")." (".count($data).")</td><td><textarea cols=20, rows=4>".implode("\n",$data)."</textarea></td></tr>\n";
echo '</table>';


echo "<br><br><br><br><h3>".vt("eMail instellingen")."</h3>";
echo "".vt("De selectie bevat")." ".count($emailPortefeuilles)." ".vt("portefeuille(s) voor de email zending").".<br><br>";
echo '<br><table border=1><tr><td>'.vt("veld").'</td><td>'.vt("voorbeeld").'</td></tr>';
foreach ($portefeuilles as $portefeuille=>$pdata)
{
  $allPdata=$template->getPortefeuileValues($portefeuille);
  $email=$template->templateData($allPdata);
  echo '<tr><td>'.vt("Onderwerp").'</td><td>'.$email['subject'].'</td></tr>';
  echo '<tr><td>'.vt("Email").'</td><td>'.$email['body'].'</td></tr>';
  break;
}
echo '</table>';


foreach ($portefeuilles as $portefeuille=>$pdata)
{
 // listarray($pdata);
 // echo $pdata['email']."<br>\n";

}

echo '<br><table border=1>';
foreach ($geenKoppeling as $type=>$data)
  echo "<tr><td>".vt("Missende")." $type ".vt("koppelingen")." (".count($data).")</td><td><textarea cols=20, rows=4>".implode("\n",$data)."</textarea></td></tr>\n";
echo '</table>';

?>

</fieldset>
</td>
<td valign="top">

<div class="buttonDiv" style="width:110px" onclick="javascript:print();">&nbsp;&nbsp;<?=maakKnop('pdf.png',array('size'=>16))?> <?=vt("Afdrukken")?></div><br>
<div class="buttonDiv" style="width:110px" onclick="javascript:saveasfile();">&nbsp;&nbsp;<?=maakKnop('disk_blue.png',array('size'=>16))?> <?=vt("Opslaan")?> </div><br>
	<input type="button" onclick="javascript:$('#bestandsnaam').show();$('#exportDiv').show();" value=" <?=vt("Export")?> " style="width:110px"><br><br>
	<?if($rdata['check_module_CRM'])
	{

	  if($_SESSION['usersession']['gebruiker']['verzendrechten']==1 || $_SESSION['usersession']['gebruiker']['verzendrechten']==3)
	  {
  	  echo '<input type="button" onclick="javascript:$(\'#bestandsnaam\').show();$(\'#eDossierDiv\').show();" value=" '.vt("eDossier").' " style="width:110px"><br><br>';
      

	  }
    if($_SESSION['usersession']['gebruiker']['verzendrechten']==2 || $_SESSION['usersession']['gebruiker']['verzendrechten']==3)
    {
      echo '<input type="button" onclick="javascript:$(\'#bestandsnaam\').show();$(\'#eMailDiv\').show();" value=" '.vt("Per eMail").' " style="width:110px"><br><br>';
      if(GetModuleAccess('PORTAAL')==1)
        echo '<input type="button" onclick="javascript:$(\'#bestandsnaam\').show();$(\'#portaalDiv\').show();" value=" '.vt("Portaal").' " style="width:110px"><br><br>';
      echo '<input type="button" onclick="javascript:$(\'#bestandsnaam\').show();$(\'#factuurDiv\').show();" value=" '.vt("Alleen factuur").' " style="width:110px"><br><br>';


    }
    $db = new DB();
    $crmVelden='';
    $extraJoin='';
    $extraVelden=array('Rapportagetenaamstelling','Rapportageafkorting');
    foreach($extraVelden as $veld)
    {
      $query="SHOW fields FROM CRM_naw like '$veld'";
      if($db->QRecords($query) > 0)
      {
         $Rapportagetenaamstelling .='</br> <input type="checkbox" name="bestandsnaamBegin[]" value="'.$veld.'"> '.$veld.'';
      }
    }


	}
  ?>
	<input type="checkbox" name="testrun" onclick="javascript:checktest(this);">Pre-run<br><br>



  <div >
<fieldset id="bestandsnaam" style="display: none;">
<legend> Bestandsnaam (PortefeuilleNr/Client + Extra tekst + .pdf)</legend>
<div class="formblock">
<div class="formlinks">
<input type="checkbox" name="bestandsnaamBegin[]" value="Portefeuille"> Portefeuille</br>
<input type="checkbox" name="bestandsnaamBegin[]" value="Client"> Client
<?=$Rapportagetenaamstelling?>
</div>
<div class="formrechts"> Extra tekst </br> <input type="text" name="bestandsnaamEind" value="" size="25"> <br>
  <input type="hidden" name="eDossierPdf" value="0">
  <input type="hidden" name="eDossierEmail" value="0">
   <input type="checkbox"  name="eDossierPdf" value="1" checked >PDF selectie<br>
  <input type="checkbox" name="eDossierEmail" value="1" checked >Email selectie<br> 

</div>
</div>
</fieldset>


<fieldset id="eDossierDiv" style="display: none;">
<div class="formblock"><div class="formlinks"> eDossier Omschrijving </div><div class="formrechts"> <input type="text" name="documentOmschrijving" value="" size="25"> </div></div>

<div class="formblock">
<div class="formlinks"> </div>
<div class="formrechts"> 
  <input type="button" onclick="javascript:eDossier();" value=" eDossier aanmaken" style="width:200px">
</div>
</div>
</fieldset>

<fieldset id="exportDiv" style="display: none;">
<div class="formblock">
<div class="formlinks"> </div>
<div class="formrechts"> 
  <input type="button" onclick="javascript:exportData();" value=" Naar directorie exporteren" style="width:200px">
</div>
</div>
</fieldset>

<fieldset id="eMailDiv" style="display: none;">
<div class="formblock">
<div class="formlinks"><input type="hidden" name="losseFactuur" value="0" /><input type="checkbox" name="losseFactuur" value="1" />factuur als losse bijlage
  (<input type="hidden" name="losseFactuurZonderRapportage" value="0" /><input type="checkbox" name="losseFactuurZonderRapportage" value="1" /> zonder rapportage)</div>
<div class="formrechts"> 
  <input type="button" onclick="javascript:eMail();" value=" eMail wachtrij vullen" style="width:200px">
</div>
</div>
</fieldset>

<fieldset id="portaalDiv" style="display: none;">
<div class="formblock">
<div class="formlinks"> </div>
<div class="formrechts"> 
  <input type="button" onclick="javascript:rapportagePortaal();" value=" Portaal wachtrij vullen " style="width:200px">
</div>
</div>
</fieldset>


<fieldset id="factuurDiv" style="display: none;">
<div class="formblock">
<div class="formlinks"> </div>
<div class="formrechts"> 
  <input type="radio" name="factuurType" value="pdf" <?if($_SESSION['backofficeSelectie']['factuurType']=='pdf')echo 'checked'?> > PDF uitvoer 
  &nbsp;&nbsp;&nbsp; <input type="radio" name="factuurType" value="xls" <?if($_SESSION['backofficeSelectie']['factuurType']=='xls')echo 'checked'?>> XLS uitvoer
  <input type="button" onclick="javascript:rapportageFactuur();" value=" Facturen aanmaken" style="width:200px">
</div>
</div>
</fieldset>

</div>
</td>
</tr>

<tr>
	<td colspan="2">
		<iframe width="540" height="300" name="generateFrame" frameborder="0" ></iframe>
	</td>
</tr>
</table>
</form>
<?
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>