<?php
/*
    AE-ICT CODEX source module versie 1.6, 19 september 2009
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2017/07/28 09:27:31 $
    File Versie         : $Revision: 1.20 $

    $Log: OrderUitvoeringV2.php,v $
    Revision 1.20  2017/07/28 09:27:31  rm
    no message

    Revision 1.19  2017/03/06 11:57:55  rvv
    *** empty log message ***

    Revision 1.18  2017/03/06 11:42:58  rvv
    *** empty log message ***

    Revision 1.17  2017/03/05 12:03:24  rvv
    *** empty log message ***

    Revision 1.16  2017/02/05 16:25:36  rvv
    *** empty log message ***

    Revision 1.15  2016/11/24 06:37:31  rvv
    *** empty log message ***

    Revision 1.14  2016/11/23 15:39:53  rm
    OrdersV2

    Revision 1.13  2016/09/28 12:28:59  rvv
    *** empty log message ***

    Revision 1.12  2016/09/24 17:10:12  rvv
    *** empty log message ***

    Revision 1.11  2016/07/28 06:00:23  rvv
    *** empty log message ***

    Revision 1.10  2016/07/28 05:45:39  rvv
    *** empty log message ***

    Revision 1.9  2016/07/27 15:55:04  rvv
    *** empty log message ***

    Revision 1.8  2016/07/24 09:30:47  rvv
    *** empty log message ***

    Revision 1.7  2016/07/18 14:48:52  rm
    5137, toevoeging van input filter

    Revision 1.6  2016/07/06 16:01:32  rvv
    *** empty log message ***

    Revision 1.5  2016/02/19 16:01:50  rm
    orders v2

    Revision 1.4  2015/11/06 20:29:14  rvv
    *** empty log message ***

    Revision 1.3  2015/08/12 11:14:29  rvv
    *** empty log message ***

    Revision 1.2  2015/08/11 06:20:11  rvv
    *** empty log message ***

    Revision 1.1  2015/08/02 14:26:28  rvv
    *** empty log message ***

 
*/

class OrderUitvoeringV2 extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function OrderUitvoeringV2()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
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

	  $orderId=(isset($_GET['orderid'])?$_GET['orderid']:$this->get('orderid'));
	  $db= new DB();
    $query="SELECT round(SUM(OrderRegelsV2.aantal),6) as aantal FROM OrderRegelsV2 WHERE OrderRegelsV2.orderid = '".$orderId."'";
    $db->SQL($query);
    $orderData=$db->lookupRecord();

    $query="SELECT round(SUM(uitvoeringsAantal),6) as aantal FROM OrderUitvoeringV2 WHERE orderid = '".$orderId."' AND id <> '".$this->get('id')."'";
    $db->SQL($query);
    $uitvoeringsData = $db->lookupRecord();

    $toAdd=round($orderData['aantal']-$uitvoeringsData['aantal'],4);
    
    if(round($this->get('uitvoeringsAantal'),4) == 0.0)
      $this->setError('uitvoeringsAantal',vt("UitvoeringsAantal te klein."));

    if(round($orderData['aantal'],4) > 0.0 && round($this->get('uitvoeringsAantal'),4) > $toAdd)
    {
      $this->set('uitvoeringsAantal',$toAdd);
      $this->setError('uitvoeringsAantal', vtb("UitvoeringsAantal > orderaantal (%s). UitvoeringsAantal aangepast.", array($orderData['aantal'])));
    }

		$datumtijd=$this->get('uitvoeringsDatum');
		$expDatumTijd=explode(" ",$datumtijd);

		$datum=explode('-',$expDatumTijd[0]);
		$tijd=explode(':',$expDatumTijd[1]);
		$tijdfout=false;
		if($tijd[0]>24||$tijd[0]<0)
		{
			$tijdfout = true;
			$tijd[0]=0;
		}
		if($tijd[1]>60||$tijd[1]<0)
		{
			$tijdfout = true;
			$tijd[1]=0;
		}
		if($tijd[2]>60||$tijd[2]<0)
		{
			$tijdfout = true;
			$tijd[2]=0;
		}
		for($i=0;$i<3;$i++)
			if(!isset($tijd[$i]) || empty($tijd[$i]))
				$tijd[$i]=0;

		$reconstructedJul=mktime($tijd[0],$tijd[1],$tijd[2],$datum[1],$datum[2],$datum[0]);
		$conversieJul=db2jul($datumtijd);

		if($conversieJul!=$reconstructedJul||$reconstructedJul==0||$conversieJul==0||$datum[0]==0||$datum[1]==0||$datum[2]==0||$tijdfout==true)
		{
			$this->setError('uitvoeringsDatum',$tijd[0].":".$tijd[1].":".$tijd[2]."_".$datum[1]."-".$datum[2]."-".$datum[0]."Onjuiste datum ingevoerd. ($datumtijd <> ".date('Y-m-d H:i:s',$reconstructedJul).")");
		}


		//echo "rvv ( $toAdd )".$this->data['fields']['uitvoeringsAantal']['value'];
		$valid = ($this->error==false)?true:false;
		if($valid==true)
		{
			$orderLogs = new orderLogs();
			$query="SELECT id,uitvoeringsAantal,uitvoeringsDatum,uitvoeringsPrijs,nettokoers,opgelopenrente FROM OrderUitvoeringV2 WHERE id='".$this->get('id')."'";
			$db->SQL($query);
			$oldData = $db->lookupRecord();
			if($oldData['id'] > 0)
			{
				foreach ($oldData as $key => $value)
				{
					$newvalue = $this->get($key);
					if ($key == 'uitvoeringsDatum')
					{
						$oldData[$key] = date('d-m-Y', db2jul($oldData[$key]));
						$newvalue = date('d-m-Y', db2jul($newvalue));
					}
					if($oldData[$key]==0 && $newvalue=='')
						$newvalue=0;

					if ($oldData[$key] != $newvalue)// && !requestType('ajax')
					{
						//	listarray($oldData);
						$orderLogs->addToLog($this->get('orderid'), null, "Uitvoering " . $this->get('id') . " " . $this->data['fields'][$key]['description'] . " naar $newvalue aangepast.");
					}
				}
			}
			else
			{
				$this->save();
				$orderLogs->addToLog($this->get('orderid'), null, "Uitvoering " . $this->get('id') . "aangemaakt, aantal:" . $this->get('uitvoeringsAantal') . ", prijs:" . $this->get('uitvoeringsPrijs'));
			}
		}
		return $valid;
	}
  
  function uitvoeringenVerschil($orderId)
  {
    $db = new DB();
    $query="SELECT orderid, round(sum(uitvoeringsAantal),4) as aantal FROM OrderUitvoeringV2 WHERE orderid='$orderId' GROUP BY orderid";
    $db->SQL($query);
    $uitvoeringRec = $db->lookupRecord();
    $query="SELECT orderid, round(sum(aantal),4) as aantal FROM OrderRegelsV2 WHERE orderid='$orderId' GROUP BY orderid";
    $db->SQL($query);
    $orderregelsRec = $db->lookupRecord();
    $verschil=($orderregelsRec['aantal'] - $uitvoeringRec['aantal']);

    return $verschil;
  }

	function uitvoeringsValutakoers($orderId)
	{
		$db = new DB();
		$query="SELECT fondsValuta FROM OrdersV2 WHERE id='".$orderId."'";
		$db->SQL($query);
		$order = $db->lookupRecord();
		$query="SELECT max(uitvoeringsDatum) as datum FROM OrderUitvoeringV2 WHERE orderid='$orderId' GROUP BY orderid";
		$db->SQL($query);
		$uitvoeringsDatum = $db->lookupRecord();
		$query = "SELECT koers,Valuta FROM Valutakoersen WHERE Valuta = '".$order['fondsValuta']."' AND Datum<='".$uitvoeringsDatum['datum']."' ORDER BY Datum DESC LIMIT 1";
		$db->SQL($query);
		$valutaKoers = $db->lookupRecord();
		$fondsValutaKoers =$valutaKoers['koers'];

		return $fondsValutaKoers;
	}

	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
	  return true;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "OrderUitvoeringV2";
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
													array("description"=>"orderid",
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
		$this->addField('uitvoeringsAantal',
										array("description"=>"Uitvoeringsaantal",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_class"    => "maskNumeric6Digits",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.4f",
													"list_numberformat"=>4,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

                          

		$this->addField('uitvoeringsDatum',
													array("description"=>"Uitvoeringsdatum",
													"default_value"=>"",
													"db_size"=>"19",
													"db_type"=>"varchar",
													"form_type"=>"text",
  //                        "form_class"    => "AIRSdatepicker",
  //                        "form_extra"    => " onchange=\"date_complete(this);\"",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('uitvoeringsPrijs',
													array("description"=>"Uitvoeringsprijs",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_class"    => "maskNumeric6Digits",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.6f",
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('nettokoers',
													array("description"=>"Nettokoers",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_class"    => "maskNumeric6Digits",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.6f",
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('opgelopenrente',
													array("description"=>"Opgelopenrente",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_class"    => "maskValuta2digitsPositive",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_numberformat"=>2,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

//		if(checkOrderAcces('notaModule'))
				$this->addField('brokerkostenTotaal',
												array("description"=>"Totale brokerkosten",
															"default_value"=>"",
															"db_size"=>"0",
															"db_type"=>"double",
															"form_type"=>"text",
															"form_class"    => "maskValuta2digitsPositive",
															"form_size"=>"0",
															"form_visible"=>true,
															"list_visible"=>true,
															"list_format"=>"%01.2f",
															"list_numberformat"=>2,
															"list_width"=>"100",
															"list_align"=>"right",
															"list_search"=>false,
															"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
?>