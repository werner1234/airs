<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 3 september 2007
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2008/05/16 07:55:46 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: grootboekpervermogensbeheerderImport.php,v $
    Revision 1.1  2008/05/16 07:55:46  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");

$mainHeader="Import standaard grootboekrekeningen.";

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$kal = new DHTML_Calendar();
$content['calendar'] = $kal->get_load_files_code();	
$content['javascript'] .= " ";
$_SESSION['NAV'] = '';



$db = new DB();

if(!$_POST)
{
  echo template($__appvar["templateContentHeader"],$content);
  $query = "SELECT Vermogensbeheerder, Naam FROM Vermogensbeheerders ";
  $db->SQL($query);
  $db->Query();
  $vermogensbeheerders = array();
  while($data=$db->nextRecord())
    $vermogensbeheerders[]=$data;
?>
<form name="editForm" action="<?=$PHP_SELF?>" method="POST">
<div class="formblock">
<div class="formlinks">naar Vermogensbeheerder </div>
<div class="formrechts">
<select class="" type="select"  name="Vermogensbeheerder" >
<?
  foreach ($vermogensbeheerders as $data)
    echo '<option value="'.$data['Vermogensbeheerder'].'">'.$data['Naam'].'</option>';
?>
</select></div>
</div>
<div class="formblock">
<div class="formlinks">Start datum </div>
<div class="formrechts">
<input name="StartDatum" value="3-9-2007" size="11" id="f-calendar-field-1" type="text"  /><a href="#" id="f-calendar-trigger-1"><img align="middle" border="0" src="javascript/calendar/img.gif" alt="" /></a><script type="text/javascript">Calendar.setup({"ifFormat":"%d-%m-%Y","daFormat":"%d-%m-%Y","inputField":"f-calendar-field-1","button":"f-calendar-trigger-1"});</script></div>
</div>
<p>&nbsp;</p>
<div class="formblock">
<div class="formlinks">&nbsp; </div>
<div class="formrechts">
<input type="submit"  value="Import" style="width:100px">
</div>
</form>
<?
echo template($__appvar["templateRefreshFooter"],$content);
}
else 
{
  include_once("../classes/editObject.php");
  $Vermogensbeheerder = $_POST['Vermogensbeheerder']; 
  $StartDatum = jul2sql(form2jul($_POST['StartDatum']));
  
  $query = "SELECT * FROM Grootboekrekeningen";
  $db->SQL($query);
  $db->Query();
  $Grootboekrekeningen = array();
  while($data=$db->nextRecord())
    $Grootboekrekeningen[]=$data;  
  
  foreach ($Grootboekrekeningen as $data) 
  {
    $query = "SELECT id FROM GrootboekPerVermogensbeheerder WHERE Vermogensbeheerder = '$Vermogensbeheerder' AND Grootboekrekening = '".$data['Grootboekrekening']."' ";
    $db->SQL($query);
    $db->Query();
    if ($db->records() >0)
    {
    //update het record
      $iddata = $db->lookupRecord();
      $query = "UPDATE GrootboekPerVermogensbeheerder SET Startdatum = '$StartDatum', change_user = '$USR', change_date = NOW()  WHERE id = '".$iddata['id']."'";
      $db->SQL($query);
      $db->Query();
    }
    else 
    {
      $object = new GrootboekPerVermogensbeheerder();
      $editObject = new editObject($object);
      $editObject->__funcvar = $__funcvar;
      $editObject->__appvar = $__appvar;

      $data['id'] =0;
      $data['Vermogensbeheerder'] = $Vermogensbeheerder;
      $data['StartDatum'] =$_POST['StartDatum'];
      unset($data['add_date']);
      unset($data['change_date']);
      unset($data['add_user']);
      unset($data['change_user']);

      $data['action']='update';
      $editObject->controller('update',$data);
    }
  }
  
  $returnUrl = 'grootboekpervermogensbeheerderList.php';
  header("Location: ".$returnUrl);
}

?>