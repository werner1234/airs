<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/06/08 08:41:04 $
 		File Versie					: $Revision: 1.49 $

 		$Log: transaktieImport.php,v $

*/


echo "no longer inuse!";

/*
include_once("wwwvars.php");
include_once ("../classes/AE_cls_fileUpload.php");


session_start();
$upl = new AE_cls_fileUpload();

$_SESSION["NAV"] = "";

global $USR;

//if ($_GET["delTemp"] == 1)
//{
//  $DB = new DB();
//  $query = "DELETE FROM TijdelijkeRekeningmutaties WHERE add_user = '$USR' ";
//	$DB->executeQuery($query);
//}


  if($_POST['posted'])
  {
    // check filetype
    if ($_FILES['importfile']["type"] != "text/comma-separated-values" &&
      $_FILES['importfile']["type"] != "text/x-csv" &&
      $_FILES['importfile']["type"] != "text/csv" &&
      $_FILES['importfile']["type"] != "text/xml" &&
      $_FILES['importfile']["type"] != "application/octet-stream" &&
      $_FILES['importfile']["type"] != "application/vnd.ms-excel" &&
      $_FILES['importfile']["type"] != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" &&
      $_FILES['importfile']["type"] != "text/plain")
    {
      $_error = "FOUT: verkeerd bestandstype(" . $_FILES['importfile'][type] . "), alleen .csv bestanden zijn toegestaan.";
    }
    // check error
    if ($_FILES['importfile']["error"] != 0)
    {
      $_error = "Fout: bestand niet ingevuld of bestaat niet (" . $_FILES['importfile']['name'] . ")";
    }

    //debug($_FILES);


    if (empty($_error))
    {
      $importcode = date("YmdHis") . $USR;  //datum als JJJJMMDDUUMM
      $importfile = $__appvar["basedir"] . "/html/importdata/depotbankcode_" . $_POST['bank'] . "_" . $importcode . "_". $_FILES['importfile']["name"];
      if (move_uploaded_file($_FILES['importfile']['tmp_name'], $importfile))
      {
        header("Location: depotbankcode/" . $_POST['bank'] . "_depotbankControle.php?file=" . urlencode($importfile) . $extra);
        exit();
      }
      // als target bestand leeg is
      $_error = "" . vt('Fout : bronbestand verkeerd geselecteerd of leeg') . " ($importfile).";
      // verwijder het lege bestand
      if (file_exists($importfile))
      {
        unlink($importfile);
      }
      else
      {
        $_error = vt("Fout : upload error.");
      }

      echo template($__appvar["templateContentHeader"], $content);
      echo $_error;
      exit;
    }
  }

echo template($__appvar["templateContentHeader"],$content);

?>
  <link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">
  <link rel="stylesheet" href="widget/css/font-awesome.min.css">


  <style>
#bestand2{
  display: none;
}
#melding{
  margin: 0;
  color:maroon;
  font-size: 1.2em;
  font-weight: bold;
}
#CSmap{
  display: none;
  font-size: 1.2em;
  
}
.filenaam{
  width: 500px;
}


</style>
<script>

function submitter()
{

	if (document.editForm.importfile.value == '')
	{
	  alert('<?=vt('Selecteer eerst een importbestand');?>');
	  return;
	}


  function checkradio(group)
  {
    var bool = false;
    if (group.length)
    { // group
      for (var b = 0; b < group.length; b++)
      {
        if (group[b].checked)
        {
          bool = true;
        }
      }
    }
    else if (group.checked)
    {
      bool = true;
    }
    return bool;
  }

  if (!checkradio(document.editForm.bank)   )
  {
    alert('<?=vt('Geef aan van welke bank de import betreft');?>');
  }
  else
    document.editForm.submit();
}
</script>

<?
  $DB = new DB();
//  if ($DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE TijdelijkeRekeningmutaties.change_user = '$USR' ") > 0)
//  {
//?>
<!--<style>-->
<!--.fout{-->
<!---->
<!--  margin: 25px;-->
<!--  background: red;-->
<!--  color: white;-->
<!--  padding: 20px;-->
<!--  width: 400px;-->
<!--  text-align: center;-->
<!--}-->
<!---->
<!---->
<!--</style>-->
<!--<div class="fout">-->
<!-- Tijdelijke rekeningmutaties gevonden voor --><?//=$USR?><!--<br/><br />-->
<!---->
<!-- <a href="--><?//=$PHP_SELF?><!--?delTemp=1"><button> verwijder tijdelijke rekeningmutaties </button></a>-->
<!--</div>-->
<?//
//	exit;
//}

?>
<style>
  .inp{
    line-height: 24px;
    padding: 4px;
    font-weight: bold;
  }
  #bestand2Oms{
    display: none;
    float: left;
    width:120px;
    background:  #E9E9E9;
    
    
  }
  #bestand2Input{
    display: none;
    float: left;
    width:500px;
  }
  #bestand1Oms{
    float: left;
    width:120px;
    background: #E9E9E9;
  }
  #bestand1Input{
    float: left;
    width:500px;
  }
  .csSelect{
    font-size: 1.1em;
    padding: 2px 5px 2px 5px;
  }
  #dialogMdlPortVrg{
    visibility: hidden;
    padding: 5px;;
    display: inline-block;
    height: 18px;
    width: 130px;
    background: orange;
  }
  article{
    width: 300px;
    float: left;
  }
  article :after{
    clear: both;
  }
  .mergeContainer{
    width: 95%;
  }
  .mergeHeader{
    background: rgba(20,60,90,1);
    color: white;
    margin: 0;
    padding: 5px;
  }
  .mergeContent{
    display: none;
    width: 100%;
    height: 350px;
  }
  .dividerRow{
    background: rgba(20,60,90,1);
    color: white;
    margin: 0;
    padding: 5px;
    font-weight: 600;
    text-align: center;
    margin-bottom: 5px;
  }

</style>




    <form enctype="multipart/form-data" action="depotbankcodesImport.php" method="POST"  name="editForm">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="posted" value="true" />

    <!-- Name of input element determines name in $_FILES array -->
    <br/><br/>
    <h3><?= vt('depotbankcode controle'); ?></h3><br/><br/>
    <?php
    if ($_error)
      echo "<b style=\"color:red;\">".$_error."</b>";
    ?>


      <div class="formblock">
        <div class="formlinks"><?= vt('Importbestand'); ?> </div>
        <div class="formrechts">
          <div id="bestand1Oms" class="inp"></div><div id="bestand1Input" class="inp">
            <input type="file" name="importfile" id="importfile" class="filenaam">
          </div>
        </div>
      </div>
      <br/>

        <div class="formblock">
          <br/>
          <div class="formlinks"> <?= vt('Welke bank'); ?></div>
          <div class="formrechts">
            <article>
              <input type="radio" name="bank" class="bankSelect" value="stroeve" id="stroeve" checked/><label for="stroeve"  >TGB (bankview)&nbsp;&nbsp;</label><br>
            <hr/>
              <input type="radio" name="bank" class="bankSelect" value="abn" id="abn"/><label for="abn" >ABN-AMRO&nbsp;&nbsp;</label><br>
              <input type="radio" name="bank" class="bankSelect" value="binck" id="binck"/><label for="binck" >Binck&nbsp;&nbsp;</label><br>
              <input type="radio" name="bank" class="bankSelect" value="degiro" id="degiro"/><label for="degiro" >DeGiro&nbsp;&nbsp;</label><br>
              
            </article>
           
            <br/>
            <br/>

          </div>
        </div>

    <div class="form">

        <div class="formblock">
          <div class="formlinks"> &nbsp;</div>
          <div class="formrechts">
            <input type="button" value="importeren" onclick="submitter();">
          </div>
        </div>
      </div>





      </form>






<script>
  
  $(document).ready(function(){
      $("#mergeToggle").click(function (e) {
        e.preventDefault();
        $(".mergeContent").toggle(300);
      });
      $("label").dblclick(function(){
      console.log("dblclick");
      console.log($("#importfile").val());
       if ($("#importfile").val() != "")
       {
         submitter();
       }
    });

    $("#UBSmap").hide();
    $("#CSmap").hide();

    $("#bestand1Oms").show().html('Bestand:');
    var banknamen = {};
    banknamen["stroeve"]    = "TGB (bankview)";
    banknamen["stroeveVT"]  = "TGB VT-contracten";
    banknamen["binckv2"]    = "Binck V2";
    banknamen["binckv3"]    = "Binck V3";
    banknamen["abn"]        = "ABN-AMRO";
    banknamen["abnv2"]      = "ABN-AMRO versie 2";
    banknamen["sns"]        = "SNS";
    banknamen["snssec"]     = "NIBC/SNS Securities";
    banknamen["atn"]        = "ATN";
    banknamen["rabo"]       = "Rabobank";
    banknamen["raboswift"]  = "Rabobank Swift";
    banknamen["abnbec"]     = "ABN-AMRO Belgie&nbsp;geldmutaties";
    banknamen["abnbes"]     = "ABN-AMRO Belgie&nbsp;transacties";
    banknamen["bpere"]      = "Rothschild";
    banknamen["credswiss"]  = "Credit Swiss";
    banknamen["airs"]       = "AIRS";
    banknamen["mabeltrans"] = "Mabeltrans (ISIN)";
    banknamen["kasbank"]    = "Kasbank";
    banknamen["kasbankv2"]  = "Kasbank V2 EFF";
    banknamen["lanschot"]   = "F. van Lanschot";
    banknamen["degiro"]     = "DeGiro";
    banknamen["degirov2"]   = "DeGiro V2";
    banknamen["pictet"]     = "Pictet";
    banknamen["lombard"]    = "Lombard";
    banknamen["ing"]        = "ING bank";
    banknamen["kbc"]        = "KBC bank";
    banknamen["ib"]         = "Interactive Brokers";
    banknamen["bil"]        = "Banque Internationale à Luxembourg";
    banknamen["mdlPort"]    = "Model portefeuille";
    banknamen["ubs"]        = "UBS";
    banknamen["ubp"]        = "UBP";
    banknamen["jb"]         = "Jul Bear CH";
    banknamen["jblux"]         = "Jul Bear LUX";
    banknamen["lynx"]       = "Lynx";
    banknamen["modulez"]    = "Module Z";
    banknamen["optimix"]    = "Optimix";
    banknamen["rabo"]       = "Rabobank";
    banknamen["btc"]       = "Bank ten Cate";

    $(".bankSelect").click(function()
    {
      var value = $(this).val();
      $('#melding').text("Selecteer een bank");
      $("#importfile").show();
      switch (value)
      {
<?
  if (!$ABNsingle)
  {
?>
        case "abn":
          
          $('#melding').html('ABN NL: selecteer beide bestanden voor de import<br/> ');
          $("#bestand2Input").show().html('<input type="file" name="importfile2" class="filenaam">');
          $("#bestand2Oms").show().html('MT940 bestand:');
          $("#bestand1Oms").show().html('MT5XX bestand:');
          break;        
<?
  }
  if (!$SNSsingle)
  {  
?>
        case "snssec":
          
          $('#melding').html('SNS/NIBC: selecteer beide bestanden voor de import<br/>');
          $("#bestand2Input").show().html('<input type="file" name="importfile2" class="filenaam">');
          $("#bestand2Oms").show().html('CTRA bestand:');
          $("#bestand1Oms").show().html('STRA bestand:');

          break;
<?
  }
?>
        case "degiro":
          
          $('#melding').html('DeGiro: selecteer beide bestanden voor de import<br/><br/>');
          $("#bestand2Input").show().html('<input type="file" name="importfile2" class="filenaam">');
          $("#bestand2Oms").show().html('Geld bestand: ');
          $("#bestand1Oms").show().html('Stukken bestand:');
          break;

        case "abnbes":
          
          $('#melding').html('AAB BE: selecteer ook het bijbehorende geldbestand<br/><br/>');
          $("#bestand2Input").show().html('<input type="file" name="importfile2" class="filenaam">');
          $("#bestand2Oms").show().html('Geld bestand: ');
          $("#bestand1Oms").show().html('Stukken bestand:');
          break;
        case "credswiss":
          $("#UBSmap").hide();
          $("#importfile").hide();
          $("#CSmap").show();
          $("#bestand1Oms").show().html('Client map:');
          $('#melding').html('Credit Swiss: map <?=$__credswissImportMap?> ');
          $("#bestand2Oms").hide();
          $("#bestand2Input").hide();
          break;
        case "ubs":
          $("#CSmap").hide();
          $("#importfile").hide();
          $("#UBSmap").show();
          $("#bestand1Oms").show().html('Client map:');
          $('#melding').html('UBS: map <?=$__ubsImportMap?> ');
          $("#bestand2Oms").hide();
          $("#bestand2Input").hide();
          break;
        default:
          $("#CSmap").hide();
          $("#UBSmap").hide();
          $('#melding').html('bank: '+banknamen[value]+' geselecteerd');
          $("#bestand2Oms").hide();
          $("#bestand2Input").hide();
          if (value == "mdlPort")
          {
            $("#dialogMdlPortVrg").css("visibility","visible");
          }
          else
          {
            $("#dialogMdlPortVrg").css("visibility","hidden");
          }

          if (value == "abnbec")
            $("#bestand1Oms").show().html('Geld bestand:');
          else
            $("#bestand1Oms").show().html('Bestand:');
      }
    });
  });
  
</script>
<?

echo template($__appvar["templateRefreshFooter"],$content);
*/