<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/12/14 08:33:04 $
    File Versie         : $Revision: 1.18 $

    $Log: moduleZ_nieuweRekeningEdit.php,v $
    Revision 1.18  2018/12/14 08:33:04  cvs
    call 7410

    Revision 1.17  2018/11/19 14:26:51  cvs
    update naar VRY omgeving

    Revision 1.16  2018/11/07 15:21:08  cvs
    call 7245

    Revision 1.15  2018/11/07 12:49:44  cvs
    call 7282

    Revision 1.14  2018/10/24 07:07:08  cvs
    call 7175

    Revision 1.13  2018/10/19 07:04:18  cvs
    call 7175

    Revision 1.12  2018/10/12 10:47:37  cvs
    call 7175

    Revision 1.11  2018/10/08 06:23:13  cvs
    call 7175, bevindingen 5-10

    Revision 1.10  2018/10/05 06:06:19  cvs
    call 7175

    Revision 1.9  2018/09/23 17:13:57  cvs
    call 7175

    Revision 1.8  2018/09/17 13:44:20  cvs
    update VRY 17-9-2018

    Revision 1.7  2018/09/17 09:42:37  cvs
    trekvelden met leeg beginnen

    Revision 1.6  2018/09/17 09:38:49  cvs
    secondOwner uitgeschakeld

    Revision 1.5  2018/09/17 08:32:51  cvs
    update VRY 17-9-2018

    Revision 1.4  2018/09/14 13:50:02  cvs
    call 6709

    Revision 1.3  2018/09/14 09:38:13  cvs
    Naar VRY omgeving ter TEST

    Revision 1.2  2018/09/07 10:12:34  cvs
    commit voor robert call 6989

    Revision 1.1  2018/06/18 06:59:57  cvs
    update naar VRY omgeving

    Revision 1.1  2018/05/25 09:34:52  cvs
    25-5-2018


*/

include_once("wwwvars.php");

include_once ("moduleZ_functions.php");
//$__debug = true;
$kpl = new AIRS_koppelingen();
$dbl = new DB();

$__funcvar['location'] = "CRM_nawEdit.php";

$data = array_merge($_GET,$_POST);

if ($data["action"] == "go")
{
  $apiData = array();
  $externId = $data["crmExternId"];
//  "secondary_owner" => $data["crmExternId2"],
    $apiData = array(
      "relation_id" => $data["crmExternId"],
      "product_id" => $data["product"],
      "risk_profile_id" => $data["riskprofile"],
      "allow_own_deposit" => ($data["allowOwnDeposit"] == 1)?true:false,
      "start_date" => jsonDate($data["startDate"]),
      "end_date" => jsonDate($data["endDate"]),
      "relation_management" => array(
        "intermediary_id" => trim($data["intermediarie"]),
        "advisor_id" => trim($data["advisor"]),
      ));

    if ($data["stort_bedrag"] != 0)
    {
      $apiData["deposit"]  = array(
      "start_date" => jsonDate($data["stort_startDate"]),
      "end_date" => jsonDate($data["stort_endDate"]),
      "period" => trim($data["stort_interval"]),
      "amount" => (float)$data["stort_bedrag"],
      "currency" => "EUR",
      );
    }
    if (trim($data["onttr_interval"]) != "")
    {
      $apiData["disbursement"] = array(
      "start_date" => jsonDate($data["onttr_startDate"]),
      "end_date" => jsonDate($data["onttr_endDate"]),
      "period" => trim($data["onttr_interval"]),
      );
    }

    $apiData["contra_account"] = array(
      "account_number" => trim($data["IBAN"]),
      "financial_institute_id" => trim($data["financial_institute_id"]),
      "registrant_name" => trim($data["tegen_naam"]),
      "registrant_city" => trim($data["tegen_plaats"]),
    );

    $testBedrag = (float)$data["result_bedrag"];
    if ($testBedrag != 0)
    {
      if (trim($data["result_verzekeraar"]) == "")
      {
        $apiData["expected_amounts"][] = array(
          "amount" => trim($data["result_bedrag"]),
        );
      }
      else
      {
        $apiData["expected_amounts"][] = array(
          "insurer_id" => trim($data["result_verzekeraar"]),
          "amount" => (float)$data["result_bedrag"],
          "policy_number" => trim($data["result_polisnr"])
        );
      }
    }

    for( $x = 2; $x < 6; $x++)
    {
      $var1 = "result_bedrag_".$x;
      $testBedrag = (float)$data[$var1];
      if ($testBedrag != 0)
      {
        if (trim($data["result_verzekeraar_".$x]) == "")
        {
          $apiData["expected_amounts"][] = array(
            "amount" => trim($data["result_bedrag_".$x]),
          );
        }
        else
        {
          $apiData["expected_amounts"][] = array(
            "insurer_id" => trim($data["result_verzekeraar_".$x]),
            "amount" => (float)$data["result_bedrag_".$x],
            "policy_number" => trim($data["result_polisnr_".$x])
          );
        }
      }
    }



  // call 6709 lege velden onderdrukken
  $apiRaw = $apiData;
  $apiData = array();
  foreach ($apiRaw as $k=>$v)
  {
    if (is_array($v))
    {
      foreach ($v as $sk=>$sv)
      {
        if (trim($sv) != "")
        {
          $apiData[$k][$sk] = $sv;
        }
      }
    }
    else
    {
      if (trim($v) != "")
      {
        $apiData[$k] = $v;
      }
    }
  }

  $result =  mzApiPOST("rekeningAdd", json_encode($apiData));
  $result = (array) json_decode($result);

  $okayMelding = "";
  $foutMelding = "";
  if ($mzError["httpCode"] != "")
  {
    $foutMelding = "lowlevel foutcode: ".$mzError["httpCode"];
  }

  if (trim($result["code"]) != 0)
  {
    $foutMelding = $result["code"]." >> ".$result["message"];
  }
  else
  {
    if ($result["status"] == 0 AND trim($result["value"]) != "")
    {

      $db = new DB();
      $query = "SELECT * FROM `CRM_naw` WHERE `externID` = '".$externId."'";
      $nawRec = $db->lookupRecordByQuery($query);

      $client = $externId;
      $portefeuille = trim($result["value"]);

      $dp = explode("-",$data["startDate"]);
      $startDatum = $dp[2]."-".$dp[1]."-".$dp[0];

      $dp = explode("-",$data["endDate"]);
      $eindDatum = $dp[2]."-".$dp[1]."-".$dp[0];

      // client alleen toevoegen als die nog niet bestaat
      $query = "SELECT id FROM `Clienten` WHERE `Client` = '".$client."'";

      if (!$clntRec = $db->lookupRecordByQuery($query))
      {

        $query = "INSERT INTO `Clienten` SET
        `add_user` = '$USR',
        `add_date` = NOW(),
        `change_user` = '$USR',
        `change_date` = NOW(),
        `Client` = '".$client."',
        `Naam` =  '".$client."'
        
      ";
//      debug($query);
        $db->executeQuery($query);
      }
      else
      {
        //
      }

      $q = array();
      $query = "SELECT * FROM `CRM_naw` WHERE `portefeuille` != '' AND `externID` = '".$externId."'";
      if ($tRec = $db->lookupRecordByQuery($query))
      {

        $query = "
          INSERT INTO CRM_naw SET
            `add_user` = '$USR',
            `add_date` = NOW(),
            `change_user` = '$USR',
            `change_date` = NOW(),
            `voorletters` = '".$tRec["voorletters"]."',
            `tussenvoegsel` = '".$tRec["tussenvoegsel"]."',
            `achternaam` = '".$tRec["achternaam"]."',
            `naam` = '".$tRec["naam"]."',
            `zoekveld` = '".$tRec["zoekveld"]."',
            `BSN` = '".$tRec["BSN"]."',
            `externID` = '".$externId."',
            `portefeuille` = '".$portefeuille."',
            `aktief` = 1,
            `debiteur` = 1
      ";
        $db->executeQuery($query);
        $lId = $db->last_id();
        if ($lId > 0)
        {
          addTrackAndTrace("CRM_naw", $lId, "voorletters", "", $tRec["voorletters"], $USR) ;
          addTrackAndTrace("CRM_naw", $lId, "tussenvoegsel", "", $tRec["tussenvoegsel"], $USR) ;
          addTrackAndTrace("CRM_naw", $lId, "achternaam", "", $tRec["achternaam"], $USR) ;
          addTrackAndTrace("CRM_naw", $lId, "naam", "", $tRec["naam"], $USR) ;
          addTrackAndTrace("CRM_naw", $lId, "zoekveld", "", $tRec["zoekveld"], $USR) ;
          addTrackAndTrace("CRM_naw", $lId, "BSN", "", $tRec["BSN"], $USR) ;
          addTrackAndTrace("CRM_naw", $lId, "externID", "", $externId, $USR) ;
          addTrackAndTrace("CRM_naw", $lId, "portefeuille", "", $portefeuille, $USR) ;
          addTrackAndTrace("CRM_naw", $lId, "aktief", "", 1, $USR) ;
          addTrackAndTrace("CRM_naw", $lId, "debiteur", "", 1, $USR) ;
        }


      }
      else
      {

        $query = "SELECT id FROM `CRM_naw` WHERE `externID` = '".$externId."'";
        $CRMrec = $db->lookupRecordByQuery($query);

        $query = "UPDATE `CRM_naw` SET portefeuille = '$portefeuille' WHERE `id` = '".$CRMrec["id"]."'";
        $db->executeQuery($query);
        addTrackAndTrace("CRM_naw", $CRMrec["id"], "portefeuille", "", $portefeuille, $USR) ;

      }

//      debug($query);


      $overeenkomst   = $kpl->showAirsDescription($data["product"], "products");
      $risico         = $kpl->showAirsDescription($data["riskprofile"], "riskprofiles");

      $parts = explode(" ",$kpl->showAirsDescription($data["advisor"], "advisors"));
      $last  = end($parts);
      $accountmanager = strtoupper($parts[0][0].substr($parts[0],-1).$last[0].substr($last,-1));

      $remisier       = $kpl->showAirsDescription($data["intermediarie"], "intermediaries");

      $query = "
                SELECT
                 Portefeuilles.Portefeuille
                FROM
                  Portefeuilles
                JOIN ModelPortefeuilles ON
                  ModelPortefeuilles.Portefeuille = Portefeuilles.Portefeuille
                WHERE
                  SoortOvereenkomst = '{$overeenkomst}' AND 
                  Risicoklasse = '{$risico}' ";
      $mdlRec = $dbl->lookupRecordByQuery($query);

      $query = "
      INSERT INTO `Portefeuilles` SET
      `add_user` = '$USR',
      `add_date` = NOW(),
      `change_user` = '$USR',
      `change_date` = NOW(),
      `Portefeuille` = '".$portefeuille."',
      `Client` = '".$client."',
      `Vermogensbeheerder` = 'VRY',
      `ModelPortefeuille` = '".$mdlRec["Portefeuille"]."',
      `Risicoklasse` = '".$risico."',
      `SoortOvereenkomst` = '".$overeenkomst."',
      `Einddatum` = '2049-12-31',
      `Accountmanager` = '".$accountmanager."',
      `Remisier` = '".$remisier."',
      `RapportageValuta` = 'EUR',
      `Depotbank` = 'MDZ',
      `Taal` = '0'
      ";
//      debug($query);
      $db->executeQuery($query);
      $lId = $db->last_id();
      if ($lId > 0)
      {
        addTrackAndTrace("Portefeuilles", $lId, "Portefeuille", "", $portefeuille, $USR) ;
        addTrackAndTrace("Portefeuilles", $lId, "Client", "", $client, $USR) ;
        addTrackAndTrace("Portefeuilles", $lId, "Vermogensbeheerder", "", 'VRY', $USR) ;
        addTrackAndTrace("Portefeuilles", $lId, "ModelPortefeuille", "", $mdlRec["Portefeuille"], $USR) ;
        addTrackAndTrace("Portefeuilles", $lId, "Risicoklasse", "", $risico, $USR) ;
        addTrackAndTrace("Portefeuilles", $lId, "SoortOvereenkomst", "", $overeenkomst, $USR) ;
        addTrackAndTrace("Portefeuilles", $lId, "Einddatum", "", $eindDatum, $USR) ;
        addTrackAndTrace("Portefeuilles", $lId, "Accountmanager", "", $accountmanager, $USR) ;
        addTrackAndTrace("Portefeuilles", $lId, "Remisier", "", $remisier, $USR) ;
        addTrackAndTrace("Portefeuilles", $lId, "RapportageValuta", "", 'EUR', $USR) ;
        addTrackAndTrace("Portefeuilles", $lId, "Depotbank", "", 'MDZ', $USR) ;
        addTrackAndTrace("Portefeuilles", $lId, "Taal", "", 0, $USR) ;
      }


      $query = "
      INSERT INTO `Rekeningen` SET
      `add_user` = '$USR',
      `add_date` = NOW(),
      `change_user` = '$USR',
      `change_date` = NOW(),
      `Portefeuille` = '".$portefeuille."',
      `Rekening` = '".$portefeuille."EUR',
      `Valuta` = 'EUR',
      `Memoriaal` = '0',
      `Deposito` = '0',
      `Depotbank` = 'MDZ'
      ";
//      debug($query);
      $db->executeQuery($query);
      $lId = $db->last_id();
      if ($lId > 0)
      {
        addTrackAndTrace("Rekeningen", $lId, "Portefeuille", "", $portefeuille, $USR) ;
        addTrackAndTrace("Rekeningen", $lId, "Rekening", "", $portefeuille."EUR", $USR) ;
        addTrackAndTrace("Rekeningen", $lId, "Valuta", "", 'EUR', $USR) ;
        addTrackAndTrace("Rekeningen", $lId, "Memoriaal", "", '0', $USR) ;
        addTrackAndTrace("Rekeningen", $lId, "Deposito", "", '0', $USR) ;
        addTrackAndTrace("Rekeningen", $lId, "Depotbank", "", 'MDZ', $USR) ;
      }

      $query = "
      INSERT INTO `Rekeningen` SET
      `add_user` = '$USR',
      `add_date` = NOW(),
      `change_user` = '$USR',
      `change_date` = NOW(),
      `Portefeuille` = '".$portefeuille."',
      `Rekening` = '".$portefeuille."MEM',
      `Valuta` = 'EUR',
      `Memoriaal` = '1',
      `Deposito` = '0',
      `Depotbank` = 'MDZ'
      ";
//      debug($query);
      $db->executeQuery($query);
      $lId = $db->last_id();
      if ($lId > 0)
      {
        addTrackAndTrace("Rekeningen", $lId, "Portefeuille", "", $portefeuille, $USR) ;
        addTrackAndTrace("Rekeningen", $lId, "Rekening", "", $portefeuille."EUR", $USR) ;
        addTrackAndTrace("Rekeningen", $lId, "Valuta", "", 'MEM', $USR) ;
        addTrackAndTrace("Rekeningen", $lId, "Memoriaal", "", '1', $USR) ;
        addTrackAndTrace("Rekeningen", $lId, "Deposito", "", '0', $USR) ;
        addTrackAndTrace("Rekeningen", $lId, "Depotbank", "", 'MDZ', $USR) ;
      }
      $okayMelding = "Rekening met nummer {$result["value"]} aangemaakt";
    }
    else
    {
      $foutMelding = "lowlevel Error httpCode: ".$result["httpCode"];
    }
  }
}



$db = new DB();




$kpl->getModuleRecords("products");
$JS = "var switches = [];";
foreach ($kpl->dataSet["products"] as $key=>$switch)
{
  $s = unserialize($switch["externExtra"]);

  $JS .= "\n\t switches['{$key}'] = [{$s['allow_secondary_owner']},{$s['allow_own_deposit']},{$s['allow_periodic_deposit']},{$s['allow_disbursement']}];";
}

///////////////////////////////////////////////////////////////////////////////

  $_SESSION['NAV'] = "";
  $_SESSION['submenu'] = New Submenu();
  $mainHeader   = "Rekening toevoegen in ModuleZ";
  $content['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div>";
  echo template($__appvar["templateContentHeader"],$content);



?>
  <link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen"><link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">


  <style>

    .ui-autocomplete {
      max-height: 100px;
      overflow-y: auto;
      /* prevent horizontal scrollbar */
      overflow-x: hidden;
    }


    INPUT{
      width: 400px;
    }
    .AIRSdatepicker{
      width: 100px;
    }

    fieldset{
      width: 1000px;
      margin: 10px;
    }
    legend{
      background: #DDD;
      padding: 4px 10px;
      font-weight: bold;
    }
    .bedrag{
      width: 100px;
      text-align: right;
    }

    #foutMelding{
      display: none;
      width: 1000px;
      height: auto;
      color: whitesmoke;
      padding: 0;
      border:1px solid #999;
      box-shadow: 4px 4px 4px #333;
    }
    .foutHead{
      background: maroon;
      color: whitesmoke;
      padding: 10px 20px;
      font-size: 1.3em;
      width: calc(100%-40px);
      height: 20px;
    }
    .foutBody{
      background: beige;
      color: maroon;
      width: calc(100%-40px);
      padding: 10px;
    }
    #okayMelding{
      display: none;
      width: 1000px;
      height: auto;
      color: whitesmoke;
      padding: 0;
      border:1px solid #999;
      box-shadow: 4px 4px 4px #333;
    }
    .okayHead{
      background: #4D9200;
      color: whitesmoke;
      padding: 10px 20px;
      font-size: 1.3em;
      width: calc(100%-40px);
      height: 20px;
    }
    .okayBody{
      background: beige;
      color: #4D9200;
      width: calc(100%-40px);
      padding: 10px;
    }
    #loading{
      display: none;
      position: absolute;
      box-sizing: padding-box;
      z-index:999;
      background: rgba(50,50,50,.5);
      color: white;
      font-size: 2rem;
      background-repeat: no-repeat;
      background-position: center;
      padding: 520px 425px;
    }
    #crmExternId, #crmExternId2  {
      width: 80px;
      border: 1px #999 solid;
      border-radius: 5px;
      background: #DDD;
      color: #666;
      text-align: center;
    }
    .pageContainer{
      width: 1050px;

    }
    .resBlock{
      border:1px #666 solid;
      display: none;
    }
    .resBlockHeader{
      background: #666;
      color: white;
    }
    .resBlockContent{
      padding: 10px;
    }

    #secOwnerDisabled{
      display:none;
    }
    .resBlockFooter{
      text-align: right;
    }
    .resBlockFooter ::after{
      content: " ";
      clear: both;
    }
  </style>
  <link rel="stylesheet" href="widget/css/font-awesome.min.css">
  <br/>
  <div class="pageContainer">


    <div id="foutMelding">
      <div class="foutHead">Foutmelding(en)</div>
      <div class="foutBody"></div>
    </div>
    <div id="okayMelding">
       <div class="okayHead">Succes</div>
        <div class="okayBody"></div>
    </div>
    <br/>
    <br/>
    <br/>
    <form name="editForm"  method="POST" id="newForm">
      <input type="hidden" name="action" value="go">

      <div id="loading"><i class="fa fa-spinner fa-spin" style="font-size:36px"></i> moment aub</div>

    <fieldset >
      <legend> Rekeninginfo</legend>




      <div class="formblock">
        <div class="formlinks">Relatie</div>
        <div class="formrechts">
          <input type="text" name="crmName" id="crmName" value="<?=$data["crmName"]?>"/>
          <input type="text" readonly name="crmExternId" id="crmExternId" value="<?=$data["crmExternId"]?>" tabindex="-1">
        </div>
      </div>
<!--      <div class="formblock">-->
<!--        <div class="formlinks">Tweede relatie</div>-->
<!--        <div class="formrechts" >-->
<!--          <span id="secOwnerInput">-->
<!--            <input type="text" name="crmName2" id="crmName2" value="--><?//=$data["crmName2"]?><!--"/>-->
<!--            <input type="text" readonly name="crmExternId2" id="crmExternId2" value="--><?//=$data["crmExternId2"]?><!--" tabindex="-1">-->
<!--          </span>-->
<!--          <span id="secOwnerDisabled">-->
<!--            uitgeschakeld bij geselecteerd produkt-->
<!--          </span>-->
<!--        </div>-->
<!--      </div>-->

      <div class="formblock">
        <div class="formlinks">Produkt</div>
        <div class="formrechts">
          <select name="product" id="productChange" class="emptyCheck" data-veldnaam="Produkt">
            <?=getMzOptions("products",$data["product"],true);?>
          </select>
        </div>
      </div>

      <div class="formblock">
        <div class="formlinks">Risicoprofiel</div>
        <div class="formrechts">
          <select name="riskprofile">
            <?=getMzOptions("riskprofiles",$data["riskprofile"],true);?>
          </select>
        </div>
      </div>

      <div class="formblock">
        <div class="formlinks">allow own deposit</div>
        <div class="formrechts">
          <select name="allowOwnDeposit" id="allowOwnDeposit">
            <option value="0" >Nee</option>
            <option value="1" >Ja</option>
          </select>
        </div>
      </div>

      <div class="formblock">
        <div class="formlinks">Start datum</div>
        <div class="formrechts">
          <button class="clearDate" data-id="startDate"><i class="fa fa-trash"></i></button>
          <input class="AIRSdatepicker datecheck" name="startDate" id="startDate" placeholder="   klik -->" readonly
                 title="selecteer de datum via de calender" value="<?=$data["startDate"]?>"
                 data-veldnaam="Start datum">
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks">Eind datum</div>
        <div class="formrechts">
          <button class="clearDate" data-id="endDate"><i class="fa fa-trash"></i></button>
          <input type="text" class="AIRSdatepicker datecheck" name="endDate" id="endDate" placeholder="   klik -->" readonly
                 title="selecteer de datum via de calender" value="<?=$data["endDate"]?>"
                 data-veldnaam="Eind datum">
        </div>
      </div>

      <div class="formblock">
        <div class="formlinks">Tussenpersoon</div>
        <div class="formrechts">
          <select name="intermediarie" class="emptyCheck" data-veldnaam="Tussenpersoon">
            <?=getMzOptions("intermediaries",$data["intermediarie"],true);?>
          </select>
        </div>
      </div>

      <div class="formblock">
        <div class="formlinks">Adviseur</div>
        <div class="formrechts">
          <select name="advisor" class="emptyCheck" data-veldnaam="Adviseur">
            <?=getMzOptions("advisors",$data["advisor"],true);?>
          </select>
        </div>
      </div>
    </fieldset>
    <br/><br/>
    <fieldset id="fldset_periodic_deposit" >
      <legend> Inleg</legend>

      <div class="formblock">
        <div class="formlinks">Start datum</div>
        <div class="formrechts">
          <button class="clearDate" data-id="stort_startDate"><i class="fa fa-trash"></i></button>
          <input type="text" class="AIRSdatepicker " name="stort_startDate" id="stort_startDate" placeholder="   klik -->" readonly
                 title="selecteer de datum via de calender" value="<?=$data["stort_startDate"]?>"
                 data-veldnaam="Inleg Start datum">
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks">Eind datum</div>
        <div class="formrechts">
          <button class="clearDate" data-id="stort_endDate"><i class="fa fa-trash"></i></button>
          <input type="text" class="AIRSdatepicker " name="stort_endDate" id="stort_endDate" placeholder="   klik -->" readonly
                 title="selecteer de datum via de calender" value="<?=$data["stort_endDate"]?>"
                 data-veldnaam="Inleg Eind datum">
        </div>
      </div>

      <div class="formblock">
        <div class="formlinks">Interval</div>
        <div class="formrechts">
          <select name="stort_interval" id="stort_interval">
            <?=getMzOptions("stortInterval",$data["stort_interval"],true);?>
          </select>
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks">Bedrag <b>&euro;</b> </div>
        <div class="formrechts">
          <input type="text" name="stort_bedrag" id="stort_bedrag" value="<?=(float)$data["stort_bedrag"]?>" class="bedrag">
        </div>
      </div>
    </fieldset>

    <br/><br/>
    <fieldset id="fldset_disbursement">
      <legend> Uitbetaling</legend>
      <div class="formblock">
        <div class="formlinks">Start datum</div>
        <div class="formrechts">

          <button class="clearDate" data-id="onttr_startDate"><i class="fa fa-trash"></i></button>
          <input type="text" class="AIRSdatepicker " name="onttr_startDate" id="onttr_startDate" placeholder="   klik -->"
                 title="selecteer de datum via de calender" value="<?=$data["onttr_startDate"]?>"
                 data-veldnaam="Uitbetaling Start datum">
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks">Eind datum</div>
        <div class="formrechts">
          <button class="clearDate" data-id="onttr_endDate"><i class="fa fa-trash"></i></button>
          <input type="text" class="AIRSdatepicker " name="onttr_endDate" id="onttr_endDate" placeholder="   klik -->" readonly
                 title="selecteer de datum via de calender" value="<?=$data["onttr_endDate"]?>"
                 data-veldnaam="Uitbetaling Eind datum">
        </div>
      </div>

      <div class="formblock">
        <div class="formlinks">Interval</div>
        <div class="formrechts">
          <select name="onttr_interval" id="onttr_interval">
            <?=getMzOptions("onttrInterval",$data["onttr_interval"],true);?>
          </select>

        </div>
      </div>


    </fieldset>
    <br/><br/>
    <fieldset>
      <legend>  Tegenrekening</legend>
      <div class="formblock">
        <div class="formlinks">IBAN</div>
        <div class="formrechts">
          <input type="text" name="IBAN" value="<?=$data["IBAN"]?>" class="emptyCheck" data-veldnaam="IBAN">
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks">Bank</div>
        <div class="formrechts">
          <select name="financial_institute_id"  class="emptyCheck" data-veldnaam="Bank">
            <?=getMzOptions("financialinstitutes",$data["financial_institute_id"],true);?>
          </select>
        </div>
      </div>

      <div class="formblock">
        <div class="formlinks">Naam</div>
        <div class="formrechts">
          <input type="text" name="tegen_naam" value="<?=$data["tegen_naam"]?>"  class="emptyCheck" data-veldnaam="Naam rekeninghouder">
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks">Plaats</div>
        <div class="formrechts">
          <input type="text" name="tegen_plaats" value="<?=$data["tegen_plaats"]?>"  class="emptyCheck" data-veldnaam="Plaats rekeninghouder">
        </div>
      </div>
    </fieldset>
    <br/><br/>
    <fieldset>
      <legend>  Verwacht resultaat</legend>
      <div id="resBlock_1" class="resBlock">
        <div class="resBlockHeader">invoer 1:</div>
        <div class="resBlockContent">
          <div class="formblock">
            <div class="formlinks">Verzekeraar</div>
            <div class="formrechts">
              <select name="result_verzekeraar">
                <?=getMzOptions("insurers",$data["result_verzekeraar"],true);?>
              </select>

            </div>
          </div>

          <div class="formblock">
            <div class="formlinks">Bedrag <b>&euro;</b> </div>
            <div class="formrechts">
              <input type="text" name="result_bedrag" value="<?=(float)$data["result_bedrag"]?>"  class="bedrag">
            </div>
          </div>
          <div class="formblock">
            <div class="formlinks">Polisnummer</div>
            <div class="formrechts">
              <input type="text" name="result_polisnr" value="<?=$data["result_polisnr"]?>">
            </div>
          </div>
        </div>
        <div class="resBlockFooter">
          <button class="openResBlock" data-id="resBlock_2"><i class="fa fa-plus"></i></button>
        </div>

      </div>
      <div id="resBlock_2" class="resBlock">
        <div class="resBlockHeader">invoer 2:</div>
        <div class="resBlockContent">
          <div class="formblock">
            <div class="formlinks">Verzekeraar</div>
            <div class="formrechts">
              <select name="result_verzekeraar_2">
                <?=getMzOptions("insurers",$data["result_verzekeraar_2"],true);?>
              </select>

            </div>
          </div>

          <div class="formblock">
            <div class="formlinks">Bedrag <b>&euro;</b> </div>
            <div class="formrechts">
              <input type="text" name="result_bedrag_2" value="<?=(float)$data["result_bedrag_2"]?>"  class="bedrag">
            </div>
          </div>
          <div class="formblock">
            <div class="formlinks">Polisnummer</div>
            <div class="formrechts">
              <input type="text" name="result_polisnr_2" value="<?=$data["result_polisnr_2"]?>">
            </div>
          </div>
        </div>
        <div class="resBlockFooter">
          <button class="openResBlock" data-id="resBlock_3"><i class="fa fa-plus"></i></button>
        </div>
      </div>
      <div id="resBlock_3" class="resBlock">
        <div class="resBlockHeader">invoer 3:</div>
        <div class="resBlockContent">
          <div class="formblock">
            <div class="formlinks">Verzekeraar</div>
            <div class="formrechts">
              <select name="result_verzekeraar_3">
                <?=getMzOptions("insurers",$data["result_verzekeraar_3"],true);?>
              </select>

            </div>
          </div>

          <div class="formblock">
            <div class="formlinks">Bedrag <b>&euro;</b> </div>
            <div class="formrechts">
              <input type="text" name="result_bedrag_3" value="<?=(float)$data["result_bedrag_3"]?>"  class="bedrag">
            </div>
          </div>
          <div class="formblock">
            <div class="formlinks">Polisnummer</div>
            <div class="formrechts">
              <input type="text" name="result_polisnr_3" value="<?=$data["result_polisnr_3"]?>">
            </div>
          </div>
        </div>
        <div class="resBlockFooter">
          <button class="openResBlock" data-id="resBlock_4"><i class="fa fa-plus"></i></button>
        </div>
      </div>
      <div id="resBlock_4" class="resBlock">
        <div class="resBlockHeader">invoer 4:</div>
        <div class="resBlockContent">
          <div class="formblock">
            <div class="formlinks">Verzekeraar</div>
            <div class="formrechts">
              <select name="result_verzekeraar_4">
                <?=getMzOptions("insurers",$data["result_verzekeraar_4"],true);?>
              </select>

            </div>
          </div>

          <div class="formblock">
            <div class="formlinks">Bedrag <b>&euro;</b> </div>
            <div class="formrechts">
              <input type="text" name="result_bedrag_4" value="<?=(float)$data["result_bedrag_4"]?>"  class="bedrag">
            </div>
          </div>
          <div class="formblock">
            <div class="formlinks">Polisnummer</div>
            <div class="formrechts">
              <input type="text" name="result_polisnr_4" value="<?=$data["result_polisnr_4"]?>">
            </div>
          </div>
        </div>
        <div class="resBlockFooter">
          <button class="openResBlock" data-id="resBlock_5"><i class="fa fa-plus"></i></button>
        </div>
      </div>
      <div id="resBlock_5" class="resBlock">
        <div class="resBlockHeader">invoer 5:</div>
        <div class="resBlockContent">
          <div class="formblock">
            <div class="formlinks">Verzekeraar</div>
            <div class="formrechts">
              <select name="result_verzekeraar_5">
                <?=getMzOptions("insurers",$data["result_verzekeraar_5"],true);?>
              </select>

            </div>
          </div>

          <div class="formblock">
            <div class="formlinks">Bedrag <b>&euro;</b> </div>
            <div class="formrechts">
              <input type="text" name="result_bedrag_5" value="<?=(float)$data["result_bedrag_5"]?>"  class="bedrag">
            </div>
          </div>
          <div class="formblock">
            <div class="formlinks">Polisnummer</div>
            <div class="formrechts">
              <input type="text" name="result_polisnr_5" value="<?=$data["result_polisnr_5"]?>">
            </div>
          </div>
        </div>
        <div class="resBlockFooter">
          &nbsp;
        </div>
      </div>
    </fieldset>
    <br/><br/>
    <button id="btnSubmit">naar ModuleZ zenden</button>

  </form>

  <script>

    $(document).ready(function () {

      <?=$JS?>


      // $("#onttr_startDate").change(function(){
      //   var oDate = $(this).val();
      //   var sDate = $("#startDate").val();
      //   console.log("startdate ",oDate, " - ", sDate);
      //   var d = dateCompare(oDate,sDate);
      //   if ( d <= 0)
      //   {
      //     console.log(`startdatum ${d} jonger dan uitkeringdatum `);
      //   }
      //   console.log(`dagen test : ${d} `);
      // });

      $("#onttr_endDate").change(function(){
        var oDate = $(this).val();
        var sDate = $("#endDate").val();
        console.log("enddate ",oDate, " - ", sDate);

      });
      $("#resBlock_1").show();

      $(".openResBlock").click(function (e) {
        e.preventDefault();
        var blk = $(this).data("id");
        console.log(blk);
        $("#"+blk).show(300);
      });


      console.log(switches);
      $("#productChange").change(function()
      {
        var val = $(this).val();
        var secOwn  = switches[val][0];
        var ownDesp = switches[val][1];
        var erDesp  = switches[val][2];
        var disbur  = switches[val][3];
        console.log("secOwn = " + secOwn + ", ownDesp = " + ownDesp + ", erDesp = " + erDesp + ", disbur = " + disbur );
        if (ownDesp == 0)
        {
          $("#allowOwnDeposit").val(0);
          $("#allowOwnDeposit").prop('disabled', 'disabled');
        }
        else
        {
          $("#allowOwnDeposit").prop('disabled', false);
        }



        if (secOwn == 0)
        {
        //   $("#secOwnerInput").hide(300);
        //   $("#secOwnerDisabled").show(300);
        //   $("#crmName2").val("");
        //   $("#crmExternId2").val("");
        // }
        // else
        // {
        //   $("#secOwnerInput").show(300);
        //   $("#secOwnerDisabled").hide(300);
        }

        if (erDesp == 0)
        {
          $("#fldset_periodic_deposit legend").text(" Inleg (uitgeschakeld bij dit produkt)");
          $("#fldset_periodic_deposit div").hide(300);
          $("#stort_startDate").val("");
          $("#onttr_startDate").prop('disabled', 'disabled');
          $("#stort_endDate").val("");
          $("#stort_endDate").prop('disabled', 'disabled');
          $("#stort_interval").val("");
          $("#stort_interval").prop('disabled', 'disabled');
          $("#stort_bedrag").val("");
          $("#stort_bedrag").prop('disabled', 'disabled');
        }
        else
        {
          $("#fldset_periodic_deposit legend").text(" Inleg");
          $("#fldset_periodic_deposit div").show(300);
          $("#stort_endDate").prop('disabled', false);
          $("#onttr_endDate").prop('disabled', false);
          $("#stort_interval").prop('disabled', false);
          $("#stort_bedrag").prop('disabled', false);
        }

        if (disbur == 0)
        {
          $("#fldset_disbursement legend").text(" Uitbetaling (uitgeschakeld bij dit produkt)");
          $("#fldset_disbursement div").hide(300);
          $("#onttr_startDate").val("");
          $("#onttr_startDate").prop('disabled', 'disabled');
          $("#onttr_endDate").val("");
          $("#onttr_endDate").prop('disabled', 'disabled');
          $("#onttr_interval").val("");
          $("#onttr_interval").prop('disabled', 'disabled');

        }
        else
        {
          $("#fldset_disbursement legend").text(" Uitbetaling");
          $("#fldset_disbursement div").show(300);
          $("#onttr_startDate").prop('disabled', false);
          $("#onttr_endDate").prop('disabled', false);
          $("#onttr_interval").prop('disabled', false);
        }
      });
<?
      if ($foutMelding != "")
      {
?>
      $(".foutBody").html("<?=$foutMelding?>");
      $("#foutMelding").show(300);
<?
      }
      if ($okayMelding != "")
      {
?>
      $(".okayBody").html("<?=$okayMelding?>");
      $("#okayMelding").show(300);
<?
      }
?>

      $(".ui-autocomplete-input").css("min-width","600px");

      $(".clearDate").click(function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        $("#"+id).val("");
      });

      $("#crmName").autocomplete(
        {
          source: "moduleZ_getClient.php",                      // link naar lookup script
          change: function(e, ui)
          {
            if (!ui.item)
            {
              $( "#popup" ).dialog("open");
              $("#crmName").val("");
              $("#crmExternId").val("");                        // reset waarde als niet uit de lookup
            }
          },
          select: function(event, ui)                           // bij selectie clientside vars updaten
          {
            $("#crmName").val(ui.item.naam);
            $("#crmExternId").val(ui.item.externID);
          },
          close: function(){

          },
          response: function( event, ui )
          {

          },
          open: function()
          {
            $(".ui-autocomplete").css("width", "500px");
          },
          minLength: 2,                                         // pas na de tweede letter starten met zoeken
          delay: 0,
          autoFocus: true
        });

      $("#crmName2").autocomplete(
        {
          source: "moduleZ_getClient.php",                      // link naar lookup script
          change: function(e, ui)
          {
            if (!ui.item)
            {
              $( "#popup" ).dialog("open");
              $("#crmName2").val("");
              $("#crmExternId2").val("");                        // reset waarde als niet uit de lookup
            }
          },
          select: function(event, ui)                           // bij selectie clientside vars updaten
          {
            $("#crmName2").val(ui.item.naam);
            $("#crmExternId2").val(ui.item.externID);
          },
          close: function(){

          },
          response: function( event, ui )
          {

          },
          open: function()
          {
            $(".ui-autocomplete").css("width", "500px");
          },
          minLength: 2,                                         // pas na de tweede letter starten met zoeken
          delay: 0,
          autoFocus: true
        });


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


      $("#btnSubmit").click(function (e) {
        var errors = "";
        e.preventDefault();
        $(".datecheck").each(function (i) {
          var value = $(this).val();
          if (!isValidDate(value) || value == "")
          {
            $(this).css("background","mistyrose");
            errors += "\n<li><b>"+$(this).data("veldnaam")+"</b> ongeldige datum opgegeven";
          }
          else
          {
            $(this).css("background","initial");
          }
        });
        $(".emptyCheck").each(function (i) {
          var value = $(this).val();
          if (value == "")
          {

            $(this).css("background","mistyrose");
            errors += "\n<li><b>"+$(this).data("veldnaam")+"</b> verplicht veld";
          }
          else
          {
            $(this).css("background","initial");
          }
        });
        if ($("#crmExternId").val() == "")
        {
          errors += "\n<li> geen geldige <b>Relatie</b> geselecteerd";
          $("#crmName").css("background","mistyrose");
        }
        else
        {
          $("#crmName").css("background","initial");
        }

        var oDate = $("#onttr_startDate").val();
        if (oDate != "")
        {
          var sDate = $("#startDate").val();
          var d = dateCompare(oDate,sDate);
          if ( d <= 0)
          {
            errors += "\n<li>start datum uitbetaling eerder dan start datum";
          }
        }

        var oDate = $("#onttr_endDate").val();
        if (oDate != "")
        {
          var sDate = $("#endDate").val();
          var d = dateCompare(sDate,oDate);
          if ( d <= 0)
          {
            errors += "\n<li>eind datum uitbetaling later dan eind datum";
          }
        }

        var oDate = $("#onttr_endDate").val();
        if (oDate != "")
        {
          var sDate = $("#startDate").val();
          var d = dateCompare(oDate,sDate);
          if ( d <= 0)
          {
            errors += "\n<li>eind datum uitbetaling eerder dan start datum";
          }
        }

        var oDate = $("#stort_startDate").val();
        if (oDate != "")
        {
          var sDate = $("#startDate").val();
          var d = dateCompare(oDate,sDate);
          if ( d <= 0)
          {
            errors += "\n<li>start datum inleg eerder dan start datum";
          }
        }

        var oDate = $("#stort_endDate").val();
        if (oDate != "")
        {
          var sDate = $("#endDate").val();
          var d = dateCompare(sDate,oDate);
          if ( d <= 0)
          {
            errors += "\n<li>eind datum inleg later dan eind datum";
          }
        }

        var oDate = $("#stort_endDate").val();
        if (oDate != "")
        {
          var sDate = $("#startDate").val();
          var d = dateCompare(oDate,sDate);
          if ( d <= 0)
          {
            errors += "\n<li>eind datum inleg eerder dan start datum";
          }
        }

        if (errors.length > 1)
        {
          $(".foutBody").html(errors);
          $("#foutMelding").show(300);
          $("html, body").animate({ scrollTop: 0 }, 300);
          return false;
        }
        $("#loading").show(100);
        $("#newForm").submit();
      });

    });



    function isValidDate(s) {
      var bits = s.split('-');
      var d = new Date(bits[2] + '/' + bits[1] + '/' + bits[0]);
      return !!(d && (d.getMonth() + 1) == bits[1] && d.getDate() == Number(bits[0]));
    }

    function dateCompare(dateNew, dateOld)
    {
      var dp1 = dateNew.split("-");
      var dNew = new Date(dp1[2],eval(dp1[1])-1,dp1[0]);
      var dp2 = dateOld.split("-");
      var dOld = new Date(dp2[2],eval(dp2[1])-1,dp2[0]);
      var dayNew = dNew.getTime()/86400000;
      var dayOld = dOld.getTime()/86400000;
      return (dayNew - dayOld);
    }



  </script>

<?
echo template($__appvar["templateRefreshFooter"],$content);

////////////////////////////////////////////////////////////////////////////////

function getMzOptions($list, $value, $emptyFirst=false)
{
  $kpl = new AIRS_koppelingen();
  if ($list == "onttrInterval" OR
      $list == "stortInterval")
  {
    $listArray = array("",""=>"---","Monthly"=>"Monthly", "Quarterly"=>"Quarterly", "Yearly"=>"Yearly");
  }
  else
  {
    $kpl->getModuleRecords($list);
    foreach ($kpl->dataSet[$list] as $k=>$v)
    {
      $listArray[$k] = $v["externDescription"];
    }
  }

  $first = true;
  $out = "";
  foreach ($listArray as $key=>$item)
  {

    if ($first)   // update info overslaan
    {
      $first = false;
      if ($emptyFirst)
      {
        $out .= "\r\n\t<option value='' >---</option>";
      }


    }
    $selected = ($key == $value)?"SELECTED":"";
    $out .= "\r\n\t<option value='{$key}' $selected >{$item}</option>";
  }

  return $out;
}

function jsonDate($date)
{
  if ($date == "")
  {
    return "";
  }
  $d = explode("-",$date);
  $output = $d[2]."-".substr("0".$d[1],-2)."-".substr("0".$d[0],-2)."T00:00:00";
  return ($output == "0000-00-00T00:00:00")?"":$output;
}