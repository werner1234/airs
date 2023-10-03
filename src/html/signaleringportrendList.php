<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 25 november 2017
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2020/04/15 14:12:43 $
    File Versie         : $Revision: 1.17 $
 		
    $Log: signaleringportrendList.php,v $
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "signaleringportrendEdit.php";
$allow_add  = false;


if ( isset ($_GET['rendID']) && requestType('ajax') === true ) {
  $AEJson = new AE_Json();
  $rendementDetailsHtml = '';
  $success = false;
  
  $signaleringPortRend = new signaleringPortRend ();
  $rendementDetails = $signaleringPortRend->parseById( (int) $_GET['rendID'], 'rendementDetails');
  
  if ( ! empty ($rendementDetails) ) {
    
    $rendementDetails = unserialize($rendementDetails);
    
    $opbrengsten = '';
    foreach ( $rendementDetails['opbrengsten'] as $key => $value ) {
      if(round($value,2) != 0.00) {
        $opbrengsten .= '<tr><td>' . vertaalTekst($key) . '</td><td class="numberRight">' . number_format($value,2,",",".") . '</td><tr>';
      }
    }
    $kosten = '';
    foreach ( $rendementDetails['kosten'] as $key => $value ) {
      if (round($value, 2) != 0.00)	{
        $kosten .= '<tr><td>' . vertaalTekst($key) . '</td><td class="numberRight">' . number_format($value,2,",",".") . '</td><tr>';
      }
    }
    
    $rendementDetailsHtml = '
			<table style="width:100%">
				<tr>
					<td colspan="2"><strong>' . vertaalTekst("Resultaat verslagperiode") . '</strong><td>
				</tr>
				<tr>
					<td>' . vertaalTekst("Waarde portefeuille per") . ' ' . date("j", strtotime($rendementDetails['datumBegin'])) . ' ' . vertaalTekst($__appvar["Maanden"][date("n",strtotime($rendementDetails['datumBegin']))]). ' ' . date("Y",strtotime($rendementDetails['datumBegin'])) . '</td>
					<td class="numberRight">' . number_format($rendementDetails['beginwaarde'],2,",",".") . '</td>
				</tr>
				<tr>
					<td>' . vertaalTekst("Waarde portefeuille per") . ' ' . date("j",strtotime($rendementDetails['datumEind'])) . ' ' .  vertaalTekst($__appvar["Maanden"][date("n",strtotime($rendementDetails['datumEind']))]) . ' ' .  date("Y",strtotime($rendementDetails['datumEind'])) . '</td>
					<td class="singleBorder numberRight">' . number_format($rendementDetails['eindwaarde'],2,",",".")	. '</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>' . vertaalTekst("Mutatie waarde portefeuille") . '</td>
					<td class="numberRight">' . number_format($rendementDetails['mutatiewaarde'],2,",",".")  . '</td>
				</tr>
				<tr>
					<td>' . vertaalTekst("Totaal stortingen gedurende verslagperiode") . '</td>
					<td class="numberRight">' . number_format($rendementDetails['stortingen'],2,",",".")  . '</td>
				</tr>
				<tr>
					<td>' . vertaalTekst("Totaal onttrekkingen gedurende verslagperiode") . '</td>
					<td class="singleBorder numberRight">' . number_format($rendementDetails['onttrekkingen'],2,",",".")  . '</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>' . vertaalTekst("Resultaat over verslagperiode") . '</td>
					<td class="doubleBorder numberRight">' . number_format($rendementDetails['resultaat'],2,",",".")  . '</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>' . vertaalTekst("Rendement over verslagperiode") . '</td>
					<td class="doubleBorder numberRight">' . number_format($rendementDetails['signaleringsPercentage'],2,",",".")  . '</td>
					<td>%</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="2"><strong>' . vertaalTekst("Samenstelling resultaat over verslagperiode") . '</strong></td>
				</tr>
				<tr>
					<td colspan="2"><strong>' . vertaalTekst("Beleggingsresultaat") . '</strong></td>
				</tr>
				<tr>
					<td>' . vertaalTekst("Ongerealiseerde koersresultaten") . '</td>
					<td class="numberRight">' . number_format($rendementDetails['ongerealiseerd'],2,",",".")  . '</td>
				</tr>
				<tr>
					<td>' . vertaalTekst("Gerealiseerde koersresultaten") . '</td>
					<td class="numberRight">' . number_format($rendementDetails['gerealiseerdKoersresultaat'],2,",",".")  . '</td>
				</tr>
				<tr>
					<td>' . vertaalTekst("Resultaat opgelopen rente") . '</td>
					<td class="numberRight numberRight">' . number_format($rendementDetails['opgelopenRente'],2,",",".")  . '</td>
				</tr>
				' . ( round($rendementDetails['valutaResultaat'],2) != 0.00 ? '<tr><td>' . vertaalTekst("Koersresultaten valuta's") . '</td><td class="numberRight">' . number_format($rendementDetails['valutaResultaat'],2,",",".") . '</td></tr>' : '') . '

				' . $opbrengsten . '
				<tr>
					<td></td>
					<td></td>
					<td class="numberRight numberRight">' . number_format($rendementDetails['opbrengstenTotaal'],2,",",".")  . '</td>
				</tr>

				<tr>
					<td colspan="2"><strong>' . vertaalTekst("Kosten") . '</td>
				</tr>
				' . $kosten . '
				<tr>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td></td><td></td><td class="singleBorder numberRight">' . number_format($rendementDetails['kostenTotaal'],2,",",".") . '</td>
				</tr>
				<tr>
					<td></td><td></td><td class="doubleBorder numberRight">' . number_format($rendementDetails['opbrengstenTotaal'] + $rendementDetails['kostenTotaal'],2,",",".") . '</td>
				</tr>

			</table>
		';
    $success = true;
  }
  
  
  
  
  echo $AEJson->json_encode(
    array(
      'success' => $success,
      'content' => $rendementDetailsHtml
    )
  );
  
  exit();
}


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
  
  if($_POST['actie']=='emails')
  {
    $query = "SELECT CRM_naw.id as crmId,CRM_naw.naam,CRM_naw.email, signaleringPortRend.*
    FROM signaleringPortRend 
    LEFT JOIN CRM_naw ON signaleringPortRend.portefeuille=CRM_naw.portefeuille 
    WHERE signaleringPortRend.id IN('" . implode("','", $ids) . "') ";
    $db->SQL($query);
    $db->query();
    $emailRecords=array();
    while ($data = $db->nextRecord())
    {
      $emailRecords[]=$data;
    }
    $aeconfig=new AE_config();
    $AEMailTemplate = new AE_MailTemplate();
    $ordersConformMailBody = $aeconfig->getData('WaardedalingPortefeuilleMailBody');
    $ordersConformMailSubject = $aeconfig->getData('WaardedalingPortefeuilleMailSubject');
    
    $aantalMails=0;
    foreach($emailRecords as $emailData)
    {
      if($emailData['email']=='')
      {
        echo vt("geen email adres aanwezig bij ").$emailData['portefeuille'].". <br>\n";
        continue;
      }
      
      $mailBody=$ordersConformMailBody;
      $mailSubject=$ordersConformMailSubject;
      
      $crmObj = new Naw();
      //CRM gegevens ophalen
      $mailData=$emailData;
      if($emailData['portefeuille']<>'')
      {
        $crmNawData = $crmObj->parseBySearch(array('portefeuille' => $emailData['portefeuille']));
        foreach ($crmNawData as $key => $value)
          $mailData[$key] = $value;
        
        //portefeuille gegevens ophalen
        $portefeuilleObj = new Portefeuilles();
        $portefeuilleData = $portefeuilleObj->parseBySearch(array('Portefeuille' => $mailData['portefeuille']));
        foreach ($portefeuilleData as $key => $value)
        {
          $mailData[$key] = $value;
        }
        
      }
      $lpw = new laatstePortefeuilleWaarde();
      $lpwData = $lpw->parseBySearch(array ('portefeuille' => $emailData['portefeuille']));
      foreach ($lpwData as $key => $value) $mailData[$key] = $value;
      
      
      $mailData = $AEMailTemplate->getExtraFields($mailData);
      
      $mailData['emailHandtekening'] = $_SESSION['usersession']['gebruiker']['emailHandtekening'];
      $mailData['huidigeDatum']=date("j")." ".$__appvar["Maanden"][date("n")]." ".date("Y");
      $mailData['huidigeGebruiker']=$USR;
      $mailData['signaleringsPercentage']=$emailData['signaleringsPercentage'];
      $mailData['signaleringsDatum']=date('d-m-Y',db2jul($emailData['datum']));
      $mailData['datum']=date('d-m-Y',db2jul($emailData['datum']));
      
      $query="SELECT Naam,titel FROM Gebruikers WHERE Gebruiker='".$USR."'";
      $db = new DB();
      $db->SQL($query);
      $dataGebr=$db->lookupRecord();
      $mailData['GebruikerNaam']=$dataGebr['Naam'];
      $mailData['GebruikerTitel']=$dataGebr['titel'];
      
      $AEMailTemplate->setData($mailData);
      $mailBody = $AEMailTemplate->ParseData($mailBody);
      $mailSubject = $AEMailTemplate->ParseData($mailSubject);
//      Oude manier vervangen door bovenste 3 regels
//			foreach ( $mailData as $key => $val )
//			{
//				$mailSubject  = str_replace("[" . $key . "]", $val, $mailSubject );
//				$mailBody= str_replace("[" . $key . "]", $val, $mailBody);
//			}
      
      $fields = array('crmId'         => $emailData['crmId'],
                      'status'        => 'aangemaakt',
                      'senderName'    => $_SESSION['usersession']['gebruiker']['Naam'],
                      'senderEmail'   => $_SESSION['usersession']['gebruiker']['emailAdres'],
                      'ccEmail'       => '',
                      'bccEmail'      => '',
                      'receiverName'  => $emailData['name'],
                      'receiverEmail' => $emailData['email'],
                      'subject'       => $mailSubject,
                      'bodyHtml'      => $mailBody);
      $query = "INSERT INTO emailQueue SET add_date=now(),add_user='$USR',change_date=now(),change_user='$USR'";
      foreach ($fields as $key => $value)
      {
        $query .= ",$key='" . mysql_escape_string($value) . "'";
      }
      $db->SQL($query);
      if($db->Query())
        $aantalMails++;
      $query="UPDATE signaleringPortRend set status=1,change_date=now(),change_user='$USR' WHERE id='".$emailData['id']."' AND status=0";
      $db->SQL($query);
      $db->Query();
    }
  }
  elseif($_POST['actie']=='verwijderen')
  {
    $query="UPDATE signaleringPortRend set status=2,change_date=now(),change_user='$USR' WHERE signaleringPortRend.id IN('" . implode("','", $ids) . "') AND status=0";
    $db->SQL($query);
    $db->Query();
  }
  elseif($_POST['actie']=='zelf')
  {
    $query="UPDATE signaleringPortRend set status=3,change_date=now(),change_user='$USR' WHERE signaleringPortRend.id IN('" . implode("','", $ids) . "') AND status=0";
    $db->SQL($query);
    $db->Query();
  }
  elseif($_POST['actie']=='negeren')
  {
    $query="UPDATE signaleringPortRend set status=4,change_date=now(),change_user='$USR' WHERE signaleringPortRend.id IN('" . implode("','", $ids) . "') AND status=0";
    $db->SQL($query);
    $db->Query();
  }
}


$list = new MysqlList2();
$list->idField = "id";
$list->idTable='signaleringPortRend';
$list->editScript = $editScript;
$__appvar['rowsPerPage']=1000;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("SignaleringPortRend","id",array("list_width"=>"100","search"=>false));
$list->addFixedField("SignaleringPortRend","portefeuille",array("list_width"=>"100","search"=>false));
$list->addFixedField("SignaleringPortRend","periode",array("list_width"=>"100","search"=>false));
$list->addFixedField("SignaleringPortRend","signaleringsPercentage",array("list_width"=>"100","search"=>false));
$list->addFixedField("SignaleringPortRend","datum",array("list_width"=>"100","search"=>false));
$list->addFixedField("SignaleringPortRend","status",array("list_width"=>"100","search"=>false));
$list->addFixedField("SignaleringPortRend","add_date",array("list_width"=>"100","search"=>false));
$list->addFixedField("SignaleringPortRend","add_user",array("list_width"=>"100","search"=>false));
$list->addFixedField("SignaleringPortRend","change_date",array("list_width"=>"100","search"=>false));
$list->addFixedField("SignaleringPortRend","change_user",array("list_width"=>"100","search"=>false));
$list->addFixedField("SignaleringPortRend","rendementDetails",array("list_width"=>"100","search"=>false, "list_invisible"=>true));


$list->categorieVolgorde=array('SignaleringPortRend'=>array('Algemeen'),
                               'Portefeuilles'=>array('Gegevens','Beheerfee','Staffels'),
                               'laatstePortefeuilleWaarde'=>array('Algemeen'),
                               'NAW'=>array("Algemeen","Adres","Verzendadres","Telefoon","Internetgegevens","Bedrijfinfo","Persoonsinfo","Legitimatie","Informatie partner","Legitimatie partner","Adviseurs","geen",'Contract','Beleggen','Rapportage','Profiel','Relatie geschenk','Recordinfo'));

$html = $list->getCustomFields(array('SignaleringPortRend','Portefeuilles','laatstePortefeuilleWaarde','NAW'),'SignaleringPortRendList');

$status=intval($_GET['status']);
$_SESSION['submenu'] = New Submenu();
$statusOpties=array(0=>vt('Nieuw'),1=>vt('Per mail Airs'),3=>vt('Zelf verstuurd'),4=>vt('Negeren'),2=>vt('Verwijderd'),'-1'=>vt('Alle'));
$_SESSION['submenu']->addItem(vt('Status:'),'');
foreach($statusOpties as $key=>$value)
  $_SESSION['submenu']->addItem($value,'signaleringportrendList.php?status='.$key);

if($status==0 && GetCRMAccess(2))
{
  $_SESSION['submenu']->addItem("<br>", '');
  $_SESSION['submenu']->addItem(vt('verwerk selectie:'), '');
  $_SESSION['submenu']->addItem(vt("Emails aanmaken"), 'javascript:parent.frames[\'content\'].verwerkSelectie(\'emails\');');
  $_SESSION['submenu']->addItem(vt("Zelf verstuurd"), 'javascript:parent.frames[\'content\'].verwerkSelectie(\'zelf\');');
  $_SESSION['submenu']->addItem(vt("Negeren"), 'javascript:parent.frames[\'content\'].verwerkSelectie(\'negeren\');');
  $_SESSION['submenu']->addItem(vt("Verwijderen"), 'javascript:parent.frames[\'content\'].verwerkSelectie(\'verwijderen\');');
}
$_SESSION['submenu']->addItem($html,"");


if(!checkAccess('portefeuille'))
{
//  $_SESSION['usersession']['gebruiker']['Accountmanager']='AURZL';
//  $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] =0;
  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
  {
    $rechtenJoin=" JOIN Portefeuilles ON signaleringPortRend.Portefeuille=Portefeuilles.Portefeuille
     LEFT Join VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR' ";
    $beperktToegankelijk = "OR ((Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."') AND Portefeuilles.consolidatie<2) ";
 
  }
  else
  {
    $rechtenJoin=" LEFT JOIN Portefeuilles ON signaleringPortRend.Portefeuille=Portefeuilles.Portefeuille ";
    $rechtenJoin.=" LEFT JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
							     LEFT JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker";
    $beperktToegankelijk = "OR ( (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) AND Portefeuilles.consolidatie<2 )";
  }
  
  $joinPortefeuilles="$rechtenJoin
  LEFT JOIN laatstePortefeuilleWaarde ON signaleringPortRend.Portefeuille = laatstePortefeuilleWaarde.Portefeuille ";

  $rechtenWhere="AND ( Portefeuilles.id is NULL $beperktToegankelijk )";
}
else
{
  $joinPortefeuilles="LEFT JOIN Portefeuilles ON signaleringPortRend.Portefeuille = Portefeuilles.Portefeuille
  LEFT JOIN laatstePortefeuilleWaarde ON signaleringPortRend.Portefeuille = laatstePortefeuilleWaarde.Portefeuille
  LEFT Join VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR'  ";
}



$joinNaw="LEFT JOIN CRM_naw ON signaleringPortRend.Portefeuille = CRM_naw.portefeuille ";

$list->ownTables=array('signaleringPortRend');
$list->setJoin("$joinPortefeuilles $joinNaw");
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
    var answer = confirm('Wilt u ' + numberSelected  + ' records verwerken?');
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



<!-- Modal -->
<div class="modal fade" id="dialogBox" tabindex="-1" role="dialog" aria-labelledby="dialogBox" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="exampleModalLabel"><?= vt('Overzicht'); ?></h2>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= vt('Sluiten'); ?></button>
      </div>
    </div>
  </div>
</div>


<br>

<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
  <?=$list->printHeader();?>
  
  <form name="listForm" method="POST">
    <input type='hidden' name='actie' value='' >
    <input type='hidden' name='idList' value='' >
    
    <?php
    while( $data = $list->getRow() ) {
      
      $showPortRend = false;
      if ( ! empty ($data['signaleringPortRend.rendementDetails']['value']) ) {
        $showPortRend = true;
      }
      
      $id=$data['id']['value'];
      $list->editIconExtra="<input type='checkbox' name='check_".$id."' value='1'>";
      
      if ( $showPortRend === true ) {
        $list->editIconExtra .= '<span class="openDiag" data-id="' . $id . '" id="openDiag_'.$id.'"><i class="fa fa-eye" aria-hidden="true"></i></span>';
      }
      $data['signaleringPortRend.status']['value']=$data['signaleringPortRend.status']['form_options'][$data['signaleringPortRend.status']['value']];
      //listarray($data);
      echo $list->buildRow($data);
    }
    ?>
  </form>
</table>


<script>
  $(function() {
    
    $(".openDiag").click(function(e) {
      $('.modal-body').html('');
      e.preventDefault();
      
      $.ajax({
        url : 'signaleringportrendList.php?rendID=' + $(this).data('id'),
        type: "GET",
        dataType: 'json',
        success:function(data, textStatus, jqXHR) {
          if ( data.success === true ) {
            $('.modal-body').html(data.content);
            $('#dialogBox').modal('show');
          } else {
          
          }
        }
      });
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
