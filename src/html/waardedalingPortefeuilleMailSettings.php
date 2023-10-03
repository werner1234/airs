<?php
/*
    AE-ICT CODEX source module versie 1.6, 31 mei 2006
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2020/03/27 13:47:59 $
    File Versie         : $Revision: 1.7 $


    $Log: waardedalingPortefeuilleMailSettings.php,v $
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$__debug = false;

$AETemplate = new AE_template();
$aeconfig = new AE_config();
$AEMessage = new AE_Message();
$AEMailTemplate = new AE_MailTemplate();

$data = array_merge($_POST, $_GET);

if ( isset ($data['emailBodyTest']) && ! empty ($data['emailBodyTest']) )
{
  $testMailData=array();
  if($data['portefeuille']<>'')
  {
    $crmObj = new Naw();
    //CRM gegevens ophalen
    $crmNawData = $crmObj->parseBySearch(array ('portefeuille' => $data['portefeuille']));
    foreach ($crmNawData as $key => $value)
      $testMailData[$key] = $value;

    //portefeuille gegevens ophalen
    $portefeuilleObj = new Portefeuilles();
    $portefeuilleData = $portefeuilleObj->parseBySearch(array('Portefeuille' => $data['portefeuille']));
    foreach ($portefeuilleData as $key => $value)
    {
      $testMailData[$key] = $value;
    }

    $lpw = new laatstePortefeuilleWaarde();
    $lpwData = $lpw->parseBySearch(array ('portefeuille' => $data['portefeuille']));
    foreach ($lpwData as $key => $value)
      $testMailData[$key] = $value;
  }
  $testMailData['emailHandtekening'] = $_SESSION['usersession']['gebruiker']['emailHandtekening'];
  $testMailData['huidigeDatum']=date("j")." ".$__appvar["Maanden"][date("n")]." ".date("Y");
  $testMailData['huidigeGebruiker']=$USR;

  $query="SELECT Naam,titel FROM Gebruikers WHERE Gebruiker='".$USR."'";
  $db = new DB();
  $db->SQL($query);
  $dataGebr=$db->lookupRecord();
  $testMailData['GebruikerNaam']=$dataGebr['Naam'];
  $testMailData['GebruikerTitel']=$dataGebr['titel'];

  if($data['portefeuille']<>'')
  {
    $query = "SELECT * FROM signaleringPortRend WHERE portefeuille='" . $data['portefeuille'] . "' ORDER BY datum desc limit 1";
    $db = new DB();
    $db->SQL($query);
    $signaleringPortRend = $db->lookupRecord();
    foreach ($signaleringPortRend as $key => $value)
    {
      $testMailData[$key] = $value;
      if ($key == 'datum')
      {
        $key = 'signaleringsDatum';
        $value = date('d-m-Y', db2jul($signaleringPortRend['datum']));
        $testMailData[$key] = $value;
        $testMailData['datum'] = date('d-m-Y', db2jul($signaleringPortRend['datum']));
      }
    }
    
  }

  $testMailData = $AEMailTemplate->getExtraFields($testMailData);
  
  $AEMailTemplate->setData($testMailData);
  $data['emailBodyTest'] = $AEMailTemplate->ParseData($data['emailBodyTest']);
  $data['emailSubjectTest'] = $AEMailTemplate->ParseData($data['emailSubjectTest']);

//  foreach ( $testMailData as $key => $val ) {
//    $data['emailSubjectTest']  = str_replace("[" . $key . "]", $val, $data['emailSubjectTest'] );
//    $data['emailBodyTest'] = str_replace("[" . $key . "]", $val, ($data['emailBodyTest']));
//  }

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

  header("Location: waardedalingPortefeuilleMailSettings.php");
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
    'Speciale velden'=>array(
      'Opmaak'
    ),
    'laatstePortefeuilleWaarde'=>array('Algemeen'),
    'signaleringPortRend'=>array('Algemeen')
  );


  $velden['Speciale velden']['Opmaak']['huidigeDatum']=array('description'=>'De huidige datum.');
  $velden['Speciale velden']['Opmaak']['huidigeGebruiker']=array('description'=>'De huidige gebruiker.');
  $velden['Speciale velden']['Opmaak']['GebruikerNaam']=array('description'=>'Naam huidige gebruiker.');
  $velden['Speciale velden']['Opmaak']['GebruikerTitel']=array('description'=>'Titel huidige gebruiker.');
  $velden['Speciale velden']['Opmaak']['emailHandtekening']=array('description'=>'Handtekening huidige gebruiker.');
  $velden['Speciale velden']['Opmaak']['signaleringsDatum']=array('description'=>'signaleringsDatum.');
  $velden['Speciale velden']['Opmaak']['toelichting']=array('description'=>'toelichting.');

  $portefeuille = new Portefeuilles();
  foreach ($portefeuille->data['fields'] as $key=>$values)
  {
    $velden['Portefeuilles'][$values['categorie']][$key]=$values;
  }
  $naw = new Naw();
  foreach ($naw->data['fields'] as $key=>$values)
  {
    $velden['Naw'][$values['categorie']][$key]=$values;
  }
  $naw = new laatstePortefeuilleWaarde();
  foreach ($naw->data['fields'] as $key=>$values)
  {
    $velden['laatstePortefeuilleWaarde']['Algemeen'][$key]=$values;
  }
  $naw = new signaleringPortRend();
  foreach ($naw->data['fields'] as $key=>$values)
  {
    $velden['signaleringPortRend']['Algemeen'][$key]=$values;
  }

  $extraOpties=array('RapportageValuta','Remisier','tweedeAanspreekpunt','Accountmanager','Depotbank','Client','Vermogensbeheerder');
  foreach ($categorieVolgorde as $table=>$categorien)
  {
    $html_opties .= "<b>$table</b>";
    foreach ($categorien as $categorie)
    {
      $html_opties .= "<div class=\"menutitle\" onclick=\"SwitchMenu('sub$table$categorie')\">$categorie</div><span class=\"submenu\" id=\"sub$table$categorie\">\n";
      foreach ($velden[$table][$categorie] as $veld=>$waarden)
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
  $aeconfig->addItem('WaardedalingPortefeuilleMailBody', $data['emailBody']);
  $aeconfig->addItem('WaardedalingPortefeuilleMailSubject', $data['emailSubject']);
  unset($_POST);
  unset($_GET);

  $message = new AE_Message();
  $message->setMessage(vt('E-mail template is opgeslagen'), 'success');
  echo $message->getMessage();
}

$ordersConformMailBody = $aeconfig->getData('WaardedalingPortefeuilleMailBody');
$ordersConformMailSubject = $aeconfig->getData('WaardedalingPortefeuilleMailSubject');

$content['jsincludes'] .= $AETemplate->loadJs('ckeditor4/ckeditor');

$content['pageHeader'] = '<div class="formTitle textB"><strong>' . vt('Signalering waardedaling portefeuille') . '</strong></div>';

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
      <form id="emailbodyForm" action="waardedalingPortefeuilleMailSettings.php" method="post" style="display: grid;">
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
            <?=$ordersConformMailBody;?>
          </textarea>
          </div>
        </div>
      </form>
    </div>
    <div id="tabs-2">
      <form id="emailbodyForm" action="waardedalingPortefeuilleMailSettings.php" method="post" style="display: grid;">
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
          <div class="formlinks"><label for="bodyHtml"><?= vt('Portefeuille'); ?></label></div>
          <div class="formrechts">
            <input name="portefeuille" autocomplete="new-password"  id="portefeuille"  />
          </div>
        </div>

        <div class="formblock">
          <div class="formlinks"><label for="bodyHtml"><?= vt('Opmaak'); ?></label></div>
          <div class="formrechts">
          <textarea name="emailBodyTest" id="emailBodyTest">
            <?=$ordersConformMailBody;?>
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