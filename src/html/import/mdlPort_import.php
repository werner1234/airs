<?
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
 		File Versie					: $Revision: 1.6 $

*/

include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("mdlPort_functies.php");

function cnvGetal($in)
{
  $out = str_replace(".","" ,$in );
  $out = str_replace(",","." ,$out );
  return $out;
}


$skipFoutregels = array();
$meldArray = array();


if ($doIt == "1")  // validatie mislukt, wat te doen?
{
  switch ($action)
  {
    case "stop":
      echo "<br>Het transactiebestand is verwijderd en de import is afgebroken";
    	if (file_exists($bestand) ) unlink($bestand);
		  exit();
      break;
    case "retry":
      $doIt = 0;
      $file = $bestand;
      break;
    default:
      $skipFoutregels = explode(",",$foutregels);
		  array_shift($skipFoutregels);  // verwijder eerste lege key
		  $file = $bestand; 
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
include("mdlPort_validate.php");


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
      $arg = array_merge($_POST,$_GET);

      ?>
    </table>
    <br>
    <br>
    <b>Vervolg aktie?</b>
    <form action="<?=$PHP_SELF?>" method="POST">
      <div id="kopje"></div>
      <input type="hidden" name="doIt" value="1">
      <input type="hidden" name="bestand" value="<?=$file?>">
      <input type="hidden" name="foutregels" value="<?=$foutregels?>">
      <input type="hidden" name="manualBoekdatum" value="<?=$arg["manualBoekdatum"]?>">
      <input type="hidden" name="mdlPortType" value="<?=$arg["mdlPortType"]?>">
      <select name="action" id="frmAction">
        <option value="stop">Bestand verwijderen en import afbreken</option>
        <option value="go">Bestand inlezen en onvolledige regels overslaan</option>
        <option value="retry">Bestand opnieuw inlezen en valideren</option>
      </select>

      <?


      if ( count($_SESSION["rekeningAddArray"]) >0 )
      {

        $rac = new rekeningAddStamgegevens($_SESSION["VB"],"TGB");
        $rac->getStyles();

        $rekArray = $_SESSION["rekeningAddArray"];
        for ($rNdx=0; $rNdx < count($rekArray); $rNdx++)
        {
          $rac->makeInputRow($rekArray[$rNdx]);
        }
        echo $rac->getHTML();
      }
      ?>


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



$csvRegels = Count(file($file));


$progressStep = 0;
$prb->setLabelValue('txt1','Converteren records ('.$csvRegels.' records)');
//$prb->hide();
$db = new DB();
$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
while ($data = fgetcsv($handle, 1000, ";"))
{
  $row++;



  if (in_array($row , $skipFoutregels))
  {
    $skipped .= "- regel $row overgeslagen<br>";
    continue; // rest overslaan, lees nieuwe regel
  }
 	$pro_step += $pro_multiplier;
 	$prb->moveStep($pro_step);


// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
	 $data = array_reverse($data);
	 $data[] = "leeg";
	 $data = array_reverse($data);
   $arg = array_merge($_POST, $_GET);
   $data[5] = cnvGetal($data[5]);
   $data[6] = cnvGetal($data[6]);
   $data[7] = cnvGetal($data[7]);
   $data[8] = cnvGetal($data[8]);

   $valutakoers = (float) $data[8];
   $fondskoers  = $data[7];

   if ($_POST["manualBoekdatum"] != "")
   {
     $data[7] = $_POST["manualBoekdatum"];
   }
   else
   {
     $dat = explode("-",$data[9]);
     if (count($dat) != 3)  // poormans headercheck als data[9] geen datum dan overslaan
     {
       continue;
     }
     $data[7] = $dat[2]."-".$dat[1]."-".$dat[0];
   }


// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
//   echo print_r($data);
//   exit();

   /*
    * [1] = portefeuille
    * [2] = Rek valuta
    * [3] = ISIN
    * [4] = fondsvaluta
    * [5] = koop
    * [6] = verkoop
    * ///////// berekende velden vanaf data[7]
    * [7] = boekdatum YYYY-MM-DD
    * [8] = rekeningnr
    * [9] = valutakoers
    * [10] = fondskoers
    * [11] = fondsOmschrijving
    * [12] = Fondscode
    * [13] = Fondseenheid
    *
    */
    if ($data[2] == "MEM")
    {
      $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Memoriaal = 1 AND Inactief = 0 AND Rekening = '{$data[1]}MEM'";

      if (!$rekRec = $db->lookupRecordByQuery($query))
      {
        $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Memoriaal = 1 AND Inactief = 0 AND Portefeuille = '{$data[1]}'";
        $rekRec = $db->lookupRecordByQuery($query);
      }
    }
    else
    {
      $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Memoriaal = 0 AND Inactief = 0 AND Portefeuille = '".$data[1]."' AND Valuta = '".$data[2]."'";
      $rekRec = $db->lookupRecordByQuery($query);
    }


   $data[8] = $rekRec["Rekening"];

   $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$data[3]."' AND Valuta = '".$data[4]."'";
   $fondsRec = $db->lookupRecordByQuery($query);

   if ($valutakoers <> 0)
   {
     $data[9] = $valutakoers;   // waarde uit file gebruiken
   }
   else
   {
      $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$data[4]."' AND Datum <= '".$data[7]."' ORDER BY Datum DESC";
      $valutaRec = $db->lookupRecordByQuery($query);
      $data[9] = $valutaRec["Koers"];
   }

  if (is_numeric($fondskoers))
  {
    $data[10] = $fondskoers;   // waarde uit file gebruiken
  }
  else
  {
    $query = "SELECT * FROM Fondskoersen WHERE Fonds='" . $fondsRec["Fonds"] . "' AND Datum <= '" . $data[7] . "' ORDER BY Datum DESC";
    $fondsKoers = $db->lookupRecordByQuery($query);
    $data[10] = $fondsKoers["Koers"];
  }


  $data[11] = $fondsRec["Omschrijving"];
  $data[12] = $fondsRec["Fonds"];
  $data[13] = $fondsRec["Fondseenheid"];


  switch ($arg["mdlPortType"])
  {
    case "deponering":
      if ($data[5] > 0)
      {
        do_D();
      }
      else
      {
        do_L();
      }
      break;
    case "beginboeking":
      if (substr($data[7],5,5) != "01-01")
      {
        echo "<BR>Overgeslagen beginboeking datum niet 01-01";
      }
      else
      {
        if ($data[5] > 0)
        {
          do_DB();
        }
        else
        {
          do_LB();
        }
      }


      break;
    default:
      if ($data[5] > 0)
      {
        do_A();
      }
      else
      {
        do_V();
      }

  }

//  if ($arg["mdlPortType"] == "deponering")
//  {
//    if ($data[5] > 0)
//    {
//      do_D();
//    }
//    else
//    {
//      do_L();
//    }
//
//  }
//  else
//  {
//    if ($data[5] > 0)
//    {
//      do_A();
//    }
//    else
//    {
//      do_V();
//    }
//  }


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

   $_query .= "$sep TijdelijkeRekeningmutaties.$key = '".mysql_escape_string($value)."'";
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
listarray($meldArray);
?>

Regels in CSV bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>