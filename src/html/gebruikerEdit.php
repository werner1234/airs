<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/07/08 08:59:33 $
    File Versie         : $Revision: 1.29 $

    $Log: gebruikerEdit.php,v $
    Revision 1.29  2020/07/08 08:59:33  cvs
    SMS gegevens tonen


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar["listurl"]  = "gebruikerList.php";
$__funcvar["location"] = "gebruikerEdit.php";



$object = new Gebruiker();

$cfg = new AE_config();
$tfaSMS = ($cfg->getData("wwBeleid_2factor") == "aan");

$editcontent['jsincludes'] .= "
  <script language=JavaScript src=\"javascript/picker.js\" type=text/javascript></script>
  <script language=JavaScript src=\"javascript/popup.js\" type=text/javascript></script>
	<script src=\"javascript/jquery-min.js\" type=\"text/javascript\"></script>
	<script src=\"javascript/jquery.vreboton.ColorPicker.js\" type=\"text/javascript\"></script>
	";
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/tabbladen.js\" type=text/javascript></script>";
$editcontent['javascript'] .='jQuery(function($){$("#bgkleur").attachColorPicker();});


function moveItem(from,to){
	var tmp_text = new Array();
	var tmp_value = new Array();
 	for(var i=0; i < from.options.length; i++) {
 		if(from.options[i].selected) 
 		{
			var blnInList = false; 				
			for(j=0; j < to.options.length; j++) 
			{ 					
 				if(to.options[j].value == from.options[i].value) 
				{ 						
 					//alert("already in list"); 						
 					blnInList = true; 						
 					break; 					
 				} 				
			} 				
			if(!blnInList) 
 			{	 					
				to.options.length++; 					
				to.options[to.options.length-1].text = from.options[i].text; 					
				to.options[to.options.length-1].value = from.options[i].value; 		 				
			}
 		}
		else
		{
			tmp_text.length++;
			tmp_value.length++;
			tmp_text[tmp_text.length-1] = from.options[i].text;
			tmp_value[tmp_text.length-1] = from.options[i].value;
			
		} 
 	}
 	from.options.length = 0;
 	for(var i=0; i < tmp_text.length; i++) {
 		from.options.length++;
		from.options[from.options.length-1].text = tmp_text[i]; 					
		from.options[from.options.length-1].value = tmp_value[i]; 		 				
 	}
 	from.selectedIndex = -1;
}

function submitForm()
{
	geenToegang = document.editForm[\'geenToegang[]\'];
	welToegang =  document.editForm[\'welToegang[]\'];
	
	for(j=0; j < geenToegang.options.length; j++)
	{
 		geenToegang.options[j].selected = true; 
	}
	
	for(j=0; j < welToegang.options.length; j++)
	{
 		welToegang.options[j].selected = true; 
	}
	
	document.editForm.submit();
}

$(function() {
    tabOpen(\'0\');
});

';

$editObject = new editObject($object);
$editObject->__funcvar    = $__funcvar;
$editObject->__appvar     = $__appvar;
$editObject->template     = $editcontent;
$editObject->skipStripAll = true;

$data = array_merge($_POST,$_GET);
$returnUrl = $data["returnUrl"];
$action = $data['action'];

if($action=='update')
{
  if(count($data['welToegang'])==2 && in_array('all',$data['welToegang']) && in_array('inaktief',$data['welToegang']))
    $data['CRM_relatieSoorten']='';
  else
    $data['CRM_relatieSoorten']=serialize($data['welToegang']);

  $orderRechtenExport=array();
  foreach ($__ORDERvar['orderRechten'] as $hoofdGroep => $hoofdgroepData)
  {

    $orderRechtenHtml .= "<b>".$hoofdgroepData['description'] . "</b><br>\n";
    foreach ($hoofdgroepData['opties'] as $optie => $omschrijving)
    {
      $orderRechtenKey=$hoofdGroep."_".$optie;

      if(isset($_POST["orderRechten_".$orderRechtenKey]) && $_POST["orderRechten_".$orderRechtenKey]==1)
        $orderRechtenExport[$orderRechtenKey]=1;
      else
        $orderRechtenExport[$orderRechtenKey]=0;
     }
  }
  $data['orderRechten']=serialize($orderRechtenExport);
  
  $emailExport=array();
  foreach($__appvar["emailBronnen"] as $key=>$omschrijving)
  {
    if(isset($_POST["email_".$key]) && $_POST["email_".$key]==1)
      $emailExport[$key]=1;
    else
      $emailExport[$key]=0;
  }
  $data['emailRechten']=serialize($emailExport);
}

$editObject->formTemplate = "gebruikerTemplate.html";
$editObject->usetemplate = true;

$editObject->controller($action,$data);
$editObject->formVars['wwb'] = "";



include_once $__appvar["basedir"].'/classes/AE_cls_secruity.php';
$sec = new AE_cls_secruity($object->get("Gebruiker"));

$editObject->formVars["WWreset"] = "";
$editObject->formVars["smsCodes"] = "";

if ($sec->beleid <> "")
{
  $editObject->formVars["WWreset"] = "<button id='btnWwReset'> ".vt("reset gebruikerswachtwoord")." </button>";

}

//if ($tfaSMS)
{
  $editObject->formVars["smsCodes"] = "
  <div style='border-radius: 6px; border: 1px #999 solid; padding: 6px; background: #f6f6f6; position: absolute; right: 20px;'>
    <span style='width: 100px; display: inline-block'>".vt("SMScode")."</span>: ".$sec->loginRec["laatsteSMScode"]."<br>
    <span style='width: 100px; display: inline-block'>".vt("Vanaf IP")."</span>: ".$sec->loginRec["laatsteIP"]."<br/>
    <span style='width: 100px; display: inline-block'>".vt("Geldig tot")."</span>: ".$sec->smsValidTill."  
  </div>";
}

if ($sec->tfa)  // google twee factor
{
  $editObject->formVars["WWreset"] .= "<button id='btnTfaReset'> ".vt("reset Google twee factor")." </button>";
}

if ($_POST["wwReset"] == "true")
{
  $sec->deleteLogin($_POST["Gebruiker"]);
  $editObject->formVars['wwb'] = "<div class='wwbDialog' >".vt("Het wachtwoord van")." ".$_POST["Gebruiker"]." ".vt("is gereset naar de standaard waarde")."</div>";
}

if ($_POST["wwReset"] == "tfa") // Google twee factor
{
  include_once "../classes/AE_cls_2factor.php";
  $uId = $_REQUEST["id"];
  $tfa = new AE_cls_2factor($uId);
  $tfa->resetSecretToUseraccount();
  $editObject->formVars['wwb'] = "<div class='wwbDialog' >".vt("Google twee factor voor")." ".$_POST["Gebruiker"]." ".vt("is gereset")."</div>";
}

$db=new DB();
if ($action == "edit")
{
	session_start();
  
	$_SESSION['submenu'] = New Submenu();
	$_SESSION['submenu']->addItem(vt("Vermogensbeheerder"),"gebruikerVermogensbeheerderKoppel.php?Gebruiker=".$object->get("Gebruiker"));
  $opties=array(
    'all'       => 'Alle relaties',
    'inaktief'  => 'Inaktieven'
  );
  $huidigeOpties=unserialize($object->get('CRM_relatieSoorten'));
  if(!is_array($huidigeOpties))
    $huidigeOpties=array_keys($opties);
    
  
  $query="SELECT veldnaam,omschrijving FROM CRM_eigenVelden WHERE relatieSoort=1";
  $db->SQL($query);
  $db->Query();
  while ($data=$db->nextRecord())
  {
    $opties[$data['veldnaam']]=$data['omschrijving'];
  }

	foreach($opties as $veld=>$omschrijving)
  {
    $omschrijving = vt($omschrijving);
    if (in_array($veld,$huidigeOpties))
    	$welToegang .= "<option value=\"".$veld."\" >".$omschrijving."</option>\n";
    else 
      $geenToegang .= "<option value=\"".$veld."\" >".$omschrijving."</option>\n";
  }
  $editObject->formVars['welToegang']   = $welToegang;
  $editObject->formVars['geenToegang']  = $geenToegang;
  //if($editObject->object->get('Beheerder')==1)
  //  $editObject->object->setOption('Beheerder','form_options',array(-1=>"Lezer",0=>"Gebruiker",1=>"Beheerder"));
  
  session_write_close();
  
}

if($object->get('Gebruiker')<>'')
  $gebruiker=$object->get('Gebruiker');
else
  $gebruiker=$USR;  
$query  = "SELECT OrderOrderdesk as orderdesk, check_module_ORDER as orderVersie, Vermogensbeheerders.Vermogensbeheerder FROM Vermogensbeheerders JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder=VermogensbeheerdersPerGebruiker.Vermogensbeheerder WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$gebruiker' limit 1";
$db->SQL($query);
$db->Query();
$data = $db->nextRecord();
if($data['orderdesk']==0)
{
  unset($editObject->object->data['fields']['orderdesk']);
  $editObject->formVars['orderdesk_description']='';
  $editObject->formVars['orderdesk_inputfield']='';
}

if($data['orderVersie']==2)
{
  $unsetVelden = array('ordersNietAanmaken', 'ordersNietVerwerken');
  foreach ($unsetVelden as $veld)
  {
    unset($editObject->object->data['fields'][$veld]);
    $editObject->formVars[$veld . '_description'] = '';
    $editObject->formVars[$veld . '_inputfield'] = '';
  }

 // listarray($__ORDERvar['orderRechten']);
  $orderRechtenHtml = '';
  $huidigeRechten=unserialize($editObject->object->get('orderRechten'));
  foreach ($__ORDERvar['orderRechten'] as $hoofdGroep => $hoofdgroepData)
  {
    if($orderRechtenHtml<>'')
      $orderRechtenHtml .= "<br>";
    $orderRechtenHtml .= "<b>".vt($hoofdgroepData['description']) . "</b><br>\n";
    foreach ($hoofdgroepData['opties'] as $optie => $omschrijving)
    {
      $rechtenKey=$hoofdGroep."_".$optie;
      if($huidigeRechten[$rechtenKey]==1)
        $checked="checked";
      else
        $checked="";
      $orderRechtenHtml .= "<input type='checkbox' $checked name='orderRechten_".$hoofdGroep."_".$optie."' id='orderRechten_".$rechtenKey."' value='1' > ".vt($omschrijving)." <br>\n";
    }
  }
  $editObject->formVars['orderRechten'] = $data['Vermogensbeheerder']." ".vt("orderversie")." ".$data['orderVersie']."<br>\n".$orderRechtenHtml;
}
else
  $editObject->formVars['orderRechten'] = $data['Vermogensbeheerder']." ".vt("orderversie")." ".$data['orderVersie'];


$emailRechtenHtml = '';
$huidigeRechten=unserialize($editObject->object->get('emailRechten'));
foreach($__appvar["emailBronnen"] as $key=>$omschrijving)
{
  if($emailRechtenHtml<>'')
    $emailRechtenHtml .= "<br>";

  if($huidigeRechten[$key]==1)
    $checked="checked";
  else
    $checked="";
  $emailRechtenHtml .= "<input type='checkbox' $checked name='email_".$key."' id='email_".$key."' value='1' > ".vt($omschrijving)." \n";
}
$editObject->formVars['emailRechten'] = $emailRechtenHtml;


echo $editObject->getOutput();


if ($result = $editObject->result)
{

    header("Location: ".$returnUrl);
    
}
else {
	echo $_error = $editObject->_error;

}

if ($_SESSION['btr']) {
  echo '<script> tabOpen(\'0\'); </script>';  
}