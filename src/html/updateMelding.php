<?
// header
$content = array();
if($_GET['fix'])
{

  if($_GET['fix']==2)
  {
    $__appvar["basedir"] =  realpath(dirname(__FILE__)."/..");
    $__appvar["tempdir"] = $__appvar["basedir"]."/temp/";
    if (file_exists($__appvar["tempdir"]."update.lock"))
    {
      if(unlink($__appvar["tempdir"]."update.lock"));
         echo "Lockfile verwijderd.";
    }
    else
      echo "Lockfile ".$__appvar["tempdir"]."update.lock"." niet gevonden.";
    exit;
  }
  echo 'Weet u zeker dat u de blokkade geforceerd wilt verwijderen? <a href="updateMelding.php?fix=2">doorgaan</a>';
  exit;
}
echo template($__appvar["templateContentHeader"],$content);
?>
<script type="text/javascript">
alert("Op dit moment worden de database gegevens bijgewerkt, een moment geduld A.U.B.\n\n(Probeer uw opdracht na 30 seconden nog eens)");
</script>
<br>Indien deze melding na lange tijd nog steeds getoond wordt kan deze blokkade worden verwijderd via onderstaande link. <br>
<a href="updateMelding.php?fix=1">"klik hier"</a>
<?
echo template($__appvar["templateContentFooter"],$content);
?>