<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 3 augustus 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/07/24 08:04:08 $
    File Versie         : $Revision: 1.12 $
 		
    $Log: CRM_nawHandelzeker.php,v $
    Revision 1.12  2020/07/24 08:04:08  cvs
    call 8314

    Revision 1.11  2020/05/08 09:53:27  cvs
    call 8314

    Revision 1.10  2020/04/29 12:06:42  cvs
    call 8314

    Revision 1.9  2020/04/22 06:36:41  cvs
    call 8314

    Revision 1.8  2020/04/17 07:15:26  cvs
    call 8314

    Revision 1.7  2020/04/15 08:25:14  cvs
    call 8314

    Revision 1.6  2020/04/06 13:41:57  cvs
    call 8314

    Revision 1.5  2020/04/06 10:11:18  cvs
    call 8314

    Revision 1.4  2020/03/02 09:43:00  cvs
    call 8314

    Revision 1.3  2020/02/28 15:33:03  cvs
    call 8314

    Revision 1.2  2020/02/24 11:11:24  cvs
    call 8314

    Revision 1.1  2020/01/27 13:01:29  cvs
    call 8314


*/
include_once("wwwvars.php");
session_start();

$subHeader     = "";
$mainHeader    = " overzicht";

$fmt = new AE_cls_formatter();

$db = new DB();
$query = "SELECT * FROM CRM_naw WHERE id = ".(int)$_REQUEST["rel_id"];
$nawRec = $db->lookupRecordByQuery($query);

$_SESSION['NAV'] = "";


$data = $_REQUEST;

$defaultProbability = 80;

$san = new AE_cls_handelzeker();
if ($data["action"] == "go")
{
  $__sanctieVars = array(
    "username" => "frank@airs.nl",
    "password" => "LuKiiJY6x"
  );
debug($_REQUEST);
//  $san = new AE_cls_handelzeker();
//
//  $san->customer_due_dilligence($data);
//
//  $san->callApiAndOutputPDF();
  exit;
}

$content = array();
$content['jsincludes'] .= '<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">';
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/jquery-min.js\"></script>";
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/jquery-ui-min.js\"></script>";
$content['jsincludes'] .= "<link rel=\"stylesheet\" href=\"style/fontAwesome/font-awesome.min.css\">";
$content['jsincludes'] .= "<link rel=\"stylesheet\" href=\"style/AIRS_default.css\">";
echo template($__appvar["templateContentHeader"],$content);

$vn = explode(" ", $nawRec["voornamen"]);
$vnp = explode(" ", $nawRec["part_voornamen"]);
?>
  <style>
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
    #risk_classificationInput{
      display: none;
    }
    .dossierMsg{
      background: darkgreen;
      color: white;
      padding: 20px;
      margin-bottom: 25px;
    }
    .dobMsg{
      display: none;
      background: maroon;
      color: white;
      padding: 20px;
      margin-bottom: 10px;
    }
  </style>


  <h1>Handelzeker screening</h1>

<!--  <form method="post" id="sanctieForm">-->
  <form method="post" id="sanctieForm" action="https://hzapi.airshost.nl/">
    <input type="hidden" name="action" value="go">
    <input type="hidden" name="code" value="<?=$code?>">
    <input type="hidden" name="apiKey" value="<?=$__appvar["HZ"]["apiKey"]?>">
    <br/>
    <br/>

    <div id="errorMsg"></div>

    <fieldset id="CheckName" class="fldset">

      <div>
        <button class="btnPselect btn btn-new btn-blue" id="p1">Persoon 1</button>
        <button class="btnPselect btn" id="p2">Persoon 2</button>
        <button class="btnPselect btn" id="p0">Leeg</button>
      </div>
      <hr/>
      <div class="vl">Portefeuille</div><div class="vr"><input name="reference" class="inpStr" style="width: 100px" value="<?=$nawRec["portefeuille"]?>"></div>
      <div class="vl">First name</div><div class="vr"><input name="firstname" class="inpStr" value=""></div>
      <div class="vl">Middle name</div><div class="vr"><input name="middlename" class="inpStr"></div>
      <div class="vl">Last name</div><div class="vr"><input name="surname" class="inpStr" value=""></div>
      <div class="vl">date of birth</div><div class="vr"><input name="dob" id="dob" class="inpStr AIRSdatepicker" style="width: 100px" value=""> (dd-mm-yyy)</div>
      <div class="dobMsg"></div>
      <div class="vl">Relevance</div><div class="vr"><select name="relevance">
          <?
          for ($p=100; $p > 74; $p--)
          {
            $selected = ($p == $defaultProbability)?"SELECTED":"";
            echo "<option value='$p' $selected>$p</option>\n";
          }
          ?>
        </select> (75-100)
      </div>
      <fieldset>
        <legend>dossier</legend>
        <?
          $dosNr = trim($nawRec["hz_dossierId"]);
          if ($dosNr != "")
          {
?>
            <h3> <input type="checkbox" name="useDossier" value="1" checked> Toevoegen aan Handelzeker dossier: <?=$dosNr?></h3>
            <input type="hidden" name="dossiernr" value="<?=$dosNr?>" />
<?
          }
          else
          {
?>
            <div class="vr"><input name="dossier" id="dossier" type="checkbox" value="1"> Create dossier</div>
            <div id="risk_classificationInput">
              <div class="vl" >Risk classification</div><div class="vr"><select name="risk_classification" id="risk_classification">
                  <?
                  $skipfirst = true;
                  foreach($san->risk_classification as $value=>$description)
                  {
                    if ($skipfirst)
                    {
                      $skipfirst = false;
                      continue;
                    }
                    //              $selected = ($value == 2)?"SELECTED":"";
                    echo "<option value='$value' $selected>$value - $description</option>";
                  }
?>
                </select>
              </div>
              <div class="dossierMsg">
                LET OP: <br/>
                In de PDF die gedownload wordt bevindt zich het dossiernummer.<br/>
                U dient deze aan de relatie kaart toe te voegen.
              </div>
            </div>
        <?
          }
        ?>

      </fieldset>

    </fieldset>





    <br/>
    <br/>
    <button id="btnSubmit">Controleer bij Handelzeker</button>
  </form>



<button><a href="<?=$_SESSION["facmodUrl"]?>&useSavedUrl=1" style="color: white">Terug naar relatie kaart</a></button>

<script>
  var p1 = {
    "voorletters": "<?=conditionData($vn[0])?>",
    "tusselvoegsel": "<?=conditionData($nawRec["tussenvoegsel"])?>",
    "achternaam": "<?=conditionData($nawRec["achternaam"])?>",
    "geboortedatum": "<?=($nawRec["geboortedatum"] == "0000-00-00")?"":$fmt->format("@D{form}",$nawRec["geboortedatum"])?>",
  };
  var p2 = {
    "voorletters": "<?=conditionData($vnp[0])?>",
    "tusselvoegsel": "<?=conditionData($nawRec["part_tussenvoegsel"])?>",
    "achternaam": "<?=conditionData($nawRec["part_achternaam"])?>",
    "geboortedatum": "<?=($nawRec["part_geboortedatum"] == "0000-00-00")?"":$fmt->format("@D{form}",$nawRec["part_geboortedatum"])?>",
  };
  var p0 = {
    "voorletters": "",
    "tusselvoegsel": "",
    "achternaam": "",
    "geboortedatum": "",
  };
  function fillForm(varName){
    var id = eval(varName);
    $("input[name='firstname']").val(id.voorletters);
    $("input[name='middlename']").val(id.tusselvoegsel);
    $("input[name='surname']").val(id.achternaam);
    $("input[name='dob']").val(id.geboortedatum);
  }
  $(document).ready(function(){
     console.log(p1);
     console.log(p2);
     console.log(p0);
     $("#dossier").change(function(){
        const chk = $(this).is(":checked");
        if (chk){
          $("#risk_classificationInput").show(300);
        }
        else {
          $("#risk_classificationInput").hide(300);
          $("#risk_classification").val(0);
        }

     });
    fillForm("p1");

    $("#btnSubmit").click(function(e){
      e.preventDefault();
      let err = "";
      const dobLen = $("#dob").val();

      if (dobLen.length != 0)
      {
        const d = dobLen.split("-");
        if (d[0] < 1 || d[0] > 31)
        {
           err += "<br/>- dag ongeldig";
        }
        if (d[1] < 1 || d[1] > 12)
        {
          err += "<br/>- maand ongeldig";
        }
        if (d[2] < 1900 || d[2] > 2050)
        {
          err += "<br/>- jaar ongeldig";
        }

        if (err != "")
        {
          $(".dobMsg").html("foute datum ingevoerd" + err);
          $(".dobMsg").show(300);
          return false;
        }
        else if (dobLen.length != 10)
        {
          $(".dobMsg").html("onjuist datum ingevuld formaat is DD-MM-JJJJ bv 01-01-1999");
          $(".dobMsg").show(300);
          return false;
        }
        else
        {
          $("#sanctieForm").submit();
        }
      }
      $("#sanctieForm").submit();
    });

    $(".btnPselect").click(function(e){
      e.preventDefault();
      var id = $(this).attr("id");
      console.log("id=" + id);
      fillForm(id);
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

function conditionData($in)
{

  return str_replace('"', "", $in);
}

?>