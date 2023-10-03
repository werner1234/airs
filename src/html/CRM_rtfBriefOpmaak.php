<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/12/05 13:39:16 $
 		File Versie					: $Revision: 1.17 $

 		$Log: CRM_rtfBriefOpmaak.php,v $
*/
include_once("wwwvars.php");
session_start();




function getFields()
{

  $categorieVolgorde=array('Naw'=>array("Algemeen","Adres","Verzendadres","Telefoon","Internetgegevens","Bedrijfinfo","Persoonsinfo","Legitimatie","Informatie partner","Legitimatie partner","Adviseurs","geen",'Contract','Beleggen','Rapportage','Profiel','Relatie geschenk'),
                           'Portefeuilles'=>array('Gegevens','Beheerfee','Staffels'),
                           'Speciale velden'=>array('Opmaak'));
  $velden['Opmaak']['leegNietTonen']=array('description'=>'Indien leeg, deze regel niet tonen.');
  $velden['Opmaak']['huidigeDatum']=array('description'=>'De huidige datum.');
  $velden['Opmaak']['huidigeGebruiker']=array('description'=>'De huidige gebruiker.');
  $velden['Opmaak']['GebruikerNaam']=array('description'=>'Naam huidige gebruiker.');
  $velden['Opmaak']['GebruikerTitel']=array('description'=>'Titel huidige gebruiker.');
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
  $extraOpties=array('RapportageValuta','Remisier','tweedeAanspreekpunt','Accountmanager','Depotbank','Client','Vermogensbeheerder');
  foreach ($categorieVolgorde as $table=>$categorien)
  {
    $html_opties .= "<b>$table</b>";
    foreach ($categorien as $categorie)
    {
      $html_opties .= "<div class=\"menutitle\" onclick=\"SwitchMenu('sub$table$categorie')\">$categorie</div><span class=\"submenu\" id=\"sub$table$categorie\">\n";
      foreach ($velden[$categorie] as $veld=>$waarden)
      {
          $html_opties .= "<label for=\"".$veld."\" title=\"".$waarden['description']."\"> {".$veld."} </label><br>\n";
          if($table == 'Portefeuilles' && substr($waarden['form_type'],0,6)=='select' && in_array($veld,$extraOpties))
          {
            $html_opties .= "<label for=\"*".$veld."\" title=\"*".$waarden['description']."\"> {*".$veld."} </label><br>\n";
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
<br><br><b>CRM velden</b>
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

//$_SESSION[NAV]='';
$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content['javascript'].="
function loadEditor(textarea,h,w)
{
  CKEDITOR.replace( textarea ,
	{
		skin : 'office2003',
		DIALOG_RESIZE_NONE : true,
		height: h,
		width: w,
    toolbar :
    [
      ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
      ['Bold', 'Italic', 'Underline','-','Maximize','Source']
    ]
	});
}

function doEditorOnload()
{
  loadEditor('kop',100,600);
  loadEditor('naw',150,600);
  loadEditor('body',400,600);
  loadEditor('voet',100,600);
  //CKEDITOR.replace( 'voet' );
}

function submitForm()
{
	document.editForm.submit();
}

function previewRtf()
{
document.editForm.target='_blank';
document.editForm.action.value='preview';
document.editForm.submit();
document.editForm.target='_self';
document.editForm.action.value='';
}
 ";
$content['body']='onLoad="doEditorOnload();"';

$content['jsincludes'].='<script type="text/javascript" src="javascript/ckeditor/ckeditor.js"></script>';

$cfg=new AE_config();
if($_POST)
{
  $data=$_POST;
 
  foreach($data as $key=>$value)
  {
    if(!is_array($data[$key]))
      $data[$key]=html_entity_decode($data[$key],ENT_QUOTES);
     // $data[$key]=htmlspecialchars_decode($data[$key]);
      
  }

  if($data['action']=='delete')
    $data['body']='';


  if($data['action']=='preview')
  {
    include_once("../classes/rtfMailing.php");

    if($_POST['groupAdres'])
      $list->setGroupBy('CRM_naw.verzendPc, CRM_naw.verzendAdres');

    $mailing = new rtfMailing(array(array(array('naam','header')),array(array('naam','body'))),$data);
    exit;
  }
 
  
  $cfg->addItem('nawRegelHoogte',addslashes($data['nawRegelHoogte']));
  $cfg->addItem('mailingKop',addslashes($data['kop']));
  $cfg->addItem('mailingNaw',addslashes($data['naw']));
  $cfg->addItem('mailingBody',addslashes($data['body']));
  $cfg->addItem('mailingVoet',addslashes($data['voet']));

  
    if($data['nawBodyName'])
  {
    if($data['body'] == '')
      $cfg->deleteField('mailing_'.$data['nawBodyName']);
    else
      $cfg->addItem('mailing_'.$data['nawBodyName'],addslashes($data['body']));

    if($data['body'] == '')
      $cfg->deleteField('mailingNaw_'.$data['nawBodyName']);
    else
      $cfg->addItem('mailingNaw_'.$data['nawBodyName'],addslashes($data['naw']));
  }
  $name['mailing']=$data['nawBodyName'];
}
else
{
  $data['nawRegelHoogte']=$cfg->getData('nawRegelHoogte');
  $data['kop']=$cfg->getData('mailingKop');
  $data['naw']=$cfg->getData('mailingNaw');
  $data['body']=$cfg->getData('mailingBody');
  $data['voet']=$cfg->getData('mailingVoet');
  $query="SELECT substring(`field`,9) as mailing FROM ae_config WHERE `field` like 'mailing\_%' AND value = ( SELECT value FROM ae_config WHERE `field`='mailingBody');";
  $db=new DB();
  $db->SQL($query);
  $name=$db->lookupRecord();
}

echo template($__appvar["templateContentHeader"],$content);


$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->returnUrl = 'CRM_nawList.php';
$_SESSION['NAV']->addItem(new NavEdit("editForm",true,true,true));

if($_GET['setBody'])
{
  $data['body']=$cfg->getById($_GET['setBody']);
  $query="SELECT substring(`field`,9) as mailing FROM ae_config WHERE id='".$_GET['setBody']."'";
  $db=new DB();
  $db->SQL($query);
  $name=$db->lookupRecord();

  $query="SELECT value FROM ae_config WHERE field='mailingNaw_".$name['mailing']."'";
  $db=new DB();
  $db->SQL($query);
  $tmp=$db->lookupRecord();

  if($tmp['value'] <> '')
    $data['naw']=$tmp['value'];

}

?>
<form method="POST" name="editForm">
<div class="formblock">
<div class="formlinks"><label for="kop" title="kop"><?= vt('Koptekst'); ?></label> </div>
<div class="formrechts">
  <?= vt('Regelafstand'); ?>:
  <select name="nawRegelHoogte">
    <OPTION VALUE="1" <?if($data['nawRegelHoogte']=='1')echo "SELECTED";?>>1
    <OPTION VALUE="1.5" <?if($data['nawRegelHoogte']=='1.5')echo "SELECTED";?>>1.5
  </select>
<textarea class=""  cols="60"  rows="2" name="kop" id="kop" ><?=$data['kop']?></textarea>
</div>
</div>

<div class="formblock">
<div class="formlinks"><label for="naw" title="naw"><?= vt('NAW gegevens'); ?></label> </div>
<div class="formrechts">
<textarea class=""  cols="60"  rows="5" name="naw" id="naw" ><?=$data['naw']?></textarea>
</div>
</div>

<div class="formblock">
<div class="formlinks"><label for="body" title="body"><?= vt('Body tekst'); ?></label> </div>
<div class="formrechts">
<textarea class=""  cols="60"  rows="10" name="body" id="body"><?=$data['body']?></textarea>
</div>
</div>

<div class="formblock">
<div class="formlinks"><label for="voet" title="voet"><?= vt('Voettekst'); ?></label> </div>
<div class="formrechts">
<textarea class=""  cols="60"  rows="2" name="voet" id="voet" ><?=$data['voet']?></textarea>
</div>
</div>

<div class="formblock">
<div class="formlinks"><?= vt('Body oplaan als'); ?>:</div>
<div class="formrechts">
<input type="text" name="nawBodyName" MAXLENGTH="50" value="<?=$name['mailing']?>">
</div>
</div>

<div class="formblock">
<div class="formlinks"> </div>
<div class="formrechts">
<input type="hidden" name="action" value="">

<input type="button" onclick="javascript:previewRtf();" name="preview" value="Preview">

</div>
</div>



</form>
<?

$fields = getFields();

$_SESSION[submenu] = New Submenu();

$db = new DB();
$query = "SELECT id,field FROM ae_config WHERE field like 'mailing\_%' "; //LIMIT 10
$db->SQL($query);
$db->Query();
if ($db->records() > 0)
{
  $_SESSION[submenu]->addItem("<br>Laad body tekst<br>","");
  $rapJavaSelect .= "\n<SCRIPT LANGUAGE = \"JavaScript\"  TYPE=\"text/javascript\">\n";
  $rapJavaSelect .= "function OpenRap()\n";
  $rapJavaSelect .= "{\nvar item = document.rapmenu.raportages.selectedIndex;\n";
  $rapJavaSelect .= "id = document.rapmenu.raportages.options[item].value;\n";
  $rapJavaSelect .= "id = document.rapmenu.raportages.options[item].value;\n";
  $rapJavaSelect .= "parent.content.location.href=\"CRM_rtfBriefOpmaak.php?setBody=\"+(id);\n}\n";
  $rapJavaSelect .= "</SCRIPT>\n";

  $rapMenu .= "\n <form action=\"reportBuilder.php\" method=\"post\" name=\"rapmenu\"> \n";
  $rapMenu .= "<select name=\"raportages\" size=\"10\" style=\"width:120px; font-size: 10px;\" onChange=\"OpenRap()\"> \n";

  while ($rapItems = $db->nextRecord())
  {
	$rapMenu .= "<option value=\"".$rapItems["id"]."\">".substr($rapItems["field"],8)."</option>\n";
  }
  $rapMenu  .= "</select>";
  $_SESSION['submenu']->addItem("$rapJavaSelect $rapMenu","");
}

$_SESSION[submenu]->addItem("<br>","");

$_SESSION[submenu]->addItem($fields,"");



echo template($__appvar["templateRefreshFooter"],$content);


?>