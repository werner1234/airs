<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/02/04 19:07:57 $
 		File Versie					: $Revision: 1.1 $

 		$Log: orderregelsNotaEmailOpmaak.php,v $
 		Revision 1.1  2017/02/04 19:07:57  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2016/12/28 19:37:27  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2016/01/30 16:43:44  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2016/01/30 16:21:38  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/08/06 15:38:08  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2013/10/26 15:40:51  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2012/11/21 15:11:46  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2011/09/08 07:17:48  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2011/08/31 14:37:39  rvv
 		*** empty log message ***

 		Revision 1.2  2010/12/01 14:40:28  rvv
 		*** empty log message ***

 		Revision 1.1  2009/10/11 14:54:09  rvv
 		*** empty log message ***




*/
include_once("wwwvars.php");

$velden=array('notaOnderwerp','notaEmail');
$cfg=new AE_config();
if($_POST)
{
  $db=new DB();
  $data=$_POST;
  foreach($velden as $veld)
  {
    //if($veld=='fondskoersLockDatum')
    //  $data['fondskoersLockDatum']=jul2sql(form2jul($data['fondskoersLockDatum']));

    $query="SELECT ae_config.id,ae_config.value FROM ae_config WHERE field='$veld'";
    $db->SQL($query);
    $oldData=$db->lookupRecord();
    if($oldData['value'] <> $data[$veld])
    {
      addTrackAndTrace('ae_config', $oldData['id'] , 'value|'.$veld, $oldData['value'], $data[$veld], $USR);
    }
    $cfg->addItem($veld, $data[$veld]);
  }
  header("Location: orderregelsNotaListV2.php");
}
else
{
  foreach($velden as $veld)
  {
    $data[$veld] = $cfg->getData($veld);
  }
}


$_SESSION['NAV']='';

$content['jsincludes'].='<script type="text/javascript" src="javascript/ckeditor/ckeditor.js"></script>';
$content['body']='onLoad="doEditorOnload();"';
$content['pageHeader'] = "<br><div class='edit_actionTxt'></div><br>";


$content['javascript'].="
function loadEditor(textarea,h,w)
{
  CKEDITOR.replace( textarea ,
	{
		skin : 'office2003',
		DIALOG_RESIZE_NONE : true,
		height: h,
		width: w,
		toolbar :
   [
    ['Source','-','Save','NewPage','Preview','-','Templates'],
    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
    '/',
    ['Font','Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock']
   ],
   font_names : 'Arial/Arial, Helvetica, sans-serif; Comic Sans MS/Comic Sans MS, cursive; Courier New/Courier New, Courier, monospace; Georgia/Georgia, serif; Lucida Sans Unicode/Lucida Sans Unicode, Lucida Grande, sans-serif; Tahoma/Tahoma, Geneva, sans-serif; Times New Roman/Times New Roman, Times, serif; Trebuchet MS/Trebuchet MS, Helvetica, sans-serif; Calibri/Calibri, Verdana, Geneva, sans-serif; Verdana/Verdana, Geneva, sans-serif' 
	});
  
  
}

function doEditorOnload()
 { 
   loadEditor('notaEmail',500,700);
 }

function saveSettings()
{
	document.selectForm.target = '';
	document.selectForm.submit();
}

function testMail()
{
	document.selectForm.target = '';
  document.selectForm.testmail.value = '1';
  document.selectForm.stap.value='opmaak';
	document.selectForm.submit();  
}  

";

echo template($__appvar["templateContentHeader"],$content);


//$data['fondskoersLockDatum'] = dbdate2form($data['fondskoersLockDatum']);
if($data['notaOnderwerp']=='')
  $data['notaOnderwerp']='Order {orderid}';

if($data['notaEmail']=='')
  $data['notaEmail']='Hierbij ontvangt u uw effectennota met ordernummer  {orderid}.
<br/><br/>
{handtekening}';
?>
<form method="POST">


  <div class="formblock">
    <div class="formlinks">eMail onderwerp</div>
    <div class="formrechts">
      <input type="text" name="notaOnderwerp" size=60 value="<?=$data['notaOnderwerp']?>">
    </div>
  </div>

  <div class="formblock">
    <div class="formlinks"><label for="notaEmail" title="email">Email</label> </div>
    <div class="formrechts">
      <textarea class=""  cols="60"  rows="2" name="notaEmail" id="notaEmail" ><?=$data['notaEmail']?></textarea>
    </div>
  </div>

  </br></br>

<input type="submit" value="Opslaan">
</form>
<?


echo template($__appvar["templateRefreshFooter"],$content);


?>