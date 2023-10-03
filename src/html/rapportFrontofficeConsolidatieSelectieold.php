<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2019/11/13 15:43:38 $
 		File Versie					: $Revision: 1.2 $

 		$Log: rapportFrontofficeConsolidatieSelectieold.php,v $
 		Revision 1.2  2019/11/13 15:43:38  rm
 		7929
 		
 		Revision 1.1  2019/10/11 13:30:24  rm
 		7929
 		
 		Revision 1.53  2019/02/27 13:48:29  rvv
 		*** empty log message ***
 		
 		Revision 1.52  2018/12/12 16:16:48  rvv
 		*** empty log message ***
 		
 		Revision 1.51  2018/10/13 17:16:37  rvv
 		*** empty log message ***
 		
 		Revision 1.50  2018/09/12 14:46:13  rvv
 		*** empty log message ***
 		
 		Revision 1.49  2018/08/27 17:17:09  rvv
 		*** empty log message ***
 		
 		Revision 1.48  2018/07/18 15:48:59  rvv
 		*** empty log message ***
 		
 		Revision 1.47  2018/07/12 08:47:10  rvv
 		*** empty log message ***
 		
 		Revision 1.46  2018/07/08 08:22:16  rvv
 		*** empty log message ***
 		
 		Revision 1.45  2018/05/06 15:00:36  rvv
 		*** empty log message ***
 		
 		Revision 1.44  2018/05/06 11:31:30  rvv
 		*** empty log message ***
 		
 		Revision 1.43  2018/04/14 17:21:13  rvv
 		*** empty log message ***
 		
 		Revision 1.42  2018/04/12 09:35:47  rvv
 		*** empty log message ***
 		
 		Revision 1.41  2018/04/12 06:07:15  rvv
 		*** empty log message ***
 		
 		Revision 1.40  2018/04/11 15:58:03  rvv
 		*** empty log message ***
 		
 		Revision 1.39  2018/04/11 15:19:17  rvv
 		*** empty log message ***
 		
 		Revision 1.38  2018/03/14 17:14:53  rvv
 		*** empty log message ***
 		
 		Revision 1.37  2018/01/03 16:24:37  rvv
 		*** empty log message ***
 		
 		Revision 1.36  2017/07/15 16:11:15  rvv
 		*** empty log message ***
 		
 		Revision 1.35  2017/06/25 14:56:20  rvv
 		*** empty log message ***
 		
 		Revision 1.34  2017/04/26 14:31:18  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2017/01/21 17:10:10  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2016/05/15 17:13:16  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2016/05/04 16:22:30  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2015/08/08 11:34:24  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2015/06/06 15:22:46  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2015/06/06 15:18:52  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2015/06/03 15:04:51  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2015/05/27 16:15:51  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2015/05/27 11:50:09  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2015/01/11 12:47:44  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2014/12/20 22:00:14  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2014/12/06 18:12:07  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2014/09/17 15:07:39  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2014/08/30 16:28:19  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2013/09/07 15:59:15  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2013/07/17 15:50:29  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2013/05/12 11:17:29  rvv
 		*** empty log message ***
 		

*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/selectOptieClass.php");
$selectie=new selectOptie();
$selectie->getInternExternActive();
$type='portefeuille';

$htmlRapportageEnabled = getVermogensbeheerderField("HTMLRapportage");
$htmlRapportJS = "htmlRapport = ['ATT','VOLK','TRANS','MUT','MODEL']";


if(!is_array($_SESSION['lastGET']))
  $_SESSION['lastGET']=array();
$_SESSION['lastGET']=array_merge($_SESSION['lastGET'],$_GET);


if($_SESSION['lastGET']['actief'] <> "inactief" )
{
  if($_SESSION['lastGET']['actief'] == "eActief" )
    $alleenActief = " WHERE Portefeuilles.consolidatie=0 AND Portefeuilles.Einddatum  >=  NOW() AND Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."'";
  else
    $alleenActief = " WHERE Portefeuilles.consolidatie=0 AND Portefeuilles.Einddatum  >=  NOW() ";
  $metInactief=false;
}
else
{
  $alleenActief = "WHERE Portefeuilles.consolidatie=0 ";
  $metInactief=true;
}


$DB = new DB();
if(checkAccess($type))
{
  $join = "";
  $beperktToegankelijk = '';
}
else
{
  $join = " INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND  VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	  				JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
  
  if($_SESSION['usersession']['gebruiker']['internePortefeuilles'] == '1')
    $internDepotToegang="OR Portefeuilles.interndepot=1";
  
  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
    $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang) ";
  else
    $beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
  
}


if(count($_POST['Vermogensbeheerder'])>0)
{
  include_once("../classes/portefeuilleSelectieClass.php");
  $pSelectie = new portefeuilleSelectie($_POST,'',false,$metInactief);
  
  $records = $pSelectie->getRecords();
  $portefeuilleList = $pSelectie->getSelectie();
  $portefeuilleWhere=" AND Portefeuilles.Portefeuille IN('".implode("','",array_keys($portefeuilleList))."')";
  $enkelVoudigeSelectieStyle='';
  $content['body'] 				= " onLoad=\"javascript:loadPortefeuilles('alles')\" ";
}
else
{
  $portefeuilleWhere = '';
  $enkelVoudigeSelectieStyle='display:none';
}



if($_SESSION['lastGET']['letter'] && !$_GET['selectie'])
  $portefeuilleWhere = " AND Portefeuilles.Client LIKE '".mysql_escape_string($_SESSION['lastGET']['letter'])."%' ";

if(!isset($_SESSION['portefeuilleIntern']) || $_SESSION['portefeuilleIntern']=='0')
  $portefeuilleWhere .= " AND Portefeuilles.interndepot=0 ";
elseif($_SESSION['portefeuilleIntern'] == "1")
  $portefeuilleWhere .= " AND Portefeuilles.interndepot=1 ";


$query = "SELECT Portefeuille, Client FROM Portefeuilles ".$join. " $alleenActief $beperktToegankelijk $portefeuilleWhere ORDER BY Client,Portefeuille ";
$DB->SQL($query);
$DB->Query();
$aantal = $DB->records();
//echo count($portefeuilleList)."<br> ($aantal) <br>";
$t=0;
while($gb = $DB->NextRecord())
{
//$portfeuilleOptions .= "<option value=\"".$gb['Portefeuille']."\" >".$gb['Portefeuille']. " - ".$gb['Client']. "</option>\n";
  $eersteLetter = strtoupper(substr($gb['Client'],0,1));
  $portefeuilles[$eersteLetter][$gb['Client']][$gb['Portefeuille']] = addslashes($gb['Portefeuille']. " - ".$gb['Client']);
}
//listarray($portefeuilles);
$portfeuilleOptions2 .= "";

// selecteer de 1e vermogensbeheerder uit de tabel vermogensbeheerders voor de selectie vakken.
$query = "SELECT  layout, CrmClientNaam,Export_data_frontOffice,frontofficeClientExcel,
  Vermogensbeheerders.rapportDoorkijk FROM Vermogensbeheerders
					  JOIN VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder =  Vermogensbeheerders.Vermogensbeheerder
					 WHERE VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."' LIMIT 1";
$DB = new DB();
$DB->SQL($query);
$DB->Query();
$rdata = $DB->nextRecord();

$frontOfficeData=unserialize($rdata['Export_data_frontOffice']);
include_once("rapportFrontofficeClientSelectieLayout.php");


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
  $html .="}</script>";
}

$html .="
<script>";
if ($htmlRapportageEnabled)
{
  $html .= "   ".$htmlRapportJS.";   ";
}
else
{
  $html .= "   htmlRapport = ['dummy']; ";
}
$html .="

function doStuff()
{
  settings();
  var xlsRapport = [";
$first=true;
$xlsRapporten=array();
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
$html .="];
	document.selectForm.selected.value = \"\";
	var tel =0;
  parent.frames['content'].$('#xls_uitvoer').hide();
   parent.frames['content'].$('#btnHTML').hide();
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
$html .= "<b>Selecteer rapport</b><br>

 	 <div id=\"wrapper\" style=\"overflow:hidden;\">
 	 <div class=\"buttonDiv\" style=\"width:65px;float:left;\" onclick=\"checkAll(1);\">&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' />Alles</div>
 	 <div class=\"buttonDiv\" style=\"width:65px;float:left;\" onclick=\"checkAll(0);\">&nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon' />Niets</div>
 	 </div><br>
<form name=\"selectForm\">";

$script="<script>
 	   function checkAll(value)
 	   {";
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
  $html .= "<br \>";
  
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
<div class="menutitle" onclick="SwitchMenu(\'subNaw0\')">Overige</div><span class="submenu" id="subNaw0">';
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


if($_SESSION['lastGET']['actief'] == "inactief" )
  $inactiefChecked = "checked";
elseif($_SESSION['lastGET']['actief'] == "eActief" )
  $eActiefChecked = "checked";
else
  $actiefChecked = "checked";

$html .='
<input type="radio" name="actief" id="actief" value="actief" '.$actiefChecked.' onClick="parent.frames[\'content\'].document.location = \''.$PHP_SELF.'?actief=actief\'">
<label for="actief" title="actief"> Actieve portefeuilles  </label> <br>
<input type="radio" name="actief" id="eActief" value="eActief" '.$eActiefChecked.' onClick="parent.frames[\'content\'].document.location = \''.$PHP_SELF.'?actief=eActief\'">
<label for="actief" title="eActief"> Eigen act. portefeuilles  </label><br>
<input type="radio" name="actief" id="inactief" value="inactief" '.$inactiefChecked.' onClick="parent.frames[\'content\'].location = \''.$PHP_SELF.'?actief=inactief\'">
<label for="inactief" title="actief"> Alle portefeuilles </label>';

$html .="<br>";
$html.=$selectie->getInternExternHTML($PHP_SELF);

$html .="<br> <iframe src=\"laatsteValuta.php\" width=\"100%\" height=\"80\" marginwidth=\"0\" marginheight=\"0\" hspace=\"0\" vspace=\"0\" align=\"middle\" frameborder=\"0\"></iframe> ";



$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->onLoad = " onLoad=\"settings();\" ";
$_SESSION['submenu']->addItem($html,"");

//
$content['jsincludes'] .= "<script language=JavaScript src=\"javascript/ae_ajax_client.js\" type=text/javascript></script>";
$content['javascript'] = "";
$content['jsincludes'] .= "<script language=JavaScript src=\"javascript/selectbox.js\" type=text/javascript></script>";
$content['style2']='<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css"> <link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">';
$content['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$content['calendar'] = $kal->get_load_files_code();

echo template($__appvar["templateContentHeader"],$content);
// selecteer laatst bekende valutadatum
$totdatum = getLaatsteValutadatum();

?>
  <script language="Javascript">
    var portefeuillesLoaded=false;
    
    function moveItem(from,to){
      var tmp_text = new Array();
      var tmp_value = new Array();
      for(var i=0; i < from.options.length; i++) {
        if(from.options[i].selected)
        {
          var blnInList = false;
          for(j=0; j < to.options.length; j++)
          {
            if(to.options[j].value == from.options[i].value)
            {
              //alert("already in list");
              blnInList = true;
              break;
            }
          }
          if(!blnInList)
          {
            to.options.length++;
            to.options[to.options.length-1].text = from.options[i].text;
            to.options[to.options.length-1].value = from.options[i].value;
          }
        }
        else
        {
          tmp_text.length++;
          tmp_value.length++;
          tmp_text[tmp_text.length-1] = from.options[i].text;
          tmp_value[tmp_text.length-1] = from.options[i].value;
          
        }
      }
      from.options.length = 0;
      for(var i=0; i < tmp_text.length; i++) {
        from.options.length++;
        from.options[from.options.length-1].text = tmp_text[i];
        from.options[from.options.length-1].value = tmp_value[i];
      }
      from.selectedIndex = -1;
    }
    
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
      document.selectForm.target 							= "_blank";
      document.selectForm.extra.value = "";
      setRapportTypes();
      document.selectForm.save.value="0";
      
      checkConsolidatie();
      
    }
    
    function html()
    {
      document.selectForm.target = "content";
      document.selectForm.extra.value = "";
      var oldAction=document.selectForm.action;
      document.selectForm.action = "rapportFrontofficeClientAfdrukkenHtml.php?counter="+counter+"&consolidatie=1";
      setRapportTypes();
      document.selectForm.save.value="0";
      checkConsolidatie();
      document.selectForm.action=oldAction;
      counter++;
    }
    
    function saveasfile()
    {
      document.selectForm.target = "";
      document.selectForm.extra.value = "";
      setRapportTypes()
      document.selectForm.save.value="1";
      checkConsolidatie();
      
    }
    
    function selectPortefeuilles()
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
    
    function submitConsolidatie()
    {
      selectPortefeuilles();
      document.selectForm.submit();
    }
    
    function checkConsolidatie()
    {
      
      selectPortefeuilles();
      
      $.ajax({
        type: "POST",
        url: "rapportFrontofficeConsolidatieAfdrukken.php?lookup=1",
        dataType: "json",
        async: false,
        data: {  portefeuille: "" + document.selectForm.Portefeuille.value , selectedPortefeuilles: $("#selectedPortefeuilles").val() },
        success: function(data, textStatus, jqXHR)
        {
          console.log(data);
          
          if(data.status==0)
          {
            submitConsolidatie();
          }
          else if(data.status==1)
          {
            AEConfirm(data.msg, 'Consolidatie actief', function () { $('#verwijder').val(1); submitConsolidatie();}   );
          }
          else if(data.status==2)
          {
            AEMessage(data.msg, 'Consolidatie actief', function ()
            {
            });
          }
          
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
        }
      });
    }
    
    function xls()
    {
      document.selectForm.target = "_blank";
      document.selectForm.extra.value = "xls";
      setRapportTypes()
      document.selectForm.save.value="1";
      checkConsolidatie();
      
      //counter++;
    }
    
    
    function loadPortefeuilles(letter)
    {
      inputBox = document.selectForm['inFields[]'];
      var Portefeuilles = new Array();
      
      <?
      $n=0;
      foreach($portefeuilles as $letter=>$clientdata)
      {
        echo "Portefeuilles['$letter']	= new Array(); \n";
        
        foreach($clientdata as $client=>$portefeuilleData)
        {
          foreach ($portefeuilleData as $portefeuille => $omschrijving)
          {
            $omschrijving = trim($omschrijving);
            echo "Portefeuilles['$letter']['$n||$portefeuille']	= '$omschrijving'; \n";
            $n++;
          }
        }
      }
      
      ?>
      
      for(var count = inputBox.options.length - 1; count >= 0; count--)
      {
        inputBox.options[count] = null;
      }
      
      if (letter == 'alles')
      {
        for (keyVar in Portefeuilles )
        {
          LoadLetter(Portefeuilles[keyVar]);
        }
      }
      else
      {
        LoadLetter(Portefeuilles[letter]);
      }
    }
    
    function LoadLetter(letterPortefeuilles)
    {
      
      inputBox = document.selectForm['inFields[]'];
      for (keyVar in letterPortefeuilles )
      {
        inputBox.options.length++;
        inputBox.options[inputBox.options.length-1].text = letterPortefeuilles[keyVar];
        var portefeuilleParts=keyVar.split('||')
        inputBox.options[inputBox.options.length-1].value = portefeuilleParts[1];
      }
    }
    
    
    function changeCheck(item)
    {
      var theForm = document.selectForm.elements, z = 0;
      for(z=0; z<theForm.length;z++)
      {
        if(theForm[z].type == "checkbox")
        {
          var test=theForm[z].name;
          if(test.search(item)==0)
          {
            if (theForm[z].checked == true)
            {
              theForm[z].checked = false;
            }
            else
            {
              theForm[z].checked = true;
            }
          }
        }
      }
    }
    
    
    function OpnieuwLaden()
    {
      document.selectForm.target = "";
      document.selectForm.action="";
      document.selectForm.submit();
    }
  </script>
  
  <br><br>
  <div class="tabbuttonRow">
    <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeClientSelectieold.php';" id="tabbutton0" value="Clienten">
    <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeFondsSelectieold.php';" id="tabbutton1" value="Fondsen">
    <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeManagementSelectieold.php';" id="tabbutton2" value="Management info">
    <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeOptieToolsold.php';" id="tabbutton3" value="Optie tools">
    <input type="button" class="tabbuttonActive" onclick="" id="tabbutton4" value="Consolidatie tool">
  
  </div>
  <br>
  
  <form action="rapportFrontofficeConsolidatieAfdrukken.php" method="POST" target="_blank" name="selectForm">
    <input type="hidden" name="posted" value="true" />
    <input type="hidden" name="save" value="" />
    <input type="hidden" name="rapport_types" value="" />
    <input type="hidden" name="extra" value="" />
    <input type="hidden" name="verwijder" id="verwijder" value="" />
    <table border="0">
      <tr>
        <td width="540">
          <div class="form">
            
            <fieldset id="Rapport" >
              <legend accesskey="R"><u>R</u>apport</legend>
              
              <div class="formblock">
                <div class="formlinks"> Van datum: </div>
                <div class="formrechts">
                  <?php
                  
                  $totJul=db2jul($totdatum);
                  $totFromDatum=date("d-m-Y",$totJul);
                  
                  $jr = substr($totdatum,0,4);
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
                  
                  $jr = substr($totdatum,0,4);
                  $kal = new DHTML_Calendar();
                  $inp = array ('name' =>"datum_van",'value' =>(!empty($_SESSION['rapportDateFrom']))?$_SESSION['rapportDateFrom']:date("d-m-Y",mktime(0,0,0,1,1,$jr)),'size'  => "11");
                  echo $kal->make_input_field("",$inp,"onChange=\"javascript:doStore()\"");
                  ?>
                  &nbsp; Vorige &nbsp; &nbsp; &nbsp;<a style="color: Navy;font-weight: bold;"  href="#" onclick="javascript:document.selectForm.datum_van.value='<?=$datumSelctie['beginMaand']?>';document.selectForm.datum_tot.value='<?=$datumSelctie['eindMaand']?>';doStore();">Maand</a>,
                  <a style="color: Navy;font-weight: bold;" href="#" onclick="javascript:document.selectForm.datum_van.value='<?=$datumSelctie['beginKwartaal']?>';document.selectForm.datum_tot.value='<?=$datumSelctie['eindKwartaal']?>';doStore();">Kwartaal</a>,
                  <a style="color: Navy;font-weight: bold;"  href="#" onclick="javascript:document.selectForm.datum_van.value='<?=$datumSelctie['beginJaar']?>';document.selectForm.datum_tot.value='<?=$datumSelctie['eindJaar']?>';doStore();">Jaar</a>
                
                </div>
              </div>
              
              <div class="formblock">
                <div class="formlinks"> T/m datum: </div>
                <div class="formrechts">
                  <?php
                  $kal = new DHTML_Calendar();
                  $inp = array ('name' =>"datum_tot",'value' =>(!empty($_SESSION['rapportDateTm']))?$_SESSION['rapportDateTm']:date("d-m-Y",db2jul($totdatum)),'size'  => "11");
                  echo $kal->make_input_field("",$inp,"onChange=\"javascript:doStore()\"");
                  ?>
                  &nbsp; Huidige &nbsp; <a style="color: Navy;font-weight: bold;" href="#" onclick="javascript:document.selectForm.datum_van.value='<?=$datumSelctie['beginMaand2']?>';document.selectForm.datum_tot.value='<?=$totFromDatum?>';doStore();">Maand</a>,
                  <a style="color: Navy;font-weight: bold;" href="#" onclick="javascript:document.selectForm.datum_van.value='<?=$datumSelctie['beginKwartaal2']?>';document.selectForm.datum_tot.value='<?=$totFromDatum?>';doStore();">Kwartaal</a>,
                  <a style="color: Navy;font-weight: bold;" href="#" onclick="javascript:document.selectForm.datum_van.value='<?=$datumSelctie['beginJaar2']?>';document.selectForm.datum_tot.value='<?=$totFromDatum?>';doStore();">Jaar</a>
                
                </div>
              </div>
              
              
              <?
              if($__appvar['master']==true)
              {
                ?>
                <div class="formblock">
                  <div class="formlinks"><input type="checkbox" value="1" id="debug" name="debug"> Debug</div>
                  <div class="formrechts"> <input type="text" value="" size="1" id="layout" name="layout"> Layout
                    <input type="checkbox" value="1" id="anoniem" name="anoniem">Anonieme rapportage
                  </div>
                </div>
                
                <?
              }
              else
              {
              ?>
              <div class="formblock">
                <div class="formlinks">
                  <div class="formrechts">
                    <input type="checkbox" value="1" id="anoniem" name="anoniem">Anonieme rapportage
                  </div>
                </div>
                <?
                }
                ?>
            
            
            </fieldset>
          
          </div>
        </td>
        <td>
          
          <div class="buttonDiv" id="afdrukkenButton" style="width:130px" onclick="javascript:print();">&nbsp;&nbsp;<?=maakKnop('pdf.png',array('size'=>16))?> Afdrukken</div><br>
          <div class="buttonDiv" id="opslaanButton" style="width:130px" onclick="javascript:saveasfile();">&nbsp;&nbsp;<?=maakKnop('disk_blue.png',array('size'=>16))?> Opslaan </div><br>
          <?
          //if($__appvar['master']==false)
          //   $xlsStyle="display:none;";
          //if($rdata['frontofficeClientExcel']==1)
          //  $xlsStyle='';
          if($__appvar['bedrijf']=='HOME')
            $xlsStyle='';
          echo '<div class="buttonDiv" id="xls_uitvoer" style="'.$xlsStyle.'" onclick="javascript:xls();">&nbsp;&nbsp;'.maakKnop('xls.png',array('size'=>16)).' XLS uitvoer </div><br>';
          //if ($__develop OR $__appvar["bedrijf"] == "HOME" OR $__appvar["bedrijf"] == "TEST")
          {
            echo '<div class="buttonDiv" id="btnHTML" onclick="javascript:html();">&nbsp;&nbsp;'.maakKnop('html.png',array('size'=>16)).' HTML</div><br>';
          }
          ?>
          
          <input type="checkbox" value="1" id="logoOnderdrukken" name="logoOnderdrukken"> Logo onderdrukken<br>
          <? if($rdata['rapportDoorkijk']==1) {?> <input type="checkbox" value="1" id="doorkijk" name="doorkijk"> Doorkijk <br><?}?>
          <input type="checkbox" value="1" id="voorbladWeergeven" name="voorbladWeergeven" checked> Voorblad weergeven<br>
          <input type="checkbox" value="1" id="metWachtwoord" name="metWachtwoord" > Met wachtwoord
        
        </td>
      </tr>
      
      <tr>
      
      <tr><td>
          <?
          foreach($portefeuilles as $letter=>$data)
          {
            echo "<a href=\"javascript:loadPortefeuilles('$letter');\" class=\"letterButton\">".$letter."</a>\n";
          }
          echo "<a href=\"javascript:loadPortefeuilles('alles');\" class=\"letterButton\" style=\"width:26px\">alles</a>\n";
          ?>
        </td></tr>
      <td valign="top">
        
        <div>
          <fieldset>
            <legend><button onclick="$('#voorFilterDiv').toggle();$('#portefeuilleSelectie').hide(); if($('#voorFilterSpanOpen').html()=='Open'){$('#voorFilterSpanOpen').html('Sluiten');}else{$('#voorFilterSpanOpen').html('Open')};return false;"> <span id="voorFilterSpanOpen">Open</span></button> &nbsp; Voorselectie Portefeuilles</legend>
            <div id="voorFilterDiv" style="display:none;">
              <?php
              $DB = new DB();
              $maxVink=25;
              $opties=array('Vermogensbeheerder'=>'Vermogensbeheerder','Accountmanager'=>'accountmanager','TweedeAanspreekpunt'=>'tweedeAanspreekpunt','Depotbank'=>'depotbank');
              foreach ($opties as $optie=>$omschrijving)
              {
                $data=$selectie->getData($optie);
                if(count($data) < $maxVink)
                  echo $selectie->createCheckBlok($optie,$data,$_POST,$omschrijving);
                else
                  echo $selectie->createSelectBlok($optie,$data,$_POST,$omschrijving);
              }
              $opties=array('Risicoklasse'=>'Risicoklasse','AFMprofiel'=>'AFMprofiel','SoortOvereenkomst'=>'SoortOvereenkomst','Remisier'=>'Remisier','PortefeuilleClusters'=>'PortefeuilleClusters');
              foreach ($opties as $optie=>$omschrijving)
              {
                $data=$selectie->getData($optie);
                if(count($data) > 1)
                {
                  if($_SESSION['selectieMethode'] =='vink' && count($data) < $maxVink)
                    echo $selectie->createCheckBlok($optie,$data,$_POST,$omschrijving);
                  else
                    echo $selectie->createSelectBlok($optie,$data,$_POST,$omschrijving);
                }
              }
              ?>
              
              <div class="formblock">
                
                <div class="formlinks"> &nbsp; </div>    <div class="formrechts">  <input type="button" onclick="javascript:OpnieuwLaden();" value="Selectie ophalen"> </div>
              </div>
            
            
            </div>
          </fieldset>
        </div>
        
        <fieldset id="PSelectie" >
          <legend accesskey="Sa"><u>S</u>electie</legend>
          <input type="hidden" name="setValue" value="fields">
          
          
          <div style="display: inline-block;" class="icon"><input type="radio" checked value="" name="Portefeuille" id="id_enkelvoudig" > </div>
          
          <div style="display: inline-block;" class="buttonDiv" style="width:230px" onclick="javascript:$('#portefeuilleSelectie').toggle();if(portefeuillesLoaded==false){portefeuillesLoaded=true;loadPortefeuilles('alles');}document.getElementById('id_enkelvoudig').checked=true;">Toon enkelvoudige selectie</div>
          
          <br>
          
          <div id="portefeuilleSelectie" style="<?=$enkelVoudigeSelectieStyle?>"><br>
            <table cellspacing="0" width="500" >
              <tr>
                <td>
                  <select name="inFields[]" multiple size="16" style="min-width : 200px; margin-left: 13px;" onfocus="document.getElementById('id_enkelvoudig').checked=true;">
                  
                  </select>
                </td>
                <td width="70" >
                  <a href="javascript:moveItem(document.selectForm['inFields[]'],document.selectForm['selectedFields[]']);">
                    <img src="images/16/pijl_rechts.png" width="16" height="16" border="0" alt="toevoegen" align="absmiddle">
                  </a>
                  <br><br>
                  <a href="javascript:moveItem(document.selectForm['selectedFields[]'],document.selectForm['inFields[]']);">
                    <img src="images/16/pijl_links.png" width="16" height="16" border="0" alt="verwijderen" align="absmiddle">
                  </a>
                </td>
                <td>
                  <select id="selectedPortefeuilles" name="selectedFields[]" multiple size="16" style="min-width : 200px">
                    <?=$portfeuilleOptions2?>
                  </select>
                </td>
                <td width="70" >
                  <a href="javascript:moveOptionUp(document.selectForm['selectedFields[]'])">
                    <img src="images/16/pijl_omhoog.png" width="16" height="16" border="0" alt="omhoog" align="absmiddle">
                  </a>
                  <br><br>
                  <a href="javascript:moveOptionDown(document.selectForm['selectedFields[]'])">
                    <img src="images/16/pijl_omlaag.png" width="16" height="16" border="0" alt="omlaag" align="absmiddle">
                  </a>
                </td>
              </tr>
            </table>
          </div>
          <br>
          <?
          $__appvar['rowsPerPage']=100;
          $list = new MysqlList();
          $list->idField = "id";
          $list->editScript = $editScript;
          $list->perPage = $__appvar['rowsPerPage'];
          
          $list->addField("Portefeuilles","id",array("width"=>100,"search"=>false));
          $list->addField("Portefeuilles","Portefeuille",array("list_width"=>150,"search"=>true));
          $list->addField("Portefeuilles","Client",array("list_width"=>200,"search"=>true));
          
          
          if($rdata['CrmClientNaam'] == '1')
          {
            $list->addField("","Naam",array("list_width"=>300,'sql_alias'=>'CRM_naw.naam',"search"=>true));
            $list->addField("","crmId",array('sql_alias'=>'CRM_naw.id',"search"=>true,'list_invisible'=>true));
          }
          else
          {
            $list->addField("Client", "Naam", array("list_width" => 300, "search" => true));
            $clientWhere="AND Portefeuilles.Client=Clienten.Client";
          }
          
          $allow_add = false;
          $internDepotToegang='';
          
          if($rdata['CrmClientNaam'] == '1')
          {
            $list->setJoin("LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.Portefeuille INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	                JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker");
          }
          else
            $list->setJoin(" INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	                  JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker");
          
          $list->setWhere("Portefeuilles.consolidatie=1 $clientWhere ");
          $_GET['sort'][] = "Portefeuilles.Client";
          $_GET['direction'][] = "ASC";
          
          if(checkAccess($type))
          {
            $beperktToegankelijk = '';
          }
          else
          {
            if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
              $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang) ";
            else
              $beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
            
          }
          
          $list->setOrder($_GET['sort'],$_GET['direction']);
          $list->setSearch($_GET['selectie']);
          $list->selectPage($_GET['page']);
          
          session_start();
          $_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
          $_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
          $_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));
          
          $selectPortefeuilles='';
          for($i=1;$i<11;$i++)
          {
            $selectPortefeuilles.="Portefeuille".$i;
            if($i<10)
              $selectPortefeuilles.=",";
          }
          $DB=new DB();
          /*
          ?>
          
          <table cellspacing="0">
          <?=$list->printHeader();?>
          <?php
          
          $alleenNaw=GetModuleAccess('alleenNAW');
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
          
          while($data = $list->getRow())
          {
          
            if($data['crmId']['value'] > 0 && $alleenNaw==0)
              $link2=  "<td><a href=\"CRM_nawEdit.php?do=viaFrontOffice&port={Portefeuille_value}&lastTab=9&frameSrc=".base64_encode("CRM_naw_dossierEdit.php?action=new&toList=1&rel_id=".$data['crmId']['value'])."\">
                        ".maakKnop('note_new.png',array('size'=>16,'tooltip'=>'Nieuw gespreksverslag'))."</a></td>";
            else
              $link2='';
          
            $template = '<tr class="list_dataregel" onmouseover="this.className=\'list_dataregel_hover\'" onmouseout="this.className=\'list_dataregel\'" onClick="javascript:document.getElementById(\'id_{Portefeuille_value}\').checked=true;">
          <td class="list_button">
            <div class="icon"><input type="radio" value="{Portefeuille_value}" id="id_{Portefeuille_value}" name="Portefeuille" ></div>
          </td>
          <td class="listTableData"  width="150" align="left" >{Portefeuille_value} &nbsp;</td>
          <td class="listTableData"  width="150" align="left" >{Client_value} &nbsp;</td>
          <td class="listTableData"  align="left" >{Naam_value} &nbsp;</td>
          '.$crmLink.$link2.'
          </tr>';
          
          
            $query="SELECT $selectPortefeuilles FROM GeconsolideerdePortefeuilles WHERE VirtuelePortefeuille='".$data['Portefeuille']['value']."'";
            $DB->SQL($query);
            $portefeuilles=$DB->lookupRecord();
            $portefeuilleArray=array();
            foreach($portefeuilles as $veld=>$waarde)
            {
              if($waarde<>'')
                $portefeuilleArray[]=$waarde;
            }
            $query="SELECT Portefeuilles.Portefeuille FROM Portefeuilles $join WHERE Portefeuilles.Portefeuille IN('".implode("','",$portefeuilleArray)."') $beperktToegankelijk";
          
            if($DB->QRecords($query))
              echo $list->buildRow($data,$template,"");
          }
          
          
          ?>
          </table>
          </fieldset>
          
          </td>
          
          
          <?
          if(isset($rapportSettings[$rdata['layout']]))
            echo $rapportSettings[$rdata['layout']];
          
          ?>
          <td valign="top">
          <fieldset id="MUT_Settings"  style="display:none;">
          <legend accesskey="m">M<u>u</u>tatie-overzicht</legend>
          <?=$mutSettings?>
          </fieldset>
          
          
            <fieldset id="mmIndex_Settings" style="display:none;" >
              <legend accesskey="m">MM-index</legend>
              <?=$mmIndexSettings?>
            </fieldset>
          
          
            <fieldset id="Model_Settings" style="visibility:hidden">
              <div class="formblock">
              <u>Niveau</u><br>
              <input type="radio" name="modelcontrole_level" value="fonds" checked> Fonds<br>
              <input type="radio" name="modelcontrole_level" value="beleggingscategorie" >Categorie<br>
              <input type="radio" name="modelcontrole_level" value="beleggingssector" >Sector<br>
              <input type="radio" name="modelcontrole_level" value="Regio" >Regio<br>
            </fieldset>
            </div>
          </td>
          
          </tr>
          </table>
          <?
          */
          ?>
  
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
if($__debug) {
  echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>