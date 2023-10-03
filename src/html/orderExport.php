<?php
/*
    AE-ICT CODEX source module versie 1.6, 31 mei 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/03/21 17:02:37 $
    File Versie         : $Revision: 1.49 $

    $Log: orderExport.php,v $
    Revision 1.49  2018/03/21 17:02:37  rvv
    *** empty log message ***

    Revision 1.48  2017/05/21 09:55:56  rvv
    *** empty log message ***

    Revision 1.47  2017/03/31 15:41:49  rvv
    *** empty log message ***

    Revision 1.46  2017/03/08 16:55:25  rvv
    *** empty log message ***

    Revision 1.45  2017/03/05 12:05:38  rvv
    *** empty log message ***

    Revision 1.44  2017/01/25 15:54:06  rvv
    *** empty log message ***

    Revision 1.43  2017/01/18 17:01:34  rvv
    *** empty log message ***

    Revision 1.42  2017/01/11 17:19:05  rvv
    *** empty log message ***

    Revision 1.41  2017/01/07 16:21:02  rvv
    *** empty log message ***

    Revision 1.40  2017/01/04 16:21:18  rvv
    *** empty log message ***

    Revision 1.39  2016/12/31 18:13:10  rvv
    *** empty log message ***

    Revision 1.38  2016/12/31 17:06:14  rvv
    *** empty log message ***

    Revision 1.37  2016/12/30 20:13:40  rvv
    *** empty log message ***

    Revision 1.36  2016/09/23 09:59:45  rvv
    *** empty log message ***

    Revision 1.35  2016/08/27 07:08:09  rvv
    *** empty log message ***

    Revision 1.34  2016/07/31 17:36:35  rvv
    *** empty log message ***

    Revision 1.33  2016/07/30 17:01:35  rvv
    *** empty log message ***

    Revision 1.32  2016/07/27 15:56:14  rvv
    *** empty log message ***

    Revision 1.31  2016/07/24 09:35:11  rvv
    *** empty log message ***

    Revision 1.30  2016/07/24 09:27:22  rvv
    *** empty log message ***

    Revision 1.29  2016/02/28 17:08:44  rvv
    *** empty log message ***

    Revision 1.28  2016/02/17 19:29:53  rvv
    *** empty log message ***

    Revision 1.27  2016/02/13 14:01:08  rvv
    *** empty log message ***

    Revision 1.26  2016/02/03 16:58:03  rvv
    *** empty log message ***

    Revision 1.25  2015/12/27 16:31:46  rvv
    *** empty log message ***

    Revision 1.24  2015/12/23 16:04:30  rvv
    *** empty log message ***

    Revision 1.23  2015/11/18 17:05:01  rvv
    *** empty log message ***

    Revision 1.22  2015/11/14 13:26:59  rvv
    *** empty log message ***

    Revision 1.21  2015/11/04 17:05:08  rvv
    *** empty log message ***

    Revision 1.20  2015/11/04 16:51:45  rvv
    *** empty log message ***

    Revision 1.19  2015/10/18 13:45:01  rvv
    *** empty log message ***

    Revision 1.18  2015/10/07 19:34:27  rvv
    *** empty log message ***

    Revision 1.17  2015/10/04 11:49:46  rvv
    *** empty log message ***

    Revision 1.16  2015/09/23 15:02:51  rvv
    *** empty log message ***

    Revision 1.15  2015/09/20 17:30:13  rvv
    *** empty log message ***

    Revision 1.13  2015/09/02 07:51:44  rvv
    *** empty log message ***

    Revision 1.12  2015/07/05 08:15:09  rvv
    *** empty log message ***

    Revision 1.11  2014/11/12 16:40:11  rvv
    *** empty log message ***

    Revision 1.10  2014/11/01 22:07:07  rvv
    *** empty log message ***

    Revision 1.9  2014/06/03 13:31:28  cvs
    *** empty log message ***

    Revision 1.8  2013/10/23 18:30:40  rvv
    *** empty log message ***

    Revision 1.6  2013/05/01 15:50:07  rvv
    *** empty log message ***

    Revision 1.5  2013/04/17 11:34:25  rvv
    *** empty log message ***

    Revision 1.4  2013/04/10 15:56:57  rvv
    *** empty log message ***

    Revision 1.3  2013/04/07 16:08:24  rvv
    *** empty log message ***

    Revision 1.2  2013/04/03 14:56:36  rvv
    *** empty log message ***

    Revision 1.1  2013/03/31 12:39:16  rvv
    *** empty log message ***

*/    
include_once("wwwvars.php");
include_once("rapport/rapportRekenClass.php");

if($_GET['orderVersie']==2)
  $orderVersie=2;
else
  $orderVersie=1;


function getBrokerinstructies($vermogensbeheerder='',$valuta='EUR')
{
  $db=new DB();
  $query="SELECT portefeuille,iban FROM Brokerinstructies WHERE vermogensbeheerder='$vermogensbeheerder' AND vvSettlement='$valuta' AND depotbank='KAS'";
  $data = $db->lookupRecordByQuery($query);
  return $data;
}

function substr_replace_array($leeg, $waarden , $start)
{
  foreach($waarden as $i=>$value)
  {
    $lenght=strlen($value);
    $leeg = substr_replace($leeg, $value, $start[$i],$lenght);
  }
  return $leeg;
}

function getOptieSymbool($depotbank,$fonds)
{
    global $__fixVars;
    $db=new DB();
    if(isset($__fixVars['BankDepotCodes'][$depotbank]))
      $bankcodeVeld=$__fixVars['BankDepotCodes'][$depotbank];
    else
      return 'unknown';

     $query="SELECT OptieBovenliggendFonds,OptieType,OptieExpDatum,OptieUitoefenPrijs,".$bankcodeVeld." as bankCode,optieCode FROM Fondsen WHERE Fonds='".mysql_real_escape_string($fonds)."'";
    $db->SQL($query);
    $fondsRecord=$db->lookupRecord();

  //  if($fondsRecord['OptieBovenliggendFonds']=='')
  //    return 'geenOptie';

    if($fondsRecord['optieCode'] <> '')
    {
      $query="SELECT optie".$bankcodeVeld." as bankCode FROM fondsOptieSymbolen WHERE `key`='".$fondsRecord['optieCode']."' AND Fonds='".mysql_real_escape_string($fondsRecord['OptieBovenliggendFonds'])."'";
      $db->SQL($query);
      $optieSymbolenRecord=$db->lookupRecord();
      $symbool=$fondsRecord['optieCode'];
    }

    if($optieSymbolenRecord['bankCode']=='')
    { //tweede poging.
      $optieParts=explode(" ",$fonds);
      $symbool=$optieParts[0];
      $query="SELECT optie".$bankcodeVeld." as bankCode FROM fondsOptieSymbolen WHERE `key`='$symbool' AND Fonds='".mysql_real_escape_string($fondsRecord['OptieBovenliggendFonds'])."'";
      $db->SQL($query);
      $optieSymbolenRecord=$db->lookupRecord();
    }
    if($optieSymbolenRecord['bankCode']!='')
      $symbool=$optieSymbolenRecord['bankCode'];

    return $symbool;

}


$db=new DB();

$exportStamp=$__appvar['bedrijf'].date('Ymd_Hi');

if($orderVersie==2)
{
  if (strpos($_SESSION['lastListQuery'], 'OrdersV2.id as id') > 0)
  {
    if (strpos($_SESSION['lastListQuery'], 'enkeleOrderRegels') > 0)
    {
      $query = "CREATE TEMPORARY TABLE enkeleOrderRegels
        SELECT OrderRegelsV2.*
        FROM OrdersV2 INNER JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid 
        WHERE OrdersV2.OrderSoort <> 'M'
        GROUP BY OrdersV2.id  ";
      $db->SQL($query);
      $db->Query();
      $query = "ALTER TABLE enkeleOrderRegels ADD INDEX( orderid ); ";
      $db->SQL($query);
      $db->Query();
    }
    $tmp = explode("LIMIT", $_SESSION['lastListQuery']);
    $ids = array();
    $db->SQL($tmp[0]);
    $db->Query();
    while ($data = $db->nextRecord())
    {
      $ids[] = $data['id'];
    }
    $extraWhere = " AND OrdersV2.id IN('" . implode("','", $ids) . "')";
  }
  $ids = array();
  foreach ($_POST as $key => $value)
  {
    if (substr($key, 0, 3) == 'id_')
    {
      $ids[] = substr($key, 3);
    }
  }
  if (count($ids) > 0)
  {
    $extraWhere .= " AND OrdersV2.id IN('" . implode("','", $ids) . "')";
  }
}
else
{
if(strpos($_SESSION['lastListQuery'],'Orders.id as id') > 0)
{
  if(strpos($_SESSION['lastListQuery'],'enkeleOrderRegels') > 0)
  {
     $query="CREATE TEMPORARY TABLE enkeleOrderRegels
        SELECT OrderRegels.*
        FROM Orders INNER JOIN OrderRegels ON Orders.orderid = OrderRegels.orderid 
        WHERE Orders.OrderSoort <> 'M'
        GROUP BY Orders.orderid  ";
      $db->SQL($query); 
      $db->Query();
      $query="ALTER TABLE enkeleOrderRegels ADD INDEX( orderid ); ";
      $db->SQL($query);
      $db->Query();
  } 
  $tmp=explode("LIMIT",$_SESSION['lastListQuery']);
  $ids=array();
  $db->SQL($tmp[0]);
  $db->Query();
  while($data=$db->nextRecord())
  {
   $ids[]=$data['id'];
  }
  $extraWhere= " AND Orders.id IN('".implode("','",$ids)."')";
}

$ids=array();
foreach ($_POST as $key=>$value)
{
  if(substr($key,0,3)=='id_')
    $ids[]=substr($key,3);
}
if(count($ids)>0)
  $extraWhere .= " AND Orders.id IN('".implode("','",$ids)."')";
}


 if($_GET['type']=='v2')
{
  //$header=array("field1","field2","field3","field4","field5","field6","field7","field8","field9","field10","field11","field12","field13","field14","field15","field16","field17","field18","field19","field20","field21","field22","field23","field24","field25","field26","field27","field28","field29","field30","field31","field32","field33","field34","field35","field36","field37","field38","field39","field40","field41","field42","field43","field44","field45","field46","field47","field48","field49","field50","field51","field52");
  $header=array(
    "Transaction Type",
    "Instruction Type",
    "Your reference",
    "Safekeeping Account",
    "Cash Account",
    "Trade Date",
    "Settlement",
    "Settlement Quantity",
    "Settlement Amount",
    "Currency",
    "ISIN",
    "PSET",
    "PSAFE",
    "Name/Address",
    "BIC",
    "Participant Number",
    "Account Number",
    "Name/Address",
    "BIC","Participant Number","Account Number","Name/Address","BIC","Participant Number","Account Number","Deal Currency","Deal Price","Broker Currency","Broker Costs","Stamp Currency","Stampable Consideration","Additional Charges Currency","Additional Charges","info 1","info2","info3","Reg details1","reg details2","reg details3","Settlement Transaction Type ","Stock Loan Margin","Stamp Status Code","Stamp Duty Amount Currency","Stamp Duty Amount","Charity Id","Transaction Reference","Levy","Bargain Condition","Accrued Interest Currency","Accrued Interest","Transaction Tax Currency","Transaction Tax");
  $template=array('','NEWM','','223633968','NL19KASA0223633968','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','');
  // call 9400
  // $template=array('','NEWM','','223633968','NL19KASA0223633968','','','','','','','','','','JCCAPAP1XXX','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','');
  $xlsdata=array();
  //$xlsdata[]=$header;
  $now=date("Ymd H:i:s");
  $db=new DB();
  $query="SELECT code,BICcode FROM BICcodes";
  $db->SQL($query);
  $db->Query();
  $bicLookup=array();
  while($data=$db->nextRecord())
  {
    $bicLookup[$data['code']]=$data['BICcode'];
  }
  
  $query="SELECT Depotbank,BICcode FROM Depotbanken";
  $db->SQL($query);
  $db->Query();
  $bicDepotLookup=array();
  while($data=$db->nextRecord())
  {
    $bicDepotLookup[$data['Depotbank']]=$data['BICcode'];
  }
  
  if($orderVersie==2)
  {
    $query="SELECT
  (SELECT OrderUitvoeringV2.uitvoeringsPrijs FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid limit 1) as Fondskoers,
  Fondsen.ISINCode,
  Fondsen.beurs,
  Fondsen.fondsEenheid,
  OrderRegelsV2.aantal,
  OrdersV2.transactieSoort,
  OrdersV2.OrderSoort,
  Fondsen.Valuta,
  Fondsen.bbLandcode,
  OrdersV2.id as orderid,
  OrdersV2.Depotbank,
  (SELECT OrderUitvoeringV2.uitvoeringsDatum FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid limit 1) as uitvoeringsDatum,
  OrderRegelsV2.add_user,
  OrderRegelsV2.positie,
  Portefeuilles.Client,
  Portefeuilles.Portefeuille,
  Portefeuilles.Vermogensbeheerder,
  Portefeuilles.InternDepot,
  OrderRegelsV2.brokerkosten,
  OrderRegelsV2.kosten,
  OrderRegelsV2.opgelopenRente,
  OrdersV2.notaValutakoers as valutakoers,
  Rekeningen.valuta as orderregelValuta,
  OrderRegelsV2.nettoBedrag,
  OrderRegelsV2.PSET,
  OrderRegelsV2.PSAF,
  OrderRegelsV2.BIC_tegenpartij as BIC_tegenpartij,
  BbLandcodes.settlementDays
FROM
  OrdersV2
INNER JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid
INNER JOIN Fondsen ON OrdersV2.fonds = Fondsen.Fonds
INNER JOIN Portefeuilles ON OrderRegelsV2.portefeuille = Portefeuilles.Portefeuille
LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
LEFT JOIN Rekeningen ON OrderRegelsV2.rekening = Rekeningen.rekening
WHERE OrdersV2.orderStatus = 2 AND Fondsen.fondssoort <> 'OPT' $extraWhere";//if(OrderRegelsV2.rekening <> '',Rekeningen.valuta, Fondsen.valuta) as orderregelValuta,
  }
  else
  {
    $query="SELECT
  (SELECT OrderUitvoering.uitvoeringsPrijs FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid limit 1) as Fondskoers,
  Fondsen.ISINCode,
  Fondsen.beurs,
  Fondsen.fondsEenheid,
  OrderRegels.aantal,
  Orders.transactieSoort,
  Fondsen.Valuta,
  Fondsen.bbLandcode,
  Orders.orderid,
  Orders.Depotbank,
  Orders.uitvoeringsDatum as uitvoeringsDatumLeeg,
  (SELECT OrderUitvoering.uitvoeringsDatum FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid limit 1) as uitvoeringsDatum,
  OrderRegels.add_user,
  Portefeuilles.Client,
  Portefeuilles.Portefeuille,
  Portefeuilles.InternDepot,
  Portefeuilles.Vermogensbeheerder,
  OrderRegels.brokerkosten,
  OrderRegels.kosten,
  OrderRegels.opgelopenRente,
  OrderRegels.valutakoers,
  OrderRegels.valuta as orderregelValuta,
  OrderRegels.nettoBedrag,
  OrderRegels.PSET,
  OrderRegels.PSAF,
  OrderRegels.USDsettlement,
  BbLandcodes.settlementDays
FROM
  Orders
INNER JOIN OrderRegels ON Orders.orderid = OrderRegels.orderid
INNER JOIN Fondsen ON Orders.fonds = Fondsen.Fonds
INNER JOIN Portefeuilles ON OrderRegels.portefeuille = Portefeuilles.Portefeuille
LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
WHERE Orders.laatsteStatus = 2 $extraWhere"; //AND fondssoort <> 'OPT'";
//WHERE Orders.laatsteStatus = 2 $extraWhere AND fondssoort <> 'OPT'";
  }
  //echo  $query;exit;
  $db->SQL($query);
  $db->Query();
  $transactievertaling=array('A'=>'S','V'=>'P');
  while($data=$db->nextRecord())
  {
    if($data['aantal']==round($data['aantal']))
      $data['aantal']=round($data['aantal']);
    
    $uitvoeringsJul=db2jul($data['uitvoeringsDatum']);
    $dagvanweek=date('N',$uitvoeringsJul);
    
    $baseDays=2;
    if($data['settlementDays'] > 0)
      $baseDays=$data['settlementDays'];
    
    if($dagvanweek<=(5-$baseDays) && $dagvanweek<6)
      $extraDagen=0;
    elseif($dagvanweek<=(10-$baseDays) && $dagvanweek<6)
      $extraDagen=2;
    else
      $extraDagen=4;
    
    $settleDatum=date('d/m/Y',$uitvoeringsJul+(($baseDays+$extraDagen)*86400)+3605);
    
    $brutoBedrag=$data['aantal']*$data['Fondskoers']*$data['fondsEenheid'];
    if(substr($data['transactieSoort']=='A',0,1))
    {
      //$nettoBedrag=(($brutoBedrag+$data['opgelopenRente'])*$data['valutakoers'])+$data['kosten']+$data['brokerkosten'];
      $transactieSoort='DVP';
    }
    else
    {
      //$nettoBedrag=(($brutoBedrag+$data['opgelopenRente'])*$data['valutakoers'])-$data['kosten']-$data['brokerkosten'];
      $transactieSoort='RVP';
    }
    $nettoBedrag= $data['nettoBedrag'];
    
    $tmp=$template;
    $tmp[0]=$transactieSoort;
    //0 DVP
    //1 NEWM
    if($orderVersie==2)
    {
      if($data['OrderSoort']=='M')
        $tmp[2]=$__appvar['bedrijf'].$data['orderid'].'-'.$data['positie'];//29
      else
        $tmp[2]=$__appvar['bedrijf'].$data['orderid'];//29
    }
    else
      $tmp[2]=$data['orderid'];//29
    //3 223610348
    //4 223610348
    
    /*
          if($data['USDsettlement']==1)
          {
            $tmp[3] = '223467561';
            $tmp[4] = 'NL90KASA0223628034';
          }
    */
    $broker=array();
    $brokerTmp=getBrokerinstructies($data['Vermogensbeheerder']);
    if($brokerTmp['portefeuille'] <> '' && $brokerTmp['iban'] <> '')
    {
      $broker=$brokerTmp;
      $tmp[3] = $broker['portefeuille'];
      $tmp[4] = $broker['iban'];
    }
    if($data['Valuta']==$data['orderregelValuta'] && $data['Valuta']<>'EUR') //$data['USDsettlement']==1
    {
      $brokerTmp = getBrokerinstructies($data['Vermogensbeheerder'],$data['Valuta']);
      if ($brokerTmp['portefeuille'] <> '' && $brokerTmp['iban'] <> '')
      {
        $broker=$brokerTmp;
        $tmp[3] = $broker['portefeuille'];
        $tmp[4] = $broker['iban'];
      }
    }
    
    $tmp[5]=date('d/m/Y',$uitvoeringsJul);//34
    $tmp[6]=$settleDatum;//39
    $tmp[7]=$data['aantal'];//19
    $tmp[8]=number_format($nettoBedrag,2,'.','');//18
    $tmp[9]=$data['orderregelValuta'];//28
    $tmp[10]=$data['ISINCode'];//14
    $tmp[11]=$bicLookup[$data['PSET']];
    $tmp[12]=$bicLookup[$data['PSAF']];
    //$tmp[13]=$data['Client'];//40
    //14
    //15
    if($data['Depotbank']=='KAS')
    {
      $tmp[16]=$data['Portefeuille'];
      $tmp[18]='';
      $tmp[19]='';
    }
    else
    {
      if($data['PSET']=='EOC' || $data['PSET']=='CEDEL')
      {
        $tmp[15]=intval($data['Portefeuille']);
        $tmp[16]='';
        $tmp[18]='';
        $tmp[19]='';
      }
      else
      {
        $tmp[16]='';
        $tmp[18]=$bicDepotLookup[$data['Depotbank']];
        $tmp[19]=$data['Portefeuille'];
      }
    }
    
    // $tmp[18]=$bicLookup[$data['PSET']];
    
    $tmp[25]=$data['Valuta'];//30
    $tmp[26]=$data['Fondskoers'];//11
    
    
    //$tmp[35]=date('H:i',$uitvoeringsJul);
    //$tmp[36]=$data['add_user'];
    //$tmp[22]=number_format($brutoBedrag,2,'.','');
    //$tmp[24]=$transactievertaling[$data['transactieSoort']];
    //$tmp[1]=$now;
    //$tmp[11]=$data['Fondskoers'];
    
    //   if($data['Valuta'] <> $data['orderregelValuta'])
    //     $tmp[32]=number_format(1/getValutaKoers($data['Valuta'],$data['uitvoeringsDatum']),8,'.','');;
    
    if($data['InternDepot']==1)
    {
      //   $tmp[5]='FUNMARK';
      //   $tmp[28]=$data['Valuta'];
      //  $tmp[32]=1;
    }
    
    
    
    //if($data['bbLandcode'] <> '')
    //  $tmp[44]=$data['bbLandcode'];
    //$tmp[54]=$data['brokerkosten'];
    //$tmp[58]='EUR';
    /*
    if($data['brokerkosten']==0)
    {
      $tmp[54]='';
      $tmp[55]='';
      $tmp[56]='';
      $tmp[57]='';
      $tmp[58]='';
    }
    $tmp[59]=$data['kosten'];
    if($data['kosten']==0)
    {
      $tmp[59]='';
      $tmp[60]='';
      $tmp[61]='';
      $tmp[62]='';
      $tmp[63]='';
    }
    */
    
    $xlsdata[]=$tmp;
  }
  
  if($format=='xls')
  {
    $filename='export.xls';
    include_once('../classes/excel/Writer.php');
    $workbook = new Spreadsheet_Excel_Writer();
    $worksheet =& $workbook->addWorksheet();
    for($regel = 0; $regel < count($xlsdata); $regel++ )
    {
      for($col = 0; $col < count($xlsdata[$regel]); $col++)
      {
        $worksheet->write($regel, $col, $xlsdata[$regel][$col]);
      }
    }
    
    $workbook->send($filename);
    $workbook->close();
  }
  else
  {
    //$csvdata = generateCSV($xlsdata);
    $csvdata='';
    for ($a=0;$a<count($xlsdata);$a++)
    {
      for($b=0;$b<count($xlsdata[$a]);$b++)
      {
        $csvdata .= str_replace("\n","",$xlsdata[$a][$b]).'~';
      }
      $csvdata = substr($csvdata,0,-1);
      $csvdata .= "\r\n";
    }
    
    $filename='Orders'.$exportStamp.'.csv';
    $appType = "text/comma-separated-values";
    header('Content-type: ' . $appType);
    header("Content-Length: ".strlen($csvdata));
    header("Content-Disposition: inline; filename=\"".$filename."\"");
    header("Pragma: public");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    echo $csvdata;
    
  }
}
elseif($_GET['type']=='v3')
{
  //$header=array("field1","field2","field3","field4","field5","field6","field7","field8","field9","field10","field11","field12","field13","field14","field15","field16","field17","field18","field19","field20","field21","field22","field23","field24","field25","field26","field27","field28","field29","field30","field31","field32","field33","field34","field35","field36","field37","field38","field39","field40","field41","field42","field43","field44","field45","field46","field47","field48","field49","field50","field51","field52");
  $header=array(
    "Transaction Type",
    "Instruction Type",
    "Your reference",
    "Safekeeping Account",
    "Cash Account",
    "Trade Date",
    "Settlement",
    "Settlement Quantity",
    "Settlement Amount",
    "Currency",
    "ISIN",
    "PSET",
    "PSAFE",
    "Name/Address",
    "BIC",
    "Participant Number",
    "Account Number",
    "Name/Address",
    "BIC","Participant Number","Account Number","Name/Address","BIC","Participant Number","Account Number","Deal Currency","Deal Price","Broker Currency","Broker Costs","Stamp Currency","Stampable Consideration","Additional Charges Currency","Additional Charges","info 1","info2","info3","Reg details1","reg details2","reg details3","Settlement Transaction Type ","Stock Loan Margin","Stamp Status Code","Stamp Duty Amount Currency","Stamp Duty Amount","Charity Id","Transaction Reference","Levy","Bargain Condition","Accrued Interest Currency","Accrued Interest","Transaction Tax Currency","Transaction Tax");
  $template=array('','NEWM','','223633968','NL19KASA0223633968','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','');
  $xlsdata=array();
  //$xlsdata[]=$header;
  $now=date("Ymd H:i:s");
  $db=new DB();
  $query="SELECT code,BICcode,correspondent FROM BICcodes";
  $db->SQL($query);
  $db->Query();
  $bicLookup=array();
  $correspondentLookup=array();
  while($data=$db->nextRecord())
  {
    $bicLookup[$data['code']]=$data['BICcode'];
    $correspondentLookup[$data['code']]=$data['correspondent'];
  }
  
  $query="SELECT Depotbank,BICcode FROM Depotbanken";
  $db->SQL($query);
  $db->Query();
  $bicDepotLookup=array();
  while($data=$db->nextRecord())
  {
    $bicDepotLookup[$data['Depotbank']]=$data['BICcode'];
  }
  
  if($orderVersie==2)
  {
    $query="SELECT
  (SELECT OrderUitvoeringV2.uitvoeringsPrijs FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid limit 1) as Fondskoers,
  Fondsen.ISINCode,
  Fondsen.beurs,
  Fondsen.fondsEenheid,
  OrderRegelsV2.aantal,
  OrdersV2.transactieSoort,
  OrdersV2.OrderSoort,
  Fondsen.Valuta,
  Fondsen.bbLandcode,
  OrdersV2.id as orderid,
  OrdersV2.Depotbank,
  (SELECT OrderUitvoeringV2.uitvoeringsDatum FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid limit 1) as uitvoeringsDatum,
  OrderRegelsV2.add_user,
  OrderRegelsV2.positie,
  Portefeuilles.Client,
  Portefeuilles.Portefeuille,
  Portefeuilles.Vermogensbeheerder,
  Portefeuilles.InternDepot,
  Portefeuilles.vastetegenrekening,
  OrderRegelsV2.brokerkosten,
  OrderRegelsV2.kosten,
  OrderRegelsV2.opgelopenRente,
  OrdersV2.notaValutakoers as valutakoers,
  Rekeningen.valuta as orderregelValuta,
  OrderRegelsV2.nettoBedrag,
  OrderRegelsV2.PSET,
  OrderRegelsV2.PSAF,
  OrderRegelsV2.BIC_tegenpartij as BIC_tegenpartij,
  BbLandcodes.settlementDays
FROM
  OrdersV2
INNER JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid
INNER JOIN Fondsen ON OrdersV2.fonds = Fondsen.Fonds
INNER JOIN Portefeuilles ON OrderRegelsV2.portefeuille = Portefeuilles.Portefeuille
LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
LEFT JOIN Rekeningen ON OrderRegelsV2.rekening = Rekeningen.rekening
WHERE OrdersV2.orderStatus = 2 AND Fondsen.fondssoort <> 'OPT' $extraWhere";//if(OrderRegelsV2.rekening <> '',Rekeningen.valuta, Fondsen.valuta) as orderregelValuta,
  }
  else
  {
    $query="SELECT
  (SELECT OrderUitvoering.uitvoeringsPrijs FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid limit 1) as Fondskoers,
  Fondsen.ISINCode,
  Fondsen.beurs,
  Fondsen.fondsEenheid,
  OrderRegels.aantal,
  Orders.transactieSoort,
  Fondsen.Valuta,
  Fondsen.bbLandcode,
  Orders.orderid,
  Orders.Depotbank,
  Orders.uitvoeringsDatum as uitvoeringsDatumLeeg,
  (SELECT OrderUitvoering.uitvoeringsDatum FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid limit 1) as uitvoeringsDatum,
  OrderRegels.add_user,
  Portefeuilles.Client,
  Portefeuilles.Portefeuille,
  Portefeuilles.InternDepot,
  Portefeuilles.Vermogensbeheerder,
  Portefeuilles.vastetegenrekening,
  OrderRegels.brokerkosten,
  OrderRegels.kosten,
  OrderRegels.opgelopenRente,
  OrderRegels.valutakoers,
  OrderRegels.valuta as orderregelValuta,
  OrderRegels.nettoBedrag,
  OrderRegels.PSET,
  OrderRegels.PSAF,
  OrderRegels.USDsettlement,
  BbLandcodes.settlementDays
FROM
  Orders
INNER JOIN OrderRegels ON Orders.orderid = OrderRegels.orderid
INNER JOIN Fondsen ON Orders.fonds = Fondsen.Fonds
INNER JOIN Portefeuilles ON OrderRegels.portefeuille = Portefeuilles.Portefeuille
LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
WHERE Orders.laatsteStatus = 2 $extraWhere"; //AND fondssoort <> 'OPT'";
//WHERE Orders.laatsteStatus = 2 $extraWhere AND fondssoort <> 'OPT'";
  }
  //echo  $query;exit;
  $db->SQL($query);
  $db->Query();
  $transactievertaling=array('A'=>'S','V'=>'P');
  while($data=$db->nextRecord())
  {
    if($data['aantal']==round($data['aantal']))
      $data['aantal']=round($data['aantal']);
    
    $uitvoeringsJul=db2jul($data['uitvoeringsDatum']);
    $dagvanweek=date('N',$uitvoeringsJul);
    
    $baseDays=2;
    if($data['settlementDays'] > 0)
      $baseDays=$data['settlementDays'];
    
    if($dagvanweek<=(5-$baseDays) && $dagvanweek<6)
      $extraDagen=0;
    elseif($dagvanweek<=(10-$baseDays) && $dagvanweek<6)
      $extraDagen=2;
    else
      $extraDagen=4;
    
    $settleDatum=date('d/m/Y',$uitvoeringsJul+(($baseDays+$extraDagen)*86400)+3605);
    
    $brutoBedrag=$data['aantal']*$data['Fondskoers']*$data['fondsEenheid'];
    if(substr($data['transactieSoort']=='A',0,1))
    {
      //$nettoBedrag=(($brutoBedrag+$data['opgelopenRente'])*$data['valutakoers'])+$data['kosten']+$data['brokerkosten'];
      $transactieSoort='DVP';
    }
    else
    {
      //$nettoBedrag=(($brutoBedrag+$data['opgelopenRente'])*$data['valutakoers'])-$data['kosten']-$data['brokerkosten'];
      $transactieSoort='RVP';
    }
    $nettoBedrag= $data['nettoBedrag'];
    
    $tmp=$template;
    $tmp[0]=$transactieSoort;
    //0 DVP
    //1 NEWM
    if($orderVersie==2)
    {
      if($data['OrderSoort']=='M')
        $tmp[2]=$__appvar['bedrijf'].$data['orderid'].'-'.$data['positie'];//29
      else
        $tmp[2]=$__appvar['bedrijf'].$data['orderid'];//29
    }
    else
      $tmp[2]=$data['orderid'];//29
    //3 223610348
    //4 223610348
    
    /*
          if($data['USDsettlement']==1)
          {
            $tmp[3] = '223467561';
            $tmp[4] = 'NL90KASA0223628034';
          }
    */
    $broker=array();
    $brokerTmp=getBrokerinstructies($data['Vermogensbeheerder']);
    if($brokerTmp['portefeuille'] <> '' && $brokerTmp['iban'] <> '')
    {
      $broker=$brokerTmp;
      $tmp[3] = $broker['portefeuille'];
      $tmp[4] = $broker['iban'];
    }
    if($data['Valuta']==$data['orderregelValuta'] && $data['Valuta']<>'EUR') //$data['USDsettlement']==1
    {
      $brokerTmp = getBrokerinstructies($data['Vermogensbeheerder'],$data['Valuta']);
      if ($brokerTmp['portefeuille'] <> '' && $brokerTmp['iban'] <> '')
      {
        $broker=$brokerTmp;
        $tmp[3] = $broker['portefeuille'];
        $tmp[4] = $broker['iban'];
      }
    }
    
    $tmp[5]=date('d/m/Y',$uitvoeringsJul);//34
    $tmp[6]=$settleDatum;//39
    $tmp[7]=$data['aantal'];//19
    $tmp[8]=number_format($nettoBedrag,2,'.','');//18
    $tmp[9]=$data['orderregelValuta'];//28
    $tmp[10]=$data['ISINCode'];//14
    $tmp[11]=$bicLookup[$data['PSET']];
    $tmp[12]=$bicLookup[$data['PSAF']];
    //$tmp[13]=$data['Client'];//40
    //14
    //15
    if($data['Depotbank']=='KAS')//intern
    {
      $tmp[14]='ISAENL2AXXX';
      $tmp[16]=$data['Portefeuille'];
      $tmp[18]='';
      $tmp[19]='';
    }
    else//extern
    {
      if(trim($data['vastetegenrekening'])<>'')
        $tmp[14]=$data['vastetegenrekening'];
      else
        $tmp[14]='EXTERN';
      
      if($data['PSET']=='EOC' || $data['PSET']=='CEDEL')
      {
        $tmp[15]=intval($data['Portefeuille']);
        $tmp[16]='';
        $tmp[18]='';
        $tmp[19]='';
      }
      else
      {
        $tmp[16]='';
        $tmp[18]=$bicDepotLookup[$data['Depotbank']];
        $tmp[19]=$data['Portefeuille'];
      }
    }
    $tmp[18]=$correspondentLookup[$data['PSET']];
    $tmp[25]=$data['Valuta'];
    $tmp[26]=$data['Fondskoers'];
    
    
    //$tmp[35]=date('H:i',$uitvoeringsJul);
    //$tmp[36]=$data['add_user'];
    //$tmp[22]=number_format($brutoBedrag,2,'.','');
    //$tmp[24]=$transactievertaling[$data['transactieSoort']];
    //$tmp[1]=$now;
    //$tmp[11]=$data['Fondskoers'];
    
    //   if($data['Valuta'] <> $data['orderregelValuta'])
    //     $tmp[32]=number_format(1/getValutaKoers($data['Valuta'],$data['uitvoeringsDatum']),8,'.','');;
    
    if($data['InternDepot']==1)
    {
      //   $tmp[5]='FUNMARK';
      //   $tmp[28]=$data['Valuta'];
      //  $tmp[32]=1;
    }
    
    
    
    //if($data['bbLandcode'] <> '')
    //  $tmp[44]=$data['bbLandcode'];
    //$tmp[54]=$data['brokerkosten'];
    //$tmp[58]='EUR';
    /*
    if($data['brokerkosten']==0)
    {
      $tmp[54]='';
      $tmp[55]='';
      $tmp[56]='';
      $tmp[57]='';
      $tmp[58]='';
    }
    $tmp[59]=$data['kosten'];
    if($data['kosten']==0)
    {
      $tmp[59]='';
      $tmp[60]='';
      $tmp[61]='';
      $tmp[62]='';
      $tmp[63]='';
    }
    */
    
    $xlsdata[]=$tmp;
  }
  
  if($format=='xls')
  {
    $filename='export.xls';
    include_once('../classes/excel/Writer.php');
    $workbook = new Spreadsheet_Excel_Writer();
    $worksheet =& $workbook->addWorksheet();
    for($regel = 0; $regel < count($xlsdata); $regel++ )
    {
      for($col = 0; $col < count($xlsdata[$regel]); $col++)
      {
        $worksheet->write($regel, $col, $xlsdata[$regel][$col]);
      }
    }
    
    $workbook->send($filename);
    $workbook->close();
  }
  else
  {
    //$csvdata = generateCSV($xlsdata);
    $csvdata='';
    for ($a=0;$a<count($xlsdata);$a++)
    {
      for($b=0;$b<count($xlsdata[$a]);$b++)
      {
        $csvdata .= str_replace("\n","",$xlsdata[$a][$b]).'~';
      }
      $csvdata = substr($csvdata,0,-1);
      $csvdata .= "\r\n";
    }
    
    $filename='Orders'.$exportStamp.'.csv';
    $appType = "text/comma-separated-values";
    header('Content-type: ' . $appType);
    header("Content-Length: ".strlen($csvdata));
    header("Content-Disposition: inline; filename=\"".$filename."\"");
    header("Pragma: public");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    echo $csvdata;
    
  }
}
elseif($_GET['type']=='v2Optie')
{
 //$header=array("field1","field2","field3","field4","field5","field6","field7","field8","field9","field10","field11","field12","field13","field14","field15","field16","field17","field18","field19","field20","field21","field22","field23","field24","field25","field26","field27","field28","field29","field30","field31","field32","field33","field34","field35","field36","field37","field38","field39","field40","field41","field42","field43","field44","field45","field46","field47","field48","field49","field50","field51","field52");
 $header=array("Counterparty Market","Instruction Type","Your Reference","Debet Account","Amount","Currency","Transfer Date","Urgent Indicator","Intermediary Institution BIC","Beneficiary Bank","Beneficiary Bank Name","Beneficiary Bank Account Number","Beneficiary","Beneficiary  Name","Beneficiary  ","Info 1","Info 2","Info 3","Info 4","Charges Indication");
 //$template=array('','INP','','','','','','','','','FDX','','','','','','','','','','','','N','','','','','','','','','','','','','','','','','','','','','','','','','','');
 $template=array("NL","I","","NL90KASA0223622699","","","","FALSE","KASANL2AXXX","KASANL2AXXX","Kasbank","","KASANL2AXXX","","NL14KASA0223629014","","","","","s");
 $xlsdata=array();
 //$xlsdata[]=$header;
 $now=date("Ymd H:i:s");
 $db=new DB();
$query="SELECT code,BICcode FROM BICcodes";
$db->SQL($query);
$db->Query();
$bicLookup=array();
while($data=$db->nextRecord())
{
  $bicLookup[$data['code']]=$data['BICcode'];
}

$query="SELECT Depotbank,BICcode FROM Depotbanken";
$db->SQL($query);
$db->Query();
$bicDepotLookup=array();
while($data=$db->nextRecord())
{
  $bicDepotLookup[$data['Depotbank']]=$data['BICcode'];
}

  if($orderVersie==2)
  {
$query="SELECT
  (SELECT OrderUitvoeringV2.uitvoeringsPrijs FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid limit 1) as Fondskoers,
  Fondsen.ISINCode,
  Fondsen.beurs,
  OrderRegelsV2.aantal,
  OrdersV2.transactieSoort,
  Fondsen.bbLandcode,
  OrdersV2.id as orderid,
  OrdersV2.Depotbank,
  (SELECT OrderUitvoeringV2.uitvoeringsDatum FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid limit 1) as uitvoeringsDatum,
  OrderRegelsV2.add_user,
  Portefeuilles.Client,
  Portefeuilles.Portefeuille,
  Portefeuilles.Vermogensbeheerder,
  Portefeuilles.depotbank,
  Portefeuilles.InternDepot,
  OrderRegelsV2.brokerkosten,
  OrderRegelsV2.kosten,
  OrderRegelsV2.opgelopenRente,
  OrderRegelsV2.nettoBedrag,
  OrdersV2.notaValutakoers as valutakoers,
  Rekeningen.valuta as orderregelValuta, 
  OrderRegelsV2.PSET,
  OrderRegelsV2.PSAF,
  OrderRegelsV2.BIC_tegenpartij as USDsettlement,
  BbLandcodes.settlementDays,
  REPLACE(Rekeningen.IBANnr,' ','') as IBANnr,
  Rekeningen.Valuta as rekeningValuta,
  CRM_naw.naam,
CRM_naw.adres,
CRM_naw.pc,
CRM_naw.plaats,
CRM_naw.land,
OrdersV2.fondseenheid as fondsEenheid,
OrdersV2.fondsValuta as Valuta,
OrdersV2.optieSymbool,
OrdersV2.optieType,
OrdersV2.optieUitoefenprijs,
OrdersV2.optieExpDatum
FROM
  OrdersV2
INNER JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid
INNER JOIN Fondsen ON OrdersV2.fonds = Fondsen.Fonds
INNER JOIN Portefeuilles ON OrderRegelsV2.portefeuille = Portefeuilles.Portefeuille
LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
LEFT JOIN Rekeningen ON OrderRegelsV2.rekening = Rekeningen.rekening
LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.portefeuille
WHERE OrdersV2.orderStatus = 2 AND Fondsen.fondssoort = 'OPT' $extraWhere ";
  }
  else
  {
    $query = "SELECT
  (SELECT OrderUitvoering.uitvoeringsPrijs FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid limit 1) as Fondskoers,
  Fondsen.ISINCode,
  Fondsen.beurs,
  Fondsen.fondsEenheid,
  OrderRegels.aantal,
  Orders.transactieSoort,
  Fondsen.Valuta,
  Fondsen.fonds,
  Fondsen.bbLandcode,
  Fondsen.optiecode as optieSymboolFout,
Fondsen.optieType,
Fondsen.optieUitoefenprijs,
Fondsen.optieExpDatum,
  Orders.orderid,
  Orders.Depotbank,
  Orders.uitvoeringsDatum as uitvoeringsDatumLeeg,
  (SELECT OrderUitvoering.uitvoeringsDatum FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid limit 1) as uitvoeringsDatum,
  OrderRegels.add_user,
  Portefeuilles.Client,
  Portefeuilles.Portefeuille,
  Portefeuilles.InternDepot,
  Portefeuilles.Vermogensbeheerder,
  Portefeuilles.depotbank,
  OrderRegels.nettoBedrag,
  OrderRegels.brokerkosten,
  OrderRegels.kosten,
  OrderRegels.opgelopenRente,
  OrderRegels.valutakoers,
  OrderRegels.valuta as orderregelValuta,
  OrderRegels.PSET,
  OrderRegels.PSAF,
  OrderRegels.USDsettlement,
  BbLandcodes.settlementDays,
  REPLACE(Rekeningen.IBANnr,' ','') as IBANnr,
  Rekeningen.Valuta as rekeningValuta,
  CRM_naw.naam,
CRM_naw.adres,
CRM_naw.pc,
CRM_naw.plaats,
CRM_naw.land
FROM
  Orders
INNER JOIN OrderRegels ON OrderRegels.orderid = Orders.orderid
INNER JOIN Fondsen ON Orders.fonds = Fondsen.Fonds
INNER JOIN Portefeuilles ON OrderRegels.portefeuille = Portefeuilles.Portefeuille
LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
LEFT JOIN Rekeningen ON concat(OrderRegels.rekeningnr,OrderRegels.valuta)=Rekeningen.Rekening
LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.portefeuille
WHERE Orders.laatsteStatus = 2 AND fondssoort = 'OPT' $extraWhere";//
  }
    $db->SQL($query);
    $db->Query();
    $transactievertaling=array('A'=>'S','V'=>'P');

  $format='fixed';
  $leeg=str_repeat (" ",2000);
  $leeg35=str_repeat (" ",35);
  $velden=array('priority code','reference','filles','currency code','filler','nar debt','debit account','filler','nar corresp.','swift address','filler','nar benif bank','swift address','banks accountnr','nar benificiary','ben. Accountnr','ground for payment','ind of cost','filler','amount','indication of non-res','nature of counterparty','intended processing dat','filler','circuit code','filler','test ket','filler','option contract advice code','text on foreign exch report','code of foreign exchange report','country of foreign exchange report','filler');
  $start=array(0,1,17,41,44,66,206,241,427,567,578,613,753,764,799,939,974,1114,1115,1325,1342,1343,1344,1352,1353,1354,1358,1374,1379,1380,1460,1464,1466);
  $template=array(0,'FDX0000','','EUR','',"adres",'IBAN','','','','','','','',"adres",
    'IBAN','optie','B','','10000','1','0','ccyymmdd','','B','','','','J','','','','');
  $maanden=array('null','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');


  while($data=$db->nextRecord())
    {
      if($data['aantal']==round($data['aantal']))
        $data['aantal']=round($data['aantal']);

      $uitvoeringsJul=db2jul($data['uitvoeringsDatum']);
      $dagvanweek=date('N',$uitvoeringsJul);

      $baseDays=2;
      if($data['settlementDays'] > 0)
        $baseDays=$data['settlementDays'];

      if($dagvanweek<=(5-$baseDays) && $dagvanweek<6)
        $extraDagen=0;
      elseif($dagvanweek<=(10-$baseDays) && $dagvanweek<6)
        $extraDagen=2;
      else
        $extraDagen=4;

      $settleDatum=date('d/m/Y',$uitvoeringsJul+(($baseDays+$extraDagen)*86400)+3605);

      $brutoBedrag=$data['aantal']*$data['Fondskoers']*$data['fondsEenheid'];

      if(substr($data['transactieSoort']=='A',0,1))
      {

       // $nettoBedrag=(($brutoBedrag+$data['opgelopenRente'])*$data['valutakoers'])+$data['kosten']+$data['brokerkosten'];
        $transactieSoort='DVP';
      }
      else
      {

      //  $nettoBedrag=(($brutoBedrag+$data['opgelopenRente'])*$data['valutakoers'])-$data['kosten']-$data['brokerkosten'];
        $transactieSoort='RVP';
      }

      if($data['naam'] <> '')
      {
        $replace=substr($data['naam'],0,35);
        $line1=substr_replace($leeg35,$replace,0,strlen($replace));
        $replace=substr($data['adres'],0,35);
        $line2=substr_replace($leeg35,$replace,0,strlen($replace));
        $replace=substr($data['pc']." ".$data['plaats'], 0, 35);
        $line3=substr_replace($leeg35,$replace,0,strlen($replace));
        $replace=substr($data['land'], 0, 35);
        $line4=substr_replace($leeg35,$replace,0,strlen($replace));
        $clientNaam=$line1.$line2.$line3.$line4;
      }
      else
      {
        $replace=substr($data['Client'],0,35);
        $line1=substr_replace($leeg35,$replace,0,strlen($replace));
        $clientNaam=$line1;
      }


      $broker=getBrokerinstructies($data['Vermogensbeheerder']);
      if($broker['portefeuille'] <> '' && $broker['iban'] <> '')
      {
        $tmp[3]=$broker['portefeuille'];
        $tmp[4]=$broker['iban'];
      }
      if($data['Valuta']==$data['orderregelValuta'] && $data['Valuta']<>'EUR') //$data['USDsettlement']==1
      {
        $tmpBroker = getBrokerinstructies($data['Vermogensbeheerder'],$data['Valuta']);
        if ($tmpBroker['portefeuille'] <> '' && $tmpBroker['iban'] <> '')
        {
          $broker=$tmpBroker;
          $tmp[3] = $broker['portefeuille'];
          $tmp[4] = $broker['iban'];
        }
      }

      $waarden=$template;
      $waarden[1]=$data['orderid'];
      $waarden[3]=$data['Valuta'];


      $regel1=$template;
      $regel2=$template;


      $regel1[2]=$data['orderid'];
      $regel2[2]=$data['orderid'];

    //\\n";
    $replace="HJCO CAP Partners" ;
    $line1=substr_replace($leeg35,$replace,0,strlen($replace));
    $replace='Beursplein 37';
    $line2=substr_replace($leeg35,$replace,0,strlen($replace));
    $replace='3011AA ROTTERDAM';
    $line3=substr_replace($leeg35,$replace,0,strlen($replace));
    $adresHJCO=$line1.$line2.$line3;

    if($data['Vermogensbeheerder']=='FDX' && $data['Valuta']=='EUR')
      $broker['iban']='NL90KASA0223622699';

      if(substr($data['transactieSoort'],0,1)=='A')
      {
        $waarden[5]=$clientNaam;
        $waarden[6]=$data["IBANnr"];
        $waarden[14]=$adresHJCO;
        $waarden[15]=$broker['iban'];

        $regel1[3]=$data['IBANnr'];
        $regel2[3]='NL90KASA0223622699';
        $regel1[14]='NL90KASA0223622699';
      }
      else
      {
        $waarden[5]=$adresHJCO;
        $waarden[6]=$broker['iban'];
        $waarden[14]=$clientNaam;
        $waarden[15]=$data["IBANnr"];

        $regel1[3]='NL90KASA0223622699';
        $regel2[3]='NL90KASA0223622699';
        $regel1[14]=$data['IBANnr'];
      }

    if($data['optieSymbool']=='')
    {
      $data['optieSymbool']=getOptieSymbool($data['depotbank'],$data['fonds']);
    }
    $replace='Nota: '.$data['orderid'].' Tr.Dat: '.date('d-m-Y',$uitvoeringsJul);
    $line1=substr_replace($leeg35,$replace,0,strlen($replace));
    $replace=$data['aantal'].' '.$data['optieSymbool'].' '.$data['optieType'].' '.$maanden[intval(substr($data['optieExpDatum'],4,2))].''.substr($data['optieExpDatum'],2,2).' '.$data['optieUitoefenprijs'];
    $line2=substr_replace($leeg35,$replace,0,strlen($replace));
    $replace='Bruto: '.number_format($data['aantal']*$data['Fondskoers']*$data['fondsEenheid'],2,',','') .' Koers: '.number_format($data['Fondskoers'],2,',',''). ' '.$__ORDERvar["transactieSoort"][$data['transactieSoort']];
    $line3=substr_replace($leeg35,$replace,0,strlen($replace));

      $waarden[16]=$line1.$line2.$line3;


      $nettoLeeg=str_repeat ("0",17);
      $nettoWaarde=str_replace('.','',number_format($data['nettoBedrag'],2,'.','.'));
      $nettoLength=strlen($nettoWaarde);
      $waarden[19]=substr_replace($nettoLeeg,$nettoWaarde,17-$nettoLength,$nettoLength);
      $waarden[22]=date('Ymd');

      $regel1[4]=number_format($nettoBedrag,2,'.','');
      $regel2[4]=number_format($data['kosten']+$data['brokerkosten'],2,'.','');

      $regel1[5]=$data['rekeningValuta'];
      $regel2[5]=$data['rekeningValuta'];
      $regel1[6]=date('d/m/Y');
      $regel2[6]=date('d/m/Y');
      $regel1[11]=$data['Client'];
      $regel2[11]='HJCO kosten';

    //listarray($waarden);//exit; // $__appvar["Maanden"]

      $xlsdata[]=$regel1;
      $xlsdata[]=$regel2;


      $regels.= substr_replace_array ($leeg, $waarden , $start)."\n";

    }


if($format=='fixed')
{

  $filename='OptieOrders'.$exportStamp.'.csv';
  $appType = "text/plain";
  header('Content-type: ' . $appType);
  header("Content-Length: ".strlen($regels));
  header("Content-Disposition: attachment; filename=\"".$filename."\"");
  header("Pragma: public");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  echo $regels;
}
    elseif($format=='xls')
    {
      $filename='OptieOrders'.$exportStamp.'.xls';
      include_once('../classes/excel/Writer.php');
	    $workbook = new Spreadsheet_Excel_Writer();
      $worksheet =& $workbook->addWorksheet();
	    for($regel = 0; $regel < count($xlsdata); $regel++ )
	    {
		    for($col = 0; $col < count($xlsdata[$regel]); $col++)
		    {
		      $worksheet->write($regel, $col, $xlsdata[$regel][$col]);
		    }
	    }

      $workbook->send($filename);
	    $workbook->close();
    }
    else
    {
			//$csvdata = generateCSV($xlsdata);
      $csvdata='';
      for ($a=0;$a<count($xlsdata);$a++)
      {
        for($b=0;$b<count($xlsdata[$a]);$b++)
        {
          $csvdata .= str_replace("\n","",$xlsdata[$a][$b]).'~';
        }
        $csvdata = substr($csvdata,0,-1);
        $csvdata .= "\r\n";
      }

      $filename='OptieOrders'.$exportStamp.'.csv';
      $appType = "text/comma-separated-values";
      header('Content-type: ' . $appType);
    	header("Content-Length: ".strlen($csvdata));
    	header("Content-Disposition: inline; filename=\"".$filename."\"");
	    header("Pragma: public");
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      echo $csvdata;

		}




}
elseif($_GET['type']=='v3Optie') // call 9442
 {
   global $__debug;
   $__debug = true;
   $header = array(
     "CounterpartyMarket",
     "Instruction Type",
     "Your Ref",
     "Debet Account",
     "Amount",
     "Currency",
     "Transfer Date",
     "Urgent",
     "Intermediary Institution BIC",
     "Beneficiary Bank BI",
     "Beneficiary bank Nam",
     "Beneficiary Bank  Account Number",
     "Beneficiary BIC",
     "Beneficiary Nam",
     "Beneficiary Account Number",
     "Line1",
     "Line2",
     "Line3",
     "Line4",
     "Charges"
   );

//debug("in V3 test");
   $xlsdata=array();
   $xlsdata[]=$header;
   $now = date("Ymd H:i:s");
   $db = new DB();
   $query = "SELECT code,BICcode FROM BICcodes";
   $db->executeQuery($query);
   $bicLookup = array();
   while( $data = $db->nextRecord())
   {
     $bicLookup[$data['code']]=$data['BICcode'];
   }

   $query = "SELECT Depotbank,BICcode FROM Depotbanken";
   $db->executeQuery($query);
   $bicDepotLookup = array();
   while($data=$db->nextRecord())
   {
     $bicDepotLookup[$data['Depotbank']]=$data['BICcode'];
   }

   if($orderVersie==2)
   {
     $query="SELECT
          (SELECT OrderUitvoeringV2.uitvoeringsPrijs FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid limit 1) as Fondskoers,
          Fondsen.ISINCode,
          Fondsen.beurs,
          OrderRegelsV2.aantal,
          OrdersV2.transactieSoort,
          Fondsen.bbLandcode,
          OrdersV2.id as orderid,
          OrdersV2.Depotbank,
          (SELECT OrderUitvoeringV2.uitvoeringsDatum FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid limit 1) as uitvoeringsDatum,
          OrderRegelsV2.add_user,
          Portefeuilles.Client,
          Portefeuilles.Portefeuille,
          Portefeuilles.Vermogensbeheerder,
          Portefeuilles.depotbank,
          Portefeuilles.InternDepot,
          OrderRegelsV2.brokerkosten,
          OrderRegelsV2.kosten,
          OrderRegelsV2.opgelopenRente,
          OrderRegelsV2.nettoBedrag,
          OrdersV2.notaValutakoers as valutakoers,
          Rekeningen.valuta as orderregelValuta, 
          OrderRegelsV2.PSET,
          OrderRegelsV2.PSAF,
          OrderRegelsV2.BIC_tegenpartij as USDsettlement,
          BbLandcodes.settlementDays,
          REPLACE(Rekeningen.IBANnr,' ','') as IBANnr,
          Rekeningen.Valuta as rekeningValuta,
          CRM_naw.naam,
        CRM_naw.adres,
        CRM_naw.pc,
        CRM_naw.plaats,
        CRM_naw.land,
        OrdersV2.fondseenheid as fondsEenheid,
        OrdersV2.fondsValuta as Valuta,
        OrdersV2.optieSymbool,
        OrdersV2.optieType,
        OrdersV2.optieUitoefenprijs,
        OrdersV2.optieExpDatum
        FROM
          OrdersV2
        INNER JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid
        INNER JOIN Fondsen ON OrdersV2.fonds = Fondsen.Fonds
        INNER JOIN Portefeuilles ON OrderRegelsV2.portefeuille = Portefeuilles.Portefeuille
        LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
        LEFT JOIN Rekeningen ON OrderRegelsV2.rekening = Rekeningen.rekening
        LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.portefeuille
        WHERE Fondsen.fondssoort = 'OPT' $extraWhere ";
     // WHERE OrdersV2.orderStatus = 2 AND Fondsen.fondssoort = 'OPT' $extraWhere ";
   }
   else
   {
     $query = "SELECT
          (SELECT OrderUitvoering.uitvoeringsPrijs FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid limit 1) as Fondskoers,
          Fondsen.ISINCode,
          Fondsen.beurs,
          Fondsen.fondsEenheid,
          OrderRegels.aantal,
          Orders.transactieSoort,
          Fondsen.Valuta,
          Fondsen.fonds,
          Fondsen.bbLandcode,
          Fondsen.optiecode as optieSymboolFout,
        Fondsen.optieType,
        Fondsen.optieUitoefenprijs,
        Fondsen.optieExpDatum,
          Orders.orderid,
          Orders.Depotbank,
          Orders.uitvoeringsDatum as uitvoeringsDatumLeeg,
          (SELECT OrderUitvoering.uitvoeringsDatum FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid limit 1) as uitvoeringsDatum,
          OrderRegels.add_user,
          Portefeuilles.Client,
          Portefeuilles.Portefeuille,
          Portefeuilles.InternDepot,
          Portefeuilles.Vermogensbeheerder,
          Portefeuilles.depotbank,
          OrderRegels.nettoBedrag,
          OrderRegels.brokerkosten,
          OrderRegels.kosten,
          OrderRegels.opgelopenRente,
          OrderRegels.valutakoers,
          OrderRegels.valuta as orderregelValuta,
          OrderRegels.PSET,
          OrderRegels.PSAF,
          OrderRegels.USDsettlement,
          BbLandcodes.settlementDays,
          REPLACE(Rekeningen.IBANnr,' ','') as IBANnr,
          Rekeningen.Valuta as rekeningValuta,
          CRM_naw.naam,
        CRM_naw.adres,
        CRM_naw.pc,
        CRM_naw.plaats,
        CRM_naw.land
        FROM
          Orders
        INNER JOIN OrderRegels ON OrderRegels.orderid = Orders.orderid
        INNER JOIN Fondsen ON Orders.fonds = Fondsen.Fonds
        INNER JOIN Portefeuilles ON OrderRegels.portefeuille = Portefeuilles.Portefeuille
        LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
        LEFT JOIN Rekeningen ON concat(OrderRegels.rekeningnr,OrderRegels.valuta)=Rekeningen.Rekening
        LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.portefeuille
        WHERE  fondssoort = 'OPT' $extraWhere";//
//        WHERE Orders.laatsteStatus = 2 AND fondssoort = 'OPT' $extraWhere";//
   }
   $db->executeQuery($query);
   $transactievertaling=array('A'=>'S','V'=>'P');
//    debug($query);



   $velden=array(
     'priority code', // 0
     'reference',
     'filles',
     'currency code',
     'filler',
     'nar debt',
     'debit account',
     'filler',
     'nar corresp.',
     'swift address',
     'filler', //10
     'nar benif bank',
     'swift address',
     'banks accountnr',
     'nar benificiary',
     'ben. Accountnr',
     'ground for payment',
     'ind of cost',
     'filler',
     'amount',
     'indication of non-res', //20
     'nature of counterparty',
     'intended processing dat',
     'filler',
     'circuit code',
     'filler',
     'test ket',
     'filler',
     'option contract advice code',
     'text on foreign exch report',
     'code of foreign exchange report', //30
     'country of foreign exchange report',
     'filler');
   $start=array(
     0,  //0
     1,
     17,
     41,
     44,
     66,
     206,
     241,
     427,
     567,
     578, // 10
     613,
     753,
     764,
     799,
     939,
     974,
     1114,
     1115,
     1325,
     1342, //20
     1343,
     1344,
     1352,
     1353,
     1354,
     1358,
     1374,
     1379,
     1380,
     1460, //30
     1464,
     1466
   );

   $maanden=array('null','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');


   while($data=$db->nextRecord())
   {
//     debug($data);
     $output = array();
     if($data['aantal']==round($data['aantal']))
     {
       $data['aantal'] = round($data['aantal']);
     }

     $uitvoeringsJul=db2jul($data['uitvoeringsDatum']);
     $dagvanweek=date('N',$uitvoeringsJul);

     $baseDays=2;
     if($data['settlementDays'] > 0)
     {
       $baseDays = $data['settlementDays'];
     }

     if($dagvanweek<=(5-$baseDays) && $dagvanweek<6)
     {
       $extraDagen = 0;
     }
     elseif($dagvanweek<=(10-$baseDays) && $dagvanweek<6)
     {
       $extraDagen = 2;
     }
     else
     {
       $extraDagen = 4;
     }

     $settleDatum=date('d/m/Y',$uitvoeringsJul+(($baseDays+$extraDagen)*86400)+3605);

     $brutoBedrag=$data['aantal']*$data['Fondskoers']*$data['fondsEenheid'];

     if(substr($data['transactieSoort']=='A',0,1))
     {

       // $nettoBedrag=(($brutoBedrag+$data['opgelopenRente'])*$data['valutakoers'])+$data['kosten']+$data['brokerkosten'];
       $transactieSoort='DVP';
     }
     else
     {

       //  $nettoBedrag=(($brutoBedrag+$data['opgelopenRente'])*$data['valutakoers'])-$data['kosten']-$data['brokerkosten'];
       $transactieSoort='RVP';
     }

     $broker=getBrokerinstructies($data['Vermogensbeheerder']);
     if($broker['portefeuille'] <> '' && $broker['iban'] <> '')
     {
       $tmp[3]=$broker['portefeuille'];
       $tmp[4]=$broker['iban'];
     }
     if($data['Valuta']==$data['orderregelValuta'] && $data['Valuta']<>'EUR') //$data['USDsettlement']==1
     {
       $tmpBroker = getBrokerinstructies($data['Vermogensbeheerder'],$data['Valuta']);
       if ($tmpBroker['portefeuille'] <> '' && $tmpBroker['iban'] <> '')
       {
         $broker=$tmpBroker;
         $tmp[3] = $broker['portefeuille'];
         $tmp[4] = $broker['iban'];
       }
     }

     // call 9442  EUR variant moet nog afgemaakt worden..
     if($data['Vermogensbeheerder']=='FDX' && $data['Valuta']=='EUR')
     {
       $broker['iban']='nog in te voeren';
     }

     if($data['optieSymbool']=='')
     {
       $data['optieSymbool'] = getOptieSymbool($data['depotbank'],$data['fonds']);
     }

     $line1 = 'Nota: '.$data['orderid'].' Tr.Dat: '.date('d-m-Y',$uitvoeringsJul);
     $line2 = $data['aantal'].' '.$data['optieSymbool'].' '.$data['optieType'].' '.$maanden[intval(substr($data['optieExpDatum'],4,2))].''.substr($data['optieExpDatum'],2,2).' '.$data['optieUitoefenprijs'];
     $line3 = 'Bruto: '.number_format($data['aantal']*$data['Fondskoers']*$data['fondsEenheid'],2,',','') .' Koers: '.number_format($data['Fondskoers'],2,',',''). ' '.$__ORDERvar["transactieSoort"][$data['transactieSoort']];

     $output[0]   = "NL";
     $output[1]   = "I";
     $output[2]   = $data['orderid'];
     $output[3]   = $data["IBANnr"];
     $output[4]   = round($data['brokerkosten']+$data['kosten'],2);
     $output[5]   = $data['Valuta'];
     $output[6]   = date('d/m/Y');
     $output[7]   = "FALSE";
     $output[8]   = "";
     $output[9]   = "ISAENL2A";
     $output[10]  = "";
     $output[11]  = "";
     $output[12]  = "";
     $output[13]  = "HJCO";//substr($data['naam'],0,34);
     $output[14]  = $broker['iban'];
     $output[15]  = $line1;
     $output[16]  = $line2;
     $output[17]  = $line3;
     $output[18]  = "";
     $output[19]  = "B";

     $rows[] = $output;


   }

   $regels = "";
   $regels .= '"'.implode('"~"', $header).'"'."\r\n";
   foreach ($rows as $row)
   {
     $regels .= '"'.implode('"~"', $row).'"'."\r\n";
   }


   $filename='OptieOrders'.$exportStamp.'.csv';
   header('Content-type: text/plain');
   header("Content-Length: ".strlen($regels));
   header("Content-Disposition: attachment; filename=\"".$filename."\"");
   header("Pragma: public");
   header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
   echo $regels;

 }
elseif($_GET['type']=='v4Optie') // call 9442
 {
   global $__debug;
   $__debug = true;

   $header = array(
     "Action",
     "Entity Code",
     "Customer Reference",
     "Sender ID",
     "Ordering Account Type",
     "Ordering Account Number",
     "Ordering Account name",
     "Transaction Currency",
     "Transaction Amount",
     "Settlement Date ",
     "Beneficiary Bank  Code",
     "Beneficiary Bic Code",
     "Beneficiary Name",
     "Beneficiary Country",
     "Beneficiary Account Number Type",
     "Beneficiary Account Number",
     "Debit Communication/Sender to receiver information",
     "Credit Communication",
     "Forex Type",
     "Forex Rate",
     "Deal Reference Number ",
     "Forex Way",
     "Forex Beneficiary Currency",
     "SSI Exception",
     "Intermediary Agent 1 BIC Code",
     "Intermediary Agent 1Account Type",
     "Intermediary Agent 1 Identifier",
     "Intermediary Agent 1 Name",
     "Fees",
     "Regulatory reporting code",
     "Keyword for tag 21 (option)",
     "Debit Value Date",
     "Credit Value Date",
     "Payment  Type",
     "Intermediary Agent 2 BIC Code",
     "Intermediary Agent 2 Account Type",
     "Intermediary Agent 2 Identifier",
     "Intermediary Agent 2 Name",
     "Instruction Type",
     "User field 8"
   );


//debug("in V3 test");
   $xlsdata=array();
   $xlsdata[]=$header;
   $now = date("Ymd H:i:s");
   $db = new DB();
   $query = "SELECT code,BICcode FROM BICcodes";
   $db->executeQuery($query);
   $bicLookup = array();
   while( $data = $db->nextRecord())
   {
     $bicLookup[$data['code']]=$data['BICcode'];
   }

   $query = "SELECT Depotbank,BICcode FROM Depotbanken";
   $db->executeQuery($query);
   $bicDepotLookup = array();
   while($data=$db->nextRecord())
   {
     $bicDepotLookup[$data['Depotbank']]=$data['BICcode'];
   }

   if($orderVersie==2)
   {
     $query="SELECT
          (SELECT OrderUitvoeringV2.uitvoeringsPrijs FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid limit 1) as Fondskoers,
          Fondsen.ISINCode,
          Fondsen.beurs,
          OrderRegelsV2.aantal,
          OrdersV2.transactieSoort,
          Fondsen.bbLandcode,
          OrdersV2.id as orderid,
          OrdersV2.Depotbank,
          (SELECT OrderUitvoeringV2.uitvoeringsDatum FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid limit 1) as uitvoeringsDatum,
          OrderRegelsV2.add_user,
          Portefeuilles.Client,
          Portefeuilles.Portefeuille,
          Portefeuilles.Vermogensbeheerder,
          Portefeuilles.depotbank,
          Portefeuilles.InternDepot,
          OrderRegelsV2.brokerkosten,
          OrderRegelsV2.kosten,
          OrderRegelsV2.opgelopenRente,
          OrderRegelsV2.nettoBedrag,
          OrdersV2.notaValutakoers as valutakoers,
          Rekeningen.valuta as orderregelValuta, 
          OrderRegelsV2.PSET,
          OrderRegelsV2.PSAF,
          OrderRegelsV2.BIC_tegenpartij as USDsettlement,
          BbLandcodes.settlementDays,
          REPLACE(Rekeningen.IBANnr,' ','') as IBANnr,
          Rekeningen.Valuta as rekeningValuta,
          CRM_naw.naam,
        CRM_naw.adres,
        CRM_naw.pc,
        CRM_naw.plaats,
        CRM_naw.land,
        OrdersV2.fondseenheid as fondsEenheid,
        OrdersV2.fondsValuta as Valuta,
        OrdersV2.optieSymbool,
        OrdersV2.optieType,
        OrdersV2.optieUitoefenprijs,
        OrdersV2.optieExpDatum
        FROM
          OrdersV2
        INNER JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid
        INNER JOIN Fondsen ON OrdersV2.fonds = Fondsen.Fonds
        INNER JOIN Portefeuilles ON OrderRegelsV2.portefeuille = Portefeuilles.Portefeuille
        LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
        LEFT JOIN Rekeningen ON OrderRegelsV2.rekening = Rekeningen.rekening
        LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.portefeuille
        WHERE Fondsen.fondssoort = 'OPT' $extraWhere ";
     // WHERE OrdersV2.orderStatus = 2 AND Fondsen.fondssoort = 'OPT' $extraWhere ";
   }
   else
   {
     $query = "SELECT
          (SELECT OrderUitvoering.uitvoeringsPrijs FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid limit 1) as Fondskoers,
          Fondsen.ISINCode,
          Fondsen.beurs,
          Fondsen.fondsEenheid,
          OrderRegels.aantal,
          Orders.transactieSoort,
          Fondsen.Valuta,
          Fondsen.fonds,
          Fondsen.bbLandcode,
          Fondsen.optiecode as optieSymboolFout,
        Fondsen.optieType,
        Fondsen.optieUitoefenprijs,
        Fondsen.optieExpDatum,
          Orders.orderid,
          Orders.Depotbank,
          Orders.uitvoeringsDatum as uitvoeringsDatumLeeg,
          (SELECT OrderUitvoering.uitvoeringsDatum FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid limit 1) as uitvoeringsDatum,
          OrderRegels.add_user,
          Portefeuilles.Client,
          Portefeuilles.Portefeuille,
          Portefeuilles.InternDepot,
          Portefeuilles.Vermogensbeheerder,
          Portefeuilles.depotbank,
          OrderRegels.nettoBedrag,
          OrderRegels.brokerkosten,
          OrderRegels.kosten,
          OrderRegels.opgelopenRente,
          OrderRegels.valutakoers,
          OrderRegels.valuta as orderregelValuta,
          OrderRegels.PSET,
          OrderRegels.PSAF,
          OrderRegels.USDsettlement,
          BbLandcodes.settlementDays,
          REPLACE(Rekeningen.IBANnr,' ','') as IBANnr,
          Rekeningen.Valuta as rekeningValuta,
          CRM_naw.naam,
        CRM_naw.adres,
        CRM_naw.pc,
        CRM_naw.plaats,
        CRM_naw.land
        FROM
          Orders
        INNER JOIN OrderRegels ON OrderRegels.orderid = Orders.orderid
        INNER JOIN Fondsen ON Orders.fonds = Fondsen.Fonds
        INNER JOIN Portefeuilles ON OrderRegels.portefeuille = Portefeuilles.Portefeuille
        LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
        LEFT JOIN Rekeningen ON concat(OrderRegels.rekeningnr,OrderRegels.valuta)=Rekeningen.Rekening
        LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.portefeuille
        WHERE  fondssoort = 'OPT' $extraWhere";//
//        WHERE Orders.laatsteStatus = 2 AND fondssoort = 'OPT' $extraWhere";//
   }
   $db->executeQuery($query);

   $transactievertaling=array('A'=>'S','V'=>'P');

   $maanden=array('null','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
   $rows[] = $header;
   while($data=$db->nextRecord())
   {
//     debug($data);
     $output = array();
     if($data['aantal']==round($data['aantal']))
     {
       $data['aantal'] = round($data['aantal']);
     }

     $uitvoeringsJul=db2jul($data['uitvoeringsDatum']);
     $dagvanweek=date('N',$uitvoeringsJul);

     $baseDays=2;
     if($data['settlementDays'] > 0)
     {
       $baseDays = $data['settlementDays'];
     }

     if($dagvanweek<=(5-$baseDays) && $dagvanweek<6)
     {
       $extraDagen = 0;
     }
     elseif($dagvanweek<=(10-$baseDays) && $dagvanweek<6)
     {
       $extraDagen = 2;
     }
     else
     {
       $extraDagen = 4;
     }

     $settleDatum=date('d/m/Y',$uitvoeringsJul+(($baseDays+$extraDagen)*86400)+3605);

     $brutoBedrag=$data['aantal']*$data['Fondskoers']*$data['fondsEenheid'];

     if(substr($data['transactieSoort']=='A',0,1))
     {

       // $nettoBedrag=(($brutoBedrag+$data['opgelopenRente'])*$data['valutakoers'])+$data['kosten']+$data['brokerkosten'];
       $transactieSoort='DVP';
     }
     else
     {

       //  $nettoBedrag=(($brutoBedrag+$data['opgelopenRente'])*$data['valutakoers'])-$data['kosten']-$data['brokerkosten'];
       $transactieSoort='RVP';
     }

     $broker=getBrokerinstructies($data['Vermogensbeheerder']);
     if($broker['portefeuille'] <> '' && $broker['iban'] <> '')
     {
       $tmp[3]=$broker['portefeuille'];
       $tmp[4]=$broker['iban'];
     }
     if($data['Valuta']==$data['orderregelValuta'] && $data['Valuta']<>'EUR') //$data['USDsettlement']==1
     {
       $tmpBroker = getBrokerinstructies($data['Vermogensbeheerder'],$data['Valuta']);
       if ($tmpBroker['portefeuille'] <> '' && $tmpBroker['iban'] <> '')
       {
         $broker=$tmpBroker;
         $tmp[3] = $broker['portefeuille'];
         $tmp[4] = $broker['iban'];
       }
     }

     // call 9442  EUR variant moet nog afgemaakt worden..
     if($data['Vermogensbeheerder']=='FDX')
     {
       switch($data['Valuta'])
       {
         case "USD":
           $broker['iban']='NL16ISAE0000004276';
           break;
         case "EUR":
           $broker['iban']='NL43ISAE0000004275';
           break;
         default:
           $broker['iban']='nog in te voeren';
       }
       $tmp[4] = $broker['iban'];
     }

     if($data['optieSymbool']=='')
     {
       $data['optieSymbool'] = getOptieSymbool($data['depotbank'],$data['fonds']);
     }

     $line1 = 'Nota: '.$data['orderid'].' Tr.Dat: '.date('d-m-Y',$uitvoeringsJul);
     $line2 = $data['aantal'].' '.$data['optieSymbool'].' '.$data['optieType'].' '.$maanden[intval(substr($data['optieExpDatum'],4,2))].''.substr($data['optieExpDatum'],2,2).' '.$data['optieUitoefenprijs'];
     $line3 = 'Bruto: '.number_format($data['aantal']*$data['Fondskoers']*$data['fondsEenheid'],2,',','') .' Koers: '.number_format($data['Fondskoers'],2,',',''). ' '.$__ORDERvar["transactieSoort"][$data['transactieSoort']];

     $output[0]   = "CR";
     $output[1]   = "53284";
     $output[2]   = $data['orderid'];
     $output[3]   = "";
     $output[4]   = "IBN";
     $output[5]   = $data["IBANnr"];
     $output[6]   = "X";
     $output[7]   = $data['Valuta'];
     $output[8]   = round($data['brokerkosten']+$data['kosten'],2);
     $output[9]   = date('Ymd');
     $output[10]  = "";
     $output[11]  = "";
     $output[12]  = "X";
     $output[13]  = "";
     $output[14]  = "IBN";
     $output[15]  = $tmp[4];
     $output[16]  = substr(trim(trim($line1)." ".trim($line2)." ".trim($line3)),0,50);
     $output[17]  = "";
     $output[18]  = "";
     $output[19]  = "";
     $output[20]  = "";
     $output[21]  = "";
     $output[22]  = "";
     $output[23]  = "";
     $output[24]  = "";
     $output[25]  = "";
     $output[26]  = "";
     $output[27]  = "";
     $output[28]  = "SHA";
     $output[29]  = "";
     $output[30]  = "";
     $output[31]  = "";
     $output[32]  = "";
     $output[33]  = "";
     $output[34]  = "";
     $output[35]  = "";
     $output[36]  = "";
     $output[37]  = "";
     $output[38]  = "INT";
     $output[39]  = "";

     $rows[] = $output;


   }

   $regels = "";
   foreach ($rows as $row)
   {
//     $regels .= '"'.implode('";"', $row).'"'."\r\n";
     $regels .= implode(';', $row)."\r\n";
   }



   $filename='OptieOrders_V4_'.$exportStamp.'.csv';
   header('Content-type: text/plain');
   header("Content-Length: ".strlen($regels));
   header("Content-Disposition: attachment; filename=\"".$filename."\"");
   header("Pragma: public");
   header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
   echo $regels;

 }

 else
{

$header=array('Type','Timestamp','TB_AI_REC_TYPE','TB_AUTO_COMM_FLAG','TB_AUTO_CHARGE_FLAG','TB_BOOK','TB_CANC_REASON_CODE','TB_CAPACITY_IND','TB_CMPY_ID','TB_CMPY_XCOD_CODE','TB_DAYS_ACCR','TB_DISPLAY_PRICE','TB_ETC_REQUIRED_FLAG','TB_EVNT_TYPE','TB_INST_CODE','TB_INST_XCOD_CODE','TB_MARKET_CODE','TB_MARKET_XCOD_CODE','TB_NET_CONSID_AMT','TB_NOM_QTY','TB_ORIG_TICKET','TB_PRICE_BASIS','TB_PRIN_AMNT','TB_PRIN_AMNT_REC_TYPE','TB_PURCH_SALE_IND','TB_RECORD_TYPE','TB_RISK_IND','TB_RSON_CODE','TB_SETT_CURR_CODE','TB_TICKET','TB_TRAD_CURR_CODE','TB_TRAD_SETT_RECIP_IND','TB_TRAD_SETT_XRAT','TB_TRAD_SOURCE','TB_TRAN_DATE','TB_TRAN_TIME ','TB_TRDR_ID','TB_TRAN_TO_BOOK','TB_TRAN_TO_RSON_CODE','TB_VALUE_DATE','TB_CPTY_CODE','TB_CPTY_XCOD_CODE','ER1_EXT_TRAD_REF','ER1_REF_TYPE','ER2_EXT_TRAD_REF','ER2_REF_TYPE','ER3_EXT_TRAD_REF','ER3_REF_TYPE','ER4_EXT_TRAD_REF','ER4_REF_TYPE','ER5_EXT_TRAD_REF','ER5_REF_TYPE','ER6_EXT_TRAD_REF','ER6_REF_TYPE','TC1_AMNT','TC1_CHARGE_SETT_RECIP_IND','TC1_CHARGE_SETT_XRAT','TC1_CHARGE_TYPE','TC1_CURRENCY','TC2_AMNT','TC2_CHARGE_SETT_RECIP_IND','TC2_CHARGE_SETT_XRAT','TC2_CHARGE_TYPE','TC2_CURRENCY','TC3_AMNT','TC3_CHARGE_SETT_RECIP_IND','TC3_CHARGE_SETT_XRAT','TC3_CHARGE_TYPE','TC3_CURRENCY','TC4_AMNT','TC4_CHARGE_SETT_RECIP_IND','TC4_CHARGE_SETT_XRAT','TC4_CHARGE_TYPE','TC4_CURRENCY','TC5_AMNT','TC5_CHARGE_SETT_RECIP_IND','TC5_CHARGE_SETT_XRAT','TC5_CHARGE_TYPE','TC5_CURRENCY','TC6_AMNT','TC6_CHARGE_SETT_RECIP_IND','TC6_CHARGE_SETT_XRAT','TC6_CHARGE_TYPE','TC6_CURRENCY','TC7_AMNT','TC7_CHARGE_SETT_RECIP_IND','TC7_CHARGE_SETT_XRAT','TC7_CHARGE_TYPE','TC7_CURRENCY','TC8_AMNT','TC8_CHARGE_SETT_RECIP_IND','TC8_CHARGE_SETT_XRAT','TC8_CHARGE_TYPE','TC8_CURRENCY','TC9_AMNT','TC9_CHARGE_SETT_RECIP_IND','TC9_CHARGE_SETT_XRAT','TC9_CHARGE_TYPE','TC9_CURRENCY','TC10_AMNT','TC10_CHARGE_SETT_RECIP_IND','TC10_CHARGE_SETT_XRAT','TC10_CHARGE_TYPE','TC10_CURRENCY','TC11_AMNT','TC11_CHARGE_SETT_RECIP_IND','TC11_CHARGE_SETT_XRAT','TC11_CHARGE_TYPE','TC11_CURRENCY','TC12_AMNT','TC12_CHARGE_SETT_RECIP_IND','TC12_CHARGE_SETT_XRAT','TC12_CHARGE_TYPE','TC12_CURRENCY','TC13_AMNT','TC13_CHARGE_SETT_RECIP_IND','TC13_CHARGE_SETT_XRAT','TC13_CHARGE_TYPE','TC13_CURRENCY','TC14_AMNT','TC14_CHARGE_SETT_RECIP_IND','TC14_CHARGE_SETT_XRAT','TC14_CHARGE_TYPE','TC14_CURRENCY','TC15_AMNT','TC15_CHARGE_SETT_RECIP_IND','TC15_CHARGE_SETT_XRAT','TC15_CHARGE_TYPE','TC15_CURRENCY','TC16_AMNT','TC16_CHARGE_SETT_RECIP_IND','TC16_CHARGE_SETT_XRAT','TC16_CHARGE_TYPE','TC16_CURRENCY','TC17_AMNT','TC17_CHARGE_SETT_RECIP_IND','TC17_CHARGE_SETT_XRAT','TC17_CHARGE_TYPE','TC17_CURRENCY','TC18_AMNT','TC18_CHARGE_SETT_RECIP_IND','TC18_CHARGE_SETT_XRAT','TC18_CHARGE_TYPE','TC18_CURRENCY','TC19_AMNT','TC19_CHARGE_SETT_RECIP_IND','TC19_CHARGE_SETT_XRAT','TC19_CHARGE_TYPE','TC19_CURRENCY','TC20_AMNT','TC20_CHARGE_SETT_RECIP_IND','TC20_CHARGE_SETT_XRAT','TC20_CHARGE_TYPE','TC20_CURRENCY','TC21_AMNT','TC21_CHARGE_SETT_RECIP_IND','TC21_CHARGE_SETT_XRAT','TC21_CHARGE_TYPE','TC21_CURRENCY','TC22_AMNT','TC22_CHARGE_SETT_RECIP_IND','TC22_CHARGE_SETT_XRAT','TC22_CHARGE_TYPE','TC22_CURRENCY','TC23_AMNT','TC23_CHARGE_SETT_RECIP_IND','TC23_CHARGE_SETT_XRAT','TC23_CHARGE_TYPE','TC23_CURRENCY','TC24_AMNT','TC24_CHARGE_SETT_RECIP_IND','TC24_CHARGE_SETT_XRAT','TC24_CHARGE_TYPE','TC24_CURRENCY','TC25_AMNT','TC25_CHARGE_SETT_RECIP_IND','TC25_CHARGE_SETT_XRAT','TC25_CHARGE_TYPE','TC25_CURRENCY','TCON1_TRAD_COND','TCON2_TRAD_COND','TCON3_TRAD_COND','TCON4_TRAD_COND');
$template=array('','','T','N','N','FUNTRAD','','A','FUNDIX','USER','','','N','TRD','','ISIN','OTC','USER','','','','CP','','T','','INP','N','NOR','','','','N','1','FDX','','','','','','','','FDX','','','NL','CTY','','','','','','','','','','N','1','FKS','','','N','1','FVP','EUR','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','');
$xlsdata=array();
$xlsdata[]=$header;
$now=date("Ymd H:i:s");
$db=new DB();
$query="SELECT
  (SELECT OrderUitvoering.uitvoeringsPrijs FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid limit 1) as Fondskoers,
  Fondsen.ISINCode,
  Fondsen.beurs,
  Fondsen.fondsEenheid,
  OrderRegels.aantal,
  Orders.transactieSoort,
  Fondsen.Valuta,
  Fondsen.bbLandcode,
  Orders.orderid,
  Orders.uitvoeringsDatum as uitvoeringsDatumLeeg,
  (SELECT OrderUitvoering.uitvoeringsDatum FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid limit 1) as uitvoeringsDatum,
  OrderRegels.add_user,
  Portefeuilles.Client,
  Portefeuilles.InternDepot,
  OrderRegels.brokerkosten,
  OrderRegels.kosten,
  OrderRegels.opgelopenRente,
  OrderRegels.valutakoers,
  OrderRegels.valuta as orderregelValuta,
  BbLandcodes.settlementDays
FROM
  Orders
INNER JOIN OrderRegels ON Orders.orderid = OrderRegels.orderid
INNER JOIN Fondsen ON Orders.fonds = Fondsen.Fonds
INNER JOIN Portefeuilles ON OrderRegels.portefeuille = Portefeuilles.Portefeuille
LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
WHERE Orders.laatsteStatus = 2";
    $db->SQL($query);
    $db->Query();
    $transactievertaling=array('A'=>'S','V'=>'P');
    while($data=$db->nextRecord())
    {
      if($data['aantal']==round($data['aantal']))
        $data['aantal']=round($data['aantal']);
      
      $uitvoeringsJul=db2jul($data['uitvoeringsDatum']);
      $dagvanweek=date('N',$uitvoeringsJul);
      
      $baseDays=2;
      if($data['settlementDays'] > 0)
        $baseDays=$data['settlementDays'];
      
      if($dagvanweek<=(5-$baseDays) && $dagvanweek<6)
        $extraDagen=0;
      elseif($dagvanweek<=(10-$baseDays) && $dagvanweek<6)
        $extraDagen=2;
      else
        $extraDagen=4;
       
      $settleDatum=date('Ymd',$uitvoeringsJul+(($baseDays+$extraDagen)*86400)+3605);
      
      $brutoBedrag=$data['aantal']*$data['Fondskoers']*$data['fondsEenheid'];
      if(substr($data['transactieSoort']=='A',0,1))
       $nettoBedrag=(($brutoBedrag+$data['opgelopenRente'])*$data['valutakoers'])+$data['kosten']+$data['brokerkosten'];
      else
       $nettoBedrag=(($brutoBedrag+$data['opgelopenRente'])*$data['valutakoers'])-$data['kosten']-$data['brokerkosten'];
      
      $tmp=$template;
      $tmp[1]=$now;
      $tmp[11]=$data['Fondskoers'];
      $tmp[14]=$data['ISINCode'];
      $tmp[18]=number_format($nettoBedrag,2,'.','');
      $tmp[19]=$data['aantal'];
      $tmp[22]=number_format($brutoBedrag,2,'.','');
      $tmp[24]=$transactievertaling[$data['transactieSoort']];
      $tmp[28]=$data['orderregelValuta'];
      $tmp[29]=$data['orderid'];
      $tmp[30]=$data['Valuta'];
      
      if($data['Valuta'] <> $data['orderregelValuta'])
        $tmp[32]=number_format(1/getValutaKoers($data['Valuta'],$data['uitvoeringsDatum']),8,'.','');;
      
      if($data['InternDepot']==1)
      {
        $tmp[5]='FUNMARK';
        $tmp[28]=$data['Valuta'];
        $tmp[32]=1;
      }
   
      $tmp[34]=date('Ymd',$uitvoeringsJul);
      $tmp[35]=date('H:i',$uitvoeringsJul);
      $tmp[36]=$data['add_user'];
      $tmp[39]=$settleDatum;
      $tmp[40]=$data['Client'];
      if($data['bbLandcode'] <> '')
        $tmp[44]=$data['bbLandcode'];
      $tmp[54]=$data['brokerkosten'];
      $tmp[58]='EUR';
      if($data['brokerkosten']==0)
      {
        $tmp[54]='';
        $tmp[55]='';
        $tmp[56]='';
        $tmp[57]='';
        $tmp[58]='';
      }
      $tmp[59]=$data['kosten'];
      if($data['kosten']==0)
      {
        $tmp[59]='';
        $tmp[60]='';
        $tmp[61]='';
        $tmp[62]='';
        $tmp[63]='';
      }
      
      $xlsdata[]=$tmp;
    }

    if($format=='xls')
    {
      $filename='Orders'.$exportStamp.'.xls';
  	  include_once('../classes/excel/Writer.php');
	    $workbook = new Spreadsheet_Excel_Writer();
      $worksheet =& $workbook->addWorksheet();
	    for($regel = 0; $regel < count($xlsdata); $regel++ )
	    {
		    for($col = 0; $col < count($xlsdata[$regel]); $col++)
		    {
		      $worksheet->write($regel, $col, $xlsdata[$regel][$col]);
		    }
	    }
      
      $workbook->send($filename);
	    $workbook->close();
    }
    else
    {
			//$csvdata = generateCSV($xlsdata);
      $csvdata='';
      for ($a=0;$a<count($xlsdata);$a++)
      {
        for($b=0;$b<count($xlsdata[$a]);$b++)
        {
          $csvdata .= str_replace("\n","",$xlsdata[$a][$b]).',';
        }
        $csvdata = substr($csvdata,0,-1);
        $csvdata .= "\r\n";
      }
            

      $filename='Orders'.$exportStamp.'.csv';
      $appType = "text/comma-separated-values";
      header('Content-type: ' . $appType);
    	header("Content-Length: ".strlen($csvdata));
    	header("Content-Disposition: inline; filename=\"".$filename."\"");
	    header("Pragma: public");
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      echo $csvdata;
  
		}
}  
?>
