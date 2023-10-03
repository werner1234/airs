<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/01/16 13:18:55 $
 		File Versie					: $Revision: 1.5 $

 		$Log: dividendMutatieSelectie.php,v $
 		Revision 1.5  2019/01/16 13:18:55  cvs
 		call 7474
 		
 		Revision 1.4  2017/10/25 14:28:38  cvs
 		call 6253
 		
 		Revision 1.3  2014/12/24 09:54:51  cvs
 		call 3105
 		
 		Revision 1.2  2014/10/01 13:32:12  cvs
 		dbs 2877
 		
 		Revision 1.1  2014/03/10 09:59:00  cvs
 		*** empty log message ***
 		

*/


include_once("wwwvars.php");
include_once("../classes/AE_cls_progressbar.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");


if ($_GET["delTemp"] == 1)
{
  $DB = new DB();
  $query = "DELETE FROM TijdelijkeRekeningmutaties WHERE add_user = '$USR' ";
	$DB->executeQuery($query);
}



$content["calendarinclude"] = "
  <script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>
  <script language=JavaScript src=\"javascript/selectbox.js\" type=text/javascript></script>";

$kal = new DHTML_Calendar();
$content["calendar"] = $kal->get_load_files_code();
$content["style2"] .= ' <link type="text/css" href="style/jquery.css" rel="stylesheet" />';
echo template($__appvar["templateContentHeader"],$content);
flush();
?>
<style>

.ar{ text-align: right;}

h2{ margin-top: 2em;}
.table{ border: 1px solid black;}

.table tr{
  border-left: 1px solid #333;
  border-right: 1px solid #333;

}
.table tr:nth-child(even) {background: #EEE}
.table tr:nth-child(odd) {background: #FFF}

.kopRow td{
  background: #333;
  color: #eee;
}
/*.dataRow1 td{color:black;}*/
/*.dataRow2 td{color:darkolivegreen;}*/
/*.dataRow3 td{color:darkred;}*/
/*.dataRow4 td{color:darkslateblue;}*/
/*.dataRow5 td{color:darkslategrey;}*/
/*.dataRow6 td{color:navy;}*/
.tdHead{
  background: #666;
  color:white;
  font-size: 1em;
  font-weight: bold;
  text-align: left;
  padding:4px;
}
.table td{
  padding:4px;
}
.ui-autocomplete {
  max-height: 350px;
  width: 450px;
}
li{
  font-size:.75em;
}
#loading{
  margin-top:25px;
  padding:  15px;
  background: white;
  width: 400px;
  height: 55px;
  border:  1px solid #999;
  border-radius: 6px;
  font-size: 1.2em;
}
</style>
<?
if($_POST['posted'])
{
  ?>
  <div id="loading" >

    <img src="images/loading.gif" style="vertical-align:middle" />&nbsp;&nbsp;&nbsp; Bezig met ophalen van de gegevens..

  </div>
  <?
  flush();
  ob_flush();
  $dataArray  = array();
  $dataSet = array();
  $fondsSet = array();
  include_once("rapport/rapportRekenClass.php");

  if ($_POST["viaFile"] == "1")
  {

    foreach ($_POST as $key=>$value)
    {
      $exp = explode("_", $key);
      if ($exp[0] == "chk")
      {
        $d = $_SESSION["divMutDataSet"][$exp[1]];
        $dataSet[] = array(
          'positieDatum'      => $d[5],
          'boekDatum'         => $d[4],
          'fonds'             => $d[0],
          'dividendKenmerk'   => $d[8],
          'divendBedrag'      => str_replace(",",".",$d[6]),
          'percentBelasting'  => str_replace(",",".",$d[7]),
          'valuta'            => '',
        );
        $fondsSet[] = $d[0];
      }
    }

  }
  else
  {
    $dataSet[] = $_POST;
  }

	$start = getmicrotime();

  foreach ($dataSet as $_POST)
  {
    if(!empty($_POST['boekDatum']))
	  {
		  $dd = explode($__appvar["date_seperator"],$_POST['boekDatum']);
		  if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
		  {
			  echo "<b>Fout: ongeldige datum opgegeven!</b>";
			  exit;
		  }
	  }
	  else
	  {
		  echo "<b>Fout: geen datum opgegeven!</b>";
		  exit;
	  }



    $db         = new DB();
    $db1        = new DB();

    $query      = "SELECT * FROM Fondsen WHERE Fonds = '".$_POST["fonds"]."'";
    $fondRec    = $db->lookupRecordByQuery($query);

    $query = "
      SELECT
        Portefeuilles.Portefeuille,
        Portefeuilles.Vermogensbeheerder,
        Portefeuilles.Client,
        ModelPortefeuilles.Portefeuille as ModelPortefeuille,
        ModelPortefeuilles.VerwerkingsmethodeDiv
      FROM
        Portefeuilles
      INNER JOIN ModelPortefeuilles ON Portefeuilles.Portefeuille = ModelPortefeuilles.Portefeuille
      WHERE 
        InternDepot = 1 
      AND
      (
        ModelPortefeuilles.VerwerkingsmethodeDiv <> 2 AND   ModelPortefeuilles.Fixed = 0
      )
      ORDER BY Portefeuilles.Portefeuille
    ";

    $db->executeQuery($query);
    $belastingFactor = $_POST["percentBelasting"]/100;
    if ($_POST["valuta"] <> "")
    {
      $valuta = $_POST["valuta"];
      $valutaText = "Afwijkend dividend valuta geselecteerd $valuta, (fonds valuta: ".$fondRec["Valuta"].") ";
    }
    else
    {
      $valuta = $fondRec["Valuta"];
      $valutaText = "";
    }

    while ($portRec = $db->nextRecord())
    {

      $stukken = getAantalStukken($portRec["Portefeuille"],$_POST["fonds"],$_POST["positieDatum"]);

      if ($stukken["totaalAantal"] > 0 )
      {
        $query = "SELECT * FROM Rekeningen WHERE Portefeuille = '".$portRec["Portefeuille"]."' AND Valuta = 'EUR' AND Memoriaal = 0 AND Inactief = 0 ORDER BY id";
        $rekeningRec = $db1->lookupRecordByQuery($query);
        if ($valuta <> "EUR")
        {
          $query    = "SELECT * FROM `Valutakoersen` WHERE Valuta = '".$valuta."' AND Datum <= '".formdate2db($_POST["boekDatum"])."' ORDER BY Datum DESC";
          $koersRec = $db1->lookupRecordByQuery($query);
          $koers    = $koersRec["Koers"];
        }
        else
        {
          $koers = 1;
        }

        $portRec["aantal"]            = $stukken["totaalAantal"];
        $portRec["brutoDividend"]     = $portRec["aantal"] * $_POST["divendBedrag"];
        $portRec["belasting"]         = round($belastingFactor * $portRec["brutoDividend"],2);
        $portRec["nettoDividend"]     = $portRec["brutoDividend"] - $portRec["belasting"];
        $portRec["dividendValuta"]    = $valuta;
        $portRec["fondsOmschrijving"] = $fondRec["Omschrijving"];
        $portRec["fonds"]             = $fondRec["Fonds"];
        $portRec["rekening"]          = $rekeningRec["Rekening"];
        $portRec["boekdatum"]         = formdate2db($_POST["boekDatum"]);
        $portRec["valutaKoers"]       = $koers;
        $portRec["fondsSoort"]        = $fondRec["fondssoort"];
        $portRec["dividendKenmerk"]   = $_POST["dividendKenmerk"];

        $dataArray[] = $portRec;
      }
    }
  }

  if (count($dataArray) == 1)
  {
?>
  <h2>Dividend mutaties voor <?=$_POST["fonds"]?></h2>
  <div>
    Dividend belasting: <strong><?=number_format($_POST["percentBelasting"],2)?>%</strong><br/>
    Dividend / stuk: <strong><?=number_format($_POST["divendBedrag"],2)?></strong><br/>
    <span style="color: red; font-weight: bold;"><?=$valutaText?></span>
  </div>
<?php
  }
  else
  {
?>
    <h2>Dividend mutaties voor meerdere fondsen:<br/><li><?=implode("<li>", $fondsSet)?></h2>
<?php
  }
?>

<br />
<form name='editForm' id="editForm" method="post" action='dividendMutatie.php' target="content">
  <input type="hidden" name="action" value="1" />

<table class="table">
<tr class="kopRow">
  <td><input type="checkbox" id="checkAll"/></td>
  <td>fonds</td>
  <td>portefeuille</td>
  <td>VB</td>
  <td>client</td>
  <td>aantal</td>
  <td>brutoDividend</td>
  <td>belasting</td>
  <td>nettoDividend</td>
  <td>valuta</td>
  <td>rekening</td>
  <td>divKenmerk</td>
</tr>
<?
$template = '
<tr class="dataRow{x}">
  <td><input type="checkbox" name="chk_{Portefeuille2}" class="check"></td>

  <td>{fonds}</td>
  <td>{Portefeuille}</td>
  <td>{Vermogensbeheerder}</td>
  <td>{Client}</td>
  <td class="ar">{aantal}</td>
  <td class="ar">{brutoDividend}</td>
  <td class="ar">{belasting}</td>
  <td class="ar">{nettoDividend}</td>
  <td class="ac">{dividendValuta}</td>
  <td class="al">{rekening}</td>
  <td class="al">{dividendKenmerk}</td>
</tr>
';
//debug($template);

  $_SESSION["dividendData"] = $dataArray;
  $x = 0;
  $prevFonds = "";
  foreach($dataArray as $inputRow)
  {
    if ($prevFonds != $inputRow["fonds"])
    {
      $prevFonds = $inputRow["fonds"];
      $x++;
      if ($x == 7) {$x = 1;}
      echo "<tr><td colspan='12' class='tdHead'>{$inputRow["fonds"]}</td>";
    }

    $inputRow["x"] = $x;
    $inputRow["brutoDividend"] = number_format($inputRow["brutoDividend"],2);
    if ($inputRow["VerwerkingsmethodeDiv"] == 1)
    {
      $inputRow["belasting"] = "<b><span style='color:red;'>exclusief</span></b>";
    }
    else
    {
      $inputRow["belasting"] = number_format($inputRow["belasting"],2);  
    }
    $inputRow["nettoDividend"] = number_format($inputRow["nettoDividend"],2);
    $inputRow["Portefeuille2"] = str_replace(" ","~",$inputRow["Portefeuille"]);

    echo templateStr($template,$inputRow);
  }

?>
<tr class="kopRow">
  <td colspan="12" >
    <input type="submit" value="verwerk" />
  </td>
</tr>
</table>
</form>
<script>
  $(document).ready(function(){
    $("#loading").hide(200);
    $('#checkAll').click(function()
    {
        $("input.check:checkbox").attr('checked', $('#checkAll').is(':checked'));
        berekenSaldo()
    });


  });

</script>
<?
  echo template($__appvar["templateRefreshFooter"],$content);
	exit;
}
else
{
	// selecteer laatst bekende valutadatum
	$totdatum = getLaatsteValutadatum();
  $jr = substr($totdatum,0,4);


	$_SESSION[NAV] = "";
	$_SESSION[submenu] = New Submenu();

  $DB = new DB();
  if ($DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE TijdelijkeRekeningmutaties.change_user = '$USR' ") > 0 AND $_GET["delTemp"] <> 2)
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
#rentePeriode{
  color: Red;
}

</style>
<div class="fout">
 <?= vt('Tijdelijke rekeningmutaties gevonden voor'); ?> <?=$USR?><br/><br />

 <a href="<?=$PHP_SELF?>?delTemp=1"><button> <?= vt('verwijder tijdelijke rekeningmutaties'); ?> </button></a>
 <a href="<?=$PHP_SELF?>?delTemp=2"><button> <?= vt('tijdelijke rekeningmutaties aanvullen'); ?> </button></a>
</div>
<?
	exit;
}


	?>

<style>
.ui-widget-header{
  background: #AAA;
}
.ui-datepicker{
  display: none;
}
</style>





<br>
<b><?= vt('Dividend mutaties genereren'); ?></b>
<br>
<br>
  <a  href="dividendMutatieViaFile.php"><button> Via bestand inlezen </button></a>

  <form action="<?=$PHP_SELF?>" method="POST" target="content" name="selectForm" >
    <input type="hidden" name="posted" value="true" />
<table border="0">
<tr>
	<td width="540">
<iframe width="538" height="15" name="generateFrame" frameborder="0" scrolling="No" marginwidth="0" marginheight="0"></iframe>
<fieldset id="Periode" >

<div class="formblock">
  <div class="formlinks"> <?= vt('positiedatum'); ?> </div>
  <div class="formrechts">
    <input name="positieDatum" id="positieDatum" size="11" class="datepicker" value="<?=date("d-m-Y")?>"/>
  </div>
</div>

<div class="formblock">
  <div class="formlinks"> <?= vt('boekdatum'); ?> </div>
  <div class="formrechts">
    <input name="boekDatum" id="boekDatum" size="11" class="datepicker" value="<?=date("d-m-Y")?>"/>
  </div>
</div>



<div class="formblock">
  <div class="formlinks"> <?= vt('fonds'); ?> </div>
  <div class="formrechts">
    <input id="Fonds" name="fonds" size="40" value=""/>
  </div>
</div>

<div class="formblock">
  <div class="formlinks"> dividend kenmerk </div>
  <div class="formrechts">
    <input id="Fonds" name="dividendKenmerk" value="" size="25" />
  </div>
</div>

<div class="formblock">
  <div class="formlinks"> dividendbedrag / stuk </div>
  <div class="formrechts">
    <input id="Fonds" name="divendBedrag" value="0" size="7" />
    &nbsp;&nbsp;&nbsp;&nbsp;<span id="rentePeriode" style="color: red"></span>
  </div>
</div>

<div class="formblock">
  <div class="formlinks"> <?= vt('percentage dividend belasting'); ?> </div>
  <div class="formrechts">
    <input id="Fonds" name="percentBelasting" value="15" size="4" />%
  </div>
</div>

<div class="formblock">
  <div class="formlinks"> <?= vt('afwijkend dividend valuta'); ?></div>
  <div class="formrechts">
    <select name="valuta">
      <option value=""><?= vt('oorsprongelijke dividend valuta'); ?></option>
      <optgroup>
<?
  $db = new DB();
  $query = "SELECT * FROM Valutas ORDER BY Valuta";
  $DB->executeQuery($query);
  while ($valRec = $DB->nextRecord())
  {
    echo "\n        <option value='".$valRec["Valuta"]."'>".$valRec["Valuta"]." &nbsp;&raquo;&nbsp;".$valRec["Omschrijving"]."</option>";
  }

?>      </optgroup>
    </select>
    
  </div>
</div>




</fieldset>

</div>
<input  type="submit" value="zoek bijbehorende posities"/>
</td>
</tr>
</table>


</form>

<script>
  $(document).ready(function(){

    $("#boekDatum").change(function(){
      checkDates();
    });
    $("#positieDatum").change(function(){
      checkDates();
    });

    $("#Fonds").autocomplete(
    {

      source : "lookups/getFonds.php", // link naar lookup script
      create : function(event, ui)// onCreate sla oude waardes op om te kunnen resetten in onClose bij geen selectie
      {

      },
      close : function(event, ui)// controle of ID gevuld is anders reset naar onCreate waarden
      {
      },
      search : function(event, ui)// als zoeken gestart het ID veld leegmaken
      {
      },
      select : function(event, ui)// bij selectie clientside vars updaten
      {
        $("#Fonds").val(ui.item.Fonds);
        if (ui.item.RentePeriodeCalc != "")
        {
          $("#rentePeriode").html("<b> Renteper.:" + ui.item.RentePeriodeCalc +"</b>");
        }
        else
        {
          $("#rentePeriode").html("");
        }




      },
      //autoFocus: true,
      minLength : 2, // pas na de tweede letter starten met zoeken
      delay : 0

    });

  $( ".datepicker" ).datepicker({
  			showOn: "button",
  			buttonImage: "images/16/agenda.gif",
  			buttonImageOnly: true,
        dateFormat: "dd-mm-yy",
        dayNamesMin: ["Zo", "Ma", "Di", "Wo", "Do", "Vr", "Za"],
        monthNames: ["januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december"],
        nextText: "volgende maand",
        prevText: "vorige maand",
        currentText: "huidige maand",
        closeText: "sluiten",
        showAnim: "slideDown",
        showButtonPanel: true,
        showOtherMonths: true,
  			selectOtherMonths: true,
        numberOfMonths: 1,
  			showWeek: true,
  			firstDay: 1
    });
 });

function checkDates()
{
  var boek    = $("#boekDatum").val().split("-");
  var bd = new Date(boek[2], boek[1]-1, boek[0]).getTime();
  var positie = $("#positieDatum").val().split("-");
  var pd = new Date(positie[2], positie[1]-1, positie[0]).getTime();

  if (pd > bd)
  {
    alert("boekdatum eerder dan positiedatum!");
    $("#boekDatum").val( $("#positieDatum").val()).select().focus();
  }
}


</script>

<script type="text/javascript">
selectTab();
</script>
	<?
	if($__debug) {
		echo getdebuginfo();
	}
	echo template($__appvar["templateRefreshFooter"],$content);
}


function getAantalStukken($portefeuille,$fonds,$rapportageDatum)
{
  $parts = explode("-",$rapportageDatum);
  $jaar = $parts[2];
  $dbdate = $parts[2]."-".$parts[1]."-".$parts[0];
  $qMutaties = "SELECT SUM(Rekeningmutaties.Aantal) AS totaalAantal, ".
	" Fondsen.Renteperiode, ".
	" Fondsen.EersteRentedatum, ".
	" Fondsen.Rentedatum, ".
	" Fondsen.Fondseenheid, ".
	" Fondsen.Valuta, ".
	" Fondsen.Fonds, ".
	" Fondsen.EindDatum, ".
	" Fondsen.Omschrijving AS FondsOmschrijving ".
	" FROM Rekeningmutaties, ".
	" Rekeningen, Fondsen, Portefeuilles
	  $join
	  WHERE Portefeuilles.Portefeuille ='".$portefeuille."' AND ".
	" Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	" Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
	" Fondsen.Fonds = Rekeningmutaties.Fonds AND ".
	" Rekeningmutaties.Fonds = '".$fonds."' AND ".
	" Rekeningmutaties.GrootboekRekening = 'FONDS' AND".
	" YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND ".
	" Rekeningmutaties.Verwerkt = '1' AND ".
	" Rekeningmutaties.Boekdatum <= '".$dbdate."' ".
	" GROUP BY Rekeningmutaties.Fonds ";
  $db = new DB();
  $db->executeQuery($qMutaties);
  $rec = $db->lookupRecord();
  return $rec;
}


function TemplateStr($template,$objectData)
{
  $data = $template;

	foreach ($objectData as $key=>$val)
	{

	    $data = str_replace( "{".$key."}", $val, $data);


 	}


  $data = eregi_replace( "\{[a-zA-Z0-9_-]+\}", "", $data);
  return $data;
}


?>