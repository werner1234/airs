<?php

/*
    AE-ICT sourcemodule created 16 nov. 2020
    Author              : Chris van Santen
    Filename            : AfmCheck.php

naar RVV 20201123
*/

include_once("wwwvars.php");
session_start();
$_SESSION["NAV"] = "";
session_write_close();



$db = new DB();

if ($_POST)
{

  switch ($_POST["action"])
  {
    case "submit":
      foreach ($_POST as $k => $v)
      {
        if (substr($k, 0, 5) == "vink_")
        {
          $id[] = substr($k, 5);
        }

      }
      break;
    default:
  }
  debug($id, "exporteer orderId nummers");
  exit;

}

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

$row1 = "
<tr class='msgRow {trClass}' id='row_{orderId}' >
  <td class='ac'><input type='checkbox' class='vink{x}' name='vink_{orderId}' id='vink_{orderId}' {disable}></td>
  <td>{Fonds}</td>
  <td>{Boekdatum}</td>
  <td>{Transactietype}</td>
  <td >{orderId}</td>
  <td >{RM_aantal}</td>
  <td >{EO_aantal}</td>
  <td >{verwerkt}</td>
  <td >{transactieCode}</td>
  <td class='ar'>{transactieMatch}</td>
  <td class='ar'>{aantalMatch}</td>
</tr>
";

$row2 = "
<tr class='msgRow {trClass}' id='row_{orderId}' >
  <td></td>
  <td>{Fonds}</td>
  <td>{Boekdatum}</td>
  <td>{Transactietype}</td>
  <td >{orderId}</td>
  <td >{RM_aantal}</td>
  <td >{EO_aantal}</td>
  <td >{verwerkt}</td>
  <td >{transactieCode}</td>
  <td class='ar'></td>
  <td class='ar'></td>
</tr>
";

$tmpl = new AE_template();
$fmt = new AE_cls_formatter();


//$apiExtern->initTables();  // tabellen aanmaken voor module
//$mail->buildRouterTable();

$tmpl->loadTemplateFromString($row1, "row1");
$tmpl->loadTemplateFromString($row2, "row2");

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
  </style>

<?



?>

  <h2> AFM controle </h2>
  <div class="box">

    <fieldset>
      <legend>Filter</legend>
      <h2><?=$filterText?></h2>
      <button id="btnAlles" class="btn-new btn-default">Alles</button>
      <button id="btnOnverwerkt" class="btn-new btn-default">Niet verwerkt</button>
      <button id="btnVerwerkt" class="btn-new btn-default">Verwerkt</button>

    </fieldset>

    <form method="post" id="form" action="../externeorderExportAFM.php">


      <input type="hidden" id="action" name="action" value=""/>

      <table>
        <tr class="trSubHead">
          <td colspan="17"> Matched</td>
        </tr>
        <tr class="trHead">
          <td class="al"><input type='checkbox' class='' id='vink_all1'></td>
          <td>Fonds</td>
          <td>Boekdatum</td>
          <td>Transactietype</td>
          <td >orderId</td>
          <td >RM_aantal</td>
          <td >EO_aantal</td>
          <td >verwerkt</td>
          <td >transactieCode</td>
          <td >transactieMatch</td>
          <td >aantalMatch</td>
        </tr>

<?
        $query = "
  SELECT
    rm.*,
    externeOrders.aantal as EO_aantal,
    externeOrders.verwerkt,
    externeOrders.transactieCode,
    rm.Transactietype=externeOrders.transactieCode as transactieMatch,
    rm.RM_aantal=externeOrders.aantal as aantalMatch
    FROM (
      SELECT
        Rekeningmutaties.Fonds,
        Rekeningmutaties.Boekdatum,
        Rekeningmutaties.Transactietype,
        Rekeningmutaties.orderId,
        round(sum( Rekeningmutaties.aantal ),6) as 'RM_aantal' 
      FROM
        Rekeningmutaties 
      WHERE
        Grootboekrekening = 'Fonds' 
      GROUP BY
        Rekeningmutaties.Fonds,
        Rekeningmutaties.Boekdatum,
        Rekeningmutaties.orderId
      ) rm
    INNER JOIN externeOrders 
      ON rm.orderId = externeOrders.externOrderId 
    AND rm.Fonds = externeOrders.Fonds
    WHERE 1 {$filterWhere}
";
//debug($query);
        $db->executeQuery($query);

        while($rec =$db->nextRecord())
        {

          if ($rec["RM_aantal"] > 0)
          {
            $rec["Transactietype"] = "A";
          }
          else
          {
            $rec["Transactietype"] = "V";
          }

          if ($rec["transactieCode"] == "A")
          {
//            $rec["EO_aantal"]       = $fmt->format("@N{.6}",$rec["EO_aantal"]);
            $rec["EO_aantal"]       = $rec["EO_aantal"];
          }
          else
          {
//            $rec["EO_aantal"]       = $fmt->format("@N{.6}",$rec["EO_aantal"] * -1);
            $rec["EO_aantal"]       = $rec["EO_aantal"] * -1;
          }

          $rec["x"]               = "1";
          $rec["Boekdatum"]       = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $rec["Boekdatum"]);
          $rec["transactieMatch"] = ($rec["transactieMatch"] == 1)?"match":"ongelijk";


          $rec["aantalMatch"]     = ($rec["RM_aantal"] == $rec["EO_aantal"])?"match":"ongelijk";


          if ($rec["transactieMatch"] == "ongelijk" OR $rec["aantalMatch"] == "ongelijk")
          {
            $rec["disable"] = " DISABLED";
            $rec["trClass"] = " redRow";
            $rec["x"]       = "";
          }

          echo $tmpl->parseBlock("row1", $rec);
        }


        ?>

        <tr class="trSubHead">
          <td colspan="17">Alleen Rekeningmutaties</td>
        </tr>
        <tr class="trHead">
          <td class="al"></td>
          <td>Fonds</td>
          <td>Boekdatum</td>
          <td>Transactietype</td>
          <td >orderId</td>
          <td >RM_aantal</td>
          <td >EO_aantal</td>
          <td >verwerkt</td>
          <td >transactieCode</td>
          <td >transactieMatch</td>
          <td >aantalMatch</td>
        </tr>

        <?
        $db1 = new DB();
        $query = "
           SELECT
            rm.*,
            externeOrders.aantal as EO_aantal,
            externeOrders.verwerkt,
            externeOrders.transactieCode
            FROM (
              SELECT
                Rekeningmutaties.Fonds,
                Rekeningmutaties.Boekdatum,
                Rekeningmutaties.Transactietype,
                Rekeningmutaties.orderId,
                round(sum( Rekeningmutaties.aantal ),6) as RM_aantal
              FROM
                Rekeningmutaties 
              WHERE
                Grootboekrekening = 'Fonds' AND 
                transactieType in ('A','V')
              GROUP BY
                Rekeningmutaties.Fonds,
                Rekeningmutaties.Boekdatum,
                Rekeningmutaties.Transactietype ,
                Rekeningmutaties.orderId
              ) rm
            LEFT JOIN externeOrders ON 
              rm.orderId = externeOrders.externOrderId 
            AND rm.Fonds = externeOrders.Fonds
            where externeOrders.id is NULL {$filterWhere}
";

        $db1->executeQuery($query);

        while($rec = $db1->nextRecord())
        {
          $rec["Boekdatum"] = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $rec["Boekdatum"]);
          $rec["transactieMatch"] = ($rec["transactieMatch"] == 1)?"match":"ongelijk";
          $rec["aantalMatch"]     = ($rec["aantalMatch"] == 1)?"match":"ongelijk";



          echo $tmpl->parseBlock("row2", $rec);
        }
        ?>


        <tr class="trSubHead">
          <td colspan="17">Alleen orders</td>
        </tr>
        <tr class="trHead">
          <td class="al"></td>
          <td>Fonds</td>
          <td>Boekdatum</td>
          <td>Transactietype</td>
          <td >ExternOrderId</td>
          <td >RM_aantal</td>
          <td >EO_aantal</td>
          <td >verwerkt</td>
          <td >transactieCode</td>
          <td >transactieMatch</td>
          <td >aantalMatch</td>
        </tr>

        <?

        $query = "
           SELECT
	rm.*,
	externeOrders.externOrderId as orderId,
	externeOrders.aantal,
	externeOrders.verwerkt,
	externeOrders.transactieCode
	from externeOrders left join
	(
	SELECT
		Rekeningmutaties.Fonds,
		Rekeningmutaties.Boekdatum,
		Rekeningmutaties.Transactietype,
		Rekeningmutaties.orderId,
		round(sum( Rekeningmutaties.aantal ),6) 
	FROM
		Rekeningmutaties 
	WHERE
		Grootboekrekening = 'Fonds' 
	GROUP BY
		Rekeningmutaties.Fonds,
		Rekeningmutaties.Boekdatum,
		Rekeningmutaties.Transactietype ,
		Rekeningmutaties.orderId
	) rm
	ON externeOrders.externOrderId =rm.orderId
	where rm.Fonds is NULL {$filterWhere}
";

        $db1->executeQuery($query);

        while($rec = $db1->nextRecord())
        {

          $rec["Boekdatum"] = (is_null ($rec["Boekdatum"]))?"":$fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $rec["Boekdatum"]);

          $rec["transactieMatch"] = ($rec["transactieMatch"] == 1)?"match":"ongelijk";
          $rec["aantalMatch"]     = ($rec["aantalMatch"] == 1)?"match":"ongelijk";



          echo $tmpl->parseBlock("row2", $rec);
        }
        ?>
        <tr>
          <td colspan="6">
            <hr/>
            Aangevinkte items
            <button class="btn-new btn-default" id='btnSubmit'><i class="fa fa-floppy-o" aria-hidden="true"></i>
                exporteren
            </button>&nbsp;
            <button class="btn-new btn-default" id='btnSubmitDefinitief'><i class="fa fa-floppy-o" aria-hidden="true"></i>
              exporteren definitef
            </button>&nbsp;
          </td>
        </tr>
      </table>

    </form>

    <br/>
    <br/>
    <br/>



  </div>

  <div style="clear: both"></div>



  <input type="hidden" value="0" id="retourId" name="retourId"/>

  <script>

    $(document).ready(function () {
      var extra = 0;

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

echo template($__appvar["templateRefreshFooter"], $content);



