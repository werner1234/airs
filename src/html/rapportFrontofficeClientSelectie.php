<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/04/25 17:13:21 $
 		File Versie					: $Revision: 1.130 $

 		$Log: rapportFrontofficeClientSelectie.php,v $
 		Revision 1.130  2020/04/25 17:13:21  rvv
 		*** empty log message ***

*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/selectOptieClass.php");

$selectie = new selectOptie($PHP_SELF);

$AETemplate = new AE_template();

$htmlRapportageEnabled = getVermogensbeheerderField("HTMLRapportage");
$htmlRapportJS = "htmlRapport = ['ATT','VOLK','TRANS','MUT','MODEL']";


if(!is_array($_SESSION['lastGET']))
{
  $_SESSION['lastGET'] = array();
}

$_SESSION['lastGET']=array_merge($_SESSION['lastGET'],$_GET);

if($_SESSION['metConsolidatie']=='')
{
  $_SESSION['metConsolidatie'] = 0;
}

$type='portefeuille';
$query = "SELECT layout, CrmClientNaam,Export_data_frontOffice,frontofficeClientExcel,Vermogensbeheerders.Vermogensbeheerder,Vermogensbeheerders.rapportDoorkijk FROM Vermogensbeheerders
					  JOIN VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder =  Vermogensbeheerders.Vermogensbeheerder
					 WHERE VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."' LIMIT 1";
$DB = new DB();
$DB->SQL($query);
$DB->Query();
$rdata = $DB->nextRecord();
$frontOfficeData=unserialize($rdata['Export_data_frontOffice']);
if($_SESSION['usersession']['gebruiker']['Beheerder']==1 && $__appvar['master']==true)
{
  unset($frontOfficeData);
}

include_once("rapportFrontofficeClientSelectieLayout.php");

$editScript = "portefeuillesEdit.php";

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addField("Portefeuilles","id",array("width"=>100,"search"=>false));
$list->addField("Portefeuilles","Portefeuille",array("list_width"=>150,"search"=>true));
$list->addField("Portefeuilles","Depotbank",array("description"=>'DP',"list_width"=>60,"search"=>false));
$list->addField("Portefeuilles","Client",array("list_width"=>200,"search"=>true));

if($rdata['CrmClientNaam'] == '1')
{
  $list->addField("","Naam",array("list_width"=>300,'sql_alias'=>'CRM_naw.naam',"search"=>true));
  $list->addField("","crmId",array('sql_alias'=>'CRM_naw.id',"search"=>true,'list_invisible'=>true));
}
else
  $list->addField("Client","Naam",array("list_width"=>300,"search"=>true));
$list->addField("Portefeuilles","consolidatie",array("list_width"=>200,"list_invisible"=>true));

$allow_add = false;
$internDepotToegang='';
if(!checkAccess($type))
{
   if($_SESSION['usersession']['gebruiker']['internePortefeuilles'] == '1')
     $internDepotToegang="OR Portefeuilles.interndepot=1";

   if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
	 {
	   $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang) ";
	 }
	 else
	 {
    	$list->setJoin(" INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	                  JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker");
	    $beperktToegankelijk = " AND  (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
	 }
}


if($_SESSION['lastGET']['actief'] <> "inactief" )
{
  if($_SESSION['lastGET']['actief'] == "eActief" )
    	$alleenActief = " AND Portefeuilles.Einddatum  >=  NOW() AND Portefeuilles.Startdatum > '1970-01-01' AND Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."'";
  else
  	$alleenActief = " AND Portefeuilles.Einddatum  >=  NOW() AND Portefeuilles.Startdatum > '1970-01-01' ";
}
if($_SESSION['lastGET']['letter'] && !$_GET['selectie'])
	$extraWhere = " AND Portefeuilles.Client LIKE '".mysql_escape_string($_SESSION['lastGET']['letter'])."%' ";


if(!isset($_SESSION['portefeuilleIntern']) || $_SESSION['portefeuilleIntern']=='0')
	$extraWhere .= " AND Portefeuilles.interndepot=0 ";
elseif($_SESSION['portefeuilleIntern'] == "1")
  $extraWhere .= " AND Portefeuilles.interndepot=1 ";

if(!isset($_SESSION['metConsolidatie']) || $_SESSION['metConsolidatie']=='0')
  $extraWhere .= " AND Portefeuilles.consolidatie=0 ";
elseif($_SESSION['metConsolidatie'] == "1")
  $extraWhere .= " AND Portefeuilles.consolidatie=1 ";

$extraWhere .= "AND Portefeuilles.Portefeuille NOT IN(SELECT ModelPortefeuilles.Portefeuille FROM ModelPortefeuilles WHERE ModelPortefeuilles.Fixed=1)";

if($rdata['CrmClientNaam'] == '1')
{
  $list->setWhere(" 1 ".$extraWhere.$alleenActief.$beperktToegankelijk." ");
  $list->setJoin("LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.Portefeuille INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	                JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker");
}
else
{
  $list->setWhere("Portefeuilles.Client = Clienten.Client " . $extraWhere . $alleenActief . $beperktToegankelijk . " ");
}

$_GET['sort'][] = "Portefeuilles.Client";
$_GET['direction'][] = "ASC";

// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

session_start();
$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));


$html = "<script language=JavaScript src=\"javascript/ae_ajax_client.js\" type=text/javascript></script>";

if($mutSettings=='')
{
  $DB=new DB();
  $query="SELECT Grootboekrekeningen.Grootboekrekening, Grootboekrekeningen.Omschrijving FROM Grootboekrekeningen WHERE (Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Onttrekking='1' OR Grootboekrekeningen.Opbrengst = '1' OR  Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1')";
  $DB->SQL($query);
  $DB->Query();
  $mutSettings .='<table>';
  while($gb=$DB->nextRecord())
  {
    if($_SESSION['lastPost']['MUT_'.$gb['Grootboekrekening']] == 1)
      $check='checked';
    else
      $check = '';
    $mutSettings.='<tr><td>'.$gb['Omschrijving'].'</td><td><input type="checkbox" name="MUT_'.$gb['Grootboekrekening'].'" value="1" '.$check.' ></td></tr>';
  }
  $mutSettings.='</table>';
}

 if(isset($rapportSelectie[$rdata['layout']]))
 {
    $html .= $rapportSelectie[$rdata['layout']];
 }
 else
 {
  $html .= "<script>
  function settings() 
  {
  ";
  $scenarioHtml="  parent.frames['content'].$('#SCENARIO_Settings').hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"SCENARIO\")
 			{
 				parent.frames['content'].$('#SCENARIO_Settings').show();
 			}
 		}
  }";
  if(checkAccess($type))
  {
    if(checkOrderAcces('handmatig_opslaan')==false)
    {
      $html .="
  var number=0;
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 		  number++;
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"MODEL\" && number == 1)
 			{
 			  parent.frames['content'].document.getElementById('Model_Settings').style.visibility=\"visible\";
 			}
 			else
 			{
 			  parent.frames['content'].document.getElementById('Model_Settings').style.visibility=\"hidden\";
 			}
		}
  }
  $scenarioHtml
";
    }
    else
    {
      $html .="
  var number=0;
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 		  number++;
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"MODEL\" && number == 1)
 			{
 			  parent.frames['content'].document.getElementById('orderButton').style.visibility=\"visible\";
 			  parent.frames['content'].document.getElementById('Model_Settings').style.visibility=\"visible\";
 			}
 			else
 			{
 			  parent.frames['content'].document.getElementById('orderButton').style.visibility=\"hidden\";
 			  parent.frames['content'].document.getElementById('Model_Settings').style.visibility=\"hidden\";
 			}
		}
  }
  $scenarioHtml
";
    }
  }
  else
  { 

  $html .="
  parent.frames['content'].$('#MUT_Settings').hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"MUT\")
 			{
 				parent.frames['content'].$('#MUT_Settings').show();
 			}
 		}
  }
  
$scenarioHtml
";
  }

  $html.="
    parent.frames['content'].$('#VKMA_Settings').hide();
    parent.frames['content'].$('#JOURNAAL_Settings').hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"VKMA\")
 			{
 				parent.frames['content'].$('#VKMA_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"JOURNAAL\")
 			{
 				parent.frames['content'].$('#JOURNAAL_Settings').show();
 			}
 		}
  }
  
  ";
  $html .="}</script>";
 }



$html .="
<script>
";
if ($htmlRapportageEnabled)
{
  $html .= "   ".$htmlRapportJS.";   ";
}
else
{
  $html .= "   htmlRapport = ['dummy']; ";
}

$html .= "


function doStuff()
{
  settings();
 
  var xlsRapport = [";  
$first=true;
$xlsRapporten=array();
if(!isset($frontOfficeData))
{
    foreach($__appvar["Rapporten"] as $rapport=>$value)
    {
      if($__appvar['bedrijf']=='HOME')
      {
        if($first==true)
          $first=false;
        else
          $html.=",";
        $html .="'".$rapport."'";
        $xlsRapporten[]=$rapport;
      }
    }
}
else
{
foreach($frontOfficeData as $rapport=>$rapportData)
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
}
$html .="];
	document.selectForm.selected.value = \"\";
	var tel =0;
  parent.frames['content'].$('#xls_uitvoer').hide();
  parent.frames['content'].$('#btnHTML').hide()
  $('#btnHTML').hide()
  //console.log(xlsRapport);
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
      if(htmlRapport.indexOf(document.selectForm.rapport_type[i].value)>=0)
      {
        parent.frames['content'].$('#btnHTML').show();
        $('#btnHTML').show();
      }
		}
	}

	executeRequest('ae_ajax_server.php','selectForm', 'storeRapportSelection', responseHandler);
}

function responseHandler(requester,formName)
{
	var theForm = document.forms[formName];
	return true;
}
</script>
";

$html .= "<b>".vt("Selecteer rapport")."</b><br>


<div id=\"wrapper\" style=\"overflow:hidden;\"> 
<div class=\"buttonDiv\" style=\"width:65px;float:left;\" onclick=\"checkAll(1);\">&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' />".vt("Alles")."</div>
<div class=\"buttonDiv\" style=\"width:65px;float:left;\" onclick=\"checkAll(0);\">&nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon' />".vt("Niets")."</div>
</div><br>

<form name=\"selectForm\">";


$xlsStyle="display:none;";
if(is_array($frontOfficeData))
{
  foreach($frontOfficeData as $rapport=>$rapportData)
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
    $checked=in_array($rapport,$_SESSION['rapportSelection']);
    if($checked && in_array($rapport,$xlsRapporten))
      $xlsStyle="";

    if(isset($frontOfficeData[$rapport]['longName']))
      $long=$frontOfficeData[$rapport]['longName'];
    else
      $long=$rapportData['omschrijving'];

    if(isset($frontOfficeData[$rapport]['shortName']))
      $short=$frontOfficeData[$rapport]['shortName'];
    else
      $short=$rapport;
      
    if($rapportData['toon'] == 1)
      $rapporten['open'][]=  array('rapport'=>$rapport,'omschrijving'=>$long.' ('.$rapportData['volgorde'].' '.$rapport.')','checked'=>$checked,'short'=>$short);
    elseif($rapportData['toonNiet'] == 0)
      $rapporten['closed'][]=array('rapport'=>$rapport,'omschrijving'=>$long.' ('.$rapportData['volgorde'].' '.$rapport.')','checked'=>$checked,'short'=>$short);
  }

  $script="<script>
  function checkAll(value) 
  {";
  foreach ($rapporten['open'] as $rapportData)
  {
  	$html .= "<input type=\"checkbox\" value=\"".$rapportData['rapport']."\" name=\"rapport_type\" id=\"".$rapportData['rapport']."\" onClick=\"JavaScript:doStuff();\" ".(($rapportData['checked']==1)?"checked":"").">  ".
					   "<label for=\"".$rapportData['rapport']."\" title=\"".$rapportData['omschrijving']."\">".$rapportData['short']." </label><br>";
    $script .="document.selectForm.elements['".$rapportData['rapport']."'].checked=value;\n";        
             
  }
  $script.="}
  </script>";
  $html .= "<br />";

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
'.$script.'
<div id="masterdiv">
<div class="menutitle" onclick="SwitchMenu(\'subNaw0\')">'.vt("Overige").'</div><span class="submenu" id="subNaw0">';
  }
  foreach ($rapporten['closed'] as $rapportData)
  {
    $html .= "<input type=\"checkbox\" value=\"".$rapportData['rapport']."\" name=\"rapport_type\" id=\"".$rapportData['rapport']."\" onClick=\"JavaScript:doStuff();\" ".(($rapportData['checked']==1)?"checked":"").">  ".
					   "<label for=\"".$rapportData['rapport']."\" title=\"".$rapportData['omschrijving']."\">".$rapportData['short']." </label><br>";
  }
  if(count($rapporten['closed']) >0)
    $html .= '</span></div>';

}
else
{

foreach($__appvar["Rapporten"] as $key=>$value)
{
	if(in_array($key,$_SESSION['rapportSelection']))
  {
		$selected = "checked";
    if(in_array($key,$xlsRapporten))
      $xlsStyle="";
  }  
	else
		$selected = "";
	$html .= "<input type=\"checkbox\" value=\"".$key."\" name=\"rapport_type\" id=\"".$key."\" onClick=\"JavaScript:doStuff();\" ".$selected.">  ".
					 "<label for=\"".$key."\" title=\"".$value."\">".$key." </label><br>";
}
}
$html .= "<input name=\"selected\" value=\"\" type=\"hidden\">\n";
$html .= "</form>";

$selectie->getInternExternActive();



$html .="<br> <iframe src=\"laatsteValuta.php\" width=\"100%\" height=\"80\" marginwidth=\"0\" marginheight=\"0\" hspace=\"0\" vspace=\"0\" align=\"middle\" frameborder=\"0\"></iframe> ";


$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");
$_SESSION['submenu']->onLoad = " onLoad=\"settings();\" ";

$content['jsincludes'] .= "<script language=JavaScript src=\"javascript/ae_ajax_client.js\" type=text/javascript></script>";
//$content[javascript] .= "";
//$content['body'] = 'onload="javascript:initSettings();"';

$content['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$content['calendar'] = $kal->get_load_files_code();
$content['style'] .=$editcontent['style'];
echo template($__appvar["templateContentHeader"],$content);
// selecteer laatst bekende valutadatum
$totdatum = getLaatsteValutadatum();

$disableCheckboxes="function enableDisableRapport(value){";
foreach ($__appvar["Rapporten"] as $rapport=>$omschrijving)
  $disableCheckboxes .="parent.frames['submenu'].selectForm.elements['".$rapport."'].disabled=value;\n";
$disableCheckboxes.="}";


$aeMessage = new AE_Message();
echo $aeMessage->getFlash();

?>
<script type="text/javascript">

var counter=0;
function doStore()
{
	executeRequest('ae_ajax_server.php','selectForm', 'storeDate', responseHandler);
}

function responseHandler(requester,formName)
{
	var theForm = document.forms[formName];
	return true;
}

<?
echo $disableCheckboxes;
?>

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

function print()
{
	document.selectForm.target = "_blank";
	document.selectForm.extra.value = "";
	document.selectForm.action = "rapportFrontofficeClientAfdrukken.php?counter="+counter;
 	setRapportTypes();
	document.selectForm.save.value="0";
	selectSelected();
	document.selectForm.submit();
	counter++;
}

function html()
{
	document.selectForm.target = "content";
	document.selectForm.extra.value = "";
	document.selectForm.action = "rapportFrontofficeClientAfdrukkenHtml.php?counter="+counter;
 	setRapportTypes();
	document.selectForm.save.value="0";
	selectSelected();
	document.selectForm.submit();
	counter++;
}

function saveasfile()
{
	document.selectForm.target = "";
	document.selectForm.extra.value = "";
	document.selectForm.action = "rapportFrontofficeClientAfdrukken.php?counter="+counter;
	setRapportTypes()
	document.selectForm.save.value="1";
	document.selectForm.submit();
	counter++;
}

function order()
{
  document.selectForm.target = "";

  document.selectForm.extra.value = "order";
  document.selectForm.action = "rapportFrontofficeClientAfdrukken.php?counter="+counter;
	setRapportTypes()
	document.selectForm.submit();
	counter++;
}

function xls()
{
  document.selectForm.target = "_blank";
  document.selectForm.extra.value = "xls";
  document.selectForm.action = "rapportFrontofficeClientAfdrukken.php?counter="+counter;
	setRapportTypes()
	document.selectForm.save.value="1";
	document.selectForm.submit();
	counter++;
}

	function selectSelected()
	{
	  if(document.selectForm['selectedFields[]'])
	  {

		var selectedFields 	= document.selectForm['selectedFields[]'];

		for(j=0; j < selectedFields.options.length; j++)
		  {
 			selectedFields.options[j].selected = true;
		  }
	  }
	}
</script>


<?php




$totdatum = getLaatsteValutadatum();

$jr = substr($totdatum,0,4);
$totJul=db2jul($totdatum);
$totFromDatum=date("d-m-Y",$totJul);

$maand = substr($totdatum,5,2);
$kwartaal = ceil(date("m",$totJul) / 3);

$datumSelctie['beginMaand']=date("d-m-Y",mktime(0,0,0,$maand-1,0,$jr));
$datumSelctie['eindMaand']=date("d-m-Y",mktime(0,0,0,$maand,0,$jr));
$datumSelctie['beginKwartaal']=date("d-m-Y",mktime(0,0,0,$kwartaal*3-5,0,$jr));
$datumSelctie['eindKwartaal']=date("d-m-Y",mktime(0,0,0,$kwartaal*3-2,0,$jr));
$datumSelctie['beginJaar']=date("d-m-Y",mktime(0,0,0,1,1,$jr-1));
$datumSelctie['eindJaar']=date("d-m-Y",mktime(0,0,0,13,0,$jr-1));
$datumSelctie['beginMaand2']=date("d-m-Y",mktime(0,0,0,$maand,0,$jr));
$datumSelctie['beginKwartaal2']=date("d-m-Y",mktime(0,0,0,$kwartaal*3-2,0,$jr));
$datumSelctie['beginJaar2']=date("d-m-Y",mktime(0,0,0,1,1,$jr));

foreach ($datumSelctie as $naam=>$datum)
{
  if(substr($naam,0,5)=='begin' && substr($datum,0,5)=='31-12')
    $datumSelctie[$naam]="01-01-".((substr($datum,6,4))+1);
}


$labelClass = 'col-3 col-md-3';
$buttonClass = 'col-9 col-md-8';
$inp = array ('name' =>"datum_van",'value' =>(!empty($_SESSION['rapportDateFrom']))?$_SESSION['rapportDateFrom']:date("d-m-Y",mktime(0,0,0,1,1,$jr)),'size'  => "11");
$inp2 = array ('name' =>"datum_tot",'value' =>(!empty($_SESSION['rapportDateTm']))?$_SESSION['rapportDateTm']:date("d-m-Y",db2jul($totdatum)),'size'  => "11");

$selectieHtml = '';
$selectieHtml .= $selectie->getHtmlActiveAllPortefeuille();
$selectieHtml .= $selectie->getHtmlInterneExternePortefeuille();
$selectieHtml .= $selectie->getHtmlConsolidatie();





?>
<div class="container-fluid">
  <br />
  <form action="rapportFrontofficeClientAfdrukken.php" method="POST" target="_blank" name="selectForm">

    <input type="hidden" name="posted" value="true" />
    <input type="hidden" name="save" value="" />
    <input type="hidden" name="rapport_types" value="" />
    <input type="hidden" name="extra" value="" />
  
    <div class="formHolder" >
      <div class="formTabGroup ">
        <?=$AETemplate->parseBlockFromFile('rapportFrontoffice/tabbuttons.html', array(
          'clienten'      => 'active'
        ))?>
      </div>
      
      <div class="formTitle textB"><?=vt("Selectie")?></div>
      <div class="formContent padded-10">
  
        <div class="row no-gutters">
          <div class="col-6 col-md-4 col col-xl-3" style="border-right: #d9d9d9 1px solid; height: 161px;     padding-left: 20px;"><?=$selectieHtml;?></div>
          <div class="col-6 col-md-4 col col-xl-3" style="border-right: #d9d9d9 1px solid; text-align: center; height: 161px;">
            <?=
            
            $AETemplate->parseBlockFromFile('rapportFrontoffice/datum_selectie.html', array(
              'inpvalue'          => $inp['value'],
              'inpname'           => $inp['name'],
              'inp2value'         => $inp2['value'],
              'inp2name'          => $inp2['name'],
    
    
              'beginMaand'        => $datumSelctie['beginMaand'],
              'beginKwartaal'     => $datumSelctie['beginKwartaal'],
              'beginJaar'         => $datumSelctie['beginJaar'],
              'eindMaand'         => $datumSelctie['eindMaand'],
              'eindKwartaal'      => $datumSelctie['eindKwartaal'],
              'eindJaar'          => $datumSelctie['eindJaar'],
    
              'beginMaand2'       => $datumSelctie['beginMaand2'],
              'beginKwartaal2'    => $datumSelctie['beginKwartaal2'],
              'beginJaar2'        => $datumSelctie['beginJaar2'],
              'totFromDatum'      => $totFromDatum,
    
              'startThisYear'     => '01-01-' . date('Y'),
              'totDatumYear'      => date('Y', strtotime($totdatum)),
              'totDatum'          => date('d-m-Y', strtotime($totdatum)),
              'fullTotDatumJs'    => date('Y/m/d H:i:s', strtotime($totdatum))
            ))
            
            ?>
          </div>
  
          <div class="col col col-xl-2" style="border-right: #d9d9d9 1px solid; height: 161px;">
  
            <div class="formblock">
              <input type="checkbox" value="1" id="logoOnderdrukken" name="logoOnderdrukken"> <?=vt("Logo onderdrukken")?> <br />
              <?=( $rdata['rapportDoorkijk'] == 1 ? '<input type="checkbox" checked value="1" id="doorkijk" name="doorkijk"> ' . vt("Doorkijk") . ' <br />' : '');?>
              
              <input type="checkbox" value="1" id="voorbladWeergeven" name="voorbladWeergeven"> <?=vt("Voorblad weergeven")?><br />
              <?=( $__appvar['master'] == true ? '<input type="checkbox" value="1" id="debug" name="debug"> '.vt("Debug").' <br />' : '');?>
              <input type="checkbox" value="1" id="crmInstellingen" name="crmInstellingen" onclick="if(this.checked){enableDisableRapport(true);}else{enableDisableRapport(false);}" > <?=vt("CRM Instellingen")?><br />
              <input type="checkbox" value="1" id="anoniem" name="anoniem"> <?=vt("Anonieme rapportage")?><br />
              <input type="checkbox" value="1" id="passwd" name="passwd"> <?=vt("Met wachtwoord")?><br /><br />
              <?=( ($__appvar['master'] == true || $__appvar['bedrijf']=='HOME' || $__appvar['bedrijf']=='TEST' ) ? '<input type="text" value="" size="1" id="layout" name="layout"> <?=vt("Layout")?> <br />' : '');?>
            </div>
            
          </div>
          
          <div class="col-2  col-xl-1  col-md-2  " style="">
            <div class=" btn-group-text-left " style="margin-left:10px">
              <div class="btn btn-default" style="width: 140px;" onclick="javascript:print();">&nbsp;&nbsp;<i style="color:red" class="fa fa-file-pdf-o fa-fw" aria-hidden="true"></i> <?=vt("Tonen")?></div><br /><br />
              <div class="btn btn-default" style="width: 140px;" onclick="javascript:saveasfile();">&nbsp;&nbsp;<i style="color:blue"  class="fa fa-floppy-o fa-fw" aria-hidden="true"></i> <?=vt("Opslaan")?> </div><br /><br />
  
            <?
            if($__appvar['bedrijf']=='HOME') {
              $xlsStyle="";
            }
            
            echo '<div class="btn btn-default" style="width: 140px;" id="xls_uitvoer" onclick="javascript:xls();">&nbsp;&nbsp;<i style="color:green"  class="fa fa-file-excel-o fa-fw" aria-hidden="true"></i> '.vt("XLS uitvoer").' </div><br /><br />';
            echo '<div class="btn btn-default" style="width: 140px;" id="btnHTML" onclick="javascript:html();">&nbsp;&nbsp;<i class="fa fa-line-chart fa-fw" aria-hidden="true"></i> '.vt("HTML").'</div><br /><br />';
            
            if((checkAccess($type) && GetModuleAccess('ORDER') < 2) || checkOrderAcces('rapportages_aanmaken') === true) {
              echo '<input class="btn btn-default" type="button" onclick="javascript:order();" 			value=" Order " style="width: 140px; visibility:hidden;     display: none;" id="orderButton">';
            }
            ?>
            </div>
          </div>
        </div>
        
      </div>
  
      <div class="formTabFooter">
        <?=$selectie->letterToolbar ();?>
      </div>
      
    </div>
  </div>
</div>
  
  
  <div class="baseRow">
    
    <?php
    $blockSize = 'col-7 col-md-7 col col-xl-7';
    ?>
    
    <div class="<?=$blockSize;?>" id="PortefueilleSelectie">
    
    
    <!-- -->
  <div class="formHolder"  id="" >
    <div class="formTitle textB"><?=vt("Portefeuille selectie")?></div>
    <div class="formContent formContentForm " id="">

<table class="table" cellspacing="0">
<?=$list->printHeader();?>
<?php
$alleenNaw=GetModuleAccess('alleenNAW');
if (!$_SESSION['btr']) {
  if (GetModuleAccess("CRM") && $alleenNaw==0)
  {
  //  $crmLink = "<td><a href=\"CRM_nawEdit.php?do=viaFrontOffice&port={Portefeuille_value}\">[ CRM ]</a></td>";
  $crmLink = "<td><a href=\"CRM_nawEdit.php?do=viaFrontOffice&port={Portefeuille_value}\">
              <img src=\"images/relaties.gif\" alt=\"CRM\" width=\"16\" height=\"16\" border=\"0\"></a></td>";
  }
  elseif($alleenNaw==1)
  {
  $crmLink = "<td><a href=\"CRM_nawOnlyEdit.php?do=viaFrontOffice&port={Portefeuille_value}\">
              <img src=\"images/relaties.gif\" alt=\"CRM\" width=\"16\" height=\"16\" border=\"0\"></a></td>";  
  }
}
$template = '<tr class="list_dataregel" onmouseover="this.className=\'list_dataregel_hover\'" onmouseout="this.className=\'list_dataregel\'" onClick="javascript:document.getElementById(\'{Portefeuille_value}\').checked=true;">
<td class="list_button">
	<div class="icon"><input type="radio" value="{Portefeuille_value}" name="Portefeuille" id="{Portefeuille_value}"></div>
</td>
<td class="listTableData"  width="150" align="left" >{Portefeuille_value} &nbsp;</td>
<td class="listTableData"  width="50" align="left" >{Depotbank_value} &nbsp;</td>
<td class="listTableData"  width="150" align="left" >{Client_value} &nbsp;</td>
<td class="listTableData"  align="left" >{Naam_value} &nbsp;</td>
'.$crmLink.'
</tr>';

if($list->records()==1)
  $recordSelected='checked';
else
  $recordSelected='';

while($data = $list->getRow())
{
  if($data['crmId']['value'] > 0 && $alleenNaw=0)
    $link2=  "<td><a href=\"CRM_nawEdit.php?do=viaFrontOffice&port={Portefeuille_value}&lastTab=9&frameSrc=".base64_encode("CRM_naw_dossierEdit.php?action=new&toList=1&rel_id=".$data['crmId']['value'])."\">
              ".maakKnop('note_new.png',array('size'=>16,'tooltip'=>'Nieuw gespreksverslag'))."</a></td>";
  else
    $link2='';

  if($data['consolidatie']['value']==1){$trclass='list_dataregel_geel';}
  else{$trclass='list_dataregel';}
  
  $newCrmLink=str_replace('{Portefeuille_value}',urlencode($data['Portefeuille']['value']),$crmLink);
  
  $template = '<tr data-lineid="{Portefeuille_value}" class="'.$trclass.'" onmouseover="this.className=\'list_dataregel_hover\'" onmouseout="this.className=\''.$trclass.'\'" onClick="javascript:document.getElementById(\'{Portefeuille_value}\').checked=true;">
<td class="list_button">
	<div class="icon"><input type="radio" '.$recordSelected.' value="{Portefeuille_value}" name="Portefeuille" id="{Portefeuille_value}"></div>
</td>
<td class="listTableData" data-field="'.$data['Portefeuille']['field'].'" width="150" align="left" >{Portefeuille_value} &nbsp;</td>
<td class="listTableData" data-field="'.$data['Depotbank']['field'].'" width="50" align="left" >{Depotbank_value} &nbsp;</td>
<td class="listTableData" data-field="'.$data['Client']['field'].'" width="150" align="left" >{Client_value} &nbsp;</td>
<td class="listTableData" data-field="'.$data['Naam']['field'].'" align="left" >{Naam_value} &nbsp;</td>
'.$newCrmLink.$link2.'
</tr>';
 

	echo $list->buildRow($data,$template,"");
}


?>
</table>
    </div>
  </div>


    </div>
  
    <div class="col-4 col-md-4 col col-xl-4" >
    
    
    
<?

if( isset ($rapportSettings[$rdata['layout']]) ) {
  echo $rapportSettings[$rdata['layout']];
}

if($rdata['layout'] <> 13 && $rdata['layout'] <> 5 && $rdata['layout'] <> 12 )
{
  $db=new DB();
  $query="SELECT id FROM inflatiepercentages limit 1";
  if($db->QRecords($query)>0)
  {
    $inflatieVinkje="<input type=\"checkbox\" value=\"1\" name=\"scenario_inflatie\"> ".vt("Inclusief inflatie").". ";
  }
  else
  {
    $inflatieVinkje='';
  }

?>
  
      <!-- -->
      <div class="formHolder"  id="MUT_Settings" style="display: none; ">
        <div class="formTitle textB"><?=vt("Mutatie-overzicht")?></div>
        <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
          <?=$mutSettings?>
        </div>
      </div>
      
      
      <!-- -->
      <div class="formHolder"  id="mmIndex_Settings" style="display: none; ">
        <div class="formTitle textB"><?=vt("MM-index")?></div>
        <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
          <?=$mmIndexSettings?>
        </div>
      </div>
  
  
      <!-- SCENARIO_Settings -->
      <div class="formHolder"  id="SCENARIO_Settings" style="display: none; ">
        <div class="formTitle textB"><?=vt("Senario")?></div>
        <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
          <input type="checkbox" value="1" name="scenario_portefeuilleWaardeGebruik"> <?=vt("Gebruik waarde op rapportage datum")?>.</br>
          <input type="checkbox" value="1" name="scenario_werkelijkVerloop"> <?=vt("Werkelijk verloop")?>.</br>
          <?=$inflatieVinkje?>
        </div>
      </div>
  
  
      <script>
        function checkWaardeprognoseSettings()
        {
          if($("#vkma_clientselectie").prop('checked')==true)
          {
            $("#vkma_naam").prop("disabled", true);
            $("#vkma_naam").css('background','#eee');
            $("#vkma_naam").val('');
            $("#vkma_bedrag").prop("disabled", true);
            $("#vkma_bedrag").css('background','#eee');
            $("#vkma_bedrag").val('');
          }
          else
          {
            $("#vkma_naam").prop("disabled", false);
            $("#vkma_naam").css('background','');
            $("#vkma_bedrag").prop("disabled", false);
            $("#vkma_bedrag").css('background','');
          }
        }
      </script>
  
      <div class="formHolder"  id="VKMA_Settings" style="display: none;">
        <div class="formTitle textB"><?=vt("Kostenmaatstaf ex-ante")?></div>
        <div class="formContent formContentForm pl-4 pt-2 PB-2" id="VKMA_form">
      
          <div class="formblock">
            <div class="formlinks"> <?=vt("Via clientselectie")?> </div>
            <div class="formrechts"> <input type="checkbox" id="vkma_clientselectie" name="vkma_clientselectie" onclick="javascript:checkWaardeprognoseSettings();" value="1" checked size="25"> </div>
          </div>
      
          <div class="formblock">
            <div class="formlinks"> <?=vt("Naam")?> </div>
            <div class="formrechts"> <input type="text" id="vkma_naam" name="vkma_naam" style="background:#ccc" value="" disabled size="25"> </div>
          </div>
      
          <div class="formblock">
            <div class="formlinks"> <?=vt("Bedrag")?> </div>
            <div class="formrechts"> <input type="text" id="vkma_bedrag" name="vkma_bedrag" style="background:#ccc" value="" disabled size="15">  </div>
          </div>

          <div class="formblock">
            <div class="formlinks"> <?=vt("Eindjaar")?> </div>
            <div class="formrechts"> <input type="text" name="vkma_eindjaar" value="" size="4"> </div>
          </div>


            <table style="margin-left: 13px; width: 330px;">
              <tr><td><strong>Kostencomponenten</strong></td></tr>
              <tr><td><b>Kostensoort</b></td><td><b>Bedrag</b></td><td><b>%</b></td><td><strong>BTW-percentag</strong></td></tr>
              <tr><td>Beheerkosten</td>      <td><input type="text" name="vkma_bedrag_beheer" value="" size="2" ></td>    <td><input type="text" name="vkma_kosten_beheer" value="" size="2" ></td>           <td><input type="text" name="vkma_btw_beheer" value="" size="2" ></td></tr>
              <tr><td>Servicekosten</td>     <td><input type="text" name="vkma_bedrag_service" value="" size="2" ></td>   <td><input type="text" name="vkma_kosten_service" value="" size="2" ></td></tr>
              <tr><td>Transactiekosten</td>  <td><input type="text" name="vkma_bedrag_transactie" value="" size="2" ></td><td><input type="text" name="vkma_kosten_transactie" value="" size="2" ></td></tr>
              <tr><td>Overige bankkosten</td><td><input type="text" name="vkma_bedrag_bank" value="" size="2" ></td>      <td><input type="text" name="vkma_kosten_bank" value="" size="2" ></td></tr>
            </table>
        </div>
  
      </div>
  
      <div class="formHolder"  id="JOURNAAL_Settings" style="display: none;">
        <div class="formTitle textB"><?=vt("Journaal opties")?></div>
        <div class="formContent formContentForm pl-4 pt-2 PB-2" id="JOURNAAL_form">
      
          <div class="formblock">
            <div class="formlinks"> <?=vt("Rekeningmutaties per rekening")?> </div>
            <div class="formrechts"> <input type="hidden" name="journaal_perRekening" value="0"> <input type="checkbox" id="journaal_perRekening" name="journaal_perRekening" value="1" > </div>
          </div>
      </div>
      

<td valign="top">
  <fieldset id="Model_Settings" style="visibility:hidden">
    <div class="formblock">
  	<u>Niveau</u><br>
  	<input type="radio" name="modelcontrole_level" value="fonds" checked> <?=vt("Fonds")?><br>
	  <input type="radio" name="modelcontrole_level" value="beleggingscategorie" ><?=vt("Categorie")?><br>
	  <input type="radio" name="modelcontrole_level" value="beleggingssector" ><?=vt("Sector")?><br>
	  <input type="radio" name="modelcontrole_level" value="Regio" ><?=vt("Regio")?><br>
	</fieldset>
  </div>
  
  
  
  
    </td>
  
<?
}
?>

</tr>
</table>




</form>

<script>
  function checkHTMLButton()
  {
    <?=( $htmlRapportageEnabled == 1 ? $htmlRapportJS.';' : 'htmlRapport = ["dummy"]; ');?>

    jQuery.each( htmlRapport, function( i, val ) {
      if (parent.frames['submenu'].$("#" + val).is(":checked"))
      {
        $('#btnHTML').show();
      }
    });

  }

  $(document).ready(function()
  {
    $('#btnHTML').hide();
    var <?=$htmlRapportJS?>;
    var htmlRapportageEnabled = <?=($htmlRapportageEnabled)?1:0;?>;
    if ( htmlRapportageEnabled == 1)  // kijk of knop getoond moet worden als er al vinkjes zijn..
    {
      setTimeout(checkHTMLButton,1000);  // wacht 1 sec om laden submenu te laten afronden
    }
  });
</script>
<?

logAccess();

if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
