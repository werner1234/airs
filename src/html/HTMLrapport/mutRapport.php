<?php
/*
    AE-ICT CODEX source module versie 1.6, 20 juni 2011
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/10/24 13:58:30 $
    File Versie         : $Revision: 1.11 $
*/
include_once("init.php");
include_once("../../classes/HTML_rapportList.php");
$data = array_merge($_POST, $_GET, $_SESSION["htmlRapportVars"]);

$editScript = "positielijstEdit.php";
$allow_add  = false;
$__appvar['rowsPerPage'] = 1000;
$list = new rapportList("htmlMUT", $_GET["portefeuille"]);
$list->postData = $data;
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("htmlMut","id",array("search"=>false,"hideColumn"=>true));

$list->addColumn("htmlMut","gbOmschrijving",array());
$list->addColumn("htmlMut","Boekdatum",array());

$list->addColumn("htmlMut","Omschrijving",array());
$list->addColumn("htmlMut","fondsOmschrijving",array());
$list->addColumn("htmlMut","Rekening",array());
$list->addColumn("htmlMut","Debet",array());
$list->addColumn("htmlMut","Credit",array());

$list->addColumn("htmlMut","Valuta",array());
$list->addColumn("htmlMut","rekValuta",array());
$list->addColumn("htmlMut","Valutakoers",array());

$list->addColumn("htmlMut","bedragEUR",array());
$list->addColumn("htmlMut","bedragVV",array());
$list->addColumn("htmlMut","Bedrag",array());

$list->setupFilter('mutHtmlRapport', array(
  'groupings' => array ('gbOmschrijving'),
  'sortFields' => array('gbOmschrijving', 'Boekdatum'),
  'sortOrder' => array('ASC', 'ASC')
));

// set searchstring
$list->setSearch((isset ($_GET['selectie']) ? $_GET['selectie'] : null));
// select page
$list->selectPage((isset ($_GET['page']) ? $_GET['page'] : null));

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList((isset ($_GET['page']) ? $_GET['page'] : null), $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch((isset ($_GET['selectie']) ? $_GET['selectie'] : null)));

$content['pageHeader'] = "<br />";

$content['javascript'] .= "
function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";

$list->postData['allowExport'] = true;
$list->postData['currentHtmlRapportUrl'] = 'rapportFrontofficeClientAfdrukkenHtml.php?rapport_types=MUT&Portefeuille='.$list->postData['portefeuille'].'&datum_van='.$list->postData['start'].'&datum_tot='.$list->postData['stop'];
$_SESSION['currentHtmlRapportUrl'] = $list->postData['currentHtmlRapportUrl'];

$list->setRapportData();

if ( ! isset($data['type']) ) {
  echo $list->getRapportJsCss($content, 'mut');
}

$list->postData['reportDate'] = dbdate2form($list->postData['start']) .' t/m '. dbdate2form($list->postData['stop']);

if ( ! isset($data['type']) ) {
  echo $list->getRapportHeader($rapportBackButtons);
}

if ( isset($data['type']) && $data['type'] === 'xls' ) {
  $headers = $list->getHeaderExport($portefeuilleData);
  while($listDatas = $list->getRow()) {
    $rowData[] = $list->buildRowExport($listDatas);
  }
  $list->makeXls($headers, $rowData, $list->postData['portefeuille'] . '_MUT');
  exit();
}

?>


  <div class="box box12" style="overflow: auto;">

    <div class="wrapper1">
      <div class="div1">
      </div>
    </div>

    <table id="mutTable" class="table-hover" cellspacing="0" border="0" style="display: table; width: 100%;">
      <?php
      echo $list->printHeader($portefeuilleData);

      /**
       * Groeperings header opnieuw instellen dmv een class
       */
      //      debug($list->filter['groupings']);
      foreach ( $list->filter['groupings'] as $groupingKey => $grouping )
      {
        $list->sortData[$grouping]['headerClass'] = 'header' . ($groupingKey + 1);
      }

      /** pak de 1e en laatste value van de grouping array */
      $list->setOrderBlock(reset($list->filter['groupings']), end($list->filter['groupings']));

      $lastRecord = array();
      while($listData = $list->getRow())
      {
        /**
         * Controlleer de order breaks
         */
        $list->checkOrderBreak($listData, $lastRecord);

        $list->sumRowTotals($listData);                                    // totaliseer voor Eindtotaal

        echo $list->buildRow($listData);                                   // print regel
        $lastRecord = $listData;
      }
      $list->orderBreakEnd($lastRecord);                               // print laatste breakfooters
      $list->printTotalFooter($lastRecord);
      ?>
    </table>
  </div>

  <div id="extraInfoPopup" title="Details ">
    <div id="infoTabs">
      <ul>
        <li><a href="#infoTab1" id="titleTab1">mutacties</a></li>
        <li><a href="#infoTab2" id="titleTab2">Dividenden/Coupons</a></li>
        <li><a href="#infoTab3" id="titleTab3">Fondsgegevens</a></li>
        <li><a href="#infoTab4" id="titleTab4">Koersinformatie</a></li>
        <li><a href="#infoTab5" id="titleTab5">Doorkijk</a></li>
      </ul>
      <div id="infoTab1">Laden....</div>
      <div id="infoTab2">Laden....</div>
      <div id="infoTab3">Laden....</div>
      <div id="infoTab4">Laden....</div>
      <div id="infoTab5">Laden....</div>
    </div>
  </div>

  <div id="filterDialog" title="Filter">
    <div id="tabs">
      <ul>
        <li><a href="#tabs-4">Periode</a></li>
        <li><a href="#tabs-1">Kolommen</a></li>
        <li><a href="#tabs-2">Groepering</a></li>
        <li><a href="#tabs-3">Sortering</a></li>
       

      </ul>
      <div id="tabs-1" style="padding:0px;">
        <p style="padding-left: 15px;"><?= vt('Vink kolom aan om te tonen en versleep de kolom om de volgorde aan te passen.'); ?></p>
        <form  action="<?=$_SERVER['REQUEST_URI'];?>" method="POST">
          <ul id="sortable" style="padding-left: 15px; list-style: none;">
            <?php
            foreach ( $list->columns as $colomnData )
            {
              $options = array_merge($list->getPropArray($colomnData['name']),$colomnData["options"]);

              if ( ! empty($options) && ! isset ($options['hideColumn']) || $options['hideColumn'] === false && ( ! isset ($options['fixed']) || $options['fixed'] === false ) )
              {
                echo '<li>
              <input name="columnVisible['.$colomnData['name'].']"  class="showHideFilter" type="hidden" data-colname="'.$colomnData['name'].'" value="off" >
              <input name="columnVisible['.$colomnData['name'].']"  class="showHideFilter" type="checkbox" '.($options['visible'] === true ? 'checked="checked"' : '').' data-colname="'.$colomnData['name'].'" >
              <input name="columnOrder[]" value="'.$colomnData['name'].'"  type="hidden"  >
              ' . strip_tags ($options['description']) . '</li>';
              }
            }
            ?>
          </ul>

          <div class="form-actions">
            <span class="closeFilter btn-new btn-default"><i class="fa fa-times" aria-hidden="true"></i> <?= vt('Sluiten'); ?></span>
            <button name="saveBtn" value="showVisibility" class="btn-new btn-save" type="submit"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?= vt('Opslaan'); ?></button>
            <button name="saveBtn" value="saveVisibility" class="btn-new btn-save" type="submit"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?= vt('Opslaan als standaard'); ?></button>

          </div>

        </form>
      </div>
      <div id="tabs-2" style="padding:0px;">
        <form  action="<?=$_SERVER['REQUEST_URI'];?>" method="POST">
          <div class="padded-10" style="display: inline-block;">
            <?php
            $select1 = '<option value="">---</option>';
            $select2 = '<option value="">---</option>';
            $select3 = '<option value="">---</option>';

            if ( empty ($data['group']) ) {
              $data['group']['first'] = ( isset ($list->filter['groupings'][0]) ? $list->filter['groupings'][0] : null);
              $data['group']['second'] = ( isset ($list->filter['groupings'][1]) ? $list->filter['groupings'][1] : null);
              $data['group']['third'] = ( isset ($list->filter['groupings'][2]) ? $list->filter['groupings'][2] : null);
            }

            foreach ( $list->sortData as $sortKey => $sortValue )
            {
              $select1 .= '<option ' .($data['group']['first'] === $sortKey ? 'selected=selected':'').' value="' . $sortKey . '">' . $list->mainObject->data[$sortKey]['description'] . '</option>';
              $select2 .= '<option ' .($data['group']['second'] === $sortKey ? 'selected=selected':'').' value="' . $sortKey . '">' . $list->mainObject->data[$sortKey]['description'] . '</option>';
              $select3 .= '<option ' .($data['group']['third'] === $sortKey ? 'selected=selected':'').' value="' . $sortKey . '">' . $list->mainObject->data[$sortKey]['description'] . '</option>';
            }
            ?>
            <div class="formblock">
              <div class="formlinks"><label for="groep1" title="Groepering 1"><?= vt('Groepering'); ?> 1</label></div>
              <div class="formrechts">
                <select name="group[first]"><?=$select1;?></select>
              </div>
            </div>
            <div class="formblock">
              <div class="formlinks"><label for="group2" title="Groepering 2"><?= vt('Groepering'); ?> 2</label></div>
              <div class="formrechts">
                <select name="group[second]"><?=$select2;?></select>
              </div>
            </div>
            <div class="formblock">
              <div class="formlinks"><label for="group3" title="Groepering 3"><?= vt('Groepering'); ?> 3</label></div>
              <div class="formrechts">
                <select name="group[third]"><?=$select3;?></select>
              </div>
            </div>
          </div>


          <div class="form-actions">
            <span class="closeFilter btn-new btn-default"><i class="fa fa-times" aria-hidden="true"></i> <?= vt('Sluiten'); ?></span>
            <button name="saveBtn" value="showGrouping" class="btn-new btn-save" type="submit"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?= vt('Opslaan'); ?></button>
            <button name="saveBtn" value="saveGrouping" class="btn-new btn-save" type="submit"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?= vt('Opslaan als standaard'); ?></button>
          </div>
        </form>

      </div>
      <div id="tabs-3" style="padding:0px;">
        <form  action="<?=$_SERVER['REQUEST_URI'];?>" method="POST">
          <div class="padded-10" style="display: inline-block;">
            <?php
            $sortFieldSelect1 = '<option value="">---</option>';
            $sortFieldSelect2 = '<option value="">---</option>';
            if ( empty ($data['sort']) ) {
              $data['sort']['first'] = ( isset ($sortFields[0]) ? $sortFields[0] : null);
              $data['sort']['second'] = ( isset ($sortFields[1]) ? $sortFields[1] : null);
            }
            if ( empty ($data['sortOrder']) ) {
              $data['sortOrder']['first'] = ( isset ($sortOrder[0]) ? $sortOrder[0] : null);
              $data['sortOrder']['second'] = ( isset ($sortOrder[1]) ? $sortOrder[1] : null);
            }

            $sortableFieldList = $list->getSortableFields ();
            foreach ( $sortableFieldList as $sortField => $sortFieldData )
            {
              $sortFieldSelect1 .= '<option ' .($data['sort']['first'] === $sortField ? 'selected=selected':'').' value="' . $sortField . '">' . $sortFieldData['descriptionShort'] . '</option>';
              $sortFieldSelect2 .= '<option ' .($data['sort']['second'] === $sortField ? 'selected=selected':'').' value="' . $sortField . '">' . $sortFieldData['descriptionShort'] . '</option>';
            }


            $sortFiel1 = '<option value="">---</option>';
            $sortFiel2 = '<option value="">---</option>';
            foreach ( array( 'ASC' => 'A->Z', 'DESC' => 'Z->A') as $key => $value ) {
              $sortFiel1 .= '<option ' . ($data['sortOrder']['first'] === $key ? 'selected=selected':'').' value="' . $key . '">' . $value . '</option>';
              $sortFiel2 .= '<option ' . ($data['sortOrder']['second'] === $key ? 'selected=selected':'').' value="' . $key . '">' . $value . '</option>';
            }

            ?>
            <div class="formblock">
              <div class="formlinks"><label for="sort1" title="Sortering 1"><?= vt('Sortering'); ?> 1</label></div>
              <div class="formrechts">
                <select name="sort[first]"><?=$sortFieldSelect1;?></select>
                <select name="order[first]"><?=$sortFiel1;?></select>
              </div>
            </div>
            <div class="formblock">
              <div class="formlinks"><label for="sort2" title="Sortering 2"><?= vt('Sortering'); ?> 2</label></div>
              <div class="formrechts">
                <select name="sort[second]"><?=$sortFieldSelect2;?></select>
                <select name="order[second]"><?=$sortFiel2;?></select>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <span class="closeFilter btn-new btn-default"><i class="fa fa-times" aria-hidden="true"></i> <?= vt('Sluiten'); ?></span>
            <button name="saveBtn" value="showSorting" class="btn-new btn-save" type="submit"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?= vt('Opslaan'); ?></button>
            <button name="saveBtn" value="saveSorting" class="btn-new btn-save" type="submit"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?= vt('Opslaan als standaard'); ?></button>
          </div>
        </form>

      </div>

      <div id="tabs-4" style="padding:0px;">
        <form  action="../rapportFrontofficeClientAfdrukkenHtml.php" method="POST" id="generateForm">
          <input type="hidden" name="Portefeuille" value="<?=$data["portefeuille"]?>" />
          <input type="hidden" name="rapport_types" value="MUT" />

          <div class="padded-10" style="display: inline-block;">
            <br />
            <table border="0">
              <tr>
                <td width="200px" style="border: none; vertical-align: top;">
                  <label for="periodeStartDatum">Vanaf datum</label>
                  <br />
                  <input size="10" type="text" name="datum_van" id="periodeStartDatum" class="AIRSdatepicker" value="<?=jul2form(db2jul($data['start']))?>"/>
                  <br /><br />
                  <button class="btn-new btnValue" data-btn="<?=jul2form(db2jul($list->postData['Startdatum']))?>"><?= vt('Vanaf start'); ?></button>
                  <button class="btn-new btnValue" data-end="<?=date('d-m-Y');?>" data-btn="01-01-<?=date("Y")?>" ><?= vt('YTD'); ?></button>
                </td>
                <td width="200px" style="border: none; vertical-align: top;">
                  <label for="periodeStopDatum"><?= vt('Tot datum'); ?> </label>
                  <br />
                  <input size="10" type="text" name="datum_tot" id="periodeStopDatum" class="AIRSdatepicker" value="<?=jul2form(db2jul($data['stop']))?>"/>
                  <br /><br /><br />
                </td>
              </tr>
            </table>
            <br />
          </div>

          <div class="form-actions">
            <span class="closeFilter btn-new btn-default"><i class="fa fa-times" aria-hidden="true"></i> <?= vt('Sluiten'); ?></span>
            <button id="btnGenerate" class="btn-new btn-save" ><i class="fa fa-floppy-o" aria-hidden="true"></i> <?= vt('Rapport opnieuw genereren'); ?></button>
        </form>
      </div>
    </div>


  </div>

  </div>



<?

logAccess();
if($__debug)
{
  echo getdebuginfo();
}
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>