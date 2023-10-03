<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/09/23 17:14:23 $
 		File Versie					: $Revision: 1.1 $

 		$Log: invulhulpTest.php,v $
 		Revision 1.1  2018/09/23 17:14:23  cvs
 		call 7175
 		

 		
*/


include("wwwvars.php");
include_once("../classes/AE_cls_digidoc.php");
$inst = new AIRS_invul_instructies();

if ($_GET["VB"] != $_SESSION["invulVB"])
{
  $_SESSION["invulVB"] = $_GET["VB"];
}

$cfg=new AE_config();
$data=array();

$db = new DB();
$query = "
SELECT DISTINCT(Vermogensbeheerder) AS vb ,Naam FROM Vermogensbeheerders ORDER BY Vermogensbeheerder
";

$db->executeQuery($query);
$options = "\n\t<option value='' >*</option>";
while ($rec = $db->nextRecord())
{
  $selected = ($_SESSION["invulVB"] == $rec["vb"])?"SELECTED":"";
  $options .= "\n\t<option value='".$rec["vb"]."' $selected>(".$rec["vb"].") >> ".$rec["Naam"]."</option>";
}

$content1["jsincludes"] = '
    <script type="text/javascript" src="javascript/jquery-min.js"></script>
    <script type="text/javascript" src="javascript/jquery-ui-min.js"></script>
    <link rel="stylesheet" href="style/AE-jqueryPluginInvulinstructie.css">
    <script type="text/javascript" src="javascript/AE-jqueryPluginInvulinstructie.js"></script>
    <script language=JavaScript src="javascript/algemeen.js" type=text/javascript></script>';

echo template($__appvar["templateContentHeader"],$content1);
?>
<link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">
<link rel="stylesheet" href="style/fontAwesome/font-awesome.min.css">
<style>
  fieldset{
    width: 400px;
  }
  button{
    width: 99%;
    padding:5px;
  }
  legend{
    background: rgba(20,60,90,1);
    color: #FFF;
    padding: 5px;

    margin:0;
    border-radius: 5px;
    width:97%
  }

</style>
<h1>Invulinstructie TESTpagina</h1>
<h2>geselecteerd: <?=$_SESSION["invulVB"]?></h2>
<p>
  script: <b>invulhulpTest.php</b>
</p>
<form id="testForm">
 <?=$inst->getMessageDiv();?>
<fieldset>
  <legend> Vermogen beheerder </legend>
  <select name="VB" id="VB">
    <?=$options?>
  </select>
</fieldset>


<br/>
<br/>
<fieldset>
  <legend> Testvragen </legend>
  <p>testvraag_1 &nbsp;&nbsp;&nbsp;&nbsp;<input name="testvraag_1"  id="testvraag_1"/></p>
  <p>testvraag_2 &nbsp;&nbsp;&nbsp;&nbsp;<input name="testvraag_2"  id="testvraag_2"/></p>
  <p>testvraag_3 &nbsp;&nbsp;&nbsp;&nbsp;<input name="testvraag_3"  id="testvraag_3"/></p>
  <button class="btn-new btn-default"> klik </button><br/>
</fieldset>
</form>


<script>
  $(document).ready(function(){
    $("#VB").change(function(){
      $("#testForm").submit();
    });
    $(document).on('focus, keyup', 'input, select', function () {
      $(this).invulInstructie({
        party: '<?=$_SESSION["invulVB"]?>',
        script: 'invulhulpTest.php',
        consoleLog: '1'
      });
    });
  });
</script>
<?


echo template($__appvar["templateRefreshFooter"],$content);
exit;
