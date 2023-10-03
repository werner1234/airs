<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/06/29 13:59:17 $
 		File Versie					: $Revision: 1.50 $


*/
include_once("wwwvars.php");
include_once ("../classes/AE_cls_fileUpload.php");


$__appvar["templateContentHeader"] = "../".$__appvar["templateContentHeader"];
$__appvar["templateRefreshFooter"] = "../".$__appvar["templateRefreshFooter"];

$content = array(
  "style" => '
<link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">
<script type="text/javascript" src="../javascript/jquery-min.js"></script>
<script type="text/javascript" src="../javascript/jquery-ui-min.js"></script>

<link rel="../stylesheet" href="style/fontAwesome/font-awesome.min.css">
  '
);

session_start();
$upl = new AE_cls_fileUpload();

$_SESSION["NAV"] = "";
if ($_GET["flip"] )
{
  if ($_GET["flip"] == 1)
  {
    $ABNsingle = true;
    $_SESSION["flip"] = 1;
  }
  
  else
  {
    $ABNsingle = false;
    $_SESSION["flip"] = 2;
  }
}
elseif ($_SESSION["flip"])  // onthouden van flip voor huidige sessie
{
  if ($_SESSION["flip"] == 1)
  {
    $ABNsingle = true;
  }
  
  else
  {
    $ABNsingle = false;
  }
}
else
{
  unset($_SESSION["flip"]);
  $ABNsingle = $__TransactieImport["ABN_enkelbestand"];
}

session_write_close();
//$content = array();
global $USR;




if ($_GET["delTemp"] == 1)
{
  $DB = new DB();
  $query = "DELETE FROM TijdelijkeRekeningmutaties WHERE add_user = '$USR' ";
	$DB->executeQuery($query);
}

// if poster
if($_POST['posted'])
{
	unset($manualBoekdatum);
  if(!empty($afshriftDatum))
	{
		$dd = explode($__appvar["date_seperator"],$afshriftDatum);
		if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
		{
			$_error = "Fout: ongeldige afschriftdatum opgegeven";
		}
		else
		{
		  $manualBoekdatum = $dd[2]."-".$dd[1]."-".$dd[0];
		}
	}

  if ($_POST['bank'] == "credswiss")
  {
    header("Location: import/".$_POST['bank']."_import.php?manualBoekdatum=".$manualBoekdatum."&CSmap=".$_POST["CSmap"]);
    exit;
  }  
	if ( $_POST['bank'] == "ubs")
  {
    header("Location: import/".$_POST['bank']."_import.php?manualBoekdatum=".$manualBoekdatum."&UBSmap=".$_POST["UBSmap"]);
    exit;
  }
	// check filetype
  if($_FILES['importfile']["type"] != "text/comma-separated-values" &&
	   $_FILES['importfile']["type"] != "text/x-csv" &&
	   $_FILES['importfile']["type"] != "text/csv" &&
	   $_FILES['importfile']["type"] != "text/xml" &&
	   $_FILES['importfile']["type"] != "application/octet-stream" &&
	   $_FILES['importfile']["type"] != "application/vnd.ms-excel" &&
	   $_FILES['importfile']["type"] != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" &&
	   $_FILES['importfile']["type"] != "text/plain")
	{
		$_error = "FOUT: verkeerd bestandstype(".$_FILES['importfile'][type]."), alleen .csv bestanden zijn toegestaan.";
	}
	// check error
	if($_FILES['importfile']["error"] != 0)
	{
		$_error = "Fout: bestand niet ingevuld of bestaat niet (".$_FILES['importfile']['name'].")";
	}

  if ($_POST["bank"] == "abnbes")
  {
  	// check filetype
	  if($_FILES['importfile2']["type"] != "text/comma-separated-values" &&
	     $_FILES['importfile2']["type"] != "text/x-csv" &&
	     $_FILES['importfile2']["type"] != "text/csv" &&
	     $_FILES['importfile2']["type"] != "application/octet-stream" &&
	     $_FILES['importfile2']["type"] != "application/vnd.ms-excel" &&
	     $_FILES['importfile2']["type"] != "text/plain")
	  {
		  $_error = "FOUT: verkeerd bestandstype(".$_FILES['importfile2']["type"]."), alleen .csv bestanden zijn toegestaan.";
  	}
	  // check error

	  if($_FILES['importfile2']["error"] != 0)
	  {
		  $_error = "Fout: bestand niet ingevuld of bestaat niet (".$_FILES['importfile2']['name'].")";
	  }

  }
  
  if ($_POST["bank"] == "degiro" )
  {
  	// check filetype
	  if($_FILES['importfile2']["type"] != "text/comma-separated-values" &&
	     $_FILES['importfile2']["type"] != "text/x-csv" &&
	     $_FILES['importfile2']["type"] != "text/csv" &&
	     $_FILES['importfile2']["type"] != "application/octet-stream" &&
	     $_FILES['importfile2']["type"] != "application/vnd.ms-excel" &&
	     $_FILES['importfile2']["type"] != "text/plain")
	  {
		  $_error = "FOUT: verkeerd bestandstype(".$_FILES['importfile2']["type"]."), alleen .csv bestanden zijn toegestaan.";
  	}
	  // check error

	  if($_FILES['importfile2']["error"] != 0)
	  {
		  $_error = "Fout: bestand niet ingevuld of bestaat niet (".$_FILES['importfile2']['name'].")";
	  }

  }


	if (empty($_error))
	{
    
		$importcode = date("YmdHis").$USR;  //datum als JJJJMMDDUUMM
		$importfile = $__appvar["basedir"]."/html/importdata/transaktie_".$_POST['bank']."_".$importcode.".csv";
    $importfile2 = $__appvar["basedir"]."/html/importdata/transaktie_".$_POST['bank']."_".$importcode."_2.csv";
    move_uploaded_file($_FILES['importfile2']['tmp_name'],$importfile2);
		if(move_uploaded_file($_FILES['importfile']['tmp_name'],$importfile))
		{
      $extra = "";
			if (count(file($importfile)) > 0 )
			{
			  if ($_POST["bank"]  == "abnbec")
        {
          $_POST["bank"] = "abnbe";
          $extra = "&type=c";
        }
        if ($_POST["bank"]  == "abnbes")
        {
          $_POST["bank"] = "abnbe";
          $extra = "&type=s&file2=".urlencode($importfile2);
        }
        if (
            $_POST["bank"]  == "abn" OR
            $_POST["bank"]  == "degiro")

        {
          
          $extra = "&file2=".urlencode($importfile2);
          if ($_POST["bank"]  == "abn")    $extra .= "&abn1=".$_POST["ABNsingle"];
        }
			  header("Location: import/".$_POST['bank']."_import.php?file=".urlencode($importfile).$extra."&manualBoekdatum=".$manualBoekdatum."&mdlPortType=".$_POST["mdlPortType"]);
			  exit();
			}
			// als target bestand leeg is
			$_error = "Fout : bronbestand verkeerd geselecteerd of leeg ($importfile).";
			// verwijder het lege bestand
			if (file_exists($importfile) ) unlink($importfile);
		}
		else
			$_error = "Fout : upload error.";
	}
	echo template($__appvar["templateContentHeader"],$content);
	echo $_error;

	exit;
}

echo template($__appvar["templateContentHeader"],$content);
if(!$_FILES['importfile']['name'])
{

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

	if (document.editForm.importfile.value == '' && document.editForm.bank.value != 'credswiss' && document.editForm.bank.value != 'ubs')
	{
	  alert('Selecteer eerst een importbestand');
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
    alert('Geef aan van welke bank de import betreft');
  }
  else
    document.editForm.submit();
}
</script>

<?
  $DB = new DB();
  if ($DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE TijdelijkeRekeningmutaties.change_user = '$USR' ") > 0)
  {
?>
<style>
.fout{

  margin: 25px;
  background: red;
  color: white;
  padding: 20px;
  width: 400px;
  text-align: center;
}


</style>
<div class="fout">
 Tijdelijke rekeningmutaties gevonden voor <?=$USR?><br/><br />

 <a href="<?=$PHP_SELF?>?delTemp=1"><button> verwijder tijdelijke rekeningmutaties </button></a>
</div>
<?
	exit;
}



$cdir = scandir($__credswissImportMap); 

foreach ($cdir as $key => $value) 
{ 
  if (!in_array($value,array(".",".."))) 
  { 
    if ($value == "verwerkt") continue;
    
    if (is_dir($__credswissImportMap."/".$value)) 
    { 
      $options .= "\n  <option value='$value'>$value</option>"; 
    } 
  } 
} 

$cdir = scandir($__ubsImportMap);

foreach ($cdir as $key => $value)
{
  if (!in_array($value,array(".","..")))
  {
    if ($value == "verwerkt") continue;

    if (is_dir($__ubsImportMap."/".$value))
    {
      $ubsOptions .= "\n  <option value='$value'>$value</option>";
    }
  }
}




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




    <form enctype="multipart/form-data" action="../transaktieImport.php" method="POST"  name="editForm">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="posted" value="true" />
    <input type="hidden" name="ABNsingle" value="<?=$ABNsingle?>" />

    <!-- Name of input element determines name in $_FILES array -->
    <br/><br/>
    <h3>Transactie import</h3><br/><br/>
    <?php
    if ($_error)
      echo "<b style=\"color:red;\">".$_error."</b>";
    ?>


      <div class="formblock">
        <div class="formlinks">Importbestand </div>
        <div class="formrechts">
<!--          <p id="melding" >Selecteer een bank</p><br/>-->
          <div id="bestand2Oms" class="inp"></div><div id="bestand2Input" class="inp"></div><div style="clear:both"></div>
          <div id="bestand1Oms" class="inp"></div><div id="bestand1Input" class="inp"><input type="file" name="importfile" id="importfile" class="filenaam">
            <div id="CSmap">
              <select name="CSmap" class="csSelect">
                <?= $options ?>
              </select>
            </div>
            <div id="UBSmap">
              <select name="UBSmap" class="csSelect">
                <?= $ubsOptions ?>
              </select>
            </div>

          </div>
        </div>
      </div>
      <br/>

        <div class="formblock">
          <br/>
          <div class="formlinks"> Welke bank</div>
          <div class="formrechts">
            <article>
               <input type="radio" checked name="bank" class="bankSelect" value="binckv3" id="binckv3"/><label for="binckv3" >Binck V3&nbsp;&nbsp;</label><br>
               <input type="radio"  name="bank" class="bankSelect" value="saxo" id="saxo"/><label for="saxo" >Saxo&nbsp;&nbsp;</label><br>
            </article>
          </div>
        </div>

    <div class="form">
        <div class="formblock">
          <div class="formlinks">Afschriftdatum &nbsp;</div>
          <div class="formrechts">
            <input class="AIRSdatepicker" type="text" name="afshriftDatum" id="afshriftDatum" onchange="date_complete(this)" value="<?= $afshriftDatum ?>" size="15"> dd-mm-jjjj (invullen overruled de datum in het bestand)
          </div>
        </div>
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
    banknamen["atn"]        = "ATN";
    banknamen["rabo"]       = "Rabobank";
    banknamen["raboswift"]  = "Rabobank Swift";
    banknamen["abnbec"]     = "ABN-AMRO Belgie&nbsp;geldmutaties";
    banknamen["abnbes"]     = "ABN-AMRO Belgie&nbsp;transacties";
    banknamen["bpere"]      = "Rothschild";
    banknamen["credswiss"]  = "Credit Swiss";
    banknamen["airs"]       = "AIRS";
    banknamen["mabeltrans"] = "Mabeltrans (ISIN)";
    banknamen["airsTempl"]  = "Airs (template)";
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
    banknamen["gs"]         = "Goldman Sachs";
    banknamen["btc"]        = "Bank ten Cate";
    banknamen["sarasin"]    = "Sarasin";
    banknamen["cacaeis"]    = "Cacaeis";
    banknamen["_template"]  = "TEMPLATE";

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
}
echo template($__appvar["templateRefreshFooter"],$content);
?>