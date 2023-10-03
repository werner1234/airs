<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/06/10 13:46:24 $
 		File Versie					: $Revision: 1.10 $

 		$Log: blancoExportFile.php,v $
 		Revision 1.10  2020/06/10 13:46:24  cvs
 		call 8517
 		
 		Revision 1.9  2020/06/10 13:44:21  cvs
 		call 8517
 		
 		Revision 1.8  2020/06/08 13:54:25  cvs
 		call 8517
 		
 		Revision 1.7  2020/05/20 09:10:13  cvs
 		call 8517
 		
 		Revision 1.6  2020/05/11 10:00:47  cvs
 		call 8517
 		
 		Revision 1.5  2020/05/04 11:45:05  cvs
 		call 8517
 		
 		Revision 1.4  2020/05/01 07:50:28  cvs
 		call 8517
 		
 		Revision 1.3  2020/05/01 06:54:39  cvs
 		call 8517
 		
 		Revision 1.2  2020/04/29 10:25:07  cvs
 		call 8517
 		
 		Revision 1.1  2020/04/24 06:37:35  cvs
 		call 8517
 		
*/
error_reporting(E_ALL);
include_once("wwwvars.php");
include_once("../classes/AE_cls_blancoExport.php");
include_once("../classes/AE_cls_lookup.php");

session_start();

$export = new blancoExport();

echo template($__appvar["templateContentHeader"],$editcontent);

echo "<h1>aanmaken Blanco exportbestanden</h1>";
echo "<div id='loading'><img src='images/loading.gif' width='48'/> moment bezig met verwerken </div>";

makeBlanco();

?>
<script>
  $("#loading").hide();
</script>
<br/><br/>
<a href='advent_filemanager.php'>Ga naar uitvoermap</a>
<?
exit;


function makeBlanco()
{
  global $USR, $rec;
  global $export, $errorArray;
  global $airsNetAmountArray;
  global $depotBank;

  $primaryGb = array("FONDS","DIV","KRUIS");
  $db = new DB();
  $lkp = new AE_lookup();

  $outputGb = array();
  $output = array();
  $controle = array();
  $airsNetAmountArray = array();

  $errorArray = array();
  $query = "
  SELECT
    TijdelijkeRekeningmutaties.*,
    Rekeningen.Depotbank,
    Portefeuilles.id as portefeuilleId,
    Fondsen.id as fondsId,
    Fondsen.ISINCode as ISIN,
    Fondsen.Valuta as fondsValuta
  FROM
    (TijdelijkeRekeningmutaties)
  INNER JOIN Rekeningen ON TijdelijkeRekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0
  INNER JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0
  LEFT JOIN Fondsen ON TijdelijkeRekeningmutaties.Fonds = Fondsen.Fonds
  
  ORDER BY 
  bankTransactieId
  AND ( verwerkt < 1 OR ISNULL( verwerkt ) ) 
	AND TijdelijkeRekeningmutaties.change_user = '$USR' 
  ";

  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {

    $uniekId = $rec["bankTransactieId"]."-".$rec["Rekening"];
    $output[$uniekId][] = $rec;
    $airsNetAmountArray[$uniekId] += $rec["Bedrag"];
    if ($rec["Transactietype"] == "D" OR $rec["Transactietype"] == "L")
    {
      $airsNetAmountArray[$uniekId] += $rec["Bedrag"];
    }

    if (in_array($rec["Grootboekrekening"], $primaryGb))
    {
      if ($rec["Grootboekrekening"] == "FONDS")
      {
        $outputGb[$uniekId] = $rec["Grootboekrekening"]."-".substr($rec["aktie"],0,1);
      }
      else
      {
        $outputGb[$uniekId] = $rec["Grootboekrekening"];
      }

    }
  }


//  debug(count($output));
//  debug($outputGb);
  foreach($output as $transId=>$dataset)
  {

    $afwijkVal = false;
    if (count($dataset) == 1)  // dataset met 1 poot
    {
      $row = $dataset[0];
      if (strtoupper(substr($row["Rekening"],-3)) == "MEM" OR strtoupper(substr($row["Rekening"],-3)) != "EUR")
      {
        $errorArray[] = "transId: {$transId}, boeking op MEM/VV rekening {$row["Rekening"]}";
      }
      if ($row["fondsValuta"] != "")
      {
        $afwijkVal = ($row["Valuta"] != $row["fondsValuta"]);
      }
//      debug($row);
      $gb = $row["Grootboekrekening"];
      switch ($gb)
      {
        case "ONTTR":
        case "STORT":
        case "BEH":
        case "BEW":
        case "KNBA":
        case "RENTE":
//          debug($row);
          $bmap = $export->mapGrootBoek($row["Grootboekrekening"], $row["Bedrag"]);
          if ($bmap["gb"] == "WITHDRAW" OR $bmap["gb"] == "DEPOSIT")
          {
            $IRN = "";
          }
          else
          {
            $IRN = "AIRS";
          }
//          $export->addField("Product Reference Name", "AIRS-$gb");
          $export->addField("Product Reference Name", "AIRS");
          $export->addField("Party Reference Value", $row["portefeuilleId"]);
          $export->addField("Counter Party Reference Value", "");
          $export->addField("No of Units", abs($row["Credit"]-$row["Debet"]),"N@3" );
          $export->addField("Unit Price", $row["Valutakoers"], "N@8");
          $export->addField("Transaction Date", $row["Boekdatum"], "D");
          $export->addField("Settlement Date", $row["settlementDatum"], "D");
          $export->addField("Instrument Reference Name", $IRN);
          $export->addField("Instrument Reference Value", $row["fondsId"]);
          $export->addField("ISIN", $row["ISIN"]);
          $export->addField("Currency", $row["Valuta"]);
          $export->addField("Performance Impact", $bmap["pi"]);
          $export->addField("Type", $bmap["gb"]);
          $export->addField("Exchange Rate", $row["Valutakoers"],"N@8");
          $export->addField("Amount", abs($row["Bedrag"]));
          $export->addField("External Execution Id", $row["bankTransactieId"]);

          $export->addField("Comments", $row["Omschrijving"]);
          $export->addField("Accrued Interest", "");
          $netAmount = abs($row["Bedrag"]);
          $export->addField("Settlement Net Amount", $netAmount);
          netCheck($netAmount, $airsNetAmountArray[$transId], $transId);
          $export->pushBuffer();
          break;

        case "RENOB":
          break;

        case "FONDS":

          $subType = "FONDS-".$row["Transactietype"];
          $amount = abs($row["Bedrag"]) * $row["Valutakoers"];
          $noOfUnits = abs($row["Bedrag"]);
          $bmap = $export->mapGrootBoek($subType, $row["Bedrag"]);

          $IRN = "AIRS";

          $st = $row["Transactietype"];

          $export->addField("Product Reference Name", "AIRS-1poot-{$subType}");
          $export->addField("Party Reference Value", $row["portefeuilleId"]);
          $export->addField("Counter Party Reference Value", "");
          $export->addField("No of Units", abs($row["Aantal"]),"N@4");
          $export->addField("Unit Price", $row["Fondskoers"],"N@8");
          $export->addField("Transaction Date", $row["Boekdatum"], "D");
          $export->addField("Settlement Date", $row["settlementDatum"], "D");
          $export->addField("Instrument Reference Name", $IRN);
          $export->addField("Instrument Reference Value", $row["fondsId"]);
          $export->addField("ISIN", $row["ISIN"]);
          $export->addField("Currency", $row["Valuta"]);
          $export->addField("Performance Impact", $bmap["pi"]);
          $export->addField("Type", $bmap["gb"]);
          $export->addField("Exchange Rate", $row["Valutakoers"],"N@8");
          $export->addField("Amount", abs($row["Aantal"] * $row["Fondskoers"]),"N");
          if ($st != "L" AND $st != "D")
          {
            $export->addField("External Execution Id", $row["bankTransactieId"]);
            $export->addField("Settlement Net Amount", $amount,"N");
          }

          $export->addField("Comments", $row["Omschrijving"]);
          $export->addField("Product Reference Name", "AIRS");

          $export->pushBuffer();
          break;

          break;
        case "DIV":
          $bmap = $export->mapGrootBoek($gb, $row["Bedrag"]);
          if ($afwijkVal)
          {
            $export->addField("Currency", "EUR");
            $export->addField("Amount", $row["Bedrag"],"N");
            $export->addField("Exchange Rate", 1,"N@8");
          }
          else
          {
            $export->addField("Currency", $row["Valuta"]);
            $export->addField("Amount", $row["Credit"]-$row["Debet"],"N");
            $export->addField("Exchange Rate", $row["Valutakoers"],"N@8");
          }
//          debug($export->lineBuffer, "afwijkval=".$afwijkVal);
//          $export->addField("Product Reference Name", "AIRS-$gb-".count($dataset));
          $export->addField("Product Reference Name", "AIRS");
          $export->addField("Party Reference Value", $row["portefeuilleId"]);
          $export->addField("Counter Party Reference Value", "");
          $export->addField("No of Units", abs($row["Credit"]-$row["Debet"]),"N@4");
          $export->addField("Unit Price", $row["Valutakoers"],"N@8");
          $export->addField("Transaction Date", $row["Boekdatum"], "D");
          $export->addField("Settlement Date", $row["settlementDatum"], "D");
          $export->addField("Instrument Reference Name", "AIRS");
          $export->addField("Instrument Reference Value", $row["fondsId"]);
          $export->addField("ISIN", $row["ISIN"]);

          $export->addField("Performance Impact", $bmap["pi"]);
          $export->addField("Type", $bmap["gb"]);


          $export->addField("Accrued Interest", "");
          $export->addField("External Execution Id", $row["bankTransactieId"]);
          $export->addField("Comments", $row["Omschrijving"]);

          $netAmount = abs($row["Bedrag"]);
          $export->addField("Settlement Net Amount", $netAmount,"N");
          netCheck($netAmount, $airsNetAmountArray[$transId], $transId);
          $export->pushBuffer();
          break;
        default:
//          debug($row);
//          debug("unknown 1 poot: ".$gb);
          $errorArray[] = "transId: {$transId} grootboek (voor 1 poot) onbekend: ".$gb;

      }
    }
    else
    {

      $mainGb = $outputGb[$transId];
      switch ($mainGb)
      {
        case "FONDS-A":
        case "FONDS-V":

        case "FONDS-D":
        case "FONDS-L":
          $externalCost = 0;
          $commission   = 0;
          $interest     = 0;
          $tax          = 0;

          foreach ($dataset as $boeking)
          {

            $afwijkVal = false;
            if ($mainGb == "FONDS-A" OR $mainGb == "FONDS-V")
            {
              if (strtoupper(substr($boeking["Rekening"],-3)) == "MEM" OR strtoupper(substr($boeking["Rekening"],-3)) != "EUR")
              {
                $errorArray[] = "transId: {$transId}, A/V boeking op MEM/VV rekening {$boeking["Rekening"]}";
              }
            }
            else
            {
              if (strtoupper(substr($boeking["Rekening"],-3)) != "EUR" AND substr($boeking["Rekening"],-3) != "MEM")
              {
                $errorArray[] = "transId: {$transId}, D/L boeking op VV rekening {$boeking["Rekening"]}";
              }
            }

            if ($boeking["fondsValuta"] != "")
            {
              $afwijkVal = ($boeking["Valuta"] != $boeking["fondsValuta"]);
            }
            $bGb = $boeking["Grootboekrekening"];
            switch ($bGb)
            {
              case "FONDS":

                $amount = abs($boeking["Bedrag"]) * $boeking["Valutakoers"];
                $noOfUnits = abs($boeking["Bedrag"]);

                $bmap = $export->mapGrootBoek($mainGb, $boeking["Bedrag"]);

                $IRN = "AIRS";

//                $export->addField("Product Reference Name", "AIRS-{$bGb}-".count($dataset));
                $export->addField("Product Reference Name", "AIRS");
                $export->addField("Party Reference Value", $boeking["portefeuilleId"]);
                $export->addField("Counter Party Reference Value", "");
                $export->addField("No of Units", abs($boeking["Aantal"]),"N@4");
                $export->addField("Unit Price", $boeking["Fondskoers"],"N@8");
                $export->addField("Transaction Date", $boeking["Boekdatum"], "D");
                $export->addField("Settlement Date", $boeking["settlementDatum"], "D");
                $export->addField("Instrument Reference Name", $IRN);
                $export->addField("Instrument Reference Value", $boeking["fondsId"]);
                $export->addField("ISIN", $boeking["ISIN"]);
                $export->addField("Currency", $boeking["Valuta"]);
                $export->addField("Performance Impact", $bmap["pi"]);
                $export->addField("Type", $bmap["gb"]);
                $export->addField("Exchange Rate", $boeking["Valutakoers"],"N@8");
                $export->addField("Amount", abs($boeking["Aantal"] * $boeking["Fondskoers"]),"N");


                $export->addField("Comments", $boeking["Omschrijving"]);
                if ($mainGb != "FONDS-D" AND $mainGb != "FONDS-L")
                {
                  $export->addField("External Execution Id", $boeking["bankTransactieId"]);
                }
                break;

              case "STORT":
              case "ONTTR":
                // skippen bij D/L
                break;
              case "RENTE":
              case "BEH":
              case "KNBA":
              case "KOBU":
              case "ROER":
              case "VALK":
                $externalCost += $boeking["Bedrag"];
                break;
              case "KOST":
                $commission += $boeking["Bedrag"];
                break;
              case "RENME":
              case "RENOB":
                $interest += $boeking["Bedrag"];
                break;
              case "TOB":
                $tax += $boeking["Bedrag"];
                break;
              default:
                debug("unknown ".$bGb);
            }  // switch $item["Grootboekrekening"]

          } // foreach $dataset



          //$export->addField("Product Reference Name", "AIRS-{$bGb}-".count($dataset));
          if ($mainGb != "FONDS-D" AND $mainGb != "FONDS-L")
          {
            $export->addField("Settlement Commission", (-1 * $commission));
            $export->addField("Settlement External Costs", (-1 * $externalCost));
            $export->addField("Accrued interest", (-1 * $interest));
            $export->addField("Settlement Exchange Tax", (-1 * $tax));
          }

        $export->addField("Product Reference Name", "AIRS");
          if ($mainGb == "FONDS-A")
          {
            $netAmount  = $export->getValue("Amount") + (-1 * $commission) + (-1 * $externalCost) + (-1 * $interest) + (-1 * $tax);
          }
          else
          {
            $netAmount  = $export->getValue("Amount") + (-1 * $interest) - (-1 * $commission) - (-1 * $externalCost) - (-1 * $tax);
          }

          $export->addField("Settlement Net Amount", $netAmount,"N");
          netCheck($netAmount, $airsNetAmountArray[$transId], $transId);
  //          debug($export->lineBuffer, "AIRS-{$bGb}-".count($dataset));
          $export->pushBuffer();
          break;
        case "DIV":
          $divbe     = 0;
          $divBedrag = 0;
          foreach ($dataset as $boeking)
          {
            $afwijkVal = false;
            if (strtoupper(substr($boeking["Rekening"],-3)) == "MEM" OR strtoupper(substr($boeking["Rekening"],-3)) != "EUR")
            {
              $errorArray[] = "transId: {$transId}, boeking op MEM/VV rekening {$boeking["Rekening"]}";
            }
            if ($boeking["fondsValuta"] != "")
            {
              $afwijkVal = ($boeking["Valuta"] != $boeking["fondsValuta"]);
            }
            $bGb = $boeking["Grootboekrekening"];
            switch ($bGb)
            {
              case "DIV":
                $bmap = $export->mapGrootBoek($bGb, $boeking["Bedrag"]);

                if ($afwijkVal)
                {
                  $export->addField("Currency", "EUR");
                  $export->addField("Amount", $boeking["Bedrag"],"N");
                  $export->addField("Exchange Rate", 1,"N@8");

                }
                else
                {
                  $export->addField("Currency", $boeking["Valuta"]);
                  $export->addField("Amount", $boeking["Credit"]-$boeking["Debet"],"N");
                  $export->addField("Exchange Rate", $boeking["Valutakoers"],"N@8");

                }
                $divBedrag = $boeking["Bedrag"];

//                $export->addField("Product Reference Name", "AIRS-$bGb-".count($dataset));
                $export->addField("Product Reference Name", "AIRS");
                $export->addField("Party Reference Value", $boeking["portefeuilleId"]);
                $export->addField("Counter Party Reference Value", "");
                $export->addField("No of Units", abs($boeking["Credit"]-$boeking["Debet"]),"N@4");
                $export->addField("Unit Price", $boeking["Valutakoers"],"N@8");
                $export->addField("Transaction Date", $boeking["Boekdatum"], "D");
                $export->addField("Settlement Date", $boeking["settlementDatum"], "D");
                $export->addField("Instrument Reference Name", "AIRS");
                $export->addField("Instrument Reference Value", $boeking["fondsId"]);
                $export->addField("ISIN", $boeking["ISIN"]);
                $export->addField("Performance Impact", $bmap["pi"]);
                $export->addField("Type", $bmap["gb"]);
                $export->addField("External Execution Id", $boeking["bankTransactieId"]);
                $export->addField("Comments", $boeking["Omschrijving"]);

                break;
              case "DIVBE":
                if ($afwijkVal)
                {
                  $divbe += ($boeking["Bedrag"]);
                }
                else
                {
                  $divbe += ($boeking["Credit"] - $boeking["Debet"]);
                }
                break;
              default:
                $errorArray[] = "transId: {$transId} grootboek onbekend: ".$bGb;
            }  // switch $item["Grootboekrekening"]

          } // foreach $dataset

          $export->addField("Settlement Withholding Tax", (-1 * $divbe) );

          $netAmount = $divBedrag + $divbe;

          $export->addField("Settlement Net Amount", $netAmount,"N");
          netCheck($netAmount, $airsNetAmountArray[$transId], $transId);
          $export->pushBuffer();
          break;
        case "KRUIS":
          // bla
          break;

        default:
          $errorArray[] = "onbekend grootboek {$gb} voor meer poot transactieId $transId";
          break;

      }

      $gb1 = $dataset[0]["Grootboekrekening"];
      $gb2 = $dataset[1]["Grootboekrekening"];

    }

  }
//  debug($export->outputArray);
  if (count($errorArray) > 0)
  {
    echo "<h2>meldingen:</h2>";
    echo "<PRE>";
    print_r($errorArray);
    echo "</PRE>";
  }

  $export->makeCsv("Blanco");


}


function netCheck($netAmount, $airsNetAmount, $transId)
{
  global $errorArray;
  $export = round(abs($netAmount),2);
  $airs   = round(abs($airsNetAmount),2);

  //if ($export - $airs != 0)
  {
    $errorArray[] = "transId: {$transId}, AIRS={$airs} EXPORT={$export} DIFF= ".($export - $airs);
  }
}

function SetMutatiesVerwerkt($where)
{
  global $USR;
  $db = new DB();
  $query = "
  UPDATE
    (TijdelijkeRekeningmutaties)
  SET
    verwerkt = 1
  WHERE
    {$where}
  AND
    TijdelijkeRekeningmutaties.change_user = '{$USR}'
  ";
  $db->executeQuery($query);

  return $db;

}
