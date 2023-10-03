<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/05/04 11:10:14 $
 		File Versie					: $Revision: 1.8 $

 		$Log: kbc_import.php,v $
 		Revision 1.8  2020/05/04 11:10:14  cvs
 		call 7598

 		Revision 1.7  2020/04/01 07:29:15  cvs
 		call 7598

 		Revision 1.6  2020/01/29 10:23:47  cvs
 		call 7598

 		Revision 1.5  2020/01/29 10:17:14  cvs
 		call 7598

 		Revision 1.4  2020/01/27 10:00:50  cvs
 		call 7598

 		Revision 1.3  2020/01/15 14:34:45  cvs
 		call 7598

 		Revision 1.2  2019/10/04 14:03:54  cvs
 		call 7598

 		Revision 1.1  2019/10/04 07:44:49  cvs
 		call 7598

 		Revision 1.2  2018/09/23 17:14:23  cvs
 		call 7175

 		Revision 1.1  2015/05/06 09:39:32  cvs
 		*** empty log message ***








*/



/*
CASH
  0 => 'leeg',
  1 => portefeuille/rekening
  2 => nvt
  3 =>
  4 => boekdatum
  5 => valutadatum
  6 => omschrijving
  7 => nettobedrag
  8 => valuta
  9 => vorig saldo
  10 => nieuw saldo
  11 => transactie id
  12 => transactie code
  13 =>
  14 => ISIN

STUKKEN
   0 => leeg
   1 => portefeuille
   2 =>
   3 =>
   4 => transid (komt terug in CASH)
   5 => extern transid                      airs transid = $data[4]."-".$data[5]
   6 => storno (Y)
   7 => interne storno id                   bij storno overgeslagen $data[7]."-".$data[8]
   8 => externe storno id
   9 => ISIN
  10 => fondssoort
  11 =>  KBC code
  12 =>  KBC fondsnaam
  13 => fondsValuta
  14 => transactieCode
  15 => deb/cre
  16 => oms transactie
  17 => aantal
  18 => koers
  19 => koers valuta
  20 => ordernr
  21 =>
  22 =>
  23 =>
  24 => boekdatum
  25 =>
  26 => valutadatum
  27 => brutobedrag
  28 => bruto valuta
  29 =>  nettobedrag
  30 => rekeningvaluta
  31 => wisselkoers
  32 => foreign fee         (in fonds valuta)
  33 => value square fee    (in fonds valuta)
  34 => kbc sec fee         (in fonds valuta)
  35 => foreign tax         (in fonds valuta)
  36 => stamp duty          (in fonds valuta)
  37 => order fees          (in fonds valuta)

dividend
  1 	CLIENT NBR
  2 	CLIENT NAME
  3 	CANC
  4 	SECURITY NAME
  5 	ISIN
  6 	TYPE
  7 	REC DT
  8 	EX DT
  9 	PAY DT
  10 	WITF PCT
  11 	QUANTITY
  12 	CUR
  13 	GROSS
  14 	WITF
  15 	WITL
  16 	CHAR
  17 	OTH
  18 	NET
  19 	CUR
  20 	PRICE
  21 	WITL PCT
  22 	FEE 1
  23 	FEE 2
  24 	FEE 5
  25 	FEE 6
  26 	EXCH RATE

 */

include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once("kbc_functies.php");

include_once "../../classes/AIRS_import_afwijkingen.php";
$afw = new AIRS_import_afwijkingen("KBC");

//debug($_REQUEST);

//$skipTransactieCodeArray = array(
//  "ACT",
//  "FEX",
//  "FIN",
//  "IVC",
//  "PRL",
//  "VCT"
//);

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


$query = "SELECT bankCode,doActie FROM kbcTransactieCodes";
$DB->executeQuery($query);
while ($row = $DB->nextRecord())
{
  $transactieMapping[$row["bankCode"]] = $row["doActie"];
}

debug($transactieMapping, "transactieMapping");
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
include("kbc_validate.php");

$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van KBC CSV bestand');

if ($doIt <> "1")  // validatie is al gebeurd dus skippen
{
  $error = array();

  if (!validateCvsFile($file) )
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

    <?
    if (count($_SESSION["importFoutFile"]) > 0)
    {
      echo "<br/><a href='wwwFoutenBestand.php?bank=KBC' ><button id='btnDownload'>Download FOUTEN bestand</button></a><br/>";
    }
    ?>

    <br>
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





global $transactieMapping, $prb;
$row = 0;
$progressStep = 0;
$csvRegels = count(file($file));
$prb->setLabelValue('txt1','Converteren records ('.$csvRegels.' records)');
echo "<li>bestand: $srt, $csvRegels regels</li>";
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
$stornoDeleteArray = array();
$fileType = "";
$dateFields = array(
  "CPNS" => array(8,9),
  "TRNS_BS" => array(22,24,26),
  "FMVT" => array(3,4)
);
$dataSet = array();
while ($data = fgetcsv($handle, 4096, ","))
{
  $row++;

  if (substr($data[0],0,3) == "CLI")
  {
    continue; // headerregels skippen
  }



  // BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
  $data = array_reverse($data);
  $data[] = "leeg";
  $data = array_reverse($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie

//    debug($data);

  if (kbcCheckDate($data[8]) AND kbcCheckDate($data[9]) )
  {
    $fileType = "CPNS";

  }
  elseif (kbcCheckDate($data[24]) AND kbcCheckDate($data[26]))
  {
    $fileType = "TRNS";
  }
  elseif (kbcCheckDate($data[4]) AND kbcCheckDate($data[5]) )
  {
    $fileType = "FMVT";
  }
  else
  {
    $fileType = "xx";
  }

  $pro_step += $pro_multiplier;
  $prb->moveStep($pro_step);
  if (in_array($row , $skipFoutregels))
  {
    $skipped .= "- regel $row overgeslagen<br>";
    continue; // rest overslaan, lees nieuwe regel
  }


//   echo print_r($data);
//   exit();
//
//debug($data);
  $skipRow = false;
  $tc = $data[14];
  switch ($fileType)
  {
    case "CPNS";

      $data[7]              = kbcDatum($data[7]);
      $data[8]              = kbcDatum($data[8]);
      $data[9]              = kbcDatum($data[9]);
      // $data[26]             = 1/$data[26];
      $data["valutaKoers"]  = $data[26];
      $data["rekValuta"]    = $data[19];
      $tc                   = $data[6];
      break;
    case "TRNS";
      $data[22]             = kbcDatum($data[22]);
      $data[24]             = kbcDatum($data[24]);
      $data[26]             = kbcDatum($data[26]);
      $data["valutaKoers"]  = $data[31];
      $data["rekValuta"]    = $data[30];
      if ($data[8] != "" AND $data[6] == "Y") // als storno dan eerdere boeking verwijderen in output
      {
        $stornoDeleteArray[] = $data[8];
        $skipRow = true;                      // deze storno regel overslaan
      }
//        debug($data, $row.": stukken boeking");
      break;
    case "FMVT";

      if (trim($data[12]) != "")
      {
        $skipRow = true;
      }
      else
      {
        $data[4] = kbcDatum($data[4]);
        $data[5] = kbcDatum($data[5]);
//          debug($data, "geld boeking");
        //do_MUT();
        $dataSet[$data[11]][] = $data;
        $skipRow = true;
      }
      break;
    default:

  }

  if ($skipRow )
  {
    continue;
  }




  $num = count($data);
  $val = $transactieMapping[trim($tc)];

  $do_func = "do_$val";
  if ( function_exists($do_func) )
  {
    call_user_func($do_func);
  }
  else
  {
    do_error(trim($tc));
  }


}
fclose($handle);

if (count($dataSet) > 0)
{

  foreach ($dataSet as $setje)
  {

    if (count($setje) == 2)
    {
      do_FX($setje);
    }
    else
    {
      $data = $setje[0];
      do_MUT();
    }

  }
}



//
// plaats output in TijdelijkeRekeningmutaties table
//
$prb->moveStep(0);
$prb->setLabelValue('txt1','Opslaan in tijdelijke tabel');
$pro_step = 0;
//debug($stornoDeleteArray);
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
  if (in_array($output[$ndx]["bankTransactieId"], $stornoDeleteArray))
  {
    continue;
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
include_once "verschillenLijst.html";
?>

  Records in KBC CSV bestand :<?=$row?><br>
  aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
  <hr>
  <a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
  <hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
