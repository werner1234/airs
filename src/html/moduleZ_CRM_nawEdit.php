<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/01/17 14:15:22 $
    File Versie         : $Revision: 1.13 $

    $Log: moduleZ_CRM_nawEdit.php,v $
    Revision 1.13  2019/01/17 14:15:22  cvs
    call 7463

    Revision 1.12  2019/01/09 12:08:39  cvs
    call 7463

    Revision 1.11  2018/10/24 10:09:29  cvs
    call 7175

    Revision 1.10  2018/10/19 07:04:18  cvs
    call 7175

    Revision 1.9  2018/10/12 10:47:37  cvs
    call 7175

    Revision 1.8  2018/10/05 06:06:43  cvs
    call 7175

    Revision 1.7  2018/09/23 17:14:23  cvs
    call 7175

    Revision 1.6  2018/09/14 13:49:39  cvs
    call 6709

    Revision 1.5  2018/09/14 09:38:13  cvs
    Naar VRY omgeving ter TEST

    Revision 1.4  2018/09/07 10:12:34  cvs
    commit voor robert call 6989

    Revision 1.3  2018/07/02 07:51:11  cvs
    call 6709

    Revision 1.2  2018/06/18 06:59:57  cvs
    update naar VRY omgeving

    Revision 1.1  2018/05/25 09:34:52  cvs
    25-5-2018


*/

include_once("wwwvars.php");
include_once ("moduleZ_functions.php");

$data = array_merge($_GET,$_POST);

if ($data["nationaliteit"] == "")
{
  $data["nationaliteit"] = "NL";
  $data["verzendLand"] = "NL";
}

if ($data["action"] == "go")
{

  $vl = trim(str_replace(".","",$data["voorletters"]));
  $voorletters = "";
  for ($x=0; $x < strlen($vl); $x++)
  {
    $voorletters .= strtoupper($vl[$x]).".";
  }
  $achternaam = trim($data["achternaam"]);
  $adres      = trim($data["verzendAdres"]);
  $plaats     = ucwords(strtolower(trim($data["verzendPlaats"])));

  $pc = str_replace(" ","",trim($data["verzendPc"]));
  $postcode = substr($pc,0,4)." ".strtoupper(substr($pc,4,2));

  $telefoons = "";
  if (trim($data["tel1"]) != "")
  {
    $telefoons[] = array("number" => trim($data["tel1"]) ,"type" => trim($data["tel1_oms"]));
  }
  if (trim($data["tel2"]) != "")
  {
    $telefoons[] = array("number" => trim($data["tel2"]),"type" => trim($data["tel2_oms"]));
  }


  $apiData = array(
    "initials" => $voorletters,
    "lastname_prefix" => trim($data["tussenvoegsel"]),
    "lastname" => "lastnamePlaceholder",
    "gender" => (strtoupper($data["geslacht"][0])=="M")?"Male":"Female",
    "date_of_birth" =>jsonDate($data["geboortedatum"]),
    "nationality" => trim($data["nationaliteit"]),
    "social_security_number" => trim($data["BSN"]),
    "identification" => array(
      "type" => trim($data["legitimatie"]),
      "identification_number" => trim($data["nummerID"]),
      "valid_until" => jsonDate($data["IdGeldigTot"])
    ),
    "address" => array(
      "street" => "streetPlaceholder",
      "number" => trim($data["VerzAdrHuisnr"]),
      "number_addition" => trim($data["VerzAdrToev"]),
      "zipcode" => $postcode,
      "city" => "cityPlaceholder",
      "country" => trim($data["verzendLand"]),
    ),
    "contact" => array(
      "email" => trim($data["email"]),
      "phone" => $telefoons
    )
  );

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


  unset($data["action"]);
  $qData = $data;
  $jsApiData =  json_encode($apiData);
  $jsApiData = str_replace("lastnamePlaceholder", utf8_encode($achternaam), $jsApiData);
  $jsApiData = str_replace("streetPlaceholder", utf8_encode($adres), $jsApiData);
  $jsApiData = str_replace("cityPlaceholder", utf8_encode($plaats), $jsApiData);
  $result =  mzApiPOST("klantAdd", $jsApiData);

  $result = (array) json_decode($result);
  $foutMelding = "";
  $okayMelding = "";
//debug($result);
  if ($mzError["httpCode"] != "")
  {
    $foutMelding = "lowlevel foutcode: ".$mzError["httpCode"];
  }
  if (trim($result["message"]) != "")
  {
    $foutMelding = $result["code"]." >> ".str_replace(",","<li>",$result["message"]);
    $okayMelding = "";
  }
  else
  {
    if ($result["status"] == "Created")
    {
      $db = new DB();
      $q = array();
      $query = "
      INSERT INTO CRM_naw SET
      `add_user` = '$USR',
      `add_date` = NOW(),
      `change_user` = '$USR',
      `change_date` = NOW(),
      `voorletters` = '".$voorletters."',
      `tussenvoegsel` = '".$data["tussenvoegsel"]."',
      `achternaam` = '".$data["achternaam"]."',
      `naam` = '".$voorletters." ".trim($data["tussenvoegsel"])." ".$data["achternaam"]."',
      `zoekveld` = '".$data["achternaam"]."',
      `BSN` = '".$data["BSN"]."',
      `externID` = '".$result["id"]."',
      `aktief` = 1,
      `debiteur` = 1
      ";

//      $query = "
//      INSERT INTO CRM_naw SET
//      `add_user` = '$USR',
//      `add_date` = NOW(),
//      `change_user` = '$USR',
//      `change_date` = NOW(),
//      `voorletters` = '".$voorletters."',
//      `tussenvoegsel` = '".$data["tussenvoegsel"]."',
//      `achternaam` = '".$data["achternaam"]."',
//      `naam` = '".$voorletters." ".trim($data["tussenvoegsel"])." ".$data["achternaam"]."',
//      `zoekveld` = '".$data["achternaam"]."',
//      `geboortedatum` = '".substr(jsonDate($data["geboortedatum"]),0,10)."',
//      `geslacht` = '".$data["geslacht"]."',
//      `nationaliteit` = '".$data["nationaliteit"]."',
//      `legitimatie` = '".$data["legitimatie"]."',
//      `IdGeldigTot` = '".substr(jsonDate($data["IdGeldigTot"]),0,10)."',
//      `nummerID` = '".$data["nummerID"]."',
//      `BSN` = '".$data["BSN"]."',
//      `verzendAdres` = '".trim($data["verzendAdres"]." ".trim($data["VerzAdrHuisnr"])." ".trim($data["VerzAdrToev"]))."',
//      `verzendPc` = '".$postcode."',
//      `verzendPlaats` = '".$plaats."',
//      `verzendLand` = '".$data["verzendLand"]."',
//      `tel1` = '".$data["tel1"]."',
//      `tel1_oms` = '".$data["tel1_oms"]."',
//      `tel2` = '".$data["tel2"]."',
//      `tel2_oms` = '".$data["tel2_oms"]."',
//      `email` = '".$data["email"]."',
//      `externID` = '".$result["id"]."',
//      `aktief` = 1
//      
//      ";

      $db->executeQuery($query);

      $lId = $db->last_id();
      if ($lId > 0)
      {
        addTrackAndTrace("CRM_naw", $lId, "voorletters", "", $voorletters, $USR) ;
        addTrackAndTrace("CRM_naw", $lId, "tussenvoegsel", "", $data["tussenvoegsel"], $USR) ;
        addTrackAndTrace("CRM_naw", $lId, "achternaam", "", $data["achternaam"], $USR) ;
        addTrackAndTrace("CRM_naw", $lId, "naam", "", $voorletters." ".trim($data["tussenvoegsel"])." ".$data["achternaam"], $USR) ;
        addTrackAndTrace("CRM_naw", $lId, "zoekveld", "", $data["achternaam"], $USR) ;
        addTrackAndTrace("CRM_naw", $lId, "BSN", "", $data["BSN"], $USR) ;
        addTrackAndTrace("CRM_naw", $lId, "externID", "", $result["id"], $USR) ;
        addTrackAndTrace("CRM_naw", $lId, "aktief", "", 1, $USR) ;
        addTrackAndTrace("CRM_naw", $lId, "debiteur", "", 1, $USR) ;
      }


      $foutMelding = "";
      $okayMelding = "Klant is toegevoegd met kenmerk {$result["id"]}";

    }
    if ($result["status"] == "Existing")
    {
      $foutMelding = "";
      $foutMelding = "Bestaat al met Extern ID: " . $result["id"];
    }
  }




}


$mainHeader   = "Client toevoegen in ModuleZ";
$content['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div>";
$_SESSION['NAV'] = "";
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
      padding: 260px 425px;
    }
    .pageContainer{
      width: 1050px;

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
    <fieldset>
      <legend>Haal client op via CRM</legend>
      <div class="formblock">
        <div class="formlinks">CRM naam: </div>
        <div class="formrechts">
          <input type="text" name="crmName" id="crmName" value="<?=$data["crmName"]?>"/>

        </div>
      </div>
    </fieldset>
    <fieldset >
      <legend> Persoonsinfo</legend>

      <div class="formblock">
        <div class="formlinks">Voorletters, Tussenvoegsel, Achternaam</div>
        <div class="formrechts">
          <input  class="" type="text"  value="<?=$data["voorletters"]?>" name="voorletters" id="voorletters" style="width: 80px">
          &nbsp;&nbsp;
          <input  class="" type="text"  value="<?=$data["tussenvoegsel"]?>" name="tussenvoegsel" id="tussenvoegsel" style="width: 80px">
          &nbsp;&nbsp;
          <input  class="emptyCheck" data-veldnaam="Achternaam" type="text"  value="<?=$data["achternaam"]?>" name="achternaam" id="achternaam" style="width: 400px">
        </div>
      </div>

      <div class="formblock">
        <div class="formlinks">Geboortedatum</div>
        <div class="formrechts">
          <input name="geboortedatum" value="<?=$data["geboortedatum"]?>" type="text" placeholder="   klik -->" readonly
                 title="selecteer de datum via de calender" class='AIRSdatepicker datecheck' id="geboortedatum"
                 data-veldnaam="geboortedatum" />

        </div>
      </div>
      <div class="formblock">
        <div class="formlinks">Geslacht</div>
        <div class="formrechts">
          <select  class="" type="select"  name="geslacht" id="geslacht" >
            <option value="M" <?=$data["geslacht"]!="V"?"SELECTED":""?>>Man</option>
            <option value="V" <?=$data["geslacht"]=="V"?"SELECTED":""?>>Vrouw</option>
          </select>
        </div>
      </div>

      <div class="formblock">
        <div class="formlinks">Nationaliteit</div>
        <div class="formrechts">
          <select name="nationaliteit" id="nationaliteit">
            <?=ISOLand(trim($data["nationaliteit"]))?>
          </select>
        </div>
      </div>

      <div class="formblock">
        <div class="formlinks">BSN nummer</div>
        <div class="formrechts">
          <input class="emptyCheck" data-veldnaam="BSN nummer" type="text"  size="9" maxlength="9" value="<?=$data["BSN"]?>" name="BSN" id="BSN" style="width: 80px"> <span id="bsnTxt"></span>
        </div>
      </div>

      <div class="formblock">
        <div class="formlinks">Soort</div>
        <div class="formrechts">
          <select  class="" type="select"  name="legitimatie" id="legitimatie" >
            <?=legitimatie($data["legitimatie"])?>
          </select>  &nbsp;&nbsp;&nbsp;Nummer: <input  class="emptyCheck" data-veldnaam="Legitimatie nummer" type="text"  size="15" value="<?=$data["nummerID"]?>" name="nummerID" id="nummerID" >
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks">Geldig tot</div>
        <div class="formrechts">
          <input name="IdGeldigTot" value="<?=$data["IdGeldigTot"]?>" type="text" placeholder="   klik -->" readonly
                 title="selecteer de datum via de calender" class='AIRSdatepicker datecheck' id="IdGeldigTot"
                 data-veldnaam="geldig tot" />
        </div>
      </div>

    </fieldset>

    <fieldset >
      <legend> Verzendadres</legend>

      <div class="formblock">
        <div class="formlinks"><label for="verzendAdres">Adres, nr, toev.</label></div>
        <div class="formrechts">
          <input type="text" name="verzendAdres" id="verzendAdres" value="<?=$data["verzendAdres"]?>" width="50" class="emptyCheck" data-veldnaam="Adres"/>&nbsp;&nbsp;
          <input type="text" name="VerzAdrHuisnr" id="VerzAdrHuisnr" value="<?=$data["VerzAdrHuisnr"]?>" style="width: 60px" class="emptyCheck" data-veldnaam="huisnummer"/>&nbsp;&nbsp;
          <input type="text" name="VerzAdrToev" id="VerzAdrToev" value="<?=$data["VerzAdrToev"]?>" style="width: 60px"/>
        </div>
      </div>

      <div class="formblock">
        <div class="formlinks"><label for="verzendPc">Postcode & Plaats</label></div>
        <div class="formrechts">
          <input type="text" name="verzendPc" id="verzendPc" value="<?=$data["verzendPc"]?>" style="width: 60px" class="emptyCheck" data-veldnaam="Postcode"/>&nbsp;&nbsp;
          <input type="text" name="verzendPlaats" id="verzendPlaats" value="<?=$data["verzendPlaats"]?>" width="50" class="emptyCheck" data-veldnaam="Plaats"/>
        </div>
      </div>

      <div class="formblock">
        <div class="formlinks"><label for="verzendPlaats">Land</label></div>
        <div class="formrechts">
          <select name="verzendLand" id="verzendLand">
            <?=ISOLand($data["verzendLand"])?>
          </select>
        </div>
      </div>
    </fieldset>


    <fieldset>
      <legend> Telefoon</legend>
      <div class="formblock">
        <div class="formlinks">Eerste telefoon</div>
        <div class="formrechts">

          <input type="text" name="tel1" id="tel1" value="<?=$data["tel1"]?>" style="width: 120px" class="emptyCheck" data-veldnaam="Eerste telefoon"/>&nbsp;&nbsp;
          <select name="tel1_oms" id="tel1_oms">
            <?=telsoort($data["tel1_oms"])?>
          </select>

        </div>
      </div>

      <div class="formblock">
        <div class="formlinks">Tweede telefoon</div>
        <div class="formrechts">
          <input type="text" name="tel2" value="<?=$data["tel2"]?>" style="width: 120px"/>&nbsp;&nbsp;
          <select name="tel2_oms">
            <?=telsoort($data["tel2_oms"])?>
          </select>
        </div>
      </div>

      <div class="formblock">
        <div class="formlinks">E-mail</div>
        <div class="formrechts">
          <input type="email" name="email" id="email" value="<?=$data["email"]?>" style="width: 400px" class="emptyCheck" data-veldnaam="E-mail"/>
        </div>
      </div>


    </fieldset>
    <br/>
    <br/>
    <button id="btnSubmit">naar ModuleZ zenden</button>

  </form>
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

    if ($okayMelding != "")
    {
      ?>
      $(".okayBody").html("<?=$okayMelding?>");
      $("#okayMelding").show(300);
      <?
    }
?>
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
        yearRange: '1920:2050',
        closeText: "sluiten",
        showAnim: "slideDown",
        showButtonPanel: true,
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 2,
        showWeek: true,
        firstDay: 1
      });

      $("#BSN").keyup(function(){
        if (checkBSN($(this).val()))
        {
          $(this).css("color","green");
          $("#bsnTxt").html("BSN voldoet");
        }
        else
        {
          $(this).css("color","red");
          $("#bsnTxt").html("<b>GEEN BSN nummer</b>");
        }

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

      $("#crmName").autocomplete(
        {
          source: "moduleZ_getClient.php?mode=2",                      // link naar lookup script
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
            console.log(ui);
            $("#voorletters").val(ui.item.voorletters);
            $("#tussenvoegsel").val(ui.item.tussenvoegsel);
            $("#achternaam").val(ui.item.achternaam);
            $("#geboortedatum").val(ui.item.geboortedatum);
            $("#geslacht").val(ui.item.geslacht);
            $("#nationaliteit").val(ui.item.nationaliteit);
            $("#BSN").val(ui.item.BSN);
            $("#legitimatie").val(ui.item.legitimatie);
            $("#IdGeldigTot").val(ui.item.IdGeldigTot);
            $("#nummerID").val(ui.item.nummerID);
            $("#verzendAdres").val(ui.item.adres);
            $("#VerzAdrHuisnr").val(ui.item.hnr);
            $("#VerzAdrToev").val("");
            $("#verzendPc").val(ui.item.pc);
            $("#verzendPlaats").val(ui.item.plaats);
            $("#verzendLand").val(ui.item.land);
            $("#tel1").val(ui.item.tel1);
            $("#tel1_oms").val(ui.item.tel1_oms);
            $("#tel2").val(ui.item.tel2);
            $("#tel2_oms").val("Mobile");
            $("#email").val(ui.item.email);
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


    });

    function checkBSN(bsn){

      // let numbers = ('000000000' + numbersRaw).substr(-9);

      bsn = '00000000'+bsn;
      numbers = bsn.toString().split("");
      numbers = numbers.slice(-9);

      check = (parseInt(numbers[0],10)*9) +
        (parseInt(numbers[1],10)*8) +
        (parseInt(numbers[2],10)*7) +
        (parseInt(numbers[3],10)*6) +
        (parseInt(numbers[4],10)*5) +
        (parseInt(numbers[5],10)*4) +
        (parseInt(numbers[6],10)*3) +
        (parseInt(numbers[7],10)*2) +
        (parseInt(numbers[8],10)*-1);

      return (check % 11 === 0);
    }

    function isValidDate(s) {
      var bits = s.split('-');
      var d = new Date(bits[2] + '/' + bits[1] + '/' + bits[0]);
      return !!(d && (d.getMonth() + 1) == bits[1] && d.getDate() == Number(bits[0]));
    }

  </script>



<?
echo template($__appvar["templateRefreshFooter"],$content);

function ISOLand( $l = 'NL')
{

  $landArray = array(
    "AF" => "_Afghanistan",
    "AX" => "Åland",
    "AL" => "Albanië",
    "DZ" => "Algerije",
    "VI" => "Amerikaanse Maagdeneilanden",
    "AS" => "Amerikaans-Samoa",
    "AD" => "Andorra",
    "AO" => "Angola",
    "AI" => "Anguilla",
    "AQ" => "Antarctica",
    "AG" => "Antigua en Barbuda",
    "AR" => "Argentinië",
    "AM" => "Armenië",
    "AW" => "Aruba",
    "AU" => "Australië",
    "AZ" => "Azerbeidzjan",
    "BS" => "Bahama's",
    "BH" => "Bahrein",
    "BD" => "Bangladesh",
    "BB" => "Barbados",
    "BE" => "België",
    "BZ" => "Belize",
    "BJ" => "Benin",
    "BM" => "Bermuda",
    "BT" => "Bhutan",
    "BO" => "Bolivia",
    "BQ" => "Caribisch Nederland",
    "BA" => "Bosnië en Herzegovina",
    "BW" => "Botswana",
    "BV" => "Bouveteiland",
    "BR" => "Brazilië",
    "VG" => "Britse Maagdeneilanden",
    "IO" => "Brits Indische Oceaanterritorium",
    "BN" => "Brunei",
    "BG" => "Bulgarije",
    "BF" => "Burkina Faso",
    "BI" => "Burundi",
    "KH" => "Cambodja",
    "CA" => "Canada",
    "CF" => "Centraal-Afrikaanse Republiek",
    "CL" => "Chili",
    "CN" => "China",
    "CX" => "Christmaseiland",
    "CC" => "Cocoseilanden",
    "CO" => "Colombia",
    "KM" => "Comoren",
    "CG" => "Congo-Brazzaville",
    "CD" => "Congo-Kinshasa",
    "CK" => "Cookeilanden",
    "CR" => "Costa Rica",
    "CU" => "Cuba",
    "CW" => "Curaçao",
    "CY" => "Cyprus",
    "DK" => "Denemarken",
    "DJ" => "Djibouti",
    "DM" => "Dominica",
    "DO" => "Dominicaanse Republiek",
    "DE" => "Duitsland",
    "EC" => "Ecuador",
    "EG" => "Egypte",
    "SV" => "El Salvador",
    "GQ" => "Equatoriaal-Guinea",
    "ER" => "Eritrea",
    "EE" => "Estland",
    "ET" => "Ethiopië",
    "FO" => "Faeröer",
    "FK" => "Falklandeilanden",
    "FJ" => "Fiji",
    "PH" => "Filipijnen",
    "FI" => "Finland",
    "FR" => "Frankrijk",
    "TF" => "Franse Zuidelijke en Antarctische Gebieden",
    "GF" => "Frans-Guyana",
    "PF" => "Frans-Polynesië",
    "GA" => "Gabon",
    "GM" => "Gambia",
    "GE" => "Georgië",
    "GH" => "Ghana",
    "GI" => "Gibraltar",
    "GD" => "Grenada",
    "GR" => "Griekenland",
    "GL" => "Groenland",
    "GP" => "Guadeloupe",
    "GU" => "Guam",
    "GT" => "Guatemala",
    "GG" => "Guernsey",
    "GN" => "Guinee",
    "GW" => "Guinee-Bissau",
    "GY" => "Guyana",
    "HT" => "Haïti",
    "HM" => "Heard en McDonaldeilanden",
    "HN" => "Honduras",
    "HU" => "Hongarije",
    "HK" => "Hongkong",
    "IE" => "Ierland",
    "IS" => "IJsland",
    "IN" => "India",
    "ID" => "Indonesië",
    "IQ" => "Irak",
    "IR" => "Iran",
    "IL" => "Israël",
    "IT" => "Italië",
    "CI" => "Ivoorkust",
    "JM" => "Jamaica",
    "JP" => "Japan",
    "YE" => "Jemen",
    "JE" => "Jersey",
    "JO" => "Jordanië",
    "KY" => "Kaaimaneilanden",
    "CV" => "Kaapverdië",
    "CM" => "Kameroen",
    "KZ" => "Kazachstan",
    "KE" => "Kenia",
    "KG" => "Kirgizië",
    "KI" => "Kiribati",
    "UM" => "Kleine afgelegen eilanden van de Verenigde Staten",
    "KW" => "Koeweit",
    "HR" => "Kroatië",
    "LA" => "Laos",
    "LS" => "Lesotho",
    "LV" => "Letland",
    "LB" => "Libanon",
    "LR" => "Liberia",
    "LY" => "Libië",
    "LI" => "Liechtenstein",
    "LT" => "Litouwen",
    "LU" => "Luxemburg",
    "MO" => "Macau",
    "MK" => "Macedonië",
    "MG" => "Madagaskar",
    "MW" => "Malawi",
    "MV" => "Maldiven",
    "MY" => "Maleisië",
    "ML" => "Mali",
    "MT" => "Malta",
    "IM" => "Man",
    "MA" => "Marokko",
    "MH" => "Marshalleilanden",
    "MQ" => "Martinique",
    "MR" => "Mauritanië",
    "MU" => "Mauritius",
    "YT" => "Mayotte",
    "MX" => "Mexico",
    "FM" => "Micronesia",
    "MD" => "Moldavië",
    "MC" => "Monaco",
    "MN" => "Mongolië",
    "ME" => "Montenegro",
    "MS" => "Montserrat",
    "MZ" => "Mozambique",
    "MM" => "Myanmar",
    "NA" => "Namibië",
    "NR" => "Nauru",
    "NL" => "Nederland",
    "NP" => "Nepal",
    "NI" => "Nicaragua",
    "NC" => "Nieuw-Caledonië",
    "NZ" => "Nieuw-Zeeland",
    "NE" => "Niger",
    "NG" => "Nigeria",
    "NU" => "Niue",
    "MP" => "Noordelijke Marianen",
    "KP" => "Noord-Korea",
    "NO" => "Noorwegen",
    "NF" => "Norfolk",
    "UG" => "Oeganda",
    "UA" => "Oekraïne",
    "UZ" => "Oezbekistan",
    "OM" => "Oman",
    "AT" => "Oostenrijk",
    "TL" => "Oost-Timor",
    "PK" => "Pakistan",
    "PW" => "Palau",
    "PS" => "Palestina",
    "PA" => "Panama",
    "PG" => "Papoea-Nieuw-Guinea",
    "PY" => "Paraguay",
    "PE" => "Peru",
    "PN" => "Pitcairneilanden",
    "PL" => "Polen",
    "PT" => "Portugal",
    "PR" => "Puerto Rico",
    "QA" => "Qatar",
    "RE" => "Réunion",
    "RO" => "Roemenië",
    "RU" => "Rusland",
    "RW" => "Rwanda",
    "BL" => "Saint-Barthélemy",
    "KN" => "Saint Kitts en Nevis",
    "LC" => "Saint Lucia",
    "PM" => "Saint-Pierre en Miquelon",
    "VC" => "Saint Vincent en de Grenadines",
    "SB" => "Salomonseilanden",
    "WS" => "Samoa",
    "SM" => "San Marino",
    "SA" => "Saoedi-Arabië",
    "ST" => "Sao Tomé en Principe",
    "SN" => "Senegal",
    "RS" => "Servië",
    "SC" => "Seychellen",
    "SL" => "Sierra Leone",
    "SG" => "Singapore",
    "SH" => "Sint-Helena, Ascension en Tristan da Cunha",
    "MF" => "Sint-Maarten",
    "SX" => "Sint Maarten",
    "SI" => "Slovenië",
    "SK" => "Slowakije",
    "SD" => "Soedan",
    "SO" => "Somalië",
    "ES" => "Spanje",
    "SJ" => "Spitsbergen en Jan Mayen",
    "LK" => "Sri Lanka",
    "SR" => "Suriname",
    "SZ" => "Swaziland",
    "SY" => "Syrië",
    "TJ" => "Tadzjikistan",
    "TW" => "Taiwan",
    "TZ" => "Tanzania",
    "TH" => "Thailand",
    "TG" => "Togo",
    "TK" => "Tokelau",
    "TO" => "Tonga",
    "TT" => "Trinidad en Tobago",
    "TD" => "Tsjaad",
    "CZ" => "Tsjechië",
    "TN" => "Tunesië",
    "TR" => "Turkije",
    "TM" => "Turkmenistan",
    "TC" => "Turks- en Caicoseilanden",
    "TV" => "Tuvalu",
    "UY" => "Uruguay",
    "VU" => "Vanuatu",
    "VA" => "Vaticaanstad",
    "VE" => "Venezuela",
    "AE" => "Verenigde Arabische Emiraten",
    "US" => "Verenigde Staten",
    "GB" => "Verenigd Koninkrijk",
    "VN" => "Vietnam",
    "WF" => "Wallis en Futuna",
    "EH" => "Westelijke Sahara",
    "BY" => "Wit-Rusland",
    "ZM" => "Zambia",
    "ZW" => "Zimbabwe",
    "ZA" => "Zuid-Afrika",
    "GS" => "Zuid-Georgia en de Zuidelijke Sandwicheilanden",
    "KR" => "Zuid-Korea",
    "SS" => "Zuid-Soedan",
    "SE" => "Zweden",
    "CH" => "Zwitserland"

  );

  $out = "<!-- $l -->";
  foreach ($landArray as $k=>$v)
  {
    $selected = ($k == $l)?"SELECTED":"";
    $out .= "\r\n\t <option value=\"$k\" $selected>$v</option>";
  }

  return $out;
}

function legitimatie($code="Passport")
{
  $landArray = array(
    "Passport"       => "paspoort",
    "IdCard"         => "ID kaart",
    "DriversLicense" => "rijbewijs",

  );

  $out = "";
  foreach ($landArray as $k=>$v)
  {
    $selected = ($k == $code)?"SELECTED":"";
    $out .= "\r\n\t <option value='$k' $selected>$v</option>";
  }

  return $out;
}

function telsoort($code="Home")
{
  $landArray = array(
    "Home"   => "thuis",
    "Work"   => "werk",
    "Mobile" => "mobiel",
  );

  $out = "";
  foreach ($landArray as $k=>$v)
  {
    $selected = ($k == $code)?"SELECTED":"";
    $out .= "\r\n\t <option value='$k' $selected>$v</option>";
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
  return $d[2]."-".substr("0".$d[1],-2)."-".substr("0".$d[0],-2)."T00:00:00";
}