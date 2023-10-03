<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 	Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 	File Versie					: $Revision: 1.10 $

 	$Log: ordersEditBulk.php,v $
 	Revision 1.10  2018/08/18 12:40:14  rvv
 	php 5.6 & consolidatie
 	
 	Revision 1.9  2013/11/06 15:52:25  rvv
 	*** empty log message ***
 	
 	Revision 1.8  2013/10/26 15:40:51  rvv
 	*** empty log message ***
 	
 	Revision 1.7  2013/09/28 14:42:13  rvv
 	*** empty log message ***
 	
 	Revision 1.6  2013/09/25 15:58:39  rvv
 	*** empty log message ***
 	
 	Revision 1.5  2013/09/18 15:37:28  rvv
 	*** empty log message ***
 	
 	Revision 1.4  2013/09/07 15:59:15  rvv
 	*** empty log message ***
 	
 	Revision 1.3  2013/08/14 15:57:30  rvv
 	*** empty log message ***
 	
 	Revision 1.2  2013/06/01 16:14:14  rvv
 	*** empty log message ***
 	
 	Revision 1.1  2013/05/29 15:48:45  rvv
 	*** empty log message ***
 	
 	

*/
include_once("wwwvars.php");
include_once("../config/ordersVars.php");
include_once("../classes/editObject.php");
include_once("../classes/AE_cls_dateFrom.php");
include_once("../classes/mysqlList.php");
include_once("orderControlleRekenClass.php");

session_start();
$__funcvar['listurl']  = "ordersList.php";
$__funcvar['location'] = "ordersEdit.php";

//$_SESSION['submenu'] = New Submenu();
//$_SESSION['submenu']->addItem("Verwerken","tijdelijkebulkordersList.php");
//$_SESSION['submenu']->addItem("<br>","");


$_GET = array_merge($_GET,$_POST);
$currentBatch=$_GET['batchId'];

$object = new TijdelijkeBulkOrders();
$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$_SESSION['NAV']='';

$kal = new DHTML_Calendar();
$editcontent['calendar'] = $kal->get_load_files_code();

$koppelObject = array();
$koppelObject[0] = new Koppel("Fondsen","editForm");
$koppelObject[0]->addFields("ISINCode","ISINCode",true,true);
$koppelObject[0]->addFields("Fonds","fonds",false,true);
$koppelObject[0]->addFields("Omschrijving","",true,true);
//$koppelObject[0]->action = "fondsSelected();";
$koppelObject[0]->name = "fonds";
$koppelObject[0]->focus = "koersLimiet";
$koppelObject[0]->extraQuery = " AND (EindDatum > NOW() OR EindDatum = '0000-00-00') ORDER BY Fondsen.Omschrijving";

//$editcontent['javascript']=str_replace('document.editForm.submit();','if(checkPage())document.editForm.submit();',$editcontent['javascript']);
$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/popup.js\" type=text/javascript></script>";
//$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/ordersEdit.js\" type=text/javascript></script>\n";
$editcontent['javascript'] .= "\n".$koppelObject[0]->getJavascript();
$editcontent['body'] = "onLoad='document.editForm.portefeuille.focus();'";

$editcontent['javascript'] .= "

function submitForm()
{
  document.editForm.submit();
}

function addRecord()
{
	//parent.frames['content'].location = '".$editScript."?action=new';
}

";

$type='portefeuille';  
if(!checkAccess($type))
{
   if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
	   $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang) ";
	 else
	 {
    	$join=" INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	                  JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker";
	    $beperktToegankelijk = " AND  (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
	 }
}

//}
$editObject->__appvar = $__appvar;
$data = $_GET;
if($data['action']=='')
  $data['action']='new';
  
$action = $data['action'];
$redirectUrl = "orderregelsList.php";
$editObject->usetemplate = true;


$editObject->controller($action,$data);


$DB = new DB();
$DB->SQL("SELECT pagina FROM TijdelijkeBulkOrders WHERE change_user='$USR' GROUP BY pagina ORDER BY change_date ASC");
$DB->Query();
$maxPagina=0;
$selectedPagina=$object->get('pagina');
if($_GET['pagina'])
  $selectedPagina=$_GET['pagina'];

while($pagina = $DB->NextRecord())
{
  if($pagina['pagina'] > $maxPagina)
    $maxPagina=$pagina['pagina'];
	$paginaOptions .= "<option value=\"".$pagina['pagina']."\"  ".($selectedPagina==$pagina['pagina']?"selected":"")." >".$pagina['pagina']."</option>\n";
}


if($data['action']=='new')
{
  $object->set('portefeuille',$data['portefeuille']);
  $object->set('client',$data['client']);
  

}


$query="SELECT MAX(regelNr) as regelNr, max(depotbank) as depotbank FROM TijdelijkeBulkOrders WHERE change_user='$USR' AND pagina='$selectedPagina'";
$DB->SQL($query);//echo $query;exit;
$regelNr=$DB->lookupRecord();
$laatsteDepotbank=$regelNr['depotbank'];
if($action=='edit' || $action=='new')
{
  if($object->get('regelNr')==1)
  {
    $object->set('regelNr',$regelNr['regelNr']+1);
  }
}

$koppelObject[1] = new Koppel("Portefeuilles","editForm",'LEFT JOIN CRM_naw on Portefeuilles.Portefeuille=CRM_naw.portefeuille '.$join);
$koppelObject[1]->addFields("Client","client",true,true);
$koppelObject[1]->addFields("Portefeuilles.Portefeuille","portefeuille",true,true);
$koppelObject[1]->addFields("Depotbank","depotbank",true,true);
$koppelObject[1]->name = "client";

$depotbankFilter='';
if($laatsteDepotbank <> '')
  $depotbankFilter=" AND depotbank='$laatsteDepotbank'";
 
$koppelObject[1]->extraQuery = " AND Portefeuilles.einddatum > NOW() AND Portefeuilles.InternDepot=0 $beperktToegankelijk $depotbankFilter";
$koppelObject[1]->focus = "transactieSoort";
$editcontent['javascript'] .= "\n".$koppelObject[1]->getJavascript();

// 

$editObject->formTemplate = '<form name="editForm"  method="POST">

<div class="form" >
  <input type="hidden" name="action" value="update">
  <input type="hidden" name="updateScript" value="{updateScript}">
  <input type="hidden" name="returnUrl" value="{returnUrl}">
  <input type="hidden" name="redirect" value="0">
  <input type="hidden" name="id" value="{id_value}">
  <input type="hidden" name="depotbank" value="{depotbank_value}">

<br>
Pagina :
<select name="pagina" onChange="document.editForm.action.value=\'edit\';document.editForm.submit();">
<option value="'.($maxPagina+1).'">Nieuw</option>
'.$paginaOptions.'
</select>
<br>
<br>

<fieldset title="Order" class="{fieldsetClass}">
<legend><b>Order regels toevoegen</b></legend>

<table border="0" cellpadding="6" cellspacing="0">
<tr>
 <td>#</td><td>Client</td><td>Portefeuille</td><td>Transactiesoort</td><td>Aantal</td><td>ISIN</td><td>Fonds</td><td>limietkoers</td><td></td>
</tr>
<tr>
<td>{regelNr_inputfield} {regelNr_error} </td>
 <td><a href="javascript:select_client(document.editForm.client.value,600,400);">
        <img src="images/16/lookup.gif" border="0" height="18" align="middle"></a> 
        {client_inputfield} {client_error}
 </td>
 <td>

    <input type="text" name="portefeuille" id="portefeuille" value="{portefeuille_value}" READONLY  style="background-color:#DDDDDD" >
   {portefeuille_error}
 </td>
  <td>{transactieSoort_inputfield} {transactieSoort_error}</td>
  <td>{aantal_inputfield} {aantal_error}</td>
 <td><a href="javascript:select_fonds(document.editForm.ISINCode.value,600,400);">
     <img src="images/16/lookup.gif" border="0" height="18" align="middle">
     </a>
     {ISINCode_inputfield} {ISINCode_error}
 </td>
  <td>{fonds_inputfield}{fonds_error}</td>
  <td>{koersLimiet_inputfield}{koersLimiet_error} &nbsp;</td>


 <td><input type="button" value="opslaan" onclick="document.editForm.submit();" /></td>

</tr>
</table>
</form>

</FIELDSET>
{knoppen}
</form></div>
';
$editObject->template = $editcontent;

echo $editObject->getOutput();



if ($result = $editObject->result)
{
  	header("Location: ordersEditBulk.php?action=new&portefeuille=".$_POST['portefeuille']."&client=".$_POST['client']."&pagina=".$_POST['pagina']);
}
else
{
	echo $_error = $editObject->_error;
}



$__appvar['rowsPerPage']=1000;

$subHeader     = "";
$mainHeader    = " Verwerk geselecteerde fondsregels tot orders.";

$editScript = "ordersEditBulk.php";
$allow_add  = false;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("TijdelijkeBulkOrders","id",array("list_width"=>"100","search"=>false));
$list->addColumn("TijdelijkeBulkOrders","regelNr",array("description"=>'#',"list_width"=>"30","search"=>false,'list_align'=>'right'));
$list->addColumn("TijdelijkeBulkOrders","client",array("list_width"=>"200","search"=>false));
$list->addColumn("TijdelijkeBulkOrders","portefeuille",array("list_width"=>"200","search"=>false));
$list->addColumn("TijdelijkeBulkOrders","transactieSoort",array("list_width"=>"100","search"=>false));
$list->addColumn("TijdelijkeBulkOrders","aantal",array("list_width"=>"100","search"=>false));
$list->addColumn("TijdelijkeBulkOrders","fonds",array("list_width"=>"200","search"=>false));
$list->addColumn("TijdelijkeBulkOrders","ISINCode",array("list_width"=>"200","search"=>false));
$list->addColumn("TijdelijkeBulkOrders","koersLimiet",array("list_width"=>"100","search"=>false));
$list->addColumn("","bedrag",array("description"=>'Bedrag',"list_width"=>"100","search"=>false,'list_align'=>'right'));

$list->setWhere("add_user='$USR' $extraWhere AND pagina='".$selectedPagina."'");
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
if($_GET['sort']=='')
{
  $_GET['sort'][]='add_date';
  $_GET['direction'][] = "ASC";
}

$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);
?>

<form method="POST" name="selectForm">
<input type="hidden" name="verwerk" value="1">
<table class="list_tabel" cellspacing="0">

<?=$list->printHeader();?>
<?php
$n=1;
while($data = $list->getRow())
{
  $fonds=$data['fonds']['value'];
  
  if(!isset($fondsen[$fonds]))
  {
    $query="SELECT Fondseenheid,Valuta FROM Fondsen WHERE Fonds='".$fonds."'";
    $DB->SQL($query);
    $fondsData=$DB->lookupRecord();
    $fondsen[$fonds]['Fondseenheid']=$fondsData['Fondseenheid'];
    $fondsen[$fonds]['Valuta']=$fondsData['Valuta'];
  }
  
  if(!isset($fondskoersen[$fonds]))
  {   
    $queryf = "SELECT koers FROM Fondskoersen WHERE Fonds = '".$fonds."' ORDER BY Datum DESC LIMIT 1";
    $DB->SQL($queryf);
    $fondsKoers = $DB->lookupRecord();
    $fondskoersen[$fonds]=$fondsKoers['koers'];
  }
    
  if(!isset($valutakoersen[$fondsen[$fonds]['Valuta']]))
  {   
    $queryf = "SELECT koers FROM Valutakoersen WHERE Valuta = '".$fondsen[$fonds]['Valuta']."' ORDER BY Datum DESC LIMIT 1";
    $DB->SQL($queryf);
    $valutaKoers = $DB->lookupRecord();
    $valutakoersen[$fondsen[$fonds]['Valuta']]=$valutaKoers['koers'];
  }

  $brutoBedragValuta=abs($data['aantal']['value'])*$fondsen[$fonds]['Fondseenheid']*$fondskoersen[$fonds];
  $brutoBedrag=$brutoBedragValuta*$valutakoersen[$fondsen[$fonds]['Valuta']];
  
  $data['bedrag']['value']=number_format($brutoBedrag,2,",",'.');  
  
  // $data['t']['value']=$n;   
	echo $list->buildRow($data);
  $n++;
}
?>
</table>
</form>

<?

echo "<div class=\"buttonDiv\" style=\"width:150px;float:left;\" ><a href='tijdelijkebulkordersList.php?checkOrders=1'>&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> Verwerken</a></div>";

//echo "<a href='tijdelijkebulkordersList.php?maakOrders=1&pagina=$selectedPagina&user=$selectedUser'>Verwerken</a>";
logAccess();
if($__debug)
{
	echo getdebuginfo();
}


  
echo template($__appvar["templateRefreshFooter"],$content);
?>