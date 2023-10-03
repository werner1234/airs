<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 	Laatste aanpassing	: $Date: 2019/02/27 15:17:53 $
 	File Versie					: $Revision: 1.8 $

 	$Log: gebruikerVermogensbeheerderKoppel.php,v $
 	Revision 1.8  2019/02/27 15:17:53  cvs
 	call 7561
 	
 	Revision 1.7  2018/08/31 13:45:47  cvs
 	PHP 5.6 proof gemaakt
 	
 	Revision 1.6  2014/11/08 18:35:29  rvv
 	*** empty log message ***
 	
 	Revision 1.5  2012/12/02 11:04:26  rvv
 	*** empty log message ***
 	
 
*/
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');

$content = array();
echo template($__appvar["templateContentHeader"],$content);

session_start();
if(is_object($_SESSION["NAV"]))
{
	if(!empty($_SESSION["NAV"]->returnUrl))
  {
    $returnUrl = $_SESSION["NAV"]->returnUrl;
  }
}

$updateGebruiker='';

if($_REQUEST['Gebruiker'] <> '')
{
  $updateGebruiker=$_REQUEST['Gebruiker'];
}
else
{
  exit;
}

$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION["NAV"]->returnUrl = $returnUrl;
$_SESSION["NAV"]->addItem(new NavEdit("editForm",true,false,true));
session_write_close();
$DB = new DB();

if ($_POST["fillin"] == true)
{
	$qg = "DELETE FROM VermogensbeheerdersPerGebruiker WHERE Gebruiker = '".$updateGebruiker."' AND Vermogensbeheerder NOT IN('".implode("','",$_POST['lid'])."')";
	$DB->executeQuery($qg);
  foreach($_POST['lid'] as $lid)
  {
    if($DB->QRecords("SELECT id FROM VermogensbeheerdersPerGebruiker WHERE Vermogensbeheerder = '".$lid."' AND Gebruiker = '".$updateGebruiker."'") < 1)
    {
      $qg = "INSERT INTO VermogensbeheerdersPerGebruiker SET Vermogensbeheerder = '".$lid."', Gebruiker = '".$updateGebruiker."', add_date = NOW(), change_date = NOW(), add_user = '".$USR."', change_user = '".$USR."'";
  	  $DB->executeQuery($qg);
    }
  }
  echo "<br>Koppeling(en) opgeslagen";
}
else 
{
	
	$users['lid'] = array();
	
	$Q = "SELECT * FROM VermogensbeheerdersPerGebruiker WHERE Gebruiker = '".$updateGebruiker."'";
	$DB->executeQuery($Q);
	while ($data = $DB->nextRecord())
	{
		$users['lid'][] = $data['Vermogensbeheerder'];
  }
  
  $Q = "SELECT * FROM Vermogensbeheerders ORDER BY Vermogensbeheerder";
  $DB->executeQuery($Q);
  while ($row = $DB->nextRecord())
  {
    if (in_array($row['Vermogensbeheerder'],$users['lid']))
    {
      $lid .= "<option value=\"".$row['Vermogensbeheerder']."\" >".$row['Vermogensbeheerder']."</option>\n";
    }
    else
    {
      $rest .= "<option value=\"".$row['Vermogensbeheerder']."\" >".$row['Vermogensbeheerder']."</option>\n";
    }
  }
?>
<script type="text/javascript">
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
	nietlid = document.editForm['nietlid[]'];
	lid =  document.editForm['lid[]'];
	
	for(j=0; j < nietlid.options.length; j++)
	{
 		nietlid.options[j].selected = true; 
	}
	
	for(j=0; j < lid.options.length; j++)
	{
 		lid.options[j].selected = true; 
	}
	
	document.editForm.submit();
}
</script>
<br>
<?= vtb('Vermogensbeheerders bij gebruiker %s', array($updateGebruiker)); ?><br>
<table border="0" >
	<form action="<?=$PHP_SELF?>" name="editForm" method="POST">
  <input type="hidden" name="fillin" value="true">
  <input type="hidden" name="Gebruiker" value="<?=$updateGebruiker?>">
  <tr>
  	<td>
  		<b><?= vt('Geen toegang tot'); ?></b>
  	</td>

  	<td>&nbsp;</td>
  	<td>
  		<b><?= vt('Toegang tot'); ?></b>
  	</td>
  </tr>
  <tr>
  <td>
  <div class="style2" valign="top">
  	<select name="nietlid[]" size="10" multiple style="width:120px" >
			<?=$rest?>
		</select>
  		</div>
  	</td>
  	<td valign="top" width="30">
  		<br><br>
			<a href="javascript:moveItem(document.editForm['nietlid[]'],document.editForm['lid[]']);"><img src="images/16/pijl_rechts.png" width="16" height="16" border="0" alt="toevoegen" align="absmiddle"></a>

			<br><br>
			<a href="javascript:moveItem(document.editForm['lid[]'],document.editForm['nietlid[]']);"><img src="images/16/pijl_links.png" width="16" height="16" border="0" alt="verwijderen" align="absmiddle"></a>  	
  	</td>
		<td>
  		<select name="lid[]" size="10" multiple style="width:120px" >
				<?=$lid?>
  		</select>  
  	</td>
  <tr>
	</form>
</table>

<?
}
echo template($__appvar["templateRefreshFooter"],$content);
