<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/03/21 11:41:27 $
    File Versie         : $Revision: 1.7 $

    $Log: dashboard_verloopVermogen.php,v $
    Revision 1.7  2018/03/21 11:41:27  cvs
    no message

    Revision 1.6  2018/03/07 15:49:03  rm
    Toevoegen export mogelijkheid canvasjs

    Revision 1.5  2017/10/27 08:51:29  cvs
    no message

    Revision 1.4  2017/07/21 13:52:56  cvs
    call 5933

    Revision 1.3  2017/06/26 11:37:54  cvs
    no message

    Revision 1.2  2017/06/02 14:21:10  cvs
    no message

    Revision 1.1  2017/04/26 15:06:22  cvs
    call 5816



*/

include_once "init.php";
include_once "../HTMLrapport/dashboard_verloopVermogen_functies.php";
include_once("../../classes/htmlReports/htmlDashboardHelper.php");
$portefeuille = $_GET["port"];
$USR = $_SESSION["USR"];



$dash = new htmlDashboardHelper($portefeuille);
$dash->startDatum = $_GET["start"];


$dataset = $dash->getRecords("maand",$_GET["start"],$_GET["stop"]);


getVerloopVermogen($dataset);



?>
  <div id="chartContainerVV" style="height: 300px; width: 99%; float: left"></div>

<script type="text/javascript" src="../javascript/jquery-min.js"></script>
<script type="text/javascript" src="../javascript/jquery-ui-min.js"></script>
<script type="text/javascript" src="../javascript/canvasjs.min.js"></script>
<script>
  $(document).ready(function ()
  {
    console.log("loading asset graph");
    var chartVV = new CanvasJS.Chart("chartContainerVV",
      {
        exportEnabled: true,
        theme:"theme2",
        title:{
          text: ""
        },
        animationEnabled: true,
        axisY :{
          includeZero: false,
        },
        toolTip:{
          content:"{legendText}" ,
        },
        data: [
          {
            type: "spline",
            showInLegend: false,
            name: "<?=vt('vermogen');?>: ",

            dataPoints: [
              <?=$jsVV?>

            ]
          },

        ],
        legend:{
          cursor:"pointer",
          itemclick : function(e) {
            if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible ){
              e.dataSeries.visible = false;
            }
            else {
              e.dataSeries.visible = true;
            }
            chart.render();
          }

        },
      });

    chartVV.render();
  });
</script>
<?



?>