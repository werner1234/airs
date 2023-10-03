<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/11/19 14:26:52 $
    File Versie         : $Revision: 1.2 $

*/
include_once("wwwvars.php");


$db = new DB();

$content['javascript'].="
function checkAll(optie)
{
  var theForm = document.listForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
    if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,7) == 'bulkId_')
    {
      if(optie == -1)
      {
        if(theForm[z].checked == true)
          theForm[z].checked=false;
        else
          theForm[z].checked=true;
      }
      else
      {
        theForm[z].checked = optie;
      }
    }
  }
 
}
";

echo template($__appvar["templateContentHeader"],$content);


if($USR=='XXX')
{
  //echo "$USR";
}
else
{
  echo "$USR heeft onvoldoende rechten.";
  exit;
}

$allChecks=getActieveControles();


echo '<div style="margin: 10px"><form name="listForm" method="POST">';

echo "Checks:<br>\n";
$header="<td>checkbox</td><td>Bulk orderId</td><td>Portefeuille</td> <td>fonds</td>";
foreach($allChecks as $check=>$checkOmschrijving)
{
  if($_POST[$check]==1 || count($_POST)==0)
  {
    $checked = 'checked';
    $header.="<td>$checkOmschrijving</td>";
  }
  else
  {
    unset($allChecks[$check]);
    $checked = '';
  }
  echo "<input type='checkbox' name='$check' value='1' $checked > $checkOmschrijving <br>\n";
}


$ids=array();
if($_POST['afvinken']==1)
{
  foreach($_POST as $key=>$value)
  {
    if(substr($key,0,7)=='bulkId_')
    {
      $id=substr($key,7);
      $ids[]=$id;
    }
  }

  $query="SELECT id,portefeuille,fonds,controleRegels FROM TijdelijkeBulkOrdersV2 WHERE id IN('".implode("','",$ids)."')";
  $db=new DB();
  $db->SQL($query);
  $db->Query();
  $regels=array();
  while($data=$db->nextRecord())
  {
    $regels[]=$data;
  }
  foreach ($regels as $data)
  {
    $checks=unserialize($data['controleRegels']);
    foreach($allChecks as $check=>$checkOmschrijving)
    {
//  foreach($allChecks as $checks=>$checkOmschrijving)
      if($checks[$check]['short'] > 0)
      {
        $checks[$check]['short']='';
        $checks[$check]['checked']=1;
      }
    }
    $query="UPDATE TijdelijkeBulkOrdersV2 SET controleRegels='".mysql_real_escape_string(serialize($checks))."' WHERE id='".$data['id']."'";
    $db->SQL($query);
    $db->Query();

  }

}



echo "   
   <br>
        <input type='checkbox' name='afvinken' value='1'> In database bijwerken.    
           <br>
   
   <br>
          <a href='javascript:checkAll(1);'> Alles selecteren</a> | 
          <a href='javascript:checkAll(0);'>Niets selecteren</a> |
          <a href='javascript:checkAll(-1);'> Selectie omkeren</a>
            <br>
            


";
$query="SELECT id,portefeuille,fonds,controleRegels FROM TijdelijkeBulkOrdersV2";
$db->SQL($query);
$db->Query();
echo "<br>\n<table><tr>$header</tr>";
while($data=$db->nextRecord())
{
  if($_POST['bulkId_'.$data['id']]==1)
    $checked = 'checked';
  else
    $checked = '';


  $checks=unserialize($data['controleRegels']);
  $tmp='';
  $error=0;
  foreach($allChecks as $check=>$checkOmschrijving)
  {
//  foreach($allChecks as $checks=>$checkOmschrijving)
    if($checks[$check]['short'] > 0)
    {
      $style = "style='background-color: red'";
      $error=1;
    }
    else
      $style='';
    $tmp.="<td $style>".$checks[$check]['short']."|".$checks[$check]['checked']."</td>";
  }

  if($error==1)
    $checkbox="<input type='checkbox' $checked name='bulkId_".$data['id']."' value='1'>";
  else
    $checkbox='';

  $row="<tr><td>$checkbox</td><td>".$data['id']."</td><td>".$data['portefeuille']."</td><td>".$data['fonds']."</td>$tmp";

  echo $row;
// listarray($checks);
}

echo "</table>";
echo ' <input type="submit"> </form></div>';

echo template($__appvar["templateContentFooter"],$content);



?>