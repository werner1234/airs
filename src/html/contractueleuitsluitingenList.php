<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 16 augustus 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/03/11 10:52:00 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: contractueleuitsluitingenList.php,v $
    Revision 1.4  2018/03/11 10:52:00  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "contractueleuitsluitingenEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->idTable = "contractueleUitsluitingen";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];
$list->categorieVolgorde=array('ContractueleUitsluitingen'=>array('Algemeen'),'Portefeuilles'=>array('Gegevens','Beheerfee','Staffels','Recordinfo'));

$list->addFixedField("ContractueleUitsluitingen","vanaf",array("list_width"=>"100","search"=>false));
$list->addFixedField("ContractueleUitsluitingen","einddatum",array("list_width"=>"100","search"=>false));
$list->addFixedField("ContractueleUitsluitingen","vermogensbeheerder",array("list_width"=>"100","search"=>false));
$list->addFixedField("ContractueleUitsluitingen","portefeuille",array("list_width"=>"100","search"=>false));
$list->addFixedField("ContractueleUitsluitingen","fonds",array("list_width"=>"100","search"=>false));
$list->addFixedField("ContractueleUitsluitingen","categoriesoort",array("list_width"=>"100","search"=>false));
$list->addFixedField("ContractueleUitsluitingen","categorie",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields(array('ContractueleUitsluitingen','Portefeuilles'),'ContUitslList');
$list->ownTables=array('contractueleUitsluitingen');

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");

$vermogensbeheerderJoin='';
$gebruikerWhere='';
foreach ($list->columns as $colData)
{

	if ($colData['objectname'] == 'Portefeuilles')
	{
		if (checkAccess('portefeuille'))
		{
			// superusers appvar
			$allow_add = true;
		}
		else
		{
			// normale user
			$allow_add = false;
			if (checkAccess())// superusers
			{
				$allow_add = true;
			}

			if ($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
			{
				$beperktToegankelijk = " (Portefeuilles.Accountmanager='" . $_SESSION['usersession']['gebruiker']['Accountmanager'] . "' OR Portefeuilles.tweedeAanspreekpunt ='" . $_SESSION['usersession']['gebruiker']['Accountmanager'] . "') AND Portefeuilles.consolidatie=0 ";
			}
			else
			{
				$vermogensbeheerderJoin="LEFT JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '" . $USR . "'
							    LEFT JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
				$beperktToegankelijk = " (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) AND Portefeuilles.consolidatie=0 ";
				$gebruikerWhere='AND (VermogensbeheerdersPerGebruiker.id IS NOT NULL OR contractueleUitsluitingen.portefeuille=\'\')';
			}

		}
	}
}

$portefeuilleJoin="LEFT JOIN Portefeuilles ON contractueleUitsluitingen.Portefeuille=Portefeuilles.Portefeuille";
$list->setJoin("$portefeuilleJoin $vermogensbeheerderJoin");

if($_GET['portefeuille']<>'')
	$uitsluitingenWhere=" AND contractueleUitsluitingen.Portefeuille='".$_GET['portefeuille']."'";
else
	$uitsluitingenWhere='';

if($gebruikerWhere<>'' || $uitsluitingenWhere <> '')
	$list->setWhere('1 '.$gebruikerWhere.$uitsluitingenWhere);
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content['javascript'] .= "

function addRecordFrame() 
{
	location =  '".$editScript."?action=new&frame=1&portefeuille=".$_GET['portefeuille']."';
}

function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
function editRecord(url) 
{
	location = url;
}
";

echo template($__appvar["templateContentHeader"],$content);
if($_GET['frame']==1 && $_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0)
{
  echo '<a href="#" onclick="addRecordFrame()"><span title="record toevoegen"><img src="icon/16/add.png" class="simbisIcon"> '.vt("toevoegen").'</span> </a><br><br>';
}

?>
	<br>
<?=$list->filterHeader();?>
	<table class="list_tabel" cellspacing="0">
		<?=$list->printHeader();?>
		<?php
		$list->customEdit =true;
		while($data = $list->getRow())
		{
			$data['extraqs']='frame='.$_GET['frame'];
	echo $list->buildRow($data);
}
?>
</table>
<?
logAccess();
if($__debug) 
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
