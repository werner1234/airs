<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/10/12 11:39:33 $
    File Versie         : $Revision: 1.25 $

    $Log: keepalive.php,v $
    Revision 1.25  2018/10/12 11:39:33  cvs
    call 7177

    Revision 1.24  2018/10/01 08:40:46  cvs
    call 7177

    Revision 1.23  2018/05/26 17:21:28  rvv
    *** empty log message ***

    Revision 1.22  2017/10/12 05:42:44  rvv
    *** empty log message ***

    Revision 1.21  2017/10/11 14:52:38  rvv
    *** empty log message ***

    Revision 1.20  2017/09/13 15:46:15  rvv
    *** empty log message ***

    Revision 1.19  2017/02/01 12:23:55  cvs
    laatsteDagelijkeUpdate

    Revision 1.18  2016/07/27 15:56:14  rvv
    *** empty log message ***

    Revision 1.17  2016/03/18 13:40:45  cvs
    no message

    Revision 1.16  2016/03/18 13:40:05  cvs




*/

include_once("wwwvars.php");
error_log('start keepalive.php    ', 3, 'php://stdout');
session_start();

//if($_GET['delete'])
// logIt($_SESSION['usersession']['gebruiker']['Gebruiker']." ".$_GET['random'].' tid:'.$_GET['tableId'].' d'.$_GET['delete']);
if($__appvar['recordLocking'])
{
  $db=new DB();
  if($_GET['tableId'] > 0 && $_SESSION['usersession']['gebruiker']['Gebruiker'] <> '')
  {
    if($_GET['table'])
      $tableWhere="AND `table`='".$_GET['table']."'";
    else
      $tableWhere='';
    if($_GET['delete']==1)
      $query="DELETE FROM tableLocks WHERE user='".$_SESSION['usersession']['gebruiker']['Gebruiker']."' AND tableId='".$_GET['tableId']."' $tableWhere";
    else
      $query="UPDATE tableLocks SET change_date=now() WHERE user='".$_SESSION['usersession']['gebruiker']['Gebruiker']."' AND tableId='".$_GET['tableId']."' $tableWhere";
    $db->SQL($query);
    $db->Query();
  }
  $query="DELETE FROM tableLocks WHERE (change_date < now() - interval 1 minute)";
  $db->SQL($query);
  $db->Query();
}

$klantmutaties='';
$db=new DB();
if($__appvar['master'] == true)
{
  $query = "SELECT id FROM klantMutaties WHERE verwerkt <> 1 ";
  if($db->QRecords($query)>0)
  {
    $items[] = array(vt("Nieuwe Klantmutaties"), "klantmutatiesList.php");
  }
  $query = "SELECT id FROM fondsAanvragen WHERE verwerkt = 0 ";
  if($db->QRecords($query)>0)
  {
    $items[]=array(vt("Nieuwe fondsaanvraag"),"fondsaanvragenList.php?filterNew=1");
  }
  $query = "SELECT id FROM fondskoersAanvragen WHERE verwerkt = 0 AND change_date>now()-interval 1 year";
  if($db->QRecords($query)>0)
  {
    $items[]=array(vt("Nieuwe fondskoersaanvraag"),"fondskoersaanvragenList.php?filterNew=1");
  }

  $query = "SELECT Rekeningen.Memoriaal FROM Rekeningen,VoorlopigeRekeningafschriften WHERE   Rekeningen.Rekening=VoorlopigeRekeningafschriften.Rekening AND VoorlopigeRekeningafschriften.verwerkt <> 1 GROUP BY Rekeningen.Memoriaal";
  if($db->QRecords($query)>0)
  {
    $menu = new Submenu();
    while($data=$db->nextRecord())
    {
      if($data['Memoriaal']==1)
        $items[]=array(vt("Nieuwe Memoriaalboekingen"),"voorlopigeRekeningafschriftenList.php?memoriaal=true");
      else
        $items[]=array(vt("Nieuwe Rekeningmutaties"),"voorlopigeRekeningafschriftenList.php");
    }
    $klantmutaties .= $menu->getHtml()."<br>";
  }
  if(count($items) > 0)
  {
    $menu = new Submenu();
    foreach ($items as $data)
      $menu->addItem($data[0],$data[1]);
    $klantmutaties .= $menu->getHtml()."<br>\n";
  }
}

$ingevoerdeOrders='';
$query="SELECT Vermogensbeheerders.OrderOrderdesk, Vermogensbeheerders.koersExport, Vermogensbeheerders.check_module_ORDER
FROM Vermogensbeheerders
Inner Join VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder WHERE VermogensbeheerdersPerGebruiker.Gebruiker='".$_SESSION['usersession']['gebruiker']['Gebruiker']."' limit 1 ";
$db->SQL($query);
$vermogensbeheerderSettings=$db->lookupRecord();
if($vermogensbeheerderSettings['OrderOrderdesk']==1)
{
  if($vermogensbeheerderSettings['check_module_ORDER']==2)
  {
    $query='SELECT id FROM OrdersV2 WHERE orderStatus=0 limit 1';
    $listfile='ordersListV2.php';
  }
  else
  {
    $query='SELECT id FROM Orders WHERE laatsteStatus=0 limit 1';
    $listfile='ordersList.php';
  }
  if($db->QRecords($query))
  {
    $menu = new Submenu();
    $menu->addItem(vt('Nieuwe orders'),$listfile.'?status=ingevoerd',array("style"=>'font-size:22px;'));
    $ingevoerdeOrders .= $menu->getHtml()."<br>";
  }
}

verwerkFixQueue();

// statusLights queries
error_log('statusLights queries    ', 3, 'php://stdout');

$statsLightsUpdate = "&middot;";
if (!isset($__appvar["crmOnly"]))   // alleen aanroepen als niet CRMonly call 4743
{
  $statsLightsUpdate = "";
  $redLight = "<img src='images/16/bulletred.png' />";
  $greenLight = "<img src='images/16/bulletgreen.png' />";

  if ($vermogensbeheerderSettings['koersExport'] == 0)  // bij alleen koers klanten DATA update verbergen
  {
    $query = "SELECT * FROM `Bedrijfsgegevens`";
    $rec = $db->lookupRecordByQuery($query);
    $dayNow = date("d-m");
    $dayTest = date("d-m",db2jul($rec["laatsteDagelijkeUpdate"]));
    $KA_tooltip = "(transactie)Data bijgewerkt";
    if ( $dayNow == $dayTest )
    {
      $statsLightsUpdate .="<span title='$KA_tooltip'>$greenLight D </span>";
    }
    else
    {
      $statsLightsUpdate .="<span title='$KA_tooltip'>$redLight D </span>";
    }
  }

  $laatsteValutaDatumJul=db2jul(getLaatsteValutadatum());
  $dayNow = date("w");
  $dayTest = date("w",$laatsteValutaDatumJul);

  $KA_tooltip = "Koersen bijgewerkt. \nLaatste koersdatum :".date("d-m-Y",$laatsteValutaDatumJul);

  if ( ($dayNow < 6 AND ($dayNow - $dayTest) == 1) OR ($dayNow == 1 AND $dayTest == 5))
  {
    $statsLightsUpdate .="<span title='$KA_tooltip'>$greenLight K </span>";
  }
  else
  {
    $statsLightsUpdate .="<span title='$KA_tooltip'>$redLight K </span>";
  }

  if ($__FIX["bedrijfscode"] <> "")
  {
    $KA_tooltip = "FIX-koppeling actief";
    $statsLightsUpdate .="<span title='$KA_tooltip'>$greenLight F </span>";
  }



  if (getVermogensbeheerderField("check_module_PORTAAL") == 1)
  {
    $dbP = new DB();
    $query = "SELECT UNIX_TIMESTAMP(`date`) AS `juldate`, `date` FROM `ae_log` WHERE `txt` LIKE '%vulPortaal klaar%' ORDER BY `id` DESC";
    if($portaalRec = $dbP->lookupRecordByQuery($query))
    {
      $KA_tooltip = "Portaal voor het laatst gevuld \nop ".date("d-m-Y H:i",$portaalRec["juldate"]);
      if (substr($portaalRec["date"],0,10) == date("Y-m-d") )
      {
        $statsLightsUpdate .="<span title='$KA_tooltip'>$greenLight P </span>";
      }
      else
      {
        $statsLightsUpdate .="<span title='$KA_tooltip'>$redLight P </span>";
      }
    }
  }

}



echo $_SESSION['usersession']['gebruiker']['Gebruiker']."|".$klantmutaties.$ingevoerdeOrders."|".$statsLightsUpdate;

?>