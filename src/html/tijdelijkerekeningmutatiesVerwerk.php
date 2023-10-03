<?php
/*
  Author  						: $Author: cvs $
  Laatste aanpassing	: $Date: 2020/01/24 10:14:58 $
  File Versie					: $Revision: 1.28 $

  $log: tijdelijkerekeningmutatiesVerwerk.php,v $




 */
// set module naam voor autenticatie leeg = iedereen.
$__appvar["module"] = "";
// SET in wwwvars ie:  $__appvar["userLevel"] = _READ;
// include wwwvars
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');
include_once "../classes/AE_cls_updateFondskoers.php";
//debug($_SESSION["importFondsKoersen"]));
$koersParse = array();
if ($__appvar["bedrijf"] == "HOME")
{
  $fndkrs = new AE_cls_updateFondskoers(false);
  $fndkrs->loadFromTRM();
  $koersParse = $fndkrs->parseResults();
}



$_SESSION["importFondsKoersenForm"] = $koersParse;
//$fndkrs->showData();
//debug($_SESSION["importFondsKoersenForm"]);
$koersAdded = 0;
$tmpl       = new AE_template();
$template = <<<EOB
<tr>
  <td>
    <input type="checkbox" name="chk_{id}" id="chk_{id}" class="vink"/>
    <input type="hidden" name="new_{id}" value="{new}"/>
  </td>
  <td>{fonds}</td>
  <td class="ac">{laatsteDatum} ({airsKoers})</td>
  <td class="ac">{datum}</td>
  <td class="ac">{dagen}</tdcl>
  <td class="ar">{koers}</td>
</tr>
EOB;

$tmpl->loadTemplateFromString($template, "dataRow");

$DB = new DB();
$meldArray = array();
session_start();
$_SESSION["submenu"] = "";
//clear navigatie
$_SESSION["NAV"] = "";
session_write_close();

$content = array();
$content['jsincludes'] .= '<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">';
$content['jsincludes'] .= "<script type='text/javascript'src='javascript/jquery-min.js'</script>";
$content['jsincludes'] .= "<script type='text/javascript'src='javascript/jquery-ui-min.js'</script>";
echo template($__appvar["templateContentHeader"], $content);
?>


<style>
  #melding{
    color: maroon;
    font-weight: normal;
    border: 1px solid red;
    width: 50%;
    padding: 10px;
  }

  #koersImport{

  }
  td{
    padding:4px 15px;
  }
  .ar{ text-align: right}
  .ac{ text-align: center}
  thead tr td{
    background: #0E3460;
    color: whitesmoke;
  }
  tbody tr:nth-child(even) { background: whitesmoke; }
  fieldset{
    margin-top:2em;
    width: fit-content;
    padding: .5em 2em;
  }
  legend{
    padding: 4px 15px;
    background: #0E3460;
    color: whitesmoke;
    font-size: 14px;
  }
  button,
  button a{
    margin-top: 1em;
    padding: 10px 15px 10px 15px;
    background: #0E3460;
    color: white!important;
    border: 0px;
    cursor: pointer;
  }
  .btnBar{
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .msgSelected{
    font-weight: 500;
    font-size: 14px;
  }
  .koersDone{
    width: 600px;
    padding: 1em;
    text-align: center;
    font-weight: bold;
    background: #0E3460;
    color:whitesmoke;
  }
</style>

<?
if ($_POST["portStartFill"] == 1)
{
  foreach ($_POST as $key=> $value)
  {
    if (substr($key, 0, 5) == "date_")
    {
      $port = substr($key,5);
      $parts = explode("-", $value);

      if (checkdate($parts[1], $parts[0], $parts[2]))
      {

        $query = "SELECT id, Startdatum FROM Portefeuilles WHERE Portefeuille = '".$port."'";

        $oldRec = $DB->lookupRecordByQuery($query);
        $nwValue = $parts[2]."-".$parts[1]."-".$parts[0];
        $query = "UPDATE Portefeuilles SET Startdatum = '{$nwValue}', change_date = NOW(), change_user='$USR' WHERE Portefeuille = '".$port."'";
        $DB->executeQuery($query);
        addTrackAndTrace("Portefeuilles", $oldRec["id"], "Startdatum", $oldRec["Startdatum"], $nwValue, $USR);
      }

    }
  }
}

if ($_POST["koersImport"] == 1)
{
  if ($_POST["skipKoersImport"] != "1")
  {
    $data = array();
    foreach ($_SESSION["importFondsKoersenForm"] as $item)
    {
      $data[$item["id"]] = $item;
    }
    $db = new DB();
//  debug($data);

    foreach ($_POST as $key=>$value)
    {

      $parts = explode("_", $key);
      if ($parts[0] == "chk")
      {
        $d = $data[$parts[1]];
        $dp = explode("-",$d["datum"]);
        $datum = "{$dp[2]}-{$dp[1]}-{$dp[0]}";
        //debug($d);
        $koers = str_replace(",","", $d["koers"]);
        $query = "INSERT INTO `Fondskoersen` SET
      `add_date`    = NOW(),
      `add_user`    = '{$USR}',
      `change_date` = NOW(),
      `change_user` = '{$USR}',
      `Datum`       = '{$datum}',
      `oorspKrsDt`  = '{$datum}',
      `Fonds`       = '{$d["fonds"]}',
      `Koers`       = {$koers},
      `import`      = 1
      ";
        if ($d["new"] == 1)
        {
          //debug($d, "DIT FONDS MOET NOG AANGEMAAKT WORDEN");
        }
        //aetodo: call 7725 bij een nieuwe moet het fonds nog aangemaakt worden!!
        if ($db->executeQuery($query))
        {
          $koersAdded++;
        }
      }
    }
    if ($koersAdded > 0)
    {
      unset($_SESSION["importFondsKoersen"]);
      unset($_SESSION["importFondsKoersenForm"]);
      $koersParse = array();
    }
  }
  else
  {
    $koersParse = array();
  }



}

if ($_POST["doIt"] == 1)
{

  $dd = explode($__appvar["date_seperator"], $_POST['datum']);

  if (checkdate(intval($dd[1]), intval($dd[0]), intval($dd[2])))
  {
    $selectieWhere = "";
    $verwerkSelectie = false;
    if ($_POST["vinkIds"] != "")  //selectie ophalen
    {
      $verwerkSelectie = true;
      $dbi = new DB();
      $query = "SELECT bankTransactieId FROM TijdelijkeRekeningmutaties WHERE id IN (".$_POST["vinkIds"].")";
      $dbi->executeQuery($query);
      while ($rec = $dbi->nextRecord())
      {
        $btId[] = $rec["bankTransactieId"];
      }
      $selectieWhere = " AND `bankTransactieId` IN ('".implode("','",$btId)."') ";
      unset($btId);
    }

    $dat = form2jul($_POST['datum']);
    $jaar = date("Y", $dat);

    $prb = new ProgressBar(); // create new ProgressBar
    $prb->pedding = 2; // Bar Pedding
    $prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040"; // Bar Border Color
    $prb->setFrame();                           // set ProgressBar Frame
    $prb->frame['left'] = 50;                   // Frame position from left
    $prb->frame['top'] = 80;                   // Frame position from top
    $prb->addLabel('text', 'txt1', 'Bezig ...'); // add Text as Label 'txt1' and value 'Please wait'
    $prb->addLabel('procent', 'pct1');           // add Percent as Label 'pct1'
    $prb->show();                               // show the ProgressBar

    $prb->moveStep(0);
    $prb->setLabelValue('txt1', 'Portefeuille controle');
    $pro_step = 0;



    $query = "SELECT MAX(date(Boekdatum)) AS Datum FROM TijdelijkeRekeningmutaties WHERE TijdelijkeRekeningmutaties.change_user = '$USR' $selectieWhere";
    $DB->SQL($query);
    $datum = $DB->lookupRecord();
    $julLaatsteMutatie = db2jul($datum['Datum']);
    if ($dat < $julLaatsteMutatie)
    {
      echo "De opgegeven afschfriftdatum (".$_POST['datum'].") ligt voor de boekdatum van de mutaties (".date("d-m-Y", $julLaatsteMutatie)."). Verwerking afgebroken.";
      $prb->hide();
      exit;
    }
    

/////////////////////
    $query = "
    SELECT
      Min(date(TijdelijkeRekeningmutaties.Boekdatum)) AS Datum,
      Rekeningen.Portefeuille,
      date(Portefeuilles.Startdatum) AS Startdatum
    FROM
      TijdelijkeRekeningmutaties
    INNER JOIN Rekeningen ON 
      TijdelijkeRekeningmutaties.Rekening = Rekeningen.Rekening
    INNER JOIN Portefeuilles ON 
      Rekeningen.Portefeuille = Portefeuilles.Portefeuille
    WHERE 
      TijdelijkeRekeningmutaties.change_user = '".$USR."'
      $selectieWhere
    GROUP BY 
      Rekeningen.Portefeuille";
    
    //$log->add("---",$query,__LINE__);
    $DB->executeQuery($query);
    $startRekeningRec = array();
    while ($sdRec = $DB->nextRecord())
    {
      $sdRec["startDay"] = db2day($sdRec["Startdatum"]);
      $sdRec["boekDay"] = db2day($sdRec["Datum"]);

      if ($sdRec["startDay"] == 0 OR ( $sdRec["boekDay"] < $sdRec["startDay"]))
      {
        $startRekeningRec[] = $sdRec;
      }
      //debug($sdRec);
    }
    if (count($startRekeningRec) > 0 AND $_POST["portStartFill"] <> 1)
    {
      $prb->hide();
      //  debug($startRekeningRec);
      ?>
      <style>
        button,
        button a{
          padding:5px;
          color:white!important;
        }
        .rekTable{
          padding: 2px;
          margin:0;
          margin-top:10px;
          border:2px #333 solid;
        }

        .rekTable .head{ background: #eee;  }
        .rekTable .head td{ background: #eee; padding:5px; font-weight: bold}
        .rekTable .extraRow { background: #ffcc66;  }

        .ac{ text-align: center}
        .ar{ text-align: right}
        .al{ text-align: left}


      </style>
      <br/>
      <br/>

      <h3>Portefeuille startdatums in-/aanvullen</h3>
      <form method="Post">
        <input type="hidden" name="portStartFill" value="1" />
        <table class="rekTable">
          <tr class="head">
            <td>Portefeuille</td>
            <td>huidige Startdatum</td>
            <td>voorstel Startdatum</td>
          </tr>
          <?
          for ($x = 0; $x < count($startRekeningRec); $x++)
          {
            $r = $startRekeningRec[$x];
            ?>
            <tr>
              <td><?= $r["Portefeuille"] ?></td>
              <td><?= ($r["startDay"] == 0) ? "niet ingevuld" : day2db($r["startDay"], "f"); ?></td>
              <td><input style="width: 100px" class="AIRSdatepicker" name="date_<?= $r["Portefeuille"] ?>" value="<?= day2db(($r["boekDay"] - 1), "f") ?>" /></td>
            </tr>
        <?
      }
      ?>

        </table>

        <br/><br/>
        <input type="submit" value=" Startdatums bijwerken, en opnieuw verwerken " />
      </form>
      <?
      echo template($__appvar["templateRefreshFooter"], $content);
      exit;
    }

    duplicaatRekeningVerwerk();

    $prb->setLabelValue('txt1', 'Verwerken tijdelijke tabel');

////////////////////////
    $query = "
    SELECT 
      DISTINCT(TijdelijkeRekeningmutaties.Rekening) AS Rekening, 
      SUM(ROUND(Bedrag,2)) AS Mutatiebedrag
		FROM 
		  TijdelijkeRekeningmutaties
		Inner Join Rekeningen ON 
		  TijdelijkeRekeningmutaties.Rekening = Rekeningen.Rekening
		WHERE 
		  TijdelijkeRekeningmutaties.change_user = '$USR' $selectieWhere 
		GROUP BY 
		  Rekening";

    $DB->SQL($query);
    $DB->Query();

    logit("call 7183: ($USR) ".$query);

    $seenReknrs = array();
    $pro_multiplier = (100 / $DB->Records());

    // call 9013
    if(!$__appvar['master'] AND !$__appvar['automatisch_RM_verwerken'] )
    {
      $verwerkt = 0;
    }
    else
    {
      $verwerkt = 1;
    }


    while ($rekeningdata = $DB->NextRecord())
    {
      //aetodo: array vullen met rekeningnrs en naar ae_log zie call 7183
      $seenReknrs[] = $rekeningdata["Rekening"];
      $pro_step += $pro_multiplier;
      $prb->moveStep($pro_step);

      //echo $rekeningdata['Rekening']." mutatie bedrag ".$rekeningdata['Mutatiebedrag']." <br>";
      // ophalen laatste Rekeningafschrift.
      $DB2 = new DB();
      $query = "SELECT Afschriftnummer, NieuwSaldo AS VorigSaldo FROM Rekeningafschriften WHERE Rekening = '".$rekeningdata['Rekening']."' AND YEAR(Rekeningafschriften.Datum) = '".$jaar."' ORDER BY Afschriftnummer DESC LIMIT 1";
      $DB2->SQL($query);
      $DB2->Query();
      if ($DB2->Records() > 0)
      {
        $afschriftdata = $DB2->NextRecord();
        $afschriftdata[Afschriftnummer]+=1;
      }
      else
      {
        $afschriftdata[Afschriftnummer] = $jaar."001";
        $afschriftdata[VorigSaldo] = 0;
      }

      $nieuwSaldo = $afschriftdata[VorigSaldo] + $rekeningdata['Mutatiebedrag'];

      // Afschriftkop
      //echo "<br><br>";
      $query = "INSERT INTO Rekeningafschriften SET ".
              "Rekening = '".$rekeningdata['Rekening']."', ".
              "Datum = '".jul2sql(form2jul($_POST['datum']))."', ".
              "Afschriftnummer = '".$afschriftdata[Afschriftnummer]."', ".
              "Saldo = '".$afschriftdata[VorigSaldo]."', ".
              "NieuwSaldo = '".round($nieuwSaldo, 2)."', ".
              "Verwerkt = '".$verwerkt."',
							 add_user = '$USR', change_user = '$USR',
							 add_date = NOW(), change_date = NOW() ";

      $DB2->SQL($query);
      if (!$DB2->Query())
      {
        fout("Fout: ".$query);
        exit;
      }
      $afschriftId = $DB2->last_id();
      // insert de Afschriftregels regels.

      $query = "
      SELECT 
        * 
      FROM 
        TijdelijkeRekeningmutaties 
      WHERE 
        Rekening = '".$rekeningdata['Rekening']."' AND 
        change_user = '$USR' 
        $selectieWhere
      ORDER BY 
        Boekdatum, 
        Grootboekrekening";
      $DB2->SQL($query);
      $DB2->Query();
      $mutatieBedrag1 = 0;
      $tel = 0;
      while ($mutatiedata = $DB2->NextRecord())
      {
        $tel++;
        // insert mutatie
        $omsString = str_replace("\\t"," ",mysql_escape_string($mutatiedata[Omschrijving]));
        $omsString = str_replace("\\r"," ",$omsString);
        $omsString = str_replace("\\n"," ",$omsString);
   
        $DB3 = new DB();
        $query = "INSERT INTO Rekeningmutaties SET ".
                " Rekening = '".$rekeningdata['Rekening']."' ".
                ",Afschriftnummer = '".$afschriftdata["Afschriftnummer"]."' ".
                ",Volgnummer = '".$tel."' ".
                ",Omschrijving = '".mysql_real_escape_string($omsString)."' ".
                ",Boekdatum = '".$mutatiedata["Boekdatum"]."' ".
                ",settlementDatum = '".$mutatiedata["settlementDatum"]."' ".
                ",Grootboekrekening = '".$mutatiedata["Grootboekrekening"]."' ".
                ",Valuta = '".$mutatiedata["Valuta"]."' ".
                ",Valutakoers = '".$mutatiedata["Valutakoers"]."' ".
                ",Fonds = '".mysql_escape_string($mutatiedata["Fonds"])."' ".
                ",Aantal = '".$mutatiedata[Aantal]."' ".
                ",Fondskoers = '".$mutatiedata["Fondskoers"]."' ".
                ",Debet = '".round($mutatiedata['Debet'], 2)."' ".
                ",Credit = '".round($mutatiedata['Credit'], 2)."' ".
                ",Bedrag = '".round($mutatiedata['Bedrag'], 2)."' ".
                ",Transactietype = '".$mutatiedata["Transactietype"]."' ".
                ",bankTransactieId = '".$mutatiedata["bankTransactieId"]."' ".
                ",Verwerkt = '".$verwerkt."' ".
                ",orderId = '".$mutatiedata["orderId"]."' ".
                ",Memoriaalboeking = '".$mutatiedata["Memoriaalboeking"]."',
								 add_user = '$USR', change_user = '$USR',
								 add_date = NOW(), change_date = NOW() ";
//debug($query);

        $DB3->SQL($query);
        if (!$DB3->Query())
        {
          fout("Fout: ".$query);
          exit;
        }
        $mutatieBedrag1 += round($mutatiedata['Bedrag'], 2);
      }
      /*
       *  cvs, 2011-4-18 soms is er een verschil van 1 cent, opgelost door optelling regel terug te schrijven in Rekeningafschriften
       */
      $query = "UPDATE Rekeningafschriften SET NieuwSaldo='".($afschriftdata[VorigSaldo] + $mutatieBedrag1)."' WHERE id = ".$afschriftId;
      $DB2->SQL($query);
      $DB2->Query();

      // done, remove tijdelijke mutatie!
      $query = "
        DELETE FROM 
          TijdelijkeRekeningmutaties 
        WHERE 
          Rekening = '".$rekeningdata['Rekening']."' AND 
          change_user = '$USR' 
          $selectieWhere ";

      $DB2->SQL($query);
      $DB2->Query();
    }
    $_txt = "De gegevens zijn verwerkt voor gebruiker ($USR)";
    $prb->hide();
    logIt("call 7183 ($USR) reknrs: ".implode(", ", $seenReknrs));
  }
  else
  {
    $_txt = "Verwerken is geanuleerd, ongeldige datum!";
  }
  ?>
  <br>
  <br>
  <br>
  &nbsp;&nbsp;&nbsp;&nbsp;<?= $_txt ?>
  <br/>
  <br/>
  <br/>
  <br/>

<?
  if ($verwerkSelectie)
  {
?>
    &nbsp;&nbsp;<button><a href="tijdelijkerekeningmutatiesList.php">Ga naar tijdelijke rekeningmutatie overzicht</a></button>
<?
  }
  else
  {
?>
    &nbsp;&nbsp;<button><a href="transaktieImport.php">Ga naar volgende TransactieImport</a></button>
<?
  }
?>


  &nbsp;&nbsp;
  <br>
  
  <?
  if (count($meldArray) > 0)
  {
    echo "<fieldset><legend> verwerkingsverslag </legend><li>";
    echo implode("<li>", $meldArray);
    echo "</fieldset>";
  }
}
else
{

  $db = new DB();
  $query = '
SELECT
	VermogensbeheerdersPerBedrijf.Bedrijf,
	TijdelijkeRekeningmutaties.Rekening,
	Rekeningen.Portefeuille,
	Portefeuilles.Vermogensbeheerder,
	Bedrijfsgegevens.vastzetdatumRapportages
FROM
	TijdelijkeRekeningmutaties
INNER JOIN Rekeningen ON TijdelijkeRekeningmutaties.Rekening = Rekeningen.Rekening
INNER JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
INNER JOIN VermogensbeheerdersPerBedrijf ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
INNER JOIN Bedrijfsgegevens ON VermogensbeheerdersPerBedrijf.Bedrijf = Bedrijfsgegevens.Bedrijf
WHERE 
  TijdelijkeRekeningmutaties.change_user = "'.$USR.'"
';



  $tmpRec = $db->lookupRecordByQuery($query);

  $vastzet = ($tmpRec["vastzetdatumRapportages"] > "0000-00-00");

  $vid = array();
  foreach ($_POST as $k=>$v)
  {
    $split = explode("_",$k);

    if ($split[0] == "vink" AND $v == "on")
    {
      $vid[] = $split[1];
    }

  }

  if ($koersAdded > 0)
  {
    echo "
    <div class='koersDone'>
    {$koersAdded} koers(en) toegevoegd
    </div>
    ";
  }

//debug($tmpRec);
  if (count($koersParse) > 0)
  {


  ?>
  <fieldset>
    <legend>Koers info vanuit het bankbestand</legend>
    <form id="frmKoers" method="post">
      <input type="hidden" name="koersImport" value="1">
      <input type="hidden" name="skipKoersImport" id="skipKoersImport" value="0">
      <table id="koersImport">
        <thead>
        <tr>
          <td><input type="checkbox" id="checkAll" checked/></td>
          <td>fonds</td>
          <td>laatste AIRS</td>
          <td>datum BANK</td>
          <td>ouderdom</td>
          <td>koers</td>
        </tr>
        </thead>
        <tbody>
        <?
        foreach ($koersParse as $row)
        {
          echo $tmpl->parseBlock("dataRow", $row);
        }
        ?>
        </tbody>
      </table>
      <div class="btnBar">
        <button class="btn" id="btnKoersSubmit">verwerk de aangevinkte koersen</button>
        <div class="msgSelected"><span id="itemTotal"></span> koers(en) geselecteerd </div>
      </div>
    </form>
  </fieldset>

    <script>
      $(document).ready(function (){
        $("#btnKoersSubmit").click(function(e){
          e.preventDefault();
          if ($('.vink').filter(':checked').length == 0)
          {
             $("#skipKoersImport").val("1");
          }
          $("#frmKoers").submit();

        });


        $('.vink').prop('checked',true);
        const totaal = $('.vink').filter(':checked').length;
        $("#itemTotal").text(totaal);
        countUnSelected();
        $("#checkAll").click(function(){
          $('.vink').prop('checked', $(this).is(":checked"));
          countUnSelected();
        });
        $(".vink").click(function(){
          countUnSelected();
        });

        function countUnSelected(){
          sel = $('.vink').filter(':checked').length
          const unSelected = totaal - sel;
          if (sel == 0){
            $("#checkAll").prop('checked', false);
          }
          console.log(totaal, sel, unSelected);
          $("#itemTotal").text(sel);
          $("#messageBar").css("background", (unSelected == 0)?"darkgreen":"darkorange");
        }


      });
    </script>
<?php
  }
  else
  {
?>
  <br/><br/>
  <div>
    boekdatum controle voor bedrijf <b><?= $tmpRec["Bedrijf"] ?></b>, <br/>
    vastzet datum gevonden: <b>
  <?
  if ($vastzet)
  {
    echo "<b>Ja, gevonden datum ".dbdate2form($tmpRec["vastzetdatumRapportages"])."</b>";
    echo "<div id='voortgang'>";
  }
  else
  {
    echo "<b>Nee</b><br/>";
  }
  echo "<br/><br/>";
  if ($_POST["recordsChecked"] > 0)
  {



    $dbi = new DB();
    $query = "
    SELECT 
      count(TijdelijkeRekeningmutaties.id) as tel 
    FROM 
      TijdelijkeRekeningmutaties 
    INNER JOIN Rekeningen ON 
      TijdelijkeRekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie = 0
    INNER JOIN Portefeuilles ON 
      Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie = 0
    INNER JOIN VermogensbeheerdersPerBedrijf ON 
      Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
    INNER JOIN Bedrijfsgegevens ON 
      VermogensbeheerdersPerBedrijf.Bedrijf = Bedrijfsgegevens.Bedrijf
    WHERE 
      TijdelijkeRekeningmutaties.change_user = '".$USR."' AND
      TijdelijkeRekeningmutaties.bankTransactieId <> ''   AND 
      TijdelijkeRekeningmutaties.id IN (".implode(",", $vid).")
    ";
    $controle = $dbi->lookupRecordByQuery($query);

    $bt = (int)$controle["tel"];
    if ($bt != $_POST["recordsChecked"])
    {
      echo '<div id="melding">';
?>
      <b><u>LET OP:</u></b> u selectie kan NIET verwerkt worden omdat de banktransactie ID vulling niet correct is<br/><br/>
      gevonden <?=$bt?> banktransactie ID's voor <?=$_POST["recordsChecked"]?> geselecteerde records.<br/><br/>
      <a href="tijdelijkerekeningmutatiesList.php"><button>klik hier, om terug te gaan</button></a><br/><br/>

<?
      echo "</div>";
      exit;
    }


  }


  ?>
  </div>
      <?
      if ($vastzet)
      {

        $query = "
  SELECT
    count(id) as aantal,
    TijdelijkeRekeningmutaties.Boekdatum
  FROM
    TijdelijkeRekeningmutaties
  WHERE
    Boekdatum <= '".$tmpRec["vastzetdatumRapportages"]."'
  AND
    TijdelijkeRekeningmutaties.change_user = '$USR'
  GROUP BY
    Boekdatum
  ";
        $db->executeQuery($query);
        $stop = false;
        if ($db->records() > 0)
        {
          while ($tmpRec = $db->nextRecord())
          {
            $txt .= "<br/> ".$tmpRec["aantal"]." mutaties op ".dbdate2form($tmpRec["Boekdatum"]);
          }
          $txt .= "<br/><br/> verwerken afgebroken..";
          $stop = true;
        }
        else
        {
          $txt = "<br/> geen afwijkende boekingen gevonden";
        }
        echo $txt."</div>";
      }
      if (!$stop)
      {
        ?>
    <br>
    <form action="<?= $PHP_SELF ?>" method="post">
      <table border="0">
        <tr>
          <td>
            <input type="hidden" name="doIt" value="1">
            <input type="hidden" name="vinkIds" value="<?=implode(",", $vid)?>">
            Afschrift datum :
          </td>
          <td>
            <input type="text" name="datum" value="<?= lastWorkday("d-m-Y") ?>" size="10"> <i>(DD-MM-JJJJ)</i><br>
          </td>
        </tr>
        <tr>
          <td>
          </td>
          <td>
            <input type="submit" value=" Verwerken ">
          </td>
        </tr>
      </table>
    </form>
    <?
  }
  }
}
// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"], $content);

function db2day($dbDate)
{
  $p = explode("-", substr($dbDate, 0, 10));
  $julDat = ceil(mktime(0, 0, 0, $p[1], $p[2], $p[0]) / 86400);
  if (substr($dbDate, 0, 10) == "0000-00-00")
    return 0;
  else
    return $julDat;
}

function day2db($julDay, $out = "jul")
{
  if ($out == "jul")
    return date("Y-m-d", ($julDay * 86400));
  else
    return date("d-m-Y", ($julDay * 86400));
}


function duplicaatRekeningVerwerk()
{
  global $USR, $meldArray;
  $db = new DB();
  $dupliceerArray = array();
  $searchArray = array();
  $query = "
  SELECT
    *
  FROM
    `RekeningenDuplicaat`
  WHERE
    RekeningenDuplicaat.actief = 1";
  $db->executeQuery($query);
  while ($dupRec = $db->nextRecord())
  {
    $dupliceerArray[$dupRec["Rekening"]] = $dupRec["RekeningDuplicaat"];
    $searchArray[] = $dupRec["Rekening"];
  }
  
  $query = "SELECT * FROM TijdelijkeRekeningmutaties WHERE TijdelijkeRekeningmutaties.change_user = '$USR' AND Rekening IN ('".implode("','", $searchArray)."') ";
  
  $db->executeQuery($query);
  while($rekRec = $db->nextRecord())
  {
    copyMysqlRow($rekRec, $dupliceerArray);
  }
}

function copyMysqlRow($rekRec, $rekeningArray)
{
  global $meldArray;
  $db = new DB();
  $rek = $rekRec["Rekening"];
  unset($rekRec["id"]);
  $rekRec["Rekening"] = $rekeningArray[$rek];
  $q = "INSERT INTO `TijdelijkeRekeningmutaties` SET\n";
  foreach($rekRec as $k => $v)
  {
    if ($k == "Omschrijving")
    {
      $v = mysql_real_escape_string($v);
    }
    $qArray[] = " `$k` = '$v' ";
  }
  $q .= implode(",  \n",$qArray);
  $db->executeQuery($q);
  $meldArray[] = "duplicaat record aangemaakt van $rek naar ".$rekRec["Rekening"].": ".$rekRec["Omschrijving"]." grootboek=".$rekRec["Grootboekrekening"];
}


?>