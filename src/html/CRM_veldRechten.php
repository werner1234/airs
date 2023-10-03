<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/07/30 10:17:58 $
 		File Versie					: $Revision: 1.6 $

 		$Log: CRM_veldRechten.php,v $
*/
include_once("wwwvars.php");
session_start();



function getFields($veldrechten)
{
  global $USR;
  $velden=array();
  $db=new DB();
  $query="SELECT VermogensbeheerdersPerGebruiker.Vermogensbeheerder, Vermogensbeheerders.CrmPortefeuilleInformatie,Vermogensbeheerders.Layout,Vermogensbeheerders.CRM_eigenTemplate,Vermogensbeheerders.check_module_SCENARIO
        FROM Vermogensbeheerders Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR' GROUP BY VermogensbeheerdersPerGebruiker.Vermogensbeheerder  limit 1";
  $db->SQL($query);
  $gebruikPortefeuilleInformatie = $db->lookupRecord();

  if($gebruikPortefeuilleInformatie['CRM_eigenTemplate'] == 1)
  {
    $query = "SELECT veldenPerTab FROM `CRM_naw_templates` WHERE intake=0 order by change_date desc limit 1";
    $db->SQL($query);
    $customFields = $db->lookupRecord();
    $customFields=unserialize($customFields['veldenPerTab']);

    foreach ($customFields as $tab=>$tabdata)
    {
      if($tabdata['naam'] <> '')
      {
        $nieuweVolgorde[$tabdata['object']][]=$tabdata['naam'];
        foreach ($tabdata['velden'] as $key=>$waarden)
          $nieuweVelden[$tabdata['object']][$key]=$waarden;
      }
    }

    if(count($nieuweVelden['Naw'])>0)
    {
       foreach($nieuweVelden['Naw'] as $key => $values)
       {
         if($values['categorie']=='')
           $values['categorie']='Algemeen';
         $velden['CRM_naw'][$values['categorie']][$key] = $values;
       }
    }
  }

  if(count($velden['CRM_naw'])<1)
  {
    $naw = new Naw();
    foreach ($naw->data['fields'] as $key => $values)
    {
      $velden['CRM_naw'][$values['categorie']][$key] = $values;
    }

  }


  foreach ($velden as $tabel=>$categorien)
  {
    $html_opties .= "<b>$tabel</b>";
    foreach ($categorien as $categorie=>$velden)
    {

      $html_opties .= "<div class=\"menutitle\" onclick=\"SwitchMenu('sub$categorie')\">$categorie</div><span class=\"submenu\" id=\"sub$categorie\">\n";
      foreach ($velden as $veld=>$waarden)
      {
        if(isset($veldrechten[$tabel.'@'.$veld]) && $veldrechten[$tabel.'@'.$veld] == 1)
          $checked='checked';
        else
          $checked='';
        $html_opties .= "<label for=\"".$veld."\" title=\"".$waarden['description']."\"><input type='checkbox' name='$tabel@$veld' value='1' $checked> ".$veld."  </label><br>\n";
      }
      $html_opties .= "</span>\n";
    }
  }



 $html = "
 <script language=\"JavaScript\" TYPE=\"text/javascript\">

function submitForm()
{
	document.editForm.submit();
}
</script>
<br><br><b>" . vt('Beperkt toegankelijke velden') . ":</b>
<br>
<form name=\"editForm\" method=\"POST\" >


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


$cfg=new AE_config();
if($_POST && $_POST['rechtenUpdate']==1)
{
  $data=$_POST;
  unset($data['rechtenUpdate']);
  unset($data['action']);
  $cfg->addItem('CRM_veldrechten',addslashes(serialize($data)));
  //listarray($_POST);
  if($_POST['action']!='updateStay')
    header("Location: welcome.php");
}


echo template($__appvar["templateContentHeader"],$content);
//unset($_SESSION['NAV']);
$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->returnUrl = 'CRM_nawList.php';
$_SESSION['NAV']->addItem(new NavEdit("editForm",true,false,false));
$_SESSION['NAV']->buttonBack=false;


if(GetCRMAccess(2))
{
?>
<form method="POST" name="editForm">
<input type="hidden" name="rechtenUpdate" value="1">
  <input type="hidden" name="action" value="">
<?
$data['CRM_veldrechten']=unserialize($cfg->getData('CRM_veldrechten'));
$fields = getFields($data['CRM_veldrechten']);
echo $fields;
?>

</form>
<?
}
else
{
  echo "Geen CRM beheer rechten.";
}


$_SESSION[submenu] = New Submenu();




echo template($__appvar["templateRefreshFooter"],$content);


?>