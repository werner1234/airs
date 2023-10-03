<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/05/13 13:49:31 $
    File Versie         : $Revision: 1.6 $
*/
include_once("wwwvars.php");
$__debug = true;

$hlp = new MONITOR_importMatrixHelper();
$db = new DB();
$matrix  = array();
$updates = array();
$filter=$_GET['datum'];
if ($filter == "")
{
  $filter = $hlp->lastDateDb;
}

$options = $hlp->createDateOptions($filter,365);

$_SESSION['NAV']='';

$_SESSION["submenu"] = New Submenu();
$_SESSION['submenu']->addItem(vt("import matrix"),"MONITOR_importMatrixList.php");

$mainHeader = vt('matrix van') . ' '.$hlp->dateDbToForm($filter)." (".date("H:i:s").")";

$koersOnlyVB = array();
$klaargezet  = array();

$query = "SELECT UNIX_TIMESTAMP(add_date) as added, add_date, DATE(Datum) as Datum FROM Valutakoersen WHERE Valuta = 'EUR' ORDER BY Datum DESC ";
$vRec  = $db->lookupRecordByQuery($query);

$dayNow  = date("w");
$dayTest = date("w",db2jul($vRec["Datum"]));

if ( (time() - $vRec["added"]) < 600)  // koersen moeten minimaal 10 min geleden zijn geupdate
{
  $koersUpdated = false;

}
else
{
  $koersUpdated = (($dayNow < 6 AND ($dayNow - $dayTest) == 1) OR ($dayNow == 1 AND $dayTest == 5));
}

$query = "SELECT * FROM `Vermogensbeheerders` WHERE `koersExport` != 0 AND DATE(`Einddatum`) > NOW() ORDER BY `Vermogensbeheerder`";

$query = "
SELECT
	VermogensbeheerdersPerBedrijf.Bedrijf ,
	Vermogensbeheerders.Vermogensbeheerder
FROM
	`Vermogensbeheerders`
	INNER JOIN VermogensbeheerdersPerBedrijf ON 
	  Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder 
WHERE
	`koersExport` != 0
  AND DATE( `Einddatum` ) > NOW() 
ORDER BY
	Vermogensbeheerders.Vermogensbeheerder
";
//debug($query);

$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
//  $koersOnlyVB[] = $rec["Vermogensbeheerder"];
  $koersOnlyVB[] = $rec["Bedrijf"];
}


$bedrijven = $hlp->getBedrijven();

$combiBedrijven = array_merge((array)$bedrijven,$koersOnlyVB);

$airshost = new AIRS_cls_airshost($combiBedrijven);
$schema = $airshost->getUpdateSchema();

//debug($airshost->infoArray);

foreach ($combiBedrijven as $bedrijf)
{
  $query = "
    SELECT 
      `Bedrijf`, 
      `complete`
    FROM 
      `UpdateHistory` 
    WHERE 
      `Bedrijf` = '$bedrijf' AND 
      DATE(`add_date`) = '$filter' AND 
      `type` = 'dagelijks' ";
  if ($rec = $db->lookupRecordByQuery($query))
  {
    $updates[$rec["Bedrijf"]]["opgehaald"] = $rec["complete"];
  }

  $query = "
    SELECT 
      Bedrijf,
      DATE(laatsteDagelijkeUpdate) as klaargezet 
    FROM 
      Bedrijfsgegevens 
    WHERE
	    Bedrijf = '$bedrijf' AND date(laatsteDagelijkeUpdate) = '$filter'";
//debug($query);
  if ($rec = $db->lookupRecordByQuery($query))
  {
    $klaargezet[$rec["Bedrijf"]] = $rec["klaargezet"];
  }

}

$depotbanken = $hlp->getDepotBanken();

$query = "SELECT * FROM `MONITOR_importMatrix` WHERE DATE(add_date) = '$filter' ORDER BY bedrijf, depotbank";
$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
  $matrix[$rec["bedrijf"]][$rec["depotbank"]] = $rec;
}



$content['pageHeader'] = "<br><div class='edit_actionTxt'>
<b>$mainHeader</b> $subHeader
</div><br>
<link rel='stylesheet' href='style/AIRS_default.css' type='text/css' media='screen'>
<link rel='stylesheet' href='style/fontAwesome/font-awesome.min.css'>
<style>
.bgGreen{
  background-color:#0A0;
  color: white;
  text-align: center;
}
.bgYellow, .bgYellow1{
  background-color:#E9AB17;
}
.bgBlue{
  color: white;
  background-color:dodgerblue;
}
.bgBlank{
  background-color:#DDD;
}
.dataCell{
  width: 50px;
}

.header{
  
  background: rgba(20,60,90,1);
  color: white;
  text-align: center;
  font-size:12px;
  padding: 5px;
  font-weight: normal;
}
.bedrijf{
  width: 80px;
  padding-left: 20px;
  font-weight: bold;
  text-align: left;
}
td{
  padding: 5px;
  border-bottom: 1px solid #999;
  
  border-left: 1px solid #999;
}
button{
 font-size: 10px;
 padding: 5px;
}
.prio1D{
  color: red;
  font-weight: bold;
}

#statusTbl {
  position: relative; 
  border-collapse: separate; 
  
  
  
}
#statusTbl thead th{
  position: sticky;
  top: 0;
  z-index: 1;
}
#statusTbl tbody{
  
}
</style>


<form method='GET' name='controleForm'>
  Filter :
  <select name='datum' onChange='document.controleForm.submit();'>
    $options
  </select>
  <br/>
</form>

";

echo template($__appvar["templateContentHeader"],$content);

?>

  <table cellpadding="0" cellspacing="0" id="statusTbl">
    <thead>
      <tr>
      <th class="header bedrijf">Bedrijf</th>
      <th class="header bedrijf">Portaal Vul</th>
      <th class="header bedrijf">Schema</th>
      <th class="header ">KrsOnly</th>
      <?
      foreach ($depotbanken as $depot)
      {
        $prio1Depot = in_array($depot,$hlp->prio1Banken)?"prio1D":"";
        echo "<th class='header $prio1Depot'>$depot</th>";
      }
      ?>
      <th class="header bedrijf"> update </th>
      </tr>
    </thead>
    <tbody>

    <?

    $updates = (array)$updates;
    $col = count($depotbanken);
    //debug($updates);
    foreach ($koersOnlyVB as $vb)
    {

      if ( $koersUpdated)
      {

        $class = "bgGreen";
      }
      else
      {
        $class = "bgYellow";

      }
//  debug(array(count($updates[$vb]),$klaargezet[$bedrijf]),$vb);
      if (count($updates[$vb]) == 1 OR $klaargezet[$vb] != "")
      {

        $sec = "section2";

        if (count($updates[$vb]) == 1)
        {
          $lastCol = "\n\t<td>" . (($updates[$vb]["opgehaald"] == 1)?"afgerond (opgehaald)":" afgerond") . "</td>\n";
        }
        else
        {
          $lastCol = "\n\t<td>" . " klaargezet" . "</td>\n";
        }


      }
      else
      {
        $sec = "section1";
        $lastCol = "\n\t<td>" . (($koersUpdated)?"<button class='btnVerwerk' data-bedrijf='$vb' data-vb='1'>Verwerk</button>":" Open") . "</td>\n";
      }
      $$sec .= "\n<tr><td class='bedrijf'>$vb</td><td>&nbsp;</td><td>{$schema[$vb]}</td><td class='$class' >&nbsp;</td><td colspan='$col'><hr/></td>$lastCol</tr>";

    }



    foreach($bedrijven as $bedrijf)
    {
      $pv = next($matrix[$bedrijf]);

      $klr = ($klaargezet[$bedrijf] != "");
      $portaalVul = imagecheckbox($pv["autoPortaalVulling"]);

      if (count($updates[$bedrijf]) == 1 OR $klr)
      {
        $sec = "section2";
      }
      else
      {
        $sec = "section1";
      }
      $$sec .= "<tr>\n\t<td class='bedrijf'>$bedrijf</td><td class='ac'>{$portaalVul}</td><td>{$schema[$bedrijf]}</td><td class='dataCell bgBlank'>&nbsp;</td>";
      $updateReady = true;

      $btnPrio1 = -1;
      $countDepots = 0;
      $countVerwerkt = 0;
      foreach($depotbanken as $depot)
      {
        $rec = $matrix[$bedrijf][$depot];
        $data = "";
        $title = "";


        if (($rec["verwerkt"] == 1 OR $rec["verwerkt"] == 2) AND $rec["bedrijf"] != "")
        {
          $countDepots++;
          $data = $rec["door"];
          if ($rec["verwerkt"] == 1)
          {
            $class = "bgGreen";
            $title = "verwerkt ".substr($rec["change_date"], -8);
          }
          else
          {
            $class = "bgBlue";
            $title = "onHold ".substr($rec["change_date"], -8);
          }

          $countVerwerkt++;
          if ($rec["verwerkPrio"] == 1 AND $btnPrio1 == -1)
          {
            $btnPrio1 = true;
          }

        }
        else if ($rec["verwerkt"] == 0 AND $rec["bedrijf"] != "")
        {
          $countDepots++;
          if ($rec["verwerkPrio"] == 1)
          {
            $btnPrio1 = false;

            $class = "bgYellow1";
            $title .= "(prio1)";

          }
          else
          {
            $class = "bgYellow";
            $title .= "(prio2)";
          }

          $updateReady = false;
        }
        else
        {

          $class = "bgBlank";
        }
        $$sec .= "\n\t<td class='dataCell $class' title='{$title}'>&nbsp;{$data}</td>";
      }

      if ($sec == "section2")
      {
        if ($updates[$bedrijf]["opgehaald"] == 1)
        {
          $$sec .= "\n\t<td>" . (($updates[$bedrijf]["opgehaald"] == 1)?"afgerond (opgehaald)":" afgerond") . "</td>\n</tr>";
        }
        else
        {
          $$sec .= "\n\t<td>" . " klaargezet" . "</td>\n</tr>";
        }

      }
      else
      {

        $klrGezet = $hlp->getKlaargezet($bedrijf, $filter);


        if ($klrGezet != 0  )
        {

          $$sec .= "\n\t<td>Bezig..</td>\n</tr>";
//      if ($countDepots == $countVerwerkt)
//      {
//        $$sec .= "\n\t<td> <button class='btnVerwerk' data-prio1='2' data-bedrijf='$bedrijf'>Verwerk2</button></td>\n</tr>";
//      }
//      elseif ($countDepots > $countVerwerkt)
//      {
//        $$sec .= "\n\t<td>Deelexport weggezet..</td>\n</tr>";
//      }
//      else
//      {
//        $$sec .= "\n\t<td>Bezig..</td>\n</tr>";
//      }

        }

        else
        {

          $$sec .= "\n\t<td>" . (($updateReady OR $btnPrio1 == 1)?"<button class='btnVerwerk' data-prio1='{$btnPrio1}' data-bedrijf='$bedrijf'>Verwerk</button>":" Open") . "</td>\n</tr>";
        }

      }

    }



    echo $section1;
    ?>
    <tr> <td colspan='100' > &nbsp;</td></tr>
    <tr> <td colspan='100' > &nbsp;</td></tr>
    <tr>
      <td colspan='100' class="header bedrijf"> Updates uitgezet</td>
    </tr>
    <?
    echo $section2;
    ?>
    </tbody>
  </table>
  <br/>
  <br/>
  <br/>

  <br/>
  <br/>
  <script>
    $(document).ready(function () {

      var logoutSec = 30; // 30 seconden
      setInterval(function(){
        $("#countDown").text(--logoutSec);
        if (logoutSec < 1)
        {
          window.location.reload();
        }
        console.log(logoutSec);
        $("#countdown").html(`pagina ververst in ${logoutSec} sec`);
      },1000);


      $(".btnVerwerk").click(function(e){
        e.preventDefault();
        $(this).attr("disabled", true);
        $.ajax({
          type: "POST",
          url: "ajax/updateMONITOR_matrix.php",
          data: {
            datum: '<?=$filter?>',
            bedrijf: $(this).data("bedrijf"),
            prio1: $(this).data("prio1"),
          },
          dataType:'json',
          success:function(data)
          {
            console.log(data);
          },
          error:function(data){
            console.log("error");
            console.table(data);
          },
        });
        const updateSoort = "dagelijks";
        var url = "queueExport.php?bedrijf=" + $(this).data("bedrijf") + "&updateSoort="+updateSoort;
        document.location.href = url;
      });
    });
  </script>

<?
echo template($__appvar["templateRefreshFooter"],$content);
