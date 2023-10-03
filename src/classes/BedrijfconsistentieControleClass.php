<?php

include_once('indexBerekening.php');
Class BedrijfConsistentieControle
{
  var $bedrijf;
  var $checks;

  function BedrijfConsistentieControle($bedrijf,$updateTimeStamp='')
  {
	  $this->bedrijf = $bedrijf;
	  $this->timeStamp = $updateTimeStamp;
	  $this->DB = new DB();
	  $this->DB2 = new DB();
 	  $this->checks = array();
  	$this->vermogensbeheerders = array();
  	$this->vermogensbeheerderWhere = '';
    $this->block=false;

 	  $query = "SELECT
              Vermogensbeheerders.Vermogensbeheerder
              FROM
              VermogensbeheerdersPerBedrijf,
              Vermogensbeheerders
              WHERE
              VermogensbeheerdersPerBedrijf.Bedrijf = '".$this->bedrijf."' AND
              Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder";
	  $this->DB->SQL($query);
    $this->DB->Query();

	  while($data = $this->DB->nextRecord())
	  {
	     $this->vermogensbeheerders[] =  $data['Vermogensbeheerder'];
	  }

	  if (count($this->vermogensbeheerders) < 1)
	  {
	    echo "<br> ".vt("Geen vermogensbeheerders gekoppeld onder bedrijf")." $bedrijf. <br>".vt("Controle afgebroken").".";
	    exit;
	  }

  }

  function getBedrijf()
  {
    return $this->bedrijf;
  }

  function getChecks()
  {

    $query = "SELECT
    check_rekeningmutaties,
    check_categorie,
    check_sector,
    check_zorgplichtFonds,
    check_zorgplichtPortefeuille,
    check_hoofdcategorie,
    check_hoofdsector,
    check_sectorRegio,
    check_sectorAttributie,
    check_historischePortefeuilleIndex,
    check_kruisposten,
    check_afmCategorie,
    check_rekeningATT,
    check_rekeningCat,
    check_rekeningDepotbank,
    check_Beurs,
    check_BB_Landcodes,
    check_duurzaamheid,
    check_duurzaamCategorie,
    Vermogensbeheerders.Vermogensbeheerder
    FROM VermogensbeheerdersPerBedrijf ,Vermogensbeheerders
    WHERE
    VermogensbeheerdersPerBedrijf.Bedrijf = '$this->bedrijf' AND
    Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder ";

    	 $this->DB->SQL($query);
       $this->DB->Query();
	     while($data = $this->DB->nextRecord())
	     {
	      while (list($check, $waarde) = each($data))
	      {
	        if ($waarde == '1')
	        {
	          $this->checks[$data['Vermogensbeheerder']][$check] = $waarde;
	        }

	      }
	     }
 //   print_r($this->checks);


	  return $this->checks;
  }
  function setChecks($data)
  {

    foreach ($this->vermogensbeheerders as $vermogensbeheerder)
    {
      foreach($data as $key=>$value)
        if($value >0)
          $this->checks[$vermogensbeheerder][$key] = 1;
    }
    return $this->checks;
  }
  function showChecks()
  {
    return $this->checks;
  }

  function checkRekeningMutaties()
  {
      $return = true;
	    $query = "SELECT Portefeuilles.Vermogensbeheerder, ".
	    "Rekeningen.Portefeuille, ".
	    "Rekeningmutaties.Afschriftnummer ".
	    "FROM Rekeningafschriften, Rekeningmutaties, Rekeningen, Portefeuilles, VermogensbeheerdersPerBedrijf, Vermogensbeheerders    ".
	    "WHERE Rekeningen.consolidatie=0 AND Portefeuilles.consolidatie=0 AND ".
	    "VermogensbeheerdersPerBedrijf.Bedrijf = '".$this->bedrijf."' AND
	     Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder AND ".
	    "VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND ".
	    "Portefeuilles.Portefeuille = Rekeningen.Portefeuille AND ".
	    "Rekeningen.Rekening = Rekeningmutaties.Rekening AND ".
	    "(Rekeningmutaties.Verwerkt = 0 ) GROUP BY Rekeningmutaties.Afschriftnummer ";

  	   echo "<br><b> ".vt("rekeningmutaties controle").".</b> ";
	     $this->DB->SQL($query);
       $this->DB->Query();
	     while($data = $this->DB->nextRecord())
	     {
		    echo "<br>".vt("Fout").": ".vt("Rekening")." ".$data['Rekening']." , ".vt("afschrift")." ".$data['Afschriftnummer']." ".
             vt("bij portefeuille")." ".$data['Portefeuille']." ".vt("is niet verwerkt").".";
		    $return = false;
	     }
	     echo "<br><b> ".vt("rekeningmutaties controle klaar").".</b> ";

    return $return;
  }


  function updateFondsenPerBedrijf()
  {
	global $USR;

	if ($this->bedrijf == '')
  {
    return false;
  }

	$Bedrijf = $this->bedrijf;
	$table="FondsenPerBedrijf";

	$this->DB->SQL("DELETE FROM ".$table." WHERE Bedrijf = '".$Bedrijf."' ");
	$this->DB->Query();

  $query="SELECT Rekeningmutaties.Fonds
FROM
Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
JOIN VermogensbeheerdersPerBedrijf ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND VermogensbeheerdersPerBedrijf.Bedrijf = '".$Bedrijf."'
INNER JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE Rekeningen.consolidatie=0 AND Portefeuilles.consolidatie=0 AND Rekeningmutaties.Fonds <> ''
GROUP BY Rekeningmutaties.Fonds";
	$this->DB->SQL($query);
	$this->DB->Query();

	while($data = $this->DB->NextRecord())
	{
		$query = "INSERT INTO ".$table." SET ".
			"  Bedrijf = '".$Bedrijf."' ".
			", Fonds = '".mysql_escape_string($data['Fonds'])."' ".
			", add_date = NOW() ".
			", add_user = '".$USR."' ".
			", change_date = NOW() ".
			", change_user = '".$USR."' ";

		$this->DB2->SQL($query);
		$this->DB2->Query();
	}

	// Doe ook de indices erbij!
		$query = "SELECT * FROM (
(SELECT Indices.Beursindex as Fonds 
	FROM Indices 
JOIN VermogensbeheerdersPerBedrijf ON Indices.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder 
	WHERE VermogensbeheerdersPerBedrijf.Bedrijf = '".$Bedrijf."' ) 
	UNION
	(SELECT
IndexPerBeleggingscategorie.Fonds
FROM
IndexPerBeleggingscategorie
INNER JOIN VermogensbeheerdersPerBedrijf ON IndexPerBeleggingscategorie.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
AND VermogensbeheerdersPerBedrijf.Bedrijf='".$Bedrijf."' GROUP BY Fonds)
)  as indextabel GROUP BY Fonds";
	$this->DB->SQL($query);
	$this->DB->Query();


	while($data = $this->DB->NextRecord())
	{
		$query = "INSERT INTO ".$table." SET ".
			"  Bedrijf = '".$Bedrijf."' ".
			", Fonds = '".mysql_escape_string($data['Fonds'])."' ".
			", add_date = NOW() ".
			", add_user = '".$USR."' ".
			", change_date = NOW() ".
			", change_user = '".$USR."' ";

		$this->DB2->SQL($query);
		$this->DB2->Query();
	}

	//Voeg ook Portefeuille Specifieke Index toe
	$query = "SELECT DISTINCT Portefeuilles.SpecifiekeIndex as Fonds 
FROM Portefeuilles 
JOIN VermogensbeheerdersPerBedrijf ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder AND VermogensbeheerdersPerBedrijf.Bedrijf = '".$Bedrijf."' 
JOIN Fondsen ON Fondsen.Fonds=Portefeuilles.SpecifiekeIndex 
WHERE 
Portefeuilles.SpecifiekeIndex <> '' ";

	$this->DB->SQL($query);
	$this->DB->Query();

	while($data = $this->DB->NextRecord())
	{
		$query = "INSERT INTO ".$table." SET ".
			"  Bedrijf = '".$Bedrijf."' ".
			", Fonds = '".mysql_escape_string($data['Fonds'])."' ".
			", add_date = NOW() ".
			", add_user = '".$USR."' ".
			", change_date = NOW() ".
			", change_user = '".$USR."' ";

		$this->DB2->SQL($query);
		$this->DB2->Query();
	}

		// Voeg ook de nog missende Fondsen toe die aan een beleggingscategorie zijn gekoppeld.
		$query="SELECT BeleggingscategoriePerFonds.Fonds,VermogensbeheerdersPerBedrijf.Bedrijf
          FROM
          BeleggingscategoriePerFonds
          Join VermogensbeheerdersPerBedrijf ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder
          LEFT Join $table ON VermogensbeheerdersPerBedrijf.Bedrijf = $table.Bedrijf AND BeleggingscategoriePerFonds.Fonds = $table.Fonds
          JOIN Fondsen ON Fondsen.Fonds=BeleggingscategoriePerFonds.Fonds 
          WHERE VermogensbeheerdersPerBedrijf.Bedrijf = '".$Bedrijf."' AND $table.Fonds IS NULL";
		$this->DB->SQL($query);
		$this->DB->Query();
		while($data = $this->DB->NextRecord())
		{
			$query = "SELECT id FROM $table WHERE  Fonds = '".mysql_escape_string($data['Fonds'])."' AND  Bedrijf = '".$Bedrijf."'";
			if($this->DB2->QRecords($query) < 1)
			{
				$query = "INSERT INTO ".$table." SET Bedrijf = '".$Bedrijf."', Fonds = '".mysql_escape_string($data['Fonds'])."', add_date = NOW() , add_user = '".$USR."', change_date = NOW() , change_user = '".$USR."' ";
				$this->DB2->SQL($query);
				$this->DB2->Query();
			}
		}

		$query="SELECT bench.Fonds FROM ( SELECT benchmarkverdeling.fonds,$table.Bedrijf,benchmarkverdeling.benchmark FROM benchmarkverdeling
INNER JOIN $table ON benchmarkverdeling.benchmark = $table.Fonds AND $table.Bedrijf='$Bedrijf') bench 
left JOIN $table ON bench.fonds=$table.fonds AND $table.Bedrijf='$Bedrijf' WHERE $table.fonds  is null GROUP BY bench.fonds";
		$this->DB->SQL($query);
		$this->DB->Query();
		while($data = $this->DB->NextRecord())
		{
			$query = "SELECT id FROM $table WHERE  Fonds = '".mysql_escape_string($data['Fonds'])."' AND  Bedrijf = '".$Bedrijf."'";
			if($this->DB2->QRecords($query) < 1)
			{
				$query = "INSERT INTO ".$table." SET  Bedrijf = '".$Bedrijf."', Fonds = '".mysql_escape_string($data['Fonds'])."' , add_date = NOW() , add_user = '".$USR."' , change_date = NOW() , change_user = '".$USR."' ";
				$this->DB2->SQL($query);
				$this->DB2->Query();
			}
		}

		$query="SELECT OptieFondsen.OptieFonds FROM  
(SELECT Fondsen.OptieBovenliggendFonds as OptieFonds,$table.Fonds as optie FROM $table 
JOIN Fondsen ON $table.Fonds=Fondsen.Fonds AND $table.Bedrijf='".$Bedrijf."' GROUP BY Fondsen.OptieBovenliggendFonds ) OptieFondsen
LEFT JOIN $table ON OptieFondsen.OptieFonds=$table.Fonds AND $table.Bedrijf='".$Bedrijf."'
WHERE $table.Fonds is null AND OptieFondsen.OptieFonds <> ''";
		$this->DB->SQL($query);
		$this->DB->Query();
		while($data = $this->DB->NextRecord())
		{
			$query = "SELECT id FROM $table WHERE  Fonds = '".mysql_escape_string($data['OptieFonds'])."' AND  Bedrijf = '".$Bedrijf."'";
			if($this->DB2->QRecords($query) < 1)
			{
				$query = "INSERT INTO ".$table." SET  Bedrijf = '".$Bedrijf."', Fonds = '".mysql_escape_string($data['OptieFonds'])."' , add_date = NOW() , add_user = '".$USR."' , change_date = NOW() , change_user = '".$USR."' ";
				$this->DB2->SQL($query);
				$this->DB2->Query();
			}
		}
  return true;
}

  function updateFondsenPerVermogensbeheerder($Vermogensbeheerder)
  {

	global $USR;

	if ($this->bedrijf == '' || $Vermogensbeheerder == '')
	  return false;

	$table="FondsenPerVermogensbeheerder";

	$this->DB->SQL("DELETE FROM ".$table." WHERE Vermogensbeheerder = '".$Vermogensbeheerder."' ");
	$this->DB->Query();

  $query="SELECT Rekeningmutaties.Fonds
FROM
Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.Vermogensbeheerder = '".$Vermogensbeheerder."'
INNER JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE Rekeningen.consolidatie=0 AND Portefeuilles.consolidatie=0 AND Rekeningmutaties.Fonds <> ''
GROUP BY Rekeningmutaties.Fonds";

	$this->DB->SQL($query);
	$this->DB->Query();

	while($data = $this->DB->NextRecord())
	{
		$query = "INSERT INTO ".$table." SET ".
			"  Vermogensbeheerder = '".$Vermogensbeheerder."' ".
			", Fonds = '".mysql_escape_string($data['Fonds'])."' ".
			", add_date = NOW() ".
			", add_user = '".$USR."' ".
			", change_date = NOW() ".
			", change_user = '".$USR."' ";

		$this->DB2->SQL($query);
		$this->DB2->Query();
	}

	// Doe ook de indices erbij!
	$query = "SELECT * FROM (
(SELECT Indices.Beursindex as Fonds 
	FROM Indices WHERE  Indices.Vermogensbeheerder = '".$Vermogensbeheerder."') 
	UNION
	(SELECT
IndexPerBeleggingscategorie.fonds as Fonds
FROM
IndexPerBeleggingscategorie
WHERE IndexPerBeleggingscategorie.Vermogensbeheerder = '".$Vermogensbeheerder."' GROUP BY Fonds)
)  as indextabel GROUP BY Fonds";

	$this->DB->SQL($query);
	$this->DB->Query();


	while($data = $this->DB->NextRecord())
	{
		$query = "INSERT INTO ".$table." SET ".
			"  Vermogensbeheerder = '".$Vermogensbeheerder."' ".
			", Fonds = '".mysql_escape_string($data['Fonds'])."' ".
			", add_date = NOW() ".
			", add_user = '".$USR."' ".
			", change_date = NOW() ".
			", change_user = '".$USR."' ";

		$this->DB2->SQL($query);
		$this->DB2->Query();
	}

	//Voeg ook Portefeuille Specifieke Index toe
	$query = "SELECT DISTINCT Portefeuilles.SpecifiekeIndex as Fonds 
  FROM Portefeuilles 
  JOIN Fondsen ON Portefeuilles.SpecifiekeIndex = Fondsen.Fonds
  WHERE Portefeuilles.Vermogensbeheerder = '".$Vermogensbeheerder."' AND Portefeuilles.SpecifiekeIndex <> '' ";

	$this->DB->SQL($query);
	$this->DB->Query();

	while($data = $this->DB->NextRecord())
	{
		$query = "INSERT INTO ".$table." SET ".
			"  Vermogensbeheerder = '".$Vermogensbeheerder."' ".
			", Fonds = '".mysql_escape_string($data['Fonds'])."' ".
			", add_date = NOW() ".
			", add_user = '".$USR."' ".
			", change_date = NOW() ".
			", change_user = '".$USR."' ";

		$this->DB2->SQL($query);
		$this->DB2->Query();
	}
  
	// Voeg ook de nog missende Fondsen toe die aan een beleggingscategorie zijn gekoppeld.
	$query="SELECT
BeleggingscategoriePerFonds.Fonds
FROM
BeleggingscategoriePerFonds
INNER JOIN Fondsen ON BeleggingscategoriePerFonds.Fonds = Fondsen.Fonds
LEFT JOIN FondsenPerVermogensbeheerder ON BeleggingscategoriePerFonds.Fonds = FondsenPerVermogensbeheerder.Fonds AND FondsenPerVermogensbeheerder.Vermogensbeheerder ='$Vermogensbeheerder'
WHERE BeleggingscategoriePerFonds.Vermogensbeheerder ='$Vermogensbeheerder' AND FondsenPerVermogensbeheerder.Fonds is NULL 
AND (Fondsen.EindDatum ='0000-00-00' OR Fondsen.EindDatum > now())";
	$this->DB->SQL($query); 
	$this->DB->Query();
  //$tmp=fopen('fondslistnew.txt','a');
	while($data = $this->DB->NextRecord())
	{
	  $query = "SELECT id FROM $table WHERE  Fonds = '".mysql_escape_string($data['Fonds'])."' AND  Vermogensbeheerder = '".$Vermogensbeheerder."'";
	  if($this->DB2->QRecords($query) < 1)
	  {
	  
     //fwrite($tmp,"$Vermogensbeheerder|".$data['Fonds']."\r\n");
		  $query = "INSERT INTO ".$table." SET ".
			  "  Vermogensbeheerder = '".$Vermogensbeheerder."' ".
			  ", Fonds = '".mysql_escape_string($data['Fonds'])."' ".
		  	", add_date = NOW() ".
		  	", add_user = '".$USR."' ".
		  	", change_date = NOW() ".
		  	", change_user = '".$USR."' ";

		  $this->DB2->SQL($query);
		  $this->DB2->Query();
	  }
	}
  
  $query="SELECT bench.Fonds FROM
(
SELECT
benchmarkverdeling.fonds,
benchmarkverdeling.benchmark
FROM
benchmarkverdeling
INNER JOIN $table ON benchmarkverdeling.benchmark = $table.Fonds
AND $table.Vermogensbeheerder ='$Vermogensbeheerder'

) bench 
left JOIN $table ON bench.fonds=$table.fonds AND $table.Vermogensbeheerder ='$Vermogensbeheerder'
WHERE $table.fonds  is null
GROUP BY bench.fonds";
	$this->DB->SQL($query); 
	$this->DB->Query();
	while($data = $this->DB->NextRecord())
	{
	  $query = "SELECT id FROM $table WHERE  Fonds = '".mysql_escape_string($data['Fonds'])."' AND Vermogensbeheerder = '".$Vermogensbeheerder."'";
	  if($this->DB2->QRecords($query) < 1)
	  {
		  $query = "INSERT INTO ".$table." SET ".
			  "  Vermogensbeheerder = '".$Vermogensbeheerder."' ".
			  ", Fonds = '".mysql_escape_string($data['Fonds'])."' ".
		  	", add_date = NOW() ".
		  	", add_user = '".$USR."' ".
		  	", change_date = NOW() ".
		  	", change_user = '".$USR."' ";

		  $this->DB2->SQL($query);
		  $this->DB2->Query();
	  }
	}

		$query="SELECT OptieFondsen.OptieFonds FROM  
(SELECT Fondsen.OptieBovenliggendFonds as OptieFonds,$table.Fonds as optie FROM $table 
JOIN Fondsen ON $table.Fonds=Fondsen.Fonds AND $table.Vermogensbeheerder='".$Vermogensbeheerder."' GROUP BY Fondsen.OptieBovenliggendFonds ) OptieFondsen
LEFT JOIN $table ON OptieFondsen.OptieFonds=$table.Fonds AND $table.Vermogensbeheerder='".$Vermogensbeheerder."'
WHERE $table.Fonds is null AND OptieFondsen.OptieFonds <> ''";
		$this->DB->SQL($query);
		$this->DB->Query();
		while($data = $this->DB->NextRecord())
		{
			$query = "SELECT id FROM $table WHERE  Fonds = '".mysql_escape_string($data['OptieFonds'])."' AND  Vermogensbeheerder = '".$Vermogensbeheerder."'";
			if($this->DB2->QRecords($query) < 1)
			{
				$query = "INSERT INTO ".$table." SET Vermogensbeheerder = '".$Vermogensbeheerder."', Fonds = '".mysql_escape_string($data['OptieFonds'])."' , add_date = NOW() , add_user = '".$USR."' , change_date = NOW() , change_user = '".$USR."' ";
				$this->DB2->SQL($query);
				$this->DB2->Query();
			}
		}

  return true;
}


function doChecks()
{
  $totaalResultaat = true;
  if (count($this->checks > 0))
  {
   // print_r($this->checks);

    foreach($this->checks as $vermogensbeheerder=>$checks)
    {

      if ($this->updateFondsenPerVermogensbeheerder($vermogensbeheerder) == false)
      {
        echo '<br>'.vt("Update tabel FondsenPerVermogensbeheerder").'('.$vermogensbeheerder.') '.vt("mislukt. Controle afgebroken");
        exit();
      }
      echo '<br>'.vt("Update tabel FondsenPerVermogensbeheerder").'('.$vermogensbeheerder.') '.vt("voltooid. Begin overige controles").'.';
      flush();

      foreach($checks as $check=>$waarde)
      {

        if ($waarde > 0 && $check != 'check_rekeningmutaties')
        {
          echo "<br><b> $check ".vt("gestart voor")." $vermogensbeheerder.</b>";
          if(method_exists($this,$check))
             $return = $this->$check($vermogensbeheerder);
          else
             $return = false;
          $this->logExport("$check klaar voor $vermogensbeheerder");
          if ($return == false)
          {
             echo "<br> $check ".vt("niet succesvol afgesloten");
             $totaalResultaat = false;
          }
          flush();
        }
      }
      echo '<br><br>-----------------------------------------<br><br>';
    }
    return $totaalResultaat;
  }
  else
  {
    echo "<br><b> ".vt("Geen vermogensbeheerders om te controleren?").".</b> ";
    return false;
  }
}
  
  function logExport($txt)
  {
    global $exportStart,$exportLogLaatste;
    if(!isset($_GET['Bedrijf']))
      return;
    if($exportStart==0)
    {
      $exportStart = time();
      $exportLogLaatste = $exportStart;
    }
    $nu=time();
    logIt($_GET['Bedrijf']." | ".$_GET['updateSoort']." | ". $_GET['tabel']." | ".($nu-$exportLogLaatste)."s | ".($nu-$exportStart)."s | $txt ",1);
    $exportLogLaatste =$nu;
  }


function check_categorie($vermogensbeheerder)
{
  $return = true;

	$query = "SELECT BeleggingscategoriePerFonds.Beleggingscategorie, 
		 FondsenPerVermogensbeheerder.Vermogensbeheerder, 
		 FondsenPerVermogensbeheerder.Fonds 
		 FROM FondsenPerVermogensbeheerder 
		 LEFT JOIN BeleggingscategoriePerFonds ON FondsenPerVermogensbeheerder.Fonds = BeleggingscategoriePerFonds.Fonds AND 
		 FondsenPerVermogensbeheerder.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder 
		 WHERE 
		 FondsenPerVermogensbeheerder.Vermogensbeheerder = '".$vermogensbeheerder."' AND 
		 (BeleggingscategoriePerFonds.Beleggingscategorie IS NULL OR BeleggingscategoriePerFonds.Beleggingscategorie='')
     GROUP BY FondsenPerVermogensbeheerder.Fonds";
	$this->DB->SQL($query);
	$this->DB->Query();
    
	while($data = $this->DB->nextRecord())
	{
		echo "<br>Fout: Fonds '".$data['Fonds']."' heeft geen beleggingscategorie (BeleggingscategoriePerFonds)'".$data['Beleggingscategorie']."'.";
    $this->checkData[$data['Fonds']][$vermogensbeheerder]['BeleggingscategoriePerFonds']['Beleggingscategorie']=1;
    $this->block=true;
		$return = false;
	}

  $query =" SELECT
  FondsenPerVermogensbeheerder.Fonds,
  BeleggingscategoriePerFonds.Beleggingscategorie,
  BeleggingscategoriePerFonds.Vermogensbeheerder
  FROM
  (FondsenPerVermogensbeheerder,BeleggingscategoriePerFonds)
  LEFT JOIN  Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
  WHERE
  BeleggingscategoriePerFonds.Fonds = FondsenPerVermogensbeheerder.Fonds AND
  BeleggingscategoriePerFonds.Vermogensbeheerder = '".$vermogensbeheerder."' AND
  FondsenPerVermogensbeheerder.Vermogensbeheerder = '".$vermogensbeheerder."' AND
  Beleggingscategorien.Beleggingscategorie is null 
  GROUP BY FondsenPerVermogensbeheerder.Fonds";
	$this->DB->SQL($query);
	$this->DB->Query();
    
	while($data = $this->DB->nextRecord())
	{
		echo "<br>".vt("Fout").": ".vt("Fonds")." '".$data['Fonds']."' ".vt("heeft niet bestaande beleggingscategorie")." '".$data['Beleggingscategorie']."'.";
    $this->checkData[$data['Fonds']][$vermogensbeheerder]['BeleggingscategoriePerFonds']['Beleggingscategorie']=1;
		$return = false;
	}

	echo "<br><b>categorie controle voor $vermogensbeheerder klaar.</b> ";

  return $return;
}


function check_duurzaamheid($vermogensbeheerder)
{
  $return = true;

	$query = "SELECT BeleggingscategoriePerFonds.Beleggingscategorie, 
		 FondsenPerVermogensbeheerder.Vermogensbeheerder, 
		 FondsenPerVermogensbeheerder.Fonds 
		 FROM FondsenPerVermogensbeheerder 
		 LEFT JOIN BeleggingscategoriePerFonds ON FondsenPerVermogensbeheerder.Fonds = BeleggingscategoriePerFonds.Fonds AND 
		 FondsenPerVermogensbeheerder.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder 
		 WHERE 
		 FondsenPerVermogensbeheerder.Vermogensbeheerder = '".$vermogensbeheerder."' AND 
		 (BeleggingscategoriePerFonds.duurzaamheid IS NULL OR BeleggingscategoriePerFonds.duurzaamheid='')
     GROUP BY FondsenPerVermogensbeheerder.Fonds";
	$this->DB->SQL($query);
	$this->DB->Query();
    
	while($data = $this->DB->nextRecord())
	{
		echo "<br>".vt("Fout").": ".vt("Fonds")." '".$data['Fonds']."' ".vt("heeft geen duurzaamheid of een duurzaamheidscore van 0 (BeleggingscategoriePerFonds)")."'".
         $data['duurzaamheid']."'.";
    $this->checkData[$data['Fonds']][$vermogensbeheerder]['BeleggingscategoriePerFonds']['duurzaamheid']=1;
		$return = false;
	}

	echo "<br><b>".vt("categorie controle voor")." $vermogensbeheerder ".vt("klaar").".</b> ";

  return $return;
}

function check_afmCategorie($vermogensbeheerder)
{
  $return = true;
  $query="SELECT FondsenPerVermogensbeheerder.Fonds, BeleggingscategoriePerFonds.afmCategorie, BeleggingscategoriePerFonds.Vermogensbeheerder
   FROM (FondsenPerVermogensbeheerder,BeleggingscategoriePerFonds)
   LEFT JOIN afmCategorien ON BeleggingscategoriePerFonds.afmCategorie = afmCategorien.afmCategorie
   WHERE
   BeleggingscategoriePerFonds.Fonds = FondsenPerVermogensbeheerder.Fonds AND
   BeleggingscategoriePerFonds.Vermogensbeheerder = '".$vermogensbeheerder."' AND
   FondsenPerVermogensbeheerder.Vermogensbeheerder = '".$vermogensbeheerder."' AND
   afmCategorien.afmCategorie is null 
   GROUP BY FondsenPerVermogensbeheerder.Fonds";
  $this->DB->SQL($query);
	$this->DB->Query();
	while($data = $this->DB->nextRecord())
	{
		echo "<br>".vt("Fout").": ".vt("Fonds")." '".$data['Fonds']."' ".vt("heeft geen bestaande afmCategorie")." '".$data['afmCategorie']."'.";
    $this->checkData[$data['Fonds']][$vermogensbeheerder]['BeleggingscategoriePerFonds']['afmCategorie']=1;
		$return = false;
	}

	$query ="SELECT FondsenPerVermogensbeheerder.Fonds, BeleggingscategoriePerFonds.afmCategorie
  FROM  (BeleggingscategoriePerFonds)
  RIGHT JOIN FondsenPerVermogensbeheerder ON FondsenPerVermogensbeheerder.Fonds = BeleggingscategoriePerFonds.Fonds
  AND (BeleggingscategoriePerFonds.Vermogensbeheerder IS NULL OR BeleggingscategoriePerFonds.Vermogensbeheerder = '$vermogensbeheerder')
  WHERE
  FondsenPerVermogensbeheerder.Vermogensbeheerder = '".$vermogensbeheerder."'
  AND BeleggingscategoriePerFonds.afmCategorie IS NULL
  GROUP BY FondsenPerVermogensbeheerder.Fonds";
	$this->DB->SQL($query);
	$this->DB->Query();

	while($data = $this->DB->nextRecord())
	{
		echo "<br>".vt("Fout").": ".vt("Fonds")." '".$data['Fonds']."' ".vt("heeft geen record in BeleggingscategoriePerFonds").".";
    $this->checkData[$data['Fonds']][$vermogensbeheerder]['BeleggingscategoriePerFonds']['afmCategorie']=1;
		$return = false;
	}
	return $return;

}
function check_sector($vermogensbeheerder)
{
  $query="SELECT geenStandaardSector FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$vermogensbeheerder."'";
  $this->DB->SQL($query);
  $geenStandaardSector=$this->DB->lookupRecord(); 
  $geenStandaardSector=$geenStandaardSector['geenStandaardSector'];
  $return = true;
  if($geenStandaardSector==true)
    $query ="SELECT
    FondsenPerVermogensbeheerder.Fonds,
    BeleggingssectorPerFonds.Beleggingssector,
    BeleggingssectorPerFonds.Vermogensbeheerder
    FROM
    (FondsenPerVermogensbeheerder, BeleggingssectorPerFonds)
    LEFT JOIN Beleggingssectoren ON BeleggingssectorPerFonds.Beleggingssector = Beleggingssectoren.Beleggingssector
    WHERE
    BeleggingssectorPerFonds.Fonds = FondsenPerVermogensbeheerder.Fonds AND
    BeleggingssectorPerFonds.Vermogensbeheerder = '".$vermogensbeheerder."' AND
    FondsenPerVermogensbeheerder.Vermogensbeheerder = '".$vermogensbeheerder."' AND
    Beleggingssectoren.Beleggingssector is null 
    GROUP BY FondsenPerVermogensbeheerder.Fonds";
  else
    $query ="SELECT
    FondsenPerVermogensbeheerder.Fonds,
    BeleggingssectorPerFonds.id,
    BeleggingssectorPerFonds.Beleggingssector,
    BeleggingssectorPerFonds.Vermogensbeheerder,
    standaardSectoren.Beleggingssector as standaardSector
    FROM FondsenPerVermogensbeheerder
    LEFT JOIN BeleggingssectorPerFonds ON FondsenPerVermogensbeheerder.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$vermogensbeheerder."' 
    LEFT JOIN Beleggingssectoren ON BeleggingssectorPerFonds.Beleggingssector = Beleggingssectoren.Beleggingssector
    JOIN Fondsen ON  FondsenPerVermogensbeheerder.Fonds = Fondsen.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$vermogensbeheerder."'
    LEFT JOIN Beleggingssectoren AS standaardSectoren  ON Fondsen.standaardSector = standaardSectoren.Beleggingssector
    WHERE
    FondsenPerVermogensbeheerder.Vermogensbeheerder = '".$vermogensbeheerder."' AND
    (Beleggingssectoren.Beleggingssector is null AND standaardSectoren.Beleggingssector is null)
    GROUP BY FondsenPerVermogensbeheerder.Fonds";
	$this->DB->SQL($query);
	$this->DB->Query();

	while($data = $this->DB->nextRecord())
	{
		echo "<br>".vt("Fout").": Fonds '".$data['Fonds']."' ".vt("heeft niet bestaande beleggingssector")." '".$data['Beleggingssector']."'.";
    $this->checkData[$data['Fonds']][$vermogensbeheerder]['BeleggingssectorPerFonds']['Beleggingssector']=1;
		$return = false;
	}

  if($geenStandaardSector==true)
  {
  	$query ="SELECT
    FondsenPerVermogensbeheerder.Fonds,
    BeleggingssectorPerFonds.Beleggingssector
    FROM  (BeleggingssectorPerFonds)
    RIGHT JOIN  FondsenPerVermogensbeheerder ON FondsenPerVermogensbeheerder.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$vermogensbeheerder."'
    
    WHERE
    FondsenPerVermogensbeheerder.Vermogensbeheerder = '".$vermogensbeheerder."'
    AND BeleggingssectorPerFonds.Beleggingssector IS NULL 
    GROUP BY FondsenPerVermogensbeheerder.Fonds";

	  $this->DB->SQL($query);
	  $this->DB->Query();

	  while($data = $this->DB->nextRecord())
	  {
	  	echo "<br>".vt("Fout").": Fonds '".$data['Fonds']."' ".vt("heeft geen record in BeleggingssectorPerFonds").".";
      $this->checkData[$data['Fonds']][$vermogensbeheerder]['BeleggingssectorPerFonds']['Beleggingssector']=1;
	  	$return = false;
  	}
  }
	echo "<br><b>".vt("sector controle voor")." $vermogensbeheerder ".vt("klaar").".</b> ";

  return $return;
}

function getSelect($vermogensbeheerder,$tabel,$key,$veld,$categorie,$value)
{
  $veldVertaling=array('Beleggingscategorien'=>'Beleggingscategorie','Beleggingssectoren'=>'Beleggingssector','Regios'=>'Regio','AttributieCategorien'=>'AttributieCategorie','afmCategorien'=>'afmCategorie','DuurzaamCategorien'=>'DuurzaamCategorie');

  $db=new DB();
  $extraFilter='';
  if($categorie=='Beleggingscategorien')
    $extraFilter="AND waarde NOT IN(SELECT hoofdcategorie FROM CategorienPerHoofdcategorie WHERE vermogensbeheerder='$vermogensbeheerder')";
  
  
  if($categorie=='ZorgplichtPerFonds')
    $query="SELECT Zorgplicht as waarde, omschrijving FROM Zorgplichtcategorien  WHERE vermogensbeheerder='$vermogensbeheerder' ORDER BY waarde";
  elseif($categorie=='afmCategorien')
    $query="SELECT afmCategorie as waarde, omschrijving FROM afmCategorien ORDER BY afmCategorie";
  else
    $query="SELECT waarde,$categorie.omschrijving FROM KeuzePerVermogensbeheerder 
    JOIN $categorie ON KeuzePerVermogensbeheerder.waarde=".$categorie.'.'.$veldVertaling[$categorie]." WHERE vermogensbeheerder='$vermogensbeheerder' AND categorie='$categorie' $extraFilter ORDER BY waarde";
  $db->SQL($query);// echo $query."<br>\n";
  $db->Query();
  $html='<select type="select"  name="veld__'.base64_encode($vermogensbeheerder.'__'.$tabel.'__'.$key.'__'.$veld).'" >';
  $html.="<option  value='' >---</option>";
  while($data=$db->nextRecord())
  {
    if($value==$data['waarde'])
      $html.="<option value='".$data['waarde']."' selected>".$data['waarde'].' - '.$data['omschrijving']."</option>";
    else
      $html.="<option value='".$data['waarde']."'>  ".$data['waarde'].' - '.$data['omschrijving']."</option>";
  }
  $html.="</select>\n";
  
  return $html;
}

function getInputTxt($vermogensbeheerder,$tabel,$key,$veld)
{
  $db=new DB(); 
  $query="SELECT $veld as waarde FROM $tabel WHERE Vermogensbeheerder='".mysql_real_escape_string($vermogensbeheerder)."' AND Fonds='".mysql_real_escape_string($key)."' ";
  $db->SQL($query);
  $tmp=$db->lookupRecord();
  $html='<input type="text" align="right" size="8" name="veld__'.base64_encode($vermogensbeheerder.'__'.$tabel.'__'.$key.'__'.$veld).'" value="'.$tmp['waarde'].'" >';
   
  return $html;
}

function getFixForm($url)
{
  //echo "rvv";
  //listarray($this->checkData);
 // exit;
  $db=new DB();
  echo '<form action="'.$url.'" method="POST"><table border=1>';
//http://rvv.aeict.nl/AIRS/html/queueExportData.php?posted=true&Bedrijf=RRP&jaar=&exportType=queue&tabel=Accountmanagers&do=update&force=1
  ksort($this->checkData); 
  foreach($this->checkData as $fonds=>$vermogensbeheerders)
  {
  
    $lookupQuery="SELECT Fondsen.standaardSector,Fondsen.Valuta,Fondsen.rating,Fondsen.ISINCode,Fondsen.Omschrijving FROM Fondsen WHERE Fondsen.Fonds='".mysql_real_escape_string($fonds)."'";
    $db->SQL($lookupQuery);
    $fondsData=$db->lookupRecord();
    $extraInfo='| '.$fondsData['standaardSector'].' | '.$fondsData['Valuta'].' | '.$fondsData['rating'].' | '.$fondsData['ISINCode'].' | '.$fondsData['Omschrijving'].' |';
    $lookupKey='Fonds';
    foreach($vermogensbeheerders as $vermogensbeheerder=>$fondsTabellen)
    {
      echo "<tr>";
      echo "<td title='".addslashes($extraInfo)."'> $fonds </td><td>$vermogensbeheerder</td>";
      foreach($fondsTabellen as $tabel=>$velden)
      {

        $selectVelden=array('id');
        foreach($velden as $veld=>$check)
           $selectVelden[]=$veld;//.=", `$veld` "
        
        $lookupQuery="SELECT  ".implode(" ,",$selectVelden)." FROM $tabel WHERE $lookupKey='$fonds' AND vermogensbeheerder='$vermogensbeheerder'";
        $db->SQL($lookupQuery);
        $lookupData=$db->lookupRecord();
       // echo "$lookupQuery <br>\n";
        
        foreach($selectVelden as $veld)
        {
          if($veld=='Beleggingssector')
            echo "<td>" . vt('Beleggingssector') . " ".$this->getSelect($vermogensbeheerder,$tabel,$fonds,$veld,'Beleggingssectoren',$lookupData[$veld])."</td>";
          elseif($veld=='Regio')
            echo "<td>Regio ".$this->getSelect($vermogensbeheerder,$tabel,$fonds,$veld,'Regios',$lookupData[$veld])."</td>";
          elseif($veld=='Beleggingscategorie')
            echo "<td>" . vt('Beleggingscategorie') . " ".$this->getSelect($vermogensbeheerder,$tabel,$fonds,$veld,'Beleggingscategorien',$lookupData[$veld])."</td>";
          elseif($veld=='AttributieCategorie')
            echo "<td>" . vt('AttributieCategorie') . " ".$this->getSelect($vermogensbeheerder,$tabel,$fonds,$veld,'AttributieCategorien',$lookupData[$veld])."</td>";
          elseif($veld=='afmCategorie')
            echo "<td>" . vt('AfmCategorie') . " ".$this->getSelect($vermogensbeheerder,$tabel,$fonds,$veld,'afmCategorien',$lookupData[$veld])."</td>";
					elseif($veld=='DuurzaamCategorie')
						echo "<td>" . vt('duurzaamCategorie') . " ".$this->getSelect($vermogensbeheerder,$tabel,$fonds,$veld,'DuurzaamCategorien',$lookupData[$veld])."</td>";
          elseif($veld=='Zorgplicht')
            echo "<td>" . vt('ZorgplichtPerFonds') . " ".$this->getSelect($vermogensbeheerder,$tabel,$fonds,$veld,'ZorgplichtPerFonds',$lookupData[$veld])."</td>";
          elseif($veld=='duurzaamheid')
            echo "<td>" . vt('Duurzaamheid') . " ".$this->getInputTxt($vermogensbeheerder,$tabel,$fonds,$veld)."</td>";
          else  
            echo "<td> $veld = ".$lookupData[$veld]." </td>";
        }
        
      }
      echo "</tr>";
    }
    
  }
 
  echo "<tr><td><input type='submit' value='".vt("Verwerk")."'></td></tr>";
  echo "</table></form>";
}

function updateRecords($data)
{
  //listarray($data);
  global $__appvar,$USR;
  $db=new DB();
  foreach($data as $key=>$value)
  {
    if($value <> '' && substr($key,0,6)=='veld__')
    {
      $keyData=base64_decode(substr($key,6));
      $keyParts=explode('__',$keyData);
      $vermogensbeheerder=$keyParts[0];
      $tabel=$keyParts[1];
      $keyWaarde=$keyParts[2];
      $veld=$keyParts[3];
      $keyVeld='Fonds';
      $query="SELECT id,$veld FROM $tabel WHERE $keyVeld='".mysql_real_escape_string($keyWaarde)."' AND vermogensbeheerder='".mysql_real_escape_string($vermogensbeheerder)."'";
      $db->SQL($query);
      //echo $query."<br>\n";
      $recordLookup=$db->lookupRecord();
      $recordId=$recordLookup['id'];
      $oudeWaarde=$recordLookup[$veld];
      if($recordId>0)
        $query="UPDATE $tabel SET 
                       $veld='".mysql_real_escape_string($value)."',change_date=now(),change_user='".$USR."' 
                       WHERE vermogensbeheerder='".mysql_real_escape_string($vermogensbeheerder)."' AND id='".$recordId."'"; 
      else
        $query="INSERT INTO $tabel 
                SET ".$keyVeld."='".mysql_real_escape_string($keyWaarde)."', 
                      $veld='".mysql_real_escape_string($value)."',
                      vermogensbeheerder='".mysql_real_escape_string($vermogensbeheerder)."',
                      change_date=now(),change_user='".$USR."',add_date=now(),add_user='".$USR."' ";
      $db->SQL($query);
      $db->Query();
      
      if($__appvar['logAccess'])
      {
        $query="INSERT INTO trackAndTrace SET 
        tabel='$tabel',recordId='$recordId',veld='$veld',oudeWaarde='$oudeWaarde',nieuweWaarde='$value',
        add_date=now(),add_user='$USR'";
        $db->SQL($query);
        $db->Query(); 
      }  
      
      
      //echo $query."<br>\n";
    }
  }
}

function check_zorgplichtFonds($vermogensbeheerder)
{
  $return = true;
  $query = "SELECT
		 ZorgplichtPerFonds.Zorgplicht,
		 FondsenPerVermogensbeheerder.Fonds
		 FROM  (Vermogensbeheerders ,FondsenPerVermogensbeheerder)
		 LEFT JOIN ZorgplichtPerFonds ON  FondsenPerVermogensbeheerder.Fonds = ZorgplichtPerFonds.Fonds AND
		 Vermogensbeheerders.Vermogensbeheerder = ZorgplichtPerFonds.Vermogensbeheerder
		 WHERE
		 Vermogensbeheerders.Vermogensbeheerder = '".$vermogensbeheerder."' AND
		 FondsenPerVermogensbeheerder.Vermogensbeheerder = '".$vermogensbeheerder."' AND
		 ZorgplichtPerFonds.Zorgplicht IS NULL 
     GROUP BY FondsenPerVermogensbeheerder.Fonds";
  $this->DB->SQL($query);
	$this->DB->Query();

	while($data = $this->DB->nextRecord())
	{
		echo "<br>".vt("Fout").": ".vt("Fonds")." '".$data['Fonds']."' ".vt("heeft geen zorgplicht").".";
    $this->checkData[$data['Fonds']][$vermogensbeheerder]['ZorgplichtPerFonds']['Zorgplicht']=1;
		$return = false;
	}
	echo "<br><b>".vt("Zorgplicht per Fonds controle voor")." $vermogensbeheerder ".vt("klaar").".</b> ";

  return $return;
}
function check_zorgplichtPortefeuille($vermogensbeheerder)
{
  $return = true;
  $query = "SELECT
    Portefeuilles.Portefeuille
    FROM (Vermogensbeheerders, Portefeuilles)
    LEFT JOIN ZorgplichtPerPortefeuille ON ZorgplichtPerPortefeuille.Portefeuille = Portefeuilles.Portefeuille
    WHERE Vermogensbeheerders.Vermogensbeheerder = '".$vermogensbeheerder."' AND
    Portefeuilles.Vermogensbeheerder = '".$vermogensbeheerder."' AND
    ZorgplichtPerPortefeuille.Portefeuille IS NULL 
    GROUP BY Portefeuilles.Portefeuille";
  $this->DB->SQL($query);
	$this->DB->Query();
	while($data = $this->DB->nextRecord())
	{
		echo "<br>".vt("Fout").": ".vt("Portefeuille")." '".$data['Portefeuille']."' ".vt("heeft geen Zorgplicht").".";
		$return = false;
	}
	echo "<br><b>".vt("Zorgplicht per Portefeuille controle voor")." $vermogensbeheerder ".vt("klaar").".</b> ";
  return $return;
}

function check_hoofdcategorie($vermogensbeheerder)
{
    $return = true;
    $query =
      "SELECT
		  BeleggingscategoriePerFonds.Beleggingscategorie,
      CategorienPerHoofdcategorie.Hoofdcategorie
		  FROM (FondsenPerVermogensbeheerder,  Vermogensbeheerders)
		  LEFT JOIN BeleggingscategoriePerFonds ON  FondsenPerVermogensbeheerder.Fonds = BeleggingscategoriePerFonds.Fonds
		  AND (BeleggingscategoriePerFonds.Vermogensbeheerder IS NULL OR BeleggingscategoriePerFonds.Vermogensbeheerder = '$vermogensbeheerder')
      LEFT JOIN CategorienPerHoofdcategorie ON CategorienPerHoofdcategorie.Beleggingscategorie = BeleggingscategoriePerFonds.Beleggingscategorie
		  WHERE
      Vermogensbeheerders.Vermogensbeheerder = '".$vermogensbeheerder."' AND
      BeleggingscategoriePerFonds.Vermogensbeheerder =  '".$vermogensbeheerder."'
      AND CategorienPerHoofdcategorie.Hoofdcategorie IS NULL
      GROUP BY BeleggingscategoriePerFonds.Beleggingscategorie";
  $this->DB->SQL($query);
	$this->DB->Query();
	while($data = $this->DB->nextRecord())
	{
		echo "<br>".vt("Fout").": ".vt("Beleggingscategorie")." '".$data['Beleggingscategorie']."' ".vt("heeft geen hoofdcategorie").".";
		$return = false;
	}
	echo "<br><b>".vt("hoofdcategorie controle voor")." $vermogensbeheerder ".vt("klaar").".</b> ";
  return $return;

}
function check_hoofdsector($vermogensbeheerder)
{
  $return = true;
  $query =
    "SELECT BeleggingssectorPerFonds.Beleggingssector, SectorenPerHoofdsector.Hoofdsector
    FROM (FondsenPerVermogensbeheerder)
    LEFT JOIN BeleggingssectorPerFonds ON FondsenPerVermogensbeheerder.Fonds = BeleggingssectorPerFonds.Fonds
    AND (BeleggingssectorPerFonds.Vermogensbeheerder IS NULL OR BeleggingssectorPerFonds.Vermogensbeheerder = '$vermogensbeheerder')
    LEFT JOIN SectorenPerHoofdsector ON SectorenPerHoofdsector.Beleggingssector = BeleggingssectorPerFonds.Beleggingssector
    AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$vermogensbeheerder."'
    AND SectorenPerHoofdsector.Vermogensbeheerder = '".$vermogensbeheerder."'
    GROUP BY BeleggingssectorPerFonds.Beleggingssector";
  $this->DB->SQL($query);
	$this->DB->Query();
	while($data = $this->DB->nextRecord())
	{
		echo "<br>".vt("Fout").": ".vt("Beleggingssector")." '".$data['Beleggingssector']."' ".vt("heeft geen hoofdsector").".";
		$return = false;
	}
	echo "<br><b>".vt("hoofdsector controle voor")." $vermogensbeheerder ".vt("klaar").".</b> ";
  return $return;
}
function check_sectorRegio($vermogensbeheerder)
{
  $return = true;
  $query="SELECT
     FondsenPerVermogensbeheerder.Fonds,
     Regios.Regio
     FROM FondsenPerVermogensbeheerder 
     LEFT JOIN BeleggingssectorPerFonds ON FondsenPerVermogensbeheerder.Fonds = BeleggingssectorPerFonds.fonds AND  BeleggingssectorPerFonds.Vermogensbeheerder = '".$vermogensbeheerder."'   
     LEFT JOIN Regios ON BeleggingssectorPerFonds.Regio = Regios.Regio
     WHERE
     FondsenPerVermogensbeheerder.Vermogensbeheerder = '".$vermogensbeheerder."'   AND
     (BeleggingssectorPerFonds.Regio IS NULL OR BeleggingssectorPerFonds.Regio='')
     GROUP BY FondsenPerVermogensbeheerder.Fonds";
  $this->DB->SQL($query);
	$this->DB->Query();
	while($data = $this->DB->nextRecord())
	{
		echo "<br>".vt("Fout").": ".vt("Fonds")." '".$data['Fonds']."' ".vt("heeft geen geldige Regio").".";
    $this->checkData[$data['Fonds']][$vermogensbeheerder]['BeleggingssectorPerFonds']['Regio']=1;
		$return = false;
	}
/*
	$query ="SELECT
  FondsenPerVermogensbeheerder.Fonds,
  BeleggingssectorPerFonds.Regio
  FROM    (FondsenPerVermogensbeheerder)
  LEFT JOIN BeleggingssectorPerFonds ON BeleggingssectorPerFonds.Fonds = FondsenPerVermogensbeheerder.Fonds
  AND (BeleggingssectorPerFonds.Vermogensbeheerder IS NULL OR BeleggingssectorPerFonds.Vermogensbeheerder = '$vermogensbeheerder')
  WHERE
  FondsenPerVermogensbeheerder.Vermogensbeheerder = '".$vermogensbeheerder."' AND
  BeleggingssectorPerFonds.Regio IS NULL 
  GROUP BY FondsenPerVermogensbeheerder.Fonds";
	$this->DB->SQL($query); 
	$this->DB->Query();
	while($data = $this->DB->nextRecord())
	{
		echo "<br>Fout: Fonds '".$data['Fonds']."' heeft geen Regio.";
    $this->checkData[$data['Fonds']][$vermogensbeheerder]['BeleggingssectorPerFonds']['Regio']=1;
		$return = false;
	}
*/
	echo "<br><b>".vt("Fonds regio controle voor")." $vermogensbeheerder ".vt("klaar").".</b> ";
  return $return;
}
function check_sectorAttributie($vermogensbeheerder)
{
  $return = true;
  $query =
  "SELECT
   BeleggingssectorPerFonds.Fonds,
   AttributieCategorien.AttributieCategorie
   FROM (BeleggingssectorPerFonds ,FondsenPerVermogensbeheerder)
   LEFT JOIN AttributieCategorien ON BeleggingssectorPerFonds.AttributieCategorie = AttributieCategorien.AttributieCategorie
   WHERE
   BeleggingssectorPerFonds.Vermogensbeheerder = '".$vermogensbeheerder."'  AND
   FondsenPerVermogensbeheerder.Fonds =   BeleggingssectorPerFonds.fonds AND
   FondsenPerVermogensbeheerder.Vermogensbeheerder = '".$vermogensbeheerder."'  AND
   AttributieCategorien.AttributieCategorie IS NULL 
   GROUP BY BeleggingssectorPerFonds.Fonds";
  $this->DB->SQL($query);
	$this->DB->Query();
	while($data = $this->DB->nextRecord())
	{
		echo "<br>".vt("Fout").": ".vt("Fonds")." '".$data['Fonds']."' ".vt("heeft geen geldige AttributieCategorie").".";
    $this->checkData[$data['Fonds']][$vermogensbeheerder]['BeleggingssectorPerFonds']['AttributieCategorie']=1;
		$return = false;
	}

	$query ="SELECT
  FondsenPerVermogensbeheerder.Fonds,
  BeleggingssectorPerFonds.AttributieCategorie
  FROM    (FondsenPerVermogensbeheerder)
  LEFT JOIN BeleggingssectorPerFonds ON BeleggingssectorPerFonds.Fonds = FondsenPerVermogensbeheerder.Fonds
  AND (BeleggingssectorPerFonds.Vermogensbeheerder IS NULL OR BeleggingssectorPerFonds.Vermogensbeheerder = '$vermogensbeheerder')
  WHERE
  FondsenPerVermogensbeheerder.Vermogensbeheerder = '".$vermogensbeheerder."' AND
  BeleggingssectorPerFonds.AttributieCategorie IS NULL 
  GROUP BY FondsenPerVermogensbeheerder.Fonds";
	$this->DB->SQL($query);
	$this->DB->Query();
	while($data = $this->DB->nextRecord())
	{
		echo "<br>".vt("Fout").": ".vt("Fonds")." '".$data['Fonds']."' ".vt("heeft geen AttributieCategorie").".";
    $this->checkData[$data['Fonds']][$vermogensbeheerder]['BeleggingssectorPerFonds']['AttributieCategorie']=1;
		$return = false;
	}


	echo "<br><b>".vt("Fonds AttributieCategorie controle voor")." $vermogensbeheerder ".vt("klaar").".</b> ";
  return $return;

}

	function check_duurzaamCategorie($vermogensbeheerder)
	{
		$return = true;
		$query =
			"SELECT
   BeleggingssectorPerFonds.Fonds,
   DuurzaamCategorien.DuurzaamCategorie
   FROM (BeleggingssectorPerFonds ,FondsenPerVermogensbeheerder)
   LEFT JOIN DuurzaamCategorien ON BeleggingssectorPerFonds.DuurzaamCategorie = DuurzaamCategorien.DuurzaamCategorie
   WHERE
   BeleggingssectorPerFonds.Vermogensbeheerder = '".$vermogensbeheerder."'  AND
   FondsenPerVermogensbeheerder.Fonds =   BeleggingssectorPerFonds.fonds AND
   FondsenPerVermogensbeheerder.Vermogensbeheerder = '".$vermogensbeheerder."'  AND
   DuurzaamCategorien.DuurzaamCategorie IS NULL 
   GROUP BY BeleggingssectorPerFonds.Fonds";
		$this->DB->SQL($query);
		$this->DB->Query();
		while($data = $this->DB->nextRecord())
		{
			echo "<br>".vt("Fout").": ".vt("Fonds")." '".$data['Fonds']."' ".vt("heeft geen geldige DuurzaamCategorie").".";
			$this->checkData[$data['Fonds']][$vermogensbeheerder]['BeleggingssectorPerFonds']['DuurzaamCategorie']=1;
			$return = false;
		}

		$query ="SELECT
  FondsenPerVermogensbeheerder.Fonds,
  BeleggingssectorPerFonds.DuurzaamCategorie
  FROM    (FondsenPerVermogensbeheerder)
  LEFT JOIN BeleggingssectorPerFonds ON BeleggingssectorPerFonds.Fonds = FondsenPerVermogensbeheerder.Fonds
  AND (BeleggingssectorPerFonds.Vermogensbeheerder IS NULL OR BeleggingssectorPerFonds.Vermogensbeheerder = '$vermogensbeheerder')
  WHERE
  FondsenPerVermogensbeheerder.Vermogensbeheerder = '".$vermogensbeheerder."' AND
  BeleggingssectorPerFonds.DuurzaamCategorie IS NULL 
  GROUP BY FondsenPerVermogensbeheerder.Fonds";
		$this->DB->SQL($query);
		$this->DB->Query();
		while($data = $this->DB->nextRecord())
		{
			echo "<br>".vt("Fout").": ".vt("Fonds")." '".$data['Fonds']."' ".vt("heeft geen DuurzaamCategorie").".";
			$this->checkData[$data['Fonds']][$vermogensbeheerder]['BeleggingssectorPerFonds']['DuurzaamCategorie']=1;
			$return = false;
		}


		echo "<br><b>".vt("Fonds DuurzaamCategorie controle voor")." $vermogensbeheerder ".vt("klaar").".</b> ";
		return $return;

	}
function check_rekeningATT($vermogensbeheerder)
{
  $return = true;
  $query = "SELECT
Rekeningen.Rekening,
Rekeningen.Beleggingscategorie,
Rekeningen.AttributieCategorie
FROM
Portefeuilles
Inner Join Rekeningen ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
WHERE Rekeningen.consolidatie=0 AND Portefeuilles.consolidatie=0 AND (AttributieCategorie='' OR AttributieCategorie is null ) AND Portefeuilles.Vermogensbeheerder='$vermogensbeheerder'
GROUP BY Rekeningen.Rekening";

  $this->DB->SQL($query);
	$this->DB->Query();
	while($data = $this->DB->nextRecord())
	{
		echo "<br>".vt("Fout").": ".vt("Rekening")." '".$data['Rekening']."' ".vt("heeft geen AttributieCategorie").".";
		$return = false;
	}
	echo "<br><b>".vt("Rekening Attributiecategorie controle voor")." $vermogensbeheerder ".vt("klaar").".</b> ";
  return $return;

}

function check_rekeningCat($vermogensbeheerder)
{
  $return = true;
  $query = "SELECT
Rekeningen.Rekening,
Rekeningen.Beleggingscategorie,
Rekeningen.AttributieCategorie
FROM
Portefeuilles
Inner Join Rekeningen ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
WHERE Rekeningen.consolidatie=0 AND Portefeuilles.consolidatie=0 AND (Beleggingscategorie='' OR Beleggingscategorie is null ) AND Portefeuilles.Vermogensbeheerder='$vermogensbeheerder'
GROUP BY Rekeningen.Rekening";

  $this->DB->SQL($query);
	$this->DB->Query();
	while($data = $this->DB->nextRecord())
	{
		echo "<br>".vt("Fout").": ".vt("Rekening")." '".$data['Rekening']."' ".vt("heeft geen beleggingscategorie").".";
		$return = false;
	}
	echo "<br><b>".vt("Rekening beleggingscategorie controle voor")." $vermogensbeheerder ".vt("klaar").".</b> ";
  return $return;

}

function check_rekeningDepotbank($vermogensbeheerder)
{
  $return = true;
  $query = "SELECT
Rekeningen.Rekening,
Rekeningen.Beleggingscategorie,
Rekeningen.AttributieCategorie,
Rekeningen.Depotbank
FROM
Portefeuilles
Inner Join Rekeningen ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
WHERE Rekeningen.consolidatie=0 AND Portefeuilles.consolidatie=0 AND (Rekeningen.Depotbank='' OR Rekeningen.Depotbank is null ) AND Portefeuilles.Vermogensbeheerder='$vermogensbeheerder'
GROUP BY Rekeningen.Rekening";

  $this->DB->SQL($query);
	$this->DB->Query();
	while($data = $this->DB->nextRecord())
	{
		echo "<br>".vt("Fout").": ".vt("Rekening")." '".$data['Rekening']."' ".vt("heeft geen Depotbank").".";
		$return = false;
	}
	echo "<br><b>".vt("Rekening Depotbank controle voor")." $vermogensbeheerder ".vt("klaar").".</b> ";
  return $return;

}



function check_kruisposten($vermogensbeheerder)
{
  		//-------------------------------------------------------------------------

  $return = true;
  $DB2 = new DB();
	$DB2->SQL("SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Kruispost = '1' ");
	$DB2->Query();
	while($gb = $DB2->NextRecord())
	{
		$grootboeken[] = $gb['Grootboekrekening'];
	}

	$grootboekSQL = "Grootboekrekening IN('".implode("','",$grootboeken)."')";

	$query = "SELECT Rekeningen.Portefeuille, ABS(SUM(ROUND((ROUND(Rekeningmutaties.Credit-Rekeningmutaties.Debet,2) * Rekeningmutaties.Valutakoers),2))) AS verschil FROM
  Rekeningmutaties
  JOIN Rekeningen ON Rekeningen.Rekening = Rekeningmutaties.Rekening
  JOIN Portefeuilles ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
  WHERE Rekeningen.consolidatie=0 AND Portefeuilles.consolidatie=0 AND YEAR(Rekeningmutaties.Boekdatum)=YEAR(NOW()) AND Portefeuilles.Einddatum > now() AND Portefeuilles.Vermogensbeheerder = '$vermogensbeheerder' AND $grootboekSQL 
  GROUP BY Rekeningen.Portefeuille 
  HAVING verschil > 0.1";

	$DB2->SQL($query);
	$DB2->Query();
	while($mutatieverschil = $DB2->NextRecord())
	{
		echo "<br>".vt("Fout").": ".vt("er staat een bedag op kruispost, Portefeuille")." : ".$mutatieverschil['Portefeuille']." ".vt("verschil").": ".$mutatieverschil['verschil'] ;
		$return = false;
	}

	return $return;
}


function check_historischePortefeuilleIndex($vermogensbeheerder)
{
  $return = true;

  $rekeningmutatieContole = 1;
  $fondskoersenControle =1;
  $valutakoersenContole =1;
  $sectorPerFondsControle=1;
  $debug=0;

  if($this->timeStamp != '')
  {
    $rekeningmutatieContole = 0;
    $fondskoersenControle =0;
    $valutakoersenContole =0;
    $sectorPerFondsControle=0;

    $query="SELECT HistorischePortefeuilleIndex.Datum FROM
            HistorischePortefeuilleIndex
            Inner Join Portefeuilles ON HistorischePortefeuilleIndex.Portefeuille = Portefeuilles.Portefeuille
            Inner Join Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
            WHERE  Portefeuilles.Vermogensbeheerder = '$vermogensbeheerder'
            ORDER BY HistorischePortefeuilleIndex.Datum DESC limit 1";

  $this->DB->SQL($query);
	$this->DB->Query();
	$data=$this->DB->nextRecord();
	$laatsteIndex=$data['Datum'];

	echo "<br> ".vt("Laatste index").": $laatsteIndex.\n"; flush();ob_flush();
	$query="SELECT DISTINCT(Portefeuilles.Portefeuille) as Portefeuille,Rekeningmutaties.Fonds FROM Rekeningmutaties
         Inner Join Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
         Inner Join Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
         WHERE Rekeningen.consolidatie=0 AND Portefeuilles.consolidatie=0 AND Portefeuilles.Vermogensbeheerder = '$vermogensbeheerder' AND
         Portefeuilles.Portefeuille = Rekeningen.Portefeuille AND
         Rekeningen.Rekening = Rekeningmutaties.Rekening AND
         Rekeningmutaties.change_date >= '".$this->timeStamp."'  AND Rekeningmutaties.boekdatum <= '$laatsteIndex' AND Portefeuilles.Einddatum > '$laatsteIndex'";
	$this->DB->SQL($query);
	echo "<br> <b>".vt("Pre")."</b> ".vt("RekeningmutatieContole").""; flush();ob_flush();
	$this->DB->Query();
	while ($data=$this->DB->nextRecord())
	{
		$rekeningmutatieContole = 1;
		echo "<br> ".vt("Rekeningmutatie voor")." ".$data['Portefeuille']." ".$data['Fonds']; flush();ob_flush();
		$pdata[$data['Portefeuille']]=$data['Portefeuille'];
	}

	$query="SELECT distinct(Fondskoersen.Fonds) as Fonds FROM Fondskoersen WHERE Fondskoersen.change_date > '".$this->timeStamp."' AND Fondskoersen.Datum <= '$laatsteIndex' ";
	$this->DB->SQL($query);
	echo "<br>  <b>".vt("Pre")."</b> ".vt("FondskoersContole fondsen ophalen").""; flush();ob_flush();
	$this->DB->Query();
	$fondsen=array();
	while ($data=$this->DB->nextRecord())
	{
	  $fondsen[]=$data['Fonds'];
	}
	$query="SELECT DISTINCT(Portefeuilles.Portefeuille) as Portefeuille,Rekeningmutaties.Fonds FROM Rekeningmutaties
          Inner Join Rekeningen ON Rekeningen.Rekening = Rekeningmutaties.Rekening
          Inner Join Portefeuilles ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
          WHERE Rekeningen.consolidatie=0 AND Portefeuilles.consolidatie=0 AND Rekeningmutaties.Fonds IN('".implode("','",$fondsen)."') AND Rekeningmutaties.Fonds <> '' AND
          Portefeuilles.Vermogensbeheerder='$vermogensbeheerder' AND Portefeuilles.Einddatum > '$laatsteIndex'";
	$this->DB->SQL($query);
	echo "<br>  <b>".vt("Pre")."</b> ".vt("FondskoersContole portefeuilles ophalen").""; flush();ob_flush();
	$this->DB->Query();
	while ($data=$this->DB->nextRecord())
	{
		$fondskoersenControle = 1;
		echo "<br> ".vt("Fondskoers voor")." ".$data['Portefeuille']." ".$data['Fonds']; flush();ob_flush();
		$pdata[$data['Portefeuille']]=$data['Portefeuille'];
	}

	$query="SELECT distinct(Valutakoersen.Valuta) as Valuta FROM Valutakoersen WHERE Valutakoersen.change_date > '".$this->timeStamp."' AND Valutakoersen.Datum <= '$laatsteIndex' ";
	$this->DB->SQL($query);
	echo "<br>  <b>".vt("Pre")."</b> ".vt("Valutakoersen Contole valuta ophalen").""; flush();ob_flush();
	$this->DB->Query();
	$valutas=array();
	while ($data=$this->DB->nextRecord())
	{
	  $valutas[]=$data['Valuta'];
	}
	$query="SELECT DISTINCT(Portefeuilles.Portefeuille) as Portefeuille,Rekeningmutaties.Valuta FROM Rekeningmutaties
          Inner Join Rekeningen ON Rekeningen.Rekening = Rekeningmutaties.Rekening
          Inner Join Portefeuilles ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
          WHERE Rekeningen.consolidatie=0 AND Portefeuilles.consolidatie=0 AND
          Rekeningmutaties.Valuta IN('".implode("','",$valutas)."') AND Rekeningmutaties.Valuta <> '' AND
          Portefeuilles.Vermogensbeheerder='$vermogensbeheerder' AND Portefeuilles.Einddatum > '$laatsteIndex'";
	$this->DB->SQL($query);
	echo "<br>  <b>".vt("Pre")."</b> ".vt("Valutakoersen Contole").""; flush();ob_flush();
	$this->DB->Query();
	while ($data=$this->DB->nextRecord())
	{
		$valutakoersenContole = 1;
		echo "<br> ".vt("Valutakoers voor")." ".$data['Portefeuille']." ".$data['Valuta']; flush();ob_flush();
		$pdata[$data['Portefeuille']]=$data['Portefeuille'];
	}
  $query="SELECT DISTINCT(Portefeuilles.Portefeuille) as Portefeuille,Rekeningmutaties.Fonds FROM BeleggingssectorPerFonds
          Inner Join Rekeningmutaties ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds
          Inner Join Rekeningen ON Rekeningen.Rekening = Rekeningmutaties.Rekening
          Inner Join Portefeuilles ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
          WHERE Rekeningen.consolidatie=0 AND Portefeuilles.consolidatie=0 AND
          BeleggingssectorPerFonds.change_date > '".$this->timeStamp."' AND Rekeningmutaties.boekDatum <= '$laatsteIndex' AND
          BeleggingssectorPerFonds.Vermogensbeheerder ='$vermogensbeheerder' AND Portefeuilles.Vermogensbeheerder='$vermogensbeheerder' AND
          Portefeuilles.Einddatum > '$laatsteIndex'";
	$this->DB->SQL($query);
	echo "<br>  <b>".vt("Pre")."</b> ".vt("sectorPerFonds Contole").""; flush();ob_flush();
	$this->DB->Query();
	while ($data=$this->DB->nextRecord())
	{
		$sectorPerFondsControle = 1;
		echo "<br> ".vt("sectorPerFonds voor")." ".$data['Portefeuille']." ".$data['Fonds']; flush();ob_flush();
		$pdata[$data['Portefeuille']]=$data['Portefeuille'];
	}

	$portefeuilles="IN('".implode("','",$pdata)."')";
	$query =  "SELECT Portefeuilles.Portefeuille, Vermogensbeheerders.PerformanceBerekening, Portefeuilles.Startdatum as PortefeuilleStartdatum,
               (SELECT HistorischePortefeuilleIndex.Datum FROM HistorischePortefeuilleIndex WHERE HistorischePortefeuilleIndex.Portefeuille =  Portefeuilles.Portefeuille ORDER BY HistorischePortefeuilleIndex.Datum DESC  limit 1 ) as laatsteIndex,
               (SELECT HistorischePortefeuilleIndex.change_date FROM HistorischePortefeuilleIndex WHERE HistorischePortefeuilleIndex.Portefeuille =  Portefeuilles.Portefeuille ORDER BY HistorischePortefeuilleIndex.Datum DESC  limit 1 ) as laatsteIndexChange,
               (SELECT Boekdatum FROM Rekeningmutaties Inner Join Rekeningen ON Rekeningen.Rekening = Rekeningmutaties.Rekening WHERE Portefeuille = Portefeuilles.Portefeuille ORDER BY Boekdatum limit 1) as Startdatum
               FROM Portefeuilles
                JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
               WHERE
               Portefeuilles.Portefeuille $portefeuilles AND Portefeuilles.Einddatum > NOW()";
  }
  else
  {
    $query =  "SELECT Portefeuilles.Portefeuille, Vermogensbeheerders.PerformanceBerekening, Portefeuilles.Startdatum as PortefeuilleStartdatum,
               (SELECT HistorischePortefeuilleIndex.Datum FROM HistorischePortefeuilleIndex WHERE HistorischePortefeuilleIndex.Portefeuille =  Portefeuilles.Portefeuille ORDER BY HistorischePortefeuilleIndex.Datum DESC  limit 1 ) as laatsteIndex,
               (SELECT HistorischePortefeuilleIndex.change_date FROM HistorischePortefeuilleIndex WHERE HistorischePortefeuilleIndex.Portefeuille =  Portefeuilles.Portefeuille ORDER BY HistorischePortefeuilleIndex.Datum DESC  limit 1 ) as laatsteIndexChange,
               (SELECT Boekdatum FROM Rekeningmutaties Inner Join Rekeningen ON Rekeningen.Rekening = Rekeningmutaties.Rekening WHERE Portefeuille = Portefeuilles.Portefeuille ORDER BY Boekdatum limit 1) as Startdatum
               FROM Portefeuilles
               JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
               WHERE Portefeuilles.consolidatie=0 AND
               Portefeuilles.Vermogensbeheerder = '".$vermogensbeheerder."' AND Portefeuilles.Einddatum > NOW()";
  }
  echo "<br> ".vt("Portefeuilles verzamelen")."."; flush();ob_flush();
  $this->DB->SQL($query);
 $this->DB->Query();
 if($debug)
   echo "$query <br>\n";
  while($data = $this->DB->nextRecord())
	{
	  if($data['laatsteIndex'] == '')
	    $data['laatsteIndex'] =$data['Startdatum'];
	  if($data['laatsteIndex'] == '')
	    $data['laatsteIndex'] =$data['PortefeuilleStartdatum'];

    $pdata[strval($data['Portefeuille'])] = $data;
  }


  foreach ($pdata as $portefeuille=>$indexData)
  {
    if(!isset($herberekenen[$portefeuille]))
      $herberekenen[$portefeuille]=$indexData['laatsteIndex'];

    if($rekeningmutatieContole)
    {
    //Rekeningmutatie controle ----------------------------------------------------
    echo "<br> rekeningmutatieContole $portefeuille gestart."; flush();ob_flush();
    $query = "SELECT Rekeningen.Portefeuille, Rekeningmutaties.Omschrijving,  Rekeningmutaties.boekdatum,Rekeningmutaties.change_date, Rekeningmutaties.boekdatum
              FROM
              Rekeningmutaties
              JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
              WHERE Rekeningen.consolidatie=0 AND 
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningmutaties.boekdatum <=  '".$indexData['laatsteIndex']."' AND
              Rekeningmutaties.change_date > '".$indexData['laatsteIndexChange']."'
              ORDER BY Boekdatum ASC LIMIT 1 ";
    $this->DB->SQL($query);
    if($debug)
	    echo "$query <br>\n";
	  $this->DB->Query();
    if($data = $this->DB->nextRecord())
    {
      echo "<br> ".vt("rekeningmutaties voor")." $portefeuille ".vt("vanaf")." ".$data['boekdatum'].".";flush();ob_flush();
      if(isset($herberekenen[$portefeuille]))
        $data['boekdatum'] = jul2sql(min(db2jul($herberekenen[$portefeuille]),db2jul($data['boekdatum'])));

      $herberekenen[$portefeuille]=$data['boekdatum'];

    }

    //eind Rekeningmutatie controle ----------------------------------------------------
    }

    if($fondskoersenControle)
    {
    // Voor welke fondsen zijn de koersen aangepast.  ----------------------------------------------------
    echo "<br> ".vt("fondskoersenControle")." $portefeuille ".vt("gestart")."."; flush();ob_flush();
        $query = "SELECT
              FROM_UNIXTIME(MIN(UNIX_TIMESTAMP(Fondskoersen.Datum))) AS vanafDatum
              FROM
              Fondskoersen
              JOIN Rekeningmutaties ON Rekeningmutaties.Fonds = Fondskoersen.Fonds
              JOIN Rekeningen ON Rekeningen.Rekening = Rekeningmutaties.Rekening
              WHERE Rekeningen.consolidatie=0 
              Fondskoersen.Datum <= '".$indexData['laatsteIndex']."' AND
              Fondskoersen.change_date > '".$indexData['laatsteIndexChange']."' AND
              Rekeningen.Portefeuille = '$portefeuille'";

      $this->DB->SQL($query);
	    $this->DB->Query();
	    if($debug)
	      echo "$query <br>\n";
      while($data = $this->DB->nextRecord())
      {
        if($data['vanafDatum'] != '')
        {
          echo "<br> ".vt("Fondskoersen voor")." $portefeuille ".vt("vanaf")." ".$data['vanafDatum'].".";flush();ob_flush();
          if(isset($herberekenen[$portefeuille]))
             $data['vanafDatum'] = jul2sql(min(db2jul($herberekenen[$portefeuille]),db2jul($data['vanafDatum'])));
          $herberekenen[$portefeuille]=$data['vanafDatum'];
        }
      }
    }


    // Eind Voor welke fondsen zijn de koersen aangepast.  ----------------------------------------------------


    if($valutakoersenContole)
    {
    echo "<br> ".vt("valutakoersenContole")." $portefeuille ".vt("gestart")."."; flush();ob_flush();
    // Voor welke veluta zijn de koersen aangepast.  ----------------------------------------------------
    $query = "SELECT
              FROM_UNIXTIME(MIN(UNIX_TIMESTAMP(Valutakoersen.Datum))) AS vanafDatum
              FROM
              Valutakoersen
              JOIN Rekeningmutaties ON Rekeningmutaties.Valuta = Valutakoersen.Valuta
              JOIN Rekeningen ON Rekeningen.Rekening = Rekeningmutaties.Rekening
              WHERE Rekeningen.consolidatie=0 AND 
              Valutakoersen.Datum <= '".$indexData['laatsteIndex']."' AND
              Valutakoersen.change_date > '".$indexData['laatsteIndexChange']."' AND
              Rekeningen.Portefeuille = '$portefeuille' ";
      $this->DB->SQL($query);
	    $this->DB->Query();
     	if($debug)
	      echo "$query <br>\n";
      while($data = $this->DB->nextRecord())
      {
        if($data['vanafDatum'] != '')
        {
          echo "<br> ".vt("Valutakoersen voor")." $portefeuille ".vt("vanaf")." ".$data['vanafDatum'].".";flush();ob_flush();
          if(isset($herberekenen[$portefeuille]))
             $data['vanafDatum'] = jul2sql(min(db2jul($herberekenen[$portefeuille]),db2jul($data['vanafDatum'])));
          $herberekenen[$portefeuille]=$data['vanafDatum'];

        }
      }
     }

     if($sectorPerFondsControle)
    {
    echo "<br> ".vt("sectorPerFondsControle")." $portefeuille ".vt("gestart")."."; flush();ob_flush();
    // BeleggingssectorPerFonds aangepast?  ----------------------------------------------------
    $query = "SELECT
              FROM_UNIXTIME(MIN(UNIX_TIMESTAMP(Rekeningmutaties.Boekdatum))) as vanafDatum
              FROM
              BeleggingssectorPerFonds
              JOIN Rekeningmutaties ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds
              JOIN  Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
              WHERE Rekeningen.consolidatie=0 
	            BeleggingssectorPerFonds.Vermogensbeheerder = '$vermogensbeheerder' AND
              Rekeningmutaties.boekdatum <=  '".$indexData['laatsteIndex']."' AND
              BeleggingssectorPerFonds.change_date >= '".$indexData['laatsteIndexChange']."' AND
              Rekeningen.Portefeuille = '$portefeuille'
              GROUP BY Rekeningen.Portefeuille ";
      $this->DB->SQL($query);
	    $this->DB->Query();
	    if($debug)
	      echo "$query <br>\n";
	    $fondsen=array();
      while($data = $this->DB->nextRecord())
      {
        echo "<br> ".vt("BeleggingssectorPerFonds voor")." $portefeuille ".vt("vanaf")." ".$data['vanafDatum'].".";flush();ob_flush();
        if(isset($herberekenen[$portefeuille]))
           $data['vanafDatum'] = jul2sql(min(db2jul($herberekenen[$portefeuille]),db2jul($data['vanafDatum'])));
        $herberekenen[$portefeuille]=$data['vanafDatum'];

      }
    }


  }

 $laatsteValuta = getLaatsteValutadatum();
 $laatsteValutaJul = db2jul($laatsteValuta);
  foreach ($herberekenen as $portefeuille=>$startDatum)
  {

      $attpdata=array();
      $attpdata['portefeuilleVan'] = $portefeuille;
      $attpdata['portefeuilleTm'] = $portefeuille;
      $attpdata['PerformanceBerekening'] = $pdata[$portefeuille]['PerformanceBerekening'];

      if(db2jul($pdata[$portefeuille]['Startdatum']) > db2jul($startDatum))
        $attpdata['datumVan'] = db2jul($pdata[$portefeuille]['Startdatum']);
      else
        $attpdata['datumVan'] = db2jul($startDatum);

      if($pdata[$portefeuille]['PerformanceBerekening'] == 6)
      {
        $attpdata['datumTm'] = mktime(0,0,0, (ceil(date('m',$laatsteValutaJul)/3)*3)-2,0,date('Y',$laatsteValutaJul));//date("Y-m-d");
      }
      else
      {
        $attpdata['datumTm'] = mktime(0,0,0,date('m',$laatsteValutaJul),0,date('Y',$laatsteValutaJul));//date("Y-m-d");
      }

      if($attpdata['datumVan'] != $attpdata['datumTm'])
      {
        if($pdata[$portefeuille]['PerformanceBerekening'] == 6)
        {
          $attpdata['datumVan'] = mktime(0,0,0,(ceil(date('m',$attpdata['datumVan'])/3)*3)-2,0,date('Y',$attpdata['datumVan'] ));
        }
        else
          $attpdata['datumVan'] = mktime(0,0,0,(date('m',$attpdata['datumVan'])),0,date('Y',$attpdata['datumVan'] ));

        if($attpdata['datumVan'] != $attpdata['datumTm'])
        {
          echo "<br> ".vt("portefeuille").": $portefeuille ".vt("bijwerken vanaf")." ".date("Y-m-d",$attpdata['datumVan'])."  ".vt("tot")." ".date("Y-m-d",$attpdata['datumTm'])." (".$pdata[$portefeuille]['PerformanceBerekening'].")";ob_flush(); flush();
          $index = new indexHerberekening($attpdata);
          $index->Bereken();
        }
      }
  }


	echo "<br><b>".vt("HistorischePortefeuilleIndex controle voor")." $vermogensbeheerder ".vt("klaar").".</b> ";
  return $return;
}

function check_valutaverschillen($vermogensbeheerder)
{
  echo "test";
  echo "<br><b>".vt("test voor")." $vermogensbeheerder .</b> ";

  $totdatum = getLaatsteValutadatum();
  $totJul = db2jul($totdatum);
  $startDatum=date("Y-01-01",$totJul);

  $query = "SELECT Portefeuilles.Portefeuille FROM Portefeuilles WHERE Portefeuilles.consolidatie=0 AND Portefeuilles.Vermogensbeheerder='$vermogensbeheerder' AND Portefeuilles.Einddatum > NOW()";
  $this->DB->SQL($query);
	$this->DB->Query();

	while($data = $this->DB->nextRecord())
	{
	  $portefeuilles[]=$data['Portefeuille'];
	}
	echo "<table><tr><td> ".vt("Portefeuille")." </td><td> ".vt("VV-resultaat")." <td></tr>";
	foreach($portefeuilles as $portefeuille)
	{

	  $startWaarden = berekenPortefeuilleWaarde($portefeuille,$startDatum,true,'EUR',$startDatum);
	  $eindWaarden = berekenPortefeuilleWaarde($portefeuille,$totdatum,false,'EUR',$startDatum);
	  $waardeMutatieTypen=array();
	  $waardeTotaal=array();

	  foreach ($startWaarden as $waarden)
	  {
	    $waardeMutatieTypen['start'][$waarden['type']] += $waarden['actuelePortefeuilleWaardeEuro']-$waarden['beginPortefeuilleWaardeEuro'];
	    $waardeTotaal['start'] += $waarden['actuelePortefeuilleWaardeEuro'];
	  }
	  foreach ($eindWaarden as $waarden)
	  {
	    $waardeMutatieTypen['eind'][$waarden['type']] += $waarden['actuelePortefeuilleWaardeEuro']-$waarden['beginPortefeuilleWaardeEuro'];
	    $waardeTotaal['eind'] += $waarden['actuelePortefeuilleWaardeEuro'];
	  }


	  $waardeMutatie 	   	= $waardeTotaal['eind'] - $waardeTotaal['start'];
		$stortingen 			 	= getStortingen($portefeuille,$startDatum,$totdatum,'EUR');
		$onttrekkingen 		 	= getOnttrekkingen($portefeuille,$startDatum,$totdatum,'EUR');
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

		$ongerealiseerdeResulaat=$waardeMutatieTypen['eind']['fondsen']-$waardeMutatieTypen['start']['fondsen'];
		$rente=$waardeMutatieTypen['eind']['rente']-$waardeMutatieTypen['start']['rente'];

		$gerealiseerdeKoersResultaat = gerealiseerdKoersresultaat($portefeuille, $startDatum, $totdatum,'EUR',true);

		$query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers ) - SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers ) AS totaal ".
		         "FROM Rekeningmutaties, Rekeningen, Portefeuilles WHERE ".
		  	     "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  	     "Rekeningen.Portefeuille = '$portefeuille' AND ".
		  	     "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  	     "Rekeningmutaties.Verwerkt = '1' AND ".
		  	     "Rekeningmutaties.Boekdatum > '$startDatum' AND ".
		  	     "Rekeningmutaties.Boekdatum <= '$totdatum' AND ".
			       "Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Opbrengst=1 OR Kosten=1)  ";
    $this->DB->SQL($query);
	  $this->DB->Query();
	  $data=$this->DB->nextRecord();
	  $totaalOpbrengstKosten=$data['totaal'];
	  $koersResulaatValutas = $resultaatVerslagperiode - ($totaalOpbrengstKosten+$ongerealiseerdeResulaat+$rente+$gerealiseerdeKoersResultaat);

  echo "<tr><td> $portefeuille </td><td align=right> ".round($koersResulaatValutas,2)."<td></tr> ";
	}
	echo "</table>";
  return true;
}

function check_rapport($vermogensbeheerder)
{
  global $__appvar;
  define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
  if($vermogensbeheerder== "HEN")
	{
    include_once("../classes/AE_cls_fpdf.php");
    include_once("rapport/PDFRapport.php");
    include_once("rapport/include/RapportPERF_L26.php");
    $returnWaarde=true;

    $db=new db();
    $query="SELECT Portefeuille,Startdatum FROM Portefeuilles WHERE Portefeuilles.consolidatie=0 AND Vermogensbeheerder='$vermogensbeheerder'";
    $db->SQL($query);
    $db->Query();
    while ($pdata=$db->nextRecord())
    {
      $portefeuille=$pdata['Portefeuille'];
      $pdf = new PDFRapport('L','mm');
      $pdf->rapportageValuta = "EUR";
	    $pdf->ValutaKoersEind  = 1;
      $pdf->ValutaKoersStart = 1;
      $pdf->ValutaKoersBegin = 1;
      loadLayoutSettings($pdf, $portefeuille);
      $startdatum=date("Y")."-01-01";
      $eindDatum=getLaatsteValutadatum();
      //$eindDatum='2011-07-01';

      $fondswaarden = berekenPortefeuilleWaarde($portefeuille,$startdatum,true,'EUR',$startdatum);
      vulTijdelijkeTabel($fondswaarden ,$portefeuille,$startdatum);
      $fondswaarden = berekenPortefeuilleWaarde($portefeuille,$eindDatum,true,'EUR',$startdatum);
      vulTijdelijkeTabel($fondswaarden ,$portefeuille,$eindDatum);

      $pdf->PortefeuilleStartdatum=$pdata['Startdatum'];
      $pdf->HENIndex=true;
      $rapport = new RapportPERF_L26($pdf, $portefeuille, $startdatum, $eindDatum);
	    $rapport->writeRapport();
      verwijderTijdelijkeTabel($portefeuille,$startdatum);
      verwijderTijdelijkeTabel($portefeuille,$eindDatum);
     // echo "$portefeuille <br>\n";
      foreach ($rapport->checks as $check=>$resultaat)
        if($resultaat[0] == false)
        {
          echo "<br>\n$portefeuille   $startdatum -> $eindDatum $check ".$resultaat[1]."";
          $returnWaarde=false;
        }
    //  listarray($rapport->checks);

    }
    return $returnWaarde;
	}
}

function check_Beurs($vermogensbeheerder)
{
  $return = true;
  $query = "SELECT Fondsen.Fonds,Fondsen.beurs
FROM Fondsen 
JOIN  FondsenPerVermogensbeheerder ON FondsenPerVermogensbeheerder.Fonds = Fondsen.Fonds AND FondsenPerVermogensbeheerder.Vermogensbeheerder = '".$vermogensbeheerder."'
WHERE (Fondsen.beurs IS NULL OR Fondsen.beurs='')";
	$this->DB->SQL($query);
	$this->DB->Query();
	while($data = $this->DB->nextRecord())
	{
		echo "<br>".vt("Fout").": ".vt("Fonds")." '".$data['Fonds']."' ".vt("heeft geen Fondsen.beurs")."heeft geen Fondsen.beurs.";
		$return = false;
	}
  return $return;
}

function check_BB_Landcodes($vermogensbeheerder)
{
  $return = true;
  $query = "SELECT Fondsen.Fonds,Fondsen.bbLandcode
FROM Fondsen 
JOIN  FondsenPerVermogensbeheerder ON FondsenPerVermogensbeheerder.Fonds = Fondsen.Fonds AND FondsenPerVermogensbeheerder.Vermogensbeheerder = '".$vermogensbeheerder."'
WHERE (Fondsen.bbLandcode IS NULL OR Fondsen.bbLandcode='')";
	$this->DB->SQL($query);
	$this->DB->Query();
	while($data = $this->DB->nextRecord())
	{
		echo "<br>".vt("Fout").": ".vt("Fonds")." '".$data['Fonds']."' ".vt("heeft geen Fondsen.bbLandcode").".";
		$return = false;
	}
  return $return;      
}


}

