<?php
/*
    AE-ICT sourcemodule created 18 sep. 2020
    Author              : Chris van Santen
    Filename            : dd_referenceEditMulti.php

    naar RVV 20201216
*/

include_once("wwwvars.php");
include_once("../classes/editObject.php");
include_once("../classes/AE_cls_digidoc.php");

$data   = array_merge($_POST,$_GET);
$action = $data['action'];
$path   = $__appvar["basedir"]."/temp/dd";
$db     = new DB();
$dbU    = new DB();

$mainHeader = "toevoegen";
$subHeader  = "van (meerdere) documenten bij client";

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editcontent["style"] = '
<link rel="stylesheet" href="style/workspace.css" type="text/css" media="screen">
<link rel="stylesheet" href="style/AIRS_default.css" type="text/css" media="screen">

';

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->returnUrl = $_SESSION["dd_referenceList"]["url"];
$extraNavSettings['opslaanNietVerlaten']['hidden'] = true;

$_SESSION['NAV']->addItem(new NavEdit("editForm",false,false,false));
$_SESSION['NAV']->extraSettings=$extraNavSettings;

echo template($__appvar["templateContentHeader"],$editcontent);


//debug($data);

if ($data["action"] == "fase2")
{
  ?>
    <h3>Stap 2. aanvullen gegevens bij bestanden</h3>
  <?php
  include_once ("../classes/AE_cls_fileUpload.php");
  $upl = new AE_cls_fileUpload();


  if (!file_exists($path))
  {
    mkdir($path);
  }
  $rel_id = $data["rel_id"];
  $prefix = $data["batch"]."--";
  foreach ($data as $k=>$v)
  {
    $kParts = explode("_", $k);
    if ($kParts[0] == "file")
    {
      if ($upl->checkExtension($v))
      {
        $uploadedFiles[] = $v;
      }
    }
  }

  $query = "SELECT omschrijving FROM (CRM_selectievelden) WHERE module = 'docCategrien' ORDER BY  omschrijving";
  $options = "<option value=''>---</option>";
  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    $selected = (stristr($rec["omschrijving"],"document"))?"SELECTED":"";
    $options .= "<option value='{$rec["omschrijving"]}' {$selected}>{$rec["omschrijving"]}</option>";
  }
?>
  <style>
    table{
      width: 90%;
      background: white;
    }
    thead td{
      background: rgba(20,60,90,1);
      color: whitesmoke;
      padding: 3px 5px;
    }
    td{
      padding: 3px 5px;
    }
    #meldingen{
      width: 90%;
      background: maroon;
      color: white;
      display: none;
      padding: 1em;
    }
    .inp-memo,
    .inp-omschrijving{
      width: 400px;
    }
  </style>
  <div id="meldingen"></div>
  <form id="formUpload" action="dd_referenceEditMulti.php">

    <input type="hidden" name="action" value="fase3" />
    <input type="hidden" name="rel_id" value="<?=$rel_id?>" />
    <input type="hidden" name="prefix" value="<?=$prefix?>" />

    <table>
      <thead>
      <tr>
        <td>Regel</td>
        <td>Bestand</td>
        <td>Omschrijving</td>
        <td>Categorie</td>
        <td>Naar<br/>portaal</td>
      </tr>
      </thead>
      <?
      $rownr = 0;
      foreach($uploadedFiles as $docRow)
      {
        $f = $path . "/" .cnvFilename($prefix.$docRow);
        $exist = file_exists($f);
        $file = explode(".", $docRow);
        $cleanFileName = cnvFilename($docRow);
        $rownr++;
        if ($exist)
        {

          echo "
        <tr>
          <td >{$rownr}:</td>
          <td >{$cleanFileName} <input type='hidden' name='row_{$rownr}' value='{$docRow}' /></td>
          <td><input class='inp-omschrijving' type='text' name='omschrijving_{$rownr}' placeholder='Omschrijving' value='{$file[0]}'/> </td>
          
          <td ><select class='inp-categorie' name='categorie_{$rownr}'>{$options}</select> </td>
          <td ><input class='inp-portaal' type='checkbox' name='portaal_{$rownr}'/></td>
        </tr>
        <tr><td colspan='10'><hr></td> </tr>  
        ";
        }
        else
        {
          echo "
        <tr>
          <td >{$rownr}:</td>
          <td >Probleem</td>
          <td>bestand {$f} is beschadigd of corrupt </td>
          
          <td ></td>
          <td ></td>
        </tr>
        <tr><td colspan='10'><hr></td> </tr>  
        ";
        }

      }
      ?>

    </table>

    <button id="btnSubmit">opslaan</button>

  </form>

  <script>
    $(document).ready(function(){
      $("#btnSubmit").click(function(e){
        e.preventDefault();
        let err = "";
        $(".inp-omschrijving").each(function() {
          if ($(this).val().trim() == "")
          {
            err = "Fout: alle omschrijvingen moeten ingevuld zijn!";
          }
        });
        if (err != "")
        {
          $("#meldingen").html(err);
          $("#meldingen").show();
        }
        else
        {
          $("#formUpload").submit();
        }

      });
    });
  </script>
<?

  exit;
}  // $data["action"] == fase2
else if ($data["action"] == "fase3")
{

  $rel_id = $data["rel_id"];
  $prefix = $data["prefix"];
  foreach ($data as $k=>$v)
  {
    $kParts = explode("_", $k);
    if ($kParts[0] == "row")
    {
      $ddArray[$kParts[1]] = array(
        "file"          => $data["row_".$kParts[1]],
        "omschrijving"  => $data["omschrijving_".$kParts[1]],
        "categorie"     => $data["categorie_".$kParts[1]],
        "portaal"       => ($data["portaal_".$kParts[1]] == "on"),
        "memo"          => $data["memo_".$kParts[1]],
      );
    }
  }


  echo "<ul>";
  foreach ($ddArray as $upFile)
  {



    $filename   = $path . "/" . cnvFilename($prefix . $upFile["file"]);
    $file       = $upFile["file"];
    $filesize   = filesize($filename);
    $filetype   = mime_content_type($filename);
    $fileHandle = fopen($filename, "r");
    $docdata    = fread($fileHandle, $filesize);
    fclose($fileHandle);

    if ($filesize == 0)
    {
      logit("Digidoc::document overgeslagen (leeg bestand) bij relatie CRM_naw id {$rel_id}" );
      echo "<li> document <b>$file</b> overgeslagen (leeg bestand)</li>";
      continue;
    }
    $fsize = round($filesize/1024,1)."Kb";
    $dd = new digidoc();
    $rec=array();
    $rec ["filename"]     = cnvFilename($file);
    $rec ["filesize"]     = "$filesize";
    $rec ["filetype"]     = "$filetype";
    $rec ["description"]  = $upFile["omschrijving"];
    $rec ["blobdata"]     = $docdata;
    $rec ["keywords"]     = "";
    $rec ["module"]       = 'CRM_naw';
    $rec ["module_id"]    = $rel_id;
    $rec ["categorie"]    = $upFile["categorie"];
    $rec ["keywords"]     = $upFile["memo"];
    $dd->useZlib = false;
    if (!$dd->addDocumentToStore($rec))
    {
      logit("Digidoc::afgebroken door foutmelding bij relatie CRM_naw id {$rel_id}" );
      echo "<br> afgebroken door foutmelding..";
      exit;
    }
    $ddRefId = $dd->referenceId;
    logit("Digidoc::document {$file} (id={$ddRefId}, {$fsize}) toegevoegd aan relatie CRM_naw id {$rel_id}" );
    echo "<li> document <b>$file ($fsize)</b> toegevoegd aan relatie</li>";

    logDD_refference($dd->referenceId,'id','','Toegevoegd.');

    if($upFile['portaal'])
    {

      /*
       * if(trim($data['portefeuille']) == '' && $data['CRMGebrNaam'] <> '')
        $data['portefeuille']='P'.str_pad($data['CRMGebrNaam'], 6, '0', STR_PAD_LEFT);
       */
      $dbHost = new DB();
      $query="SELECT portefeuille, CRMGebrNaam FROM CRM_naw WHERE id='".$rel_id."'";
      $CRMRec = $dbHost->lookupRecordByQuery($query);

      if(trim($CRMRec['portefeuille']) == '' AND $CRMRec['CRMGebrNaam'] != '')
      {
        $CRMRec['portefeuille'] ='P'.str_pad($CRMRec['CRMGebrNaam'], 6, '0', STR_PAD_LEFT);
      }



      $airsRefId=$dd->referenceId;
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
          logit("Digidoc::Niet gelukt om document in de portaal te plaatsen bij relatie CRM_naw id {$rel_id}" );
          echo "Niet gelukt om document in de portaal te plaatsen.<br>\n";flush(); ob_flush();
        }
        else
        {
          logit("Digidoc::document {$file}  toegevoegd aan portaal relatie CRM_naw id {$rel_id}" );
          echo "<li> document <b>$file</b> toegevoegd in het portaal</li>";
        }
        logDD_refference($airsRefId,'portaalKoppelId','Toegevoegd aan portaal.',$dd->referenceId);

        $query = "UPDATE dd_reference SET  portaalKoppelId ='{$dd->referenceId}' WHERE id = {$airsRefId}";
        $dbU->executeQuery($query);
      }
      else
      {
        logit("Digidoc::{$file}  Client/Portefeuille onbekend in portaal, document niet toegevoegd CRM_naw id {$rel_id}" );
        echo "<li> <b>$file</b>: Client/Portefeuille onbekend in portaal, document niet toegevoegd</li>";
      }


    }


    unlink($filename);

  }

?>
<br/>
<br/>
  <button id="btnBack" class="btnBlue">terug naar document overzicht</button>
<br/>
<br/>
<script>
  $(document).ready(function (){
    $("#btnBack").click(function(e){
      e.preventDefault();
      window.open("frameSet.php?page=<?=base64_encode("dd_referenceList.php?module=CRM_naw&id={$rel_id}")?>", "extraFrame");
    });

  });
</script>

<?php
}
else
{

  // onderhoud verwijder alle bestanden die overgebleven zijn uit eerdere sessies
  $filesInPath = scandir($path);
  foreach ($filesInPath as $f)
  {
    $fParts = explode("_", $f);
    if ($fParts[0] == $USR)
    {
      unlink($path."/".$f);
    }
  }

  $_SESSION["importCombine"] = $USR."_".date("Ymd_Hi");  // "batch" code
?>



  <section class="mergeContainer">

      <iframe src="dd_reference_multiUpload.php?rel_id=<?=$data["rel_id"]?>&batch=<?=$_SESSION["importCombine"]?>" frameborder="0" width="100%" height="100%" id="mergeframe" name="mergeframe"></iframe>

  </section><br/>
<?php
}

echo template($__appvar["templateRefreshFooterZonderMenu"],$content);

function logDD_refference($recordId,$veld,$oude,$nieuwe)
{
  global $USR;
  $db=new DB();
  $query="INSERT INTO  trackAndTrace SET tabel='dd_reference', recordId ='$recordId',veld='$veld',oudeWaarde='".mysql_real_escape_string($oude)."',nieuweWaarde='".mysql_real_escape_string($nieuwe)."',add_date=now(),add_user='$USR'";
  $db->SQL($query);
  $db->query();
}
