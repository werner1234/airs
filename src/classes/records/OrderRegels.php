<?php
/*
    AE-ICT CODEX source module versie 1.6, 31 mei 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/03/15 16:29:12 $
    File Versie         : $Revision: 1.33 $

    $Log: OrderRegels.php,v $
    Revision 1.33  2017/03/15 16:29:12  rvv
    *** empty log message ***

    Revision 1.32  2016/08/21 13:17:03  rvv
    *** empty log message ***

    Revision 1.31  2016/02/17 19:37:01  rvv
    *** empty log message ***

    Revision 1.30  2015/10/18 13:38:35  rvv
    *** empty log message ***

    Revision 1.29  2015/09/23 14:57:02  rvv
    *** empty log message ***

    Revision 1.28  2015/09/16 16:20:12  rvv
    *** empty log message ***

    Revision 1.27  2015/07/08 15:35:21  rvv
    *** empty log message ***

    Revision 1.19  2014/12/18 07:23:35  rvv
    *** empty log message ***

    Revision 1.18  2014/02/09 11:05:15  rvv
    *** empty log message ***

    Revision 1.17  2013/04/07 16:05:04  rvv
    *** empty log message ***

    Revision 1.16  2012/12/22 15:29:19  rvv
    *** empty log message ***

    Revision 1.15  2012/04/11 17:14:09  rvv
    *** empty log message ***

    Revision 1.14  2012/01/25 19:07:00  rvv
    *** empty log message ***

    Revision 1.13  2011/12/21 19:16:40  rvv
    *** empty log message ***

    Revision 1.12  2011/12/18 14:22:17  rvv
    *** empty log message ***

    Revision 1.11  2011/12/04 12:52:04  rvv
    *** empty log message ***

    Revision 1.10  2011/10/30 13:34:03  rvv
    *** empty log message ***

    Revision 1.9  2010/07/25 14:40:47  rvv
    *** empty log message ***

    Revision 1.8  2009/11/29 15:14:51  rvv
    *** empty log message ***

    Revision 1.7  2009/10/17 15:58:58  rvv
    *** empty log message ***

    Revision 1.6  2009/09/12 11:14:24  rvv
    *** empty log message ***

    Revision 1.5  2007/11/26 15:15:15  rvv
    *** empty log message ***

    Revision 1.4  2006/10/18 06:54:35  rvv
    ordercontrole toevoegingen

    Revision 1.2  2006/06/28 12:44:53  cvs
    *** empty log message ***

    Revision 1.1  2006/06/08 14:47:14  cvs
    *** empty log message ***



*/

class OrderRegels extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function OrderRegels()
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
  	($this->get("orderid")=="")?$this->setError("orderid", vt("Mag niet leeg zijn!")):true;
		($this->get("portefeuille")=="")?$this->setError("portefeuille", vt("Mag niet leeg zijn!")):true;
		($this->get("rekeningnr")=="")?$this->setError("rekeningnr", vt("Mag niet leeg zijn!")):true;
		($this->get("controle")=='2')?$this->setError("controle", vt("Bevestig controle.")):true;
		($this->get("controle")=='1')?$this->setError("controle", vt("Bevestig controle.")):true;

		$aantal = $this->get("aantal");
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
	 $query="SELECT laatsteStatus FROM Orders WHERE orderid='".$this->get("orderid")."'";
	 $db->SQL($query);
	 $status=$db->lookupRecord();
   $query="SELECT check_module_ORDERNOTAS FROM Vermogensbeheerders limit 1";
   $db->SQL($query);
	 $nota=$db->lookupRecord();

	  if($_SESSION['usersession']['gebruiker']['ordersNietAanmaken']==1)
    {
      if($this->get('id') == 0)
       return false;
    }
    if($_SESSION['usersession']['gebruiker']['ordersNietVerwerken']==1)
    {
      if($status['laatsteStatus'] > 0)
        return false;
    }


 	  if($status['laatsteStatus']>0 && $nota['check_module_ORDERNOTAS']==0)
	    $level=1;
     
    $query="SELECT sum(aantal) as aantal FROM OrderRegels WHERE orderid='".$this->get("orderid")."'"; 
    $db->SQL($query);
	  $regels=$db->lookupRecord();
    $query="SELECT aantal FROM Orders WHERE orderid='".$this->get("orderid")."'"; 
    $db->SQL($query);
	  $order=$db->lookupRecord();
    if($regels['aantal'] <> $order['aantal'] && $status['laatsteStatus'] == 2)
      $level=7;
     
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

	/*
  * Table definition
  */
  function defineData()
  {
    global $__ORDERvar,$__appvar;
    $this->data['name']  = "Orderregels";
    $this->data['table']  = "OrderRegels";
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
													"list_order"=>"true"));

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
													"list_order"=>"true"));

		$this->addField('portefeuille',
													array("description"=>"Portefeuille",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "form_extra"=>'onchange="lookupPort()"'));

		$this->addField('rekeningnr',
													array("description"=>"Rekeningnr.",
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
													"list_order"=>"true"));

		$this->addField('valuta',
													array("description"=>"Valuta",
													"default_value"=>"EUR",
													"db_size"=>"6",
													"db_type"=>"varchar",
							            "form_type"=>"select",
													"form_size"=>"1",
													"form_select_option_notempty"=>true,
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
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
													"list_align"=>"right",
													"list_width"=>"100",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('transactieAantal',
													array("description"=>"transactieAantal",
													"default_value"=>"",
													"db_size"=>"12,4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"8",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.4f",
													"list_numberformat"=>4,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('status',
													array("description"=>"Status",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"list_conversie"=>$__ORDERvar["status"]));

		$this->addField('controle_regels',
													array("description"=>"Controle regels",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

				$this->addField('CheckResult',
													array("description"=>"CheckResult",
													"default_value"=>"",
													"db_size"=>"65536",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('controle',
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
													"list_order"=>"true"));


		$this->addField('memo',
													array("description"=>"Memo",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('handelsDag',
													array("description"=>"Handelsdag",
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

		$this->addField('handelsTijd',
													array("description"=>"Handelstijd",
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

		$this->addField('beurs',
													array("description"=>"Beurs",
													"db_size"=>"4",
													"db_type"=>"varchar",
													"form_size"=>"12",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT Beurs,Omschrijving FROM Beurzen ",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Beurzen"));

			$this->addField('interneNummer',
													array("description"=>"Interne nummer",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_size"=>"12",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('memoHandel',
													array("description"=>"Memo",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('valutakoers',
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
													"list_order"=>"true"));

		$this->addField('fondsKoers',
													array("description"=>"Fondskoers",
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
													"list_order"=>"true"));

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
													"list_order"=>"true"));
                          
			$this->addField('opgelopenRente',
													array("description"=>"Opgelopen rente",
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


		$this->addField('brutoBedrag',
													array("description"=>"Bruto bedrag",
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
													"list_format"=>"%01.2f",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('aanvullendeInfo',
													array("description"=>"Extra info",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('definitief',
													array("description"=>"Definitief",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
if($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "AEI" || $__appvar["bedrijf"] == "TEST" || $__appvar["bedrijf"] == "FDX"  || $__appvar["bedrijf"]=='VEC')
{
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
													"keyIn"=>"BICcodes"));
 
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
													"keyIn"=>"BICcodes"));                         

 		$this->addField('USDsettlement',
													array("description"=>"USD-settlement",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true")); 
}                                                    
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

		$this->addField('add_user',
													array("description"=>"add_user",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>false,
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
													"form_visible"=>false,
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
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
	
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
													"list_order"=>"true"));

  }
}
?>
