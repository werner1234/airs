<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/08/23 11:38:33 $
    File Versie         : $Revision: 1.3 $

    $Log: modulezUploadRapportages.php,v $
    Revision 1.3  2019/08/23 11:38:33  cvs
    call 8024

    Revision 1.2  2019/05/06 07:19:07  cvs
    call 7753

    Revision 1.1  2019/05/03 14:46:21  cvs
    call 7753

    Revision 1.2  2019/04/17 08:36:39  cvs
    call 7629

    Revision 1.1  2019/04/08 12:48:14  cvs
    call 7629

    Revision 1.1  2018/06/18 06:59:57  cvs
    update naar VRY omgeving

    Revision 1.1  2018/05/25 09:34:52  cvs
    25-5-2018


*/

include_once("wwwvars.php");
include_once ("moduleZ_functions.php");
include_once('../classes/pclzip.lib.php');
include_once('../api_moduleZ/apiMdlZ_helper.php');
include_once ("../classes/AE_cls_fileUpload.php");
$upl = new AE_cls_fileUpload();

$portDigits = array("0","1","2","3","4","5","6","7","8","9","-");
$data = array_merge($_GET,$_POST);
$error = "";
$done = "";
$db = new DB();

if ($data["action"] == "upload2")
{

  $ext = substr(strtolower($_FILES["uploadFile"]["name"]),-4);
  if ($ext != ".pdf")
  {
    $error = "Geen pdf bestand (".$_FILES["uploadFile"]["name"]."), bewerking afgebroken";
  }
  if (trim($data["subject"]) == "" OR trim($data["message"]) == "" )
  {
    $error .= "<br> onderwerp en bericht zijn verplicht";
  }
  $file = $_FILES["uploadFile"]["name"];
  $portefeuille = "";
  for ($x=0; $x < strlen($file); $x++)
  {

    if (in_array($file[$x], $portDigits))
    {
      $portefeuille .= $file[$x];
    }
    else
    {
      break;
    }
  }

  if (trim($portefeuille) == "" )
  {
    $error .= "<br> geen portefeuille in de bestandsnaam";
  }
  else
  {
    $query = "SELECT id FROM Portefeuilles WHERE Portefeuille = '".$portefeuille."'";
    if (!$rec = $db->lookupRecordByQuery($query))
    {
      $error .= "<br> portefeuille $portefeuille bestaat niet";
    }
  }
  if (!$upl->checkExtension($_FILES['uploadFile']['name']))
  {
    $error = "Fout: veboden bestandsformaat";
  }

  if ($error == "")
  {
//    debug($_FILES);
//    debug($data);
//    exit;



    $apiHelper = new apiMdlZ_helper();
    $apiHelper->newBatch();
    $batch = $apiHelper->batchnr;


    $dirTargetName = $__appvar["basedir"]."/api_moduleZ/content";
    $file = $dirTargetName."/".$_FILES["uploadFile"]["name"];

    move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $file);

    $apiHelper->addFile(basename($file), $portefeuille, null, $data["subject"], $data["message"]);
    $done = "$portefeuille -> $file toegevoegd";

  }

}

if ($data["action"] == "upload")
{
  $ext = substr(strtolower($_FILES["uploadFile"]["name"]),-4);
  if ($ext != ".zip")
  {
    $error = "Geen zip bestand (".$_FILES["uploadFile"]["name"]."), bewerking afgebroken";
  }
  if ($_FILES["uploadFile"]["size"] < 1)
  {
    $error = "Bestand bevat geen data";
  }
  if ($error == "")
  {
    $apiHelper = new apiMdlZ_helper();
    $apiHelper->newBatch();
    $batch = $apiHelper->batchnr;

    $dirName = $__appvar["basedir"]."/temp/".date("Ymd-Hi");
    $dirTargetName = $__appvar["basedir"]."/api_moduleZ/content";
    $zipFile = $dirName."/rapportage.zip";
    mkdir($dirName, 0777, true);
    move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $zipFile);
    $archive = new PclZip($zipFile);
    if ($archive->extract(PCLZIP_OPT_PATH, $dirName) == 0)
    {
      echo $error = "Unzip error : ".$archive->errorInfo(true);
    }
    else
    {
      unlink($zipFile);                                               // verwijdert .zip bestand
      $portDigits = array("0","1","2","3","4","5","6","7","8","9","-");
      if ($handle = opendir($dirName))
      {
        while ($file = readdir($handle))                              // verplaats .pdf files naar
        {
          if ($file != '.' && $file != '..')
          {
            if (substr(strtolower($file),-4) == ".pdf")
            {
              rename($dirName."/".$file, $dirTargetName."/".$file);
              $portefeuille = "";
              for ($x=0; $x < strlen($file); $x++)
              {

                 if (in_array($file[$x], $portDigits))
                 {
                   $portefeuille .= $file[$x];
                 }
                 else
                 {
                   break;
                 }
              }
              $apiHelper->addFile($file, $portefeuille);
              $response[] = "$portefeuille -> $file toegevoegd";
            }
          }
        }
        exec('rm -rf '.$dirName);
      }
      $done = "<li>";
      $done .= implode("<li>", $response);
      $done .= "<br/><br/>klaar met inlezen";
    }



  }

}

$mainHeader   = "";
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
    #msgDialog{
      width: 600px;
      padding: 15px;
      background: maroon;
      color: white;
      font-size: 1.2em;
    }
    #msgDone{
      width: 600px;
      padding: 15px;
      background: darkgreen;
      color: white;
      font-size: 1.2em;
    }
    .vDiv{
      margin-top:10px;
    }
    .lDiv{
      display: inline-block;
      width: 120px;
    }
  </style>
  <link rel="stylesheet" href="widget/css/font-awesome.min.css">
  <br/>
  <div class="pageContainer">
  <h2>Rapportage bestand uploaden voor ModuleZ</h2>
<?
  if ($error != "")
  {
?>
    <div id="msgDialog">
      <?=$error?>
    </div>
<?
  }
  if ($done != "")
  {
    ?>
    <div id="msgDone">
      <?=$done?>
    </div>
    <?
  }
?>
  <form enctype="multipart/form-data" method="post" >
    <input type="hidden" name="action" value="upload" />
  <fieldset>
    <legend>Selecteer bestand (.zip)</legend>
    <div id="loadProducts">
      <input type="file" name="uploadFile">
    </div>
    <br/>
    <br/>
    <input type="submit" value="start upload">
  </fieldset>
  </form>

  <form enctype="multipart/form-data" method="post" >
      <input type="hidden" name="action" value="upload2" />
      <fieldset>
        <legend>Selecteer pdf bestand </legend>
        <div id="loadProducts2">
          <input type="file" name="uploadFile">
        </div>
        <div class="vDiv"><div class="lDiv">Onderwerp</div><input name="subject" size="62"></div>
        <div class="vDiv"><div class="lDiv">Bericht</div><textarea name="message" cols="70" rows="6"></textarea></div>
        <br/>
        <br/>
        <input type="submit" value="start upload">
      </fieldset>
    </form>

  <br/>
  <br/>
  <br/>

</div>
  <script>

    $(document).ready(function () {


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