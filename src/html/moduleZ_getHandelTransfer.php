<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/11/04 13:58:41 $
    File Versie         : $Revision: 1.15 $

    $Log: moduleZ_getHandelTransfer.php,v $
    Revision 1.15  2019/11/04 13:58:41  cvs
    call 8224

    Revision 1.14  2018/12/14 09:07:32  cvs
    no message

    Revision 1.13  2018/11/21 14:09:57  rvv
    *** empty log message ***

    Revision 1.12  2018/11/19 14:26:51  cvs
    update naar VRY omgeving

    Revision 1.11  2018/11/07 17:05:52  rvv
    *** empty log message ***

    Revision 1.10  2018/11/07 11:48:30  cvs
    call 7282

    Revision 1.9  2018/11/07 09:43:49  rvv
    *** empty log message ***

    Revision 1.8  2018/10/31 17:20:54  rvv
    *** empty log message ***

    Revision 1.7  2018/10/19 07:04:18  cvs
    call 7175

    Revision 1.6  2018/10/09 12:33:39  cvs
    call 7175

    Revision 1.5  2018/10/08 06:23:13  cvs
    call 7175, bevindingen 5-10

    Revision 1.4  2018/09/23 17:14:23  cvs
    call 7175

    Revision 1.3  2018/09/08 17:42:21  rvv
    *** empty log message ***

    Revision 1.2  2018/09/07 11:11:36  cvs
    commit voor robert call 6989

    Revision 1.1  2018/09/07 10:11:45  cvs
    commit voor robert call 6989

    Revision 1.3  2018/07/02 08:08:25  cvs
    call 6709

    Revision 1.2  2018/07/02 07:49:17  cvs
    call 6709

    Revision 1.1  2018/06/18 06:59:57  cvs
    update naar VRY omgeving

    Revision 1.1  2018/05/25 09:34:52  cvs
    25-5-2018


*/

include_once("wwwvars.php");
include_once ("moduleZ_functions.php");




$fase2  = false;
$cfg    = new AE_config();
$db     = new DB();
$fmt    = new AE_cls_formatter();
$lkp    = new AE_lookup();
$rows   = array();


// ophalen money market fund
$query = "
  SELECT 
    Fonds,
    Omschrijving 
  FROM 
    Fondsen 
  WHERE 
    (einddatum>now() OR einddatum='0000-00-00') AND 
    OptieBovenliggendFonds='' AND 
    Fondseenheid IN(1)  AND 
    id in ('14358') 
  ORDER BY 
  Omschrijving";

$db->executeQuery($query);
$fondsOptions='';

while($fonds=$db->nextRecord())
{
  $fondsOptions.="<option value='".$fonds['Fonds']."'>".$fonds['Omschrijving']."</option>";
}


if ($_REQUEST["posted"] == 1)
{
  $data = $_SESSION["moduleZ-cash"];
  unset($_SESSION["moduleZ-cash"]);

//$__debug = true;
  debug($data);
  define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
  include_once("rapport/MutatievoorstelFondsen.php");
  include_once("../classes/portefeuilleSelectieClass.php");



  foreach($data as $portefeuilleData)
  {

    $fonds = (trim($portefeuilleData['fonds']) != "")?trim($portefeuilleData['fonds']):$_REQUEST['fonds'];
    if($portefeuilleData['paused'])
    {
      echo "Portefeuille " . $portefeuilleData['portefeuille'] . " is paused, verwerking overgeslagen.<br>\n";
      continue;
    }
    if($portefeuilleData['cashPositie']==0)
    {
      echo "Portefeuille " . $portefeuilleData['portefeuille'] . " cashPositie van 0 overgeslagen.<br>\n";
      continue;
    }

    //$portefeuilleData['portefeuille']='100RD Vlieger';
    $rapportageDatum=date('Y-m-d');//db2jul(getLaatsteValutadatum());
    $selectData=array('datumVan'=>db2jul(date('Y',$rapportageDatum).'-01-01'),
                      'datumTm'=> db2jul($rapportageDatum),
                      'externeBatchId'=>$portefeuilleData['batch'],
                      'selectedPortefeuilles'=>array($portefeuilleData['portefeuille']),
                      'fonds'=>$fonds,
                      'skipPortefeuilleSelectie'=>true,
                      'berekeningswijze'=>'Totaal vermogen',
                      'berekeningswijzeViaBedrag'=>true,
                      'afronding' => 0.0001,
                      'bedrag'=>$portefeuilleData['cashPositie']);

    $rapport = new MutatievoorstelFondsen($selectData);
    $rapport->writeRapport();
    if(count($rapport->orderData)>0)
    {
      $log = $rapport->OutputOrder();
      echo "Voor portefeuille " . $portefeuilleData['portefeuille'] . " is " . $log['message']."<br>\n";
    }
    else
    {
      echo "Voor portefeuille " . $portefeuilleData['portefeuille'] . " is geen order aangemaakt.<br>\n";
    }
  }
?>
  <br/>
  <br/>
  <br/>
  &nbsp;&nbsp;<button><a href="tijdelijkebulkordersv2List.php">Ga naar de tijdelijke orders</a></button>
<?
  exit;
}

if ($_REQUEST["posted"] == "fase2")
{
  $fase2 = true;
  $prodData = array();

  $result =  mzApiGET("positions");
  $result = (array) json_decode($result);
  $airsBatchId = date("Ymd")."T".date("His");
}


$content = array(
  "pageHeader" => "<br><div class='edit_actionTxt'><b>Liquide posities orderen</b></div><br><br>"
);
echo template($__appvar["templateContentHeader"],$content);

?>
  <link href="widget/classTemplates/widget.css" rel="stylesheet" type="text/css" media="screen">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<style>
  .moduleZtable{
    min-width: 800px;
  }
  .moduleZtable td{
    padding: 5px;
  }
  .moduleZheader{
    background: rgba(20,60,90,1);
    color: #FFF;
  }
  .moduleZheader td{
    color: #FFF;
  }
  #loading{
    display: none;
    box-sizing: padding-box;
    z-index:999;
    background: rgba(50,50,50,.5);
    color: white;
    font-size: 2rem;
    background-repeat: no-repeat;
    background-position: center;
    padding: 25px;
    text-align: center;
    width: 450px;
  }
  #foutMelding{

    width: 1000px;
    height: auto;
    color: whitesmoke;
    padding: 0;
    border:1px solid #999;
    box-shadow: 4px 4px 4px #333;
  }
  .foutHead{
    background: maroon;
    color: whitesmoke;
    padding: 10px 20px;
    font-size: 1.3em;
    width: calc(100%-40px);
    height: 20px;
  }
  .foutBody{
    background: beige;
    color: maroon;
    width: calc(100%-40px);
    padding: 10px;
  }
</style>
<br/>
<div class="moduleZ-container">
  <div id="loading"><i class="fa fa-spinner fa-spin" style="font-size:36px"></i> moment aub</div>
  <div >
    <form method="post" id="formReb">


<?
if ($fase2 AND $result["code"] != "")
  {
?>   <div id="foutMelding">
    <div class="foutHead">Foutmelding(en)</div>
    <div class="foutBody">FOUT <?=$result["code"]?>: <?=$result["message"]?></div>
  </div>
<?

  }
  else
  {

    if ($fase2)
    {


      ?>
      <input type="hidden" name="posted" value="1"/>
      <div class="formBlock">
        <div class="formLinks">Fonds</div>
        <div class="formRechts"><select name="fonds"><?=$fondsOptions?></select></div>
      </div>
      <table class="moduleZtable">
        <tr class="moduleZheader">
          <td>Portefeuille</td>
          <td>Client</td>
          <td>paused</td>
          <td>identified</td>
          <td>cashPositie</td>
          <td>batch</td>
        </tr>


        <?
        foreach ($result["accounts"] as $item)
        {
          $item = (array)$item;
          $item["batch"]    = $airsBatchId;

          if ($portRec = $lkp->getData("Portefeuilles", "Portefeuille = '{$item["account_number"]}'"))
          {
            $naam = $portRec["Client"];
          }
          else
          {
            $naam = "niet in AIRS";
          }

          $row = array(
            "portefeuille" => $item["account_number"],
            "client"       => $naam,
            "paused"       => $item["paused"],
            "identified"   => $item["identified"],
            "cashPositie"  => $item["transfer"],
            "batch"        => $item["batch"],
          );



          if ($item["paused"] == "1")
          {
            $pausedRows[] = $row;
          }
          else
          {
            $normalRows[] = $row;
          }
          $rows[] = $row;

        }

        $_SESSION["moduleZ-cash"] = $rows;

        foreach ($normalRows as $row)
        {
          echo "
          <tr >
            <td  class='borderU kp10 '>{$row["portefeuille"]}</td>
            <td  class='borderU kp30 '>{$row["client"]}</td>
            <td  class='borderU kp10 ac'>" . mz_showCheck($row["paused"]) . "</td>
            <td  class='borderU kp10 ac'>" . mz_showCheck($row["identified"]) . "</td>
            <td  class='borderU ar kp15 bold'>" . $fmt->format("@N{.2}", $row["cashPositie"]) . "</td>
            <td  class='borderU kp10 ac'>".$row["batch"]."</td>
          </tr>
          ";
        }
        echo "<tr class='moduleZheader'><td colspan='20'><hr/></td>";
        foreach ($pausedRows as $row)
        {
          echo "
          <tr >
            <td  class='borderU kp10 '>{$row["portefeuille"]}</td>
            <td  class='borderU kp30 '>{$row["client"]}</td>
            <td  class='borderU kp10 ac'>" . mz_showCheck($row["paused"]) . "</td>
            <td  class='borderU kp10 ac'>" . mz_showCheck($row["identified"]) . "</td>
            <td  class='borderU ar kp15 bold'>" . $fmt->format("@N{.2}", $row["cashPositie"]) . "</td>
            <td  class='borderU kp10 ac'>".$row["batch"]."</td>
          </tr>
          ";
        }
        ?>
      </table>
      <br/>
      <br/>
      <input type="submit" value="  akkoord voor verwerking  "/>
      <?
    }
    else
    {
      $query = "SELECT * FROM `API_moduleZ_logging` WHERE `referer` LIKE '%api/reports/positions' ORDER BY id DESC";
      if ($rec = $db->lookupRecordByQuery($query))
      {
        $date = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $rec["add_date"]);
      }
      else
      {
        $date = "geen vermelding gevonden";
      }
      ?>
      <input type="hidden" name="posted" value="fase2"/>
      <div>
        Vorige sessie was <?=$date?>
        <br/>
        <br/>
      </div>
      <button id="btnSubmitReb"> start laden van de selectie</button>
      <?
    }

?>
    </form>
</div>

  <script type="text/javascript" src="javascript/jquery-min.js"></script>
  <script type="text/javascript" src="javascript/jquery-ui-min.js"></script>
  <link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">

  <script>
    $(document).ready(function(){
      $("#btnSubmitReb").click(function (e) {
        e.preventDefault();
        $("#loading").show(100);
        $("#formReb").submit();

      });
    });
  </script>

<?
 }
echo template($__appvar["templateRefreshFooter"],$content);

