<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 16 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2013/01/30 16:57:27 $
    File Versie         : $Revision: 1.1 $

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader    = " ";
$mainHeader   = vt("gespreksverslagen Kopieeren van");

//Edit content kopieren voor juiste stylesheets
$content = $editcontent;

$editScript = "CRM_naw_dossierEdit.php";
$allow_add  = false;

if($_POST['verwerk']=='1' && $_POST['doelRelatie'] > 0)
{
  foreach ($_POST as $key=>$value)
  {
    if(substr($key,0,3)=='id_')
    {
      $ids[]=substr($key,3);
    }
  }
  if(count($ids) > 0)
  {
    $db2=new DB();

    $query="UPDATE CRM_naw_dossier SET rel_id='".$_POST['doelRelatie']."', memo=CONCAT(memo,'\nVerplaatst vanaf relatieId (".mysql_real_escape_string($_POST['relId']).") ','".date("d-m-Y H:m:s")."'), change_date=NOW() WHERE id IN('".implode("','",$ids)."')";
    $db2->SQL($query);
    if($db2->Query())
      $records=count($ids);
  }
  $mainHeader="(".$records.") " . vt('record(s) verplaatst.') . "";
  $content['pageHeader'] = "<br><div class='edit_actionTxt'><b>$mainHeader</b> $subHeader</div><br><br>";
  echo template($__appvar["templateContentHeader"],$content);
  echo template($__appvar["templateRefreshFooter"],$content);
  exit;
}
else
{


$rel_id = $_POST['bronRelatie'];

if($rel_id < 1)
{
  $relatieSelectie="bronRelatie";
  $typeSelectie=vt("Bron");
  $knopTekst=vt("Gespreksverslagen ophalen");
  $selectAll='';
  $hiddenRelId='';
}
else
{  
  $NAW = new db();
  $q = "SELECT * FROM CRM_naw WHERE id = '$rel_id'";
  $NAW->SQL($q);
  $nawRec = $NAW->lookupRecord();
  $subHeader .= "van <b>".$nawRec['naam'].", ".$nawRec['a_plaats']."</b>";
  
  $relatieSelectie='doelRelatie';
  $typeSelectie=vt("Doel");
  $knopTekst=vt("Gespreksverslagen verplaatsen");
  $selectAll='<div> <a href="javascript:checkAll();"> check</a>  / <a href="javascript:uncheckAll();"> uncheck</a>  </div>';
  $hiddenRelId='<input type="hidden" name="relId" value="'.$rel_id.'">';
}

  $autocomplete = new Autocomplete();
  $autocomplete->minLeng = 2;
  $autocomplete->resetVirtualField('PortefeuilleSelectie');
  $autocomplete->addVirtuelField(
    'PortefeuilleSelectie',
    array(
      'autocomplete' => array(
        'table' => 'CRM_naw',
        'prefix' => true,
        'returnType' => 'expanded',
        'join' => array(
          'Portefeuilles' => array(
            'type' => 'left',
            'on' => array(
              'CRM_naw.Portefeuille' => 'Portefeuille'
            )
          )
        ),
        'extra_fields' => array(
          'Portefeuille',
          'Client',
          'id',
        ),
        'label'        => array('CRM_naw.naam', 'Portefeuilles.Portefeuille', 'CRM_naw.zoekveld' ),
        'searchable'   => array('CRM_naw.naam', 'Portefeuilles.Portefeuille', 'CRM_naw.zoekveld'),
        'extra_fields' => array('CRM_naw.id'),
        'field_value'  => array('CRM_naw.naam'),
        'value'        => 'CRM_naw.naam',
        'actions'      => array(
          'select' => '
            console.log(ui.item.data.CRM_naw);
            $("#' . $relatieSelectie . '").val(ui.item.data.CRM_naw.id);
          '
        )//,
//        'conditions'   => array(
//          'AND' => array(
//            '(Portefeuilles.EindDatum >= now() OR Portefeuilles.EindDatum = "0000-00-00")',
//          )
//        )
      ),
      'form_size'    => '15',
      'form_value'   => '',
    )
  );

  $content['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('PortefeuilleSelectie');









$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("Naw_dossier","id",array("list_width"=>"100","search"=>false));
$list->addColumn("","checkbox",array('description'=>'copy',"list_width"=>"30","search"=>false));
$list->addColumn("Naw_dossier","datum",array("list_width"=>"100","search"=>false,"list_align"=>"left","form_type"=>"calendar"));
$list->addColumn("Naw_dossier","kop",array("list_width"=>"","search"=>false));
$list->addColumn("Naw_dossier","add_user",array("description"=>"toegevoegd door","list_width"=>"","search"=>false));





$mainHeader   = vt("gespreksverslagen verplaatsen");
$list->setWhere("rel_id = '".$rel_id."'");



// default sortering
$_GET['sort'] = array("CRM_naw_dossier.datum","CRM_naw_dossier.id");
$_GET['direction'] = array("DESC","DESC");
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));

//$content['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/popup.js\" type=text/javascript></script>";

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content['javascript'] .= "
function uncheckAll()
{
  var theForm = document.editForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox')
   {
    theForm[z].checked = false;
   }
  }
}
function checkAll()
{
  var theForm = document.editForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox')
   {
    theForm[z].checked = true;
   }
  }
}
";




//$koppelObject[1] = new Koppel("CRM_naw","editForm");//LEFT JOIN Portefeuilles on CRM_naw.portefeuille=Portefeuilles.Portefeuille $join
//$koppelObject[1]->addFields("id",$relatieSelectie,false,true);
//$koppelObject[1]->addFields("naam","CRMnaam",true,true);
//$koppelObject[1]->addFields("portefeuille","",true,true);
//$koppelObject[1]->addFields("zoekveld","",true,true);
//$koppelObject[1]->name = "port";
////$koppelObject[1]->extraQuery = " AND Portefeuilles.einddatum > NOW() $beperktToegankelijk";
//$koppelObject[1]->action = "";
//$koppelObject[1]->focus = "";
//$content['javascript'] .= "\n".$koppelObject[1]->getJavascript();



echo template($__appvar["templateContentHeader"],$content);
?>
<form method="POST" name="editForm">
<input type="hidden" name="verwerk" value="1">
<?=$hiddenRelId?>
<table class="list_tabel" cellspacing="0">
<?
if($rel_id > 0)
{
  echo $list->printHeader(true);
  while($data = $list->getRow())
  {
  	$data['disableEdit']=true;
  	$data['checkbox']['value']="<input type=\"checkbox\" name=\"id_".$data['id']['value']."\" value=\"1\">";
  	echo $list->buildRow($data);
  }
}
?>
</table>
<br><br>
<?=$typeSelectie?>:




<!--<a href="javascript:select_port('')"> <img src="images/16/lookup.gif" border="0" height="18" align="middle"></a>-->
<input  class="" type="hidden"  value="" name="<?=$relatieSelectie?>" id="<?=$relatieSelectie?>" >

<!--<input  class="" type="text"  size="60" value="" name="CRMnaam" id="CRMnaam" >-->

    <input  class="" type="text"  size="60" value="" name="PortefeuilleSelectie" id="PortefeuilleSelectie" >

<br><br>
<?=$selectAll?>
<br><br>
<div class="formlinks"> <input type="submit" value="<?=$knopTekst?>" > </div>
<?=$knoppen?>
</form>
<?
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);

}
?>