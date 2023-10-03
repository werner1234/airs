<?php
/*
    AE-ICT CODEX source module versie 1.6, 31 mei 2006
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2018/12/19 15:45:01 $
    File Versie         : $Revision: 1.11 $

    $Log: ordersConfirmMailSettings.php,v $
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/AE_MailTemplate.php");

$__debug = false;

$AETemplate = new AE_template();
$aeconfig = new AE_config();
$AEMessage = new AE_Message();
$AEMailTemplate = new AE_MailTemplate();

$data = array_merge($_POST, $_GET);

if ( isset ($data['emailBodyTest']) && ! empty ($data['emailBodyTest']) )
{
  $orderData=array();
  $orderRegelData=array();
  $ordersConformMailData=array();
  if($data['orderId']>0)
  {
    $orderObj = new OrdersV2();
    $orderData = $orderObj->parseBySearch(array('id' => $data['orderId']));

    $orderRegelObj = new OrderRegelsV2();
    $orderRegelData = $orderRegelObj->parseBySearch(array('orderid' => $data['orderId']));
  }

  $crmObj = new Naw();
  $orderRedenen = new Orderredenen();
  //CRM gegevens ophalen
  if($orderRegelData['portefeuille'] <> '')
  {
    $crmNawData = $crmObj->parseBySearch(array('portefeuille' => $orderRegelData['portefeuille']));
    foreach ($crmNawData as $key => $value)
    {
      $ordersConformMailData[$key] = $value;
    }

    //portefeuille gegevens ophalen
    $portefeuilleObj = new Portefeuilles();
    $portefeuilleData = $portefeuilleObj->parseBySearch(array('Portefeuille' => $orderRegelData['portefeuille']));
    foreach ($portefeuilleData as $key => $value)
    {
      $ordersConformMailData[$key] = $value;
    }
  }
  foreach ( $orderData as $key => $value ) {
    $ordersConformMailData[$key] = $value;
    $ordersConformMailData[$orderObj->data['table'] . '.' . $key] = $value;
  }
  foreach ( $orderRegelData as $key => $value ) {
    $ordersConformMailData[$key] = $value;
    $ordersConformMailData[$orderRegelObj->data['table'] . '.' . $key] = $value;
  }

  //Fonds gegevens ophalen
  if($ordersConformMailData['fonds']<>'')
  {
    $fondsen = new Fonds();
    $fondsData = $fondsen->parseBySearch(array('Fonds' => $ordersConformMailData['fonds']));
    foreach ($fondsData as $key => $value)
    {
      $ordersConformMailData[$key] = $value;
    }
  }
  $orderRedenen = new Orderredenen();

  $orderRedenData = $orderRedenen->parseBySearch(array( 'orderreden' => $orderRegelData['orderReden']), 'all', null, 1);
  if(is_object($orderRegelObj))
    $controleRegelArray=$orderRegelObj->createCheckHtml(unserialize($ordersConformMailData['controleRegels']),true,true);

  $ordersConformMailData['controleRegels'] = '<table class="table table-boxed" id="">' .$controleRegelArray['html']. '</table>';
  $actieveChecks=getActieveControles();
  foreach($actieveChecks as $key=>$value)
  {
    $ordersConformMailData['controle' . ucfirst($key)] = '';
    $ordersConformMailData['controle' . ucfirst($key).'2'] = '';
  }
  foreach($controleRegelArray['htmlMailLos'] as $key=>$htmlValue)
    $ordersConformMailData['controle'.ucfirst($key)] = '' .$htmlValue. '';
  foreach($controleRegelArray['htmlMailLos2'] as $key=>$htmlValue)
    $ordersConformMailData['controle'.ucfirst($key).'2'] = '' .$htmlValue. '';
  $ordersConformMailData['emailHandtekening'] = $_SESSION['usersession']['gebruiker']['emailHandtekening'];
  $ordersConformMailData['orderReden'] = ( isset($orderRedenData['omschrijving']) ? $orderRedenData['omschrijving'] : '' );
  $ordersConformMailData['transactieSoort'] = $__ORDERvar['transactieSoort'][$ordersConformMailData['transactieSoort']];
  $ordersConformMailData['transactieType'] = $__ORDERvar['transactieType'][$ordersConformMailData['transactieType']];
  $ordersConformMailData['huidigeDatum']=date("j")." ".$__appvar["Maanden"][date("n")]." ".date("Y");
  $ordersConformMailData['huidigeGebruiker']=$USR;

  $query="SELECT Naam,titel FROM Gebruikers WHERE Gebruiker='".$USR."'";
  $db = new DB();
  $db->SQL($query);
  $dataGebr=$db->lookupRecord();
  $ordersConformMailData['GebruikerNaam']=$dataGebr['Naam'];
  $ordersConformMailData['GebruikerTitel']=$dataGebr['titel'];
  $ordersConformMailData = $AEMailTemplate->getExtraFields($ordersConformMailData);

  $ordersConformMailData['add_date'] = $orderRegelData['add_date'];
  $ordersConformMailData['change_date'] = $orderRegelData['change_date'];
  $AEMailTemplate->setData($ordersConformMailData);
  $data['emailBodyTest'] = $AEMailTemplate->ParseData($data['emailBodyTest']);
  $data['emailSubjectTest'] = $AEMailTemplate->ParseData($data['emailSubjectTest']);
  
  
  $mail = new PHPMailer();
  $mail->IsSMTP();
  $mail->SMTPDebug=true;

  $mail->From     = $_SESSION['usersession']['gebruiker']['emailAdres'];
  $mail->FromName = $_SESSION['usersession']['gebruiker']['Naam'];

  $mail->Subject = $data['emailSubjectTest'];
  $mail->Body    = $data['emailBodyTest'];

  $mail->AltBody = html_entity_decode(strip_tags('body'));
  $mail->AddAddress($data['emailTo']);

  if( $mail->Send() ) {
    $AEMessage->setFlash(vt('Bericht is verzonden'), 'Error');
  } else {
    $AEMessage->setFlash(vt('kan bericht:kon niet worden verstuurd'), 'Error');
  }

  header("Location: ordersConfirmMailSettings.php");
  exit();
}


//debug($editcontent);
echo $editcontent['style'];
//echo $editcontent['style'];
echo $AEMessage->getFlash();

function getFields()
{

  $categorieVolgorde=array(
    'Naw'=>array(
      "Algemeen","Adres","Verzendadres","Telefoon","Internetgegevens","Bedrijfinfo",
      "Persoonsinfo",
      "Legitimatie",
//      "Informatie partner",
//      "Legitimatie partner",
//      "Adviseurs",
//      "geen",
//      'Contract',
      'Beleggen',
      'Rapportage',
      'Profiel',
//      'Relatie geschenk'
    ),
    'Portefeuilles'=>array(
      'Gegevens'
//    ,'Beheerfee','Staffels'
    ),
    'OrdersV2'=>array(
      'Orders'
    ),
    'OrderRegelsV2'=>array(
      'OrderRegels'
    ),
    'Fonds'=>array(
      'Fonds'
    ),
   'Speciale velden'=>array(
     'Opmaak'
   )
  );


  $OrdersV2Obj = new OrdersV2();
  foreach ($OrdersV2Obj->data['fields'] as $key => $values) {
    if ( $values['categorie'] ) {
      $velden['Orders'][$key]=$values;
    }
  }
  $OrderRegelsV2Obj = new OrderRegelsV2();
  foreach ($OrderRegelsV2Obj->data['fields'] as $key => $values) {
    if ( $values['categorie'] ) {
      $velden['OrderRegels'][$key] = $values;
    }
  }

  $actieveChecks=getActieveControles();
  foreach($actieveChecks as $key=>$value)
    $velden['OrderRegels']['controle'.ucfirst($key)]=array('description'=>'controle'.ucfirst($key));

  $velden['Opmaak']['huidigeDatum']=array('description'=>vt('De huidige datum.'));
  $velden['Opmaak']['huidigeGebruiker']=array('description'=>vt('De huidige gebruiker.'));
  $velden['Opmaak']['GebruikerNaam']=array('description'=>vt('Naam huidige gebruiker.'));
  $velden['Opmaak']['GebruikerTitel']=array('description'=>vt('Titel huidige gebruiker.'));
  $velden['Opmaak']['emailHandtekening']=array('description'=>vt('Handtekening huidige gebruiker.'));


  $portefeuille = new Portefeuilles();
  foreach ($portefeuille->data['fields'] as $key=>$values)
  {
    $velden[$values['categorie']][$key]=$values;
  }
  $naw = new Naw();
  foreach ($naw->data['fields'] as $key=>$values)
  {
    $velden[$values['categorie']][$key]=$values;
  }

  $naw = new Fonds();
  foreach ($naw->data['fields'] as $key=>$values)
  {
    $velden['Fonds'][$key]=$values;
  }

  $extraOpties=array('RapportageValuta','Remisier','tweedeAanspreekpunt','Accountmanager','Depotbank','Client','Vermogensbeheerder');
  foreach ($categorieVolgorde as $table=>$categorien)
  {
    $html_opties .= "<b>$table</b>";
    foreach ($categorien as $categorie)
    {
      $html_opties .= "<div class=\"menutitle\" onclick=\"SwitchMenu('sub$table$categorie')\">$categorie</div><span class=\"submenu\" id=\"sub$table$categorie\">\n";
      foreach ($velden[$categorie] as $veld=>$waarden)
      {
        $html_opties .= "[".$veld."]<br>\n";
        if($table == 'Portefeuilles' && substr($waarden['form_type'],0,6)=='select' && in_array($veld,$extraOpties))
        {
          $html_opties .= "<label for=\"*".$veld."\" title=\"*".$waarden['description']."\"> [*".$veld."] </label><br>\n";
        }
      }
      $html_opties .= "</span>\n";
    }
  }

  $html = "
 <script language=\"JavaScript\" TYPE=\"text/javascript\">
function Aanpassen()
{
	document.kolForm.submit();
}
function Opslaan()
{
	document.kolForm.kolUpdate.value=\"2\";
	document.kolForm.submit();
}
function Herladen()
{
	document.kolForm.kolUpdate.value=\"3\";
	document.kolForm.submit();
}
</script>
<br><br><b><?= vt('CRM velden'); ?></b>
<br>
<form name=\"kolForm\" target=\"content\" action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\" >
<input type=\"hidden\" name=\"kolUpdate\" value=\"1\">

<style type=\"text/css\">
.menutitle{
cursor:pointer;
margin-bottom: 5px;
background-color:#ECECFF;
color:#000000;
width:120px;
padding:2px;
text-align:center;
font-weight:bold;
/*/*/border:1px solid #000000;/* */
}

input {
	color: Navy;
	background-color:#FBFBFB;
	font-size:14px;
	border : 0px;
	border-bottom : 1px solid silver;
	border-left : 1px solid silver;
	font-weight: bold;
}

.submenu{
margin-bottom: 0.5em;
}
</style>

<script type=\"text/javascript\" src=\"javascript/menu.js\"></script>

<div id=\"masterdiv\">
";
  $html .= $html_opties;
  $html .="</div>";
  $html .="</form>";

  return $html;
}


if ( isset ($data['emailBody']) && ! empty ($data['emailBody']) ) {
  $aeconfig->addItem('ordersConformMail', $data['emailBody']);
  $aeconfig->addItem('ordersConformMailSubject', $data['emailSubject']);
  unset($_POST);
  unset($_GET);

  $message = new AE_Message();
  $message->setMessage(vt('E-mail template is opgeslagen'), 'success');
  echo $message->getMessage();
}

$ordersConformMail = $aeconfig->getData('ordersConformMail');
$ordersConformMailSubject = $aeconfig->getData('ordersConformMailSubject');

$content['jsincludes'] .= $AETemplate->loadJs('ckeditor4/ckeditor');

$content['pageHeader'] = '<div class="formTitle textB"><strong>' . vt('Bevestigings mail') . '</strong></div>';

session_start();


$fields = getFields();

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($fields,"");


$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->items['navedit']->buttonDelete = false; //deny save here we dont need it
$_SESSION['NAV']->items['navedit']->buttonSave = true; //deny save here we dont need it
session_write_close();

$content['javascript'] .= "
function submitForm () {
	$('#emailbodyForm').submit();
}
";


  echo template($__appvar["templateContentHeader"],$content);

?>


  <div id="tabs">
    <ul>
      <li><a href="#tabs-1"><?= vt('Mail template opmaken'); ?></a></li>
      <li><a href="#tabs-2"><?= vt('Test e-mail verzenden'); ?></a></li>
    </ul>
    <div id="tabs-1">
      <form id="emailbodyForm" action="ordersConfirmMailSettings.php" method="post" style="display: grid;">
        <div class="formblock">
          <div class="formlinks"><label for="bodyHtml"><?= vt('Onderwerp'); ?></label></div>
          <div class="formrechts">
            <input name="emailSubject" id="emailSubject" style="width:600px" value="<?=$ordersConformMailSubject;?>" />
          </div>
        </div>

        <div class="formblock">
          <div class="formlinks"><label for="bodyHtml"><?= vt('Opmaak'); ?></label></div>
          <div class="formrechts">
          <textarea name="emailBody" id="emailBody">
            <?=$ordersConformMail;?>
          </textarea>
          </div>
        </div>
      </form>
    </div>
    <div id="tabs-2">
      <form id="emailbodyForm" action="ordersConfirmMailSettings.php" method="post" style="display: grid;">
        <div class="formblock">
          <div class="formlinks"><label for="bodyHtml"><?= vt('Onderwerp'); ?></label></div>
          <div class="formrechts">
            <input name="emailSubjectTest" autocomplete="new-password"  id="emailSubjectTest" style="width:600px" value="<?=$ordersConformMailSubject;?>" />
          </div>
        </div>

        <div class="formblock">
          <div class="formlinks"><label for="bodyHtml"><?= vt('E-mail adres'); ?></label></div>
          <div class="formrechts">
            <input name="emailTo" autocomplete="new-password"  id="emailTo"  />
          </div>
        </div>

        <div class="formblock">
          <div class="formlinks"><label for="bodyHtml"><?= vt('Order id'); ?></label></div>
          <div class="formrechts">
            <input name="orderId" autocomplete="new-password"  id="orderId"  />
          </div>
        </div>

        <div class="formblock">
          <div class="formlinks"><label for="bodyHtml"><?= vt('Opmaak'); ?></label></div>
          <div class="formrechts">
          <textarea name="emailBodyTest" id="emailBodyTest">
            <?=$ordersConformMail;?>
          </textarea>
        </div>
          <div class="formblock">
            <div class="formlinks">&nbsp;</div>
            <div class="formrechts">
              <br />
              <br />
              <button style="width:200px" type="submit" class="btn-new btn-default" value="1"><?= vt('Test mail verzenden'); ?></button>
            </div>

      </form>

    </div>
  </div>





<script>
  $(function () {

    $( "#tabs" ).tabs();

      CKEDITOR.replace( 'emailBodyTest' ,
        {
//            uiColor: '#9AB8F3',
          enterMode : CKEDITOR.ENTER_BR,
          allowedContent: true,
//            scayt_autoStartup:true,
//            disableNativeSpellChecker:false,
//            scayt_sLang: 'nl_NL'
        });

    CKEDITOR.replace( 'emailBody' ,
      {
//            uiColor: '#9AB8F3',
        enterMode : CKEDITOR.ENTER_BR,
        allowedContent: true,
//            scayt_autoStartup:true,
//            disableNativeSpellChecker:false,
//            scayt_sLang: 'nl_NL'
      });
  });
</script>
<?php
  echo template($__appvar["templateRefreshFooter"],$content);