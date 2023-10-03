<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2020/05/08 14:41:02 $
    File Versie         : $Revision: 1.153 $

    $Log: CRM_nawList.php,v $
    Revision 1.153  2020/05/08 14:41:02  rm
    8541 emailings eMail opmaak: via nieuwe templates

    Revision 1.152  2020/05/07 05:38:48  rvv
    *** empty log message ***

    Revision 1.151  2020/05/06 14:56:43  rvv
    *** empty log message ***

    Revision 1.150  2020/02/19 14:59:20  rvv
    *** empty log message ***

    Revision 1.149  2019/11/29 07:51:46  cvs
    call 8142

    Revision 1.148  2019/10/04 09:32:44  cvs
    call 8142

    Revision 1.147  2019/10/02 13:42:46  cvs
    call 8136

    Revision 1.146  2019/09/04 15:29:38  rvv
    *** empty log message ***

    Revision 1.145  2019/06/19 15:50:26  rvv
    *** empty log message ***

    Revision 1.144  2018/12/21 17:48:19  rvv
    *** empty log message ***

    Revision 1.143  2018/12/10 12:38:03  rvv
    instellingen per relatiesoort uitgezet.

    Revision 1.142  2018/10/14 10:14:51  rvv
    *** empty log message ***

    Revision 1.141  2018/10/14 07:04:02  rvv
    *** empty log message ***

    Revision 1.140  2018/10/13 17:16:37  rvv
    *** empty log message ***

    Revision 1.139  2018/10/06 17:19:09  rvv
    *** empty log message ***

    Revision 1.138  2018/09/23 17:14:23  cvs
    call 7175

    Revision 1.137  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.136  2018/04/18 16:15:08  rvv
    *** empty log message ***

    Revision 1.135  2018/02/15 08:11:29  rvv
    *** empty log message ***

    Revision 1.134  2018/02/15 07:48:07  rvv
    *** empty log message ***

    Revision 1.133  2018/02/14 16:50:44  rvv
    *** empty log message ***

    Revision 1.132  2017/12/17 17:05:03  rvv
    *** empty log message ***

    Revision 1.131  2017/12/16 18:42:38  rvv
    *** empty log message ***

    Revision 1.130  2017/12/03 10:34:43  rvv
    *** empty log message ***

    Revision 1.129  2017/11/15 17:11:00  rvv
    *** empty log message ***

    Revision 1.128  2017/10/04 16:07:15  rvv
    *** empty log message ***

    Revision 1.127  2017/07/22 18:20:50  rvv
    *** empty log message ***

    Revision 1.126  2017/05/08 07:53:43  rm
    Link naar HTMLrapport dashboard

    Revision 1.125  2017/04/16 10:31:39  rvv
    *** empty log message ***

    Revision 1.124  2017/03/20 06:58:52  rvv
    *** empty log message ***

    Revision 1.123  2017/03/15 16:34:28  rvv
    *** empty log message ***

    Revision 1.122  2017/02/15 15:02:19  rvv
    *** empty log message ***

    Revision 1.121  2017/02/15 14:54:37  cvs
    no message

    Revision 1.120  2017/02/11 17:32:35  rvv
    *** empty log message ***

    Revision 1.119  2017/01/10 09:47:13  rm
    Html rapportage links

    Revision 1.118  2017/01/05 13:36:08  cvs
    call 4986, portaldocu check

    Revision 1.117  2017/01/04 16:34:18  rvv
    *** empty log message ***

    Revision 1.116  2017/01/04 16:23:00  cvs
    call 4986

    Revision 1.115  2016/12/29 07:48:35  rvv
    *** empty log message ***

    Revision 1.114  2016/12/28 06:18:20  rvv
    *** empty log message ***

    Revision 1.113  2016/12/27 18:57:17  rvv
    *** empty log message ***

    Revision 1.112  2016/12/26 17:22:31  rvv
    *** empty log message ***

    Revision 1.111  2016/12/24 16:33:40  rvv
    *** empty log message ***

    Revision 1.110  2016/12/07 16:51:35  rvv
    *** empty log message ***

    Revision 1.109  2016/12/04 08:57:55  rvv
    *** empty log message ***

    Revision 1.108  2016/12/04 08:28:56  rvv
    *** empty log message ***

    Revision 1.107  2016/12/03 19:23:15  rvv
    *** empty log message ***

    Revision 1.106  2016/11/21 06:29:48  rvv
    *** empty log message ***

    Revision 1.105  2016/11/20 10:38:02  rvv
    *** empty log message ***

    Revision 1.104  2016/11/19 19:00:39  rvv
    *** empty log message ***

    Revision 1.103  2016/11/02 16:23:29  rvv
    *** empty log message ***

    Revision 1.102  2016/11/02 13:24:49  rm
    alle links in tabel uitzetten
    Eigen url per veld mee kunnen geven

    Revision 1.101  2016/09/04 14:40:56  rvv
    *** empty log message ***

    Revision 1.100  2015/12/20 16:44:58  rvv
    *** empty log message ***

    Revision 1.99  2015/11/18 17:05:01  rvv
    *** empty log message ***

    Revision 1.98  2015/06/29 18:29:18  rvv
    *** empty log message ***

    Revision 1.97  2015/05/27 11:52:51  rvv
    *** empty log message ***

    Revision 1.96  2015/05/23 13:56:31  rvv
    *** empty log message ***

    Revision 1.95  2015/05/23 12:50:37  rvv
    *** empty log message ***

    Revision 1.94  2015/04/19 08:38:21  rvv
    *** empty log message ***

    Revision 1.93  2015/04/15 18:21:19  rvv
    *** empty log message ***

    Revision 1.92  2015/04/11 17:07:30  rvv
    *** empty log message ***

    Revision 1.91  2015/02/22 09:53:55  rvv
    *** empty log message ***

    Revision 1.90  2014/11/12 16:40:11  rvv
    *** empty log message ***

    Revision 1.89  2014/10/15 16:03:56  rvv
    *** empty log message ***

    Revision 1.88  2014/10/01 16:02:48  rvv
    *** empty log message ***

    Revision 1.87  2014/09/03 15:55:22  rvv
    *** empty log message ***

    Revision 1.86  2014/08/30 16:28:19  rvv
    *** empty log message ***

    Revision 1.85  2014/07/12 15:28:36  rvv
    *** empty log message ***

    Revision 1.84  2014/05/29 12:07:22  rvv
    *** empty log message ***

    Revision 1.83  2014/04/19 16:15:26  rvv
    *** empty log message ***

    Revision 1.82  2014/02/28 16:39:28  rvv
    *** empty log message ***

    Revision 1.81  2014/01/15 16:17:36  rvv
    *** empty log message ***

    Revision 1.80  2013/12/22 16:04:27  rvv
    *** empty log message ***

    Revision 1.79  2013/12/04 16:27:55  rvv
    *** empty log message ***

    Revision 1.78  2013/10/16 15:33:35  rvv
    *** empty log message ***

    Revision 1.77  2013/08/24 15:47:39  rvv
    *** empty log message ***

    Revision 1.76  2013/08/18 12:21:44  rvv
    *** empty log message ***

    Revision 1.75  2013/08/07 17:17:44  rvv
    *** empty log message ***

    Revision 1.74  2013/08/04 10:47:37  rvv
    *** empty log message ***

*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$customTemplate = new AE_CustomTemplate('crmEmailings');


if ($_SESSION["ddCategorie"] == "")
{
  $db = new DB();
  $query = "SELECT omschrijving,omschrijving FROM (CRM_selectievelden) WHERE module = 'docCategrien'";
  $db->executeQuery($query);
  $dd_option = "\t<option value='' > ----- </option>";
  while ($rec = $db->nextRecord())
  {
    $dd_option .= "\n\t<option value='".$rec['omschrijving']."' >".$rec['omschrijving']."</option>";
  }
  $_SESSION["ddCategorie"] = $dd_option;
}

if($__appvar['bedrijf']=='ANT')
  $__appvar['rowsPerPage']=250;
  
if($_GET['mailing'])
  $__appvar['rowsPerPage']=10000;

$mainHeader = vt("relatie overzicht");

$editScript = "CRM_nawEdit.php";
$allow_add  = true;
$type='portefeuille';

$list = new MysqlList2();
$list->extraFormHeaderTags = "enctype=\"multipart/form-data\"";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];
$list->storeTableIds='CRM_naw';
$list->idTable='CRM_naw';


if ($_POST["koppelDocument"] == 1)
{
  include_once "CRM_nawList_koppelDigidocs.php";
  exit;
}
elseif($_GET["takenAanmaken"] == 1)
{

  if (GetCRMAccess(2))
    include_once "taken_aanmaken.php";
  exit;
}

$db= new DB();

$query="SELECT VermogensbeheerdersPerGebruiker.Vermogensbeheerder, Vermogensbeheerders.CrmPortefeuilleInformatie,Vermogensbeheerders.Layout,Vermogensbeheerders.CRM_eigenTemplate,Vermogensbeheerders.check_module_SCENARIO
        FROM Vermogensbeheerders Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR' GROUP BY VermogensbeheerdersPerGebruiker.Vermogensbeheerder  limit 1";
$db->SQL($query);
$gebruikPortefeuilleInformatie = $db->lookupRecord();

//if($_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0 && $_SESSION['usersession']['gebruiker']['Accountmanager'] == '')
//{
  $query = "SELECT CRMeigenRecords FROM Gebruikers WHERE Gebruiker='$USR'";
  $db->SQL($query);
  $gebruikersData = $db->lookupRecord();
  if ($gebruikersData['CRMeigenRecords'] > 0)
  {
    $eigenaarFilter = " AND (CRM_naw.prospectEigenaar='$USR' OR CRM_naw.accountEigenaar='$USR') ";
   // $orEigenaar = " OR (CRM_naw.prospectEigenaar='$USR' OR CRM_naw.accountEigenaar='$USR') ";
  }
//}

if($_GET['sql'])
  $_SESSION['CRM_naw_listFilter']=$_GET['sql'];
else
  $_GET['sql']=$_SESSION['CRM_naw_listFilter'];

if($_SESSION['CRM_naw_listFilter']=='all')
{
  unset($_SESSION['CRM_naw_listFilter']);
  unset($_GET['sql']);
}

$_GET['sql']=$_SESSION['CRM_naw_listFilter'];



switch ($_GET['sql'])
{
	case "debiteur":
		$where="debiteur = 1 AND aktief=1";
		$subHeader = ", Clienten";
		break;
  case "crediteur":
		$where="crediteur = 1 AND aktief=1";
		$subHeader = ", " . vt('Leveranciers') . "";
		break;
  case "prospect":
		$where="prospect = 1 AND aktief=1";
		$subHeader = ", " . vt('Prospects') . "";
		break;
  case "overige":
		$where="overige = 1 AND aktief=1";
		$subHeader = ", " . vt('Overige') . "";
		break;
	case "inaktief":
    $filter=getRelatieSoortenFilter(true);
		$where="aktief <> 1 $filter";
		$subHeader = ", " . vt('inaktieve relaties') . "";
		break;
	default:
    if($_GET['sql'] <> '')
      $where="aktief = 1 AND `".$_GET['sql']."`=1 ";
    else
    {
      $filter=getRelatieSoortenFilter();
	    $where="aktief = 1 $filter";
    }
		$subHeader = ", " . vt('alle aktieve relaties') . "";
		break;
}


if($_GET['letter'])
	$extraWhere = " AND CRM_naw.zoekveld  LIKE '".mysql_escape_string($_GET['letter'])."%' ";

$list->setWhere($where.$extraWhere);
$where='';
if($_POST['toXls'] == '2' || $_POST['toXls'] == '3')
{
  $list->addFixedField("Naw","id",array("list_width"=>"150","search"=>false,"list_visible"=>true));
}
else
{
$list->addFixedField("Naw","zoekveld",array("list_width"=>"150","search"=>true));
$list->addFixedField("Naw","portefeuille",array("list_width"=>"150","search"=>true));
$list->addFixedField("Naw","externID",array("list_width"=>"150","search"=>true));
}

$query="SELECT omschrijving FROM CRM_selectievelden WHERE CRM_selectievelden.module='evenementen'; ";
$db->SQL($query);
$db->Query();
$options .= "<option value=\"\" SELECTED>---</option>\n";
while($data=$db->nextRecord())
{
  $options .= "<option value=\"".$data['omschrijving']."\">".$data['omschrijving']." </option>\n";
}



$query="SELECT substring(`field`,9) as mailing FROM ae_config WHERE `field` like 'mailing\_%' AND value = ( SELECT value FROM ae_config WHERE `field`='mailingBody');";
$db->SQL($query);
$huidigeMailing=$db->lookupRecord();
$huidigeMailing=$huidigeMailing['mailing'];
$query = "SELECT substring(`field`,9) as mailing,field  FROM ae_config WHERE field like 'mailing\_%' "; //LIMIT 10
$db->SQL($query);
$db->Query();
$mailingTemplate=array();
while ($rapItems = $db->nextRecord())
  $mailingTemplate['Brief'][]=$rapItems;

/*
if ($handle = opendir("RTF_templates"))
{
  while (false !== ($file = readdir($handle)))
	  if (substr(strtolower($file),-4) == ".rtf")
      $mailingTemplate['RTF_Template'][] = $file;
	closedir($handle);
}
*/

if (count($mailingTemplate) > 0)
{
  $rapMenu = "";
  foreach ($mailingTemplate['Brief'] as $rapItems)
  {
    if($huidigeMailing==$rapItems["mailing"])
      $selected="selected";
    else
      $selected='';
	  $rapMenu .= "<option $selected value=\"".$rapItems["field"]."\">".$rapItems["mailing"]."</option>\n";
  }
  if(count($mailingTemplate['RTF_Template']) > 0)
  {
    $rapMenu .= '<option disabled="disabled">---------</option>';
    foreach ($mailingTemplate['RTF_Template'] as $rtf)
      $rapMenu .= "<option value=\"".$rtf."\">".$rtf."</option>\n";

  }
  $rapMenu  .= "</select>";
}

$content['jsincludes'].='<script type="text/javascript" src="javascript/ckeditor4/ckeditor.js"></script>';
$content['javascript'].="

function loadEditor(textarea,h,w)
{
  CKEDITOR.replace( textarea ,
	{
    height: h,
		width: w,
    uiColor: '#9AB8F3',
    allowedContent: true 
	});
}

 function doEditorOnload()
 {
   loadEditor('body',400,1000);
 }
 
 function switchToTemplate()
 {
  $('#mailingHtmlDiv').hide();
  $('#mailingTemplatesDiv').show();
  $('#body').hide();
  $('.trhideclass1').hide();
  document.editForm.gebruikHandtekening.value=\"0\";
 }
 ";

 if($_POST['body']<>'')
   $_SESSION['usersession']['crmNawListMailbody']=$_POST['body'];

 if($_SESSION['usersession']['crmNawListMailbody']<>'')
  $data['body']=$_SESSION['usersession']['crmNawListMailbody'];
 if($data['body']=='')
  $data['body']=$_SESSION['usersession']['gebruiker']['emailHandtekening'];


$cfg=new AE_config();

if (portalCheck())
{
  $portaal = '
    <tr>
      <td>portaal:</td>
      <td>
        <input type="checkbox" name="dd_portaal" value="1" /> naar portaal zenden
        
      </td>
    <tr>

  ';
}
else
{
  $portaal = "";
}



$koppelform = '

<div id="digidocKoppelForm">
<input type="hidden" name="koppelDocument" value="0">
  <table>
    <tr>
      <td>' . vt('bestand') . ':</td><td>
        <input type="file" name="importfile" />
         
      </td>
    <tr>
    <tr>
      <td>' . vt('omschrijving') . ':</td><td>
        <input type="text" name="dd_omschrijving" />
         
      </td>
    <tr>
    <tr>
      <td>' . vt('categorie') . ':</td>
      <td>
        <select  class="" type="select"  name="dd_categorie" >
          ' . $_SESSION["ddCategorie"] . '
        </select>         
      </td>
    <tr>
    ' . $portaal . '
    <tr>
      <td>&nbsp;</td>
      <td><input type="button" id="koppelgButton" value="koppel document aan relaties." onclick="document.editForm.koppelDocument.value=\'1\';document.editForm.submit();">
    </td>
    <tr>
  </table>
 </div>

';




$mailingScript='
 <br><br>
 '.$koppelform.'
 <div id=\'mailingForm\'>
  <table>
  <tr><td>' . vt('Te gebruiken template') . '</td><td><select name="mailingRtf" >'.$rapMenu.'</td><tr>
  <tr><td>' . vt('Documenten aan relaties koppelen') . '. </td><td><input type="checkbox" name="storeDD" value="1"> Documentnaam:<input type="text" name="DDnaam" value=""></td><tr>
  <tr><td>' . vt('Evenement aan relaties koppelen') . '</td><td><select name="evenementRtf">'.$options.'</select></td><tr>
  <tr><td>' . vt('Groeperen op adres') . '</td><td><input type="checkbox" name="adresGroup" value="1"></td><tr>
  <tr><td>' . vt('Kwartaal rapportage filter') . '</td><td>' . vt('Actief') . '<input type="checkbox" name="rapportFilter" value="1">  ' . vt('Email') . ' <input type="checkbox" name="rapportEmail" value="1"> ' . vt('Papier') . '<input type="checkbox" name="rapportPapier" value="1"> ' . vt('Extra adressen') . '<input type="checkbox" name="extraAdressen" value="1"></td><tr>
  <tr><td>' . vt('Genereer mailing') . '</td><td><input type="button" id="mailingButton" value="Genereer rtf file." onclick="document.editForm.submit();document.editForm.toXls.value=\'0\';"></td><tr>
  </table>
</div>
 <div id=\'eMailingForm\'>
  <table>
  <tr>
    <td>' . vt('Templates') . '</td>
    <td><select name="template" id="templateSelect">' . $customTemplate->getTemplateSelect() . '</select>
     ' . vt('Handtekening toevoegen') . ' <input type="checkbox" onclick="javascript:addSignatureToBody(this);" name="addSignature" value="1">
    </td>
   <tr>
  
  ' . $customTemplate->getLangSwitcher($categorie) . '
  
  
  <tr><td>' . vt('ExtraVelden') . '</td><td>' . $customTemplate->getExtraFields() . '</td><tr>
  <tr><td>' . vt('Te gebruiken template') . '</td>
  <td><div id="mailingHtmlDiv" ><a href="javascript:switchToTemplate();">' . vt('Klik hier om een mailing template te selecteren ipv onderstaande template') . '.</a></div> 
      <div id="mailingTemplatesDiv" style="display:none"> <select disabled name="mailingEmail" > '.$rapMenu.' 
        <input type="hidden" name="gebruikHandtekening" value="1"> 
      </div>  </td><tr>
  <tr  class="trhideclass1" ><td>' . vt('Body tekst') . '</td><td><textarea class="" cols="60"  rows="10" name="body" id="body">'.$data['body'].'</textarea><tr>
  <tr><td>' . vt('Documenten aan relaties koppelen') . '. </td><td><input type="checkbox" name="estoreDD" value="1"> ' . vt('Documentnaam') . ':<input type="text" name="eDDnaam" value=""></td><tr>
  <tr><td>' . vt('Evenement aan relaties koppelen') . '</td><td><select name="evenementEmail">'.$options.'</select></td><tr>
  
  <tr><td>' . vt('E-mail afzender naam') . '</td><td><input type="text" name="senderName" id="senderName" value="' . $_SESSION['usersession']['gebruiker']['Naam'] . '" size=80></td><tr>
  <tr><td>' . vt('E-mail afzender adres') . '</td><td><input type="text" name="senderEmail" id="senderEmail" value="' . $_SESSION['usersession']['gebruiker']['emailAdres'] . '" size=80></td><tr>
  
  <tr><td>' . vt('eMail cc emailadres') . '</td><td><input type="text" name="ccEmail" id="ccEmail" value="'.$cfg->getData('emailccEmail').'" size=80></td><tr>
  <tr><td>' . vt('eMail bcc emailadres') . '</td><td><input type="text" name="bccEmail" id="bccEmail" value="'.$cfg->getData('emailbccEmail').'" size=80></td><tr>
  <tr><td>' . vt('Onderwerp') . '</td><td><input type="text" name="onderwerp" id="onderwerp" value="" size=80></td><tr>
  <tr><td>' . vt('Kwartaal rapportage filter') . '</td><td>' . vt('Actief') . '<input type="checkbox" name="rapporteFilter" value="1"> ' . vt('Email') . ' <input type="checkbox" name="rapporteEmail" value="1"> ' . vt('Papier') . '<input type="checkbox" name="rapportePapier" value="1">' . vt('Extra adressen') . '<input type="checkbox" name="extraeAdressen" value="1"></td><tr>
  <tr><td>' . vt('Genereer mailing') . '</td><td><input type="button" id="mailingButton" value="Creëer emails" onclick="document.editForm.submit();document.editForm.toXls.value=\'0\';">
  <input type="button" id="mailingButton" value="Opslaan body-tekst" onclick="document.editForm.toXls.value=\'-1\';document.editForm.submit();document.editForm.toXls.value=\'0\';"></td><tr>
  </table>
</div>
</div>
<br>
';



if(checkAccess($type))
  $beperktToegankelijk = " 1 ";
else
{
  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
	{
	   $beperktToegankelijk = " (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."') ";
	}
	else
	{
    $join = "LEFT Join VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND 
    ( VermogensbeheerdersPerGebruiker.Gebruiker='".$USR."' OR Portefeuilles.Portefeuille='')
    JOIN Gebruikers as GebruikersRechten ON GebruikersRechten.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR'
    JOIN Vermogensbeheerders ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder ";
    $beperktToegankelijk = "  (Portefeuilles.beperktToegankelijk = '0' OR  GebruikersRechten.beperkingOpheffen = '1' ) AND Vermogensbeheerders.CrmPortefeuilleInformatie = '1' ";
	}

}
$query = "SELECT count(Portefeuilles.id) as aantal
          FROM Portefeuilles
          $join
          WHERE $beperktToegankelijk ";

$db->SQL($query);
$dbData = $db->lookupRecord();
//$list->categorieVolgorde['laatstePortefeuilleWaarde']=array('Algemeen');
//$list->addFixedField("laatstePortefeuilleWaarde","laatsteWaarde",array("list_width"=>"150","search"=>true));
$list->categorieVolgorde=array('Naw'=>array("Algemeen","Adres","Verzendadres","Telefoon","Internetgegevens","Bedrijfinfo","Persoonsinfo","Legitimatie","Informatie partner","Legitimatie partner","Adviseurs","geen",'Contract','Beleggen','Rapportage','Profiel','Relatie geschenk','Recordinfo'),
                               'CRM_evenementen'=>array('Algemeen'),
                               'CRM_naw_adressen'=>array('Algemeen'),
                               'CRM_naw_kontaktpersoon'=>array('Algemeen'),
                               'CRM_naw_rekeningen'=>array('Algemeen'),
                               'Naw_dossier'=>array('Algemeen'),
                               'taken'=>array('Algemeen'),
                               'Gebruiker'=>array('Algemeen'));


if($gebruikPortefeuilleInformatie['CRM_eigenTemplate'] == 1)
{
$query = "SELECT veldenPerTab FROM `CRM_naw_templates` WHERE intake=0 order by change_date desc limit 1";
$db->SQL($query);
$customFields = $db->lookupRecord();
$customFields=unserialize($customFields['veldenPerTab']);
$naw=new Naw();
foreach ($customFields as $tab=>$tabdata)
{
  if($tabdata['naam'] <> '')
  {
    $nieuweVolgorde[$tabdata['object']][]=$tabdata['naam'];
    foreach ($tabdata['velden'] as $key=>$waarden)
      $nieuweVelden[$tabdata['object']][$key]=$waarden;
  }
}
if(isset($nieuweVelden['Naw']))
  $nieuweVelden['Naw']['PortGec']=$naw->data['fields']['PortGec'];

$nieuweVelden['Naw']['clientGesproken']=$naw->data['fields']['clientGesproken'];
foreach ($nieuweVolgorde as $object=>$veldData)
  $list->categorieVolgorde[$object]=$veldData;
}

$extraTabellen=array();
$extraTabelJoinAdded=array();
$joinParticipanten='';
if(GetModuleAccess('ParticipatieGebruiker') == 1)
{
  $extraTabellen[]='participanten';
}
$joinExtra='';

if($__appvar["crmOnly"] ==true)
{
  $extraTabellen[]='Dd_reference';
}
else
{
  $extraTabellen[]='GeconsolideerdePortefeuilles';
  $extraTabellen[]='ZorgplichtPerPortefeuille';
  $extraTabellen[]='Rekeningen';
  $extraTabellen[]='ModelPortefeuillesPerPortefeuille';
  $extraTabellen[]='StandaarddeviatiePerPortefeuille';
  $extraTabellen[]='Dd_reference';
  $extraTabellen[]='crmLaatsteFondsWaarden';
}

if(defined('DBportaal'))
  $extraTabellen[]='CRM_portaalClienten';

foreach($extraTabellen as $tabel)
  $list->categorieVolgorde[$tabel]=array('Algemeen');

$categorie = substr(preg_replace("/[^A-Za-z0-9 ]/", '', $_GET['sql']),0,15);
if(($dbData['aantal'] > 0 || checkAccess($type) || ($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)) && $__appvar["crmOnly"] == false)
{
  $list->categorieVolgorde['Portefeuilles']=array('Gegevens','Beheerfee','Staffels','Recordinfo');
  $list->categorieVolgorde['laatstePortefeuilleWaarde']=array('Algemeen');
  $html = $list->getCustomFields(array_merge(array('Naw','CRM_evenementen','CRM_naw_adressen','CRM_naw_kontaktpersoon','CRM_naw_rekeningen','Portefeuilles','Naw_dossier','taken','laatstePortefeuilleWaarde','Gebruiker'),$extraTabellen),"crm_naw",$nieuweVelden);//,$categorie
  $joinPortefeuilles="LEFT JOIN Portefeuilles ON CRM_naw.Portefeuille = Portefeuilles.Portefeuille 
  LEFT Join VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR'  ";

  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
  {
    $where .= " AND ( (Portefeuilles.Portefeuille IS NOT NULL AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR' AND (Portefeuilles.Accountmanager='" . $_SESSION['usersession']['gebruiker']['Accountmanager'] . "' OR
                Portefeuilles.tweedeAanspreekpunt ='" . $_SESSION['usersession']['gebruiker']['Accountmanager'] . "')) OR (Portefeuilles.Portefeuille IS NULL  $eigenaarFilter ) ) ";
  }
  else
  {
    $joinPortefeuilles.="
    LEFT JOIN Gebruikers as GebruikersRechten ON GebruikersRechten.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR'
    LEFT JOIN Vermogensbeheerders ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder ";
    
    $where .= " AND ( (Portefeuilles.Portefeuille IS NOT NULL AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR' AND Portefeuilles.consolidatie<2 AND $beperktToegankelijk) OR ( Portefeuilles.Portefeuille IS NULL $eigenaarFilter )  )  ";
  }
}
else
{
  $html = $list->getCustomFields(array_merge(array('Naw','CRM_evenementen','CRM_naw_adressen','CRM_naw_kontaktpersoon','CRM_naw_rekeningen','Naw_dossier','taken','Gebruiker'),$extraTabellen),"crm_naw",$nieuweVelden);//,$categorie
}
  $joinEvenementen='';
  $joinGebruikers='';
$joinCRM_naw_rekeningen ='';
$joinCRM_naw_kontaktpersoon ='';
$joinCRM_naw_adressen='';
$meerdereKoppelingen=false;
//listarray($_SESSION);

  foreach ($list->columns as $colData)
  {
    if(in_array($colData['objectname'],$extraTabelJoinAdded))
      continue;
    if($colData['objectname'] == 'CRM_evenementen')
    {
      $joinEvenementen=" LEFT JOIN CRM_evenementen ON CRM_naw.id = CRM_evenementen.rel_id ";
      $extraTabelJoinAdded[]=$colData['objectname'];
      $meerdereKoppelingen=true;
    }
    elseif($colData['objectname'] == 'CRM_naw_rekeningen')
    {
      $joinCRM_naw_rekeningen=" LEFT JOIN CRM_naw_rekeningen ON CRM_naw.id = CRM_naw_rekeningen.rel_id ";
      $extraTabelJoinAdded[]=$colData['objectname'];
      $meerdereKoppelingen=true;
    }
    elseif($colData['objectname'] == 'CRM_naw_kontaktpersoon')
    {
      $joinCRM_naw_kontaktpersoon=" LEFT JOIN CRM_naw_kontaktpersoon ON CRM_naw.id = CRM_naw_kontaktpersoon.rel_id ";
      $extraTabelJoinAdded[]=$colData['objectname'];
      $meerdereKoppelingen=true;
    }
    elseif($colData['objectname'] == 'CRM_naw_adressen')
    {
      $joinCRM_naw_adressen=" LEFT JOIN CRM_naw_adressen ON CRM_naw.id = CRM_naw_adressen.rel_id ";
      $extraTabelJoinAdded[]=$colData['objectname'];
      $meerdereKoppelingen=true;
    }
    elseif($colData['objectname'] == 'Gebruiker')
    {
      $joinGebruikers=" LEFT JOIN Gebruikers ON CRM_naw.accountEigenaar = Gebruikers.Gebruiker ";
      $extraTabelJoinAdded[]=$colData['objectname'];
    }
    elseif($colData['objectname'] == 'participanten')
    {
      $joinParticipanten=" LEFT JOIN participanten ON CRM_naw.id=participanten.crm_id ";
      $extraTabelJoinAdded[]=$colData['objectname'];
    }
    elseif($colData['objectname'] == 'GeconsolideerdePortefeuilles')
    {
      $joinExtra.=" LEFT JOIN GeconsolideerdePortefeuilles ON CRM_naw.portefeuille=GeconsolideerdePortefeuilles.VirtuelePortefeuille ";
      $extraTabelJoinAdded[]=$colData['objectname'];
    }
    elseif($colData['objectname'] == 'Dd_reference')
    {
      $joinExtra.=" LEFT JOIN dd_reference ON CRM_naw.id = dd_reference.module_id AND dd_reference.module='CRM_naw'";
      $extraTabelJoinAdded[]=$colData['objectname'];
      $meerdereKoppelingen=true;
    }
    elseif($colData['objectname'] == 'ZorgplichtPerPortefeuille')
    {
      $joinExtra.=" LEFT JOIN ZorgplichtPerPortefeuille ON CRM_naw.portefeuille=ZorgplichtPerPortefeuille.Portefeuille";
      $extraTabelJoinAdded[]=$colData['objectname'];
      $meerdereKoppelingen=true;
    }
    elseif($colData['objectname'] == 'Rekeningen')
    {
      $joinExtra.=" LEFT JOIN Rekeningen ON CRM_naw.portefeuille=Rekeningen.Portefeuille AND Rekeningen.consolidatie<2 ";
      $extraTabelJoinAdded[]=$colData['objectname'];
      $meerdereKoppelingen=true;
    }
    elseif($colData['objectname'] == 'ModelPortefeuillesPerPortefeuille')
    {
      $joinExtra.=" LEFT JOIN ModelPortefeuillesPerPortefeuille ON CRM_naw.portefeuille=ModelPortefeuillesPerPortefeuille.Portefeuille ";
      $extraTabelJoinAdded[]=$colData['objectname'];
      $meerdereKoppelingen=true;
    }
    elseif($colData['objectname'] == 'StandaarddeviatiePerPortefeuille')
    {
      $joinExtra.=" LEFT JOIN StandaarddeviatiePerPortefeuille ON CRM_naw.portefeuille=StandaarddeviatiePerPortefeuille.Portefeuille ";
      $extraTabelJoinAdded[]=$colData['objectname'];
    }
    elseif($colData['objectname'] == 'Naw_dossier' && !isset($Naw_dossierAdded))
    {
      $Naw_dossierAdded=true;
/*      $query="CREATE TEMPORARY TABLE laatsteDossier
        SELECT CRM_naw.id as rel_id,  MAX(CRM_naw_dossier.datum) as datum,
          MAX(if(CRM_naw_dossier.clientGesproken =1,CRM_naw_dossier.datum,null)) as clientGesproken
        FROM CRM_naw LEFT JOIN CRM_naw_dossier ON CRM_naw.id= CRM_naw_dossier.rel_id GROUP BY CRM_naw.id  ORDER BY CRM_naw_dossier.datum desc  ";

 $query="CREATE TEMPORARY TABLE laatsteDossier
  SELECT CRM_naw.id as rel_id,
lastDossier.id,
CRM_naw_dossier.datum,
CRM_naw_dossier.type,
CRM_naw_dossier.duur,
CRM_naw_dossier.add_user,
CRM_naw_dossier.add_date,
CRM_naw_dossier.change_user,
CRM_naw_dossier.change_date,
lastSpoken.clientGesproken
FROM CRM_naw
LEFT JOIN (SELECT max(CRM_naw_dossier.id) as id,CRM_naw_dossier.rel_id FROM CRM_naw_dossier GROUP BY CRM_naw_dossier.rel_id ) as lastDossier ON CRM_naw.id=lastDossier.rel_id
LEFT JOIN CRM_naw_dossier ON lastDossier.id= CRM_naw_dossier.id
LEFT JOIN (SELECT MAX(if(CRM_naw_dossier.clientGesproken =1,CRM_naw_dossier.datum,null)) as clientGesproken ,CRM_naw_dossier.rel_id FROM CRM_naw_dossier GROUP BY CRM_naw_dossier.rel_id ) as lastSpoken ON CRM_naw.id= lastSpoken.rel_id";

      $db->SQL($query);
      $db->Query();
      $query="ALTER TABLE laatsteDossier ADD INDEX( rel_id ); ";
      $db->SQL($query);
      $db->Query();
*/
      $meerdereKoppelingen=true;
      $joinDossier=" LEFT JOIN CRM_naw_dossier  ON CRM_naw.laatsteGesprekId = CRM_naw_dossier.id ";
    }
    elseif($colData['objectname'] == 'CRM_portaalClienten' && !isset($CRM_portaalClienten))
    {
      $velden=array('id','change_user','change_date','add_user','add_date','portefeuille','name','name1','email','password','passwordChange','passwordTimes','loginTimes','loginLast','clientWW','verzendAanhef','accountmanagerNaam','accountmanagerGebruikerNaam','accountmanagerEmail','geblokkeerd','rel_id','depotbank','overRide2factor','accountmanagerTelefoon');
      $CRM_portaalClienten=true;
            $doel="CREATE TEMPORARY TABLE `CRM_portaalClienten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `change_user` varchar(10)  DEFAULT NULL,
  `change_date` datetime DEFAULT NULL,
  `add_user` varchar(10)  DEFAULT NULL,
  `add_date` datetime DEFAULT NULL,
  `portefeuille` varchar(20)  NOT NULL,
  `name` varchar(60)  NOT NULL,
  `name1` varchar(60)  NOT NULL,
  `email` varchar(60)  NOT NULL,
  `password` varchar(20)  NOT NULL,
  `passwordChange` datetime NOT NULL,
  `passwordTimes` int(11) NOT NULL,
  `loginTimes` int(11) NOT NULL,
  `loginLast` datetime NOT NULL,
  `clientWW` varchar(150)  NOT NULL,
  `verzendAanhef` varchar(255)  NOT NULL,
  `accountmanagerNaam` varchar(75)  NOT NULL,
  `accountmanagerGebruikerNaam` varchar(50)  NOT NULL,
  `accountmanagerEmail` varchar(50)  NOT NULL,
  `geblokkeerd` tinyint(4) NOT NULL,
  `rel_id` bigint(20) NOT NULL,
  `depotbank` varchar(10)  NOT NULL,
  `overRide2factor` tinyint(255) NOT NULL,
  `accountmanagerTelefoon` varchar(25)  NOT NULL,
  PRIMARY KEY (`id`),  KEY `portefeuille` (`portefeuille`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ";
       $DB=new DB(DBportaal);
       $query='SELECT '.implode(",",$velden)." FROM clienten";
       $DB->SQL($query);
       $DB->query();
       while($dbData=$DB->nextRecord())
         $clientData[]=$dbData;
  
      $DB=new DB();
      $DB->SQL($doel);
      $DB->query();
      foreach($clientData as $clientRecord)
      {
        foreach ($clientRecord as $key=> $item)
        {
          $clientRecord[$key]=mysql_real_escape_string($item);
        }
         $insert="INSERT INTO CRM_portaalClienten values('".implode("','",$clientRecord)."')";
        $DB->SQL($insert);
        $DB->query();
      }
      $joinExtra.=" LEFT JOIN CRM_portaalClienten  ON CRM_naw.portefeuille = CRM_portaalClienten.Portefeuille ";
      $extraTabelJoinAdded[]=$colData['objectname'];
    }
    elseif($colData['objectname'] == 'crmLaatsteFondsWaarden' && !isset($crmLaatsteFondsWaardenAdded))
    {
      $crmLaatsteFondsWaardenAdded=true;
      $joinExtra.=" LEFT JOIN crmLaatsteFondsWaarden ON CRM_naw.portefeuille=crmLaatsteFondsWaarden.Portefeuille ";
      $extraTabelJoinAdded[]=$colData['objectname'];
    }
    if($colData['objectname'] == 'taken' && !isset($takenAdded))
    {
      $takenAdded=true;
 $query="CREATE TEMPORARY TABLE laatsteTaak
SELECT CRM_naw.id as rel_id,
taken.id,
taken.add_date,
taken.kop,
taken.soort,
taken.afgewerkt,
taken.spoed,
taken.gebruiker,
taken.relatie,
taken.zichtbaar,
taken.add_user,
taken.change_user,
taken.change_date
FROM CRM_naw 
LEFT JOIN (SELECT max(taken.id) as id, taken.rel_id FROM taken GROUP BY taken.rel_id ) as lastTaak ON CRM_naw.id=lastTaak.rel_id
LEFT JOIN taken ON taken.id= lastTaak.id";
 
      $db->SQL($query);
      $db->Query();
      $query="ALTER TABLE laatsteTaak ADD INDEX( rel_id ); ";
      $db->SQL($query);
      $db->Query();
      $joinTaken="LEFT JOIN laatsteTaak as taken ON CRM_naw.id = taken.rel_id ";
    }
  }
  $joinWaarde="LEFT JOIN laatstePortefeuilleWaarde as laatstePortefeuilleWaarde ON CRM_naw.portefeuille = laatstePortefeuilleWaarde.portefeuille ";
  $list->ownTables=array('CRM_naw');
  $list->setJoin("$joinPortefeuilles $joinEvenementen  $joinCRM_naw_rekeningen  $joinCRM_naw_kontaktpersoon  $joinCRM_naw_adressen $joinDossier $joinTaken $joinGebruikers $joinWaarde $joinParticipanten $joinExtra");
  $list->queryWhere.=$where;
  $list->skipCloseForm=true;
  $list->extraButtons="
<script>

function uitvoerChange(type)
{
	if(type == 1 )
	{
			$('#mailingForm').show();
			document.editForm.toXls.value='2';
			$('#eMailingForm').hide();
			$('#digidocKoppelForm').hide();
	}
	else if(type == 2 )
	{
	  	$('#mailingForm').hide();
	  	$('#digidocKoppelForm').hide();
	  	document.editForm.toXls.value='3';
      doEditorOnload();
			$('#eMailingForm').show();
			document.editForm.mailingEmail.disabled=false;
	}
  else if(type   == 3  )
	{
	  	$('#mailingForm').hide();
	  	$('#eMailingForm').hide();
	  	$('#digidocKoppelForm').show();
	}
	else
	{
	  $('#mailingForm').hide();
		$('#eMailingForm').hide();
		$('#digidocKoppelForm').hide();
	}
}
</script>
<a href=\"?mailing=1\" onclick=\"uitvoerChange(1);\" ><img alt=\"naar XLS\" src=\"images/16/word.gif\" width=\"16\" height=\"16\" border=\"0\"> " . vt('Mailing') . " </a> &nbsp;&nbsp;\n
<a href=\"?mailing=2\" onclick=\"uitvoerChange(2);\" >".maakKnop('mail_new.png',array('size'=>16,'tooltip'=>'Mailing'))." " . vt('eMailing') . " </a> &nbsp;&nbsp;\n
<a href=\"?mailing=3\" onclick=\"uitvoerChange(3);\" >".maakKnop('mail_new.png',array('size'=>16,'tooltip'=>'Mailing'))." " . vt('Document koppelen') . " </a> &nbsp;&nbsp;\n
".(GetCRMAccess(2) && in_array($__appvar['bedrijf'],array('TEST','HOME','ANO','RRP'))?"<a href=\"?takenAanmaken=1\" >".maakKnop('note_new.png',array('size'=>16,'tooltip'=>'Taken'))." Taken aanmaken </a>":'')." &nbsp;&nbsp;\n
$mailingScript
";

//<img alt=\"naar XLS\" src=\"images/16/internetMail.gif\" width=\"16\" height=\"16\" border=\"0\">
if($_POST['toXls'] == '2') //mailing
{
  include_once("../classes/rtfMailing.php");

  if($_POST['adresGroup'])
    $list->setGroupBy('CRM_naw.verzendPc, CRM_naw.verzendAdres');

  $list->setXLS('');

  $_POST['evenement']=$_POST['evenementRtf'];
  $_POST['mailing']=$_POST['mailingRtf'];
//listarray($list->columns);
//listarray($list->getSQL());exit;

  $mailing = new rtfMailing($list->xlsData,$_POST);

  //$list->getXLS();
}

if($_POST['toXls'] == '3') //mailing
{
  include_once("../classes/CRMeMailing.php");

  if($_POST['adresGroup'])
    $list->setGroupBy('CRM_naw.verzendPc, CRM_naw.verzendAdres');

  $list->setXLS('');
  $mailing = new CRMeMailing();
  $_POST['evenement']=$_POST['evenementEmail'];

  $_POST['mailing']=$_POST['mailingEmail'];
  $mailing->prepareMails($list->xlsData,$_POST);
  //$list->getXLS();
}

// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);


$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

if(isset($_SESSION['verzendStatus']))
{
  $verzendStatus=$_SESSION['verzendStatus'];
  unset($_SESSION['verzendStatus']);
}

$content['body'] = 'onload="javascript:uitvoerChange('.$_GET['mailing'].');"';

if($meerdereKoppelingen==true)
  $meerdereKoppelingenTxt="<br>\n<div  class='edit_actionTxt' style='font-size: 12px; color:red'>&nbsp;  " . vt('Let op; er kunnen meerdere regels per relatie worden getoond') . "</div>";
else
  $meerdereKoppelingenTxt='';

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
&nbsp;  <b>$mainHeader</b> $subHeader $meerdereKoppelingenTxt
</div><br>$verzendStatus";

$content['javascript'] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}

function wwwTo(ml)
{

  if (ml.search(/http:/i) < 0)   // -1 is not found
  ml = \"http://\"+ml;

  window.open(ml,'nieuwVenster');
}

";
//echo $__appvar["basedir"].$__appvar["templateContentHeader"];

echo template($__appvar["templateContentHeader"],$content);



echo $list->filterHeader();


if($_GET['mailing'])
{
?>
<div id="wrapper" style="overflow:hidden;"> 
<div class="buttonDiv" style="width:150px;float:left;" onclick="checkAll(1);">&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> <?= vt('Alles selecteren'); ?></div>
<div class="buttonDiv" style="width:150px;float:left;" onclick="checkAll(0);">&nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon' /> <?= vt('Niets selecteren'); ?></div>
<div class="buttonDiv" style="width:150px;float:left;" onclick="checkAll(-1);">&nbsp;&nbsp;<img src='icon/16/replace2.png' class='simbisIcon' /><?= vt('Selectie omkeren'); ?></div>
</div>
<script language="JavaScript" TYPE="text/javascript">
function checkAll(optie)
{
  var theForm = document.editForm.elements, z = 0;
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
</script>
<br /><br />
<?
}
?>
<table class="list_tabel" cellspacing="0">

  <?
if(!$_GET['mailing'])
{
echo '<tr><td colspan="20">';
for($a=65; $a <= 90; $a++)
	echo "<a href=\"".$PHP_SELF."?letter=".chr($a)."&actief=".$actief."\" class=\"letterButton\">".chr($a)."</a>\n";
?>
<a href="<?=$PHP_SELF?>" class="letterButton" style="width:26px"><?= vt('alles'); ?></a><br><br>
</td></tr>
<?php
}
$header=$list->printHeader();
if($_GET['mailing'])
{
  $pattern = '~<a href=.*</span>(.*)</a>~';
  $header = preg_replace($pattern, '$1', $header);
}
echo $header;
$_SESSION['submenu'] = New Submenu();
//// call 8142
if ($__appvar["bedrijf"] == "AND" OR $__appvar["bedrijf"] == "HOME" OR $__appvar["bedrijf"] == "ANO")
{

  $_SESSION["submenu"]->addItem("Name screening","sanctieInput.php",array("style"=>"background-color: #E6FAE6;"));
}

$_SESSION['submenu']->addItem(vt("verjaardaglijst"),"CRM_naw_verjaardag.php");
$_SESSION['submenu']->addItem("<br>","");
$_SESSION['submenu']->addItem(vt("Print laatste verslagen"),"CRM_naw_dossierPrint.php?categorie=".$_GET['sql'],array('target'=>'_blank'));
$_SESSION['submenu']->addItem(vt("Print laatste activiteit"),"CRM_naw_dossierPrint.php?type=activiteit&categorie=".$_GET['sql'],array('target'=>'_blank'));
$_SESSION['submenu']->addItem(vt("Print vergeten contact"),"CRM_naw_dossierPrint.php?type=vergeten&categorie=".$_GET['sql'],array('target'=>'_blank'));
$_SESSION['submenu']->addItem(vt("Print contact planning"),"CRM_naw_dossierPrint.php?type=planning&categorie=".$_GET['sql'],array('target'=>'_blank'));
$_SESSION['submenu']->addItem(vt("Brief instellingen"),"CRM_rtfBriefOpmaak.php");
if(!isset($__appvar["crmOnly"]))
{
  $_SESSION['submenu']->addItem(vt("Rapportage instellingen"), "CRM_rapportageInstelling.php", array('target' => '_blank'));
}
$_SESSION['submenu']->addItem(vt("Print afspraken"),"CRM_naw_dossierPrint.php?type=afspraken&categorie=".$_GET['sql'],array('target'=>'_blank'));

if(!isset($__appvar["crmOnly"]) && GetCRMAccess(1))
{
  $_SESSION['submenu']->addItem(vt("Rapportage selectie"), "CRM_rapportageSelectie.php");
  if($__appvar['bedrijf']=='TEST'||$__appvar['bedrijf']=='RCN')
  $_SESSION['submenu']->addItem(vt("Rapportage selectie detail"),"CRM_rapportageSelectieDetail.php");
}
if(GetCRMAccess(2))
{
  $_SESSION['submenu']->addItem(vt("Zoek en vervang"),"CRMSearchReplace.php");
}


$_SESSION['submenu']->addItem($html,"");

$_SESSION['lastListQuery']=$list->sqlQuery;

$_SESSION['savedReturnUrl'] = $_SERVER["REQUEST_URI"];

if($gebruikPortefeuilleInformatie['check_module_SCENARIO'] > 0)
  $list->editIconTd='width="60"';
else
  $list->editIconTd='width="40"';

$htmlRapportageEnabled = getVermogensbeheerderField("HTMLRapportage");

$list->noClick = true;
while($data = $list->getRow())
{
  if ( isset ($data['CRM_naw.naam']) ) {$data['CRM_naw.naam']['noClick'] = false;}

  if ( $htmlRapportageEnabled )
  {
    if (isset ($data['CRM_naw.zoekveld']))
    {
      $data['CRM_naw.zoekveld']['noClick'] = false;
    }
    if (isset ($data['CRM_naw.portefeuille']))
    {
      $data['CRM_naw.portefeuille']['noClick'] = false;
    }

    if (isset ($data['laatstePortefeuilleWaarde.laatsteWaarde']))
    {
      $data['laatstePortefeuilleWaarde.laatsteWaarde']['url'] = 'rapportFrontofficeClientAfdrukkenHtml.php?rapport_types=VOLK&Portefeuille=' . $data['CRM_naw.portefeuille']['value'];
    }

    if (isset ($data['laatstePortefeuilleWaarde.rendement']))
    {
      $data['laatstePortefeuilleWaarde.rendement']['url'] = 'rapportFrontofficeClientAfdrukkenHtml.php?rapport_types=ATT&Portefeuille=' . $data['CRM_naw.portefeuille']['value'];
    }

    if (isset ($data['CRM_naw.portefeuille']) && ! empty ($data['CRM_naw.portefeuille']['value']) )
    {
      $data['CRM_naw.portefeuille']['url'] = $__appvar['baseurl'].'/HTMLrapport/dashboard.php?port=' . $data['CRM_naw.portefeuille']['value'];
    }
  }

  $list->editIconExtra='<a class="icon" href="CRM_nawEdit.php?action=edit&id='.$data['id']['value'].'&lastTab=9&frameSrc='.base64_encode("CRM_naw_dossierEdit.php?action=new&toList=1&rel_id=".$data['id']['value']).'"> '.maakKnop('note_new.png',array('size'=>16,'tooltip'=>'Nieuw gespreksverslag')).'</a>';//<img align="absmiddle" src="images/taken.gif" alt="Nieuw gespreksverslag" width="16" height="16" border="0">
  if($gebruikPortefeuilleInformatie['check_module_SCENARIO'] > 0)
  {
    $list->editIconExtra.='<a class="icon" href="CRM_nawScenario.php?action=edit&id='.$data['id']['value'].'"> '.maakKnop('money.png',array('size'=>16,'tooltip'=>'Scenario analyse')).'</a>';
  }
//  $data['CRM_naw.zoekveld']['noClick']=true;
  if($_GET['mailing'])
  {
    $query="SELECT CRM_naw_adressen.* FROM CRM_naw_adressen WHERE (CRM_naw_adressen.rapportage=1 or CRM_naw_adressen.evenement='rapportage') AND CRM_naw_adressen.rel_id='".$data['id']['value']."'";
    if($db->QRecords($query))
      $extraAdres=vt('Extra adres aanwezig');
    else
      $extraAdres='';
    $data['test']['noClick']=true;
    $data['test']['value']="<input type='checkbox' checked value='".$data['id']['value']."' name='check_".$data['id']['value']."'> $extraAdres";
  }
  //if($db->QRecords("SELECT CRM_naw.id FROM CRM_naw JOIN Portefeuilles ON CRM_naw.Portefeuille=Portefeuilles.Portefeuille WHERE Portefeuilles.consolidatie=1 AND CRM_naw.id='".$data['id']['value']."'"))
  if($db->QRecords("SELECT id FROM CRM_naw WHERE PortGec=1 AND id='".$data['id']['value']."'"))
    $data["tr_class"] = "list_dataregel_geel";

  echo $list->buildRow($data);
}


?>
</table>
</form>
  
  <script type='text/javascript'>
    
    
    <?=$customTemplate->getTemplateSelectAjax();?>
    <?=$customTemplate->addContentToField('body', 'addSignatureToBody', $_SESSION['usersession']['gebruiker']['emailHandtekening']);?>
  
  </script>


<?
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);

function portalCheck()
{
  $query = "
    SELECT 
      Vermogensbeheerders.check_portaalDocumenten,
      Vermogensbeheerders.Vermogensbeheerder
    FROM 
      (Vermogensbeheerders) 
    INNER JOIN VermogensbeheerdersPerGebruiker ON 
      Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder 
    WHERE 
      VermogensbeheerdersPerGebruiker.Gebruiker =  '".$_SESSION["USR"]."' 
    GROUP BY 
      VermogensbeheerdersPerGebruiker.Gebruiker ";
  $DB = new DB();
  $rec = $DB->lookupRecordByQuery($query);
  return ($rec["check_portaalDocumenten"] == 1);

}
?>