<?php
/*
    AE-ICT CODEX source module versie 1.6, 20 juni 2011
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2020/05/01 13:50:07 $
    File Versie         : $Revision: 1.15 $

    $Log: fondsOverzichtRapport.php,v $
    Revision 1.15  2020/05/01 13:50:07  rm
    8603

    Revision 1.14  2019/02/15 15:11:15  rm
    Toevoegen Filter voor user en fonds

    Revision 1.13  2018/04/11 15:06:22  rm
    6806

    Revision 1.12  2018/04/06 14:35:42  rm
    6634

    Revision 1.11  2018/04/04 14:35:01  rm
    6634

    Revision 1.10  2017/12/13 13:06:01  rm
    Html rapport

    Revision 1.9  2017/05/10 14:16:14  rm
    Htmlrapport

    Revision 1.8  2017/05/03 13:23:45  rm
    no message

    Revision 1.7  2017/01/18 08:19:15  rm
    Html rapportage

    Revision 1.6  2017/01/13 14:06:19  rm
    Html rapportage

    Revision 1.5  2017/01/11 14:24:48  rvv
    *** empty log message ***

    Revision 1.4  2017/01/10 09:46:40  rm
    Html rapportage

    Revision 1.3  2016/12/22 09:39:52  rm
    Html rapportage

    Revision 1.2  2016/12/19 07:44:15  rm
    Html rapport

    Revision 1.1  2016/12/07 08:47:59  cvs
    call 5469






*/
include_once("init.php");
include_once("../../classes/HTML_rapportList.php");
$grafLineType = "spline";
$data = array_merge($_POST, $_GET, $_SESSION["htmlFondsRapportVars"]);


$subHeader     = "";
$mainHeader    = "";

$editScript = "positielijstEdit.php";
$allow_add  = false;
$__appvar['rowsPerPage'] = 1000;
$list = new rapportList("htmlFondsOverzicht");
$list->postData = $data;
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];
$list->postData = $data;


$list->addColumn("htmlFondsOverzicht","Client",array());
$list->addColumn("htmlFondsOverzicht","Portefeuille",array());
$list->addColumn("htmlFondsOverzicht","Naam",array());

//$list->addColumn("htmlFondsOverzicht","depotbank",array());
$list->addColumn("htmlFondsOverzicht","accountmanager",array());
$list->addColumn("htmlFondsOverzicht","depotbank",array());
$list->addColumn("htmlFondsOverzicht","soortOvereenkomst",array());
$list->addColumn("htmlFondsOverzicht","risicoklasse",array());
$list->addColumn("htmlFondsOverzicht","AantalInPortefeuille",array());
$list->addColumn("htmlFondsOverzicht","AandeelTotaalvermogen",array());
$list->addColumn("htmlFondsOverzicht","AandeelBeleggingscategorie",array());

$list->addColumn("htmlFondsOverzicht","AandeelTotaalBelegdvermogen",array());
$list->addColumn("htmlFondsOverzicht","actueleWaarde",array());

$list->forceWhere  = '
  WHERE Rapport <> "statics"
  AND `add_user` = "' . $USR . '"
  AND `Fonds` = "' . $data['fonds'] . '"
';

//$list->forceWhere  = "WHERE Rapport <> 'statics'";
/** Einde zichtbaarheid */
$db = new DB();
$query = "
  SELECT 
    * 
  FROM 
    `".$list->mainObject->tableName."` 
  WHERE 
    `add_user` = '".$USR."' AND 
    `Rapport` = 'statics'
    AND `Fonds` = '" . $data['fonds'] . "'
  ";

$statRec = $db->lookupRecordByQuery($query);

$statics = unserialize($statRec["memo"]);

$list->setupFilter('htmlFondsOverzichtRapport', array(
//  'groupings' => array ('hoofdcategorie', 'beleggingscategorie'),
  'sortFields' => array('portefeuille'),
  'sortOrder' => array('ASC'),
  'hideOrderBreak' => false
));

$list->setRapportData();

// set searchstring
$list->setSearch((isset ($_GET['selectie']) ? $_GET['selectie'] : null));
// select page
$list->selectPage((isset ($_GET['page']) ? $_GET['page'] : null));

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList((isset ($_GET['page']) ? $_GET['page'] : null), $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch((isset ($_GET['selectie']) ? $_GET['selectie'] : null)));




$aeTemplate = new AE_template();

$content['jsincludes'] .= $aeTemplate->loadJs ('fondsRapport', 'js');
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/dragtable/jquery.dragtable.js\"></script>";
$content['style2'] = '
<link rel="stylesheet" href="../style/fontAwesome/font-awesome.min.css">

';

$templ= template("../".$__appvar["templateContentHeader"],$content);

$templ = str_replace('"javascript', '"../javascript', $templ);
$templ = str_replace('"style', '"../style', $templ);
$templ = str_replace('rel="../style', 'rel="style', $templ);


$db = new DB();
$query = "SELECT * FROM Fondsen WHERE Fonds ='".$data["specifiekeIndex"]."' ";
$fondsRec = $db->lookupRecordByQuery($query);


$query = "SELECT * FROM `Portefeuilles`WHERE `Portefeuilles`.`Portefeuille` = '" . $data['portefeuille']. "'";
$portefeuilleData = $db->lookupRecordByQuery($query);
$portefeuilleData['rapportagevaluta'] = ( empty ($portefeuilleData['rapportagevaluta']) ? 'EUR' : $portefeuilleData['rapportagevaluta'] );

//--- LEFT JOIN `CRM_naw` ON `Portefeuilles`.`Portefeuille` = `CRM_naw`.`portefeuille`
$crmDB = new DB();
$crmQuery = "SELECT * FROM `CRM_naw` WHERE `CRM_naw`.`portefeuille` = '" . $data['portefeuille']. "'";
$crmDB->executeQuery($crmQuery);
$crmData = $crmDB->nextRecord();

$list->postData['currentHtmlRapportUrl'] = 'rapportFrontofficeHtml_fondsoverzicht.php?fonds='.$list->postData['fonds'].'&datum_tot='.date('Y-m-d', strtotime($statics['einddatum']));
$_SESSION['currentHtmlRapportUrl'] = $list->postData['currentHtmlRapportUrl'];

if ( isset($data['type']) && $data['type'] === 'xls' ) {
  $headers = $list->getHeaderExport($portefeuilleData);

  foreach ( $list->filter['groupings'] as $groupingKey => $grouping )
  {
    $list->sortData[$grouping]['headerClass'] = 'header' . ($groupingKey + 1);
  }

  /** pak de 1e en laatste value van de grouping array */
  $list->setOrderBlock(reset($list->filter['groupings']), end($list->filter['groupings']));


  while($listDatas = $list->getRow()) {
    $rowData[] = $list->buildRowExport($listDatas);
  }
  $list->makeXls($headers, $rowData, $list->postData['portefeuille'] . '_FONDS');

  unset($data['type']);
  unset($_POST['type']);
  unset($_SESSION["htmlFondsRapportVars"]['type']);

  exit();
}
echo $templ;
echo $list->getRapportJsCss($content, 'trans');

?>

<br />

  <div class="formHolder box box12">
    <div style="height:40px;" class="formTitle textB">
      <span><?= vt('Rapportage'); ?></span>

      <button class="btn-new btn-default pull-right" id="filterDialogBtn"><i class="fa fa-filter" aria-hidden="true"></i> <?= vt('Filters instellen'); ?></button>
      <?=$rapportBackButtons;?>

      <a href="<?=$__appvar['baseurl'];?>/<?=$list->postData['currentHtmlRapportUrl'];?>&type=xls" target="_blank" class="btn-new btn-default pull-right" id=""><i class="fa fa-file-excel-o" aria-hidden="true"></i> <?= vt('xls export'); ?></a>
    </div>

    <div class="formContent">
      <div style="margin-left:13px; margin-bottom: -3px;">
        <table class="table-striped table-hover">
          <tr>
            <td class="w150 bold"><?= vt('Fonds'); ?></td>
            <td class="w150"><?=$statics['fonds']['Omschrijving'];?></td>
            <td class="w50"></td>
            <td class="w150 bold"><?= vt('ISIN Code'); ?></td>
            <td class="w150"><?=$statics['fonds']['ISINCode'];?></td>

            <td class="w50"></td>
            <td class="w150 bold"><?= vt('Factor'); ?></td>
            <td class="w150"><?=$statics['fonds']['Fondseenheid'];?></td>
          </tr>

          <tr>
            <td class="w150 bold"><?= vt('Rapportagedatum'); ?> </td>
            <td class="w150"><?=date('d-m-Y', db2jul($statics['einddatum']));?></td>
            <td class="w50"></td>
            <td class="w150 bold"><?= vt('Valuta'); ?></td>
            <td class="w150"><?=$statics['fonds']['Valuta'];?></td>
            <td class="w50"></td>
            <td class="w150 bold"><?= vt('Laatste koers'); ?></td>
            <td class="w150"><?=number_format($statics['fonds']['Koers'], 2, ',', '.');?></td>
          </tr>

        </table>
      </div>
    </div>
  </div>


  <div class="box box12">
    <table id="fondsTable" class="table-hover" cellspacing="0" border="0" style="width:100%" >
      <?php
      echo $list->printHeader($portefeuilleData);

      /**
       * Groeperings header opnieuw instellen dmv een class
       */
      foreach ( $list->filter['groupings'] as $groupingKey => $grouping )
      {
        $list->sortData[$grouping]['headerClass'] = 'header' . ($groupingKey + 1);
      }

      /** pak de 1e en laatste value van de grouping array */
      $list->setOrderBlock(reset($list->filter['groupings']), end($list->filter['groupings']));

      $started   = false;
      $benchmarkCumArray = 0;
      $grafDate  = "";


      $lastRecord = array();

      while($listData = $list->getRow())
      {
        $listData['Portefeuille']['value'] = '<a style="text-decoration: underline;color: #337ab7;" href="'.$__appvar['baseurl'].'/rapportFrontofficeClientAfdrukkenHtml.php?rapport_types=VOLK&datum_tot='.date('Y-m-d', $_SESSION["htmlFondsRapportVars"]['datum_tot']).'&Portefeuille='.$listData['Portefeuille']['value'].'">' . $listData['Portefeuille']['value'] . '</a>';
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


  <div id="filterDialog" title="Filter">
    <div id="tabs">
      <ul>
        <li><a href="#tabs-4"><?= vt('Periode'); ?></a></li>
        <li><a href="#tabs-1"><?= vt('kolommen tonen/verbergen'); ?></a></li>
        <li><a href="#tabs-2"><?= vt('Groepering'); ?></a></li>

      </ul>
      <div id="tabs-1" style="padding:0px;">
        <p style="padding-left: 15px;"><?= vt('Vink kolom aan om te tonen en versleep de kolom om de volgorde aan te passen.'); ?></p>

        <form  action="<?=$_SERVER['REQUEST_URI'];?>" method="POST">
          <ul id="sortable"  style="list-style: none;">
            <?php
            foreach ( $list->columns as $colomnData )
            {
              $options = array_merge($list->getPropArray($colomnData['name']),$colomnData["options"]);
              if ( ! isset ($options['hideColumn']) || $options['hideColumn'] === false && ( ! isset ($options['fixed']) || $options['fixed'] === false ) )
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
              $data['group']['first'] = ( isset ($groupings[0]) ? $groupings[0] : null);
              $data['group']['second'] = ( isset ($groupings[1]) ? $groupings[1] : null);
              $data['group']['third'] = ( isset ($groupings[2]) ? $groupings[2] : null);
            }

            foreach ( $list->sortData as $sortKey => $sortValue )
            {
              $select1 .= '<option ' .($data['group']['first'] === $sortKey ? 'selected=selected':'').' value="' . $sortKey . '">' . $list->mainObject->data[$sortKey]['description'] . '</option>';
              $select2 .= '<option ' .($data['group']['second'] === $sortKey ? 'selected=selected':'').' value="' . $sortKey . '">' . $list->mainObject->data[$sortKey]['description'] . '</option>';
              $select3 .= '<option ' .($data['group']['third'] === $sortKey ? 'selected=selected':'').' value="' . $sortKey . '">' . $list->mainObject->data[$sortKey]['description'] . '</option>';
            }
            ?>
            <div class="formblock">
              <div class="formlinks"><label for="groep1" title="Groepering 1"><?= vt('Groepering 1'); ?></label></div>
              <div class="formrechts">
                <select name="group[first]"><?=$select1;?></select>
              </div>
            </div>
            <div class="formblock">
              <div class="formlinks"><label for="group2" title="Groepering 2"><?= vt('Groepering 2'); ?></label></div>
              <div class="formrechts">
                <select name="group[second]"><?=$select2;?></select>
              </div>
            </div>
            <div class="formblock">
              <div class="formlinks"><label for="group3" title="Groepering 3"><?= vt('Groepering 3'); ?></label></div>
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
      <div id="tabs-4" style="padding:0px;">
        <form  action="../rapportFrontofficeHtml_fondsoverzicht.php" method="POST" id="generateForm">
          <input type="hidden" name="fonds" value="<?=$data["fonds"]?>" />
          <div class="padded-10" style="display: inline-block;">
            <br />
            <table border="0">
              <td width="200px" style="border: none; vertical-align: top;">
                <label for="periodeStopDatum"><?= vt('Datum'); ?> </label>
                <br />
                <input size="10" type="text" name="datum_tot" id="periodeStopDatum" class="AIRSdatepicker" value="<?=jul2form($data['datum_tot'])?>"/>
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

  </div>

  <style>
    .extraInfoiArticle{

    }
  </style>




  </article>
  <style>
    .tooltipSpan{
      display: inline-block;
      width: 100px;
    }
  </style>

<?

?>
  <script>

    $(document).ready(function()
    {

      $("#btnGenerate").click(function(e){
        e.preventDefault();
        $("#altFonds").val($("#fonds").val());
        $("#btnGenerate").waitMe({
          effect: 'bounce',
          text: '<?= vt('herberekenen'); ?>',
          bg: 'rgba(255,255,255,.8)',
          color: 'Navy',
          maxSize: 20,
          textPos: 'horizontal',
          fontSize: '14px',
          onClose: function() {}
        });
        $("#generateForm").submit();
      });
      $(".btnValue").click(function(e)
      {
        e.preventDefault();
        $("#periodeStartDatum").val($(this).attr("data-btn"));
      });

      $("#terugBtn").click(function(e)
      {
        var ind = $(this).attr("id");
        console.log(ind);
        window.open("../rapportFrontofficeClientSelectie.php", "content");
      });


      $("#<?=$_GET["interval"]?>").prop('disabled', true);
      $("#<?=$_GET["interval"]?>").addClass('btn-active');

      $( ".AIRSdatepicker" ).datepicker({
        showOn: "button",
        buttonImage: "../javascript/calendar/img.gif",//"images/datePicker.png",
        buttonImageOnly: true,
        dateFormat: "dd-mm-yy",
        dayNamesMin: ["<?= vt('Zo'); ?>", "<?= vt('Ma'); ?>", "<?= vt('Di'); ?>", "<?= vt('Wo'); ?>", "<?= vt('Do'); ?>", "<?= vt('Vr'); ?>", "<?= vt('Za'); ?>"],
        monthNames: ["<?= vt('januari'); ?>","<?= vt('februari'); ?>","<?= vt('maart'); ?>","<?= vt('april'); ?>","<?= vt('mei'); ?>","<?= vt('juni'); ?>","<?= vt('juli'); ?>","<?= vt('augustus'); ?>","<?= vt('september'); ?>","<?= vt('oktober'); ?>","<?= vt('november'); ?>","<?= vt('december'); ?>"],
        monthNamesShort: [ "<?= vt('Jan'); ?>", "<?= vt('Feb'); ?>", "<?= vt('Mrt'); ?>", "<?= vt('Apr'); ?>", "<?= vt('Mei'); ?>", "<?= vt('Jun'); ?>", "<?= vt('Jul'); ?>", "<?= vt('Aug'); ?>", "<?= vt('Sep'); ?>", "<?= vt('Okt'); ?>", "<?= vt('Nov'); ?>", "<?= vt('Dec'); ?>" ],
        nextText: "<?= vt('volgende maand'); ?>",
        prevText: "<?= vt('vorige maand'); ?>",
        currentText: "<?= vt('huidige maand'); ?>",
        changeMonth: true,
        changeYear: true,
        yearRange: '2000:2050',
        closeText: "<?= vt('sluiten'); ?>",
        showAnim: "slideDown",
        showButtonPanel: true,
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 2,
        showWeek: true,
        firstDay: 1
      });








    });


  </script>


<?

logAccess();
if($__debug)
{
  echo getdebuginfo();
}
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>