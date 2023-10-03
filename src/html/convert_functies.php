<?php
/* 	
  AE-ICT source module
    Author                 : $Author: cvs $
    Laatste aanpassing     : $Date: 2013/12/16 08:20:59 $
    File Versie            : $Revision: 1.4 $
 		
    $Log: convert_functies.php,v $
    Revision 1.4  2013/12/16 08:20:59  cvs
    *** empty log message ***

    Revision 1.3  2011/07/19 14:33:11  cvs
    *** empty log message ***

    Revision 1.2  2011/07/16 09:50:39  cvs
    *** empty log message ***

    Revision 1.1  2011/06/22 11:47:03  cvs
    *** empty log message ***

 		
 	
*/

function depotBankSelector($huidigeWaarde)
{
  $db = new DB();
  $query = "SELECT Depotbank, Omschrijving FROM Depotbanken ORDER BY Depotbank";
  $db->executeQuery($query);
  while ($depotRec = $db->nextRecord())
  {
    $selected = ($depotRec["Depotbank"] == $huidigeWaarde)?"SELECTED":"";
    $out .= "<OPTION value='".$depotRec["Depotbank"]."' $selected>(".$depotRec["Depotbank"].") ".$depotRec["Omschrijving"]."</OPTION>\n";
  }
  return $out;
}

function vermogensbeheerderSelector($huidigeWaarde)
{
  $db = new DB();
  $query = "SELECT Vermogensbeheerder, Naam FROM Vermogensbeheerders ORDER BY Vermogensbeheerder";
  $db->executeQuery($query);
  while ($depotRec = $db->nextRecord())
  {
    $selected = ($depotRec["Vermogensbeheerder"] == $huidigeWaarde)?"SELECTED":"";
    $out .= "<OPTION value='".$depotRec["Vermogensbeheerder"]."' $selected>(".$depotRec["Vermogensbeheerder"].") ".$depotRec["Naam"]."</OPTION>\n";
  }
  return $out;
}

function checkAndAddPortefeuille($data,$dryrun=true)
{
  global $portefeuillesChecked, $USR, $dryrunPorteuille,$portefeuillesQueries;

  if (in_array($data["Portefeuille"], $portefeuillesChecked))
  {
    return false;
  }
    
  $db = new DB();
  $query = "SELECT * FROM Portefeuilles WHERE Portefeuille = '".$data["Portefeuille"]."' ";
  $db->executeQuery($query);
  $portRec = $db->lookupRecord();
  if ($portRec["id"] > 0)  // record gevonden in checkedarray plaatsen
  {
    $portefeuillesChecked[] = $data["Portefeuille"];
  }
  else
  {
    $query = "SELECT Accountmanager FROM  Accountmanagers WHERE Vermogensbeheerder = '".$data["vermogensbeheerder"]."' ";
    $db->executeQuery($query);
    $accManRec = $db->lookupRecord();
     
    $insertStr  = "  add_date            = NOW()";
    $insertStr .= ", add_user            = '$USR' ";
    $insertStr .= ", change_date         = NOW()";
    $insertStr .= ", change_user         = '$USR'";
    $insertStr .= ", Portefeuille        = '".$data["Portefeuille"]."' ";
    $insertStr .= ", PortefeuilleVoorzet = ''";
    $insertStr .= ", Vermogensbeheerder  = '".$data["vermogensbeheerder"]."' ";
    $insertStr .= ", Accountmanager      = '".$accManRec["Accountmanager"]."' ";
    $insertStr .= ", Client              = 'CLIENT_".$data["vermogensbeheerder"]."' ";
    $insertStr .= ", Depotbank           = '".$data["depotbank"]."' ";
    $insertStr .= ", Startdatum          = '".$data["datum"]."' - INTERVAL 1 DAY ";
    $insertStr .= ", Einddatum           = '2037-12-31' ";
    $insertStr .= ", RapportageValuta    = 'EUR' ";
    $query  = "INSERT INTO Portefeuilles SET ";
    $query .=  $insertStr;
    if ($dryrun)
    {
      $dryrunPorteuille[] = $data["Portefeuille"]." / ".$data["vermogensbeheerder"]." / ".$data["depotbank"];
      $portefeuillesChecked[] = $data["Portefeuille"];
      $portefeuillesQueries[] = $query;
    }
    else
    {
     
  //    echo "<br>".$query;
      if ($db->executeQuery($query)) 
        $portefeuillesChecked[] = $data["Portefeuille"];
    }
    
    
     
  }
  
}

function checkAndAddRekening($data,$dryrun=true)
{
  global $rekeningNrsChecked, $USR, $dryrunRekeningnr, $rekeningNrsQueries;
  $reknr = trim($data["Portefeuille"]).$data["fondsValuta"];
  if (in_array($reknr, $rekeningNrsChecked))
  {
    return false;
  }
    
  $db = new DB();
  $query = "SELECT * FROM Rekeningen WHERE Rekening = '".$reknr."' ";
  $db->executeQuery($query);
  $portRec = $db->lookupRecord();
  if ($portRec["id"] > 0)  // record gevonden in checkedarray plaatsen
  {
    $rekeningNrsChecked[] = $reknr;
  }
  else
  {
    $insertStr  = "  add_date            = NOW()";
    $insertStr .= ", add_user            = '$USR' ";
    $insertStr .= ", change_date         = NOW()";
    $insertStr .= ", change_user         = '$USR'";
    $insertStr .= ", Rekening            = '".$reknr."' ";
    $insertStr .= ", Portefeuille        = '".$data["Portefeuille"]."' ";
    $insertStr .= ", Tenaamstelling      = '((".$data["vermogensbeheerder"].")) $reknr'";
    $insertStr .= ", Valuta              = '".$data["fondsValuta"]."' ";
    
    $query  = "INSERT INTO Rekeningen SET ";
    $query .=  $insertStr;
 //   echo "<br>". $query;
    if ($dryrun)
    {
      $dryrunRekeningnr[] = $data["Portefeuille"]." / ".$data["fondsValuta"]." / ((".$data["vermogensbeheerder"].")) $reknr";
      $rekeningNrsChecked[] = $reknr;
      $rekeningNrsQueries[] =  $query;
    }
    else
    {
      if ($db->executeQuery($query))
      { 
        $rekeningNrsChecked[] = $reknr;
        checkAndAddPortefeuille($data);
      }
    }  
     
  }
}  

function CheckAndAddBeleggingscategoriePerFonds($fonds, $vermogensbeheerder,$dryrun=true)
{
  global $beleggingscategoriePerFondsChecked, $USR, $dryrunBeleggingscategorie, $beleggingscategorieQueries;
  $zoekKey = $fonds.$vermogensbeheerder;
  if (in_array($zoekKey, $beleggingscategoriePerFondsChecked))
  {
    return false;
  }
    
  $db = new DB();
  $query = "SELECT * FROM BeleggingscategoriePerFonds WHERE Fonds = '".$fonds."'  AND Vermogensbeheerder = '".$vermogensbeheerder."' ";
  $db->executeQuery($query);
  $portRec = $db->lookupRecord();
  if ($portRec["id"] > 0)  // record gevonden in checkedarray plaatsen
  {
    $beleggingscategoriePerFondsChecked[] = $zoekKey;
  }
  else
  {
    $insertStr  = "  add_date            = NOW()";
    $insertStr .= ", add_user            = '$USR' ";
    $insertStr .= ", change_date         = NOW()";
    $insertStr .= ", change_user         = '$USR'";
    $insertStr .= ", Vermogensbeheerder  = '".$vermogensbeheerder."' ";
    $insertStr .= ", Fonds               = '".$fonds."' ";
    $insertStr .= ", Beleggingscategorie = 'AAND' ";
        
    $query  = "INSERT INTO BeleggingscategoriePerFonds SET ";
    $query .=  $insertStr;
    
    if ($dryrun)
    {
      $dryrunBeleggingscategorie[] = $zoekKey;
      $beleggingscategoriePerFondsChecked[] = $zoekKey;
      $beleggingscategorieQueries[] = $query;
    }
    else
    {
      if ($db->executeQuery($query))
      { 
        $beleggingscategoriePerFondsChecked[] = $zoekKey;
      }
    }  
     
  }
}

?>