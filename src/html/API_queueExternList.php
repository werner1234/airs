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
include_once("../classes/AE_cls_APIextern.php");
$apiExtern = new AE_cls_APIextern();

if ($_POST)
{
//  debug($_POST);
  $pushCSV = false;
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
      $apiExtern->setIgnored($o);
      $pushCSV = true;
      break;
    case "export":
      foreach ($_POST as $k=>$v)
      {
        if (substr($k,0,5) == "vink_")
        {
          $o[] = substr($k,5);
        }
      }
      $apiExtern->exportToCSV($o);
      break;
    case "submit":
      foreach ($_POST as $k=>$v)
      {
        if (substr($k,0,5) == "vink_")
        {
          $id = substr($k,5);

          if ((int)$_POST["id_".$id] > 0)
          {
            if ($apiExtern->addToCRM($id, (int)$_POST["crmId_".$id]))
            {
               $o[] = $id;
            }
          }
        }
        $apiExtern->setFinished($o);
      }
      break;
    default:
  }
 

}



$mailRow = "
<tr class='msgRow {partner}' id='row_{id}'>
  <td>
    <input type='checkbox' class='vink{x}' name='vink_{id}' id='vink_{id}'>
    <input type='hidden' id='id_{id}' name='id_{id}' value='{id}' /> 
    <input type='hidden' id='crmId_{id}' name='crmId_{id}' value='{crmId}' />
    <button class='showContent' id='content_{id}'><i class='fa fa-envelope-open-o' aria-hidden='true'></i></button>
  </td>
  <td>{add_date}</td>
  <td>{eventCode}</td>
  <td>{zoekveld}</td>
  <td class='{classEmail}'>{email}</td>
  <td class='ac'>{match}</td>
  <td class='ac'>{crmId}</td>
  
</tr>
";

$tmpl = new AE_template();
$fmt = new AE_cls_formatter();


//$apiExtern->initTables();  // tabellen aanmaken voor module
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

  .moreThanOne{
    color: red;
  }
  .inp{
    background: transparent;
    border: 0;

    font-size: .9em;
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
  .ac{
    text-align: center;
  }
</style>

<div id="dialog-preview" title="Data bekijken">

  <div style="margin-left: 23px;">
  <p id="previewScr"></p>
  </div>
</div>



<?
if ($apiExtern->errorState())
{
  echo "<h2>Fout:</h2>";
  echo $apiExtern->lastStatus();
  exit;
}

echo $apiExtern->lastStatus();

$queue = $apiExtern->populateQueue();
//debug($queue);
?>


<div class="mailbox">
  <form method="post" id="mailForm">




    <input type="hidden" id="mailAction" name="mailAction" value="" />

<table>
  <tr class="trSubHead">
    <td colspan="7"> met E-mail match </td>
  </tr>
  <tr class="trHead">
    <td class="al"><input type='checkbox' class='' id='vink_all1'></td>
    <td>datum</td>
    <td>event</td>
    <td>zoekveld</td>
    <td>E-mail</td>
    <td>match</td>
    <td>crmId</td>
  </tr>


<?
  $jsRoute = array();
  $mailDouble = $apiExtern->mailDoubles;
  foreach($queue["match"] as $msg)
  {
     $msg["x"] = 1;
     $msg["add_date"] = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $msg["add_date"]);
     //$msg["id"] = $msg["index"];
    if ($msg["email"] != "" AND $mailDouble[$msg["email"]] > 1)
    {
      $msg["email"] .= " (".$mailDouble[$msg["email"]].")";
      $msg["classEmail"] = "moreThanOne";
    }
     $jsRoute[$msg["id"]] = $msg["route"];
     $msg["koppeling"] = $msg["CRM_naam"];
     $msg["btnRoute"]   = "<button class='btnRoute' id='btn_".$msg["id"]."' title='koppel mail aan client'><i class='fa fa-check-square' aria-hidden='true'></i></button>";
     $msg["btnPreview"] = "<button class='btnPreview' id='btnP_".$msg["id"]."' title='bekijk het E-mailbericht'><i class=\"fa fa-envelope-open-o\" aria-hidden=\"true\"></i></button>";
     $msg["btnDownload"] = "<button class='btnDownload' id='btnD_".$msg["id"]."' title='download het E-mailbericht'><i class=\"fa fa-download\" aria-hidden=\"true\"></i></button>";

     echo $tmpl->parseBlock("mailRow",$msg);
  }
 echo "<tr><td colspan='15'><hr/></td></tr> 

  <tr class='trSubHead'>
    <td colspan='7'> zonder match </td>
  </tr>
  <tr class=\"trHead\">
    <td class=\"al\"><input type='checkbox' class='' id='vink_all2'></td>
    <td>datum</td>
    <td>event</td>
    <td>zoekveld</td>
    <td>E-mail</td>
    <td>match</td>
    <td>crmId</td>
  </tr>
  ";

 foreach($queue["nomatch"] as $msg)
  {
    $msg["x"] = 2;
     $msg["stamp"] = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $msg["add_date"]);
     $msg["CRM_naam"] = str_replace("'", "`",$msg["CRM_naam"]);  // calll 6791 ' in naam geeft weergave probleem
    if ($msg["email"] != "" AND $mailDouble[$msg["email"]] > 1)
    {
      $msg["email"] .= " (".$mailDouble[$msg["email"]].")";
      $msg["classEmail"] = "moreThanOne";
    }
     $jsRoute[$msg["id"]] = $msg["route"];
     $msg["btnRoute"] = "<button class='btnRoute' id='btn_".$msg["id"]."' title='koppel mail aan client'><i class=\"fa fa-question-circle-o\" aria-hidden=\"true\"></i></button>";
     $msg["btnPreview"] = "<button class='btnPreview' id='btnP_".$msg["id"]."' title='bekijk het E-mailbericht'><i class=\"fa fa-envelope-open-o\" aria-hidden=\"true\"></i></button>";
     $msg["btnDownload"] = "<button class='btnDownload' id='btnD_".$msg["id"]."' title='download het E-mailbericht'><i class=\"fa fa-download\" aria-hidden=\"true\"></i></button>";
     $msg["koppeling"] = $msg["CRM_naam"];

     echo $tmpl->parseBlock("mailRow",$msg);
  }

 
?>
<tr>
  <td colspan="6"><hr/>Aangevinkte items
    <button class="btn-new btn-default" id='submitMails'><i class="fa fa-floppy-o" aria-hidden="true"></i> toevoegen</button>&nbsp;
    <button class="btn-new btn-default btn-delete" id='deleteMails'><i class="fa fa-times" aria-hidden="true"></i> negeren</button>
    <button class="btn-new btn-default btn-save" id='exportMails'><i class="fa fa-arrow-down" aria-hidden="true"></i> exporteer selectie naar CSV *</button>
  </td>
</tr>
</table>
  </form>
    <sup>*</sup> CSV bestand heeft `tab` als delimiter
  <br/>
  <br/>
  <br/>

    <form action="API_queueExternHistorie.php" id="historieForm" method="post">
    <button class="btn-new btn-default btn-save" id='exportRapport'><i class="fa fa-arrow-down" aria-hidden="true"></i> exporteer historie naar CSV *</button>
      vanaf welke datum: <input style="width: 90px;" name="datum" class="AIRSdatepicker" value="<?=date("d-m-Y")?>">

    </form>
</div>

<div style="clear: both"></div>

<div id="showContentScr"></div>

<input type="hidden" value="0" id="retourId" name="retourId" />

<script>

  $(document).ready(function ()
  {
    var extra = 0;

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


    $(".showContent").click(function (e)
    {
      e.preventDefault();
      var myId = $(this).attr("id").substring(8);
      $.ajax(
        {
          type: "GET",
          url: "API_queueContent.php",
          data: {id: myId}
        }).done(function( msg )
      {
//        console.log(msg);
        $( "#previewScr" ).html(msg);
        dialogPreview.dialog( "open" );
      });


    });



    $("#exportRapport").click(function (e)
    {
      console.log("historie click");
      e.preventDefault();
      $("#historieForm").submit();
    });

    $("#submitMails").click(function (e)
    {
      e.preventDefault();
      $("#mailAction").val("submit");
      $("#mailForm").submit();
    });


    $("#deleteMails").click(function (e)
    {
      e.preventDefault();
      $("#mailAction").val("delete");
      $("#mailForm").submit();
    })
    $("#exportMails").click(function (e)
    {
      e.preventDefault();
      $("#mailAction").val("export");
      $("#mailForm").submit();
    })
    $('#vink_all1').change(function()
    {
      var c = $(this).is(':checked');
      console.log("in vink_all1 " + c);

      if($(this).is(':checked'))
      {
        $(".vink1").prop('checked', true);
      } else
      {
        $(".vink1").prop('checked', false);
      }
    });
    $('#vink_all2').change(function()
    {
      var c = $(this).is(':checked');
      console.log("in vink_all2 " + c);

      if($(this).is(':checked'))
      {
        $(".vink2").prop('checked', true);
      } else
      {
        $(".vink2").prop('checked', false);
      }
    });





  });

</script>


<?

echo template($__appvar["templateRefreshFooter"],$content);


?>
