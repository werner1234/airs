<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/07/08 15:35:21 $
 		File Versie					: $Revision: 1.43 $

 		$Log: Orders.php,v $
 		Revision 1.43  2015/07/08 15:35:21  rvv
 		*** empty log message ***
 		
 		Revision 1.37  2014/11/30 13:04:47  rvv
 		*** empty log message ***

 		Revision 1.36  2014/07/27 11:25:09  rvv
 		*** empty log message ***
 		
 		Revision 1.35  2014/03/22 15:50:41  rvv
 		*** empty log message ***
 		
 		Revision 1.34  2014/03/08 16:59:39  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2013/06/01 16:12:14  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2013/02/13 17:03:03  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2012/12/06 12:12:35  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2012/12/02 11:02:59  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2012/11/28 17:01:36  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2012/04/11 17:14:09  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2012/03/11 17:15:23  rvv
 		*** empty log message ***

 		Revision 1.26  2012/01/22 13:43:13  rvv
 		*** empty log message ***

 		Revision 1.25  2011/12/18 14:22:17  rvv
 		*** empty log message ***

 		Revision 1.24  2011/12/04 12:52:04  rvv
 		*** empty log message ***

 		Revision 1.23  2011/11/19 15:36:03  rvv
 		*** empty log message ***

 		Revision 1.22  2011/11/12 18:29:17  rvv
 		*** empty log message ***

 		Revision 1.21  2011/11/09 18:49:17  rvv
 		*** empty log message ***

 		Revision 1.20  2011/11/03 19:25:20  rvv
 		*** empty log message ***

 		Revision 1.19  2011/11/02 08:20:31  rvv
 		*** empty log message ***

 		Revision 1.18  2011/10/30 13:34:03  rvv
 		*** empty log message ***

 		Revision 1.17  2011/10/05 18:03:09  rvv
 		*** empty log message ***

 		Revision 1.16  2011/09/14 09:11:39  rvv
 		*** empty log message ***

 		Revision 1.15  2011/09/08 07:15:32  rvv
 		*** empty log message ***

 		Revision 1.14  2009/10/07 13:02:07  rvv
 		*** empty log message ***

 		Revision 1.13  2009/10/07 10:49:16  rvv
 		*** empty log message ***

 		Revision 1.12  2009/10/07 10:04:41  rvv
 		*** empty log message ***

 		Revision 1.11  2009/09/12 11:14:24  rvv
 		*** empty log message ***

 		Revision 1.10  2009/01/20 17:46:24  rvv
 		*** empty log message ***

 		Revision 1.9  2007/11/26 15:15:15  rvv
 		*** empty log message ***

 		Revision 1.8  2007/08/24 10:59:38  cvs
 		fondscode lengte van 16 naar 26 chars

 		Revision 1.7  2007/08/13 15:34:46  rvv
 		*** empty log message ***

 		Revision 1.6  2006/11/14 11:49:47  rvv
 		Logging alle statusveranderingen

 		Revision 1.5  2006/11/10 11:50:22  rvv
 		logt nu alle wijzigingen

 		Revision 1.4  2006/10/18 06:54:35  rvv
 		ordercontrole toevoegingen

 		Revision 1.3  2006/06/28 12:44:53  cvs
 		*** empty log message ***

 		Revision 1.2  2006/06/09 09:57:26  cvs
 		*** empty log message ***

 		Revision 1.1  2006/06/08 14:47:14  cvs
 		*** empty log message ***


*/

class Orders extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function Orders()
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
    global $__ORDERvar;
    global $USR;

    $db = new DB();
    $query="SELECT controle_regels FROM OrderRegels WHERE orderId='".$this->get('orderId')."'";

if($this->get("id"))
{
 	  $query = "SELECT laatsteStatus, orderid, aantal, fondsCode, fonds, transactieType, transactieSoort, tijdsLimiet, tijdsSoort, koersLimiet FROM Orders WHERE id = '".$this->get("id")."'";
	  #$query = "SELECT laatsteStatus, orderid, aantal, fondsCode, fonds, transactieType, transactieSoort, tijdsLimiet, tijdsSoort, koersLimiet FROM Orders WHERE id = ".$this->get("id");
          $db->SQL($query);
	  $oldRec = $db->lookuprecord();
	  foreach($oldRec as $key=>$value)//Nieuwe log functie die alle wijzigingen logt.
  	{
		  $newvalue = $this->get($key);
		  if ($key == 'tijdsLimiet')
	  	{
		  	$limietdatum = explode('-',$newvalue);
			  $limietdatum[2] = str_replace('00','',$limietdatum[2]);
			  $newvalue = implode('-',$limietdatum);
			  if($oldRec[$key] == "0000-00-00")
			    $newvalue = $oldRec[$key];
		  }
	  	if ($oldRec[$key] != $newvalue)
	  	{
  			if ($__ORDERvar[$key][$newvalue] != '')
	  		  $txt .= date("Ymd_Hi")."/$USR - $key naar ".$__ORDERvar[$key][$newvalue]."  \n";
	  		else
	  		  $txt .= date("Ymd_Hi")."/$USR - $key naar $newvalue \n";
	  		if ($key == 'laatsteStatus')
	  		  $verandering = true;
	  	}
	  }
	  $txt .= $this->get("status");
    $this->set("status",$txt);
    }
    
    if($verandering==true)
    {
      $cfg=new AE_config();
      $mailserver=$cfg->getData('smtpServer');
      $query="SELECT
Orders.id,
Gebruikers.emailAdres,
Orders.fonds,
Vermogensbeheerders.OrderStatusKeuze,
OrderRegels.portefeuille,
OrderRegels.client,
OrderRegels.aantal,
Orders.transactieSoort,
Orders.fondsOmschrijving,
(SELECT SUM(uitvoeringsAantal*uitvoeringsPrijs)/sum(uitvoeringsAantal) FROM OrderUitvoering WHERE OrderUitvoering.orderid=Orders.orderid  ) as uitvoeringsPrijs
FROM Orders
INNER JOIN OrderRegels ON Orders.orderid = OrderRegels.orderid
INNER JOIN Portefeuilles ON OrderRegels.portefeuille = Portefeuilles.Portefeuille
INNER JOIN Gebruikers ON Portefeuilles.Accountmanager = Gebruikers.Accountmanager
INNER JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
WHERE Orders.id='".$this->get("id")."'";
       $db->SQL($query);
       $db->Query();
       $mailData=array();
       while($data=$db->nextRecord())
       {
         $orderStatusmelding=unserialize($data['OrderStatusKeuze']);
         if($orderStatusmelding[$this->get("laatsteStatus")]['checkedEmail']==1)
         if($data['emailAdres'] <> '')
            $mailData[$data['emailAdres']][]=$data;
       }
       
       foreach($mailData as $emailAdres=>$orderData)
       {
         $subject="Order ".$this->get("orderid")." van status ".$__ORDERvar['laatsteStatus'][$oldRec['laatsteStatus']]." naar ".$__ORDERvar['laatsteStatus'][$this->get('laatsteStatus')];
         $mailBody="<h3>Order ".$this->get("orderid")." naar status ".$__ORDERvar['laatsteStatus'][$this->get('laatsteStatus')]."</h3>";
         if($orderRegel['uitvoeringsPrijs'] <> '')
           $mailBody.="uitvoeringsPrijs: ".$orderRegel['uitvoeringsPrijs']."<br>";
         $mailBody.="<table border=1>";
         $mailBody.="<tr><td>portefeuille</td><td>client</td><td>aantal</td><td>transactie</td><td>fondsOmschrijving</td></tr>";
         foreach($orderData as $orderRegel)
         {
           $mailBody.="<tr>
           <td>".$orderRegel['portefeuille']."</td>
           <td>".$orderRegel['client']."</td>
           <td align='right'>".$orderRegel['aantal']."</td>
           <td>".$orderRegel['transactieSoort']."</td>
           <td>".$orderRegel['fondsOmschrijving']."</td>
           </tr>";
         }
         $mailBody.="</table><br>\n Verzonden op ".date("d-m-Y H:i");

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

    // check of er geen ordercontrolle fouten zijn
    if ($this->get("laatsteStatus") >0 && $this->get("laatsteStatus") != 5 && $verandering == true)
	  {
	  	$query = "SELECT count(OrderRegels.controle) as fouten from OrderRegels, Orders
	      				WHERE OrderRegels.orderid = Orders.orderid AND Orders.id = '".$this->get("id")."' AND OrderRegels.controle = 2 ;" ;
	  	$db->SQL($query);
	  	$aantal = $db->lookupRecord();
	  	if ($aantal['fouten'] != 0)
	  	{
	 	  	$this->setError("laatsteStatus","Er zitten ".$aantal['fouten']." foute(n) in de orderregels.");
		   	$this->set("laatsteStatus",$this->get("laatsteStatus")-1)	;
		  }
	  }

	  if($this->get("orderid") <> '')
	  {
	    $query = "UPDATE OrderRegels SET status = '".$this->get("laatsteStatus")."' WHERE orderid = '".$this->get("orderid")."' ";
	    $db->SQL($query);
	    $db->Query();
	  }

	  $aantal = $this->get("aantal");
	  if ($aantal < 0)
	    $this->set("aantal",abs($aantal));

		($this->get("fondsOmschrijving")=="")?$this->setError("fondsOmschrijving","Mag niet leeg zijn!"):true;
		($this->get("transactieType")=="")?$this->setError("transactieType","Mag niet leeg zijn!"):true;
		($this->get("transactieSoort")=="")?$this->setError("transactieSoort","Mag niet leeg zijn!"):true;

	  if($_POST['laatsteStatus']>=2)
    {
      $query = "SELECT sum(uitvoeringsAantal) as totaal FROM OrderUitvoering WHERE orderid='".$this->get("orderid")."' ";
      $db->SQL($query);
      $regelsRec = $db->lookupRecord();
      if (round($this->get("aantal"),4) != round(($regelsRec["totaal"]),4) && $regelsRec["totaal"] > 0)
      {
        $this->set("laatsteStatus",1);
        $this->setError("laatsteStatus","Uitvoerings aantal ongelijk aan order aantal. Status terug gezet naar doorgegeven.");
      }
    }

    if($this->get("Depotbank") =='')
    {
      $db=new DB();
      if($_POST['portefeuille'] <> '')
        $portefeuille=$_POST['portefeuille'];
      elseif($this->get('orderid') <> '')
      {
        $query="SELECT portefeuille FROM OrderRegels WHERE orderid='".$this->get('orderid')."'";
        $db->SQL($query);
        $portefeuille=$db->lookupRecord();
        $portefeuille=$portefeuille['portefeuille'];
      }

		  $query="SELECT depotbank FROM Portefeuilles WHERE portefeuille='$portefeuille'";
      $db->SQL($query);
      $depotbank=$db->lookupRecord();
      $this->set('Depotbank',$depotbank['depotbank']);
    }


		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
	  if($_SESSION['usersession']['gebruiker']['ordersNietAanmaken']==1)
    {
      if($this->get('id') == 0)
       return false;
    }
    if($_SESSION['usersession']['gebruiker']['ordersNietVerwerken']==1)
    {
      if($this->get('laatsteStatus') > 0)
        return false;
    }

	  if ($type == "delete")
	    return false;
	  else
		  return true;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    global $__ORDERvar,$USR;
    $this->data['table']  = "Orders";
    $this->data['identity'] = "id";
    
    
    $query="SELECT  Vermogensbeheerders.OrderStandaardTransactieType
     FROM Vermogensbeheerders
     Inner Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
     WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR' limit 1";
    $db=new DB();
    $db->SQL($query);
    $standaard=$db->lookupRecord();

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>false,"list_width"=>"100",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('vermogensBeheerder',
													array("description"=>"Vermogensbeheerder",
													"default_value"=>"",
													"db_size"=>"5",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"5",
													"form_visible"=>false,"list_width"=>"100",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('orderid',
													array("description"=>"uniek orderid",
													"default_value"=>"",
													"db_size"=>"16",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"16",
													"form_visible"=>false,
													"list_visible"=>true,"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('aantal',
													array("description"=>"aantal",
													"default_value"=>"",
													"db_size"=>"12",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"12",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.4f",
													"list_numberformat"=>4,
													"list_numberformatZonderNullen"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);aantalChanged(this);"',
													"list_align"=>"right",
													"list_width"=>"100",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('fondsCode',
													array("description"=>"fondsCode",
													"default_value"=>"",
													"db_size"=>"26",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"16",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"120",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('fonds',
													array("description"=>"fonds",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>false,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Fondsen"));

		$this->addField('fondsOmschrijving',
													array("description"=>"fonds omschrijving",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"form_extra"=>'onChange="javascript:fondsOmschrijvingChange();"'));

		$this->addField('transactieType',
													array("description"=>"transactieType", 
													"default_value"=>$standaard['OrderStandaardTransactieType'],
													"db_size"=>"4",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_options"=>$__ORDERvar['transactieType'],
													"form_select_option_notempty"=>false,
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('transactieSoort',
													array("description"=>"transactieSoort",
													"default_value"=>'',
													"db_size"=>"2",
													"db_type"=>"char",
													"form_type"=>"selectKeyed",
													"form_options"=>$__ORDERvar['transactieSoort'],
													"form_select_option_notempty"=>false,
													"form_size"=>"2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('tijdsLimiet',
													array("description"=>"tijdsLimiet",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

	  $this->addField('tijdsSoort',
													array("description"=>"soort tijdlimiet",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_options"=>$__ORDERvar["tijdsSoort"],
													"form_select_option_notempty"=>true,
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_width"=>"150",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('koersLimiet',
													array("description"=>"koersLimiet",
													"default_value"=>"0.000",
													"db_size"=>"12,3",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"12,3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_format"=>"%01.3f",
													"list_numberformat"=>2,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

			$this->addField('Depotbank',
													array("description"=>"Depotbank",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_select_option_notempty"=>true,
													"form_size"=>"15",
													"form_visible"=>true,
													"list_width"=>"100",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('laatsteStatus',
													array("description"=>"status",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_options"=>$__ORDERvar['status'],
													"form_select_option_notempty"=>true,
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('memo',
													array("description"=>"memo",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"35",
													"form_rows"=>"6",
													"form_visible"=>true,
													"list_width"=>"100",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('status',
													array("description"=>"logboek",
													"default_value"=>"",
													"db_size"=>"65",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"40",
													"form_extra"=>" READONLY ",
													"form_rows"=>"6",
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
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
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('batchId',
													array("description"=>"batchId",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>false,"list_width"=>"100",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('OrderSoort',
													array("description"=>"Soort order",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"varchar",
													"form_type"=>"radio",
													"form_options"=>array('M'=>'Meervoudig (1 instrument; meerdere portefeuilles)','E'=>'Enkelvoudig (1 poretefeuille; 1 instrument)','C'=>'Combinatie (1 portefeuille; meerdere instrumenten)'),
													"form_size"=>"1",
													"form_extra"=>"",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"120",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('giraleOrder',
													array("description"=>"Girale order",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
                          "form_extra"=>'onChange="javascript:setAantal();"',
													"form_visible"=>true,
													"list_visible"=>false,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));
                          
  }
}
?>