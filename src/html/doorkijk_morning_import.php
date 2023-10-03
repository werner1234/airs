<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/08/23 11:36:50 $
    File Versie         : $Revision: 1.7 $

    $Log: doorkijk_morning_import.php,v $
    Revision 1.7  2019/08/23 11:36:50  cvs
    call 8024
*/

// veld 1:	Fonds
// veld 2:  Valuta
// veld 3:  ISISN
// veld 4:  Fondsnaam
// veld 5:  MS_Ticker, wordt niet gebruikt, tbv FE.
// veld 6-x:    Wegingskolommen
// veld x+1:    Totaal, wordt niet gebruikt, tbv FE.

include_once("wwwvars.php");
include_once("doorkijk_morning_functies.php");
include_once ("../classes/AE_cls_fileUpload.php");
$upl = new AE_cls_fileUpload();
$_SESSION["NAV"] = "";
//$allow_add = checkAccess($type);

define("DELIM",";");
define("BEGIN_VELDEN",6);

$targetDirectory = "import/";

echo template($__appvar["templateContentHeader"],$editcontent);

// --- laatst ingelezen Eurokoersdatum ---
$parts = explode("-",substr(getLaatsteValutadatum(),0,10));
$toon_datum = $parts[2]."-".$parts[1]."-".$parts[0];


if ( $_POST['action'] == "upload" )
{
  $uploadFile = basename($_FILES["fileToUpload"]["name"]) ;
  $file = "importdata/".$uploadFile ;
  $fileType = pathinfo($file, PATHINFO_EXTENSION);
  if (!$upl->checkExtension($_FILES['fileToUpload']['name']))
  {
    echo vt("Fout").": ".vt("verkeerd bestandsformaat");
    exit;
  }

  if ( $fileType != "csv" )
  {
    echo vt("Fout").": ".vt("Geen csv-file, import afgebroken");
    exit;
  }
  // Check if $uploadOk is set to 0 by an error

  if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $file))
  {
    echo vt("Fout").": ".vt("Upload mislukt, import afgbroken");
    exit;
  }
  $csv = array();

  $csvRegels = 1;
  include_once ("doorkijk_morning_validate.php");

  if ($_POST["doIt"] <> "1")
  {
    if ( !validateCvsFile($file, $_POST["categorieSoort"]) )
    {
?>
      <table cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="2" bgcolor="#BBBBBB">
          <?=vt("Foutmelding bij validatie van CSV bestand")?><br>
          <?=vt("Bestandsnaam")?>: <?=$file?>
        </td>
      </tr>
<?
      foreach ( $error as $errorMsg )
      {
        $errorMsg = explode(':', $errorMsg);
        $foutregels[] = trim($errorMsg[0]);
        echo '
          <tr>
              <td bgcolor="#BBBBBB">' . trim($errorMsg[0]) . '</td>
              <td>&nbsp;&nbsp; ' . trim($errorMsg[1]) . '</td>
          </tr>
        ';
      }
?>
        </table>
        <br>
        <br>
        <b><?=vt("Vervolg aktie")?>?</b>
        <form action="<?=$PHP_SELF?>" method="post">
          <input type="hidden" name="doIt" value="1">
          <input type="hidden" name="bestand" value="<?=$file?>">
          <input type="hidden" name="foutregels" value="<?=implode(",", $foutregels)?>">
          <input type="hidden" name="categorieSoort" value="<?=$_POST['categorieSoort'];?>" >
          <input type="hidden" name="datumVanaf" id="datumVanaf" value="<?=$_POST['datumVanaf'];?>">
          <select name="action">
            <option value="stop"><?=vt("Bestand verwijderen en import afbreken")?></option>
            <option value="go"><?=vt("Bestand inlezen en onvolledige regels overslaan")?></option>
            <option value="retry"><?=vt("Bestand opnieuw inlezen en valideren")?></option>
          </select>
          <input type="submit" name='submit' value="<?=vt("Uitvoeren")?>">
        </form>
<?
      }
      else
      {
?>       
        <br/><br/><br/><h3><?=vt("Validatie voltooid")?>.</h3><br/><hr/>
        <form action="<?=$PHP_SELF?>" method="post">
            <input type="hidden" name="action" value="go" />
            <input type="hidden" name="bestand" value="<?=$file?>" />
            <input type="hidden" name="datumVanaf" id="datumVanaf" value="<?=$_POST['datumVanaf'];?>">
            <input type="hidden" name="categorieSoort" value="<?=$_POST['categorieSoort'];?>" >
            <input type="submit" value="inlezen" />
        </form>

 <?
        }
        echo template($__appvar["templateRefreshFooter"],$content);
        exit();
    }

}

if ($_POST["action"] == "stop" OR $_POST["action"] == "retry")
{
  $skipFoutregels = array();
  switch ($_POST["action"])
  {
    case "stop":
      echo "<br>".vt("Het transactiebestand is verwijderd en de import is afgebroken");
      if (file_exists($_POST["bestand"]) ) { unlink($_POST["bestand"]); }
      exit();
      break;
    case "retry":
      $doIt = 0;
      $file = $_POST["bestand"];
      break;
    default:
//      $skipFoutregels = explode(",",$foutregels);
//      array_shift($skipFoutregels);  // verwijder eerste lege key
//      $file =$_POST["bestand"];
  }
}


if ( $_POST['action'] == 'go'  )
{
  debug($_POST,"start verwerking");
    $categorieSoort = $_POST["categorieSoort"];
    $output = 0;
    $file = $_POST['bestand'] ;
    $row = -1;
    $handle = fopen($file, "r");
    $DB = new DB();

    $skipFoutregels = explode(",",$_POST["foutregels"]);
    $skipped = "";

    while ($data = fgetcsv($handle, 4096, DELIM))
    {

      $row++;
      if ($row == 0)
      {
         for ($x=0; $x < count($data); $x++)
         {
            $header[$data[$x]] = $x;
         }
         $aantal_velden = count($header) - 2; // 1 minder tgv tellen vanaf 0, 1 minder omdat laatste kol is totaal
         continue;
      }
      else
      {
        if (in_array($row, $skipFoutregels))
        {
          $skipped .= "<br>- ".vt("regel")." $row ".vt("overgeslagen");
          continue; // rest overslaan, lees nieuwe regel
        }
  
        for ($idx = BEGIN_VELDEN; $idx <= $aantal_velden; $idx++)
        {
          // lege waarden niet verwerken

          $weging = str_replace(",", ".", $data[$idx]);
          if ($weging != 0)
          {
            $parts = explode("-", $_POST['datumVanaf']);
            $datum = $parts[2] . "-" . $parts[1] . "-" . $parts[0];

            $query = " 
              INSERT INTO 
                doorkijk_categorieWegingenPerFonds 
              SET
                add_user          = '$USR', 
                add_date          = NOW(), 
                change_user       = '$USR',
                change_date       = NOW(),
                datumVanaf        = '$datum', 
                Fonds             = '" . $data[$header['Fonds']] . "', 
                Valuta            = '" . $data[$header['Val']] . "',
                ISINCode          = '" . $data[$header['ISIN']] . "',
                msCategoriesoort  = '$categorieSoort',
                datumProvider     = '" . formdate2db($data[$header['ProvDatum']]) . "',
                msCategorie       = '" . array_search($idx, $header) . "', 
                weging            = '$weging'";

            $DB->executeQuery($query);
            $output++;
          }
        }
      }
    }
    fclose($handle);
    if (file_exists($file)) { unlink($file); }
    
?>
    <H2><?=vt("Klaar met inlezen")?></H2>

  <?=vt("Records in Extern CSV bestand")?>: <?=$row?><br/>
  <?=vt("Aangemaakte mutatieregels")?>: <?=$output?><br/>
  <?=vt("Aantal overgeslagen regels")?>: <?=$skipped?>

<?
echo template($__appvar["templateRefreshFooter"],$content);
exit;
}


?>
 <br/>
 <form action="" method="post" enctype="multipart/form-data">
    <input type='hidden' name='action' value='upload' >
    <h3> <?=vt("Selecteer bestand")?>:</h3>
    <input type="file" name="fileToUpload" id="fileToUpload" value="<?=$file?>"><br><br>
    
    <label for="datumVanaf"><?=vt("Datum vanaf")?>:</label>
    <input type="text" class="AIRSdatepicker" name="datumVanaf" id="datumVanaf" value="<?=$toon_datum;?>" onchange="date_complete(this);"><br><br>
    <table>
        <tr>
            <td><input type="radio" name="categorieSoort" value="Beleggingscategorien" >
              <?=vt("Beleggingscategorien")?>
            </td>
        </tr>
        <tr>
            <td><input type="radio" name="categorieSoort" value="Beleggingssectoren" id="Beleggingssectoren"><label for="Beleggingssectoren"> <?=vt("Beleggingssectoren")?></label></td>
        </tr>
        <tr>
            <td><input type="radio" name="categorieSoort" value="Regios" id="Regios"><label for="Regios"> <?=vt("Regios")?></label></td>
        </tr>
        <tr>
            <td><input type="radio" name="categorieSoort" value="Valuta" id="Valuta"><label for="Valuta"> <?=vt("Valuta")?></label></td>
        </tr>
        <tr>
            <td><input type="radio" name="categorieSoort" value="Rating" id="Rating"><label for="Rating"> <?=vt("Rating")?></label></td>
        </tr>
        <tr>
            <td><input type="radio" name="categorieSoort" value="Looptijd" id="Looptijd"><label for="Looptijd"> <?=vt("Looptijd")?></label></td>
        </tr>
        <tr>
          <td><input type="radio" name="categorieSoort" value="Coupon" id="Coupon"><label for="Coupon"> <?=vt("Coupon")?></label></td>
        </tr>
        <tr>
          <td><input type="radio" name="categorieSoort" value="Subtype obligaties" id="subtypeobligaties"><label for="subtypeobligaties"> <?=vt("Subtype obligaties")?></label></td>
        </tr>
    </table>
    <br>
   <input type="submit" name="submit" value="<?=vt("Inlezen")?>">
</form> 

<?

    echo template($__appvar["templateRefreshFooter"],$content);

    /*
    Controle op dubbele import regels in het bestand

      SELECT
        count(id) as aantal,
      Fonds,
      msCategoriesoort,
      datumVanaf,
      msCategorie
      FROM
        doorkijk_categorieWegingenPerFonds
      GROUP BY
      Fonds,
      msCategoriesoort,
      msCategorie,
      datumVanaf

      Having aantal > 1
    */