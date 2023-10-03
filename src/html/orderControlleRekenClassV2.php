<?php

/*
  AE-ICT source module
  Author  						: $Author: rvv $
  Laatste aanpassing	: $Date: 2020/06/06 15:46:03 $
  File Versie					: $Revision: 1.107 $

  $Log: orderControlleRekenClassV2.php,v $
  Revision 1.107  2020/06/06 15:46:03  rvv
  *** empty log message ***

  Revision 1.106  2020/04/20 05:53:57  rvv
  *** empty log message ***

  Revision 1.105  2020/04/18 17:04:33  rvv
  *** empty log message ***

  Revision 1.104  2020/03/11 15:07:17  rvv
  *** empty log message ***

  Revision 1.103  2020/02/29 16:20:56  rvv
  *** empty log message ***

  Revision 1.102  2020/02/19 14:59:20  rvv
  *** empty log message ***

  Revision 1.101  2020/01/11 14:35:57  rvv
  *** empty log message ***

  Revision 1.100  2020/01/08 15:45:12  rvv
  *** empty log message ***

  Revision 1.99  2020/01/06 05:13:36  rvv
  *** empty log message ***

  Revision 1.98  2020/01/05 10:37:10  rvv
  *** empty log message ***

  Revision 1.97  2020/01/04 18:54:41  rvv
  *** empty log message ***

  Revision 1.96  2019/09/22 08:42:35  rvv
  *** empty log message ***

  Revision 1.95  2019/09/21 16:30:12  rvv
  *** empty log message ***

  Revision 1.94  2019/08/17 18:08:31  rvv
  *** empty log message ***

  Revision 1.93  2019/08/14 16:30:09  rvv
  *** empty log message ***

  Revision 1.92  2019/06/22 16:30:27  rvv
  *** empty log message ***

  Revision 1.91  2019/05/18 16:26:59  rvv
  *** empty log message ***

  Revision 1.90  2019/04/27 18:29:47  rvv
  *** empty log message ***

  Revision 1.89  2018/12/12 16:16:48  rvv
  *** empty log message ***

  Revision 1.88  2018/12/08 18:25:00  rvv
  *** empty log message ***

  Revision 1.87  2018/12/01 19:48:44  rvv
  *** empty log message ***

  Revision 1.86  2018/11/29 07:48:48  rvv
  *** empty log message ***

  Revision 1.85  2018/11/17 17:30:55  rvv
  *** empty log message ***

  Revision 1.84  2018/11/16 16:39:19  rvv
  *** empty log message ***

  Revision 1.83  2018/11/10 18:21:35  rvv
  *** empty log message ***

  Revision 1.82  2018/09/19 09:27:42  rvv
  *** empty log message ***

  Revision 1.81  2018/09/16 08:04:49  rvv
  *** empty log message ***

  Revision 1.80  2018/09/15 17:44:10  rvv
  *** empty log message ***

  Revision 1.79  2018/08/19 08:06:30  rvv
  *** empty log message ***

  Revision 1.78  2018/08/18 12:40:14  rvv
  php 5.6 & consolidatie

  Revision 1.77  2018/07/25 07:01:30  rvv
  *** empty log message ***

  Revision 1.76  2018/05/14 06:27:48  rvv
  *** empty log message ***

  Revision 1.75  2018/05/13 09:16:07  rvv
  *** empty log message ***

  Revision 1.74  2018/05/12 15:43:07  rvv
  *** empty log message ***

  Revision 1.73  2018/05/06 11:31:30  rvv
  *** empty log message ***

  Revision 1.72  2018/03/11 10:52:00  rvv
  *** empty log message ***

  Revision 1.71  2018/02/14 16:23:19  rm
  6574

  Revision 1.70  2018/02/14 10:29:02  rm
  6574

  Revision 1.69  2017/12/31 09:36:45  rvv
  *** empty log message ***

  Revision 1.68  2017/12/30 09:47:59  rvv
  *** empty log message ***

  Revision 1.67  2017/12/06 16:47:38  rvv
  *** empty log message ***

  Revision 1.66  2017/12/02 19:11:59  rvv
  *** empty log message ***

  Revision 1.65  2017/11/19 14:26:52  rvv
  *** empty log message ***

  Revision 1.64  2017/11/18 18:57:19  rvv
  *** empty log message ***

  Revision 1.63  2017/11/12 13:25:34  rvv
  *** empty log message ***

  Revision 1.62  2017/11/11 18:22:43  rvv
  *** empty log message ***

  Revision 1.61  2017/09/03 11:39:56  rvv
  *** empty log message ***

  Revision 1.60  2017/06/25 10:33:30  rvv
  *** empty log message ***

  Revision 1.59  2017/06/24 16:33:47  rvv
  *** empty log message ***

  Revision 1.58  2017/06/10 18:11:08  rvv
  *** empty log message ***

  Revision 1.57  2017/05/31 16:14:10  rvv
  *** empty log message ***

  Revision 1.56  2017/05/11 11:58:52  rvv
  *** empty log message ***

  Revision 1.55  2017/05/11 06:32:01  rvv
  *** empty log message ***

  Revision 1.54  2017/05/10 15:51:49  rvv
  *** empty log message ***

  Revision 1.53  2017/05/10 14:39:25  rvv
  *** empty log message ***

  Revision 1.52  2017/05/08 18:22:21  rvv
  *** empty log message ***

  Revision 1.51  2017/04/19 19:02:30  rvv
  *** empty log message ***

  Revision 1.50  2017/04/19 16:02:23  rvv
  *** empty log message ***

  Revision 1.49  2017/04/05 15:36:50  rvv
  *** empty log message ***

  Revision 1.48  2017/03/31 15:38:23  rvv
  *** empty log message ***

  Revision 1.47  2017/03/08 16:54:47  rvv
  *** empty log message ***

  Revision 1.46  2016/12/27 18:16:47  rvv
  *** empty log message ***

  Revision 1.45  2016/12/24 16:33:40  rvv
  *** empty log message ***

  Revision 1.44  2016/12/22 08:30:51  rvv
  *** empty log message ***

  Revision 1.43  2016/12/22 07:18:59  rvv
  *** empty log message ***

  Revision 1.42  2016/12/21 16:32:06  rvv
  *** empty log message ***

  Revision 1.41  2016/07/27 15:56:14  rvv
  *** empty log message ***

  Revision 1.40  2016/07/25 05:33:53  rvv
  *** empty log message ***

  Revision 1.39  2016/07/24 09:25:42  rvv
  *** empty log message ***

  Revision 1.38  2016/07/20 16:07:33  rvv
  *** empty log message ***

  Revision 1.37  2016/07/16 15:18:12  rvv
  *** empty log message ***

  Revision 1.36  2016/07/13 15:41:08  rvv
  *** empty log message ***

  Revision 1.35  2016/07/09 18:56:46  rvv
  *** empty log message ***

  Revision 1.34  2016/07/06 16:05:51  rvv
  *** empty log message ***

  Revision 1.33  2016/07/03 08:38:32  rvv
  *** empty log message ***

  Revision 1.32  2016/06/25 16:29:24  rvv
  *** empty log message ***

  Revision 1.31  2016/06/15 15:54:57  rvv
  *** empty log message ***

  Revision 1.30  2016/06/05 12:18:55  rvv
  *** empty log message ***

  Revision 1.29  2016/06/01 11:44:46  rvv
  *** empty log message ***

  Revision 1.28  2016/06/01 07:37:48  rvv
  *** empty log message ***

  Revision 1.27  2016/04/24 15:28:49  rvv
  *** empty log message ***

  Revision 1.26  2016/04/06 15:36:05  rvv
  *** empty log message ***

  Revision 1.25  2016/03/17 14:50:11  rm
  OrdersV2

  Revision 1.24  2016/03/13 16:22:57  rvv
  *** empty log message ***

  Revision 1.23  2016/02/21 17:20:10  rvv
  *** empty log message ***

  Revision 1.22  2016/02/14 11:17:26  rvv
  *** empty log message ***

  Revision 1.21  2015/12/27 16:28:12  rvv
  *** empty log message ***

 */
global $__appvar;
include_once("../classes/AE_cls_fpdf.php");
include_once("./rapport/Zorgplichtcontrole.php");
include_once("./rapport/rapportRekenClass.php");
define('FPDF_FONTPATH', $__appvar["basedir"] . "/html/font/");

Class orderControlleBerekeningV2
{

  var $__ORDERvar;
  var $errors = array();
  
  function orderControlleBerekeningV2($bulk = false,$bulkIdFilter='')
  {
    global $__appvar;
    $this->data = array();
    $this->checks = array();
    $this->allchecks = array();
    $this->vermogensbeheerderchecks = array();
    $this->bulk = $bulk;
    $this->bulkIdFilter = $bulkIdFilter;
    $this->mailTxt=array();
    $this->checksKort=array();
    $this->extraLogs=false;
    if(isset($__appvar['OptieValDebug']))
      $this->extraLogs=$__appvar['OptieValDebug'] ;
  }
  
  function getPortefeuilleOpties($portefeuille='',$fonds='',$depot='',$rekeningForceren=false)
  {
    $db=new DB();
  
    $query="SELECT Portefeuilles.Depotbank,Portefeuilles.Accountmanager,Portefeuilles.Vermogensbeheerder,Vermogensbeheerders.OrderuitvoerBewaarder, Vermogensbeheerders.orderViaConsolidatie FROM Portefeuilles
JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder=Vermogensbeheerders.Vermogensbeheerder WHERE Portefeuille='$portefeuille'";
    $db->SQL($query);
    $portdata=$db->lookupRecord();
    if($depot=='')
      $depot=$portdata['Depotbank'];

    if($portdata['OrderuitvoerBewaarder']==1 || $portdata['orderViaConsolidatie']==1 )
    {
      $query = "SELECT 
if(Rekeningen.Depotbank='$depot',0,1) as volgordeDepot,
if(Rekeningen.Afdrukvolgorde=0,99,Rekeningen.Afdrukvolgorde) as volgordeRekening, 
 Rekeningen.Rekening,Rekeningen.Portefeuille,Rekeningen.Valuta,Rekeningen.Depotbank
FROM Rekeningen WHERE 
Rekeningen.Deposito = 0 AND Rekeningen.Inactief = 0  AND Rekeningen.Memoriaal = 1 AND
Rekeningen.Portefeuille='$portefeuille' ORDER BY volgordeDepot,volgordeRekening,Rekeningen.afdrukVolgorde,rekening";
    
    }
    else
    {
      $query = "SELECT Rekening, 
    if(Rekeningen.Valuta='EUR',-1,Valutas.Afdrukvolgorde) as volgordeLiq , 
    if(Rekeningen.Depotbank='".$depot."',0,1) AS volgordeDepot,
    Rekeningen.Valuta
    FROM Rekeningen JOIN Valutas ON Rekeningen.Valuta=Valutas.Valuta 
    WHERE 
    Rekeningen.consolidatie=0 AND Rekeningen.Inactief=0 AND Rekeningen.Memoriaal=0 AND Rekeningen.Deposito=0 AND Rekeningen.Portefeuille = '$portefeuille' 
    ORDER BY volgordeDepot, Rekeningen.Afdrukvolgorde, volgordeLiq limit 1";
    }
    $db->SQL($query);
    $rekdata=$db->lookupRecord();

    if($portdata['OrderuitvoerBewaarder']==1 || $portdata['orderViaConsolidatie']==1)
    {
      $depot = $rekdata['Depotbank'];
    }
    else
    {
      $query = "SELECT fixDepotbankenPerVermogensbeheerder.rekeningNrTonen FROM fixDepotbankenPerVermogensbeheerder WHERE 
   fixDepotbankenPerVermogensbeheerder.vermogensbeheerder='" . $portdata['Vermogensbeheerder'] . "' AND
   fixDepotbankenPerVermogensbeheerder.depotbank='" . $depot . "' ";
      $db->SQL($query);
      $fix = $db->lookupRecord();
      if ($db->records() > 0 && $fix['rekeningNrTonen'] == 0 && $rekeningForceren == false)
      {
        $rekdata['Rekening'] = '';
      }
    }

    $query="SELECT Valuta as fondsValuta FROM Fondsen WHERE Fonds='".mysql_real_escape_string($fonds)."'";
    $db->SQL($query);
    $fondsdata=$db->lookupRecord();

    
    return array('Rekening'=>$rekdata['Rekening'],
                 'Depotbank'=>$depot,
                 'accountmanager'=>$portdata['Accountmanager'],
                 'fondsValuta'=>$fondsdata['fondsValuta']);
  }
  
  function getTransactieSoort($portefeuille='',$fonds='',$orderAantal=0,$juldat='')
  {
    $db=new DB();
    $query="SELECT fondssoort FROM Fondsen WHERE Fonds='".mysql_real_escape_string($fonds)."'";
    $db->SQL($query);
    $fondsdata=$db->lookupRecord();
    
    if($fondsdata['fondssoort']=='OPT')
    {
      $query="SELECT ifnull(SUM(Aantal),0) as aantal FROM Rekeningmutaties WHERE 
      Rekening IN (SELECT Rekening  FROM Rekeningen WHERE Portefeuille IN ('".$portefeuille."'))
      AND Fonds='".mysql_real_escape_string($fonds)."' AND Boekdatum >= '".date('Y',$juldat).'-01-01'."' AND Boekdatum <= '".date('Y-m-d',$juldat)."'";
      $db->SQL($query); 
      $db->Query();
      $fondsAantal=$db->lookupRecord();
      $fondsAantal=$fondsAantal['aantal'];

      if($orderAantal>0 && $fondsAantal>=0)
        $transactieSoort='AO';
      elseif($orderAantal>0 && $fondsAantal<0)
        $transactieSoort='AS';
      elseif($orderAantal<0 && $fondsAantal<=0)
        $transactieSoort='VO';
      elseif($orderAantal<0 && $fondsAantal>0)
        $transactieSoort='VS';

      //echo"$fonds | a: $fondsAantal | o: $orderAantal | t: $transactieSoort <br>\n";
    }
    else
    {
      if($orderAantal < 0)
        $transactieSoort='V';
      else
        $transactieSoort='A';   
    }
        
    return $transactieSoort;
  }
  
  function updateChecksByBulkorderregelId($orderregelId,$checkVinkjes,$resetCheckVinkjes=false,$accountmanagerValidatie=false)
  {
    global $__ORDERvar,$__appvar;
    $orderLog=new orderLogs();
    $db=new DB();
    $validatieVast='';

      $query="SELECT portefeuille,rekening,aantal,controleRegels,fonds FROM TijdelijkeBulkOrdersV2 WHERE id='".$orderregelId."'";
      $db->SQL($query);
      $db->Query();
      $orderegelData=$db->lookupRecord();
      $validatieVelden=array('aanw'=>'Aanw','short'=>'Short','liqu'=>'Liqu','zorg'=>'Zorg','risi'=>'Risi','groot'=>'Groot','vbep'=>'Vbep','akkam'=>'AkkAM');
   
      $export['controleRegels']=unserialize($orderegelData['controleRegels']);
      $this->setdata($orderregelId,$orderegelData['portefeuille'],$orderegelData['rekening'],$orderegelData['aantal']);
      $checks = getActieveControles('',$orderegelData['portefeuille']);
   
    if($resetCheckVinkjes===2)
    {
     // echo "---<br>\n";
     // listarray($export["controleRegels"]);
      $checkall=$export["controleRegels"];
      foreach($checks as $check=>$checkData)
        $checkall[$check]['checked'] = 0;
      $this->setregels($checkall);
      $resultaat = $this->check();
    }

//echo $resetCheckVinkjes;
 //   listarray($export['controleRegels']);
  
    if(isset($__appvar['extraOrderLogging']))
      $extraLog=$__appvar['extraOrderLogging'];
    else
      $extraLog=false;

    if($accountmanagerValidatie==true)
    {

      $afvinken=true;
      foreach($checks as $check=>$checkData)
      {
        if ($check <> 'akkam' && $export['controleRegels'][$check]['short'] <> 0)
        {
          $afvinken=false;
          //$export['controleRegels'][$check]['checked'] =
        }
      }
      if($afvinken==true)
      {
        $checkVinkjes['akkam'] = 1;
        if($extraLog)
          logIt("$orderregelId|".$orderegelData['portefeuille']."|".$orderegelData['fonds']."|mutatie akkam via Akk.Am.Check|");
      }

    }

    $resetAllChecks=false;
      foreach($checks as $check=>$checkData)
      {
//ano, fin/ant,alv

        if($resetCheckVinkjes===2)
        {
          $vorigResultaat = $export['controleRegels'][$check]['resultaat'] ;
          if(trim($vorigResultaat) <> trim($resultaat[$check]))
          {
            if($export['controleRegels'][$check]['checked']<>0)
            {
              $orderLog->addToBulkLog($orderregelId, 'Portefeuille ' . $orderegelData['portefeuille'] . ' check ' . $check . ' -> 0 (mutatie)');
              if($extraLog)
                logIt("$orderregelId|".$orderegelData['portefeuille']."|".$orderegelData['fonds']."|mutatie $check|".$vorigResultaat."|".$resultaat[$check]);
            }
            $export['controleRegels'][$check]['checked'] = 0;
            $validatieVast='validatieVast=0,';
            $resetAllChecks=true;
          }
        }
        elseif($resetCheckVinkjes==true && $export['controleRegels'][$check]['checked']==1)
        {
          //echo $export['controleRegels'][$check]['checked'];
          if($export['controleRegels'][$check]['checked']<>0)
          {
            $orderLog->addToBulkLog($orderregelId, 'Portefeuille ' . $orderegelData['portefeuille'] . ' check ' . $check . ' -> 0 (reset)');
            if($extraLog)
              logIt("$orderregelId|".$orderegelData['portefeuille']."|".$orderegelData['fonds']."|reset $check|".$export['controleRegels'][$check]['resultaat']."|".$resultaat[$check]);
          }
          $export['controleRegels'][$check]['checked'] = 0;
          $validatieVast='validatieVast=0,';

        }

        if($checkVinkjes[$check]==1)
        {
          if($export['controleRegels'][$check]['checked']==0)
          {
            $orderLog->addToBulkLog($orderregelId, 'Portefeuille ' . $orderegelData['portefeuille'] . ' check ' . $check . ' -> 1');
            if($extraLog)
              logIt("$orderregelId|".$orderegelData['portefeuille']."|".$orderegelData['fonds']."|check $check|".$export['controleRegels'][$check]['resultaat']."|".$resultaat[$check]);
          }
          $export['controleRegels'][$check]['checked'] = 1;
        }
      }
    
    if($resetAllChecks==true)
    {
      foreach($checks as $check=>$checkData)
        $export['controleRegels'][$check]['checked'] = 0;
    }


   // listarray($export);
    if($resetCheckVinkjes!==2)
    {
      $this->setregels($export["controleRegels"]);
      $this->check();
    }


      foreach ($checks as $key => $checkName)
      {
        if(isset($this->checkResultaat[$key]))
          $export['controleRegels'][$key]['resultaat'] = $this->checkResultaat[$key];
        $export['controleRegels'][$key]['naam'] = $__ORDERvar["orderControles"][$key];

        if($resetCheckVinkjes!==false)
          $export['controleRegels'][$key]['short'] = ( isset($this->checksKort[$key]) ? $this->checksKort[$key] : null);

        if(isset($this->mailTxt[$key]))
          $export['controleRegels'][$key]['mailTxt'] = $this->mailTxt[$key];
      }


     //   listarray($this->checkResultaat);

      $maxCheck=$this->checkmaxGetal();
  //  echo "old:".$maxCheck."<br>\n";
      $maxCheck=0;
      foreach($export['controleRegels'] as $check=>$values)
      {
        if($values['checked'] == 0)
          $maxCheck = max($values['short'], $maxCheck);
      }
//listarray($export["controleRegels"]);
   // echo "new".$maxCheck."<br>\n";
   // echo " $validatieVast voor $orderregelId <br>\n";
      $query="UPDATE TijdelijkeBulkOrdersV2 SET controleStatus='$maxCheck',";
      foreach($validatieVelden as $check=>$checkNaam)
       $query.="validatie".$checkNaam."='".$export["controleRegels"][$check]['short']."', $validatieVast ";
      $query.=" controleRegels='".mysql_real_escape_string(serialize($export["controleRegels"]))."' WHERE id='".$orderregelId."'";
      $db->SQL($query); //echo "| $query |<br>";
      $db->Query();
      

      
      return array('controleStatus'=>$maxCheck,'controleRegels'=>$export['controleRegels']);
    // echo "$query <br>\n";


  }

  function setallchecks($data = array())
  {
    $this->allchecks = $data;
  }

  function setchecks($data = array())
  {
   
    foreach($data as $key=>$value)
    {
      if ($value['checked'] == 1)
      {
        $this->checks[$key]['checked'] = ($__ORDERvar["orderControles"][$key] = '1');
      }
      else
      {
        $this->checks[$key]['checked'] = ($__ORDERvar["orderControles"][$key] = '0');
      }
      $this->checks[$key]['negeren'] = 0;
    }
  }

  function checkMaxGetal()
  {
    $resultaat = $this->check();
    $hoogste = 0;
    if ( is_array($resultaat) ) {
      foreach ($resultaat as $keyname => $value)
      {
        if ($this->checksKort[$keyname] > $hoogste) {
          $hoogste = $this->checksKort[$keyname];
        }
      }
    }

    return $hoogste;
  }

  function checkmax()
  {
    $resultaat = $this->check();
    $hoogste = 0;
    foreach ($resultaat as $keyname => $value)
    {
      if ($value > $hoogste)
        $hoogste = $value;
    }
    return $hoogste;
  }

  function check()
  {
    $resultaat = array();
    $checks=array('aanw'=>'aanwezigheidsCheck',
                  'short'=>'ShortPositiesCheck',
                  'liqu'=>'LiquiditeitenCheck',
                  'zorg'=>'ZorgplichtCheck',
                  'risi'=>'RisicoCheck',
                  'groot'=>'GrootteCheck',
                  'vbep'=>'BeperkingenCheck',
                  'akkam'=>'AkkoordCheck',
                  'optie'=>'OptieCheck',
                  'rest'=>'RestrictieCheck');

    $mogelijkeChecksEigenFonds=array('akkam','vbep');
    foreach($checks as $check=>$checkFunction)
    {
      if ( isset($this->checks[$check]) && $this->checks[$check]['checked'] == 1 && $this->checks[$check]['negeren'] == 0)
      {
        if ($this->data['Fonds'] == '' && $this->data['fondsKoers']==0.00 && !in_array($check,$mogelijkeChecksEigenFonds))
          $resultaat[$check] = "Check overgeslagen ivm handmatig fonds.";
        else
          $resultaat[$check] = $this->$checkFunction();
      }
    }
       
    if ( isset($this->data['tijdelijkeTabel']) && $this->data['tijdelijkeTabel'] == 1)
    {
      $this->VerwijderTijdelijkeTabel();
    }
    $this->checkResultaat=$resultaat;
    return $resultaat;
  }


  function getchecks($vermogensbeheerder = '')
  {
    /**
     * @todo kan deze controlle weg ivm nieuw fonds
     */
//    if ($this->data['Fonds'] == '')
//    {
//      $this->vermogensbeheerderchecks = array();
//      $this->errors['fonds_field'] = 'Geen fonds';
//      return false;
//    }
    $db = new DB();
    $vermogensbeheerderFilter = ( ! empty($vermogensbeheerder) ? "WHERE Vermogensbeheerder = '$vermogensbeheerder'" : '');

    $query = "SELECT Vermogensbeheerders.Vermogensbeheerder, Vermogensbeheerders.order_controle, Vermogensbeheerders.OrderuitvoerBewaarder, Vermogensbeheerders.orderLiqVerkopen FROM Vermogensbeheerders $vermogensbeheerderFilter";
    $db->SQL($query);
    $db->Query();
    while ($checks = $db->nextRecord())
    {
      $aktief=getActieveControles($checks['Vermogensbeheerder']);
      $velden=unserialize($checks['order_controle']);
      $nieuweVelden=array();
      foreach($aktief as $checknaam=>$omschrijving)
        $nieuweVelden[$checknaam]=$velden[$checknaam];
      $this->vermogensbeheerderchecks[$checks['Vermogensbeheerder']] = $nieuweVelden;//$checks['order_controle'];
      $this->vermogensbeheerderInclVerkopen[$checks['Vermogensbeheerder']] = $checks['orderLiqVerkopen'];
    }

    return $this->vermogensbeheerderchecks;
  }


  function setregels($regels = array())
  {
    if ( is_array ($regels) ) 
    {
      foreach ( $regels as $key => $value ) {
        $this->checks[$key]['negeren'] = 0;
        if ( isset ($value['checked']) && $value['checked'] == 1) {
          $this->checks[$key]['negeren'] = 1;
        }
      }
    }
    else
    {
      foreach ($this->allchecks as $key => $value) {
        $this->checks[$key]['negeren'] = 0;
      }
    }
  }

  function getOrderregelDateFilter($orderId,$portefeuille)
  {
    if($orderId > 0 && $this->bulk == false)
    {
      $db = new DB();
      $query = "SELECT (add_date + interval 10 second) as add_date FROM OrderRegelsV2 WHERE orderId='" . $orderId . "' AND portefeuille='".$portefeuille."'";
      $db->SQL($query);
      $data = $db->lookupRecord();
      if($data['add_date'] <> '')
        $filter  = "AND OrderRegelsV2.add_date <= '".$data['add_date']."'";
      return $filter;
    }
    else
      return '';
  }
  /**
   * setData
   * Instellen van de data voor de berekeningen
   * 
   * @param type $orderid = id van de order
   * @param type $portefeuille =  portefeuillenummer
   * @param type $rekening =  rekeningnummer
   * @param type $aantal = aantal aankoop/verkoop
   * @param type $silent = regelkleur
   */
  
  function setdata($orderid, $portefeuille, $rekening='', $aantal, $silent = false)
  {

    $this->data['eigenOrderid'] = $orderid;
    $this->data['portefeuille'] = $portefeuille;
    $this->data['rekening'] = $rekening;
    $this->data['valuta'] = 'EUR';
    
    $this->data['transactieAantal'] = $aantal;
    $this->data['aantal'] = $aantal;
    $this->data['silent'] = $silent;
    $this->data['bulk'] = $this->bulk;
    $this->data['rapportageDatum'] = substr(getLaatsteValutadatum(), 0, 10);
    $this->data['tijdelijkeTabel'] = 0;

    $portefeuilleObject = new Portefeuilles ();
    $vermogensBeheerder = $portefeuilleObject->parseBySearch(array('portefeuille' => $portefeuille), 'Vermogensbeheerder');

    /** ajax validatie voor het opslaan **/
    if ( isset($_SESSION['orderData']) && is_array($_SESSION['orderData']) )
    {
      $huidige = $_SESSION['orderData'];
      $huidige['Fonds'] = $_SESSION['orderData']['fonds'];
      $huidige['ISINCode'] = $_SESSION['orderData']['ISINCode'];
  
      if(is_object($this->orderregelObject) && $this->orderregelObject->get('id')>0)
      {
        $this->data['aantal'] = $this->orderregelObject->get('aantal');
        $this->data['bedrag']= $this->orderregelObject->get('bedrag');
        $this->data['transactieAantal'] = $this->data['aantal'];
      }
      
      foreach($huidige as $key=>$value)
      {
        if($value<>'')
          $this->data[$key] = $value;
      }
    }
    elseif ($this->bulk == true)/** bulk orders **/
    {
      $db=new DB();
      $query = "SELECT TijdelijkeBulkOrdersV2.id, TijdelijkeBulkOrdersV2.Fonds,transactieSoort, '' as transactieType,
    Portefeuilles.vermogensBeheerder,Fondsen.Valuta as fondsValuta,koersLimiet,TijdelijkeBulkOrdersV2.ISINCode,
    '' as fixOrder , TijdelijkeBulkOrdersV2.koers,TijdelijkeBulkOrdersV2.depotbank, TijdelijkeBulkOrdersV2.bedrag
    FROM TijdelijkeBulkOrdersV2 
    JOIN Portefeuilles ON TijdelijkeBulkOrdersV2.Portefeuille=Portefeuilles.Portefeuille
    LEFT JOIN Fondsen ON TijdelijkeBulkOrdersV2.fonds=Fondsen.Fonds WHERE TijdelijkeBulkOrdersV2.id='$orderid' ";
      $db->SQL($query);
      $huidige = $db->lookupRecord();
      $this->data['bedrag']= $huidige['bedrag'];
    }
    else /** opslaan order **/
    {
      $eigenOrderid = $this->data['eigenOrderid'];

      $query = "SELECT OrdersV2.Fonds,
  				   OrdersV2.transactieSoort,
  				   OrdersV2.transactieType,
  				   OrdersV2.depotbank,
  				   OrdersV2.fondsOmschrijving,
  				   OrdersV2.ISINCode,
  				   Fondsen.Fonds as Fonds,
  				   Fondsen.Valuta as fondsValuta,
  				   OrdersV2.koersLimiet,
             OrdersV2.fixOrder
  			FROM OrdersV2 
  			LEFT JOIN Fondsen ON OrdersV2.fonds = Fondsen.Fonds
  			WHERE OrdersV2.id = '" . $eigenOrderid . "'";

      $db = new DB();
      $db->SQL($query);
      $db->Query();
      $huidige = $db->nextRecord();
      if(is_object($this->orderregelObject) && $this->orderregelObject->get('id')>0)
      {
        $this->data['aantal'] = $this->orderregelObject->get('aantal');
        $this->data['bedrag']= $this->orderregelObject->get('bedrag');
        $this->data['transactieAantal'] = $this->data['aantal'];
      }
      $this->data['fondsOmschrijving'] = $huidige['fondsOmschrijving'];
    }

    $this->data['fonds'] = $huidige['Fonds']; //=> Greater Europe Fund
    $this->data['transactieSoort'] = $huidige['transactieSoort']; //=> V
    $this->data['transactieType'] = $huidige['transactieType']; //=>
    $this->data['vermogensBeheerder'] = $vermogensBeheerder; //=> dgc
   
    $this->data['Fonds'] = $huidige['Fonds']; //=> Greater Europe
    $this->data['fondsValuta'] = $huidige['fondsValuta']; //=> USD
    $this->data['koersLimiet'] = $huidige['koersLimiet']; //=> 0.00000
    $this->data['fixOrder'] = $huidige['fixOrder'];
    $this->data['ISINCode'] = $huidige['ISINCode'];
    $this->data['fondsKoers'] = $huidige['koers'];
    $this->data['depotbank'] = $huidige['depotbank'];

    $checks=$this->getchecks($vermogensBeheerder);
    if (!requestType('ajax'))
    {
    //  listarray($this->data);exit;
    }
    $this->setchecks($checks[$vermogensBeheerder]);
    $this->data['inclVerkopen'] = $this->vermogensbeheerderInclVerkopen[$vermogensBeheerder];
  }

  function vulTijdelijkeTabel()
  {
    global $__appvar;
    $fondswaarden = berekenPortefeuilleWaarde($this->data['portefeuille'], $this->data['rapportageDatum']);
    vulTijdelijkeTabel($fondswaarden, $this->data['portefeuille'], $this->data['rapportageDatum']);
    $this->data['tijdelijkeTabel'] = 1;
  }

  function VerwijderTijdelijkeTabel()
  {
    verwijderTijdelijkeTabel($this->data['portefeuille'], $this->data['rapportageDatum']);
    $this->data['tijdelijkeTabel'] = 0;
  }
  /*
  function createControleExport($data)
  {
    global $__ORDERvar;

    $export=array();
    foreach ( $__ORDERvar['orderControles'] as $key => $checkName ) 
    {
      $export['controleRegels'][$key]['resultaat'] = ( isset($this->checkResultaat[$key]) ? $this->checkResultaat[$key] : null);
      $export['controleRegels'][$key]['naam'] = $__ORDERvar["orderControles"][$key];
      $export['controleRegels'][$key]['short'] = ( isset($this->checksKort[$key]) ? $this->checksKort[$key] : null);
    
	    if( isset ($data['order_controle_checkbox_'.$key]) ) 
      {
       $export['controleRegels'][$key]['checked'] = $data['order_controle_checkbox_' . $key];
      }
	  }
    
    return $export;
  }
  */

  function getKoersen($fonds,$orderId=0)
  {
    $rapportageDatum = substr($this->data['rapportageDatum'], 0, 10);
    $db2=new DB();
    if($this->bulk==true && $orderId > 0)
    {
      $query = "SELECT koers as fondsKoers,fondsValuta,fondseenheid FROM TijdelijkeBulkOrdersV2 WHERE id=$orderId";
      $db2->SQL($query);
      $overigeFondsKoers = $db2->lookupRecord();
    }
    if($fonds<> '' && ( !isset($overigeFondsKoers) || $overigeFondsKoers['fondsKoers'] == 0 || $overigeFondsKoers['fondseenheid'] == 0 || $overigeFondsKoers['fondsValuta'] == ''))
    {
      $query = "SELECT Fondskoersen.Koers as fondsKoers, Fondsen.Valuta as fondsValuta, Fondsen.Fondseenheid as fondseenheid FROM Fondskoersen ,Fondsen
 	  			    WHERE Fondskoersen.Fonds = Fondsen.Fonds AND
 	  			    Fondsen.Fonds = '" . $fonds. "' AND
 	  			    Fondskoersen.Datum <= '" .$rapportageDatum. "'
 	  			    ORDER BY Datum DESC LIMIT 1";
      $db2->SQL($query);
      $overigeFondsKoers = $db2->lookupRecord();
    }
    $query = "SELECT Koers FROM Valutakoersen
					  WHERE Valuta = '" . $overigeFondsKoers['fondsValuta'] . "' AND
					  Datum <= '" .$rapportageDatum. "'
					  ORDER BY Datum DESC LIMIT 1";
    $db2->SQL($query);
    $overigeFondsValutaKoers = $db2->lookupRecord();
    $overigeFondsKoers['valutaKoers']=$overigeFondsValutaKoers['Koers'];

    return $overigeFondsKoers;
  }
  
  function aanwezigheidsCheck()
  {
    $portefeuille = $this->data['portefeuille'];
    $eigenOrderid = $this->data['eigenOrderid'];
    $txt = "";
    $txtMail="";
    $txtSilent = "0";

    $db = new DB();
    if ($this->bulk == true)
    {
      if($this->data['Fonds'] =='' && $this->data['ISINCode'] <> '' )
        $fondsFilter="AND TijdelijkeBulkOrdersV2.ISINCode = '" . $this->data['ISINCode'] . "'";
      elseif($this->data['Fonds'] =='' &&  $this->data['fondsOmschrijving'] <> '' )
        $fondsFilter="AND TijdelijkeBulkOrdersV2.fondsOmschrijving = '" . $this->data['fondsOmschrijving'] . "'";
      else
        $fondsFilter="AND TijdelijkeBulkOrdersV2.Fonds = '" . $this->data['Fonds'] . "'";

      $query = "SELECT id as orderid, aantal, Fonds, transactieSoort,fondsOmschrijving
		  		FROM TijdelijkeBulkOrdersV2
			  	WHERE Portefeuille = '" . $portefeuille . "' 
			  	AND id <> '" . $eigenOrderid . "' $fondsFilter ".$this->bulkIdFilter;
      $db->SQL($query);
      $db->Query();
      while ($actieveTransacties = $db->nextRecord())
      {
        $txt .= "In bulkorder " . $actieveTransacties['orderid'] . " "
                . $actieveTransacties['transactieSoort'] . " "
                . $actieveTransacties['aantal'] . " "
                . $actieveTransacties['fondsOmschrijving'] . "<br>";
        $txtSilent = '1';
        $txtMail = "Openstaande transacties in ".$actieveTransacties['fondsOmschrijving']." aanwezig.";
      }
      $eigenOrderid = -1;
    }

    if($this->data['Fonds'] =='' &&   $this->data['ISINCode'] <> '' )
      $fondsFilter="AND OrdersV2.ISINCode = '" . $this->data['ISINCode'] . "'";
    elseif($this->data['Fonds'] =='' &&   $this->data['fondsOmschrijving'] <> '' )
      $fondsFilter="AND OrdersV2.fondsOmschrijving = '" . $this->data['fondsOmschrijving'] . "'";
    else
      $fondsFilter="AND OrdersV2.Fonds = '" . $this->data['Fonds'] . "'";

    $dateFilter=$this->getOrderregelDateFilter($eigenOrderid,$portefeuille);
    $query = "SELECT OrderRegelsV2.orderid, OrderRegelsV2.aantal, OrdersV2.Fonds, OrdersV2.transactieSoort
				FROM OrdersV2, OrderRegelsV2
				WHERE OrderRegelsV2.orderid = OrdersV2.id
				$fondsFilter
				AND OrderRegelsV2.Portefeuille = '" . $portefeuille . "'
				AND OrderRegelsV2.orderregelStatus < '4'
				AND OrderRegelsV2.orderid <> '" . $eigenOrderid . "' $dateFilter";

    $db->SQL($query);
    $db->Query();
    while ($actieveTransacties = $db->nextRecord())
    {
      if($this->data['Fonds'] =='')
        $fondsBeschrijving= $this->data['fondsOmschrijving'];
     else
       $fondsBeschrijving= $this->data['Fonds'];
      $txt .= "In " . $actieveTransacties['orderid'] . " "
              . $actieveTransacties['transactieSoort'] . " "
              . $actieveTransacties['aantal'] . " $fondsBeschrijving <br>";
      $txtMail = "Openstaande transacties in $fondsBeschrijving aanwezig.";
      $txtSilent = '1';
    }
    if ($txt == "")
    {
      $txt = "Geen niet-uitgevoerde orders voor dit Fonds gevonden.";
      $txtMail="Geen openstaande transacties in dit fonds.";
    }
    $this->checksKort['aanw'] = $txtSilent;
    $this->mailTxt['aanw'] = $txtMail;

    if ($this->data['silent'] == true)
      return $txtSilent;
    else
      return $txt;
  }

  function ShortPositiesCheck()
  {
    $db = new DB();
    $portefeuille = $this->data['portefeuille'];
    $eigenOrderid = $this->data['eigenOrderid'];
    $transactieAantal = $this->data['transactieAantal'];
    $txt = "";
    $txtMail = "";
    $txtSilent = "0";
    $totaalAantal = 0;
    $totaalAantalPortefeuille =0;
    $nominaalAanwezig=false;

    if ($this->data['transactieSoort'] == "V")
    {
      $db = new db();
      $query="SELECT Vermogensbeheerders.OrderuitvoerBewaarder 
FROM Portefeuilles JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder= Vermogensbeheerders.Vermogensbeheerder 
WHERE Portefeuilles.consolidatie=0 AND Portefeuilles.portefeuille='$portefeuille'";
      $db->SQL($query);
      $bewaarder=$db->lookupRecord();


      $txt = "Verkoop check<br>";
      if($this->data['bedrag'] <> 0)
        $nominaalAanwezig=true;
      if ($this->bulk == true)
      {
        $query = "SELECT id as orderid, aantal, Fonds, transactieSoort,depotbank
		  		FROM TijdelijkeBulkOrdersV2
			  	WHERE
		  		Fonds = '" . $this->data['Fonds'] . "'
			  	AND Portefeuille = '" . $portefeuille . "'
			  	AND id <> '" . $eigenOrderid . "' ";
        $db->SQL($query);
        $db->Query();
        while ($actieveTransacties = $db->nextRecord())
        {
          if ($actieveTransacties['transactieSoort'] == "A" || $actieveTransacties['transactieSoort'] == "AO")
          {
            if($bewaarder['OrderuitvoerBewaarder'] == 0 ||  $this->data['depotbank'] <> $actieveTransacties['depotbank'])
              $totaalAantal += $actieveTransacties['aantal'];
            $totaalAantalPortefeuille+= $actieveTransacties['aantal'];
          }
          if ($actieveTransacties['transactieSoort'] == "V")
          {
            if($bewaarder['OrderuitvoerBewaarder'] == 0 ||  $this->data['depotbank'] <> $actieveTransacties['depotbank'])
              $totaalAantal -= $actieveTransacties['aantal'];
            $totaalAantalPortefeuille -= $actieveTransacties['aantal'];
          }
        }
        $eigenOrderid = -1;
      }

      $dateFilter=$this->getOrderregelDateFilter($eigenOrderid,$portefeuille);
      $query = "SELECT OrderRegelsV2.orderid, OrderRegelsV2.aantal,OrderRegelsV2.bedrag, OrdersV2.depotbank, OrdersV2.Fonds, OrdersV2.transactieSoort
	  		   	FROM OrdersV2, OrderRegelsV2
				WHERE OrderRegelsV2.orderid = OrdersV2.id
				AND (OrdersV2.Fonds = '" . $this->data['Fonds'] . "')
				AND OrderRegelsV2.Portefeuille = '" . $portefeuille . "'
				AND OrderRegelsV2.orderregelStatus < '4'
				AND OrderRegelsV2.orderid <> '" . $eigenOrderid . "' $dateFilter";
      $db->SQL($query);
      $db->Query();

      while ($actieveTransacties = $db->nextRecord())
      {
        if ($actieveTransacties['transactieSoort'] == "A" || $actieveTransacties['transactieSoort'] == "AO")
        {
          if($bewaarder['OrderuitvoerBewaarder'] == 0 ||  $this->data['depotbank'] == $actieveTransacties['depotbank'])
            $totaalAantal += (isset ($actieveTransacties['aantal'])?$actieveTransacties['aantal']:0);
          $totaalAantalPortefeuille+= $actieveTransacties['aantal'];
        }
        if ($actieveTransacties['transactieSoort'] == "V")
        {
          if($bewaarder['OrderuitvoerBewaarder'] == 0 ||  $this->data['depotbank'] == $actieveTransacties['depotbank'])
            $totaalAantal -= (isset ($actieveTransacties['aantal'])?$actieveTransacties['aantal']:0);
          $totaalAantalPortefeuille -= $actieveTransacties['aantal'];
        }
       // if($actieveTransacties['bedrag'] <> 0)
       //   $nominaalAanwezig=true;
      }

      //Bewaarder
      if($bewaarder['OrderuitvoerBewaarder']==1)
      {
        $aantalAanwezig       = fondsWaardeOpdatum($portefeuille, $this->data['Fonds'], $this->data['rapportageDatum'], 'EUR', $this->data['rapportageDatum'], '',$this->data['depotbank']);
        $aantalAanwezigTotaal = fondsAantalOpdatum($portefeuille, $this->data['Fonds'], $this->data['rapportageDatum']);
      }
      else
      {
        $aantalAanwezig = fondsAantalOpdatum($portefeuille, $this->data['Fonds'], $this->data['rapportageDatum']);
        $aantalAanwezigTotaal = $aantalAanwezig;
      }
      $totaalAantal += $aantalAanwezig['totaalAantal']; //Het al in Portefeuille aanwezige aantal ophalen.
      $totaalAantal -= $transactieAantal; // Deze transactie erafhalen
  
      $totaalAantalPortefeuille += $aantalAanwezigTotaal['totaalAantal'];
      $totaalAantalPortefeuille -= $transactieAantal;
      if($nominaalAanwezig==true)
      {
        $txt = "Niet te bepalen ivm nominale orders <br>";
        $txtSilent = '2';
      }
      elseif (round($totaalAantal, 4) < 0)
      {
        $txt = "Na verkoop aantal < 0 ! (" . round($totaalAantal, 4) . ") <br>";
        $txtMail = "Short-positie na transactie.";
        $txtSilent = '2';
      }
      else
      {
        $txt = "Na verkoop: " . round($totaalAantal, 4) . "<br>";
        $txtSilent = '0';
      }
      if($bewaarder['OrderuitvoerBewaarder']==1)
        $txt.="bij bewaarder (".$this->data['depotbank'].") (Totale Portefeuille aantal na transacties ".round($totaalAantalPortefeuille,4).")";
    }
    elseif($this->data['transactieSoort'] == "AS" || $this->data['transactieSoort'] == "VS" || $this->data['transactieSoort'] == "AO" || $this->data['transactieSoort'] == "VO")
    {
      $aantalAanwezig = fondsAantalOpdatum($portefeuille, $this->data['Fonds'], $this->data['rapportageDatum']);
      if($this->data['transactieSoort'] == "AS"  && ($aantalAanwezig['totaalAantal']>=0 || $transactieAantal > abs($aantalAanwezig['totaalAantal'])))
      {
        $txt = "transactieSoort A/S. Huidig aantal (" . round($aantalAanwezig['totaalAantal'], 4) . ") <br>";
        $txtSilent = '2';
      }
      if($this->data['transactieSoort'] == "VS"  && ($aantalAanwezig['totaalAantal']<=0 || $transactieAantal > $aantalAanwezig['totaalAantal']))
      {
        $txt = "transactieSoort V/S. Huidig aantal (" . round($aantalAanwezig['totaalAantal'], 4) . ") <br>";
        $txtSilent = '2';
      }
      if($this->data['transactieSoort'] == "AO" && $aantalAanwezig['totaalAantal']<0)
      {
        $txt = "transactieSoort A/O. Huidig aantal (" . round($aantalAanwezig['totaalAantal'], 4). ") < 0. <br>";
        $txtSilent = '2';
      }
      if($this->data['transactieSoort'] == "VO" && $aantalAanwezig['totaalAantal']>0)
      {
        $txt = "transactieSoort V/O. Huidig aantal (" . round($aantalAanwezig['totaalAantal'], 4). ") > 0.<br>";
        $txtSilent = '2';
      }
    }
    else
    {
      $txt = "Geen Verkoop transactie."; // ".$this->data['transactieSoort'];
      $txtMail = "Geen short-positie na transactie.";
    }

    $this->checksKort['short'] = $txtSilent;
    $this->mailTxt['short'] = $txtMail;

    if ($this->data['silent'] == true)
      return $txtSilent;
    else
      return $txt;
  }

  function LiquiditeitenCheck()
  {
    global $__appvar;
    $db = new DB();
    $newSaldo=0;
    $verkoopSaldo=0;
    
    /** set waarden **/
    $portefeuille = $this->data['portefeuille'];
    $eigenOrderid = $this->data['eigenOrderid'];
    $transactieAantal = $this->data['transactieAantal'];
    $rekeningValuta = $this->data['valuta'];
    $rapportageDatum = $this->data['rapportageDatum'];

    $txt = "";
    $txtMail = '';
    $txtSilent = "0";

    if(isset($this->data['rekening']) && $this->data['rekening'] <> '')
    {
      $query = "SELECT Rekeningen.Rekening, Rekeningen.Valuta, Rekeningen.Depotbank FROM Rekeningen WHERE rekening='" . $this->data['rekening'] . "'";
    }
    elseif ($this->bulk == true)
    {
      $query = "SELECT TijdelijkeBulkOrdersV2.Rekening,Rekeningen.Valuta, Rekeningen.Depotbank  FROM TijdelijkeBulkOrdersV2
      JOIN Rekeningen ON Rekeningen.Rekening=TijdelijkeBulkOrdersV2.Rekening 
      WHERE TijdelijkeBulkOrdersV2.id = '" . $eigenOrderid . "' AND TijdelijkeBulkOrdersV2.Portefeuille = '" . $portefeuille . "' " . $this->bulkIdFilter;
    }
    else
    {
      $query = "SELECT OrderRegelsV2.Rekening, Rekeningen.Valuta, Rekeningen.Depotbank  FROM OrderRegelsV2
      JOIN Rekeningen ON Rekeningen.Rekening=OrderRegelsV2.Rekening 
      WHERE OrderRegelsV2.orderId='" . $eigenOrderid . "' AND OrderRegelsV2.Portefeuille = '" . $portefeuille . "'";
    }
    $db->SQL($query);
    $db->Query();
    $rekening = $db->lookupRecord();
  
    $query="SELECT Vermogensbeheerders.OrderuitvoerBewaarder
            FROM Portefeuilles JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder= Vermogensbeheerders.Vermogensbeheerder
            WHERE Portefeuilles.consolidatie=0 AND Portefeuilles.portefeuille='$portefeuille'";
    $db->SQL($query);
    $bewaarder=$db->lookupRecord();
    if($bewaarder['OrderuitvoerBewaarder']==1)
    {
      $query = "SELECT Rekening,
      if(Rekeningen.Valuta='EUR',-1,Valutas.Afdrukvolgorde) as volgordeLiq ,
      if(Rekeningen.Depotbank='".$rekening['Depotbank']."',0,1) AS volgordeDepot,
      Rekeningen.Valuta
      FROM Rekeningen JOIN Valutas ON Rekeningen.Valuta=Valutas.Valuta
      WHERE
      Rekeningen.consolidatie=0 AND Rekeningen.Inactief=0 AND Rekeningen.Memoriaal=0 AND Rekeningen.Deposito=0 AND Rekeningen.Portefeuille = '$portefeuille'
      ORDER BY volgordeDepot,volgordeLiq, Rekeningen.Afdrukvolgorde limit 1";
      $db->SQL($query);
      $rekening=$db->lookupRecord();
      $rekeningValuta = $rekening['Valuta'];
    }
    
    if ($rekeningValuta == '')
      $rekeningValuta = $rekening['Valuta'];
    $rekening = $rekening['Rekening'];

    if($rekening=='')
    {
      $tmp=$this->getPortefeuilleOpties($portefeuille,'','',true);
      $rekening=$tmp['Rekening'];
    }
    
    if ($this->bulk == true)
      $koers=$this->getKoersen($this->data['Fonds'],$eigenOrderid);
    else
      $koers=$this->getKoersen($this->data['Fonds']);

    /** wanneer transactie soort is een aankoop **/
    if ($this->data['transactieSoort'] == "A" || $this->data['transactieSoort'] == "AO" || $this->data['transactieSoort'] == "AS")
    {
      if ($this->data['tijdelijkeTabel'] == 0)
      {
        $fondswaarden = berekenPortefeuilleWaarde($portefeuille, $rapportageDatum);
        vulTijdelijkeTabel($fondswaarden, $portefeuille, $rapportageDatum);
        $this->data['tijdelijkeTabel'] = 1;
      }
    
      // haal actuele stand rekening op.
      $_beginJaar = substr($rapportageDatum, 0, 4) . "-01-01";
      $query = "SELECT SUM(Bedrag) as totaal FROM Rekeningmutaties
    			  WHERE boekdatum >= '" . $_beginJaar . "' AND
    			  boekdatum <= '" . $rapportageDatum . "'  AND
    		      Rekening = '" . $rekening . "'
    			  Group By Rekeningmutaties.Rekening";
      $db->SQL($query);
      $rekeningSaldo = $db->lookupRecord();

      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal " .
            "FROM TijdelijkeRapportage WHERE " .
            " rapportageDatum ='" . $rapportageDatum . "' AND " .
            " portefeuille = '" . $portefeuille . "' AND type='rekening' "
            . $__appvar['TijdelijkeRapportageMaakUniek'];
      $db->SQL($query); 
      $db->Query();
      $totaalWaarde = $db->nextRecord();

      /** wanneer er een koersLimiet is ingevoerd deze gebruiken **/
      if ($this->data['koersLimiet'] != 0) //Indien Limiet dan deze koers gebruiken
      {
        $aankoopBedrag = $transactieAantal * $this->data['koersLimiet'] * $koers['fondseenheid'];
      }
      else //Koersen ophalen.
      {
        $aankoopBedrag = $transactieAantal * $koers['fondsKoers'] * $koers['fondseenheid'];
      }
      
      /** Rekening valutakoers ophalen **/
      $query = "SELECT Koers,Datum FROM Valutakoersen
					WHERE Valuta = '" . $rekeningValuta . "' AND
					Datum <= '" . $this->data['rapportageDatum'] . "'
					ORDER BY Datum DESC LIMIT 1";
      $db->SQL($query);
      $RekeningValutaKoers = $db->lookupRecord();

      //echo $this->data['Fonds']."->bedrag=".(( ( $rekeningSaldo['totaal'] * $RekeningValutaKoers['Koers'] ) - ( $aankoopBedrag * $FondsValutaKoers['Koers'] )) / $RekeningValutaKoers['Koers'])."<br>\n";
      if($this->data['transactieAantal']==0 && $this->data['bedrag'] <> 0)
        $newSaldo = ( ( $rekeningSaldo['totaal'] * $RekeningValutaKoers['Koers'] ) - $this->data['bedrag'] * $koers['valutaKoers']);
      else
        $newSaldo = ( ( $rekeningSaldo['totaal'] * $RekeningValutaKoers['Koers'] ) - ( $aankoopBedrag * $koers['valutaKoers'] )) / $RekeningValutaKoers['Koers'];

      if($aankoopBedrag == 0 && $this->data['bedrag'] == 0)
      {
        $txt .= "Geschat orderbedrag is " . number_format($aankoopBedrag, 2, ",", ".") . "? <br> ";
        $txtSilent = '2';
      }
      if ($newSaldo < 0) //Salso na te plannen aankoop (Nog zonder overige lopende transacties)
      {
        $txt .= "Saldo na huidige order $rekening " . number_format($newSaldo, 2, ",", ".") . " <br> ";
        $txtSilent = '2';
      }
          // Overige lopende transacties op huidige rekening.
      $aantalOrders=array();
      if ($this->bulk == true)
      {
        $query = "SELECT id as orderid, aantal, Fonds, transactieSoort, koersLimiet
		  		FROM TijdelijkeBulkOrdersV2
			  	WHERE
		  	  Portefeuille = '" . $portefeuille . "'
			  	AND id <> '" . $eigenOrderid . "' ".$this->bulkIdFilter;

        $db->SQL($query); //echo " $newSaldo <br>\n $query <br>\n";
        $db->Query();
        while ($overigeOrders = $db->nextRecord())
        {
          $koers=$this->getKoersen($overigeOrders['Fonds'],$overigeOrders['orderid']);
          if ($overigeOrders['koersLimiet'] != 0)
          {
            $overigeFondsKoers['Koers'] = $overigeOrders['koersLimiet'];
          }
          if ($overigeOrders['transactieSoort'] == "A" || $overigeOrders['transactieSoort'] == "AO") //aankopen aftrekken van Saldo
          {
            $aantalOrders['b']['a']++;
            $newSaldo -= ($overigeOrders['aantal'] * $koers['fondsKoers'] * $koers['fondseenheid'] * $koers['valutaKoers']) / $RekeningValutaKoers['Koers'];
            //echo "aankoop ".$overigeOrders['Fonds']." ".(($overigeOrders['aantal'] * $overigeFondsKoers['Koers'] * $overigeFondsKoers['Fondseenheid'] * $overigeFondsValutaKoers['Koers']) / $RekeningValutaKoers['Koers'])."<br>\n";
          }
          if ($overigeOrders['transactieSoort'] == "V" || $overigeOrders['transactieSoort'] == "VS") //Verkopen optellen bij saldo uitgezet
          {
            $aantalOrders['b']['v']++;
            $verkoopSaldo += ($overigeOrders['aantal'] * $koers['fondsKoers'] * $koers['fondseenheid']  * $koers['valutaKoers']) / $RekeningValutaKoers['Koers'];
          }
        }
        //$eigenOrderid = -1;
      }
      
      /*
      $query = "SELECT OrderRegelsV2.aantal, OrdersV2.transactieSoort, OrdersV2.Fonds, OrdersV2.koersLimiet FROM OrderRegelsV2, OrdersV2
 					WHERE OrderRegelsV2.orderid = OrdersV2.id AND
 					((OrderRegelsV2.orderregelStatus < 4 AND (OrdersV2.transactieSoort = 'A' OR OrdersV2.transactieSoort = 'AO')) OR
 					(OrderRegelsV2.orderregelStatus = 2 OR OrderRegelsV2.orderregelStatus = 3) AND (OrdersV2.transactieSoort = 'V' OR OrdersV2.transactieSoort = 'VS'))  AND
 					OrderRegelsV2.Portefeuille = '" . $portefeuille . "'
 					AND OrdersV2.id <> '" . $eigenOrderid . "' ";*/
      $dateFilter=$this->getOrderregelDateFilter($eigenOrderid,$portefeuille);
      $query = "SELECT OrderRegelsV2.aantal,OrderRegelsV2.bedrag, OrdersV2.transactieSoort, OrdersV2.Fonds, OrdersV2.koersLimiet FROM OrderRegelsV2, OrdersV2
 					WHERE OrderRegelsV2.orderid = OrdersV2.id AND
 					(OrderRegelsV2.orderregelStatus < 4 AND (OrdersV2.transactieSoort IN('A','AO','V','VS')))  AND
 					OrderRegelsV2.Portefeuille = '" . $portefeuille . "'
 					AND OrdersV2.id <> '" . $eigenOrderid . "' $dateFilter";

      $db->SQL($query); //echo " $newSaldo <br>\n $query <br>\n";   OrderRegelsV2.Valuta = '" . $rekeningValuta . "' AND
      $db->Query();
      $db2 = new DB();
      while ($overigeOrders = $db->nextRecord())
      {
        $query = "SELECT Fondskoersen.Koers, Fondskoersen.Datum, Fondsen.Valuta, Fondsen.Fondseenheid  FROM Fondskoersen ,Fondsen
 	  			    WHERE Fondskoersen.Fonds = Fondsen.Fonds AND
 	  			    Fondsen.Fonds = '" . $overigeOrders['Fonds'] . "' AND
 	  			    Fondskoersen.Datum <= '" . $this->data['rapportageDatum'] . "'
 	  			    ORDER BY Datum DESC LIMIT 1";
        $db2->SQL($query);
        $overigeFondsKoers = $db2->lookupRecord();
        $query = "SELECT Koers,Datum FROM Valutakoersen
					  WHERE Valuta = '" . $overigeFondsKoers['Valuta'] . "' AND
					  Datum <= '" . $this->data['rapportageDatum'] . "'
					  ORDER BY Datum ASC LIMIT 1";
        $db2->SQL($query);
        $overigeFondsValutaKoers = $db2->lookupRecord();

        $koers=$this->getKoersen($overigeOrders['Fonds']);
        if ($overigeOrders['koersLimiet'] != 0)
        {
          $overigeFondsKoers['Koers'] = $overigeOrders['koersLimiet'];
        }
        if ($overigeOrders['transactieSoort'] == "A" || $overigeOrders['transactieSoort'] == "AO") //aankopen aftrekken van Saldo
        {
          $aantalOrders['o']['a']++;
          if($overigeOrders['aantal']==0 && $overigeOrders['bedrag']<>0)
             $newSaldo -= $overigeOrders['bedrag']* $koers['valutaKoers'];
           else
             $newSaldo -= ($overigeOrders['aantal'] * $koers['fondsKoers'] * $koers['fondseenheid'] * $koers['valutaKoers']) / $RekeningValutaKoers['Koers'];
        }
        if ($overigeOrders['transactieSoort'] == "V" || $overigeOrders['transactieSoort'] == "VS") //Verkopen optellen bij saldo
        {
          $aantalOrders['o']['v']++;
          if($overigeOrders['aantal']==0 && $overigeOrders['bedrag']<>0)
            $verkoopSaldo += $overigeOrders['bedrag']* $koers['valutaKoers'];
          else
            $verkoopSaldo += ($overigeOrders['aantal'] * $koers['fondsKoers'] * $koers['fondseenheid'] * $koers['valutaKoers']) / $RekeningValutaKoers['Koers'];
        }
      }
      $aantalTxt=' (';
      foreach($aantalOrders as $bron=>$typen)
        foreach($typen as $type=>$aantal)
          $aantalTxt.="$bron$type=$aantal,";
      $aantalTxt.=')';
      //echo " $newSaldo <br>\n<br>\n"; 
      $txt .= "Saldo liquiditeiten $rekening na orders na aankopen  ".number_format($newSaldo, 2, ",", ".").", Totaal liquiditeiten EUR ".number_format($totaalWaarde['totaal'], 2, ",", ".").", Liquiditeiten $rekening na verkopen ".number_format($newSaldo+$verkoopSaldo, 2, ",", ".")."$aantalTxt<br>";

      if($this->data['inclVerkopen']==1 && $newSaldo+$verkoopSaldo > 0)
      {
        $txtMail="Liquiditeiten positief saldo na transactie.";
        //if ($txtSilent == '2')
        //  $txtSilent = '1';
        //else
          $txtSilent = '0';
      }
      elseif ($newSaldo < 0 ) //Salso na te plannen aankoop (Nog zonder overige lopende transacties)
      {
        $txtMail="Liquiditeiten negatief saldo na transactie.";
        if($newSaldo+$verkoopSaldo > 0)
          $txtSilent = '1';
        else
          $txtSilent = '2';
      }
      else
      {
        $txtMail="Liquiditeiten positief saldo na transactie.";
        if ($txtSilent == '2')
          $txtSilent = '1';
        else
          $txtSilent = '0';
      }
    }    
    else
    {
      $txtSilent = '0';
      $txt = "Geen aankoop order.";
    }
    $this->checksKort['liqu'] = $txtSilent;
    $this->mailTxt['liqu'] = $txtMail;

    if ($this->data['silent'] == true)
      return $txtSilent;
    else
      return $txt;
  }

  function getFondsKoers($fonds, $datum)
  {
    $db = new DB();
    $query = "SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
    $db->SQL($query);
    $koers = $db->lookupRecord();
    return $koers['Koers'];
  }

  function ZorgplichtCheck()
  {
    global $__appvar, $USR;
    
    $txtSilent = null;


    if($this->data['fonds']=='')
    {
      $this->checksKort['zorg'] = 0;
      if ($this->data['silent'] == true)
      {
        return 0;
      }
      else
      {
        return "Niet mogelijk ivm eigen fonds";
      }
    }
    $db = new DB();
    $portefeuille = $this->data['portefeuille'];
    $eigenOrderid = $this->data['eigenOrderid'];
    $rapportageDatum = substr($this->data['rapportageDatum'], 0, 10);

    if ($this->data['tijdelijkeTabel'] == 0)
    {
      $fondswaarden = berekenPortefeuilleWaarde($portefeuille, $rapportageDatum);
      vulTijdelijkeTabel($fondswaarden, $portefeuille, $rapportageDatum);
      $this->data['tijdelijkeTabel'] = 1;
    }
  /*
    $preprocessorError='';
    if($this->data['vermogensBeheerder']=='GRO')
    {
      ob_start(); // Start output buffering
      runPreProcessor($this->data['portefeuille']);
      $preprocessorError = ob_get_contents(); // Store buffer in variable
      ob_end_clean(); // End buffering and clean up
  
    }
    */
    $orders = array();
    $orders[] = array('aantal' => $this->data['transactieAantal'],'bedrag' => $this->data['bedrag'], 'transactieSoort' => $this->data['transactieSoort'], 'orderid' => $eigenOrderid, 'Fonds' => $this->data['Fonds'], 'koersLimiet' => $this->data['koersLimiet']);
    if ($this->bulk == true)
    {
      $query = "SELECT id as orderid, aantal, Fonds, transactieSoort, koersLimiet
		  		FROM TijdelijkeBulkOrdersV2
			  	WHERE
			  	 Portefeuille = '" . $portefeuille . "'
			  	AND id <> '" . $eigenOrderid . "' ".$this->bulkIdFilter; //		  AND		Fonds = '" . $this->data['Fonds'] . "'

      $db->SQL($query);
      $db->Query();
      while ($overigeOrders = $db->nextRecord())
      {
        $overigeOrders['bulk']=true;
        $orders[] = $overigeOrders;
      }
      $eigenOrderid = -1;
    }

    $dateFilter=$this->getOrderregelDateFilter($eigenOrderid,$portefeuille);
    $query = "SELECT OrderRegelsV2.aantal,OrderRegelsV2.bedrag, OrdersV2.transactieSoort, OrdersV2.id as orderid, OrdersV2.Fonds, OrdersV2.koersLimiet FROM OrderRegelsV2, OrdersV2
 							WHERE OrderRegelsV2.orderid = OrdersV2.id AND OrderRegelsV2.orderregelStatus < 4 AND OrderRegelsV2.Portefeuille = '" . $portefeuille . "' AND OrdersV2.id <> '" . $eigenOrderid . "' $dateFilter";
    $db->SQL($query);
    $db->Query();
    while ($overigeOrders = $db->nextRecord())
      $orders[] = $overigeOrders;


    $query = "SELECT Portefeuille,Vermogensbeheerder FROM Portefeuilles WHERE Portefeuilles.consolidatie=0 AND Portefeuille='$portefeuille' ";
    $db->SQL($query);
    $pdata = $db->lookupRecord();

    $query="SELECT id FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.type = 'Rekening' AND  TijdelijkeRapportage.rapportageDatum ='" . $rapportageDatum . "' AND " .
              " TijdelijkeRapportage.portefeuille = '$portefeuille' " . $__appvar['TijdelijkeRapportageMaakUniek'] . "  ORDER BY valutaVolgorde LIMIT 1";
    $db->SQL($query);
    $rekening = $db->lookupRecord();
/*
    $query="SELECT sum(actuelePortefeuilleWaardeEuro) as waarde FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.type = 'Rekening' AND  TijdelijkeRapportage.rapportageDatum ='" . $rapportageDatum . "' AND " .
      " TijdelijkeRapportage.portefeuille = '$portefeuille' " . $__appvar['TijdelijkeRapportageMaakUniek'] . "";
    $db->SQL($query);
    $liqBegin = $db->lookupRecord();
*/
    $mutatieWaarde=0;
    $insert=false;
    foreach ($orders as $order)
    {
      $aantal = floatval($order['aantal']);
      if (in_array($order['transactieSoort'], array('V', 'VO', 'VS')))
        $aantal = $aantal * -1;

      if($order['bulk']==true)
        $koers=$this->getKoersen($order['Fonds'],$order['orderid']);
      else
        $koers=$this->getKoersen($order['Fonds']);

      if($this->data['transactieAantal']==0 && $this->data['bedrag']<> 0)
      {
        if (in_array($order['transactieSoort'], array('V', 'VO', 'VS')))
          $aankoopWaarde = $this->data['bedrag']*-1* $koers['valutaKoers'];
        else
          $aankoopWaarde = $this->data['bedrag']* $koers['valutaKoers'];
      }
      else
        $aankoopWaarde = $aantal * $koers['fondsKoers'] * $koers['fondseenheid'] * $koers['valutaKoers'];
      //echo "$aankoopWaarde = ".$aantal." * $valutaKoers * $fondsKoers * ".$fonds['FondsEenheid']."<br>\n ";

      $query = "SELECT id FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.type = 'fondsen' AND " .
              " TijdelijkeRapportage.rapportageDatum ='" . $rapportageDatum . "' AND " .
              " TijdelijkeRapportage.portefeuille = '$portefeuille'  AND " .
              " TijdelijkeRapportage.Fonds = '" . $order['Fonds'] . "' " . $__appvar['TijdelijkeRapportageMaakUniek'];
      $db->SQL($query);
      $db->Query();
      if ($db->records() > 0)
      {
        $query = "UPDATE TijdelijkeRapportage SET actuelePortefeuilleWaardeEuro=actuelePortefeuilleWaardeEuro+$aankoopWaarde, totaalAantal=(totaalAantal + $aantal)
					          WHERE TijdelijkeRapportage.type = 'fondsen' AND " .
          " TijdelijkeRapportage.rapportageDatum ='" . $rapportageDatum . "' AND " .
          " TijdelijkeRapportage.portefeuille = '$portefeuille'  AND " .
          " TijdelijkeRapportage.Fonds = '" . $order['Fonds'] . "' " . $__appvar['TijdelijkeRapportageMaakUniek'];
      }
      else
      {
        $query = "SELECT Fonds,Beleggingscategorie FROM BeleggingscategoriePerFonds WHERE Vermogensbeheerder='" . $pdata['Vermogensbeheerder'] . "' AND Fonds = '" . mysql_real_escape_string($order['Fonds']) . "' ";
        $db->SQL($query);
        $Beleggingscategorie = $db->lookupRecord();
        $Beleggingscategorie = $Beleggingscategorie['Beleggingscategorie'];

        $query = "INSERT INTO TijdelijkeRapportage SET totaalAantal='$aantal', actuelePortefeuilleWaardeEuro='$aankoopWaarde', add_user='$USR', TijdelijkeRapportage.sessionId = '" . $_SESSION['usersession']['sessionId'] . "',
                    TijdelijkeRapportage.type = 'Fondsen', Beleggingscategorie='$Beleggingscategorie',
                    rapportageDatum ='" . $rapportageDatum . "',
                    portefeuille = '$portefeuille',
                    Fonds='" . mysql_real_escape_string($order['Fonds']) . "' ";
        $insert=true;
        $debug=$query;
      }
      $db->SQL($query);
      $db->Query();
      $query = "UPDATE TijdelijkeRapportage SET actuelePortefeuilleWaardeEuro=actuelePortefeuilleWaardeEuro-$aankoopWaarde WHERE TijdelijkeRapportage.id ='".$rekening['id']."'";
      $db->SQL($query);
      $db->Query();
      $mutatieWaarde+=$aankoopWaarde;
    }
  

    if($this->data['vermogensBeheerder']=='GRO')
    {
     
      ob_start(); // Start output buffering
      runPreProcessor($this->data['portefeuille']);
      $preprocessorError = ob_get_contents(); // Store buffer in variable
      ob_end_clean(); // End buffering and clean up
    }
/*
    $query="SELECT sum(actuelePortefeuilleWaardeEuro) as waarde FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.type = 'Rekening' AND  TijdelijkeRapportage.rapportageDatum ='" . $rapportageDatum . "' AND " .
      " TijdelijkeRapportage.portefeuille = '$portefeuille' " . $__appvar['TijdelijkeRapportageMaakUniek'] . "";
    $db->SQL($query);
    $liqEind = $db->lookupRecord();
*/
    $zorgplicht = new Zorgplichtcontrole();

    $zpwaarde = $zorgplicht->zorgplichtMeting($pdata, $rapportageDatum);

    if ($zpwaarde['voldoet'] == 'Nee')
    {
      $txtSilent = 1;
      $txtMail="Overschrijding risicoprofiel na transactie.";
    }
    else
    {
      $txtMail="Geen overschrijding risicoprofiel na transactie.";
    }

//Mutatiewaarde: ".round($mutatieWaarde,2)."<br>\nLiq mutatie:".round($liqEind['waarde'])."->".round($liqEind['waarde'])." <br>\n
    $txt = "Conclusie: " . $zpwaarde['zorgMeting'] . "<br>\n";// (na order EUR $aankoopWaarde)
    $txt .=$zpwaarde['zorgMetingReden'];
  
    if($preprocessorError<>'')
    {
      $txtSilent = 1;
      $txt="Fout bij koppelen opties. $preprocessorError";
    }

    $this->mailTxt['zorg'] = $txtMail;
    $this->checksKort['zorg'] = $txtSilent;
    if ($this->data['silent'] == true)
      return $txtSilent;
    else
      return $txt;
  }
  
  function GrootteCheck()
  {
    global $__appvar;
    $order= $this->data;

    $portefeuille = $this->data['portefeuille'];
    $eigenOrderid = $this->data['eigenOrderid'];
    $transactieAantal = $this->data['transactieAantal'];
    $rekeningValuta = $this->data['valuta'];
    $rapportageDatum = $this->data['rapportageDatum'];

    $db = new db();
    $query="SELECT Vermogensbeheerders.orderMaxBedrag, Vermogensbeheerders.orderMaxPercentage,Vermogensbeheerders.orderMaxPercentagePositie,Vermogensbeheerders.OrderuitvoerBewaarder 
FROM Portefeuilles JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder= Vermogensbeheerders.Vermogensbeheerder 
WHERE Portefeuilles.consolidatie=0 AND Portefeuilles.portefeuille='$portefeuille'";
    $db->SQL($query);
    $totaalcheck=$db->lookupRecord();

    /*
    $query="SELECT Vermogensbeheerders.OrderuitvoerBewaarder 
FROM Portefeuilles JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder= Vermogensbeheerders.Vermogensbeheerder 
WHERE Portefeuilles.consolidatie=0 AND Portefeuilles.portefeuille='$portefeuille'";
    $db->SQL($query);
    $bewaarder=$db->lookupRecord();
    if($bewaarder['OrderuitvoerBewaarder']==1)
    {
      $fondswaarden=array();
      $fondswaardenTmp = berekenPortefeuilleWaarde($portefeuille, $rapportageDatum,false,'EUR',$rapportageDatum,2,true);
      foreach($fondswaardenTmp as $regel)
      {
        if($regel['Bewaarder']==$this->data['depotbank'])
        {
          $fondswaarden[]=$regel;
        }
      }
    }
    else
    {
    */
      $fondswaarden = berekenPortefeuilleWaarde($portefeuille, $rapportageDatum,false,'EUR',$rapportageDatum);
    //}
    $totaalWaarde=0;
    $positieWaarde=0;
    foreach($fondswaarden as $waarde)
    {
      $totaalWaarde += $waarde['actuelePortefeuilleWaardeEuro']; 
      if($waarde['fonds']==$this->data['Fonds'])
        $positieWaarde += $waarde['actuelePortefeuilleWaardeEuro']; 
      
    }
 

    if($order['bulk']==true)
      $koers=$this->getKoersen($this->data['Fonds'],$eigenOrderid);
    else
      $koers=$this->getKoersen($this->data['Fonds']);

      /** wanneer er een koersLimiet is ingevoerd deze gebruiken **/
      if($this->data['transactieAantal']==0 && $this->data['bedrag']<> 0)
        $bedrag = abs($this->data['bedrag'] * $koers['valutaKoers']);
      elseif ($this->data['koersLimiet'] != 0) //Indien Limiet dan deze koers gebruiken
        $bedrag = abs($transactieAantal * $this->data['koersLimiet'] * $koers['fondseenheid'] * $koers['valutaKoers']);
      else //Koersen ophalen.
        $bedrag = abs($transactieAantal  * $koers['fondsKoers'] * $koers['fondseenheid'] * $koers['valutaKoers']);
    if (in_array($order['transactieSoort'], array('V', 'VO', 'VS')))
      $nieuwePositieWaarde = $positieWaarde-$bedrag;
    else
      $nieuwePositieWaarde = $positieWaarde+$bedrag;     
  
  
    $percentage=$bedrag/$totaalWaarde*100;
    $percentagePositie=$nieuwePositieWaarde/$totaalWaarde*100;
  
    $txtSilent=0;
    $txt='';
    $txtMail='';
    if ($bedrag > $totaalcheck['orderMaxBedrag'])
    {
      $txt .= "Orderbedrag EUR ".number_format($bedrag,2,",",".")." is meer dan EUR ".number_format($totaalcheck['orderMaxBedrag'],2,",",".").'. ';
      $txtMail.="Orderbedrag > EUR ".number_format($totaalcheck['orderMaxBedrag'],2,",",".").". ";
      $txtSilent = '2';
    }
    
    if ($percentage > $totaalcheck['orderMaxPercentage'])
    {
      $txt .= "Orderaandeel ".number_format($percentage, 1,",",".")."% is meer dan ".number_format( $totaalcheck['orderMaxPercentage'],1,",",".").'%. ';
      $txtMail.="Omvang transactie > ".number_format($totaalcheck['orderMaxPercentage'], 1,",",".")."%. ";
      $txtSilent = '2';
    }
    if ($percentagePositie > $totaalcheck['orderMaxPercentagePositie'])
    {
      $txt .= "Nieuwe positie ".number_format($percentagePositie, 1,",",".")."% is meer dan ".number_format( $totaalcheck['orderMaxPercentagePositie'],1,",",".").'%.';
      $txtMail .="Fonds positie > ".number_format( $totaalcheck['orderMaxPercentagePositie'],1,",",".")."%. ";
      $txtSilent = '2';
    }
    
    if($txtSilent==0)
    {
      $txt = "Orderbedrag EUR ".number_format($bedrag, 2, ",", ".") . " ligt onder ".number_format($totaalcheck['orderMaxBedrag'],2,",",".").".<br>\n";
      $txt .= 'Orderaandeel '.number_format($percentage, 1, ",", ".")."% ligt onder ".number_format($totaalcheck['orderMaxPercentage'], 1,",",".")."%.<br>\n";
      $txt .= "Nieuwe positie ".number_format($percentagePositie, 1,",",".")."% ligt onder ".number_format( $totaalcheck['orderMaxPercentagePositie'],1,",",".")."%.<br>\n";
      $txtSilent = '0';
      $txtMail.="Orderbedrag overschrijdt maximum niet. ";
      $txtMail.="Omvang transactie < ".number_format($totaalcheck['orderMaxPercentage'], 1,",",".")."%. ";
      $txtMail.="Fonds positie < ".number_format( $totaalcheck['orderMaxPercentagePositie'],1,",",".")."%. ";
    }
    else
    {
      $txtMail="Orderbedrag overschrijdt maximum. $txtMail";
    }
    if($bewaarder['OrderuitvoerBewaarder']==1)
    {
      $txt.="(Berekenend met bewaarder ".$this->data['depotbank'].")";
    }

    $this->checksKort['groot'] = $txtSilent;
    $this->mailTxt['groot'] = $txtMail;

    if ($this->data['silent'] == true)
      return $txtSilent;
    else
      return $txt;  
      
  }

  function RisicoCheck()
  {
    global $__appvar;

    $db = new DB();
    $db2 = new DB();
    $portefeuille = $this->data['portefeuille'];
    $eigenOrderid = $this->data['eigenOrderid'];
    $transactieAantal = $this->data['transactieAantal'];
    $rekeningValuta = $this->data['valuta'];
    $rapportageDatum = $this->data['rapportageDatum'];
    $risicoTotaal=0;

    if($this->data['fonds']=='')
    {
      $this->checksKort['risi'] = 0;
      if ($this->data['silent'] == true)
      {
        return 0;
      }
      else
      {
        return "Niet mogelijk ivm eigen fonds";
      }
    }

    if ($this->data['tijdelijkeTabel'] == 0)
    {
      $fondswaarden = berekenPortefeuilleWaarde($portefeuille, $rapportageDatum);
      vulTijdelijkeTabel($fondswaarden, $portefeuille, $rapportageDatum);
      $this->data['tijdelijkeTabel'] = 1;
    }
    $DB3 = new DB();
    // haal totaalwaarde op om % te berekenen
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal " .
            "FROM TijdelijkeRapportage WHERE " .
            " rapportageDatum ='" . $rapportageDatum . "' AND " .
            " portefeuille = '" . $portefeuille . "' "
            . $__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query, __FILE__, __LINE__);
    $DB3->SQL($query);
    $DB3->Query();
    $totaalWaarde = $DB3->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];

    $query = "SELECT Beleggingscategorien.Omschrijving, " .
            " BeleggingscategoriePerFonds.RisicoPercentageFonds, " .
            " Valutas.Omschrijving AS ValutaOmschrijving, " .
            " Fondsen.Fonds AS Fonds, " .
            " TijdelijkeRapportage.valuta, " .
            " TijdelijkeRapportage.actueleValuta, " .
            " TijdelijkeRapportage.beleggingscategorie, " .
            " SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, " .
            " SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel " .
            " FROM (TijdelijkeRapportage, Portefeuilles, BeleggingscategoriePerFonds)  " .
            " LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)  " .
            " LEFT JOIN Fondsen on (TijdelijkeRapportage.Fonds = Fondsen.Fonds)  " .
            " LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie) " .
            " WHERE " .
            " TijdelijkeRapportage.portefeuille = '" . $portefeuille . "' AND " .
            " Portefeuilles.Portefeuille = TijdelijkeRapportage.portefeuille AND " .
            " Portefeuilles.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder AND " .
            " BeleggingscategoriePerFonds.Fonds = TijdelijkeRapportage.fonds  AND " .
            " TijdelijkeRapportage.type = 'fondsen' AND " .
            " TijdelijkeRapportage.rapportageDatum = '" . $rapportageDatum . "'"
            . $__appvar['TijdelijkeRapportageMaakUniek'] .
            " GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.fonds, TijdelijkeRapportage.valuta " .
            " ORDER BY Beleggingscategorien.Afdrukvolgorde asc, Valutas.Afdrukvolgorde asc";
    debugSpecial($query, __FILE__, __LINE__);
    $DB3 = new DB();
    $DB3->SQL($query);
    $DB3->Query();
//echo $query;
    while ($categorien = $DB3->NextRecord())
    {
      $risico = $categorien['RisicoPercentageFonds'];
      $risicoBedrag = (ABS($categorien['subtotaalactueel']) / 100) * $risico;
      $risicoTotaal += $risicoBedrag;
    }
// huidige orders ophalen

    if ($this->bulk == true)
    {
      $query = "SELECT TijdelijkeBulkOrdersV2.id as orderid, aantal, TijdelijkeBulkOrdersV2.Fonds, transactieSoort, koersLimiet,
      							BeleggingscategoriePerFonds.RisicoPercentageFonds,
							Fondsen.valuta,
							Fondsen.Fonds as fondskort
TijdelijkeBulkOrdersV2 
INNER JOIN Fondsen ON TijdelijkeBulkOrdersV2.Fonds = Fondsen.Fonds
INNER JOIN Portefeuilles ON TijdelijkeBulkOrdersV2.portefeuille = Portefeuilles.Portefeuille 
INNER JOIN BeleggingscategoriePerFonds ON BeleggingscategoriePerFonds.fonds = Fondsen.fonds AND Portefeuilles.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder
			  	WHERE 
		  		TijdelijkeBulkOrdersV2.Fonds = '" . $this->data['Fonds'] . "'
			  	AND TijdelijkeBulkOrdersV2.Portefeuille = '" . $portefeuille . "'
			  	AND TijdelijkeBulkOrdersV2.id <> '" . $eigenOrderid . "' ".$this->bulkIdFilter;

      $DB3->SQL($query); // echo " $query <br>\n";
      $DB3->Query();
      while ($overigeOrders = $DB3->nextRecord())
      {
        $overigeOrders['bulk']=true;
        $orderdata[] = $overigeOrders;
      }

      $eigenOrderid = -1;
    }

    $dateFilter=$this->getOrderregelDateFilter($eigenOrderid,$portefeuille);
    $query = "SELECT OrderRegelsV2.aantal,
							OrdersV2.transactieSoort,
							OrdersV2.orderid,
							OrdersV2.Fonds,
							OrdersV2.koersLimiet,
							BeleggingscategoriePerFonds.RisicoPercentageFonds,
							Fondsen.valuta,
							Fondsen.fonds as fondskort
FROM 	OrderRegelsV2
INNER JOIN OrdersV2 ON OrderRegelsV2.orderid = OrdersV2.id
INNER JOIN Fondsen ON 	OrdersV2.Fonds = Fondsen.Fonds 
INNER JOIN Portefeuilles ON OrderRegels.portefeuille = Portefeuilles.Portefeuille 
INNER JOIN BeleggingscategoriePerFonds ON BeleggingscategoriePerFonds.fonds = Fondsen.fonds AND Portefeuilles.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder
					WHERE 
 							OrderRegelsV2.status < 4 AND
 							OrderRegelsV2.Portefeuille =  '" . $portefeuille . "' $dateFilter";
    $DB3->SQL($query);
    $DB3->Query();
    while ($orders = $DB3->NextRecord())
    {
      $orderdata[] = $orders;
    }

    foreach ($orderdata as $orders)
    {

      if($orders['bulk']==true)
        $koers=$this->getKoersen($orders['fondskort'] ,$orders['orderid']);
      else
        $koers=$this->getKoersen($orders['fondskort'] );


      if ($orders['koersLimiet'] != 0)
        $koers['fondsKoers']= $orders['koersLimiet'];

      if ($orders['transactieSoort'] == "A") //aankopen aftrekken van Saldo
      {
        $bedrag = -1 * ($orders['aantal'] * $koers['fondsKoers'] * $koers['fondseenheid'] * $koers['valutaKoers']);
      }
      else if ($orders['transactieSoort'] == "V") //Verkopen optellen bij saldo
      {
        $bedrag = ($orders['aantal'] * $koers['fondsKoers'] * $koers['fondseenheid'] * $koers['valutaKoers']);
      }
      $risicoBedrag = ($bedrag / 100) * $orders['RisicoPercentageFonds'];
      $risicoTotaal += $risicoBedrag;
      $totaalWaarde += $bedrag; //Totaal waarde portefeuille ook aanpassen.
    }

//	eind huidige orders toevoegen

    $risicoPercentage = $risicoTotaal / ($totaalWaarde / 100);

    // print risico klasse portefeuille.
    $query = "SELECT  " .
            " Risicoklassen.Risicoklasse, " .
            " Risicoklassen.Minimaal, " .
            " Risicoklassen.Maximaal " .
            " FROM Risicoklassen, Portefeuilles WHERE " .
            " Risicoklassen.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND " .
            " Portefeuilles.Portefeuille = '" . $portefeuille . "' AND " .
            " Portefeuilles.Risicoklasse = Risicoklassen.Risicoklasse ";

    $DB3->SQL($query);
    $DB3->Query();
    $risicodata = $DB3->nextRecord();

    if ($risicoPercentage < $risicodata['Minimaal'])
    {
      $txt = number_format($risicoPercentage, 2, ",", ".") . " is minder dan " . $risicodata['Minimaal'];
      $txtSilent = '1';
    }
    elseif ($risicoPercentage > $risicodata['Maximaal'])
    {
      $txt = number_format($risicoPercentage, 2, ",", ".") . " is meer dan " . $risicodata['Maximaal'];
      $txtSilent = '1';
    }
    else
    {
      $txt = number_format($risicoPercentage, 2, ",", ".") . " ligt tussen " . $risicodata['Minimaal'] . " en " . $risicodata['Maximaal'];
      $txtSilent = '0';
    }

    $this->checksKort['risi'] = $txtSilent;
    $this->mailTxt['risi'] = $txt;

    if ($this->data['silent'] == true)
      return $txtSilent;
    else
      return $txt;
  }

  function WaardeAankopen()
  {
    $db = new DB();
    $portefeuille = $this->data['portefeuille'];
    $eigenOrderid = $this->data['eigenOrderid'];
    $transactieAantal = $this->data['transactieAantal'];
    $rekeningValuta = $this->data['valuta'];
    $rapportageDatum = $this->data['rapportageDatum'];
    $transactiesoort = $this->data['transactieSoort'];
    $orderData = array();
    if ($this->bulk == true)
    {
      $query = "SELECT id as orderid, aantal, fonds, transactieSoort, koersLimiet
		  		FROM TijdelijkeBulkOrdersV2
			  	WHERE
			  	Portefeuille = '" . $portefeuille . "'
			  	AND id <> '" . $eigenOrderid . "' ".$this->bulkIdFilter;

      $db->SQL($query); //echo " $newSaldo <br>\n $query <br>\n";
      $db->Query();
      while ($overigeOrders = $db->nextRecord())
      {
        $orderData[] = $overigeOrders;
      }
    }
    $dateFilter=$this->getOrderregelDateFilter($eigenOrderid,$portefeuille);
    $query = "SELECT OrderRegelsV2.aantal, OrdersV2.transactieSoort, OrdersV2.id as orderid, OrdersV2.Fonds, OrdersV2.koersLimiet FROM OrderRegelsV2, OrdersV2
 				WHERE OrderRegelsV2.orderid = OrdersV2.id AND
 				OrderRegelsV2.orderregelStatus < 1 AND
 				OrderRegelsV2.orderid <> '" . $eigenOrderid . "' AND
 				OrderRegelsV2.Portefeuille = '" . $portefeuille . "' $dateFilter";
    $db->SQL($query);
    $db->Query();
    $db2 = new DB();
    while ($overigeOrders = $db->nextRecord())
    {
      $orderData[] = $overigeOrders;
    }
    foreach ($orderData as $overigeOrders)
    {
      $query = "SELECT 	Fondskoersen.Koers,
      				  	Fondskoersen.Datum,
      					Fondsen.Valuta,
      					Fondsen.Fonds FROM Fondskoersen, Fondsen
 	  			  WHERE Fondskoersen.Fonds = Fondsen.Fonds AND
 	  			  Fondsen.Fonds = '" . $overigeOrders['Fonds'] . "' AND
 	  			  Fondskoersen.Datum <= '" . $rapportageDatum . "'
 	  			  ORDER BY Datum DESC LIMIT 1";
      $db2->SQL($query);
      $overigeFonds = $db2->lookupRecord();

      $query = "SELECT Koers,Datum FROM Valutakoersen
					WHERE Valuta = '" . $overigeFonds['Valuta'] . "' AND
					Datum <= '" . $rapportageDatum . "'
					ORDER BY Datum DESC LIMIT 1";
      $db2->SQL($query);
      $overigeRegekningValutaKoers = $db2->lookupRecord();

      if ($overigeOrders['koersLimiet]'] != 0)
        $overigeFonds['koers'] = $overigeOrders['koersLimiet'];
      if ($overigeOrders['transactieSoort'] == "A") //aankopen
      {
        $waarde -= ($overigeOrders['aantal'] * $overigeFonds['Koers'] * $overigeFonds['Koers']) / $overigeRegekningValutaKoers['Koers'];
      }
      if ($overigeOrders['transactieSoort'] == "V") //Verkopen
      {
        $waarde += ($overigeOrders['aantal'] * $overigeFonds['Koers'] * $overigeFonds['Koers']) / $overigeRegekningValutaKoers['Koers'];
      }
    }
//huidige aankoop

    $query = "SELECT 	Fondskoersen.Koers,
      				  	Fondskoersen.Datum,
      					Fondsen.Valuta,
      					Fondsen.Fonds FROM Fondskoersen, Fondsen
 	  			  WHERE Fondskoersen.Fonds = Fondsen.Fonds AND
 	  			  Fondsen.Fonds = '" . $this->data['Fonds'] . "' AND
 	  			  Fondskoersen.Datum <= '" . $rapportageDatum . "'
 	  			  ORDER BY Datum DESC LIMIT 1";
    $db2->SQL($query);
    $overigeFonds = $db2->lookupRecord();

    $query = "SELECT Koers,Datum FROM Valutakoersen
					WHERE Valuta = '" . $overigeFonds['Valuta'] . "' AND
					Datum <= '" . $rapportageDatum . "'
					ORDER BY Datum DESC LIMIT 1";
    $db2->SQL($query);
    $overigeRegekningValutaKoers = $db2->lookupRecord();

    if ($overigeOrders['koersLimiet]'] != 0)
      $overigeFonds['koers'] = $overigeOrders['koersLimiet'];
    if ($transactiesoort == "A") //aankopen
    {
      $waarde -= ($transactieAantal * $overigeFonds['Koers'] ) * $overigeRegekningValutaKoers['Koers'];
    }
    if ($transactiesoort == "V") //Verkopen
    {
      $waarde += ($transactieAantal * $overigeFonds['Koers'] ) * $overigeRegekningValutaKoers['Koers'];
    }
    return $waarde; //in Euro
  }
  
  function BeperkingenCheck()
  {
    $db = new DB();
    $portefeuille = $this->data['portefeuille'];
    $query="SELECT profielOverigeBeperkingen FROM CRM_naw WHERE portefeuille='".$portefeuille."'";
    $tmp=$db->lookupRecordByQuery($query);
    $txt=$tmp['profielOverigeBeperkingen'];
    $txt=str_replace(array('<', '>'), array('&lt;', '&gt;'), $txt);
    $txt=nl2br($txt);

    if ($txt == "")
    {
      $txt = "Geen beperkingen voor deze portefeuille gevonden.";
      $txtSilent=0;
    }
    else
    {
      $txtSilent=1;
    }
    
    $this->checksKort['vbep'] = $txtSilent;
    $this->mailTxt['vbep'] = $txt;

    if ($this->data['silent'] == true)
      return $txtSilent;
    else
      return $txt;
     
  }


  function AkkoordCheck()
  {
    $db=new DB();

    $query="SELECT orderAkkoord FROM Vermogensbeheerders JOIN Portefeuilles ON Vermogensbeheerders.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder WHERE  Portefeuilles.Portefeuille='".$this->data['portefeuille']."'";
    $db->sql($query);
    $tmp=$db->lookupRecordByQuery($query);

    $txtSilent = 0;
    $txt='';
    if ($this->bulk == true)
    {
      if($tmp['orderAkkoord'] == 0 || $tmp['orderAkkoord']==2)
      {
        $txtSilent = 2;
        $txt='Bevestiging van bulkorder nodig.';
      }
    }
    else
    {
      if($tmp['orderAkkoord'] == 1 || $tmp['orderAkkoord']==2)
      {
        $txtSilent = 2;
        $txt='Bevestiging van order nodig.';
      }
    }

    $this->checksKort['akkam'] = $txtSilent;
    $this->mailTxt['akkam'] = $txt;

    if ($this->data['silent'] == true)
      return $txtSilent;
    else
      return $txt;

  }

  function OptieCheck()
  {
    if($this->extraLogs){logit('OptieCheck');}
    $db = new DB();
    $portefeuille = $this->data['portefeuille'];


    $eigenOrderid = $this->data['eigenOrderid'];
    $transactieAantal = $this->data['transactieAantal'];
    $rekeningValuta = $this->data['valuta'];
    $rapportageDatum = $this->data['rapportageDatum'];
    $transactiesoort = $this->data['transactieSoort'];
    //
    $juldat=db2jul($this->data['rapportageDatum']);
    $txtSilent = 0;
    $txt='';
  
    $query="SELECT Rekeningmutaties.Fonds, SUM(Aantal) as aantal FROM Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
INNER JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
 WHERE Rekeningmutaties.Boekdatum>= '".date('Y',$juldat).'-01-01'."' AND Rekeningmutaties.Boekdatum <= '".date('Y-m-d',$juldat)."' AND Rekeningen.Portefeuille='".$portefeuille."' AND
Rekeningmutaties.Fonds = '".mysql_real_escape_string($this->data['Fonds'])."' GROUP BY Rekeningmutaties.Fonds";
    $db->SQL($query);
    if($this->extraLogs){logit($query);}
    $db->Query();
    $data=$db->nextRecord();
    $fondsAantal=round($data['aantal'],6);

    
    
    if($this->data['transactieSoort']=='V')
    {
   //   $portefeuille='236925';
      $query="SELECT Rekeningmutaties.Fonds, SUM(Aantal) as aantal,Fondsen.fondsEenheid  FROM Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
INNER JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
 WHERE Rekeningmutaties.Boekdatum>= '".date('Y',$juldat).'-01-01'."' AND Rekeningmutaties.Boekdatum <= '".date('Y-m-d',$juldat)."' AND Rekeningen.Portefeuille='".$portefeuille."' AND
Rekeningmutaties.Fonds IN (SELECT Fonds FROM Fondsen WHERE OptieBovenliggendFonds='".mysql_real_escape_string($this->data['Fonds'])."') AND Fondsen.OptieType='C'
GROUP BY Rekeningmutaties.Fonds HAVING aantal < 0 ";
      $db->SQL($query);
      if($this->extraLogs){logit($query);}
      $db->Query();
      $benodigdeFondsAantalOpties=0;
      while($data=$db->nextRecord())
      {
        if($txt<>'')
          $txt.="<br>\n";
        $txt.="Optie '".$data['Fonds']."' met aantal: ".$data['aantal']." aanwezig. ";
        $benodigdeFondsAantalOpties+=$data['aantal']*$data['fondsEenheid'];
      }
      $overig=$this->overigeFondsTransacties($this->data['Fonds'],true);
      $aantalNaOrder=$fondsAantal+$benodigdeFondsAantalOpties-$transactieAantal;
      $aantalNaOverigeOrders=$aantalNaOrder+$overig['aantal'];
      if($aantalNaOrder<0 ||  $aantalNaOverigeOrders<0 )
      {
        $overigeOrderTxt='';
        if($aantalNaOverigeOrders<>0)
          $overigeOrderTxt=", na overige orders:$aantalNaOverigeOrders";
        $txt .= "<br>\nFondsaantal: $fondsAantal, ViaOpties: $benodigdeFondsAantalOpties, Deze order:$transactieAantal, Na order:$aantalNaOrder".$overigeOrderTxt."\n";
        $txtSilent = 2;
      }

      if($overig['aantal']<>0)
      {
        $txt .= "<br>\nTransacties voor Fonds (" . $this->data['Fonds'] . ") aantal: " . $overig['aantal'] . ". (" . $overig['log'] . ")";
        $txtSilent = 2;
      }
      if($txt=='')
        $txt='Geen opties.';
    }
    elseif($this->data['transactieSoort']=='VO')
    {
    
      //$portefeuille='057610';
      $query="SELECT OptieBovenliggendFonds as Fonds FROM Fondsen WHERE Fonds='".mysql_real_escape_string($this->data['Fonds'])."'";
      $db->SQL($query);
      $db->Query();
      $fonds=$db->nextRecord();
    
      $query="SELECT Rekeningmutaties.Fonds, SUM(Aantal) as aantal FROM Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
 WHERE Rekeningmutaties.Boekdatum>= '".date('Y',$juldat).'-01-01'."' AND Rekeningmutaties.Boekdatum <= '".date('Y-m-d',$juldat)."' AND Rekeningen.Portefeuille='".$portefeuille."' AND
Rekeningmutaties.Fonds='".mysql_real_escape_string($fonds['Fonds'])."'
GROUP BY Rekeningmutaties.Fonds HAVING aantal <> 0 ";
      $db->SQL($query);
      if($this->extraLogs){logit($query);}
      $db->Query();
      $data=$db->nextRecord();
      $fondsAantal=round($data['aantal'],6);

      $overig=$this->overigeFondsTransacties($fonds['Fonds'],true);
      $fondsAantal+=$overig['aantal'];
      $txt.="Fonds (".$fonds['Fonds'].") met aantal: ".round($fondsAantal,6)." aanwezig ( positie: ".round($data['aantal'],6)." / orders: ".round($overig['aantal'],6)." ) .";

      $query="SELECT Rekeningmutaties.Fonds, SUM(Aantal) as aantal,Fondsen.fondsEenheid FROM Rekeningmutaties 
JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
INNER JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
 WHERE Rekeningmutaties.Boekdatum>= '".date('Y',$juldat).'-01-01'."' AND Rekeningmutaties.Boekdatum <= '".date('Y-m-d',$juldat)."' AND Rekeningen.Portefeuille='".$portefeuille."' AND
Rekeningmutaties.Fonds IN (SELECT Fonds FROM Fondsen WHERE OptieBovenliggendFonds='".mysql_real_escape_string($fonds['Fonds'])."') AND Fondsen.OptieType='C'
GROUP BY Rekeningmutaties.Fonds HAVING aantal < 0 ";
      $db->SQL($query);
      if($this->extraLogs){logit($query);}
      $db->Query();
      if($db->records()==0)
        $txt.="<br>\nGeen optie posities voor (".$fonds['Fonds'].") aanwezig.";
      while($optiedata=$db->nextRecord())
      {
        if($txt<>'')
          $txt.="<br>\n";
        $txt.="Optie (".$optiedata['Fonds'].") met aantal: ".($optiedata['fondsEenheid']*$optiedata['aantal']*-1)." aanwezig, ".($optiedata['aantal'])." aanwezig.";
        $fondsAantal+=($optiedata['fondsEenheid']*$optiedata['aantal']);
      }
      $txt.="<br>\nFonds (".$fonds['Fonds'].") aantal na opties ".$fondsAantal.".";


      $query2 = "SELECT Fondsen.Fondseenheid as fondseenheid FROM Fondsen WHERE Fondsen.Fonds = '".mysql_real_escape_string($this->data['Fonds'])."'";
      $db->SQL($query2);
      $db->Query();
      $fondseenheid=$db->nextRecord();

      $nieuwAantal=($fondsAantal - ($transactieAantal* $fondseenheid['fondseenheid']) );
      $txt.="<br>\nNa deze transactie Fonds (".$fonds['Fonds'].") aantal: ".$nieuwAantal.".";
      if($nieuwAantal<0)
        $txtSilent =2;
      //$overig=$this->overigeFondsTransacties($data['Fonds'],true);
     // $nieuwAantal+=$overig['aantal'];
      $txt.="<br>\nNa overige transacties voor Fonds (".$fonds['Fonds'].") aantal: ".$nieuwAantal.". (".$overig['log'].")";
    
      if($nieuwAantal<0)
        $txtSilent =2;

    }
    else
    {
      $txt='Geen V of VO transactie.';
    }
    if($this->extraLogs){logit($txtSilent."|".$txt);}
    $this->checksKort['optie'] = $txtSilent;
    $this->mailTxt['optie'] = $txt;

    if ($this->data['silent'] == true)
      return $txtSilent;
    else
      return $txt;

  }

  function overigeFondsTransacties($fonds,$metOpties=false)
  {
    $db = new DB();
    $portefeuille = $this->data['portefeuille'];
    $eigenOrderid = $this->data['eigenOrderid'];
    $totaalAantal = 0;
    $optiesWhere='';
    if($metOpties==true)
    {
      $opties=array();
      $query="SELECT Fonds FROM Fondsen WHERE OptieBovenliggendFonds='".mysql_real_escape_string($fonds)."' AND Fondsen.OptieType='C' AND EindDatum>now()";
      $db->SQL($query);
      $db->Query();
      while ($optie = $db->nextRecord())
      {
         $opties[]=$optie['Fonds'];
      }
      $optiesWhere=" OR Fonds IN('".implode("','",$opties)."')";
    }

      if ($this->bulk == true)
      {
        $query = "SELECT id as orderid, aantal, Fonds, transactieSoort
		  		FROM TijdelijkeBulkOrdersV2
			  	WHERE
		  		Fonds = '" . $fonds. "'
			  	AND Portefeuille = '" . $portefeuille . "'
			  	AND  TijdelijkeBulkOrdersV2.id <> '" . $eigenOrderid . "'";
        $db->SQL($query);
        $db->Query();
        while ($actieveTransacties = $db->nextRecord())
        {
          if ($actieveTransacties['transactieSoort'] == "A" || $actieveTransacties['transactieSoort'] == "AO")
            $totaalAantal += $actieveTransacties['aantal'];
          if ($actieveTransacties['transactieSoort'] == "V")
            $totaalAantal -= $actieveTransacties['aantal'];
        }
        $eigenOrderid = -1;
      }

      $dateFilter=$this->getOrderregelDateFilter($eigenOrderid,$portefeuille);
      $query = "SELECT OrderRegelsV2.orderid, OrderRegelsV2.aantal,OrdersV2.fondseenheid,OrderRegelsV2.bedrag, OrdersV2.Fonds, OrdersV2.transactieSoort
	  		   	FROM OrdersV2, OrderRegelsV2
				WHERE OrderRegelsV2.orderid = OrdersV2.id
				AND (OrdersV2.Fonds = '" .$fonds . "' $optiesWhere)
				AND OrderRegelsV2.Portefeuille = '" . $portefeuille . "'
				AND OrderRegelsV2.orderregelStatus < '4' AND OrdersV2.id <> '$eigenOrderid' $dateFilter";
      $db->SQL($query);
      $db->Query();
      if($this->extraLogs){logit($query);}

       $log='';
      while ($actieveTransacties = $db->nextRecord())
      {
        if($actieveTransacties['fondseenheid']==0)
          $actieveTransacties['fondseenheid']=1;

        $log.=$actieveTransacties['transactieSoort'].':'.$actieveTransacties['aantal']*$actieveTransacties['fondseenheid'].',';
        if ($actieveTransacties['transactieSoort'] == "A" || $actieveTransacties['transactieSoort'] == "AO")
          $totaalAantal += $actieveTransacties['aantal']*$actieveTransacties['fondseenheid'];
        if ($actieveTransacties['transactieSoort'] == "V" || $actieveTransacties['transactieSoort'] == "VO")
          $totaalAantal -= $actieveTransacties['aantal']*$actieveTransacties['fondseenheid'];
      }
      if (round($totaalAantal, 4) < 0)
      {
        $txt = "Na transacties aantal < 0 ! (" . round($totaalAantal, 4) . ") $log<br>";
      }
      else
      {
        $txt = "Na transacties : " . round($totaalAantal, 4) . " $log<br>";
      }
      if($this->extraLogs){logit("'aantal'=>$totaalAantal,'txt'=>$txt,'log'=>$log");}

      return array('aantal'=>$totaalAantal,'txt'=>$txt,'log'=>$log);
  }

  function RestrictieCheck()
  {

    $txtSilent = 0;
    $txt='';
    if($this->data['fonds']=='')
    {
      $this->checksKort['rest'] = 0;
      if ($this->data['silent'] == true)
      {
        return 0;
      }
      else
      {
        return "Niet mogelijk ivm eigen fonds";
      }
    }

    $db=new DB();

    $query="SELECT Portefeuilles.Vermogensbeheerder,Vermogensbeheerders.geenStandaardSector FROM Portefeuilles 
  LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
   WHERE portefeuille='".$this->data['portefeuille']."'";
    $db->SQL($query);
    $db->Query();
    $verm=$db->nextRecord();
    $query="SELECT
contractueleUitsluitingen.categoriesoort,
contractueleUitsluitingen.fonds,
contractueleUitsluitingen.portefeuille,
contractueleUitsluitingen.vermogensbeheerder,
contractueleUitsluitingen.categorie,
contractueleUitsluitingen.vanaf,
contractueleUitsluitingen.soortReservering,
contractueleUitsluitingen.geldrekening,
contractueleUitsluitingen.bedrag
FROM
contractueleUitsluitingen
WHERE ((contractueleUitsluitingen.Vermogensbeheerder='".$verm['Vermogensbeheerder']."' AND Portefeuille='') OR contractueleUitsluitingen.Portefeuille='".$this->data['portefeuille']."') 
AND vanaf < now()
AND (einddatum='0000-00-00' OR einddatum > now())";
    $db->SQL($query);
    $db->Query();
    $uitgeslotenFondsen=array();
    $uitegeslotenCategorien=array();
    $reserveringen=array();
    $geldrekeningen=array();
    $koppelVertaling=array('Beleggingscategorien'=>'beleggingscategorie',
                           'Beleggingssectoren'=>'beleggingssector',
                           'Fondssoort'=>'fondssoort',
                           'Regios'=>'regio',
                           'afmCategorien'=>'afmCategorie',
                           'Valuta'=>'valuta',
                           'Rating'=>'rating',
                           'Zorgplichtcategorien'=>'zorgplicht',
                           'Hoofdcategorien'=>'hoofdcategorie');



    while($data = $db->nextRecord())
    {
      if($data['fonds']<>'')
      {
        if($data['categoriesoort']=='Reservering')
          $reserveringen[$data['fonds']] = $data;
        else
          $uitgeslotenFondsen[$data['fonds']] = $data;
      }
      if($data['geldrekening']<>'' && $data['portefeuille'] == $this->data['portefeuille'])
        $geldrekeningen[$data['geldrekening']]=$data;
      if($data['categoriesoort']<>'')
        $uitegeslotenCategorien[$koppelVertaling[$data['categoriesoort']]][$data['categorie']]=$data['categorie'];
    }

    $query = "SELECT
	  Fondsen.Fonds,
		Fondsen.standaardSector,
		Fondsen.valuta,
		Fondsen.rating,
		Fondsen.fondssoort,
  (SELECT  Beleggingssector FROM  BeleggingssectorPerFonds WHERE BeleggingssectorPerFonds.Fonds = Fondsen.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$verm['Vermogensbeheerder']."' AND Vanaf <= now() ORDER BY BeleggingssectorPerFonds.Vanaf  DESC LIMIT 1) as beleggingssector,
  (SELECT  AttributieCategorie FROM  BeleggingssectorPerFonds WHERE BeleggingssectorPerFonds.Fonds = Fondsen.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$verm['Vermogensbeheerder']."' AND Vanaf <= now() ORDER BY Vanaf DESC LIMIT 1) as attributieCategorie,
  (SELECT  Regio FROM  BeleggingssectorPerFonds WHERE BeleggingssectorPerFonds.Fonds = Fondsen.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$verm['Vermogensbeheerder']."' AND Vanaf <= now() ORDER BY Vanaf DESC LIMIT 1) as regio,
  (SELECT  Beleggingscategorie FROM  BeleggingscategoriePerFonds WHERE BeleggingscategoriePerFonds.Fonds = Fondsen.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$verm['Vermogensbeheerder']."' AND Vanaf <= now()  ORDER BY Vanaf  DESC LIMIT 1) as beleggingscategorie,
  (SELECT  afmCategorie FROM  BeleggingscategoriePerFonds WHERE BeleggingscategoriePerFonds.Fonds = Fondsen.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$verm['Vermogensbeheerder']."'AND Vanaf <= now() ORDER BY Vanaf  DESC LIMIT 1) as afmCategorie,
  Fondsen.Forward,
  ZorgplichtPerFonds.Zorgplicht
  FROM Fondsen
  LEFT JOIN ZorgplichtPerFonds ON Fondsen.fonds = ZorgplichtPerFonds.Fonds AND ZorgplichtPerFonds.Vermogensbeheerder='".$verm['Vermogensbeheerder']."'
  WHERE Fondsen.Fonds='".mysql_real_escape_string($this->data['Fonds'])."'" ;



    $db->SQL($query);
    $db->Query();
    $fondsData = $db->nextRecord();
    if($fondsData['beleggingssector']=='' && $verm['geenStandaardSector']==0)
      $fondsData['beleggingssector']=$fondsData['standaardSector'];
    if($fondsData['Zorgplicht']=='')
    {
      $query="SELECT Zorgplicht FROM ZorgplichtPerBeleggingscategorie WHERE Vermogensbeheerder='".$verm['Vermogensbeheerder']."' AND beleggingscategorie='".$fondsData['beleggingscategorie']."' limit 1";
      $db->SQL($query);
      $data = $db->NextRecord();
      $fondsData['Zorgplicht'] = $data['Zorgplicht'];
    }

    $query="SELECT CategorienPerHoofdcategorie.Hoofdcategorie FROM CategorienPerHoofdcategorie 
  WHERE CategorienPerHoofdcategorie.Vermogensbeheerder = '".$verm['Vermogensbeheerder']."' AND CategorienPerHoofdcategorie.Beleggingscategorie='".$fondsData['beleggingscategorie']."'";
    $db->SQL($query);
    $db->Query();
    $data = $db->NextRecord();
    $fondsData['hoofdcategorie']=$data['Hoofdcategorie'];
  
    
    if(is_array($uitgeslotenFondsen[$this->data['Fonds']]))
    {
      $txt .= "Fonds " . $this->data['Fonds'] ." uitgesloten fonds.<br>\n";
      $txtSilent = 2;
    }
  
    foreach($reserveringen as $fonds=>$rekeningData)
    {
      $txt.=$fonds." | ".$rekeningData['soortReservering']." | € ".$rekeningData['bedrag']."<br>\n";
      $txtSilent = 2;
    }
    
    foreach($geldrekeningen as $rekening=>$rekeningData)
    {
      $txt.=$rekening." | ".$rekeningData['soortReservering']." | € ".$rekeningData['bedrag']."<br>\n";
      $txtSilent = 2;
    }
  
    $uitsluitingCategorie=false;
    foreach($koppelVertaling as $check)
    {
      if(isset($uitegeslotenCategorien[$check][$fondsData[$check]]))
      {
        $txt.=",uitgesloten in $check";
        $txtSilent = 2;
        $uitsluitingCategorie = true;
      }
    }
    if($uitsluitingCategorie==true)
    {
      $txt = "Fonds " . $this->data['Fonds'] . " $txt";
    }
    
    if($txtSilent<>2)
    {
      $txt="Geen restricties";
    }

    $this->checksKort['rest'] = $txtSilent;
    $this->mailTxt['rest'] = $txt;

    if ($this->data['silent'] == true)
      return $txtSilent;
    else
      return $txt;

  }

}
