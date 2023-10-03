<?php
/*
    AE-ICT sourcemodule created 22 mei 2019
    Author              : Chris van Santen
    Filename            : AE_cls_reconV3Class.php

    $Log: AE_cls_reconV3.php,v $

*/

class AE_cls_depotCodeControle
{
  var $user;
  var $depotbank = "";
  var $batch = "";
  var $tableBank = "depCon_bankPile";
  var $tableAirs = "depCon_airsPile";
  var $airsPile = array();
  var $keyedAirsPile = array();
  var $bankPile = array();
  var $keyedBankPile = array();
var $multi = array();


  var $matchArray = array();
  var $unmatchArray = array();
  var $bankPortefeuilles = array();
  var $bankRekeningNrs= array();

  var $airsPortefeuilles = array();
  var $airsRekeningNrs= array();
  var $airsCashPile = array();
  var $portefeuilleVB = array();

  var $noFondsPile = array();
  var $depotWherePORT = "";
  var $depotWhereREK = "";
  var $depotFondsCodeField = "";
  var $trPile = array();
  var $cashPosYesterday = Array();

  function AE_cls_depotCodeControle($depotBank)
  {
    global $USR, $__appvar;
    $this->user = $USR;
    $this->depotbank = $depotBank;
    $this->batch = date("YmdHi")."_".rand(11111,99999);
    $this->initModule();
    $this->truncateTables();

    $this->depotWhereREK = "Rekeningen.Depotbank = '".$this->depotbank."' ";
    $this->depotWherePORT = "Portefeuilles.Depotbank = '".$this->depotbank."' ";
    switch (strtoupper($this->depotbank))
    {
      case "TGB":
        $this->depotFondsCodeField = "stroeveCode";
        break;
      case "BIN":
        $this->depotFondsCodeField = "binckCode";
        break;
      case "GIRO":
        $this->depotFondsCodeField = "giroCode";
        break;
      default:
        break;

    }

//    debug($this,"init class");

  }

  function addToBankPile($data)
  {
    $bankItem = array();
    $bankItem["batch"]        = $this->batch;
    $bankItem["eigenaar"]     = $this->depotbank;
    $bankItem["ISIN"]         = $data["ISIN"];
    $bankItem["bankCode"]     = $this->ontnullen($data["bankCode"]);
    $bankItem["valuta"]       = $data["valuta"];
    $bankItem["beurs"]        = $data["beurs"];
    $bankItem["isinValuta"]   = $data["ISIN"].$data["valuta"];
    $this->bankPile[] = $bankItem;
  }

  function bankPileIsinVal()
  {
    $this->keyedBankPile = array();
    foreach ($this->bankPile as $item)
    {
      if ($this->keyedBankPile[$item["isinValuta"]] != "")
      {
        $this->keyedBankPile[$item["isinValuta"]]["count"] =  $this->keyedBankPile[$item["isinValuta"]]["count"] + 1;

        $this->keyedBankPile[$item["isinValuta"]]["multi"][] = array(
          "bankCode"  => $item["bankCode"],
          "beurs"     => $item["beurs"],
        );
        $this->multi[] = $this->keyedBankPile[$item["isinValuta"]];
      }
      else
      {
        $item["count"] = 1;
        $this->keyedBankPile[$item["isinValuta"]] = $item;
      }
    }
  }

  function airsPileIsinVal()
  {
    $this->keyedAirsPile = array();
    $this->multi = array();
    foreach ($this->airsPile as $item)
    {
      if ($this->keyedAirsPile[$item["fonds_isinValuta"]] != "")
      {
        $this->keyedAirsPile[$item["fonds_isinValuta"]]["count"] =  $this->keyedAirsPile[$item["fonds_isinValuta"]]["count"] + 1;

        $this->keyedAirsPile[$item["fonds_isinValuta"]]["multi"][] = array(
          "fonds_bankcode"  => $item["fonds_bankcode"],
        );
        $this->multi[] = $this->keyedAirsPile[$item["isinValuta"]];
      }
      else
      {
        $item["count"] = 1;
        $this->keyedAirsPile[$item["fonds_isinValuta"]] = $item;
      }
    }
  }

  function showBankPile()
  {
    $out = "";
    $this->bankPileIsinVal();
    $cols = 6;
    $multiCols = 0;
    foreach ($this->multi as $row)
    {
      $out .= "\n<tr>
        <td>{$row["isinValuta"]}</td>
        <td class='ac'>{$row["bankCode"]}</td>
        <td>{$row["ISIN"]}</td>
        <td>{$row["valuta"]}</td>
        <td class='ac'>{$row["beurs"]}</td>
        <td class='ac'>{$row["count"]}</td>
        ";
      if (count($row["multi"]) > 0)
      {
        $multiCols = (count($row["multi"]) > $multiCols)?count($row["multi"])*2:$multiCols;
        $x = -1;
        foreach($row["multi"] as $sub)
        {
          $x++;
          $out .= "
          <td class='dccSub$x ac'>{$sub["bankCode"]}</td>
          <td class='dccSub$x ac'>{$sub["beurs"]}</td>
        ";
        }
      }
      $out .= "
      </tr>
        ";
    }
    // header bouwen
    $html = "
      <table class='dccTable'>
        <tr class='dccHeaderTr'>
          <td>IsinValuta</td>
          <td>Bankcode</td>
          <td>Isin</td>
          <td>Valuta</td>
          <td>Beurscode</td>
          <td>Count</td>
          
      ";
      for ($x=0; $x < $multiCols; $x++)
      {
        $html .= "<td class='dccSub$x'>bankcode ".($x+2)."</td><td class='dccSub$x'>beursCode ".($x+2)."</td>";
      }
      $html .= "</tr>".$out."</table>";

    return $html;

  }


  function showAirsPile()
  {
    $out = "";
    $this->airsPileIsinVal();
    debug(count($this->airsPile), count($this->keyedAirsPile));
    $cols = 6;
    $multiCols = 0;
    foreach ($this->keyedAirsPile as $row)
    {
      $out .= "\n<tr>
        <td>{$row["fonds_id"]}</td>
        <td class='ac'>{$row["fonds_fonds"]}</td>
        <td>{$row["fonds_omschrijving"]}</td>
        <td>{$row["fonds_importCode"]}</td>
        <td class='ac'>{$row["fonds_valuta"]}</td>
        <td class='ac'>{$row["fonds_isin"]}</td>
        <td class='ac'>{$row["fonds_bankcode"]}</td>
        <td class='ac'>{$row["fonds_isinValuta"]}</td>
        <td class='ac'>{$row["fonds_isinValuta2"]}</td>
        <td class='ac'>{$row["fonds_aantal"]}</td>
        ";
      if (count($row["multi"]) > 0)
      {
        $multiCols = (count($row["multi"]) > $multiCols)?count($row["multi"]):$multiCols;
        $x = -1;
        foreach($row["multi"] as $sub)
        {
          $x++;
          $out .= "
          <td class='dccSub$x ac'>{$sub["fonds_bankcode"]}</td>
        ";
        }
      }
      $out .= "
        <td class='ac'>{$row["positie_fonds"]}</td>
        <td class='ac'>{$row["positie_portefeuilles"]}</td>
        <td class='ac'>{$row["positie_stukken"]}</td>
      </tr>
        ";
    }
    // header bouwen
    $html = "
      <table class='dccTable'>
        <tr class='dccHeaderTr'>
          <td>fonds_id</td>
          <td>fonds_fonds</td>
          <td>fonds_omschrijving</td>
          <td>fonds_importCode</td>
          <td>fonds_valuta</td>
          <td>fonds_isin</td>
          <td>fonds_bankcode</td>
          <td>fonds_isinValuta</td>
          <td>fonds_isinValuta2</td>
          <td>fonds_aantal</td>
          
      ";
//    for ($x=0; $x < $multiCols; $x++)
//    {
//      $html .= "<td class='dccSub$x'>bankcode ".($x+2)."</td>";
//    }

    $html .= "
          <td>positie_fonds</td>
          <td>positie_portefeuilles</td>
          <td>positie_stukken</td>
    </tr>".$out."</table>";

    return $html;

  }

  function fillAirsPile()
  {
    global $_DB_resources;
    $this->airsPile = array();
    $out = array();
    $db = new DB();
    $query = "
SELECT DISTINCT
	* 
FROM
(
	SELECT
		fd.id,
		fd.Fonds,
		fd.fondssoort,
		fd.EindDatum,
		fd.Omschrijving,
		fd.FondsImportCode,
		fd.Valuta AS 'Valuta-AIRS',
		fd.ISINCode AS 'ISIN-Airs',
		fd.stroeveCode,
		CONCAT( LEFT ( fd.ISINCode, 12 ), fd.Valuta ) AS 'AIRSCombi' 
	FROM
		Fondsen AS fd 
	WHERE
		fd.stroevecode <> '' OR 
		( 
		  fd.fondssoort <> 'OPT' AND 
		  ISINcode <> '' 
		  AND 
		  ( 
		    fd.Einddatum = '0000-00-00' OR fd.Einddatum > now()
		  ) 
		)
) fd1
LEFT JOIN 
(
	SELECT
		CONCAT( LEFT ( fd.ISINCode, 12 ), fd.Valuta ) AS 'AIRSCombi',
		count(*) AS 'AantalAIRS' 
	FROM
		Fondsen AS fd 
	WHERE
	(
		fd.stroevecode <> '' OR 
		( 
		  fd.fondssoort <> 'OPT' AND 
		  ISINcode <> '' AND 
		  ( 
		    fd.Einddatum = '0000-00-00' OR fd.Einddatum > now()
		  ) 
		)
	) AND ISINcode <> '' 
	GROUP BY
	  CONCAT( LEFT ( fd.ISINCode, 12 ), fd.Valuta )) fd2 ON fd1.AIRSCombi = fd2.AIRSCombi
	LEFT JOIN 
	(
	  SELECT
		  pos.Fonds,
	  	count( pos.Portefeuille ),
		  sum( Aant ) 
	  FROM
		(
		  SELECT
			  RK.Portefeuille,
			  RM.Fonds,
			  sum( Aantal ) AS 'Aant' 
		  FROM
			  Rekeningmutaties RM
			INNER JOIN Rekeningen RK ON RM.Rekening = RK.Rekening
			INNER JOIN Portefeuilles PF ON RK.Portefeuille = PF.Portefeuille 
		  WHERE
			  RK.Depotbank = '".$this->depotbank."' AND 
			  RK.Consolidatie = 0 AND 
			  Boekdatum >= '".date("Y")."-01-01'  AND 
			  Grootboekrekening = 'Fonds' 
		  GROUP BY
			  RK.Portefeuille,
			  RM.Fonds 
		  HAVING
			  sum( aantal ) <> 0 
		) pos 
	  GROUP BY
	    pos.Fonds 
	) fd3 ON fd1.Fonds = fd3.Fonds

    ";

    $db->executeQuery($query);
    $cnt = 0;
    while ($data = $db->nextRecord("num"))
    {
      $cnt++;
      $this->airsPile[] = array(
        "fonds_id"                => $data[0],
        "fonds_fonds"             => mysql_real_escape_string($data[1]),
        "fonds_soort"             => mysql_real_escape_string($data[2]),
        "fonds_einddatum"         => mysql_real_escape_string($data[3]),
        "fonds_omschrijving"      => str_replace("'","",$data[4]),
        "fonds_importCode"        => mysql_real_escape_string($data[5]),
        "fonds_valuta"            => $data[6],
        "fonds_isin"              => $data[7],
        "fonds_bankcode"          => $this->ontnullen(mysql_real_escape_string($data[8])),
        "fonds_isinValuta"        => $data[9],
        "fonds_isinValuta2"       => $data[10],
        "fonds_aantal"            => $data[11],
        "positie_fonds"           => mysql_real_escape_string($data[12]),
        "positie_portefeuilles"   => $data[13],
        "positie_stukken"         => $data[14],
      );

    }
    return count($this->airsPile);
  }

  function ontnullen($in)
  {
    while (substr($in,0,1) == "0" AND strlen($in) > 0)
    {
      $in = substr($in,1);
    }
    return $in;
  }


  function bankPileToDB()
  {

    $db = new DB();
    $rows = array();
    $date = date("Y-m-d H:i:s");
    $query = "
    INSERT INTO `{$this->tableBank}` 
        (add_user,add_date,batch,eigenaar,ISIN,valuta,bankCode,beurs,isinValuta )
    VALUES
    ";
    foreach($this->bankPile as $b)
    {
      $rows[] = "('{$this->user}', '{$date}', '{$b["batch"]}', '{$b["eigenaar"]}', '{$b["ISIN"]}', '{$b["valuta"]}', '{$b["bankCode"]}', '{$b["beurs"]}', '{$b["isinValuta"]}')";
    }

    $query .= implode(",\n", $rows);
    $rows = array();
    $db->executeQuery($query);
    //debug($query);
    $query = "";

  }

  function airsPileToDB()
  {


    $db = new DB();
    $rows = array();
    $date = date("Y-m-d H:i:s");
    $queryStart = "
    INSERT INTO `{$this->tableAirs}` (add_user, add_date, batch, eigenaar, fonds_id, fonds_fonds, fonds_soort, fonds_einddatum, fonds_omschrijving, fonds_importCode, fonds_valuta, fonds_isin, fonds_bankcode, fonds_isinValuta, fonds_isinValuta2, fonds_aantal, positie_fonds, positie_portefeuilles, positie_stukken ) VALUES
    ";
    $x=0;
    $p=1;

    foreach($this->airsPile as $a)
    {
      $rows[] ="('{$this->user}','{$date}','{$a["batch"]}','{$a["eigenaar"]}',{$a["fonds_id"]},'{$a["fonds_fonds"]}','{$a["fonds_soort"]}','{$a["fonds_einddatum"]}','{$a["fonds_omschrijving"]}','{$a["fonds_importCode"]}','{$a["fonds_valuta"]}','{$a["fonds_isin"]}','{$a["fonds_bankcode"]}','{$a["fonds_isinValuta"]}','{$a["fonds_isinValuta2"]}','{$a["fonds_aantal"]}','{$a["positie_fonds"]}','{$a["positie_portefeuilles"]}','{$a["positie_stukken"]}')";
      $x++;
      if ($x > 500)
      {
        $x = 0;

        $query = $queryStart.implode(",\n", $rows);
       // debug("<hr>".$query,$p);
        $rows = array();
        $db->executeQuery($query);
        $p++;
      }

    }

    $query = $queryStart.implode(",\n", $rows);
//    debug($query);
    $db->executeQuery($query,$p);
  }


  function process()
  {
    global $USR;

    $prb = new ProgressBar();	// create new ProgressBar
    $prb->pedding = 2;	// Bar Pedding
    $prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
    $prb->setFrame();          	                // set ProgressBar Frame
    $prb->frame['left'] = 50;	                  // Frame position from left
    $prb->frame['top'] = 	80;	                  // Frame position from top
    $prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
    $prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
    $prb->show();

    $db  = new DB();
    $db2 = new DB();
    $db3 = new DB();
    $actArray = array();
    $query = "UPDATE `{$this->tableAirs}` SET actie = 'Stockdiv' WHERE `fonds_soort` = 'STOCKDIV' AND add_user = '$USR'";
    $db->executeQuery($query);
    $actArray[] = array(
    "message" => "STOCKDIV markeren (".$db->mutaties().") ",
    "query"   => $query
    );

    $query = "UPDATE `{$this->tableAirs}` SET actie = 'Verwijderen, Optie' WHERE `fonds_soort` = 'OPT' AND actie = '' AND fonds_einddatum < NOW() AND add_user = '$USR'";
    $db->executeQuery($query);
    $actArray[] = array(
      "message" => "OPT einddatum < now (".$db->mutaties().")",
      "query"   => $query
    );
    $query = "UPDATE `{$this->tableAirs}` SET actie = 'Geen actie, Optie' WHERE `fonds_soort` = 'OPT' AND actie = '' AND fonds_einddatum >= NOW() AND add_user = '$USR'";
    $db->executeQuery($query);
    $actArray[] = array(
      "message" => "OPT einddatum < now (".$db->mutaties().")",
      "query"   => $query
    );
   // $prb->setLabelValue('txt1','controle bankcode <> AIRS');
    $updateArr = array();
    $updateAct = array(
      0 => "Mogelijk verwijderen, maar positie aanwezig",
      1 => "TGB Code verwijderen (onbekend)",
      2 => "Akkoord",
      3 => "Akkoord, TGB of AIRS Dubbel",
      4 => "Akkoord, stroeveCode",
      5 => "Verschil ISIN/Val o.b.v. Fondscode ",

    );
    $query = "
      SELECT 
       	`depCon_airsPile`.*,
        `depCon_bankPile`.`isinValuta` AS b_isinValuta,
        `depCon_bankPile`.`ISIN` AS b_isin,
        `depCon_bankPile`.`bankCode` AS b_bankcode
      FROM 
        `{$this->tableAirs}` 
      LEFT JOIN `{$this->tableBank}` ON
        `{$this->tableAirs}`.fonds_bankcode = `{$this->tableBank}`.bankCode AND
        `{$this->tableBank}`.add_user = '$USR'
      WHERE      
        `{$this->tableAirs}`.actie = '' AND `{$this->tableAirs}`.add_user = '$USR'
        
      
    ";
    debug($query);
    $this->bankPileIsinVal();
//    debug($this->keyedBankPile);
    $db->executeQuery($query);
    $total = $db->records();
    $factor = 100/$total;
    $cnt = 1;
    echo "<li>controle AIRS items ({$total})";
    echo "<li>bankPile (".count($this->bankPile).")";
    echo "<li>AirsPile (".count($this->airsPile).")";
    while ($rec = $db->nextRecord())
    {
      $cnt++;
      $prb->setLabelValue('txt1','controle bankcode <> AIRS ('.$cnt.' / '.$total.')');
      $pro_step += $factor;
      $prb->moveStep($pro_step);

      if ($rec["b_bankcode"] == null AND $rec["fonds_bankcode"] != "")
      {
        //debug($rec);
        $q2 = "SELECT * FROM `{$this->tableBank}` WHERE `isinValuta` = '{$rec["fonds_isinValuta"]}' AND `add_user` = '$USR'";
      //  debug($q2);
        if ($bRec = $db2->lookupRecordByQuery($q2))
        {
          $q3 = "
            UPDATE 
              `{$this->tableAirs}` 
            SET 
              `{$this->tableAirs}`.actie = 'Wijzigen, Code moet zijn {$bRec["bankCode"]}' 
            WHERE 
              `{$this->tableAirs}`.id = '{$rec["id"]}' ";
//          debug($q3);
          $db3->executeQuery($q3);
        }
        else
        {
          if ($rec["positie_portefeuilles"] > 0)
          {
            $updateArr[0][] = $rec["id"];
          }
          else
          {
            $updateArr[1][] = $rec["id"];
          }
        }

      }
      else
      {

        if ($rec["fonds_bankcode"] == $rec["b_bankcode"])
        {

          if ($this->keyedBankPile[$rec["b_isinValuta"]]["count"] == 1 AND $rec["fonds_aantal"] == 1)
          {
            $updateArr[2][] = $rec["id"];
          }
          else
          {
            $updateArr[3][] = $rec["id"];
          }
        }
        else
        {
          if (substr($rec["b_isin"],0,7) == substr($rec["fonds_isin"],0,7))
          {
            $updateArr[4][] = $rec["id"];
          }
          else
          {
            $updateArr[5][] = $rec["id"];
          }
        }



      }






    }
    foreach ($actArray as $item)
    {
      echo "<li>".$item["message"];
    }

    for ($x=0; $x < count($updateAct); $x++)
    {
      $items = count($updateArr[$x]);
      echo "<li>bijwerken <b>".$updateAct[$x]."</b> ({$items})";

      $factor = 100/$items;
      $prb->moveStep(0);
      if ($items > 0)
      {

        $prb->setLabelValue('txt1','update actie "'.$updateAct[$x].'" ('.$cnt.' / '.$items.')');
        $pro_step += $factor;
        $prb->moveStep($pro_step);
        $q2 = "
      UPDATE
        `{$this->tableAirs}` 
      SET 
        `{$this->tableAirs}`.actie = '{$updateAct[$x]}' 
      WHERE 
        `{$this->tableAirs}`.id IN (".implode(",",$updateArr[$x])."); 
      ";
//        debug($q2);
        $db2->executeQuery($q2);
      }

    }
    $prb->hide();
    echo "<br/>Klaar met actie updates";
  }

  function controle()
  {
    $query = "select actie, count(id) as aantal FROM depCon_airsPile group by actie";
  }


  function truncateTables()
  {
    global $USR;
    $db = new DB();
    $query = "DELETE FROM `{$this->tableBank}` WHERE `add_user` = '$USR' ";
    $db->executeQuery($query);
    $query = "DELETE FROM `{$this->tableAirs}` WHERE `add_user` = '$USR' ";
    $db->executeQuery($query);
  }

  function initModule()
  {
    $tst = new SQLman();
    $tableBank = $this->tableBank;
    $tableAirs = $this->tableAirs;
    $tst->tableExist($tableBank,true);
    $tst->changeField($tableBank,"batch",array("Type"=>" varchar(30)","Null"=>false));
    $tst->changeField($tableBank,"eigenaar",array("Type"=>" varchar(30)","Null"=>false));  // weg
    $tst->changeField($tableBank,"ISIN",array("Type"=>" varchar(25)","Null"=>false));
    $tst->changeField($tableBank,"valuta",array("Type"=>" varchar(10)","Null"=>false));
    $tst->changeField($tableBank,"bankCode",array("Type"=>" varchar(25)","Null"=>false));
    $tst->changeField($tableBank,"beurs",array("Type"=>" varchar(25)","Null"=>false));
    $tst->changeField($tableBank,"isinValuta",array("Type"=>" varchar(40)","Null"=>false));  // nw


    $tst->tableExist($tableAirs,true);
    $tst->changeField($tableAirs,"batch",array("Type"=>" varchar(30)","Null"=>false));
    $tst->changeField($tableAirs,"eigenaar",array("Type"=>" varchar(30)","Null"=>false)); // weg
    $tst->changeField($tableAirs,"fonds_id",array("Type"=>" int","Null"=>false)); //nw
    $tst->changeField($tableAirs,"fonds_fonds",array("Type"=>" varchar(50)","Null"=>false)); //nw
    $tst->changeField($tableAirs,"fonds_soort",array("Type"=>" varchar(8)","Null"=>false)); //nw
    $tst->changeField($tableAirs,"fonds_einddatum",array("Type"=>" date","Null"=>false)); //nw
    $tst->changeField($tableAirs,"fonds_omschrijving",array("Type"=>" varchar(80)","Null"=>false));
    $tst->changeField($tableAirs,"fonds_importCode",array("Type"=>" varchar(35)","Null"=>false));
    $tst->changeField($tableAirs,"fonds_valuta",array("Type"=>" varchar(4)","Null"=>false));
    $tst->changeField($tableAirs,"fonds_isin",array("Type"=>" varchar(30)","Null"=>false));
    $tst->changeField($tableAirs,"fonds_bankcode",array("Type"=>" varchar(30)","Null"=>false));
    $tst->changeField($tableAirs,"fonds_isinValuta",array("Type"=>" varchar(50)","Null"=>false));
    $tst->changeField($tableAirs,"fonds_isinValuta2",array("Type"=>" varchar(50)","Null"=>false));
    $tst->changeField($tableAirs,"fonds_aantal",array("Type"=>" double","Null"=>false));
    $tst->changeField($tableAirs,"positie_fonds",array("Type"=>" varchar(50)","Null"=>false));
    $tst->changeField($tableAirs,"positie_portefeuilles",array("Type"=>" int","Null"=>false));
    $tst->changeField($tableAirs,"positie_stukken",array("Type"=>" double","Null"=>false));
    $tst->changeField($tableAirs,"actie",array("Type"=>" varchar(60)","Null"=>false));
  }

}
