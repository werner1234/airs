<?php
/*
    AE-ICT CODEX source module versie 1.6, 10 april 2010
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2018/10/24 14:20:55 $
    File Versie         : $Revision: 1.15 $

    $Log: modelportefeuillefixedList.php,v $
    Revision 1.15  2018/10/24 14:20:55  rm
    7258 Model Portefeuille List: toevoegen ISINCode

    Revision 1.14  2017/08/30 15:01:24  rvv
    *** empty log message ***

    Revision 1.13  2017/05/17 13:38:28  rm
    5728

    Revision 1.12  2017/04/05 14:50:30  rm
    5728

    Revision 1.11  2017/04/05 14:47:31  rm
    5728

    Revision 1.10  2017/03/27 11:55:34  rm
    5728

    Revision 1.9  2017/02/15 10:56:00  rm
    no message

    Revision 1.8  2017/02/10 09:27:42  rm
    no message

    Revision 1.7  2017/02/06 07:52:11  rm
    no message

    Revision 1.6  2017/01/31 12:33:15  rm
    no message

    Revision 1.5  2017/01/18 16:05:27  rm
    5430 (EFI) Invoer FX-modelportefeuilles

    Revision 1.4  2015/04/29 15:22:34  rvv
    *** empty log message ***

    Revision 1.3  2014/06/11 15:44:58  rvv
    *** empty log message ***

    Revision 1.2  2011/08/31 14:37:40  rvv
    *** empty log message ***

    Revision 1.1  2011/04/27 17:53:49  rvv
    *** empty log message ***

    Revision 1.7  2011/01/19 16:43:55  rvv
    *** empty log message ***

    Revision 1.6  2010/08/28 14:11:23  rvv
    *** empty log message ***

    Revision 1.5  2010/08/20 06:14:08  rvv
    *** empty log message ***

    Revision 1.4  2010/06/20 15:33:19  rvv
    *** empty log message ***

    Revision 1.3  2010/05/02 10:04:24  rvv
    *** empty log message ***

    Revision 1.2  2010/04/25 10:52:21  rvv
    *** empty log message ***

    Revision 1.1  2010/04/11 11:57:06  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = '
  <a href="#" id="addNewRecord" onClick=""><img src="images//16/record_new.gif" width="16" height="16" border="0" alt="record toevoegen" align="absmiddle">&nbsp;toevoegen</a>
  
<a href="?action=xls&Portefeuille='.$_GET['Portefeuille'].'&Datum='.$_GET['Datum'].'&type='.$_GET['type'].'" ><img src="images//16/xls.gif" width="16" height="16" border="0" alt="record toevoegen" align="absmiddle">&nbsp;XLS-export</a>';
$mainHeader    = vt("overzicht");

$content['style'] = $editcontent['style'];

$editScript = "modelportefeuillefixedEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = 500;

$list->addColumn("ModelPortefeuilleFixed","id",array("list_width"=>"100","search"=>false));
//$list->addColumn("","inlineEdit",array("list_width"=>"15","search"=>false));
$list->addColumn("ModelPortefeuilleFixed","Portefeuille",array("list_width"=>"100","search"=>false));
$list->addColumn("ModelPortefeuilleFixed","Fonds",array("list_width"=>"200","search"=>false));
$list->addColumn("ModelPortefeuilleFixed","Percentage",array("list_width"=>"50","search"=>false));
$list->addColumn("","ISINCode",array('sql_alias'=>'Fondsen.ISINCode',"list_width"=>"160","search"=>false));
$list->addColumn("","valuta",array('sql_alias'=>'Fondsen.valuta', "list_width"=>"100","search"=>false,"list_order"=>true));
$list->addColumn("","Beleggingscategorie",array('sql_alias'=>'BeleggingscategoriePerFonds.Beleggingscategorie',"list_width"=>"100","search"=>false,"list_order"=>true, 'description' => 'Categorie'));
$list->addColumn("","Beleggingssector",array('sql_alias'=>'BeleggingssectorPerFonds.Beleggingssector',"list_width"=>"100","search"=>false,"list_order"=>true, 'description' => 'Sector'));
$list->addColumn("","regio",array('sql_alias'=>'BeleggingssectorPerFonds.Regio',"list_width"=>"100","search"=>false,"list_order"=>true));
$list->addColumn("","afmCategorie",array('sql_alias'=>'BeleggingscategoriePerFonds.afmCategorie',"list_width"=>"100","search"=>false,"list_order"=>true));

$list->setJoin("LEFT JOIN Fondsen on ModelPortefeuilleFixed.Fonds=Fondsen.Fonds
Inner Join Portefeuilles ON ModelPortefeuilleFixed.Portefeuille = Portefeuilles.Portefeuille
Left Join BeleggingssectorPerFonds ON ModelPortefeuilleFixed.Fonds = BeleggingssectorPerFonds.Fonds AND Portefeuilles.Vermogensbeheerder = BeleggingssectorPerFonds.Vermogensbeheerder
Left Join BeleggingscategoriePerFonds ON ModelPortefeuilleFixed.Fonds = BeleggingscategoriePerFonds.Fonds AND Portefeuilles.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder
");
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
$list->setWhere("ModelPortefeuilleFixed.Portefeuille='".$_GET['Portefeuille']."' AND Datum='".$_GET['Datum']."'");
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);



//$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
//$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
//$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));

if($_GET['action'] == 'xls')
{
  $list->addColumn("","fondsimportcode",array('sql_alias'=>'Fondsen.fondsimportcode', "list_width"=>"100","search"=>false,"list_order"=>true));
  $list->setXLS();
  $list->getXLS();
}
else
{


  /**
   * Autocomplete velden aanmaken voor fonds
   */
  $autocomplete = new Autocomplete();
  $autocomplete->resetVirtualField('Fonds');

  $autocomplete->addVirtuelField(
    'Fonds',
    array(
      'autocomplete' => array(
        'table'        => 'Fondsen',
        'label'        => array(
          'Fondsen.Fonds',
          'Fondsen.ISINCode',
          'combine' => '({Valuta})'
        ),
        'searchable'   => array('Fondsen.Fonds', 'Fondsen.ISINCode', 'Fondsen.Omschrijving'),
        'field_value'  => array('Fondsen.Fonds'),
        'extra_fields' => array('*'),
        'value'        => 'Fonds',
        'actions'      => array(
          'select' => '
            event.preventDefault(); 
            changeFonds(ui, $(this));
          ',
          'change' => '
          
          '
        ),
        'conditions'   => array(
          'AND' => array(
            '(Fondsen.EindDatum >= now() OR Fondsen.EindDatum = "0000-00-00")',
            'Fondsen.Fonds <> "Liquiditeiten"'
          )
        ),
        'default_values' => array (
          'Liquiditeiten' => 'Liquiditeiten'
        ),
        'default_sort' => 'asc'
      ),

      'form_size'    => '15',
    )
  );

  $content['javascript'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('Fonds', null, true);




$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content['javascript'] .= "
function addRecord()
{
	location = '".$editScript."?action=new&Portefeuille=".$_GET['Portefeuille']."&Datum=".$_GET['Datum']."&type=".$_GET['type']."';
}

function changeFonds(ui, element) {
  element.val(ui.item.field_value);
  $('input[name=Fonds]').val(ui.item.value);
}

$(function () {
  $(document).on('click', '#addNewRecord', function () {
    $('#addNewRecordHolder').slideToggle();
    $('#addNewRecordHolder input[name=FondsLookup]').autocomplete(Fonds_autocomp_opt);
  });
  

  /** tooltip **/
  $('body').tooltip({ selector: '[data-toggle=\"tooltip\"]' });

  $(document).on('click', '.saveInline', function () {
    curBtn = $(this);
    
    row = $(this).parent().parent();
    curPercentage = row.find('.PercentageVal').html();

   
    rowId = $(this).data('rowid');
    postData = row.find('.input').serializeArray();
    
    row.find('.help-block.with-errors').remove();
    row.find(':input').css('border-color', ''); //reset inputfield style
  
    $.ajax({
      url : 'modelportefeuillefixedEdit.php?action=update&id=' + rowId,
      type: \"POST\",
      dataType: 'json',
      data : postData,
      success:function(data, textStatus, jqXHR) {
        if ( data.success === true ) {
          if ( data.saved === false ) {
            $.each(data.errors, function (field, fieldMessage) {
              row.find('[name=' + field + ']').css('border-color', 'red');
              row.find('[name=' + field + ']').parent().append('<span class=\"help-block with-errors\">' + fieldMessage + '</span>');
            });
          } else {
            $.each(data.lineData, function( index, value ) {
              if ( value != null && value.length > 0 ) {
                if ( index === 'Percentage' ) {
                  row.find('td[data-field=\"'+index+'\"]').html('<span class=\"PercentageVal\" style=\"margin-right: 6px;    display: block;\">' + value + \"</span>\");
                } else {
                  row.find('td[data-field=\"'+index+'\"]').html('<span style=\"margin-right: 6px;    display: block;\">' + value + \"</span>\");
                }
              } else {
                row.find('td[data-field=\"'+index+'\"]').html(' ');
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
      url : 'modelportefeuillefixedEdit.php?action=update&saveType=new&Portefeuille=".$_GET['Portefeuille']."&Datum=".$_GET['Datum']."&type=".$_GET['type']."',
      type: \"POST\",
      dataType: 'json',
      data : postData,
      success:function(data, textStatus, jqXHR) {
        if ( data.success === true ) {
          if ( data.saved === false ) {
            $.each(data.errors, function (field, fieldMessage) {
              $('#addNewRecordHolder').find('[name=' + field + ']').css('border-color', 'red');
              $('#addNewRecordHolder').find('[name=' + field + ']').parent().append('<span class=\"help-block with-errors\">' + fieldMessage + '</span>');
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
    fondsTd = row.find('td[data-field=\"Fonds\"]');
    currentFonds = fondsTd.html();
    var input = $('<input type=\"text\" name=\"Fonds\" class=\"input fondsautocomplete\" value=\"\" />');
    input.appendTo(fondsTd).focus().autocomplete(Fonds_autocomp_opt);

	  
	  //rentepercentage vervangen
	  percentageTd = row.find('td[data-field=\"Percentage\"]');
	  currentPercentage = percentageTd.html();
    var input = $('<input type=\"text\" name=\"Percentage\" class=\"input\" value=\"\" />');
    input.appendTo(percentageTd);
    
    curBtn.hide();
    curBtn.parent().find('.saveInline').show();
    curBtn.parent().find('.cancelInline').show();
	});
	
	
	$(document).on('click', '.deleteInline', function () {
	  curBtn = $(this);
    row = $(this).parent().parent();
    rowId = $(this).data('rowid');
    
    AEConfirm('Weet u zeker dat u deze regel wilt verwijderen?', 'Verwijderen', function () {
      $.ajax({
        url : 'modelportefeuillefixedEdit.php?action=delete&id=' + rowId,
        type: \"POST\",
        dataType: 'json',
        success:function(data, textStatus, jqXHR) {
          if ( data.success === true ) {
            if ( data.saved === false ) {
              alert('regel kon niet worden verwijderd');
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
	
	
	$('#mytable').on('focus', '.autocomplete-input', function () { 
      $(this).autocomplete(Fonds_autocomp_opt);
  });

})


";

  $content['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/bootstrapTooltip.js\"></script>";


  echo template($__appvar["templateContentHeader"],$content);
  echo '
    <div class="formHolder box box12" id="addNewRecordHolder" style="display:none;">
      <div class="formTitle textB">' . vt('Nieuwe regel') . '</div>
      <div class=" formContent">
        <div id="orderPreMessages" class=""></div>
        <div class="padded-10">
          <table>
            <tr>
              <td>' . vt('Fonds') . '</td>
              <td>' . vt('Percentage') . '</td>
              <td></td>
            </tr>
            <tr>
              <td>
                <input type="text" name="FondsLookup" class="input fondsautocomplete ui-autocomplete-input" value="" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true"> <br />
                <input type="hidden" value="" name="Fonds" class="input" >
              </td>
              <td><input type="text" name="Percentage" class="input" value=""></td>
              <td><button class="btn-new btn-save btn-xs" id="newInLine"><i class="fa fa-floppy-o" aria-hidden="true"></i> ' . vt('Opslaan') . '</button></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  ';


	//unset field 1 name zodat er geen header is en dus een blank veld
	$list->columns[1]['name'] = '';
?>

<table class="list_tabel" cellspacing="0">
<?=$list->printHeader(true);?>
<?php

// naam van field 1 weer herstellen zodat we deze kunnen vullen
$list->columns[1]['name'] = 'inlineEdit';

$totaalPercentage=0;
while($data = $list->getRow())
{
//	debug($data);
	$data['inlineEdit'] = array(
		'value' => '
		  <span data-toggle="tooltip" title="Regel verwijderen" class="btn-new btn-default btn-xs deleteInline" data-rowid="' . $data['id']['value'] . '"><i class="fa fa-times" aria-hidden="true"></i></span>
		  <span data-toggle="tooltip" title="Regel Wijzigen" class="btn-new btn-default btn-xs editInline" data-rowid="' . $data['id']['value'] . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> </span>
		  <span class="btn-new btn-default btn-xs saveInline" style="display:none;" data-rowid="' . $data['id']['value'] . '"><i class="fa fa-floppy-o" aria-hidden="true"></i> </span>
		  <span data-toggle="tooltip" title="Wijzigen ongedaan maken" class="btn-new btn-default btn-xs cancelInline" style="display:none;" data-rowid="' . $data['id']['value'] . '"><i class="fa fa-refresh" aria-hidden="true"></i> </span>
		  ',
	);
  $data['disableEdit'] = true;
	// $list->buildRow($data,$template="",$options="");
	$totaalPercentage+=$data['Percentage']['value'];
  $data['Percentage']['value'] = '<span class="PercentageVal">'.$data['Percentage']['value'].'</span>';

//  $data['test']['value']=$data['id']['value'];

//  $data['id']['value']=$data['id']['value']."&type=".$_GET['type'];
	echo $list->buildRow($data);
	$lastData=$data;
}
include_once("rapport/rapportRekenClass.php");

if($_GET['Datum']<>'')
  $datum=$_GET['Datum'];
else
  $datum=substr(getLaatsteValutadatum(),0,10);
$regels=berekenFixedModelPortefeuille($_GET['Portefeuille'],$datum);
vulTijdelijkeTabel($regels,$_GET['Portefeuille'],$datum);
$tmp=AFMstd($_GET['Portefeuille'],$datum);
verwijderTijdelijkeTabel($_GET['Portefeuille'],$datum);

if($_GET['type'] <> 2)
{
echo '<tr class="list_dataregel"><td class="list_button"><div>&nbsp;</div></td>
<td class="listTableData" width="100"  align="left" >&nbsp;</td>
<td class="listTableData" width="100"  align="left" ><b>' . vt('Totaal') . '</b></td>
<td class="listTableData" width="100"  align="right" ><b class="tableTotal">'.$totaalPercentage.' </b>&nbsp; </td>
</tr>';
echo '<tr class="list_dataregel"><td class="list_button"><div>&nbsp;</div></td>
<td class="listTableData" width="100"  align="left" >&nbsp;</td>
<td class="listTableData" width="100"  align="left" ><b>' . vt('AFM stdev') . '</b></td>
<td class="listTableData" width="100"  align="right" ><b>'.round($tmp['std'],2).' </b>&nbsp; </td>
</tr>';
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
}
?>