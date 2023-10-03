<?php
/*
    AE-ICT CODEX source module versie 1.6, 6 augustus 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/08/14 16:30:08 $
    File Versie         : $Revision: 1.28 $

    $Log: crm_naw_templatesEdit.php,v $
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");




function getFields($type='')
{
  if($type=='intake')
    $objecten=array('Naw'=>'Naw');
  else
    $objecten=array('Naw'=>'Naw','Portefeuilles'=>'Portefeuilles','CRM_naw_adressen'=>'Adressen','CRM_naw_kontaktpersoon'=>'Contactpersoon');

  foreach ($objecten as $objectnaam=>$omschrijving)
  {
    $naw = new $objectnaam();
    $veldenKey=array();

    foreach ($naw->data['fields'] as $key=>$values)
     $veldenKey[]=$key;
    natcasesort($veldenKey);
    $html_opties .= "<div class=\"menutitle\" onclick=\"SwitchMenu('sub$objectnaam')\">$omschrijving</div><span class=\"submenu\" id=\"sub$objectnaam\">\n";
    foreach ($veldenKey as $key)
      $html_opties .= "<label for=\"".$key."\" title=\"".$naw->data['fields'][$key]['description']."\"> ".$key." </label><br>\n";
    $html_opties .= "</span>\n";

    if($objectnaam=='Naw')
    {
      $veldenKeyNaw=$veldenKey;
      $nawFields= $naw->data['fields'];
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
<br><br><b>Velden</b>
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

return array($html,$veldenKeyNaw,$nawFields);
}


$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "crm_naw_templatesList.php";
$__funcvar['location'] = "crm_naw_templatesEdit.php";

$object = new CRM_naw_templates();



$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->skipStripAll=true;


$data = array_merge($_POST,$_GET);
if($data['opslaanNew']==1 && $data['action'] <> 'delete')
  $data['id']=0;

$action = $data['action'];

if (!empty($action) && $action == 'edit')
{
  $object->setOption('intake', 'form_extra', 'disabled');
}

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;
$editObject->formTemplate = "crm_naw_templatesEditTemplate.html";


/*
$db=new db();
$query="SELECT VermogensbeheerdersPerGebruiker.Vermogensbeheerder, max(Vermogensbeheerders.CrmPortefeuilleInformatie) as CrmPortefeuilleInformatie,Vermogensbeheerders.Layout
        FROM Vermogensbeheerders Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR' GROUP BY VermogensbeheerdersPerGebruiker.Vermogensbeheerder";
$db->SQL($query);
$gebruikPortefeuilleInformatie = $db->lookupRecord();
*/

if($action=='edit')
  $editObject->controller($action,$data);


if($object->get('intake')==1 || $_POST['intake']==1)
{
  $fields = getFields('intake');

  $tabs=array('Basis'=>vt('Basis'));
}
else
{
  
  $object->setOption('intakeOmschrijving', 'form_type', 'hidden');
  $fields = getFields();
  $tabs=array('Basis'=>vt('Basis'),
            '0'=>vt('Algemeen'),
            '1'=>vt('Persoon'),
            '2'=>vt('Adviseurs'),
            '3'=>vt('Memo'),
            '4'=>vt('Contract'),
            '5'=>vt('Beleggen'),
            '6'=>vt('Rapportage'),
            '7'=>vt('Profiel'),
            '8'=>vt('Relatie'),
            '9'=>vt('Frame'),
            '10'=>vt('Portefeuille'),
            '11'=>vt('Beheerfee'),
            '12'=>vt('Staffels'),
            'Adressen'=>vt('Adressen'),
            'Contactpersoon'=>vt('Contactpersoon'),
            'Rekeningen'=>vt('Rekeningen'));

  if($__appvar["crmOnly"] ==true)
  {
    unset($tabs['10']);
    unset($tabs['11']);
    unset($tabs['12']);
  }
}
$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("<br>","");
$_SESSION['submenu']->addItem($fields[0],"");
$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

foreach ($tabs as $tab=>$omschrijving)
  $loadEditor.="loadEditor('$tab',400,1000);\n";


$editcontent['javascript'].="
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
$loadEditor
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
}
 ";
$editcontent['body']='onLoad="doEditorOnload();"';
$editcontent['jsincludes'].='<script type="text/javascript" src="javascript/ckeditor4/ckeditor.js"></script>';
$editObject->template = $editcontent;


if($_POST && $action=='update')
{
  $naw=new Naw();
  $portefeuille = new Portefeuilles();
  foreach ($tabs as $tab=>$omschrijving)
  {
    if(isset($_POST[$tab]) ) //&& !in_array($omschrijving,array('Adressen','Contactpersoon','Portefeuille','Beheerfee','Staffels'))
    {
       $tabData['text'][$tab]=$_POST[$tab];
       $tabData['naam'][$tab]=$_POST['naam_'.$tab];
       $tabData['size'][$tab]=$_POST['size_'.$tab];
       
       if($tab=='Basis' && $tabData['naam'][$tab] == '' || $tab==10 || $tab==11 || $tab==12)//
         $tabData['naam']['Basis']='Basis';


       foreach ($fields[1] as $field)
       {
         $oldTab=$tab;
         if(strpos($_POST[$tab],'{'.$field.'_'))
         { 
            if($tab>=10)
            {
              if(!isset($portefeuille->data['fields'][$field]))
              {
                $tab = 'Basis';
              }
            }
           $foundFields[$tab]['velden'][$field]=$fields[2][$field];
           $foundFields[$tab]['naam']=$tabData['naam'][$tab];
         }

         if(isset($foundFields[$tab]['velden'][$field]['categorie']))
           $foundFields[$tab]['velden'][$field]['categorie']=$tabData['naam'][$tab];

         
         $foundFields[$tab]['table']='CRM_naw';
         $foundFields[$tab]['object']='Naw';
         
         if($tab > 9 && isset($foundFields[$tab]['velden'][$field]))
         {
           $foundFields[$tab]['naam']='Portefeuille'; //listarray($foundFields[$tab]);//exit;
           //$foundFields[$tab]['table']='Portefeuilles';
           //$foundFields[$tab]['object']='Portefeuilles';
           $foundFields[$tab]['velden'][$field]['categorie']='Portefeuille';
          // $foundFields[$tab]['velden'][$field]['description']=$field;
         }
         $tab=$oldTab;
       }
    }
  }

  $tab='RecordInfo';
  $foundFields[$tab]['naam']=$tab;
  $foundFields[$tab]['table']='CRM_naw';
  $foundFields[$tab]['object']='Naw';
  $recordInfo=array('id','add_date','add_user','change_user','change_date');
  foreach ($recordInfo as $field)
  {
    $foundFields[$tab]['velden'][$field]['categorie']=$tab;
    $foundFields[$tab]['velden'][$field]['description']=$field;
    $foundFields[$tab]['velden'][$field]['form_visible']=1;
    $foundFields[$tab]['velden'][$field]['list_visible']=1;

  }
  $data['tabs']=serialize($tabData);
  $data['veldenPerTab']=serialize($foundFields);

  $templateLeeg=file_get_contents("CRM_include/CRM_nawEditTemplate.html");

  foreach ($tabs as $tab=>$omschrijving)
  {
    $style='';
    if($tabData['naam'][$tab] <> '' && !in_array($omschrijving,array('Basis','Adressen','Contactpersoon','Rekeningen','Portefeuille','Beheerfee','Staffels')))
    {
      if($tabData['size'][$tab] > 0)
        $style='style="width:'.$tabData['size'][$tab].';"';
      $tabButtons.='<input type="button" class="tabbuttonInActive" '.$style.' onclick="javascript:tabOpen(\''.$tab.'\');" id="tabbutton'.$tab.'" name="'.$tabData['naam'][$tab].'" value="'.$tabData['naam'][$tab].'">';
    }
    $templateLeeg=str_replace("{".$omschrijving."}",$_POST[$tab],$templateLeeg);
  }
  $templateLeeg=str_replace("{hoofdTabs}",$tabButtons,$templateLeeg);

  if($object->get('intake')==1 || $_POST['intake']==1)
  {
  
    $extraVariable='';
    if($_POST['pdfMaken']==1)
    {
      $extraVariable.='<p><input name="createPdf" type="checkbox" value="1" checked /> ' . vt('pdf opslaan bij documenten') . '.</p>';
    }
    if($_POST['intakeOmschrijving']<>'')
    {
      $extraVariable.='<input name="intakeOmschrijving" type="hidden" value="'.$_POST['intakeOmschrijving'].'"/>';
    }


    $templateLeeg=str_replace("<tr><td>{PortefeuilleTabs}</td></tr>",'<input type="hidden" name="template" value="intake">'.$extraVariable,$templateLeeg);
    $fp = fopen("CRM_nawEditTemplate_intake.html", 'w');
    fwrite($fp, $templateLeeg);
    fclose($fp);
    if ($__appvar["AWSbased"] == "loaded")
    {
      $f = "CRM_nawEditTemplate_intake.html";
      copy($f, "../data/html/$f" );
    }
  }
  else
  {
    $fp = fopen("CRM_nawEditTemplate_custom.html", 'w');
    fwrite($fp, $templateLeeg);
    fclose($fp);
    if ($__appvar["AWSbased"] == "loaded")
    {
      $f = "CRM_nawEditTemplate_custom.html";
      copy($f, "../data/html/$f" );
    }
    $templates=array('Adressen'=>array('pre'=>'<form name="editForm" action="{updateScript}">
<div class="form">
<input type="hidden" name="action" value="{action}">
<input type="hidden" name="updateScript" value="{updateScript}">
<input type="hidden" name="returnUrl" value="{returnUrl}">
{id_inputfield}{rel_id_inputfield}
</div>
<div>',
'post'=>'
</form></div>'),
'Contactpersoon'=>array('pre'=>'<form name="editForm" action="{updateScript}" method="POST">
<div class="form">
<input type="hidden" name="action" value="{action}">
<input type="hidden" name="updateScript" value="{updateScript}">
<input type="hidden" name="returnUrl" value="{returnUrl}">
{id_inputfield}{rel_id_inputfield}
</div>
<div>','post'=>'
</form></div>'),
'Rekeningen'=>array('pre'=>'<form name="editForm" action="{updateScript}" method="POST">
<div class="form">
<input type="hidden" name="action" value="{action}">
<input type="hidden" name="updateScript" value="{updateScript}">
<input type="hidden" name="returnUrl" value="{returnUrl}">
{id_inputfield}{rel_id_inputfield}
</div>
<div>','post'=>'
</form></div>'));



    foreach ($templates as $tab=>$arround)
    {
      if($_POST[$tab] <> '')
      {
        $fp = fopen("CRM_naw_".$tab."EditTemplate_custom.html", 'w');
        fwrite($fp, $arround['pre'].$_POST[$tab].$arround['post']);
        fclose($fp);
        if ($__appvar["AWSbased"] == "loaded")
        {
          $f = "CRM_naw_".$tab."EditTemplate_custom.html";
          copy($f, "../data/html/$f" );
        }
      }
    }
  }
}


//$data['tabs']='tyest data';
if($action<>'edit')
  $editObject->controller($action,$data);

if($object->get('intake')==0 && $_POST['intake']==0)
  $object->setOption('pdfMaken', 'form_extra', 'disabled');

$tabData=unserialize($object->get('tabs'));

if(!is_array($tabData))
  $tabData=array();

foreach ($tabs as $tab=>$omschrijving)
{
  
  if(!isset($tabData['text'][$tab]) && file_exists("CRM_include/CRM_nawEditTemplate_$omschrijving.html"))
  {
    if($object->get('intake')==1 || $_POST['intake']==1)
      $tabData['text'][$tab]='Intake';
    else
      $tabData['text'][$tab]=file_get_contents("CRM_include/CRM_nawEditTemplate_$omschrijving.html");
  }

  if(!isset($tabData['naam'][$tab]))
  {
    $tabData['naam'][$tab]=$omschrijving;
  }
}

foreach ($tabs as $tab=>$omschrijving)
{
  if(in_array($omschrijving,array('Basis','Adressen','Contactpersoon','Rekeningen','Portefeuille','Beheerfee','Staffels')))
  {
    $extraInput='';
  }
  else
  {
    $extraInput='' . vt('Naam') . ' <input type="text" name="naam_'.$tab.'" '.$readonly.' value="'.$tabData['naam'][$tab].'"><br>
' . vt('Grootte') . ' <input type="text" size=5 name="size_'.$tab.'" '.$readonly.' value="'.$tabData['size'][$tab].'"> ';
  }
  if(!isset($tabData['naam'][$tab]))
    $tabData['naam'][$tab]=$omschrijving;

  $tabEdit.= '
<div class="formblock">
<div class="formlinks">'.$tab.' <br>
'.$extraInput.'</div>
<div class="formrechts">
<textarea class=""  cols="60"  rows="10" name="'.$tab.'" id="'.$tab.'">'.htmlspecialchars($tabData['text'][$tab]).'</textarea>
</div>
</div>';
}

$editObject->formVars['tabs']=$tabEdit;

if($data['intake']==1)
  $object->set('intake',1);






// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");

echo $editObject->getOutput();

if ($result = $editObject->result)
{
  if($object->get('intake')==1 && $object->get('intakeOmschrijving')<>'' && $object->get('id')>0)
  {
    $fp = fopen("CRM_include/CRM_nawEditTemplate_intake_".$object->get('id').".html", 'w');
    fwrite($fp, $templateLeeg);
    fclose($fp);
    if ($__appvar["AWSbased"] == "loaded")
    {
      $f = "CRM_include/CRM_nawEditTemplate_intake_".$object->get('id').".html";
      copy($f, "../data/html/$f" );
    }
  }
  
  header("Location: ".$returnUrl);
}
else
{
	echo $_error = $editObject->_error;
}
?>