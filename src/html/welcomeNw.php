<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/09/18 09:17:30 $
    File Versie         : $Revision: 1.15 $

*/

// set module naam voor autenticatie leeg = iedereen.
$__appvar["module"] = "";
// SET in wwwvars ie:  $__appvar["userLevel"] = _READ;
// include wwwvars
include_once("wwwvars.php");

$db = new DB();

$cfg = new AE_config();
$layName = $USR."_WidgetTabbladen";
$tabData = unserialize($cfg->getData($layName));

if ($tabData == false)
{
  $tabName1 = "tab 1";
  $tabName2 = "tab 2";
  $tabName3 = "tab 3";
  $tabOn1   = true;
  $tabOn2   = false;
  $tabOn3   = false;
}
else
{
  $tabName1 = ($tabData["tab1"] != "")?$tabData["tab1"]:"tab 1";
  $tabName2 = ($tabData["tab2"] != "")?$tabData["tab2"]:"tab 2";
  $tabName3 = ($tabData["tab3"] != "")?$tabData["tab3"]:"tab 3";
  $tabOn1   = ($tabData["tabVink1"] != "0");
  $tabOn2   = ($tabData["tabVink2"] != "0");
  $tabOn3   = ($tabData["tabVink3"] != "0");
}

if($__appvar["bedrijf"])
{
  $q = "
  SELECT 
    Vermogensbeheerders.Logo
  FROM 
    VermogensbeheerdersPerBedrijf, 
    Vermogensbeheerders 
  WHERE 
    Bedrijf = '".$__appvar["bedrijf"]."' AND 
    VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder";

  $logodata = $db->lookupRecordByQuery($q);

}

if ($__develop)
{
  $logodata = array("Logo" => "logo_attica.png");
}

if ($__appvar["bedrijf"] == "TST")
{
  $logodata = array("Logo" => "logo_ORC.png");
}

session_start();
$_SESSION['submenu'] = New Submenu();

$_SESSION['NAV'] = "";
session_write_close();

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
?>
<style>
  .ui-tabs{
    width: 100% !important;
  }
  #widgetContwiner iframe {
    width: 99%;
    height: 100%;
    border: none; }

  #widgetContwiner {
    width: 100%;
    height: 3530px;
    padding: 0;
    overflow: hidden; }
</style>
<?

if($_SESSION['usersession']['gebruiker']['updateInfoAan'])
{

  $cfg  = new AE_config();
  $tmpl = new AE_template();
  $tmpl->appendSubdirToTemplatePath("welcome");
  $tmpl->loadTemplateFromFile("softwarePopup_head.html","kop");
  $tmpl->loadTemplateFromFile("softwarePopup_foot.html","voet");
  $tmpl->loadTemplateFromFile("softwarePopup_row.html" ,"row");

  $welcomeId   = 'lastUpdateInfo_'.$USR;
  $lastWelcome = $cfg->getData($welcomeId);
//  $lastWelcome='2001-01-01';
  if($lastWelcome=='')
  {
    $lastWelcome=date('Y-m-01');
  }

  $db = new DB();
  if($lastWelcome <> '')
  {
    $query = "
    SELECT 
      id,
      versie,
      informatie,
      add_date 
    FROM 
      updateInformatie 
    WHERE 
      publiceer = 1 AND 
      add_date > '$lastWelcome' 
    ORDER BY 
      versie";

    $db->executeQuery($query);
    if($db->records() > 0)
    {
      $versieInfo = $tmpl->parseBlock("kop",array());
      while($data=$db->nextRecord())
      {
        $data['add_date']   = date('d-m-Y',db2jul($data['add_date']));
        $data["informatie"] = nl2br($data["informatie"]);
        $versieInfo .= $tmpl->parseBlock("row", $data);
      }
      $versieInfo .= $tmpl->parseBlock("voet",array());
    }
    echo $versieInfo;
  }

  $query = "SELECT add_date FROM updateInformatie WHERE publiceer=1 ORDER BY add_date desc";
  $laatsteRecord = $db->lookupRecordByQuery($query);
  $cfg->addItem($welcomeId,$laatsteRecord['add_date']);
}

if ($__develop)
{
//  debug($_SESSION["usersession"]["gebruiker"]);
}


$img = "";
if ( is_file($__appvar["basedir"]."/html/rapport/logo/".$logodata['Logo'] ) )
{
  $tmp = getimagesize($__appvar["basedir"]."/html/rapport/logo/".$logodata['Logo']);
  $maxWidth  = 240;
  $maxHeight = 80;
  $width     =$tmp[0];
  $height    =$tmp[1];
  $factor    = 1;
  if ($width > $maxWidth)
  {
    $factor=$maxWidth/$width;
  }

  if ($height > $maxHeight && ($maxHeight/$height < $factor))
  {
    $factor=$maxHeight/$height;
  }
  $width=$width*$factor;
  $height=$height*$factor;
  $img = '<img width="'.$width.'" height="'.$height.'" src="rapport/logo/'.$logodata['Logo'].'"/>';
?>
<!--  <div align="left>">-->
<!--  <img width="--><?//=$width?><!--" height="--><?//=$height?><!--" src="rapport/logo/--><?//=$logodata['Logo']?><!--">-->
<!--  </div>-->
<?
}

if ($_GET["msg"] <> "")
{
  include_once "welcomeMessages.php";
}


?>

<style>
  .actuVink{
    border: 0;
    padding: 10px;
  }
</style>

    <div class="container" >
      <div class="row">
        <div class="col-lg-12 text-center">
          <span class="pull-left">
            <button  id="btnSetup" title="<?= vt('Instellen tabbladen'); ?>"><i class="fa fa-cog actuVink" ></i></button>

          </span>
          <h2><?=$img?> <?= vt('Uw persoonlijke startpagina'); ?></h2>
          <div id="widgetContwiner">


<?
if ($tabOn1 AND !$tabOn2 AND !$tabOn3)
{
?>
  <iframe seamless="seamless" src="welcomeNw_tabblad.php" id="iFrameTabblad" scrolling="no" style="

      height: 1200px;
      width: 100%;
      margin: auto ;
      border:none;">

  </iframe>
  <br/>
  <br/>
  <br/>
  <br/>
  <br/>
  <br/>
<?
}
else
{
?>
          <div id="tabs">
            <ul>
              <?
              if ($tabOn1)
              {
                echo '<li><a href="#tab1" id="btnTab1">'.$tabName1.'</a></li>';
              }
              if ($tabOn2)
              {
                echo '<li><a href="#tab2" id="btnTab2">'.$tabName2.'</a></li>';
              }
              if ($tabOn3)
              {
                echo '<li><a href="#tab3" id="btnTab3">'.$tabName3.'</a></li>';
              }

              ?>

            </ul>
            <?
            if ($tabOn1)
            {
              ?>
              <div id="tab1">
                <iframe src="welcomeNw_tabblad.php" id="iFrameTabblad" style="width: 100%;  height:calc(90% - 135px); border:0"></iframe>
              </div>

              <?
            }
            if ($tabOn2)
            {
              ?>
              <div id="tab2">
                <iframe src="" id="iFrameTabblad2" style="width: 100%;  height:calc(90% - 135px); border:0"></iframe>
              </div>

              <?
            }
            if ($tabOn3)
            {
              ?>
              <div id="tab3">
                <iframe src="" id="iFrameTabblad3" style="width: 100%;  height:calc(90% - 135px); border:0"></iframe>
              </div>

              <?
            }

}
?>
          </div>
        </div>
      </div>

    </div>

<script>
  $(document).ready(function () {

    $("#btnSetup").click(function(e)
    {
      e.preventDefault();
      window.location ="welcomeNw_tabbladSetup.php";
    });

    $( "#tabs" ).tabs();
    $("#btnTab1").click(function (e)
    {
      console.log("laden tab1");
      e.preventDefault();
      var param = "";
      if ($(this).data("tab") != "" && $(this).data("tab") != undefined)
      {
        param = "?tab="+$(this).data("tab")||"";
      }

      $("#iFrameTabblad").attr("src","welcomeNw_tabblad.php"+param);
    });

    $("#btnTab2").click(function (e)
    {
      console.log("laden tab2");
      e.preventDefault();
      $("#iFrameTabblad2").attr("src","welcomeNw_tabblad.php?tab=tab2");
    });

    $("#btnTab3").click(function (e)
    {
      console.log("laden tab3");
      e.preventDefault();
      $("#iFrameTabblad3").attr("src","welcomeNw_tabblad.php?tab=tab3");
    });

  });
</script>

<?
//clear navigatie
$_SESSION['NAV'] = "";
session_write_close();

echo template($__appvar["templateRefreshFooter"],$content);
?>