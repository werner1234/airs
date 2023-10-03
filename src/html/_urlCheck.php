<?php
/*
  Author  						: $Author: cvs $
  Laatste aanpassing	: $Date: 2019/05/14 13:48:01 $
  File Versie					: $Revision: 1.3 $

  $Log: _urlCheck.php,v $
  Revision 1.3  2019/05/14 13:48:01  cvs
  call 7783

  Revision 1.2  2019/05/10 14:34:13  cvs
  call 7783

  Revision 1.1  2019/05/10 13:31:34  cvs
  call 7783


 */

include_once("wwwvars.php");
include_once("../config/JSON.php");
session_start();

//$content = array();
global $USR;

if ($_GET["export"] == "csv")
{
  $file = "/tmp/urlChecker_".date("YmdHis").".csv";
  file_put_contents ($file, implode("\n", $_SESSION["urlChecker"]));
  if(false !== ($handler = fopen($file, 'r')))
  {
    header('Content-Description: File Transfer');
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file)); //Remove
    readfile($file);

  }
  unlink($file);
  unset ($_GET);
  exit;
}



// if poster

//$content = array("javascript"=>'<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">');
$content['style2'] .= '<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">';
echo template($__appvar["templateContentHeader"], $content);


?>
<style>

 .pContainer{
 margin:0 auto;
 width: 1610px;
 min-height: 150px;
 border: 1px black solid;

 }
 .head{
   margin:0 auto;
   width: 1600px;
   background: #333;
   color: white;
   text-align: center;
   padding: 5px;
   font-size: 2em;
 }
.k0{ display: inline-block; width: 80px;}
.k1{ display: inline-block; width: 1000px;}
.k2{ display: inline-block; width: 49px;  text-align: center }
.k3{ display: inline-block; width: 290px;  text-align: center }
.k3{ display: inline-block; width: 50px;  text-align: center }
 .header{
 background: #DDD;
   padding: 5px;

}
 .rowContainer{
   width: 1598px;
   height: 24px;
   padding: 5px;
   border-bottom: 1px #bbb solid;
 }
 .rowClr{
   padding: 5px;
 }
 /*.rowClr:nth-child(even) {background: #EEE}*/
 /*.rowClr:nth-child(odd) {background: #FFF}*/
</style>
<br/>
<div class="head">URL check</div>
<br/>
<br/>
  <div class="pContainer">

<?
  if ($_POST["posted"] == 1)
  {
    $out = array();

?>
<div class="rowContainer">
    <span class="k0 header">teller</span>
    <span class="k1 header">url</span>
    <span class="k2 header">Okay</span>
    <span class="k3 header">filetype</span>
    <span class="k4 header">duur</span>
</div>

<?
  $rows = file($_FILES["file"]["tmp_name"]);
  $count = 0;
  $tRows = count($rows);
  foreach ($rows as $row)
  {
    $count++;
    $start = time();
    $url = trim($row);
    $o = (array)json_decode(urlTest($url));
    $elapsed = time() - $start;
    $out[] = $url."\t".(($o["code"])?"":"fout")."\t".$o["type"];

    echo "
    <div class='rowContainer'>
      <span class='k0 rowClr'>$count / $tRows</span>
      <span class='k1 rowClr'>$url</span>
      <span class='k2 rowClr'>".(($o["code"])?"":"fout")."</span>
      <span class='k3 rowClr'>{$o["type"]}</span>
      <span class='k4 rowClr'>{$elapsed}sec.</span>
    </div>";
    flush();
    ob_flush();

  }

  $_SESSION["urlChecker"] = $out;


  echo "<br/><br/><br/><form>&nbsp;&nbsp;&nbsp;<input type='submit' value='maak .CSV'> <input type='hidden' name='export' value='csv'></form> ";
  }
  else
  {

?>


  <br/>
  <br/>
  <form method="post" enctype="multipart/form-data" >
    <input type="hidden" name="posted" value="1" >

    <div class="formblock">
      <div class="formlinks">bestand:</div>
      <div class="formrechts">
        <input type="file" name="file"   type="file" value="" >
      </div>
    </div>
    <div class="formblock">
      <br/>
      <br/>
      <input type="submit" value="start verwerken">
    </div>
  </form>
</div>


<?
}
echo template($__appvar["templateRefreshFooter"], $content);


function urlTest($url)
{
  $res = get_headers($url);
  foreach ($res as $item)
  {
    if (stristr($item, "HTTP/"))
    {
      $r1 = explode(" ",$item);
      $code = $r1[1];
    }
    if (stristr($item, "Content-Type:"))
    {
      $r1 = explode("/",$item);
      $filetype = $r1[1];
    }
  }



  return '{ "code": "'.$code.'", "type": "'.$filetype.'"}';




}