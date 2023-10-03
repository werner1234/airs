<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/09/23 07:44:01 $
 		File Versie					: $Revision: 1.7 $

 		$Log: email_queue.php,v $
 		naar RVV 20210120
*/


include("wwwvars.php");
include_once("../classes/AE_cls_digidoc.php");
include_once("../classes/AE_cls_Email.php");
ini_set('default_charset', 'utf-8');

if ( ! isset ($__appvar['office365']) ) {
  $mail = new AE_cls_Email();
} else {
  $mail = new AE_cls_ExchangeOnline();
}

if ($_POST)
{
//  debug($_POST);
//exit;
  $o = array();
  switch ($_POST["mailAction"])
  {
    case "delete":
      foreach ($_POST as $k=>$v)
      {
        if (substr($k,0,5) == "vink_")
        {
          $o[] = substr($k,5);
        }
      }
      $mail->deleteFromQueue($o);
      break;
    case "submit":
      foreach ($_POST as $k=>$v)
      {
        if (substr($k,0,5) == "vink_")
        {
          $id = substr($k,5);

          if ((int)$_POST["id_".$id] > 0)
          {
            if ($mail->storeInDigidoc($id,  (int)$_POST["id_".$id], $_POST["categorie_".$id]))
            {
               $o[] = $id;
            }
          }
        }
        $mail->deleteFromQueue($o);
      }
      break;
    default:
  }
 

}


$db = new DB();
$query = "SELECT omschrijving as val,omschrijving FROM (CRM_selectievelden) WHERE module = 'docCategrien' ";
$db->executeQuery($query);
$options = "\n<option value='email'>E-mail</option>";
while ($rec = $db->nextRecord())
{
  if ($rec["val"] == "email")
  {
    continue;
  }

  $options .= "\n<option value='{$rec["val"]}'>{$rec["omschrijving"]}</option>";

}


$mailRow = "
<tr class='msgRow {partner}' id='row_{id}'>
  <td><input type='checkbox' class='vink' name='vink_{id}' id='vink_{id}'></td>
  <td>{stamp}</td>
  <td>{subject}</td>
  <td>{from}</td>
  <td ><input class='inp' type='text' id='naam_{id}' name='naam_{id}' value='{koppeling}' READONLY/>
       <input type='hidden' id='id_{id}' name='id_{id}' value='{CRM_id}' /> </td>
       <input type='hidden' id='extra_{id}' name='extra_{id}' value='' /> </td>
       <input type='hidden' id='categorie_{id}' name='categorie_{id}' value='email' /> </td>
  <td >{btnRoute} {btnPreview} {btnDownload}</td>
</tr>
";

$tmpl = new AE_template();
$fmt = new AE_cls_formatter();


$mail->initTables();  // tabellen aanmaken voor module
//$mail->buildRouterTable();

$tmpl->loadTemplateFromString($mailRow,"mailRow");

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
    width: 1100px;
  }

  .orange{
    background: orange;
  }
  .inp{
    background: transparent;
    border: 0;

    font-size: .9em;
  }
  #dialog-message{
    


  }
  .legenda{
    width: 500px;
    padding:8px;
    border-radius: 4px;
  }
  .legenda span{
    margin: 5px;
    display: inline-block;
    width: 95%;
    padding:8px;
    border-radius: 4px;
  }
#extraKoppelingen{
  display: none;
}
</style>

<div id="dialog-message" title="Selecteer de juiste relatie">

  <div style="margin-left: 23px;">
    <p id="koppelScr">
      <select id="koppelId"></select>
      <input class="koppelAnders"/>
    </p>
  </div>
  <div style="margin-left: 23px;">
    <hr/>
    Document categorie: <select id="ddCat"><?=$options?></select>
  </div>
</div>

<div id="dialog-preview" title="E-mail bekijken">

  <div style="margin-left: 23px;">
  <p id="previewScr"></p>
  </div>
</div>


<button class="btn-new btn-default"><a href="dd_inlees_email.php"><i class="fa fa-angle-double-left" aria-hidden="true"></i> terug </a></button><br/><br/>
<?
if ($mail->errorState())
{
  echo "<h2>Fout:</h2>";
  echo $mail->lastStatus();
  exit;
}

echo $mail->lastStatus();

$queue = $mail->populateQueue();

?>


<div class="mailbox">
  <form method="post" id="mailForm">




    <input type="hidden" id="mailAction" name="mailAction" value="" />
<table>
  <tr class="trHead">
    <td class="al"><input type='checkbox' class='' id='vink_all'></td>
    <td>datum</td>
    <td>onderwerp</td>
    <td>afzender/ontvanger</td>
    <td>koppeling</td>
    <td>wijzig</td>
  </tr>

  <tr class="trSubHead">
    <td colspan="6"> Enkelvoudige vermelding </td>
  </tr>
<?
  $jsRoute = array();
  foreach($queue["matchSingle"] as $msg)
  {

     $msg["stamp"] = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $msg["stamp"]);
     $msg["CRM_naam"] = str_replace("'", "`",$msg["CRM_naam"]); // calll 6791 ' in naam geeft weergave probleem
     //$msg["id"] = $msg["index"];
     $jsRoute[$msg["id"]] = $msg["route"];
     $msg["koppeling"] = $msg["CRM_naam"];
     $msg["btnRoute"]   = "<button class='btnRoute' id='btn_".$msg["id"]."' title='koppel mail aan client'><i class='fa fa-check-square' aria-hidden='true'></i></button>";
     $msg["btnPreview"] = "<button class='btnPreview' id='btnP_".$msg["id"]."' title='bekijk het E-mailbericht'><i class=\"fa fa-envelope-open-o\" aria-hidden=\"true\"></i></button>";
     $msg["btnDownload"] = "<button class='btnDownload' id='btnD_".$msg["id"]."' title='download het E-mailbericht'><i class=\"fa fa-download\" aria-hidden=\"true\"></i></button>";
     $msg["partner"] = ($msg["group"] == "partMatch")?"orange":"";
     echo $tmpl->parseBlock("mailRow",$msg);
  }
 echo "<tr><td colspan='15'><hr/></td></tr> 

  <tr class='trSubHead'>
    <td colspan='6\'> Meervoudige vermelding </td>
  </tr>
  ";

 foreach($queue["matchMulti"] as $msg)
  {
     $msg["stamp"] = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $msg["stamp"]);
     $msg["CRM_naam"] = str_replace("'", "`",$msg["CRM_naam"]);  // calll 6791 ' in naam geeft weergave probleem

     $jsRoute[$msg["id"]] = $msg["route"];
     $msg["btnRoute"] = "<button class='btnRoute' id='btn_".$msg["id"]."' title='koppel mail aan client'><i class=\"fa fa-question-circle-o\" aria-hidden=\"true\"></i></button>";
     $msg["btnPreview"] = "<button class='btnPreview' id='btnP_".$msg["id"]."' title='bekijk het E-mailbericht'><i class=\"fa fa-envelope-open-o\" aria-hidden=\"true\"></i></button>";
     $msg["btnDownload"] = "<button class='btnDownload' id='btnD_".$msg["id"]."' title='download het E-mailbericht'><i class=\"fa fa-download\" aria-hidden=\"true\"></i></button>";
     $msg["koppeling"] = $msg["CRM_naam"];
     echo $tmpl->parseBlock("mailRow",$msg);
  }
 echo "<tr><td colspan='15'><hr/></td></tr>
 
  <tr class='trSubHead'>
    <td colspan='6\'> Geen vermeldingen </td>
  </tr>";
foreach($queue["noMatch"] as $msg)
{
  $msg["stamp"] = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $msg["stamp"]);

  $jsRoute[$msg["id"]] = $msg["route"];
  $msg["btnRoute"]   = "<button class='btnRoute' id='btn_".$msg["id"]."' title='koppel mail aan client'><i class=\"fa fa-question-circle-o\" aria-hidden=\"true\"></i></button>";
  $msg["btnPreview"] = "<button class='btnPreview' id='btnP_".$msg["id"]."' title='bekijk het E-mailbericht'><i class=\"fa fa-envelope-open-o\" aria-hidden=\"true\"></i></button>";
  $msg["btnDownload"] = "<button class='btnDownload' id='btnD_".$msg["id"]."' title='download het E-mailbericht'><i class=\"fa fa-download\" aria-hidden=\"true\"></i></button>";
  echo $tmpl->parseBlock("mailRow",$msg);
}
?>
<tr><td colspan="6"><hr/>Aangevinkte mails <button class="btn-new btn-default" id='submitMails'><i class="fa fa-floppy-o" aria-hidden="true"></i> opslaan</button>&nbsp;<button class="btn-new btn-default btn-delete" id='deleteMails'><i class="fa fa-times" aria-hidden="true"></i> verwijderen</button></td></tr>
</table>
</div>

<div style="clear: both"></div>
<fieldset class="legenda">
  <legend> legenda </legend>
  <span style="background: orange; color:#333">koppeling via partner E-mail beschikbaar</span>

</fieldset>
<input type="hidden" value="0" id="retourId" name="retourId" />

<script>

  $(document).ready(function ()
  {
    var extra = 0;
    $("#btnKoppel").click(function ()
    {
      $("#extraKoppelingen").show(300);
    });

    $("#submitMails").click(function (e)
    {
      e.preventDefault();
      $("#mailAction").val("submit");
      $("#mailForm").submit();
    })
    $("#deleteMails").click(function (e)
    {
      e.preventDefault();
      $("#mailAction").val("delete");
      $("#mailForm").submit();
    })

    $('#vink_all').change(function()
    {
      var c = $(this).is(':checked');
      console.log("in vink_all " + c);

      if($(this).is(':checked'))
      {
        $(".vink").prop('checked', true);
      } else
      {
        $(".vink").prop('checked', false);
      }
    });

    var btnId;
    var dialog = $("#dialog-message").dialog({
      modal: false,
      draggable: false,
      resizable: true,
      position: ['center', 'center'],
      show: 'blind',

      width: 800,
      buttons: {
        "ok": function() {
//          var koppeling1 = $("#extra_1").val();
//          var koppeling2 = $("#extra_2").val();
//          var koppeling3 = $("#extra_3").val();
//          var kopStr = koppeling1 + "|" + koppeling2 + "|" + koppeling3;
//          console.log(kopStr);
          $(this).dialog("close");
          console.log(" >>>> " + $("#koppelAnders").val());
          if ($("#koppelAnders").val() != "")
          {

          }
          else
          {
            $("#retourId").val($("#koppelId").val());

          }
          $("#vink_"+btnId).attr('checked', true);
          $("#btn_"+btnId).html("<i class='fa fa-check-square' aria-hidden='true'></i>");
          if ($("#retourId").val() > 0 )
          {

            $.ajax(
              {
                type: "POST",
                url: "lookups/dd_getCRM_nam.php",
                data: { id: $("#retourId").val() }
              }).done(function( naam )
            {
              console.log(naam);
              $( "#naam_"+btnId ).val(naam);
              $( "#id_"+btnId).val($("#retourId").val());
              $( "#categorie_"+btnId).val($("#ddCat").val());
            });
          }


        }
      }
    });
    dialog.dialog( "close" );

    var dialogPreview = $( "#dialog-preview" ).dialog({
      width: 800,
      modal: true,
      draggable: true,
      resizable: true,
      position: ['center', 'center'],
      show: 'blind',
      buttons: {
        "ok": function() {
          $(this).dialog("close");
        }
      }
    });
    dialogPreview.dialog( "close" );

    //$(".koppelAnders").autocomplete(autoCompleteVars);
    $(".btnRoute").click(function(e)
    {
      console.log("in click");
      e.preventDefault();
      btnId = $(this).attr("id").substr(4);


      $.ajax(
        {
          type: "POST",
          url: "lookups/dd_mail_dialog.php",
          data: { id: btnId }
        }).done(function( msg )
      {
//        console.log(msg);
        $( "#koppelScr" ).html(msg);
      });

      console.log("this="+ btnId);
      var n = "route_"+btnId;
      $("#ddCat").val($("#categorie_"+btnId).val());
      dialog.dialog( "open" );

      //alert(id);
    });

    $(".btnDownload").click(function(e)
    {
      e.preventDefault();
      btnId = $(this).attr("id").substr(5);
      window.open("lookups/dd_preview.php?download="+btnId);

    });

    $(".btnPreview").click(function(e)
    {
      console.log("in click");
      e.preventDefault();
      btnId = $(this).attr("id").substr(5);


      $.ajax(
        {
          type: "POST",
          url: "lookups/dd_preview.php",
          data: { id: btnId }
        }).done(function( msg )
      {
//        console.log(msg);
        $( "#previewScr" ).html(msg);
      });

      console.log("this="+ btnId);

      dialogPreview.dialog( "open" );

      //alert(id);
    });

  });

</script>
</form>

<?

echo template($__appvar["templateRefreshFooter"],$content);


?>
