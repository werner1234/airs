<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/07/09 11:56:18 $
    File Versie         : $Revision: 1.4 $

    $Log: standaardtakenToevoegen.php,v $
    Revision 1.4  2017/07/09 11:56:18  rvv
    *** empty log message ***

    Revision 1.3  2014/04/26 16:40:33  rvv
    *** empty log message ***

    Revision 1.2  2013/12/14 17:14:49  rvv
    *** empty log message ***

    Revision 1.1  2013/09/01 13:31:16  rvv
    *** empty log message ***

    Revision 1.105  2013/08/14 15:57:30  rvv
    *** empty log message ***


*/


include_once("wwwvars.php");

$deb_id=$_GET['deb_id'];
if($_POST['deb_id'])
  $deb_id=$_POST['deb_id'];

if ($deb_id  > 0)
{
  $db = new db();
  $q = "SELECT * FROM CRM_naw WHERE id = '".$deb_id."'";
  $db->SQL($q);
  $nawRec = $db->lookupRecord();
  $subHeader = " bij <b>".$nawRec['naam'].", ".$nawRec['a_plaats']."</b>";
}

  $_SESSION['NAV']='';

$mainHeader=vt('Taken aanmaken');
$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div>";

$content['javascript'] .= "
function openDiv(field)
{
		$('#kop_'+field).toggle();
}
";


foreach($_POST as $key=>$value)
{
  if(substr($key,0,9) =='aanmaken_')
  {
    $id=substr($key,9);
    $aanmaken[$id]=array('gebruiker'=>$_POST['gebruiker_'.$id],
                         'rel_id'=>$deb_id,
                         'relatie'=>$nawRec['naam'],
                         'zichtbaar'=>jul2db(form2jul($_POST['zichtbaarna_'.$id])),
                         'standaardtaakId'=>$id,
                         'soort'=>$_POST['type_'.$id],
                         'kop'=>$_POST['taak_'.$id]);
    
  }
}

$counter=0;
foreach($aanmaken as $taakId=>$taakData)
{
  $query="INSERT INTO taken SET ";
  foreach($taakData as $veld=>$waarde)
    $query.=" $veld='".addslashes($waarde)."', ";
  $query.= "add_date=now(),add_user='$USR',change_date=now(),change_user='$USR'";
  
  $db->SQL($query);
  if($db->Query())
    $counter++;
}



echo template($__appvar["templateContentHeader"],$content);
?>

<table>
<?
if($counter > 0)
{
  echo $counter." " . vt('taken aangemaakt.') . "";
}
else
{

if($standaardbrief=='')
  $selected = " CHECKED";

$db=new DB();

$query="SELECT waarde,omschrijving FROM CRM_selectievelden WHERE module = 'standaardTaken' ORDER BY omschrijving";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
{
    $typeOptions.="<option $selected value=\"".$data['waarde']."\">".$data['omschrijving']." </option>\n";
}
$query="SELECT Gebruiker,Naam FROM Gebruikers ORDER BY Gebruiker";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
{
  if($data['Gebruiker']==$USR)
    $selected='SELECTED';
  else
    $selected='';  
  $userOptions.="<option $selected value=\"".$data['Gebruiker']."\">".$data['Gebruiker'].' - '.$data['Naam']." </option>\n";
}


$query="SELECT id,taak, hoofdtaak as categorie, dagenTotZichtbaar FROM standaardTaken ORDER BY categorie,taak";
$db->SQL($query);
$db->Query();

$lastcategorie='';
$output='';

$tableHeader="<tr><td>" . vt('Check') . "</td><td>" . vt('Wie') . "</td><td>" . vt('Zichtbaar vanaf') . "</td><td>" . vt('Omschrijving') . "</td></tr>";
while($data=$db->nextRecord())
{
  if($data['categorie'] != $lastcategorie)
  {

    if($lastcategorie <> '')
    {
      $output .= "</table></div>\n";
      $output .= "<br/><br/><a href=\"javascript:openDiv('".$data['id']."')\"><b>".$data['categorie']."</b> </a>\n";
    }
    else
      $output .= "<br/><br/><a href=\"javascript:openDiv('".$data['id']."')\"><b>".$data['categorie']."</b> </a>\n";
    $output .= "<div style='display: none' id='kop_".$data['id']."'>\n <table> $tableHeader";
  }

  $output .= "<tr><td><input type=\"checkbox\" name=\"aanmaken_".$data['id']."\" value=\"1\"></td>\n";
  $output .= "<td><select type=\"select\" name=\"gebruiker_".$data['id']."\">".$userOptions."</select></td> \n";
  $output .= "<td><input type=\"text\" size=\"8\" name=\"zichtbaarna_".$data['id']."\" value=\"".date('d-m-Y',time()-86400+($data['dagenTotZichtbaar']*86400))."\" ></td>\n";
  $output .= "<input type=\"hidden\" value=\"".$data['categorie']."\" name=\"type_".$data['id']."\"></td>\n";
  $output .= "<td><input type=\"text\" size=\"60\" name=\"taak_".$data['id']."\" value=\"".$data['taak']."\" ></td></tr>\n";

  $selected = "";
  $lastcategorie=$data['categorie'];
}
if($lastcategorie <> '')
  $output .= "</table></div>\n";

?>
</table>

<form name="editForm" action="<?=$PHP_SELF?>" method="POST">
  <input type="hidden" name="action" value="print">
  <input type="hidden" name="noForce" value="1">
  <input type="hidden" name="deb_id" value="<?=$deb_id?>">

  <?=$output?>
  <br><br>
  <button type="submit" value="Taken aanmaken"><?= vt('Taken aanmaken'); ?></button>
</form>

<?



}
echo template($__appvar["templateRefreshFooter"],$content);
?>