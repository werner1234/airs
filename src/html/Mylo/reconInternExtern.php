<?php
/*
    AE-ICT sourcemodule created 15 mrt. 2021
    Author              : Chris van Santen
    Filename            : reconInternExtern.php


*/

include_once("wwwvars.php");
session_start();
$_SESSION["NAV"] = "";



$db = new DB();

if ($_POST["action"] == "submit")
{

      foreach ($_POST as $k => $v)
      {
        if (substr($k, 0, 5) == "vink_")
        {
          $id[] = substr($k, 5);
        }

      }

  debug($id, "exporteer orderId nummers");
  exit;

}

$_SESSION["NAV"] = '';
$content['style'] .= '
<link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">
<link rel="stylesheet" href="../style/smoothness/jquery-ui-1.11.1.custom.css">
';
$content['jsincludes'] = "<script type='text/javascript' src='../javascript/jquery-min.js'></script>";
$content['jsincludes'] .= "<script type='text/javascript' src='../javascript/jquery-ui-min.js'></script>";

$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";
echo template("../".$__appvar["templateContentHeader"], $content);
?>

  <link href="../style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">
  <link rel="stylesheet" href="../style/fontAwesome/font-awesome.min.css">
  <style>


    table {
      width: 90%;
      background: whitesmoke;
      margin: 10px;
      padding: 10px;
    }

    .trHead {
      background: rgba(20, 60, 90, 1);
      color: white;
      border-bottom: 1px solid #FFF;
    }

    .trHead td {
      color: white;
      padding: 5px;
      padding-left: 10px;

    }

    .trSubHead {
      background: #666;
      color: white;
      border-bottom: 1px solid #FFF;
      height: 2rem;
    }

    .trSubHead td {
      color: white;
      padding-left: 10px;
      text-align: left;
      font-size:1rem;
      font-weight: bold;
    }



    .msgRow :hover {
      cursor: pointer;
      background: #ffc121;
    }

    .redRow {
      background: #ffaaaa;
    }

    .ar{
      text-align: right;
    }

    legend{
      background: #666;
      color: white;
      padding:3px 15px;
      font-size: 1rem;
    }
    fieldset{
      width: 90%;
    }


    .ac {
      text-align: center;
    }
    h2{
      margin-left:2rem;
    }
    #dateSelect{
      border:1px solid #999;
      padding:15px;
      width: 700px;
    }
    #loading{
      display: flex;
      position: fixed;
      left:10%;
      top:10%;
      padding:30px;
      background: white;
      border:1px solid #666;
      box-shadow: 5px 5px 5px #999;
      align-items: center;
      justify-content: center;
    }
  </style>


  <div id="loading"><img src="../images/loading.gif"> &nbsp;&nbsp;&nbsp;&nbsp;<h2>Recon laden...</h2></div>
<?php
flush();
ob_flush();
flush();
ob_flush();

$d = explode("-", $_POST["datum"]);
if ($d[0] < 1 OR $d[0] > 31 OR
    $d[1] < 1 OR $d[1] > 12 OR
    $d[2] < 2010 OR $d[2] > date("Y"))
{
  $datum = date("Y-m-d");
}
else
{
  $datum = "{$d[2]}-{$d[1]}-{$d[0]}";
}

if (!$_POST["datum"])
{
  $datum = date("Y-m-d");
  $_POST["datum"]  = date("d-m-Y");
  $_POST["action"] = "fase2";
}



$query = "
SELECT 
  Rekeningmutaties.Fonds,
  Fondsen.ISINCode,
  SUM(Rekeningmutaties.Aantal) as Aantal
  
FROM 
  Rekeningmutaties
JOIN Rekeningen ON 
  Rekeningmutaties.Rekening = Rekeningen.Rekening AND 
  Rekeningen.consolidatie = 0
JOIN Portefeuilles ON 
  Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND 
  Portefeuilles.consolidatie = 0
Join Fondsen on
	Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE 
  YEAR(Rekeningmutaties.Boekdatum) = '".substr($datum,0,4)."' AND 
  Rekeningmutaties.Verwerkt = '1' AND
	Rekeningmutaties.Fonds <> '' AND 
  Rekeningmutaties.Boekdatum <= '{$datum}' AND
  Portefeuilles.Portefeuille NOT IN ('225222', '16793670')
  
GROUP BY Rekeningmutaties.Fonds, Fondsen.ISINCode";


// Portefeuilles.Portefeuille <> '225222'
//
// gewijzigd in  Portefeuilles.Portefeuille  NOT IN ('225222', '16793670')
$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
  $output[$rec["ISINCode"]]["extern"] = $rec;
}

$query = "
SELECT 
       Rekeningen.Rekening,
  Rekeningmutaties.Fonds,
  Fondsen.ISINCode,
  SUM(Rekeningmutaties.Aantal) as Aantal
  
FROM 
  Rekeningmutaties
JOIN Rekeningen ON 
  Rekeningmutaties.Rekening = Rekeningen.Rekening AND 
  Rekeningen.consolidatie = 0
JOIN Portefeuilles ON 
  Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND 
  Portefeuilles.consolidatie = 0
Join Fondsen on
	Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE 
  YEAR(Rekeningmutaties.Boekdatum) = '".substr($datum,0,4)."' AND 
  Rekeningmutaties.Verwerkt = '1' AND
	Rekeningmutaties.Fonds <> '' AND
  Rekeningmutaties.Boekdatum <= '{$datum}' AND
  Portefeuilles.Portefeuille IN ('225222', '16793670')
  
GROUP BY Rekeningmutaties.Fonds, Fondsen.ISINCode";

// Portefeuilles.Portefeuille = '225222'
//
// gewijzigd in  Portefeuilles.Portefeuille IN ('225222', '16793670')
//debug($query);

$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
  $output[$rec["ISINCode"]]["intern"] = $rec;
}



$query = "
SELECT 
  Rekeningen.Rekening,
  Rekeningen.Valuta,
  SUM(Rekeningmutaties.Bedrag) as bedrag
  
FROM 
  Rekeningmutaties
JOIN Rekeningen ON 
  Rekeningmutaties.Rekening = Rekeningen.Rekening AND 
  Rekeningen.consolidatie = 0
JOIN Portefeuilles ON 
  Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND 
  Portefeuilles.consolidatie = 0
WHERE 
  YEAR(Rekeningmutaties.Boekdatum) = '".substr($datum,0,4)."' AND 
  Rekeningmutaties.Verwerkt = '1' AND
  Rekeningmutaties.Boekdatum <= NOW() AND
  Portefeuilles.Portefeuille  NOT IN ('225222', '16793670')
  
GROUP BY Rekeningen.Valuta";
// Portefeuilles.Portefeuille <> '225222'
//
// gewijzigd in  Portefeuilles.Portefeuille  NOT IN ('225222', '16793670')

//debug($query);
$db = new DB();
$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
  $cashRecs[] = $rec;
}


$query = "
SELECT 
  
  Rekeningen.Valuta,
  SUM(Rekeningmutaties.Bedrag) as bedrag
  
FROM 
  Rekeningmutaties
JOIN Rekeningen ON 
  Rekeningmutaties.Rekening = Rekeningen.Rekening AND 
  Rekeningen.consolidatie = 0
JOIN Portefeuilles ON 
  Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND 
  Portefeuilles.consolidatie = 0
WHERE 
  YEAR(Rekeningmutaties.Boekdatum) = '".substr($datum,0,4)."' AND 
  Rekeningmutaties.Verwerkt = '1' AND
  Rekeningmutaties.Boekdatum <= NOW() AND
  Portefeuilles.Portefeuille IN ('225222', '16793670')
  
GROUP BY Rekeningen.Valuta";
// Portefeuilles.Portefeuille = '225222'
//
// gewijzigd in  Portefeuilles.Portefeuille IN ('225222', '16793670')

$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
  $internRecs[] = $rec;
}

foreach ($rapport->dbWaarden as $item)
{
  $query = "SELECT * FROM `Fondsen` WHERE `Fonds` = '".$item["Fonds"]."'";
  $fnds = $db->lookupRecordByQuery($query);
//  debug($item);
//  debug($fnds);

  if ($item["Fonds"] == "")
  {
    $item["Fonds"] = "EUR";
    $item["totaalAantal"] = $item["actuelePortefeuilleWaardeEuro"];
  }
  $airsPositie[] = array(
    "isin" => $fnds["ISINCode"],
    "valuta" => $fnds["Valuta"],
    "fonds" => $item["Fonds"],
    "fondsValuta" => $fnds["Valuta"],
    "fondsOmschrijving" => $item["FondsOmschrijving"],
    "aantal" => $item["totaalAantal"]

  );
}
//debug($cashRecs);
//debug($internRecs);
//debug($output );
$filterWhere = "AND externeOrders.verwerkt = 0";
$filterText = "Onverwerkte orders";

if ($_GET["filter"])
{
  switch ($_GET["filter"])
  {
    case "verwerkt":
      $filterWhere = "AND externeOrders.verwerkt = 1 ";
      $filterText  = "Verwerkte orders";
      break;
    case "onverwerkt":
      $filterWhere = "AND externeOrders.verwerkt = 0 ";
      $filterText  = "Onverwerkte orders";
      break;
    case "alles":
      $filterWhere = "";
      $filterText = "";
      break;
  }
}
//<td class='ac'><input type='checkbox' class='vink{x}' name='vink_{orderId}' id='vink_{orderId}' {disable}></td>
$row1 = "
<tr class='msgRow {trClass}' id='row_{isin}' >
  
  <td>{Fonds}</td>
  <td>{Rekening}</td>
  <td class='ar'>{externAantal}</td>
  <td class='ar'>{internAantal}</td>
  <td class='ar'>{Match}</td>
</tr>
";



$tmpl = new AE_template();
$fmt = new AE_cls_formatter();


//$apiExtern->initTables();  // tabellen aanmaken voor module
//$mail->buildRouterTable();

$tmpl->loadTemplateFromString($row1, "row1");
$tmpl->loadTemplateFromString($row2, "row2");


$pdfData = array();

$pdfData[] = array(
  "Recon datum",
  $_POST["datum"]
);


$out = "";

?>
  <h2> Moka interne/externe controle </h2>
  <form method="post" id="dateSelect" >
    <input type="hidden" id="action" name="action" value="fase2"/>
    <br/>
    Andere recon datum <input size="12" class="AIRSdatepicker" type="text" name="datum" value="<?=$_POST["datum"]?>">
    <br/>
    <br/>
    <input type="submit" value="Verwerk met opgegeven datum">
  </form>
  <div class="box">
    <form method="post" id="form" >
      <input type="hidden" id="action" name="action" value=""/>
      <input type="hidden" name="datum" value="<?=$_POST["datum"]?>">
      <?

      $out .= '      
      <table>
        <tr class="trSubHead">
          <td colspan="17"> Recon</td>
        </tr>
        <tr class="trHead">
          <td>Fonds</td>
          <td>Rekening</td>
          <td class="ar">positie Clienten/extern</td>
          <td class="ar">positie Depotbank/intern</td>
          <td >Verschil</td>
        </tr>
        ';

      $pdfData[] = array(
        "Fonds",
        "Rekening",
        "positie Clienten/extern",
        "positie Depotbank/intern",
        "Verschil",
      );

        foreach ($output as $isin=>$item)
        {

          $item["extern"]["Aantal"] = round($item["extern"]["Aantal"],6);
          $item["intern"]["Aantal"] = round($item["intern"]["Aantal"],6);

//          debug($item, $isin);
          $match = round($item["extern"]["Aantal"] - $item["intern"]["Aantal"],6);
          $dataset = array(
            "Fonds" => $item["extern"]["Fonds"],
            'Rekening' => $item["intern"]["Rekening"],
            "externAantal" => (float)$item["extern"]["Aantal"],
            "internAantal" => (float)$item["intern"]["Aantal"],
            "isin" => $isin,
            "Match" => $match
          );


          if ($match != 0)
          {

            $dataset["trClass"] = " redRow";

          }

          $out .=  $tmpl->parseBlock("row1", $dataset);
          $pdfData[] = $dataset;
        }


        $dataset = array(
          "Fonds" => "Cash ".$cashRecs[0]["Valuta"],
          "Rekening" => "Saldo clienten/extern",
          "externAantal" => $fmt->format("@N{.2}",$cashRecs[0]["bedrag"]),
          "internAantal" => 0,

          "Match" => ""
        );

      $out .=  $tmpl->parseBlock("row1", $dataset);
      $pdfData[] = $dataset;

        $dataset = array(
          "Fonds" => "Cash ".$internRecs[0]["Valuta"],
          "Rekening" => "Saldo depotbank/intern",
          "externAantal" => 0,
          "internAantal" => $fmt->format("@N{.2}",$internRecs[0]["bedrag"]),

          "Match" => ""
        );

      $out .=  $tmpl->parseBlock("row1", $dataset);
      $pdfData[] = $dataset;

      $out .= "</table>";
      $_SESSION["mokaRecon"] = $pdfData;
      echo $out;

        ?>


        <!--        <tr>-->
        <!--          <td colspan="6">-->
        <!--            <hr/>-->
        <!--            Aangevinkte items-->
        <!--            <button class="btn-new btn-default" id='btnSubmit'><i class="fa fa-floppy-o" aria-hidden="true"></i>-->
        <!--                exporteren-->
        <!--            </button>&nbsp;-->
        <!--            <button class="btn-new btn-default" id='btnSubmitDefinitief'><i class="fa fa-floppy-o" aria-hidden="true"></i>-->
        <!--              exporteren definitef-->
        <!--            </button>&nbsp;-->
        <!--          </td>-->
        <!--        </tr>-->


    </form>

    <br/>
    <br/>
    <br/>



  </div>

  <div style="clear: both"></div>

  <button id="btnPdf"> naar PDF </button>


  <input type="hidden" value="0" id="retourId" name="retourId"/>

  <script>

    $(document).ready(function () {

      setTimeout(function(){ $("#loading").hide();}, 1000);


      $( ".AIRSdatepicker" ).datepicker({
        showOn: "button",
        buttonImage: "../javascript/calendar/img.gif",//"images/datePicker.png",
        buttonImageOnly: true,
        dateFormat: "dd-mm-yy",
        dayNamesMin: ["Zo", "Ma", "Di", "Wo", "Do", "Vr", "Za"],
        monthNames: ["januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december"],
        monthNamesShort: [ "Jan", "Feb", "Mrt", "Apr", "Mei", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dec" ],
        nextText: "volgende maand",
        prevText: "vorige maand",
        currentText: "huidige maand",
        changeMonth: true,
        changeYear: true,
        yearRange: '2000:2050',
        closeText: "sluiten",
        showAnim: "slideDown",
        showButtonPanel: true,
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 2,
        showWeek: true,
        firstDay: 1
      });

      var extra = 0;
      $("#btnPdf").click(function (e){
        e.preventDefault();
        window.location.href = "reconInternExternPDF.php";
      });
      $("#btnVerwerkt").click(function (e){
        e.preventDefault();
        window.location.href = "?filter=verwerkt";
      });
      $("#btnOnverwerkt").click(function (e){
        e.preventDefault();
        window.location.href = "?filter=onverwerkt";
      });
      $("#btnAlles").click(function (e){
        e.preventDefault();
        window.location.href = "?filter=alles";
      });

      $(".showContent").click(function (e) {
        e.preventDefault();
        var myId = $(this).attr("id").substring(8);
        $.ajax(
          {
            type: "GET",
            url: "API_queueContent.php",
            data: {id: myId}
          }).done(function (msg) {
//        console.log(msg);
          $("#previewScr").html(msg);
          dialogPreview.dialog("open");
        });


      });

      $("#btnSubmit").click(function (e) {
        e.preventDefault();
        $("#action").val("submit");
        $("#form").submit();
      });

      $("#btnSubmitDefinitief").click(function (e) {
        e.preventDefault();
        $("#action").val("definitef");
        $("#form").submit();
      });

      $('#vink_all1').change(function () {
        var c = $(this).is(':checked');
        console.log("in vink_all1 " + c);

        if ($(this).is(':checked'))
        {
          $(".vink1").prop('checked', true);
        } else
        {
          $(".vink1").prop('checked', false);
        }
      });
      $('#vink_all2').change(function () {
        var c = $(this).is(':checked');
        console.log("in vink_all2 " + c);

        if ($(this).is(':checked'))
        {
          $(".vink2").prop('checked', true);
        } else
        {
          $(".vink2").prop('checked', false);
        }
      });


    });

  </script>




<?

echo template("../".$__appvar["templateRefreshFooter"], $content);



