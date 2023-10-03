<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/06/08 11:44:05 $
 		File Versie					: $Revision: 1.32 $

 		$Log: abn_import.php,v $
 		Revision 1.32  2018/06/08 11:44:05  cvs
 		lege regels onderdrukken
 		
 		Revision 1.31  2017/09/19 10:49:17  cvs
 		call 6115
 		
 		Revision 1.30  2015/12/01 07:26:41  cvs
 		update 2540
 		
 		Revision 1.29  2015/06/11 16:16:40  cvs
 		*** empty log message ***
 		
 		Revision 1.28  2015/05/21 12:09:57  cvs
 		*** empty log message ***
 		
 		Revision 1.27  2015/05/11 13:36:52  cvs
 		*** empty log message ***
 		
 		Revision 1.26  2015/05/08 12:08:58  cvs
 		*** empty log message ***
 		
 		Revision 1.25  2014/12/16 07:30:30  cvs
 		*** empty log message ***
 		
 		Revision 1.24  2014/10/13 11:40:54  cvs
 		call 3117
 		
 		Revision 1.23  2014/07/10 06:53:14  cvs
 		*** empty log message ***
 		
 		Revision 1.22  2014/03/12 10:02:40  cvs
 		*** empty log message ***
 		
 		Revision 1.21  2012/05/15 15:02:19  cvs
 		controlebedrag

 		Revision 1.20  2011/11/29 09:34:43  cvs
 		als importregel met spatie dan verwijder spatie

 		Revision 1.19  2008/10/01 07:48:06  cvs
 		nieuwe commit 1-10-2008

 		Revision 1.18  2008/06/05 06:54:40  rvv
 		*** empty log message ***

 		Revision 1.17  2006/03/06 11:46:19  cvs
 		do_D: als bedrag = 0 toch eerste transactie verwerken




*/


include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");

include_once("abn_functies.php");

include_once "../../classes/AIRS_import_zoekVervang.php";
include_once "../../classes/AIRS_import_afwijkingen.php";
$afw = new AIRS_import_afwijkingen("AAB");
$zv = new AIRS_import_zoekVervang("AAB");

$VB = "";
//$skipFoutregels = array();

//if ($_POST["addRekening"] == "1")
//{
//  
//  $rac = new rekeningAddStamgegevens($_SESSION["VB"],"AAB");
//  $rac->addRekeningen($_POST);
//  $doIt = 0;
//  $file = $bestand; 
//}




if ($doIt == "1")  // validatie mislukt, wat te doen?
{

  switch ($action)
  {
    case "stop":
      echo "<br>Het transactiebestand is verwijderd en de import is afgebroken";
    	if (file_exists($bestand) ) unlink($bestand);
    	if (file_exists($bestand2) ) unlink($bestand2);
      $DB = new DB();
		  $DB->SQL("DELETE FROM TijdelijkeRekeningmutaties WHERE  TijdelijkeRekeningmutaties.change_user = '$USR' ");
		  $DB->Query();
		  exit();
      break;
    case "retry":
      $doIt = 0;
      $file = $bestand;
      break;
    default: 
      header("location:../tijdelijkerekeningmutatiesList.php");
      exit();
  }
}


//
// check of er records in de TijdelijkeRekeningmutaties tabel zitten
//
$DB = new DB();
$rekeningAddArray = array();
$content = array();
$content[style] = '<link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">            
                   <script type="text/javascript" src="../javascript/jquery-1.11.1.min.js"></script>
                   <script type="text/javascript" src="../javascript/jquery-ui-min.js"></script>';
echo template("../".$__appvar["templateContentHeader"],$content);

?>
<style>
    #skipScreen{
      display: none;
      padding:5px;
      margin-bottom: 30px;
    }
 </style>
<?
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
include("abn_validate.php");


$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van ABNAMRO bestanden');

if ($doIt <> "1")  // validatie is al gebeurd dus skippen
{

  
  
  $error = array();
  if ($_GET["abn1"] == "1")
  {
    validateFile($file,"single");
  }
  else
  {
    validateFile($file,"FND");
    validateFile($file2,"GELD");
  }

  if (count($error) > 0)        
	{
		$prb->hide();
?>
  	<table cellpadding="0" cellspacing="0">
  	<tr>
    	<td colspan="2" bgcolor="#BBBBBB">
     	 Foutmelding bij validatie van ABN bestand<br>
     	 Bestandsnaam :<?=$file?> of <?=$file2?>
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
    <div id="kopje"></div>
	  <input type="hidden" name="doIt" value="1">
  	<input type="hidden" name="abn1" value="<?=$abn1?>">
  	<input type="hidden" name="bestand" value="<?=$file?>">
  	<input type="hidden" name="bestand2" value="<?=$file2?>">
  	<input type="hidden" name="foutregels" value="<?=$foutregels?>">
  	<select name="action" id="frmAction">
    	<option value="stop">Bestand verwijderen en import afbreken</option>
<?
if ($_GET["abn1"] == "1")
  {
?>
    	<option value="retry">Bestand opnieuw inlezen en valideren</option>
<?
  }
?>
  	</select>
    <input type="submit" value=" Uitvoeren">
<?

//    if ( count($_SESSION["rekeningAddArray"]) >0 )
//    {
//     
//      $rac = new rekeningAddStamgegevens($_SESSION["VB"],"BIN");
//      $rac->getStyles();
//      
//      $rekArray = $_SESSION["rekeningAddArray"];
//      for ($rNdx=0; $rNdx < count($rekArray); $rNdx++)
//      {
//         $rac->makeInputRow($rekArray[$rNdx]);
//      }
//      echo $rac->getHTML();
//    }  

//    <input type="hidden" name="addRekening" id="addRekening" value="0">
?>            
    <br/> 
    
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





$prb->max = $csvRegels;

$prb->setLabelValue('txt1','Converteren records ('.$csvRegels.' regel)');

//$mt940_skip = Array("KOOP","VERKOOP","DIV","COUPON");
$mt940_skip = Array("KOOP ","VERKOOP ","DIV","COUPON","ANN.KOOP","ANN.VERKOOP","LOSSING","OPHEFFING  ");
$content = array();
echo template("../".$__appvar["templateContentHeader"],$content);



$dataSet = Array();
echo "<hr/>Verwerken van $file <br/>";
$volgnr = 1;

$row = 1;
$handle = fopen($file, "r");
$ndx=0;

$row = Array();
$_tempRow = Array();
$regtel = 0;

//
// lees alle bekende records in array
//

while ($data = fgets($handle, 4096))
{
  if (trim($data) == "")
  {
    continue;
  }
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

$fase1Tel = count($dataSet);


if (count($meldArray) > 0)
{
  listarray($meldArray);
}

$meldarray = array();

if ($_GET["abn1"] <> "1")
{
  /// GELD bestand
  $file = $file2;
  echo "<hr/>Verwerken van $file <br/>";
  $volgnr = 1;

  $row = 1;
  $handle = fopen($file, "r");


  $row = Array();
  $_tempRow = Array();
  $regtel = 0;

  //
  // lees alle bekende records in array
  //

  while ($data = fgets($handle, 4096))
  {
    if (trim($data) == "")
    {
      continue;
    }
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
  //
  //
  //  alle ongeldige records weggooien
  //
  $fase2Tel = count($dataSet) - $fase1Tel;

  if (count($meldArray) > 0)
  {
    listarray($meldArray);
  }

  $meldarray = array();
}


?>
  

<br/>

<?


$skipMeldingen = array();

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
          //$mt940_skip = Array("KOOP ","VERKOOP ","DIV","COUPON","ANN.KOOP","ANN.VERKOOP","LOSSING","OPHEFFING  ");
		  		for ($xx=0; $xx < count($mt940_skip);$xx++)
		    	{
		    		$arrValue = $mt940_skip[$xx];
		  	  	if (substr(strtoupper($_r[1]),0,strlen($arrValue)) == $arrValue)
		  	  	{
		  	  	  array_pop($_tmprows);  // verwijder 86 regel
		  	  	  array_pop($_tmprows);  // verwijder vorige 61 regel
		  	  	  $addRecord--;
		  	  		$skipTag = true;
		  	  		$mt940_83_skip++;
		  	  	}
		    	}
          
          if (strstr(strtoupper($_r[1]),"UITKERING VAN") AND  strstr(strtoupper($_r[1]),"DIV.BEL"))
          {
            array_pop($_tmprows);  // verwijder 83 regel
		  	  	array_pop($_tmprows);  // verwijder vorige 61 regel
		  	  	$addRecord--;
            $skipTag = true;
		  	  	$mt940_83_skip++;
          }
		    	if ($skipTag)
          {
		    	  $mt940_rec[] = "[SKIP]  ".str_replace(chr(13),"#",$_r[1]);
            $skipMeldingen[] = "[SKIP]  ".str_replace(chr(13),"#",$_r[1]);
          }  
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
if (count($skipMeldingen) > 0)
{
  //echo "<button id='skipToggle'> Laat ".count($skipMeldingen)." overgeslagen mutaties zien </button><div >";
  echo "<button id='skipToggle'> Laat ".count($skipMeldingen)." overgeslagen mutaties zien </button><div id='skipScreen'>";
  for ($xx=0; $xx < count($skipMeldingen); $xx++ )
  {
    echo "<li>".$skipMeldingen[$xx]."</li>";
  }
  echo "</div><br /><br />";
?>
 <script>
    $(document).ready(function()
    {

      $("#skipToggle").click(function ()
      {
        $("#skipScreen").toggle(400);
      });
    });
 </script>
<?
}
//listarray($dataSet554);
$skipToAddRekening = false;
if (count($dataSet554) > 0) // er bestaand MT554 mutaties
{
  
  validate554RekeningNrs($dataSet554);
  echo "<hr/>Gevonden vermogensbeheerder $VB <br/>";
  //debug($rekeningAddArray);
  if (count($rekeningAddArray) > 0)  
  {
    $skipToAddRekening = false;
    //$skipToAddRekening = true;
  }
    
    
  // inlezen AAB transactie lijst
  if (!$skipToAddRekening)
  {
    $query = "SELECT * FROM AABTransaktieCodes";
    $DB->SQL($query);
    $DB->Query();
    while ($fillRec = $DB->nextRecord())
    {
      $transAct[$fillRec["code"]] = $fillRec["actie"];
      $transOms[$fillRec["code"]] = $fillRec["toelichting"];
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
            if ($do_func == "do_niets")
            {
              $msg[] = "554: Transactie met ".$_velden[0]." (".$transOms[$_velden[0]].") overgeslagen";
            }
            else
            {
              if ( function_exists($do_func) )
              {
                //$logje[]=$_velden[0]." - ".$do_func;
                $rec["bankTransactieCode"] = $_velden[0];
                call_user_func($do_func,convertRecord($rec));
              }
            }
            
          }
          else
          {
            if (is_numeric($_velden[0]))   // als er een transactietype is in errorlog opnemen
              $error[] = "554: Transactietype ".$_velden[0]." komt niet voor";
          }

        }
      }
    }
  }
}

if (count($dataSet940) <> 0) // er bestaand MT940 mutaties
{

  validate940RekeningNrs($dataSet940);
// call 5486
  $zv->VB = $VB;
  $zv->getRules();

  if (count($rekeningAddArray) > 0)  
  {
    $skipToAddRekening = false;
    //$skipToAddRekening = true;
    
  }
  if (!$skipToAddRekening)
  {
    for ($_ndx = 0; $_ndx < count($dataSet940);$_ndx++)
    {
      do_mt940($dataSet940[$_ndx],$_ndx);
    }
  }
	
}


$_SESSION["VB"] = $VB;
$_SESSION["rekeningAddArray"] = $rekeningAddArray;



if (!$skipToAddRekening)
{
  //
  // plaats output in TijdelijkeRekeningmutaties table
  //
  // call 5486 aanmaken extra veld in table
  include_once("../classes/AE_cls_SQLman.php");
  $tst = new SQLman();
  $tst->changeField("TijdelijkeRekeningmutaties","OmschrijvingOrg",array("Type"=>" text","Null"=>false));

  $prb->moveStep(0);

  $prb->max = count($output);
  $pro_step = 0;
  $prb->setLabelValue('txt1','Opslaan in tijdelijke tabel ('.$prb->max.') records');
  //$output = array();
  include_once("checkForDoubleImport.php");
  reset($output);
  $afbreken = false;
  for ($ndx=0;$ndx < count($output);$ndx++)
  {
    if ($ndx == 0)
    {
      if (checkForDoubleImport($output[$ndx]))
      {
        $afbreken = true;
        $prb->hide();
        Echo "<hr><h1> FOUT: De eerste transactieregel komt exact overeen met reeds aanwezige informatie</h1>";
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
    $_query .= "$sep TijdelijkeRekeningmutaties.$key = '".mysql_escape_string(trim($value))."'
    ";
     $sep = ",";
    }
    $_query .= ", add_date = NOW()";
    $_query .= ", add_user = '".$USR."'";
    $_query .= ", change_date = NOW()";
    $_query .= ", change_user = '".$USR."'";

  //listarray($_query);
    //debug($_query);
    $DB->SQL($_query);
    if (!$DB->Query())
    {
      echo mysql_error();
      Echo "<br> FOUT bij het wegschrijven naar de database!";
      exit();
    }
  }
}

$prb->hide();
if (count($msg) > 0 )
{
?>
  <table border="1" cellpadding="5" cellspacing="0">
<tr>
  <td colspan="2" bgcolor="#DDD">
  <b>meldingen tijdens inlezen</b>
  </td>
</tr>
<?
	for ($x=0 ; $x < count($msg); $x++)
	{
?>
<tr>
  <td><?=$x?></td>
  <td><?=$msg[$x]?></td>
</tr>
<?
	}
?>
</table>
<br>
	<br>
<?
}


if (count($error) > 0 OR count($_SESSION["rekeningAddArray"]) > 0)
{
?>
<table border="1" cellpadding="5" cellspacing="0">
<tr>
  <td colspan="2" bgcolor="#FAA">
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
<?

?>
	<b>Vervolg aktie?</b>
	<form action="<?=$PHP_SELF?>" method="POST">
     <div id="kopje"></div>
	  <input type="hidden" name="doIt" value="1">
  	<input type="hidden" name="bestand" value="<?=$file?>">
  	<input type="hidden" name="foutregels" value="<?=$foutregels?>">
  	<select name="action" id="frmAction">
    	<option value="stop">Bestand verwijderen en import afbreken</option>
<?
if( !$afbreken)
{
  ?>
      <option value="go">Bestand inlezen en onvolledige regels overslaan</option>
      <?
}
    	?>
  	</select>
    <input type="submit" value=" Uitvoeren">
<?
//  if ( count($_SESSION["rekeningAddArray"]) >0 )
//    {
//     
//      $rac = new rekeningAddStamgegevens($_SESSION["VB"],"AAB");
//      $rac->getStyles();
//      
//      $rekArray = $_SESSION["rekeningAddArray"];
//      for ($rNdx=0; $rNdx < count($rekArray); $rNdx++)
//      {
//         $rac->makeInputRow($rekArray[$rNdx]);
//      }
//      echo $rac->getHTML();
//    } 
?>  
    <!--<input type="hidden" name="addRekening" id="addRekening" value="0">-->
    
  	<br/>
    
	</form>
  <script>
    $(document).ready(function(){
      
//      $("#skipToggle").click(function()
//      {
//        $("#skipScreen").toggle(400);
//      });
      
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
     
//debug($_SESSION["rekeningAddArray"]);
  $url = "";
}
else
{
  
?>
  totaal aangemaakte mutatieregels : <?=count($output)?><br/>
  <b>Klaar met inlezen <br></b>

<br/>

<? 

if ($afbreken)
{
  ?>
	<b>Vervolg aktie?</b>
	<form action="<?=$PHP_SELF?>" method="POST">
     <div id="kopje"></div>
	  <input type="hidden" name="doIt" value="1">
  	<input type="hidden" name="bestand" value="<?=$file?>">
  	<input type="hidden" name="foutregels" value="<?=$foutregels?>">
  	<select name="action" id="frmAction">
    	<option value="stop">Bestand verwijderen en import afbreken</option>
  	</select>
    <input type="submit" value=" Uitvoeren">
<?    
  exit;
}
else
{
?>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<?
}

  
  
}
listarray($meldArray);


echo template("../".$__appvar["templateRefreshFooter"],$content);
?>