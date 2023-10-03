<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/20 17:09:45 $
 		File Versie					: $Revision: 1.75 $

 		$Log: rapportBackofficeClientSelectie.php,v $
 		Revision 1.75  2020/05/20 17:09:45  rvv
 		*** empty log message ***
 		

*/
//$AEPDF2=true;
//

include_once("../classes/selectOptieClass.php");
$type='portefeuille';
// selecteer de 1e vermogensbeheerder uit de tabel vermogensbeheerders voor de selectie vakken.


$rapportenSelectie=unserialize($rdata['Export_data_frontOffice']);

foreach ($rapportenSelectie as $rapport=>$opties)
{
  $rdata[$rapport] = $opties['checked'];
}

//include_once("rapportFrontofficeClientSelectieLayout.php");


$html = "<script language=JavaScript src=\"javascript/ae_ajax_client.js\" type=text/javascript></script>";
$html .= "<b>".vt("selecteer rapport")."</b><br><br><form name=\"selectForm\"> <input type='hidden' name='selected' value=''>";


if($_SESSION['rapportSelectionBack'])
  $checkedReport=$_SESSION['rapportSelectionBack'];
else
{
  $checkedReport=array();
  foreach ($rapportenSelectie as $rapport=>$data)
    if($data['checked']==1)
      $checkedReport[]=$rapport;
}

if(is_array($rapportenSelectie))
{
  foreach($rapportenSelectie as $rapport=>$rapportData)
  {
    if($rapportData['volgorde']=='')
      $rapportData['volgorde']=99;
    $rapportVolgorde[$rapportData['volgorde']][$rapport]=$rapportData;
  }

  ksort($rapportVolgorde);
  $rapportenSorted=array();
  foreach($rapportVolgorde as $volgordeId=>$rapportdata)
    foreach($rapportdata as $rapport=>$rapData)
    {
      if(isset($__appvar["Rapporten"][$rapport]))
        $rapportenSorted[$rapport]=$rapData;
    }
  foreach ($__appvar["Rapporten"] as $rapport=>$omschrijving)
    $rapportenSorted[$rapport]['omschrijving']=$omschrijving;

  
  $rapporten=array('open'=>array(),'closed'=>array());
  foreach ($rapportenSorted as $rapport=>$rapportData)
  {
    $checked=in_array($rapport,$checkedReport);
    if($checked && in_array($rapport,$xlsRapporten))
      $xlsStyle="";

    if(isset($rapportenSelectie[$rapport]['longName']))
      $long=$rapportenSelectie[$rapport]['longName'];
    else
      $long=$rapportData['omschrijving'];

    if(isset($rapportenSelectie[$rapport]['shortName']))
      $short=$rapportenSelectie[$rapport]['shortName'];
    else
      $short=$rapport;

    if($rapportData['toon'] == 1)
      $rapporten['open'][]=  array('rapport'=>$rapport,'omschrijving'=>$long.' ('.$rapportData['volgorde'].' '.$rapport.')','checked'=>$checked,'short'=>$short);
    elseif($rapportData['toonNiet'] == 0)
      $rapporten['closed'][]=array('rapport'=>$rapport,'omschrijving'=>$long.' ('.$rapportData['volgorde'].' '.$rapport.')','checked'=>$checked,'short'=>$short);
  }
  
  foreach ($rapporten['open'] as $rapportData)
  {
  	$html .= "<input type=\"checkbox\" value=\"".$rapportData['rapport']."\" name=\"rapport_type\" id=\"".$rapportData['rapport']."\" onClick=\"doStuff()\" ".(($rapportData['checked']==1)?"checked":"").">  ".
					   "<label for=\"".$rapportData['rapport']."\" title=\"".$rapportData['omschrijving']."\">".$rapportData['short']." </label><br>";
  }
  $html .= "<br \>";
  
  
  $html .="
<script>
function doStuff()
{

  var xlsRapport = [";  
$first=true;
$xlsRapporten=array();
foreach($rapportenSelectie as $rapport=>$rapportData)
{
  if($rapportData['xls']==1 || $__appvar['bedrijf']=='HOME')
  {
    if($first==true)
      $first=false;
    else
      $html.=","; 
    $html .="'".$rapport."'";
    $xlsRapporten[]=$rapport;
  }
}  
$html .="];
	document.selectForm.selected.value = \"\";
	var tel =0;
  parent.frames['content'].$('#xls_uitvoer').hide();
  parent.frames['content'].selectTab();

	document.selectForm.selected.value = \"\";
	for(var i=0; i < document.selectForm.rapport_type.length; i++)
	{
		if(document.selectForm.rapport_type[i].checked == true)
		{
			document.selectForm.selected.value = document.selectForm.selected.value + '|' + document.selectForm.rapport_type[i].value;
			tel++;
      
      if(xlsRapport.indexOf(document.selectForm.rapport_type[i].value)>=0)
      {
        parent.frames['content'].$('#xls_uitvoer').show();
      }
		}
	}

	executeRequest('ae_ajax_server.php','selectForm', 'storeRapportSelectionBack', responseHandler);
}
function responseHandler(requester,formName)
{
	var theForm = document.forms[formName];
	return true;
}

doStuff();
</script>
";

  if(count($rapporten['closed']) >0)
  {
  $html .='<style type="text/css">
.menutitle{
cursor:pointer;
margin-bottom: 5px;
background-color:#ECECFF;
color:#000000;
width:120px;
padding:2px;
text-align:center;
font-weight:bold;
/*/*/border:1px solid #000000;/* */
}

input {
	color: Navy;
	background-color:#FBFBFB;
	font-size:14px;
	border : 0px;
	border-bottom : 1px solid silver;
	border-left : 1px solid silver;
	font-weight: bold;
}

.submenu{
margin-bottom: 0.5em;
}
</style>
<script type="text/javascript" src="javascript/menu.js"></script>


<div id="masterdiv">
<div class="menutitle" onclick="SwitchMenu(\'subNaw0\')">'.vt("Overige").'</div><span class="submenu" id="subNaw0">';
  }
  foreach ($rapporten['closed'] as $rapportData)
  {
    $html .= "<input type=\"checkbox\" value=\"".$rapportData['rapport']."\" name=\"rapport_type\" id=\"".$rapportData['rapport']."\" onClick=\"parent.frames['content'].selectTab();\" ".(($rapportData['checked']==1)?"checked":"").">  ".
					   "<label for=\"".$rapportData['rapport']."\" title=\"".vt($rapportData['omschrijving'])."\">".vt($rapportData['short'])." </label><br>";
  }
  if(count($rapporten['closed']) >0)
    $html .= '</span></div>';
}

$selectie=new selectOptie();
$selectie->getInternExternActive();
$html .= $selectie->getSelectieMethodeHTML($PHP_SELF);
$html .= $selectie->getInternExternHTML($PHP_SELF);
$html .="<br>";
if(method_exists($selectie,'getConsolidatieHTML'))
  $html.=$selectie->getConsolidatieHTML($PHP_SELF);
$html.='</form>';

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");
$_SESSION['submenu']->onLoad = " onLoad=\"parent.frames['content'].selectTab();\" ";
$_SESSION['NAV'] = "";
$content['javascript'] .= "";
$content['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$content['calendar'] = $kal->get_load_files_code();
$content['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";
$content['body']='onload="javascript:periodeSelected();"';
echo template($__appvar["templateContentHeader"],$content);
$totdatum = getLaatsteValutadatum();

// <script></script>
$disableCheckboxes="function enableDisableRapport(value){";
foreach ($__appvar["Rapporten"] as $rapport=>$omschrijving)
  $disableCheckboxes .="parent.frames['submenu'].selectForm.elements['".$rapport."'].disabled=value;\n";
$disableCheckboxes.="}";

?>
<script type="text/javascript">
  <?=$disableCheckboxes?>
function setRapportTypes()
{
  if(parent.frames['submenu'].document.selectForm.rapport_type && document.selectForm.rapport_types)
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
 <?=$selectie->getPortefeuilleInternJava();?>
  <?
  if(method_exists($selectie,'getConsolidatieJava'))
    echo $selectie->getConsolidatieJava();
  ?>
}

function checkBrievenDiv()
{
  var theForm = document.selectForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
    {
     if(theForm[z].name == "inclBrief" && theForm[z].type=='checkbox' )
     {
        if(theForm[z].checked)
        {
          $('#brievenDiv').show();
        }
        else
        {
          $('#brievenDiv').hide();
        }
     }
   }
}


<?
if($rapportSelectie[$rdata['Layout'].'_b'])
  echo $rapportSelectie[$rdata['Layout'].'_b'];
else
  echo $rapportSelectie['default_b'];
?>

function print()
{
  document.selectForm.action = "rapportBackofficeClientAfdrukken.php";
	document.selectForm.target = "generateFrame";
	document.selectForm.save.value="0";
	document.selectForm.type.value="pdf";
	setRapportTypes(); selectSelected();
	document.selectForm.submit();
	document.selectForm.target = "";
	document.selectForm.action = "";
}
function xls()
{
  document.selectForm.action = "rapportBackofficeClientAfdrukken.php";
	document.selectForm.target = "generateFrame";
	document.selectForm.save.value="0";
<?php

if($rdata['Vermogensbeheerder']=='MER'||$rdata['Vermogensbeheerder']=='BOX'||$rdata['Vermogensbeheerder']=='AVM')
  echo 'document.selectForm.type.value="xls";'."\n";
else  
	echo 'document.selectForm.type.value="xlsRapport";'."\n";
?>  
	setRapportTypes(); selectSelected();
	document.selectForm.submit();
	document.selectForm.target = "";
	document.selectForm.action = "";
}

function saveasfile()
{
  document.selectForm.action = "rapportBackofficeClientAfdrukken.php";
	document.selectForm.target = "generateFrame";
	document.selectForm.save.value="1";
	document.selectForm.type.value="pdf";
	setRapportTypes(); selectSelected();
	document.selectForm.submit();
	document.selectForm.target = "";
	document.selectForm.action = "";
}

function saveSettings()
{
	document.selectForm.target = "";//generateFrame
	setRapportTypes();
	selectSelected();
	document.selectForm.submit();
}



<?=$selectie->getSelectJava();?>

function periodeSelected()
{
    var theForm = document.selectForm.elements, z = 0;
    var waarde='';
    var CRM_rapport_vink = 0;
    for(z=0; z<theForm.length;z++)
    {
     if(theForm[z].name == "periode")
     {
        if(theForm[z].checked)
        {
          waarde=theForm[z].value;
        }
     }
     if(theForm[z].name == "inclFactuur" && theForm[z].type=='checkbox'){checkIndex=z;}
    }
    if(waarde=='Kwartaalrapportage' || waarde=='Maandrapportage' || waarde=='Halfjaarrapportage' || waarde=='Jaarrapportage' )
    {
      $('#factuurinfo').show();
    }
    else
    {
      $('#factuurinfo').hide();theForm[checkIndex].checked=false;document.selectForm.factuurnummer.value='';
    }

  setTimeout('checkRapportageInstelling();', 1000);

}



function checkRapportageInstelling()
{
<?
  if($rdata['check_portaalCrmVink']==0)
  {
?>
    var theForm = document.selectForm.elements, z = 0;
    var waarde='';
    var CRM_rapport_vink = 0;
    for(z=0; z<theForm.length;z++)
    {
     if(theForm[z].name == "periode")
     {
        if(theForm[z].checked)
        {
          waarde=theForm[z].value;
        }
     }
     if(theForm[z].name == "CRM_rapport_vink" && theForm[z].type=='checkbox' && theForm[z].checked){CRM_rapport_vink=1;checkIndex=z;}
    }
    if(waarde=='Clienten' && CRM_rapport_vink==1)
    {
      CRM_rapport_vink=false;
      alert("Gebruikte selectie 'Alle clienten' en 'CRM rapportage instellingen' is niet mogelijk.");
      theForm[checkIndex].checked=CRM_rapport_vink;
    }
      enableDisableRapport(CRM_rapport_vink);
  <?
  }
  ?>
}

	function selectSelected()
	{
	  if(document.selectForm['inFields[]'])
	  {
		var inFields  			= document.selectForm['inFields[]'];
		var selectedFields 	= document.selectForm['selectedFields[]'];

		for(j=0; j < selectedFields.options.length; j++)
		  {
 			selectedFields.options[j].selected = true;
		  }
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
	<input type="button" class="<?=$samenvattingStyle?>" onclick="document.selectForm.stap.value='samenvatting';saveSettings();"  id="tabbutton2" value="<?=vt("Samenvatting")?>">
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
  <input type="hidden" name="save" value="" />
  <input type="hidden" name="type" value="" />
  <input type="hidden" name="rapport_types" value="" />
  <input type="hidden" name="portefeuilleIntern" value="" />
  <input type="hidden" name="exportRap" value="" />
  <input type="hidden" name="metConsolidatie" value="<?=$_SESSION['metConsolidatie']?>" />

<table border="0">
  <tr>
    <td width="540" valign="top">
      <fieldset id="Selectie" >
      <legend accesskey="S"><?=vt("Selectie")?></legend>
        <div class="formblock">
          <div class="formlinks"> <?=vt("Periode")?> </div>
          <div class="formrechts">
            <input type="radio" name="periode" value="Clienten"  onclick="javascript:periodeSelected();" <?if($_SESSION['backofficeSelectie']['periode']=='Clienten'){echo 'checked';}?>><?=vt("Alle clienten")?> <br><br>
            <input type="radio" name="periode" value="Maandrapportage" onclick="javascript:periodeSelected();" <?if($_SESSION['backofficeSelectie']['periode']=='Maandrapportage' || $_SESSION['backofficeSelectie']['periode']=='') echo 'checked';?>><?=vt("Maandrapportage")?> <br>
            <input type="radio" name="periode" value="Kwartaalrapportage" onclick="javascript:periodeSelected();" <?if($_SESSION['backofficeSelectie']['periode']=='Kwartaalrapportage') echo 'checked';?> ><?=vt("Kwartaalrapportage")?> <br>
            <input type="radio" name="periode" value="Halfjaarrapportage" onclick="javascript:periodeSelected();" <?if($_SESSION['backofficeSelectie']['periode']=='Halfjaarrapportage') echo 'checked';?> ><?=vt("Halfjaarrapportage")?> <br>
            <input type="radio" name="periode" value="Jaarrapportage" onclick="javascript:periodeSelected();" <?if($_SESSION['backofficeSelectie']['periode']=='Jaarrapportage') echo 'checked';?> ><?=vt("Jaarrapportage")?> <br>
          </div>
        </div>
<?
echo $selectie->createDatumSelectie($_SESSION['backofficeSelectie']);
if($_SESSION['selectieMethode'] == 'portefeuille')
{
?>
<script language="Javascript">

</script>
<table cellspacing="0" border = 1>

<?
  $DB = new DB();
  if(checkAccess($type))
  {
  	$join = "";
  	$beperktToegankelijk = '';
  }
  else
  {
  	$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
  	         JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
  	$beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
  }


	//$query = "SELECT Portefeuille, Client FROM Portefeuilles ".$join. " WHERE Portefeuilles.Einddatum  >=  NOW() $beperktToegankelijk ORDER BY Client ";

	$query=$selectie->queries['ClientPortefeuille'];
  $DB->SQL($query);
  $DB->Query();
  while($gb = $DB->NextRecord())
    $pData[$gb['Portefeuille']]=$gb;
  echo "<br><br>";
  echo $selectie->createEnkelvoudigeSelctie($pData,$_SESSION['backofficeSelectie']);
  echo "<br><br>";
}
else
{
  $DB = new DB();
  $maxVink=35;
  $opties=array(
    'Vermogensbeheerder'=>'Vermogensbeheerder',
    'Accountmanager'=>'accountmanager',
    'TweedeAanspreekpunt'=>'tweedeAanspreekpunt',
    'Client'=>'client',
    'Portefeuille'=>'portefeuilles',
    'Depotbank'=>'depotbank'
  );
  foreach ($opties as $optie=>$omschrijving)
  {
    $data=$selectie->getData($optie);
    if($_SESSION['selectieMethode'] =='vink' && count($data) < $maxVink)
      echo $selectie->createCheckBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
    else
      echo $selectie->createSelectBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
  }
  $opties=array(
    'Risicoklasse'=>'Risicoklasse',
    'ModelPortefeuille'=>'ModelPortefeuille',
    'AFMprofiel'=>'AFMprofiel',
    'SoortOvereenkomst'=>'SoortOvereenkomst',
    'Remisier'=>'Remisier',
    'PortefeuilleClusters'=>'PortefeuilleClusters',
    'selectieveld1'=>'Selectieveld1',
    'selectieveld2'=>'Selectieveld2'
  );
  foreach ($opties as $optie=>$omschrijving)
  {
    $data=$selectie->getData($optie);
    if(count($data) > 1)
    {
      if($_SESSION['selectieMethode'] =='vink' && count($data) < $maxVink)
        echo $selectie->createCheckBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
      else
        echo $selectie->createSelectBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
    }
  }
}
?>
<div id="factuurinfo">
<div class="formblock">
<div class="formlinks"> <?=vt("Factuur toevoegen")?> </div>
<div class="formrechts">
<input type="hidden" value="0" name="inclFactuur">
<input type="checkbox" name="inclFactuur" value="1" <?if($_SESSION['backofficeSelectie']['inclFactuur'] > 0) echo "checked";?>>
<input type="text" name="factuurnummer" size="4" id="inclFactuurCheck" value="<?=$_SESSION['backofficeSelectie']['factuurnummer']?>">
</div>
</div>
</fieldset>
</td>
<td valign="top">

<div class="buttonDiv" style="width:110px" onclick="javascript:print();">&nbsp;&nbsp;<?=maakKnop('pdf.png',array('size'=>16))?> <?=vt("Afdrukken")?></div><br>
<div class="buttonDiv" style="width:110px" onclick="javascript:saveasfile();">&nbsp;&nbsp;<?=maakKnop('disk_blue.png',array('size'=>16))?> <?=vt("Opslaan")?> </div><br>

	<?
	 if($__appvar['master']==false && $rdata['Vermogensbeheerder']<>'MER')
     $xlsStyle="display:none;";
	echo '<input type="button" id="xls_uitvoer" onclick="javascript:xls();" 			  value=" '.vt("XLS uitvoer").' " style="width:110px;'.$xlsStyle.'"><br><br>';
	?>
	<input type="hidden" value="0" name="logoOnderdrukken">
	<input type="checkbox" value="1" id="logoOnderdrukken" name="logoOnderdrukken" <?if($_SESSION['backofficeSelectie']['logoOnderdrukken']==1)echo "checked";?>> <?=vt("Logo onderdrukken")?> <br>
	<input type="hidden" value="0" name="voorbladWeergeven">
	<input type="checkbox" value="1" id="voorbladWeergeven" name="voorbladWeergeven" <?if($_SESSION['backofficeSelectie']['voorbladWeergeven']==1)echo "checked";?>> <?=vt("Voorblad weergeven")?> <br>
	<input type="hidden" value="0" name="memoOnderdrukken">
	<input type="checkbox" value="1" id="memoOnderdrukken" name="memoOnderdrukken"  <?
	if($_SESSION['backofficeSelectie']['memoOnderdrukken'] == 1 || !isset($_SESSION['backofficeSelectie']['memoOnderdrukken']))echo "checked";?>> <?=vt("Memo onderdrukken")?> <br>
	<input type="hidden" value="0" name="inclBrief">
	<input type="checkbox" value="1" id="inclBrief" onclick="javascript:checkBrievenDiv();" name="inclBrief" <?if($_SESSION['backofficeSelectie']['inclBrief']==1)echo "checked";else $brievenDivStyle='style="display: none"';?>> <?=vt("Brief toevoegen")?>  <br>
	<input type="hidden" value="0" name="CRM_rapport_vink">
	<input type="checkbox" value="1" id="CRM_rapport_vink" onclick="javascript:periodeSelected();" name="CRM_rapport_vink"
	<?if($_SESSION['backofficeSelectie']['CRM_rapport_vink']==1 ||  (!isset($_SESSION['backofficeSelectie']['CRM_rapport_vink']) && $rdata['check_module_CRM'] && $rdata['CrmPortefeuilleInformatie']))
	  echo "checked";?>> <?=vt("CRM rapportage instellingen")?> <br>
	<input type="hidden" value="0" name="CRM_extraAdres">
	<input type="checkbox" value="1" id="CRM_extraAdres" name="CRM_extraAdres" <?if($_SESSION['backofficeSelectie']['CRM_extraAdres']==1)echo "checked";?>> <?=vt("CRM extra adressen gebruiken")?> <br>
 <?
if(isset($rapportSettings[$rdata['Layout'].'_b']))
  echo $rapportSettings[$rdata['Layout'].'_b'];
else
  echo $rapportSettings['default_b'];
  

$theDir = realpath(dirname(__FILE__))."/PDF_templates/";
$files=array();
$dir = @opendir($theDir); // open the directory
if(empty($dir))
{
  mkdir($theDir);
  $dir = @opendir($theDir);
}
while($file = readdir($dir)) // loop once for each name in the directory
{
	// if the name is not a directory and the name is not the name of this program file
	if(is_file($theDir.$file))
	{
	  if(!in_array($file,array('.','..')))
  	{
	     $files[]=$file;
  	}
	}
} 
?>
<?=vt("Brief")?>
<select  class="" type="select"  name="pdfBrief" id="pdfBrief" >
<option value=""> --- </option>
<?
foreach($files as $file)
{
  $selected='';
  if(trim($_SESSION['backofficeSelectie']['pdfBrief'])==$file)
   $selected='selected';
  echo "<option $selected value=\"$file\" >$file</option>\n";
}
?>

</select>
  <?

  if($__appvar["bedrijf"] =='HOME' || $__appvar["bedrijf"] =='TEST' || $__debug==true )
  {
    if($_SESSION['backofficeSelectie']['testset']==2)
      $checked='checked';
    else
      $checked='';
    echo '<br><br><br>'.vt("Test opties").':<br><input type="hidden" value="0" name="testset">
  <input type="checkbox" value="2" id="testset" name="testset" '.$checked.' > '.vt("Test selectie").' <br>
  <input type="hidden" value="0" name="debug">
  <input type="checkbox" value="1" id="debug" name="debug"> '.vt("Debug info").'<br>';
  }
  ?>

</div>


</td>
</tr>
</table>
</form>

<?echo progressFrame();?>
<?

if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
