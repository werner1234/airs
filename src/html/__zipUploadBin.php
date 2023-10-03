
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
include_once('../classes/pclzip.lib.php');
include_once ("../classes/AE_cls_fileUpload.php");

$upl = new AE_cls_fileUpload();

$portDigits = array("0","1","2","3","4","5","6","7","8","9","-");
$data = array_merge($_GET,$_POST);
$error = "";
$done = "";
$db = new DB();



if ($data["action"] == "fase2")
{

//  debug($_REQUEST);
//  exit;
  $filedata = $_SESSION["zipUpload"]["files"];

  $categorie        = $_REQUEST["categorie"];
  $stdOmschrijving  = $_REQUEST["omschrijving"];
  $path             =  $_SESSION["zipUpload"]["path"];
  $dataset          = array();
  foreach ($_REQUEST as $fld=>$value)
  {

    $fldParts = explode("#", $fld);
    $ident = explode("||", $fldParts[1]);
    $fieldname    = $fldParts[0];
    $portefeuille = $ident[0];
    $index        = $ident[1];

    if ($fieldname == "check")
    {
      $desc = $filedata[$index]["file"];
      if ($_REQUEST["indeling"] == "file")
      {


      }

      $dataset[$index] = array(
        "portefeuille" => $portefeuille,
        "portaal"      => ($_REQUEST["portaal#".$portefeuille."||".$index] == on),
        "categorie"    => $_REQUEST["categorie#".$portefeuille."||".$index],
        "file"         => $filedata[$index]["file"],
        "crmId"        => $filedata[$index]["exists"],
        "desc"         => $_REQUEST["omschrijving#".$portefeuille."||".$index]
      );
    }
  }


//  debug($_SESSION["zipUpload"]);
  foreach ($dataset as $item)
  {

    $filename   = $path."/".$item["file"];
    $file       = basename($filename);
    $filesize   = filesize($filename);
    $filetype   = mime_content_type($filename);
    $fileHandle = fopen($filename, "r");
    $docdata    = fread($fileHandle, $filesize);
    fclose($fileHandle);

    $dd = new digidoc();
    $rec=array();
    $rec ["filename"]     = $file;
    $rec ["filesize"]     = "$filesize";
    $rec ["filetype"]     = "$filetype";
    $rec ["description"]  = $item["desc"];
    $rec ["keywords"]     = "";
    $rec ["module"]       = 'CRM_naw';
    $rec ["module_id"]    = $item["crmId"];
    $rec ["categorie"]    = $item["categorie"];
    $rec ["blobdata"]     = $docdata;
    //debug($rec, $filename);
    $dd->useZlib = false;
    if (!$dd->addDocumentToStore($rec))
    {
      echo "<br> afgebroken door foutmelding..";
      exit;
    }
    echo "<li> document <b>$file</b> toegevoegd aan relatie met portefeuille <b>".$item["portefeuille"]."</b> in categorie <b>".$item["categorie"]."</b></li>";

    logDD_refference($dd->referenceId,'id','','Toegevoegd.');

    if ($item["portaal"])
    {
      $dbU = new DB(DBportaal);
      $dbHost = new DB();
      $query="SELECT portefeuille FROM CRM_naw WHERE id='".$item["crmId"]."'";

      $CRMRec = $dbHost->lookupRecordByQuery($query);

      if(trim($CRMRec['portefeuille']) == '' AND $CRMRec['CRMGebrNaam'] != '')
      {
        $CRMRec['portefeuille'] ='P'.str_pad($CRMRec['CRMGebrNaam'], 6, '0', STR_PAD_LEFT);
      }

      $airsRefId = $dd->referenceId;
      $dbPort = new DB(DBportaal);
      $query="SELECT id FROM clienten WHERE portefeuille='".$CRMRec["portefeuille"]."'";

      if ($clntRec = $dbPort->lookupRecordByQuery($query))
      {
        $dd = new digidoc(DBportaal);
        $dd->useZlib = false;
        $rec ["module_id"] = $clntRec["id"];
        $rec ["module"] = 'clienten';
        $extraVelden=array('portaalKoppelId'=>$airsRefId,'reportDate'=>date('Y-m-d'),'clientID'=>$clntRec["id"]);
        if($dd->addDocumentToStore($rec,$extraVelden) == false)
        {
          echo "Niet gelukt om document in de portaal te plaatsen.<br>\n";flush(); ob_flush();
        }
        else
        {
          echo "<li> document <b>$file</b> toegevoegd in het portaal</li>";
        }
        logDD_refference($airsRefId,'portaalKoppelId','Toegevoegd aan portaal.',$dd->referenceId);

        $query = "UPDATE dd_reference SET  portaalKoppelId ='{$dd->referenceId}' WHERE id = {$airsRefId}";
        $dbHost->executeQuery($query);
      }
      else
      {
        echo "<li> <b>$file</b>: Client/Portefeuille onbekend in portaal, document niet toegevoegd</li>";
      }
    }

//    unlink($filename);
  }

  exec('rm -rf '.$path);
//  debug($_REQUEST);

  exit;
}


if ($data["action"] == "upload")
{

//debug($_REQUEST);
//exit;
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


    $indeling = $_REQUEST["indeling"];
    $delimiter = $_REQUEST["delimiter"];
    $categorie = $_REQUEST["categorie"];
//    debug($_REQUEST);
    $stdOmschrijving = trim($_REQUEST["omschrijving"]);

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
      $files = scanAllDir($dirName);
      $rawFiles = $files;
      $files = array();
      $portDetected = "";
      $index = 0;

      foreach ($rawFiles as $item)
      {
        if ($indeling == "map")
        {
          $p = explode("/", $item);
          if ($stdOmschrijving != "")
          {
            $desc = $stdOmschrijving;
          }
          else
          {
            $d = explode ("/", $item);
            $desc = substr($d[count($d)-1],0,-4);
          }
        }
        else
        {
          $p = explode($delimiter, basename($item));
          if ($stdOmschrijving != "")
          {
            $desc = $stdOmschrijving;
          }
          else
          {
            $d = explode ($delimiter, $item);
            $desc = substr($d[1],0,-4);
          }

        }
        $portefeuille = mysql_real_escape_string($p[1]);


//debug($item, $desc);
        $query = "SELECT id, naam, Portefeuille FROM `CRM_naw` WHERE `Portefeuille` = '".$portefeuille."'";
//        debug($query);
        $pRec = $db->lookupRecordByQuery($query);
        $files[] = array(
          "exists"        =>  (int)$pRec["id"],
          "name"          => $pRec["naam"],
          "portefeuille"  => $pRec["Portefeuille"],
          "category"      => $categorie,
          "file"          => $item,
          "desc"          => $desc
        );

      }

      $_SESSION["zipUpload"]["path"]  = $dirName;
      $_SESSION["zipUpload"]["files"] = $files;
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

  fieldset div{
    line-height: 3;
  }
  table{
    width: 90%;
    margin:0 auto;
  }
  td{
    border-bottom: 1px #999 solid;
  }

  thead td{
    background: rgba(20,60,90,1);;
    color: white;
    border:none;
    padding:5px 10px;
    vertical-align: sub;
  }
  tbody tr:hover{
    background: beige;
  }
  .vink,
  .vinkP{
    cursor: pointer;
  }


  legend{
    width: 200px;
    height: 18px;
    background: rgba(20,60,90,1);;
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
  #messageBar{
    position: fixed;
    background: darkgreen;
    text-align: center;
    font-weight: bold;
    font-size:1.5em;
    color: white;
    top:0;
    height: 30px;
    left:0;
    right:0;
    padding-top:0.75em;

  }
  #btnSubmit{
    margin: 5px 50px;
    padding: 10px 20px;
    background: rgba(20,60,90,1);;
    color: white;
    cursor: pointer;
    border:0;
    outline: none;
  }

  .vDiv{
    margin-top:10px;
  }
  .lDiv{
    display: inline-block;
    width: 200px;
  }
  #itemSelected{
    font-width: bold;
    font-size: larger;
  }
  #itemCountRow{
    padding: 5px;
    text-align: center;
    font-size: 1em;
  }
  #fileSelect{
    display: none;
  }
  #uitlegContent{
    padding:.5em 1em;
    margin-left:2em;
    line-height: 1.2em;
  }
</style>
<link rel="stylesheet" href="widget/css/font-awesome.min.css">
<br/>
<br/>

<div class="pageContainer">
  <h2>&nbsp;&nbsp;&nbsp; Binck Jaaropgaves Uploaden in .ZIP file</h2>
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
  <form enctype="multipart/form-data" method="post" id="theForm">
    <?
    if (count($files) == 0)
    {
      ?>
      <input type="hidden" name="action" value="upload" />
      <input type="hidden" name="indeling" value="file" />
      <input type="hidden" name="delimiter" value="_" />

      <fieldset>
        <legend>Selecteer  bestand (.zip)</legend>
        <br/>




        <div>
          <div class="lDiv">Standaard categorie: </div>
          <select name="categorie">
            <?=catOptions()?>
          </select>
        </div>
        <div>
          <div class="lDiv">Standaard omschrijving: </div>
          <input name="omschrijving" size="60"/>

        </div>
        <div id="loadProducts">
          <div class="lDiv">Zipbestand:</div>
          <input type="file" name="uploadFile">
        </div>
        <br/>
        <br/>
        <input type="submit" value="start upload">
      </fieldset>
      <?
    }
    else
    {

      ?>
      <div id="messageBar">
        Niet geselecteerde bestanden <span id="unSelectedItems"></span> van totaal <span id="itemTotal"></span>
      </div>
      <input type="hidden" name="action" value="fase2" />
      <input type="hidden" name="indeling" value="<?=$_REQUEST["indeling"]?>" />
      <input type="hidden" name="delimter" value="<?=$_REQUEST["delimiter"]?>" />
      <table>
        <thead>
        <tr>
          <td><input type='checkbox' id='checkAll' class='vinkH'></td>
          <td>Portefeuile</td>
          <td>Naam</td>
          <td>Bestand</td>
          <td>Omschrijving</td>
          <td>categorie</td>
          <td><input type='checkbox' id='checkAllP' class='vinkP'> naar portaal</td>
        </tr>
        </thead>
        <tbody>
        <?
        $index = 0;
        //debug($files);
        foreach ($files as $index=>$docs)
        {

          $exists   = ($docs["exists"] > 0);
          $name     = $docs["name"];
          $crmId    = $docs["exists"];
          $port     = $docs["portefeuille"];
          $file     = $docs["file"];
          $category = $docs["category"];
          $desc     = $docs["desc"];

          if ($exists)
          {
            echo "
            <tr>
            <td>
              <input type='checkbox' name='check#{$port}||{$index}' class='vink'> 
            </td>
            <td>{$port}</td>
            <td>{$name}</td>
            <td>".basename($file)."</td>
            <td><input name='omschrijving#{$port}||{$index}' value='{$desc}'></td>
            <td><select name='categorie#{$port}||{$index}'>".catOptions($category)."</select></td>
            <td><input type='checkbox' name='portaal#{$port}||{$index}' class='vinkP'> </td>
            </tr>
      ";

          }
          else
          {
            echo "
            <tr>
            <td> </td>
            <td>{$port}</td>
            <td>Bestaat niet in CRM</td>
            <td>".basename($file)."</td>
            <td>".$file."</td>
            </tr>
      ";
          }



        }
        ?>
        </tbody>

      </table>
      <button id="btnSubmit">verwerk</button>
      <?

    }
    ?>
  </form>



  <br/>
  <br/>
  <br/>

</div>
<script>

  $(document).ready(function () {
    let sel;

    const mapTxt = `


      `;

    $("#btnSubmit").click(function(e){
      e.preventDefault();
      if (sel != 0)
      {
        $("#theForm").submit();
      }
      else
      {
        alert("selecteer tenminste 1 bestand");
      }
    });

    $("#indeling").change(function(){
      const v = $(this).val();
      console.log(v);
      if (v == "file")
      {

        $("#fileSelect").show(200);
      }
      else
      {

        $("#fileSelect").hide(200);
      }
    });
    $
    $('.vink').prop('checked',true);
    const totaal = $('.vink').filter(':checked').length;
    $("#itemTotal").text(totaal);
    countUnSelected();
    $("#checkAll").click(function(){
      $('.vink').prop('checked', $(this).is(":checked"));
      countUnSelected();
    });
    $("#checkAllP").click(function(){
      $('.vinkP').prop('checked', $(this).is(":checked"));
      countUnSelected();
    });
    $(".vink").click(function(){
      countUnSelected();
    });

    function countUnSelected(){
      sel = $('.vink').filter(':checked').length
      const unSelected = totaal - sel;
      console.log(totaal, sel, unSelected);
      $("#unSelectedItems").text(unSelected);
      $("#messageBar").css("background", (unSelected == 0)?"darkgreen":"darkorange");
    }

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

function scanAllDir($dir)
{
  $result = array();

  foreach(scandir($dir) as $filename)
  {
    if ($filename[0] === '.') continue;
    $filePath = $dir . '/' . $filename;
    if (is_dir($filePath))
    {
      foreach (scanAllDir($filePath) as $childFilename)
      {
        $result[] = $filename . '/' . $childFilename;
      }
    }
    else
    {
      $result[] = $filename;
    }
  }

  return $result;
}

function logDD_refference($recordId,$veld,$oude,$nieuwe)
{
  global $USR;
  $db=new DB();
  $query="INSERT INTO  trackAndTrace SET tabel='dd_reference', recordId ='$recordId',veld='$veld',oudeWaarde='".mysql_real_escape_string($oude)."',nieuweWaarde='".mysql_real_escape_string($nieuwe)."',add_date=now(),add_user='$USR'";
  $db->SQL($query);
  $db->query();
}

function catOptions($value)
{
  global $optionsArray;

  if (count($optionsArray) == 0)
  {
    $db = new DB();
    $query = "SELECT omschrijving FROM (CRM_selectievelden) WHERE module = 'docCategrien' ";
    $options = "<option value=''>---</option>";
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $optionsArray[] = $rec["omschrijving"];
    }
  }

  foreach ($optionsArray as $option)
  {
    $selected = (stristr($option,$value))?"SELECTED":"";
    $options .= "<option value='{$option}' {$selected}>{$option}</option>";
  }
  return $options;

}
