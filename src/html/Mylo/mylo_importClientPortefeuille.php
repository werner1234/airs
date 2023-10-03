<?php
/*
    AE-ICT sourcemodule created 10 jul. 2020
    Author              : Chris van Santen
    Filename            : mylo_importClientPortefeuille.php

    $Log: mylo_importClientPortefeuille.php,v $
    Revision 1.4  2020/07/29 09:59:10  cvs
    call 8750
naar RVV 20201123

*/

include_once("wwwvars.php");
session_start();
$_SESSION["NAV"] = "";
session_write_close();

$myloDepotbank = "BIN";

$cfg = new AE_config();
$db = new DB();
$content["style"] = '
<link rel="stylesheet" href="../widget/css/font-awesome.min.css" >
<link rel="stylesheet" href="../widget/css/jquery-ui-1.11.1.custom.css"  type="text/css" media="screen">
<link rel="stylesheet" href="../style/workspace.css" type="text/css" media="screen">
<link rel="stylesheet" href="../style/AIRS_default.css" type="text/css" media="screen">
<link rel="stylesheet" href="../style/dropzone.css"  type="text/css" media="screen">
';
$content['jsincludes'] = '
<script type="text/javascript" src="../javascript/jquery-min.js"></script>
<script type="text/javascript" src="../javascript/jquery-ui-min.js"></script>
<script type="text/javascript" src="../javascript/algemeen.js"></script>
<script type="text/javascript" src="../javascript/dropzone.js"></script>
  
';
echo template("../".$__appvar["templateContentHeader"], $content);

?>

<style>
  .spinner{
    position: absolute;
    top:100px;
    left:100px;
    text-align: center;
    display: none;
    padding: 2rem;
    background: white;
    border:1px solid #999;
    border-radius: 10px;
  }
</style>
 <div class="spinner">
   <img src="../images/loading.gif"/><br/><br/>
   Moment geduld a.u.b.
 </div>
<?php

if ($_POST['posted'])
{

?>
  <script>
    $(".spinner").show();
  </script>
  <?php
  ob_flush();flush();

  $clientImport = false;
  $portefeuilleImport = false;

  // check filetype
  if ($_FILES['clientBestand']["error"] == 0)
  {
    if (  $_FILES['clientBestand']["type"] != "text/comma-separated-values" &&
          $_FILES['clientBestand']["type"] != "text/x-csv" &&
          $_FILES['clientBestand']["type"] != "text/csv" &&
          $_FILES['clientBestand']["type"] != "application/octet-stream" &&
          $_FILES['clientBestand']["type"] != "application/vnd.ms-excel" &&
          $_FILES['clientBestand']["type"] != "text/plain")
    {
      $_error = "FOUT: verkeerd client bestandstype(".$_FILES['clientBestand']["type"]."), alleen tekst bestanden zijn toegestaan.";
    }
    else
    {
      $clientImport = true;
    }

  }

  if ($_FILES['portefeuilleBestand']["error"] == 0)
  {
    if (  $_FILES['portefeuilleBestand']["type"] != "text/comma-separated-values" &&
      $_FILES['portefeuilleBestand']["type"] != "text/x-csv" &&
      $_FILES['portefeuilleBestand']["type"] != "text/csv" &&
      $_FILES['portefeuilleBestand']["type"] != "application/octet-stream" &&
      $_FILES['portefeuilleBestand']["type"] != "application/vnd.ms-excel" &&
      $_FILES['portefeuilleBestand']["type"] != "text/plain")
    {
      $_error = "FOUT: verkeerd portefeulle bestandstype(".$_FILES['portefeuilleBestand']["type"]."), alleen tekst bestanden zijn toegestaan.";
    }
    else
    {
      $portefeuilleImport = true;
    }

  }



  if (empty($_error))
  {
    session_start();


    if ($clientImport)
    {
      $filename = $_FILES['clientBestand']["tmp_name"];
      if (!$handle = @fopen($filename, "r"))
      {
        $error[] = "FOUT bestand $filename is niet leesbaar";
        return false;
      }
      $cAdded   = 0;
      $cUpdated = 0;
      $row      = 0;
      while ($data = fgetcsv($handle, 4096, ","))
      {
//        debug($data);
        $row++;
        if ($data[0] != "" AND $data[0] != "Client")
        {
          // call 9448
          // let op  utf8To8859 doe ook een replace van ' => `

          $data[0] = utf8To8859($data[0]);
          $data[1] = utf8To8859($data[1]);
          $data[2] = utf8To8859($data[2]);
          $data[3] = utf8To8859($data[3]);
          $data[4] = utf8To8859($data[4]);
          $data[5] = utf8To8859($data[5]);
          $data[11] = utf8To8859($data[11]);
          $data[12] = utf8To8859($data[12]);
          $data[13] = utf8To8859($data[13]);

          $query = "SELECT id FROM `Clienten` WHERE `Client` = '{$data[0]}'";
          $chkClient = $db->lookupRecordByQuery($query);
          if ($chkClient)
          {
            $qStart = "UPDATE `Clienten` SET ";
            $qEnd   = "WHERE `id` = ".$chkClient["id"];
            $cUpdated++;
          }
          else
          {
            $qStart = "INSERT INTO `Clienten` SET ";
            $qEnd   = ", `add_date` = NOW(), `add_user` = '$USR'";
            $cAdded++;
          }

          $q =
            $qStart."
              `change_date` = NOW()
            , `change_user` = '$USR'
            , `Client`      = '{$data[0]}' 
            , `Naam`        = '{$data[1]}' 
            , `Naam1`       = '{$data[2]}' 
            , `Adres`       = '{$data[3]}' 
            , `Woonplaats`  = '{$data[4]}' 
            , `Telefoon`    = '{$data[5]}' 
            , `Email`       = '{$data[6]}' 
            , `Land`        = '{$data[7]}' 
            , `pc`          = '{$data[8]}'
            , `extraInfo`   = '{$data[9]}|{$data[10]}|{$data[11]}|{$data[12]}|{$data[13]}'
            ".$qEnd;

          $db->executeQuery($q);

        }
      }
    }


    if ($portefeuilleImport)
    {
      $updateArray = array();
      $filename = $_FILES['portefeuilleBestand']["tmp_name"];
      if (!$handle = @fopen($filename, "r"))
      {
        $error[] = "FOUT bestand $filename is niet leesbaar";
        return false;
      }
      $pAdded   = 0;
      $pUpdated = 0;
      $row      = 0;
      $output   = array();
      while ($data = fgetcsv($handle, 4096, ","))
      {
        $row++;
        if ($row == 1)
        {
          $output[] = $data; // header foutbestand
        }

        if ($data[0] != "" AND $data[0] != "Portefeuille (portfolionumber)")
        {
          $query = "SELECT id FROM `Portefeuilles` WHERE `Portefeuille` = '{$data[0]}'";
          $chkPortefeuille = $db->lookupRecordByQuery($query);

          $query = "SELECT id FROM `Clienten` WHERE `Client` = '{$data[1]}'";
          $chkClient = $db->lookupRecordByQuery($query);

          if (!$chkClient) // client bestaat niet dus dit record skippen en in foutbestand toevoegen
          {
            $output[] = $data;
            continue;
          }


          if ($chkPortefeuille)
          {
            $qStart = "UPDATE `Portefeuilles` SET ";
            $qEnd   = "WHERE `id` = ".$chkPortefeuille["id"];
            $pUpdated++;
          }
          else
          {
            $qStart  = "INSERT INTO `Portefeuilles` SET ";
            $qEnd    = ", `add_date` = NOW(), `add_user` = '$USR'";
            $qEnd   .= ", `Vermogensbeheerder` = 'MOK'";
            $qEnd   .= ", `Einddatum` = '2037-12-31'";
            $qEnd   .= ", `selectieveld2`  = 'nvt'";
            $qEnd   .= ", `kwartaalAfdrukken`   = 1 ";
            $qEnd   .= ", `maandAfdrukken`      = 1 ";
            $pAdded++;


            // toevoegen EUR rekening
            $query = "INSERT INTO Rekeningen SET 
          `add_date`            = NOW(),
          `add_user`            = '$USR',
          `change_date`         = NOW(),
          `change_user`         = '$USR',
          `Inactief`            = 0,
          `Rekening`            = '{$data[0]}EUR',
          `Valuta`              = 'EUR',
          `Portefeuille`        = '{$data[0]}',
          `Depotbank`           = '{$myloDepotbank}',
          `Beleggingscategorie` = '',
          `AttributieCategorie` = '',
          `typeRekening`        = '',
          `Memoriaal`           = '0',
          `Tenaamstelling`      = 'Cash account'
            ";
            $db->executeQuery($query);

            // toevoegen MEM rekening
            $query = "INSERT INTO Rekeningen SET 
          `add_date`            = NOW(),
          `add_user`            = '$USR',
          `change_date`         = NOW(),
          `change_user`         = '$USR',
          `Inactief`            = 0,
          `Rekening`            = '{$data[0]}MEM',
          `Valuta`              = 'EUR',
          `Portefeuille`        = '{$data[0]}',
          `Depotbank`           = '{$myloDepotbank}',
          `Beleggingscategorie` = '',
          `AttributieCategorie` = '',
          `typeRekening`        = '',
          `Memoriaal`           = '1',
          `Tenaamstelling`      = 'Cash account'
            ";
            $db->executeQuery($query);
            /////////////////////
          }

          $q =
            $qStart."
              `change_date`       = NOW()
            , `change_user`       = '$USR'
            , `Depotbank`         = '{$myloDepotbank}'
            , `Portefeuille`      = '{$data[0]}'
            , `Client`            = '{$data[1]}'
            , `Accountmanager`    = '{$data[2]}'
            , `SoortOvereenkomst` = 'ExecutionOnly'
            , `Risicoklasse`      = '{$data[4]}'
            , `Memo`              = '".mysql_real_escape_string($data[5])."'
            , `Taal`              = '{$data[6]}'
            ".$qEnd;


          $db->executeQuery($q);
          $updateArray[] = $data[1];
        }
      }
//debug($updateArray);
      $updateArray = array_unique($updateArray);  //ontdubbelen
      if (count($updateArray) > 0)
      {
        chkAndAddConsolidaties($updateArray);
      }

    }
  }



  if (count($error) > 0)
  {
    foreach($error as $item)
    {
      echo "<li>$item</li>";
    }
  }
?>
  <script> $(".spinner").hide(200); </script>
  <style>
    thead td{
      background: rgba(20,60,90,1);
      color: white;
      padding: 8px;
    }
    td{
      border-bottom: 1px #EEE solid;

    }
    .ar{
      text-align: right;
    }
  </style>
  <br/>
  <br/>
  <h2>verwerkings resultaat</h2>
  <br/>
  <table cellspacing="0">
    <thead>
    <tr>
      <td>&nbsp;</td>
      <td>toegevoegd</td>
      <td>gemuteerd</td>
    </tr>
    </thead>
    <tr>
      <td>Clienten</td>
      <td class="ar"><?=$cAdded?></td>
      <td class="ar"><?=$cUpdated?></td>
    </tr>
    <tr>
      <td>Portefeuilles</td>
      <td class="ar"><?=$pAdded?></td>
      <td class="ar"><?=$pUpdated?></td>
    </tr>

  </table>
  <?php

  if (count($output) > 1)
  {
    $_SESSION["myloFoutBestand"] = $output;
    echo "<br><br><b>Er zijn ".(count($output)-1)." portefeuilles overgeslagen omdat de client onbekend was.</b>";
    echo "<br><br><a href='mylo_foutBestand.php'>Klik hier om het foutbestand te downloaden</a>";
  }

  unlink($filename);
  exit;
}




if (!$_FILES['importfile']['name'])
{
  ?>
  <style>
    #bestand2{
      display: none;


    }
    #feedback{
      display: none;
      padding: 2em;
      background: maroon;
      color: white;
      border-radius: 10px;
    }

    legend{
      padding: 5px;
      background: rgba(20,60,90,1);
      color: white;
    }
  </style>
  <script>

  </script>

  <form enctype="multipart/form-data" action="<?= $PHP_SELF ?>" method="POST"  name="editForm">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="posted" value="true" />
    <!-- Name of input element determines name in $_FILES array -->
    <br />
    <?php
    if ($_error)
      echo "<b style=\"color:red;\">".$_error."</b>";


?>



    <br/>
    <h2>Moka, inlezen Clienten/Portefeuilles</h2>
    <div class="formblock">
      <div class="formlinks">&nbsp; </div>
      <div class="formrechts">
        <div id="feedback"></div>
      </div>
    </div>
    <div class="formblock">
      <div class="formlinks">&nbsp; </div>
      <div class="formrechts">
        <div id="feedback"></div>
      </div>
    </div>

    <div class="form">
      <div class="formblock">
        <div class="formlinks"><span id="clientBestand">Client bestand</span> </div>
        <div class="formrechts">
          <input type="file" name="clientBestand" size="50" value="">
        </div>
      </div>





      <div class="formblock">
        <div class="formlinks">Portefeuille bestand</div>
        <div class="formrechts">
          <input type="file" name="portefeuilleBestand" size="50" value="">
        </div>
      </div>


      <div class="formblock">
        <div class="formlinks"> &nbsp;</div>
        <div class="formrechts"><br/>
          <input type="button" value="importeren" onclick="submitter();">
        </div>
      </div>

    </div>

        <br/>
        <br/>

  </form>




  <script>
    function feedback(txt,greenColor)
    {
      if (greenColor)
      {
        $("#feedback").css("background","rgba(20,90,20,1)");
      }
      else
      {
        $("#feedback").css("background","maroon");
      }

      if (txt != "")
      {
        $("#feedback").html(txt);
        $("#feedback").show(300);
      }
      else
      {
        $("#feedback").html("");
        $("#feedback").hide();
      }
    }

    function submitter()
    {
      if (document.editForm.clientBestand.value == '' && document.editForm.portefeuilleBestand.value == '')
      {
        feedback("Selecteer eerst een importbestand");
        return;
      }

      document.editForm.submit();
    }


    $(document).ready(function()
    {


    });

    </script>
        <?
}
echo template("../".$__appvar["templateRefreshFooter"], $content);

function chkAndAddConsolidaties($clientArray = array())
{
  global $USR;
  $myloDepotbank = "BIN";
//  debug($clientArray);
  if (count($clientArray) == 0)
  {
    return false;
  }

  $devWhere = "WHERE Client IN ('".implode("','",$clientArray)."') AND `consolidatie` = 0 ";

  $db = new DB();
  $query = "SELECT * FROM `Portefeuilles` {$devWhere} ORDER BY `Client`, `Portefeuille`";
//debug($query);
  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    $clntPort[$rec["Client"]][] = array(
      "Portefeuille" => $rec["Portefeuille"],
      "Accountmanager" => $rec["Accountmanager"],
      "Taal" => $rec["Taal"]
    );
   // $clntPort[$rec["Client"]][] = $rec["Portefeuille"];
  }

  foreach ($clntPort as $clnt=>$prtf)
  {
//    debug($prtf, $clnt);
    if (count($prtf) < 2)
    {
      $query = "
      UPDATE `Portefeuilles` SET 
        `selectieveld2`       = 'ptf. single',
        `change_user`         = '$USR',
        `change_date`         = NOW()
      WHERE 
        `Client` = '{$clnt}' AND 
        `Portefeuille` = '{$prtf[0]["Portefeuille"]}'
        ";

      $db->executeQuery($query);
//      debug("skip", $clnt);
      continue;
    }
    $cPort = $clnt."_CON";

    $query = "SELECT * FROM `GeconsolideerdePortefeuilles` WHERE `VirtuelePortefeuille` = '{$cPort}'";
    $geconsRec = $db->lookupRecordByQuery($query);

    $query = "SELECT id FROM `Portefeuilles` WHERE `Portefeuille` = '{$cPort}'";
    $pRec = $db->lookupRecordByQuery($query);

    if (!$pRec)
    {
      $query = "INSERT INTO `Portefeuilles` SET
      `add_user`            = '$USR',
      `add_date`            = NOW(),
      `change_user`         = '$USR',
      `change_date`         = NOW(),
      `consolidatie`        = 1,
      `kwartaalAfdrukken`   = 1,
      `maandAfdrukken`      = 1,
      `selectieveld2`       = 'ptf.cons.',
      `Depotbank`           = '{$myloDepotbank}',
      `Portefeuille`        = '{$cPort}',
      `Client`              = '{$clnt}',
      `Accountmanager`      = '{$prtf[0]["Accountmanager"]}',
      `Vermogensbeheerder`  = 'MOK',
      `Einddatum`           = '2037-12-31',
      `Taal`                = '{$prtf[0]["Taal"]}'
";
//      debug($query);
      $db->executeQuery($query);

    }
    $query = "
        UPDATE 
          `Portefeuilles` 
        SET
          `selectieveld2`       = 'nvt'
        WHERE 
          `Client`              = '{$clnt}' AND 
          `consolidatie`        = 0
      ";
    $db->executeQuery($query);

    if (!$geconsRec)
    {
      $query = "INSERT INTO `GeconsolideerdePortefeuilles` SET
      `add_user`            = '$USR',
      `add_date`            = NOW(),
      `change_user`         = '$USR',
      `change_date`         = NOW(),
      `VirtuelePortefeuille`= '{$cPort}',
      `Client`              = '{$clnt}',
      `Vermogensbeheerder`  = 'MOK',
      `Einddatum`           = '2037-12-31'
      ";
      $indx = 1;
      $pTot = count($prtf);
      if ($pTot > 40)
      {
        $error = "Client $clnt heeft meer dan 40 ({$pTot}) portefeuilles";
        $pTot = 40;
      }

      for ($i =0 ; $i < $pTot; $i++)
      {
        $query .= "\n , `Portefeuille".($i+1)."` = '{$prtf[$i]["Portefeuille"]}' ";
      }
//      debug($query);
      $db->executeQuery($query);
    }
    else
    {
      $query = "UPDATE `GeconsolideerdePortefeuilles` SET
      change_date = NOW(),
      change_user = '{$USR}'
      ";
      //debug($geconsRec);
      $addArray = array();
      $emptyEntries = array();
      for ($j = 1; $j < 41; $j++)
      {
        $value = array_shift($prtf);
        $query .= "\n, `Portefeuille{$j}` = '{$value["Portefeuille"]}' ";
      }
      $query .= "\n WHERE `id` = ".$geconsRec["id"];
//      debug($query);
      $db->executeQuery($query);

    }
  }

  $conArray = array();
  foreach ($clientArray as $clnt)
  {
    $conArray[] = $clnt."_CON";
  }
  //debug($conArray);
  $con = new AIRS_consolidatie();
  //$VPs = $con->ophalenVPsViaPortefeuille($conArray);
  if( count($conArray) > 0 )
  {
    $con->bijwerkenConsolidaties($conArray);
  }

}

function utf8To8859($in)
{

  return str_replace("'", "`",iconv('UTF-8','ISO-8859-1',  $in));
}