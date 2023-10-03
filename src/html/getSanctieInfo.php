<?php
/*
    AE-ICT sourcemodule created 08 Jan 2020
    Author              : Chris van Santen
    Filename            : getSanctieInfo.php

*/

include_once("wwwvars.php");
include_once("../../classes/AE_cls_handelzeker.php");
$__sanctieVars = array(
  "username" => "frank@airs.nl",
  "password" => "LuKiiJY6x"
);

session_start();
$_SESSION["submenu"] = "";
$_SESSION["NAV"] = "";
session_write_close();

$data = $_REQUEST;

$defaultProbability = 80;


if ($data["action"] == "go")
{

  $san = new AE_cls_handelzeker();

  $san->customer_due_dilligence($data);
  debug($san->feedback, " API call");
  debug($san->individuals, "details");

  exit;
}
//debug($__appvar);

$content = array();
$content['jsincludes'] .= '<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">';
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/jquery-min.js\"></script>";
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/jquery-ui-min.js\"></script>";
$content['jsincludes'] .= "<link rel=\"stylesheet\" href=\"style/fontAwesome/font-awesome.min.css\">";
echo template($__appvar["templateContentHeader"],$content);
?>
  <style>
    legend{
      padding: 4px 25px;
      background: #0A246A;
      color: white;
    }
    .vl{
      display: inline-block;
      width: 200px;
      font-weight: bold;
      margin-bottom: 5px;
    }
    .vr{
      margin-bottom: 15px;
      margin-left: 15px;
    }
    .fldset{

    }
    .inpStr{
      width: 50%;
    }
    #errorMsg{
      display: none;
      background: maroon;
      color: white;
      padding: 20px;
      margin-bottom: 25px;
    }
  </style>
  <h1>Handelzeker screening</h1>

  <form method="post" id="sanctieForm">
    <input type="hidden" name="action" value="go">
    <input type="hidden" name="code" value="<?=$code?>">


    <br/>
    <br/>

    <div id="errorMsg"></div>

        <fieldset id="CheckName" class="fldset">
          <legend id="legendName">CheckName</legend>
          <div class="vl">First name</div><div class="vr"><input name="firstname" class="inpStr"></div>
          <div class="vl">Middle name</div><div class="vr"><input name="middlename" class="inpStr"></div>
          <div class="vl">Last name</div><div class="vr"><input name="surname" class="inpStr"></div>
          <div class="vl">date of birth</div><div class="vr"><input name="dob" class="inpStr AIRSdatepicker" style="width: 100px" readonly></div>
          <div class="vl">Relevance</div><div class="vr"><select name="relevance">
              <?
              for ($p=100; $p > 74; $p--)
              {
                $selected = ($p == $defaultProbability)?"SELECTED":"";
                echo "<option value='$p' $selected>$p</option>\n";
              }
              ?>
            </select>
          </div>
          <div class="vl">PEP</div><div class="vr"><input name="pep" type="checkbox" value="1" checked> PEP</div>
        </fieldset>





    <br/>
    <br/>
    <button id="btnSubmit">Verwerk</button>
  </form>


  <script>
    $(document).ready(function () {
      $( ".AIRSdatepicker" ).datepicker({
        showOn: "button",
        buttonImage: "javascript/calendar/img.gif",//"images/datePicker.png",
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
        yearRange: '1900:2050',
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
  </script>
<?

echo template($__appvar["templateRefreshFooter"],$content);



function str_putcsv($data)
{

  $data = (array)$data;

  $out = "";
  $header = "";
  $firstRow = true;
  foreach ($data as $row)
  {

    $row = (array)$row;

    $dataRow = array();
    foreach ($row as $k=>$v)
    {
      if ($firstRow)
      {
        $header[] = $k;
      }
      $dataRow[] = str_replace('"',"'",utf8_decode($v));
    }

    if ($firstRow)
    {
      $out[] = '"'.implode('","', $header).'"';
    }
    $out[] = '"'.implode('","', $dataRow).'"';
    $firstRow = false;
  }

  return implode("\r\n", $out);

}

