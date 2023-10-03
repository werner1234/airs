<?


// set module naam voor autenticatie leeg = iedereen.
$__appvar["module"] = "";
// SET in wwwvars ie:  $__appvar["userLevel"] = _READ;
// include wwwvars
include_once("wwwvars.php");


$WelkomMenuV2 = getVermogensbeheerderField("WelkomMenuV2");

if ($WelkomMenuV2)
{
  include_once ("welcomeNw.php");
  exit;
}


session_start();
$_SESSION['submenu'] = New Submenu();

// selecteer bedrijfslogo
if($__appvar["bedrijf"])
{
	$q = "SELECT Vermogensbeheerders.Logo FROM VermogensbeheerdersPerBedrijf, Vermogensbeheerders WHERE Bedrijf = '".$__appvar["bedrijf"]."' AND VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder LIMIT 1";
	$DB = new DB();
	$DB->SQL($q);
	$DB->Query();
	$logodata = $DB->nextRecord();
}

//$content = array();
echo template($__appvar["templateContentHeader"],$content);
//echo 'Versie: '.$PRG_VERSION.' '.date("d-m-Y",db2jul($PRG_RELEASE));
?>
<?


if(is_file($__appvar["basedir"]."/html/rapport/logo/".$logodata['Logo']))
{
$tmp=getimagesize($__appvar["basedir"]."/html/rapport/logo/".$logodata['Logo']);
$maxWidth=1000;
$maxHeight=200;
$width=$tmp[0];
$height=$tmp[1];
$factor=1;
if($width > $maxWidth)
  $factor=$maxWidth/$width;
if($height > $maxHeight && ($maxHeight/$height < $factor))
  $factor=$maxHeight/$height;
$width=$width*$factor;
$height=$height*$factor;  
?>
<div align="left>">
<img width="<?=$width?>" height="<?=$height?>" src="rapport/logo/<?=$logodata['Logo']?>">
<?
}

echo "<table><tr>";
if (GetModuleAccess("CRM"))
{
  $list=createVerjaardagslijst(true);
  if($list<>'')
  {
    echo "<td valign='top'><br><b>" . vt('Verjaardagen') . "</b>";
    echo $list;
    echo "</td>";
  } 
}

if(1)
{
  include_once("../classes/mysqlList.php");
  $list = new MysqlList();
  $list->idField = "id";
  $list->editScript = $editScript;
  $list->perPage = 250;//$__appvar['rowsPerPage'];
  $list->addColumn("Taken","id",array("list_width"=>"100","search"=>false));
  $list->addColumn("","relatie",array("list_order"=>true,"search"=>true,"sql_alias"=>"if(ISNULL(CRM_naw.zoekveld),taken.relatie,if(CRM_naw.zoekveld='',taken.relatie,CRM_naw.zoekveld))","list_width"=>"","search"=>true));
  $list->addColumn("Taken","rel_id",array("list_width"=>"","search"=>true,"list_invisible"=>true));
  $list->addColumn("Taken","kop",array("list_width"=>"","search"=>true));
  $list->addColumn("Taken","soort",array("list_width"=>"100","search"=>true,"list_align"=>"center"));
  $list->addColumn("Taken","zichtbaar",array("list_width"=>"70"));
  $list->addColumn("Taken","gebruiker",array("list_width"=>"70","search"=>true,"list_align"=>"center","description"=>"Wie"));
  $list->addColumn("naw","zoekveld",array("list_width"=>"100","search"=>true,"list_align"=>"center","list_invisible"=>true));
  $list->forceFrom=" FROM taken ";
  $list->setJoin("LEFT JOIN CRM_naw ON taken.rel_id=CRM_naw.id");
  $list->setWhere("gebruiker='$USR' AND afgewerkt = 0 AND zichtbaar < NOW()");
  // set default sort
  $_GET['sort'][]      = "taken.spoed";
  $_GET['direction'][] = "DESC";
  // set sort
  $list->setOrder($_GET['sort'],$_GET['direction']);
  // set searchstring
  $list->setSearch($_GET['selectie']);
  // select page
  $list->selectPage($_GET['page']);
    
  if($list->records() > 0)
  {
    echo "<td valign='top'><br><b>Mijn taken:</b>";
    $DB = new DB();
    $DB->SQL("SELECT gebruiker,bgkleur FROM Gebruikers");
    $DB->Query();
    while ($usrData = $DB->nextRecord())  
    {
      $usrColors[$usrData['gebruiker']] = $usrData['bgkleur'];
    }
    echo '<table class="list_tabel" cellspacing="0" >';
    echo $list->printHeader();
    while($data = $list->getRow())
    {
      if ($data['spoed']['value'] <> 0)
        $data['tr_class'] = "list_dataregel_rood";
      $data['gebruiker']['td_style'] = " style=\"background-color:#".$usrColors[$data['gebruiker']['value']].";\" ";
     // if($data['zoekveld']['value']<>'')
     //   $data['relatie']['value']=$data['zoekveld']['value'];

      if(strlen($data['kop']['value'])>=23)
      {
        $data['kop']['value']=substr($data['kop']['value'],0,20).'...';
      }

  $template = '
	<tr class="list_dataregel" onmouseover="this.className=\'list_dataregel_hover\'" onmouseout="this.className=\'list_dataregel\'" title="Klik op de knop links om de details te zien/muteren">
<td class="list_button"><div class="icon"><a href="CRM_nawEdit.php?action=edit&id={rel_id_value}&taakId={id_value}&frame=1&returnUrl=welcome.php&toHome=1"><img src="images//16/muteer.gif" width="16" height="16" border="0" alt="" align="absmiddle">&nbsp;</a></div></td>
<td class="listTableData"   align="left" >{relatie_value} &nbsp;</td>
<td class="listTableData"   align="left" >{kop_value} &nbsp;</td>

<td class="listTableData" width="100"  align="center" >{soort_value} &nbsp;</td>
<td class="listTableData" width="100"  align="center" >{zichtbaar_value} &nbsp;</td>
<td class="listTableData" width="50"  '.$data['gebruiker']['td_style'].' align="center" >{gebruiker_value} &nbsp;</td>
</tr>';

	echo $list->buildRow($data,$template);
  }
  //listarray($list->sqlQuery);
  echo '</table>';
  }
  echo "</td>";
}

if(1)
{
  
  $query="SELECT
laatstePortefeuilleWaarde.portefeuille,
Portefeuilles.Client,
Portefeuilles.ZpMethode,
laatstePortefeuilleWaarde.zorgMeting,
Portefeuilles.TijdelijkUitsluitenZp
FROM
Gebruikers
INNER JOIN Portefeuilles ON Gebruikers.Accountmanager = Portefeuilles.Accountmanager
INNER JOIN laatstePortefeuilleWaarde ON Portefeuilles.Portefeuille = laatstePortefeuilleWaarde.portefeuille
WHERE ((Portefeuilles.TijdelijkUitsluitenZp=0 AND laatstePortefeuilleWaarde.zorgMeting <> 'Voldoet') OR  Portefeuilles.TijdelijkUitsluitenZp=2) AND 
Portefeuilles.ZpMethode <> 0 AND laatstePortefeuilleWaarde.zorgMeting <> '' AND Gebruikers.Gebruiker='$USR' AND 
Portefeuilles.Startdatum < now() AND Portefeuilles.Startdatum > '0000-00-00' AND Portefeuilles.Einddatum > now() ";

    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    
    echo "<td valign='top'>";
    if($DB->records() > 0)
    {      
      echo "<br><b>Afwijkingen zorgplicht:</b>";
      echo '<table>


<tr class="list_kopregel" ><td class="list_kopregel_data">Portefeuille</td><td class="list_kopregel_data">Client</td><td class="list_kopregel_data" >resultaat </td></tr>';
  
      while ($zorgData = $DB->nextRecord()) 
      {
        if($zorgData['ZpMethode']==2)
          $rapport='AFM';
        else
          $rapport='ZORG';

        if($zorgData['TijdelijkUitsluitenZp'] == 2)
          $extraStyle='style="background-color:#99ff99" ';
        else
          $extraStyle='';
          
      echo '	<tr '.$extraStyle.' class="list_dataregel" onmouseover="this.className=\'list_dataregel_hover\'" onmouseout="this.className=\'list_dataregel\'" title="Klik op de knop links om de details te zien/muteren">

      <td class="listTableData">
        <a href="rapportFrontofficeClientAfdrukken.php?portefeuille='.$zorgData['portefeuille'].'&rapport='.$rapport.'" target="_blank">'.$zorgData['portefeuille'].'</a>
      </td>
        <td class="listTableData" >'.$zorgData['Client'].'</td>
        <td class="listTableData" >'.$zorgData['zorgMeting']."</td>
      </tr>";
      }
    
      echo "</table>";
   
    }
  $ordermoduleAccess=GetModuleAccess("ORDER");
  if($ordermoduleAccess==2)
  {
    $query = "SELECT
OrderRegelsV2.client,
OrderRegelsV2.aantal,
OrdersV2.fondsOmschrijving,
OrdersV2.transactieSoort,
OrderRegelsV2.portefeuille,
OrderRegelsV2.orderregelStatus as `status`
FROM
OrdersV2
INNER JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid
INNER JOIN Portefeuilles ON OrderRegelsV2.portefeuille = Portefeuilles.Portefeuille
INNER JOIN Gebruikers ON Portefeuilles.Accountmanager = Gebruikers.Accountmanager
WHERE OrderRegelsV2.orderregelStatus < 3 AND Gebruikers.Gebruiker='$USR'";
  }
  else
  {
    $query = "SELECT
OrderRegels.client,
OrderRegels.aantal,
Orders.fondsOmschrijving,
Orders.transactieSoort,
OrderRegels.portefeuille,
OrderRegels.`status`
FROM
Orders
INNER JOIN OrderRegels ON Orders.orderid = OrderRegels.orderid
INNER JOIN Portefeuilles ON OrderRegels.portefeuille = Portefeuilles.Portefeuille
INNER JOIN Gebruikers ON Portefeuilles.Accountmanager = Gebruikers.Accountmanager
WHERE OrderRegels.status < 3 AND Gebruikers.Gebruiker='$USR'";
  }

    $DB->SQL($query);
    $DB->Query();

    if($DB->records() > 0)
    {      
      echo "<br><b>Openstaande orders:</b>";
      echo '<table>
      	<tr class="list_kopregel" ><td class="list_kopregel_data">Portefeuille</td>
                           <td class="list_kopregel_data" >Client </td>
                           <td class="list_kopregel_data" >Aantal </td>
                           <td class="list_kopregel_data" >FondsOmschrijving </td>
                           <td class="list_kopregel_data" >TransactieSoort </td>
                           <td class="list_kopregel_data" >Status </td></tr>';
  
      while ($orderData = $DB->nextRecord()) 
      {
      echo '	<tr class="list_dataregel" onmouseover="this.className=\'list_dataregel_hover\'" onmouseout="this.className=\'list_dataregel\'" title="Klik op de knop links om de details te zien/muteren">
        <td class="listTableData">'.$orderData['portefeuille'].'</td>
        <td class="listTableData" >'.$orderData['client'].'</td>
        <td class="listTableData" >'.$orderData['aantal'].'</td>
        <td class="listTableData" >'.$orderData['fondsOmschrijving'].'</td>
        <td class="listTableData" >'.$orderData['transactieSoort'].'</td>
        <td class="listTableData" >'.$__ORDERvar["status"][$orderData['status']].'</td>
      </tr>';
      }
    
      echo "</table>";
    
    }
       echo "</td>";   
}  
      

echo "</tr></table>";


?>
</div>
<?


if($_SESSION['usersession']['gebruiker']['updateInfoAan'])
{
  $cfg=new AE_config();
  $welcomeId='lastUpdateInfo_'.$USR;
  $lastWelcome=$cfg->getData($welcomeId);
  //$lastWelcome='2000-01-01';
  if($lastWelcome=='')
    $lastWelcome=date('Y-m-01');
  $db = new DB();
  if($lastWelcome <> '')
  {
    
    $query = "SELECT id,versie,informatie,add_date FROM updateInformatie WHERE publiceer=1 AND add_date > '$lastWelcome' ORDER BY versie";
    $db->SQL($query);
    $db->Query();
    if($db->records() > 0)
    {
      $versieInfo=' 
  <div id="popup" style="position:absolute; width:400px;  background:#fff; left:50%; top:50%; border-radius:5px; padding:50px; margin-left:-320px; margin-top:-150px; text-align:left; box-shadow:0 0 10px 0 #000;">
  <div id="close" style="position:absolute;background:black;color:white;right:-15px;top:-15px;border-radius:50%;width:30px; height:30px;line-height:30px;text-align:center; font-size:8px;font-weight:bold;font-family:\'Arial Black\', Arial, sans-serif;cursor:pointer;box-shadow:0 0 10px 0 #000;" style="position:absolute;background:black;color:white;right:-15px;top:-15px;border-radius:50%;width:30px; height:30px;line-height:30px;text-align:center; font-size:8px;font-weight:bold;font-family:\'Arial Black\', Arial, sans-serif;cursor:pointer;box-shadow:0 0 10px 0 #000;">X</div>
  <h4>Nieuwe update informatie beschikbaar.</h4>';
      while($data=$db->nextRecord())
      {
        $versieInfo.='Update <b>'.$data['versie']."</b>, ".date('d-m-Y',db2jul($data['add_date']))."<br><br>
        <span>".str_replace("\n","<br>",$data['informatie'])."</span>
        <br><br>\n";
      }
      $versieInfo.='</div>
    <script>
    $(document).ready(function(){
    $("#close").click(function(){
    $("#popup").fadeOut();
	  });
    });
    </script>';
    }
    echo $versieInfo;
  }
  
  $query = "SELECT add_date FROM updateInformatie WHERE publiceer=1 ORDER BY add_date desc limit 1";
  $db->SQL($query);
  $laatsteRecord=$db->lookupRecord();
  $cfg->addItem($welcomeId,$laatsteRecord['add_date']);
}


$_SESSION['NAV'] = "";
session_write_close();
// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);
?>