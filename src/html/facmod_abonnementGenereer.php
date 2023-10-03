<?php
/*
    AE-ICT CODEX source module versie 1.2, 26 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/12/06 14:17:45 $
    File Versie         : $Revision: 1.4 $

    $Log: facmod_abonnementGenereer.php,v $
    Revision 1.4  2019/12/06 14:17:45  cvs
    call 7675

    Revision 1.3  2019/11/18 09:11:36  cvs
    call 7675

    Revision 1.2  2019/11/13 15:13:48  cvs
    call 7675

    Revision 1.1  2019/07/22 09:11:22  cvs
    call 7675


*/
include_once("wwwvars.php");

$db = new DB();
$fmt = new AE_cls_formatter();

if (!facmodAccess())
{
  echo "geen toegang";
  return false;
}

if ($_GET["action"] == "go")
{

  $maandkort = $fmt->maandNaamKort;

  $periodeArray = array(
    "M" => 1,
    "K" => 3,
    "H" => 6,
    "J" => 12,
  );

  $query = "
    SELECT 
      facmod_abonnement.*,
      DATE_FORMAT(facmod_abonnement.vorigeVerwerkdatum,'%c') AS verwerk_maand, 
      DATE_FORMAT(facmod_abonnement.vorigeVerwerkdatum,'%Y') AS verwerk_jaar,
      DATE_FORMAT(facmod_abonnement.add_date,'%c') AS start_maand,
      DATE_FORMAT(facmod_abonnement.add_date,'%Y') AS start_jaar,
      CRM_naw.naam
    FROM 
      facmod_abonnement, 
      CRM_naw 
    WHERE 
      actief = 1 AND 
      CRM_naw.id = facmod_abonnement.rel_id
    ORDER BY 
      rel_id, 
      volgnr
      ";

  $db->executeQuery($query);
  $recs = $db->records();

  $now["maand"]  = date("n", mktime());
  $now["jaar"]   = date("Y", mktime());
  $now["telmnd"] = telmnd($now["jaar"], $now["maand"]);

  echo "<br><b>Maand : " . $now["maand"] . "  <br>";
  echo "Jaar : " . $now["jaar"] . "  <br>";
  echo "Verwerk datum : " . date("d-n-Y", mktime()) . "<br>";
  echo "Aantal abonnementen in selectie: " . $recs . "</b><br><br>";

  $t = 0;
  while ($data = $db->nextRecord())
  {

    $vvSplit = explode("-", substr($data["vorigeVerwerkdatum"],0,10));
    $vorigeVerwerk["maand"] = (int)$vvSplit[1];
    $vorigeVerwerk["jaar"]  = (int)$vvSplit[0];
    $vorigeVerwerk["telmnd"]  = telmnd($vorigeVerwerk["jaar"],$vorigeVerwerk["maand"]);

    $periode = $periodeArray[$data['periode']];
    $klantinfo = "<b>" . $data["naam"] . " / " . $data["txt"] . ", periode: " . $periode . " start jaar/maand: " . $data["start_jaar"] . " / " . $data["start_maand"] . "</b><br>";

    if ($vorigeVerwerk["maand"] == 0)  // eerste keer nooit eerder verwerkt
    {
      echo "<li>Eerste verwerking: ".$klantinfo;
      $volgende_periode = nextPeriod($periode);

      if ($data["eenheid"] == "uur")
      {
        $aantalMaanden = 1; // factor
      }
      else
      {
        $aantalMaanden = telmnd( $volgende_periode["jaar"], $volgende_periode["maand"]) - telmnd($data["start_jaar"], $data["start_maand"]) ;
      }


      if ($aantalMaanden > 0)
      {

        if ($volgende_periode["maand"] == 1)
        {
          $tm_maand = 12;
          $tm_jaar = $volgende_periode["jaar"] - 1;
        }
        else
        {
          $tm_maand = $volgende_periode["maand"] - 1;
          $tm_jaar = $volgende_periode["jaar"];
        }
        $factuurregel = "{$data["txt"]} {$maandkort[$data["start_maand"]]} {$data["start_jaar"]} t/m {$maandkort[$tm_maand]} $tm_jaar";
//          writeLog("Factuurregel $factuurregel , Aantal maanden tot volgende periode: " . $aantalMaanden, "");
        insertFactuurRegel();
        $t++;
      }

    }
    else  // verwerk de volgende periode
    {
      $startTV = nextPeriod($periode);
      $tmTV    = telmndToYM($startTV["telmnd"] + $periode -1);   // eind datum

      if ($now["telmnd"] > $vorigeVerwerk["telmnd"])   // vorige verwerking was vorige maand of eerder
      {
        $aa = ($data["achteraf"] == 1)?" achter":" vooruit";
        if ($data["achteraf"] == 1)
        {
          $startTV = telmndToYM($startTV["telmnd"] - $periode);
          $tmTV = telmndToYM($tmTV["telmnd"] - $periode );
        }
        $factuurregel = "{$data["txt"]} {$maandkort[$startTV["maand"]]} {$startTV["jaar"]} t/m {$maandkort[$tmTV["maand"]]} {$tmTV["jaar"]} ";;

        echo "<li>{$factuurregel}";

//      writeLog("Factuurregel $factuurregel , Aantal maanden tot volgende periode: " . $aantalMaanden, "");
        if ($data["eenheid"] == "uur")
        {
          $aantalMaanden = 1; // factor
        }
        else
        {
          $aantalMaanden = $periode;
        }
        insertFactuurRegel();
        $t++;

      }
    }
  }

}
else
{
  $query = "SELECT vorigeVerwerkdatum FROM facmod_abonnement ORDER BY vorigeVerwerkdatum DESC";
  $testRec = $db->lookupRecordByQuery($query);

  echo template($__appvar["templateContentHeader"],$editcontent);

  ?>
  <br/>
  <br/>
  <h3>genereer nieuwe factuurregels</h3>
  <form name="editForm" >
    <div class="form">
      <input type="hidden" name="action" value="go">
      <div class="formblock">
        <div class="formlinks">vorige verwerkingsdatum</div>
        <div class="formrechts">
          <?=$fmt->format("@D{form}",$testRec["vorigeVerwerkdatum"])?>
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks">&nbsp;</div>
        <div class="formrechts">
          <input type="submit" value="start verwerking">
        </div>
      </div>
    </div>
  </form>
  <?


  echo template($__appvar["templateRefreshFooter"],$content);
  exit;
}


function nextPeriod($periode)
{
  $maand = date("n", mktime());
  $jaar  = date("Y", mktime());
  $pos   = false;

  $periodes["maand"]     = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
  $periodes["tweemaand"] = array(1, 3, 5, 7, 9, 11);
  $periodes["kwartaal"]  = array(1, 4, 7, 10);
  $periodes["halfjaar"]  = array(1, 7);
  $periodes["jaar"]      = array(1);

  switch ($periode)
  {
    case 1 :
      $type = "maand";
      break;
    case 2 :
      $type = "tweemaand";
      break;
    case 3 :
      $type = "kwartaal";
      break;
    case 6 :
      $type = "halfjaar";
      break;
    case 12 :
      $type = "jaar";
      break;
    default :
      return false;
      break;
  }

  while (!$pos)
  {
    if ($maand > 12)
    {
      $maand = 1;
      $jaar += 1;
    }
    else
    {
      $maand++;
    }
    $pos = in_array($maand, $periodes[$type]);
  }
  return (array('jaar' => $jaar, 'maand' => $maand, 'telmnd' => telmnd($jaar, $maand)));
}


function insertFactuurRegel()
{
  global $data,
         $aantalMaanden,
         $factuurregel,
         $__facmod,
         $USR;
  $db = new DB();
  switch ($data["btw"])
  {
    case "H":
      $btwPer = $__facmod["btwH"];
      break;
    case "L":
      $btwPer = $__facmod["btwL"];
      break;
    default:
      $btwPer = 0;
      break;

  }
  // insert factuurregel
  $insertquery = "INSERT INTO facmod_factuurregels SET ";
  $insertquery .= "volgnr  = '" . $data["volgnr"] . "',";
  $insertquery .= "rel_id  = '" . $data["rel_id"] . "',";
  $insertquery .= "aantal  = '" . $data["aantal"] . "',";
  $insertquery .= "rubriek  = '" . $data["rubriek"] . "',";
  $insertquery .= "factor  = '" . $aantalMaanden . "',";
  $insertquery .= "eenheid = '". $data["eenheid"]."',";
  $insertquery .= "txt = '" . $factuurregel . "',";
  $insertquery .= "btw = '" . $data["btw"] . "',";
  $insertquery .= "btw_per = '" . $btwPer . "',";
  $insertquery .= "stuksprijs = '" . $data["stuksprijs"] . "',";
  $insertquery .= "totaal_excl = '" . ($data["aantal"] * $aantalMaanden * $data["stuksprijs"]) . "',";
  $insertquery .= "add_date = NOW(),";
  $insertquery .= "add_user = '$USR' ,";
  $insertquery .= "change_date = NOW(),";
  $insertquery .= "change_user = '$USR' ,";
  $insertquery .= "door = '" . $USR . "'";

  echo "<br>" . $data["rel_naam"] . "<br>" . $factuurregel;

  $query = "UPDATE facmod_abonnement SET vorigeVerwerkdatum = NOW() WHERE id = " . $data["id"];

  if (!$db->executeQuery($insertquery))
  {
    debug($insertquery,"FOUTmelding");
    exit;
  }

  if (!$db->executeQuery($query))
  {
    debug($query,"FOUTmelding");
    exit;
  }
}

echo "<br>Klaar met verwerken " . $t . " factuurregels gegenereerd!";
echo "<br>Nog niet verwerkte abbonementen " . $nn;


function telmnd($jaar, $maand)
{
  if ((int)$jaar < 2000)
  {
    return 0;
  }
  else
  {
    return (int)(($jaar - 2000) * 12 ) + $maand;  // aantal maanden sinds 2000
  }

}

function telmndToYM($telmnd = 0)
{
  if ($telmnd <= 0)
  {
    return false;
  }
  else
  {
    if ($telmnd%12 == 0)
    {
      $jaar  = floor($telmnd/12)+1999;
      $maand = 12;
    }
    else
    {
      $jaar  = floor($telmnd/12)+2000;
      $maand = $telmnd%12;
    }
  }
  return array("jaar"=>$jaar, "maand"=> $maand, "telmnd"=> $telmnd);
}




