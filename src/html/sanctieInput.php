<?php
/*
    AE-ICT sourcemodule created 02 okt. 2019
    Author              : Chris van Santen
    Filename            : sanctieInput.php

*/

include_once("wwwvars.php");

session_start();
$_SESSION["submenu"] = "";
$_SESSION["NAV"] = "";
session_write_close();
$defaultProbability = 80;

$data = $_POST;
$apiUrl = "https://testapi.aml-check.com/v4/namecheck.asmx/";
switch ($__appvar["bedrijf"] )
{
  case "AND":
    $apiUrl = "https://api.aml-check.com/v4/namecheck.asmx/";
    $code   = "c3d392e9-a989-4e9d-b38c-a045a2d1754c";
    break;
  case "ANO":
  case "HOME":
    $code   = "aaa";
    break;
  default:
    $code   = "notSet";

}
//$code   = "c3d392e9-a989-4e9d-b38c-a045a2d1754c";


if ($data["action"] == "go")
{



  $d = explode("-",$data["DateFrom"]);
  $date = $d[2]."-".$d[1]."-".$d[0];
  switch ($data["listType"])
  {
    case "CheckName":
      $url = $apiUrl.$data["listType"]."?code=".$code."&dateFrom=".$date."&Nom=".urlencode($data["Nom"])."&Prenom=".urlencode($data["Prenom"])."&IsPerson=".$data["IsPerson"]."&Probabilite=".$data["Probabilite"];
      $result = file_get_contents($url);
      $xml = (array)simplexml_load_string($result, "SimpleXMLElement");
      $result = str_putcsv($xml["Hit"]);
      $filename = $data["listType"]."-".date("Ymd_Hi").".csv";
      header("Content-type: text/csv");
      header("Content-Disposition: attachment; filename=$filename");
      header("Pragma: no-cache");
      header("Expires: 0");
      echo $result;
      exit;
      break;
    case "CheckNameWithDocument":
      $url = $apiUrl.$data["listType"]."?code=".$code."&dateFrom=".$date."&Nom=".urlencode($data["Nom"])."&Prenom=".urlencode($data["Prenom"])."&IsPerson=".$data["IsPerson"].
             "&Probabilite=".$data["Probabilite"]."&Passport=".urlencode($data["Passport"])."&Language=".$data["Language"];

      $result = file_get_contents($url);

      $xml = simplexml_load_string($result, "SimpleXMLElement", LIBXML_NOCDATA);
      $xml = (array)simplexml_load_string($result, "SimpleXMLElement");

      $x = (array)$xml["Hits"];
//      debug($x["Hit"]);
      $result = str_putcsv($x["Hit"]);
      $filename = $data["listType"]."-".date("Ymd_Hi").".csv";
      header("Content-type: text/csv");
      header("Content-Disposition: attachment; filename=$filename");
      header("Pragma: no-cache");
      header("Expires: 0");
      echo $result;
      exit;
      break;
    case "CreateNameCheckReportInPDF":
      $url = $apiUrl.$data["listType"]."?code=".$code."&dateFrom=".$date."&Nom=".urlencode($data["Nom"])."&Prenom=".urlencode($data["Prenom"])."&IsPerson=".$data["IsPerson"].
             "&Probabilite=".$data["Probabilite"]."&Passport=".urlencode($data["Passport"])."&Language=".$data["Language"];

      $result = file_get_contents($url);

      $xml = (array)simplexml_load_string($result, "SimpleXMLElement", LIBXML_NOCDATA);

      $xml = (array)simplexml_load_string($result, "SimpleXMLElement");
      $pdf = base64_decode($xml[0]);

      $filename = $data["listType"]."-".date("Ymd_Hi").".pdf";
      header("Content-type: application/pdf");
      header("Content-Disposition: attachment; filename=$filename");
      header("Pragma: no-cache");
      header("Expires: 0");
      echo $pdf;
      exit;
      break;
    case "CheckNameExtendedWithNegativeArticles":
      $url = $apiUrl.$data["listType"]."?code=".$code."&dateFrom=".$date."&Name=".urlencode($data["Name"])."&Forename=".urlencode($data["Forename"])."&IsPerson=".$data["IsPerson"].
              "&ProbabilityForSanctionsList=".$data["ProbabilityForSanctionsList"]."&ProbabilityForPepList=".urlencode($data["ProbabilityForPepList"])."&ProbabilityForInterpolList=".$data["ProbabilityForInterpolList"].
              "&CountryRestrictionForPepList=&CountryRestrictionForInterpolList=";

      $result = file_get_contents($url);
      $xml = (array)simplexml_load_string($result, "SimpleXMLElement");
      $x = (array)$xml["NegativeArticles"];

      $result = str_putcsv($x["Article"]);
      $filename = $data["listType"]."-".date("Ymd_Hi").".csv";
      header("Content-type: text/csv");
      header("Content-Disposition: attachment; filename=$filename");
      header("Pragma: no-cache");
      header("Expires: 0");
      echo $result;
      exit;
      break;

  }
  debug ($data);
  exit;
}

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
    display: none;
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
<h1>Name screening</h1>

<form method="post" id="sanctieForm">
  <input type="hidden" name="action" value="go">
  <input type="hidden" name="code" value="<?=$code?>">

<fieldset>
  <legend>soort aanvraag</legend>
  Welke aanvraag wilt u doen?
  <div style="margin-left: 50px">
    <input type="radio" class="radChange" name="listType" value="CreateNameCheckReportInPDF" id="r4" checked><label for="r4" title="9.19 CreateNameCheckReportInPDF"> Check Name with Passport Number PDF </label><br/>
    <input type="radio" class="radChange" name="listType" value="CheckNameExtendedWithNegativeArticles" id="r3" ><label for="r3" title="9.11 CheckNameExtendedWithNegativeArticles"> Check Name with Bad Press </label><br/>
    <input type="radio" class="radChange" name="listType" value="CheckName" id="r1" ><label for="r1" title="9.09 CheckName"> Check Name </label><br/>
    <input type="radio" class="radChange" name="listType" value="CheckNameWithDocument" id="r2"  ><label for="r2" title="9.15 CheckNameWithDocument"> Check Name with Passport Number </label><br/>
  </div>
</fieldset>
<br/>
<br/>

<div id="errorMsg"></div>
<fieldset>
  <legend> variabelen voor aanvraag</legend>
  <div class="vl">DateFrom</div><div class="vr"><div class="vr">
    <input name="DateFrom" class="AIRSdatepicker" type="text" value="01-01-2000" style="width: 100px">
  </div>
  <div class="vl">Natural Person / Legal Entity</div><div class="vr"><select name="IsPerson" id="isPerson"><option value="true">Natural Person</option><option value="false">Legal Entity</option></select> </div>

  <fieldset id="CheckName" class="fldset">
    <legend id="legendName">CheckName</legend>
    <div class="vl">Name</div><div class="vr"><input name="Nom" class="inpStr"></div>
    <div class="hideFirstName"> <div class="vl">First Name</div><div class="vr"><input name="Prenom" class="inpStr"></div> </div>
    <div class="vl">Probability</div><div class="vr"><select name="Probabilite">
        <?
        for ($p=100; $p > 0; $p--)
        {
          $selected = ($p == $defaultProbability)?"SELECTED":"";
          echo "<option value='$p' $selected>$p</option>\n";
        }
        ?>
      </select>
    </div>
    <div id="extraDocumentFields" class="fldset">
      <div class="vl">Passport</div><div class="vr"><input name="Passport" class="inpStr"></div>
      <div class="vl">Language</div><div class="vr"><select name="Language" ><option value="EN">EN - English</option><option value="FR">FR - French</option></select> </select></div>
    </div>
  </fieldset>

    <fieldset id="CheckNameExtendedWithNegativeArticles" class="fldset">
      <legend>CheckNameExtendedWithNegativeArticles</legend>
      <div class="vl">Name</div><div class="vr"><input name="Name" class="inpStr"></div>
      <div class="hideFirstName"> <div class="vl">First Name</div><div class="vr"><input name="Forename" class="inpStr"></div></div>
      <div class="vl">ProbabilityForSanctionsList</div><div class="vr"><select name="ProbabilityForSanctionsList">
          <?
          for ($p=100; $p > 0; $p--)
          {
            $selected = ($p == $defaultProbability)?"SELECTED":"";
            echo "<option value='$p' $selected>$p</option>\n";
          }
          ?>
        </select>
      </div>
      <div class="vl">ProbabilityForPepList</div><div class="vr"><select name="ProbabilityForPepList">
          <?
          for ($p=100; $p > 0; $p--)
          {
            $selected = ($p == $defaultProbability)?"SELECTED":"";
            echo "<option value='$p' $selected>$p</option>\n";
          }
          ?>
        </select>
      </div>
      <div class="vl">ProbabilityForInterpolList</div><div class="vr"><select name="ProbabilityForInterpolList">
          <?
          for ($p=100; $p > 0; $p--)
          {
            $selected = ($p == $defaultProbability)?"SELECTED":"";
            echo "<option value='$p' $selected>$p</option>\n";
          }
          ?>
        </select>
      </div>

      </div>



    </fieldset>

</fieldset>

<br/>
<br/>
  <button id="btnSubmit">Verwerk</button>
</form>


<script>
  $(document).ready(function () {
    var inputType = "CheckNameWithDocument";
    var isPerson  = ($("#isPerson").val() == "true");
    console.log(isPerson);
    $("#CheckName").show();
    $("#extraDocumentFields").show(100);


    $("#isPerson").change(function(){
      isPerson  = ($("#isPerson").val() == "true");
      console.log(isPerson);
      if (isPerson)
      {
        $(".hideFirstName").show(200);
      }
      else
      {
        $("input[name='Forname']").val("");
        $("input[name='Prenom']").val("");
        $(".hideFirstName").hide(200);
      }

    });

    $("#btnSubmit").click(function (e) {
      e.preventDefault();
      var error = "";
      switch (inputType) {
        case "CheckName":
          if (isPerson)
          {
            if ( $("input[name='Nom']").val() == "" ||
                 $("input[name='Prenom']").val() == "") {
              error = "Name en First name mogen niet leeg zijn!";
            }
          }
          else
          {
            if ( $("input[name='Nom']").val() == "" ) {
              error = "Name mag niet leeg zijn!";
            }
          }

          break;
        case "CheckNameWithDocument":
        case "CreateNameCheckReportInPDF":
          if (isPerson)
          {
            if ( $("input[name='Nom']").val() == "" ||
                 $("input[name='Prenom']").val() == "" ||
                 $("input[name='Passport']").val() == "" ) {
              error = "Passport, Name en Firstname mogen niet leeg zijn!";
            }
          }
          else
          {
            if ( $("input[name='Nom']").val() == "" ||
                 $("input[name='Passport']").val() == "" ) {
              error = "Passport en Name mogen niet leeg zijn!";
            }
          }
          break;
        case "CheckNameExtendedWithNegativeArticles":
          if ( $("input[name='Name']").val() == "" ||
               $("input[name='Forname']").val() == "" ) {
            error = "Name en Forname mogen niet leeg zijn!";
          }
          break;
      }
      if (error != "") {
        $("#errorMsg").html(error);
        $("#errorMsg").show(300);
      } else {
        $("#sanctieForm").submit();
      }


    });

    $(".radChange").change(function () {
      var check = $(this).val();
      console.log(check);
      inputType = check;
      if (isPerson)
      {
        $(".hideFirstName").show();
      }
      else
      {
        $(".hideFirstName").hide();
      }
      $(".fldset").hide();
      $("#errorMsg").hide(100);
      switch (check) {
        case "CheckName":
          $("#legendName").text(check);
          $("#CheckName").show(300);
          break;
        case "CheckNameWithDocument":
          $("#legendName").text(check);
          $("#CheckName").show(300);
          $("#extraDocumentFields").show(300);

          break;
        case "CreateNameCheckReportInPDF":
          console.log("bingo");
          $("#legendName").text(check);
          $("#CheckName").show(300);
          $("#extraDocumentFields").show(300);
          // check = "CheckNameWithDocument";
          break;
        case "CheckNameExtendedWithNegativeArticles":
          $("#CheckNameExtendedWithNegativeArticles").show(300);
          break;
        default:

      }
      $("#" + check).show(300);


    });
  });
</script>
<?

echo template($__appvar["templateRefreshFooter"],$content);

function ISOcountry($cc="NL")
{
  $isoCountries =  array(
    "AF" => "Afghanistan",
    "AX" => "Åland Islands",
    "AL" => "Albania",
    "DZ" => "Algeria",
    "AS" => "American Samoa",
    "AD" => "Andorra",
    "AO" => "Angola",
    "AI" => "Anguilla",
    "AQ" => "Antarctica",
    "AG" => "Antigua and Barbuda",
    "AR" => "Argentina",
    "AM" => "Armenia",
    "AW" => "Aruba",
    "AU" => "Australia",
    "AT" => "Austria",
    "AZ" => "Azerbaijan",
    "BS" => "Bahamas",
    "BH" => "Bahrain",
    "BD" => "Bangladesh",
    "BB" => "Barbados",
    "BY" => "Belarus",
    "BE" => "Belgium",
    "BZ" => "Belize",
    "BJ" => "Benin",
    "BM" => "Bermuda",
    "BT" => "Bhutan",
    "BO" => "Bolivia, Plurinational State of",
    "BQ" => "Bonaire, Sint Eustatius and Saba",
    "BA" => "Bosnia and Herzegovina",
    "BW" => "Botswana",
    "BV" => "Bouvet Island",
    "BR" => "Brazil",
    "IO" => "British Indian Ocean Territory",
    "BN" => "Brunei Darussalam",
    "BG" => "Bulgaria",
    "BF" => "Burkina Faso",
    "BI" => "Burundi",
    "KH" => "Cambodia",
    "CM" => "Cameroon",
    "CA" => "Canada",
    "CV" => "Cape Verde",
    "KY" => "Cayman Islands",
    "CF" => "Central African Republic",
    "TD" => "Chad",
    "CL" => "Chile",
    "CN" => "China",
    "CX" => "Christmas Island",
    "CC" => "Cocos (Keeling) Islands",
    "CO" => "Colombia",
    "KM" => "Comoros",
    "CG" => "Congo",
    "CD" => "Congo, the Democratic Republic of the",
    "CK" => "Cook Islands",
    "CR" => "Costa Rica",
    "CI" => "Côte d\'Ivoire",
    "HR" => "Croatia",
    "CU" => "Cuba",
    "CW" => "Curaçao",
    "CY" => "Cyprus",
    "CZ" => "Czech Republic",
    "DK" => "Denmark",
    "DJ" => "Djibouti",
    "DM" => "Dominica",
    "DO" => "Dominican Republic",
    "EC" => "Ecuador",
    "EG" => "Egypt",
    "SV" => "El Salvador",
    "GQ" => "Equatorial Guinea",
    "ER" => "Eritrea",
    "EE" => "Estonia",
    "ET" => "Ethiopia",
    "FK" => "Falkland Islands (Malvinas)",
    "FO" => "Faroe Islands",
    "FJ" => "Fiji",
    "FI" => "Finland",
    "FR" => "France",
    "GF" => "French Guiana",
    "PF" => "French Polynesia",
    "TF" => "French Southern Territories",
    "GA" => "Gabon",
    "GM" => "Gambia",
    "GE" => "Georgia",
    "DE" => "Germany",
    "GH" => "Ghana",
    "GI" => "Gibraltar",
    "GR" => "Greece",
    "GL" => "Greenland",
    "GD" => "Grenada",
    "GP" => "Guadeloupe",
    "GU" => "Guam",
    "GT" => "Guatemala",
    "GG" => "Guernsey",
    "GN" => "Guinea",
    "GW" => "Guinea-Bissau",
    "GY" => "Guyana",
    "HT" => "Haiti",
    "HM" => "Heard Island and McDonald Islands",
    "VA" => "Holy See (Vatican City State)",
    "HN" => "Honduras",
    "HK" => "Hong Kong",
    "HU" => "Hungary",
    "IS" => "Iceland",
    "IN" => "India",
    "ID" => "Indonesia",
    "IR" => "Iran, Islamic Republic of",
    "IQ" => "Iraq",
    "IE" => "Ireland",
    "IM" => "Isle of Man",
    "IL" => "Israel",
    "IT" => "Italy",
    "JM" => "Jamaica",
    "JP" => "Japan",
    "JE" => "Jersey",
    "JO" => "Jordan",
    "KZ" => "Kazakhstan",
    "KE" => "Kenya",
    "KI" => "Kiribati",
    "KP" => "Korea, Democratic People\'s Republic of",
    "KR" => "Korea, Republic of",
    "KW" => "Kuwait",
    "KG" => "Kyrgyzstan",
    "LA" => "Lao People\'s Democratic Republic",
    "LV" => "Latvia",
    "LB" => "Lebanon",
    "LS" => "Lesotho",
    "LR" => "Liberia",
    "LY" => "Libya",
    "LI" => "Liechtenstein",
    "LT" => "Lithuania",
    "LU" => "Luxembourg",
    "MO" => "Macao",
    "MK" => "Macedonia, the former Yugoslav Republic of",
    "MG" => "Madagascar",
    "MW" => "Malawi",
    "MY" => "Malaysia",
    "MV" => "Maldives",
    "ML" => "Mali",
    "MT" => "Malta",
    "MH" => "Marshall Islands",
    "MQ" => "Martinique",
    "MR" => "Mauritania",
    "MU" => "Mauritius",
    "YT" => "Mayotte",
    "MX" => "Mexico",
    "FM" => "Micronesia, Federated States of",
    "MD" => "Moldova, Republic of",
    "MC" => "Monaco",
    "MN" => "Mongolia",
    "ME" => "Montenegro",
    "MS" => "Montserrat",
    "MA" => "Morocco",
    "MZ" => "Mozambique",
    "MM" => "Myanmar",
    "NA" => "Namibia",
    "NR" => "Nauru",
    "NP" => "Nepal",
    "NL" => "Netherlands",
    "NC" => "New Caledonia",
    "NZ" => "New Zealand",
    "NI" => "Nicaragua",
    "NE" => "Niger",
    "NG" => "Nigeria",
    "NU" => "Niue",
    "NF" => "Norfolk Island",
    "MP" => "Northern Mariana Islands",
    "NO" => "Norway",
    "OM" => "Oman",
    "PK" => "Pakistan",
    "PW" => "Palau",
    "PS" => "Palestinian Territory, Occupied",
    "PA" => "Panama",
    "PG" => "Papua New Guinea",
    "PY" => "Paraguay",
    "PE" => "Peru",
    "PH" => "Philippines",
    "PN" => "Pitcairn",
    "PL" => "Poland",
    "PT" => "Portugal",
    "PR" => "Puerto Rico",
    "QA" => "Qatar",
    "RE" => "Réunion",
    "RO" => "Romania",
    "RU" => "Russian Federation",
    "RW" => "Rwanda",
    "BL" => "Saint Barthélemy",
    "SH" => "Saint Helena, Ascension and Tristan da Cunha",
    "KN" => "Saint Kitts and Nevis",
    "LC" => "Saint Lucia",
    "MF" => "Saint Martin (French part)",
    "PM" => "Saint Pierre and Miquelon",
    "VC" => "Saint Vincent and the Grenadines",
    "WS" => "Samoa",
    "SM" => "San Marino",
    "ST" => "Sao Tome and Principe",
    "SA" => "Saudi Arabia",
    "SN" => "Senegal",
    "RS" => "Serbia",
    "SC" => "Seychelles",
    "SL" => "Sierra Leone",
    "SG" => "Singapore",
    "SX" => "Sint Maarten (Dutch part)",
    "SK" => "Slovakia",
    "SI" => "Slovenia",
    "SB" => "Solomon Islands",
    "SO" => "Somalia",
    "ZA" => "South Africa",
    "GS" => "South Georgia and the South Sandwich Islands",
    "SS" => "South Sudan",
    "ES" => "Spain",
    "LK" => "Sri Lanka",
    "SD" => "Sudan",
    "SR" => "Suriname",
    "SJ" => "Svalbard and Jan Mayen",
    "SZ" => "Swaziland",
    "SE" => "Sweden",
    "CH" => "Switzerland",
    "SY" => "Syrian Arab Republic",
    "TW" => "Taiwan, Province of China",
    "TJ" => "Tajikistan",
    "TZ" => "Tanzania, United Republic of",
    "TH" => "Thailand",
    "TL" => "Timor-Leste",
    "TG" => "Togo",
    "TK" => "Tokelau",
    "TO" => "Tonga",
    "TT" => "Trinidad and Tobago",
    "TN" => "Tunisia",
    "TR" => "Turkey",
    "TM" => "Turkmenistan",
    "TC" => "Turks and Caicos Islands",
    "TV" => "Tuvalu",
    "UG" => "Uganda",
    "UA" => "Ukraine",
    "AE" => "United Arab Emirates",
    "GB" => "United Kingdom",
    "US" => "United States",
    "UM" => "United States Minor Outlying Islands",
    "UY" => "Uruguay",
    "UZ" => "Uzbekistan",
    "VU" => "Vanuatu",
    "VE" => "Venezuela, Bolivarian Republic of",
    "VN" => "Viet Nam",
    "VG" => "Virgin Islands, British",
    "VI" => "Virgin Islands, U.S.",
    "WF" => "Wallis and Futuna",
    "EH" => "Western Sahara",
    "YE" => "Yemen",
    "ZM" => "Zambia",
    "ZW" => "Zimbabwe",
  );

  $out = "";
  foreach ($isoCountries as $code => $counrty)
  {
    $selected = ($code == $cc)?"SELECTED":"";
    $out .= "\n<option value='$code' $selected> $code - $counrty</option>";
  }

  return $out;

}

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

