<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/07/11 17:29:42 $
 		File Versie					: $Revision: 1.19 $

 		$Log: rapportBackofficeClientOpmaak.php,v $
 		Revision 1.19  2020/07/11 17:29:42  rvv
 		*** empty log message ***

*/
//$AEPDF2=true;

$customTemplate = new AE_CustomTemplate('backofficeEmail');

function getFields()
{

  $categorieVolgorde=array('Naw'=>array("Algemeen","Adres","Verzendadres","Telefoon","Internetgegevens","Bedrijfinfo","Persoonsinfo","Legitimatie","Informatie partner","Legitimatie partner","Adviseurs","geen",'Extra algemeen','Beleggen','Rapportage','Profiel','Relatie geschenk'),
                           'Portefeuilles'=>array('Gegevens','Beheerfee','Staffels'));

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
  
  $AccountmanagerVelden=array('Accountmanager'=>array('Titel','Titel2'),'tweedeAanspreekpunt'=>array('Titel','Titel2'));
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
            if(isset($AccountmanagerVelden[$veld]))
            {
              foreach($AccountmanagerVelden[$veld] as $index=>$veldNaam)
              {
                $html_opties .= "<label for=\"".$veld.$veldNaam."\" title=\"".$veld.$veldNaam."\"> {".$veld.$veldNaam."} </label><br>\n";
              }
            }
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


if($_POST['testmail']==1)
{
  $mail = new PHPMailer();
  $mail->IsSMTP();
  if($_POST['debug']==1)
    $mail->SMTPDebug=9;
  else
    $mail->SMTPDebug=1;
  $mail->From     = $_POST['afzenderEmail'];
  $mail->FromName = $_POST['afzender'];
  $_POST['email']=$mail->encodeInlineImages($_POST['email']);
  $mail->Body    = $_POST['email'];
  $mail->AltBody = html_entity_decode(strip_tags($_POST['email']));
  $mail->AddAddress($_POST['afzenderEmail'],$_POST['afzender']);
  $mail->Subject = $_POST['onderwerp'];
  if(!$mail->Send())
  {
    echo vt("Fout bij het zenden naar")." " .$_POST['afzenderEmail']. "<br>\n";
  }
  else
  {
    echo vt("Email is verzonden naar")." ".$_POST['afzenderEmail'].". <br>\n";
  }
}


$db=new DB();
//$query="SELECT substring(`field`,9) as mailing FROM ae_config WHERE `field` like 'mailing\_%' AND value = ( SELECT value FROM ae_config WHERE `field`='mailingBody');";
//$db->SQL($query);
//$huidigeMailing=$db->lookupRecord();
$huidigeMailing=$_POST['mailingEmail'];//$huidigeMailing['mailing'];
$query = "SELECT substring(`field`,9) as mailing,field  FROM ae_config WHERE field like 'mailing\_%' "; //LIMIT 10
$db->SQL($query);
$db->Query();
$mailingTemplate=array();
while ($rapItems = $db->nextRecord())
  $mailingTemplate[]=$rapItems;


if (count($mailingTemplate) > 0)
{
  $mailingSelect = "<select name=\"mailingEmail\" onchange=\"document.selectForm.stap.value='opmaak';saveSettings();\" > <option value=''>---</option>";
  foreach ($mailingTemplate as $rapItems)
  {
    if($huidigeMailing==$rapItems["field"])
      $selected="selected";
    else
      $selected='';
    $mailingSelect .= "<option $selected value=\"".$rapItems["field"]."\" >".$rapItems["mailing"]."</option>\n";
  }
  $mailingSelect  .= "</select>";
}


if($_POST['mailingEmail'])
{
  $data['email']=$cfg->getData($_POST['mailingEmail']);
}

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem(vt("Verstuur testmail"),"javascript:parent.frames['content'].testMail();");
$_SESSION['submenu']->addItem($html,"");
//$_SESSION['submenu']->addItem('eMail opmaak','kwartaalBriefEdit.php?brief=eMailopmaak&titel=eMailtitel');

$_SESSION['submenu']->addItem(getFields(),"");
$_SESSION['NAV'] = "";


$content['javascript'].="


function loadEditor(textarea,h,w)
{
  CKEDITOR.replace( textarea ,
	{
    height: h,
		width: w,
    uiColor: '#9AB8F3',
    allowedContent: true ,
    font_names:'Arial/Arial, Helvetica, sans-serif;Roboto/Roboto, sans-serif;Comic Sans MS/Comic Sans MS, cursive;Courier New/Courier New, Courier, monospace;Georgia/Georgia, serif;Lucida Sans Unicode/Lucida Sans Unicode, Lucida Grande, sans-serif; Tahoma/Tahoma, Geneva, sans-serif; Times New Roman/Times New Roman, Times, serif; Trebuchet MS/Trebuchet MS, Helvetica, sans-serif; Calibri/Calibri, Verdana, Geneva, sans-serif; Verdana/Verdana, Geneva, sans-serif'
	});
	
}

function doEditorOnload()
 { 
   loadEditor('email',500,1000);
   loadEditor('brief',500,1000);
 }

function saveSettings()
{
	document.selectForm.target = '';
	document.selectForm.submit();
}

function testMail()
{
	document.selectForm.target = '';
  document.selectForm.testmail.value = '1';
  document.selectForm.stap.value='opmaak';
	document.selectForm.submit();  
}  

";
if($_SESSION['backofficeSelectie']['inclBrief'] < 1)
  $content['body']='onLoad="doEditorOnload();$(\'#BriefSettings\').hide();"';
else
  $content['body']='onLoad="doEditorOnload();"';

$content['jsincludes'].='<script type="text/javascript" src="javascript/ckeditor4/ckeditor.js"></script>';
echo template($__appvar["templateContentHeader"],$content);

?>
<script type="text/javascript">

</script>

<br><br>
<div class="tabbuttonRow">
<?
$opmaakStyle='tabbuttonInActive';
$selectieStyle='tabbuttonInActive';
$samenvattingStyle='tabbuttonInActive';
$productieStyle='tabbuttonInActive'; 
if($_SESSION['backofficeSelectie']['stap'] == 'opmaak')
  $opmaakStyle='tabbuttonActive';
elseif($_SESSION['backofficeSelectie']['stap'] == 'samenvatting')
  $samenvattingStyle='tabbuttonActive';
elseif($_SESSION['backofficeSelectie']['stap'] == 'productie')
   $productieStyle='tabbuttonActive';   
else
  $selectieStyle='tabbuttonActive';
?>
	<input type="button" class="<?=$selectieStyle?>" onclick="document.selectForm.stap.value='selectie';saveSettings();" id="tabbutton0" value="<?=vt("Selectie")?>">
	<input type="button" class="<?=$opmaakStyle?>" onclick="document.selectForm.stap.value='opmaak';saveSettings();"  id="tabbutton1" value="<?=vt("Opmaak")?>">
	<input type="button" class="<?=$samenvattingStyle?>" onclick="document.selectForm.stap.value='samenvatting';saveSettings();"  id="tabbutton1" value="<?=vt("Samenvatting")?>">
	<input type="button" class="<?=$productieStyle?>" onclick="document.selectForm.stap.value='productie';saveSettings();"  id="tabbutton3" value="<?=vt("Productie")?>">
</div>
<br>

<form method="POST" name="selectForm">
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="stap" value="" />
<input type="hidden" name="save" value="" />
<input type="hidden" name="testmail" value="" />
<input type="hidden" name="exportRap" value="" />



<fieldset id="EmailSettings" >
  
  <div class="formblock">
    <div class="formlinks"><label for="factuurnr"><?=vt("Templates")?></label></div>
    <div class="formrechts">
      <select name="template" id="templateSelect"><?=$customTemplate->getTemplateSelect();?></select>
    </div>
  </div>
  
  
<div class="formblock">
<div class="formlinks"><?=vt("eMail afzender")?></div>
<div class="formrechts">
<input type="text" name="afzender" id="afzender" size=60 value="<?=$data['afzender']?>">
</div>
</div>
<div class="formblock">
<div class="formlinks"><?=vt("eMail afzender emailadres")?></div>
<div class="formrechts">
<input type="text" name="afzenderEmail" id="afzenderEmail" size=60 value="<?=$data['afzenderEmail']?>">
</div>
</div>

<div class="formblock">
<div class="formlinks"><?=vt("eMail cc emailadres")?></div>
<div class="formrechts">
<input type="text" name="ccEmail" id="ccEmail" size=60 value="<?=$data['ccEmail']?>">
</div>
</div>

<div class="formblock">
<div class="formlinks"><?=vt("eMail bcc emailadres")?></div>
<div class="formrechts">
<input type="text" name="bccEmail" id="bccEmail" size=60 value="<?=$data['bccEmail']?>">
</div>
</div>

<div class="formblock">
<div class="formlinks"><?=vt("eMail onderwerp")?></div>
<div class="formrechts">
<input type="text" name="onderwerp" id="onderwerp" size=60 value="<?=$data['onderwerp']?>">
</div>
</div>
  
  <div class="formblock">
    <div class="formlinks"><?=vt("eMail template laden")?></div>
    <div class="formrechts">
      <?=$mailingSelect?>
    </div>
  </div>

<div class="formblock">
<div class="formlinks"><label for="email" title="email"><?=vt("Email")?></label> </div>
<div class="formrechts">
<textarea class=""  cols="60"  rows="2" name="email" id="email" ><?=$data['email']?></textarea>
</div>
</div>

</fieldset>

<div id="BriefSettings">
<fieldset>
<div class="formblock">
<div class="formlinks"><label for="brief" title="brief">Brief</label> </div>
<div class="formrechts">
<textarea class=""  cols="60"  rows="2" name="brief" id="brief" ><?=$data['brief']?></textarea>
</div>
</div>
</fieldset>
</div>

</form>
  
  
  <script type='text/javascript'>
    
    
    <?=$customTemplate->getTemplateSelectAjax();?>
    
  </script>

<?
echo progressFrame();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
