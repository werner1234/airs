<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/04/17 08:36:39 $
    File Versie         : $Revision: 1.2 $

    $Log: modulezUploadRapporteges.php,v $
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

$data = array_merge($_GET,$_POST);
$error = "";
$done = "";

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
  </style>
  <link rel="stylesheet" href="widget/css/font-awesome.min.css">
  <br/>
  <div class="pageContainer">
  <h2>Rapportage zip bestand uploaden voor ModuleZ</h2>
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