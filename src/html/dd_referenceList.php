<?php
/*
    AE-ICT CODEX source module versie 1.6, 14 november 2009
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/08/10 17:26:01 $
    File Versie         : $Revision: 1.23 $

    $Log: dd_referenceList.php,v $
    Revision 1.23  2019/08/10 17:26:01  rvv

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("Documenten");

$editScript = "dd_referenceEdit.php";
$editScriptMulti = "dd_referenceEditMulti.php"; //call 8862
$_SESSION["dd_referenceList"]["url"] = $_SERVER["REQUEST_URI"];

$allow_add  = true;
$__appvar['rowsPerPage'] = 100;
$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$id = $_GET['id'];

$list->addColumn("Dd_reference","id",array("list_width"=>"100","search"=>false));
$list->addColumn("","toon",array("list_width"=>"100","search"=>false));
$list->addColumn("Dd_reference","add_date",array("description"=>'Datum',"list_width"=>"100","search"=>false));


$list->addColumn("Dd_reference","description",array("list_width"=>"400","search"=>true));
//$list->addColumn("Dd_reference","keywords",array("list_width"=>"200","search"=>true));
$list->addColumn("Dd_reference","categorie",array("list_width"=>"200","search"=>true));
$list->addColumn("Dd_reference","filename",array("list_width"=>"200","search"=>false));
$list->addColumn("Dd_reference","filesize",array("list_width"=>"100","search"=>false));
$list->addColumn("","editType",array("list_width"=>"30","list_invisible"=>true));
$list->addColumn("Dd_reference","dd_id",array("list_width"=>"100","list_invisible"=>true));
$list->addColumn("Dd_reference","datastore",array("list_width"=>"100","list_invisible"=>true));
$list->addColumn("Dd_reference","filetype",array("list_width"=>"100","list_invisible"=>true,'form_type'=>'checkbox'));
if($_DB_resources[DBportaal])
{
  $list->addColumn("Dd_reference","portaalKoppelId",array('description'=>'portaal',"list_width"=>"100"));
  $dbPortaal=new DB(DBportaal);
}

$idDdb=$id;
$idCRM=$id;
if($_GET['categorie'] != '')
{
  if($_GET['categorie']=='Documenten')
  {
    $idCRM=-1;
    $categorieFilter="AND (dd_reference.categorie = '' OR dd_reference.categorie = 'Documenten')";
  }
  elseif($_GET['categorie']=='Gespreksverslagen')
  {
   // $idDdb=-1;
    $categorieFilter="AND dd_reference.categorie = '".$_GET['categorie']."'";
  }
  else
  {
    $idCRM=-1;
    $categorieFilter="AND dd_reference.categorie = '".$_GET['categorie']."'";
  }
}

if( !isset($_GET['gespreksverslagen']) || $_GET['gespreksverslagen']==1)
{

  $list->forceSelect =
    "
SELECT * FROM (
(SELECT
    dd_reference.id,
    'dd' as editType,
    dd_reference.add_date ,
    dd_reference.filename,
    dd_reference.filesize,
    dd_reference.filetype,
    dd_reference.description,
    dd_reference.keywords,
    dd_reference.dd_id,
    dd_reference.datastore,
    dd_reference.categorie
  FROM
  (dd_reference)
  WHERE
   module_id = '$idDdb' AND module='CRM_naw' $categorieFilter)
UNION
  (SELECT
    CRM_naw_dossier.id,
    'Gespreksverslagen',
    CRM_naw_dossier.datum,
    null,
    null,
    null,
    CRM_naw_dossier.kop,
    null,
    null,
    null,
    null
  FROM
  CRM_naw_dossier
  WHERE
  CRM_naw_dossier.rel_id='$idCRM')
) as tmp
";
}
else
{
  $list->forceSelect =
    "SELECT * FROM (SELECT
    dd_reference.id,
    'dd' as editType,
    dd_reference.add_date ,
    dd_reference.filename,
    dd_reference.filesize,
    dd_reference.description,
    dd_reference.keywords,
    dd_reference.dd_id,
    dd_reference.datastore,
    dd_reference.categorie
  FROM
  (dd_reference)
  WHERE
   module_id = '$idDdb' AND module='CRM_naw' $categorieFilter) as tmp
";
}

$list->forceFrom="";
$list->noTables=true;


if(!isset($_GET['sort']))
{
  $_GET['sort'][]='add_date';
  $_GET['direction'][]='desc';
}


if ($id > 0)
{
  $NAW = new db();
  $q = "SELECT id,naam,portefeuille FROM CRM_naw WHERE id = $id";
  $NAW->SQL($q);
  $nawRec = $NAW->lookupRecord();
  $subHeader = " bij <b>".$nawRec['naam'].", ".$nawRec['portefeuille']."</b>";
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

if($_GET['action'] == 'xls')
{
  $list->setXLS();
  $list->getXLS();
}
else
{
  if(!is_a($_SESSION['submenu'],'Submenu'))
    $_SESSION['submenu']=new Submenu();
  $_SESSION['submenu']->addItem("XLS-lijst","$PHP_SELF?action=xls&".$_SERVER['QUERY_STRING']);
  $_SESSION['submenu']->addItem("<br>","");
  $_SESSION['submenu']->addItem("Kopieer documenten","CRM_naw_dossierCopy.php?tabel=dd_reference&relid=$id");
  $_SESSION['submenu']->addItem("<br>","");
  $_SESSION['submenu']->addItem("Als Zip downloaden","javascript:void(0)", array('onclick' => 'parent.frames[\'content\'].openDocumentSelect();'));

$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION["NAV"]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION["NAV"]->addItem(new NavSearch($_GET['selectie']));
$content = $editcontent;
$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content["javascript"] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScriptMulti."?action=new&rel_id=".$id."';
}
";
echo template($__appvar["templateContentHeader"],$content);

$query="SELECT CRM_selectievelden.omschrijving FROM (CRM_selectievelden) WHERE module = 'docCategrien' ORDER BY omschrijving ";

$DB = new DB();
$optieArray=array(''=>'Alles','Documenten'=>'Documenten','rapportage'=>'Rapportages');
if($DB->QRecords("SELECT CRM_naw_dossier.id FROM CRM_naw_dossier WHERE CRM_naw_dossier.rel_id='$id'"))
  $optieArray['Gespreksverslagen']='Gespreksverslagen';
$DB->SQL($query);
$DB->Query();

while($data = $DB->NextRecord())
{
	$optieArray[$data['omschrijving']]=$data['omschrijving'];
}

uksort($optieArray, 'strcasecmp');

foreach ($optieArray as $key=>$data)
  $options .= "<option value=\"".$key."\" ".($_GET['categorie']==$key?"selected":"").">".$data."</option>\n";
  
//  listarray($optieArray);
  if( !isset($_GET['gespreksverslagen']) || $_GET['gespreksverslagen']==1)
  {
    $gespreksverslagenCheckbox='checked';
  }
  else
  {
    $gespreksverslagenCheckbox='';
  }
?>


<form method="GET"  name="controleForm">
<input type="hidden" name="id" value="<?=$id?>">
<?= vt('Categorie'); ?> :
<select name="categorie" onChange="document.controleForm.submit();">
<?=$options?>
</select>
  <input name="gespreksverslagen" type="hidden" id="gespreksverslagen" value="0">
  <input name="gespreksverslagen" type="checkbox" id="gespreksverslagen" value="1" <?=$gespreksverslagenCheckbox?> onclick="document.controleForm.submit();"><?= vt('Incl. Gespreksverslagen'); ?>
<br>
<br>

<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
$db=new DB();
$zipList = '';
while($data = $list->getRow())
{
  if( strstr($data['filetype']['value'],'rtf') ) {
    $img = '<img alt="rtf" src="images/16/word.gif" width="16" height="16" border="0">';
  } elseif(strstr($data['filetype']['value'],'pdf')) {
    $img = '<img alt="pdf" src="images/pdf_20x20.gif" width="16" height="16" border="0">';
  } elseif(strstr($data['filetype']['value'],'msword')) {
    $img = '<img alt="pdf" src="images/16/icon-msoffice.png" width="16" height="16" border="0">';
  } else {
    $img = '<img alt="rtf" src="images/16/save.gif" width="16" height="16" border="0">';
  }

 $data['filename']['value']=str_replace("id".$nawRec['id']."_",'', $data['filename']['value']);
 $data['filename']['value']=str_replace($nawRec['portefeuille']."_",'', $data['filename']['value']);

  if($data['filesize']['value'] > 1048576)
   $data['filesize']['value'] = round($data['filesize']['value']/1048576,2)." Mb";
  elseif($data['filesize']['value'] > 1024)
   $data['filesize']['value'] = round($data['filesize']['value']/1024,2)." Kb";


	// $list->buildRow($data,$template="",$options="");
//listarray($data);
  $portaalVink = true;
	if($data['editType']['value']=='dd')
	{
	  $list->editScript='dd_referenceEdit.php';
	  $data['toon']['value']= ( $addCheckbox === true ? $toZipCheckbox : '' ) . "<a title=\"" . vt('Tonen') . "\" href=\"dd_push.php?show=1&datastore=".$data['datastore']['value']."&dd_id=".$data['dd_id']['value']."\"> $img </a>
	  <a title=\"" . vt('Mailen') . "\" href=\"CRM_mailer.php?docRefId=".$data['id']['value']."&id=$id\"> <img alt=\"email\" src=\"images/16/internetMail.gif\" width=\"16\" height=\"16\" border=\"0\"> </a>
	  ";
	}
	elseif ($data['editType']['value']=='Gespreksverslagen')
	{
    $portaalVink = false;
	  $list->editScript='CRM_naw_dossierEdit.php';
	  $data['toon']['value']='';
	}

	if($data['categorie']['value'] == '')
	{
	  if($data['dd_id']['value'])
	    $type='Documenten';
	  else
	    $type='Gespreksverslagen';
	  $data['categorie']['value']=$optieArray[$type];
	}

  if($_DB_resources[DBportaal] AND $portaalVink)
  {

    $data['portaalKoppelId']['form_type']='checkbox';
    $query = "SELECT id FROM dd_reference WHERE portaalKoppelId='" . $data['id']['value'] . "'";
    if ($data['editType']['value']=='dd' && $dbPortaal->QRecords($query)>0)
      $data['portaalKoppelId']['value']='1';
    else
      $data['portaalKoppelId']['value']='0';

  }

  if ( ! empty ($data['filetype']['value']) ) {
    $zipList .= '
      <tr>
        <td><input class="toZip" type="checkbox" name="'.$data['id']['value'].'" /></td>  
        <td>' . date('d-m-Y', strtotime($data['add_date']['value'])) . '</td>  
        <td>' . $data['description']['value'] . '</td>  
        <td>' . $data['categorie']['value'] . '</td>   
        <td>' . $data['filename']['value'] . '</td>  
        <td>' . $data['filesize']['value'] . '</td>  
      </tr>
    ';
  }


	echo $list->buildRow($data);

}
?>
</table>

  <div style="display: none" id="dialogMessage" title="<?=vt('Documenten');?>">

  <div id="FileList">

    <div class="pl-3 pb-3">
      <?= vt('Selecteer een of meerdere bestanden om deze als ZIP bestand te downloaden.'); ?>
    </div>

    <div class="box box12" >
      <div class="btn-group" role="group" style="height:26px;">
        <div class="btn btn-hover btn-default "  onclick="checkAllP(1);">&nbsp;&nbsp;<img src="icon/16/checks.png" class="simbisIcon" /> Alles </div>
        <div class="btn btn-hover btn-default"  onclick="checkAllP(0);">&nbsp;&nbsp;<img src="icon/16/undo.png" class="simbisIcon" /> Niets </div>
        <div class="btn btn-hover btn-default" onclick="checkAllP(-1);">&nbsp;&nbsp;<img src="icon/16/replace2.png" class="simbisIcon" /> Omkeren</div>
      </div>
    </div>
    </br></br></br>

    <table>
      <tr class="list_kopregel">
        <td class="list_kopregel_data"></td>
        <td style="width:100px" class="list_kopregel_data">Datum</td>
        <td class="list_kopregel_data">Omschrijving</td>
        <td class="list_kopregel_data">Categorie</td>
        <td class="list_kopregel_data">Filename</td>
        <td class="list_kopregel_data">Filesize</td>
      </tr>

      <?=$zipList;?>
    </table>
  </div>
  </div>

<?
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooterZonderMenu"],$content);
}
?>



  <script>

    function openDocumentSelect () {
      $( "#dialogMessage" ).dialog({
        position: { my: "center", at: "top" },
        draggable: false,
        modal: true,
        resizable: false,
        width: 'auto',
        title: '<?=vt('Download als zip')?>',
        minHeight: 150,
        buttons:
          {
            "<?=vt('Download Zip');?>": function (event) {
              downloadAsZip();
              $(this).dialog('destroy');
            },
            "<?=vt('Sluiten');?>": function () { $(this).dialog('destroy'); }
          }
      });
    }

    function checkAllP(optie)
    {
      if(optie == 1) {
        $('.toZip:checkbox').prop( "checked", true );
      } else if(optie == 0) {
        $('.toZip:checkbox').prop( "checked", false );
      } else if (optie == -1) {
        $('.toZip:checkbox').each(function () {
          if ( $(this).prop('checked') ) {
            $(this).prop( "checked", false );
          } else {
            $(this).prop( "checked", true );
          }
        });
      }
    }

    function downloadAsZip (e) {
        var selected = [];
        $('.toZip:checked').each(function() {
          selected.push($(this).attr('name'));
        });

        if ( selected.length > 0 ) {
          let url = 'dd_push.php?downloadZip=1&fileName=Doc_<?=$id;?>_<?=$nawRec['portefeuille'];?>_<?=date('Y-m-d');?>&fileIds=' + selected.toString();

          location.href = url;
        }

    };

  </script>
