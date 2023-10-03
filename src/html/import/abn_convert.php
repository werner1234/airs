<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2011/12/06 14:07:41 $
 		File Versie					: $Revision: 1.2 $

 		$Log: abn_convert.php,v $
 		Revision 1.2  2011/12/06 14:07:41  cvs
 		eerste spatie verwijderen indien aanwezig
 		
 		Revision 1.1  2011/07/16 09:52:45  cvs
 		*** empty log message ***
 		
 		Revision 1.19  2008/10/01 07:48:06  cvs
 		nieuwe commit 1-10-2008
 		
 		Revision 1.18  2008/06/05 06:54:40  rvv
 		*** empty log message ***

 		Revision 1.17  2006/03/06 11:46:19  cvs
 		do_D: als bedrag = 0 toch eerste transactie verwerken




*/


include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");

include_once("abn_convert_functies.php");

//$skipFoutregels = array();

if ($doIt == "1")  // validatie mislukt, wat te doen?
{

  if ($action == "stop")
	{
		 // file wissen
		 echo "<br>Het transactiebestand is verwijderd en de import is afgebroken";
		 if (file_exists($bestand) ) unlink($bestand);
		 $DB = new DB();
		 $DB->SQL("DELETE FROM TijdelijkeRekeningmutaties WHERE  TijdelijkeRekeningmutaties.change_user = '$USR' ");
		 $DB->Query();
		 exit();
	}
	else
	{
     header("location:../tijdelijkerekeningmutatiesList.php");
     exit();
	}

}


//
// check of er records in de TijdelijkeRekeningmutaties tabel zitten
//
$DB = new DB();

$content = array();
$content[style] = '<link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">';
echo template("../".$__appvar["templateContentHeader"],$content);
if ($DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE TijdelijkeRekeningmutaties.change_user = '$USR' ") > 0)
{
	echo "<br>
<br>
De tabel TijdelijkeRekeningmutaties is niet leeg voor gebruiker ($USR) (".$DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE TijdelijkeRekeningmutaties.change_user = '$USR'").")<br>
<br>
de import is geannuleerd ";
	exit;

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
include("abn_convert_validate.php");


$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van ABNAMRO bestand');

if ($doIt <> "1")  // validatie is al gebeurd dus skippen
{
	if (!validateFile($file))
	{
		$prb->hide();
?>
  	<table cellpadding="0" cellspacing="0">
  	<tr>
    	<td colspan="2" bgcolor="#BBBBBB">
     	 Foutmelding bij validatie van ABN bestand<br>
     	 Bestandsnaam :<?=$file?>
    	</td>
  	</tr>
<?
	$foutregels = "";
	$_vsp = "";
	for ($x=0;$x < count($error);$x++)
	{
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
	<form action="<?=$PHP_SELF?>">
	  <input type="hidden" name="doIt" value="1">
  	<input type="hidden" name="bestand" value="<?=$file?>">
  	<input type="hidden" name="foutregels" value="<?=$foutregels?>">
  	<select name="action">
    	<option value="stop">Bestand verwijderen en import afbreken</option>
  	</select>
  	<input type="submit" value=" Uitvoeren">
	</form>

<?
	exit();
	}
}



$prb->max = $csvRegels;

$prb->setLabelValue('txt1','Converteren records ('.$csvRegels.' regel)');

//$mt940_skip = Array("KOOP ","VERKOOP ","DIV","COUPON","ANN.KOOP","ANN.VERKOOP","LOSSING");
$mt940_skip = Array();
$content = array();
echo template("../".$__appvar["templateContentHeader"],$content);


if (!$file)  $file   = "abn.STA";
$volgnr = 1;

$row = 1;
$handle = fopen($file, "r");
$ndx=0;
$dataSet = Array();
$row = Array();
$_tempRow = Array();
$regtel = 0;

//
// lees alle bekende records in array
//

while ($data = fgets($handle, 4096))
{
  if ($data[0] == " ") $data = substr($data,1);  // als eerste char een spatie deze wegknippen
	$regtel++;
	$prb->moveNext();

  switch (trim($data))
  {
   	case "ABNANL2A":
        //cycle
   		break;
   	case "500":
   	case "510":
   	case "571":
   	case "554":
   	case "940":  //type record
   	  $dataSet[$ndx][type] = $data;
 			break;
   	case "-":  // einde record
      $ndx++;
 			break;
  	default:
  	  if (substr($data,0,1) <> ":")
   	  {
   	    $dataSet[$ndx][txt] = substr($dataSet[$ndx][txt],0,-1)." ".$data;
   	  }
   	  else
   	  {
   	  	$_regel = explode(":",$data);
   	  	$_prevKey = $_regel[1];
   	  	$dataSet[$ndx][txt] .= $_regel[1]."&&".$_regel[2];  // vul data velden
   	  }
   		break;
   }

}
fclose($handle);


//
//  alle ongeldige records weggooien
//

for($loopndx = 0;$loopndx < count($dataSet);$loopndx++)
{
	$_var = trim($dataSet[$loopndx][type]);

	switch ($_var)
	{
		case "940":
		  $_tmprows = array();
		  $_mt940Count++;
		  $_data = explode(chr(10),$dataSet[$loopndx][txt]);
		  $addRecord = 0;
		  for ($subLoop = 0; $subLoop < count($_data);$subLoop++)
		  {
		  	$_r = explode("&&",$_data[$subLoop]);
		  	array_push($_tmprows,$_data[$subLoop]);
		  	if ($_r[0] == "61") $addRecord++; // veld 61 bestaat
		  	if ($_r[0] == "86")                    // skip veld 83 als de tekst begin met waarden uit de $mt940_skip array (transacties)
		  	{
		  		$skipTag = false;
		  		for ($xx=0; $xx < count($mt940_skip);$xx++)
		    	{
		    		$arrValue = $mt940_skip[$xx];
		  	  	if (substr(strtoupper($_r[1]),0,strlen($arrValue)) == $arrValue)
		  	  	{
		  	  	  array_pop($_tmprows);  // verwijder 83 regel
		  	  	  array_pop($_tmprows);  // verwijder vorige 61 regel
		  	  	  $addRecord--;
		  	  		$skipTag = true;
		  	  		$mt940_83_skip++;
		  	  	}
		    	}
		    	if ($skipTag)
		    	  $mt940_rec[] = "[SKIP]  ".str_replace(chr(13),"#",$_r[1]);
		    	else
		    	  $mt940_rec[] = str_replace(chr(13),"#",$_r[1]);
		  	}

		  }
		  if ($addRecord > 0)
		  {
	  		$_mt940CountClean++;
	  		$dataSet940[] = array("type"=>"940","txt"=>implode(chr(10),$_tmprows));
	  	}
		  break;
    case "554":
		  $_mt554Count++;
		  $dataSet554[] = $dataSet[$loopndx];
		  break;
		default:

	}
}

// $dataSet554 bevat nu alle geldige 554 records
// $dataSet940 bevat nu alle geldige 940 records

//listarray($dataSet554);

if (count($dataSet554) > 0) // er bestaand MT554 mutaties
{

  // inlezen AAB transactie lijst

  $query = "SELECT * FROM AABTransaktieCodes";
  $DB->SQL($query);
  $DB->Query();
  while ($fillRec = $DB->nextRecord())
  {
    $transAct[$fillRec["code"]] = $fillRec["actie"];
  }
  //listarray($transAct);
  for ($_ndx = 0; $_ndx < count($dataSet554);$_ndx++)
  {
    $rec = $dataSet554[$_ndx];
    $_data = explode(chr(10),$dataSet554[$_ndx][txt]);

    for ($subLoop = 0; $subLoop < count($_data);$subLoop++)
    {
      $_r = explode("&&",$_data[$subLoop]);
      if ($_r[0] == "72")
      {
        $_velden = explode("/",$_r[1]);

        if (array_key_exists($_velden[0], $transAct))
        {
          $do_func = $transAct[$_velden[0]];
          if ( function_exists($do_func) )
          {
            //$logje[]=$_velden[0]." - ".$do_func;
            call_user_func($do_func,convertRecord($rec));
          }
        }
        else
        {
          if (is_numeric($_velden[0]))   // als er een transactietype is in errorlog opnemen
            $error[] = "Transactietype ".$_velden[0]." komt niet voor";
        }
        /*
          echo "<br>transactie ".$_velden[0]." niet gevonden";

        switch ($_velden[0])
        {
          case "01010101":  //aankoop van stukken
          case "01010401":
            do_A(convertRecord($rec));
            $call[] = "do_A";
            break;
          case "01010102":  //verkoop van stukken
          case "01010402":
            do_V(convertRecord($rec));
            $call[] = "do_V";
            break;
          case "01010201":  //aankoop van opties
            do_AO(convertRecord($rec));
            $call[] = "do_AO";
            break;
          case "01010202":  //verkoop van opties
            do_VO(convertRecord($rec));
            $call[] = "do_VO";
            break;
          case "01040601":  //Contant dividend
            do_CD(convertRecord($rec));
            $call[] = "do_CD";
            break;
          case "01040501":  //betaling couponrente
            do_CR(convertRecord($rec));
            $call[] = "do_CR";
            break;
          case "01040401":  //lichting van stukken
            do_L(convertRecord($rec));
            $call[] = "do_L";
            break;
          case "01040402":  //deponering van stukken
            do_D(convertRecord($rec));
            $call[] = "do_D";
            break;
          default:
            if (is_numeric($_velden[0]))   // als er een transactietype is in errorlog opnemen
              $error[] = "Transactietype ".$_velden[0]." komt niet voor";
            break;
        }
        */
      }
    }
  }
}

if (count($dataSet940) <> 0) // er bestaand MT940 mutaties
{
	for ($_ndx = 0; $_ndx < count($dataSet940);$_ndx++)
	{
		do_mt940($dataSet940[$_ndx],$_ndx);
	}
}

//
// plaats output in TijdelijkeRekeningmutaties table
//
$prb->moveStep(0);

$prb->max = count($output);
$pro_step = 0;
$prb->setLabelValue('txt1','Opslaan in tijdelijke tabel ('.$prb->max.') records');
//$output = array();
include_once("checkForDoubleImport.php");
reset($output);
for ($ndx=0;$ndx < count($output);$ndx++)
{
  if ($ndx == 0)
  {
    if (checkForDoubleImport($output[$ndx]))
    {
      $prb->hide();
      Echo "<br> FOUT: De eerste transactieregel komt exact overeen met reeds aanwezige informatie";
	    //exit();
    }
  }
	$prb->moveNext();
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


if (count($error) > 0)
{
?>
<table border="1" cellpadding="5" cellspacing="0">
<tr>
  <td colspan="2" bgcolor="#DDDDDD">
  <b>fouten tijdens inlezen</b>
  </td>
</tr>
<?
	for ($x=0 ; $x < count($error); $x++)
	{
?>
<tr>
  <td><?=$x?></td>
  <td><?=$error[$x]?></td>
</tr>
<?
	}
?>
</table>
<br>
	<br>
	<b>Vervolg aktie?</b>
	<form action="<?=$PHP_SELF?>">
	  <input type="hidden" name="doIt" value="1">
  	<input type="hidden" name="bestand" value="<?=$file?>">
  	<input type="hidden" name="foutregels" value="<?=$foutregels?>">
  	<select name="action">
    	<option value="stop">Bestand verwijderen en import afbreken</option>
    	<option value="go">Ga naar tijdelijk importbestand</option>
  	</select>
  	<input type="submit" value=" Uitvoeren">
	</form>

<?
}
else
{
?>
  <a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<?
}
//listarray($logje);
?>


<b>Klaar met inlezen <br></b>
aangemaakte mutatieregels : <?=count($output)?><BR>

<hr>
<?

echo template("../".$__appvar["templateRefreshFooter"],$content);
?>