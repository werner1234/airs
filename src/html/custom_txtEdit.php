<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/01/11 14:35:57 $
 		File Versie					: $Revision: 1.6 $

 		$Log: custom_txtEdit.php,v $
 		Revision 1.6  2020/01/11 14:35:57  rvv
 		*** empty log message ***

*/
 		
include_once("wwwvars.php");
if($_POST)
{
  $change="change_user='".mysql_real_escape_string($_POST['vermogensbeheerder'])."',";
  $change.="change_date=now()";
  $add =$change.",";
  $add="add_user='".mysql_real_escape_string($_POST['vermogensbeheerder'])."',";
  $add.="add_date=now()";

  if($_POST['id'] > 0)
  {
    $query = "UPDATE custom_txt SET
              Vermogensbeheerder = '".addslashes($_POST['vermogensbeheerder'])."',
              field = '".addslashes($_POST['field'])."',
              title = '".addslashes($_POST['title'])."',
              txt = '".addslashes($_POST['tekst'])."',
              extraKoppeling = '".addslashes($_POST['extraPdf'])."',
              type = '".addslashes($_POST['type'])."', $change
              WHERE id = '".$_POST['id']."'";
  }
  else
  {
        $query = "INSERT INTO custom_txt SET
              Vermogensbeheerder = '".addslashes($_POST['vermogensbeheerder'])."',
              field = '".addslashes($_POST['field'])."',
              title = '".addslashes($_POST['title'])."',
              txt = '".addslashes($_POST['tekst'])."',
              extraKoppeling = '".addslashes($_POST['extraPdf'])."',
              type = '".addslashes($_POST['type'])."', $add ";
  }
  $db=new DB();
  $db->SQL($query);
  $db->Query();
  header("location: custom_txtList.php");
  exit;
}
else
{
$query = "SELECT *
          FROM
            custom_txt
          WHERE
            Vermogensbeheerder = '".$_GET['vermogensbeheerder']."' AND
            field = concat('".$_GET['rapport']."','_','".$_GET['veld']."') AND
            type = '".$_GET['type']."'  ";
$db = new DB();
$db->SQL($query);
$data = $db->lookupRecord();

///
$options.='<option value="" >'.vt("Geen").'</option>';
 $theDir = realpath(dirname(__FILE__))."/PDF_templates/";
 $dir = @opendir($theDir); // open the directory
 if(empty($dir))
 {
   mkdir($theDir);
   $dir = @opendir($theDir);
 }
 while($file = readdir($dir)) // loop once for each name in the directory
 {
	 if(is_file($theDir.$file) )
	 {
	 	 $filedata = stat($theDir.$file); // get some info about the file
		  if (strtolower(substr($file,-4)) == ".pdf")
			{
			  $fullFile=$theDir.$file;
			  if($data['extraKoppeling']==$fullFile)
			   $selected="selected";
			  else
			    $selected='';
			 $options.='<option value="'.$fullFile.'" '.$selected.'>'.$file.'</option>';
			}
	 }
 }

///
$__appvar["basedir"] =  realpath(dirname(__FILE__)."/..");

$editcontent['javascript'].="

function loadEditor(textarea)
{
  CKEDITOR.replace( textarea ,
	{
    height: 400,
		width: 600,
    uiColor: '#9AB8F3',
    allowedContent: true 
	});
}

";

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->returnUrl = 'custom_txtList.php';
$_SESSION['NAV']->addItem(new NavEdit("editForm",true,false,true));

$_SESSION['submenu'] = New Submenu();

$editcontent['body'].="onLoad=\"doEditorOnload()\"";
echo template($__appvar["templateContentHeader"],$editcontent);

?>
 <script type="text/javascript" src="javascript/ckeditor4/ckeditor.js"></script>
<style>
.formlinks{
width: 200px;
}
</style>

<div class="form">
<form name="editForm" action="<?=$PHP_SELF?>" method="POST">
<input type="hidden" name="type" value="<?=$_GET['type']?>">
<input type="hidden" name="id" value="<?=$data['id']?>">
<input type="hidden" name="field" value="<?=$_GET['rapport']."_".$_GET['veld']?>">
<input type="hidden" name="vermogensbeheerder" value="<?=$_GET['vermogensbeheerder']?>">
<input type="hidden" value="save" name="action">

<br><br>
<div class="formblock">
  <div class="formlinks"> <?=vt("Kop tekst")?></div>
  <div class="formrechts">
    <input size="80"  name="title" id="title" value="<?=$data['title']?>">
  </div>
</div>


<div class="formblock">
  <div class="formlinks"> </div>
  <div class="formrechts">
    <textarea class=""  cols="60"  rows="10" name="tekst" id="tekst" ><?=$data['txt'];?></textarea>
  </div>
</div>

<div class="formblock">
  <div class="formlinks"> <?=vt("Extra PDF")?> </div>
  <div class="formrechts">
    <select name="extraPdf" id="extraPdf">
    <?=$options?>
    </select>
  </div>
</div>

</form>
</div>
<script language="JavaScript" type="text/javascript">
function doEditorOnload()
{
 loadEditor('tekst');
}
</script>



</form>
<?

// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);
}
