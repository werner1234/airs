<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/02/19 09:35:30 $
 		File Versie					: $Revision: 1.5 $

 		$Log: reconBoekVerschilmutaties.php,v $
 		Revision 1.5  2020/02/19 09:35:30  cvs
 		call 7937
 		
 		Revision 1.4  2020/02/12 07:49:00  cvs
 		call 7937
 		
 		Revision 1.3  2020/02/03 12:17:54  cvs
 		call 8390
 		
 		Revision 1.2  2020/02/03 10:51:33  cvs
 		call 7937
 		
 		Revision 1.1  2015/12/01 07:38:14  cvs
 		update 2540, call 4352
 		

*/
include_once("wwwvars.php");
session_start();
$_SESSION["NAV"] = "";
session_write_close();
//$content = array();

$data = $_POST;

$batch = false;
if ($_GET["batch"] != '')
{
  $batch = $_GET["batch"];
}

if ($data["posted"] == "true")
{
  include_once "../classes/AIRS_import_afwijkingen.php";
  
  if ($data["verschil"] < 0 OR $data["verschil"] > 50)
  {
    $error = "<div class='error'>Verschil bedrag buiten toegestane bereik</div>";
  }
 else
 {
   debug($data);
   $db = new DB();
   $dbLkup = new DB();

   $query = "DELETE FROM TijdelijkeRekeningmutaties WHERE add_user = '$USR' ";
   $db->executeQuery($query);

   if ($data["batch"] != "")
   {
     $query = "
     SELECT 
       * 
     FROM 
      `tijdelijkeRecon` 
     WHERE 
       ABS(verschil) <= " . $data["verschil"] . " AND 
       ABS(verschil) > 0 AND 
       (fondsCodeMatch = 'op rekening' OR 
        fondsCodeMatch = '' ) AND
       batch = '{$data["batch"]}' AND
       cashPositie = 1";
   }
   else
   {

     $query = "
     SELECT 
       * 
     FROM 
      `tijdelijkeRecon` 
     WHERE 
       ABS(verschil) <= " . $data["verschil"] . " AND 
       ABS(verschil) > 0 AND 
       (fondsCodeMatch = 'op rekening' OR 
        fondsCodeMatch = '' ) AND
       
       change_user = '{$USR}' AND
       cashPositie = 1";
   }


   $db->executeQuery($query);
   while ($rec = $db->nextRecord())
   {

     $afw = new AIRS_import_afwijkingen($rec["depotbank"]);

     $query = "
        SELECT
          *
        FROM
          `Valutakoersen`
        WHERE
          `Valuta` = '".$rec["valuta"]."' AND
          Datum <= '".$rec["reconDatum"]."'
        ORDER BY
          `Datum` DESC
       ";
     $koersRec = $dbLkup->lookupRecordByQuery($query);
     
     $verschil = $rec["verschil"];
     if( $verschil > 0 )
     {
       $waardeVeld="Credit='$verschil',Debet=0";
     }
     else
     {
       $waardeVeld="Debet='".abs($verschil)."',Credit=0";
     }

     $mr = array(
       "Rekening" => $rec["rekeningnummer"],
       "Grootboekrekening" => "KNBA",
       "Omschrijving" => "Correctie bankkosten"
     );
     $mr = verschilMut($mr);

     $reknr = findRekNr($rec["rekeningnummer"], $rec["depotbank"] );

     $query="
      INSERT INTO 
        TijdelijkeRekeningmutaties 
      SET 
        Rekening='".$reknr."',
        Omschrijving= '".$mr["Omschrijving"]."',
        Boekdatum='".$rec["reconDatum"]."',
        Valuta='".$rec["valuta"]."',
        Valutakoers='".$koersRec["Koers"]."',
        $waardeVeld, 
        Grootboekrekening= '".$mr["Grootboekrekening"]."',
        Bedrag='$verschil',
        change_date=now(),
        add_date=now(),
        add_user='$USR',
        change_user='$USR',
        Verwerkt='0',
        Fonds='',
        Fondskoers='0'";
     $tel++;

     $dbLkup->executeQuery($query);
   }
   echo template($__appvar["templateContentHeader"],$content);
?>
<h2>Klaar met genereren</h2>
Er zijn <?=(int)$tel?> mutaties aangemaakt.<br/>
<br/>
<a href="tijdelijkerekeningmutatiesList.php">Ga naar de tijdelijke rekeningmutaties</a>
<?
   echo template($__appvar["templateRefreshFooter"],$content);
   exit;
 }
}

echo template($__appvar["templateContentHeader"],$content);
?>
<style>
  .error{
    margin: 25px;
    background: red;
    color: white;
    padding: 20px;
    width: 400px;
    text-align: center;
    font-size: 1.5em;
 }
  
</style>
<form action="<?=$PHP_SELF?>" method="POST"  name="editForm">

<?=$error?>
  
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="batch" value="<?=$batch?>" />

<br />
<b>Verschilmutaties aanmaken</b><br><br>
<?
  if ($batch)
  {
    echo "<h3>voor batch {$batch}</h3>";
  }
  else
  {
    echo "<h3>voor gebruiker {$USR}</h3>";
  }


?>
<div class="form">


<div class="formblock">
  <div class="formlinks"><span id="posBestand">maximale absolute verschil</span> </div>
  <div class="formrechts">
    <input type="text" name="verschil" size="7" value="0.10"> ( tussen 0.01 en 50.00 )
  </div>
</div>

<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="submit" value="Maak tijdelijkerekeningmutaties aan" />
<br/><br/>
<span style="color:red; font-weight: bold"><b>LET OP:</b> Bestaande tijdelijkerekeningmutaties worden gewist!</span>
</div>
</div>

</form>
<?
echo template($__appvar["templateRefreshFooter"],$content);

function verschilMut($mr)
{
  global $afw;
  $mr = $afw->reWrite("KNBA",$mr);
  $mr = $afw->reWrite("Omschrijving",$mr);
  return $mr;
}

function findRekNr($rekeningNr, $depot)
{
  $db = new DB();

    $query = "SELECT * FROM Rekeningen WHERE `consolidatie` = 0 AND `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rec["Rekening"];
    }
    else
    {
      $query = "SELECT * FROM Rekeningen WHERE `consolidatie` = 0 AND `Rekening` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
      if ($rec = $db->lookupRecordByQuery($query))
      {
        return $rekeningNr;
      }
      else
      {
        return false;
      }

    }

}


?>