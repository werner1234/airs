<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/07/21 13:52:56 $
    File Versie         : $Revision: 1.3 $

    $Log: dashboard_transacties.php,v $
    Revision 1.3  2017/07/21 13:52:56  cvs
    call 5933

    Revision 1.2  2017/06/02 14:21:10  cvs
    no message

    Revision 1.1  2017/04/26 15:06:22  cvs
    call 5816



*/

include_once("init.php");

$tmpl = new AE_template();
$tmpl->templatePath = getcwd()."/classTemplates/";
$tmpl->appendSubdirToTemplatePath("dashboard");
$tmpl->loadTemplateFromFile("TRANS_dataRow.html","TRANS_datarow");

$fmt = new AE_cls_formatter(",",".");

$portefeuille = $_GET["port"];


//test($portefeuille, date("Y-m-d"));

$USR = $_SESSION["USR"];

$_TRANS = array();


getTRANSvalus();


?>
        <br/>
        <table class="extraInfoTable" style="width: 100%;">
          <tr class="trHeader">
            <td><?=vt('Datum');?></td>
            <td><?=vt('Type');?></td>
            <td class="ar"><?=vt('Aantal');?></td>
            <td><?=vt('Fonds');?></td>
            <td><?=vt('Aankoop');?> &euro;</td>
            <td><?=vt('Verkoop');?> &euro;</td>
          </tr>
<?

    foreach ($_TRANS as $item)
    {
      $item["datum"] = $fmt->format("@D{form}", $item["datum"]);
      $item["aankoopWaardeEur"] = $fmt->format("@N&euro; {.2B} ", $item["aankoopWaardeEur"]);
      $item["verkoopWaardeEur"] = $fmt->format("@N&euro; {.2B}", $item["verkoopWaardeEur"]);
      echo $tmpl->parseBlock("TRANS_datarow",$item);
    }
?>
        </table>
<?



function getTRANSvalus()
{
  global $_TRANS, $_GET;
  global $portefeuille;

  $rapportDatum = $_GET["stop"];
  $_TRANS = array();
  $db = new DB();
  $query = "
  SELECT 
    Fondsen.Omschrijving,
    Fondsen.Fondseenheid, 
    Fondsen.Fonds,
    Rekeningmutaties.Boekdatum, 
    Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		Rekeningmutaties.Afschriftnummer,
    Rekeningmutaties.omschrijving as rekeningOmschrijving,
		Rekeningmutaties.Aantal AS Aantal, 
		Rekeningmutaties.Fonds,  
    Rekeningmutaties.Fondskoers, 
    Rekeningmutaties.Debet as Debet, 
    Rekeningmutaties.Credit as Credit, 
    Rekeningmutaties.Valutakoers
  FROM 
    Rekeningmutaties, 
    Fondsen, 
    Rekeningen, 
    Portefeuilles, 
    Grootboekrekeningen 
  WHERE
    Rekeningmutaties.Rekening = Rekeningen.Rekening AND 
    Rekeningmutaties.Fonds = Fondsen.Fonds AND 
    Rekeningen.Portefeuille = '".$portefeuille."' AND 
    Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND 
    Rekeningmutaties.Verwerkt = '1' AND 
    Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND 
    (transactietype LIKE 'A%' OR transactietype LIKE 'V%') AND
    Grootboekrekeningen.FondsAanVerkoop = '1' AND 
    Rekeningmutaties.Boekdatum <= '".$rapportDatum."' 
  ORDER BY 
    Rekeningmutaties.Boekdatum DESC, 
    Rekeningmutaties.Fonds, 
    Rekeningmutaties.id
  LIMIT 10;  
  ";

  $db->executeQuery($query);
  while ($mutaties = $db->nextRecord())
  {

    $v = array();

    $v["datum"] = $mutaties["Boekdatum"];
    $v['transactietype'] = $mutaties["Transactietype"];
    $v['aantal'] = abs($mutaties["Aantal"]);
    $v['fonds'] = $mutaties["Fonds"];
    $v['fondsOmschrijving'] = $mutaties["Omschrijving"];
    $v['portefeuille'] = $portefeuille;

    $verkoop_waardeinValuta   = "";
    $t_aankoop_waardeinValuta = 0;

    switch($mutaties["Transactietype"])
    {

      case "A" :
      case "A/S" :
      case "A/O" :
        $t_aankoop_waarde = abs($mutaties["Debet"]) * $mutaties["Valutakoers"] * $mutaties['Rapportagekoers'];
        $t_aankoop_waardeinValuta = abs($mutaties["Debet"]);
        $t_aankoop_koers = $mutaties["Fondskoers"];

        if ($t_aankoop_waarde > 0)
        {
          $aankoop_koers = $t_aankoop_koers;
        }

        if ($t_aankoop_waardeinValuta > 0)
        {
          $aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
        }

        if ($t_aankoop_koers > 0)
        {
          $aankoop_waarde = $t_aankoop_waarde;
        }

        break;

      case "V" :
      case "V/O" :
      case "V/S" :
        $t_verkoop_waarde 				= abs($mutaties["Credit"]) * $mutaties["Valutakoers"] * $mutaties['Rapportagekoers'];
        $t_verkoop_waardeinValuta = abs($mutaties["Credit"]);
        $t_verkoop_koers					= $mutaties["Fondskoers"];

        if ($t_verkoop_koers > 0)
        {
          $verkoop_koers = $t_verkoop_koers;
        }

        if ($t_verkoop_waardeinValuta > 0)
        {
          $verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
        }

        if ($t_verkoop_waarde > 0)
        {
          $verkoop_waarde	= $t_verkoop_waarde;
        }
        break;

    }


    if ($t_aankoop_waardeinValuta <> 0)
    {
      $v['aankoopWaardeEur'] = $t_aankoop_waardeinValuta * $mutaties["Valutakoers"];
    }
    else
    {
      $v['verkoopWaardeEur'] = $verkoop_waardeinValuta * $mutaties["Valutakoers"];
    }

    $_TRANS[] = $v;


  }

}



















?>