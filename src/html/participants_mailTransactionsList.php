<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 18 november 2005
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2018/04/16 14:34:43 $
    File Versie         : $Revision: 1.4 $
*/

include_once("wwwvars.php");


include_once("../classes/mysqlList.php");
$data = array_merge($_GET, $_POST);

$AEMessage =  new AE_Message();

if ( isset ($data['markMails']) && (int) $data['markMails'] === 1 ) {
  $db = new DB();
  $query = "UPDATE `participantenFondsVerloop` SET  `participantenFondsVerloop`.`print_date` = NOW() WHERE `participantenFondsVerloop`.`id` IN (" . implode(',', array_values($data['toSend'])) . ")";
  $db->SQL($query);
  $db->Query();
  $AEMessage->setMessage(vt('Geselecteerde items zijn gemarkeerd als verzonden.'));
}


$list = new MysqlList2();

if (!isset ($data['sort']))
{
  $data['sort'][] = "ParticipantenFondsVerloop.datum";
  $data['direction'][] = "DESC";
}


$subHeader = "";
$mainHeader = " overzicht";

$editScript = "";

$allow_add = false;

$list->idField = "id";
$list->editScript = $editScript;
$__appvar['rowsPerPage'] = 96000;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("NAW", "zoekveld", array("list_width" => "300", "search" => true));
$list->addColumn("participanten", "fonds_fonds", array("list_width" => "300", "search" => true,  'list_align' => 'left'));
$list->addFixedField("ParticipantenFondsVerloop", "datum", array("list_width" => "100", "search" => true));
$list->addFixedField("ParticipantenFondsVerloop", "transactietype", array("list_width" => "150", 'list_align' => 'left', "search" => true));
$list->addFixedField("ParticipantenFondsVerloop", "aantal", array("list_width" => "100", "search" => false));

if (isset ($data['showHistory']) && !empty ($data['showHistory']))
{
  $list->addFixedField("ParticipantenFondsVerloop", "print_date", array("list_width" => "100", "search" => false));
}

$list->idTable = "participantenFondsVerloop";
$list->ownTables = array('participantenFondsVerloop');

$list->setJoin(" 
LEFT JOIN participanten on participanten.id = `participantenFondsVerloop`.`participanten_id` 
LEFT JOIN CRM_naw on participanten.crm_id = CRM_naw.id ");

if (isset ($data['showHistory']) && ! empty ($data['showHistory']))
{
  $list->setWhere(' (`participantenFondsVerloop`.`print_date` != "" AND `participantenFondsVerloop`.`print_date` != "0000-00-00 00:00:00")');
}
else
{
  $list->setWhere(' (`participantenFondsVerloop`.`print_date` = "" OR `participantenFondsVerloop`.`print_date` = "0000-00-00 00:00:00")');
}

$list->categorieVolgorde=array('ParticipantenFondsVerloop'=>array('Fonds verloop'), 'participanten'=>array('Algemeen'), 'NAW'=>array('Algemeen'));

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($list->getCustomFields(array('ParticipantenFondsVerloop', 'participanten', 'NAW')), "");


// set sort
$list->setOrder((isset($data['sort'])?$data['sort']:''), (isset($data['direction'])?$data['direction']:''));
// set searchstring
$list->setSearch((isset($data['selectie'])?$data['selectie']:''));
// select page
$list->selectPage((isset($data['page'])?$data['page']:''));


session_start();
$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'], $allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));
session_write_close();


$editcontent['javascript'] .= "
function addRecord() 
{
	parent.frames['content'].location = '" . $editScript . "?action=new';
}


function checkAll (optie)
{
  if ( optie == 1 )
  {
    $('#transactionTable input:checkbox').attr ( 'checked' , true );
  }
  else if ( optie == 0 )
  {
    $('#transactionTable input:checkbox').attr ( 'checked' , false );
  } 
  else if ( optie == -1 )
  {
    $('#transactionTable input[type=checkbox]').each(function () {
      var curElement = $( this );
      var curState = (this.checked ? 1 : 0);
      if ( curState == 1 )
      {
        curElement.attr ( 'checked' , false );
      }
      else 
      {
        curElement.attr ( 'checked' , true );
      }
    });
  }
}

$(document).on('click', '#processExampleMail', function () {
  $('#sendMails').val(0);
  $('#transactionForm').attr('target', '_blank');
  $('#transactionForm').attr('action', 'participants_mailTransactionsProcess.php');
  $('#transactionForm').submit()
});
$(document).on('click', '#processSendMail', function () {
  $('#sendMails').val(1);
  $('#transactionForm').attr('target', '_self');
  $('#transactionForm').attr('action', 'participants_mailTransactionsProcess.php');
  $('#transactionForm').submit()
});

$(document).on('click', '#processMarkedAsSend', function () {
  AEConfirm('Weet u zeker dat u de status van de geselecteerde items wilt aanpassen?', 'Status wijzigen', function () {
    $('#sendMails').val(0);
    $('#markMails').val(1);
    $('#transactionForm').attr('target', '_self');
    $('#transactionForm').attr('action', 'participants_mailTransactionsList.php');
    $('#transactionForm').submit()
  });

  
});


";
echo template($__appvar["templateContentHeader"], $editcontent);
?>
  <h2>Transactie-overzicht</h2>
  <?=$AEMessage->getMessage();?>
  <div class="row padded-10" id="groupSelectie" ">
  <div class="box box12">
    <div class="btn-group" role="group">
      <div class="btn-new btn-default" style="width:150px;float:left;" onclick="checkAll(1);">
        &nbsp;&nbsp;<img src="icon/16/checks.png" class="simbisIcon"> <?= vt('Alles selecteren'); ?>
      </div>
      <div class="btn-new btn-default" style="width:150px;float:left;" onclick="checkAll(0);">
        &nbsp;&nbsp;<img src="icon/16/undo.png" class="simbisIcon"> <?= vt('Niets selecteren'); ?>
      </div>
      <div class="btn-new btn-default" style="width:160px;float:left;" onclick="checkAll(-1);">
        &nbsp;&nbsp;<img src="icon/16/replace2.png" class="simbisIcon"> <?= vt('Selectie omkeren'); ?>
      </div>

      <?php
      if (!isset ($data['showHistory']) || empty ($data['showHistory']))
      {
        echo '<a href="participants_mailTransactionsList.php?showHistory=true" class="btn-new btn-default" style="width:160px;float:left;">&nbsp;&nbsp;<i class="fa fa-history" aria-hidden="true"></i> ' . vt('Geschiedenis tonen') . '</a>';
      }
      else
      {
        echo '<a href="participants_mailTransactionsList.php" class="btn-new btn-default" style="width:250px;float:left;">&nbsp;&nbsp;<i class="fa fa-reply" aria-hidden="true"></i> ' . vt('Terug naar transactie overzicht') . '</a>';
      }
      ?>

    </div>
  </div>
  </div>

  <div class="row padded-10" id="groupSelectie" ">
  <div class="box box12">
    <div class="btn-group" role="group">

      <div id="processExampleMail" class="btn-new btn-default" style="width:150px;float:left;">
        <i class="fa fa-eye" aria-hidden="true"></i> <?= vt('Voorbeeld'); ?>
      </div>
      <?php
      if (!isset ($data['showHistory']) || empty ($data['showHistory']))
      {
        ?>
        <div id="processSendMail" class="btn-new btn-default" style="width:150px;float:left;">
          <i class="fa fa-envelope-o" aria-hidden="true"></i> <?= vt('Verzenden'); ?>
        </div>

        <div id="processMarkedAsSend" class="btn-new btn-default" style="width:200px;float:left;">
          <i class="fa fa-check-square" aria-hidden="true"></i> <?= vt('markeer als verzonden'); ?>
        </div>
      <?php } ?>

    </div>
  </div>
  </div>


  <div class="row padded-10">
    <div class="box box12">

    </div>
  </div>



    <?=$list->filterHeader();?>


  <form id="transactionForm" action="participants_mailTransactionsList.php" method="post" target="">
    <input type="hidden" id="sendMails" name="sendMails" value="0"/>
    <input type="hidden" id="markMails" name="markMails" value="0"/>
    <table class="list_tabel" cellspacing="0" id="transactionTable">
      <?=$list->printHeader(true);?>
      <?php
      while ($data = $list->getRow())
      {
        $data['disableEdit'] = true;
        $data['send']['noClick'] = true;
        $data['send']['tr_style'] = 'style="width:30px;"';
        $data['send']['value'] = '
          <div style="width: 50px;"></div>
          <a target="__blanc" href="participants_mailTransactionsProcess.php?toSend[check_' . $data['id']['value'] . '] = ' . $data['id']['value'] . '"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
          <input type="checkbox" checked value="' . $data['id']['value'] . '" name="toSend[check_' . $data['id']['value'] . ']">
        ';

        echo $list->buildRow($data);
      }
      ?>
    </table>
  </form>
<?
logAccess();
if ($__debug)
{
  echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"], $editcontent);
?>