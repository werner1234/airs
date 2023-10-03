<?php
/*
    AE-ICT CODEX source module versie 1.6, 31 mei 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/05/30 12:44:27 $
    File Versie         : $Revision: 1.81 $

    $Log: OrderRegelsV2.php,v $
    Revision 1.81  2020/05/30 12:44:27  rvv
    *** empty log message ***

    Revision 1.80  2019/08/08 08:08:10  rm
    7868

    Revision 1.79  2019/04/27 18:37:19  rvv
    *** empty log message ***

    Revision 1.78  2018/11/24 19:04:48  rvv
    *** empty log message ***

    Revision 1.77  2018/11/22 07:30:45  rvv
    *** empty log message ***

    Revision 1.76  2018/11/21 08:43:55  rvv
    *** empty log message ***

    Revision 1.75  2018/11/08 07:56:18  rvv
    *** empty log message ***

    Revision 1.74  2018/10/26 14:55:44  rm
    7269 Orders v2: Order reden toevoegen

    Revision 1.73  2018/09/19 13:42:20  rm
    4773

    Revision 1.72  2018/09/15 17:37:10  rvv
    *** empty log message ***

    Revision 1.71  2018/09/05 15:47:36  rvv
    *** empty log message ***

    Revision 1.70  2018/05/30 15:21:47  rm
    6889

    Revision 1.69  2018/05/23 13:43:43  rvv
    *** empty log message ***

    Revision 1.68  2018/05/12 15:29:35  rvv
    *** empty log message ***

    Revision 1.67  2018/05/02 16:04:27  rvv
    *** empty log message ***

    Revision 1.66  2018/02/16 16:12:16  rm
    Fractie weer kunnen inleggen

    Revision 1.65  2018/02/12 08:38:20  rm
    Fracties niet mogelijk maken ivm fix crash

    Revision 1.64  2018/02/07 17:13:54  rvv
    *** empty log message ***

    Revision 1.63  2017/12/30 09:46:33  rvv
    *** empty log message ***

    Revision 1.62  2017/12/20 16:57:25  rvv
    *** empty log message ***

    Revision 1.61  2017/12/08 18:23:43  rm
    no message

    Revision 1.60  2017/09/21 15:01:00  rm
    advies relaties

    Revision 1.59  2017/09/20 15:46:26  rvv
    *** empty log message ***

    Revision 1.58  2017/09/20 13:05:40  rvv
    *** empty log message ***

    Revision 1.57  2017/07/23 13:39:04  rvv
    *** empty log message ***

    Revision 1.56  2017/07/19 19:20:46  rvv
    *** empty log message ***

    Revision 1.55  2017/06/24 16:48:38  rvv
    *** empty log message ***

    Revision 1.54  2017/05/11 14:14:26  rvv
    *** empty log message ***

    Revision 1.53  2017/05/04 12:57:54  rm
    javascript problemen

    Revision 1.52  2017/03/15 16:29:12  rvv
    *** empty log message ***

    Revision 1.51  2016/12/07 15:14:13  rvv
    *** empty log message ***

    Revision 1.50  2016/11/13 16:25:14  rvv
    *** empty log message ***

    Revision 1.49  2016/11/10 12:26:10  rvv
    *** empty log message ***

    Revision 1.48  2016/10/14 09:45:42  rm
    validatie tabel breedte

    Revision 1.47  2016/09/28 15:51:48  rvv
    *** empty log message ***

    Revision 1.46  2016/09/24 17:10:12  rvv
    *** empty log message ***

    Revision 1.45  2016/09/14 11:46:07  rvv
    *** empty log message ***

    Revision 1.44  2016/09/07 12:15:30  rvv
    *** empty log message ***

    Revision 1.43  2016/08/21 13:17:03  rvv
    *** empty log message ***

    Revision 1.42  2016/08/03 18:21:33  rvv
    *** empty log message ***

    Revision 1.41  2016/07/30 18:22:13  rvv
    *** empty log message ***

    Revision 1.40  2016/07/27 15:55:04  rvv
    *** empty log message ***

    Revision 1.39  2016/07/24 09:30:47  rvv
    *** empty log message ***

    Revision 1.38  2016/07/17 09:41:51  rvv
    *** empty log message ***

    Revision 1.37  2016/07/13 15:39:31  rvv
    *** empty log message ***

    Revision 1.36  2016/07/09 18:55:55  rvv
    *** empty log message ***

    Revision 1.35  2016/07/06 16:01:32  rvv
    *** empty log message ***

    Revision 1.34  2016/07/03 11:28:08  rvv
    *** empty log message ***

    Revision 1.33  2016/07/01 06:34:01  rvv
    *** empty log message ***

    Revision 1.32  2016/06/23 13:58:48  rm
    Orders v2

    Revision 1.31  2016/06/17 12:19:24  rm
    no message

    Revision 1.30  2016/06/09 08:18:21  rm
    Nieuwe notifier

    Revision 1.29  2016/06/08 07:36:57  rm
    Orders

    Revision 1.28  2016/06/05 12:14:30  rvv
    *** empty log message ***

    Revision 1.27  2016/06/01 11:37:15  rvv
    *** empty log message ***

    Revision 1.26  2016/05/25 09:47:09  rm
    orders

    Revision 1.25  2016/05/19 12:56:48  rvv
    *** empty log message ***

    Revision 1.24  2016/04/06 15:32:51  rvv
    *** empty log message ***

    Revision 1.23  2016/02/22 08:14:49  rm
    orders v2

    Revision 1.22  2016/02/14 11:24:55  rvv
    *** empty log message ***

    Revision 1.21  2015/12/23 16:02:46  rm
    OrdersV2

    Revision 1.20  2015/12/18 15:19:10  rm
    OrdersV2

    Revision 1.19  2015/12/09 15:47:31  rm
    OrdersV2

    Revision 1.18  2015/12/04 12:16:29  rvv
    *** empty log message ***

    Revision 1.17  2015/11/22 14:18:48  rvv
    *** empty log message ***

    Revision 1.16  2015/11/20 08:55:23  rm
    no message

    Revision 1.15  2015/11/18 15:59:11  rm
    no message

    Revision 1.14  2015/11/15 12:12:02  rvv
    *** empty log message ***

    Revision 1.13  2015/11/04 13:28:43  rm
    no message

    Revision 1.12  2015/11/01 18:07:43  rvv
    *** empty log message ***

    Revision 1.11  2015/10/18 13:38:35  rvv
    *** empty log message ***

    Revision 1.10  2015/09/30 07:48:22  rvv
    *** empty log message ***

    Revision 1.9  2015/08/26 15:44:29  rvv
    *** empty log message ***

    Revision 1.8  2015/08/12 13:28:42  rm
    orders v2

    Revision 1.7  2015/08/11 06:20:11  rvv
    *** empty log message ***

    Revision 1.6  2015/08/02 14:26:28  rvv
    *** empty log message ***

    Revision 1.5  2015/07/26 16:29:54  rvv
    *** empty log message ***

    Revision 1.4  2015/07/24 14:49:37  rm
    OrdersV2

    Revision 1.3  2015/07/19 15:01:04  rvv
    *** empty log message ***

    Revision 1.2  2015/07/10 14:54:39  rm
    ordersV2

    Revision 1.1  2015/07/08 15:35:21  rvv
    *** empty log message ***





*/

class OrderRegelsV2 extends Table
{
  /*
  * Object vars
  */

  var $data = array();
  var $orderObject = null;
  var $orderControlleDone = array();
  var $forceOrdercheck = false;
  /*
  * Constructor
  */
  function OrderRegelsV2()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
  }

  function setOrderOject ($orderObject)
  {
    $this->orderObject = $orderObject;
  }

	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}

	/*
	 * Veldvalidatie
	 */
	function validate()
	{
		($this->get("portefeuille")=="")?$this->setError("portefeuille","Mag niet leeg zijn!"):true;

     if(!is_object($this->orderObject) && $this->get('orderid') > 0)
     {
       $this->orderObject=new OrdersV2();
       $this->orderObject->getById($this->get('orderid'));
     }

     $fixOrder=0;
     if(is_object($this->orderObject))
       $fixOrder=$this->orderObject->get('fixOrder');

     if($fixOrder==0)
     {
		   ($this->get("rekening")=="")?$this->setError("rekening",vt("Mag niet leeg zijn!")):true;
     }
     else
     {
        $this->set('rekening','');
     }

     if ( $this->error !== false ) {
        if($this->get('orderid')>0)
          $forceUpdate=false;
        else
          $forceUpdate=true;
        
        if ( isset ($this->forceOrdercheck) ) {
          $forceUpdate = $this->forceOrdercheck;
        }
       $this->forceOrdercheck = $forceUpdate;
        if(!isset($this->skipOrderControle) || $this->skipOrderControle==false)
          $this->orderControlle(false,true);
        ($this->get("controleStatus")=='2')?$this->setError("controleStatus",vt("Bevestig controle.")):true;
        ($this->get("controleStatus")=='1')?$this->setError("controleStatus",vt("Bevestig controle.")):true;
     }

     $aantal = $this->get("aantal");
     $bedrag = $this->get('bedrag');

     if ( ($_POST['orderSelectieType'] === 'N' || $_GET['orderSelectieType'] === 'N') || $_POST['orderSelectieType'] === 'O' || $_GET['orderSelectieType'] === 'O' ) {
       (empty($bedrag))?$this->setError("bedrag",vt("Mag niet leeg zijn.")):true;
     } else {
      ($aantal != 0)?true:$this->setError("aantal",vt("Geen geldig aantal."));
      (empty($aantal))?$this->setError("aantal",vt("Geen geldig aantal.")):true;
     }

    //Dit is voor het inleggen van fracties
//    $isFix = $this->orderObject->get('fixOrder');//$isFix == 1 &&
//    if ( $isFix == 1 && ( is_numeric( $aantal ) && floor( $aantal ) != $aantal ) ) {
//      $this->setError("aantal", "Aantal mag geen decimalen bevatten!");
//    }
//$this->setError("controleStatus","Bevestig controle.");

	  if ($aantal < 0)
	    $this->set("aantal",abs($aantal));

		$valid = ($this->error==false)?true:false;

		return $valid;
	}

	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
	 $level = 7;
	 $db=new DB();
	 $query="SELECT orderStatus FROM OrdersV2 WHERE id='".$this->get("orderid")."'";
	 $db->SQL($query);
	 $status=$db->lookupRecord();


	  switch ($type)
	  {
	  	case "edit":
	  		return ($level >=3 )?true:false;
	  		break;
	  	case "delete":
	  		return ($level >=7 )?true:false;
	  		break;
	  	default:
	  	  return false;
	  		break;
	  }
	}

  function orderControlle($forceLog=false,$forceUpdate=false)
  {
    global $__ORDERvar, $USR;

    $data = array_merge($_POST,$_GET); //haal post en get data op
  
    
    if($this->get('controleRegels') <> '' && $this->forceOrdercheck == false)
      $export['controleRegels'] = unserialize($this->get('controleRegels'));
    else
      $export['controleRegels']=array();


    /** bij een status groter dan 0 tonen we de html met de vooraf ingevoerde regels. De orderregel heeft zijn eigen status.**/
    $mailBevestigingVerzonden = $this->get('mailBevestigingVerzonden');
    if($this->get('orderregelStatus') > 0 && $forceUpdate==false || (! empty ($mailBevestigingVerzonden) && $mailBevestigingVerzonden !== '0000-00-00 00:00:00' ) )
    {
      $this->data['orderData']['orderCheckHtml']=$this->createCheckHtml($export['controleRegels'],true);
      return false;
    }

    

    include_once('../html/orderControlleRekenClassV2.php');
    include_once("../html/rapport/rapportRekenClass.php");
    include_once("../html/rapport/Zorgplichtcontrole.php");

    $orderCheck = new orderControlleBerekeningV2();
    $orderCheck->orderregelObject=$this;
    $orderLogs = new orderLogs();
    $orderId = $this->get('orderid');
  
    
    
    $portefeuille = ( $_POST['portefeuille']<>'' ? $_POST['portefeuille'] : $this->get('portefeuille'));
    if(!isset($this->orderControlleDone[$portefeuille]))
      $this->orderControlleDone[$portefeuille]=false;

    if( ! empty ($portefeuille))
    {
      $orderCheck->setdata($orderId, $portefeuille, $this->get('rekening'), $this->get('aantal'),	false);

      if ( ! empty ($orderCheck->errors) )
      {
        foreach ( $orderCheck->errors as $errorKey => $errorMessage ) {
          $this->setError($errorKey, $errorMessage);
        }
        $this->error = true;
        return false;
      }

      if($this->orderControlleDone[$portefeuille]==false)
        $resultaat = $orderCheck->check(); //voer de check uit

      $actieveChecks=getActieveControles('',$portefeuille);
      
      /** loop checkboxes wanneer deze is aangevinkt set de export regel **/
      //$export['controleRegels']['date'] = date('Y-m-d H:i:s');
      foreach ( $actieveChecks as $key => $checkName )
      {
        $export['controleRegels'][$key]['vorigResultaat'] = $export['controleRegels'][$key]['resultaat'] ;
        $export['controleRegels'][$key]['resultaat'] = ( isset($resultaat[$key]) ? $resultaat[$key] : null);
        $export['controleRegels'][$key]['naam'] = $__ORDERvar["orderControles"][$key];
        $export['controleRegels'][$key]['short'] = ( isset($orderCheck->checksKort[$key]) ? $orderCheck->checksKort[$key] : null);
        $export['controleRegels'][$key]['mailTxt'] = ( isset($orderCheck->mailTxt[$key]) ? $orderCheck->mailTxt[$key] : null);

        if( isset ($data['order_controle_checkbox_'.$key]) )
        {

      

          if (!requestType('ajax'))
          {
          //  listarray($data);exit;
          }
          if (  ( ! isset($_POST['validate']) || $_POST['validate'] == false ) &&
                 ($export['controleRegels'][$key]['checked'] != $data['order_controle_checkbox_' . $key] ||
                 $forceLog ||
                 ($data['order_controle_forceLog_' . $key]==1 && $data['order_controle_checkbox_' . $key] ==1))
             )
          {
            $orderLogs->addToLog($this->get('orderid'), null, "Check $key " . $this->get('portefeuille') . " " . $export['controleRegels'][$key]['checked'] . " -> " . $data['order_controle_checkbox_' . $key]);
          }
          $export['controleRegels'][$key]['checked'] = $data['order_controle_checkbox_' . $key];
        }
      }

      if($this->orderControlleDone[$portefeuille]==true)
        return false;

      $htmlData = $this->createCheckHtml($export['controleRegels'],false,true);

      $orderCheck->setregels($export['controleRegels']);
      $this->set("controleRegels",serialize($export["controleRegels"]));

      if( isset ($export['controleRegels']) && count($export['controleRegels']) > 0)
      {
        $this->data['orderData']['orderCheckHtml']=$htmlData['html'];
      }

      $maxCheck = $orderCheck->checkMaxGetal();
      $this->data['orderData']['recheck']=$htmlData['recheck'];
      $this->data['orderData']['maxCheck']=$maxCheck;
      $this->set('controleStatus',$maxCheck);
      $this->orderControlleDone[$portefeuille]=true;
    }

  }

  function orderRegelUpdate($data,$forceCheck=false)
  {
    global $__ORDERvar;
    $noInsert = false;
    $orderLogs=new orderLogs();

    if (isset($data['orderregelId']) && $data['orderregelId'] > 0)
    {
      $thisOrderRegelId = $data['orderregelId'];
      $noInsert = true;
    }

    $db=new DB();
    $forceLog=false;

    if($this->get('id')==0)
    {
      $query = "SELECT OrderRegelsV2.id FROM OrdersV2 JOIN OrderRegelsV2 ON OrderRegelsV2.orderid = OrdersV2.id WHERE OrdersV2.id = '".$data['OrdersV2.id']."' 
      AND OrdersV2.orderSoort <> 'M'
      AND OrdersV2.orderSoort <> 'O'
      AND OrdersV2.orderSoort <> 'X' ";
      if ($db->QRecords($query) == 1)
      {
        $orderregelData=$db->nextRecord();
        $thisOrderRegelId=$orderregelData['id'];
        $this->getById($thisOrderRegelId);
        $noInsert = true;
      }
    }

    /** nieuwe order regel */
    if ($noInsert === false)
    {
      $uitsluiten = array('id', 'add_date', 'add_user', 'change_date', 'change_user');
      foreach ($this->data['fields'] as $key => $value)
      {
        if (!in_array($key, $uitsluiten) && isset($data[$key]))
         $this->set($key, $data[$key]);
      }
      /** bepalen van de positie **/
      $orderRegelsObj = new OrderRegelsV2();
      $orderRegelData = $orderRegelsObj->parseBySearch(array('orderid' => $this->get('orderid')), 'all', 'ORDER BY positie DESC',1);
      if ( $orderRegelData == false ) {
        $orderRegelData['positie'] = 0;
      }

      $newPos = 0;
      if (!empty ($orderRegelData))
        $newPos = $orderRegelData["positie"] + 1;

      $this->set('positie', $newPos);
      /** einde van bepalen van de positie **/
      if(isset($data['OrdersV2.id']))
        $this->set('orderid', $data['OrdersV2.id']);
      $this->save();
      $orderLogs->addToLog($this->get('orderid'), null, "Portefeuille:".$this->get('portefeuille')." Aantal:".$this->get('aantal')." toegevoegd.");
    }
    /** We gaan de order regelwijzigen */
    else
    {
     // if ($this->get('orderregelStatus') < 1)
      //  $this->set('controleRegels', serialize($export["controleRegels"]));
      $updateFields = array('portefeuille', 'rekening', 'valuta', 'aantal', 'client', 'bedrag', 'orderReden', 'orderaantal');
      foreach ($updateFields as $veld)
      {
        if (isset ($data[$veld]) && $data[$veld] <> '')
        {
          if ($data[$veld] <> $this->get($veld))
          {
            $orderLogs->addToLog($this->get('orderid'), null, "$veld " . $this->get($veld) . "->" . $data[$veld]);
            $forceLog=true;
          }
          $this->set($veld, $data[$veld]);
        }
      }

    }

    $this->orderControlle($forceLog,$forceCheck);
//    if(!requestType('ajax'))
//      echo "update <br>\n";
    $this->save();
  }

  function createCheckHtml($controles,$disabled=false,$returnArray=false)
  {
      $html = '';//"<fieldset><legend><b>Controles</b></legend><table border=1>";
      $htmlLos=array();
      $htmlMailLos=array();
      $recheck=0;
      foreach ($controles as $key=>$values) //maak chk boxes voor deze vermogensbeheerder.
      {
        $mutatie="";
        $forceLog='';
        if($disabled==false && isset ($values['vorigResultaat']) && isset ($values['resultaat']) &&  $values['vorigResultaat'] <> ''  && $values['resultaat'] <> '' && $values['vorigResultaat'] <> $values['resultaat'])
        {
        //  echo $values['vorigResultaat'] ;
        //  echo $values['resultaat'];
         // xdiff_string_diff ($values['vorigResultaat'],$values['resultaat'] );
          $mutatie = "(Mutatie)";
          $forceLog = "<input type=\"hidden\" value=\"1\" name=\"order_controle_forceLog_".$key."\" id=\"order_controle_forceLog_".$key."\">";
          $values['checked'] =0;
          $recheck=1;
        }
        if( isset ($values['short']) && $values['short'] > 0)
        {
          if( isset ($values['checked']) && $values['checked'] == 1)
            $error='';
          else
            $error='class="input_error"';

          if($disabled==true)
            $checkbox="<input type=\"checkbox\" disabled id=\"order_controle_checkbox_".$key."\" name=\"order_controle_checkbox_".$key."\" ".( isset ($values['checked']) && $values['checked'] == 1 ? "checked" : "" )."> ";
          else
            $checkbox="$forceLog <input type=\"hidden\" value=\"0\" id=\"order_controle_checkbox_".$key."_hidden\" name=\"order_controle_checkbox_".$key."\"> 
            <input type=\"checkbox\" $error value=\"1\" id=\"order_controle_checkbox_".$key."\" name=\"order_controle_checkbox_".$key."\" ".( isset ($values['checked']) && $values['checked'] == 1 ? "checked" : "" )."> ";
        }
        else
          $checkbox='&nbsp;';

        $htmlRegel="<tr> <td width=40> $checkbox</td><td width=200><label for=\"order_controle_checkbox_".$key."\" title=\"".$values['naam']."\">".$values['naam']." </label>$mutatie</td>\n<td>".$values['resultaat'] ." </td></tr>\n ";
        $htmMailRegel=$values['mailTxt'];
        $html .= $htmlRegel;
        $htmlLos[$key]=$htmlRegel;
        $htmlMailLos[$key]=$htmMailRegel;
        $htmlMailLos2[$key]=$values['resultaat'];

      }
//      $html .= "</table></fieldset>";
      if($returnArray==true)
         return array('html'=>$html,'recheck'=>$recheck,'htmlLos'=>$htmlLos,'htmlMailLos'=>$htmlMailLos,'htmlMailLos2'=>$htmlMailLos2);
      else
         return $html;
  }

  function annuleerFix()
  {
     global $USR;
     include_once("../classes/AE_cls_FIXtransport.php");

     if(!is_object($this->orderObject) && $this->get('orderid') > 0)
     {
       $this->orderObject=new OrdersV2();
       $this->orderObject->getById($this->get('orderid'));
     }

     if(!is_object($this->orderObject))
     {
       echo "Geen orderinformatie beschikbaar. Annuleer verzoek afgebroken.";
       exit;
     }

      $fixOrder=new FixOrders();
      $fixOrder->getByField('AIRSorderReference',$this->get('id'));
      if($fixOrder->get('orderid'))
      {
        $cancel=array("typeBericht" => "del",'ordernr'=>$fixOrder->get('orderid'),"user" => $USR);
        $fix = new AE_FIXtransport();
        if($fix->addToQueue($cancel))
        {
          $fixOrder->set('laatsteStatus','6'); //pending cancel. Normaal komt deze ook via fix bericht maar mocht deze niet komen dan staat de fixorder al op pending cancel.
          $fixOrder->save();
          $this->orderObject->set('fixAnnuleerdatum',date('Y-m-d H:i:s'));
          $this->orderObject->save();
          $fix->orderLogs->addToLog($this->orderObject->get('id'), $fixOrder->get('orderid'),"Annuleer verzoek verzonden.");
        }
      }
      else
      {
        echo "Order (nog) niet kunnen annuleren. Nog geen fixorder id aanwezig.";
        exit;
      }
  }

  function verzendFix() //deze functie kan volgens mij weg omdat orders niet langer regel per regel verzonden worden.
  {
    logit('Niet actief');
    echo "Niet actief";
    exit();
  }

 

	/*
  * Table definition
  */
  function defineData()
  {
    global $__ORDERvar;
    $this->data['name']  = "OrderregelsV2";
    $this->data['table']  = "OrderRegelsV2";
    $this->data['identity'] = "id";

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('orderid',
													array("description"=>"Order id",
													"default_value"=>"",
													"db_size"=>"16",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"16",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "categorie"=>"Algemeen"));

		$this->addField('positie',
													array("description"=>"Positie",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"3",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
                          "categorie"=>"Algemeen"));

		$this->addField('portefeuille',
													array("description"=>"Portefeuille",
													"default_value"=>"",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Portefeuilles",
                          "form_extra"=>'onchange="lookupPort()"',
                          "categorie"=>"Algemeen"));

		$this->addField('rekening',
													array("description"=>"Rekening",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_size"=>"1",
													"form_select_option_notempty"=>true,
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Rekeningen",
                          "categorie"=>"Algemeen"));

			$this->addField('aantal',
													array("description"=>"aantal",
													"default_value"=>"",
													"db_size"=>"12",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"12",
                          "form_format"=>"%f",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.6f",
													"list_numberformat"=>6,
													"list_align"=>"right",
													"list_width"=>"100",
													"list_search"=>false,
													"list_order"=>"true",
                                "categorie"=>"Algemeen"));

      $this->addField('bedrag',
													array("description"=>"Bedrag",
													"default_value"=>"",
													"db_size"=>"12",
													"db_type"=>"double",
													"form_type"=>"text",
                          "form_class"=> "form-control",
													"form_size"=>"12",
													"form_visible"=>true,
													"list_visible"=>true,
//													"list_format"=>"%01.4f",
													"list_numberformat"=>2,
													"list_align"=>"right",
													"list_width"=>"100",
													"list_search"=>false,
													"list_order"=>"true",
                          "categorie"=>"Algemeen"));


		$this->addField('orderregelStatus',
													array("description"=>"Status",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"list_conversie"=>$__ORDERvar["orderStatus"]));

		$this->addField('controleRegels',
													array("description"=>"Controle regels",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                                "categorie"=>"Algemeen"));

		$this->addField('controleStatus',
													array("description"=>"Controle",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"3",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"false"));

		$this->addField('client',
													array("description"=>"Client",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
												  "list_order"=>"true",
                          "keyIn"=>"Clienten",
                          "categorie"=>"Algemeen"));

		$this->addField('orderReden',
													array("description"=>"Order reden",
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"varchar",
                          "select_query"=>"SELECT waarde,omschrijving FROM KeuzePerVermogensbeheerder JOIN Orderredenen ON KeuzePerVermogensbeheerder.waarde=Orderredenen.orderreden WHERE KeuzePerVermogensbeheerder.categorie='Orderredenen' GROUP BY waarde ORDER BY Afdrukvolgorde ",
                          "form_type"=>"selectKeyed",
													"form_size"=>"100",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
                          "keyIn"=>"Orderredenen",
													"list_order"=>"true",
                          "categorie"=>"Algemeen"));

    		$this->addField('add_date',
													array("description"=>"add_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

    $this->addField('kosten',
                    array("description"=>"Kosten",
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

    $this->addField('brokerkosten',
                    array("description"=>"Brokerkosten",
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

    $this->addField('opgelopenRente',
                    array("description"=>"Opgel. rente",
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

    $this->addField('brutoBedrag',
                    array("description"=>"Bruto bedrag",
                          "default_value"=>"",
                          "db_size"=>"12,5",
                          "db_type"=>"double",
                          "form_type"=>"text",
                          "form_size"=>"12,5",
                          "form_visible"=>true,
                          "form_extra"=>"READONLY",
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_format"=>"%01.2f",
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "categorie"=>"Algemeen"));

    $this->addField('nettoBedrag',
                    array("description"=>"Netto bedrag",
                          "default_value"=>"",
                          "db_size"=>"12,5",
                          "db_type"=>"double",
                          "form_type"=>"text",
                          "form_size"=>"12,5",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "categorie"=>"Algemeen"));

    $this->addField('PSET',
                    array("description"=>"PSET",
                          "db_size"=>"12",
                          "db_type"=>"varchar",
                          "form_size"=>"12",
                          "form_type"=>"selectKeyed",
                          "select_query"=>"SELECT code, concat(code,' - ',naam,' (',BICcode,')') FROM BICcodes WHERE PSET=1 ORDER BY code",
                          "form_visible"=>true,"list_width"=>"150",
                          "list_visible"=>true,
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "keyIn"=>"BICcodes",
                          "categorie"=>"Algemeen"));

    $this->addField('PSAF',
                    array("description"=>"PSAF",
                          "db_size"=>"12",
                          "db_type"=>"varchar",
                          "form_size"=>"12",
                          "form_type"=>"selectKeyed",
                          "select_query"=>"SELECT code, concat(code,' - ',naam,' (',BICcode,')') FROM BICcodes WHERE PSAF=1 ORDER BY code",
                          "form_visible"=>true,"list_width"=>"150",
                          "list_visible"=>true,
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "keyIn"=>"BICcodes",
                          "categorie"=>"Algemeen"));

    $this->addField('effRekeningTegenpartij',
                    array("description"=>"effRekeningTegenpartij",
                          "default_value"=>"",
                          "db_size"=>"24",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"24",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"150",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "categorie"=>"Algemeen"));

    $this->addField('BIC_tegenpartij',
                    array("description"=>"BIC_tegenpartij",
                          "default_value"=>"",
                          "db_size"=>"24",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"24",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"150",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "categorie"=>"Algemeen"));

    $this->addField('notaDefinitief',
                    array("description"=>"Definitief",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "form_size"=>"4",
                          "form_type"=>"checkbox",
                          "form_visible"=>true,"list_width"=>"150",
                          "list_visible"=>true,
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "categorie"=>"Algemeen"));

    $this->addField('regelNotaValutakoers',
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

    $this->addField('printDate',
                    array("description"=>"Afdrukdatum",
                          "default_value"=>"",
                          "db_size"=>"0",
                          "db_type"=>"datetime",
                          "form_type"=>"calendar",
                          "form_size"=>"0",
                          "form_visible"=>false,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "categorie"=>"Algemeen"));

    $this->addField('mailBevestigingVerzonden',
                    array("description"=>"mailBevestiging",
                          "default_value"=>"",
                          "db_size"=>"0",
                          "db_type"=>"datetime",
                          "form_type"=>"calendar",
                          "form_size"=>"0",
                          "form_visible"=>false,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

    $this->addField('mailBevestigingData',
                    array("description"=>"mailBevestiging",
                          "default_value"=>"",
                          "db_size"=>"0",
                          "db_type"=>"text",
                          "form_type"=>"text",
                          "form_size"=>"0",
                          "form_visible"=>false,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
						  
	$this->addField('externeBatchId',
										array("description"=>"externeBatchId",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  
    $this->addField('kopieOrderId',
                    array("description"=>"Kopie order",
                          "default_value"=>"",
                          "db_size"=>"16",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"16",
                          "form_visible"=>false,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "categorie"=>"Algemeen"));
  
  
    $this->addField('orderaantal',
                    array("description"=>"orderaantal",
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
                          "list_order"=>"true"));
  }
}
?>