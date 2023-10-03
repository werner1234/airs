<?php
/*
  AE-ICT source module
    Author                 : $Author: cvs $
    Laatste aanpassing     : $Date: 2014/03/12 11:18:50 $
    File Versie            : $Revision: 1.2 $

    $Log: advent_filemanager.php,v $
    Revision 1.2  2014/03/12 11:18:50  cvs
    *** empty log message ***

    Revision 1.1  2013/11/15 10:22:21  cvs
    aanpassing tbv Adventexport

    Revision 1.2  2012/06/21 10:42:12  cvs
    update 21-6-2012

    Revision 1.1  2011/11/29 10:39:56  cvs
    *** empty log message ***

    Revision 1.1  2011/10/26 12:20:43  cvs
    versie 1.00 eerste commit



*/

include_once("wwwvars.php");
include_once("../config/advent_functies.php");
session_start();

$cfg = new AE_config();
$directory = str_replace("//","\\",$cfg->getData("advent_outputDir"));

$autoDelete = ($cfg->getData("advent_deleteAfterDownload") == "ja");

if ($_POST)
{

 if ($_POST["action"] == "delete")
 {
   unlink($directory."/".$_POST["file"]);
   //logActivity("export","verwijderen van ".$_POST["file"],__FILE__);

 }
 if ($_POST["action"] == "push")
 {

    $dbTT = new DB();
    $file = $directory."/".$_POST["file"];
    /*
    if (strpos($file, "geldMutatie") > 0 AND $autoDelete)
    {
      $handle = fopen($file, "r");
      while ($data = fgetcsv($handle, 1000, ";"))
      {
	       if (count($data) < 2)  continue;  // lege regels overslaan
	       $row++;
         $dataRow = '"'.implode('","',$data).'"';
      $query = '
        INSERT INTO cashTransactiesHistorie
        (
        '.$ctraFieldLayout.',
        batchId,
        batchId_stamp,
        add_user,
        add_date,
        change_user,
        change_date,
        filename
        )
        VALUES
        (
        '.$dataRow.',"'.$batchId.'","'.$batchId_stamp.'","'.$_SESSION["USR"].'", now(),"'.$_SESSION["USR"].'", now(),"'.$_POST["file"].'"
        )';
        $dbTT->executeQuery($query);
      }
    }
    if (strpos($file, "effectenMutatie") > 0 AND $autoDelete)
    {
      $dbTT = new DB();
      $file = $directory."/".$_POST["file"];
      $handle = fopen($file, "r");
      while ($data = fgetcsv($handle, 1000, ";"))
      {
	       if (count($data) < 2)  continue;  // lege regels overslaan
	       $row++;
      $dataRow = '"'.implode('","',$data).'"';
      $query = '
        INSERT INTO effectenTransactiesHistorie
        (
        '.$straFieldLayout.',
        batchId,
        batchId_stamp,
        add_user,
        add_date,
        change_user,
        change_date,
        filename
        )
        VALUES
        (
        '.$dataRow.',"'.$batchId.'","'.$batchId_stamp.'","'.$_SESSION["USR"].'", now(),"'.$_SESSION["USR"].'", now(),"'.$_POST["file"].'"
        )';
        $dbTT->executeQuery($query);
      }

    }

    logActivity("export","downloaden van ".$_POST["file"],__FILE__);
    */
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header("Content-type: application/force-download");
    header("Content-Transfer-Encoding: Binary");
    header("Content-length: ".filesize($file));
    header("Content-disposition: attachment; filename=\"".basename($file)."\"");
    readfile("$file");
    header('Connection: close');

    if ($autoDelete)
      unlink($file);

    //header("location:".$PHP_SELF);
    exit();
 }
}


echo template($__appvar["templateContentHeader"],$editcontent);

?>
<style>


.rowitem{
  float: left;
  width: 95%;
}

.done{
  color: red;
}

</style>
<script>
$(document).ready(function()
{
});


function Download(file)
{
  $("#action").val("push");
  $("#file").val(file);
  fm.submit();

}
function Delete(file)
{
  $("#action").val("delete");
  $("#file").val(file);
  fm.submit();
}

</script>

<form action="<?=$PHP_SELF?>" method="POST" name="fm">
<input type="hidden" name="file" id="file" value="" />
<input type="hidden" name="action" id="action" value="" />

</form>

<br />
<fieldset style="background-color: beige; width: 700px;">
  <legend> instellingen </legend>
  uitvoermap: <?=$directory?> <br />
  automatisch verwijderen na download: <?=($autoDelete)?"ja":"nee";?>
</fieldset>
<p></p>
<table>


<?

if (!is_dir($directory))
{
  $error[] = "FOUT: uitvoermap is geen geldige map op de server";
}
else
{
  if (!is_writable($directory) ) $error[] = "FOUT: geen rechten om te schrijven in uitvoermap";
}
if (count($error) > 0)
{
  echo "meldingen <hr />";
  for ($x=0 ;$x < count($error);$x++)
  {
    echo "<li>".$error[$x]."</li>";
  }
  echo "<a href='advent_setup.php' >Instellingen aanpassen</a>";
  exit;
}

if ($handle = opendir("$directory"))
{
    while ($file = readdir($handle) )
    {
        $files[] = $file;
    }
    sort($files);
    if (count($files) > 2)  // zijn er bestanden?
    {
      for ($x=0; $x < count($files); $x++)
      {
        $file = $files[$x];
        if(is_dir($full_path."/".$file))
          continue;
        else
        {
          $download = (getRights("export"))?"<button onClick='$(\"#d_$x\").hide(); Download(\"$file\")' > download </button>":"";
          $delete   = (getRights("import"))?"<button onClick='Delete(\"$file\")' > verwijderen </button>":"";
          echo "<tr  id='d_$x'>
                  <td>{$download}</td>
                  <td>{$delete}</td>
                  <td>{$file}</td>
                </tr>";
        }
      }
    }
    else
      echo "Geen bestanden gevonden!";
}
?>
</table>
<br /><br />
<a href='tijdelijkerekeningmutatiesList.php'>Ga naar tijdelijke rekeningmutaties</a>
<?

echo template($__appvar["templateRefreshFooter"],$content);
?>