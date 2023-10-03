<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/08/13 16:52:24 $
 		File Versie					: $Revision: 1.4 $

 		$Log: custom_txtList.php,v $
 		Revision 1.4  2016/08/13 16:52:24  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2011/08/31 14:37:39  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2010/05/05 18:28:17  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/05/06 10:21:26  rvv
 		*** empty log message ***


*/

include_once("wwwvars.php");

include_once("../classes/mysqlList.php");
$_SESSION['NAV']='';
$editScript = "custom_txtEdit.php";

$rapporten = array('ATT','PERF','FRONT','HUIS');
$list = new MysqlList();
$list->idField = 'id';

$list->addField("","link",array("description"=>' ',"list_width"=>50,"search"=>false));
$list->addField("","rapport",array("list_width"=>100,"search"=>false));
$list->addField("","veld",array("list_width"=>100,"search"=>false));
$list->addField("","vermogensbeheerder",array("list_width"=>100,"search"=>false));
$list->addField("","koppeling",array("list_width"=>100,"search"=>false));

$content[javascript] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);

$db = new DB();
$query="SELECT vermogensbeheerder,txtKoppeling FROM Vermogensbeheerders WHERE txtKoppeling <> '' ";
$db->SQL($query);
$db->Query();
while ($data = $db->nextRecord())
{
  $vermogensbeheerders[$data['vermogensbeheerder']] = $data['txtKoppeling'];
}

?>
<br>
<table class="list_tabel" cellspacing="0">
<?
echo $list->printHeader(true);

foreach ($vermogensbeheerders as $vermogensbeheerder=>$koppeling)
{
  switch ($koppeling)
  {
    case 'Accountmanager' :
      $query = "SELECT Vermogensbeheerder, Accountmanager as veld  FROM Accountmanagers WHERE vermogensbeheerder = '$vermogensbeheerder' ORDER BY veld";
    break;
    case 'Risicoklasse' :
      $query = "SELECT Vermogensbeheerder, Risicoklasse as veld  FROM Risicoklassen WHERE vermogensbeheerder = '$vermogensbeheerder' ORDER BY veld";
    break;
    case 'ModelPortefeuille' :
      $query = "SELECT Vermogensbeheerder, ModelPortefeuille as veld  FROM Portefeuilles WHERE vermogensbeheerder = '$vermogensbeheerder' AND ModelPortefeuille <> '' GROUP BY ModelPortefeuille ORDER BY veld";
    break;
    case 'SpecifiekeIndex' :
      $query = "SELECT Vermogensbeheerder, SpecifiekeIndex as veld  FROM Portefeuilles WHERE vermogensbeheerder = '$vermogensbeheerder' AND SpecifiekeIndex <> '' GROUP BY SpecifiekeIndex ORDER BY veld";
    break;
    case 'Vermogensbeheerder' :
      $query = "SELECT Vermogensbeheerder, Vermogensbeheerder as veld  FROM Vermogensbeheerders WHERE vermogensbeheerder = '$vermogensbeheerder' AND vermogensbeheerder <> '' ORDER BY veld";
    break;
    default:
      $query = 'SELECT null';
    break;
  }

 $db->SQL($query);
 $db->Query();
 $db2=New DB();
 while ($dbdata = $db->nextRecord())
 {
   foreach($list->columns as $waarden)
   {
     foreach ($waarden['options'] as $key=>$value)
       $data[$waarden['name']][$key]=$value;
   }
  $data['disableEdit'] = true;
  $data['vermogensbeheerder']['value'] = $dbdata['Vermogensbeheerder'];
  $data['veld']['value'] = $dbdata['veld'];
  $data['koppeling']['value'] = $koppeling;


  foreach ($rapporten as $rapport)
  {
    $query = "SELECT id FROM custom_txt
            WHERE
            Vermogensbeheerder = '".$dbdata['Vermogensbeheerder']."' AND
            field = concat('$rapport','_','".$dbdata['veld']."') AND
            type = '$koppeling' AND
            txt <> '' ";
    if($db2->QRecords($query) > 0)
      $extra = drawButton('relaties');
    else
      $extra = '';
    $data['link']['value']="<a href=\"$editScript?action=edit&rapport=$rapport&vermogensbeheerder=".$dbdata['Vermogensbeheerder']."&veld=".$dbdata['veld']."&type=$koppeling\">".drawButton("edit")."$extra</a>\n";
    $data['rapport']['value'] = $rapport;
    echo $list->buildRow($data);
  }
 }

}




?>
</table>
<?
logAccess();
echo template($__appvar["templateRefreshFooter"],$content);

?>