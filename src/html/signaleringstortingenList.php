<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 25 november 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/03/02 13:27:23 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: signaleringstortingenList.php,v $
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "signaleringstortingenEdit.php";
$allow_add  = false;

if(isset($_POST))
{
	$ids=array();
	foreach($_POST as $key=>$value)
	{
		if(substr($key,0,6)=='check_')
		{
			$ids[]=substr($key,6);
		}
	}

	$db=new DB();

  if($_POST['actie']=='verwijderen')
	{
		$query="UPDATE signaleringStortingen set status=2,change_date=now(),change_user='$USR' WHERE signaleringStortingen.id IN('" . implode("','", $ids) . "') AND status=0";
		$db->SQL($query);
		$db->Query();
	}
	elseif($_POST['actie']=='bevestigen')
	{
		$query="UPDATE signaleringStortingen set status=1,change_date=now(),change_user='$USR' WHERE signaleringStortingen.id IN('" . implode("','", $ids) . "') AND status=0";
		$db->SQL($query);
		$db->Query();
	}
}


$list = new MysqlList2();
$list->idField = "id";
$list->idTable='signaleringStortingen';
$list->editScript = $editScript;
$__appvar['rowsPerPage']=1000;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("signaleringStortingen","id",array("list_width"=>"100","search"=>false));
$list->addFixedField("signaleringStortingen","portefeuille",array("list_width"=>"100","search"=>false));
$list->addFixedField("signaleringStortingen","bedrag",array("list_width"=>"100","search"=>false));
$list->addFixedField("signaleringStortingen","datum",array("list_width"=>"100","search"=>false));
$list->addFixedField("signaleringStortingen","status",array("list_width"=>"100","search"=>false));
$list->addFixedField("signaleringStortingen","add_date",array("list_width"=>"100","search"=>false));
$list->addFixedField("signaleringStortingen","passend",array("list_width"=>"50","list_align"=>"center", "search"=>false));

$list->addFixedField("signaleringStortingen","opmerking",array("list_width"=>"100","search"=>false));


$list->categorieVolgorde=array('signaleringStortingen'=>array('Algemeen'),
															 'Portefeuilles'=>array('Gegevens','Beheerfee','Staffels'),
	                             'laatstePortefeuilleWaarde'=>array('Algemeen'),
                               'Rekeningmutaties'=>array('Algemeen'),
															 'NAW'=>array("Algemeen","Adres","Verzendadres","Telefoon","Internetgegevens","Bedrijfinfo","Persoonsinfo","Legitimatie","Informatie partner","Legitimatie partner","Adviseurs","geen",'Contract','Beleggen','Rapportage','Profiel','Relatie geschenk','Recordinfo'));

$html = $list->getCustomFields(array('signaleringStortingen','Portefeuilles','Rekeningmutaties','laatstePortefeuilleWaarde','NAW'),'signaleringStortingenList');

$status=intval($_GET['status']);
$_SESSION['submenu'] = New Submenu();
$statusOpties=array(0=>vt('Nieuw'),1=>vt('Bevestigd'),2=>vt('Verwijderd'),'-1'=>vt('Alle'));
$_SESSION['submenu']->addItem('Status:','');
foreach($statusOpties as $key=>$value)
  $_SESSION['submenu']->addItem($value,'signaleringstortingenList.php?status='.$key);

if($status==0 && GetCRMAccess(2))
{
	$_SESSION['submenu']->addItem("<br>", '');
	$_SESSION['submenu']->addItem(vt('verwerk selectie:'), '');
	$_SESSION['submenu']->addItem(vt("Bevestigen"), 'javascript:parent.frames[\'content\'].verwerkSelectie(\'bevestigen\');');
  $_SESSION['submenu']->addItem(vt("Verwijderen"), 'javascript:parent.frames[\'content\'].verwerkSelectie(\'verwijderen\');');
}
$_SESSION['submenu']->addItem($html,"");

if(!checkAccess('portefeuille'))
{
  //$_SESSION['usersession']['gebruiker']['Accountmanager']='ALGDOO1';
  //$_SESSION['usersession']['gebruiker']['overigePortefeuilles']=0;
  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
  {
    $rechtenJoin=" JOIN Portefeuilles ON signaleringStortingen.Portefeuille=Portefeuilles.Portefeuille ";
    $beperktToegankelijk = "OR ((Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."') AND Portefeuilles.consolidatie<2) ";
    
  }
  else
  {
    $rechtenJoin=" LEFT JOIN Portefeuilles ON signaleringStortingen.Portefeuille=Portefeuilles.Portefeuille ";
    $rechtenJoin.=" LEFT JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
							     LEFT JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker";
    $beperktToegankelijk = "OR ( (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) AND Portefeuilles.consolidatie<2 )";
  }
  
  $joinPortefeuilles="$rechtenJoin
  LEFT JOIN laatstePortefeuilleWaarde ON signaleringStortingen.Portefeuille = laatstePortefeuilleWaarde.Portefeuille ";
  
  $rechtenWhere="AND ( Portefeuilles.id is NULL $beperktToegankelijk )";
}
else
{
  $joinPortefeuilles = "LEFT JOIN Portefeuilles ON signaleringStortingen.Portefeuille = Portefeuilles.Portefeuille
  LEFT JOIN laatstePortefeuilleWaarde ON signaleringStortingen.Portefeuille = laatstePortefeuilleWaarde.Portefeuille
  LEFT Join VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR'  ";
}


$joinNaw = "LEFT JOIN CRM_naw ON signaleringStortingen.Portefeuille = CRM_naw.portefeuille ";

$joinRekmut = "LEFT JOIN Rekeningmutaties ON Rekeningmutaties.id = signaleringStortingen.rekeningmutatieId ";

$list->ownTables=array('signaleringStortingen');
$list->setJoin("$joinPortefeuilles $joinNaw $joinRekmut");
if($status>=0)
{
  $list->setWhere("status='" . $status . "' $rechtenWhere");
}
else
{
  $list->setWhere("1 $rechtenWhere");
}
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

if($aantalMails>0)
	$mailMessage="<br>\n $aantalMails " . vt('mail(s) aangemaakt in de eMail queue.') . "";
else
	$mailMessage='';

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader $mailMessage
</div><br>

<div id=\"wrapper\" style=\"overflow:hidden;\"> 
<div class=\"buttonDiv\" style=\"width:150px;float:left;\" onclick=\"checkAll(1);\">&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> " . vt('Alles selecteren') . "</div>
<div class=\"buttonDiv\" style=\"width:150px;float:left;\" onclick=\"checkAll(0);\">&nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon' /> " . vt('Niets selecteren') . "</div>
<div class=\"buttonDiv\" style=\"width:160px;float:left;\" onclick=\"checkAll(-1);\">&nbsp;&nbsp;<img src='icon/16/replace2.png' class='simbisIcon' /> " . vt('Selectie omkeren') . "</div>
</div>

<br>
";


$editcontent['javascript'] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}

function checkAll(optie)
{
  var theForm = document.listForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,6) == 'check_')
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
    if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,6) == 'check_')
    {
      if(theForm[z].checked == true)
        counter++;
    }
  }
  return counter;
}

function verwerkSelectie(formActie)
{
  var numberSelected=countCheck();
  if(numberSelected > 0)
  {
    var answer = confirm('" . vt('Wilt u') . " ' + numberSelected  + ' " . vt('records verwerken?') . "');
    if(answer)
    {
      document.listForm.actie.value=formActie;
      document.listForm.submit();
      //alert('test');
    }
  }
}



";
echo template($__appvar["templateContentHeader"],$editcontent);
?>
<style>
	.doubleBorder {
		border-color: black!important;
		border-bottom: 1px double;
	}
	.singleBorder {
		border-color: black!important;
		border-bottom: 1px solid;
	}
	.numberRight {
		text-align: right;
	}
	.list_button {
		width: 65px!important;
	}
</style>
<br>

	<?=$list->filterHeader();?>
	<table class="list_tabel" cellspacing="0">
		<?=$list->printHeader();?>

		<form name="listForm" method="POST">
		<input type='hidden' name='actie' value='' >
		<input type='hidden' name='idList' value='' >

<?php
while($data = $list->getRow())
{
  $id=$data['id']['value'];
  $list->editIconExtra="<input type='checkbox' name='check_".$id."' value='1'>";

	$data['signaleringStortingen.status']['value']=$data['signaleringStortingen.status']['form_options'][$data['signaleringStortingen.status']['value']];
	//listarray($data);
	echo $list->buildRow($data);
}
?>
		</form>
</table>


<script>
	$(function() {
		$('.dialogBox').dialog({
			autoOpen:false,
			width:800
		});

		$(".openDiag").click(function(e) {
			e.preventDefault();
			$('#' + $(this).attr('id') + '_box').dialog('open');
		});
	});
</script>

<?
logAccess();
if($__debug) 
{
	echo getdebuginfo();
}




echo template($__appvar["templateRefreshFooter"],$editScript);
?>
