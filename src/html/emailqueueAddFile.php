<?php
/*
    AE-ICT CODEX source module versie 1.6, 2 juni 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/02/22 09:53:55 $
    File Versie         : $Revision: 1.2 $

    $Log: emailqueueAddFile.php,v $
    Revision 1.2  2015/02/22 09:53:55  rvv
    *** empty log message ***

    Revision 1.1  2011/07/10 14:18:28  rvv
    *** empty log message ***

    Revision 1.3  2011/06/15 15:37:48  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");

if(isset($_POST['posted']) && $_POST['posted']=='true')
{
    $ids=array();
  $_error=array();

	if($_FILES['importfile']['name']=='')
		$_error[] = "Fout: geen bestandsnaam?";
	if($_FILES['importfile']['error'] != 0)
		$_error[] = "foutcode: ".$_FILES['importfile']['error'];

	$handle = fopen($_FILES['importfile']['tmp_name'], "r");
  $contents = fread($handle, filesize($_FILES['importfile']['tmp_name']));
  $blobData = bin2hex($contents);

  if(count($_error)==0)
  {
    
    $db=new DB();
    /*
    $query="SELECT id FROM emailQueue";
    $db->SQL($query);
    $db->Query();
    while ($data=$db->nextRecord())
      $ids[]=$data['id'];
    */
    $ids=explode('|',$_POST['ids']);
    if(count($ids) > 0)
    {
      foreach ($ids as $id)
      {
        $query="INSERT INTO emailQueueAttachments
        SET emailQueueId='$id',
        filename='".$_FILES['importfile']['name']."',
        attachment=unhex('$blobData'),
        add_date=now(),add_user='$USR',change_date=now(),change_user='$USR'";
        $db->SQL($query);
        if(!$db->Query())
          $_error[] = "Toevoegen mislukt.";
      }
    }
    else
      $_error[] = "Geen email ids ontvangen.";

	  if(count($_error)==0)
	  {
      $_error[] = "Upload voltooid.";
      include('emailqueueList.php');
      foreach ($_error as $error)
       	echo "<b style=\"color:red;\">".$error."</b><br>";
      exit;
	  }
  }
}
$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>Bijlage toevoegen</b> $subHeader </div><br><br>";
echo template($__appvar["templateContentHeader"],$editcontent);
?>
<div class="form">
<form enctype="multipart/form-data"  method="POST" action="emailqueueAddFile.php">
<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="ids" value="<?=implode('|',$ids)?>" />

<?php
if($_error)
{
foreach ($_error as $error)
	echo "<b style=\"color:red;\">".$error."</b><br>";
}
?>
<div class="form">
<div class="formblock">
<div class="formlinks">Bestand </div>
<div class="formrechts">
<input type="file" name="importfile" size="50">
</div>
</div>
<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="submit" value="Toevoegen">
</div>
</div>
</form>
</div>
<?
echo template($__appvar["templateRefreshFooter"],$editcontent);
?>