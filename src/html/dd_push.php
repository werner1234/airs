<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/04/30 16:23:58 $
 		File Versie					: $Revision: 1.2 $
*/

include_once("wwwvars.php");
include_once("../classes/AE_cls_digidoc.php");
$dd = new digidoc();
if ($_GET["show"] == 1)
{
  if($_GET["datastore"] <> '')
    $dd->pushDocument($_GET["datastore"],$_GET["dd_id"]);
  else
  {
    $filename=$dd->retrieveDocumentToFile($_GET['docRefId'],$__appvar['tempdir']);
    $filePath=$__appvar['tempdir'].$filename;
    $fileSize=filesize($filePath);
    $handle = fopen($filePath, "r");
    $contents = fread($handle,$fileSize );
    fclose($handle);
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$fileSize);
    echo $contents;
  }
  exit;
} elseif ( isset ($_GET['downloadZip']) && (int) $_GET['downloadZip'] === 1 && (isset ($_GET['fileIds']) && !empty ($_GET['fileIds'])) ) {
  $zip = new ZipArchive;
  $fileIds = explode(',', $_GET['fileIds']);

  $zipFileName = 'Documents.zip';
  if ( isset ($_GET['fileName']) && ! empty ($_GET['fileName']) ) {
    $zipFileName = basename($_GET['fileName']) . '.zip';
  }

  $tmpZipName = tempnam($__appvar['tempdir'], "zip");
  if( $zip->open($tmpZipName, ZipArchive::CREATE ) === TRUE) {
    if (!is_array($fileIds)) {
      $fileIds = array($fileIds);
    }

    // Haal bestanden op
    $currentFileNames = array();
    $removeFileList = array();
    foreach ($fileIds as $fileId) {
      $filename = $dd->retrieveDocumentToFile( (int) $fileId, $__appvar['tempdir'], $currentFileNames);
      $filename = basename($filename);
      $currentFileNames[] = $filename;
      $removeFileList[] = $__appvar['tempdir'] . $filename;
      $zip->addFile($__appvar['tempdir'] . $filename, $filename);
    }
    $zip->close();

    // Download zip bestand
    header('Content-type: application/zip');
    header('Content-Disposition: attachment; filename="'.$zipFileName.'"');
    readfile($tmpZipName);

    // Verwijder zip bestand en tijdelijke bestanden
    unlink($tmpZipName);
    foreach ( $removeFileList as $tempFile ) {
      unlink($tempFile);
    }
  }
}