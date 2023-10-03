<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 7 oktober 2015
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2019/06/21 14:09:31 $
    File Versie         : $Revision: 1.16 $
 		
    $Log: modelportefeuillesperportefeuilleList.php,v $
    Revision 1.16  2019/06/21 14:09:31  rm
    7857

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = vt("modelportefeuilles per portefeuille");
$mainHeader    = vt("overzicht");
if($_GET['frame']==1)
  unset($_SESSION['ModelPortefeuillesPerPortefeuille']);

$editScript = "modelportefeuillesperportefeuilleEdit.php";
$allow_add  = true;



$filterData = array_merge($_GET, $_POST);

if ( isset ($filterData['portefeuille'])) {
  $_POST['quickfilter_portefeuille'] = $filterData['portefeuille'];
}

$quickfilter_portefeuille = null;
$quickfilter_vanaf = null;

// reset filter (Filter verwijderen)
if ( $filterData['resetFilter'] && $filterData['resetFilter'] == 1 ) {
  unset($_POST['quickfilter_portefeuille']);
  unset($_POST['quickfilter_vanaf']);
  unset($_SESSION['ModelPortefeuillesPerPortefeuille']['filter']);
  $_SESSION['ModelPortefeuillesPerPortefeuille']['filter'] = null;
  header("Location: modelportefeuillesperportefeuilleList.php");
}

// wanneer er een filter is ingesteld, deze omzetten naar de snelfilter
if ( isset ($_SESSION['ModelPortefeuillesPerPortefeuille']['filter']) && ! empty($_SESSION['ModelPortefeuillesPerPortefeuille']['filter']) ) {
  foreach ( $_SESSION['ModelPortefeuillesPerPortefeuille']['filter'] as $listFilterData) {
    $filterOnField = explode('.', $listFilterData['veldnaam']);
    if ( $filterOnField[1] === 'Vanaf' && $listFilterData['methode'] === 'gelijk' ) {
      $quickfilter_vanaf = $listFilterData['waarde'];
    }
    
    if ( $filterOnField[1] === 'Portefeuille' && $listFilterData['methode'] === 'gelijk' ) {
      $quickfilter_portefeuille = $listFilterData['waarde'];
      $_GET['portefeuille'] = $quickfilter_portefeuille;
    }
  }
}

if ( isset ($_POST['quickfilter_portefeuille']) && ! empty ($_POST['quickfilter_portefeuille']) ) {
  $quickfilter_portefeuille = $_POST['quickfilter_portefeuille'];
}

if ( isset ($_POST['quickfilter_vanaf']) ) {
  $quickfilter_vanaf = $_POST['quickfilter_vanaf'];
}

if ( isset ($_POST['quickfilter_portefeuille']) ) {
  $_GET['portefeuille'] = $_POST['quickfilter_portefeuille'];
  $quickfilter_portefeuille = $_POST['quickfilter_portefeuille'];
  if ( ! empty ($_POST['quickfilter_portefeuille']) ) {
    $_SESSION['ModelPortefeuillesPerPortefeuille']['filter'][0] = array(
      'veldnaam' => 'ModelPortefeuillesPerPortefeuille.Portefeuille',
      'methode' => 'gelijk',
      'waarde' => $_POST['quickfilter_portefeuille'],
    );
    
  } else {
    foreach ( $_SESSION['ModelPortefeuillesPerPortefeuille']['filter'] as $key => $value ) {
      if ( $value['veldnaam'] === 'ModelPortefeuillesPerPortefeuille.Portefeuille' ) {
        unset($_SESSION['ModelPortefeuillesPerPortefeuille']['filter'][$key]);
      }
    }
    $_GET['portefeuille'] = '';
    $quickfilter_portefeuille = '';
  }
}


$portefeuilleWhere = '';
if($_GET['portefeuille']<>'')
{
  $portefeuilleWhere = "ModelPortefeuillesPerPortefeuille.Portefeuille='" . $_GET['portefeuille'] . "'";
  $portefeuilleWhereFilter="WHERE $portefeuilleWhere";
}

$db=new DB();

$query = "
SELECT
  ModelPortefeuillesPerPortefeuille.Portefeuille,
  SUM(ModelPortefeuillesPerPortefeuille.Percentage) as totaal,
  ModelPortefeuillesPerPortefeuille.Vanaf
FROM 
  ModelPortefeuillesPerPortefeuille 
$portefeuilleWhereFilter
GROUP BY 
  ModelPortefeuillesPerPortefeuille.Portefeuille,ModelPortefeuillesPerPortefeuille.Vanaf
ORDER BY 
  ModelPortefeuillesPerPortefeuille.Vanaf desc

";
$firstDate = null;
$db->SQL($query);
$db->query();
$dateFilter[] = '<option value="">---</option>';
if($db->records() > 0)
{
  while ($data = $db->NextRecord())
  {
    if ( ! empty ($portefeuilleWhereFilter) && $firstDate === null && is_null($quickfilter_vanaf)  ) {
      $firstDate = date('d-m-Y', strtotime($data['Vanaf']));
      $quickfilter_vanaf = $firstDate;
      $_POST['quickfilter_vanaf'] = $firstDate;
    }
    $dateFilter[$data['Vanaf']] = '<option ' . ( $quickfilter_vanaf ==  date('d-m-Y', strtotime($data['Vanaf'])) ? 'selected':'' ) . ' value="' . date('d-m-Y', strtotime($data['Vanaf'])) . '">' . date('d-m-Y', strtotime($data['Vanaf'])) . '</option>';;
  }
}

$vanafWhere = '';
if ( isset ($_POST['quickfilter_vanaf']) ) {
  
  
  if ( ! empty ($_POST['quickfilter_vanaf']) ) {
    $_SESSION['ModelPortefeuillesPerPortefeuille']['filter'][1] = array(
      'veldnaam' => 'ModelPortefeuillesPerPortefeuille.Vanaf',
      'methode' => 'gelijk',
      'waarde' => $_POST['quickfilter_vanaf'],
    );
    $quickfilter_vanaf = $_POST['quickfilter_vanaf'];
  } else {
    foreach ( $_SESSION['ModelPortefeuillesPerPortefeuille']['filter'] as $key => $value ) {
      if ( $value['veldnaam'] === 'ModelPortefeuillesPerPortefeuille.Vanaf' ) {
        unset($_SESSION['ModelPortefeuillesPerPortefeuille']['filter'][$key]);
      }
    }
    $quickfilter_vanaf = '';
  }
}

if ( ! empty ($quickfilter_vanaf) ) {
  $vanafWhere = ( ! empty($portefeuilleWhere) ? ' AND ':'' ) . "ModelPortefeuillesPerPortefeuille.Vanaf='" . date('Y-m-d', strtotime($quickfilter_vanaf)) . "'";
  $vanafWhereFilter = ( ! empty($portefeuilleWhere) ? '  ':' WHERE ' ) . $vanafWhere;
}




$query="
SELECT
  ModelPortefeuillesPerPortefeuille.Portefeuille,
  SUM(ModelPortefeuillesPerPortefeuille.Percentage) as totaal,
  ModelPortefeuillesPerPortefeuille.Vanaf
FROM 
  ModelPortefeuillesPerPortefeuille $portefeuilleWhereFilter $vanafWhereFilter
GROUP BY 
  ModelPortefeuillesPerPortefeuille.Portefeuille,
  ModelPortefeuillesPerPortefeuille.Vanaf
HAVING 
  totaal <> 100
ORDER BY 
  ModelPortefeuillesPerPortefeuille.Portefeuille,
  ModelPortefeuillesPerPortefeuille.Vanaf desc ";

$db->SQL($query);
$db->query();
if($db->records() > 0)
{
  $table = '<br />
  <table class="table table-compact" style="width:300px;">
    <thead>
      <tr>
        <td><strong>'.vt("Portefeuille").'</strong></td>
        <td><strong>'.vt("Vanaf").'</strong></td>
        <td><strong>'.vt("Totaal percentage").'</strong></td>
      </tr>  
    </thead>';
  while ($data = $db->NextRecord())
  {
    if ( $data['totaal'] != 100 ) {
      $table.="<tr><td>".$data['Portefeuille']."</td><td>".date('d-m-Y', strtotime($data['Vanaf']))."</td><td align='right' class='tableTotal'>".number_format($data['totaal'], 2)."</td></tr>";
    }
  }
  $table.="</table><br />";
}

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];


$list->addFixedField("ModelPortefeuillesPerPortefeuille","Portefeuille",array("list_width"=>"100","search"=>false));
$list->addFixedField("ModelPortefeuillesPerPortefeuille","ModelPortefeuille",array("list_width"=>"150","search"=>false));
$list->addFixedField("ModelPortefeuillesPerPortefeuille","Percentage",array("list_width"=>"100","search"=>false, 'list_format' => null, 'list_nobreak' => true));
$list->addFixedField("ModelPortefeuillesPerPortefeuille","Vanaf",array("list_width"=>"100","search"=>false));

$list->categorieVolgorde['ModelPortefeuillesPerPortefeuille']=array('Algemeen');
$list->categorieVolgorde['Portefeuilles']=array('Gegevens','Beheerfee','Staffels');

$html = $list->getCustomFields(array('ModelPortefeuillesPerPortefeuille','Portefeuilles'));


foreach ($list->columns as $colData)
{
  if ($colData['objectname'] == 'Portefeuilles')
  {
    $joinPortefeuilles = " LEFT JOIN Portefeuilles ON Portefeuilles.Portefeuille = ModelPortefeuillesPerPortefeuille.Portefeuille ";
  }
}

$list->ownTables=array('ModelPortefeuillesPerPortefeuille');
$list->setJoin("$joinPortefeuilles");

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");

// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
//if($_GET['portefeuille']<>'')
	$list->setWhere($portefeuilleWhere . $vanafWhere);

// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);

//$list->setFilter();

// select page
$list->selectPage($_GET['page']);

$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION["NAV"]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION["NAV"]->addItem(new NavSearch($_GET['selectie']));

$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div>";

$content["javascript"] .= "
function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
function addRecordFrame() 
{
	location =  '".$editScript."?action=new&frame=1&Portefeuille=".$_GET['portefeuille']."';
}
function editRecord(url) 
{
	location = url;
}
";

$content['style'] .= $editcontent['style'];
$content['jsincludes'] .= $editcontent['jsincludes'];



echo template($__appvar["templateContentHeader"],$content);
?>
<style>
  form[name=editForm] table:first-of-type {
    display: none;
  }
  
  .table-compact td {
    padding:0px!important;
  }
</style>
<br>
<?
if($_GET['frame']==1 && $_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0)
{
  echo '<a href="#" onclick="addRecordFrame()"><span title="'.vt("record toevoegen").'"><img src="icon/16/add.png" class="simbisIcon">'.vt("toevoegen").'</span> </a><br><br>';
}


/**
 * Ophalen portefeuilles aanwezig in tabel
 */
$db=new DB();
$query="
  SELECT
    ModelPortefeuillesPerPortefeuille.Portefeuille
    
    FROM ModelPortefeuillesPerPortefeuille
    GROUP BY ModelPortefeuillesPerPortefeuille.Portefeuille
    
    ORDER BY ModelPortefeuillesPerPortefeuille.Portefeuille desc
";
$db->SQL($query);
$db->query();
$portefeuilleList[] = '<option value="">---</option>';
if( $db->records() > 0 ) {
  while ( $data = $db->NextRecord() ) {
    $portefeuilleList[] = '<option ' . ( $quickfilter_portefeuille ==  $data['Portefeuille'] ? 'selected':'' ) . ' value="' . $data['Portefeuille'] . '">' . $data['Portefeuille'] . '</option>';
  }
}


$modelPortefeuilleQuery = '
SELECT ModelPortefeuilles.Portefeuille,if(ModelPortefeuilles.fixed=1,concat(ModelPortefeuilles.Portefeuille,\' (FX)\'),concat(ModelPortefeuilles.Portefeuille,\' (Dyn)\')) FROM ModelPortefeuilles
        JOIN Portefeuilles ON ModelPortefeuilles.Portefeuille=Portefeuilles.Portefeuille
        WHERE Portefeuilles.Einddatum>now() AND ModelPortefeuilles.Fixed<2 ORDER BY Portefeuille';


$db->SQL($modelPortefeuilleQuery);
$db->query();
$modelPortefeuilleList[] = '<option value=" ">---</option>';
if( $db->records() > 0 ) {
  while ( $data = $db->NextRecord() ) {
    $modelPortefeuilleList[] = '<option value="' . $data['Portefeuille'] . '">' . $data['Portefeuille'] . '</option>';
  }
}
?>
  
  <form method="post">
    <div class="formHolder box box12"  >
      <div class="formTitle textB"><?=vt("Snel filter")?></div>
      <div class=" formContent">
        <div id="orderPreMessages" class=""></div>
        <div class="padded-10">
          <table>
            <tr>
              <td><?=vt("Portefeuille")?></td>
              <td><?=vt("Datum")?></td>
              <td></td>
            </tr>
            <tr>
              <td>
                <?php
                if ( isset ($filterData['frame']) && (int) $filterData['frame'] === 1 ) {
                  echo $quickfilter_portefeuille;
                } else {
                  echo '<select name="quickfilter_portefeuille" class="input ">' . implode(" \n ", $portefeuilleList) . '</select>';
                }
                ?>
              </td>
              <td>
                <?='<select name="quickfilter_vanaf" class="input ">' . implode(" \n ", $dateFilter) . '</select>'?>
                
                </td>
              <td><button class="btn-new btn-save btn-xs" ><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=vt("Filter instellen")?></button></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </form>
  


<form name="editForm" method="POST">
<?php

//if (  isset ($filterData['frame']) || (int) $filterData['frame'] === 1 )
//{
//  $list->hideFilter = true;
//  $list->noExport = true;
//
//}
echo $list->filterHeader();
echo $table;


if ( ! empty ($quickfilter_vanaf) && ! empty ($quickfilter_portefeuille) ) {
  
  if ( $_SESSION['usersession']['gebruiker']['Beheerder'] == 1 )
  {
    echo '<span id="copy" class="btn-new btn-default"><i class="fa fa-files-o" aria-hidden="true"></i> '.vt("Kopieer").'</span> &nbsp;';
  }

  $button = '';
  if( $_GET['frame'] == 1 ) {
    if ($_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0 ) {
      $button = '<a href="#" id="newInLine"><img src="images//16/save.gif" width="16" height="16" border="0" alt="Verzenden" align="absmiddle">&nbsp;Naar AIRS verzenden</a>
      <input type="hidden" name="frame" value="1">';
    } else {
      $button = 'Geen rechten om te verzenden.';
    }
  } else {
    $button = '<button class="btn-new btn-save btn-xs" id="newInLine"><i class="fa fa-floppy-o" aria-hidden="true"></i> Opslaan</button>';
  }


  echo '
    <span id="addNewRecord" class="btn-new btn-default"><i class="fa fa-plus" aria-hidden="true"></i> '.vt("Nieuwe regel toevoegen").'</span>
  
    <br /><br /><br />
  
<div class="formHolder box box12" id="addNewRecordHolder" style="display:none;">
  <div class="formTitle textB">'.vt("Nieuwe regel").'</div>
  <div class=" formContent">
    <div id="orderPreMessages" class=""></div>
    <div class="padded-10">
      <table>
        <tr>
          <td>'.vt("ModelPortefeuille").'</td>
          <td>'.vt("Percentage").'</td>
          <td></td>
        </tr>
        <tr>
          <td>
          <select  name="ModelPortefeuille" class="input" >' . implode(" \n ", $modelPortefeuilleList) . '</select> <br />
          </td>
          <td><input type="text" name="Percentage" class="input" value=""></td>
          <td>'.$button.'</td>
        </tr>
      </table>
    </div>
  </div>
</div>
  
  
  
  ';
}


?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
$list->customEdit =true;



//$list->columns[0]['name'] = 'inlineEdit';
$totaalPercentage = 0;
$list->noClick = true;
while($data = $list->getRow())
{
	$data['extraqs']='frame='.$_GET['frame'];
  
  if ( ! empty ($quickfilter_portefeuille) &&  ! empty ($quickfilter_vanaf) ) {
    $delete = '';
    if ( (! isset ($filterData['frame']) || (int) $filterData['frame'] === 0) && $_SESSION['usersession']['gebruiker']['Beheerder'] == 1 )
    {
      $delete = '<span data-toggle="tooltip" title="'.vt("Regel verwijderen").'" class="btn-new btn-default btn-xs deleteInline" data-rowid="' . $data['id']['value'] . '"><i class="fa fa-times" aria-hidden="true"></i></span>';
    }
  
    $saveBtn = ' <span class="btn-new btn-default btn-xs saveInline" style="display:none;" data-rowid="' . $data['id']['value'] . '"><i class="fa fa-floppy-o" aria-hidden="true"></i> </span>';
    if ($_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0 )
    {
      $saveBtn = ' <span class="btn-new btn-default btn-xs saveInline" style="display:none;" data-rowid="' . $data['id']['value'] . '"><i class="fa fa-floppy-o" aria-hidden="true"></i> <small>'.vt("Naar AIRS verzenden").'</small></span>';
    }
    
    $data['inlineEdit'] = array(
      'value' => $delete . $saveBtn . '
        
        <span data-toggle="tooltip" title="'.vt("Regel Wijzigen").'" class="btn-new btn-default btn-xs editInline" data-rowid="' . $data['id']['value'] . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> </span>
        <span data-toggle="tooltip" title="'.vt("Wijzigen ongedaan maken").'" class="btn-new btn-default btn-xs cancelInline" style="display:none;" data-rowid="' . $data['id']['value'] . '"><i class="fa fa-refresh" aria-hidden="true"></i> </span>
        ',
    );
    $totaalPercentage+=$data['ModelPortefeuillesPerPortefeuille.Percentage']['value'];
    
    $data['ModelPortefeuillesPerPortefeuille.Percentage']['value'] = '<span class="PercentageVal">'.number_format($data['ModelPortefeuillesPerPortefeuille.Percentage']['value'], 2).'</span>';
  }
	echo $list->buildRow($data);
}

if ( ! empty ($quickfilter_portefeuille) &&  ! empty ($quickfilter_vanaf) )
{
  echo '
  <tr class="list_dataregel"><td class="list_button"><div>&nbsp;</div></td>
<td class="listTableData" width="100"  align="left" >&nbsp;</td>
<td class="listTableData" width="100"  align="left" ><b>'.vt("Totaal").'</b></td>
<td class="listTableData" width="100"  align="right" ><b class="tableTotal">' . $totaalPercentage . ' </b>&nbsp; </td>
</tr>';
}


?>
</table>
</form>
  
  
  
  <div id="copyDialog" title="Kopieer naar nieuwe datum" style="display: none;">
    <input type="hidden" name="currentSelectedDate" id="currentSelectedDateInput" value="" />
    <p><?=vt("Regels kopieren van")?> <span id="currentSelectedDateView"></span> <?=vt("uit portefeuille")?>: <?= ( ! empty ($_POST['quickfilter_portefeuille']) ? $_POST['quickfilter_portefeuille'] : '') ?></p>
    
    <div class="formblock">
      <div class="formlinks"><?=vt("Kopieren naar")?>: </div>
      <div class="formrechts">
        <input id="newDate" type="text" class="AIRSdatepicker"/>
      </div>
    </div>
  
  </div>
  
  
  
  <script type="text/javascript">
    
    
    $(function () {
      
      $('#newDate').on('change', function () {
        var selectedDate = $('#newDate').datepicker('getDate');
        var now = new Date();
        now.setHours(0,0,0,0);
        if (selectedDate > now) {
          $('#newDate').val('');
          AEMessage('<?=vt("Let op: Datum mag niet in de toekomst liggen")?>!', '<?=vt("Datum")?>');
        }
        
        var options = $('#FixedDatum option');
        
        
        var formatedSelectedDate = $.datepicker.formatDate('yy-mm-dd', selectedDate);
        $.map(options ,function(option) {
          if ( $.datepicker.formatDate('yy-mm-dd', new Date(option.value)) === formatedSelectedDate) {
            $('#newDate').val('');
            AEMessage('<?=vt("Let op: Geselecteerde datum bestaat al")?>!', '<?=vt("Datum")?>');
          }
        });
      });
      
      
      $(document).on('click', '#copy', function () {
        $('#currentSelectedDateView').html($("#FixedDatum option:selected").text());
        $('#currentSelectedDateInput').val($('#FixedDatum').val());
        
        dialog = $( "#copyDialog" ).dialog({
          autoOpen: false,
//      height: 400,
//      width: 350,
          modal: true,
          buttons: {
            "Regels kopieren": function() {
              if ( $('#newDate').val() !== '' ) {
                parent.frames['content'].location = "modelportefeuillesperportefeuilleEdit.php?type=copyTo&Portefeuille=<?=$_POST['quickfilter_portefeuille']?>&Vanaf=<?=$_POST['quickfilter_vanaf']?>&newDate=" + $('#newDate').val() + "";
              }
              else {
                AEMessage('<?=vt("Let op: Er moet een datum ingevuld worden")?>!', '<?=vt("Datum")?>');
              }
            },
            Cancel: function() {
              dialog.dialog( "close" );
            }
          },
          close: function() {
            dialog.dialog( "close" );
          }
        });
        
        
        dialog.dialog( "open" );
      });
  
  
      $(document).on('click', '#addNewRecord', function () {
        $('#addNewRecordHolder').slideToggle();
      });
  
  
      /** tooltip **/
      // $('body').tooltip({ selector: '[data-toggle="tooltip"]' });
  
      $(document).on('click', '.saveInline', function () {
        curBtn = $(this);
    
        row = $(this).parent().parent();
        curPercentage = row.find('.PercentageVal').html();
    
    
        rowId = $(this).data('rowid');
        postData = row.find('.input').serializeArray();
    
        row.find('.help-block.with-errors').remove();
        row.find(':input').css('border-color', ''); //reset inputfield style
    
        $.ajax({
          url : 'modelportefeuillesperportefeuilleEdit.php?action=update&id=' + rowId,
          type: "POST",
        dataType: 'json',
          data : postData,
          success:function(data, textStatus, jqXHR) {
          if ( data.success === true ) {
            if ( data.saved === false ) {
              $.each(data.errors, function (field, fieldMessage) {
                row.find('[name=' + field + ']').css('border-color', 'red');
                row.find('[name=' + field + ']').parent().append('<span class="help-block with-errors">' + fieldMessage + '</span>');
              });
            } else {
              $.each(data.lineData, function( index, value ) {
                if ( value != null && value.length > 0 ) {
                  if ( index === 'Percentage' ) {
                    row.find('td[data-field="'+index+'"]').html('<span class="PercentageVal" style="margin-right: 6px;    display: block;">' + value + "</span>");
                  } else {
                    row.find('td[data-field="'+index+'"]').html('<span style="margin-right: 6px;    display: block;">' + value + "</span>");
                  }
                } else {
                  row.find('td[data-field="'+index+'"]').html(' ');
                }
              });
          
              var n1 = parseFloat($('.tableTotal').html());
              var n2 = parseFloat(data.lineData.Percentage);
              var n3 = parseFloat(curPercentage);
          
              var r = (n1 - n3) + n2;
          
              $('.tableTotal').html(r);
          
              row.find('.input').remove();
          
              row.find('.help-block.with-errors').remove();
              row.find(':input').css('border-color', ''); //reset inputfield style
          
              curBtn.hide();
              curBtn.parent().find('.saveInline').hide();
              curBtn.parent().find('.editInline').show();
              curBtn.parent().find('.cancelInline').hide();
            }
          }
        }
      });
      });
  
      
      
      $(document).on('click', '#newInLine', function () {
        postData = $('#addNewRecordHolder').find('.input').serializeArray();
    
        $('#addNewRecordHolder').find('.help-block.with-errors').remove();
        $('#addNewRecordHolder').find(':input').css('border-color', ''); //reset inputfield style
    
        $.ajax({
          url : 'modelportefeuillesperportefeuilleEdit.php?action=update&saveType=new&Portefeuille=<?=$_POST['quickfilter_portefeuille']?>&Vanaf=<?=$_POST['quickfilter_vanaf']?>&type=<?=$_GET['type']?>&frame=<?=$_GET['frame']?>',
          type: "POST",
        dataType: 'json',
          data : postData,
          success:function(data, textStatus, jqXHR) {
          if ( data.success === true ) {
            if ( data.saved === false ) {
              $.each(data.errors, function (field, fieldMessage) {
                $('#addNewRecordHolder').find('[name=' + field + ']').css('border-color', 'red');
                $('#addNewRecordHolder').find('[name=' + field + ']').parent().append('<span class="help-block with-errors">' + fieldMessage + '</span>');
              });
            } else {
              var n1 = parseFloat($('.tableTotal').html());
              var n2 = parseFloat(data.lineData.Percentage);
              var r = n1 + n2;
          
              $('.tableTotal').html(r);
              $('.list_tabel > tbody > tr').eq(0).after(data.trHtml);
              $('#addNewRecordHolder').find('input[name=Fonds]').val('');
              $('#addNewRecordHolder').find('input[name=FondsLookup]').val('');
              $('#addNewRecordHolder').find('input[name=Percentage]').val('');
          
            }
          }
        }
      });
      });
  
  
  
      $(document).on('click', '.editInline', function () {
        curBtn = $(this);
        row = $(this).parent().parent();
    
        // vervangen van fonds veld
        fondsTd = row.find('td[data-field="ModelPortefeuille"]');
        currentFonds = fondsTd.html();
        var input = $('<select name="ModelPortefeuille" class="input" ><?= implode("", $modelPortefeuilleList) ?></select>');
        input.appendTo(fondsTd);
    
    
        //rentepercentage vervangen
        percentageTd = row.find('td[data-field="Percentage"]');
        currentPercentage = percentageTd.html();
        var input = $('<input type="text" name="Percentage" class="input" value="" />');
        input.appendTo(percentageTd);
    
        curBtn.hide();
        curBtn.parent().find('.saveInline').show();
        curBtn.parent().find('.cancelInline').show();
      });
  
  
      $(document).on('click', '.deleteInline', function () {
        curBtn = $(this);
        row = $(this).parent().parent();
        rowId = $(this).data('rowid');
    
        AEConfirm('<?=vt("Weet u zeker dat u deze regel wilt verwijderen")?>?', '<?=vt("Verwijderen")?>', function () {
          $.ajax({
            url : 'modelportefeuillesperportefeuilleEdit.php?action=delete&id=' + rowId,
            type: "POST",
          dataType: 'json',
            success:function(data, textStatus, jqXHR) {
            if ( data.success === true ) {
              if ( data.saved === false ) {
                alert('<?=vt("regel kon niet worden verwijderd")?>');
              } else {
                var n1 = parseFloat($('.tableTotal').html());
                var n2 = parseFloat(row.find('.PercentageVal').html());
            
                var r = n1 - n2;
            
                $('.tableTotal').html(r);
            
                row.hide();
              }
            }
          }
        });
        });
      });
  
      $(document).on('click', '.cancelInline', function () {
        curBtn = $(this);
        row = $(this).parent().parent();
    
        row.find('.input').remove();
    
        row.find('.help-block.with-errors').remove();
        row.find(':input').css('border-color', ''); //reset inputfield style
    
    
        curBtn.hide();
        curBtn.parent().find('.saveInline').hide();
        curBtn.parent().find('.editInline').show();
      });
  
      
      
      
    });
    
  
  </script>





<?
//foreach ( $filterCount as $date => $count ) {
//  $count = number_format($count, 2);
//  if ( (float) $count != 100 ) {
//    echo date('d-m-Y', strtotime($date) ) . ' ' . $count;
//  }
//}

logAccess();
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
