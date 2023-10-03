<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/06/29 13:59:17 $
 		File Versie					: $Revision: 1.50 $


*/
include_once("wwwvars.php");
include_once ("../classes/AE_cls_fileUpload.php");


$__appvar["templateContentHeader"] = "../".$__appvar["templateContentHeader"];
$__appvar["templateRefreshFooter"] = "../".$__appvar["templateRefreshFooter"];



$content = array(
  "style" => '
<link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">
<script type="text/javascript" src="../javascript/jquery-min.js"></script>
<script type="text/javascript" src="../javascript/jquery-ui-min.js"></script>

<link rel="../stylesheet" href="style/fontAwesome/font-awesome.min.css">
  '
);

session_start();
$upl = new AE_cls_fileUpload();

$_SESSION["NAV"] = "";

$action = $_REQUEST["action"];

session_write_close();
//$content = array();
global $USR;





// if poster
if($_POST['posted'])
{

	// check filetype
  if($_FILES['importfile']["type"] != "text/comma-separated-values" &&
	   $_FILES['importfile']["type"] != "text/x-csv" &&
	   $_FILES['importfile']["type"] != "text/csv" &&
	   $_FILES['importfile']["type"] != "text/xml" &&
	   $_FILES['importfile']["type"] != "application/octet-stream" &&
	   $_FILES['importfile']["type"] != "application/vnd.ms-excel" &&
	   $_FILES['importfile']["type"] != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" &&
	   $_FILES['importfile']["type"] != "text/plain")
	{
		$_error = "FOUT: verkeerd bestandstype(".$_FILES['importfile'][type]."), alleen .csv bestanden zijn toegestaan.";
	}
	// check error
	if($_FILES['importfile']["error"] != 0)
	{
		$_error = "Fout: bestand niet ingevuld of bestaat niet (".$_FILES['importfile']['name'].")";
	}

	if (empty($_error))
	{
    $rawData = array();
    $handle = fopen($_FILES['importfile']['tmp_name'], "r");
    $row = 0;
    while ($data = fgetcsv($handle, 8192, ","))
    {
      $row++;
      if ($row == 1)
      {
        continue; //skip header
      }

//debug($data);
      switch ($action)
      {
        case "stukken":
          $pos = getPositionByFonds($data[0], $data[3], $data[4]);
          $rawData[] = array(
            "type" => "stukken",
            "portefeuille" => $data[0],
            "isin"         => $data[3]."/".$data[4],
            "aantalVerkoop" => sprintf('%f',round($data[6],8)),
            "inPositie"     => sprintf('%f',$pos),
            "positieNieuw"  => sprintf('%f', round(($pos - $data[6]),8)),
            "status"        => ($pos - $data[6]) >= 0?"Akkoord":"Let op Short!"
          );

          break;
        case "geld";
          $rawData[$data[0]] += $data[8];
          break;
      }
    }
    fclose($handle);
    unlink($_FILES['importfile']['tmp_name']);

	}

	//debug($rawData);

}

echo template($__appvar["templateContentHeader"],$content);
//if(!$_FILES['importfile']['name'])
{

?>
  <link rel="stylesheet" href="../style/smoothness/jquery-ui-1.11.1.custom.css">
  <link rel="stylesheet" href="../widget/css/font-awesome.min.css">


  <style>

    table {
      width: 90%;
      background: whitesmoke;
      margin: 10px;
      padding: 10px;
    }

    .trHead {
      background: rgba(20, 60, 90, 1);
      color: white;
      border-bottom: 1px solid #FFF;
    }

    .trHead td {
      color: white;
      padding: 5px;
      padding-left: 10px;

    }

    .trSubHead {
      background: #666;
      color: white;
      border-bottom: 1px solid #FFF;
      height: 2rem;
    }

    .trSubHead td {
      color: white;
      padding-left: 10px;
      text-align: left;
      font-size:1rem;
      font-weight: bold;
    }

  #melding{
    margin: 0;
    color:maroon;
    font-size: 1.2em;
    font-weight: bold;
  }

  .filenaam{
    width: 500px;
  }


  .inp{
    line-height: 24px;
    padding: 4px;
    font-weight: bold;
  }
  #bestand2Oms{
    display: none;
    float: left;
    width:120px;
    background:  #E9E9E9;
    
    
  }
  #bestand2Input{
    display: none;
    float: left;
    width:500px;
  }
  #bestand1Oms{
    float: left;
    width:120px;
    background: #E9E9E9;
  }
  #bestand1Input{
    float: left;
    width:500px;
  }
  .csSelect{
    font-size: 1.1em;
    padding: 2px 5px 2px 5px;
  }
  #dialogMdlPortVrg{
    visibility: hidden;
    padding: 5px;;
    display: inline-block;
    height: 18px;
    width: 130px;
    background: orange;
  }
  article{
    width: 300px;
    float: left;
  }
  article :after{
    clear: both;
  }
  .mergeContainer{
    width: 95%;
  }
  .mergeHeader{
    background: rgba(20,60,90,1);
    color: white;
    margin: 0;
    padding: 5px;
  }
  .mergeContent{
    display: none;
    width: 100%;
    height: 350px;
  }
  .dividerRow{
    background: rgba(20,60,90,1);
    color: white;
    margin: 0;
    padding: 5px;
    font-weight: 600;
    text-align: center;
    margin-bottom: 5px;
  }
  .ar{
    text-align: right;
  }
  .rood td{
    background: rgba(253,22,19,0.42);
  }

</style>

  <?php

  if (count($rawData) > 0)
  {
    echo $_error;



    if ($action == "stukken")
    {
      $out .= '      
      <table>
        <tr class="trSubHead">
          <td colspan="17"> Verkoopcontrole '.$action.'</td>
        </tr>
        <tr class="trHead">
          <td>Portefeuille</td>
          <td>ISIN</td>
          <td class="ar">Aantal verkoop</td>
          <td class="ar">In positie</td>
          <td >Aantal nieuw</td>
          <td >Status</td>
        </tr>
        ';
      foreach ($rawData as $item)
      {
//      debug($item);
        $highlight = ($item["status"] != "Akkoord")?"rood":"";
        {

        }
        $out .= "
      <tr class='{$highlight}'>
        <td>{$item["portefeuille"]}</td>
        <td>{$item["isin"]}</td>
        <td class='ar'>{$item["aantalVerkoop"]}</td>
        <td class='ar'>{$item["inPositie"]}</td>
        <td class='ar'>{$item["positieNieuw"]}</td>
        <td>{$item["status"]}</td>
      </tr>
      ";
      }

      $out .= "</table>";
    }
    else
    {
      $out .= '      
      <table>
        <tr class="trSubHead">
          <td colspan="17"> Verkoopcontrole '.$action.'</td>
        </tr>
        <tr class="trHead">
          <td>Portefeuille</td>
          <td class="ar">Verkoop bedrag</td>
          <td class="ar">Waarde Prtf</td>
          <td >% Verkoop</td>
          <td >Status</td>
        </tr>
        ';
      include_once ("../rapport/rapportRekenClass.php");
      foreach ($rawData as $portefeuille => $verkoop)
      {
        $pw = portefeuilleValue($portefeuille);
        $verkoop = number_format($verkoop,2,".","");
        $perct  = round(($verkoop/$pw) * 100,1);
        $status = ($perct > 90)?"Let op, volledige verkoop":"Akkoord";
        if ($verkoop > $pw)
        {
          $status = "Let op, verkoop > prtf waarde";
        }
        $highlight = ($status != "Akkoord")? "rood":"";
        $out .= "
      <tr class='{$highlight}'>
        <td>{$portefeuille}</td>
        <td class='ar'>{$verkoop}</td>
        <td class='ar'>{$pw}</td>
        <td class='ar'>{$perct}%</td>
        <td>{$status}</td>
      </tr>
      ";
      }
    }


    echo $out;

  }
  else
  {
    ?>


    <form enctype="multipart/form-data"  method="POST"  name="editForm">
      <!-- MAX_FILE_SIZE must precede the file input field -->
      <input type="hidden" name="posted" value="true" />
      <input type="hidden" name="action" value="<?=$action?>" />


      <!-- Name of input element determines name in $_FILES array -->
      <br/><br/>
      <h3>Verkoop controle <?=$action?></h3><br/><br/>

      <div class="formblock">
        <div class="formlinks">Importbestand </div>
        <div class="formrechts">
          <input type="file" name="importfile" id="importfile" class="filenaam">
        </div>
      </div>
      <br/>


      <div class="form">
        <div class="formblock">
          <div class="formlinks"> &nbsp;</div>
          <div class="formrechts">
            <input type="submit" value="verwerk" ">
          </div>
        </div>
      </div>

    </form>



    <script>

      $(document).ready(function()
      {



      });

    </script>
    <?
  }

}
echo template($__appvar["templateRefreshFooter"],$content);

function getPositionByFonds($portefeuille, $isin, $valuta="")
{
  $db = new DB();
  $fonds = getFonds($isin, $valuta);

  $query = "
     SELECT
        Rekeningen.Portefeuille as portefeuille,
        Rekeningmutaties.Fonds,
        SUM(Rekeningmutaties.Aantal) AS aantal
      FROM 
        Rekeningmutaties
      JOIN Rekeningen ON  
        Rekeningmutaties.Rekening  = Rekeningen.Rekening AND Rekeningen.consolidatie = '0'
      JOIN Portefeuilles ON 
        Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie = '0'
      WHERE
        Rekeningmutaties.Grootboekrekening = 'FONDS' AND
        YEAR(Rekeningmutaties.Boekdatum) = '".date("Y")."' AND
        Rekeningmutaties.Verwerkt = '1' AND
        Rekeningmutaties.Boekdatum <= NOW() AND
        Rekeningen.Portefeuille = '{$portefeuille}' AND
        Rekeningmutaties.Fonds = '{$fonds["Fonds"]}'
      GROUP BY 
        portefeuille,Rekeningmutaties.Fonds
      HAVING 
        round(aantal,8) <> 0
    ";
  $positie = $db->lookupRecordByQuery($query);

  return round((float) $positie["aantal"],8);

}

function getFonds($isin, $valuta)
{

  $db = new DB();

  $query = "SELECT * FROM Fondsen WHERE ISINCode = '{$isin}' AND  Valuta = '{$valuta}'";

  $fonds = $db->lookupRecordByQuery($query);

  return $fonds;


}

function portefeuilleValue($portefeuille)
{
  include_once ("../rapport/rapportRekenClass.php");
  $pWaarde = berekenPortefeuilleWaardeQuick($portefeuille,date("Y-m-d"));
  $totaal = 0;
  foreach ($pWaarde as $item)
  {
     $totaal += $item["actuelePortefeuilleWaardeEuro"];
  }
  return $totaal;
}