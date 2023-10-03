<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/03/02 11:02:36 $
 		File Versie					: $Revision: 1.16 $

 		$Log: AE_cls_lookup.php,v $
 		Revision 1.16  2020/03/02 11:02:36  cvs
 		call 8434
 		
 		Revision 1.15  2019/12/09 09:56:41  cvs
 		update 6-11-2019
 		
 		Revision 1.14  2019/07/01 13:11:24  cvs
 		call 7743
 		
 		Revision 1.13  2018/09/07 10:17:27  cvs
 		commit voor robert call 6989
 		
 		Revision 1.12  2018/07/04 16:32:26  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2018/07/03 14:25:55  cvs
 		call 7010
 		
 		Revision 1.10  2017/04/13 13:31:50  cvs
 		typo ubscode
 		
 		Revision 1.9  2017/04/13 13:28:37  cvs
 		call 5639
 		
 		Revision 1.8  2017/04/13 13:12:43  cvs
 		call 5767
 		
 		Revision 1.7  2017/02/22 07:39:35  cvs
 		call 5571
 		
 		Revision 1.6  2016/10/27 07:05:31  cvs
 		no message
 		
 		Revision 1.5  2016/03/16 12:54:08  cvs
 		call 4747
 		
 		Revision 1.4  2015/03/16 12:40:39  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2014/04/04 09:04:55  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2013/12/16 08:23:02  cvs
 		*** empty log message ***

 		Revision 1.1  2013/12/11 10:09:14  cvs
 		*** empty log message ***

 		Revision 1.1  2012/10/24 14:34:45  cvs
 		start project




*/
class AE_lookup
{
  var $user;
  var $server;

  function AE_lookup()
  {
    global $USR;
	  $this->user = $USR;
    $this->server = 1;
  }




  /**
   * Haal dagboek gegevens op
   *
   * @param "search" values voor rekening en depotbank
   * @param "part" defineer output
   */
  function getRekening($search,$part="all")
  {
    $db = new DB($this->server);
    
    if (!is_array($search) )
    {
      echo "LKP->GETREKENING: pas de invoer aan naar de nieuwe methodiek";
      exit;
    }
    else
    {
      if ($search["rekening"] == "" OR $search["depotbank"] == "") 
      {
        debug($search,"Fout reknr of depotbank ontbreekt");
        exit;
      }
    }
    
    $q = "SELECT
            Rekeningen.*,
            Portefeuilles.Depotbank as bank,
            Portefeuilles.SoortOvereenkomst,
            Portefeuilles.InternDepot,
            Portefeuilles.Remisier,
            Portefeuilles.PortefeuilleDepotbank
          FROM
            Rekeningen
          LEFT JOIN Portefeuilles ON
            Rekeningen.Portefeuille = Portefeuilles.Portefeuille
          WHERE
          Rekeningen.Depotbank = '".$search["depotbank"]."' AND 
          Rekeningen.consolidatie = 0 AND 
          (`Rekening` = '".$search["rekening"]."' OR `RekeningDepotbank` = '".$search["rekening"]."')";
    
    if ($rec = $db->lookupRecordByQuery($q) )
    {
      if ($part == "all")
      {
        if (trim($rec["typeRekening"]) == "") $rec["typeRekening"] = "Ontbreekt";
        return $rec;
      }
      else
        return $rec[$part];
    }
    else
      return false;
  }

  function getData($table, $search, $part="all")
  {
    $db = new DB($this->server);
    $q = "SELECT * FROM `$table` WHERE $search";

    if ($rec = $db->lookupRecordByQuery($q) )
    {
      if ($part == "all")
        return $rec;
      else
        return $rec[$part];
    }
    else
      return false;
  }

  function getPortefeuille($search,$part="all")
  {
    $db = new DB($this->server);
    $q = "SELECT Rekeningen.*, Portefeuilles.Depotbank as bank FROM Rekeningen
    LEFT JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille WHERE $search";
    
    if ($rec = $db->lookupRecordByQuery($q) )
    {
      if ($part == "all")
        return $rec;
      else
        return $rec[$part];
    }
    else
      return false;
  }

  function getAdventInfoByEffectenPositie($code,$bank="aab",$part="all")
  {
    $db = new DB($this->server);

    $q= "
    SELECT
      Fondsen.*,
      advent_FondsMapping.adventCode,
      advent_FondsMapping.adventSecCode,
      advent_FondsMapping.Omschrijving,
      advent_FondsMapping.Fonds
    FROM
      advent_FondsMapping
    INNER JOIN Fondsen ON
       Fondsen.Fonds = advent_FondsMapping.Fonds
    WHERE
      ";



    switch  ($bank)
    {
      case "ISIN":
        $parts = explode("|", $code);
        $q .= "Fondsen.ISINCode = '{$parts[0]}' AND Fondsen.Valuta = '{$parts[1]}'";
        $fieldCheck = "ISINCode";
        break;
      case "AAB":
        $rec = false;
        $parts = explode("|", $code);

        $useISIN = true;
        if (trim($parts[2]) <> "")  //AABcode gevonden
        {
          $q2 = "Fondsen.AABCode = '".trim($parts[2])."' OR Fondsen.ABRCode = '".trim($parts[2])."'";
          $fieldCheck = "AABCode";

          if ($rec = $db->lookupRecordByQuery($q.$q2) )
          {
            $rec["adventSecCodeValuta"] = $rec["adventSecCode"].strtolower(substr($rec["Valuta"],0,2));
//            $useISIN = false;
//            $q .= $q2;
          }
        }

//        if ($useISIN)
//        {
//          $q .= "Fondsen.ISINCode = '{$parts[0]}' AND Fondsen.Valuta = '{$parts[1]}'";
//          $fieldCheck = "ISINCode";
//
//        }
        return $rec;
        break;
      case "BINCK":
        $parts = explode("|", $code);
        $useISIN = true;
        if (trim($parts[2]) <> "")  //BINCKcode gevonden
        {
          $q2 = "Fondsen.binckCode = '".trim($parts[2])."' ";  // eventuele voorloop nullen verwijderen
          $fieldCheck = "binckCode";
       
          if ($rec = $db->lookupRecordByQuery($q.$q2) ) 
          {
           $useISIN = false;
           $q .= $q2; 
          }
        }
        
        if ($useISIN)
        {
          $q .= "Fondsen.ISINCode = '{$parts[0]}' AND Fondsen.Valuta = '{$parts[1]}'";
          $fieldCheck = "ISINCode";
          
        }
        break;
      case "TGB":
        $parts = explode("|", $code);
        $useISIN = true;
        if (trim($parts[2]) <> "")  //TGBcode gevonden
        {
          $q2 = "CAST(Fondsen.StroeveCode AS SIGNED) = '".(int)$parts[2]."' ";  // eventuele voorloop nullen verwijderen
          $fieldCheck = "stroeveCode";

          if ($rec = $db->lookupRecordByQuery($q.$q2) )
          {
           $useISIN = false;
           $q .= $q2;
          }
        }

        if ($useISIN)
        {
          $q .= "Fondsen.ISINCode = '{$parts[0]}' AND Fondsen.Valuta = '{$parts[1]}'";
          $fieldCheck = "ISINCode";

        }
        break;
      case "FVL":
        $parts = explode("|", $code);
        $useISIN = true;
        if (trim($parts[2]) <> "")  //TGBcode gevonden
        {

          $q2 = "CAST(Fondsen.FVLCode AS SIGNED) = '".(int)$parts[2]."' ";  // eventuele voorloop nullen verwijderen
          $fieldCheck = "FVLCode";

          if ($rec = $db->lookupRecordByQuery($q.$q2) )
          {
            $useISIN = false;
            $q .= $q2;
          }
        }

        if ($useISIN)
        {
          $q .= "Fondsen.ISINCode = '{$parts[0]}' AND Fondsen.Valuta = '{$parts[1]}'";
          $fieldCheck = "ISINCode";

        }
        break;
      case "LOM":
        $parts = explode("|", $code);
        $useISIN = true;
        if (substr($parts[3],0,12) == "LOMBARD LOAN")
        {

          switch ($parts[1])
          {
            case "CHF":
              $q .= "Fondsen.Fonds = 'LODHlenCHF' AND Fondsen.Valuta = '{$parts[1]}'";
              break;
            case "EUR":
              $q .= "Fondsen.Fonds = 'LODHLening' AND Fondsen.Valuta = '{$parts[1]}'";
              break;
            default:
              $q .= "Fondsen.Fonds = 'leningOnbekend'";
              break;
          }

          $parts[0] = "leningDummy";

        }
        else
        {
          if (trim($parts[2]) <> "")  //LOMcode gevonden
          {
            $q2 = "CAST(Fondsen.LomCode AS SIGNED) = '".(int)$parts[2]."' ";  // eventuele voorloop nullen verwijderen
            $fieldCheck = "LomCode";

            if ($rec = $db->lookupRecordByQuery($q.$q2) )
            {
              $useISIN = false;
              $q .= $q2;
            }
          }

          if ($useISIN)
          {
            $q .= "Fondsen.ISINCode = '{$parts[0]}' AND Fondsen.Valuta = '{$parts[1]}'";
            $fieldCheck = "ISINCode";

          }
        }

        break;
      case "SAXO":
        $parts = explode("|", $code);
        $useISIN = true;
        if (trim($parts[2]) <> "")  //Saxocode gevonden
        {
          $q2 = "Fondsen.SAXOcode = '".trim($parts[2])."' ";
          $fieldCheck = "SAXOcode";

          if ($rec = $db->lookupRecordByQuery($q.$q2) )
          {
            $useISIN = false;
            $q .= $q2;
          }
        }

        if ($useISIN)
        {
          $q .= "Fondsen.ISINCode = '{$parts[0]}' AND Fondsen.Valuta = '{$parts[1]}'";
          $fieldCheck = "ISINCode";

        }
        break;
      case "UBS":
        $parts = explode("|", $code);
        $useISIN = true;
        if (trim($parts[2]) <> "")  //UBScode gevonden
        {

          $q2 = "CAST(Fondsen.UBScode AS SIGNED) = '".(int)$parts[2]."' ";  // eventuele voorloop nullen verwijderen
          $fieldCheck = "UBScode";

          if ($rec = $db->lookupRecordByQuery($q.$q2) )
          {
            $useISIN = false;
            $q .= $q2;
          }
        }

        if ($useISIN)
        {
          $q .= "Fondsen.ISINCode = '{$parts[0]}' AND Fondsen.Valuta = '{$parts[1]}'";
          $fieldCheck = "ISINCode";

        }
        break;
      case "UBSL":
        $parts = explode("|", $code);
        $useISIN = true;
        //aetodo: USBL code nog toevoegen aan fondsen
        if (trim($parts[2]) <> "")  //UBScode gevonden
        {

          $q2 = "Fondsen.UBScode = '".$parts[2]."' ";
          $fieldCheck = "UBSLcode";

          if ($rec = $db->lookupRecordByQuery($q.$q2) )
          {
            $useISIN = false;
            $q .= $q2;
          }
        }

        if ($useISIN)
        {
          $q .= "Fondsen.ISINCode = '{$parts[0]}' AND Fondsen.Valuta = '{$parts[1]}'";
          $fieldCheck = "ISINCode";

        }

        break;
      default:
        $q .= "Fondsen.AABCode = '$code' OR Fondsen.ABRCode = '$code' ";
        $fieldCheck = "AABCode";
    }

    if ($useISIN AND trim($parts[0]) == "")  // cal 5571 afbreken wanneer bankcode onbekend en ISIN niet gevuld
    {
      return false;
    }


    if ($rec = $db->lookupRecordByQuery($q) )
    {
      
//      if ($rec[$fieldCheck] == "")
//      {
//        return false;
//        exit;
//      }
      $rec["adventSecCodeValuta"] = $rec["adventSecCode"].strtolower(substr($rec["Valuta"],0,2));
      if ($part == "all")
        return $rec;

      else
        return $rec[$part];
    }
    else
      return false;
  }

  function getFonds($fonds,$part="all")
  {
    $db = new DB($this->server);
    $q = "SELECT * FROM Fondsen WHERE Fonds='$fonds' ";
    if ($rec = $db->lookupRecordByQuery($q) )
    {
      if ($part == "all")
        return $rec;
      else
        return $rec[$part];
    }
    else
      return false;
  }

  function getAdventMapping($search,$part="all")
  {
    $db = new DB($this->server);
    $q = "SELECT * FROM advent_FondsMapping WHERE $search";
    if ($rec = $db->lookupRecordByQuery($q) )
    {
      if ($part == "all")
        return $rec;
      else
        return $rec[$part];
    }
    else
      return false;
  }

}

?>