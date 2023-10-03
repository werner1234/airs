<?php
/*
    AE-ICT sourcemodule created 31 mrt. 2021
    Author              : Chris van Santen
    Filename            : AE_cls_APIextern.php


*/

class AE_cls_externalAPI
{
  var $status = "";
  var $user = "";
  var $table = "APIextern_queue";
  var $error  = array();
  var $db;
  var $fieldArray  = array();
  var $mailDoubles = array();
  var $skipFields = array("id", "add_date", "add_user", "change_date", "change_user");
  var $skipDatafields = array("action");
  var $depotbank = "nvt";
  var $VB = "nvt";
  var $accMan = "nvt";

  function AE_cls_externalAPI($vb, $depot)
  {
    $this->user      = $_SESSION["USR"];
    $this->db        = new DB();
    $this->depotbank = $depot;
    $this->VB        = $vb;
    $this->accMan    = "ALG".$this->VB;
    $this->buildFieldArray();
  }


  function buildFieldArray()
  {
    $query = "show fields from CRM_naw";
    $this->db->executeQuery($query);
    while($rec = $this->db->nextRecord())
    {
      $fld = $rec["Field"];
      if (!in_array($fld, $this->skipFields))
      {
        $this->fieldArray[] = $fld;
      }
    }
  }

  function messageCount()
  {
    $this->messageCount = imap_num_msg($this->mailbox);
    return $this->messageCount;
  }

  function populateQueue($type="client")
  {

    switch($type)
    {
      case "portefeuille":
        $where = "portefeuilleupdate";
        break;
      case "mutaties":
        $where = "mutatieupdate";
        break;
      default:
        $where = "clientupdate";
        break;
    }

    $row = array();
    $query = "
    SELECT
      id,
      add_date,
      `action`,
      dataFields,
      `type`
    FROM
      {$this->table}
    WHERE
      finished = 0 AND 
      `action` = '{$where}'
    ORDER BY
      id DESC
    ";
//    debug($query);
    $this->db->executeQuery($query);
    $rows = array();
    $id = 1000;
    while ($rec = $this->db->nextRecord())
    {
      $datafields = (array)json_decode($rec["dataFields"]);
      $id++;
      switch($type)
      {
        case "portefeuille":
          $where = "portefeuilleupdate";
          $item = array(

            "id"          => "P-".$id,
            "portfolioId" => $datafields["portfolioId"],
            "add_date"    => $rec["add_date"],
            "recId"       => $rec["id"],

          );
          unset($datafields["portfolioId"]);
          unset($datafields["action"]);
          unset($datafields["new"]);
          break;
        case "mutaties":
          $item = array(

            "id"          => "M-".$id,
            "portfolioId" => $datafields["portfolioId"],
            "add_date"    => $rec["add_date"],
            "recId"       => $rec["id"],

          );
          unset($datafields["portfolioId"]);
          unset($datafields["action"]);
          unset($datafields["new"]);
          break;
        default:
          $item = array(

            "id"          => "C-".$id,
            "clientid"    => $datafields["clientid"],
            "add_date"    => $rec["add_date"],
            "recId"       => $rec["id"],

          );
          unset($datafields["clientid"]);
          unset($datafields["action"]);
          unset($datafields["new"]);
          break;
      }

      foreach ($datafields as $k=>$v)
      {
        $item["datafields"] .= "{$k}: {$v}\n";
      }
      $rows[$item["id"]] = $item;
    }

    $_SESSION["bGiro"]["dataSet"][strtoupper(substr($type,0,1))] = $rows;
    return $rows;
  }

  function errorList()
  {
    if (count($this->error) > 0)
    {
      return "<ul><li>".implode("</li><li>", $this->error) ."</li></ul>";
    }
    else
    {
      return false;
    }
  }

  function setIgnored($ids)
  {
    if (count($ids) < 1)
    {
      return false;
    }
    $db = new DB();
    $db->debug = false;
    $query = "
    UPDATE {$this->table} SET
      change_date = NOW(),
      change_user = '{$this->user}',
      finished  = 1,
      ignored = 1
    WHERE 
      id IN (".implode(", ",$ids).")";
//    debug($query);
    $db->executeQuery($query);
  }

  function setFinished($ids)
  {
    if (count($ids) < 1)
    {
      return false;
    }
    $db = new DB();
    $db->debug = false;
    $query = "
    UPDATE {$this->table} SET
      change_date = NOW(),
      change_user = '{$this->user}',
      finished  = 1
    WHERE 
      id IN (".implode(", ",$ids).")";

    $db->executeQuery($query);
  }

  function addMutaties($ids)
  {
    $finishIds = array();
    $mClosed = 0;
    $mAdded = 0;
    $db = new DB();

    foreach ($ids as $id)
    {
      $query = "SELECT * FROM {$this->table} WHERE id = {$id}";
      if ($rec = $db->lookupRecordByQuery($query))
      {
        $data   = json_decode($rec["dataFields"], true);

        $rekening = trim($data["portfolioId"])."EUR";
        $query = "SELECT * FROM `Rekeningen` WHERE `Rekening` = '{$rekening}'";
        if (!$rekRec = $db->lookupRecordByQuery($query))
        {
          $this->error[] = "Rekening <b>{$rekening}</b> bestaat niet in portefeuille {$data["portfolioId"]}";
          continue;
        }

        if ($data["closeAccount"] == 1)
        {
          $query = "
            INSERT INTO `APIextern_closeAccounts` SET 
              `add_user`     = '{$this->user}'
            , `add_date`     = NOW()
            , `change_user`  = '{$this->user}'
            , `change_date`  = NOW()
            , `portefeuille` = '{$data["portfolioId"]}'
            , `sluitDatum`   = '{$data["date"]}'
              ";
          if ($db->executeQuery($query))
          {
            $mClosed++;
            $finishIds[] = $id;
          }
          $this->error[] = "Portefeuile {$data["portfolioId"]} wordt binnenkort gesloten ";
          continue;
        }

        if (strtolower($data["direction"]) != "w" AND strtolower($data["direction"]) != "i")
        {
          $this->error[] = "Richting foutief <b>{$data["direction"]}</b> voor {$rekening}";
          continue;
        }

        if (strtolower($data["direction"]) == "w")
        {
          $debet          = abs($data["amount"]);
          $credit         = 0;
          $bedrag         = $data["amount"] * -1;
          $omschrijving   = "Ontrekking";
          $grootboek      = "ONTTR";
        }
        else
        {
          $debet          = 0;
          $credit         = abs($data["amount"]);
          $bedrag         = $data["amount"];
          $omschrijving   = "Storting";
          $grootboek      = "STORT";
        }

//        debug($data);

        $query = "
          INSERT INTO TijdelijkeRekeningmutaties SET
            add_date            = NOW()
          , add_user            = '{$this->user}'
          , change_date         = NOW()
          , change_user         = '{$this->user}'  
          , `bankTransactieId`  = '{$data["mutationId"]}'
          , `orderId`           = '{$data["batchId"]}'
          , `Boekdatum`         = '{$data["date"]}'
          , `settlementDatum`   = '{$data["date"]}'
          , `Rekening`          = '{$rekening}'
          , `Valuta`            = 'EUR'
          , `Valutakoers`       = '1'
          , `Fonds`             = ''
          , `Aantal`            = 0
          , `Fondskoers`        = 0
          , `Grootboekrekening` = '{$grootboek}'
          , `Debet`             = '{$debet}'
          , `Credit`            = '{$credit}'
          , `Bedrag`            = '{$bedrag}'
          , `Omschrijving`      = '{$omschrijving}'
          , `Transactietype`    = ''
          , `Verwerkt`          = 0
          , `Memoriaalboeking`  = 0
          
          ";

//debug($query);

        if ($db->executeQuery($query))
        {
          $mAdded++;
          $finishIds[] = $id;
        }


      }
      else
      {
        $this->error[] = "Client: record met queueId: {$id} niet gevonden";
      }
    }

    $this->setFinished($finishIds);
    return array(
      "add"    => $mAdded,
      "close"  => $mClosed
    );



  }

  function addClienten($ids)
  {
    $finishIds  = array();
    $cUpdated   = 0;
    $cAdded     = 0;
    $db         = new DB();

    foreach ($ids as $id)
    {
      $query = "SELECT * FROM {$this->table} WHERE id = {$id}";
      if ($rec = $db->lookupRecordByQuery($query))
      {
        $data   = json_decode($rec["dataFields"], true);
        $client = trim($data["clientid"]);
        if (strlen($client) > 25)
        {
          $this->error[] = "Client: id <b>{$client}</b> bevat meer dan 25 tekens";
          continue;
        }
        $query  = "SELECT * FROM `Clienten` WHERE Client = '{$client}'";
        if ($clntRec = $db->lookupRecordByQuery($query))
        {
          $qStart = "UPDATE `Clienten` SET ";
          $qEnd   = "WHERE `id` = ".$clntRec["id"];
          $cUpdated++;
        }
        else
        {
          $qStart = "INSERT INTO `Clienten` SET ";
          $qEnd   = "
          , `add_date` = NOW()
          , `add_user` = '{$this->user}'
          , `Client`      = '{$data["clientid"]}' 
          ";
          $cAdded++;
        }

        $q =
          $qStart."
              `change_date` = NOW()
            , `change_user` = '{$this->user}'
            , `Naam`        = '".trim($data["firstname"]." ".$data["lastname"])."' 
            , `Naam1`       = '' 
            , `Adres`       = '".trim($data["street"]." ".$data["houseNumber"]." ".$data["houseNumber"])."' 
            , `Woonplaats`  = '{$data["city"]}' 
            , `Telefoon`    = '' 
            , `Email`       = '{$data["email"]}' 
            , `Land`        = '' 
            , `pc`          = '{$data["zipcode"]}'
            , `extraInfo`   = ''
            ".$qEnd;
        if ($db->executeQuery($q))
        {
          $finishIds[] = $id;
        }

      }
      else
      {
        $this->error[] = "Client: record met queueId: {$id} niet gevonden";
      }
    }

    $this->setFinished($finishIds);
    return array(
      "add"    => $cAdded,
      "update" => $cUpdated
    );
  }

  function addPortefeuilles($ids)
  {
    $finishIds = array();
    $pUpdated = 0;
    $pAdded   = 0;

    $db       = new DB();

    foreach ($ids as $id)
    {
      $query = "SELECT * FROM {$this->table} WHERE id = {$id}";
      if ($rec = $db->lookupRecordByQuery($query))
      {
        $data = json_decode($rec["dataFields"], true);


        $client = trim($data["clientid"]);
        $portefeuille = trim($data["portfolioId"]);
        if (strlen($client) > 22)
        {
          $this->error[] = "Portefeuille: id <b>{$portefeuille}</b> bevat meer dan 22 tekens";
          continue;
        }

        if ($data["modelPortfolio"] != "")
        {
          $query = "SELECT * FROM `Portefeuilles` WHERE `Portefeuille` = '{$data["modelPortfolio"]}'";
          if (!$mdlRec = $db->lookupRecordByQuery($query))
          {
            $this->error[] = "Modelportefeuille <b>{$data["modelPortfolio"]}</b> bestaat niet in portefeuille $portefeuille";
            continue;
          }
        }

        $query = "SELECT * FROM `Clienten` WHERE Client = '{$client}'";
        debug($query);
        if (!$clntRec = $db->lookupRecordByQuery($query))
        {
          $this->error[] = "Portefeuille <b>{$portefeuille}</b> niet aangemaakt omdat Client <b>{$client}</b> niet bestaat";
          continue;
        }
        else
        {
          $query = "SELECT id FROM `Portefeuilles` WHERE `Portefeuille` = '{$portefeuille}'";
          $chkPortefeuille = $db->lookupRecordByQuery($query);
          $addPortefeuille = false;
          if ($chkPortefeuille)
          {
            $qStart = "UPDATE `Portefeuilles` SET ";
            $qEnd = "WHERE `id` = " . $chkPortefeuille["id"];
            $pUpdated++;
          }
          else
          {
            $addPortefeuille = true;
            $endDate = $this->checkDate($data['endDate']);

            $qStart = "INSERT INTO `Portefeuilles` SET ";
            $qEnd = "
              , `add_date`                    = NOW()
              , `add_user`                    = '{$this->user}'
              , `Vermogensbeheerder`          = '{$this->VB}'
              , `selectieveld2`               = ''
              , `kwartaalAfdrukken`           = 1 
              , `maandAfdrukken`              = 1 
              , `RapportageValuta`            = 'EUR' 
              , `BeheerfeeBTW`                = '21' 
              , `BeheerfeeAantalFacturen`     = 12 
              , `BeheerfeePercentageVermogen` = 0.3 
              , `BeheerfeeMethode`            = 3 
              , `BeheerfeeBasisberekening`    = 6 
              , `Accountmanager`              = '{$this->accMan}' 
            ";
          }

          $q =
            $qStart . "
              `change_date`       = NOW()
            , `change_user`       = '{$this->user}'
            , `Einddatum`         = '{$endDate}'
            , `Depotbank`         = '{$this->depotbank}'
            , `Portefeuille`      = '{$portefeuille}'
            , `Client`            = '{$client}'
            , `ModelPortefeuille` = '{$data["modelPortfolio"]}'
            " . $qEnd;


          if ($db->executeQuery($q))
          {
            $finishIds[] = $id;

            if ($addPortefeuille)
            {
              $pAdded++;

              // toevoegen EUR rekening
              $query = "INSERT INTO `Rekeningen` SET 
                `add_date`            = NOW(),
                `add_user`            = '{$this->user}',
                `change_date`         = NOW(),
                `change_user`         = '{$this->user}',
                `Inactief`            = 0,
                `Rekening`            = '{$portefeuille}EUR',
                `Valuta`              = 'EUR',
                `Portefeuille`        = '{$portefeuille}',
                `Depotbank`           = '{$this->depotbank}',
                `Beleggingscategorie` = '',
                `AttributieCategorie` = '',
                `typeRekening`        = '',
                `Memoriaal`           = '0', 
                `IBANnr`              = '{$data["iban"]}'
              ";
              $db->executeQuery($query);


              // toevoegen MEM rekening
              $query = "INSERT INTO Rekeningen SET 
                `add_date`            = NOW(),
                `add_user`            = '{$this->user}',
                `change_date`         = NOW(),
                `change_user`         = '{$this->user}',
                `Inactief`            = 0,
                `Rekening`            = '{$portefeuille}MEM',
                `Valuta`              = 'EUR',
                `Portefeuille`        = '{$portefeuille}',
                `Depotbank`           = '{$this->depotbank}',
                `Beleggingscategorie` = 'Liquiditeiten',
                `AttributieCategorie` = '',
                `typeRekening`        = '',
                `Memoriaal`           = '1' 
              ";
              $db->executeQuery($query);
            }

          }

        }

      }
      else
      {
        $this->error[] = "Portefeuille: record met queueId: {$id} niet gevonden";
        continue;
      }


    }

    $this->setFinished($finishIds);
    return array(
      "add"    => $pAdded,
      "update" => $pUpdated
    );
  }



  function checkDate($date)
  {

    $isDate = false;
    $dp = explode("-", $date);
    if (
      ((int)$dp[0] > 2000 AND (int)$dp[0] < 2050) AND
      ((int)$dp[1] > 0    AND (int)$dp[1] < 13)   AND
      ((int)$dp[2] > 0    AND (int)$dp[2] < 32)
    )
    {
      $isDate = true;
    }

    return $isDate?$date:"2037-12-31";
  }


  function getContentById($id)
  {
    $db = new DB();
    $out = "";
    $query = "SELECT * FROM {$this->table} WHERE id = '$id'";
    $rec = $db->lookupRecordByQuery($query);
    $datafields = (array)json_decode($rec["dataFields"]);
    $datafields["eventCode"] = $rec["eventCode"];
    $datafields["submitterIp"] = $rec["submitterIp"];

    foreach ($datafields as $k=>$v)
    {
      if (in_array($k, $this->fieldArray) OR $k == "eventCode" OR $k == "submitterIp")
      {
        $out .= "
        <div class='dispRow'>
        <div class='dispKey'>$k</div>
        <div class='dispValue'>$v</div>
        </div>
        ";
      }
    }
    return $out;
  }


}