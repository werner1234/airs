<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/03/21 11:41:27 $
    File Versie         : $Revision: 1.7 $

    $Log: dashboard_assetVerdeling.php,v $
    Revision 1.7  2018/03/21 11:41:27  cvs
    no message

    Revision 1.6  2018/03/07 15:49:03  rm
    Toevoegen export mogelijkheid canvasjs

    Revision 1.5  2017/10/27 08:50:59  cvs
    no message

    Revision 1.4  2017/07/21 13:52:56  cvs
    call 5933

    Revision 1.3  2017/06/26 11:37:54  cvs
    no message

    Revision 1.2  2017/06/02 14:21:10  cvs


    Revision 1.1  2017/04/26 15:06:22  cvs
    call 5816



*/


include_once "../HTMLrapport/init.php";
include_once "../HTMLrapport/dashboard_assetVerdeling_functies.php";
//


$portefeuille = $_GET["port"];
$USR = $_SESSION["USR"];
;
getASSETvalues();



?>
  <div id="chartContainer" style="height: 300px; width: 45%; float: left"></div>
  <div style="width: 54%; float: left"><ul><?=$divATT?></ul></div>
<?php

?>
<script type="text/javascript" src="../javascript/jquery-min.js"></script>
<script type="text/javascript" src="../javascript/jquery-ui-min.js"></script>
<script type="text/javascript" src="../javascript/canvasjs.min.js"></script>
<script>
  $(document).ready(function ()
  {

    console.log("loading asset graph");
    var chart = new CanvasJS.Chart("chartContainer",
      {
        exportEnabled: true,
        title: {
          text: "",
          fontWeight: "normal"
        },
        toolTip:{
          content:"{legendText}" ,
        },
        data: [
          {
            indexLabelFontSize: 15,
            valueFormatString: " {y}%",
            type: "pie",


            dataPoints: [
              <?=$jsATT?>
            ]
          }
        ]

      });
    chart.render();
  });

</script>
