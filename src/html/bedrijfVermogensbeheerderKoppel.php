<?php
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');

$content = array();
echo template($__appvar["templateContentHeader"],$content);

session_start();
if(is_object($_SESSION[NAV]))	{
	if(!empty($_SESSION[NAV]->returnUrl))
		$returnUrl = $_SESSION[NAV]->returnUrl;
}

$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->returnUrl = $returnUrl;
$_SESSION[NAV]->addItem(new NavEdit("editForm",true,false,true));  
session_write_close();

if ($fillin == true)
{
	$DB = new DB();
	$qg = "DELETE FROM VermogensbeheerdersPerBedrijf WHERE Bedrijf = '".$Bedrijf."' ";
	$DB->SQL($qg);
	$DB->Query();
  for ($a=0; $a < count($lid);$a++)
  {
    $qg = "INSERT INTO VermogensbeheerdersPerBedrijf SET Vermogensbeheerder = '".$lid[$a]."', Bedrijf = '".$Bedrijf."', add_date = NOW(), change_date = NOW(), add_user = '".$USR."', change_user = '".$USR."'";
  	$DB->SQL($qg);
    $res = $DB->Query();
  }
  echo "<br>Koppeling opgeslagen";
}
else 
{
	
	$users[lid] = array();
	
	$Q = "SELECT * FROM VermogensbeheerdersPerBedrijf WHERE Bedrijf = '".$Bedrijf."'";
	$res = Query($Q);
	while ($data = NextRecord($res))
	{
		$users[lid][] = $data[Vermogensbeheerder];
  }
  
  $Q = "SELECT * FROM Vermogensbeheerders ORDER BY Vermogensbeheerder";
  $res = Query($Q);
  while ($row = NextRecord($res))
  {
    if (in_array($row[Vermogensbeheerder],$users[lid]))
			$lid .= "<option value=\"".$row[Vermogensbeheerder]."\" >".$row[Vermogensbeheerder]."</option>\n";
    else 
      $rest .= "<option value=\"".$row[Vermogensbeheerder]."\" >".$row[Vermogensbeheerder]."</option>\n";
      
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
Vermogensbeheerders bij bedrijf <?=$Bedrijf?><br>
<table border="0" >
	<form action="<?=$PHP_SELF?>" name="editForm">
  <input type="hidden" name="fillin" value="true">
  <input type="hidden" name="Bedrijf" value="<?=$Bedrijf?>">
  <tr>
  	<td>
  		<b>Geen toegang tot</b>
  	</td>

  	<td>&nbsp;</td>
  	<td>
  		<b>Toegang tot</b>
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
?>