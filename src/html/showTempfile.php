<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/07/31 14:36:15 $
 		File Versie					: $Revision: 1.7 $

 		$Log: showTempfile.php,v $
 		Revision 1.7  2019/07/31 14:36:15  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2017/07/16 10:50:44  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2017/02/12 11:20:39  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2012/11/21 15:13:09  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2011/06/13 14:36:12  rvv
 		*** empty log message ***

 		Revision 1.2  2011/05/05 15:44:12  rvv
 		*** empty log message ***

 		Revision 1.1  2011/04/30 16:23:58  rvv
 		*** empty log message ***

 		Revision 1.1  2009/11/15 16:45:31  rvv
*/

include_once("wwwvars.php");

if ($_GET["show"] == 1)
{
    $filename=$_GET['filename'];
    if(strstr($filename,'../'))
      exit;
    $filePath=$__appvar['tempdir'].$filename;
    $fileSize=filesize($filePath);
    $handle = fopen($filePath, "r");
    $contents = fread($handle,$fileSize );
    if($_GET["unlink"] == 1)
      unlink($filePath);
    fclose($handle);
    header("Pragma: public");
    header("Expires: 0");
    if(substr($filename,-3)=='xls')
      header("Content-type: application/vnd.ms-excel");
    if(substr($filename,-3)=='pdf')
      header("Content-Type: application/pdf");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$fileSize);
    echo $contents;
  exit;
}
elseif ($_GET["show"] == 2)
{
$db=new DB();
$query="SELECT id,filename,attachment FROM emailQueueAttachments WHERE id='".$_GET['id']."'";
$db->SQL($query);
$db->Query();
$att=$db->lookupRecord();

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-Disposition: attachment; filename="'.$att['filename'].'"');
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".strlen($att['attachment']));

    echo $att['attachment'];


}
elseif ($_GET["show"] == 3 || $_GET["show"] == 4)
{
    $db=new DB();
    if($_GET["show"] == 3)
    {
        $veld = 'pdfData';
        $prefix='';
    }
    elseif($_GET["show"] == 4)
    {
        $veld = 'pdfFactuurData';
        $prefix='factuur_';
    }
    $query="SELECT id,filename, $veld as pdfData FROM portaalQueue WHERE id='".$_GET['id']."'";
    $db->SQL($query);
    $db->Query();
    $att=$db->lookupRecord();

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-Disposition: attachment; filename="'.$prefix.$att['filename'].'"');
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".strlen($att['pdfData']));
    echo $att['pdfData'];
}
elseif ($_GET["show"] == 5)
{
    $db=new DB();
    $query="SELECT id,filename, blobdata as pdfData FROM eDossierQueue WHERE id='".$_GET['id']."'";
    $db->SQL($query);
    $db->Query();
    $att=$db->lookupRecord();

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-Disposition: attachment; filename="'.$att['filename'].'"');
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".strlen($att['pdfData']));
    echo $att['pdfData'];
}
?>