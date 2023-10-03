<?php

/*
AE-ICT source module
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/05/19 15:50:56 $
File Versie					: $Revision: 1.9 $

$Log: bepaalActieveFondsenClass.php,v $
Revision 1.9  2018/05/19 15:50:56  rvv
*** empty log message ***



*/
class bepaalActieveFondsen
{

  var $verbose = false;

  function bepaalActieveFondsen()
  {
    global $USR, $__appvar;
  }

  function createTable()
  {
    $tables['ActieveFondsen'] = "CREATE TABLE `ActieveFondsen` (
  `id` int(11) NOT NULL auto_increment,
  `FondsImportCode` varchar(16) default NULL,
  `Fonds` varchar(25) NOT NULL default '',
  `ISINCode` varchar(26) NOT NULL default '',
  `Valuta` varchar(4) default NULL,
  `fondssoort` varchar(8) NOT NULL default '',
  `identifierVWD` varchar(20) NOT NULL default '',
  `identifierFactSet` varchar(20) NOT NULL default '',
  `koersmethodiek` tinyint(3) NOT NULL default '0',
  `Aantal` double NOT NULL default '0.00',
  `portefeuilleAantal` int(11) default '0',
  `Fondseenheid` double default NULL,
  `EindDatum` date NOT NULL default '0000-00-00',
  `Lossingsdatum` date NOT NULL default '0000-00-00',
  `OptieExpDatum` varchar(6) NOT NULL default '', 
  `KoersAltijdAanvragen` tinyint(3) NOT NULL default '0',
  `koersControleOverslaan` tinyint(3) NOT NULL default '0',
  `InPositie` tinyint(3) NOT NULL default '0',
  `Actief` tinyint(3) NOT NULL default '0',
  `laatsteKoers` double default NULL,
  `laatsteKoersDatum` date NOT NULL default '0000-00-00',
  `add_user` varchar(10) NOT NULL default '',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `Fonds` (`Fonds`)
)";


    $db = new DB();
    foreach ($tables as $table => $query)
    {
      if ($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
      {
        $db->SQL($query);
        $db->Query();
        if ($this->verbose)
          logScherm("$table ".vt("aangemaakt"));
      }
      else
      {
        $db->SQL("DROP TABLE `$table`");
        $db->Query();
        $db->SQL($query);
        $db->Query();
        $query="show variables like 'character_set_database'";
        $db->SQL($query);
        $charset=$db->LookupRecord();
        $charset=$charset['Value'];
        $query="ALTER TABLE `$table` CONVERT TO CHARACTER SET $charset";
        $db->SQL($query);
        $db->Query();
        if ($this->verbose)
          logScherm("$table ".vt("aangemaakt"));
      }
    }


  }

  function fillTable($portefeuilles=array())
  {
    global $USR;
    $db = new DB();
    if(count($portefeuilles)>0)
      $extraWhere=" AND Portefeuilles.Portefeuille IN('" . implode("','", $portefeuilles) . "') ";
    else
      $extraWhere='';

    $fondsen = array();
    $query="SELECT Fondsen.Fonds FROM Fondsen ORDER BY Fonds";
    $db->SQL($query);
    $db->Query();
    while ($data = $db->nextRecord())
    {
      if (!isset($fondsen[$data['Fonds']]))
      {
        $fondsen[$data['Fonds']] = array();
        $fondsen[$data['Fonds']]['Aantal'] = 0;
      }
    }
    
    $query = "SELECT
round(Sum(Rekeningmutaties.Aantal),4) AS Aantal,
Rekeningmutaties.Fonds,
Portefeuilles.Portefeuille,
Portefeuilles.Vermogensbeheerder
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
INNER JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.Einddatum > now()
WHERE Rekeningen.consolidatie=0 AND Portefeuilles.consolidatie=0 AND 
Rekeningmutaties.Boekdatum >= '" . date('Y') ."-01-01' AND Rekeningmutaties.Fonds <> '' $extraWhere 
GROUP BY Rekeningmutaties.Fonds,Portefeuilles.Portefeuille
HAVING Aantal <> 0 
ORDER BY Rekeningmutaties.Fonds,Portefeuilles.Portefeuille";
   // echo "Bepalen fondsaantallen.<br>\n";
    logScherm(vt("Bepalen fondsaantallen."));
    $db->SQL($query);
    $db->Query();

    $portefeuilles = array();
    $this->vermogensbeheerders = array();
    while ($data = $db->nextRecord())
    {
      $this->vermogensbeheerders[$data['Vermogensbeheerder']] = $data['Vermogensbeheerder'];
      if (!isset($fondsen[$data['Fonds']]))
      {
        $fondsen[$data['Fonds']] = array();
        $fondsen[$data['Fonds']]['Aantal'] = 0;
        $fondsen[$data['Fonds']]['portefeuilleAantal'] = 0;
      }

      $fondsen[$data['Fonds']]['Aantal'] += $data['Aantal'];
      $fondsen[$data['Fonds']]['InPositie'] = 1;
      $fondsen[$data['Fonds']]['portefeuilleAantal']++;


      if (!isset($portefeuilles[$data['Fonds']][$data['Vermogensbeheerder']]))
        $portefeuilles[$data['Fonds']][$data['Vermogensbeheerder']] = 1;
      else
        $portefeuilles[$data['Fonds']][$data['Vermogensbeheerder']]++;
    }

    $query = "SELECT Fonds FROM Fondsen WHERE KoersAltijdAanvragen=1";
    if ($this->verbose)
      logScherm(vt("Ophalen KoersAltijdAanvragen Fondsen."));
    $db->SQL($query);
    $db->Query();
    while ($data = $db->nextRecord())
    {
      if (!isset($fondsen[$data['Fonds']]))
      {
        $fondsen[$data['Fonds']] = array();
        $fondsen[$data['Fonds']]['Aantal'] = 0;
        $fondsen[$data['Fonds']]['portefeuilleAantal'] = 0;
      }
    }

    $query = "SELECT Indices.Beursindex FROM Indices GROUP BY Indices.Beursindex ORDER BY Indices.Beursindex";
    if ($this->verbose)
      logScherm(vt("Ophalen Indices."));
    $db->SQL($query);
    $db->Query();
    while ($data = $db->nextRecord())
    {
      if (!isset($fondsen[$data['Beursindex']]))
      {
        $fondsen[$data['Beursindex']] = array();
        $fondsen[$data['Beursindex']]['Aantal'] = 0;
        $fondsen[$data['Beursindex']]['portefeuilleAantal'] = 0;
      }
    }

    $fondsGegevens = array();
    $now=time();
    foreach ($fondsen as $fonds => $fondsData)
    {
      $query = "SELECT Fonds,FondsImportCode,ISINCode,identifierVWD,identifierFactSet,
   koersmethodiek,Fondseenheid,Einddatum,Lossingsdatum,OptieExpDatum,Valuta,KoersAltijdAanvragen,fondssoort,koersControle
   FROM Fondsen WHERE Fonds='" . mysql_real_escape_string($fonds) ."'";
   //     "' AND (Einddatum > now() OR Einddatum < '1980-01-01')";
      $db->SQL($query);
      $fondsInfo = $db->lookupRecord();
      if (isset($fondsInfo['Fonds']))
      {
        $fondsInfo['Aantal'] = $fondsData['Aantal'];
        $fondsInfo['InPositie'] = $fondsData['InPositie'];
        $fondsInfo['portefeuilleAantal'] = $fondsData['portefeuilleAantal'];
        if($fondsInfo['Einddatum']=='0000-00-00' || db2jul($fondsInfo['Einddatum']) > $now )
          $fondsInfo['Actief'] = 1;
        else
          $fondsInfo['Actief'] = 0;

        $query="SELECT Koers,Datum FROM Fondskoersen WHERE Fonds='" . mysql_real_escape_string($fonds) ."' ORDER BY datum desc limit 1";
        $db->SQL($query);
        $koersInfo = $db->lookupRecord();
        $fondsInfo['laatsteKoers']=$koersInfo['Koers'];
        $fondsInfo['laatsteKoersDatum']=$koersInfo['Datum'];

        $fondsGegevens[$fondsInfo['Fonds']] = $fondsInfo;
        if ($this->verbose)
          echo "<br>\n".vt("Gegevens voor")." $fonds ".vt("opgehaald").".";
      }
      else
      {
        if ($this->verbose)
          echo "<br>\n".vt("Gegevens voor")." $fonds ".vt("niet opgehaald").".";
      }
    }

    $vertaling['koersControle'] = 'koersControleOverslaan';
    $n = 0;
    foreach ($fondsGegevens as $fondsData)
    {
      $insert = '';
      foreach ($fondsData as $key => $value)
      {
        if (isset($vertaling[$key]))
          $key = $vertaling[$key];
        $insert .= "`$key`='" . mysql_real_escape_string($value) . "',";
      }
      $insert .= "change_user='$USR',add_user='$USR',add_date=now(),change_date=now()";
      $query = "INSERT INTO ActieveFondsen SET $insert";
      $db->SQL($query);
      if (!$db->Query())
      {
        if ($this->verbose)
          logScherm("$query ".vt("Mislukt").".");
      }
      else
        $n++;
    }
    if ($this->verbose)
      logScherm("$n ".vt("records aangemaakt").".");


    $this->portefeuilles=$portefeuilles;
    $this->fondsGegevens=$fondsGegevens;
      
  }


  function createXls($onlyPrepare=false)
  { 
    global $__appvar;
    $db=new DB();
    $query = "SELECT Vermogensbeheerder FROM Vermogensbeheerders WHERE Vermogensbeheerder IN('" .
      implode("','", $this->vermogensbeheerders) . "') ORDER BY Vermogensbeheerder";
    $db->SQL($query);
    $db->Query();
    $vermogensbeheerders = array();
    while ($data = $db->nextRecord())
    {
      $vermogensbeheerders[$data['Vermogensbeheerder']] = $data['Vermogensbeheerder'];
    }

    include_once ('../classes/AE_cls_xls.php');
    if($onlyPrepare==false)
      $xls = new AE_xls();
    $xlsData = array();
    $tmp = array('Importcode', 'Fonds');
    $html .= "<table><tr><td>".vt("Importcode")."</td><td>".vt("Fonds")."</td>";
    foreach ($vermogensbeheerders as $vermogensbeheerder)
    {
      $html .= "<td>$vermogensbeheerder</td>";
      $tmp[] = $vermogensbeheerder;
    }
    $html .= "</tr>\n";
    $xlsData[] = $tmp;

    foreach ($this->portefeuilles as $fonds => $aantallenPerVermogensbeheerder)
    {
      $tmp = array($this->fondsGegevens[$fonds]['FondsImportCode'], $fonds);
      $html .= "<tr><td>" . $this->fondsGegevens[$fonds]['FondsImportCode'] . "</td><td>$fonds</td>";
      foreach ($vermogensbeheerders as $vermogensbeheerder)
      {
        $html .= "<td>" . $aantallenPerVermogensbeheerder[$vermogensbeheerder] . "</td>";
        $tmp[] = $aantallenPerVermogensbeheerder[$vermogensbeheerder];
      }
      "</tr>\n";
      $xlsData[] = $tmp;
    }
    $html .= "</table>\n";

    if($onlyPrepare==false)
    {
      $xls->setData($xlsData);
      $xls->OutputXls($__appvar['tempdir'] . '/fondsenPerVermogensbeheerder.xls', true);
      echo '<br><a href="showTempfile.php?show=1&filename=fondsenPerVermogensbeheerder.xls">".vt("XLS uitvoer")."</a> ".vt("fondsenPerVermogensbeheerder.xls")."<br><br>';
    }
    else
    {
      return $xlsData;
    }

  }


}

