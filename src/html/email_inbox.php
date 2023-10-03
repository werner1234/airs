<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/10/27 08:54:35 $
 		File Versie					: $Revision: 1.2 $

 		$Log: email_inbox.php,v $
 		Revision 1.2  2017/10/27 08:54:35  cvs
 		no message
 		
 		Revision 1.1  2016/04/22 10:11:06  cvs
 		call 4296 naar ANO
 		
 		Revision 1.2  2016/01/30 16:43:44  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/01/30 16:21:38  rvv
 		*** empty log message ***
 		
 		
*/


include("wwwvars.php");
include_once("../classes/AE_cls_digidoc.php");
include_once("../classes/AE_cls_Email.php");
ini_set('default_charset', 'utf-8');

$mailRow = "
<tr class='msgRow' id='row_{id}'>
  <td>{index}</td>
  <td>{stamp}</td>
  <td>{subject}</td>
  <td>{from}</td>
</tr>
";

$tmpl = new AE_template();
$fmt = new AE_cls_formatter();

if ( ! isset ($__appvar['office365']) ) {
  $mail = new AE_cls_Email();
} else {
  $mail = new AE_cls_ExchangeOnline();
}

$mail->initTables();  // tabellen aanmaken voor module
//$mail->buildRouterTable();

$tmpl->loadTemplateFromString($mailRow,"mailRow");

$_SESSION['NAV']='';
$content['pageHeader'] = "<br><div class='edit_actionTxt'><b>$mainHeader</b> $subHeader</div><br><br>";
echo template($__appvar["templateContentHeader"],$content);
?>
<link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">
<link rel="stylesheet" href="style/fontAwesome/font-awesome.min.css">

<button class="btn-new btn-default"><a href="dd_inlees_email.php"><i class="fa fa-angle-double-left" aria-hidden="true"></i> terug </a></button>
<button class="btn-new btn-default"><a href="?"><i class="fa fa-refresh" aria-hidden="true"></i> ververs</a></button><br/><br/>
<?
if ($mail->errorState())
{
  echo "<h2>Fout:</h2>";
  echo $mail->lastStatus();
  exit;
}

echo $mail->lastStatus();

$mail->populateInbox();


?>
<style>
  .headField
  {
    display: inline-block;
    width: 70px;
    font-weight: normal;

  }
  table{
    width: 100%;
    background: whitesmoke;
    margin:10px;
    padding:10px;
  }
  .trHead{
    background:  rgba(20,60,90,1);
    color: white;
    border-bottom: 1px solid #FFF;

  }
  .trHead td{
    padding: 5px;
    color:white;
    padding-left:10px;
  }
  #previewMsg{
    background: #dff0d8;
    color: #333; !important;
    width: 800px;
    height: 400px;
    box-shadow: #000A28 3px 3px 3px;
    overflow: auto;
    padding:5px;
    float: left;
  }
  .mailHead{
    background: #0A246A;
    color: whitesmoke; !important;
    width: 760px;
    height: 30px;
    box-shadow: #000A28 3px 3px 3px;
    padding: 10px;
    font-weight: bold;
  }
  .msgRow :hover{
    cursor: pointer;
    background: #ffc121;
  }
.mailbox{
  float: left;
  width: 770px;
}
</style>
<div class="mailbox">
<table>
  <tr class="trHead">
    <td>#</td>
    <td>datum</td>
    <td>onderwerp</td>
    <td>afzender</td>
  </tr>
<?php

  foreach($mail->messageArray as $msg)
  {
     $msg["stamp"] = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $msg["stamp"]);
     $msg["id"] = $msg["index"];
     $msg["index"]++;
//     echo $tmpl->parseBlock("mailRow",$msg);

     echo '
       <tr class="msgRow" id="row_' . $msg["id"] . '">
        <td>' . $msg["index"] . '</td>
        <td>' . $msg["stamp"] . '</td>
        <td>' . $msg['subject'] . '</td>
        <td>' . $msg['from'] . '</td>
      </tr>
     ';
  }
?>

</table>
</div>


<script>
  $(document).ready(function ()
  {


  })


</script>
<?

echo template($__appvar["templateRefreshFooter"],$content);


?>
