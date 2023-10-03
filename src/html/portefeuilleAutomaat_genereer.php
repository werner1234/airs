<?php
/*
Author  						: $Author: cvs $
Laatste aanpassing	: $Date: 2012/03/09 09:08:56 $
File Versie					: $Revision: 1.1 $

$Log: portefeuilleAutomaat_genereer.php,v $
Revision 1.1  2012/03/09 09:08:56  cvs
*** empty log message ***


*/
include_once("wwwvars.php");



session_start();

$_SESSION["NAV"] = "";
$_SESSION["submenu"] = "";

$cfg = new AE_config();
$directory = str_replace("//","\\",$cfg->getData("portAutomaat_importmap"));


$content = array();
$_bank = $_POST["bank"];

if ($_POST["posted"])
{
  header("location: portefeuilleAutomaat_verwerk.php?bank=$_bank");
  exit;
}

echo template($__appvar["templateContentHeader"],$content);

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


</script>

<form action="<?=$PHP_SELF?>" method="POST" name="fm">
<input type="hidden" name="file" id="file" value="" />
<input type="hidden" name="action" id="action" value="" />
  
</form>

<br />
<fieldset style="background-color: beige; width: 700px;">
  <legend> <?= vt('importmap'); ?> </legend>
  <?=$directory?> <br />
  
</fieldset>
<p></p>
<table>
<?

if (!is_dir($directory))
{
  $error[] = "FOUT: importmap is geen geldige map op de server";
}
else
{
  if (!is_writable($directory) ) $error[] = "FOUT: geen rechten om te schrijven in importmap";  
}
if (count($error) > 0)
{
  echo "" . vt('meldingen') . " <hr />";
  for ($x=0 ;$x < count($error);$x++)
  {
    echo "<li>".$error[$x]."</li>";
  }
  echo "<a href='portefeuilleAutomaat_setup.php' >" . vt('Instellingen aanpassen') . "</a>";
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
        if($file == ".." OR $file == ".")
        {
          continue;
        }   
        else 
          $options .= "<OPTION value='$file' >$file</OPTION>\n";
       }          
    }
    else
      echo vt("Geen bankmappen gevonden!");
} 
?>
</table>
<form action="<?=$PHP_SELF?>" method="POST">
<input type="hidden" name="posted" value="true" />
<?= vt('Welke bankmap gebruiken'); ?> :
<select name="bank" >
  <?=$options?>
</select>
<br />
<br />
<input type="submit" value="Bankmap inlezen" />

</form>
<?
echo template($__appvar["templateRefreshFooter"],$content);

?>