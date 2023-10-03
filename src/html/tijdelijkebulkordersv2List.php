<?php
/*
    AE-ICT CODEX source module versie 1.6, 28 maart 2009
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/02/29 16:20:56 $
    File Versie         : $Revision: 1.74 $

    $Log: tijdelijkebulkordersv2List.php,v $
    Revision 1.74  2020/02/29 16:20:56  rvv
    *** empty log message ***

    Revision 1.73  2019/11/27 11:53:36  rvv
    *** empty log message ***

    Revision 1.72  2019/11/27 11:31:28  rvv
    *** empty log message ***

    Revision 1.71  2019/11/23 18:52:32  rvv
    *** empty log message ***

    Revision 1.70  2019/09/21 16:30:12  rvv
    *** empty log message ***

    Revision 1.69  2019/08/28 15:41:48  rvv
    *** empty log message ***

    Revision 1.68  2019/08/24 17:00:00  rvv
    *** empty log message ***

    Revision 1.67  2018/11/17 17:30:55  rvv
    *** empty log message ***

    Revision 1.66  2018/11/10 18:21:35  rvv
    *** empty log message ***

    Revision 1.65  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.64  2018/04/18 16:15:08  rvv
    *** empty log message ***

    Revision 1.63  2018/04/14 17:21:13  rvv
    *** empty log message ***

    Revision 1.62  2018/04/12 06:07:15  rvv
    *** empty log message ***

    Revision 1.61  2018/04/11 15:19:17  rvv
    *** empty log message ***

    Revision 1.60  2018/04/07 15:23:45  rvv
    *** empty log message ***

    Revision 1.59  2017/11/18 18:57:19  rvv
    *** empty log message ***

    Revision 1.58  2017/11/12 13:25:34  rvv
    *** empty log message ***

    Revision 1.57  2017/11/11 18:22:43  rvv
    *** empty log message ***

    Revision 1.56  2017/09/03 11:39:56  rvv
    *** empty log message ***

    Revision 1.55  2017/08/21 11:07:09  rvv
    *** empty log message ***

    Revision 1.54  2017/07/30 10:17:58  rvv
    *** empty log message ***

    Revision 1.53  2017/07/29 17:17:11  rvv
    *** empty log message ***

    Revision 1.52  2017/07/15 16:11:15  rvv
    *** empty log message ***

    Revision 1.51  2017/07/08 17:15:51  rvv
    *** empty log message ***

    Revision 1.50  2017/07/02 12:11:28  rvv
    *** empty log message ***

    Revision 1.49  2017/07/01 17:20:46  rvv
    *** empty log message ***

    Revision 1.48  2017/06/25 10:33:30  rvv
    *** empty log message ***

    Revision 1.47  2017/06/24 16:33:47  rvv
    *** empty log message ***

    Revision 1.46  2017/06/21 16:08:14  rvv
    *** empty log message ***

    Revision 1.45  2017/05/10 14:39:25  rvv
    *** empty log message ***

    Revision 1.44  2017/05/07 09:20:32  rvv
    *** empty log message ***

    Revision 1.43  2017/05/07 08:10:33  rvv
    *** empty log message ***

    Revision 1.42  2017/05/06 17:22:56  rvv
    *** empty log message ***

    Revision 1.41  2017/05/03 16:19:17  rvv
    *** empty log message ***

    Revision 1.40  2017/04/29 17:22:39  rvv
    *** empty log message ***

    Revision 1.39  2017/04/19 16:02:23  rvv
    *** empty log message ***

    Revision 1.38  2017/03/31 15:38:23  rvv
    *** empty log message ***

    Revision 1.37  2017/03/15 16:34:28  rvv
    *** empty log message ***

    Revision 1.36  2017/02/11 17:32:35  rvv
    *** empty log message ***

    Revision 1.35  2016/12/10 19:24:51  rvv
    *** empty log message ***

    Revision 1.34  2016/10/26 16:15:33  rvv
    *** empty log message ***

    Revision 1.33  2016/09/29 08:45:13  rvv
    *** empty log message ***

    Revision 1.32  2016/09/28 08:55:58  rvv
    *** empty log message ***

    Revision 1.31  2016/09/17 07:49:37  rvv
    *** empty log message ***

    Revision 1.30  2016/07/27 15:56:14  rvv
    *** empty log message ***

    Revision 1.29  2016/07/24 09:25:42  rvv
    *** empty log message ***

    Revision 1.28  2016/07/20 16:07:33  rvv
    *** empty log message ***

    Revision 1.27  2016/07/13 15:41:08  rvv
    *** empty log message ***

    Revision 1.26  2016/06/05 12:18:55  rvv
    *** empty log message ***

    Revision 1.25  2016/06/01 12:09:32  rvv
    *** empty log message ***

    Revision 1.24  2016/06/01 07:38:39  rvv
    *** empty log message ***

    Revision 1.23  2016/05/26 06:39:05  rvv
    *** empty log message ***

    Revision 1.22  2016/05/25 15:43:02  rvv
    *** empty log message ***

    Revision 1.21  2016/05/19 12:58:37  rvv
    *** empty log message ***

    Revision 1.20  2016/05/09 06:37:17  rvv
    *** empty log message ***

    Revision 1.19  2016/04/22 14:44:29  rm
    orders

    Revision 1.18  2016/04/17 17:13:43  rvv
    *** empty log message ***

    Revision 1.17  2016/04/08 14:18:51  rm
    no message

    Revision 1.16  2016/02/28 17:20:32  rvv
    *** empty log message ***

    Revision 1.15  2016/02/21 17:21:12  rvv
    *** empty log message ***

    Revision 1.14  2016/02/14 11:17:26  rvv
    *** empty log message ***

    Revision 1.13  2016/02/10 17:18:41  rvv
    *** empty log message ***

    Revision 1.12  2015/12/20 17:22:55  rvv
    *** empty log message ***

    Revision 1.11  2015/12/13 17:57:40  rvv
    *** empty log message ***

    Revision 1.10  2015/12/06 18:53:13  rvv
    *** empty log message ***

    Revision 1.9  2015/12/06 18:01:51  rvv
    *** empty log message ***

    Revision 1.8  2015/12/04 13:35:18  rvv
    *** empty log message ***

    Revision 1.7  2015/11/27 15:18:17  rm
    OrdersV2

    Revision 1.6  2015/11/15 12:19:09  rvv
    *** empty log message ***

    Revision 1.5  2015/11/07 16:29:39  rvv
    *** empty log message ***

    Revision 1.4  2015/11/01 18:00:22  rvv
    *** empty log message ***

    Revision 1.3  2015/10/18 13:45:01  rvv
    *** empty log message ***

    Revision 1.2  2015/10/11 17:30:58  rvv
    *** empty log message ***

    Revision 1.1  2015/09/30 07:53:45  rvv
    *** empty log message ***
*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/orderRegelsAanmaken.php");
include_once('orderControlleRekenClassV2.php');

$content['style'] = $editcontent['style'];


if($_GET['verschilRekeningOrdersAanmaken']==1)
{
  $restPortefeuille='0000-0000-0000-0';
  //$restPortefeuille='9901-0000-0009-6';
  
  $db=new DB();
  $query="SELECT externeBatchId FROM TijdelijkeBulkOrdersV2 GROUP BY externeBatchId";//WHERE externeBatchId<>''
  $db->SQL($query);
  $db->Query();
  $batchIdAantal=$db->records();
  $tmp=$db->nextRecord();
  $batchId=$tmp['externeBatchId'];
  
  $query="SELECT aantal, fonds,transactieSoort FROM TijdelijkeBulkOrdersV2
WHERE portefeuille='$restPortefeuille' AND externeBatchId='".mysql_real_escape_string($batchId)."'";
  $db->SQL($query);
  $db->Query();
  $correctieAanwezig=$db->records();
  
  if($batchIdAantal==1 && $correctieAanwezig==0)
  {
    $query="SELECT client FROM Portefeuilles WHERE Portefeuille='$restPortefeuille'";
    $db->SQL($query);
    $db->Query();
    $client=$db->nextRecord();
    
    $rapDatum=getLaatsteValutadatum();
    $rapDatumJul=db2jul($rapDatum);
    $restPortefeuilleRegels=berekenPortefeuilleWaarde($restPortefeuille,$rapDatum,false,'EUR',$rapDatum);
    $huidigeRestAantallen=array();
    foreach($restPortefeuilleRegels as $regel)
    {
      if($regel['fonds']<>'')
      {
        $huidigeRestAantallen[$regel['fonds']]=$regel['totaalAantal'];//+5;
      }
    }


    $query="SELECT sum(aantal) as aantal, fonds,transactieSoort FROM TijdelijkeBulkOrdersV2 WHERE externeBatchId='".mysql_real_escape_string($batchId)."' GROUP BY fonds,transactieSoort ORDER BY fonds,transactieSoort";
    $db->SQL($query);
    $db->Query();
    $aankoopAantallen=array();
    $orderTotalen=array();
    $restAantallen=array();
    while($data=$db->nextRecord())
    {
      $afgerond=ceil($data['aantal']);
      $orderTotalen[$data['fonds']][$data['transactieSoort']]=$data['aantal'];
      $transactieAantallen[$data['fonds']][$data['transactieSoort']]=$afgerond;
      $restAantallen[$data['fonds']][$data['transactieSoort']]=$afgerond-$data['aantal'];
    }
   
  //  $huidigeRestAantallen

    $nieuweOrders=array();
    $nieuweAantallen=array();
    foreach($restAantallen as $fonds=>$transacties)
    {
      $nieuweAantallen[$fonds]=$huidigeRestAantallen[$fonds];
      foreach($transacties as $transactieType=>$aantal)
      {
        if($transactieType=='A')
          $nieuweAantallen[$fonds]+=$aantal;
        elseif($transactieType=='V')
          $nieuweAantallen[$fonds]-=$aantal;
      }
      if($nieuweAantallen[$fonds]<1)
      {
        $bijkopen=1-floor($nieuweAantallen[$fonds]);
        $restAantallen[$fonds]['A']+=$bijkopen;
        $nieuweAantallen[$fonds]+=$bijkopen;
        //echo $bijkopen."a<br>\n";
      }
      elseif($nieuweAantallen[$fonds]>2)
      {
        $verkopen=floor($nieuweAantallen[$fonds])-1;
        $restAantallen[$fonds]['V']+=$verkopen;
        $nieuweAantallen[$fonds]-=$verkopen;
        //echo $verkopen."v<br>\n";
      }
     
    }
    $nieuweOrders=$restAantallen;


  
    include_once('orderControlleRekenClassV2.php');
    $ordercheck=new orderControlleBerekeningV2(true);
    $fix = new AE_FIXtransport();
    
    foreach($nieuweOrders as $fonds=>$orderDetails)
    {
  
      $query="SELECT id,OptieType,OptieExpDatum,OptieUitoefenPrijs,optieCode,fondssoort,Fondseenheid,Omschrijving as fondsOmschrijving,ISINCode FROM Fondsen WHERE fonds='".mysql_escape_string($fonds)."'";
      $db->SQL($query);
      $db->Query();
      $extraFondsData = $db->nextRecord();
  
      foreach($orderDetails as $transactieType=>$aantal)
      {
        if($transactieType=='V')
          $aantal=$aantal*-1;
        
        $extraData = $ordercheck->getPortefeuilleOpties($restPortefeuille, $fonds);
        $extraData['transactieSoort'] = $ordercheck->getTransactieSoort($restPortefeuille, $fonds,$aantal, $rapDatumJul);
        $orderregel['fondsBankcode']=$fix->getFondscode($extraData['Depotbank'], $fonds);
        $fondsKoers=globalGetFondsKoers($fonds,$rapDatum);
        $valutaKoers=globalGetValutaKoers($extraData['fondsValuta'],$rapDatum);

        $query = "INSERT INTO TijdelijkeBulkOrdersV2 SET add_user='$USR',change_user='$USR',add_date=NOW(),change_date=NOW()," .
          " bron = 'modelControle', " .
          " fonds = '" . mysql_escape_string($fonds) . "', " .
          " fondsOmschrijving = '" . mysql_escape_string($extraFondsData['fondsOmschrijving']) . "', " .
          " ISINCode = '" . $extraFondsData['ISINCode'] . "', " .
          " portefeuille = '" . $restPortefeuille . "', " .
          " accountmanager = '" . $extraData['accountmanager'] . "', " .
          " client = '" . mysql_escape_string($client['client']) . "', " .
          " aantal = '" . round(abs($aantal),4) . "', " .
          " Rekening = '" . $extraData['Rekening'] . "', " .
          " transactieSoort = '" . $extraData['transactieSoort'] . "', " .
          " fondsValuta = '" . $extraData['fondsValuta'] . "', " .
          " koers ='" . $fondsKoers. "', " .
          " orderbedrag = '" . round(-1*$aantal*$fondsKoers*$valutaKoers*$extraFondsData['Fondseenheid'],2) . "', " .
          " externeBatchId = '" . $batchId . "', " .
          " fondseenheid= '" . mysql_escape_string($extraFondsData['Fondseenheid']) . "', " .
          " fondssoort= '" . mysql_escape_string($extraFondsData['fondssoort']) . "', " .
          " optieSymbool= '" . mysql_escape_string($extraFondsData['optieCode']) . "', " .
          " optieType= '" . mysql_escape_string($extraFondsData['OptieType']) . "', " .
          " optieUitoefenprijs= '" . mysql_escape_string($extraFondsData['OptieUitoefenPrijs']) . "', " .
          " optieExpDatum= '" . mysql_escape_string($extraFondsData['OptieExpDatum']) . "', " .
          " fondsBankcode= '" . mysql_escape_string($orderregel['fondsBankcode']) . "', " .
          " beurs= '" . mysql_escape_string($orderregel['beurs']) . "', " .
          " depotbank = '" . $extraData['Depotbank'] . "' ";
       //echo $query."<br>\n";
        $db->SQL($query);
        $db->Query();
      }
      
    }
    $st='style="text-align:right;width:75px;font-weight: bold;"';
    if(count($nieuweOrders)>0)
    {
      $message.="<br><table border='1'><tr><td style='font-weight: bold;'>Fonds</td>
<td $st>Aankopen</td><td $st>Verkopen</td><td $st>EP</td>
<td $st>Aankopen</td><td $st>Verkopen</td>
<td $st>Nw EP</td>
<td $st>Tot. Aankopen</td><td $st>Tot. Verkopen</td></tr>";
      $st='style="text-align:right;width:75px"';
      foreach ($nieuweOrders as $fonds => $orderData)
      {
        $message .= "<tr><td>$fonds</td>
<td $st>".round($orderTotalen[$fonds]['A'],4)."</td>
<td $st>".round($orderTotalen[$fonds]['V'],4)."</td>
<td $st>".round($huidigeRestAantallen[$fonds],4)."</td>
<td $st>".round($restAantallen[$fonds]['A'],4)."</td>
<td $st>".round($restAantallen[$fonds]['V'],4)."</td>
<td $st>".round($nieuweAantallen[$fonds],4)."</td>
<td $st>".round($orderTotalen[$fonds]['A']+$nieuweOrders[$fonds]['A'],4)."</td>
<td $st>".round($orderTotalen[$fonds]['V']+$nieuweOrders[$fonds]['V'],4)."</td></tr>\n";
       }
      $message.="</table>";
    }
 
  }
  else
  {
    if($correctieAanwezig>0)
    {
      $message='<br>Er zijn al records voor '.$restPortefeuille.' aanwezig voor deze batch. Verwerking afgebroken.';
    }
    else
    {
      $message = "<br>Aantal verschillende externeBatchIds (" . $batchIdAantal . "). Verwachte aantal 1.";
    }
  }
  unset($_POST);
  
}

if($_GET['lookup']=='kleineOrders' || $_GET['lookup']=='kleineOrdersVerwijderen')
{
  global $USR;
  $AEJson = new AE_Json();
  $db=new DB();

  $db->SQL('SELECT max(Vermogensbeheerders.OrderOrderdesk) as OrderOrderdesk, max(Gebruikers.orderdesk) as orderdeskMedewerker
FROM Vermogensbeheerders 
JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder  AND  VermogensbeheerdersPerGebruiker.Gebruiker = "'.$USR.'"  
JOIN Gebruikers ON VermogensbeheerdersPerGebruiker.Gebruiker=Gebruikers.Gebruiker');
  $gebruikersGegevens=$db->lookupRecord();

  if($gebruikersGegevens['OrderOrderdesk']==1 && $gebruikersGegevens['orderdeskMedewerker']==0)
  {
    if($_SESSION['usersession']['gebruiker']['Accountmanager']<>'')
      $where=" AND accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR (add_user='$USR' AND bron='bulkInvoer') ";
    else
      $where=" AND add_user='$USR' ";
  }

  if($_GET['lookup']=='kleineOrdersVerwijderen')
  {
    $verwijderIds=array();
    foreach($_POST as $key=>$value)
    {
      if(substr($key,0,6)=='portc_')
      {
        $ids=explode(",",$value);
        foreach($ids as $id)
          $verwijderIds[] = $id;
      }
    }
    $query = "DELETE FROM TijdelijkeBulkOrdersV2 WHERE 1 $where AND ABS(orderbedrag) < '" . $_POST['orderBedrag'] . "' AND id IN('".implode("','",$verwijderIds)."')";
    $db->SQL($query);
    $db->query();
  }
  else
  {

    $query = "SELECT id, portefeuille,orderbedrag FROM TijdelijkeBulkOrdersV2 WHERE 1 $where AND  ABS(orderbedrag) < '" . $_POST['orderBedrag'] . "' AND id IN ('".implode("','", $_POST['ids']) ."')";
    $db->SQL($query);
    $db->query();

    $som = array();
    while ($data = $db->nextRecord())
    {
      $som[$data['portefeuille']]['totaal'] += $data['orderbedrag'];
      if ($data['orderbedrag'] < 0)
      {
        $som[$data['portefeuille']]['neg'] += $data['orderbedrag'];
      }
      if ($data['orderbedrag'] > 0)
      {
        $som[$data['portefeuille']]['pos'] += $data['orderbedrag'];
      }
      $som[$data['portefeuille']]['aantal']++;
      if($som[$data['portefeuille']]['ids']=='')
        $som[$data['portefeuille']]['ids']=$data['id'];
      else
        $som[$data['portefeuille']]['ids'].=','.$data['id'];

    }
    $table = '</br>
      <div class="box box12" >
        <div class="btn-group" role="group" style="height:22px;">
          <div class="btn-new btn-default" style="width:100px;float:left;" onclick="checkAllP(1);">&nbsp;&nbsp;<img src="icon/16/checks.png" class="simbisIcon" /> Alles </div>
          <div class="btn-new btn-default" style="width:100px;float:left;" onclick="checkAllP(0);">&nbsp;&nbsp;<img src="icon/16/undo.png" class="simbisIcon" /> Niets </div>
          <div class="btn-new btn-default" style="width:100px;float:left;" onclick="checkAllP(-1);">&nbsp;&nbsp;<img src="icon/16/replace2.png" class="simbisIcon" /> Omkeren</div>
        </div>
      </div>
</br></br></br>
<table>';
    $table .= "<tr class='list_kopregel' > <td><b>Check</b></td><td><b>Portefeuille</b></td><td align='right'><b>Aantal orders</b></td> <td align='right'><b>Aankopen</b></td> <td align='right'><b>Verkopen</b></td><td align='right'><b>Saldo</b></td></tr>\n";
    $n = 0;
    foreach ($som as $portefeuille => $regelData)
    {
      $n++;
      $table .= "<tr class='list_dataregel' onmouseover=\"this.className='list_dataregel_hover'\" onmouseout=\"this.className='list_dataregel'\" >
   <td><input type='checkbox' value='".$regelData['ids']."' name='portc_$n' id='portc_$n'> </td><td>" . $portefeuille . "</td><td align='right'>" . $regelData['aantal'] . "</td>  <td align='right'>" . $regelData['neg'] . "</td> <td align='right'>" . $regelData['pos'] . "</td>  <td align='right'>" . $regelData['totaal'] . "</td>  </tr>\n";
    }
    $table .= '<table>';

    echo $AEJson->json_encode(
      array(
        'success' => true,
        'table'   => $table
      )
    );
    exit();
  }
}

if($_GET['lookup']=='afrondenOphalen' || $_GET['lookup']=='afrondenVerwerken')
{
  global $USR;
  $AEJson = new AE_Json();
  $db=new DB();
  
  $afronding=1;
  if($_POST['afrondingsAantal'])
    $afronding=abs(floatval($_POST['afrondingsAantal']));
  if($afronding<0.000001)
    $afronding=0.000001;
  
  if($_POST['afrondMethode']=='afronden')
    $methode='round';
  elseif($_POST['afrondMethode']=='omhoog')
    $methode='ceil';
  elseif($_POST['afrondMethode']=='omlaag')
    $methode='floor';
  $db->SQL('SELECT max(Vermogensbeheerders.OrderOrderdesk) as OrderOrderdesk, max(Gebruikers.orderdesk) as orderdeskMedewerker
FROM Vermogensbeheerders
JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder  AND  VermogensbeheerdersPerGebruiker.Gebruiker = "'.$USR.'"
JOIN Gebruikers ON VermogensbeheerdersPerGebruiker.Gebruiker=Gebruikers.Gebruiker');
  $gebruikersGegevens=$db->lookupRecord();
  
  if($gebruikersGegevens['OrderOrderdesk']==1 && $gebruikersGegevens['orderdeskMedewerker']==0)
  {
    if($_SESSION['usersession']['gebruiker']['Accountmanager']<>'')
      $where=" AND accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR (add_user='$USR' AND bron='bulkInvoer') ";
    else
      $where=" AND add_user='$USR' ";
  }
  
  if($_GET['lookup']=='afrondenVerwerken')
  {
    $query = "UPDATE TijdelijkeBulkOrdersV2 SET aantal=$methode(CAST(aantal AS DECIMAL(14,6))/$afronding)*$afronding WHERE 1 $where AND id IN ('".implode("','", $_POST['ids']) ."')";
    $db->SQL($query);
    $db->query();
    logit($query);
    echo $AEJson->json_encode(
      array(
        'success' => true,
        'table'   => 'Done'
      ));
      exit;
  }
  else
  {
    $query = "SELECT id, portefeuille,fondsOmschrijving,aantal,$methode(CAST(aantal AS DECIMAL(14,6))/$afronding)*$afronding as afronding FROM TijdelijkeBulkOrdersV2 WHERE 1 $where AND id IN ('".implode("','", $_POST['ids']) ."')";
    $db->SQL($query);
    $db->query();
  
    $regels = array();
    while ($data = $db->nextRecord())
    {
     $regels[]=$data;
    }
    $table = '</br>
<table>';
    $table .= "<tr class='list_kopregel' > <td><b>Portefeuille</b></td><td><b>Fonds</b></td><td align='right'><b>Aantal</b></td> <td align='right'><b>Afgerond</b></td></tr>\n";
    $n = 0;
    foreach ($regels as $id => $regelData)
    {
      $n++;
      $table .= "<tr class='list_dataregel' onmouseover=\"this.className='list_dataregel_hover'\" onmouseout=\"this.className='list_dataregel'\" >
   <td>" . $regelData['portefeuille'] . "</td><td>" . $regelData['fondsOmschrijving'] . "</td><td align='right'>" . $regelData['aantal'] . "</td>  <td align='right'> " . $regelData['afronding'] . "</td></tr>\n";
    }
    $table .= '<table>';
    
    echo $AEJson->json_encode(
      array(
        'success' => true,
        'table'   => $table
      )
    );
    exit();
  }
}


if (isset($_GET['resetFilter']) && $_GET['resetFilter'] == 1) {

  unset($_SESSION['TijdelijkeBulkOrdersV2List']);
  unset($_GET['resetFilter']);
}

if(isset($__appvar['extraOrderLogging']))
  $extraLog=$__appvar['extraOrderLogging'];
else
  $extraLog=false;

if($extraLog==true)
{
  $tmpLog='POST_log |';
  foreach($_POST as $key=>$value)
    $tmpLog.="$key:$value;";
  logit($tmpLog);
}

if($_GET['rapportageInvoer']==1)
{
   $_POST['filter_0_veldnaam']='TijdelijkeBulkOrdersV2.bron';
   $_POST['filter_0_methode']='nietGelijk';
   $_POST['filter_0_waarde']='bulkInvoer';
}

session_start();
//if($_POST['listGroup']!='')
$__appvar['rowsPerPage']=10000;
//else
//  $__appvar['rowsPerPage']=5000;

$db=new DB();
global $__ORDERvar;
//$validaties=array('aanw'=>'Aanw','short'=>'Short','liqu'=>'Liqu','zorg'=>'Zorg','risi'=>'Risi','groot'=>'Groot');

$query="SELECT Fondsen.Fonds FROM Fondsen
JOIN TijdelijkeBulkOrdersV2 ON Fondsen.Fonds=TijdelijkeBulkOrdersV2.Fonds
WHERE Fondsen.orderinlegInBedrag=1";
$db->SQL($query);
$db->Query();
$nominaalFonds=array();
while($data=$db->nextRecord())
  $nominaalFonds[$data['Fonds']]=$data['Fonds'];


if($_POST['verwerk'] > 0)
{
  $nieuweWaarden=array();
  $validateIds=array();
  $mutatieIds=array();
  foreach ($_POST as $key=>$value)
  {
    if(substr($key,0,7)=='aantal_')
    {
      $nieuweWaarden[substr($key,7)]['aantal']=$value;
    }
    if(substr($key,0,7)=='bedrag_')
    {
      $nieuweWaarden[substr($key,7)]['bedrag']=$value;
    }
    if(substr($key,0,9)=='rekening_')
    {
      $nieuweWaarden[substr($key,9)]['rekening']=$value;
    }
    if(substr($key,0,6)=='check_')
    {
      $ids[]=substr($key,6);
    }
    if(substr($key,0,24)=='order_controle_checkbox_')
    {
      $validateIds[]=$value;
      $end=substr($key,24);
      $check=explode("_",$end);
      $validateIdKeys[$value][$check[0]]=1;
    } 
  }

  if($_GET['checkOrders']==1)
  {
    $validateIds=$ids;
  }

  $orderIdIn="IN('".implode("','",$ids)."')";
  $queries=array();
  if($_POST['verwerk'] == 2)
  {

    $mutatieIds=$ids;
    $queries[]="DELETE FROM TijdelijkeBulkOrdersV2 WHERE TijdelijkeBulkOrdersV2.id $orderIdIn";
    $queries[]="DELETE FROM orderLogs WHERE orderLogs.bulkorderRecordId $orderIdIn AND orderRecordId=0";
    if($extraLog)
    {
      $query="SELECT id,aantal,rekening,portefeuille,orderbedrag,portefeuillePercentage,modelPercentage,fonds FROM TijdelijkeBulkOrdersV2 WHERE TijdelijkeBulkOrdersV2.id $orderIdIn";
      $db->SQL($query);
      $db->query();
      while ($dbWaarden = $db->nextRecord())
      {
        logIt("TijdelijkeBulkOrdersV2 mutatie: Delete Portefeuille:".$dbWaarden['portefeuille']." Fonds:".$dbWaarden['fonds']." Aantal:".$dbWaarden['aantal']);
      }
    }

  }
  elseif($_POST['verwerk'] == 1)
  {
    $orderLog=new orderLogs();
    foreach($nieuweWaarden as $id=>$nieuweWaarde)
    {
       $rekening= 
       $query="SELECT fonds,aantal,bedrag,rekening,portefeuille,orderbedrag,portefeuillePercentage,modelPercentage,depotbank,afwijkingsbedrag,transactieSoort,fondsValuta FROM TijdelijkeBulkOrdersV2 WHERE id='$id'";
       $db->SQL($query);
       $dbWaarden=$db->lookupRecord();
       if($_GET['naarNominaal']==1 && in_array($dbWaarden['fonds'],$nominaalFonds) && in_array($id,$ids) && $dbWaarden['transactieSoort']=='A')
       {
         $dbWaarden['aantal']=0;
         $nieuweWaarde['aantal']=0;
         if($dbWaarden['afwijkingsbedrag']<>0)
           $nieuweWaarde['bedrag']=$dbWaarden['afwijkingsbedrag'];
         else
           $nieuweWaarde['bedrag']=1;
  
         $valutaKoers=globalGetValutaKoers($dbWaarden['fondsValuta'],getLaatsteValutadatum());
         $nieuweWaarde['orderbedrag']=$nieuweWaarde['bedrag']*$valutaKoers;
         $extraVelden.=",orderbedrag='".round($nieuweWaarde['orderbedrag'],2)."' ";
       }
       if($dbWaarden['aantal'] <> $nieuweWaarde['aantal'] || $dbWaarden['bedrag'] <> $nieuweWaarde['bedrag'] || $dbWaarden['rekening'] <> $nieuweWaarde['rekening'] )
       {
         $extraVelden='';
         if($dbWaarden['aantal'] <> $nieuweWaarde['aantal'])
         {
           //$afm=AFMstd($dbWaarden['portefeuille'],$einddatum);
           if($dbWaarden['aantal']<>0)
           {
             $factor = $nieuweWaarde['aantal'] / $dbWaarden['aantal'];
             $extraVelden = ",orderbedrag='" . ($dbWaarden['orderbedrag'] * $factor) . "',modelPercentage='" . ($dbWaarden['modelPercentage'] * $factor) . "',afmStdevNa=0 ";
             $orderLog->addToBulkLog($id, "Portefeuille " . $dbWaarden['portefeuille'] . " aantal van " . $dbWaarden['aantal'] . " naar " . $nieuweWaarde['aantal'] . ".");
           }
         }
         if($dbWaarden['bedrag'] <> $nieuweWaarde['bedrag'])
         {
           if($nieuweWaarde['bedrag']<>0)
           {
             $factor = globalGetValutaKoers($dbWaarden['fondsValuta'],getLaatsteValutadatum());
             $extraVelden = ",orderbedrag='" . ($nieuweWaarde['bedrag'] * $factor) . "',modelPercentage=0,afmStdevNa=0 ";
             $orderLog->addToBulkLog($id, "Portefeuille " . $dbWaarden['portefeuille'] . " orderbedrag van " . $dbWaarden['orderbedrag'] . " naar " . ($nieuweWaarde['bedrag']*$factor) . ".");
           }
         }
         if($dbWaarden['rekening'] <> $nieuweWaarde['rekening'])
           $orderLog->addToBulkLog($id,"Portefeuille ".$dbWaarden['portefeuille']." rekening van ".$dbWaarden['rekening']." naar ".$nieuweWaarde['rekening'].".");

         $query="SELECT Vermogensbeheerders.OrderuitvoerBewaarder,Vermogensbeheerders.orderViaConsolidatie,Portefeuilles.consolidatie FROM Portefeuilles JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder=Vermogensbeheerders.Vermogensbeheerder WHERE Portefeuille='".mysql_real_escape_string($dbWaarden['portefeuille'])."'";
         $db->SQL($query);
         $bewaarder=$db->lookupRecord();
         if($bewaarder['OrderuitvoerBewaarder']==1 || ($bewaarder['orderViaConsolidatie']==1 && $bewaarder['consolidatie']==1))
         {
           $query="SELECT depotbank FROM Rekeningen WHERE rekening='".mysql_real_escape_string($nieuweWaarde['rekening'])."'";
           $db->SQL($query);
           $depotbank=$db->lookupRecord();
           if($depotbank['depotbank'] <> $dbWaarden['depotbank'] || $dbWaarden['depotbank']=='')
           {
             $orderLog->addToBulkLog($id,"Portefeuille ".$dbWaarden['portefeuille']." depotbank van ".$dbWaarden['depotbank']." naar ".$depotbank['depotbank'].".");
             $extraVelden.=",depotbank='".mysql_real_escape_string($depotbank['depotbank'])."'";
           }
         }
         if($dbWaarden['transactieSoort']=='A')
           $aantalTeken="+";
         else
           $aantalTeken='-';

         $query="UPDATE TijdelijkeBulkOrdersV2 SET rekening='".mysql_real_escape_string($nieuweWaarde['rekening'])."',
                        aantal='".round($nieuweWaarde['aantal'],6)."',nieuwAantal=(aantalInPositie $aantalTeken '".round($nieuweWaarde['aantal'],6)."'), bedrag='".round($nieuweWaarde['bedrag'],2)."' $extraVelden WHERE id='$id'";

         if($extraLog)
           logIt("TijdelijkeBulkOrdersV2 mutatie: Portefeuille:".$dbWaarden['portefeuille']." $query");
         $db->SQL($query);
         $db->Query();
         $mutatieIds[]=$id;
       }  
    }
    $queries[]="UPDATE TijdelijkeBulkOrdersV2 SET naarOrder=0 WHERE TijdelijkeBulkOrdersV2.id $orderIdIn";
  }

  if(checkOrderAcces('orderGeenHervalidatie') == false)
  {
    $query = "SELECT id FROM TijdelijkeBulkOrdersV2 WHERE portefeuille IN (SELECT portefeuille FROM TijdelijkeBulkOrdersV2 WHERE TijdelijkeBulkOrdersV2.id IN('" . implode("','", $mutatieIds) . "') GROUP BY portefeuille)";
    $db->SQL($query);
    $db->query();
    $mutatieIds = array();
    while ($data = $db->nextRecord())
    {
      $mutatieIds[] = $data['id'];
    }
  }
  foreach($queries as $query)
  {
    $db->SQL($query);
    $db->Query();
  }
}



$subHeader     = "";
$mainHeader    = " Verwerk geselecteerde fondsregels tot orders.";

$editScript = "TijdelijkeBulkOrdersV2Edit.php";
$allow_add  = false;

$list = new MysqlList2();
//$list->idField = "id";
$list->idTable="TijdelijkeBulkOrdersV2";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("","sel",array("list_width"=>"30","search"=>false));
//$list->addFixedField("TijdelijkeBulkOrdersV2","naarOrder",array("list_width"=>"50","search"=>false));
$checks=getActieveControles();
foreach($checks as $check=>$checkOmschrijving)
  $list->addFixedField("TijdelijkeBulkOrdersV2","validatie".ucfirst($check),array("list_width"=>"50","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","transactieSoort",array("list_width"=>"100","search"=>false,"list_align"=>'right'));
$list->addFixedField("TijdelijkeBulkOrdersV2","aantal",array("list_width"=>"100","search"=>false));
if(count($nominaalFonds)>0)
  $list->addFixedField("TijdelijkeBulkOrdersV2","bedrag",array("list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","fonds",array("list_width"=>"200","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","ISINCode",array("list_width"=>"100","search"=>false));
//$list->addFixedField("TijdelijkeBulkOrdersV2","valuta",array("list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","portefeuille",array("list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","accountmanager",array("list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","rekening",array("list_width"=>"130","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","add_user",array("list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","bron",array("list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","depotbank",array("list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","controleStatus",array("list_invisible"=>'true',"list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","controleRegels",array("list_invisible"=>'true',"list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","validatieVast",array("list_invisible"=>'true',"list_width"=>"100","search"=>false));

$html = $list->getCustomFields(array('TijdelijkeBulkOrdersV2'),'TijdelijkeBulkOrdersV2List');

$db->SQL('SELECT max(Vermogensbeheerders.OrderOrderdesk) as OrderOrderdesk, max(Gebruikers.orderdesk) as orderdeskMedewerker
FROM Vermogensbeheerders 
JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder  AND  VermogensbeheerdersPerGebruiker.Gebruiker = "'.$USR.'"  
JOIN Gebruikers ON VermogensbeheerdersPerGebruiker.Gebruiker=Gebruikers.Gebruiker');
$gebruikersGegevens=$db->lookupRecord();

if($gebruikersGegevens['OrderOrderdesk']==1 && $gebruikersGegevens['orderdeskMedewerker']==0)
{
  if($_SESSION['usersession']['gebruiker']['Accountmanager']<>'')
   $list->setWhere("accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR (add_user='$USR' AND bron='bulkInvoer') ");
  else
    $list->setWhere("add_user='$USR' $extraWhere");
}
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);

//$list->setGroupBy('TijdelijkeBulkOrdersV2.portefeuille');

// select page
$list->selectPage($_GET['page']);

if($extraLog==true)
{
  logIt('TijdelijkeBulkOrdersV2 listing ophalen');
}
$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

$_SESSION['submenu'] = New Submenu();

//$_SESSION['submenu']->addItem('Opnieuw valideren',"tijdelijkebulkordersv2List.php?checkOrders=1");
//$_SESSION['submenu']->addItem('Verwerken',"tijdelijkebulkordersv2Verwerken.php");
$_SESSION['submenu']->addItem($html,"");

if($_POST['listGroup']!='')
  $groupSelectieStyle="style='display:none'";

$toValidationBtn = '';
if ( true ) //checkOrderAcces('verwerkenBulk_valideren') === true 
{
  $validationBtn = '<div class="btn-new btn-default" style="width:210px;float:left;" onclick="javascript: if(fromChanged){AEConfirm(\'De aantallen/rekening wijzigingen worden niet opgeslagen. Wilt u doorgaan?\',\'Naar validatie\',function (){ clickValidatie();} ,function (){ })} else {clickValidatie();};"><img src="icon/16/navigate_right.png" class="simbisIcon" /> Naar validatie</div>
  <div class="btn-new btn-default " style="width:180px;float:left;" onclick="javascript:clickHerrekenen();"><img src="icon/16/checks.png" class="simbisIcon" /> Selectie herrekenen</div>';
}
else
{
  $validationBtn = '<div class="btn-new btn-default" style="width:210px;float:left;"><img src="icon/16/navigate_right.png" class="simbisIcon" /> Geen validatie rechten</div>';
}
 
if ( checkOrderAcces('rapportages_aanmaken') === true )
{
  $bewerkBtn ='<div class="btn-new btn-default" style="width:210px;float:left;" onclick="javascript:clickOpslaan();"><img src="icon/16/refresh.png" class="simbisIcon" /> Aanpassingen opslaan</div>
  <div id="divVerwijderen" class="btn-new btn-default" style="width:180px;float:left;display:none" onclick="javascript:if(fromChanged){AEConfirm(\'De overige aantallen/rekening wijzigingen worden niet opgeslagen. Wilt u doorgaan?\',\'Records verwijderen\',function (){ clickDelete();} ,function (){ })} else {AEConfirm(\'Wilt u doorgaan met het verwijderen van de geselecteerde records?\',\'Records verwijderen\',function (){ clickDelete();} ,function (){ })};"><img src="icon/16/delete.png" class="simbisIcon" /> Verwijder selectie <span id="aantalSelected"> </span></div>';

 // if($__appvar["bedrijf"] == "ANO" || $__appvar["bedrijf"] == "TEST")
 // {
    //$_SESSION['submenu']->addItem("Kleine orders verwijderen", 'javascript:parent.frames[\'content\'].verwijderKleineOrders(\'\');');
  $verwijderKleinBtn ='<div id="divVerwijderenKlein" class="btn-new btn-default" style="width:180px;float:left;display:none" onclick="javascript:verwijderKleineOrders(\'\');" style="width:180px;float:left;"><img src="icon/16/delete.png" class="simbisIcon" /> Kleine orders verwijderen <span id="aantalSelected"> </span></div>';
  $afrondenBtn = '<div id="divAfronden" class="btn-new btn-default" style="width:180px;float:left;display:none" onclick="javascript:afrondenOrders(\'\');" style="width:180px;float:left;"><img src="icon/16/gear.png" class="simbisIcon" /> Afronden orders <span id="aantalSelected"> </span></div>';
  if($__appvar["bedrijf"] == "TEST"||
     $__appvar["bedrijf"] == "BGD"||
     $__appvar["bedrijf"] == "VCK"||
     $__appvar["bedrijf"] == "VCKACC"||
     $__appvar["bedrijf"] == "VRY"||
     $__appvar["bedrijf"] == "VRYACC" )
  {
    $verschilRekeningBtn = '<div id="divVerschilRekening" class="btn-new btn-default" style="width:180px;float:left" onclick="javascript:verschilRekeningOrders(\'\');" style="width:180px;float:left;"><img src="icon/16/refresh.png" class="simbisIcon" /> Verschilrekening orders <span id="VerschilRekeningSelected"> </span></div>';
  }
  if($__appvar["bedrijf"] == "TEST" ||1 )
  {
    $naarNominaalBtn = '<div id="divNaarNominaal" class="btn-new btn-default" style="width:180px;float:left" onclick="javascript:naarNominaal(\'\');" style="width:180px;float:left;"><img src="icon/16/gear.png" class="simbisIcon" /> Omzetten naar nominaal <span id="VnaarNominaalSelected"> </span></div>';
  }
 // }
}
else
{
  $bewerkBtn ='<div id="divVerwijderen" class="btn-new btn-default" style="width:180px;float:left;display:none"><img src="icon/16/delete.png" class="simbisIcon" /> Geen verwijder rechten <span id="aantalSelected"> </span></div>';
}

$content['pageHeader'] = '
  <br><div class="edit_actionTxt"><strong>'.$mainHeader.'</strong> '.$subHeader.'</div>

  <div class="main_content">  
    <div class="row" >
      <div class="box box12" >
        <div class="btn-group" role="group" style="height:22px;">
          '.$validationBtn.$bewerkBtn.$verwijderKleinBtn.$afrondenBtn.$verschilRekeningBtn.$naarNominaalBtn.'
    </div>
      </div>   
    </div>
    <br />
    <div class="row" id="groupSelectie" '.$groupSelectieStyle.' >
      <div class="box box12" >
        <div class="btn-group" role="group" style="height:22px;">
          <div class="btn-new btn-default" style="width:150px;float:left;" onclick="checkAll(1);">&nbsp;&nbsp;<img src="icon/16/checks.png" class="simbisIcon" /> Alles selecteren</div>
          <div class="btn-new btn-default" style="width:150px;float:left;" onclick="checkAll(0);">&nbsp;&nbsp;<img src="icon/16/undo.png" class="simbisIcon" /> Niets selecteren</div>
          <div class="btn-new btn-default" style="width:160px;float:left;" onclick="checkAll(-1);">&nbsp;&nbsp;<img src="icon/16/replace2.png" class="simbisIcon" /> Selectie omkeren</div>
        </div>
      </div>
    </div>
    <br />
    <div class="row" >
      <div class="box box12" >
        <div class="btn-group" role="group" style="height:22px;">
          <div class="btn-new btn-default" style="width:150px;float:left;" onclick="document.listForm.verwerk.value=0;document.listForm.listGroup.value=\'\';document.listForm.submit();">Group geen</div>
          <div class="btn-new btn-default" style="width:150px;float:left;" onclick="document.listForm.verwerk.value=0;document.listForm.listGroup.value=\'fonds\';document.listForm.submit();">Group fonds</div>
          <div class="btn-new btn-default" style="width:150px;float:left;" onclick="document.listForm.verwerk.value=0;document.listForm.listGroup.value=\'portefeuille\';document.listForm.submit();">Group portefeuille</div>
          <div class="btn-new btn-default" style="width:150px;float:left;" onclick="document.listForm.verwerk.value=0;document.listForm.listGroup.value=\'accountmanager\';document.listForm.submit();">Group accountmanager </div>
          <div class="btn-new btn-default" style="width:150px;float:left;" onclick="document.listForm.verwerk.value=0;document.listForm.listGroup.value=\'gereed\';document.listForm.submit();">Gereedstaande orders</div>
          <div class="btn-new btn-default" style="width:150px;float:left;" onclick="document.listForm.verwerk.value=0;document.listForm.listGroup.value=\'add_user\';document.listForm.submit();">Group Gebuiker</div>  </div>
      </div>   
    </div>
  </div>
';




$content['javascript'] .= "

if(fromChanged==undefined)
{
  var fromChanged = false;
}

function changeInFrom()
{
  fromChanged = true;
}

function checkChangeInFrom()
{
  if(fromChanged)
  {
    if(!confirm (''))
    {
      return 0;
    }
  }
  return 1;
  
  
}

function clickHerrekenen()
{
  document.listForm.verwerk.value=1;
  document.listForm.action='tijdelijkebulkordersv2List.php?checkOrders=1';
  document.listForm.submit();
}

function clickValidatie()
{
  document.listForm.action='tijdelijkebulkordersv2Verwerken.php';
  document.listForm.verwerk.value=10;
  document.listForm.submit();
}

function clickOpslaan()
{
  document.listForm.verwerk.value=1;
  document.listForm.submit();
}

$.fn.enterKey = function (fnc) {
    return this.each(function () {
        $(this).keypress(function (ev) {
            var keycode = (ev.keyCode ? ev.keyCode : ev.which);
            if (keycode == '13') {
                fnc.call(this, ev);
            }
        })
    })
}

function clickDelete()
{
  if(checkChangeInFrom()==0){return 0};
 document.listForm.verwerk.value=2;
 document.listForm.submit();
}

function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}

function checkIds(ids,state)
{
  for(z=0; z<ids.length;z++)
  {
   var fieldname='check_'+ids[z];
   $('[name='+fieldname+']').prop('checked', state);
  }
  controleerVinkjes();
}

function checkAll(optie)
{
  var theForm = document.listForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,6) == 'check_')
   {
      if(optie == -1)
      {
        if(theForm[z].checked == true)
          theForm[z].checked=false;
        else
          theForm[z].checked=true;  
      }
      else
      {
        theForm[z].checked = optie;
      }
   }
  }
  controleerVinkjes();
}

function checkAllP(optie)
{
  var theForm = document.kleineOrders.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  { 
   if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,6) == 'portc_')
   {
      if(optie == -1)
      {
        if(theForm[z].checked == true)
          theForm[z].checked=false;
        else
          theForm[z].checked=true;  
      }
      else
      {
        theForm[z].checked = optie;
      }
   }
  }
}

function controleerVinkjes()
{
  var theForm = document.listForm.elements, z = 0, toonVerwijderen=0 ;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,6) == 'check_')
   {
      if(theForm[z].checked==true)
      {
        toonVerwijderen++;
        
      }
   }
  }

  if(toonVerwijderen>0)
  {
    $('#divVerwijderen').show();  
    $('#divVerwijderenKlein').show();
    $('#divAfronden').show();
    $('#divNaarNominaal').show();
    $('#aantalSelected').html('('+toonVerwijderen+')');
  }
  else
  {
     $('#divVerwijderen').hide();  
     $('#divVerwijderenKlein').hide();
     $('#divAfronden').hide();
     $('#divNaarNominaal').hide();
  }
      
}

function showLoading(text) {
  // add the overlay with loading image to the page
  $('#overlay').remove();
  var over = '<div id=\"overlay\"><div id=\"loading-box\">' +
          '<div id=\"loading-txt\">' + text + '</div>' +
          '<img id=\"loading-img\" src=\"images/ajax-loader.gif\">' +
          '</div></div>';
  $(over).appendTo('body');
}
;
function removeLoading() {
  $('#overlay').remove();
}


function kleineOrdersOphalen()
{
   var selectedIds = [ ];
   for(z=0; z<document.listForm.length;z++)
   {
     if(document.listForm[z].type == 'checkbox' && document.listForm[z].name.substr(0,6) == 'check_' && document.listForm[z].checked == true)
      {
        checkId=document.listForm[z].name.substr(6);
        selectedIds.push(checkId);
      }
   }
       
  var postData = {  orderBedrag : $('#orderBedrag').val(), ids : selectedIds }
  var formURL = 'tijdelijkebulkordersv2List.php';
  return $.ajax({
    url : formURL + '?lookup=kleineOrders',
    type: \"POST\",
    dataType: 'json',
    data : postData,
    success:function(data, textStatus, jqXHR) {
      if ( data.success == false ) {
        $('#verwijderList').html('<div class=\"alert alert-warning\">Orderlijst kon niet worden opgehaald</div>');
        return false;
      } else {
        $('#verwijderList').html(data.table);
        return true;
      }
    }
  });
}

function verwijderKleineOrders()
{
   varIdCount=0;
   for(z=0; z<document.listForm.length;z++)
    {
      if(document.listForm[z].type == 'checkbox' && document.listForm[z].name.substr(0,6) == 'check_' && document.listForm[z].checked == true)
      {
       varIdCount++;
      }
    }
      if(varIdCount==0)
      {
      
         AEMessage('Nog geen orderregels geselecteerd.','Kleine orders verwijderen.');
        return;
      }

 var inputform='<form method=\"POST\" onsubmit=\"return false;\" action=\"tijdelijkebulkordersv2List.php?lookup=kleineOrdersVerwijderen\" name=\"kleineOrders\" ><table border=0><tr><td>Verwijderen o.b.v. Orderbedrag</td><td><input type=\"text\" name=\"orderBedrag\" id=\"orderBedrag\" value=\"\"></td></tr><tr><td colspan=\"2\"><div id=\"verwijderList\"></div><td></tr></form> <script>$(\"#orderBedrag\").enterKey(function (){kleineOrdersOphalen();return false;})<\\/script>';
 $( \"#dialogMessage\" ).html('<div style=\"padding: 10px; max-width: 500px; word-wrap: break-word;\">' + inputform+ '</div>');
 $( \"#dialogMessage\" ).dialog({
    draggable: false,
    modal: true,
    resizable: false,
    width: 'auto',
    title: 'Verwijderen o.b.v. Orderbedrag',
    minHeight: 150,
    buttons: 
    {\"Ophalen orders\": function () 
      {
        kleineOrdersOphalen();
      },
          \"Verwijder selectie\": function () 
      {
        var nChecked=0;
        var checkedId='';
        for(z=0; z<document.kleineOrders.length;z++)
        {
          if(document.kleineOrders[z].type == 'checkbox' && document.kleineOrders[z].name.substr(0,6) == 'portc_' && document.kleineOrders[z].checked == true)
          {
            nChecked++;
          }
        }
        if(nChecked==0) { $(\"#verwijderList\").html('<div class=\"alert alert-warning\">Er is geen portefeuilles geselecteerd.</div>'); return 0; }
        document.kleineOrders.submit();
        $(this).dialog('destroy');
      }  
      ,\"Sluiten\": function () { $(this).dialog('destroy'); }  
    }
  });
}

function afrondenVerwerken(actie)
{
   var selectedIds = [ ];
   for(z=0; z<document.listForm.length;z++)
   {
     if(document.listForm[z].type == 'checkbox' && document.listForm[z].name.substr(0,6) == 'check_' && document.listForm[z].checked == true)
      {
        checkId=document.listForm[z].name.substr(6);
        selectedIds.push(checkId);
      }
   }
   
  var postData = {  afrondingsAantal : $('#afrondingsAantal').val(), afrondMethode : $('input[name=\"afrondMethode\"]:checked').val(), ids : selectedIds }
  var formURL = 'tijdelijkebulkordersv2List.php';
  return $.ajax({
    url : formURL + '?lookup='+actie,
    type: \"POST\",
    dataType: 'json',
    data : postData,
    success:function(data, textStatus, jqXHR) {
      if ( data.success == false ) {
        $('#afrondList').html('<div class=\"alert alert-warning\">Lijst kon niet worden opgehaald</div>');
        return false;
      } else {
        if(actie=='afrondenVerwerken'){
        location.href='tijdelijkebulkordersv2List.php';
        }
        $('#afrondList').html(data.table);
        return true;
      }
    }
  });
}


function afrondenOrders()
{
   varIdCount=0;
   for(z=0; z<document.listForm.length;z++)
    {
      if(document.listForm[z].type == 'checkbox' && document.listForm[z].name.substr(0,6) == 'check_' && document.listForm[z].checked == true)
      {
       varIdCount++;
      }
    }
      if(varIdCount==0)
      {
      
         AEMessage('Nog geen orderregels geselecteerd.','Orders afronden.');
        return;
      }

 var inputform='<form method=\"POST\" onsubmit=\"return false;\" action=\"tijdelijkebulkordersv2List.php?lookup=ordersAfronden\" name=\"afrondenForm\" ><table border=0><tr><td>Afrondingsaantal</td><td><input type=\"text\" name=\"afrondingsAantal\" id=\"afrondingsAantal\" value=\"\"></td></tr><tr><td><input type=\"radio\" name=\"afrondMethode\" id=\"afrondMethode\" value=\"afronden\"> Afronden</td></tr><tr><td><input type=\"radio\" name=\"afrondMethode\" id=\"afrondMethode\" value=\"omlaag\"> Afronden naar beneden</td></tr><tr><td><input type=\"radio\" name=\"afrondMethode\" id=\"afrondMethode\" value=\"omhoog\"> Afronden naar boven</td></tr><tr><td colspan=\"2\"><div id=\"afrondList\"></div><td></tr></form> <script>$(\"#afrondAantal\").enterKey(function (){afrondenOphalen();return false;})<\\/script>';
 $( \"#dialogMessage\" ).html('<div style=\"padding: 10px; max-width: 500px; word-wrap: break-word;\">' + inputform+ '</div>');
 $( \"#dialogMessage\" ).dialog({
    draggable: false,
    modal: true,
    resizable: false,
    width: 'auto',
    title: 'Afronden',
    minHeight: 150,
    buttons:
    {\"Voorbeeld tonen\": function ()
      {
        afrondenVerwerken('afrondenOphalen');
      },
          \"Doorvoeren\": function ()
      {
        afrondenVerwerken('afrondenVerwerken');
        $(this).dialog('destroy');
      }
      ,\"Sluiten\": function () { $( \"#dialogMessage\" ).html(''); $(this).dialog('destroy'); }
    }
  });
}

function naarNominaal()
{
  document.listForm.verwerk.value=1;
  document.listForm.action='tijdelijkebulkordersv2List.php?naarNominaal=1';
  document.listForm.submit();
}

function verschilRekeningOrders()
{
  document.listForm.verwerk.value=1;
  document.listForm.action='tijdelijkebulkordersv2List.php?verschilRekeningOrdersAanmaken=1';
  document.listForm.submit();
}


";
echo template($__appvar["templateContentHeader"],$content);

$disableEdit=true;

if($message<>'')
{
  echo $message;
}


function getPortefeuilleRekeningen($portefeuille,$depotbank='')
{
  global $rekeningenPerPortefeuille;
  
  if(isset($rekeningenPerPortefeuille[$portefeuille][$depotbank]))
    return $rekeningenPerPortefeuille[$portefeuille][$depotbank];


   $db=new DB();


  $query="SELECT Vermogensbeheerders.orderViaConsolidatie,Vermogensbeheerders.OrderuitvoerBewaarder,Portefeuilles.consolidatie FROM Portefeuilles
JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder=Vermogensbeheerders.Vermogensbeheerder WHERE Portefeuille='$portefeuille'";
  $db->SQL($query);
  $portdata=$db->lookupRecord();

  if($portdata['OrderuitvoerBewaarder']==1 || ($portdata['orderViaConsolidatie']==1 && $portdata['consolidatie']==1))
  {
    $query = "SELECT if(Rekeningen.Afdrukvolgorde=0,99,Rekeningen.Afdrukvolgorde) as volgordeRekening, 
if(Rekeningen.Depotbank='$depotbank',0,1) as volgordeDepot,
 if(Rekeningen.Valuta='EUR',0,1) as volgordeValuta, 
 Rekeningen.Rekening,Rekeningen.Portefeuille,Rekeningen.Valuta,Rekeningen.Depotbank
FROM Rekeningen WHERE 
Rekeningen.Deposito = 0 AND Rekeningen.Inactief = 0  AND Rekeningen.Memoriaal = 1 AND
Rekeningen.Portefeuille='$portefeuille' ORDER BY volgordeRekening,volgordeDepot,volgordeValuta,Rekeningen.afdrukVolgorde,rekening";

  }
  else
  {
    $query = "SELECT if(Rekeningen.Depotbank='$depotbank',0,1) as volgordeDepot,
 if(Rekeningen.Valuta='EUR',0,1) as volgordeValuta, 
 Rekeningen.Rekening,Rekeningen.Portefeuille,Rekeningen.Valuta,Rekeningen.Depotbank
FROM Rekeningen WHERE 
Rekeningen.Memoriaal = 0 AND Rekeningen.Inactief = 0 AND Rekeningen.Deposito = 0 AND 
Rekeningen.Portefeuille='$portefeuille' ORDER BY volgordeDepot,volgordeValuta,Rekeningen.afdrukVolgorde,rekening";
  }
  $db->SQL($query);
  $db->Query();
  
  while($data=$db->nextRecord())
    $rekeningenPerPortefeuille[$portefeuille][$depotbank][]=$data['Rekening'];

  return $rekeningenPerPortefeuille[$portefeuille][$depotbank];
}

function createSelect($name,$values,$selectedValue)
{
  $html="<select name='$name' onchange='javascript:changeInFrom();'>";
  if(!in_array($selectedValue,$values))
  {
    $values[] = '';
    $selectedValue='';
  }
  foreach($values as $value)
  {
    if($value==$selectedValue)
      $html.="<option SELECTED value='$value'>$value</option>"; 
    else
      $html.="<option value='$value'>$value</option>"; 
  }
   $html.="</select>\n";  
   return $html;
}

function checkConsolidatie($portefeuille,$fonds,$type='orderAanwezig')
{
  $db=new DB();
  $query="SELECT id FROM Portefeuilles where Portefeuille='$portefeuille' AND consolidatie=1";
  $db->SQL($query);
  $consolidatie=$db->lookupRecord();
  $portefeuilles=array();
  if(count($consolidatie)>0)
  {
    if($type=='orderAanwezig')
    {
      $query="SELECT * FROM GeconsolideerdePortefeuilles where VirtuelePortefeuille='$portefeuille'";
      $db->SQL($query);
      $consolidatie=$db->lookupRecord();
      foreach ($consolidatie as $key => $value)
      {
        if (substr($key, 0, 12) == 'Portefeuille' && $value <> '')
        {
          $portefeuilles[] = $value;
        }
      }
      $query="SELECT Portefeuille FROM PortefeuillesGeconsolideerd where VirtuelePortefeuille='$portefeuille'";
      $db->SQL($query);
      $db->query();
      while($port=$db->nextRecord())
      {
        $portefeuilles[]=$port['Portefeuille'];
      }
  
      $query = "SELECT count(id) as aantal FROM TijdelijkeBulkOrdersV2 WHERE portefeuille IN('" . implode("','", $portefeuilles) . "') AND fonds='" . mysql_real_escape_string($fonds) . "'";
      $db->SQL($query);
      $aantal = $db->lookupRecord();
      if ($aantal['aantal'] > 0)
      {
        return true;
      }
      else
      {
        return false;
      }
    }
    else
    {
      return true;
    }
  }
  return false;
  
}

function checkRekeningGebruik($portefeuille,$depot='')
{
  global $__checkRekeningGebruikPortefeuille;

  $db=new DB();

  $query="SELECT Vermogensbeheerders.orderViaConsolidatie,Vermogensbeheerders.OrderuitvoerBewaarder,Portefeuilles.Vermogensbeheerder,Portefeuilles.Depotbank  FROM Portefeuilles
JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder=Vermogensbeheerders.Vermogensbeheerder WHERE Portefeuille='$portefeuille'";
  $db->SQL($query);
  $portefeuilleData=$db->lookupRecord();

  if($depot<>'')
    $portefeuilleData['Depotbank']=$depot;


  if(isset($__checkRekeningGebruikPortefeuille[$portefeuilleData['Vermogensbeheerder']][$portefeuilleData['Depotbank']]))
    return $__checkRekeningGebruikPortefeuille[$portefeuilleData['Vermogensbeheerder']][$portefeuilleData['Depotbank']];

  if($portefeuilleData['OrderuitvoerBewaarder']==1 || $portefeuilleData['orderViaConsolidatie']==1)
  {
    $__checkRekeningGebruikPortefeuille[$portefeuilleData['Vermogensbeheerder']][$portefeuilleData['Depotbank']]=1;
    return 1;
  }

  $query="SELECT rekeningNrTonen FROM fixDepotbankenPerVermogensbeheerder WHERE Vermogensbeheerder='".$portefeuilleData['Vermogensbeheerder']."' AND depotbank='".$portefeuilleData['Depotbank']."'";
  $db->sql($query);
  $FixDepotbankenPerVermogensbeheerder=$db->lookupRecord();
  if($db->records())
  {
    $__checkRekeningGebruikPortefeuille[$portefeuilleData['Vermogensbeheerder']][$portefeuilleData['Depotbank']]=$FixDepotbankenPerVermogensbeheerder['rekeningNrTonen'];
    return $FixDepotbankenPerVermogensbeheerder['rekeningNrTonen'];
  }

  $query="SELECT orderRekeningTonen FROM Depotbanken WHERE depotbank='".$portefeuilleData['Depotbank']."'";
  $db->sql($query);
  $depotbanken=$db->lookupRecord();
  if($db->records())
  {
    $__checkRekeningGebruikPortefeuille[$portefeuilleData['Vermogensbeheerder']][$portefeuilleData['Depotbank']]=$depotbanken['orderRekeningTonen'];
    return $depotbanken['orderRekeningTonen'];
  }

}

$loadingActief=false;


$list->extraFormHeaderTags='action="tijdelijkebulkordersv2List.php"';
?>
<br>

<div id="dialogMessage" title="Basic dialog"></div>

<div class="main_content">
  
  <div class="row">
    <div class="formHolder box box12">
      <div class="formTitle textB">Filters</div>
      <div class="formContent padded-10">
        <?=$list->filterHeader();?>
      </div>
    </div>
  </div>
<div>
   <div class="row">
    <div class="formHolder box box12 {fieldsetClass}">
      <div class="formTitle textB">Bulkorders</div>
      <div class="formContent">
        <form name="listForm" id="listForm" method="POST" action="tijdelijkebulkordersv2List.php">
      
            <input type="hidden" name="verwerk" value="1">
            <input type="hidden" id="listGroup" name="listGroup" value="<?=$_POST['listGroup']?>">
            <input type="hidden" id="openGroup" name="openGroup" value="<?=$_POST['openGroup']?>">
            <?php
            if($_POST['listGroup']!='')
            {
              
              $listHeaderOrg=$list->printHeader($disableEdit);
              //echo $listHeaderOrg;
              $re = "/<colgroup>.*<\/colgroup>/i"; 
              preg_match($re, $listHeaderOrg, $matches); 
              $listHeader=$matches[0].'<tr class="list_kopregel"><td>Sel</td>';
              foreach($list->columns as $colData)
              {
                $column = array_merge($list->objects[$colData['objectname']]->data['fields'][$colData['name']],$colData['options']);
                //listarray($column);
                if(strpos($listHeaderOrg,$colData['name'])!==false)
                  $listHeader.='<td class="list_kopregel_data">'.$column['description'].'</td>';
              }
              $listHeader.= '</tr>'; 
              
             // listarray($list->columns);exit;
            }  
            else
            {
              echo "<table>";
              echo $list->printHeader($disableEdit);
            }  
          //  listarray($listHeader);exit;


            while($data = $list->getRow())
            {
  
              //$data['TijdelijkeBulkOrdersV2.aantalInPositie']['value']=getFondsAantal($data['TijdelijkeBulkOrdersV2.portefeuille']['value'],$data['TijdelijkeBulkOrdersV2.fonds']['value']);
              
              foreach($data as $key=>$value)
                $data[$key]['noClick']=$disableEdit;
              $regelOk=true;

              $export['controleRegels']=unserialize($data['TijdelijkeBulkOrdersV2.controleRegels']['value']);

              $revalidatieId=in_array($data['id']['value'],$validateIds);
              $revalidatieMutatie=in_array($data['id']['value'],$mutatieIds);

              if( $revalidatieId || $revalidatieMutatie)
              {
                if($loadingActief==false)
                {
                  echo "<script>showLoading('Valideren');</script>";
                  flush();
                  ob_flush();
                  $loadingActief=true;
                }
                if($data['id']['value'] != "")
                {

                  $order = new orderControlleBerekeningV2(true);
                  //validatieVast
                 // listarray($revalidatieId);
                  if($revalidatieId)
                    $validatieType=2;
                  else
                    $validatieType=$revalidatieMutatie;
       //       echo $validatieType;
                  if($extraLog)
                    logIt("updateChecksByBulkorderregelId id:".$data['id']['value']."|validatieType: $validatieType | validatieId: $revalidatieId | validatieMut: $revalidatieMutatie");

                  $result=$order->updateChecksByBulkorderregelId($data['id']['value'],$validateIdKeys[$data['id']['value']],$validatieType);
                  $export['controleRegels']=$result['controleRegels'];
                  $data['TijdelijkeBulkOrdersV2.controleStatus']['value']=$result['controleStatus'];

                }
              }
              else
              {
                if($loadingActief==false)
                {
                  echo "<script>showLoading('Laden');</script>";
                  flush();
                  ob_flush();
                  $loadingActief=true;
                }
              }
              if($data['TijdelijkeBulkOrdersV2.controleStatus']['value']=='')
                $data['TijdelijkeBulkOrdersV2.controleStatus']['value']=1;

             // listarray($export['controleRegels']);
              //listarray($export);
             /*
             verwerken
             alles selecteren
             niets selecteren
             */
            $disableCheckbox=false;
            foreach($checks as $check=>$checkOmschrijving)
            {
              $checkVeld='TijdelijkeBulkOrdersV2.validatie'.ucfirst($check);
              if(is_array($export['controleRegels'][$check]))
              {
                $checkData=$export['controleRegels'][$check];
                $data[$checkVeld]['list_nobreak']=true;
                $data[$checkVeld]['value']='';
                $title='';
                $title=str_replace('<br>','',$checkData['resultaat']);
                $data[$checkVeld]['value'].= "<label title='".$title."'>";//substr($check,0,1)
                if($checkData['checked'] == 1)
                {
                  $data[$checkVeld]['list_nobreak']=true;
                  $data[$checkVeld]['value']='<input type="checkbox" checked disabled >';
                  $data[$checkVeld]['td_style']='style="background-color:#66CC66;text-align:center" ';
                }
                elseif($checkData['short'] > 0)
                {
                   //$data[$checkVeld]['value'].="<input type=\"checkbox\" name=\"order_controle_checkbox_".$data['id']['value']."\" value=\"1\">";
                  $data[$checkVeld]['value'].="Fout";
                  if($checkData['short']==1 && $check=='liqu')
                    $data[$checkVeld]['td_style']='style="background-color:#FFA500;text-align:center" ';
                  elseif($checkData['short']>=1)
                    $data[$checkVeld]['td_style']='style="background-color:#FAA39A;text-align:center" ';
                }
                else
                {
                  if($title<>'')
                  {
                    $data[$checkVeld]['value'].="Ok";
                    $data[$checkVeld]['td_style']='style="background-color:#66CC66;text-align:center" ';
                  }
                  elseif($checkData['checked'] >0)
                  {
                    $data[$checkVeld]['value'].="<input disabled checked type=\"checkbox\" >";
                    $data[$checkVeld]['td_style']='style="background-color:#66CC66;text-align:center" ';  
                  }
                  else
                  {
                    $data[$checkVeld]['value'].="na";
                    $data[$checkVeld]['td_style']='style="background-color:#FFCC66;text-align:center"" ';
                  }    
                }
                $data[$checkVeld]['value'].="</label> ";
              }
              else
              {
                $data[$checkVeld]['value']="na";
                //$disableCheckbox=true;
              }
              
            }
  
              //if($data['TijdelijkeBulkOrdersV2.depotbank']['value']=='NB'||$data['TijdelijkeBulkOrdersV2.depotbank']['value']=='')
              //  $disableCheckbox=true;
              
              
              if($disableCheckbox==false)
                $data['.sel']['value']="<input onclick=\"javascript:controleerVinkjes();\" type=\"checkbox\" name=\"check_".$data['id']['value']."\" value=\"1\">";
              $data['.sel']['list_nobreak']=true;
              $data['disableEdit']=$disableEdit;
              $data['TijdelijkeBulkOrdersV2.aantal']['list_nobreak']=true;
              $data['TijdelijkeBulkOrdersV2.bedrag']['list_nobreak']=true;
              unset($data['TijdelijkeBulkOrdersV2.aantal']['list_format']);

              if ( checkOrderAcces('verwerkenBulk_bewerken') === true )
              {
                if($data['TijdelijkeBulkOrdersV2.bedrag']['value']<>0)
                  $readonly='readonly';
                else
                  $readonly='';
                $data['TijdelijkeBulkOrdersV2.aantal']['value']='<input style="text-align:right;width:75px" type="text" '.$readonly.' name="aantal_'.$data['id']['value'].'" value="'.$data['TijdelijkeBulkOrdersV2.aantal']['value'].'" onchange="javascript:changeInFrom();">';
                if(checkRekeningGebruik($data['TijdelijkeBulkOrdersV2.portefeuille']['value'],$data['TijdelijkeBulkOrdersV2.depotbank']['value'])==1)
                {

                    $data['TijdelijkeBulkOrdersV2.rekening']['list_nobreak']=true;
                    $tmp=getPortefeuilleRekeningen($data['TijdelijkeBulkOrdersV2.portefeuille']['value'],$data['TijdelijkeBulkOrdersV2.depotbank']['value']);


                    $data['TijdelijkeBulkOrdersV2.rekening']['value']=createSelect('rekening_'.$data['id']['value'],$tmp,$data['TijdelijkeBulkOrdersV2.rekening']['value']);

                }
                else
                   $data['TijdelijkeBulkOrdersV2.rekening']['value']='';
              }

              if(in_array($data['TijdelijkeBulkOrdersV2.fonds']['value'],$nominaalFonds))
              {
                $data['TijdelijkeBulkOrdersV2.fonds']['list_nobreak']=true;
                $data['TijdelijkeBulkOrdersV2.fonds']['value']="<span style='background-color:#FFA500' title='Dit betreft een fonds waarvan de order wellicht in bedrag ingelegd dient te worden.'> ".$data['TijdelijkeBulkOrdersV2.fonds']['value']."</span>";
                if($data['TijdelijkeBulkOrdersV2.bedrag']['value']<>0)
                {
                  unset($data['TijdelijkeBulkOrdersV2.bedrag']['list_format']);
                  $data['TijdelijkeBulkOrdersV2.bedrag']['value'] = '<input style="text-align:right;width:75px" type="text" name="bedrag_' . $data['id']['value'] . '" value="' . $data['TijdelijkeBulkOrdersV2.bedrag']['value'] . '" onchange="javascript:changeInFrom();">';
                  if(isset($data['TijdelijkeBulkOrdersV2.aantalInPositie']['value']))
                    $data['TijdelijkeBulkOrdersV2.aantalInPositie']['value']='NB';
                  if(isset($data['TijdelijkeBulkOrdersV2.nieuwAantal']['value']))
                    $data['TijdelijkeBulkOrdersV2.nieuwAantal']['value']='NB';
                }
              }
              else
              {
                $data['TijdelijkeBulkOrdersV2.bedrag']['value']='';
              }
  
              if(checkConsolidatie($data['TijdelijkeBulkOrdersV2.portefeuille']['value'],$data['TijdelijkeBulkOrdersV2.fonds']['value'],'orderAanwezig')==1)
              {
                $data['TijdelijkeBulkOrdersV2.portefeuille']['list_nobreak']=true;
                $data['TijdelijkeBulkOrdersV2.portefeuille']['value']= "<span style='background-color:#c795ff' title='Consolidatie waarbij ook een order voor losse portefeuille aanwezig is.'> " .$data['TijdelijkeBulkOrdersV2.portefeuille']['value']."</span>";
              }
  
  
              if($_POST['listGroup']!='')
              { 
                if($_POST['listGroup']=='gereed')
                {
                  $groupOn = 'controleStatus';
                  if($data['TijdelijkeBulkOrdersV2.'.$groupOn]['value']==2)
                    $data['TijdelijkeBulkOrdersV2.'.$groupOn]['value']=1;
                }
                else
                  $groupOn=$_POST['listGroup'];
                 $groupRegels[$data['TijdelijkeBulkOrdersV2.'.$groupOn]['value']][$data['id']['value']]=$list->buildRow($data);
                 
                 
              }
              else
              {
                echo $list->buildRow($data);
              }
            }
            $n=1;
            $gereedVertaling=array(''=>'ongevalideerd',0=>'gereed',1=>'ongevalideerd',2=>'ongevalideerd');
            foreach($groupRegels as $group=>$regels)
            {
               if($_POST['listGroup']=='gereed')
                  $group=$gereedVertaling[$group];
               $checkOn="javascript:checkIds(['".implode("','",array_keys($regels))."'],true);";
               $checkOff="javascript:checkIds(['".implode("','",array_keys($regels))."'],false);";
              
              if($n==$_POST['openGroup'])
                $style='';
              else  
                $style='display:none';
                
              echo '<div onclick="javascript:$(\'#regels_'.$n.'\').toggle(); if($(\'#regels_'.$n.'\').attr(\'style\') == \'display: none;\'){$(\'#openGroup\').val(\'0\');} else {$(\'#openGroup\').val(\''.$n.'\');}" style="cursor: pointer;">
              '.count($regels).' orderregels onder '.$group.'. </div>';
              
              echo '<div id="regels_'.$n.'" style="'.$style.'"> <a href="'.$checkOn.'"><b>( checks on </b></a><b>/</b><a href="'.$checkOff.'"><b> checks off)</b></a> ';
              echo '<table>';
              //echo '<tr><td colspan=10>'.$portefeuille.'</td></tr>';
              echo $listHeader;
              
              foreach($regels as $index=>$regelData)
              {
                
                echo $regelData;
                
              }
              echo '</table>';
              echo '</div>';
              $n++;
            }
            if($_POST['listGroup']=='')
             echo '</table>'; 
          //  listarray($groupRegels);
            ?>
         
        </form>
      </div>
    </div>
   </div>
</div>   

<?
if($loadingActief==true)
{
  echo "<script>removeLoading();</script>";
}
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>
