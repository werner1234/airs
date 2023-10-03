<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar["listurl"]  = "updatehistoryList.php";
$__funcvar["location"] = "updatehistoryEdit.php";

$object = new UpdateHistory();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->usetemplate = true;
$editObject->formTemplate = "updatehistoryEditTemplate.html";

$editObject->controller($action,$data);

$remoteDef=unserialize($object->get('tableDef'));
$localDef=getTableDef();

if(is_array($remoteDef))
{
  $missing=array('tabel'=>array(),'veld'=>array(),'formaat'=>array());
  foreach ($localDef as $table=>$fields)
  {
    if(!is_array($remoteDef[$table]))
    {
      $missing['tabel'][] = vt("Tabel")." $table ".vt("niet aanwezig")." <br>\n";
    }
    else
    {
      foreach ($fields as $fieldname=>$size)
      {
        if(!isset($remoteDef[$table][$fieldname]))
        {
          $missing['veld'][] = vt("Veld").$table.$fieldname." ".vt("niet aanwezig")."<br>\n";
        }
        else
        {
          if($size != $remoteDef[$table][$fieldname])
          {
            $missing['formaat'][]="Formaat van $table.$fieldname afwijkend ($size <> ".$remoteDef[$table][$fieldname].") <br>\n";
          }
        }
      }
    }
  }
}
else
 $missing['tabel'][] = vt("Geen informatie beschikbaar.");

foreach ($missing as $key=>$values)
{
  $editObject->formVars["tablediff"].="<b>$key</b><br>\n";
  foreach ($values as $value)
    $editObject->formVars["tablediff"].=$value;
}





echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
?>