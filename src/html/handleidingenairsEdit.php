<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 4 januari 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: handleidingenairsEdit.php,v $
    Revision 1.3  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.2  2017/01/07 16:21:02  rvv
    *** empty log message ***

    Revision 1.1  2017/01/04 16:19:18  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = " muteren";

$__funcvar['listurl']  = "handleidingenairsList.php";
$__funcvar['location'] = "handleidingenairsEdit.php";



$object = new HandleidingenAIRS();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = array_merge($_POST,$_GET);
$action = $data['action'];

if($action=='download')
{
	$object->getById($data['id']);
	$docData=unserialize($object->get('bijlage'));

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header('Content-Disposition: attachment; filename="'.$docData['filename'].'"');
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".$docData['size']);
	echo base64_decode($docData['data']);

}

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->formTemplate = "handleidingenairsEditTemplate.html";
$editObject->usetemplate = true;

$docWarning='';
if($_FILES['bijlage']['tmp_name'] <> '')
{

	$filename = $_FILES['bijlage']['tmp_name'];
	$file = $_FILES['bijlage']['name'];
	$filesize = filesize($filename);
	$filetype = mime_content_type($filename);
	$fileHandle = fopen($filename, "r");
	$docdata = fread($fileHandle, $filesize);
	fclose($fileHandle);
	$data['bijlage']=serialize(array('data'=>base64_encode($docdata),'mime'=>$filetype,'size'=>$filesize,'filename'=>$file));
	$tail=strtolower(substr($file,-4));
	if($tail!='.pdf' && $tail!='.mp4')
	{
		$docWarning.="Document niet toegevoegd. Alleen .pdf en .mp4 bestanden kunnen worden toegevoegd.";
		$object->setError('bijlage',$docWarning);
	}
}
if($_FILES['bijlage']['name'] <> '' && $_FILES['bijlage']['error'] <> 0)
{
	$docWarning.=" Verwerken van ".$_FILES['bijlage']['name']." mislukt. Is het bestand < 10MB?";
	$object->setError('bijlage',$docWarning);
}

$editObject->controller($action,$data);


// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
?>