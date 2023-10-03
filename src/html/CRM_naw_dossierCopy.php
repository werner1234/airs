<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 16 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/11/18 10:59:45 $
    File Versie         : $Revision: 1.9 $

    $Log: CRM_naw_dossierCopy.php,v $
    Revision 1.9  2018/11/18 10:59:45  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/AE_cls_digidoc.php");
session_start();
//Edit content kopieren voor juiste stylesheets
$content = $editcontent;
$subHeader = " ";
$mainHeader = "Records kopiëren van";
$__appvar['rowsPerPage'] = 100;
$editScript = "CRM_naw_dossierEdit.php";
$allow_add = false;

if ($_GET['tabel'])
  $tabel = $_GET['tabel'];
elseif ($_POST['tabel'])
  $tabel = $_POST['tabel'];
else
  $tabel = 'CRM_naw_dossier';


$doelIds = array();
if ($_POST['verwerk'] == '1' && ($_POST['doelRelatie'] > 0 || count($_POST['selectedFields']) > 0)) {
  foreach ($_POST as $key => $value) {
    if (substr($key, 0, 3) == 'id_') {
      $ids[] = substr($key, 3);
    }
  }
  if ($_POST['doelRelatie'] > 0)
    $doelIds[] = $_POST['doelRelatie'];
  foreach ($_POST['selectedFields'] as $doelId) {
    $doelIds[] = $doelId;
  }

  if (count($ids) > 0 && count($doelIds) > 0) {
    $records = 0;
    if ($tabel == 'dd_reference') {
      $dd_referenceData = array();
      $DB = new DB();
      $query = "SELECT * FROM dd_reference WHERE module='CRM_naw' AND id IN('" . implode("','", $ids) . "')";
      $DB->SQL($query);
      $DB->Query();
      while ($data = $DB->nextRecord())
        $dd_referenceData[] = $data;
      foreach ($dd_referenceData as $refData) {
        $DB = new DB();
        $query = "SELECT * FROM " . $refData['datastore'] . " WHERE referenceId='" . $refData['id'] . "'";
        $DB->SQL($query);
        $document = $DB->lookupRecord();
        foreach ($refData as $veld => $data)
          $document[$veld] = $data;
        if ($document['blobCompressed'] == 1)
          $document['blobdata'] = gzuncompress($document['blobdata']);
        foreach ($doelIds as $doelId) {
          $document['module_id'] = $doelId;
          $doc = new digidoc();
          $doc->addDocumentToStore($document);
          $records++;
        }
        //echo "Bestand ".$document['filename']." gekopieerd.<br>\n";
      }
    } else {
      $db = new DB();
      $db2 = new DB();
      $query = "SELECT * FROM $tabel WHERE id IN('" . implode("','", $ids) . "')";
      $db->SQL($query);
      $db->Query();
      while ($verslag = $db->nextRecord()) {
        $query = "UPDATE $tabel SET memo=CONCAT('" . mysql_real_escape_string($verslag['memo']) . "','\nGekopieerd naar (" . mysql_real_escape_string($_POST['doelRelatie']) . ") ','" . date("d-m-Y H:m:s") . "'), change_date=NOW() WHERE id='" . $verslag['id'] . "'";
        $db2->SQL($query);
        $db2->Query();
        unset($verslag['id']);
        unset($verslag['change_date']);
        $verslag['memo'] = "Gekopieerd van (" . $verslag['rel_id'] . ") op " . date("d-m-Y H:m:s");
        foreach ($doelIds as $doelId) {
          $fields = '';
          $verslag['rel_id'] = $doelId;
          foreach ($verslag as $key => $value) {
            $fields .= ", $key='" . mysql_real_escape_string($value) . "' ";
          }
          $query = "INSERT INTO $tabel SET change_date=NOW() $fields";
          $db2->SQL($query);
          $db2->Query();
          $records++;
        }
      }
    }
  }
  $mainHeader = "(" . $records . ") record(s) gekopieerd van ";
}


$autocomplete = new Autocomplete();
$autocomplete->minLeng = 2;
$autocomplete->resetVirtualField('CRMnaam');
$editObject->formVars['CRMnaam'] = $autocomplete->addVirtuelField(
  'CRMnaam',
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
      'source_data' => array(
        'name' => array(
          'crmInactief',
        )
      ),
      'extra_fields' => array(
        'Portefeuille',
        'Client',
        'id',
      ),
      'label' => array('CRM_naw.naam', 'Portefeuilles.Portefeuille', 'CRM_naw.zoekveld'),
      'searchable' => array('CRM_naw.naam', 'Portefeuilles.Portefeuille', 'CRM_naw.zoekveld'),
      'extra_fields' => array('CRM_naw.id'),
      'field_value' => array('CRM_naw.naam'),
      'value' => 'CRM_naw.naam',
      'conditions'  => array (
        'AND' => array(
          '(`aktief` = 1 OR 1 = "{$get:crmInactief}")'
        )
      ),
      'actions' => array(
        'select' => '
            event.preventDefault();
            
            var exists = 0 != $("#selectedFields option[value=" + ui.item.data.CRM_naw.id + "]").length;
            if ( exists == false ) {
              var o = new Option(ui.item.data.CRM_naw.zoekveld + " - " + ui.item.data.Portefeuilles.Portefeuille + " - " + ui.item.data.CRM_naw.naam, ui.item.data.CRM_naw.id);
              $(o).html(ui.item.data.CRM_naw.zoekveld + " - " + ui.item.data.Portefeuilles.Portefeuille + " - " + ui.item.data.CRM_naw.naam);
              $("#selectedFields").append(o);
            }
            $("#CRMnaam").val("");
          '
      )
    ),
    'form_size' => '15',
    'form_value' => '',
  )
);

$content['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('CRMnaam');


$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

if ($tabel == 'CRM_naw_dossier') {
  $list->addColumn("Naw_dossier", "id", array("list_width" => "100", "search" => false));
  $list->addColumn("", "checkbox", array('description' => 'copy', "list_width" => "30", "search" => false));
  $list->addColumn("Naw_dossier", "datum", array("list_width" => "100", "search" => false, "list_align" => "left", "form_type" => "calendar"));
  $list->addColumn("Naw_dossier", "kop", array("list_width" => "", "search" => false));
  $list->addColumn("Naw_dossier", "add_user", array("description" => "toegevoegd door", "list_width" => "", "search" => false));
  $_GET['sort'] = array($tabel . ".datum", $tabel . ".id");
}
if ($tabel == 'CRM_naw_kontaktpersoon') {
  $list->addColumn("CRM_naw_kontaktpersoon", "id", array("list_width" => "100", "search" => false));
  $list->addColumn("", "checkbox", array('description' => 'copy', "list_width" => "30", "search" => false));
  $list->addColumn("CRM_naw_kontaktpersoon", "naam", array("list_width" => "", "search" => false));
  $list->addColumn("CRM_naw_kontaktpersoon", "naam1", array("list_width" => "", "search" => false));
  $list->addColumn("", "mail", array("list_width" => "30"));
  $list->addColumn("", "sjabloon", array("list_width" => "60", 'description' => "sjabloon ", 'list_nobreak' => true));
  $list->addColumn("CRM_naw_kontaktpersoon", "functie", array("list_width" => "", "search" => false));
  $list->addColumn("CRM_naw_kontaktpersoon", "tel1", array("list_width" => "100", "search" => false));
  $list->addColumn("CRM_naw_kontaktpersoon", "tel2", array("list_width" => "100", "search" => false));
  $list->addColumn("CRM_naw_kontaktpersoon", "email", array("list_invisible" => "true"));
}
if ($tabel == 'CRM_evenementen') {
  $list->addColumn("CRM_evenementen", "id", array("list_width" => "100", "search" => false));
  $list->addColumn("", "checkbox", array('description' => 'copy', "list_width" => "30", "search" => false));
  $list->addColumn("CRM_evenementen", "evenement", array("list_width" => "300", "search" => false));
}
if ($tabel == 'CRM_naw_adressen') {
  $list->addColumn("CRM_naw_adressen", "id", array("list_width" => "100", "search" => false));
  $list->addColumn("", "checkbox", array('description' => 'copy', "list_width" => "30", "search" => false));
  $list->addColumn("CRM_naw_adressen", "naam", array("list_width" => "100", "search" => false));
  $list->addColumn("CRM_naw_adressen", "naam1", array("list_width" => "100", "search" => false));
  $list->addColumn("", "sjabloon", array("list_width" => "60", 'description' => "sjabloon ", 'list_nobreak' => true));
  $list->addColumn("CRM_naw_adressen", "adres", array("list_width" => "100", "search" => false));
  $list->addColumn("CRM_naw_adressen", "pc", array("list_width" => "100", "search" => false));
  $list->addColumn("CRM_naw_adressen", "plaats", array("list_width" => "100", "search" => false));
}
if ($tabel == 'CRM_naw_rekeningen') {
  $list->addColumn("CRM_naw_rekeningen", "id", array("list_width" => "100", "search" => false));
  $list->addColumn("", "checkbox", array('description' => 'copy', "list_width" => "30", "search" => false));
  $list->addColumn("CRM_naw_rekeningen", "rekening", array("list_width" => "100", "search" => false));
  $list->addColumn("CRM_naw_rekeningen", "bank", array("list_width" => "100", "search" => false));
  $list->addColumn("CRM_naw_rekeningen", "omschrijving", array("list_width" => "300", "search" => false));
}
if ($tabel == 'dd_reference') {
  $list->addColumn("Dd_reference", "id", array("list_width" => "100", "search" => false));
  $list->addColumn("", "checkbox", array('description' => 'copy', "list_width" => "30", "search" => false));
  $list->addColumn("", "toon", array("list_width" => "50", "search" => false));
  $list->addColumn("Dd_reference", "add_date", array("description" => 'Datum', "list_width" => "100", "search" => false));
  $list->addColumn("Dd_reference", "filename", array("list_width" => "200", "search" => false));
  $list->addColumn("Dd_reference", "categorie", array("list_width" => "200", "search" => true));
  $list->addColumn("", "editType", array("list_width" => "30", "list_invisible" => true));
  $list->addColumn("Dd_reference", "dd_id", array("list_width" => "100", "list_invisible" => true));
  $list->addColumn("Dd_reference", "datastore", array("list_width" => "100", "list_invisible" => true));
  $list->addColumn("Dd_reference", "filetype", array("list_width" => "100", "list_invisible" => true));
}

$rel_id = $_GET['relid'];
if ($rel_id > 0) {
  $NAW = new db();
  $q = "SELECT * FROM CRM_naw WHERE id = $rel_id";
  $NAW->SQL($q);
  $nawRec = $NAW->lookupRecord();
  $subHeader .= " <b>" . $nawRec['naam'] . ", " . $nawRec['a_plaats'] . "</b>";
  if ($tabel == 'dd_reference')
    $list->setWhere("module_id = " . $rel_id);
  else
    $list->setWhere("rel_id = " . $rel_id);
}
// default sortering

$_GET['direction'] = array("DESC", "DESC");
// set sort
$list->setOrder($_GET['sort'], $_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'], $allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

$content['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/popup.js\" type=text/javascript></script>";
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
$db = new DB();
$q = "SELECT id, concat(zoekveld,' ',naam,' ',portefeuille) as omschrijving FROM CRM_naw HAVING omschrijving <> '' ORDER BY omschrijving";
$db->SQL($q);
$db->query();
$crmSelectOpties = '';
while ($crmRecord = $db->nextRecord()) {
  $crmSelectOpties .= "<option value=\"" . $crmRecord['id'] . "\" >" . $crmRecord['omschrijving'] . "</option>\n";
}

$koppelObject[1] = new Koppel("CRM_naw", "editForm");//LEFT JOIN Portefeuilles on CRM_naw.portefeuille=Portefeuilles.Portefeuille $join
$koppelObject[1]->addFields("id", "doelRelatie", false, true);
$koppelObject[1]->addFields("naam", "CRMnaam", true, true);
$koppelObject[1]->addFields("portefeuille", "", true, true);
$koppelObject[1]->addFields("zoekveld", "", true, true);
$koppelObject[1]->name = "port";
//$koppelObject[1]->extraQuery = " AND Portefeuilles.einddatum > NOW() $beperktToegankelijk";
$koppelObject[1]->action = "";
//$koppelObject[1]->focus = "";
$content['javascript'] .= "\n" . $koppelObject[1]->getJavascript();
//document.editForm.doelRelatie.value,600,400
echo template($__appvar["templateContentHeader"], $content);

if ($tabel == "dd_reference")
{
?>
  <div style="padding:16px; color:maroon">De gespreksverslagen zullen  niet worden gekopieerd. Deze dienen los gekopieerd te worden.</div>
<?php
}
?>



    <script language="Javascript">
        function removeItem() {
            $('#selectedFields option:selected').remove()
        }

        $(document).ready(function() {
          $(window).keydown(function(event){
            if(event.keyCode == 13) {
              event.preventDefault();
              return false;
            }
          });
        });

        function moveItem(from, to) {
            var tmp_text = new Array();
            var tmp_value = new Array();
            for (var i = 0; i < from.options.length; i++) {
                if (from.options[i].selected) {
                    var blnInList = false;
                    for (j = 0; j < to.options.length; j++) {
                        if (to.options[j].value == from.options[i].value) {
                            //alert("already in list");
                            blnInList = true;
                            break;
                        }
                    }
                    if (!blnInList) {
                        to.options.length++;
                        to.options[to.options.length - 1].text = from.options[i].text;
                        to.options[to.options.length - 1].value = from.options[i].value;
                    }
                } else {
                    tmp_text.length++;
                    tmp_value.length++;
                    tmp_text[tmp_text.length - 1] = from.options[i].text;
                    tmp_value[tmp_text.length - 1] = from.options[i].value;

                }
            }
            from.options.length = 0;
            for (var i = 0; i < tmp_text.length; i++) {
                from.options.length++;
                from.options[from.options.length - 1].text = tmp_text[i];
                from.options[from.options.length - 1].value = tmp_value[i];
            }
            from.selectedIndex = -1;
        }
        //orginele functie
        // function selectSelected() {
        //   console.log('test');
        //     if (document.editForm['inFields[]']) {
        //         var inFields = document.editForm['inFields[]'];
        //         var selectedFields = document.editForm['selectedFields[]'];
        //         for (j = 0; j < selectedFields.options.length; j++) {
        //             selectedFields.options[j].selected = true;
        //         }
        //         document.editForm.submit();
        //     }
        // }

        function selectSelected() {
          console.log('test');
          if (document.editForm['selectedFields[]']) {
            var selectedFields = document.editForm['selectedFields[]'];
            for (j = 0; j < selectedFields.options.length; j++) {
              selectedFields.options[j].selected = true;
            }
            document.editForm.submit();
          }
        }

    </script>


  <form method="POST" name="editForm">
    <input type="hidden" name="verwerk" value="1">
    <table class="list_tabel" cellspacing="0">
      <?= $list->printHeader(true); ?>
      <?php
      while ($data = $list->getRow()) {
        $data['disableEdit'] = true;
        $data['checkbox']['value'] = "<input type=\"checkbox\" name=\"id_" . $data['id']['value'] . "\" value=\"1\">";
        echo $list->buildRow($data);
      }
      ?>
    </table>
    <br><br>
    Kopieren naar:

    <strong>Relatie zoeken <small>(Toevoegen aan selectie)</small></strong><br/>
    <input class="" type="text" size="60" value="" name="CRMnaam" id="CRMnaam">

    <input type="checkbox" id="crmInactief" name="crmInactief" value="0">
    <label for="crmInactief"> Inactief tonen</label><br>

    <script>
      $("#crmInactief").change(function() {
        $('#crmInactief').attr('value', '0');
        if(this.checked) {
          $('#crmInactief').attr('value', '1');
        }
      });
    </script>
    <br/>
    <br/>

    <strong>Geselecteerde relaties</strong><br/>


    <table>
      <tr>
        <td>
          <select id="selectedFields" name="selectedFields[]" multiple size="16" style="min-width : 200px"></select>
        </td>
        <td width="300">
          <a class="btn btn-delete text-white" href="javascript:removeItem();">
            Selectie uit lijst wissen
          </a>
        </td>
      </tr>
    </table>


    <!--  <a href="javascript:select_port('')"> <img src="images/16/lookup.gif" border="0" height="18" align="middle"></a>-->
    <input class="" type="hidden" value="" name="doelRelatie" id="doelRelatie">
    <input class="" type="hidden" value="<?= $tabel ?>" name="tabel" id="tabel">


    <br><br>
    <div class="formlinks"><input type="button" onclick="javascript:selectSelected();" value="Verwerk selectie.">
    </div>
    <div><a href="javascript:checkAll();"> check</a> / <a href="javascript:uncheckAll();"> uncheck</a></div>


  </form>
<?
if ($__debug) {
  echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"], $content);
?>