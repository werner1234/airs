<?php
include_once("wwwvars.php");

function readfile_chunked($filename,$retbytes=true) {
   $chunksize = 1*(1024*1024); // how many bytes per chunk
   $buffer = '';
   $cnt =0;
   // $handle = fopen($filename, 'rb');
   $handle = fopen($filename, 'rb');
   if ($handle === false) {
       return false;
   }
   while (!feof($handle)) {
       $buffer = fread($handle, $chunksize);
       echo $buffer;
       if ($retbytes) {
           $cnt += strlen($buffer);
       }
   }
       $status = fclose($handle);
   if ($retbytes && $status) {
       return $cnt; // return num. bytes delivered like readfile() does.
   }
   return $status;
}

$filename = $_GET['file'];
$action = $_GET['action'];
$filetype = $_GET['filetype'];
if(!empty($filename))
{
	if($action == "attachment") {
		$content_disposition = "attachment";
	}
	else {
		$content_disposition = "inline";
	}

	if($filetype == "gzip")
	{
 		$appType = "application/octet-stream";

	}
	else if($filetype == "csv")
	{
		//$appType = "application/octet-stream";
		$appType = "text/comma-separated-values";
	}
	else if($filetype == "xls" || substr($filename,-4)=='.xls')
	{
		$appType = "application/octet-stream";
	}
	else
	{
 		$appType = "application/pdf";
	}

	$file = $__appvar['tempdir'].$filename;
	header('Content-type: ' . $appType);
	if(headers_sent())
		echo "FOUT: headers zijn al verzonden";

	header("Content-Length: ".filesize($file));
	header("Content-Disposition: ".$content_disposition."; filename=\"".$filename."\"");
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

	readfile_chunked($file);
	unlink($file);
}
exit;
?>