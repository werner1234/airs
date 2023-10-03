<?php

class AE_RekeningMutaties
{

  var $volgNummer = 0;

  function AE_RekeningMutaties()
  {
    $this->db = new DB();
    $this->AE_Message = new AE_Message();
    $this->AETemplate = new AE_template();
    $this->AEDate = new AE_datum();
    $this->AELog = new AE_Log();
  }

  /**
   * Ophalen afschrift
   * @author RM
   * @since 11-9-2014
   * 
   * @param type $copyId
   * @return null
   */
  function getCopyData($copyId, $table = 'Rekeningafschriften')
  {
    $db = new DB();
    // afschriftgegevens ophalen
    $copyQuery = "SELECT
      Rekeningen.Valuta, 
      Rekeningen.Memoriaal, 
      $table.* 
      FROM 
        $table, 
        Rekeningen 
      WHERE $table.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0 AND $table.id = '" . $copyId . "'
    ";

    $db->SQL($copyQuery);
    $db->Query();
    $afschrift = $db->NextRecord();

    if (!empty($afschrift))
    {
      $afschrift['Saldo'] = round($afschrift['Saldo'], 2);
      $afschrift['NieuwSaldo'] = round($afschrift['NieuwSaldo'], 2);

      $afschrift['aRekening'] = $afschrift["Rekening"];
      $afschrift['aValuta'] = $afschrift["Valuta"];
      $afschrift['aDatum'] = jul2form(db2jul($afschrift["Datum"]));
      $afschrift['aAfschriftnummer'] = $afschrift["Afschriftnummer"];

      if (empty($afschrift['Memoriaal']))
      {
        $afschrift['txtSaldo'] = "Saldo";
        $afschrift['txtNieuwSaldo'] = "Nieuw Saldo";
        $afschrift['aSaldo'] = $afschrift["Saldo"];
        $afschrift['aNieuwSaldo'] = $afschrift["NieuwSaldo"];
      }

      $afschrift['aMemoriaal'] = $afschrift['Memoriaal'];
      $afschrift['aId'] = $afschrift["id"];
      $afschrift['aTotaal'] = round(($afschrift["NieuwSaldo"] - $afschrift["Saldo"]), 2);
    }
    else
    {
      $afschrift = null;
    }

    return $afschrift;
  }

  /**
   * Bereken het mutatie verschil van een afschrift/rekening nummer
   * @param type $data
   * @param type $type
   * @return type
   */
  function getMutationSum($data, $type = '')
  {
    $table = 'Rekeningmutaties';
    if ( $type === 'temp' ) {$table = 'VoorlopigeRekeningmutaties';}
    
    $db = new DB();
    $totalQuery = "
      SELECT SUM(ROUND(Bedrag,2)) AS Totaal 
      FROM `" . $table . "` 
      WHERE Afschriftnummer = '" . $data['Afschriftnummer'] . "'
      AND Rekening = '" . $data['Rekening'] . "'
    ";
    $db->SQL($totalQuery);
    $db->Query();
    $totaal = $db->NextRecord();
    // Reken mutatieverschil uit
    $total['mutatieVerschil'] = $data['aTotaal'] - round($totaal['Totaal'], 2);

    return $total;
  }
  

  /**
   * balance account
   * @param type $data
   */
  function balanceAccount($data, $type)
  {
    global $USR;
    $DB = new DB();
    $_GET['action'] = 'update';
    
    $calculateDb = 'Rekeningmutaties';
    $updateDb = 'Rekeningafschriften';
    if ( $type === 'temp' ) {
      $calculateDb = 'VoorlopigeRekeningmutaties';
      $updateDb = 'VoorlopigeRekeningafschriften';
    }

    $balanceAccountQuery = ' 
      SELECT 
      SUM(ROUND(Bedrag,2)) AS totaal 
      FROM '.$calculateDb.' 
      WHERE Afschriftnummer = "' . $data['Afschriftnummer'] . '"
      AND Rekening = "' . $data['Rekening'] . '"
    ';

    $DB->SQL($balanceAccountQuery);
    $DB->Query();
    $totaal = $DB->NextRecord();
    $mutatie = round($totaal['totaal'], 2);

    $newbalanceQuery = '
      UPDATE `'.$updateDb.'` 
        SET NieuwSaldo=ROUND(Saldo + ' . $mutatie . ',2),
          change_date=NOW(),
          change_user= "' . $USR . '" 
            
        WHERE `'.$updateDb.'`.id = "' . $data['afschrift_id'] . '"
    ';
    $DB->SQL($newbalanceQuery);
    $DB->Query();
  }
  
  /**
   * Tegenregel voor B boekingen
   * @param type $rekening
   * @param type $afschrift
   */
  function balanceStartTransaction($rekening, $afschrift, $object, $table, $type) {
    $rekeningmutaties = $object;
    
    /** Haal eerst B boekingen op en kijk of er wat openstaat **/
    $beginboeking = $rekeningmutaties->parseBySearch(
      array(
        'Afschriftnummer'   => $afschrift, 
        'Rekening'          => $rekening, 
        'Transactietype'    => 'B',
        'Grootboekrekening' => 'FONDS'
      ), 
      array(
        'SUM(ROUND(`Bedrag`,2)) AS `Totaal`'
      )
    );

    //haal tegenboekingen op
    $tegenregel = $rekeningmutaties->parseBySearch(
      array(
        'Afschriftnummer'   => $afschrift, 
        'Rekening'          => $rekening, 
        'Grootboekrekening'    => 'VERM'
      ), 
      array(
        'SUM(ROUND(`Bedrag`,2)) AS `Totaal`'
      )
    );
  
    $mutatie = abs(round($beginboeking['Totaal'] + $tegenregel['Totaal'], 2));
  
    if ( $mutatie != 0 ) {
      /** get latest boekdatum **/
      $latestDateQuery = 'SELECT max(`Boekdatum`) AS `Boekdatum`
        FROM `' . $table . '`
        WHERE `Afschriftnummer` = "'.$afschrift.'"
        AND `Rekening` = "'.$rekening.'"
        AND `Transactietype` = "B"
      ';
      
      $latestDate = $this->db->lookupRecordByQuery($latestDateQuery);
      $latestDate = date('Y-m-d', db2jul($latestDate['Boekdatum']));
      
      
      $query = "
        INSERT INTO `" . $table . "` SET
          `Boekdatum` = '" . $latestDate . "',
          `Afschriftnummer` = '" . $afschrift . "',
          `Rekening` = '" . $rekening . "',
          `Valuta` = 'EUR',  
          `add_user` = '" . $_SESSION['usersession']['user'] . "',
          `add_date` = '" . date('Y-m-d H:i:s') . "',
          `change_user` = '" . $_SESSION['usersession']['user'] . "',
          `change_date` = '" . date('Y-m-d H:i:s') . "',
          `Volgnummer` = '" . $this->getVolgnummer($rekening, $afschrift, $type) . "',
          `Omschrijving` = 'Inbreng begingegevens',
          `Verwerkt` = 0,
          `Grootboekrekening` = 'VERM',
          `Debet` = 0,
          `Credit` = '" . abs($mutatie) . "',
          `Bedrag` = '" . abs($mutatie) . "',
          `Valutakoers` = '1'
      ";
      /** save query * */
      if ($this->db->executeQuery($query))
      {
        $this->AE_Message->setFlash('Tegenregel Begin boeking is aangemaakt', 'success');
      }
      else
      {
        $this->AE_Message->setFlash('Tegenregel Begin boekingen kon niet worden aangemaakt', 'error');
      }
    }
  }

  /**
   * Create an memoriaal counter rule
   * 
   * @param type $rekening Rekening
   * @param type $afschrift Afschriftnummer
   */
  function balenceMemoriaalAccount($rekening, $afschrift, $type)
  {
    $rekeningmutaties = new Rekeningmutaties();
    $table = $rekeningmutaties->data['table'];

    if ( $type === 'temp' ) {
      $rekeningmutaties = new VoorlopigeRekeningmutaties ();
      $table = $rekeningmutaties->data['table'];
    }
    
    /** Haal eerst B boekingen op en kijk of er wat openstaat **/
    $this->balanceStartTransaction($rekening, $afschrift, $rekeningmutaties, $table, $type);
    
    $totaal = $rekeningmutaties->parseBySearch(
      array (
        'Afschriftnummer' => $afschrift,
        'Rekening'        => $rekening
      ),
      array (
        'SUM(ROUND(`Bedrag`,2)) AS `Totaal`'
      )
    );

//    $getMutationsTotalQuery = $rekeningmutaties->getQueryTemplate('getMutationsTotalByCopyAndAccount.sql', array(
//      'copy'      => $afschrift,
//      'account'   => $rekening
//    )); debug($getMutationsTotalQuery);
//    
//    $totaal = $this->db->lookupRecordByQuery($getMutationsTotalQuery);
    $mutatie = round($totaal['Totaal'], 2);

    
    if ($mutatie != 0)
    {
      $mutatie = number_format($mutatie, 2, '.', '');

      /** get latest boekdatum **/
      $latestDateQuery = 'SELECT max(`Boekdatum`) AS `Boekdatum`
        FROM `' . $table . '`
        WHERE `Afschriftnummer` = "'.$afschrift.'"
        AND `Rekening` = "'.$rekening.'"
      ';
      
      $latestDate = $this->db->lookupRecordByQuery($latestDateQuery);
      $latestDate = date('Y-m-d', db2jul($latestDate['Boekdatum']));
      
      $query = "
        INSERT INTO `" . $table . "` SET
          `Boekdatum` = '" . $latestDate . "',
          `Afschriftnummer` = '" . $afschrift . "',
          `Rekening` = '" . $rekening . "',
          `Valuta` = 'EUR',  
          `add_user` = '" . $_SESSION['usersession']['user'] . "',
          `add_date` = '" . date('Y-m-d H:i:s') . "',
          `change_user` = '" . $_SESSION['usersession']['user'] . "',
          `change_date` = '" . date('Y-m-d H:i:s H:i:s') . "',
          `Volgnummer` = '" . $this->getVolgnummer($rekening, $afschrift, $type) . "',
          `Omschrijving` = 'Div. deponeringen/lichtingen',
          `Verwerkt` = 0,
      ";
      
      if ($mutatie > 0) {
        $query .= "
          `Grootboekrekening` = 'ONTTR',
          `Debet` = '" . abs($mutatie) . "',
          `Bedrag` = '" . - abs($mutatie) . "',
          `Valutakoers` = '1'
        ";
      } else {
        $query .= "
          `Grootboekrekening` = 'STORT',
          `Credit` = '" . abs($mutatie) . "',
          `Bedrag` = '" . abs($mutatie) . "',
          `Valutakoers` = '1'
        ";
      }

      /** save query **/
      if ($this->db->executeQuery($query))
      {
        $this->AE_Message->setFlash('Tegenregel is aangemaakt', 'success');
      }
      else
      {
        $this->AE_Message->setFlash('Tegenregel kon niet worden aangemaakt', 'error');
      }
    }
    else
    {
      $this->AE_Message->setFlash('Tegenregel kon niet worden aangemaakt, totaal open staande bedrag is 0', 'info');
    }
  }

  /**
   * get next volgnummer first check 
   * @param type $rekening
   * @param type $afschrift
   * @return type
   */
  function getVolgnummer($rekening, $afschrift, $type = '')
  {
    if ($this->volgNummer == 0)
    {
      $table = 'Rekeningmutaties';
      if ( $type === 'temp' ) {
        $table = 'VoorlopigeRekeningmutaties';
      }
      
      $this->db->SQL("SELECT 
        (Volgnummer+1) AS Volgende

        FROM `" . $table . "`
        WHERE `Afschriftnummer` = '" . $afschrift . "' AND `" . $table . "`.`Rekening` = '" . $rekening . "' 
        ORDER BY Volgnummer DESC LIMIT 1");
      $this->db->Query();
      if ($this->db->Records() > 0)
      {
        $volgnr = $this->db->NextRecord();
        $this->volgNummer = $volgnr['Volgende'];
      }
      else
      {
        $this->volgNummer++;
      }
    }
    else
    {
      $this->volgNummer++;
    }
    return $this->volgNummer;
  }

  function getAccountsForClient($clientId, $memoriaal = 0, $fields = null)
  {
    
    $queryFields = '*';
    if (!empty($fields) && is_array($fields))
    {
      $queryFields = implode(",", $fields);
    }

    $DB = new DB();
    $query = $this->AETemplate->parseFile('rekeningmutaties/query/getRekeningenMain.sql', array(
      'clientId'  => $clientId,
      'memoriaal' => $memoriaal,
      'fields'    => $queryFields
    ));

    $DB->SQL($query);
    $DB->Query();

    $accountData = array();
    while ($row = $DB->NextRecord())
    {
      $accountData[] = $row;
    }
    return $accountData;
  }

  
  
  /**
   * checkMutationsForErrors
   * Function to check mutation lines for errors
   */
  // local functions

  function checkMutationsForErrors($data)
  {
    $dblkup = new DB();
    $triggers = "";
    
    if ( ! isset($data["fondssoort"]["value"]) ) {$data["fondssoort"]["value"] = '';}
    if ( ! isset($data["regelnr"]["value"]) ) {$data["regelnr"]["value"] = '';}
    // prio 8
    $boekDateParts = explode("-", $data["Boekdatum"]["value"]);
    $addDateParts = explode("-", $data["add_date"]["value"]);
    
    $t = array(
      $data["add_date"]["value"],
      $this->_julDag($data["add_date"]["value"]),
      $data["Boekdatum"]["value"],
      $this->_julDag($data["Boekdatum"]["value"])
    );

    if ( 
      ( 
        $boekDateParts[1] <> $addDateParts[1]    // maanden ongelijk
        AND $addDateParts[2] > 6                     // boekdatm > 6
      ) 
      OR
      (
      $this->_julDag($data["add_date"]["value"]) - $this->_julDag($data["Boekdatum"]["value"])  > 31
      )
    ) {
      $data["tr_class"] = "list_dataregel_blauw";
      $triggers .= "/8";
    }


    // prio 7
    if ($this->passedEinddatum($data["einddatum"]["value"]) )
    {
      $data["tr_class"] = "list_dataregel_maroon";
      $triggers .= "/7";
    }
    
    
    // prio 6
    if (
      $data["Grootboekrekening"]["value"] == "FONDS" AND
      $data["fondssoort"]["value"] == "OPT" AND 
      (
        $data["Transactietype"]["value"] <> "A/O" AND
        $data["Transactietype"]["value"] <> "A/S" AND
        $data["Transactietype"]["value"] <> "V/O" AND
        $data["Transactietype"]["value"] <> "V/S"
      )
    ) {
      $data["tr_class"] = "list_dataregel_cyaan";
      $triggers .= "/6";
    }
    // prio 5
    if (
      (
        $data["Grootboekrekening"]["value"] == "FONDS" AND
        $data["fondssoort"]["value"] <> "STOCKDIV" AND 
        ( $data["Transactietype"]["value"] == "D" OR $data["Transactietype"]["value"] == "L") AND
        $data["Fondskoers"]["value"] == "0"
      )
      OR (
        $data["Grootboekrekening"]["value"] == "FONDS" AND
        $data["fondssoort"]["value"] == "STOCKDIV" AND 
        ( $data["Transactietype"]["value"] == "D" OR $data["Transactietype"]["value"] == "L") AND
        $data["Fondskoers"]["value"] <> "0"
      )
    )
    {
      $data["tr_class"] = "list_dataregel_groen";
      $triggers .= "/5";
    }
    // prio 4
    if ($data["Aantal"]["value"] == 0 AND $data["Grootboekrekening"]["value"] == "FONDS")
    {
      $data["tr_class"] = "list_dataregel_oranje";
      $triggers .= "/4";
    }


    // prio 3
    if ($data["Grootboekrekening"]["value"] == "FONDS" AND $data["Fondskoers"]["value"] <> "0")
    {
      $query = "SELECT Koers FROM Fondskoersen WHERE Fonds = '" . $data["Fonds"]["value"] . "' AND Datum <= NOW() ORDER BY Datum DESC";
      $tmpRec = $dblkup->lookupRecordByQuery($query);

      if ($tmpRec["Koers"] / $data["Fondskoers"]["value"] >= 9.5)
      {
        $data["AIRSkoers"]["value"] = $tmpRec["Koers"];
        $data["tr_class"] = "list_dataregel_geel";
        $triggers .= "/3";
      }
    }
    // prio 2
    if (
        ( $data["Valuta"]["value"] <> "EUR" AND $data["Valutakoers"]["value"] == 1) 
        OR ( $data["Valuta"]["value"] == "EUR" AND $data["Valutakoers"]["value"] <> 1) 
        OR ( $data["Valutakoers"]["value"] == 0 ) 
        OR ( $data["Valuta"]["value"] == "???" ) 
        OR ( trim($data["Valuta"]["value"]) == "" )
    ) {
      $data["tr_class"] = "list_dataregel_rood";
      $triggers .= "/2";
    }

    if (
      (
        substr($data["Rekening"]["value"], -3) <> "EUR" AND
        substr($data["Rekening"]["value"], -3) <> "MEM" AND
        substr($data["Rekening"]["value"], -3) <> "DEP"
      ) 
      AND $data["Valuta"]["value"] == "EUR"
    ) {
      $data["tr_class"] = "list_dataregel_rood";
      $triggers .= "/2";
    }

    // prio 1
    if ($data["regelnr"]["value"] == -99)
    {
      $used = false;
      for ($ts = 0; $ts < count($colorArray); $ts++)
      {
        if (stristr($data["Omschrijving"]["value"], $colorArray[$ts]))
        {
          $data["tr_class"] = "list_dataregel_rose";
          $used = true;
        }
      }
      // **datum
      if (substr($data["Omschrijving"]["value"],0,2) == "**" AND
          substr($data["Omschrijving"]["value"],15,2) == "//" )
      {
        $data["tr_class"] = "list_dataregel_rose";
        $used = true;
        $colorArray[] = "combi ** //";
      }
      
      if ($used)
      {
        $triggers .= "/1";
      }
    }
    if (stristr($data["Omschrijving"]["value"], "KOSTEN ") and
            $data["Grootboekrekening"]["value"] == "ONTTR")
    {
      $data["tr_class"] = "list_dataregel_rose";
      $triggers .= "/1";
    }
    $data["triggers"]["value"] = $triggers;

    return $data;
  }
  
  function _julDag($dbDatum)
  {
    $timestamp = db2jul($dbDatum);
    $julian = mktime(1,1,1,date('m', $timestamp),date('d', $timestamp),date('Y', $timestamp));
    
//    $parts = explode("-",$dbDatum);
//    debug($parts);
//    $julian = mktime(1,1,1,$parts[1],$parts[2],$parts[0]);
//    debug($julian);
    return floor($julian / 86400);
  }
  
  // local functions
  function passedEinddatum($dbdate)
  {
    $parts = explode("-", $dbdate);
    if ((int)$parts[0] == 0) return false;  // lege datum

    return (mktime(0,0,0,$parts[1],$parts[2],$parts[0]) < mktime());
  }
  
  
  /**
   * getFondsAantal
   * returns the amount of one fonds in the given account on this given date
   * 
   * @param var $account rekeningnummer
   * @param var $fonds Fonds
   * @param date $date datum
   * @return int aantal
   */
  function getFondsAantal($account, $fonds, $date) 
  {
    $rekeningen = new Rekeningen();
    $query = $rekeningen->getQueryTemplate('getFondsAantal.sql', array(
      'account'   => $account,
      'fonds'     => $fonds,
      'date'      => $this->AEDate->formToDb($date),
      'year'      => date('Y', form2jul($date))
    ));

    $this->AELog->logIt($query);
    $totaal = $this->db->lookupRecordByQuery($query);
    
    return $totaal['aantal'];
  }

  /**
   * getFondsAantal
   * returns the amount of one fonds in the given account on this given date
   *
   * @param var $account rekeningnummer
   * @param var $fonds Fonds
   * @param date $date datum
   * @return int aantal
   */
  function getFondsAantalPortefeuille($portefeuille, $fonds, $date)
  {
    $rekeningen = new Rekeningen();
    $query = $rekeningen->getQueryTemplate('getFondsAantalPortefeuille.sql', array(
      'portefeuille'   => $portefeuille,
      'fonds'     => $fonds,
      'date'      => $this->AEDate->formToDb($date),
      'year'      => date('Y', form2jul($date))
    ));

    $totaal = $this->db->lookupRecordByQuery($query);

    return $totaal['aantal'];
  }

}
