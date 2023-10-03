<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/04/06 12:41:36 $
 		File Versie					: $Revision: 1.9 $

 		$Log: hsbc_import.php,v $
 		Revision 1.9  2020/04/06 12:41:36  cvs
 		call 6991
 		
 		Revision 1.8  2020/01/14 14:06:10  cvs
 		call 8300
 		
 		Revision 1.7  2019/08/23 10:04:58  cvs
 		call 8017
 		
 		Revision 1.6  2019/03/06 14:14:55  cvs
 		call 6991
 		
 		Revision 1.5  2019/02/14 11:08:08  cvs
 		call 7243
 		
 		Revision 1.4  2019/02/11 09:22:05  cvs
 		call 6991
 		
 		Revision 1.3  2019/02/06 15:39:26  cvs
 		call 6991
 		
 		Revision 1.2  2019/01/23 13:23:30  cvs
 		call 6991
 		
 		Revision 1.1  2018/11/23 13:34:45  cvs
 		call 6991
 		

*/




include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once("hsbc_functies.php");

$skipFoutregels = array();
$meldArray = array();

include_once "../../classes/AIRS_import_afwijkingen.php";
$afw = new AIRS_import_afwijkingen("HSBC");



if ($_POST["doIt"] == "1")  // validatie mislukt, wat te doen?
{
  $file = $_POST["bestand"];
  switch ($_POST["action"])
  {
    case "stop":
      echo "<br>Het transactiebestand is verwijderd en de import is afgebroken";
    	if (file_exists($_POST["bestand"]) ) unlink($_POST["bestand"]);
		  exit();
      break;
    case "retry":
      $doIt = 0;
      $file = $_POST["bestand"];
      break;
    default: 
      $skipFoutregels = explode(",",$foutregels);
		  array_shift($skipFoutregels);  // verwijder eerste lege key
		  $file = $_POST["bestand"];
  }
}



//
// check of er records in de TijdelijkeRekeningmutaties tabel zitten
//
$DB = new DB();
$rekeningAddArray = array();


$content = array();
$content["style"] = '
  <link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">
  <script type="text/javascript" src="../javascript/jquery-1.11.1.min.js"></script>
  ';

echo template("../".$__appvar["templateContentHeader"],$content);
if ($_GET["retry"] == 1)
{
  $query = "DELETE FROM TijdelijkeRekeningmutaties WHERE add_user = '$USR' ";
	$DB->executeQuery($query);
}
else
{
  $tempRecords = $DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE  TijdelijkeRekeningmutaties.change_user = '$USR'");
  if ($tempRecords > 0)
  {
  	echo "<br>
  <br>
  De tabel TijdelijkeRekeningmutaties is niet leeg voor gebruiker ($USR) (".$tempRecords.")<br>
  <br>
  de import is geannuleerd ";
  	exit;
  }  
}


//
// setup van de progressbar
//
	$prb = new ProgressBar();	// create new ProgressBar
	$prb->pedding = 2;	// Bar Pedding
	$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
	$prb->setFrame();          	                // set ProgressBar Frame
	$prb->frame['left'] = 50;	                  // Frame position from left
	$prb->frame['top'] = 	80;	                  // Frame position from top
	$prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
	$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
	$prb->show();	                              // show the ProgressBar


$csvRegels = 1;
include("hsbc_validate.php");

$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van HSBC CSV bestand');

if ($doIt <> "1")  // validatie is al gebeurd dus skippen
{
	if (!validateCvsFile($file))
	{
		$prb->hide();
?>
  	<table cellpadding="0" cellspacing="0">
  	<tr>
    	<td colspan="2" bgcolor="#BBBBBB">
     	 Foutmelding bij validatie van CSV bestand<br>
     	 Bestandsnaam :<?=$file?>
    	</td>
  	</tr>
<?
	$foutregels = "";
	$_vsp = "";
	for ($x=0;$x < count($error);$x++)
	{
		$_spA = explode(":",$error[$x]);
		$_sp = trim($_spA[0]);
		if ( $_vsp <> $_sp )
		$foutregels .= ",".$_sp;
		$_vsp = $_sp;
?>
  	<tr>
    	<td bgcolor="#BBBBBB"><?=$x?></td>
    	<td>&nbsp;&nbsp;
	      <?=$error[$x];?>
  	  </td>
  	</tr>

<?

	}
?>
	</table>
	<br>
<?
    if (count($_SESSION["importFoutFile"]) > 0)
    {
      echo "<br/><a href='wwwFoutenBestand.php?bank=HSBC' ><button id='btnDownload'>Download FOUTEN bestand</button></a><br/>";
    }
?>
	<br>
	<b>Vervolg aktie?</b>
	<form action="<?=$PHP_SELF?>" method="POST">
    <div id="kopje"></div>
	  <input type="hidden" name="doIt" value="1">
  	<input type="hidden" name="bestand" value="<?=$file?>">
  	<input type="hidden" name="foutregels" value="<?=$foutregels?>">
  	<select name="action" id="frmAction">
    	<option value="stop">Bestand verwijderen en import afbreken</option>
    	<option value="go">Bestand inlezen en onvolledige regels overslaan</option>
      <option value="retry">Bestand opnieuw inlezen en valideren</option>
  	</select>
    
    <input type="hidden" name="addRekening" id="addRekening" value="0">
  	<button id="btnSubmit"> Uitvoeren </button>
	</form>
  
  <script>
    $(document).ready(function(){
      
      var t = $('input[type="checkbox"]:checked').length;
      
      $("#btnSubmit").click(function(){
        errorTxt = "";
        if (t > 0)
        {
          for (n=100; n <= indexCount; n++ )
          {
            checkbox = n+"_check";
            if ($("#"+checkbox).is(':checked'))
            {
              
              var field = $('input[name='+n+'_rekNr]').attr("name");
              var test = $('input[name='+field+']').val();
              if (test.length < 1)
              {
                errorTxt = errorTxt + "\nrij "+ eval(n-99)+": rekeningnr mag niet leeg zijn";
              }
             
              var field = $('select[name='+n+'_portefeuille]').attr("name");
              var test = $('select[name='+field+']').val();
             
              if (test.length < 1)
              {
                errorTxt = errorTxt + "\nrij "+ eval(n-99)+": portefeuille mag niet leeg zijn";
              }
             
              var field = $('select[name='+n+'_valuta]').attr("name");
              var test = $('select[name='+field+']').val();
             
              if (test.length < 1)
              {
                errorTxt = errorTxt + "\nrij "+ eval(n-99)+": valuta mag niet leeg zijn";
              }
            }
            
          }
        }
        if (errorTxt.length > 0)
        {
          alert(errorTxt);
          return false;
        }
        
      });
      if (t > 0)
      {
        $("#frmAction").hide();
        $("#addRekening").val("1");
        $("#kopje").html("<b>Rekeningen toevoegen</b>");
      }
      $('input[type="checkbox"]').change(function(){
        var t = $('input[type="checkbox"]:checked').length;
        if (t > 0)
        {
          $("#frmAction").hide();
          $("#addRekening").val("1");
          $("#kopje").html("<b>Rekeningen toevoegen</b>");
        }
        else
        {
          $("#frmAction").show(200);
          $("#addRekening").val("0");
          $("#kopje").html("<b>Mutaties verwerken</b>");
        }
      });
      
    });
  </script>
<?
	exit();
	}
}



$progressStep = 0;
$prb->setLabelValue('txt1','Converteren records ('.$csvRegels.' records)');

 $query = "SELECT HSBCcode,doActie FROM hsbcTransactieCodes";
 $DB->executeQuery($query);
 while ($row = $DB->nextRecord())
 {
   $transactieMapping[$row["HSBCcode"]] = $row["doActie"];
 }
// debug("<br/><br/><br/><br/>");
debug($transactieMapping,"Transactie codes");
$row = 0;

$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
$sontigeTotaal = 0;
while ($data = fgetcsv($handle, 4096, ";"))
{
  $row++;
  if ($row == 1)
  {
    continue;  // skip header
  }

  $pro_step += $pro_multiplier;
  $prb->moveStep($pro_step);
  if (in_array($row, $skipFoutregels))
  {
    $skipped .= "- regel $row overgeslagen<br>";
    continue; // rest overslaan, lees nieuwe regel
  }

// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
  $data = array_reverse($data);
  $data[] = "leeg";
  $data = array_reverse($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
//   echo print_r($data);
//   exit();


  $match = explode(":", $data[1]);

  $data["row"] = $row;

  switch (substr($match[0], 1))
  {
    case "Order":

      //$index = "ORD:".$data[50];
      if (trim($data[50]) == "")
      {
        $index = $data[21];  // call 6991 voor koppels
      }
      else
      {
        $index = "ORD:".$data[50];
      }


      $data["ISIN"]         = $data[2];
      $data["bankcode"]     = $data[2];
      $data["boek"]         = hsbcDate($data[3]);
      $data["settle"]       = hsbcDate($data[9]);
      $data["rekening"]     = $data[8];
      $data["rekValuta"]    = $data[28];
      $data["transId"]      = $data[21];
      $data["portefeuille"] = $match[1];
      $data["nettoBedrag"]  = hsbcNumber($data[5]);
      $data["aantal"]       = hsbcNumber($data[6]);
      $data["koers"]        = hsbcNumber($data[7]);
      $data["kosten1"]      = hsbcNumber($data[13]); // courtage
      $data["kosten2"]      = hsbcNumber($data[14]); // spesen
      $data["kosten3"]      = hsbcNumber($data[15]); // frantspasen
      $data["kosten4"]      = hsbcNumber($data[16]); // gebuhren
      $data["kosten5"]      = hsbcNumber($data[54]);
      $data["renob"]        = hsbcNumber($data[25]);
      $data["fondsVal"]     = $data[11];
      $data["wisKrsFvRekv"] = hsbcNumber($data[12]);
      $data["omschrijving"] = $data[26];
      $data["storno"]       = $data[24];
//      $data["tax"] = $data[];
//      $data["taxValuta"] = $data[65];
//      $data["taxWisselKrs"] = $data[66];


      $data["transactieCode"] = $data[4];

      $fonds = array();

      if (stristr($data[17], "sonstige Umbuchung / Änderung sonstiger"))
      {
        $meldArray[] = "regel ".$row." overgeslagen: onnodige stukkenboeking";
        $sontigeTotaal += $data[6];
        $index = "-1";
      }


      if (!hsbcGetFonds($data["bankcode"], $data["ISIN"], $data["fondsVal"]))
      {
        $meldArray[] = "regel ".$row.": Fonds ".$data["bankcode"]." (".$data["ISIN"]." ".$data["fondsVal"].") --> niet gevonden ";
      }
      else
      {
        $data["fonds"] = $fonds;
      }

      break;
    case "Kupon":
      $index = $data[22];
      $data["ISIN"]         = $data[2];
      $data["bankcode"]     = $data[2];
      $data["boek"]         = hsbcDate($data[14]);
      $data["settle"]       = hsbcDate($data[31]);
      $data["rekening"]     = $data[21];
      $data["rekValuta"]    = $data[25];
      $data["transId"]      = $data[22];
      $data["portefeuille"] = $match[1];
      $data["nettoBedrag"]  = hsbcNumber($data[5]);
      $data["aantal"]       = hsbcNumber($data[6]);
      $data["koers"]        = hsbcNumber($data[7]);
      $data["kosten1"]      = hsbcNumber($data[9]);
      $data["kosten2"]      = hsbcNumber($data[11]);
      $data["kosten3"]      = hsbcNumber($data[18]);
      $data["fondsVal"]     = $data[15];
      $data["wisKrsFvRekv"] = hsbcNumber($data[16]);
      $data["omschrijving"] = $data[20];
      $data["storno"]       = $data[23];
      $data["tax"]          = hsbcNumber($data[12]);
      $data["taxValuta"]    = $data[34];
      $data["taxWisselKrs"] = hsbcNumber($data[35]);

      $data["transactieCode"] = $data[4];

      $fonds = array();
      if (!hsbcGetFonds($data["bankcode"], $data["ISIN"], $data["fondsVal"]))
      {
        $meldArray[] = "regel ".$row.": Fonds ".$data["bankcode"]." (".$data["ISIN"]." ".$data["fondsVal"].") --> niet gevonden ";
      }
      else
      {
        $data["fonds"] = $fonds;
      }
//      debug($data);
      break;
    case "Buchung":
      $index = -1;
      if (hsbcNumber($data[3]) == 0)
      {
        continue;   // boeking zonder waarde
      }
      $boekDatum = hsbcDate($data[5]);
      if (substr($data[12], 0, 5) == "FX-07")
      {
        $index = substr($data[12],0, -2);
        $dp = explode("#", $data[12]);
        $datumRaw = explode(".",substr($dp[0],-10));
        $datum = $datumRaw[2]."-".$datumRaw[1]."-".$datumRaw[0];
        if ($datumRaw[2] > 2018 AND $datumRaw[2] < 2039)
        {
          $boekDatum = $datum;
        }

        $transActieCode = "FX";
      }
      else
      {
        $index = $data[12];
        $transActieCode = $data[2];
      }
      $data["boek"]           = $boekDatum;
      $data["settle"]         = hsbcDate($data[4]);
      $data["rekening"]       = $match[1];
      $data["rekValuta"]      = $data[7];
      $data["transId"]        = $data[12];
      $data["nettoBedrag"]    = hsbcNumber($data[3]);
      $data["omschrijving"]   = $data[11];
      $data["storno"]         = $data[13];
      $data["transactieCode"] = $transActieCode;

      break;
    default:
      $index = "-1";
      break;
  }

  if ($index == -1)
  {
    continue;
  }

  $dataSet[$index][] = $data;
}

if ($sontigeTotaal != 0)
{
  $meldArray[] = "";
  $meldArray[] = "<b>LET OP onnodige stukkenboekingen verschil {$sontigeTotaal}</b>";
}

//debug($dataSet);
foreach($dataSet as $k=>$item)
{

  if (count($item) == 1)
  {
    $data = $item[0];

    if ($item[0]["storno"] == "Y")
    {
      $meldArray[] = "regel ".$item[0]["row"].": Storno ({$k}) overgeslagen ";
      continue;
    }

    if ($data["transactieCode"] == "FX")
    {
      $do_func = "do_FX";
    }
    else
    {
      $val = $transactieMapping[$data["transactieCode"]];

      $do_func = "do_$val";
    }

    if ( function_exists($do_func) )
    {
      call_user_func($do_func);
    }
    else
    {
      do_error("functie {$do_func}");
    }

  }
  else if (count($item) == 2)
  {

    if (substr($k,0,4) == "ORD:")
    {
      if (
        $item[0][6] + $item[1][6] == 0 AND
        $item[0][7] == $item[1][7] AND
        $item[0][9] == $item[1][9] AND
        $item[0][11] == $item[1][11] AND
        $item[0][26] == $item[1][26] AND
        $item[0][44] == $item[1][44] AND
        $item[0][65] == $item[1][65]
      )
      {
        $meldArray[] = "regel ".$item[0]["row"]." en ".$item[1]["row"].": 0 boeking overgeslagen ";
        continue;
      }

      $data = $item[0];
      $val = $transactieMapping[$data["transactieCode"]];
      $do_func = "do_$val";
      if ( function_exists($do_func) )
      {
        call_user_func($do_func);
      }
      else
      {
        do_error("functie {$do_func}");
      }
      $data = $item[1];
      $val = $transactieMapping[$data["transactieCode"]];
      $do_func = "do_$val";
      if ( function_exists($do_func) )
      {
        call_user_func($do_func);
      }
      else
      {
        do_error("functie {$do_func}");
      }
      continue;
    }

    if ($item[0]["storno"] == "Y" OR
        $item[1]["storno"] == "Y")
    {
      $meldArray[] = "regel ".$item[0]["row"]." en ".$item[1]["row"].": Storno ({$k}) overgeslagen ";
      continue;
    }

    $data = array(
      "a" => $item[0],
      "b" => $item[1]
    );

    call_user_func("DO_FX");
  }
  else
  {

    do_error("regel: ".$k." dataset met meer dan 2 poten");
  }
}

fclose($handle);

//
// plaats output in TijdelijkeRekeningmutaties table
//
$prb->moveStep(0);
$prb->setLabelValue('txt1','Opslaan in tijdelijke tabel');
$pro_step = 0;

reset($output);
for ($ndx=0;$ndx < count($output);$ndx++)
{
  if ($ndx == 0)
  {
    if (checkForDoubleImport($output[$ndx]) AND !$__develop )
    {
      $prb->hide();
      Echo "<br> FOUT: De eerste transactieregel komt exact overeen met reeds aanwezige informatie";
	    exit();
    }



  }
   $pro_step += $pro_multiplier;
   $prb->moveStep($pro_step);

	$_query = "INSERT INTO TijdelijkeRekeningmutaties SET";
	$sep = " ";
	while (list($key, $value) = each($output[$ndx]))
	{
 	  if ($manualBoekdatum AND $key == "Boekdatum")
	  {
	    $value = $manualBoekdatum;
	  }

   $_query .= "$sep TijdelijkeRekeningmutaties.$key = '".mysql_escape_string($value)."'
";
   $sep = ",";
	}
  $_query .= ", add_date = NOW()";
  $_query .= ", add_user = '".$USR."'";
	$_query .= ", change_date = NOW()";
  $_query .= ", change_user = '".$USR."'";
  $DB->SQL($_query);
	if (!$DB->Query())
	{
	  echo mysql_error();
	  Echo "<br> FOUT bij het wegschrijven naar de database!";
	  exit();
	}
}
$prb->hide();
?>


<b>Klaar met inlezen <br></b>
<?
foreach ($meldArray as $item)
{
  if (strstr($item,"notabedrag sluit aan"))
  {
    continue;
  }
  echo "<br/>".$item;
}

//listarray($meldArray);
?>
<br/>
<br/>
Records in HSBC bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>