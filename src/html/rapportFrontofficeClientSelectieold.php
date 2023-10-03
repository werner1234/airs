<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2019/12/04 14:14:18 $
 		File Versie					: $Revision: 1.3 $

 		$Log: rapportFrontofficeClientSelectieold.php,v $
 		Revision 1.3  2019/12/04 14:14:18  rm
 		rapport knoppen omzetten naar oud
 		
 		Revision 1.2  2019/10/11 13:30:24  rm
 		7929
 		
 		Revision 1.1  2019/09/18 14:12:24  rm
 		7929
 		
 		Revision 1.113  2019/02/27 13:48:29  rvv
 		*** empty log message ***
 		
 		Revision 1.112  2019/01/21 08:52:28  rvv
 		*** empty log message ***
 		
 		Revision 1.111  2019/01/20 12:13:03  rvv
 		*** empty log message ***
 		
 		Revision 1.110  2018/10/13 17:16:37  rvv
 		*** empty log message ***
 		
 		Revision 1.109  2018/08/27 17:17:09  rvv
 		*** empty log message ***
 		
 		Revision 1.108  2018/05/27 10:21:15  rvv
 		*** empty log message ***
 		
 		Revision 1.107  2018/03/03 17:11:34  rvv
 		*** empty log message ***
 		
 		Revision 1.106  2018/02/11 13:24:12  rvv
 		*** empty log message ***
 		
 		Revision 1.105  2017/10/17 13:02:20  rm
 		Html rapport
 		
 		Revision 1.104  2017/08/21 07:20:27  cvs
 		call 5933
 		
 		Revision 1.103  2017/07/17 06:37:59  rvv
 		*** empty log message ***
 		
 		Revision 1.102  2017/07/16 10:50:44  rvv
 		*** empty log message ***
 		
 		Revision 1.101  2017/04/05 15:37:30  rvv
 		*** empty log message ***
 		
 		Revision 1.100  2017/04/02 10:01:40  rvv
 		*** empty log message ***
 		
 		Revision 1.99  2017/04/02 05:50:00  rvv
 		*** empty log message ***
 		
 		Revision 1.98  2017/02/28 13:40:15  cvs
 		no message
 		
 		Revision 1.97  2017/01/29 10:17:56  rvv
 		*** empty log message ***
 		
 		Revision 1.96  2017/01/27 12:21:05  cvs
 		excel knop verdwijnt
 		
 		Revision 1.95  2017/01/25 15:54:06  rvv
 		*** empty log message ***
 		
 		Revision 1.94  2017/01/16 13:24:49  cvs
 		no message
 		
 		Revision 1.93  2017/01/16 13:17:33  cvs
 		no message
 		
 		Revision 1.92  2017/01/16 12:34:29  cvs
 		call 5558
 		
 		Revision 1.91  2016/12/28 19:37:27  rvv
 		*** empty log message ***
 		
 		Revision 1.90  2016/12/22 08:42:58  rvv
 		*** empty log message ***
 		
 		Revision 1.89  2016/12/21 16:32:06  rvv
 		*** empty log message ***
 		
 		Revision 1.88  2016/04/17 17:56:35  rvv
 		*** empty log message ***
 		
 		Revision 1.87  2016/04/04 08:50:47  cvs
 		HTML rapportage
 		
 		Revision 1.86  2016/03/20 14:26:45  rvv
 		*** empty log message ***
 		
 		Revision 1.85  2015/12/16 17:04:12  rvv
 		*** empty log message ***
 		
 		Revision 1.84  2015/09/13 11:30:03  rvv
 		*** empty log message ***
 		
 		Revision 1.83  2015/06/06 15:22:46  rvv
 		*** empty log message ***
 		
 		Revision 1.82  2015/06/06 15:18:52  rvv
 		*** empty log message ***
 		
 		Revision 1.81  2015/06/03 15:04:51  rvv
 		*** empty log message ***
 		
 		Revision 1.80  2015/05/27 16:15:51  rvv
 		*** empty log message ***
 		
 		Revision 1.79  2015/05/23 12:50:37  rvv
 		*** empty log message ***
 		
 		Revision 1.78  2015/05/03 12:59:54  rvv
 		*** empty log message ***
 		
 		Revision 1.77  2015/03/24 16:30:44  rvv
 		*** empty log message ***
 		
 		Revision 1.76  2015/03/22 10:39:17  rvv
 		*** empty log message ***
 		
 		Revision 1.75  2014/11/02 14:03:24  rvv
 		*** empty log message ***
 		
 		Revision 1.74  2014/10/19 08:50:41  rvv
 		*** empty log message ***
 		
 		Revision 1.73  2014/08/23 15:36:34  rvv
 		*** empty log message ***
 		
 		Revision 1.72  2014/01/18 17:28:39  rvv
 		*** empty log message ***
 		
 		Revision 1.71  2014/01/11 15:51:42  rvv
 		*** empty log message ***
 		
 		Revision 1.70  2013/12/22 16:04:27  rvv
 		*** empty log message ***
 		
 		Revision 1.69  2013/12/07 17:50:41  rvv
 		*** empty log message ***
 		
 		Revision 1.68  2013/10/05 15:56:28  rvv
 		*** empty log message ***
 		
 		Revision 1.67  2013/09/07 15:59:15  rvv
 		*** empty log message ***
 		
 		Revision 1.66  2013/09/04 16:16:25  rvv
 		*** empty log message ***
 		
 		Revision 1.65  2013/05/12 11:17:29  rvv
 		*** empty log message ***
 		
 		Revision 1.64  2013/01/06 10:08:36  rvv
 		*** empty log message ***
 		
 		Revision 1.63  2012/12/22 15:31:53  rvv
 		*** empty log message ***
 		
 		Revision 1.62  2012/11/25 13:15:50  rvv
 		*** empty log message ***
 		
 		Revision 1.61  2012/08/11 13:05:20  rvv
 		*** empty log message ***
 		
 		Revision 1.60  2012/06/27 15:59:31  rvv
 		*** empty log message ***

 		Revision 1.59  2012/05/06 11:54:01  rvv
 		*** empty log message ***

 		Revision 1.58  2012/04/11 17:14:52  rvv
 		*** empty log message ***

 		Revision 1.57  2012/04/01 07:37:40  rvv
 		*** empty log message ***

 		Revision 1.56  2011/11/23 18:54:09  rvv
 		*** empty log message ***

 		Revision 1.55  2011/11/09 18:50:11  rvv
 		*** empty log message ***

 		Revision 1.54  2011/10/12 17:56:12  rvv
 		*** empty log message ***

 		Revision 1.53  2011/08/31 14:37:40  rvv
 		*** empty log message ***

 		Revision 1.52  2011/07/30 16:37:29  rvv
 		*** empty log message ***

 		Revision 1.51  2011/07/27 16:26:05  rvv
 		*** empty log message ***

 		Revision 1.50  2011/06/15 15:37:48  rvv
 		*** empty log message ***

 		Revision 1.49  2011/06/13 14:36:12  rvv
 		*** empty log message ***

 		Revision 1.48  2011/06/02 15:03:40  rvv
 		*** empty log message ***

 		Revision 1.47  2011/04/17 09:11:14  rvv
 		*** empty log message ***

 		Revision 1.46  2011/04/13 14:16:57  rvv
 		*** empty log message ***

 		Revision 1.45  2011/03/13 18:40:35  rvv
 		*** empty log message ***

 		Revision 1.44  2011/03/06 18:20:04  rvv
 		*** empty log message ***

 		Revision 1.43  2011/03/02 08:02:15  rvv
 		*** empty log message ***

 		Revision 1.42  2011/02/26 16:00:39  rvv
 		*** empty log message ***

 		Revision 1.41  2011/02/24 17:49:22  rvv
 		*** empty log message ***

 		Revision 1.40  2010/11/27 16:15:25  rvv
 		*** empty log message ***

 		Revision 1.39  2010/11/14 10:49:33  rvv
 		*** empty log message ***

 		Revision 1.38  2010/08/28 14:58:45  rvv
 		*** empty log message ***
*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/selectOptieClass.php");
$selectie=new selectOptie();

$htmlRapportageEnabled = getVermogensbeheerderField("HTMLRapportage");
$htmlRapportJS = "htmlRapport = ['ATT','VOLK','TRANS','MUT','MODEL']";


if(!is_array($_SESSION['lastGET']))
  $_SESSION['lastGET']=array();
$_SESSION['lastGET']=array_merge($_SESSION['lastGET'],$_GET);
if($_SESSION['metConsolidatie']=='')
  $_SESSION['metConsolidatie']=0;

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
  unset($frontOfficeData);

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
    $alleenActief = " AND Portefeuilles.Einddatum  >=  NOW() AND Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."'";
  else
    $alleenActief = " AND Portefeuilles.Einddatum  >=  NOW() ";
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
  $list->setWhere("Portefeuilles.Client = Clienten.Client ".$extraWhere.$alleenActief.$beperktToegankelijk." ");

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

$selectie->getInternExternActive();
if($_SESSION['lastGET']['actief'] == "inactief" )
  $inactiefChecked = "checked";
elseif($_SESSION['lastGET']['actief'] == "eActief" )
  $eActiefChecked = "checked";
else
  $actiefChecked = "checked";

$html .='
<input type="radio" name="actief" id="actief" value="actief" '.$actiefChecked.' onClick="parent.frames[\'content\'].document.location = \''.$PHP_SELF.'?actief=actief\'">
<label for="actief" title="actief"> Actieve portefeuilles  </label>
<input type="radio" name="actief" id="eActief" value="eActief" '.$eActiefChecked.' onClick="parent.frames[\'content\'].document.location = \''.$PHP_SELF.'?actief=eActief\'">
<label for="actief" title="eActief"> Eigen act. portefeuilles  </label>
<input type="radio" name="actief" id="inactief" value="inactief" '.$inactiefChecked.' onClick="parent.frames[\'content\'].location = \''.$PHP_SELF.'?actief=inactief\'">
<label for="inactief" title="actief"> Alle portefeuilles </label>';


$html .="<br>";
$html.=$selectie->getInternExternHTML($PHP_SELF);
$html .="<br>";
if(method_exists($selectie,'getConsolidatieHTML'))
  $html.=$selectie->getConsolidatieHTML($PHP_SELF);

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
  
  <br><br>
  <div class="tabbuttonRow">
    <input type="button" class="tabbuttonActive" onclick="" id="tabbutton0" value="Clienten">
    <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeFondsSelectieold.php';" id="tabbutton1" value="Fondsen">
    <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeManagementSelectieold.php';" id="tabbutton2" value="Management info">
    <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeOptieToolsold.php';" id="tabbutton3" value="Optie tools">
    <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeConsolidatieSelectieold.php';" id="tabbutton4" value="Consolidatie tool">
  </div>
  <br>
  
  <form action="rapportFrontofficeClientAfdrukken.php" method="POST" target="_blank" name="selectForm">
    <input type="hidden" name="posted" value="true" />
    <input type="hidden" name="save" value="" />
    <input type="hidden" name="rapport_types" value="" />
    <input type="hidden" name="extra" value="" />
    
    <table border="0">
      <tr>
        <td width="570">
          <div class="form">
            
            <fieldset id="Selectie" >
              <legend accesskey="S"><u>S</u>electie</legend>
              
              
              
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
              
              $kal = new DHTML_Calendar();
              $inp = array ('name' =>"datum_van",'value' =>(!empty($_SESSION['rapportDateFrom']))?$_SESSION['rapportDateFrom']:date("d-m-Y",mktime(0,0,0,1,1,$jr)),'size'  => "11");
              $kal2 = new DHTML_Calendar();
              $inp2 = array ('name' =>"datum_tot",'value' =>(!empty($_SESSION['rapportDateTm']))?$_SESSION['rapportDateTm']:date("d-m-Y",db2jul($totdatum)),'size'  => "11");
              
              ?>
              <table>
                <tr>
                  <td width="100">
                    Van datum:
                  </td>
                  <td>
                    <?=$kal->make_input_field("",$inp,"onChange=\"javascript:doStore()\"")?>
                  </td>
                  <td>
                    &nbsp; Vorige &nbsp; &nbsp; &nbsp;<a style="color: Navy;font-weight: bold;"  href="#" onclick="javascript:document.selectForm.datum_van.value='<?=$datumSelctie['beginMaand']?>';document.selectForm.datum_tot.value='<?=$datumSelctie['eindMaand']?>';doStore();">Maand</a>,
                    <a style="color: Navy;font-weight: bold;" href="#" onclick="javascript:document.selectForm.datum_van.value='<?=$datumSelctie['beginKwartaal']?>';document.selectForm.datum_tot.value='<?=$datumSelctie['eindKwartaal']?>';doStore();">Kwartaal</a>,
                    <a style="color: Navy;font-weight: bold;"  href="#" onclick="javascript:document.selectForm.datum_van.value='<?=$datumSelctie['beginJaar']?>';document.selectForm.datum_tot.value='<?=$datumSelctie['eindJaar']?>';doStore();">Jaar</a>
                  </td>
                </tr>
                <tr>
                  <td>
                    T/m datum:
                  </td>
                  <td>
                    <?=$kal2->make_input_field("",$inp2,"onChange=\"javascript:doStore()\"")?>
                  </td>
                  <td>
                    &nbsp; Huidige &nbsp; <a style="color: Navy;font-weight: bold;" href="#" onclick="javascript:document.selectForm.datum_van.value='<?=$datumSelctie['beginMaand2']?>';document.selectForm.datum_tot.value='<?=$totFromDatum?>';doStore();">Maand</a>,
                    <a style="color: Navy;font-weight: bold;" href="#" onclick="javascript:document.selectForm.datum_van.value='<?=$datumSelctie['beginKwartaal2']?>';document.selectForm.datum_tot.value='<?=$totFromDatum?>';doStore();">Kwartaal</a>,
                    <a style="color: Navy;font-weight: bold;" href="#" onclick="javascript:document.selectForm.datum_van.value='<?=$datumSelctie['beginJaar2']?>';document.selectForm.datum_tot.value='<?=$totFromDatum?>';doStore();">Jaar</a>
                  </td>
                </tr>
              
              </table>
              
              
              <div class="formblock">
                <div>  <input type="checkbox" value="1" id="logoOnderdrukken" name="logoOnderdrukken"> Logo onderdrukken <? if($rdata['rapportDoorkijk']==1) {?> <input type="checkbox" value="1" id="doorkijk" name="doorkijk"> Doorkijk <?}?>
                  <input type="checkbox" value="1" id="voorbladWeergeven" name="voorbladWeergeven"> Voorblad weergeven
                  <?
                  if($__appvar['master']==true)
                  {
                    ?><input type="checkbox" value="1" id="debug" name="debug"> Debug <input type="text" value="" size="1" id="layout" name="layout"> Layout <?
                  }
                  ?>
                  <input type="checkbox" value="1" id="anoniem" name="anoniem">Anonieme rapportage
                  <input type="checkbox" value="1" id="crmInstellingen" name="crmInstellingen" onclick="if(this.checked){enableDisableRapport(true);}else{enableDisableRapport(false);}" >CRM Instellingen
                </div>
            </fieldset>
          
          </div>
        </td>
        <td>
          <div class="buttonDiv" onclick="javascript:print();">&nbsp;&nbsp;<?=maakKnop('pdf.png',array('size'=>16))?> Afdrukken</div><br>
          <div class="buttonDiv" onclick="javascript:saveasfile();">&nbsp;&nbsp;<?=maakKnop('disk_blue.png',array('size'=>16))?> Opslaan </div><br>
          
          <?
          
          if($__appvar['bedrijf']=='HOME')
            $xlsStyle="";
          //   $xlsStyle="display:none;";
          //if($rdata['frontofficeClientExcel']==1)
          //  $xlsStyle='';
          
          echo '<div class="buttonDiv" id="xls_uitvoer" style="'.$xlsStyle.'" onclick="javascript:xls();">&nbsp;&nbsp;'.maakKnop('xls.png',array('size'=>16)).' XLS uitvoer </div><br>';
          //if ($__develop OR $__appvar["bedrijf"] == "HOME" OR $__appvar["bedrijf"] == "TEST")
          {
            echo '<div class="buttonDiv" id="btnHTML" onclick="javascript:html();">&nbsp;&nbsp;'.maakKnop('html.png',array('size'=>16)).' HTML</div><br>';
          }
          
          if((checkAccess($type) && GetModuleAccess('ORDER') < 2) || checkOrderAcces('rapportages_aanmaken') === true)
            echo '<input type="button" onclick="javascript:order();" 			value=" Order " style="width:100px;visibility:hidden" id="orderButton"><br>';
          ?>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <a href="<?=$PHP_SELF?>?letter=0-9" class="letterButton" > 0-9 </a>
          <?
          for($a=65; $a <= 90; $a++)
          {
            echo "<a href=\"".$PHP_SELF."?letter=".chr($a)."\" class=\"letterButton\">".chr($a)."</a>\n";
          }
          ?>
          <a href="<?=$PHP_SELF?>?letter=" class="letterButton" style="width:26px">alles</a>
        </td>
      </tr>
    </table>
    
    <table>
      <tr>
        <td valign="top">
          
          
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
              
              $template = '<tr class="'.$trclass.'" onmouseover="this.className=\'list_dataregel_hover\'" onmouseout="this.className=\''.$trclass.'\'" onClick="javascript:document.getElementById(\'{Portefeuille_value}\').checked=true;">
<td class="list_button">
	<div class="icon"><input type="radio" '.$recordSelected.' value="{Portefeuille_value}" name="Portefeuille" id="{Portefeuille_value}"></div>
</td>
<td class="listTableData"  width="150" align="left" >{Portefeuille_value} &nbsp;</td>
<td class="listTableData"  width="50" align="left" >{Depotbank_value} &nbsp;</td>
<td class="listTableData"  width="150" align="left" >{Client_value} &nbsp;</td>
<td class="listTableData"  align="left" >{Naam_value} &nbsp;</td>
'.$newCrmLink.$link2.'
</tr>';
              
              
              echo $list->buildRow($data,$template,"");
            }
            
            
            ?>
          </table>
        </td>
        
        <?
        if(isset($rapportSettings[$rdata['layout']]))
          echo $rapportSettings[$rdata['layout']];
        
        if($rdata['layout'] <> 13 && $rdata['layout'] <> 5)
        {
          $db=new DB();
          $query="SELECT id FROM inflatiepercentages limit 1";
          if($db->QRecords($query)>0)
          {
            $inflatieVinkje="<input type=\"checkbox\" value=\"1\" name=\"scenario_inflatie\"> Inclusief inflatie. ";
          }
          else
          {
            $inflatieVinkje='';
          }
          
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
            
            <fieldset id="SCENARIO_Settings"  style="display:none;">
              <legend accesskey="e">S<u>e</u>nario</legend>
              <input type="checkbox" value="1" name="scenario_portefeuilleWaardeGebruik"> Gebruik waarde op rapportage datum.</br>
              <input type="checkbox" value="1" name="scenario_werkelijkVerloop"> Werkelijk verloop.</br>
              <?=$inflatieVinkje?>
            </fieldset>
          </td>
          
          <td valign="top">
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
?>