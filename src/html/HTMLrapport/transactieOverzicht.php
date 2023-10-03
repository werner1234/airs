<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2016/09/02 12:31:34 $
    File Versie         : $Revision: 1.1 $

    $Log: transactieOverzicht.php,v $
    Revision 1.1  2016/09/02 12:31:34  cvs
    no message



*/

include_once 'init.php';

$db = new DB();
$templ = new AE_template();
$templ->templatePath = getcwd()."/classTemplates/";
$templ->appendSubdirToTemplatePath("transactieOverzicht");


$valutaKoersQuery = "/ (
    SELECT 
      Koers 
    FROM 
      Valutakoersen 
    WHERE 
      Valuta='{rappotageValuta}' AND 
      Datum <= Rekeningmutaties.Boekdatum 
    ORDER BY 
      Datum DESC 
    LIMIT 1 
    )";
$templ->loadTemplateFromFile("getTransacties.sql","getTransacties");



$query = $templ->parseBlock("getTransacties",array("fonds" => "ING", "portefeuille" => "057562"));
$db->executeQuery($query);
$header = true;
?>
<style>
  .header{
    background: beige;
  }
</style>
  <table border="1" cellpadding="5">


<?
while ($rec = $db->nextRecord())
{
  $rec["Boekdatum"] = substr($rec["Boekdatum"],0,10);
  if ($header)
  {
    $header = false;
    echo "<tr class='header'><td>";
    foreach($rec as $k=>$v)
    {
      echo "$k</td><td>";
    }
    echo "</td></tr>";

  }

  echo "<tr><td>".implode("</td><td>", $rec)."</td></tr>";
}

?>
  </table>

