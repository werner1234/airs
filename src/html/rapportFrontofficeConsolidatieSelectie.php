<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/06/27 16:20:24 $
 		File Versie					: $Revision: 1.59 $

 		$Log: rapportFrontofficeConsolidatieSelectie.php,v $
 		Revision 1.59  2020/06/27 16:20:24  rvv
 		*** empty log message ***
 		


*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/selectOptieClass.php");


$AETemplate = new AE_template();

$selectie = new selectOptie($PHP_SELF);
$selectie->getInternExternActive();
$type = 'portefeuille';

$htmlRapportageEnabled = getVermogensbeheerderField("HTMLRapportage");
$htmlRapportJS = "htmlRapport = ['ATT','VOLK','TRANS','MUT','MODEL']";


if (!is_array($_SESSION['lastGET']))
{
  $_SESSION['lastGET'] = array();
}
$_SESSION['lastGET'] = array_merge($_SESSION['lastGET'], $_GET);


if ($_SESSION['lastGET']['actief'] <> "inactief")
{
  if ($_SESSION['lastGET']['actief'] == "eActief")
  {
    $alleenActief = " WHERE Portefeuilles.consolidatie=0 AND Portefeuilles.Einddatum  >=  NOW() AND Portefeuilles.Accountmanager='" . $_SESSION['usersession']['gebruiker']['Accountmanager'] . "'";
  }
  else
  {
    $alleenActief = " WHERE Portefeuilles.consolidatie=0 AND Portefeuilles.Einddatum  >=  NOW() ";
  }
  $metInactief = false;
}
else
{
  $alleenActief = "WHERE Portefeuilles.consolidatie=0 ";
  $metInactief = true;
}


$DB = new DB();
if (checkAccess($type))
{
  $join = "";
  $beperktToegankelijk = '';
}
else
{
  $join = " INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND  VermogensbeheerdersPerGebruiker.Gebruiker = '" . $USR . "'
	  				JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
  
  if ($_SESSION['usersession']['gebruiker']['internePortefeuilles'] == '1')
  {
    $internDepotToegang = "OR Portefeuilles.interndepot=1";
  }
  
  if ($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
  {
    $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='" . $_SESSION['usersession']['gebruiker']['Accountmanager'] . "' OR Portefeuilles.tweedeAanspreekpunt ='" . $_SESSION['usersession']['gebruiker']['Accountmanager'] . "' $internDepotToegang) ";
  }
  else
  {
    $beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
  }
  
}


if (count($_POST['Vermogensbeheerder']) > 0)
{
  include_once("../classes/portefeuilleSelectieClass.php");
  $pSelectie = new portefeuilleSelectie($_POST, '', false, $metInactief);
  
  $records = $pSelectie->getRecords();
  $portefeuilleList = $pSelectie->getSelectie();
  $portefeuilleWhere = " AND Portefeuilles.Portefeuille IN('" . implode("','", array_keys($portefeuilleList)) . "')";
  $enkelVoudigeSelectieStyle = '';
  $content['body'] = " onLoad=\"javascript:loadPortefeuilles('alles')\" ";
}
else
{
  $portefeuilleWhere = '';
  $enkelVoudigeSelectieStyle = 'display:none';
}


if ($_SESSION['lastGET']['letter'] && !$_GET['selectie'])
{
  $portefeuilleWhere = " AND Portefeuilles.Client LIKE '" . mysql_escape_string($_SESSION['lastGET']['letter']) . "%' ";
}

if (!isset($_SESSION['portefeuilleIntern']) || $_SESSION['portefeuilleIntern'] == '0')
{
  $portefeuilleWhere .= " AND Portefeuilles.interndepot=0 ";
}
elseif ($_SESSION['portefeuilleIntern'] == "1")
{
  $portefeuilleWhere .= " AND Portefeuilles.interndepot=1 ";
}


$query = "SELECT Portefeuille, Client FROM Portefeuilles " . $join . " $alleenActief $beperktToegankelijk $portefeuilleWhere ORDER BY Client,Portefeuille ";
$DB->SQL($query);
$DB->Query();
$aantal = $DB->records();
//echo count($portefeuilleList)."<br> ($aantal) <br>";
$t = 0;
while ($gb = $DB->NextRecord())
{
//$portfeuilleOptions .= "<option value=\"".$gb['Portefeuille']."\" >".$gb['Portefeuille']. " - ".$gb['Client']. "</option>\n";
  $eersteLetter = strtoupper(substr($gb['Client'], 0, 1));
  $portefeuilles[$eersteLetter][$gb['Client']][$gb['Portefeuille']] = addslashes($gb['Portefeuille'] . " - " . $gb['Client']);
}
//listarray($portefeuilles);
$portfeuilleOptions2 .= "";

// selecteer de 1e vermogensbeheerder uit de tabel vermogensbeheerders voor de selectie vakken.
$query = "SELECT  layout, CrmClientNaam,Export_data_frontOffice,frontofficeClientExcel,
  Vermogensbeheerders.rapportDoorkijk FROM Vermogensbeheerders
					  JOIN VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder =  Vermogensbeheerders.Vermogensbeheerder
					 WHERE VermogensbeheerdersPerGebruiker.Gebruiker = '" . $USR . "' LIMIT 1";
$DB = new DB();
$DB->SQL($query);
$DB->Query();
$rdata = $DB->nextRecord();

$frontOfficeData = unserialize($rdata['Export_data_frontOffice']);
include_once("rapportFrontofficeClientSelectieLayout.php");


$html = "<script language=JavaScript src=\"javascript/ae_ajax_client.js\" type=text/javascript></script>";
if ($mutSettings == '')
{
  $DB = new DB();
  $query = "SELECT Grootboekrekeningen.Grootboekrekening, Grootboekrekeningen.Omschrijving FROM Grootboekrekeningen WHERE (Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Onttrekking='1' OR Grootboekrekeningen.Opbrengst = '1' OR  Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1')";
  $DB->SQL($query);
  $DB->Query();
  $mutSettings .= '<table>';
  while ($gb = $DB->nextRecord())
  {
    if ($_SESSION['lastPost']['MUT_' . $gb['Grootboekrekening']] == 1)
    {
      $check = 'checked';
    }
    else
    {
      $check = '';
    }
    $mutSettings .= '<tr><td>' . $gb['Omschrijving'] . '</td><td><input type="checkbox" name="MUT_' . $gb['Grootboekrekening'] . '" value="1" ' . $check . ' ></td></tr>';
  }
  $mutSettings .= '</table>';
}


if (isset($rapportSelectie[$rdata['layout']]))
{
  $html .= $rapportSelectie[$rdata['layout']];
}
else
{
  $html .= "<script>
function settings()
{
";
  $scenarioHtml = "  parent.frames['content'].$('#SCENARIO_Settings').hide();
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
  
  if (checkAccess($type))
  {
    $html .= "
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
    $html .= "
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
  $html .= "}</script>";
}

$html .= "
<script>";
if ($htmlRapportageEnabled)
{
  $html .= "   " . $htmlRapportJS . ";   ";
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
$first = true;
$xlsRapporten = array();
foreach ($frontOfficeData as $rapport => $rapportData)
{
  if ($rapportData['xls'] == 1 || $__appvar['bedrijf'] == 'HOME')
  {
    if ($first == true)
    {
      $first = false;
    }
    else
    {
      $html .= ",";
    }
    $html .= "'" . $rapport . "'";
    $xlsRapporten[] = $rapport;
  }
}
$html .= "];
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
$html .= "<b>".vt("Selecteer rapport")."</b><br>

 	 <div id=\"wrapper\" style=\"overflow:hidden;\">
 	 <div class=\"buttonDiv\" style=\"width:65px;float:left;\" onclick=\"checkAll(1);\">&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' />".vt("Alles")."</div>
 	 <div class=\"buttonDiv\" style=\"width:65px;float:left;\" onclick=\"checkAll(0);\">&nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon' />".vt("Niets")."</div>
 	 </div><br>
<form name=\"selectForm\">";

$script = "<script>
 	   function checkAll(value)
 	   {";
$xlsStyle = "display:none;";
if (is_array($frontOfficeData))
{
  foreach ($frontOfficeData as $rapport => $rapportData)
  {
    if ($rapportData['volgorde'] == '')
    {
      $rapportData['volgorde'] = 99;
    }
    $rapportVolgorde[$rapportData['volgorde']][$rapport] = $rapportData;
  }
  
  ksort($rapportVolgorde);
  $rapportenSorted = array();
  foreach ($rapportVolgorde as $volgordeId => $rapportdata)
  {
    foreach ($rapportdata as $rapport => $rapData)
    {
      if (isset($__appvar["Rapporten"][$rapport]))
      {
        $rapportenSorted[$rapport] = $rapData;
      }
    }
  }
  foreach ($__appvar["Rapporten"] as $rapport => $omschrijving)
  {
    $rapportenSorted[$rapport]['omschrijving'] = $omschrijving;
  }
  $rapporten = array('open' => array(), 'closed' => array());
  
  foreach ($rapportenSorted as $rapport => $rapportData)
  {
    $checked = in_array($rapport, $_SESSION['rapportSelection']);
    if ($checked && in_array($rapport, $xlsRapporten))
    {
      $xlsStyle = "";
    }
    
    if (isset($frontOfficeData[$rapport]['longName']))
    {
      $long = $frontOfficeData[$rapport]['longName'];
    }
    else
    {
      $long = $rapportData['omschrijving'];
    }
    
    if (isset($frontOfficeData[$rapport]['shortName']))
    {
      $short = $frontOfficeData[$rapport]['shortName'];
    }
    else
    {
      $short = $rapport;
    }
    
    if ($rapportData['toon'] == 1)
    {
      $rapporten['open'][] = array('rapport' => $rapport, 'omschrijving' => $long . ' (' . $rapportData['volgorde'] . ' ' . $rapport . ')', 'checked' => $checked, 'short' => $short);
    }
    elseif ($rapportData['toonNiet'] == 0)
    {
      $rapporten['closed'][] = array('rapport' => $rapport, 'omschrijving' => $long . ' (' . $rapportData['volgorde'] . ' ' . $rapport . ')', 'checked' => $checked, 'short' => $short);
    }
  }
  
  $script = "<script>
  function checkAll(value) 
  {";
  foreach ($rapporten['open'] as $rapportData)
  {
    $html .= "<input type=\"checkbox\" value=\"" . $rapportData['rapport'] . "\" name=\"rapport_type\" id=\"" . $rapportData['rapport'] . "\" onClick=\"JavaScript:doStuff();\" " . (($rapportData['checked'] == 1)?"checked":"") . ">  " .
      "<label for=\"" . $rapportData['rapport'] . "\" title=\"" . $rapportData['omschrijving'] . "\">" . $rapportData['short'] . " </label><br>";
    $script .= "document.selectForm.elements['" . $rapportData['rapport'] . "'].checked=value;\n";
    
  }
  $script .= "}
  </script>";
  $html .= "<br \>";
  
  if (count($rapporten['closed']) > 0)
  {
    $html .= '<style type="text/css">
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
' . $script . '
<div id="masterdiv">
<div class="menutitle" onclick="SwitchMenu(\'subNaw0\')">Overige</div><span class="submenu" id="subNaw0">';
  }
  foreach ($rapporten['closed'] as $rapportData)
  {
    $html .= "<input type=\"checkbox\" value=\"" . $rapportData['rapport'] . "\" name=\"rapport_type\" id=\"" . $rapportData['rapport'] . "\" onClick=\"JavaScript:doStuff();\" " . (($rapportData['checked'] == 1)?"checked":"") . ">  " .
      "<label for=\"" . $rapportData['rapport'] . "\" title=\"" . $rapportData['omschrijving'] . "\">" . $rapportData['short'] . " </label><br>";
  }
  if (count($rapporten['closed']) > 0)
  {
    $html .= '</span></div>';
  }
  
}
else
{
  foreach ($__appvar["Rapporten"] as $key => $value)
  {
    if (in_array($key, $_SESSION['rapportSelection']))
    {
      $selected = "checked";
      if (in_array($key, $xlsRapporten))
      {
        $xlsStyle = "";
      }
    }
    else
    {
      $selected = "";
    }
    $html .= "<input type=\"checkbox\" value=\"" . $key . "\" name=\"rapport_type\" id=\"" . $key . "\" onClick=\"JavaScript:doStuff();\" " . $selected . ">  " .
      "<label for=\"" . $key . "\" title=\"" . $value . "\">" . $key . " </label><br>";
  }
}
$html .= "<input name=\"selected\" value=\"\" type=\"hidden\">\n";
$html .= "</form>";


$labelClass = 'col-3 col-md-3';
$buttonClass = 'col-9 col-md-8';

$selectieHtml = '';
$selectieHtml .= $selectie->getHtmlActiveAllPortefeuille();
$selectieHtml .= $selectie->getHtmlInterneExternePortefeuille();


$html .= "<br> <iframe src=\"laatsteValuta.php\" width=\"100%\" height=\"80\" marginwidth=\"0\" marginheight=\"0\" hspace=\"0\" vspace=\"0\" align=\"middle\" frameborder=\"0\"></iframe> ";


$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->onLoad = " onLoad=\"settings();\" ";
$_SESSION['submenu']->addItem($html, "");


//
$content['jsincludes'] .= "<script language=JavaScript src=\"javascript/ae_ajax_client.js\" type=text/javascript></script>";
$content['javascript'] = "";
$content['jsincludes'] .= "<script language=JavaScript src=\"javascript/selectbox.js\" type=text/javascript></script>";
$content['style2'] = '<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css"> <link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">';
$content['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$content['calendar'] = $kal->get_load_files_code();
//debug(htmlspecialchars($editcontent['jsincludes']));
$content['style'] .= $editcontent['style'];
$content['jsincludes'] .= $editcontent['jsincludes'];
echo template($__appvar["templateContentHeader"], $content);
// selecteer laatst bekende valutadatum


$totdatum = getLaatsteValutadatum();


$totJul = db2jul($totdatum);
$totFromDatum = date("d-m-Y", $totJul);

$jr = substr($totdatum, 0, 4);
$maand = substr($totdatum, 5, 2);
$kwartaal = ceil(date("m", $totJul) / 3);

$datumSelctie['beginMaand'] = date("d-m-Y", mktime(0, 0, 0, $maand - 1, 0, $jr));
$datumSelctie['eindMaand'] = date("d-m-Y", mktime(0, 0, 0, $maand, 0, $jr));
$datumSelctie['beginKwartaal'] = date("d-m-Y", mktime(0, 0, 0, $kwartaal * 3 - 5, 0, $jr));
$datumSelctie['eindKwartaal'] = date("d-m-Y", mktime(0, 0, 0, $kwartaal * 3 - 2, 0, $jr));
$datumSelctie['beginJaar'] = date("d-m-Y", mktime(0, 0, 0, 1, 1, $jr - 1));
$datumSelctie['eindJaar'] = date("d-m-Y", mktime(0, 0, 0, 13, 0, $jr - 1));
$datumSelctie['beginMaand2'] = date("d-m-Y", mktime(0, 0, 0, $maand, 0, $jr));
$datumSelctie['beginKwartaal2'] = date("d-m-Y", mktime(0, 0, 0, $kwartaal * 3 - 2, 0, $jr));
$datumSelctie['beginJaar2'] = date("d-m-Y", mktime(0, 0, 0, 1, 1, $jr));

foreach ($datumSelctie as $naam => $datum)
{
  if (substr($naam, 0, 5) == 'begin' && substr($datum, 0, 5) == '31-12')
  {
    $datumSelctie[$naam] = "01-01-" . ((substr($datum, 6, 4)) + 1);
  }
}

$jr = substr($totdatum, 0, 4);
$inp = array('name' => "datum_van", 'value' => (!empty($_SESSION['rapportDateFrom']))?$_SESSION['rapportDateFrom']:date("d-m-Y", mktime(0, 0, 0, 1, 1, $jr)), 'size' => "11");
$inp2 = array('name' => "datum_tot", 'value' => (!empty($_SESSION['rapportDateTm']))?$_SESSION['rapportDateTm']:date("d-m-Y", db2jul($totdatum)), 'size' => "11");

?>

  <script language="Javascript">
  
    <?=$selectie->getSelectJava();?>
    var portefeuillesLoaded = false;
    
    function moveItem(from, to)
    {
      var tmp_text = new Array();
      var tmp_value = new Array();
      for (var i = 0; i < from.options.length; i++)
      {
        if (from.options[i].selected)
        {
          var blnInList = false;
          for (j = 0; j < to.options.length; j++)
          {
            if (to.options[j].value == from.options[i].value)
            {
              //alert("already in list");
              blnInList = true;
              break;
            }
          }
          if (!blnInList)
          {
            to.options.length++;
            to.options[to.options.length - 1].text = from.options[i].text;
            to.options[to.options.length - 1].value = from.options[i].value;
          }
        }
        else
        {
          tmp_text.length++;
          tmp_value.length++;
          tmp_text[tmp_text.length - 1] = from.options[i].text;
          tmp_value[tmp_text.length - 1] = from.options[i].value;
          
        }
      }
      from.options.length = 0;
      for (var i = 0; i < tmp_text.length; i++)
      {
        from.options.length++;
        from.options[from.options.length - 1].text = tmp_text[i];
        from.options[from.options.length - 1].value = tmp_value[i];
      }
      from.selectedIndex = -1;
    }
    
    var counter = 0;
    
    function doStore()
    {
      executeRequest('ae_ajax_server.php', 'selectForm', 'storeDate', responseHandler);
    }
    
    function responseHandler(requester, formName)
    {
      var theForm = document.forms[formName];
      return true;
    }
    
    function setRapportTypes()
    {
      document.selectForm.rapport_types.value = "";
      var tel = 0;
      for (var i = 0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
      {
        if (parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
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
      setRapportTypes();
      document.selectForm.save.value = "0";
      
      checkConsolidatie();
      
    }
    
    function html()
    {
      document.selectForm.target = "content";
      document.selectForm.extra.value = "";
      var oldAction = document.selectForm.action;
      document.selectForm.action = "rapportFrontofficeClientAfdrukkenHtml.php?counter=" + counter + "&consolidatie=1";
      setRapportTypes();
      document.selectForm.save.value = "0";
      checkConsolidatie();
      document.selectForm.action = oldAction;
      counter++;
    }
    
    function saveasfile()
    {
      document.selectForm.target = "";
      document.selectForm.extra.value = "";
      setRapportTypes()
      document.selectForm.save.value = "1";
      checkConsolidatie();
      
    }
    
    function selectPortefeuilles()
    {
      if (document.selectForm['inFields[]'])
      {
        var inFields = document.selectForm['inFields[]'];
        var selectedFields = document.selectForm['selectedFields[]'];
        
        for (j = 0; j < selectedFields.options.length; j++)
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
        data: {
          portefeuille: "" + document.selectForm.Portefeuille.value,
          selectedPortefeuilles: $("#selectedPortefeuilles").val()
        },
        success: function (data, textStatus, jqXHR) {
          console.log(data);
          
          if (data.status == 0)
          {
            submitConsolidatie();
          }
          else if (data.status == 1)
          {
            AEConfirm(data.msg, 'Consolidatie actief', function () {
              $('#verwijder').val(1);
              submitConsolidatie();
            });
          }
          else if (data.status == 2)
          {
            AEMessage(data.msg, 'Consolidatie actief', function () {
            });
          }
          
        },
        error: function (jqXHR, textStatus, errorThrown) {
        }
      });
    }
    
    function xls()
    {
      document.selectForm.target = "_blank";
      document.selectForm.extra.value = "xls";
      setRapportTypes()
      document.selectForm.save.value = "1";
      checkConsolidatie();
      
      //counter++;
    }
    
    
    function loadPortefeuilles(letter)
    {
      $('.btn-toolbar .btn-group a').removeClass('active');
      $('.sel-' + letter).addClass('active');
      
      var portefeuillesLoaded = true;
      
      inputBox = document.selectForm['inFields[]'];
      var Portefeuilles = new Array();
      
      <?
      $n = 0;
      foreach ($portefeuilles as $letter => $clientdata)
      {
        echo "Portefeuilles['$letter']	= new Array(); \n";
        
        foreach ($clientdata as $client => $portefeuilleData)
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
      
      for (var count = inputBox.options.length - 1; count >= 0; count--)
      {
        inputBox.options[count] = null;
      }
      
      if (letter == 'alles')
      {
        for (keyVar in Portefeuilles)
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
      for (keyVar in letterPortefeuilles)
      {
        inputBox.options.length++;
        inputBox.options[inputBox.options.length - 1].text = letterPortefeuilles[keyVar];
        var portefeuilleParts = keyVar.split('||')
        inputBox.options[inputBox.options.length - 1].value = portefeuilleParts[1];
      }
    }
    
    
    function changeCheck(item)
    {
      var theForm = document.selectForm.elements, z = 0;
      for (z = 0; z < theForm.length; z++)
      {
        if (theForm[z].type == "checkbox")
        {
          var test = theForm[z].name;
          if (test.search(item) == 0)
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
      document.selectForm.action = "";
      document.selectForm.submit();
    }
  </script>
  <br/>
  
  <div class="container-fluid">
    
    <form action="rapportFrontofficeConsolidatieAfdrukken.php" method="POST" target="_blank" name="selectForm">
      <input type="hidden" name="posted" value="true"/>
      <input type="hidden" name="save" value=""/>
      <input type="hidden" name="rapport_types" value=""/>
      <input type="hidden" name="extra" value=""/>
      <input type="hidden" name="verwijder" id="verwijder" value=""/>
      
      <div class="formHolder">
        
        <div class="formTabGroup ">
          <?=$AETemplate->parseBlockFromFile('rapportFrontoffice/tabbuttons.html', array(
            'consolidatie' => 'active'
          ))?>
        </div>
        
        <div class="formTitle textB">Selectie</div>
        <div class="formContent padded-10">
          
          <div class="row no-gutters">
            <div class="col-6 col-md-4 col col-xl-3" style="border-right: #d9d9d9 1px solid; height: 161px; padding-left: 30px;"><?=$selectieHtml;?></div>
            <div class="col-6 col-md-4 col col-xl-3" style="border-right: #d9d9d9 1px solid; text-align: center; height: 161px;">
              
              <?=$AETemplate->parseBlockFromFile('rapportFrontoffice/datum_selectie.html', array(
                'inpvalue'  => $inp['value'],
                'inpname'   => $inp['name'],
                'inp2value' => $inp2['value'],
                'inp2name'  => $inp2['name'],
                
                
                'beginMaand'    => $datumSelctie['beginMaand'],
                'beginKwartaal' => $datumSelctie['beginKwartaal'],
                'beginJaar'     => $datumSelctie['beginJaar'],
                'eindMaand'     => $datumSelctie['eindMaand'],
                'eindKwartaal'  => $datumSelctie['eindKwartaal'],
                'eindJaar'      => $datumSelctie['eindJaar'],
                
                'beginMaand2'    => $datumSelctie['beginMaand2'],
                'beginKwartaal2' => $datumSelctie['beginKwartaal2'],
                'beginJaar2'     => $datumSelctie['beginJaar2'],
                'totFromDatum'   => $totFromDatum,
                
                'startThisYear'  => '01-01-' . date('Y'),
                'totDatumYear'   => date('Y', strtotime($totdatum)),
                'totDatum'       => date('d-m-Y', strtotime($totdatum)),
                'fullTotDatumJs' => date('Y/m/d H:i:s', strtotime($totdatum))
              ))?>
            
            
            </div>
            <div class="col col col-xl-2" style="border-right: #d9d9d9 1px solid; height: 161px;">
              
              <div class="formblock">
                
                <input type="checkbox" value="1" id="logoOnderdrukken" name="logoOnderdrukken"> <?=vt("Logo onderdrukken")?><br>
                <? if ($rdata['rapportDoorkijk'] == 1) { ?>
                  <input type="checkbox" value="1" id="doorkijk" name="doorkijk"> <?=vt("Doorkijk")?> <br><? } ?>
                <input type="checkbox" value="1" id="voorbladWeergeven" name="voorbladWeergeven" checked> <?=vt("Voorblad weergeven")?><br>
                <input type="checkbox" value="1" id="metWachtwoord" name="metWachtwoord"> <?=vt("Met wachtwoord")?>
              
              
              </div>
            
            </div>
            <div class="col-1  col-xl-1  col-md-2  " style="">
              <div class=" btn-group-text-left " style="margin-left:10px">
                <div class="btn btn-default" style="width: 140px;" onclick="javascript:print();">
                  &nbsp;&nbsp;<i style="color:red" class="fa fa-file-pdf-o fa-fw  " aria-hidden="true"></i> <?=vt("Tonen")?>
                </div>
                <br/><br/>
                <div class="btn btn-default" style="width: 140px;" onclick="javascript:saveasfile();">
                  &nbsp;&nbsp;<i style="color:blue" class="fa fa-floppy-o fa-fw " aria-hidden="true"></i> <?=vt("Opslaan")?>
                </div>
                <br/><br/>
                
                <?
                if ($__appvar['bedrijf'] == 'HOME')
                {
                  $xlsStyle = "";
                }
                
                echo '<div class="btn btn-default" style="width: 140px;" id="xls_uitvoer" onclick="javascript:xls();">&nbsp;&nbsp;<i style="color:green" class="fa fa-file-excel-o fa-fw" aria-hidden="true"></i> '.vt("XLS uitvoer").' </div><br /><br />';
                echo '<div class="btn btn-default" style="width: 140px;" id="btnHTML" onclick="javascript:html();">&nbsp;&nbsp;<i class="fa fa-line-chart fa-fw" aria-hidden="true"></i> '.vt("HTML").'</div><br /><br />';
                
                if ((checkAccess($type) && GetModuleAccess('ORDER') < 2) || checkOrderAcces('rapportages_aanmaken') === true)
                {
                  echo '<input class="btn btn-default" type="button" onclick="javascript:order();" 			value=" '.vt("Order").' " style="width: 140px; visibility:hidden;     display: none;" id="orderButton">';
                }
                ?>
              </div>
            </div>
          </div>
        
        </div>
        
        
        <div class="formTabFooter">
          <div class="btn-toolbar" role="toolbar">
            <div class="btn-group mr-2" role="group" aria-label="First group">
              <?
              foreach ($portefeuilles as $letter => $data)
              {
                echo '<a class="btn btn-hover btn-default sel-' . $letter . '" href="javascript:loadPortefeuilles(\'' . $letter . '\');" class="letterButton">' . $letter . '</a>';
              }
              ?>
              <a class="btn btn-hover btn-default sel-alles" href="javascript:loadPortefeuilles('alles');" class="letterButton"><?=vt("alles")?></a>
            </div>
          </div>
        </div>
      
      
      </div>
      
      
      <!-- Voorselectie Portefeuilles -->
      <div class="formHolder" id="">
        <div class="formTabGroup ">
          <div class="btn-group">
          <span class="btn btn-hover btn-default {clienten}"
                onclick="$('#voorFilterDiv').toggle();$('#portefeuilleSelectie').hide(); if($('#voorFilterSpanOpen').html()=='Open'){$('#voorFilterSpanOpen').html('<?=vt("Sluit")?>');}else{$('#voorFilterSpanOpen').html('<?=vt("Open")?>')};return false;"
          > <span id="voorFilterSpanOpen"><?=vt("Open")?></span> <?=vt("Voorselectie Portefeuilles")?></span>
          
          </div>
        </div>
        <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
          
          <div id="voorFilterDiv" style="display:none;">
            <?php
            $DB = new DB();
            $maxVink = 25;
            $opties = array('Vermogensbeheerder' => 'Vermogensbeheerder', 'Accountmanager' => 'accountmanager', 'TweedeAanspreekpunt' => 'tweedeAanspreekpunt', 'Depotbank' => 'depotbank');
            foreach ($opties as $optie => $omschrijving)
            {
              $data = $selectie->getData($optie);
              if (count($data) < $maxVink)
              {
                echo $selectie->createCheckBlok($optie, $data, $_POST, $omschrijving);
              }
              else
              {
                echo $selectie->createSelectBlok($optie, $data, $_POST, $omschrijving);
              }
            }
            $opties = array(
              'Risicoklasse' => 'Risicoklasse',
              'AFMprofiel' => 'AFMprofiel',
              'SoortOvereenkomst' => 'SoortOvereenkomst',
              'Remisier' => 'Remisier',
              'PortefeuilleClusters' => 'PortefeuilleClusters',
              'selectieveld1'=>'Selectieveld1',
              'selectieveld2'=>'Selectieveld2');
            foreach ($opties as $optie => $omschrijving)
            {
              $data = $selectie->getData($optie);
              if (count($data) > 1)
              {
                if ($_SESSION['selectieMethode'] == 'vink' && count($data) < $maxVink)
                {
                  echo $selectie->createCheckBlok($optie, $data, $_POST, $omschrijving);
                }
                else
                {
                  echo $selectie->createSelectBlok($optie, $data, $_POST, $omschrijving);
                }
              }
            }
            ?>
            
            <div class="formblock">
              
              <div class="formlinks"> &nbsp;</div>
              <div class="formrechts">
                <input type="button" onclick="javascript:OpnieuwLaden();" value="<?=vt("Selectie ophalen")?>"></div>
            </div>
          </div>
        </div>
      </div>
  
  
      <!-- Selectie -->
      <div class="formHolder"  id="PSelectie" >
        <div class="formTabGroup ">
          <div class="btn-group">
  
          <span class="btn btn-hover btn-default" onclick="javascript:$('#portefeuilleSelectie').toggle();if(portefeuillesLoaded==false){portefeuillesLoaded=true;loadPortefeuilles('alles');}document.getElementById('id_enkelvoudig').checked=true;">
            <?=vt("Toon enkelvoudige selectie")?>
          </span>

        </div>
      </div>
        <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      
          <input type="hidden" name="setValue" value="fields">
      
      
          <div style="display: none" class="icon">
            <input type="radio" checked value="" name="Portefeuille" id="id_enkelvoudig"></div>
      
          
      
          <br>
      
          <div id="portefeuilleSelectie" style="<?=$enkelVoudigeSelectieStyle?>"><br>
            <table cellspacing="0" width="500">
              <tr>
                <td>
                  <select name="inFields[]" multiple size="16" style="min-width : 200px; margin-left: 13px;" onfocus="document.getElementById('id_enkelvoudig').checked=true;">
              
                  </select>
                </td>
                <td width="70">
                  <a href="javascript:moveItem(document.selectForm['inFields[]'],document.selectForm['selectedFields[]']);">
                    <img src="images/16/pijl_rechts.png" width="16" height="16" border="0" alt="<?=vt("toevoegen")?> align="absmiddle">
                  </a>
                  <br><br>
                  <a href="javascript:moveItem(document.selectForm['selectedFields[]'],document.selectForm['inFields[]']);">
                    <img src="images/16/pijl_links.png" width="16" height="16" border="0" alt="<?=vt("verwijderen")?> align="absmiddle">
                  </a>
                </td>
                <td>
                  <select id="selectedPortefeuilles" name="selectedFields[]" multiple size="16" style="min-width : 200px">
                    <?=$portfeuilleOptions2?>
                  </select>
                </td>
                <td width="70">
                  <a href="javascript:moveOptionUp(document.selectForm['selectedFields[]'])">
                    <img src="images/16/pijl_omhoog.png" width="16" height="16" border="0" alt="<?=vt("omhoog")?> align="absmiddle">
                  </a>
                  <br><br>
                  <a href="javascript:moveOptionDown(document.selectForm['selectedFields[]'])">
                    <img src="images/16/pijl_omlaag.png" width="16" height="16" border="0" alt="<?=vt("omlaag")?> align="absmiddle">
                  </a>
                </td>
              </tr>
            </table>
          </div>
          <br>
          <?
          $__appvar['rowsPerPage'] = 100;
          $list = new MysqlList();
          $list->idField = "id";
          $list->editScript = $editScript;
          $list->perPage = $__appvar['rowsPerPage'];
      
          $list->addField("Portefeuilles", "id", array("width" => 100, "search" => false));
          $list->addField("Portefeuilles", "Portefeuille", array("list_width" => 150, "search" => true));
          $list->addField("Portefeuilles", "Client", array("list_width" => 200, "search" => true));
      
      
          if ($rdata['CrmClientNaam'] == '1')
          {
            $list->addField("", "Naam", array("list_width" => 300, 'sql_alias' => 'CRM_naw.naam', "search" => true));
            $list->addField("", "crmId", array('sql_alias' => 'CRM_naw.id', "search" => true, 'list_invisible' => true));
          }
          else
          {
            $list->addField("Client", "Naam", array("list_width" => 300, "search" => true));
            $clientWhere = "AND Portefeuilles.Client=Clienten.Client";
          }
      
          $allow_add = false;
          $internDepotToegang = '';
      
          if ($rdata['CrmClientNaam'] == '1')
          {
            $list->setJoin("LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.Portefeuille INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '" . $USR . "'
	                JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker");
          }
          else
          {
            $list->setJoin(" INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '" . $USR . "'
	                  JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker");
          }
      
          $list->setWhere("Portefeuilles.consolidatie=1 $clientWhere ");
          $_GET['sort'][] = "Portefeuilles.Client";
          $_GET['direction'][] = "ASC";
      
          if (checkAccess($type))
          {
            $beperktToegankelijk = '';
          }
          else
          {
            if ($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
            {
              $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='" . $_SESSION['usersession']['gebruiker']['Accountmanager'] . "' OR Portefeuilles.tweedeAanspreekpunt ='" . $_SESSION['usersession']['gebruiker']['Accountmanager'] . "' $internDepotToegang) ";
            }
            else
            {
              $beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
            }
        
          }
      
          $list->setOrder($_GET['sort'], $_GET['direction']);
          $list->setSearch($_GET['selectie']);
          $list->selectPage($_GET['page']);
      
          session_start();
          $_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
          $_SESSION["NAV"]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'], $allow_add));
          $_SESSION["NAV"]->addItem(new NavSearch($_GET['selectie']));
      
          $selectPortefeuilles = '';
          for ($i = 1; $i < 11; $i++)
          {
            $selectPortefeuilles .= "Portefeuille" . $i;
            if ($i < 10)
            {
              $selectPortefeuilles .= ",";
            }
          }
          $DB = new DB();
      
          ?>
        </div>
      </div>
  
  
  
  
      <?
  
      if( isset ($rapportSettings[$rdata['layout']]) )
      {
        echo $rapportSettings[$rdata['layout']];
      }
  
      if($rdata['layout'] <> 13 && $rdata['layout'] <> 5 )
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
            <!--
        <div class="formblock">
          <div class="formlinks"> Invoer Profiel </div>
          <div class="formrechts"> <select name="vkma_risicoklasse"><option value="">-</option><?=$risicoklassen?></select></div>
        </div>
      -->
            <div class="formblock">
              <div class="formlinks"> <?=vt("Eindjaar")?> </div>
              <div class="formrechts"> <input type="text" name="vkma_eindjaar" value="" size="4"> </div>
            </div>
        
            <div class="formblock">
              <div class="formlinks"> <?=vt("Kostencomponenten")?> </div>
              <div class="formrechts"> <input type="text" name="vkma_kosten_beheer" value="" size="2" > <?=vt("Beheerkosten")?> <br>
                <input type="text" name="vkma_kosten_service" value="" size="2"> <?=vt("Servicekosten")?> <br>
                <input type="text" name="vkma_kosten_transactie" value="" size="2"> <?=vt("Transactiekosten")?> <br>
                <input type="text" name="vkma_kosten_bank" value="" size="2" > <?=vt("Overige bankkosten")?> <br>
              </div>
            </div>
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
                <u><?=vt("Niveau")?></u><br>
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
        <?=($htmlRapportageEnabled == 1?$htmlRapportJS . ';':'htmlRapport = ["dummy"]; ');?>
        
        jQuery.each(htmlRapport, function (i, val) {
          if (parent.frames['submenu'].$("#" + val).is(":checked"))
          {
            $('#btnHTML').show();
          }
        });
        
      }
      
      $(document).ready(function () {
        $('#btnHTML').hide();
        var <?=$htmlRapportJS?>;
        var htmlRapportageEnabled = <?=($htmlRapportageEnabled)?1:0;?>;
        if (htmlRapportageEnabled == 1)  // kijk of knop getoond moet worden als er al vinkjes zijn..
        {
          setTimeout(checkHTMLButton, 1000);  // wacht 1 sec om laden submenu te laten afronden
        }
      });
    </script>

<?
if ($__debug)
{
  echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"], $content);
