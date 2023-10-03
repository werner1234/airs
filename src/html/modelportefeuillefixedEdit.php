<?php
/*
    AE-ICT CODEX source module versie 1.6, 10 april 2010
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.10 $

    $Log: modelportefeuillefixedEdit.php,v $
    Revision 1.10  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.9  2017/05/18 12:18:58  rm
    5728

    Revision 1.8  2017/02/15 10:56:00  rm
    no message

    Revision 1.7  2017/02/06 07:52:11  rm
    no message

    Revision 1.6  2017/01/31 12:33:15  rm
    no message

    Revision 1.5  2017/01/18 16:05:27  rm
    5430 (EFI) Invoer FX-modelportefeuilles

    Revision 1.4  2013/12/07 17:50:41  rvv
    *** empty log message ***

    Revision 1.3  2013/10/05 15:56:28  rvv
    *** empty log message ***

    Revision 1.2  2013/08/28 15:57:29  rvv
    *** empty log message ***

    Revision 1.1  2011/04/27 17:55:43  rvv
    *** empty log message ***

    Revision 1.4  2010/05/23 13:55:53  rvv
    *** empty log message ***

    Revision 1.3  2010/05/02 10:04:24  rvv
    *** empty log message ***

    Revision 1.2  2010/04/25 10:52:21  rvv
    *** empty log message ***

    Revision 1.1  2010/04/11 11:57:06  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "modelportefeuillefixedList.php";
$__funcvar['location'] = "modelportefeuillefixedEdit.php";

$object = new ModelPortefeuilleFixed();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;


$editObject->formTemplate = "modelportefeuillefixedTemplate.html";
$editObject->usetemplate = true;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

//$data = $_GET;
$data = array_merge($_GET, $_POST);
$action = $data['action'];
$aeJson = new AE_Json();

/** bij ajax post velden extra valideren */
if( requestType('ajax') && $action == 'update') {

  $errorMssg = array();

  if ( empty ($data['Fonds']) ) {
    unset($data['Fonds']);
  } elseif ($data['Fonds'] != 'Liquiditeiten') {
    $fondsObj = new Fonds();
    $fondsData = $fondsObj->parseBySearch('Fonds = "' . $data['Fonds'] . '"');
    if ( $fondsData === false ) {
      $errorMssg['Fonds'] = 'Foutief fonds ingevoerd.';
    }
  }

  if ( empty ($data['Percentage']) ) {
    unset($data['Percentage']);
  } else {
    if ( ! is_numeric ($data['Percentage']) ) {
      $errorMssg['Percentage'] = 'Foutief percentage ingevoerd.';
    }
  }

  if ( isset ($data['saveType']) && $data['saveType'] === 'new' ) {

    if ( ! isset ($data['Fonds']) || empty ($data['Fonds']) ) {
      $errorMssg['Fonds'] = 'Fonds mag niet leeg zijn.';
    }

    if ( ! isset ($data['Percentage']) || empty ($data['Percentage']) ) {
      $errorMssg['Percentage'] = 'Percentage mag niet leeg zijn.';
    }
  }



  if ( ! empty ($errorMssg) ) {
    echo $aeJson->json_encode(array(
      'success' => true,
      'saved'   => false,
      'errors'   => $errorMssg
    ));
    exit();
  }
}

$editObject->includeHeaderInOutput = false;  // geen templateheaders in $editObject->output toevoegen

if($action !='new')
{
  $editObject->formVars["submit"]='
  <a href="#" onClick="editForm.action.value=\'delete\';editForm.submit();">
  <img src="images/16/delete.gif" width="16" height="16" border="0" alt="verwijder record" align="absmiddle">&nbsp;verwijder</a>';
}

  $editObject->formVars["submit"].='
    <a href="#" onClick="editForm.submit();">
    <img src="images/16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;opslaan</a>';


 if($data['type'])
  $editObject->formVars['type']=$data['type'];


$editObject->controller($action,$data);

if($data['Portefeuille'])
  $object->set('Portefeuille',$data['Portefeuille']);
if($data['Datum'])
  $object->set('Datum',$data['Datum']);
  
$koppelObject = array();
$koppelObject[0] = new Koppel("Fondsen","editForm");
$koppelObject[0]->addFields("Fonds","Fonds",false,true);
$koppelObject[0]->addFields("ISINCode","",true,true);
$koppelObject[0]->addFields("Omschrijving","",true,true);
$koppelObject[0]->name = "fonds";
$koppelObject[0]->extraQuery = " AND (Fondsen.EindDatum  >=  NOW() OR Fondsen.EindDatum = '0000-00-00') ";

$getFonds=urlencode(base64_encode(gzcompress("SELECT Fonds, Omschrijving FROM Fondsen WHERE 1=1 ".$alleenActief." ORDER BY Omschrijving")));
$editObject->formVars['getFonds']=$getFonds;

if(is_object($editObject->form))
  $editcontent['ajaxinclude']=$editObject->form->makeAjaxLookup()."\n".$koppelObject[0]->getJavascript();
// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
if ($action != 'update' && $action != 'delete' )
  echo template($__appvar["templateContentHeader"],$editcontent);


if( requestType('ajax') && $action == 'update' ) {
  $db = new DB();
  $returnDataQuery = "
    SELECT 
    
    ModelPortefeuilleFixed.id,
    ModelPortefeuilleFixed.Portefeuille,
    ModelPortefeuilleFixed.Fonds,
    ModelPortefeuilleFixed.Percentage,
    Fondsen.valuta,
    BeleggingscategoriePerFonds.Beleggingscategorie,
    BeleggingssectorPerFonds.Beleggingssector,
    BeleggingssectorPerFonds.Regio,
    BeleggingscategoriePerFonds.afmCategorie
    
    FROM  ModelPortefeuilleFixed 
   
    LEFT JOIN Fondsen on ModelPortefeuilleFixed.Fonds=Fondsen.Fonds
    Inner Join Portefeuilles ON ModelPortefeuilleFixed.Portefeuille = Portefeuilles.Portefeuille
    Left Join BeleggingssectorPerFonds ON ModelPortefeuilleFixed.Fonds = BeleggingssectorPerFonds.Fonds AND Portefeuilles.Vermogensbeheerder = BeleggingssectorPerFonds.Vermogensbeheerder
    Left Join BeleggingscategoriePerFonds ON ModelPortefeuilleFixed.Fonds = BeleggingscategoriePerFonds.Fonds AND Portefeuilles.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder
    
     WHERE ModelPortefeuilleFixed.id = " . $object->get('id') . "
  ";

  $db->sql($returnDataQuery);
  $linedata = $db->lookupRecord();

  $rowdata = '';
  if ( $data['saveType'] === 'new' ) {
    $rowdata = "
    <tr data-lineid=\"" . $object->get('id') . "\" class=\"list_dataregel\" onmouseover=\"this.className='list_dataregel_hover'\" onmouseout=\"this.className='list_dataregel'\" title=\"Klik op de knop links om de details te zien/muteren\">
<td data-field=\"\" class=\"listTableData\">
		  <span data-toggle=\"tooltip\" title=\"Regel verwijderen\" class=\"btn-new btn-default btn-xs deleteInline\" data-rowid=\"" . $object->get('id') . "\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i></span>
		  <span data-toggle=\"tooltip\" title=\"Regel Wijzigen\" class=\"btn-new btn-default btn-xs editInline\" data-rowid=\"" . $object->get('id') . "\"><i class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i> </span>
		  <span class=\"btn-new btn-default btn-xs saveInline\" style=\"display:none;\" data-rowid=\"" . $object->get('id') . "\"><i class=\"fa fa-floppy-o\" aria-hidden=\"true\"></i> </span>
		  <span data-toggle=\"tooltip\" title=\"Wijzigen ongedaan maken\" class=\"btn-new btn-default btn-xs cancelInline\" style=\"display:none;\" data-rowid=\"" . $object->get('id') . "\"><i class=\"fa fa-refresh\" aria-hidden=\"true\"></i> </span>
		   &nbsp;</td>
<td data-field=\"Fonds\" class=\"listTableData\" width=\"200\" align=\"left\">" . $linedata['Fonds'] . " &nbsp;</td>
<td data-field=\"Percentage\" class=\"listTableData\" width=\"50\" align=\"right\"><span class=\"PercentageVal\">" . $linedata['Percentage'] . "</span> &nbsp;</td>
<td data-field=\"valuta\" class=\"listTableData\" width=\"100\">" . $linedata['valuta'] . " &nbsp;</td>
<td data-field=\"Beleggingscategorie\" class=\"listTableData\" width=\"100\">" . $linedata['Beleggingscategorie'] . " &nbsp;</td>
<td data-field=\"Beleggingssector\" class=\"listTableData\" width=\"100\">" . $linedata['Beleggingssector'] . " &nbsp;</td>
<td data-field=\"regio\" class=\"listTableData\" width=\"100\">" . $linedata['Regio'] . " &nbsp;</td>
<td data-field=\"afmCategorie\" class=\"listTableData\" width=\"100\">" . $linedata['afmCategorie'] . " &nbsp;</td>
</tr>
    
    
    ";
  }

  echo $aeJson->json_encode(array(
    'success' => true,
    'saved'   => $editObject->result,
    'lineData'  => $linedata,
    'trHtml' => $rowdata
  ));
  exit();
}

if( requestType('ajax') && $action == 'delete' ) {
  echo $aeJson->json_encode(array(
    'success' => true,
    'saved'   => $editObject->result,
  ));
  exit();
}

echo $editObject->getOutput();

	

if ($result = $editObject->result)
{
  if (!headers_sent())
  {
	  header("Location: modelportefeuillefixedList.php?Portefeuille=".$data['Portefeuille']."&Datum=".$data['Datum']."&type=".$data['type']);//$returnUrl
    exit;
  }
  else
  {
   echo "Header al verzonden..";
  }
}
else
{
	echo $_error = $editObject->_error;
}
echo template($__appvar["templateRefreshFooter"],$content);
?>