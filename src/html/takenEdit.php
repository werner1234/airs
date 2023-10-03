
<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 18 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.8 $
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("Taaklijst muteren");

$__funcvar['listurl']  = "takenList.php";
$__funcvar['location'] = "takenEdit.php";


$object = new Taken();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;


//$koppelObject = array();
//$koppelObject[0] = new Koppel("CRM_naw","editForm");
//$koppelObject[0]->addFields("id","rel_id",false,false);
//$koppelObject[0]->addFields("naam","relatie",true,true);
//$koppelObject[0]->addFields("plaats","",true,false);
//$koppelObject[0]->name = "naw";
//$koppelObject[0]->extraQuery = " AND aktief = 1 ";

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";
//$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/popup.js\" type=text/javascript></script>";
//$editcontent['javascript'] .= "\n".$koppelObject[0]->getJavascript();

$DB = new DB();

$editObject->formVars['portefeuilleDisplay'] = '-';
if ($action <> "new")
{
  $DB->SQL("SELECT * FROM taken LEFT JOIN CRM_naw ON CRM_naw.id = taken.rel_id WHERE taken.id='".$_GET["id"]."'");
  $taak = $DB->lookupRecord();
  $editObject->formVars['portefeuilleDisplay'] = ! empty ($taak['portefeuille']) ? $taak['portefeuille'] : '-';
}


$autocomplete = new Autocomplete();
$autocomplete->minLeng = 2;
$autocomplete->resetVirtualField('relatie');
$autocomplete->addVirtuelField(
  'relatie',
  array(
    'autocomplete' => array(
      'table'        => 'CRM_naw',
      'prefix'       => true,
      'returnType'   => 'expanded',
      'label'        => array('CRM_naw.portefeuille', 'CRM_naw.naam'),
      'searchable'   => array('CRM_naw.naam', 'CRM_naw.portefeuille'),
      'extra_fields'  => array('CRM_naw.id'),
      'field_value'  => array('CRM_naw.naam'),
      'value'  => 'CRM_naw.id',
      'actions'      => array(
        'change' => '
          if (!ui.item) {
            $("input[name=\'rel_id\']").val("");
            $(".portefeuilleDisplay").html("-");
           }
        ',
        'select' => '
          event.preventDefault();
          $("#relatie").val(ui.item.field_value);
          $("input[name=\'rel_id\']").val(ui.item.value);
          $(".portefeuilleDisplay").html("-");
          if ( ui.item.data.CRM_naw.portefeuille != "" ) {
            $(".portefeuilleDisplay").html(ui.item.data.CRM_naw.portefeuille);
          }
          
        '
      ),
      'conditions'   => array('`CRM_naw`.`aktief` = 1')
    )
  )
);

$editcontent['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('relatie');

/*
$DB->SQL("SELECT * FROM Gebruikers ORDER BY gebruiker");
$DB->Query();

$gebruikersnaam = strtolower($taak["gebruiker"]);
while ($urow = $DB->nextRecord())
{
  if($urow['Naam']=='')
    $urow['Naam']=$urow['Gebruiker'];
  $user = $urow['Gebruiker'];

  if ( ($action == "new" && $user == $USR)  OR  $user == $gebruikersnaam )
    $option = " SELECTED ";
  else
    $option ="";

  if (!empty($urow['Gebruiker']))
    $options .= "<option value=\"".$user."\" $option>".$urow['Naam']."</option>\n";
}
*/

$editObject->formVars['gebruikerOptions'] = $options;

if($_GET['deb_id'])
  $editObject->formVars['deb_id']="<input type='hidden' name='deb_id' value='".$_GET['deb_id']."'>";

$editObject->template = $editcontent;

$data = array_merge($_GET,$_POST);
$action = $data[action];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;
$editObject->formTemplate ="takenEditTemplate.html";
$editObject->controller($action,$data);

if($_GET['returnUrl'])
  $_SESSION['NAV']->returnUrl=$_GET['returnUrl'];

if($_GET['toHome']==1)
  $editObject->formVars["toHome"]='1';

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
if ($action == "new" AND $_GET['rel_id'] > 0)
{
  $DB->SQL("SELECT * FROM CRM_naw WHERE id = '".$_GET['rel_id']."'" );
  $nawRec = $DB->lookupRecord();
  $object->set("rel_id",$_GET['rel_id']);
  $object->set("relatie",$nawRec['naam']);
}
if($data['frame']==1)
{
  $editObject->formVars["submit"]='
     <a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;Opslaan</a>
     <a href="#" onClick="editForm.action.value=\'delete\';editForm.submit();"><img src="images//16/delete.gif" width="16" height="16" border="0" alt="verwijder record" align="absmiddle">&nbsp;verwijder</a>
     <a href="#" onClick="window.history.back();window.history.back();"><img src="images//16/terug.gif" width="16" height="16" border="0" alt="Ga terug zonder opslaan" align="absmiddle">&nbsp;terug</a>
     <input type="hidden" name="frame" value="1">';
  $frame="&frame=1";

  $html=$editObject->getOutput();
  //$html=str_replace("</div></form>",'<div class="formblock"><div class="formlinks">&nbsp;</div><div class="formrechts">'.$editObject->formVars["submit"]."</div></div></form></div>",$html);
  echo $html;
}
else
  echo $editObject->getOutput();

if ($result = $editObject->result)
{

  if($data['toHome']==1)
  {
    echo "<script>
  if (top.location != location) {
  top.frames['content'].location='welcome.php' 
  }
  </script>
  ";
    exit;
  }
  if(stripos($returnUrl,'deb_id')===false)
    $returnUrl.="&deb_id=".$data['deb_id'];
  header("Location: ".$returnUrl);
}
else
{
  echo $_error = $editObject->_error;
}
?>