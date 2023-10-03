<?php
/*
    AE-ICT sourcemodule created 25 sep. 2020
    Author              : Chris van Santen
    Filename            : rekeningafschriften_v2_List.php


*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");


$data = array_merge($_POST, $_GET);
$memoriaal = ( isset ($data['memoriaal']) ? $data['memoriaal'] : 0);
$list = new MysqlList2();


$table = 'Rekeningafschriften';
$editScript = "rekeningafschriften_v2_Edit.php";
$list->setFullEditScript('rekeningmutaties_v2_Edit.php?action=edit&afschrift_id={id}');


$content['javascript'] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new&memoriaal=".$memoriaal."';
}

function checkAll(optie)
{
  var theForm = document.listForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,12) == 'afschriftId_') 
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

function countCheck()
{
  var counter=0;
  var theForm = document.listForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
    if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,12) == 'afschriftId_') 
    {
      if(theForm[z].checked == true)
        counter++;
    }
  }
  return counter;
}


function verzenden()
{
  var numberSelected=countCheck();
  if(numberSelected > 0)
  {
    window.open('', 'formpopup', 'width=400,height=400,resizeable,scrollbars');
    document.listForm.target = 'formpopup';
    document.listForm.submit();
  }
  else
  {
    alert('".vt("Geen afschriften geselecteerd").".');
  }
}

";

if( ($_SESSION['usersession']['gebruiker']['mutatiesAanleveren'] > 0 || checkAccess() ) && ( isset ($data['type']) && $data['type'] == 'temp') ) {
  $table = 'VoorlopigeRekeningafschriften';
  $editScript = "rekeningafschriften_v2_Edit.php";
  $list->setFullEditScript('rekeningmutaties_v2_Edit.php?action=edit&type=temp&afschrift_id={id}');
  $content['javascript'] .= "
    function addRecord() {
      parent.frames['content'].location = '".$editScript."?action=new&type=temp&memoriaal=".$memoriaal."';
    }
";
}



//debug($table->data['table']);

$list->idField = "id";
$list->idTable = $table;
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addFixedField($table,"id",array("width"=>100,"search"=>false));
$list->addFixedField($table,"Rekening",array("search"=>true));
$list->addFixedField("Portefeuilles","Client",array("width"=>100,"search"=>true));
$list->addFixedField($table,"Afschriftnummer",array("width"=>120,"search"=>true));
$list->addFixedField($table,"Datum",array("width"=>100,"align"=>"right","search"=>false));
$list->addFixedField($table,"Saldo",array("width"=>100,"align"=>"right","search"=>false));
$list->addFixedField($table,"NieuwSaldo",array("width"=>100,"align"=>"right","search"=>false));
$list->addFixedField($table,"Verwerkt",array("align"=>"center","width"=>100,"search"=>false));
$list->addFixedField($table,"change_user",array("width"=>100,"align"=>"right","search"=>true));
$list->addFixedField("Rekeningen","Memoriaal",array("list_invisible"=>true));


$list->categorieVolgorde=array('Portefeuilles'=>array('Gegevens','Beheerfee','Staffels'),$table=>array('Algemeen'),
                               'Rekeningen'=>array('Algemeen'));

$list->ownTables=array($table);

$html = $list->getCustomFields(array($table,'Portefeuilles','Rekeningen'));
$_SESSION['submenu'] = New Submenu();

if(checkAccess())
{
  $_SESSION['submenu']->addItem(vt("Verwerk selectie"), 'javascript:parent.frames[\'content\'].verzenden(\'\');');
}
$_SESSION['submenu']->addItem("<br>","");
$_SESSION['submenu']->addItem($html,"");

$allow_add = checkAccess();

//if(count($_GET) == 2 && $_GET['status']=='verwerkt')
//  $jaarFilter=" AND YEAR($table.Datum)='".date('Y')."' " ;

if(empty($_GET['sort']))
{
	$_GET['sort'] = array("$table.Datum","$table.Rekening");
	$_GET['direction'] = array("DESC","ASC");
}

if($memoriaal)
	$memSelect = "1";
else
	$memSelect = "0";

$type='portefeuille';
if(!checkAccess($type))
{
  if($_SESSION['usersession']['gebruiker']['internePortefeuilles'] == '1')
  {
    $internDepotToegang = "OR Portefeuilles.interndepot=1";
  }

  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
	{
	    $list->setJoin("JOIN  Rekeningen ON $table.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0
        JOIN  Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0 ");
	   $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang ) ";
	}
	else
	{
  $list->setJoin("
        JOIN  Rekeningen ON $table.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0
        JOIN  Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0
        JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
 				JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ");
  $beperktToegankelijk = " AND  (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
	 }
}
else
{
  $list->setJoin("JOIN  Rekeningen ON $table.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0
  JOIN  Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0 ");
}


if($status == "verwerkt")
	$list->setWhere("Portefeuilles.Portefeuille = Rekeningen.Portefeuille AND $table.verwerkt = '1' AND $table.Rekening = Rekeningen.Rekening $beperktToegankelijk $jaarFilter");
else
	$list->setWhere("Portefeuilles.Portefeuille = Rekeningen.Portefeuille AND $table.verwerkt = '0' AND $table.Rekening = Rekeningen.Rekening $beperktToegankelijk $jaarFilter");

$__appvar['rowsPerPage'] = 100;
$list->perPage = $__appvar['rowsPerPage'];
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
session_write_close();


echo template($__appvar["templateContentHeader"],$content);
?>
<br>
<form action="rekeningafschriften_v2_List.php" method="GET"  name="controleForm">
<input type="hidden" name="memoriaal" value="<?=$memoriaal?>">
  <?=vt("Overzicht")?> :
<select name="status" onChange="document.controleForm.submit()">
<option value="" <?=($status=="verwerkt")?"":"selected"?>><?=vt("Niet verwerkt")?></option>
<option value="verwerkt" <?=($status=="verwerkt")?"selected":""?>><?=vt("Verwerkt")?></option>
</select>
<input type="submit" value="<?=vt("Overzicht")?>">
</form>
<br>
<?
echo $list->filterHeader();


echo "
	<div id=\"wrapper\" style=\"overflow:hidden;\">
		<div class=\"buttonDiv\" style=\"width:150px;float:left;\" onclick=\"checkAll(1);\">&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> ".vt("Alles selecteren")."</div>
		<div class=\"buttonDiv\" style=\"width:150px;float:left;\" onclick=\"checkAll(0);\">&nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon' /> ".vt("Niets selecteren")."</div>
		<div class=\"buttonDiv\" style=\"width:160px;float:left;\" onclick=\"checkAll(-1);\">&nbsp;&nbsp;<img src='icon/16/replace2.png' class='simbisIcon' /> ".vt("Selectie omkeren")."</div>
	</div>
	
	<br>
	";
?>

<form name="listForm" method="POST" action="transactiesVerwerken.php">
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
 
$DB=new DB();

while($data = $list->getRow())
{

  // check of totaal gelijk is aan mutatiebedrag!
  $mutatieBedrag = round(($data["$table.NieuwSaldo"]["value"] - $data["$table.Saldo"]["value"]),2);
  // Haal totaal mutaties op

  $DB->SQL("SELECT SUM(Bedrag) AS Totaal FROM Rekeningmutaties WHERE Afschriftnummer = '".$data["$table.Afschriftnummer"]["value"]."' AND Rekening = '".$data["$table.Rekening"]["value"]."'");
  $DB->Query();
  $totaal = $DB->NextRecord();
  // Reken mutatieverschil uit
  $mutatieVerschil = $mutatieBedrag - round($totaal['Totaal'],2);
  // Zet Fieldset Class voor mutatie veschil
  if($mutatieVerschil  <> 0)
  {
    $class  = "list_rekeningmutatie_verschil";
  }
  else
  {
    $class  = "list_dataregel";
  }
   $data['tr_class']=$class;


    $checkbox='<input type="checkbox" name="afschriftId_'.$data['id']['value'].'" value="1"  >';//checked
    $list->editIconExtra=$checkbox;

   echo $list->buildRow($data);



  //sprintf($row[list_format], $printdata)
  /*
  echo '<tr class="'.$class.'" onmouseover="this.className=\'list_dataregel_hover\'" onmouseout="this.className=\''.$class.'\'">
  <td class="list_button"><div class="icon"><a href="rekeningmutaties_v2_Edit.php?action=new&afschrift_id='.$data['id']['value'].'"><img src="images//16/muteer.gif" width="16" height="16" border="0" alt="" align="absmiddle">&nbsp;</a></div></td>
  <td class="listTableData" width="130" >'.$data['Client']['value'].'&nbsp;</td>
  <td class="listTableData" width="130" >'.$data['Rekening']['value'].' &nbsp;</td>
  <td class="listTableData" width="130" >'.$data['Afschriftnummer']['value'].'&nbsp;</td>
  <td class="listTableData" width="100" align="right">'.jul2form(db2jul($data['Datum']['value'])).'&nbsp;</td>
  <td class="listTableData" width="100" align="right">'.sprintf($data['Saldo']['list_format'],$data['Saldo']['value']).'&nbsp;</td>
  <td class="listTableData" width="100" align="right">'.sprintf($data['NieuwSaldo']['list_format'],$data['NieuwSaldo']['value']).'&nbsp;</td>
  <td class="listTableData" width="100" align="center">'.checkboximage($data['Verwerkt']['value']).'&nbsp;</td>
  <td class="listTableData" width="130" align="center">'.$data['change_user']['value'].'&nbsp;</td>
  </tr>';
  */
}
?>
</table>
</form>
<?
logAccess();
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
