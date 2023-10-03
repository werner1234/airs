<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/13 15:34:43 $
 		File Versie					: $Revision: 1.34 $

 		$Log: AE_cls_FIXtransport.php,v $
 		Revision 1.34  2020/05/13 15:34:43  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2020/01/22 16:01:04  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2019/11/11 10:03:01  cvs
 		call 8243
 		
 		Revision 1.31  2019/08/17 18:22:04  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2018/11/24 19:06:05  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2018/11/10 18:22:21  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2018/09/05 15:47:24  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2018/02/17 19:13:19  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2017/11/29 16:12:51  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2017/03/08 16:49:38  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2017/03/04 19:17:07  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2016/11/09 17:09:29  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2016/09/28 06:24:15  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2016/07/03 08:42:31  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2016/06/01 06:45:37  rm
 		Nieuwe notifier
 		
 		Revision 1.19  2016/04/24 15:31:40  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2016/04/20 14:58:15  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2016/03/13 16:21:35  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2016/02/28 17:18:51  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2016/02/27 15:59:10  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2016/02/21 17:16:20  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2016/01/27 17:04:42  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2015/11/22 14:23:35  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2015/11/18 17:01:41  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2015/11/15 12:13:34  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2015/11/01 18:02:33  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2015/10/18 13:37:37  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2015/10/11 17:28:16  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2015/09/30 07:52:43  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2015/08/26 15:45:00  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2015/07/12 10:46:15  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2015/07/05 12:33:21  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2015/06/26 16:04:47  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/06/24 15:55:30  rvv
 		*** empty log message ***
 		


*/
include_once('../config/FIX_vars.php');

class AE_FIXtransport
{
  var $user;
  var $db;
  var $FIX;
  var $encryptKey;
  
  var $airsToFix;
  var $fixToAirs;
  
  function AE_FIXtransport()
  {
    global $_DB_resources,$__fixVars;
    global $USR, $__FIX; 
    // testomgeving
    if($__FIX["develop"]==true)
    {
      $_DB_resources[DBFix]['server'] = "test.simboek.nl";
      $_DB_resources[DBFix]['user'] = 'FIX_cvs';
      $_DB_resources[DBFix]['passwd'] = "2bRLuhy1Dg";
      $_DB_resources[DBFix]['db'] = "FIXqueue";
    }
    else
    {
      // productie omgeving
    	$_DB_resources[DBFix]['server'] = "fix.airs.nl";
      $_DB_resources[DBFix]['user']   = 'FIX_prod_'.$__FIX["bedrijfscode"];
      $_DB_resources[DBFix]['passwd'] = "2bRLuhy1Dg";
      $_DB_resources[DBFix]['db']     = "FIXqueue";
    }

    $this->db = new DB();
	$this->db->debug = false;
	$this->db->connect(DBFix);
    $this->dbLocal = new DB();
    $this->FIX = $__FIX;
	$this->user = $USR;
	//  $this->dbId=99;
    $this->encryptKey = "bla";

    $this->notifier = new AIRS_Notify();
    $this->orderLogs = new orderLogs();

    $this->airsToFix =  $__fixVars["queueMappingFields"];


    foreach ($this->airsToFix as $k => $v)
    {
      $this->fixToAirs[$v] = $k;
    }
  }


  function testConnection()
  {
    $query = "SELECT id FROM airsClientQueue";
    $testRec = $this->db->lookupRecordByQuery($query);
	  return ($testRec["id"] > 0);

  }


  function readMessagesFromQueue()
  {
    $query = "
      SELECT
        *
      FROM
        airsClientQueue
      WHERE
        verwerkt = 0  AND
        direction = 'toAIRS' AND
        bedrijfscode = '".$this->FIX["bedrijfscode"]."'
      ORDER BY
        id
        ";

    $this->db->executeQuery($query);
    while ($qRec = $this->db->nextRecord())
    {
      if (md5($qRec["message"]) <> $qRec["messageHash"])
      {
        $this->addError("AirsClient","CRC error in Queue record: ".$qRec["id"]);
        $out[] = array("error" =>"CRC error in Queue record: ".$qRec["id"]);
      }
      //TODO decrypt message
      $qRec["message"] = unserialize($qRec["message"]);
      $out[] = $qRec;
    }

    return $out;
  }


  function createMessage($data)
  {
    $out = array(
        "version" => "AIRSside 1.00",
        "bedrijfscode" => $this->FIX["bedrijfscode"],
            );

    foreach($data as $key=>$value)
    {

      if ($this->airsToFix[$key] <> "")
      {
        // validate if needed
        //
        $out[$this->airsToFix[$key]] = $value;
      }
      else
      {
        $out["AIRS_".$key] = $value;
      }


    }

    $serOut = serialize($out);

    $key = $this->encryptKey;
    // encryption goes her

    return serialize($out);


  }


  function addToQueue($data)
  {
    $message = $this->createMessage($data);
    $messageHash = md5($message);

    if($data['ordernr'] <> '')
     $ordernr=$data['ordernr'];
    else
      $ordernr=-1;
    //TODO encryptie voor message inbouwen
    $query = "INSERT INTO airsClientQueue SET
      `add_user` = '".$this->user."'
      ,`add_date` = NOW()
      ,`change_user` = '".$this->user."'
      ,`change_date` = NOW()
      ,`bedrijfscode` = '".$this->FIX["bedrijfscode"]."'
      ,`ordernr` = '".$ordernr."'
      ,`stamp` = NOW()
      ,`direction` = 'toFix'
      ,`messageType` = 'order'
      ,`message` = '".mysql_real_escape_string($message)."'
      ,`messageHash` = '".$messageHash."'
      ";
    $insert=$this->db->executeQuery($query);
    $lastId=$this->db->last_id();
    logIt("aanmaken FIX Qbericht: i:$insert id:$lastId ".$query);
    
    $query = "SELECT id FROM `airsClientQueue` WHERE
      `add_user` = '".$this->user."'   
      AND `change_user` = '".$this->user."'   
      AND `bedrijfscode` = '".$this->FIX["bedrijfscode"]."'    
      AND `ordernr` = '".$ordernr."'    
      AND `direction` = 'toFix'    
      AND `messageType` = 'order'    
      AND `messageHash` = '".$messageHash."'
      ";
    $records=$this->db->QRecords($query);
    logIt("controle FIX Qbericht: r:$records  ".$query);
    
    return ($records>0?true:false);
  }

  function addError($errorCode,$txt)
  {
    $query = "INSERT INTO queueErrorLog SET
       `add_user` = '".$this->user."'
      ,`add_date` = NOW()
      ,`change_user` = '".$this->user."'
      ,`change_date` = NOW()
      ,`bedrijfscode` = '".$this->FIX["bedrijfscode"]."'
      ,`ip` = '".$_SERVER["REMOTE_ADDR"]."'
      ,`module` = 'AirsQueue'
      ,`errorCode` = '$errorCode'
      ,`text`  = '$txt'
";
    $this->db->executeQuery($query);
  }


  function addToAirs($data)
  {
    $query = "INSERT INTO fixOrders SET
      `add_user`      = '".mysql_real_escape_string($this->user)."'
      ,`add_date`     = NOW()
      ,`change_user`  = '".mysql_real_escape_string($this->user)."'
      ,`change_date`  = NOW()
      ,`portefeuille` = '".mysql_real_escape_string($data['portefeuille'])."'
      ,`client`       =  '".mysql_real_escape_string($data['client'])."'
      ,`rekeningnr`   = '".mysql_real_escape_string($data['rekening'])."'
      ,`vermogensBeheerder` = '".mysql_real_escape_string($data['vermogenbeheerder'])."'
      ,`orderid`      = ''
      ,`aantal`       = '".mysql_real_escape_string($data['aantal'])."'
      ,`fondsCode`    = '".mysql_real_escape_string($data['ISIN'])."'
      ,`fonds`        = '".mysql_real_escape_string($data['AIRSfondsCode'])."'
      ,`fondsOmschrijving` = '".mysql_real_escape_string($data['AIRSfondsOms'])."'
      ,`transactieType` = '".mysql_real_escape_string($data['typeTransactie'])."'
      ,`transactieSoort` = '".mysql_real_escape_string($data['transactieSoort'])."'
      ,`tijdsLimiet`  = '".mysql_real_escape_string($data['limietDatum'])."'
      ,`tijdsSoort`   = ''
      ,`koersLimiet`  = '".mysql_real_escape_string($data['limietKoers'])."'
      ,`status` = '".date("Ymd H:i:s")." Verzonden naar queue.'
      ,`laatsteStatus` = ''
      ,`Depotbank` = '".mysql_real_escape_string($data['depotbank'])."'
      ,`uitvoeringsPrijs` = ''
      ,`uitvoeringsDatum` = ''
      ,`aantalUitgevoerd` = ''
      ,`meldingen` = ''
      ,`verwerkt` = ''
      ,`verwerktResult` = ''
      ,`bankfondsCode` = '".mysql_real_escape_string($data['bankCode'])."'
      ,`DepotbankOrderId` = ''
      ,`AIRSorderReference` = '".mysql_real_escape_string($data['AIRSorderReference'])."'
      ,`no_legs` = '".mysql_real_escape_string($data['no_legs'])."'
      ,`legs` = '".mysql_real_escape_string(serialize($data['legs']))."'
      ,`FIXRecordArray` = ''
      ,`AIRS_bedrijf` = '".mysql_real_escape_string($data['vermogenbeheerder'])."'
      ";
    $this->dbLocal->executeQuery($query);
    $lastId=$this->dbLocal->last_id();
    return $lastId;
  }


  function getFondscode($depotbank,$fonds)
  {

    global $__fixVars;


    if(isset($__fixVars['BankDepotCodes'][$depotbank]))
      $bankcodeVeld=$__fixVars['BankDepotCodes'][$depotbank];
    else
      return '';//unknown


    $query="SELECT $bankcodeVeld as bankCode FROM Fondsen WHERE Fonds='".mysql_real_escape_string($fonds)."'";
    $this->dbLocal->SQL($query);
    $fondsRecord=$this->dbLocal->lookupRecord();
    $bankCode=$fondsRecord['bankCode'];

    if($bankCode == '')
      return '';//unknown
    else
      return $bankCode;
  }
  
  function getBeurs($depotbank,$fonds)
  {
    $query="SELECT beurs, binckBeurs FROM Fondsen WHERE Fonds='".mysql_real_escape_string($fonds)."'";
    $this->dbLocal->SQL($query);
    $fondsRecord=$this->dbLocal->lookupRecord();
    if($depotbank=='BIN' && $fondsRecord['binckBeurs']<>'')
      $beurs=$fondsRecord['binckBeurs'];
    else
      $beurs=$fondsRecord['beurs'];
    
    if($beurs == '')
      return '';//unknown
    else
      return $beurs;
  }
  
  function getFondsValuta($depotbank,$fonds)
  {
    $query="SELECT Valuta as fondsValuta, binckValuta FROM Fondsen WHERE Fonds='".mysql_real_escape_string($fonds)."'";
    $this->dbLocal->SQL($query);
    $fondsdata=$this->dbLocal->lookupRecord();
    if(($depotbank=='BIN' || $depotbank=='BINB') && $fondsdata['binckValuta']<>'')
      $fondsdata['fondsValuta']=$fondsdata['binckValuta'];

    return $fondsdata['fondsValuta'];
}

  function getOptiecode($depotbank,$fonds,$transactieSoort,$orderObject='')
  {
    global $__fixVars;

    if(isset($__fixVars['BankDepotCodes'][$depotbank]))
      $bankcodeVeld=$__fixVars['BankDepotCodes'][$depotbank];
    else
      return 'unknown';

    if($fonds=='' && is_object($orderObject))
    {
      $fondsRecord=array();
      $symbool=$orderObject->get('optieSymbool');
      $fondsRecord['OptieExpDatum']=$orderObject->get('optieExpDatum');
      $fondsRecord['OptieUitoefenPrijs']=$orderObject->get('optieUitoefenprijs');
      $fondsRecord['OptieType']=$orderObject->get('optieType');
      $bankCode=$orderObject->get('bankCode');
      
      if($fondsRecord['OptieExpDatum']=='' || $fondsRecord['OptieUitoefenPrijs']=='' || $fondsRecord['OptieType']=='')
        return 'geenOptie';
  

    }
    else
    {
    $query="SELECT OptieBovenliggendFonds,OptieType,OptieExpDatum,OptieUitoefenPrijs,".$bankcodeVeld." as bankCode,optieCode,fondssoort FROM Fondsen WHERE Fonds='".mysql_real_escape_string($fonds)."'";
    $this->dbLocal->SQL($query);
    $fondsRecord=$this->dbLocal->lookupRecord();

    if($fondsRecord['OptieBovenliggendFonds']=='')
      return 'geenOptie';

    if($fondsRecord['optieCode'] <> '')
    {
      $query="SELECT optie".$bankcodeVeld." as bankCode FROM fondsOptieSymbolen WHERE `key`='".$fondsRecord['optieCode']."' AND Fonds='".mysql_real_escape_string($fondsRecord['OptieBovenliggendFonds'])."'";
      $this->dbLocal->SQL($query);
      $optieSymbolenRecord=$this->dbLocal->lookupRecord();
      $symbool=$fondsRecord['optieCode'];
    }

    if($optieSymbolenRecord['bankCode']=='')
    { //tweede poging.
      $optieParts=explode(" ",$fonds);
      $symbool=$optieParts[0];
      $query="SELECT optie".$bankcodeVeld." as bankCode FROM fondsOptieSymbolen WHERE `key`='$symbool' AND Fonds='".mysql_real_escape_string($fondsRecord['OptieBovenliggendFonds'])."'";
      $this->dbLocal->SQL($query);
      $optieSymbolenRecord=$this->dbLocal->lookupRecord();
    }
    if($optieSymbolenRecord['bankCode']!='')
      $symbool=$optieSymbolenRecord['bankCode'];
    $bankCode=$optieSymbolenRecord['bankCode'];
    }

    if(substr($transactieSoort,0,1)=='V')
        $fixTransactieSoort='sell';
    else
        $fixTransactieSoort='buy';

    $oc='';
    if(substr($transactieSoort,1,1)=='S')
       $oc='close';
    elseif(substr($transactieSoort,1,1)=='O')
       $oc='open';

    return array('bankCode'=>$bankCode,
                 'leg'=>array("symbol"=>$symbool,
                   'expiry'=>$fondsRecord['OptieExpDatum'],
                   'strike'=>$fondsRecord['OptieUitoefenPrijs'],
                   'leg_type'=>$fondsRecord['OptieType'],
                   'side'=>$fixTransactieSoort,
                   'oc'=>$oc));
  }

  function getDepotbankPortefeuille($rekening,$portefeuille,$toon=0)
  {
    $db=new DB();
    $query="SELECT Rekeningen.Rekening,Rekeningen.Memoriaal,Vermogensbeheerders.OrderuitvoerBewaarder,Rekeningen.RekeningDepotbank,Portefeuilles.Depotbank
FROM Rekeningen 
JOIN Portefeuilles ON Rekeningen.Portefeuille=Portefeuilles.Portefeuille 
JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder=Vermogensbeheerders.Vermogensbeheerder
WHERE Rekeningen.Rekening='".mysql_real_escape_string($rekening)."'";
    $db->SQL($query);
    $rekening=$db->lookupRecord();
    $depotbank=$rekening['Depotbank'];
    if($rekening['OrderuitvoerBewaarder']==1 && $rekening['Memoriaal']==1 )
    {
      if($rekening['RekeningDepotbank']<>'')
        $portefeuileDepotbank=substr($rekening['RekeningDepotbank'],0,-3);
      else
        $portefeuileDepotbank=substr($rekening['Rekening'],0,-3);
    }
    else
    {
      $query="SELECT PortefeuilleDepotbank FROM Portefeuilles WHERE Portefeuilles.Portefeuille='".mysql_real_escape_string($portefeuille)."'";
      $db->SQL($query);
      $pdata=$db->lookupRecord();
      if($pdata['PortefeuilleDepotbank']<>'')
        $portefeuileDepotbank=$pdata['PortefeuilleDepotbank'];
      else
        $portefeuileDepotbank=$portefeuille;
    }
    if($depotbank=='KNOX')
    {
      if(substr($portefeuileDepotbank,-1)=='S')
        $portefeuileDepotbank=substr($portefeuileDepotbank,0,-1);
    }
    
    if($toon==1 && $portefeuille<>$portefeuileDepotbank)
    {
      return "$portefeuille / $portefeuileDepotbank";
    }
    else
      return $portefeuileDepotbank;
  }

  function decodeMessage($data)
  {
    $message=$data['message'];
    if($message['AIRSorderReference'] < 1)
    {
      $query="SELECT id,AIRSorderReference FROM fixOrders WHERE orderid = '".$data['ordernr']."'";
      if($this->dbLocal->QRecords($query) < 1)
      {
        $this->addError(2,"FixOrder in Airs niet gevonden. fix ordernr:(".$data['ordernr'].")");
        return -1;
      }
      $fixOrderRecord=$this->dbLocal->lookupRecord();
      $airsOrderReference=$fixOrderRecord['AIRSorderReference'];
    }
    else
      $airsOrderReference=$message['AIRSorderReference'];

    $query="SELECT id FROM OrdersV2 WHERE id = '".$airsOrderReference."'";
    if($this->dbLocal->QRecords($query) < 1)
    {
      echo "Order niet gevonden.";
      $this->addError(3,"Airs order niet gevonden. Airs orderregel:(".$airsOrderReference.")");
      return -1;
    }

    $query="SELECT id FROM fixOrders WHERE AIRSorderReference = '".$airsOrderReference."'";
    if($this->dbLocal->QRecords($query) < 1)
    {
      $this->addError(4,"Airs FixOrder niet gevonden. Airs orderregel:(".$airsOrderReference.")");
      return -1;
    }
    else
    {
      $fixOrderRecord=$this->dbLocal->lookupRecord();
      $fixOrderRecordId=$fixOrderRecord['id'];
    }

    $transactionStamp=$message['transact_time'];
    if ($transactionStamp == "")
      $transTime = db2jul($data['stamp']);//time();
    else
    {
      $message['timeOffset']=trim(substr($transactionStamp,18,4).' '.$message['text']);
      $transTime = mktime(substr($transactionStamp,9,2),substr($transactionStamp,12,2),substr($transactionStamp,15,2),substr($transactionStamp,4,2),substr($transactionStamp,6,2),substr($transactionStamp,0,4));
    }
    $updateData=array('AIRSorderReference'=>$airsOrderReference,
                      'verwerktStamp'=>$data['stamp'],
                      'transTime'=>$transTime,
                      'aantalUitgevoerd'=>$message['cum_qty'],
                      'laatsteUitvoering'=>$message['last_shares'],
                      'uitvoeringsPrijs'=>$message['last_price'],
                      'orderid'=>$message['client_order_id'],
                      'laatsteStatus'=>trim($message['status'].' '.$message['ord_status']),
                      'statusTxt'=>trim(trim($message['text']).' '.trim($message['free_text'])),
                      'exec_type'=>$message['exec_type'],
                      'exec_id'=>$message['exec_id'],
                      'exec_ref_id'=>$message['exec_ref_id'],
                      'timeOffset'=>$message['timeOffset'],
                      'type'=>$message['type'],
                      'fixOrderRecordId'=>$fixOrderRecordId);

     return $updateData;
  }

  function updateOrder($data)
  {
    global $__ORDERvar,$__fixVars;

    $query="SELECT id,orderid,status,laatsteStatus,uitvoeringsPrijs,uitvoeringsDatum,aantalUitgevoerd,verwerktStamp,change_date
    FROM fixOrders WHERE AIRSorderReference='".$data['AIRSorderReference']."'";
    $this->dbLocal->SQL($query);
    $oldFixOrder=$this->dbLocal->lookupRecord();

    if($data['orderid']<>'')
      $fixOrderId=$data['orderid'];
    else
      $fixOrderId=$oldFixOrder['orderid'];

    $query="SELECT OrdersV2.id,OrdersV2.orderStatus FROM OrdersV2  WHERE OrdersV2.id='".$data['AIRSorderReference']."'";
      $this->dbLocal->SQL($query);
    $orderRecord=$this->dbLocal->lookupRecord();

    if(strtolower($data['type'])=='rej')
    {
      if($oldFixOrder['laatsteStatus']=='CP')
        $this->orderDoorgevenReject($orderRecord['id'],$data['fixOrderRecordId'],$data['transTime'],$data['timeOffset']);
      elseif($oldFixOrder['laatsteStatus']=='6')
        $this->orderCancelReject($orderRecord['id'],$data['fixOrderRecordId'],$data['transTime'],$data['timeOffset']);
      else
        $this->orderLogs->addToLog($orderRecord['id'],$data['fixOrderRecordId'],'Geweigerd. Laatste status ('.$oldFixOrder['laatsteStatus'].')',date('Y-m-d H:i:s',$data['transTime']),'SYS',5,$data['timeOffset']);

    //  $data['laatsteStatus']="8";
    }
    //listarray($data);listarray($oldFixOrder);
    foreach($oldFixOrder as $key=>$value)
    {

      if($key=='orderid' && $oldFixOrder[$key] < 1)
        $updateVelden[$key] = $data[$key];
      elseif($key=='statusTxt')
        $updateVelden[$key] = $value."\n".date('Ymd_H:i:s',$data['transTime']).' '.$data[$key];
      elseif($key=='verwerktStamp')
        $updateVelden[$key] = $data[$key];
      elseif(isset($data[$key]) && $data[$key] <> '' && ($data[$key]<>'' || $data[$key]< 1))
        $updateVelden[$key] = $data[$key];

    }
    $query="UPDATE fixOrders SET change_date=now(),change_user='".$this->user."'";
    foreach($updateVelden as $key=>$value)
      $query.=", $key = '".mysql_real_escape_string($value)."'";
    $query.=" WHERE AIRSorderReference='".$data['AIRSorderReference']."'";
    $this->dbLocal->SQL($query);
    $this->dbLocal->Query();

    $uitvoeringsTxt='';
    if($data['laatsteUitvoering'] > 0)
    {
      $orderUitvoeringVertaling=array('0'=>'New','1'=>'Partially filled','2'=>'Filled','3'=>'Done for Day',
'4'=>'Cancelled','5'=>'Replaced','6'=>'Pending Cancel','8'=>'Rejected','A'=>'Pending New','B'=>'Calculated',
'C'=>'Expired','D'=>'Restated','E'=>'Pending Replace','F'=>'Trade','G'=>'Trade Correct','H'=>'Trade Cancel',
'O'=>'Eliminated by corporate event');

      $uitvoeringsTxt=" aantal:".$data['laatsteUitvoering']." prijs:".$data['uitvoeringsPrijs'];
      if($data['exec_ref_id'] <> '' && ($data['exec_type']=='G' || $data['exec_type']=='H'))
      {
        $this->orderLogs->addToLog($orderRecord['id'],$data['fixOrderRecordId'],
        "Uitvoering: '".$orderUitvoeringVertaling[$data['exec_type']]."' $uitvoeringsTxt " ,
        date('Y-m-d H:i:s',$data['transTime']),'SYS',5,$data['timeOffset']);

        $query="SELECT id FROM OrderUitvoeringV2 WHERE exec_id = '".$data['exec_ref_id']."' AND OrderUitvoeringV2.orderId='".$orderRecord['id']."'";
        if($this->dbLocal->QRecords($query)!=0)
        {
          $uitvoerId=$this->dbLocal->nextRecord();
          $query="UPDATE OrderUitvoeringV2 SET uitvoeringsAantal='".$data['laatsteUitvoering']."',uitvoeringsDatum='".date('Y-m-d H:i:s',$data['transTime'])."',uitvoeringsPrijs='".$data['uitvoeringsPrijs']."' WHERE id='".$uitvoerId['id']."'";
          $this->dbLocal->SQL($query);
          $this->dbLocal->Query();
          $this->orderLogs->addToLog($orderRecord['id'],$data['fixOrderRecordId'],"Uitvoering met exec_id = '".$data['exec_ref_id']."' '".$orderUitvoeringVertaling[$data['exec_type']]."' bijgewerkt.",date('Y-m-d H:i:s',$data['transTime']),'SYS',5,$data['timeOffset']);

        }
        else
        {
          $this->orderLogs->addToLog($orderRecord['id'],$data['fixOrderRecordId'],"Geen exec_id = '".$data['exec_ref_id']."' gevonden voor '".$orderUitvoeringVertaling[$data['exec_type']]."'",date('Y-m-d H:i:s',$data['transTime']),'SYS',5,$data['timeOffset']);
        }
      }
      else
      {
        if(strtolower($data['type'])=='trd')
        {
          $query = "INSERT INTO OrderUitvoeringV2 SET exec_id='" . $data['exec_id'] . "', uitvoeringsAantal='" . $data['laatsteUitvoering'] . "',uitvoeringsDatum='" . date('Y-m-d H:i:s', $data['transTime']) . "',uitvoeringsPrijs='" . $data['uitvoeringsPrijs'] . "',orderid='" . $orderRecord['id'] . "',add_date=now(),change_date=now(),add_user='SYS',change_user='SYS'";
          $this->dbLocal->SQL($query);
          $this->dbLocal->Query();
        }
      }
    }


  $orderStatusConversie=array("0" => "1", //"New", = doorgegeven
  "1" => "2", //"Partially", => uitgevoerd
  "2" => "2", //"Traded", => uitgevoerd
  "3" => "", //"Done for Day",
  "4" => "6", //"Cancelled", => geannuleerd
  "5" => "", //"Replaced",
  "6" => "", //"Pending Cancel",
  "8" => "7", //"Rejected", => "Rejected"
  "A" => "1", //"Pending New", =>doorgegeven
  "B" => "", //"Calculated",
  "C" => "5", //"Expired", => vervallen
  "E" => "", //"Pending Replace",
  "S" => "7", //"Cancelled by Market Operation",  => geweigerd
  "O" => "7",  //"Eliminated by corporate event",  => geweigerd
  "CP" => "1", //"New Charm queue" => doorgegeven
  "F0" => "5", // expiratie limiet => vervallen
  "F9" => "5"); // overige => vervallen



    if($data['laatsteStatus'] <> '')
    {
      $query="UPDATE OrdersV2 SET change_date=now(),orderSubStatus='".$data['laatsteStatus']."' WHERE id='".$orderRecord['id']."'";
      $this->dbLocal->SQL($query);
      $this->dbLocal->Query();
      $this->orderLogs->addToLog($orderRecord['id'],$data['fixOrderRecordId'],'substatus '.$__fixVars['ord_status'][$data['laatsteStatus']].' ('.$data['laatsteStatus'].')',date('Y-m-d H:i:s',$data['transTime']),'SYS',1,$data['timeOffset']);
    }
    if(isset($orderStatusConversie[$data['laatsteStatus']]) && trim($orderStatusConversie[$data['laatsteStatus']]) <> '')
    {
      $subStatus=$data['laatsteStatus'];
      $query="SELECT sum(uitvoeringsAantal) as uitgevoerd FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderId='".$orderRecord['id']."'";
      $this->dbLocal->SQL($query);
      $aantal=$this->dbLocal->lookupRecord();
      if($data['laatsteStatus']=='4' && $aantal['uitgevoerd'] > 0)
      {
         $this->orderLogs->addToLog($orderRecord['id'],$data['fixOrderRecordId'],'Vanwege uitvoeringen status naar uitgevoerd.',date('Y-m-d H:i:s',$data['transTime']),'SYS',5,$data['timeOffset']);
         $data['laatsteStatus']='2';
      }

      $query="UPDATE OrdersV2 SET change_date=now(),orderStatus='".$orderStatusConversie[$data['laatsteStatus']]."' WHERE id='".$orderRecord['id']."'";
      $this->dbLocal->SQL($query);
      $this->dbLocal->Query();
      $query="UPDATE OrderRegelsV2 SET change_date=now(),orderregelStatus='".$orderStatusConversie[$data['laatsteStatus']]."' WHERE orderid='".$orderRecord['id']."'";
      $this->dbLocal->SQL($query);
      $this->dbLocal->Query();

      if($orderStatusConversie[$data['laatsteStatus']]=='6' || $orderStatusConversie[$data['laatsteStatus']]=='7')
        $messageType='error';
      else
        $messageType='info';

      $nieuweStatusTxt="fix status ".$__fixVars['ord_status'][$data['laatsteStatus']]." ".trim($uitvoeringsTxt." ".$data['statusTxt']);
      $this->notifier->addRow('fixOrder', $orderRecord['id'], 'Order '.$orderRecord['id'].' heeft '.$nieuweStatusTxt, array('ttl' => '1u','type'=>$messageType));
    
      $this->orderLogs->addToLog($orderRecord['id'], 
                         $data['fixOrderRecordId'],
                         $nieuweStatusTxt,
                         date('Y-m-d H:i:s',$data['transTime']),
                         'SYS',5,$data['timeOffset']);
    }
    elseif($data['laatsteStatus'] <> '')
    {
      $logStatus=$__fixVars['ord_status'][$data['laatsteStatus']];
      
      if($logStatus=='')
      {
        if(substr($data['laatsteStatus'],0,1)=='F')
          $logStatus='';
        else
          $logStatus = "onbekende status " . $data['laatsteStatus'];
      }
      else
        $logStatus="fix status ".$logStatus;
        
      $this->orderLogs->addToLog($orderRecord['id'], 
                         $data['fixOrderRecordId'],
                         $logStatus." ".trim($uitvoeringsTxt." ".$data['statusTxt']),
                         date('Y-m-d H:i:s',$data['transTime']),
                         'SYS',5,$data['timeOffset']);    
    }
    $this->verzendStatusMail($orderRecord['id'],$orderRecord['orderStatus'],$orderStatusConversie[$data['laatsteStatus']]);


    
    return 1;
        
    
  }
  
  function updateQueue($id,$verwerkt,$message='')
  {
    if($message <> '')
      $message='-'.$message;
      
    if($verwerkt < 0)
      $verwerktTxt='fail'.$message;
    else
      $verwerktTxt='ok';  
      
    $query = "UPDATE airsClientQueue SET verwerkt=1 ,verwerktStamp=now(),verwerktResult='$verwerktTxt' WHERE
          direction = 'toAIRS' AND bedrijfscode = '".$this->FIX["bedrijfscode"]."' AND id='$id'";
    $this->db->executeQuery($query);
  }
  
  function orderCancelReject($orderRecordId,$fixorderId,$tijdstip,$timeOffset)
  {
     $this->orderLogs->addToLog($orderRecordId,$fixorderId,'Annulering geweigerd',date('Y-m-d H:i:s',$tijdstip),'SYS',5,$timeOffset);   
     $query="UPDATE OrdersV2 SET fixAnnuleerdatum='0000-00-00' WHERE id='".$orderRecordId."'";
     $this->dbLocal->executeQuery($query);
  }

  function orderDoorgevenReject($orderRecordId,$fixorderId,$tijdstip,$timeOffset)
  {
    $this->orderLogs->addToLog($orderRecordId,$fixorderId,'Doorgeven geweigerd',date('Y-m-d H:i:s',$tijdstip),'SYS',5,$timeOffset);
    $query="UPDATE OrdersV2 SET orderStatus=0, fixVerzenddatum='0000-00-00' WHERE id='".$orderRecordId."'";
    $this->dbLocal->executeQuery($query);
  }

  function verzendStatusMail($airsOrderId,$oudeStatus,$nieuweStatus)
  {
    global $__ORDERvar;

    $cfg = new AE_config();
    $mailserver = $cfg->getData('smtpServer');


    $db=new DB();
    $query="SELECT uitvoeringsAantal,uitvoeringsDatum,uitvoeringsPrijs FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid='".$airsOrderId."'";
    $db->SQL($query);
    $db->Query();
    $uitvoeringen=array();
    $totaalUitvoeringen=0;
    while ($data = $db->nextRecord())
    {
      $uitvoeringen[]=$data;
      $totaalUitvoeringen+=$data['uitvoeringsAantal'];
    }
    $query="SELECT SUM(OrderRegelsV2.aantal) as aantal FROM OrderRegelsV2 WHERE OrderRegelsV2.orderid='".$airsOrderId."'";
    $db->SQL($query);
    $db->Query();
    $data = $db->nextRecord();
    $orderegelTotaal=$data['aantal'];


    $query = "SELECT
OrdersV2.id,
Gebruikers.emailAdres,
OrdersV2.fonds,
Vermogensbeheerders.OrderStatusKeuze,
OrderRegelsV2.portefeuille,
OrderRegelsV2.client,
OrderRegelsV2.aantal,
OrdersV2.transactieSoort,
OrdersV2.fondsOmschrijving
FROM OrdersV2
INNER JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid
INNER JOIN Portefeuilles ON OrderRegelsV2.portefeuille = Portefeuilles.Portefeuille
INNER JOIN Gebruikers ON Portefeuilles.Accountmanager = Gebruikers.Accountmanager
INNER JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
WHERE OrdersV2.id='" . $airsOrderId . "'";
    $db->SQL($query);
    $db->Query();
    $mailData = array();
    while ($data = $db->nextRecord())
    {
      $orderStatusmelding = unserialize($data['OrderStatusKeuze']);
      if (($orderStatusmelding[$nieuweStatus]['checkedEmail'] == 1 && $oudeStatus != $nieuweStatus ) || ($orderStatusmelding[$nieuweStatus]['checkedEmail'] == 1 && $orderegelTotaal==$totaalUitvoeringen && $oudeStatus==$nieuweStatus ))
      {
        if ($data['emailAdres'] <> '')
        {
          $mailData[$data['emailAdres']][] = $data;
        }
      }
    }


    foreach ($mailData as $emailAdres => $orderData)
    {
      $subject = "Order " . $airsOrderId. " van status " . $__ORDERvar['orderStatus'][$oudeStatus] . " naar " . $__ORDERvar['orderStatus'][$nieuweStatus];
      $mailBody = "<h3>Order " . $airsOrderId . " naar status " . $__ORDERvar['orderStatus'][$nieuweStatus] . "</h3>";
      if ($orderData[0]['uitvoeringsPrijs'] <> '')
        $mailBody.="uitvoeringsPrijs: " . $orderData[0]['uitvoeringsPrijs'] . "<br>";
      $mailBody.="<table border=1>";
      $mailBody.="<tr><td>portefeuille</td><td>client</td><td>aantal</td><td>transactie</td><td>fondsOmschrijving</td></tr>";
      foreach ($orderData as $orderRegel)
      {
        $mailBody.="<tr>
           <td>" . $orderRegel['portefeuille'] . "</td>
           <td>" . $orderRegel['client'] . "</td>
           <td align='right'>" . $orderRegel['aantal'] . "</td>
           <td>" . $orderRegel['transactieSoort'] . "</td>
           <td>" . $orderRegel['fondsOmschrijving'] . "</td>
           </tr>";
      }
      $mailBody.="</table>";

      $mailBody.="<br>\n<table border=1>";
      $mailBody.="<tr><td>uitvoeringsAantal</td><td>uitvoeringsDatum</td><td>uitvoeringsPrijs</td></tr>";
      foreach ($uitvoeringen as $uitvoering)
        $mailBody.="<tr>
           <td align='right'>" . $uitvoering['uitvoeringsAantal'] . "</td>
           <td align='right'>" . $uitvoering['uitvoeringsDatum'] . "</td>
           <td align='right'>" . $uitvoering['uitvoeringsPrijs'] . "</td>
           </tr>";
      $mailBody.="</table>";

      $mailBody.="<br>\n Verzonden op " . date("d-m-Y H:i");

      if($mailserver !='')
      {
        $emailAddesses=explode(";",$emailAdres);
        include_once('../classes/AE_cls_phpmailer.php');
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->From     = $emailAddesses[0];
        $mail->FromName = "Airs";
        $mail->Body    = $mailBody;
        $mail->AltBody = html_entity_decode(strip_tags($mailBody));
        foreach ($emailAddesses as $emailadres)
          $mail->AddAddress($emailadres,$emailadres);
        $mail->Subject = $subject;
        $mail->Host=$mailserver;
        if(!$mail->Send())
          echo "Verzenden van e-mail mislukt.";
      }
    }


  }

}

?>