  <?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/01/14 12:08:07 $
 		File Versie					: $Revision: 1.11 $

 		$Log: pictet_import.php,v $
 		Revision 1.11  2020/01/14 12:08:07  cvs
 		call 6888
 		
 		Revision 1.10  2019/07/08 14:44:19  cvs
 		call 7927
 		
 		Revision 1.9  2019/06/25 15:09:30  cvs
 		call 7917
 		
 		Revision 1.8  2019/03/22 12:35:44  cvs
 		call 6686
 		
 		Revision 1.7  2018/10/03 15:32:40  cvs
 		no message
 		
 		Revision 1.6  2018/10/01 06:50:35  cvs
 		call 7173
 		
 		Revision 1.5  2018/05/30 13:11:53  cvs
 		call 6888
 		
 		Revision 1.4  2018/05/16 13:32:19  cvs
 		call 6888
 		
 		Revision 1.3  2018/03/27 09:59:30  cvs
 		call 6768
 		
 		Revision 1.2  2018/01/22 11:06:45  cvs
 		call 4125
 		
 		Revision 1.1  2015/12/01 09:01:53  cvs
 		update 2540, call 4352
 		
 		Revision 1.1  2015/05/06 09:39:32  cvs
 		*** empty log message ***
 		







*/

/*
dataindex
  0 = PORTFOLIO
  1 = PICTET DATE
  2 = TRANSACTION
  3 = PICTET CODE
  4 = REVERSAL
  5 = PICTET ID
  6 = ISIN
  7 = TELEKURS
  8 = SEDOL
  9 = CUSIP
  10 = PICTET BOOKING TYPE
  11 = BOOKING DATE
  12 = TRADE DATE
  13 = VALUE DATE
  14 = SECURITY CURR
  15 = COST PRICE CURR
  16 = A/C TYPE
  17 = A/C CURR
  18 = CONTRACT NBR
  19 = TEXTE
  20 = QUANTITY
  21 = TRADE CURR
  22 = GROSS AMOUNT TRADE CURR
  23 = GROSS UNIT PRICE
  24 = NET AMOUNT VAL. CURR
  25 = FX BETWEEN TRADE AND A/C CURR
  26 = NET AMOUNT A/C CURR
  27 = TRANSACTION DESCRIPTION
  28 = REUTERS KEY CODE
  29 = BLOOMBERG KEY CODE
  30 = NET AMOUNT CUSTOMER CURR.
  31 = NET REALISED GAIN/LOSS
  32 = NET REALISED GAIN/LOSS ON MARKET
  33 = NET REALISED GAIN/LOSS ON CURR.
  34 = CODE OPERATION ESPECES
  35 = BROKERAGE FEES
  36 = FOREIGN TAXS
  37 = CORRESPONDENT FEES
  38 = DB CR AMOUNT
  39 = NUMERO ORDRE
  40 = NUMERO CONTAINER
  41 = N? REFERENCE D ORIGINE
  42 = BROKERAGE FEES (REF CCY)
  43 = CORRESPONDENT FEES (REF CCY)
  44 = FOREIGN TAXS (REF CCY)
  45 = HANDLING CHARGES
  46 = HANDLING CHARGES (REF CCY)
  47 = SWISS STAMP
  48 = SWISS STAMP (REF CCY)
  49 = MISCELLANEOUS FEES
  50 = MISCELLANEOUS FEES (REF CCY)
*/


include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once("pictet_functies.php");

// call 7173
include_once "../../classes/AIRS_import_afwijkingen.php";
$afw = new AIRS_import_afwijkingen("PIC");

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
include("pictet_validate.php");

$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van Pictet CSV bestand');

if ($doIt <> "1")  
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

 $query = "SELECT PICcode,doActie FROM pictetTransactieCodes  ";
 $DB->executeQuery($query);
 while ($row = $DB->nextRecord())
 {
   $transactieMapping[$row["PICcode"]] = $row["doActie"];
 }
debug($transactieMapping,"transactie Mapping");
$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
while ($data = fgetcsv($handle, 1000, "\t"))
{
	$row++;
  if (count($data) < 10) continue; //minder dan 10 kol is overslaan
  $data = convertRecord($data);

  if ($row < 4) continue;  // headers overslaan
  
  
 	$pro_step += $pro_multiplier;
 	$prb->moveStep($pro_step);
	if (in_array($row , $skipFoutregels))
 	{
 		$skipped .= "- regel $row overgeslagen<br>";
 		continue; // rest overslaan, lees nieuwe regel
 	}

// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
	 $data = array_reverse($data);
	 $data[] = $row;
	 $data = array_reverse($data);
   do_algemeen();
   
   if (trim($data[5]) <> "")
   {
     $meldArray[] = "regel ".$row.": ".$mr["Rekening"]." bevat STORNO --overgeslagen-- ";
     continue;
   }  
   $id = $data[1]."-".$data[40];
   $FXarray[$id][] = $data;
   
}

fclose($handle);
//debug($FXarray);
$data = "";
foreach($FXarray as $key=>$value)
{
  if ( trim($value[0][3]) == "ICAC" AND count($value) == 2)
  {
    do_FX($value[0], $value[1], $key);
  }
  else
  {
    //debug($value, "valie");
    for ($kIndx = 0; $kIndx < count($value);$kIndx++)
    {
      $data = $value[$kIndx];
      //debug($data,"data");
      $PICcodeNotFound = true;
      $fonds = array();
      if (trim($data[6]) <> "")
      {
        $PICcode = trim($data[6]);
        $query = "SELECT * FROM Fondsen WHERE PICcode = '".$PICcode."' ";
        $DB->SQL($query);
        if ($fonds = $DB->lookupRecord())  $PICcodeNotFound = false;
      }
      if ($data[7] <> "" AND $data[15] <> "" AND $PICcodeNotFound)
      {
        $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$data[7]."' AND Valuta = '".$data[22]."' ";
        $DB->SQL($query);
        $fonds = $DB->lookupRecord();
      }
      
      $val = $transactieMapping[trim($data[3])];
      //debug($val,$data[3]);
      $do_func = "do_$val";
//      debug($val,$do_func);



      if ( function_exists($do_func) )
      {

        if ($do_func == "do_CA")
        {

          switch ($data[4])
          {
            case "ETRBL":
              call_user_func($do_func);
              break;
            case "ETDDE":     // opboeking stocks
            case "ETRB":
            case "ETATS":     // spinoff

              if ($data[21] < 0)
              {
                call_user_func("do_L");
              }
              else
              {
                call_user_func("do_D");
              }

              break;
            case "ETDV":      // ontvangen aandelen uit stock dividend
            case "ETS":       // exercise of rights
            case "ETR OE":    // expiratie oude opties
            case "ETE-":
            case "ETE+":
              if ($data[21] < 0)
              {
                call_user_func("do_Lnul");
              }
              else
              {
                call_user_func("do_Dnul");
              }

              break;
//            case "ETDV":
//              call_user_func("do_A");
//
//              break;
            default:
              echo "<BR>FOUT ".$data[3]."/".$data[4]." nog niet gedefinieerd";
          }

        }
        else
        {
          call_user_func($do_func);
        }


      }
      else
      {
        do_error($data[0],$data[3]);
      }
    }
  }

}


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
listarray($meldArray);
?>

Records in Pictet CSV bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>