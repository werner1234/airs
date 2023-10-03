<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "rentepercentageEdit.php";

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
if($_GET['frame'] == 1)
  $__appvar['rowsPerPage']=50;

$list->perPage = $__appvar['rowsPerPage'];

$extraOptions = array();
if (requestType('ajax')) { // if ajax zet zoeken en sorteren uit in de tabel
  $extraOptions = array('search' => false, 'list_order' => false);
}

$list->addField("Rentepercentage","id", $extraOptions + array("width"=>100,"search"=>false));
$list->addField("Rentepercentage","Fonds", $extraOptions + array("search"=>true));
$list->addField("Rentepercentage","Datum", $extraOptions + array("list_width"=>150,"search"=>false));
$list->addField("Rentepercentage","Rentepercentage", $extraOptions + array("list_width"=>150,"search"=>false));


// normale user
$allow_add = false;
if(checkAccess( isset($type) ? $type : null )) 
{
	// superusers
	$allow_add = true;
}



if( $_GET['frame'] == 1 ) {
  $mainHeader    = vt("Rente Percentages bij")." ".$_GET['Fonds'];
  $subHeader     = '<a href="#" onClick="javascript:addRecord();"><img src="images//16/record_new.gif" width="16" height="16" border="0" alt="record toevoegen" align="absmiddle">&nbsp;'.vt("toevoegen").'</a>';

  
  if( requestType('ajax') ) {
      /** selecteer ajax templates **/
      $__appvar['templateContentHeader'] = 'templates/ajax_head.inc';
      $__appvar['templateRefreshFooter'] = 'templates/ajax_voet.inc';

      $subHeader     = '<a href="#"  data-href="' . $editScript . '?action=new&frame=1&Fonds=' .urlencode($_GET['Fonds']) . '" id="addRentePercentage"><img src="images//16/record_new.gif" width="16" height="16" border="0" alt="record toevoegen" align="absmiddle">&nbsp;'.vt("toevoegen").'</a>';

      $content['inlineStyle'] = "
        #renteList table{
            width: 100%;
            margin: 0px;
        }
        .edit_actionTxt a {
          float: right;
        }

      ";

      $content['script_voet'] = "
        $('#addRentePercentage').on('click', function (event) {
          event.preventDefault();
          $('#modelContent').load(encodeURI($(this).data('href')));
        });

        $('#renteList .list_button  a').on('click', function (event) {
          event.preventDefault();
          $('#modelContent').load(encodeURI($(this).attr('href')));
        });

      ";
  }


  $content['pageHeader'] = "<div class='edit_actionTxt'> <b>$mainHeader</b> $subHeader</div><br>";
  $content['javascript'] .= "
function addRecord() 
{
	location = '".$editScript."?action=new&frame=1&Fonds=".$_GET['Fonds']."';
}
";

  echo template($__appvar["templateContentHeader"],$content);
  if(!empty($_GET['Fonds']))
  {
	  $list->setWhere(" Fonds = '".$_GET['Fonds']."' ");
  }

if( ! isset($_GET['sort']) )
{
  $_GET['sort'][]='Datum';
  $_GET['direction'][]='DESC';
}  
  // set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch( isset($_GET['selectie']) ? $_GET['selectie'] : null );
// select page
$list->selectPage( isset($_GET['page']) ? $_GET['page'] : null );
  ?><table class="list_tabel" cellspacing="0"><?
  echo  $list->printHeader();
  while($data = $list->printRow())
  {
  
    $data=str_replace("rentepercentageEdit.php?action=edit","rentepercentageEdit.php?frame=1&action=edit",$data);
	  echo $data;
  }
  ?></table><?

  echo template($__appvar["templateRefreshFooter"],$content);
  
}
else 
{
  
if(!empty($Fonds))
{
	$list->setWhere(" Fonds = '".$Fonds."' ");
}
  
$DB = new DB();
$DB->SQL("SELECT Fonds FROM Fondsen ORDER BY Fonds ASC");
$DB->Query();
while($data = $DB->NextRecord())
{
	$options .= "<option value=\"".$data['Fonds']."\" ".($Fonds==$data['Fonds']?"selected":"").">".$data['Fonds']."</option>\n";
}

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

$content['javascript'] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new&Fonds=".$_GET['Fonds']."';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>
<br>
<form action="rentepercentageList.php" method="GET"  name="controleForm">
  <?=vt("Fonds")?> :
<select name="Fonds" onChange="document.controleForm.submit();">
<option value="">--</option>
<?=$options?>
</select>
<input type="submit" value="<?=vt("Overzicht")?>">
</form>
<br>
<br>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->printRow())
{
	echo $data;
}
?>
</table>
<?
logAccess();
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);

}
