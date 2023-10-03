<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/02/01 12:49:14 $
    File Versie         : $Revision: 1.1 $

    $Log: welcomeNw_tabbladSetup.php,v $
    Revision 1.1  2018/02/01 12:49:14  cvs
    update naar airsV2

    Revision 1.9  2017/10/27 08:58:01  cvs
    call 6300

    Revision 1.8  2017/06/02 08:59:40  cvs
    no message

    Revision 1.7  2017/05/29 09:35:38  cvs
    no message

    Revision 1.6  2017/03/15 12:34:36  cvs
    geen widget vulling onder IE11



*/

// set module naam voor autenticatie leeg = iedereen.
$__appvar["module"] = "";
// SET in wwwvars ie:  $__appvar["userLevel"] = _READ;
// include wwwvars
include_once("wwwvars.php");






$cfg = new AE_config();

$layName = $USR."_WidgetTabbladen";

if ($_POST["action"] == "update")
{
//  debug($_POST);
  $tabData = array(
    "tab1" => $_POST["tabblad-1"],
    "tab2" => $_POST["tabblad-2"],
    "tab3" => $_POST["tabblad-3"],
    "tabVink1" => ($_POST["tabbladVink-1"] == 1)?"1":"0",
    "tabVink2" => ($_POST["tabbladVink-2"] == 1)?"1":"0",
    "tabVink3" => ($_POST["tabbladVink-3"] == 1)?"1":"0",
    );

  $cfg->addItem($layName, serialize($tabData));
  ?>
  <script>
    window.open("welcomeNw.php", "content");
  </script>
  <?
  exit;
}
$tabData = unserialize($cfg->getData($layName));

$checked1 = ($tabData["tabVink1"] != "0")?"checked":"";
$checked2 = ($tabData["tabVink2"] != "0")?"checked":"";
$checked3 = ($tabData["tabVink3"] != "0")?"checked":"";
//debug($tabData,$layName);

//$wdg->showSettings();

session_start();

$content = array(
  "jsincludes" => '
   
    <link rel="stylesheet" href="widget/css/gridstack.css">
    <link rel="stylesheet" href="widget/css/font-awesome.min.css">
    <link rel="stylesheet" href="widget/css/Bootstrap_3_2_0.css"> 
    <link rel="stylesheet" href="widget/css/jquery-ui-1.11.1.custom.css"  type="text/css" media="screen">
    

    <script src="widget/js/jquery.min.js"></script>
    <script src="widget/js/bootstrap.min.js"></script>
    
    <script src="widget/js/jquery-ui.js"></script>
    
    <script src="widget/js/lodash.min.js"></script>
    <script src="widget/js/knockout-min.js"></script>
    <script src="widget/js/gridstack.js"></script>
    <script src="widget/js/gridstack.jQueryUI.js"></script>
    <script type="text/javascript" src="javascript/algemeen.js"></script>
    <script src="widget/js/bootstrapTooltip.js"></script>
   '
  );


$template_content["style"] .= "\n    <link href='style/welcome.css' rel='stylesheet' type='text/css' media='screen'>";
echo template($__appvar["templateContentHeader"],$content);


if ($__develop)
{
//  debug($_SESSION["usersession"]["gebruiker"]);
}

?>
  <style type="text/css">
    input{

    }
    td{
      margin-top: 5px;
      padding: 5px;
    }
    .kop{
      background: rgba(20,60,90,1);
      color: #FFF;
      font-size: 14px;
      padding:10px;

    }
    .inpTab{
      font-size: 14px;
      padding: 5px;
      width: 150px;
    }
    .ac{
      text-align: center;
    }
    h2{
      font-size: 18px;
    }
  </style>
<br/>
<h2><?= vt('Configureren tabbladen startpagina'); ?></h2>
<br/>
<form method="post" id="setupForm">
  <input name="action" value="update" type="hidden"/>
  <section>
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center">
        <table >
          <tr>
            <td class="kop">&nbsp;</td>
            <td class="kop"><?= vt('Aan'); ?>/Uit</td>
            <td class="kop"><?= vt('Namen tabbladen (maximaal 15 tekens)'); ?></td>
          </tr>
          <tr>
            <td>1.</td>
            <td class="ac"><input type="checkbox" value="1" class="inpVink" name="tabbladVink-1" <?=$checked1?>></td>
            <td><input maxlength="15" class="inpTab" placeholder="<?= vt('tabblad koptekst'); ?>" name="tabblad-1" value="<?=$tabData["tab1"]?>" > </td>
          </tr>
          <tr>
            <td>2.</td>
            <td class="ac"><input type="checkbox" value="1" class="inpVink" name="tabbladVink-2" <?=$checked2?>></td>
            <td><input maxlength="15" class="inpTab" placeholder="<?= vt('tabblad koptekst'); ?>" name="tabblad-2" value="<?=$tabData["tab2"]?>" ></td>
          </tr>
          <tr>
            <td>3.</td>
            <td class="ac"><input type="checkbox" value="1" class="inpVink" name="tabbladVink-3" <?=$checked3?>></td>
            <td><input maxlength="15" class="inpTab" placeholder="<?= vt('tabblad koptekst'); ?>" name="tabblad-3" value="<?=$tabData["tab3"]?>" ></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>
              <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover" role="button" id="btnOpslaan"><span class="ui-button-text"><?= vt('Opslaan'); ?></span></button>
              </td>
          </tr>
        </table>

        </div>
      </div>
      <div class="grid-stack" data-gs-width="12">

      </div>
    </div>
  </section>

</form>

<script>
  $(document).ready(function () {
    $("#btnOpslaan").click(function(e){
      e.preventDefault();
      $("#setupForm").submit();
    });
  });
</script>

<?

//clear navigatie
$_SESSION['NAV'] = "";
session_write_close();

echo template($__appvar["templateRefreshFooter"],$content);
?>