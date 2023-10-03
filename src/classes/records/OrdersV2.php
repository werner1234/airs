<?php

/*
  AE-ICT source module
  Author  						: $Author: rvv $
  Laatste aanpassing	: $Date: 2020/01/22 15:58:51 $
  File Versie					: $Revision: 1.97 $

  $Log: OrdersV2.php,v $
  Revision 1.97  2020/01/22 15:58:51  rvv
  *** empty log message ***

  Revision 1.96  2019/10/16 06:26:24  cvs
  no message

  Revision 1.95  2019/10/11 17:33:03  rvv
  *** empty log message ***

  Revision 1.94  2019/09/06 10:19:43  rm
  fixVerzenddatum/fixAnnuleerdatum geforceerd naar datum velden

  Revision 1.93  2019/09/06 07:28:42  rm
  8060

  Revision 1.92  2019/08/28 05:55:45  rvv
  *** empty log message ***

  Revision 1.91  2019/08/17 18:45:12  rvv
  *** empty log message ***

  Revision 1.90  2019/08/09 14:27:40  rm
  4913

  Revision 1.89  2019/08/08 08:08:10  rm
  7868

  Revision 1.88  2018/11/24 19:04:48  rvv
  *** empty log message ***

  Revision 1.87  2018/11/21 09:36:24  rvv
  *** empty log message ***

  Revision 1.86  2018/11/21 08:43:55  rvv
  *** empty log message ***

  Revision 1.85  2018/08/18 12:40:14  rvv
  php 5.6 & consolidatie

  Revision 1.84  2018/06/27 09:03:43  rm
  6560

  Revision 1.83  2018/05/23 13:43:43  rvv
  *** empty log message ***

  Revision 1.82  2018/02/08 06:51:06  rvv
  *** empty log message ***

  Revision 1.81  2017/12/27 06:24:22  rvv
  *** empty log message ***

  Revision 1.80  2017/12/23 18:11:43  rvv
  *** empty log message ***

  Revision 1.79  2017/12/11 14:11:42  rm
  no message

  Revision 1.78  2017/12/08 18:23:43  rm
  no message

  Revision 1.77  2017/09/21 15:01:00  rm
  advies relaties

  Revision 1.76  2017/09/03 11:42:10  rvv
  *** empty log message ***

  Revision 1.75  2017/07/19 19:20:46  rvv
  *** empty log message ***

  Revision 1.74  2017/06/24 16:48:38  rvv
  *** empty log message ***

  Revision 1.73  2017/06/14 13:34:18  rm
  orders opnieuw inleggen en inleggen vanuit rapportage

  Revision 1.72  2017/05/04 12:57:54  rm
  javascript problemen

  Revision 1.71  2017/03/15 16:16:25  rm
  5692

  Revision 1.70  2017/03/11 20:18:50  rvv
  *** empty log message ***

  Revision 1.69  2017/03/05 12:03:24  rvv
  *** empty log message ***

  Revision 1.68  2017/03/04 19:17:44  rvv
  *** empty log message ***

  Revision 1.67  2017/03/01 11:59:44  rm
  Ordersv2

  Revision 1.66  2016/12/21 16:29:00  rvv
  *** empty log message ***

  Revision 1.65  2016/11/14 07:06:42  rvv
  *** empty log message ***

  Revision 1.64  2016/11/13 16:25:14  rvv
  *** empty log message ***

  Revision 1.63  2016/11/09 17:09:54  rvv
  *** empty log message ***

  Revision 1.62  2016/10/14 09:47:52  rm
  Beurs sorteren

  Revision 1.61  2016/09/14 14:40:06  rm
  update van kalender

  Revision 1.60  2016/09/14 11:46:07  rvv
  *** empty log message ***

  Revision 1.59  2016/09/07 09:36:00  rvv
  *** empty log message ***

  Revision 1.58  2016/09/02 06:49:48  rm
  Wijzigingen in status ivm rechten
  5222 - 4754

  Revision 1.57  2016/08/31 14:39:40  rm
  Orders

  Revision 1.56  2016/08/31 08:22:38  rvv
  *** empty log message ***

  Revision 1.55  2016/08/25 13:31:36  rvv
  *** empty log message ***

  Revision 1.54  2016/08/24 16:21:53  rvv
  *** empty log message ***

  Revision 1.53  2016/08/17 15:55:21  rvv
  *** empty log message ***

  Revision 1.52  2016/07/27 15:55:04  rvv
  *** empty log message ***

  Revision 1.51  2016/07/20 16:04:32  rvv
  *** empty log message ***

  Revision 1.50  2016/07/20 15:08:47  rm
  validatie op limiet order

  Revision 1.49  2016/07/18 17:48:56  rvv
  *** empty log message ***

  Revision 1.48  2016/07/15 10:06:50  rvv
  *** empty log message ***

  Revision 1.47  2016/07/14 17:05:55  rvv
  *** empty log message ***

  Revision 1.46  2016/07/13 15:39:31  rvv
  *** empty log message ***

  Revision 1.45  2016/07/09 18:55:55  rvv
  *** empty log message ***

  Revision 1.44  2016/07/08 13:57:16  rm
  Orders v2

  Revision 1.43  2016/07/03 08:43:20  rvv
  *** empty log message ***

  Revision 1.42  2016/06/15 15:51:31  rvv
  *** empty log message ***

  Revision 1.41  2016/06/09 08:18:21  rm
  Nieuwe notifier

  Revision 1.40  2016/06/05 12:14:30  rvv
  *** empty log message ***

  Revision 1.39  2016/06/03 15:01:19  rm
  Orders

  Revision 1.38  2016/06/02 07:43:27  rm
  Orders

  Revision 1.37  2016/05/25 14:20:55  rvv
  *** empty log message ***

  Revision 1.36  2016/05/19 14:58:52  rvv
  *** empty log message ***

  Revision 1.35  2016/05/19 12:56:48  rvv
  *** empty log message ***

  Revision 1.34  2016/04/30 15:35:50  rvv
  *** empty log message ***

  Revision 1.33  2016/04/20 15:20:14  rvv
  *** empty log message ***

  Revision 1.32  2016/04/20 15:07:42  rm
  orders

  Revision 1.31  2016/04/20 15:05:21  rvv
  *** empty log message ***

  Revision 1.30  2016/04/19 11:20:28  rvv
  *** empty log message ***

  Revision 1.29  2016/04/17 17:12:16  rvv
  *** empty log message ***

  Revision 1.28  2016/03/17 16:26:43  rvv
  *** empty log message ***

  Revision 1.27  2016/03/17 14:50:11  rm
  OrdersV2

  Revision 1.26  2016/03/13 16:20:54  rvv
  *** empty log message ***

  Revision 1.25  2016/03/11 13:25:39  rvv
  *** empty log message ***

  Revision 1.24  2016/02/28 17:19:29  rvv
  *** empty log message ***

  Revision 1.23  2016/02/27 15:59:28  rvv
  *** empty log message ***

  Revision 1.22  2016/02/24 16:12:45  rm
  opnieuw inleggen check

  Revision 1.21  2016/02/24 13:09:15  rm
  verzendcheck

  Revision 1.20  2016/02/14 11:54:10  rvv
  *** empty log message ***

  Revision 1.19  2016/02/10 17:18:16  rvv
  *** empty log message ***

  Revision 1.18  2015/12/09 15:47:31  rm
  OrdersV2

  Revision 1.17  2015/12/06 18:03:19  rvv
  *** empty log message ***

  Revision 1.16  2015/11/22 14:18:48  rvv
  *** empty log message ***

  Revision 1.15  2015/11/15 12:12:02  rvv
  *** empty log message ***

  Revision 1.14  2015/11/13 16:02:47  rm
  no message

  Revision 1.13  2015/11/04 13:28:43  rm
  no message

  Revision 1.12  2015/11/01 18:07:43  rvv
  *** empty log message ***

  Revision 1.11  2015/09/30 07:48:22  rvv
  *** empty log message ***

  Revision 1.10  2015/08/12 12:55:25  rm
  orders v2

  Revision 1.9  2015/08/12 11:14:29  rvv
  *** empty log message ***

  Revision 1.8  2015/08/11 06:20:11  rvv
  *** empty log message ***

  Revision 1.7  2015/08/02 14:26:28  rvv
  *** empty log message ***

  Revision 1.6  2015/07/31 14:18:35  rm
  orders v2

  Revision 1.5  2015/07/24 14:49:37  rm
  OrdersV2

  Revision 1.4  2015/07/19 15:01:04  rvv
 * ** empty log message ***

  Revision 1.3  2015/07/12 10:47:16  rvv
 * ** empty log message ***

  Revision 1.2  2015/07/10 14:55:01  rm
  ordersV2

  Revision 1.1  2015/07/08 15:35:21  rvv
 * ** empty log message ***



 */

class OrdersV2 extends Table
{
  /*
   * Object vars
   */

  var $data             = array();
  var $orderregelObject = null;
  var $forceLog         = false;
  /*
   * Constructor
   */

  function OrdersV2()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'], 0);
  }

  function setOrderregelObject($orderregelObject)
  {
    $this->orderregelObject = $orderregelObject;
  }

  function addField($name, $properties)
  {
    $this->data['fields'][$name] = $properties;
  }

  /*
   * Veldvalidatie
   */

  function validate($forceLog=false)
  {
    if($this->forceLog==true)
    {
      $forceLog=true;
      $this->forceLog=false;
    }
    global $__ORDERvar;
    global $USR;
    $orderLogs = new orderLogs();
    $verandering = null;
    $db = new DB();

    if ($this->get('id') && $this->get('id') != 'validateOrderId')
    {
      $oldRec = $this->parseBySearch(array('id' => $this->get('id')), array('orderStatus', 'ISINCode', 'fonds', 'transactieType', 'transactieSoort', 'tijdsLimiet', 'tijdsSoort', 'koersLimiet','memo', 'fixOrder'), null, 1);
      foreach ($oldRec as $key => $value)//Nieuwe log functie die alle wijzigingen logt.
      {
        $txt = '';
        $newvalue = $this->get($key);
        if($key=='memo')
        {
          if ($oldRec[$key] != $newvalue && (!requestType('ajax') || $forceLog==true) )
              $orderLogs->addToLog($this->get('id'), null, "$key aangepast.");
        }
        else
        {
          if ($key == 'tijdsLimiet')
          {
            $limietdatum = explode('-', $newvalue);
            $limietdatum[2] = str_replace('00', '', $limietdatum[2]);
            $newvalue = implode('-', $limietdatum);
            if ($oldRec[$key] == "0000-00-00")
            {
              $newvalue = $oldRec[$key];
            }
          }

          if ($oldRec[$key] != $newvalue)
          {
            if (isset ($__ORDERvar[$key][$newvalue]) && $__ORDERvar[$key][$newvalue] != '')
            {
              if (!requestType('ajax') || $forceLog==true)
              {
                $orderLogs->addToLog($this->get('id'), null, "$key naar " . $__ORDERvar[$key][$newvalue] . "|" . $oldRec[$key] . "->" . $newvalue);
              }
            }
            else
            {
              if (!requestType('ajax') || $forceLog==true)
              {
                $orderLogs->addToLog($this->get('id'), null, "$key naar $newvalue " . "|" . $oldRec[$key] . "->" . $newvalue);
              }
            }
            if ($key == 'orderStatus')
            {
              $verandering = true;
            }
          }
        }
      }
      // $txt .= $this->get("orderStatus");
    }
  /*
    $query="SELECT sum(uitvoeringsAantal) as aantal FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid='".$this->get("id")."'";
    $db->SQL($query);
    $uitvoering=$db->lookupRecord();
    $query="SELECT sum(aantal) as aantal FROM OrderRegelsV2 WHERE OrderRegelsV2.orderid='".$this->get("id")."'";
    $db->SQL($query);
    $orderegels=$db->lookupRecord();
    if($uitvoering['aantal']==$orderegels['aantal'])
    {

    }
    */
    if ($verandering == true && (!requestType('ajax') || $forceLog==true)  )
    {
      $cfg = new AE_config();
      $mailserver = $cfg->getData('smtpServer');
      $query = "SELECT
OrdersV2.id,
Gebruikers.emailAdres,
OrdersV2.fonds,
Vermogensbeheerders.OrderStatusKeuze,
OrderRegelsV2.portefeuille,
OrderRegelsV2.client,
OrderRegelsV2.aantal,
OrdersV2.transactieSoort,
OrdersV2.fondsOmschrijving,
(SELECT SUM(uitvoeringsAantal*uitvoeringsPrijs)/sum(uitvoeringsAantal) FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrdersV2.id  ) AS uitvoeringsPrijs
FROM OrdersV2
INNER JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid
INNER JOIN Portefeuilles ON OrderRegelsV2.portefeuille = Portefeuilles.Portefeuille
INNER JOIN Gebruikers ON Portefeuilles.Accountmanager = Gebruikers.Accountmanager
INNER JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
WHERE OrdersV2.id='" . $this->get("id") . "'";
      $db->SQL($query);
      //logIt("Mail versturen ".$query);
      $db->Query();
      $mailData = array();
      while ($data = $db->nextRecord())
      {
        $orderStatusmelding = unserialize($data['OrderStatusKeuze']);
        if ($orderStatusmelding[$this->get("orderStatus")]['checkedEmail'] == 1)
        {
          if ($data['emailAdres'] <> '')
          {
            $mailData[$data['emailAdres']][] = $data;
          }
        }
      }
      $query="SELECT uitvoeringsAantal,uitvoeringsDatum,uitvoeringsPrijs FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid='".$this->get("id")."'";
      $db->SQL($query);
      $db->Query();
      $uitvoeringen=array();
      while ($data = $db->nextRecord())
      {
         $uitvoeringen[]=$data;
      }

      foreach ($mailData as $emailAdres => $orderData)
      {
        $subject = "Order " . $this->get("id") . " van status " . $__ORDERvar['orderStatus'][$oldRec['orderStatus']] . " naar " . $__ORDERvar['orderStatus'][$this->get('orderStatus')];
        $mailBody = "<h3>Order " . $this->get("id") . " naar status " . $__ORDERvar['orderStatus'][$this->get('orderStatus')] . "</h3>";
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

        if(count($uitvoeringen) > 0)
        {
          $mailBody .= "<br><table border=1>";
          $mailBody .= "<tr><td>uitvoeringsAantal</td><td>uitvoeringsDatum</td><td>uitvoeringsPrijs</td></tr>";
          foreach ($uitvoeringen as $uitvoering)
          {
            $mailBody .= "<tr>
           <td align='right'>" . $uitvoering['uitvoeringsAantal'] . "</td>
           <td align='right'>" . $uitvoering['uitvoeringsDatum'] . "</td>
           <td align='right'>" . $uitvoering['uitvoeringsPrijs'] . "</td>
           </tr>";
          }
          $mailBody .= "</table>";
        }
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

    /** Bij meervoudige controlle opnieuw uitvoeren bij status 1 */
    //&& $this->get("orderSoort") == 'M'

    if ( $this->get("orderStatus") == 1  && $verandering == true)
    {
      if (!isset($this->skipOrderControle) || $this->skipOrderControle == false)
      {
       $orderRegelObj = new OrderRegelsV2();
        $orderegels = $orderRegelObj->parseBySearch(array('orderid' => $this->get('id')), array("id", "orderid"), null, -1);
        foreach ($orderegels as $orderegel)
        {
          $orderRegelObj = new OrderRegelsV2();
          $orderRegelObj->getById($orderegel['id']);
          $orderRegelEditObj = new editObject($orderRegelObj);
          $orderegel['OrdersV2.id'] = $orderegel['orderid'];
          $orderegel['orderregelId'] = $orderegel['id'];
      
          $orderRegelEditObj->data = $orderegel;
          $orderRegelEditObj->setFields();
          $orderRegelObj->orderRegelUpdate($orderegel, $verandering);
        }
      }
    }
    // check of er geen ordercontrolle fouten zijn
    if ($this->get("orderStatus") > 0 && $this->get("orderStatus") != 5 && $verandering == true)
    {
      $query = "SELECT count(OrderRegelsV2.controleStatus) AS fouten 
      FROM OrderRegelsV2
      JOIN OrdersV2 ON OrderRegelsV2.orderid = OrdersV2.id 
	    WHERE OrdersV2.id = '" . $this->get("id") . "' AND OrderRegelsV2.controleStatus > 0 ;";
      $db->SQL($query);
      $aantal = $db->lookupRecord();
      if ($aantal['fouten'] != 0)
      {
        $this->setError("orderStatus", vtb("Er zitten %s foute(n) in de orderregels.", array($aantal['fouten'])));
        $this->set("orderStatus", $this->get("orderStatus") - 1);
      }
    }


    if ($this->get("id") <> '' && ( ! isset($_POST['validate']) || $_POST['validate'] == false ) )
    {
      $query = "UPDATE OrderRegelsV2 SET orderregelStatus = '" . $this->get("orderStatus") . "' 
      WHERE orderid = '" . $this->get("id") . "' ";

      $db->SQL($query);
      $db->Query();
    }

    $koersLimiet = $this->get("koersLimiet");
    $transactieType = $this->get('transactieType');

    if ($transactieType === 'L' && (empty($koersLimiet) || $koersLimiet == 0))
    {
      $this->setError("koersLimiet", vt("Mag niet leeg zijn!"));
    }

    if ($this->get("tijdsSoort") == 'DAT') //$transactieType === 'L' &&
    {
      if (($this->get("tijdsLimiet") == '' || $this->get("tijdsLimiet") == '0000-00-00'))
      {
        $this->setError("tijdsLimiet", vt("Mag niet leeg zijn!"));
      }
    }
    $aantal = $this->get("aantal");
    if ($aantal < 0)
    {
      $this->set("aantal", abs($aantal));
    }

    ($this->get("fondsOmschrijving") == "")?$this->setError("fondsOmschrijving", vt("Mag niet leeg zijn!")):true;
    ($this->get("transactieType") == "")?$this->setError("transactieType", vt("Mag niet leeg zijn!")):true;
    ($this->get("transactieSoort") == "")?$this->setError("transactieSoort", vt("Mag niet leeg zijn!")):true;

    if ($this->get("tijdsSoort") == 'GTC' && ($this->get("tijdsLimiet") == '' || $this->get("tijdsLimiet") == '0000-00-00'))
    {
      $jaarLater=mktime(0,0,0,date('m'),date('d'),date('Y')+1);
      $dagVanWeek=date('w',$jaarLater);
      if($dagVanWeek==0 || $dagVanWeek==6)
        $jaarLater=$jaarLater-3*86400;
      $this->set("tijdsLimiet", date('Y-m-d',$jaarLater-86400));
    }

/*
    if ($this->get('orderStatus') > 2 && $this->get('fixOrder') == 0)
    {
      $query = "SELECT sum(uitvoeringsAantal) AS totaal FROM OrderUitvoeringV2 WHERE orderid='" . $this->get("id") . "' ";
      $db->SQL($query);
      $regelsRec = $db->lookupRecord();
      $query="SELECT SUM(OrderRegelsV2.aantal) as aantal FROM OrderRegelsV2 WHERE OrderRegelsV2.orderid = '".$this->get("id") ."'";
      $db->SQL($query);
      $orderregels=$db->lookupRecord();
      if (round($orderregels["aantal"], 4) != round(($regelsRec["totaal"]), 4) && $regelsRec["totaal"] > 0)
      {
        $this->set("orderStatus",2);
        $this->setError("orderStatus", "Uitvoerings aantal ongelijk aan order aantal. Status terug gezet naar uitgevoerd.");
      }
    }
  */
    if ($this->get('fixOrder') == 1)
    {


      /**
       * @todo: controleren of dit inderdaad weg kan
       */
//      if($this->get('ISINCode') == '')
//      {
//          $query = "SELECT fondssoort FROM Fondsen WHERE fonds='".$this->get("fonds")."'";
//          $db->SQL($query);
//          $soort = $db->lookupRecord();
//          if($soort['fondssoort']<>'OPT')
//            $this->setError("ISINCode", "Verplicht bij fix order voor '".$this->get("fonds")."' (fondssoort: '".$soort['fondssoort']."')");
//      }


      if ($this->get('beurs') == '')
      {
        $this->setError("beurs", vt("Verplicht voor fix order."));
      }

      if ($this->get('orderStatus') < 1)
      {
        if (empty($_GET['fonds_id']))
        {
          // $this->setError('ISINCode', 'Geen geldig fonds ingevoerd!');
        }
        else
        {
          $fondsObject = new Fonds();
          $fondsData = $fondsObject->parseById($_GET['fonds_id']);
          if (empty($fondsData))
          {
            $this->setError('ISINCode', vt('Geen geldig fonds ingevoerd!'));
          }
        }
      }

    }


//    ($this->get("depotbank") == "") ? $this->setError("depotbank", "Mag niet leeg zijn!") : true;
//    if ($this->get("depotbank") == '')
//    {
//      $portefeuille = '';
//
//      $db = new DB();
//      if ($_POST['portefeuille'] <> '')
//        $portefeuille = $_POST['portefeuille'];
//      elseif ($this->get('id') <> '')
//      {
//        $query = "SELECT portefeuille FROM OrderRegelsV2 WHERE orderid='" . $this->get('id') . "'";
//        $db->SQL($query);
//        $portefeuille = $db->lookupRecord();
//        $portefeuille = $portefeuille['portefeuille'];
//      }
//
//      if ( ! empty ($portefeuille) ) {
//        $query = "SELECT depotbank FROM Portefeuilles WHERE portefeuille='$portefeuille'";
//        $db->SQL($query);
//        $depotbank = $db->lookupRecord();
//        $this->set('depotbank', $depotbank['depotbank']);
//      }
//    }


    $valid = ($this->error == false)?true:false;

    return $valid;
  }

  function getOrderStatusOpties($vermogensbeheerderKeuze, $huidigeStatus)
  {
    global $__ORDERvar, $USR;
    if (is_array($vermogensbeheerderKeuze))
    {
      foreach ($vermogensbeheerderKeuze as $index => $checkData)
      {
        if ($checkData['checked'] == 1)
        {
          unset($__ORDERvar["status"][$index]);
        }
      }
    }

    $statusItems = count($__ORDERvar["orderStatus"]);
    $n = 0;
    foreach ($__ORDERvar["orderStatus"] as $index => $waarde)
    {
      if ($index == $huidigeStatus)
      {
        $indexHuidigeStatus = $n;
      }
      $indexLookup[$n] = $index;
      $n++;
    }
    $statusItems = count($indexLookup);

    $orderSoort = $this->get('OrderSoort');

    if (
      (
        $this->adviesRelatie === true
        && ( $this->adviesStatus != 5 && $this->adviesStatus != 0 )
      )
      && ($orderSoort === 'E' || $orderSoort === 'C' || $orderSoort === 'N' )
      && $this->orderregelObject->data['fields']['mailBevestigingVerzonden']['value'] == '0000-00-00 00:00:00'
      && $huidigeStatus < 1
    ) {
      $selectStatus = array(
        $indexLookup[$indexHuidigeStatus] => $__ORDERvar["orderStatus"][$indexLookup[$indexHuidigeStatus]],
        6 => $__ORDERvar["orderStatus"][6]
      );
    } elseif ( ( checkOrderAcces('orderVierOgen') === true && (int) $huidigeStatus < 1 ) && ($this->get('id') > 0 && $this->get('add_user') == $USR) )
    {
      if($huidigeStatus == -1)
        $selectStatus = array($indexLookup[$indexHuidigeStatus] => $__ORDERvar["orderStatus"][$indexLookup[$indexHuidigeStatus]],
                              0 => $__ORDERvar["orderStatus"][0],6 => $__ORDERvar["orderStatus"][6]);
      else
        $selectStatus = array(($indexLookup[$indexHuidigeStatus]) => $__ORDERvar["orderStatus"][$indexLookup[$indexHuidigeStatus]],6 => $__ORDERvar["orderStatus"][6]);
    }
    elseif ( checkOrderAcces ('handmatig_volgendeStatus') === false ) {
//        $selectStatus = array($indexLookup[$indexHuidigeStatus] => $__ORDERvar["orderStatus"][$indexLookup[$indexHuidigeStatus]]);
      if($huidigeStatus == -1) {
        $selectStatus = array(
          $indexLookup[$indexHuidigeStatus] => $__ORDERvar["orderStatus"][$indexLookup[$indexHuidigeStatus]],
          0 => $__ORDERvar["orderStatus"][0],
          6 => $__ORDERvar["orderStatus"][6]
        );
      } else {
        $selectStatus = array(
          $indexLookup[$indexHuidigeStatus] => $__ORDERvar["orderStatus"][$indexLookup[$indexHuidigeStatus]],
          6 => $__ORDERvar["orderStatus"][6]
        );
      }
    } elseif ($indexHuidigeStatus < $statusItems)//&& $action == 'edit'
    {
      if ($huidigeStatus < 2)
      {
        $selectStatus = array(($indexLookup[$indexHuidigeStatus])     => $__ORDERvar["orderStatus"][$indexLookup[$indexHuidigeStatus]],
                              ($indexLookup[$indexHuidigeStatus + 1]) => $__ORDERvar["orderStatus"][$indexLookup[$indexHuidigeStatus + 1]],
                              ($indexLookup[$statusItems - 3])        => $__ORDERvar["orderStatus"][$indexLookup[$statusItems - 3]],
                              ($indexLookup[$statusItems - 2])        => $__ORDERvar["orderStatus"][$indexLookup[$statusItems - 2]],
                              ($indexLookup[$statusItems - 1])        => $__ORDERvar["orderStatus"][$indexLookup[$statusItems - 1]]);
      }
      elseif ($huidigeStatus > 3)
      {
        $selectStatus = array($indexLookup[$indexHuidigeStatus] => $__ORDERvar["orderStatus"][$indexLookup[$indexHuidigeStatus]]);
      }
      else
      {
        $selectStatus = array(($huidigeStatus)     => $__ORDERvar["orderStatus"][$huidigeStatus],
                              ($huidigeStatus + 1) => $__ORDERvar["orderStatus"][$huidigeStatus + 1]);
      }
    }

    return $selectStatus;
  }

  /**
   * Order clonen
   */
  function cloneOrder ($data, $copyNewBatchId)
  {
    $orderLogs = new orderLogs();

    $ordersObj = new OrdersV2();
    $ordersEditObject = new editObject($ordersObj);
    $ordersEditObject->controller('edit', $data);

    $real_add_user=$ordersObj->get('add_user');
    $db = new DB();

    if($data['copyid'] > 0)
    {
      $ordersObj->set('id', 0);
      $ordersObj->set('batchId', $copyNewBatchId);
      if ( isset ($data['OrderSoort']) ) {
        $ordersObj->set('OrderSoort', $data['OrderSoort']);
      }
      $ordersObj->set('orderStatus', -1);
      $ordersObj->set('fixVerzenddatum', '');

      $ordersObj->set('fixAnnuleerdatum', '');
      $ordersObj->set('notaValutakoers', '');

      $ordersObj->save();
      $orderLogs->addToLog($ordersObj->get('id'), null, 'Order gekopieerd van order:'.$data['id'], '', '', 5, '');
    }

    $orderRegelDB = new DB();
    $query="SELECT SUM(uitvoeringsaantal) as uitvoeringsaantal,SUM(uitvoeringsprijs*uitvoeringsaantal) as uitvoeringswaarde FROM OrderUitvoeringV2 WHERE orderid='". $data['id']."'";
    $orderRegelDB->executeQuery($query);
    $huidigeUitvoeringen=$orderRegelDB->nextRecord();

    $copyOrderRegelQuery = "SELECT id,aantal,bedrag FROM `OrderRegelsV2` WHERE `orderid`= '" . $data['id'] . "'";
    $orderRegelDB->executeQuery($copyOrderRegelQuery);
    $copyRegels=array();
    $totaalAantal=0;
    $totaalBedrag=0;
    while ( $copyOrderRegelData = $orderRegelDB->nextRecord() )
    {
      $copyRegels[$copyOrderRegelData['id']] = $copyOrderRegelData;
      $totaalAantal+=$copyOrderRegelData['aantal'];
      $totaalBedrag+=$copyOrderRegelData['bedrag'];
    }

    if(isset($data['remove']))
      $aantalTeller = $totaalAantal-$huidigeUitvoeringen['uitvoeringsaantal'];
    elseif(isset($data['toAdd']))
      $aantalTeller = $data['toAdd'];
    else
      $aantalTeller=$totaalAantal;

    if($totaalAantal==0)
      $verdeling=$huidigeUitvoeringen['uitvoeringswaarde']/$totaalBedrag;
    else
      $verdeling=$aantalTeller/$totaalAantal;

    if(round($totaalAantal)==$totaalAantal)
      $afronding=0;
    else
      $afronding=4;
    $aantalRegels=count($copyRegels);
    $n=0;

    foreach($copyRegels as $id=>$aantalData)
    {
      $n++;
      $newAantal=round($aantalData['aantal']*$verdeling,$afronding);
      if($aantalTeller-$newAantal > 0)
        $aantalTeller-=$newAantal;
      elseif($aantalTeller >0)
      {
        $newAantal = $aantalTeller;
        $aantalTeller=0;
      }
      else
        $newAantal=0;
      if($aantalRegels==$n && $aantalTeller>0)
        $newAantal=$aantalTeller;

      $copyOrderRegelObj = new OrderRegelsV2();
      $copyOrderRegelObj->getById($id);
      if($huidigeUitvoeringen['uitvoeringsaantal'] <> 0) // bij annulering opnieuw inleggen.
      {
        $oudeAaantal = $copyOrderRegelObj->get('aantal');
        $copyOrderRegelObj->set('aantal', $oudeAaantal - $newAantal);
        $copyOrderRegelObj->set('bedrag', round($aantalData['bedrag'] * $verdeling, 2));
        $copyOrderRegelObj->save();
        $orderLogs->addToLog($data['id'], null, 'Order regel ' . $copyOrderRegelObj->get('positie') . ' aantal aangepast: ' . $oudeAaantal . '->' . ($oudeAaantal - $newAantal) . ' (deeluitvoering)', '', '', 5, '');
      }
      if($data['copyid'] > 0)
      {
        $copyOrderRegelObj->set('id', 0);
        $copyOrderRegelObj->set('controleRegels', '');
        $copyOrderRegelObj->set('controleStatus', 0);
        $copyOrderRegelObj->set('orderregelStatus', 0);
        $copyOrderRegelObj->set('orderid', $ordersObj->get('id'));

        // Bij nominaal en meervoudig nominaal geen aantal en bedrag berekenen
        if ( ! in_array($ordersObj->get('OrderSoort'), array('N','O')) ) {
          $copyOrderRegelObj->set('aantal', $newAantal);
          $copyOrderRegelObj->set('bedrag', round($aantalData['bedrag'] * $verdeling, 2));
        }

        $copyOrderRegelObj->set('notaDefinitief', '');
        $copyOrderRegelObj->set('printDate', '');
        $copyOrderRegelObj->set('regelNotaValutakoers', '');
        $copyOrderRegelObj->set('mailBevestigingVerzonden', '');
        $copyOrderRegelObj->set('mailBevestigingData', '');

        $copyOrderRegelObj->orderRegelUpdate();
        $orderLogs->addToLog($ordersObj->get('id'), null, 'Order regel ' . $copyOrderRegelObj->get('positie') . ' aangemaakt vanuit kopie', '', '', 5, '');
        
        $db = new DB();
        $updateOrgineelQuery = "Update `OrderRegelsV2` set `kopieOrderId` = '".$copyOrderRegelObj->get('id')."' WHERE `id` = '" . $id . "' ";
        $db->executeQuery($updateOrgineelQuery);
      }
    }

    if($data['copyid'] > 0)
    {
      $queries=array("UPDATE OrdersV2 SET add_user='".$real_add_user."' WHERE id='".$ordersObj->get('id')."'",
        "UPDATE OrderRegelsV2 SET add_user='".$real_add_user."' WHERE orderid='".$ordersObj->get('id')."'" );
      foreach($queries as $query)
        $db->executeQuery($query);
      $ordersObj->set('orderStatus', 0);
      $ordersObj->save();
      $orderLogs->addToLog($data['id'], null, 'Order gekopieerd naar order:'.$ordersObj->get('id'), '','',5,'');
    }

    return $ordersObj->get('id');

  }

  function getStandaard()
  {
    global $USR;

    $query = "SELECT 
  (Vermogensbeheerders.Vermogensbeheerder) as Vermogensbeheerder,
  (Vermogensbeheerders.OrderStandaardType) as OrderStandaardType, 
  (Vermogensbeheerders.OrderStandaardMemo) as OrderStandaardMemo , 
  (Vermogensbeheerders.OrderStandaardTijdsSoort) as OrderStandaardTijdsSoort, 
  (Vermogensbeheerders.OrderStatusKeuze) as OrderStatusKeuze, 
  (Vermogensbeheerders.OrderOrderdesk) as OrderOrderdesk, 
  max(Vermogensbeheerders.orderredenVerplicht) as orderredenVerplicht,
  (Vermogensbeheerders.OrderuitvoerBewaarder) as OrderuitvoerBewaarder
  FROM Vermogensbeheerders
  Inner Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
  WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR' 
  GROUP BY VermogensbeheerdersPerGebruiker.Gebruiker 
  order by Vermogensbeheerder limit 1
  
  ";
    $db = new DB();
    $db->SQL($query);
    $standaard = $db->lookupRecord();

    return $standaard;
  }

  function setStandaard($standaard)
  {
    $soort = '';
    if (isset($_GET['orderSelectieType']) && $_GET['orderSelectieType'] <> '')
    {
      $soort = $_GET['orderSelectieType'];
    }
    elseif (isset($_POST['orderSelectieType']) && $_POST['orderSelectieType'] <> '')
    {
      $soort = $_POST['orderSelectieType'];
    }
    elseif ($standaard['OrderStandaardType'] == 0)
    {
      $soort = 'M';
    }
    elseif ($standaard['OrderStandaardType'] == 1)
    {
      $soort = 'E';
    }
    elseif ($standaard['OrderStandaardType'] == 2)
    {
      $soort = 'C';
    }
    if (!isset($_GET['batchId']) && $_GET['action'] == 'new')
    {
      $cfg = new AE_config();
      $newBatchId = $cfg->getData('lastOrderBatchId') + 1;
      $cfg->addItem('lastOrderBatchId', $newBatchId);
      $_GET['batchId'] = $newBatchId;
    }
    // listarray($_POST);
    // echo $soort;exit;
    if ($soort == '')
    {
      $db = new DB();
      $query = "SELECT batchId FROM OrdersV2 WHERE id = '" . $_GET['id'] . "' AND batchId > 0 ORDER BY id";
      $db->SQL($query);
      $batchId = $db->lookupRecord();
      $query = "SELECT id FROM OrdersV2 WHERE batchId = '" . $batchId['batchId'] . "' AND batchId > 0 ORDER BY id";
      if ($db->QRecords($query) > 1)
      {
        if ($soort == '')
        {
          $soort = 'C';
        }
        $noInsert = true;
      }
      $query = "SELECT OrderRegelsV2.* FROM OrdersV2 JOIN OrderRegelsV2 ON OrderRegels.orderid = Orders.id WHERE Orders.id = '" . $_GET['id'] . "'";
      if ($db->QRecords($query) == 1)
      {
        if ($soort == '')
        {
          $soort = 'E';
        }
        $regel = $db->nextRecord();
        foreach ($regel as $key => $value)
        {
          if ($key == 'id')
          {
            $orderregelId = $value;
          }
          if ($_GET[$key] == '' && !in_array($key, array('add_date', 'add_user', 'change_date', 'change_user', 'id')))
          {
            $_GET[$key] = $value;
          }
        }
        $noInsert = true;
      }
    }

    $this->setOption('OrderSoort', 'default_value', $soort);
    $this->setOption('tijdsSoort', 'default_value', $standaard['OrderStandaardTijdsSoort']); //DAT
    $this->setDefaults();

  }

  /*
   * Toegangscontrole
   */

  function checkAccess($type = '')
  {
    if ($type == "delete")
      return false;
    else
      return true;
  }

  function annuleerFix()
  {
    global $USR;
    include_once("../classes/AE_cls_FIXtransport.php");

    $fixOrder = new FixOrders();
    $fixOrder->getByField('AIRSorderReference', $this->get('id'));
    if ($fixOrder->get('orderid'))
    {
      $cancel = array("typeBericht" => "del", 'ordernr' => $fixOrder->get('orderid'), "user" => $USR);
      $fix = new AE_FIXtransport();
      if ($fix->addToQueue($cancel))
      {
        $fixOrder->set('laatsteStatus', '6'); //pending cancel. Normaal komt deze ook via fix bericht maar mocht deze niet komen dan staat de fixorder al op pending cancel.
        $fixOrder->save();
        $this->set('fixAnnuleerdatum', date('Y-m-d H:i:s'));
        $this->save();
        $fix->orderLogs->addToLog($this->get('id'), $fixOrder->get('orderid'), "Annuleer verzoek verzonden.");
      }
    }
    else
    {
      /** bij status 6 is de order al geannulleerd  */
      if ($this->get('orderStatus') != 6)
      {
        echo "Order (nog) niet kunnen annuleren. Nog geen fixorder id aanwezig.";
        exit;
      }
    }
  }

  function verzendFix()
  {
    global $USR, $__appvar;
    include_once("../classes/AE_cls_FIXtransport.php");
    $db = new DB();
    $fix = new AE_FIXtransport();
    logIt("verzendFix 1.) Begin verzendFix voor ".$this->get('id'));
    if ($this->get('OrderSoort') == 'M' || $this->get('OrderSoort') == 'O' || $this->get('OrderSoort') == 'X')
    {
      $query = "SELECT id,portefeuille,rekening,aantal,bedrag FROM OrderRegelsV2 WHERE orderid='" . $this->get('id') . "'";
      $db->SQL($query);
      $db->Query();
      $bulk = array();
      $totalen = array();
      while ($data = $db->nextRecord())
      {
        $data['portefeuille']=$fix->getDepotbankPortefeuille($data['rekening'],$data['portefeuille']);
        $bulk[] = array('portefeuille' => $data['portefeuille'], 'aantal' => $data['aantal'], 'rekening' => $data['rekening'], 'bedrag' => round($data['bedrag'],2));
        $totalen['aantal'] += $data['aantal'];
        $totalen['bedrag'] += $data['bedrag'];
        $totalen['ids'][]=$data['id'];
      }
      $orderregelData = array("portefeuille" => '', "rekening" => '', "client" => '', "aantal" => $totalen['aantal'], "bedrag" => round($totalen['bedrag'],2));
      if (count($bulk) == 0)
      {
        echo "Geen orderregels beschikbaar. Verzend verzoek afgebroken.";
        listarray($this);
        exit;
      }
    }
    else
    {
      if (!is_object($this->orderregelObject) && $this->get('id') > 0)
      {
        $this->orderregelObject = new OrderRegelsV2();
        $this->orderregelObject->getByField('orderid', $this->get('id'));
      }
      if (!is_object($this->orderregelObject))
      {
        echo "Geen orderregel informatie beschikbaar. Verzend verzoek afgebroken.";
        listarray($this);
        exit;
      }
      $orderregelData = array("portefeuille" => $this->orderregelObject->get('portefeuille'),
                              "rekening"     => $this->orderregelObject->get('rekening'),
                              "client"       => $this->orderregelObject->get('client'),
                              "aantal"       => $this->orderregelObject->get('aantal'),
                              "bedrag"       => round($this->orderregelObject->get('bedrag'),2));
    }
    if ($this->get('transactieType') == 'L')
    {
      $fixTypeTransactie = 'lim';
    }
    else
    {
      $fixTypeTransactie = 'mkt';
    }
    
    if (substr($this->get('transactieSoort'), 0, 1) == 'V')
    {
      $fixTransactieSoort = 'sell';
    }
    else
    {
      $fixTransactieSoort = 'buy';
    }
    logIt("verzendFix 2.) Orderregels opgehaald voor ".$this->get('id'));
    $datum = $this->get('tijdsLimiet');
    $optieCode = $fix->getOptiecode($this->get('depotbank'), $this->get('fonds'), $this->get('transactieSoort'), $this);
    $fonds = new Fonds();
    $fonds->getByField('Fonds', $this->get('fonds'));
    logIt("verzendFix 3.) Orderregels getOptiecode ".$this->get('id'));
  
    $beurs=$this->get('beurs');
    $valuta = $this->get('fondsValuta');
    if ($valuta == '')
    {
      $valuta = $fonds->get('Valuta');
    }

    $fondssoort = $fonds->get('fondssoort');

    $legs = '';
    $no_legs = '';
    if (is_array($optieCode) && count($optieCode) > 0)
    {
      $legs = array($optieCode['leg']);
      $no_legs = count($legs);
    }

/*  
    if($fondssoort<>'' && $fondssoort=='OPT')
    {
      $afbreken=false;
      $veldenCheck=array('symbol','expiry','strike','leg_type','oc');
      foreach($veldenCheck as $veld)
      {
        if($optieCode[$veld]=='')
        {
          echo "Voor optie ".$this->get('fonds')." ontbreekt een waarde voor $veld .<br>\n";
          $afbreken=true;
        }
      }
      if($afbreken==true)
      {
        exit;
      }
    }
*/
    $fondsInfo=new FondsExtraInformatie();
    $fondsInfo->getByField('fonds',$this->get('fonds'));
    $Belfnds_FIX=$fondsInfo->get('Belfnds_FIX');
    if($Belfnds_FIX==1)
    {
      if(in_array($this->get('depotbank'),array('UBS','UBSL')))
      {
        if ($fixTransactieSoort == 'buy')
        {
          $fixTransactieSoort = 'subscription';
        }
        elseif ($fixTransactieSoort == 'sell')
        {
          $fixTransactieSoort = 'redemption';
        }
      }
      elseif(in_array($this->get('depotbank'),array('LOM')))
      {
        $beurs='XXXX';
      }
      logIt("verzendFix 3.1) Belfnds_FIX: $Belfnds_FIX, transactiesoort: $fixTransactieSoort, beurs: $beurs");
    }
    
    if($this->get('depotbank')=='BIN'||$this->get('depotbank')=='BINB')
    {
      $fixBeurs = $fix->getBeurs($this->get('depotbank'), $this->get('fonds'));
      $fixValuta = $fix->getFondsValuta($this->get('depotbank'), $this->get('fonds'));
  
      if($fixBeurs<>'')
        $this->set('beurs',$fixBeurs);
      if($fixValuta<>'')
        $valuta=$fixValuta;
    }
    logIt("verzendFix 4.) depot beurs en valuta ".$this->get('id'));
    
    $push='';
    $memo=$this->get('memo');
    if(substr($memo,0,6)=='push=1')
    {
      $push='Y';
    }
    
    $order = array(
      "typeBericht"        => "new",
      "depotbank"          => $this->get('depotbank'),//"TGB",
      "vermogenbeheerder"  => $__appvar['bedrijf'], //"DGC",
      "portefeuille"       => $orderregelData['portefeuille'],
      "rekening"           => $orderregelData['rekening'],
      "client"             => $orderregelData['client'],
      "valuta"             => $valuta,
      "ISIN"               => $this->get('ISINCode'),
      "fondssoort"         => $fondssoort,
      "beurs"              => $beurs,//XXXX Mag voor TGB leeg zijn.
      "AIRSfondsCode"      => $this->get('fonds'),
      "AIRSfondsOms"       => $this->get('fondsOmschrijving'),
      "bankCode"           => $fix->getFondscode($this->get('depotbank'), $this->get('fonds')), //"1005570",
      "typeTransactie"     => $fixTypeTransactie,
      "transactieSoort"    => $fixTransactieSoort,
      "aantal"             => $orderregelData['aantal'],
      "bedrag"             => round($orderregelData['bedrag'],2),
      "tijdsSoort"         => $this->get('tijdsSoort'),
      "limietKoers"        => $this->get('koersLimiet'),
      "careOrder"          => $this->get('careOrder'),
      "limietDatum"        => str_replace("-", "", substr($datum, 0, 10)),
      "AIRSorderReference" => $this->get('id'),
      "AIRSorderRecordId"  => $this->get('id'),
      "no_legs"            => $no_legs,
      "push_fiat"          => $push,
      "depotbankPortefeuille"  => $fix->getDepotbankPortefeuille($orderregelData['rekening'],$orderregelData['portefeuille']),
      "legs"               => $legs,
      "bulk"               => $bulk,
      "Belfnds_FIX"        => $Belfnds_FIX,
      "user"               => $USR);
    //listarray($order);exit;


    $query="SELECT date(fixVerzenddatum) as fixVerzenddatum FROM OrdersV2 WHERE id='". $this->get('id')."'";
    $db->SQL($query);
    $verzendDatum=$db->lookupRecord();
    $verzonden=false;
    if ($verzendDatum['fixVerzenddatum']=='0000-00-00')
    {
      if($fix->addToQueue($order))
      {
        logIt("verzendFix 5.) order addToQueue ".$this->get('id'));
        $lastId = $fix->addToAirs($order);
        $fix->orderLogs->addToLog($this->get('id'), $lastId, "Verzonden naar fix queue.");
        $this->set('fixVerzenddatum', date('Y-m-d H:i:s'));
        $this->set("orderStatus", 1);
       
        $cfg = new AE_config();
        $cfg->addItem('lastFixOrder', time());
        if ($this->get('OrderSoort') == 'M' || $this->get('OrderSoort') == 'O')
        {
          $query="UPDATE OrderRegelsV2 SET orderregelStatus=1,change_date=now() WHERE id IN('".implode("','",$totalen['ids'])."')";
          $db->SQL($query);
          $db->Query();
        }
        else
        {
          $this->orderregelObject->set('orderregelStatus',1);
        }
        $verzonden=true;
      }
      else
      {
        $fix->orderLogs->addToLog($this->get('id'), '', 'Order kon niet worden verzonden naar de fix queue.');
        $fix->orderLogs->addToLog($this->get('id'), '', 'orderStatus terug gezet naar ingevoerd .');
        $this->set("orderStatus", 0);
        if ($this->get('OrderSoort') == 'M' || $this->get('OrderSoort') == 'O')
        {
          $query="UPDATE OrderRegelsV2 SET orderregelStatus=0,change_date=now() WHERE id IN('".implode("','",$totalen['ids'])."')";
          $db->SQL($query);
          $db->Query();
        }
        else
        {
          $this->orderregelObject->set('orderregelStatus',0);
        }
      }
      if($this->get('change_user') == $USR && (time()-db2jul($this->get('change_date')) < 1800))
      {
        $skipOrderControle=true;
        logIt("verzendFix 5.1) skipOrderControle: $skipOrderControle | ".$this->get('change_user')." == $USR && (".(time()-db2jul($this->get('change_date')))." < 1800))");
      }
      else
      {
        $skipOrderControle=false;
      }
      
      $this->skipOrderControle=$skipOrderControle;
      logIt("verzendFix 6.) order opslaan ".$this->get('id'));
      $this->save();
      if ($this->get('OrderSoort') <> 'M' && $this->get('OrderSoort') <> 'O')
      {
        $this->orderregelObject->skipOrderControle=$skipOrderControle;
        $this->orderregelObject->save();
      }
      logIt("verzendFix 7.) Klaar met Order ".$this->get('id')."  $verzonden");
    }
    return $verzonden;
  }
  
  function testForSpeedup()
  {
    $txt = "try{parent.kaInterval=3;parent.checkForSpeedup();}catch(err) { alert(err);}";

    return $txt;
  }


  function OrderValidatie($orderId)
  {
    $this->orderregelObject = new OrderRegelsV2();
    $db=new db();
    $query="SELECT id FROM OrderRegelsV2 WHERE orderid='".$orderId."'";
    $db->SQL($query);
    $db->Query();
    $controleStatus=0;
    $regels=array();
    while($data=$db->nextRecord())
    {
      $this->orderregelObject->getById($data['id']);
      $this->orderregelObject->orderControlle();
      $lastCheck=$this->orderregelObject->get('controleStatus');
      if($lastCheck<>0)
        $regels[]=$data['id'];
      if($lastCheck>$controleStatus)
        $controleStatus=$lastCheck;
      $this->orderregelObject->save();
    }
    return array('controleStatus'=>$controleStatus,'orderRegelIds'=>$regels,'recheck'=>$this->orderregelObject->data['orderData']['recheck'],'maxCheck'=>$this->orderregelObject->data['orderData']['maxCheck']);
  }

  function updateOrderregelStatus()
  {
    if($this->get('id') > 0)
    {
      $db=new DB();
      $query="UPDATE OrderRegelsV2 SET orderregelStatus='".$this->get('orderStatus') ."',change_date=now() WHERE orderid='".$this->get('id')."'";
      $db->SQL($query);
      $db->Query();
    }
  }
  /*
   * Table definition
   */

  function defineData()
  {
    global $__ORDERvar, $USR;
    $this->data['table'] = "OrdersV2";
    $this->data['identity'] = "id";


    $query = "SELECT  Vermogensbeheerders.OrderStandaardTransactieType
     FROM Vermogensbeheerders
     Inner Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
     WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR' limit 1";
    $db = new DB();
    $db->SQL($query);
    $standaard = $db->lookupRecord();

    $this->addField('id', array("description"   => "id",
      "default_value" => "",
      "db_size"       => "11",
      "db_type"       => "int",
      "form_type"     => "text",
      "form_size"     => "11",
      "form_visible"  => false, "list_width"    => "100",
      "list_visible"  => true,
      "list_align"    => "right",
      "list_search"   => false,
      "list_order"    => "true"));

    $this->addField('ISINCode', array("description"   => "ISINCode",
      "default_value" => "",
      "db_size"       => "26",
      "db_type"       => "varchar",
      "form_type"     => "text",
      "form_size"     => "16",
      "form_visible"  => true,
      "list_visible"  => true,
      "list_width"    => "120",
      "list_align"    => "left",
      "list_search"   => false,
      "list_order"    => "true"));

    $this->addField('fonds', array("description"   => "fonds",
      "default_value" => "",
      "db_size"       => "50",
      "db_type"       => "varchar",
      "form_type"     => "text",
      "form_size"     => "50",
      "form_visible"  => false,
      "list_width"    => "150",
      "list_visible"  => true,
      "list_align"    => "left",
      "list_search"   => false,
      "list_order"    => "true",
      "keyIn"         => "Fondsen",
      "validate"      => array(
        'required' => true,
        'empty'    => false
    )));

    $this->addField('depotbank', array("description"                 => "depotbank",
                                       "default_value"               => "",
                                       "db_size"                     => "15",
                                       "db_type"                     => "varchar",
                                       "form_type"                   => "selectKeyed",
                                       "form_select_option_notempty" => true,
                                       "form_size"                   => "15",
                                       "form_visible"                => true,
                                       "list_width"                  => "30",
                                       "list_visible"                => true,
                                       "list_align"                  => "left",
                                       "list_search"                 => false,
                                       "list_order"                  => "true",
                                       "categorie"=>"Algemeen"));
		$this->addField('fondseenheid',
													array("description"=>"Fondseenheid",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_size"=>"8",
													"form_type"=>"text",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
                                "categorie"=>"Algemeen"));

		$this->addField('fondsValuta',
													array("description"=>"Valuta",
													"db_size"=>"4",
													"db_type"=>"char",
													"form_size"=>"4",
													"form_type"=>"selectKeyed",
                          "select_query" => "SELECT Valuta,Valuta FROM Valutas ORDER BY Valuta",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Valutas",
                                "categorie"=>"Algemeen"));

		$this->addField('fondsBankcode',
													array("description"=>"fondsBankcode",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_size"=>"12",
													"form_type"=>"varchar",
													"form_visible"=>true,"list_width"=>"150",
                                                    "default_value"=>'',
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "categorie"=>"Algemeen"));

		$this->addField('optieSymbool',
													array("description"=>"Symbool",
													"db_size"=>"5",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"value"=>"",
                                "categorie"=>"Algemeen"));

		$this->addField('optieType',
													array("description"=>"[P]ut/[C]all",
													"db_size"=>"1",
													"db_type"=>"varchar",
													"form_options"=>array('P','C'),
													"form_type"=>"select",
													"form_size"=>"1",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
                                "categorie"=>"Algemeen"));

		$this->addField('optieUitoefenprijs',
													array("description"=>"Uitoefenprijs",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,"list_width"=>"150",
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
                                "categorie"=>"Algemeen"));

		$this->addField('optieExpDatum',
													array("description"=>"Expiratie datum",
													"db_size"=>"6",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
                                "categorie"=>"Algemeen"));

    $this->addField('fondsOmschrijving', array("description"   => "fonds omschrijving",
      "default_value" => "",
      "db_size"       => "50",
      "db_type"       => "varchar",
      "form_type"     => "text",
      "form_size"     => "30",
      "form_visible"  => true,
      "list_visible"  => true,
      "list_width"    => "150",
      "list_align"    => "left",
      "list_search"   => true,
      "list_order"    => "true",
      "form_extra"    => 'onChange="javascript:fondsOmschrijvingChange();"',
      "validate"      => array(
        'required' => true,
        'empty'    => false
    ),
                                               "categorie"=>"Algemeen"));

    $this->addField('transactieType', array("description"                 => "transactieType",
      "default_value"               => $standaard['OrderStandaardTransactieType'],
      "db_size"                     => "4",
      "db_type"                     => "varchar",
      "form_type"                   => "selectKeyed",
      "form_options"                => $__ORDERvar['transactieType'],
      "form_select_option_notempty" => false,
      "form_size"                   => "4",
      "form_visible"                => true,
      "list_visible"                => true,
      "list_width"                  => "150",
      "list_align"                  => "left",
      "list_search"                 => false,
      "list_order"                  => "true",
      "validate"                    => array(
        'required' => true,
        'empty'    => false
    ),
                                            "categorie"=>"Algemeen"));

    $this->addField('transactieSoort', array("description"                 => "transactieSoort",
      "default_value"               => '',
      "db_size"                     => "2",
      "db_type"                     => "char",
      "form_type"                   => "selectKeyed",
      "form_options"                => $__ORDERvar['transactieSoort'],
      "form_select_option_notempty" => false,
      "form_size"                   => "2",
      "form_visible"                => true,
      "list_visible"                => true,
      "list_width"                  => "150",
      "list_align"                  => "left",
      "list_search"                 => false,
      "list_order"                  => "true",
                                             "categorie"=>"Algemeen"));

    $this->addField('tijdsLimiet', array("description"   => "Datum",
      "default_value" => "",
      "db_size"       => "0",
      "db_type"       => "date",
      "form_type"     => "calendar",
      "form_class"    => "AIRSdatepicker",
      "form_extra"    => " onchange=\"date_complete(this);\"",
      "form_size"     => "0",
      "form_visible"  => true,
      "list_visible"  => true,
      "list_width"    => "150",
      "list_align"    => "left",
      "list_search"   => false,
      "list_order"    => "true",
                                         "categorie"=>"Algemeen"));

    $this->addField('tijdsSoort', array("description"                 => "soort tijdlimiet",
      "default_value"               => "",
      "db_size"                     => "15",
      "db_type"                     => "varchar",
      "form_type"                   => "selectKeyed",
      "form_options"                => $__ORDERvar["tijdsSoort"],
      "form_select_option_notempty" => true,
      "form_size"                   => "15",
      "form_visible"                => true,
      "list_visible"                => true,
      "list_align"                  => "left",
      "list_width"                  => "150",
      "list_search"                 => false,
      "list_order"                  => "true",
                                        "categorie"=>"Algemeen"));

    $this->addField('koersLimiet', array("description"       => "koersLimiet",
      "default_value"     => "0.000",
      "db_size"           => "12,3",
      "db_type"           => "double",
      "form_type"         => "text",
      "form_size"         => "12,3",
      "form_visible"      => true,
      "list_visible"      => true,
      "list_width"        => "150",
      "list_format"       => "%01.3f",
      "list_numberformat" => 2,
      "list_align"        => "right",
      "list_search"       => false,
      "list_order"        => "true",
                                         "categorie"=>"Algemeen"));

    $this->addField('orderStatus', array("description"                 => "status",
      "default_value"               => "",
      "db_size"                     => "15",
      "db_type"                     => "varchar",
      "form_type"                   => "selectKeyed",
      "form_options"                => $__ORDERvar['orderStatus'],
      "form_select_option_notempty" => true,
      "form_size"                   => "15",
      "form_visible"                => true,
      "list_visible"                => true,
      "list_width"                  => "100",
      "list_align"                  => "left",
      "list_search"                 => false,
      "list_order"                  => "true"));

    $this->addField('orderSubStatus', array("description"                 => "subStatus",
      "default_value"               => "",
      "db_size"                     => "2",
      "db_type"                     => "varchar",
      "form_type"                   => "text",
      "form_size"                   => "15",
      "form_visible"                => true,
      "list_visible"                => true,
      "list_width"                  => "100",
      "list_align"                  => "left",
      "list_search"                 => false,
      "list_order"                  => "true"));
      
    $this->addField('memo', array("description"   => "memo",
      "default_value" => "",
      "db_size"       => "60",
      "db_type"       => "text",
      "form_type"     => "textarea",
      "form_size"     => "35",
      "form_rows"     => "6",
      "form_visible"  => true,
      "list_width"    => "100",
      "list_visible"  => true,
      "list_align"    => "left",
      "list_search"   => false,
      "list_order"    => "true",
                                  "categorie"=>"Algemeen"));


    $this->addField('add_date', array("description"  => "add_date",
      "db_size"      => "0",
      "db_type"      => "datetime",
      "form_type"    => "datum",
      "form_visible" => true,
      "list_visible" => true,
      "list_align"   => "right",
      "list_search"  => false,
      "list_order"   => "true",
                                      "categorie"=>"Algemeen"));

    $this->addField('add_user', array("description"   => "add_user",
      "default_value" => "",
      "db_size"       => "10",
      "db_type"       => "varchar",
      "form_type"     => "text",
      "form_size"     => "10",
      "form_visible"  => false,
      "list_visible"  => true,
      "list_align"    => "left",
      "list_search"   => false,
      "list_order"    => "true"));

    $this->addField('change_date', array("description"   => "change_date",
      "default_value" => "",
      "db_size"       => "0",
      "db_type"       => "datetime",
      "form_type"     => "datum",
      "form_size"     => "0",
      "form_visible"  => false,
      "list_visible"  => true,
      "list_align"    => "left",
      "list_search"   => false,
      "list_order"    => "true",
                                         "categorie"=>"Algemeen"));

    $this->addField('change_user', array("description"   => "change_user",
      "default_value" => "",
      "db_size"       => "10",
      "db_type"       => "varchar",
      "form_type"     => "text",
      "form_size"     => "10",
      "form_visible"  => false,
      "list_visible"  => true,
      "list_align"    => "left",
      "list_search"   => false,
      "list_order"    => "true"));

    $this->addField('batchId', array("description"   => "batchId",
      "default_value" => "",
      "db_size"       => "11",
      "db_type"       => "int",
      "form_type"     => "text",
      "form_size"     => "11",
      "form_visible"  => false, "list_width"    => "100",
      "list_visible"  => true,
      "list_align"    => "right",
      "list_search"   => false,
      "list_order"    => "true"));

    $this->addField('OrderSoort', array("description"   => "Soort order",
      "default_value" => "",
      "db_size"       => "1",
      "db_type"       => "varchar",
      "form_type"     => "radio",
      "form_options"  => array('M' => 'Meervoudig (1 instrument; meerdere portefeuilles)', 'E' => 'Enkelvoudig (1 poretefeuille; 1 instrument)', 'C' => 'Combinatie (1 portefeuille; meerdere instrumenten)',
                               'N'=>'Nominaal Enkelvoudig','O'=>'Nominaal Meervoudig','F'=>'FX-transacties Enkelvoudig','X'=>'FX-transacties Meervoudig'),
      "form_size"     => "1",
      "form_extra"    => "",
      "form_visible"  => true,
      "list_visible"  => true,
      "list_width"    => "120",
      "list_align"    => "left",
      "list_search"   => false,
      "list_order"    => "true"));

    $this->addField('giraleOrder', array("description"  => "Girale order",
      "db_size"      => "4",
      "db_type"      => "tinyint",
      "form_type"    => "checkbox",
      "form_extra"   => 'onChange="javascript:setAantal();"',
      "form_visible" => true,
      "list_visible" => false,
      "list_align"   => "center",
      "list_search"  => false,
      "list_order"   => "true",
                                         "categorie"=>"Algemeen"));

    $this->addField('beurs',	array("description"=>"Beurs",
			"db_size"      => "4",
			"db_type"      => "varchar",
			"form_size"    => "12",
			"form_type"    => "selectKeyed",
			"select_query" => "SELECT Beurs, CONCAT(Omschrijving, ' (', beurs, ')') as Omschrijving FROM Beurzen order by `Omschrijving` ASC",
			"form_visible" => true,
      "list_width"   => "150",
			"list_visible" => true,
			"list_align"   => "left",
			"list_search"  => false,
			"list_order"   => "true",
			"keyIn"        => "Beurzen",
                                    "categorie"=>"Algemeen"));
      
    $this->addField('fixOrder', array("description"  => "fix order",
      "db_size"      => "4",
      "db_type"      => "tinyint",
      "form_type"    => "checkbox",
      "form_extra"   => '',
      "form_visible" => true,
      "list_visible" => true,
      "list_align"   => "center",
      "list_search"  => false,
      "list_order"   => "true"));

    $this->addField('careOrder', array("description"  => "care-order",
                                      "db_size"      => "4",
                                      "db_type"      => "tinyint",
                                      "form_type"    => "checkbox",
                                      "form_extra"   => '',
                                      "form_visible" => true,
                                      "list_visible" => true,
                                      "list_align"   => "center",
                                      "list_search"  => false,
                                      "list_order"   => "true"));
  
  

    
    $this->addField('fixVerzenddatum', array("description"  => "fix verzonden",
      "db_size"      => "4",
      "db_type"      => "datetime",
      "form_type"    => "datum",
      "form_extra"   => '',
      "form_visible" => true,
      "list_visible" => true,
      "list_align"   => "left",
      "list_search"  => false,
      "list_order"   => "true",
      "categorie"=>"Algemeen"));
    $this->addField('fixAnnuleerdatum', array("description"  => "fix geannuleerd",
      "db_size"      => "4",
      "db_type"      => "datetime",
      "form_type"    => "datum",
      "form_extra"   => '',
      "form_visible" => true,
      "list_visible" => true,
      "list_align"   => "left",
      "list_search"  => false,
      "list_order"   => "true",
      "categorie"=>"Algemeen"));

    $this->addField('fondssoort',
    array("description"=>"Fondssoort",
          "db_size"=>"8",
          "db_type"=>"char",
          "form_size"=>"8",
          "form_type"=>"hidden",
          "form_visible"=>false,
          "list_width"=>"150",
          "list_visible"=>true,
          "list_align"=>"right",
          "list_search"=>false,
          "list_order"=>"true",
          "categorie"=>"Algemeen"));


    $this->addField('settlementdatum',
                    array("description"=>"Settlementdatum",
                          "default_value"=>'',
                          "db_size"=>"24",
                          "db_type"=>"date",
                          "form_type"=>"calendar",
                          "form_size"=>"24",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"150",
                          "form_class"    => "AIRSdatepicker",
                          "form_extra"=>'onchange="date_complete(this);"',
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "categorie"=>"Algemeen"));

    $this->addField('notaValutakoers',
                    array("description"=>"Valutakoers",
                          "default_value"=>"",
                          "db_size"=>"12,5",
                          "db_type"=>"double",
                          "form_type"=>"text",
                          "form_size"=>"12,5",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_format"=>"%01.2f",
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "categorie"=>"Algemeen"));
  }

}

?>