<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/03/11 13:30:18 $
 		File Versie					: $Revision: 1.3 $

 		$Log: API_queueExternList.php,v $
 		Revision 1.3  2019/03/11 13:30:18  cvs
 		call 7364
 		
 		Revision 1.2  2019/03/08 09:44:45  cvs
 		call 7364
 		
 		Revision 1.1  2019/03/01 08:58:02  cvs
 		call 7364
 		
 		Revision 1.5  2018/04/19 07:13:00  cvs
 		call 6791
 		
 		Revision 1.4  2018/02/16 10:29:09  cvs
 		no message
 		
 		Revision 1.3  2017/12/13 13:40:47  cvs
 		call 5911
 		
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
include_once("../classes/AE_cls_externalAPI.php");
$apiExtern = new AE_cls_externalAPI("VCK", "VCK");

$delArray = array();
$addArray = array();
if ($_POST)
{
  switch ($_POST["action"])
  {
    case "deleteItems":
      foreach ($_POST as $k=>$v)
      {
        $idx = explode("_", $k);

        if ($idx[0] == "vink")
        {
          $cat = substr($idx[1],0,1);
          $delArray[] = $_SESSION["bGiro"]["dataSet"][$cat][$idx[1]]["recId"];
        }
      }

      $apiExtern->setIgnored($delArray);
      break;
    case "addItems":
      foreach ($_POST as $k=>$v)
      {
        $idx = explode("_", $k);

        if ($idx[0] == "vink")
        {
          $cat = substr($idx[1],0,1);
          $addArray[$cat][] = $_SESSION["bGiro"]["dataSet"][$cat][$idx[1]]["recId"];
        }
      }
      $result["C"] = $apiExtern->addClienten($addArray["C"]);
      $result["P"] = $apiExtern->addPortefeuilles($addArray["P"]);
      $errormsg = $apiExtern->errorList();
      break;
    default:
      debug($_POST);
      debug($_SESSION["bGiro"]["dataSet"]);
      exit;

  }





}



$clientRow = "
<tr class='msgRow {partner}' id='row_{id}'>
  <td>
    <input type='checkbox' class='vink{x}' name='vink_{id}' id='vink_{id}'>
    <input type='hidden' id='id_{id}' name='id_{id}' value='{id}' /> 
    <input type='hidden' id='client_{id}' name='client_{id}' value='{clientid}' />
  </td>
  <td>{stamp}</td>
  <td>{clientid}</td>
  <td><pre>{datafields}</pre></td>

</tr>
";

$portefeuilleRow = "
<tr class='msgRow {partner}' id='row_{id}'>
  <td>
    <input type='checkbox' class='vink{x}' name='vink_{id}' id='vink_{id}'>
    <input type='hidden' id='id_{id}' name='id_{id}' value='{id}' /> 
    <input type='hidden' id='portefeuille_{id}' name='portefeuille_{id}' value='{clientid}' />
  </td>
  <td>{stamp}</td>
  <td>{portfolioId}</td>
  <td><pre>{datafields}</pre></td>

</tr>
";

$tmpl = new AE_template();
$fmt = new AE_cls_formatter();


//$apiExtern->initTables();  // tabellen aanmaken voor module
//$mail->buildRouterTable();

$tmpl->loadTemplateFromString($clientRow, "clientRow");
$tmpl->loadTemplateFromString($portefeuilleRow, "portefeuilleRow");

$_SESSION["NAV"]='';
$content['style'] .= '
<link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen">
<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">
';
$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";
echo template($__appvar["templateContentHeader"],$content);
?>

<link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">
<link rel="stylesheet" href="style/fontAwesome/font-awesome.min.css">
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
    color:white;
    padding:5px;
    padding-left:10px;

  }
  .trSubHead{
    background: #666;
    color: white;
    border-bottom: 1px solid #FFF;
  }
  .trSubHead td{
    color:white;
    padding-left:10px;
    text-align: center;
    font-size: 20px;
  }
  #previewMsg{
    display: none;
    background: #dff0d8;
    position: relative;
    color: #333; !important;
    width: 800px;
    height: auto;
    box-shadow: #000A28 3px 3px 3px;

    padding:5px;

  }

  .msgRow td{
    padding-bottom: 5px;
    border-bottom: 1px #333 solid;
  }
  .msgRow :hover{
    cursor: pointer;
    background: #ffc121;
  }
  .queueContainer{
    float: left;
    width: 1100px;
  }

  #showContentScr{
    position: absolute;
    top: 120px;
    left:120px;
    width: 400px;
    min-height: 120px;
    display: none;
    border:3px #FFF solid;
    box-shadow: 3px 3px 3px #333;
  }

  .legenda span{
    margin: 5px;
    display: inline-block;
    width: 95%;
    padding:8px;
    border-radius: 4px;
  }

  .ac{
    text-align: center;
  }
</style>



<div id="previewMsg">
  <h2>verwerking:</h2>
  <ul>
    <li><?=$result["C"]["add"]?> Clienten toegevoegd </li>
    <li><?=$result["C"]["update"]?> Clienten gemuteerd</li>
    <li><?=$result["P"]["add"]?> Portefeuilles toegevoegd</li>
    <li><?=$result["P"]["update"]?> Portefeuilles gemuteerd</li>
  </ul>
  <hr/>
  <h2>Foutmeldingen:</h2>
  <?=$errormsg?></div>




<?


$queue = $apiExtern->populateQueue();
//debug($queue);
?>


<div class="queueContainer">
  <form method="post" id="queueForm">




    <input type="hidden" id="queueAction" name="action" value="" />

<table>
  <tr class="trSubHead">
    <td colspan="7"> Clienten </td>
  </tr>
  <tr class="trHead">
    <td class="al"><input type='checkbox' class='' id='vink_all1'></td>
    <td>datum</td>
    <td>clientid</td>
    <td>datavelden</td>
  </tr>


<?

  foreach ($queue as $item)
  {
    $item["x"] = "1";
    $item["stamp"] = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $item["add_date"]);
    $jsRoute[$item["id"]] = $item["route"];
    echo $tmpl->parseBlock("clientRow",$item);
  }

?>


  <tr class="trSubHead">
    <td colspan="7"> Portefeuilles </td>
  </tr>
  <tr class="trHead">
    <td class="al"><input type='checkbox' class='' id='vink_all2'></td>
    <td>datum</td>
    <td>portfolioId</td>
    <td>datavelden</td>
  </tr>


<?
$queue = $apiExtern->populateQueue("portefeuille");
foreach ($queue as $item)
{
  $item["x"] = "2";
  $item["stamp"] = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $item["add_date"]);
  $jsRoute[$item["id"]] = $item["route"];
  echo $tmpl->parseBlock("portefeuilleRow",$item);
}


?>

  <tr>
    <td colspan="6"><hr/>Aangevinkte items
      <button class="btn-new btn-default" id='submitClients'><i class="fa fa-floppy-o" aria-hidden="true"></i> verwerken</button>&nbsp;
      <button class="btn-new btn-default btn-delete" id='deleteClients'><i class="fa fa-times" aria-hidden="true"></i> verwijderen</button>
    </td>
  </tr>
  </table>
  </form>

</div>

<div style="clear: both"></div>

<div id="showContentScr"></div>

<input type="hidden" value="0" id="retourId" name="retourId" />

<script>

  $(document).ready(function ()
  {
    const showMsg = <?=(count($result)>0 OR $errormsg != "")?"true":"false"?>;
    var extra = 0;

    if (showMsg)
    {
      $("#previewMsg").show(200);
    }

    $("#submitClients").click(function (e)
    {
      e.preventDefault();
      $("#queueAction").val("addItems");
      $("#queueForm").submit();
    });


    $("#deleteClients").click(function (e)
    {
      e.preventDefault();
      $("#queueAction").val("deleteItems");
      $("#queueForm").submit();
    })

    $('#vink_all1').change(function()
    {
      $(".vink1").prop('checked', $(this).is(':checked'));
    });

    $('#vink_all2').change(function()
    {
      $(".vink2").prop('checked', $(this).is(':checked'));
    });

  });

</script>


<?

echo template($__appvar["templateRefreshFooter"],$content);


?>
