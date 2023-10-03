<?php
/*
    AE-ICT CODEX source module versie 1.6, 14 november 2009
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/03/06 08:52:20 $
    File Versie         : $Revision: 1.2 $

    $Log: dd_bulkimport.php,v $
    Revision 1.2  2018/03/06 08:52:20  cvs
    Degiro Jaaropgaves

    Revision 1.1  2018/02/23 08:11:12  cvs
    call 6685




*/

include("wwwvars.php");
include_once("../classes/AE_cls_digidoc.php");
include_once("../classes/AE_cls_Email.php");

$cfg=new AE_config();
$data=array();

$seperator = "_";

$map = "_dd_bulkmap";

if ($_POST["posted"] == "true")
{
  $data = explode("[]",$_POST["data"]);


  foreach($data as $item)
  {
    $pair = explode("|",$item);
    if ($_POST["check_".$pair[0]] == "on" )
    {
      $files[] = $pair[1];
    }

  }



  foreach ($files as $item)
  {
    $fileparts  = explode("/", $item);
    $nameOnly = $fileparts[count($fileparts)-1];
    if (checkForPortefeuille($nameOnly))
    {
      $filename=$item;
      $file=$filename;
      $filesize = filesize($filename);
      $filetype = mime_content_type($filename);
      $fileHandle = fopen($filename, "r");
      $docdata = fread($fileHandle, $filesize);
      fclose($fileHandle);

      $dd = new digidoc();
      $rec=array();
      $rec ["filename"] = $nameOnly;
      $rec ["filesize"] = "$filesize";
      $rec ["filetype"] = "$filetype";
      $rec ["description"] =$_POST["omschrijving"];
      $rec ["blobdata"] = $docdata;
      $rec ["keywords"] = "";
      $rec ["module"] = 'CRM_naw';
      $rec ["module_id"] = $rel_id;
      $rec ["categorie"] = $_POST['categorie'];
      $dd->useZlib = false;
      $dd->addDocumentToStore($rec);
      echo "<li>document toegevoegd aan portefeuille $portefeuille.<br>\n";flush(); ob_flush();
      if($_POST['toPortaal'])
      {
        $dbp=new DB(DBportaal);
        $query="
          SELECT 
            id 
          FROM 
            clienten 
          WHERE 
            portefeuille='".mysql_real_escape_string($portefeuille)."'";

        $clientData=$dbp->lookupRecordByQuery($query);
        if($clientData['id'] > 0)
        {
          $clientId=$clientData['id'];
          $airsRefId=$dd->referenceId;
          $dd = new digidoc(DBportaal);
          $dd->useZlib = false;
          $rec ["module_id"] = $clientId;
          $rec ["module"] = 'clienten';
          $extraVelden=array('portaalKoppelId'=>$airsRefId,'reportDate'=>date('Y-m-d'),'clientID'=>$clientId);
          if($dd->addDocumentToStore($rec,$extraVelden) == false)
          {
            echo $portefeuille.": Niet gelukt om document in de portaal te plaatsen.<br>\n";flush(); ob_flush();
          }
          $db=new DB();
          $query="UPDATE dd_reference SET portaalKoppelId='".$dd->referenceId."' WHERE id='$airsRefId'";
          $db->SQL($query);
          $db->Query();
        }
        else
        {
          echo $portefeuille.": Geen clientkoppeling in Portaal gevonden.<br>\n";flush(); ob_flush();
        }
      }

     // unlink($item);
    }
    else
    {
      $s = explode("/",$item);
      echo "<li>bestand ".$s[count($s)-1]." overgeslagen. Portefeuille $portefeuileCheckFailed niet in het CRM gevonden<br>\n";flush(); ob_flush();
    }


  }


  exit;
}


echo template($__appvar["templateContentHeader"],$content);
?>
  <h1>Document inlezen vanuit map..</h1>
<?

if ($handle = opendir($map))
{
  $stamp = date("Ymd_Hi") . "-";

}
$ndx = 1;
while ( ($file = readdir($handle)) !== false )
{
    $fp = getcwd()."/".$map."/".$file;

    $progressStep = 0;
    if (!is_dir($fp) )
    {
      $bestanden[] = array(
        "id"       => $ndx,
        "fullname" => $fp,
        "name"     => $file,
        "size"     => displayFileSize(filesize($fp)),
        "portefeuille" => checkForPortefeuille($file)
      );
      $ndx++;
    }


}
closedir($handle);



$rowTemplate = "
<tr class='msgRow' id='row_{id}'>
  <td>{id}</td>
  <td><input type='checkbox' class='chkBx' name='check_{id}' checked/> </td>
  <td>{portefeuille}</td>
  <td>{name}</td>
  <td>{size}</td>
  <td>{fullname}</td>
  
</tr>
";

$tmpl = new AE_template();
$fmt = new AE_cls_formatter();

$tmpl->loadTemplateFromString($rowTemplate,"rowTemplate");

$_SESSION[NAV]='';

?>
<style>
  .headField
  {
    display: inline-block;
    width: 70px;
    font-weight: normal;

  }
  table{
    width: 100%;
    background: whitesmoke;
    margin:10px;
    padding:10px;
  }
  .trHead{
    background: saddlebrown;
    color: white;
    border-bottom: 1px solid #FFF;
  }
  .trHead td{
    color:white;
    padding-left:10px;
  }
  #previewMsg{
    background: #dff0d8;
    color: #333; !important;
    width: 800px;
    height: 400px;
    box-shadow: #000A28 3px 3px 3px;
    overflow: auto;
    padding:5px;
    float: left;
  }
  .mailHead{
    background: #0A246A;
    color: whitesmoke; !important;
    width: 760px;
    height: 30px;
    box-shadow: #000A28 3px 3px 3px;
    padding: 10px;
    font-weight: bold;
  }
  .msgRow :hover{
    cursor: pointer;
    background: #ffc121;
  }
  .mailbox{
    float: left;
    width: 770px;
  }
</style>
<form method="post">
  <input name="posted" type="hidden" value="true"/>


<fieldset>
  <div class="formblock">
    <div class="formlinks">&nbsp; </div>
    <div class="formrechts">
      <input type="checkbox" name="toPortaal"  > Naar portaal <br/>
    </div>
  </div>
  <div class="formblock">
    <div class="formlinks">Categorie:  </div>
    <div class="formrechts">
      <select name="categorie" id="categorie">
        <?=categorien()?>
      </select>
    </div>
  </div>
  <div class="formblock">
    <div class="formlinks">Omschrijving:  </div>
    <div class="formrechts">
      <input type="text" name="omschrijving" value="ingelezen via bulkimport: " style="width: 300px;"/>
    </div>
  </div>

</fieldset>

<div class="mailbox">
  <table>
    <tr class="trHead">
      <td>#</td>
      <td> </td>
      <td>portefeuille</td>
      <td>bestand</td>
      <td>grootte</td>
      <td>..</td>
    </tr>
    <?
    foreach($bestanden as $msg)
    {
      echo $tmpl->parseBlock("rowTemplate",$msg);
      $text .= $msg["id"]."|".$msg["fullname"]."[]";
    }
    ?>


  </table>
  <input type="hidden" name="data" value="<?=$text?>" />
  <input type="submit" value="Verwerk"/>
</form>
</div>

<?
function categorien()
{
  $db = new DB();
  $query = "SELECT omschrijving FROM (CRM_selectievelden) WHERE module = 'docCategrien'";
  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    $out .= "<option value='".$rec["omschrijving"]."'>".$rec["omschrijving"]."</option>\n";
  }
  return $out;
}

function displayFileSize($bytes)
{
  if ($bytes/1024 < 1 )
  {
    return $bytes." b";
  }
  else
  {
    $kilo = floor($bytes/1024);
    if ($kilo/1024 < 1)
    {
      return round($bytes/1024,1). " Kb";
    }
    else
    {
      $mega = $kilo/1024;
      return round($mega,1). " Mb";
    }
  }
}

function checkForPortefeuille($filenaam)  // bestaat portefeuile in CRM_NAW
{
  global $portefeuille, $rel_id, $seperator, $portefeuileCheckFailed;
  $db = new DB();
  /*  2018--> Binck jaaropgave
  $pair = explode($seperator,$filenaam);

  if ($pair[1] != "")
  {

    $query = "
  SELECT
    portefeuille,
    id
  FROM
    CRM_naw
  WHERE 
    portefeuille = '".$pair[1]."'
  ";

    if ($rec = $db->lookupRecordByQuery($query))
    {
      $portefeuille = $rec["portefeuille"];
      $rel_id       = $rec["id"];
      return $portefeuille;
    }
  }
*/
  /*  2018--> DeGiro jaaropgave */
  $pair = explode(".",substr($filenaam,-11));

  if ($pair[0] != "")
  {
    $portefeuileCheckFailed = $pair[0];
    $query = "
  SELECT
    portefeuille,
    id
  FROM
    CRM_naw
  WHERE 
    portefeuille = '".$pair[0]."'
  ";

    if ($rec = $db->lookupRecordByQuery($query))
    {
      $portefeuille = $rec["portefeuille"];
      $rel_id       = $rec["id"];
      return $portefeuille;
    }
  }

  return false;
}

?>