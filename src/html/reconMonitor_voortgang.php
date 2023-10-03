<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/09/02 07:30:11 $
    File Versie         : $Revision: 1.1 $

    $Log: reconMonitor_voortgang.php,v $
    Revision 1.1  2019/09/02 07:30:11  cvs
    call 7934


*/
include_once("wwwvars.php");
$__debug = true;

$hlp = new reconMonitor_importMatrixHelper();

$statusArray  = $hlp->statusArray;
$statusBG = $hlp->statusBG;
$statusFG = $hlp->statusFG;
$optionsText = "\n\t<option value=''>---</option>";
foreach ($statusArray as $k=>$v)
{
  $optionsText .= "\n\t<option value='$k'>$v</option>";
}

$db = new DB();
$matrix  = array();
$updates = array();
$filter=$_GET['datum'];
if ($filter == "")
{
  $filter = $hlp->lastDateDb;
}

$options = $hlp->createDateOptions($filter);

$_SESSION['NAV']='';

$mainHeader='matrix van '.$hlp->dateDbToForm($filter)." (".date("H:i:s").") <br/> <span id='countdown'>pagina ververst</span>";

$koersOnlyVB = array();
$klaargezet  = array();

$query = "SELECT UNIX_TIMESTAMP(add_date) as added, add_date, DATE(Datum) as Datum FROM Valutakoersen WHERE Valuta = 'EUR' ORDER BY Datum DESC ";
$vRec  = $db->lookupRecordByQuery($query);

$dayNow  = date("w");
$dayTest = date("w",db2jul($vRec["Datum"]));

if ( (time() - $vRec["added"]) < 600)  // koersen moeten minimaal 10 min geleden zijn geupdate
{
  $koersUpdated = false;

}
else
{
  $koersUpdated = (($dayNow < 6 AND ($dayNow - $dayTest) == 1) OR ($dayNow == 1 AND $dayTest == 5));
}

$bedrijven = $hlp->getBedrijven();



//debug($airshost->infoArray);

foreach ($bedrijven as $bedrijf)
{
  $query = "
    SELECT  
      `Bedrijf`, 
      `complete`
    FROM 
      `UpdateHistory` 
    WHERE 
      `Bedrijf` = '$bedrijf' AND 
      DATE(`add_date`) = '$filter' AND 
      `type` = 'dagelijks' ";
  if ($rec = $db->lookupRecordByQuery($query))
  {
    $updates[$rec["Bedrijf"]]["opgehaald"] = $rec["complete"];
  }

  $query = "
    SELECT 
      Bedrijf,
      DATE(laatsteDagelijkeUpdate) as klaargezet 
    FROM 
      Bedrijfsgegevens 
    WHERE
	    Bedrijf = '$bedrijf' AND date(laatsteDagelijkeUpdate) = '$filter'";

  if ($rec = $db->lookupRecordByQuery($query))
  {
    $klaargezet[$rec["Bedrijf"]] = $rec["klaargezet"];
  }
  
}

$depotbanken = $hlp->getDepotBanken();

$query = "SELECT * FROM `reconMonitor_matrix` WHERE DATE(datum) = '$filter' ORDER BY bedrijf, depotbank";
$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
  $matrix[$rec["bedrijf"]][$rec["depotbank"]] = $rec;
}

//debug($matrix);

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
<b>$mainHeader</b> $subHeader
</div><br>
<link rel='stylesheet' href='style/AIRS_default.css' type='text/css' media='screen'>

<style>
.bgGreen{
  background-color:#0A0;
  color: white;
  text-align: center;
}
.bgYellow{
  background-color:#E9AB17;
}
.bgBlank{
  background-color:#DDD;
}
.dataCell{
  width: 70px;
}
.header{
  background: rgba(20,60,90,1);
  color: white;
  text-align: center;
  padding:8px;
}
.bedrijf{
  width: 70px;
  padding-left: 20px;
  font-weight: bold;
  text-align: left;
}
td{
  padding: 2px;
  border-bottom: 1px solid #999;
  
  border-left: 1px solid #999;
}
button{
 font-size: 9px;
 padding: 5px;
}
.dataCell button{
display: inline-block;
width: 100%;
height: 100%;

margin:0;
}
thead tr {
    position: sticky;
    top: 0;
    z-index: 1;
}
</style>


<form method='GET' name='controleForm'>
  Filter :
  <select name='datum' onChange='document.controleForm.submit();'>
    $options
  </select>
  <br/>
</form>

";

echo template($__appvar["templateContentHeader"],$content);

$bedrijfTrRow = "
<thead>
  <tr>
    <td class='header bedrijf'>Bedrijf</td>
";
foreach ($depotbanken as $depot)
{
  $bedrijfTrRow .=  "<td class='header'>$depot</td>";
}
$bedrijfTrRow .= "</tr></thead>";

?>
<table cellpadding="0" cellspacing="0" id="overzicht">
<?
echo $bedrijfTrRow;
$updates = (array)$updates;
$col = count($depotbanken);
//debug($updates);


foreach($bedrijven as $bedrijf)
{

  $klr = ($klaargezet[$bedrijf] != "");
  $dataRow = "";
  $dataRow .= "<tr>\n\t<td class='bedrijf'>$bedrijf</td>";
  $vbReady = true;

  foreach($depotbanken as $depot)
  {
    $rec = $matrix[$bedrijf][$depot];


    $data = "";
    $title = "";
    $class = "";
    $style = "";
    if ($rec["bedrijf"] != "")
    {
      if ($rec["verwerkt"] == 1)
      {

        $class = "bgGreen";
        $data = $rec["door"];
        $title = substr($rec["datum"], -8);
      }
      else if ($rec["bedrijf"] != "")
      {
        if ($rec["status"] != 5)
        {
          $vbReady = false;
        }
        switch ($rec["status"])
        {
          case 1:
          case 2:
          case 3:
          case 4:
          case 5:
            $memoFilled = strlen($rec["memo"]) != 0 ? "*" : "";
            $style = "background: {$statusBG[$rec["status"]]}; color: {$statusFG[$rec["status"]]};";
            $data = $memoFilled . " {$rec["door"]}";
            $title = "\nStatus: " . $statusArray[$rec["status"]] . "\nMemo: " . $rec["memo"];
            break;
          default:

            $class = "bgYellow";
        }

      }
      $dataRow .= "\n\t<td class='dataCell $class'><button class='btnEdit' data-recid='{$rec["id"]}' data-status='{$rec["status"]}' data-memo='{$rec["memo"]}' style='$style' title='{$title}'>&nbsp;{$data}</button></td>";
    }
    else
    {
      $class = "bgBlank";
      $dataRow .= "\n\t<td class='dataCell $class' style='$style' title='{$title}'>&nbsp;</td>";
    }



  }
  if ($vbReady)
  {
    $section2 .= $dataRow;
  }
  else
  {
    $section1 .= $dataRow;
  }
}



echo $section1;
?>
  </table>
<br/>
<br/>
<br/>
  <table cellpadding="0" cellspacing="0" id="overzicht">
  <tr>
    <td colspan='100' class="header bedrijf"> Vermogensbeheerders met afgeronde reconcillatie</td>
  </tr>
  <tr> <td colspan='100' > &nbsp;</td></tr>
<?
echo $bedrijfTrRow;
echo $section2;
?>

</table>
<br/>
<br/>
<br/>

  <style>
    .AEui-dialog-title{
      background: whitesmoke;
      border-radius: 20px;
      box-shadow: #999 5px 5px 5px;
      padding: 20px;
      width: 600px;
      height: 400px;
      border:2px solid #333;
    }
    label, input { display:block; }
    input.text { margin-bottom:12px; width:95%; padding: .4em; }
    fieldset { padding:0; border:0; margin-top:25px; }
    h1 { font-size: 1.2em; margin: .6em 0; }
    div#users-contain { width: 350px; margin: 20px 0; }
    div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
    div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
    .ui-dialog .ui-state-error { padding: .3em; }
    .validateTips { border: 1px solid transparent; padding: 0.3em; }
  </style>

  <div id="dialog-form" title="Aanpassen reconjob" class="popup">
    <p class="validateTips">Muteren reconjob</p>
    <label for="popup_status">Nieuwe status</label>
    <select name="status" id="popup_status"class="text ui-widget-content ui-corner-all">
    <?=$optionsText?>
    </select> <span id="statusError" style="font-weight: 700; color: maroon"></span>
    <label for="popup_memo">Opmerkingen</label>
    <textarea name="memo" id="popup_memo"class="text ui-widget-content ui-corner-all" style="width: 300px; height: 100px"></textarea>
  </div>
<br/>
<br/>
<link rel="stylesheet" type="text/css" href="style/jquery.css">
<link rel="stylesheet" type="text/css" href="style/smoothness/jquery-ui-1.11.1.custom.css">

<script>
  $(document).ready(function () {


    var autoRefresh = true;
    var recId = 0;

    dialog = $( "#dialog-form" ).dialog({
        autoOpen: false,
        height: 400,
        width: 350,
        modal: true,
        position: { my: "center center", at: "center center" },
        buttons: {
            "opslaan": editRecord,
            "terug": function() {
                   dialog.dialog( "close" );
            }
        },
        close: function() {
            autoRefresh = true;
            console.log("autorefresh1 on "+ autoRefresh);

        }
    });

      function editRecord()
      {
        $("#statusError").html("");
        var nwStatus = $("#popup_status").val();
        if (nwStatus == "")
        {
            $("#statusError").html("nieuwe status is verplicht");

        }
        else
        {
            $.ajax({
                type: "POST",
                url: "ajax/updatereconMatrix.php",
                data: {
                    recId: recId,
                    status: nwStatus,
                    memo: $("#popup_memo").val(),
                },
                dataType:'json',
                success:function(data)
                {
                    console.log(data);
                    window.location.reload();
                },
                error:function(data){
                    console.log("error");

                },
            });
            dialog.dialog( "close" );
        }



      }

    var refreshRate = 30;
    var logoutSec = refreshRate; // 30 seconden

    setInterval(function(){
      if (autoRefresh)
      {
        $("#countDown").text(--logoutSec);
        if (logoutSec < 1)
        {
           window.location.reload();
        }
        $("#countdown").html(`pagina ververst in ${logoutSec} sec`);
      }
      else
      {
        $("#countdown").html(`pagina ververst gepauzeerd`);
      }

    },1000);

    $(".btnEdit").click(function(e){
        e.preventDefault();
        autoRefresh = false;
        $("#popup_status").val($(this).data(""));
        $("#popup_memo").val($(this).data("memo"));
        console.log("autorefresh off "+ autoRefresh);
        dialog.dialog( "open" );
        // $(this).attr("disabled", true);
        recId = $(this).data("recid");
        console.table(recId);

    });
    $(".btnVerwerk").click(function(e){
      e.preventDefault();
    });
  });
</script>

<?
echo template($__appvar["templateRefreshFooter"],$content);
