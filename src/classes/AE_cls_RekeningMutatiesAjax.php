<?php

/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2020/06/24 14:42:11 $
    File Versie         : $Revision: 1.17 $

    $Log: AE_cls_RekeningMutatiesAjax.php,v $
    Revision 1.17  2020/06/24 14:42:11  rm
    8702

    Revision 1.16  2020/05/08 14:51:12  rm
    8489 Meervoudige FX orders: invoer op bewaarder

    Revision 1.15  2018/12/14 16:35:36  rvv
    *** empty log message ***



*/
class AE_RekeningMutatiesAjax extends AE_AjaxHelper
{
  var $defaultAccountListKey = 'Rekening';
  var $defaultAccountListValue = 'Rekening';

  var $data = array();
  var $__appvar = array();

  function AE_RekeningMutatiesAjax ($data)
  {
    global $__appvar;
    
    $this->date = new AE_datum;
    $this->__appvar = $__appvar;
    $this->data = $data;
    $this->AETemplate = new AE_template();
    $this->AERekeningmutaties = new AE_RekeningMutaties();
    $this->AEJson = new AE_Json();
  }
  
  
  function ledgerFondsUse () {
    $this->itemsExist(array('grootboek'));
    $theQuery = "SELECT FondsGebruik FROM Grootboekrekeningen WHERE GrootboekRekening = '" . $this->data['grootboek'] . "'";


    $DB = new DB ();
    $DB->SQL($theQuery);
    if ( ! $DB->Query() ) {
      echo "Query mislukt.";
      exit;
    }
    
    $data = $DB->NextRecord();

    echo $this->AEJson->json_encode(array(
      'FondsGebruik' => $data['FondsGebruik'],
    ));
  }

  function FondsAantal ()
  {
    
    
    if ( isset ($this->data['OrderuitvoerBewaarder']) && (int) $this->data['OrderuitvoerBewaarder'] === 1 ) {
      if ( ! isset ($this->data['date']) )
      {
        $this->data['date'] = date ('d-m-Y');
      }
      
      
      $this->itemsExist(array('portefeuille', 'date', 'fondsId'));
  
      $db = new DB ();
      $query = "
        SELECT
          sum(Rekeningmutaties.Aantal) as aantal,
          Rekeningmutaties.Fonds,
          IF (Rekeningmutaties.Bewaarder <> '',	Rekeningmutaties.Bewaarder,Rekeningen.Depotbank) AS BewaarderGebruiken
          FROM Rekeningmutaties
        JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
        
        
        WHERE Rekeningen.Portefeuille = '" . mysql_real_escape_string($this->data['portefeuille']) . "'
        AND 	Rekeningmutaties.Boekdatum >= '" . date('Y', strtotime($this->data['date'])) . "-01-01'
        AND	Rekeningmutaties.Boekdatum <=  '" . date('Y-m-d', strtotime($this->data['date'])) . "'
        AND Rekeningmutaties.Verwerkt = '1'
        AND Rekeningmutaties.GrootboekRekening IN ('FONDS', 'kruis')
        AND Rekeningmutaties.Fonds = '" . mysql_real_escape_string($this->data['fondsId']) . "'
        
        GROUP BY Rekeningmutaties.Fonds , BewaarderGebruiken
      ";
//      debug($query);
      $db->executeQuery($query);
      if ( $db->records() > 0 ) {
        $totalSum = 0;
        while ( $amount = $db->nextRecord() ) {
          $totalSum += $amount['aantal'];
          $returnAmount[$amount['BewaarderGebruiken']] = $amount;
        }
  
        echo $this->returnJson(array('totaalAantal' => $totalSum, 'subAantal' => $returnAmount));
      } else {
        echo $this->returnJson(array('aantal' => 0));
      }
      exit();
    }
    
    
    if ( isset ($this->data['portefeuille']) )
    {
      if ( ! isset ($this->data['date']) )
      {
        $this->data['date'] = date ('d-m-Y');
      }
      if ( empty ($this->data['fondsId']) )
      {
        echo $this->returnJson(array(
          'sucess'  => 'false'
        ));
        exit();
      }

      
      $this->itemsExist(array('portefeuille', 'date', 'fondsId'));
      $data['aantal'] = $this->AERekeningmutaties->getFondsAantalPortefeuille($this->data['portefeuille'], $this->data['fondsId'], $this->data['date']);
      $data['aantal'] = number_format($data['aantal'], 6, '.', '') + 0;
      echo $this->returnJson($data);
      exit();
    }

    $this->itemsExist(array('rekening', 'date', 'fondsId'));
    $data['aantal'] = $this->AERekeningmutaties->getFondsAantal($this->data['rekening'], $this->data['fondsId'], $this->data['date']);

    echo $this->returnJson($data);
  }

  function getExchangeRate ()
  {
    //expectedFields
    $valuta = $this->data['valuta'];
    $rekeningValuta = $this->data['rekeningValuta'];
    $datum = $this->date->formToDb($this->data['date']);

    $DB = new DB();
    $query = "SELECT * FROM Valutakoersen WHERE Valutakoersen.Valuta = '" . $valuta . "' AND Valutakoersen.datum <= '" . $datum . "' ORDER BY Valutakoersen.datum DESC LIMIT 1";
    $DB->SQL($query);
    $DB->Query();
    $data1 = $DB->NextRecord();
    
    if ( isset($data1['Datum']) && ! empty($data1['Datum']) ) {
      $data1['Datum'] = date('Y-m-d', db2jul($data1['Datum']));
    }

    $query = "SELECT * FROM Valutakoersen WHERE Valutakoersen.Valuta = '" . $rekeningValuta . "' AND Valutakoersen.datum <= '" . $datum . "' ORDER BY Valutakoersen.datum DESC LIMIT 1";
    $DB->SQL($query);
    $DB->Query();
    $data2 = $DB->NextRecord();
    
    if ( isset($data1['Datum']) && ! empty($data1['Datum']) ) {
      $data2['Datum'] = date('Y-m-d', db2jul($data2['Datum']));
    }

    echo $this->AEJson->json_encode(array('valuta' => $data1, 'rekeningValuta' => $data2));
  }

  // voor rekeningmutatiesTemplate.php
  function getFondskoers ()
  {
    $fonds = $this->data['fonds'];
    $datum = $this->date->formToDb($this->data['date']);

    $DB = new DB();

    //"SELECT Fondskoersen.Koers, Fondsen.Valuta, Fondsen.Fondseenheid FROM Fondskoersen LEFT JOIN Fondsen ON Fondskoersen.Fonds = Fondsen.Fonds WHERE Fondskoersen.Fonds = '".$fonds."' AND Fondskoersen.datum = '".$datum."'"
    $query = "SELECT Fondskoersen.Koers, Fondsen.Valuta, Fondsen.Fondseenheid, Fondskoersen.datum FROM Fondsen  LEFT JOIN Fondskoersen ON Fondsen.Fonds = Fondskoersen.Fonds AND Fondskoersen.datum <=  '" . $datum . "' WHERE Fondsen.Fonds = '" . $fonds . "' ORDER BY Fondskoersen.datum DESC LIMIT 1";

    $DB->SQL($query);
    $DB->Query();
    if ( $DB->Records() > 0 )
    {
      $data = $DB->NextRecord();
    }
    else
    {
      $DB = new DB();
      $DB->SQL("SELECT Fondskoersen.Koers, Fondsen.Valuta, Fondsen.Fondseenheid , Fondskoersen.datum FROM Fondskoersen LEFT JOIN Fondsen ON Fondskoersen.Fonds = Fondsen.Fonds WHERE Fondskoersen.Fonds = '" . $fonds . "' ORDER BY Fondskoersen.datum DESC LIMIT 1");
      $DB->Query();
      $data = $DB->NextRecord();
    }

    if ( isset($data['datum']) && ! empty($data['datum']) ) {
      $data['datum'] = date('Y-m-d', db2jul($data['datum']));
    }

    echo $this->AEJson->json_encode($data);
//    return $data[Koers] . "|" . $data[Valuta] . "|" . $data[Fondseenheid];
  }

  function getMutationDifference ()
  {
    $this->itemsExist(array('aAfschriftnummer', 'aRekening', 'aTotaalMutatie'));
    
    $type = '';
    if ( isset ($this->data['mutationType']) && $this->data['mutationType'] === 'temp' ) {$type = 'temp';}
    
    $AERekeningmutaties = new AE_RekeningMutaties ();
    $data = array(
      'Afschriftnummer' => $this->data['aAfschriftnummer'],
      'Rekening' => $this->data['aRekening'],
      'aTotaal' => $this->data['aTotaalMutatie']
    );
    echo $this->AEJson->json_encode($AERekeningmutaties->getMutationSum($data, $type));
  }

  /**
   * Get saldo used from rekeningafschriften_v2_Edit
   * 
   * @author RM
   * @since 16-9-2014
   * 
   * @param array form (post data)
   */
  function getSaldo ()
  {
    $this->itemsExist(array('form'));

    $stamp = form2jul($this->data['form']['Datum']);

    $DB = new DB();
    $getSalsoQuery = "
    SELECT NieuwSaldo,Afschriftnummer 
    FROM Rekeningafschriften 
    WHERE Rekeningafschriften.Rekening = '" . $this->data['form']['Rekening'] . "'
    AND YEAR(Rekeningafschriften.Datum) = '" . date("Y", $stamp) . "'
    ORDER BY Afschriftnummer DESC
    LIMIT 1";

    $DB->SQL($getSalsoQuery);
    $DB->Query();
    if ( $DB->Records() > 0 )
    {
      $data = $DB->NextRecord();
      $data['Afschriftnummer'] ++;
      
      echo $this->AEJson->json_encode(array(
        'afschriftNummer' => $data['Afschriftnummer'],
        'nieuwSaldo' => round($data['NieuwSaldo'], 2) //round newsaldo
      ));
    }
    else
    {
      $nummer = (date("Y", $stamp) * 1000) + 1;
      echo $this->AEJson->json_encode(array(
        'afschriftNummer' => $nummer,
        'nieuwSaldo' => 0
      ));
    }
  }
  
  /**
   * Get list of accounts
   * 
   * @param type $form
   * @return type
   * 
   */
  function fetchRekeningenList ()
  {
    $this->itemsExist(array('form'));

    $data = array();
    
    $rekeningenObj = new Rekeningen();
  
    /**
     * Wanneer OrderuitvoerBewaarder == 1 een andere query uitvoeren
     */
    if ( isset ($this->data['form']['OrderuitvoerBewaarder']) && (int) $this->data['form']['OrderuitvoerBewaarder'] === 1 ) {
      $memoriaal = 1;
      
      
      if ( isset ($this->data['form']['setMemoriaal']) ) {
        $setMemoriaal = ($this->data['form']['setMemoriaal']);
        if ( $setMemoriaal == '0' ) {
          $memoriaal = 0;
        }
      }
      
      $db = new DB();
      $bewaarderQuery = "
        SELECT
          Portefeuilles.Portefeuille,
          SUM(Rekeningmutaties.aantal) as aantal,
          max(if(Rekeningmutaties.Bewaarder is null,'',Rekeningmutaties.Bewaarder)) as Depotbank
          
          FROM
            Portefeuilles
          
            INNER JOIN Clienten ON Portefeuilles.Client = Clienten.Client
            INNER JOIN Rekeningen ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
            LEFT JOIN Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening AND  year(Rekeningmutaties.Boekdatum)='" . date('Y') . "' AND
            Rekeningmutaties.Fonds='" . mysql_real_escape_string($this->data['form']['fonds']) . "' AND Grootboekrekening='FONDS'
            
            WHERE Portefeuilles.Portefeuille = '".mysql_real_escape_string($this->data['form']['Portefeuille'])."'
            
            GROUP BY Portefeuilles.Portefeuille,Rekeningmutaties.Bewaarder
            ORDER BY Depotbank ,aantal
      ";
      
      $db->executeQuery($bewaarderQuery);
      $bewaarderData = $db->nextRecord();
  
  
      $fetchRekening = "
        SELECT if(Rekeningen.Depotbank='" . $bewaarderData['Depotbank'] . "',0,1) as volgordeDepot,
        if(Rekeningen.Valuta='EUR',0,1) as volgordeValuta,
        Rekeningen.Rekening,Rekeningen.Portefeuille,Rekeningen.Valuta,Rekeningen.Depotbank,
         
          Memoriaal,
          Rekening,
          Rekeningen.Depotbank as Rekening_Depotbank,
          Portefeuilles.Depotbank as Portefeuille_Depotbank,
          Rekeningen.Termijnrekening,
          Rekeningen.Deposito,
          Rekeningen.Depotbank,
          Portefeuilles.Client,
          Valuta
        
        FROM Rekeningen
         
         LEFT JOIN Portefeuilles on Rekeningen.Portefeuille = Portefeuilles.Portefeuille
         
         WHERE Rekeningen.Deposito = 0 AND Rekeningen.Inactief = 0  AND Rekeningen.Memoriaal = '" . $memoriaal . "' AND
        Rekeningen.Portefeuille='".mysql_real_escape_string($this->data['form']['Portefeuille'])."'
        ORDER BY volgordeDepot,volgordeValuta,Rekeningen.afdrukVolgorde,rekening
       ";
 
  
      $db->executeQuery($fetchRekening);
      while ( $rekeningData = $db->nextRecord() ) {
        if ( ! empty ($rekeningData['Rekening_Depotbank']) ) {
          $accounts[] = $rekeningData;
        }
      }
  
      if ( ! empty ($accounts) ) {
        foreach ( $accounts as $listItem ) {
          $accountsList['accounts'][] = $listItem['Rekening'];
          $accountsList['fullAccounts'][$listItem['Rekening']] = $listItem;
        }
      }
  
      if ( count ($accountsList) > 0 ) {
        echo $this->AEJson->json_encode($accountsList);
      } else {
        echo $this->AEJson->json_encode(array());
      }
      return true;
    }

    $accountsList = array();
    $db = new DB();
    $fetchRekening = "SELECT 
      Memoriaal,
      Rekening,
      Rekeningen.Depotbank as Rekening_Depotbank,
      Portefeuilles.Depotbank as Portefeuille_Depotbank,
      Rekeningen.Termijnrekening,
      Rekeningen.Deposito,
      Rekeningen.Depotbank,
      Portefeuilles.Client,
      Valuta
      FROM Rekeningen
      LEFT JOIN Portefeuilles on Rekeningen.Portefeuille = Portefeuilles.Portefeuille
      
      WHERE Portefeuilles.Portefeuille = '".$this->data['form']['Portefeuille']."' 
      AND Memoriaal IN (".$this->data['form']['Memoriaal'].")
      AND Rekeningen.Inactief = 0
      ORDER BY `Rekening` DESC
    ";
    
    /**
     * @comment
     * Was voorheen op basis van client
     * WHERE Portefeuilles.Client = '".$this->data['form']['Client']."'
     */
    
    $db->executeQuery($fetchRekening);
    while ( $rekeningData = $db->nextRecord() ) {
      $accounts[] = $rekeningData;
    }
    
    $eurAccounts = array();
    $depoAccounts = array();
    foreach ( $accounts as $key => $account ) {
      //was substr ($account['Rekening'], -3) === 'EUR'
      if ( (substr ($account['Rekening'], -3) === 'EUR' && $account['Memoriaal'] == 0) ) {
        if ( ($account['Rekening_Depotbank'] === $account['Portefeuille_Depotbank'])  && (int)$account['Termijnrekening'] === 0 && (int)$account['Deposito'] === 0 ) {
          $depoAccounts[] = $account;
        } else {
          $eurAccounts[] = $account;
        }
        unset($accounts[$key]);
      }
    }
    $fullAccounts = array_merge($depoAccounts, $eurAccounts,  $accounts);

    if ( ! empty ($fullAccounts) ) {
      foreach ( $fullAccounts as $listItem ) {
        $accountsList['accounts'][] = $listItem['Rekening'];
        $accountsList['fullAccounts'][$listItem['Rekening']] = $listItem;
      }
    }
    
    if ( count ($accountsList) > 0 ) {
      echo $this->AEJson->json_encode($accountsList);
    } else {
      echo $this->AEJson->json_encode(array());
    }
  }
  
  
  
  /**
   * Get list of accounts
   * 
   * @param type $form
   * @return type
   * 
   */
  function getRekeningenList ()
  {
    $this->itemsExist(array('form'));

    $data = array();
    
    $AERekiningMutaties = new AE_RekeningMutaties();

    $accountsList = array();
    $accounts = $AERekiningMutaties->getAccountsForClient(
      $this->data['form']['Client'], 
      $this->data['form']['Memoriaal'],
      array(
        'Memoriaal',
        'Rekening',
        'Rekeningen.Depotbank as Rekening_Depotbank',
        'Portefeuilles.Depotbank as Portefeuille_Depotbank',
        'Rekeningen.Termijnrekening',
        'Rekeningen.Deposito'
      )
    );
    
    $eurAccounts = array();
    $depoAccounts = array();
    foreach ( $accounts as $key => $account ) {
      if ( (substr ($account['Rekening'], -3) === 'EUR' && $account['Memoriaal'] == 0) ) {
        if ( ($account['Rekening_Depotbank'] === $account['Portefeuille_Depotbank'])  && (int)$account['Termijnrekening'] === 0 && (int)$account['Deposito'] === 0 ) {
          $depoAccounts[] = $account;
        } else {
          $eurAccounts[] = $account;
        }
        unset($accounts[$key]);
      }
    }
//    $fullAccounts = $depoAccounts + $eurAccounts + $accounts;   
    $fullAccounts = array_merge($depoAccounts, $eurAccounts,  $accounts);

    if ( ! empty ($fullAccounts) ) {
      foreach ( $fullAccounts as $listItem ) {
        $accountsList['accounts'][] = $listItem['Rekening'];
      }
    }
    
    if ( count ($accountsList) > 0 ) {
      echo $this->AEJson->json_encode($accountsList);
    } else {
      echo $this->AEJson->json_encode(array());
    }
  }
  
  
  
  function listRekeningen () 
  {
    $this->itemsExist(array('form'));
    
    $operator = ' where';
    $conditions = '';
    
    if ( isset($this->data['form']['client']) ) {
      $conditions .= $operator . ' `Portefeuilles`.`Client` = "' . $this->data['form']['client'] . '"';
      $operator = ' AND';
    }
    
    if ( isset($this->data['form']['memoriaal']) ) {
      if (is_array($this->data['form']['memoriaal'])){ $memSelect = implode(',', $this->data['form']['memoriaal']);} else {$memSelect = $this->data['form']['memoriaal'];}
      $conditions .= $operator . ' `Rekeningen`.`Memoriaal` IN (' . $memSelect . ')';
      $operator = ' AND';
    }
    
    if ( isset($this->data['form']['inactief']) ) {
      if (is_array($this->data['form']['inactief'])){ $statusSelect = implode(',', $this->data['form']['inactief']);} else {$statusSelect = $this->data['form']['inactief'];}
      $conditions .= $operator . ' `Rekeningen`.`Inactief` IN (' . $statusSelect . ')';
      $operator = ' AND';
    }
    $conditions .= $operator . ' Rekeningen.consolidatie = 0 ';

    $selectRekeningenQuery = "
      Select * FROM `Rekeningen`
        LEFT JOIN `Portefeuilles` on `Rekeningen`.`Portefeuille` = `Portefeuilles`.`Portefeuille` 
        " . ( ! empty ($conditions) ? $conditions : '' ) . "
    ";
    $db = new DB();
    $db->SQL($selectRekeningenQuery);
    $db->Query();

    $accountData = array();
    while ($row = $db->NextRecord())
    {
      $accountData[] = $row;
    }
    echo $this->AEJson->json_encode($accountData);
    
//    SELECT {fields}
//  FROM Rekeningen,Portefeuilles 
//  WHERE Rekeningen.Portefeuille = Portefeuilles.Portefeuille 
//  AND Portefeuilles.Client = '{clientId}'
//  AND Memoriaal IN ({memoriaal})
//  AND Rekeningen.Inactief = 0
//  ORDER BY `Rekening` DESC
    
    
  }
  

  /**
   * 
   * 
   * 
   * 
   */
  function getVoorlopigeSaldo ()
  {
    $this->itemsExist(array('form'));

    $rekeningNr = $this->data['form']['Rekening'];
    $stamp = form2jul($this->data['form']['Datum']);
    $saldo = 0;

    $DB = new DB();
    $query = $this->AETemplate->parseFile('rekeningmutaties/query/getVoorlopigeSaldoMain.sql', array(
        'rekeningNr' => $this->data['form']['Rekening'],
        'stampYear' => date('Y', $stamp)
    ));
    /** old full query * */
//  $query="SELECT * 
//    FROM (
//       (SELECT Afschriftnummer,change_date 
//       FROM VoorlopigeRekeningafschriften  
//       WHERE VoorlopigeRekeningafschriften.Rekening = '".$rekeningNr."'
//        AND YEAR(VoorlopigeRekeningafschriften.Datum) = '".date("Y",$stamp)."' 
//      ORDER BY Afschriftnummer DESC 
//      LIMIT 1)
//         UNION
//       (SELECT Afschriftnummer,change_date FROM Rekeningafschriften  WHERE Rekeningafschriften.Rekening = '".$rekeningNr."' AND YEAR(Rekeningafschriften.Datum) = '".date("Y",$stamp)."' ORDER BY Afschriftnummer DESC LIMIT 1) 
//      ) 
//  as tmp 
//order by change_date desc limit 1";  
    $DB->SQL($query);
    $DB->Query();
    if ( $DB->Records() > 0 )
    {
      $data = $DB->NextRecord();
      $data['Afschriftnummer'] ++;
      $Afschriftnummer = $data['Afschriftnummer'];
    }
    else
    {
      $Afschriftnummer = (date("Y", $stamp) * 1000) + 1;
    }

    $DB->SQL("SELECT NieuwSaldo FROM VoorlopigeRekeningafschriften WHERE VoorlopigeRekeningafschriften.Rekening = '" . $rekeningNr . "' AND YEAR(VoorlopigeRekeningafschriften.Datum) = '" . date("Y", $stamp) . "' AND Verwerkt = 0 ORDER BY Afschriftnummer DESC LIMIT 1");
    $DB->Query();
    if ( $DB->Records() > 0 )
    {
      $data = $DB->NextRecord();
      $saldo = round($data['NieuwSaldo'], 2);
    }
    else
    {
      $DB->SQL("SELECT NieuwSaldo FROM Rekeningafschriften WHERE Rekeningafschriften.Rekening = '" . $rekeningNr . "' AND YEAR(Rekeningafschriften.Datum) = '" . date("Y", $stamp) . "' ORDER BY Afschriftnummer DESC LIMIT 1");
      $DB->Query();
      if ( $DB->Records() > 0 )
      {
        $data = $DB->NextRecord();
        $saldo = round($data['NieuwSaldo'], 2);
      }
    }
    echo $this->AEJson->json_encode(array(
      'afschriftNummer' => $Afschriftnummer,
      'saldo' => $saldo
    ));
  }

  /**
   * checks if items exist in the data array exits the script is a item is not found
   * @param type $itemArray array of required items
   * 
   * exit with message
   * 
   * @author RM
   * @since 5-8-2014
   */
  function itemsExist ($itemArray)
  {
    foreach ($itemArray as $item)
    {
      if ( !isset($this->data[$item]) )
      {
        exit('Niet alle benodigde waarden zijn gevonden: ' . $item);
      }
    }
  }

}