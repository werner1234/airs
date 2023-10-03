<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/06/18 06:59:57 $
    File Versie         : $Revision: 1.1 $

    $Log: moduleZ_getMisc.php,v $
    Revision 1.1  2018/06/18 06:59:57  cvs
    update naar VRY omgeving

    Revision 1.1  2018/05/25 09:34:52  cvs
    25-5-2018


*/

include_once("wwwvars.php");
include_once ("moduleZ_functions.php");

$data = array_merge($_GET,$_POST);

$mainHeader   = "Diverse waarde vanuit ModuleZ ophalen";
$content['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div>";

echo template($__appvar["templateContentHeader"],$content);


?>
  <link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen"><link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">


  <style>
    fieldset{
      margin: 10px 20px;
    }
    legend{
      width: 200px;
      height: 18px;
      background: #316AC5;
      color: white;
      font-size: 1rem;
      padding: 4px;
    }
    .pageContainer{
      width: 1050px;

    }
  </style>
  <link rel="stylesheet" href="widget/css/font-awesome.min.css">
  <br/>
<div class="pageContainer">

  <fieldset>
    <legend>Products</legend>
    <div id="loadProducts">
      <i class="fa fa-spinner fa-spin" style="font-size:36px"></i> moment aub
    </div>
  </fieldset>

 <fieldset>
    <legend>Riskprofiles</legend>
    <div id="loadRiskprofiles">
      <i class="fa fa-spinner fa-spin" style="font-size:36px"></i> moment aub
    </div>
  </fieldset>

  <fieldset>
    <legend>Advisors</legend>
    <div id="loadAdvisors">
      <i class="fa fa-spinner fa-spin" style="font-size:36px"></i> moment aub
    </div>
  </fieldset>

  <fieldset>
    <legend>Intermediaries</legend>
    <div id="loadIntermediaries">
      <i class="fa fa-spinner fa-spin" style="font-size:36px"></i> moment aub
    </div>
  </fieldset>

  <fieldset>
    <legend>Insurers</legend>
    <div id="loadInsurers">
      <i class="fa fa-spinner fa-spin" style="font-size:36px"></i> moment aub
    </div>
  </fieldset>

  <fieldset>
    <legend>FinancialInstitutes</legend>
    <div id="loadFinancialInstitutes">
      <i class="fa fa-spinner fa-spin" style="font-size:36px"></i> moment aub
    </div>
  </fieldset>


  <br/>
  <br/>
  <br/>

</div>
  <script>

    $(document).ready(function () {

<?
    if ($foutMelding != "")
    {
?>
      $(".foutBody").html("<?=$foutMelding?>");
      $("#foutMelding").show(300);
<?
    }
?>
      $("#loadProducts").load("moduleZ_getMisc_load.php?action=products");
      $("#loadRiskprofiles").load("moduleZ_getMisc_load.php?action=riskprofiles");
      $("#loadAdvisors").load("moduleZ_getMisc_load.php?action=advisors");
      $("#loadIntermediaries").load("moduleZ_getMisc_load.php?action=intermediaries");
      $("#loadInsurers").load("moduleZ_getMisc_load.php?action=insurers");
      $("#loadFinancialInstitutes").load("moduleZ_getMisc_load.php?action=financialinstitutes");

    });

  </script>



<?
echo template($__appvar["templateContentFooter"],$content);

function jsonDate($date)
{
  if ($date == "")
  {
    return "0000-00-00T00:00:00";
  }
  $d = explode("-",$date);
  return $d[2]."-".substr("0".$d[1],-2)."-".substr("0".$d[0],-2)."T00:00:00";
}