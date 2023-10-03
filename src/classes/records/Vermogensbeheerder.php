<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/06/24 15:13:45 $
 		File Versie					: $Revision: 1.142 $

 		$Log: Vermogensbeheerder.php,v $
 		Revision 1.142  2020/06/24 15:13:45  rvv
 		*** empty log message ***
 		
 		Revision 1.141  2020/05/07 05:39:18  rvv
 		*** empty log message ***
 		
 		Revision 1.140  2020/04/22 15:36:54  rvv
 		*** empty log message ***
 		
 		Revision 1.139  2020/02/09 10:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.138  2020/01/11 19:36:29  rvv
 		*** empty log message ***
 		
 		Revision 1.137  2020/01/08 14:27:35  rvv
 		*** empty log message ***
 		
 		Revision 1.136  2019/10/30 07:37:00  rvv
 		*** empty log message ***
 		
 		Revision 1.135  2019/10/05 17:32:54  rvv
 		*** empty log message ***
 		
 		Revision 1.134  2019/08/24 17:32:02  rvv
 		*** empty log message ***
 		
 		Revision 1.133  2019/08/08 07:35:21  rm
 		6348
 		
 		Revision 1.132  2019/03/23 17:04:21  rvv
 		*** empty log message ***
 		
 		Revision 1.131  2018/10/13 17:15:10  rvv
 		*** empty log message ***
 		
 		Revision 1.130  2018/09/19 17:19:08  rvv
 		*** empty log message ***
 		
 		Revision 1.129  2018/09/02 12:03:11  rvv
 		*** empty log message ***
 		
 		Revision 1.128  2018/07/21 15:51:44  rvv
 		*** empty log message ***
 		
 		Revision 1.127  2018/06/27 09:03:43  rm
 		6560
 		
 		Revision 1.126  2018/02/11 13:23:01  rvv
 		*** empty log message ***
 		
 		Revision 1.125  2018/01/03 10:06:09  rvv
 		*** empty log message ***
 		
 		Revision 1.124  2017/12/30 16:32:11  rvv
 		*** empty log message ***
 		
 		Revision 1.123  2017/12/16 18:34:42  rvv
 		*** empty log message ***
 		
 		Revision 1.122  2017/12/14 09:47:46  rvv
 		*** empty log message ***
 		
 		Revision 1.121  2017/12/13 17:00:57  rvv
 		*** empty log message ***
 		
 		Revision 1.120  2017/12/04 11:04:24  cvs
 		call 6349
 		
 		Revision 1.119  2017/12/02 19:09:44  rvv
 		*** empty log message ***
 		
 		Revision 1.118  2017/11/29 09:13:39  rvv
 		*** empty log message ***
 		
 		Revision 1.117  2017/11/25 20:20:06  rvv
 		*** empty log message ***
 		
 		Revision 1.116  2017/09/20 13:05:05  rvv
 		*** empty log message ***
 		
 		Revision 1.115  2017/09/13 09:58:46  rvv
 		*** empty log message ***
 		
 		Revision 1.114  2017/07/08 17:14:42  rvv
 		*** empty log message ***
 		
 		Revision 1.113  2017/06/28 15:13:39  rvv
 		*** empty log message ***
 		
 		Revision 1.112  2017/05/03 14:30:20  rvv
 		*** empty log message ***
 		
 		Revision 1.111  2017/04/22 16:39:58  rvv
 		*** empty log message ***
 		
 		Revision 1.110  2017/04/02 05:52:32  rvv
 		*** empty log message ***
 		
 		Revision 1.109  2017/02/08 16:18:22  rvv
 		*** empty log message ***
 		
 		Revision 1.108  2017/01/21 17:05:18  rvv
 		*** empty log message ***
 		
 		Revision 1.107  2017/01/07 16:08:15  rvv
 		*** empty log message ***
 		
 		Revision 1.106  2016/12/30 20:51:30  rvv
 		*** empty log message ***
 		
 		Revision 1.105  2016/12/12 07:47:54  rvv
 		*** empty log message ***
 		
 		Revision 1.104  2016/10/23 11:36:50  rvv
 		*** empty log message ***
 		
 		Revision 1.103  2016/09/14 13:04:59  rvv
 		*** empty log message ***
 		
 		Revision 1.102  2016/07/20 16:03:51  rvv
 		*** empty log message ***
 		
 		Revision 1.101  2016/07/10 10:02:23  rvv
 		*** empty log message ***
 		
 		Revision 1.100  2016/04/06 15:33:18  rvv
 		*** empty log message ***
 		
 		Revision 1.99  2016/03/13 16:08:01  rvv
 		*** empty log message ***
 		
 		Revision 1.98  2016/03/02 17:00:53  rvv
 		*** empty log message ***
 		
 		Revision 1.97  2016/01/23 17:57:41  rvv
 		*** empty log message ***
 		
 		Revision 1.96  2015/11/14 13:31:43  rvv
 		*** empty log message ***
 		
 		Revision 1.95  2015/11/11 17:15:47  rvv
 		*** empty log message ***
 		
 		Revision 1.94  2015/11/05 07:26:47  rvv
 		*** empty log message ***
 		
 		Revision 1.93  2015/10/28 16:36:36  rvv
 		*** empty log message ***
 		
 		Revision 1.92  2015/10/19 06:11:56  rvv
 		*** empty log message ***
 		
 		Revision 1.91  2015/09/23 14:57:02  rvv
 		*** empty log message ***
 		
 		Revision 1.90  2015/09/05 17:32:26  rvv
 		*** empty log message ***
 		
 		Revision 1.89  2015/09/05 16:18:24  rvv
 		*** empty log message ***
 		
 		Revision 1.88  2015/05/27 16:18:37  rvv
 		*** empty log message ***
 		
 		Revision 1.87  2015/04/26 12:23:49  rvv
 		*** empty log message ***
 		
 		Revision 1.86  2015/01/31 19:54:14  rvv
 		*** empty log message ***
 		
 		Revision 1.85  2015/01/28 13:17:45  rm
 		Participanten
 		
 		Revision 1.84  2014/12/06 18:08:01  rvv
 		*** empty log message ***
 		
 		Revision 1.83  2014/11/30 13:04:47  rvv
 		*** empty log message ***
 		
 		Revision 1.82  2014/10/19 08:56:08  rvv
 		*** empty log message ***
 		
 		Revision 1.81  2014/09/13 14:39:56  rvv
 		*** empty log message ***
 		
 		Revision 1.80  2014/08/09 14:44:18  rvv
 		*** empty log message ***
 		
 		Revision 1.79  2014/03/29 16:24:32  rvv
 		*** empty log message ***
 		
 		Revision 1.78  2014/03/08 16:59:39  rvv
 		*** empty log message ***
 		
 		Revision 1.77  2014/02/22 18:38:01  rvv
 		*** empty log message ***
 		
 		Revision 1.76  2014/01/18 17:22:27  rvv
 		*** empty log message ***
 		
 		Revision 1.75  2013/12/22 16:00:12  rvv
 		*** empty log message ***
 		
 		Revision 1.74  2013/12/08 13:49:23  rvv
 		*** empty log message ***
 		
 		Revision 1.73  2013/08/04 10:44:18  rvv
 		*** empty log message ***
 		
 		Revision 1.72  2013/05/22 15:50:48  rvv
 		*** empty log message ***
 		
 		Revision 1.71  2013/05/01 15:48:34  rvv
 		*** empty log message ***
 		
 		Revision 1.70  2013/04/24 16:10:35  rvv
 		*** empty log message ***
 		
 		Revision 1.69  2013/04/20 16:37:06  rvv
 		*** empty log message ***
 		
 		Revision 1.68  2013/03/27 18:49:35  rvv
 		*** empty log message ***
 		
 		Revision 1.67  2012/12/12 16:50:45  rvv
 		*** empty log message ***
 		
*/

class Vermogensbeheerder extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function Vermogensbeheerder()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],"0");
  }

	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}

	function checkAccess($type)
	{
		return checkAccess($type);
	}

	function validate()
	{
		($this->get("Vermogensbeheerder")=="")?$this->setError("Vermogensbeheerder",vt("Mag niet leeg zijn!")):true;
    
    if($this->get("PerformanceBerekening")=="" || $this->get("PerformanceBerekening")=="0")
    {
      $this->setError("PerformanceBerekening",vt("Performance berekening mag niet leeg zijn!"));
    }
    
//		(!is_numeric($this->get("Koers")))?$this->setError("Koers","Moet een getal zijn."):true;

		$query  = "SELECT id FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->get("Vermogensbeheerder")."' ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->NextRecord();

		if($DB->records() >0 && $this->get("id") <> $data[id])
		{
			$this->setError("Vermogensbeheerder", vtb("%s bestaat al", array($this->get("Vermogensbeheerder"))));
		}

		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    global $__ORDERvar;
    $this->data['table']  = "Vermogensbeheerders";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

		$this->addField('id',
													array("description"=>"id",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Vermogensbeheerder',
													array("description"=>"Vermogensbeheerder",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_size"=>"10",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"key_field"=>true));

		$this->addField('Naam',
													array("description"=>"Naam",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_size"=>"50",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('Adres',
													array("description"=>"Adres",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_size"=>"25",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Woonplaats',
													array("description"=>"Woonplaats",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('rekening',
													array("description"=>"Rekening",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('bank',
													array("description"=>"Bank",
													"db_size"=>"40",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('LEInrVBH',
										array("description"=>"LEI-nummer",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Contactpersoon',
													array("description"=>"Contactpersoon",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Telefoon',
													array("description"=>"Telefoon",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_size"=>"25",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Fax',
													array("description"=>"Fax",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_size"=>"25",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Email',
													array("description"=>"Email",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_size"=>"60",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('website',
													array("description"=>"Website",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_size"=>"50",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Exportpad',
													array("description"=>"Exportdatabase",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_size"=>"100",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

			$this->addField('ddInleesLocatie',
													array("description"=>"Document inlees locatie",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_size"=>"50",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>false,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

			$this->addField('ddInleesPortefeuillePreg',
													array("description"=>"Document portefeuille preg",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_size"=>"50",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>false,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('NAWPad',
													array("description"=>"NAW-gegevens",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_size"=>"100",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Layout',
													array("description"=>"Rapport-layout",
													"db_size"=>"4",
													"db_type"=>"int",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('OIH',
													array("description"=>"Onderverdeling in Hoofdsector",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('OIS',
													array("description"=>"Onderverdeling in Beleggingssector",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('OIR',
													array("description"=>"Onderverdeling in Regio",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('HSE',
													array("description"=>"Huidige Samenstelling Effectenportefeuille",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('OIB',
													array("description"=>"Onderverdeling in Beleggingscategorie",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('OIV',
													array("description"=>"Onderverdeling in Valuta",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('PERF',
													array("description"=>"Performancemeting",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('VOLK',
													array("description"=>"Vergelijkend Overzicht",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('VHO',
													array("description"=>"Vergelijkend Historisch Overzicht",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('TRANS',
													array("description"=>"Transactie-overzicht",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('GRAFIEK',
													array("description"=>"Risico verdeling",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

			$this->addField('ATT',
													array("description"=>"Attributie",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AfdrukvolgordeOIH',
													array("description"=>"AfdrukvolgordeOIH",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AfdrukvolgordeOIS',
													array("description"=>"AfdrukvolgordeOIS",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AfdrukvolgordeOIR',
													array("description"=>"AfdrukvolgordeOIR",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AfdrukvolgordeHSE',
													array("description"=>"AfdrukvolgordeHSE",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AfdrukvolgordeOIB',
													array("description"=>"AfdrukvolgordeOIB",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AfdrukvolgordeOIV',
													array("description"=>"AfdrukvolgordeOIV",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AfdrukvolgordePERF',
													array("description"=>"AfdrukvolgordePERF",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AfdrukvolgordeVOLK',
													array("description"=>"AfdrukvolgordeVOLK",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AfdrukvolgordeVHO',
													array("description"=>"AfdrukvolgordeVHO",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AfdrukvolgordeTRANS',
													array("description"=>"AfdrukvolgordeTRANS",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AfdrukvolgordeGRAFIEK',
													array("description"=>"AfdrukvolgordeGRAFIEK",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AfdrukvolgordeATT',
													array("description"=>"AfdrukvolgordeATT",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AfdrukSortering',
													array("description"=>"Afdruk sortering",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_size"=>"20",
													"form_type"=>"select",
													"form_options"=>array("Client","Portefeuille","Postcode"),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Index1',
													array("description"=>"Aandelen-index",
													"db_size"=>"16",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'select_query' => "SELECT Fonds,Omschrijving FROM Fondsen WHERE EindDatum > NOW() OR EindDatum = '0000-00-00' ORDER BY Fonds",
													'select_query_ajax' => "SELECT Fonds,Omschrijving FROM Fondsen WHERE Fonds='{value}'",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Index2',
													array("description"=>"Obligatie-index",
													"db_size"=>"16",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'select_query' => "SELECT Fonds,Omschrijving FROM Fondsen WHERE EindDatum > NOW() OR EindDatum = '0000-00-00' ORDER BY Fonds",
													'select_query_ajax' => "SELECT Fonds,Omschrijving FROM Fondsen WHERE Fonds='{value}'",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('IndexRisicovrij',
													array("description"=>"Risicovrije-index",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'select_query' => "SELECT Fonds,Omschrijving FROM Fondsen WHERE EindDatum > NOW() OR EindDatum = '0000-00-00' ORDER BY Fonds",
													'select_query_ajax' => "SELECT Fonds,Omschrijving FROM Fondsen WHERE Fonds='{value}'",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('PerformanceBerekening',
													array("description"=>"Performance Berekening",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"leftt",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('MUT',
													array("description"=>"Mutatie-overzicht",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AfdrukvolgordeMUT',
													array("description"=>"AfdrukvolgordeMUT",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('VraagOmNAWImport',
													array("description"=>"Vraag om NAW-import bij aanmelden",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Gebruikersbeheer',
													array("description"=>"Gebruikersbeheer",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('SamenvoegenClientRapporten',
													array("description"=>"Samenvoegen Clientrapporten",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('PadPDFcombine',
													array("description"=>"Locatie PDFcombine",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_size"=>"100",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Logo',
													array("description"=>"Logo",
													"db_size"=>"35",
													"db_type"=>"varchar",
													"form_size"=>"35",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Export_dag_pad',
													array("description"=>"Dag-export pad",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_size"=>"65",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Export_maand_pad',
													array("description"=>"Maand-export pad",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_size"=>"65",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Export_kwartaal_pad',
													array("description"=>"Kwartaal-export pad",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_size"=>"65",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));


			$this->addField('Export_data_frontOffice',
													array("description"=>"Front office",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Export_data_dag',
													array("description"=>"Export data dag",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Export_data_maand',
													array("description"=>"Export data maand",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('order_controle',
													array("description"=>"Ordercontrole",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('grafiek_kleur',
													array("description"=>"Grafiek Kleur",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Export_data_kwartaal',
													array("description"=>"Export data kwartaal",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('FactuurBeheerfeeBerekening',
													array("description"=>"Factuurbeheerfee-berekening",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('csvSeperator',
													array("description"=>"CSV scheidingsteken",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"select",
													"form_options"=>array(",",";"),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('rapportLink',
													array("description"=>"Links in VOLK",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

	    $this->addField('attributieInPerformance',
													array("description"=>"Attributie in performance",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('rapportLinkUrl',
													array("description"=>"Link URL",
													"db_size"=>"150",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('OptieTools',
													array("description"=>"Optie Tools weergeven",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Attributie',
													array("description"=>"Attributie type",
													"db_size"=>"1",
													"db_type"=>"char",
													"form_type"=>"selectKeyed",
													"form_options"=>array('C'=>'Attributie per categorie','S'=>'Attributie per sector'),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('grafiek_sortering',
													array("description"=>"Grafiek op afdrukvolgorde ivp vermogen",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('check_rekeningmutaties',
													array("description"=>"check_rekeningmutaties",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('check_categorie',
													array("description"=>"check_categorie",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('check_sector',
													array("description"=>"check_sector",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('check_zorgplichtFonds',
													array("description"=>"Fonds zorgplicht controle",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('check_zorgplichtPortefeuille',
													array("description"=>"Portefeuille zorgplicht controle",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('check_hoofdcategorie',
													array("description"=>"check_hoofdcategorie",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('check_hoofdsector',
													array("description"=>"check_hoofdsector",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

	 $this->addField('check_afmCategorie',
													array("description"=>"check_afmCategorie",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

	 $this->addField('check_rekeningDepotbank',
													array("description"=>"check_rekening Depotbank",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

	 $this->addField('check_Beurs',
													array("description"=>"check_BEURS",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
	 $this->addField('check_BB_Landcodes',
													array("description"=>"check_BB_Landcodes",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

	 $this->addField('check_duurzaamheid',
													array("description"=>"check_duurzaamheid",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('check_duurzaamCategorie',
										array("description"=>"check_duurzaamCategorie",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                                                                                                                                                           
    $this->addField('check_module_CRM',
													array("description"=>"CRM module inschakelen",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

    $this->addField('check_module_CRM_eigenVelden',
													array("description"=>"CRM module eigen velden aanmaken",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


		$this->addField('check_module_ORDER',
													array("description"=>"ORDER module",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
                          "form_options"=>array(0=>'Uit',1=>'Versie 1',2=>'Versie 2'),
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('check_module_PORTAAL',
													array("description"=>"PORTAAL module inschakelen",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('portaalPeriode',
													array("description"=>"Periode",
													"db_size"=>"1",
													"db_type"=>"int",
													"form_type"=>"selectKeyed",
                          "form_select_option_notempty"=>true,
													"form_options"=>array('0'=>'Ytd','1'=>'Qtd'),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('check_module_SCENARIO',
													array("description"=>"Scenario-analyse module inschakelen",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('ScenarioAfwijkendProfielPDF',
										array("description"=>"Afwijkend profiel PDF",
													"default_value"=>"1",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('check_module_UREN',
													array("description"=>"Uren module inschakelen",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('ScenarioMinimaleKans',
													array("description"=>"Scenario Minimaal slagingspercentage",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
                          "form_size"=>"4",
													"form_visible"=>true,
													"form_format"=>"%01.1f",
													"list_format"=>"%01.1f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('ScenarioGewenstProfiel',
													array("description"=>"Scenario gewenst profiel",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));                          
                                                      
 		$this->addField('check_module_portefeuilleWaarde',
													array("description"=>"Portefeuillewaarde herrekenen module inschakelen",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  
    $this->addField('portefeuilleWaardeInclVkm',
                    array("description"=>"incl VKM",
                          "default_value"=>"",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "form_type"=>"checkbox",
                          "form_size"=>"4",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
    
                                                   
		$this->addField('check_module_ORDERNOTAS',
													array("description"=>"ORDER nota's genereren",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('OrderLoggingOpNota',
													array("description"=>"Logging op nota",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('OrderCheck',
													array("description"=>"Order check module",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('check_module_BOEKEN',
													array("description"=>"Voorlopige rekeningmutatie module inschakelen",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('check_module_FACTUURHISTORIE',
													array("description"=>"Factuur historie module inschakelen",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('FACTUURHISTORIE_gebruikLaatsteWaarde',
													array("description"=>"Factuur historie gebruik laatste factuur",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('check_sectorRegio',
													array("description"=>"check_sectorRegio",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('check_sectorAttributie',
													array("description"=>"check_sectorAttributie",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('check_historischePortefeuilleIndex',
													array("description"=>"check_historischePortIndex",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('orderAdviesNotificatie',
										array("description"=>"Advies notificatie",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_select_option_notempty"=>true,
													"form_options"=>$__ORDERvar['orderAdviesNotificaties'],
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		
    $this->addField('orderAdviesBcc',
                    array("description"=>"Advies bcc-adres",
                          "default_value"=>"",
                          "db_size"=>"200",
                          "db_type"=>"text",
                          "form_type"=>"text",
                          "form_size"=>"50",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

		$this->addField('orderredenVerplicht',
										array("description"=>"Orderreden verplicht",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_select_option_notempty"=>true,
													"form_options"=>array('0'=>'Niet verplicht','1'=>'Advies'),
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


			$this->addField('check_kruisposten',
													array("description"=>"check_kruisposten",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


		$this->addField('naamInExport',
													array("description"=>"Naam in export bestand.",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
/*
		$this->addField('uitgebreideAutoupdate',
													array("description"=>"Uitgebreide Autoupdate.",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
*/
		$this->addField('txtKoppeling',
													array("description"=>"Rapportage-tekst koppeling.",
													"default_value"=>"",
													"db_size"=>"30",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_options"=>array('Accountmanager'=>'Accountmanager','Risicoklasse'=>'Risicoklasse','SpecifiekeIndex'=>'SpecifiekeIndex',
													'ModelPortefeuille'=>'ModelPortefeuille','Vermogensbeheerder'=>'Vermogensbeheerder'),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

			$this->addField('maandRapportageYTD',
													array("description"=>"Maandrapportage YTD",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

			$this->addField('kwartaalRapportageYTD',
													array("description"=>"Kwartaalrapportage YTD",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

				$this->addField('CrmPortefeuilleInformatie',
													array("description"=>"CRM portefeuille informatie",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

				$this->addField('CrmPortefeuilleInformatie',
													array("description"=>"CRM portefeuille informatie",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

				$this->addField('CrmExtraSpatie',
													array("description"=>"CRM extra template spatie",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

				$this->addField('CrmClientNaam',
													array("description"=>"Gebruik client naam via CRM",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

				$this->addField('CrmTerugRapportage',
													array("description"=>"Via CRM data ontvangen.",
                          "default_value"=>"1",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_select_option_notempty"=>true,
													"form_options"=>array('0'=>'Uitgeschakeld','1'=>'Handmatige goedkeuring','2'=>'Automatisch verwerken (alles)','4'=>'Automatisch verwerken (rekeningmutaties)','8'=>'Automatisch verwerken (klantmutaties)'),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

				$this->addField('CrmAutomatischVerzenden',
													array("description"=>"CRM data verzenden.",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_select_option_notempty"=>true,
													"form_options"=>array('0'=>'Handmatige keuze','1'=>'Direct verzenden'),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
                          
				$this->addField('koersExport',
													array("description"=>"Alleen Koers/Fonds export",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_options"=>array('1'=>'Koersen 7dg + Alle fondsen','2'=>'Koersen 7dg + Fondsn 2dg'),
													"form_visible"=>true,
													"form_extra"=>"onchange='javascript:checkFieldStatus();'",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

				$this->addField('CRM_eigenTemplate',
													array("description"=>"CRM eigen template",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('CRM_alleenNAW',
													array("description"=>"CRM alleen NAW",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
		
    $this->addField('NAW_inclDocumenten',
                    array("description"=>"incl. documenten",
                          "db_size"=>"1",
                          "db_type"=>"tinyint",
                          "form_type"=>"checkbox",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true"));


		$this->addField('FactuurMinimumBedrag',
													array("description"=>"FactuurMinimumBedrag",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('FactuurMinimumPerTransactie',
													array("description"=>"Factuur minimumbedrag per transactie",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
                          
	  	$this->addField('VerouderdeKoersDagen',
													array("description"=>"Koers verouderd na x dagen",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

	  	$this->addField('verrekeningBestandsvergoeding',
													array("description"=>"Verrekening",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"selectKeyed",
													"form_select_option_notempty"=>true,
													"form_options"=>array('0'=>'na ontvangst','1'=>'na geaccordeerd'),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

		 	$this->addField('bestandsvergoedingBtw',
													array("description"=>"Verrekening voor/na btw",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"selectKeyed",
													"form_select_option_notempty"=>true,
													"form_options"=>array('0'=>'voor','1'=>'na'),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

		 	$this->addField('bestandsvergoedingNiveau',
													array("description"=>"Bestandsvergoeding op",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"selectKeyed",
													"form_select_option_notempty"=>true,
													"form_options"=>array('0'=>'Vermogensbeheerder','1'=>'Bedrijf'),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

			$this->addField('kwartaalCheck',
													array("description"=>"CRM rapport kwartaal check",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

				$this->addField('module_bestandsvergoeding',
													array("description"=>"Bestandsvergoeding module",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_select_option_notempty"=>true,
													"form_options"=>array('0'=>'Uitgeschakeld','1'=>'Berekenen','2'=>'Invoeren'),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('OrderStandaardType',
													array("description"=>"Order standaard type",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_select_option_notempty"=>true,
													"form_options"=>array('0'=>'Meervoudig','1'=>'Enkelvoudig','2'=>'Combinatie'),
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  
    $this->addField('orderLiqVerkopen',
                    array("description"=>"Incl. Verkopen",
                          "db_size"=>"1",
                          "db_type"=>"tinyint",
                          "form_type"=>"checkbox",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true"));
  
  
    $this->addField('OrderStandaardTransactieType',
													array("description"=>"Order standaard TransactieType",
													"default_value"=>"",
													"db_size"=>"2",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_options"=>array('B'=>'Bestens','IN'=>'Inline with market','L'=>'Limiet','SL'=>'Stoploss'),
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
 
 		$this->addField('OrderCheckClientNaam',
													array("description"=>"OrderCheck Client naamgeving",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_select_option_notempty"=>true,
													"form_options"=>array('0'=>'Client','1'=>'CRM_naw.zoekveld','2'=>'CRM_naw.naam'),
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                                                   
	  $this->addField('OrderStandaardTijdsSoort',
													array("description"=>"Order standaard tijdlimiet",
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
                          
		$this->addField('OrderOrderdesk',
													array("description"=>"Orderdesk",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('OrderStandaardMemo',
													array("description"=>"Standaard order memo",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"35",
													"form_rows"=>"6",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('OrderStatusKeuze',
													array("description"=>"Order status opties",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('orderPreValidatie',
													array("description"=>"Prevalidatie Orders Rapportages",
													"db_size"=>"1",
													"db_type"=>"int",
													"form_type"=>"selectKeyed",
                          "form_select_option_notempty"=>true,
													"form_options"=>array('0'=>'Uitgeschakeld','1'=>'Ingeschakeld','2'=>'PopUp'),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('transactiemeldingWaarde',
													array("description"=>"Transactiemeldings waarde",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('transactiemeldingEmail',
													array("description"=>"Transactiemelding email",
													"db_size"=>"200",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));


 		$this->addField('transactieMeldingType',
													array("description"=>"Transactie mail als pdf",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));                         

 		$this->addField('autoPortaalVulling',
													array("description"=>"Automatische portaal vulling",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));   
                                                    
 		$this->addField('fondsenmeldingEmail',
													array("description"=>"Nieuw fonds melding email",
													"db_size"=>"200",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));
	
		$this->addField('emailSignaleringen',
										array("description"=>"Signaleringen email",
													"db_size"=>"200",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('standaardRapportageFreq',
										array("description"=>"Standaard rapportage freq.",
													"db_size"=>"1",
													"form_type"=>"selectKeyed",
													"form_options"=>array('m'=>'Maand','k'=>'Kwartaal','c'=>'CRM','r'=>'Rapportagedatum'),
													"form_size"=>"100",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));
    $this->addField('bedragTransactiesignalering',
                    array("description"=>"Bedrag Transactiesignalering",
                          "db_size"=>"0",
                          "default_value"=>"0",
                          "db_type"=>"double",
                          "form_type"=>"text",
                          "form_format"=>"%01.2f",
                          "list_format"=>"%01.2f",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true"));
			
		$this->addField('orderControleEmail',
													array("description"=>"Ordercontrole email",
													"db_size"=>"200",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));   
                          
		$this->addField('orderCheckMaxAge',
													array("description"=>"Order/transactie check niet ouder dan x dagen",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

				$this->addField('check_rekeningATT',
													array("description"=>"check_rekening Attributie",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
					$this->addField('check_rekeningCat',
													array("description"=>"check_rekening Categorie",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
 	      	$this->addField('geenStandaardSector',
													array("description"=>"Geen standaardsector gebruiken",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));
                          
			    $this->addField('ddVerwijderen',
													array("description"=>"Documenten kunnen Verwijderen",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));
      
		     $this->addField('TransactiefeeBtw',
													array("description"=>"Transactiefee verrekening voor/na btw",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"selectKeyed",
													"form_select_option_notempty"=>true,
													"form_options"=>array('0'=>'voor','1'=>'na'),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));
                          
			    $this->addField('BeheerfeeAdministratieVergoedingVast',
													array("description"=>"Beheerfee administratievergoeding vastbedrag",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));
                          
 		     $this->addField('check_module_VRAGEN',
													array("description"=>"CRM VRAGEN module inschakelen",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                                               
 		     $this->addField('frontofficeClientExcel',
													array("description"=>"Rapportage frontoffice Clienten naar Excel inschakelen",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
 		     $this->addField('OrderuitvoerBewaarder',
													array("description"=>"Orderuitvoer op bewaarder",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  
    $this->addField('orderViaConsolidatie',
                    array("description"=>"Orders via consolidaties",
                          "default_value"=>"",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "form_type"=>"checkbox",
                          "form_size"=>"4",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
         
         $this->addField('check_participants',
													array("description"=>"Participantenregistratie",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"1",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
         $this->addField('check_portaalCrmVink',
													array("description"=>"Portaalvulling via CRM rapportage instellingen",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"1",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
 
 		    $this->addField('check_portaalDocumenten',
       										array("description"=>"Documenten naar Portaal",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"1",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
  	  	$this->addField('Einddatum',
													array("description"=>"Einddatum",
													"db_size"=>"0",
													"db_type"=>"date",
													"default_value"=>"2037-12-31",
													"form_type"=>"calendar",
													"form_visible"=>true,
                          "list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));       
                          
		$this->addField('orderMaxBedrag',
													array("description"=>"Max. bedrag:",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
                          "form_size"=>"8",
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));      
                                                                               
		$this->addField('orderMaxPercentage',
													array("description"=>"Max. weging order:",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
                          "form_size"=>"2",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));  

		$this->addField('orderMaxPercentagePositie',
													array("description"=>"Max. weging positie:",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
                          "form_size"=>"2",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('orderVierOgen',
										array("description"=>"4-ogen principe",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"1",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('orderGeenHervalidatie',
										array("description"=>"Order geen hervalidatie",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"1",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('SdFrequentie',
													array("description"=>"Frequentie",
													"db_size"=>"2",
													"db_type"=>"char",
													"form_type"=>"selectKeyed",
													"form_options"=>array('w'=>'Wekelijks (vrijdag met opvulling)','wv'=>'Wekelijks (alleen vrijdag)','2w'=>'Tweewekelijks','m'=>'Maandelijks'),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
  	$this->addField('SdMethodiek',
													array("description"=>"Methodiek",
													"db_size"=>"2",
													"db_type"=>"char",
													"form_type"=>"selectKeyed",
													"form_options"=>array('s'=>'Startdate','r'=>'Rolling'),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));  
		$this->addField('SdWaarnemingen',
													array("description"=>"Aantal waarnemingen",
													"db_size"=>"11",
                          "default_value"=>"36",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));                     
 		 $this->addField('SdOpbouw',
       										array("description"=>"Opbouw naar frequentie",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"1",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));  
                          
     $this->addField('aanlevertijd',
                           array("description"=>"Aanlevertijd",
                           "default_value"=>"00:00:00",
                           "db_size"=>"0",
                           "db_type"=>"time",
                           "form_type"=>"time",
                           "form_size"=>"5",
                           "form_visible"=>true,
                           "list_visible"=>true,
                           "list_width"=>"100",
                           "list_align"=>"left",
                           "list_search"=>false,
                           "list_order"=>"true"));

		$this->addField('kasbankBrokerVerwerking',
										array("description"=>"Kasbank broker verwerking",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"1",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('orderAkkoord',
										array("description"=>"Attributie type",
													"db_size"=>"1",
													"db_type"=>"char",
													"form_type"=>"selectKeyed",
													"form_select_option_notempty"=>true,
													"form_options"=>array('0'=>'Bulkorders','1'=>'Orders','2'=>'Beiden'),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
		
		$this->addField('orderFxToestaan',
										array("description"=>"Fx transacties toestaan",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('orderTransRep',
										array("description"=>"MIFID Trans.-reporting",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('orderTransRepDecisionMaker',
										array("description"=>"MIFID DecisionMaker",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('adventVerwerking',
										array("description"=>"Advent verwerking",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('millogicVerwerking',
										array("description"=>"Millogic verwerking",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('HTMLRapportage',
										array("description"=>"HTML-Rapportage",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('WelkomMenuV2',
										array("description"=>"Welkom-menuV2",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('vvRappToegestaan',
										array("description"=>"VV-rapp Toegestaan",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('rapportDoorkijk',
										array("description"=>"Doorkijk",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('eMailInlezen',
										array("description"=>"eMail inlezen",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_options"=>array(0=>'Uit',1=>'Handmatig inlezen',2=>'Automatisch inlezen'),
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

    $this->addField('portaalVragenToestaan',
                    array("description"=>"Vragenlijst in portaal",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "form_type"=>"checkbox",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true"));
    
		$this->addField('spreadKosten',
										array("description"=>"Spreadkosten in basispunten",
													"db_size"=>"0",
													"default_value"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('jaarafsluitingPerBewaarder',
										array("description"=>"Jaarafsluiting per bewaarder",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('fondskostenDoorkijkExport',
										array("description"=>"Fondskosten doorkijk export",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('emailPortaalvulling',
										array("description"=>"Email portaalvulling",
													"db_size"=>"200",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));
		
    $this->addField('ixpVerwerking',
                    array("description"=>"IXP-verwerking",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "form_type"=>"checkbox",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true"));
  
    $this->addField('morningstar',
                    array("description"=>"Morningstar",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "form_type"=>"selectKeyed",
                          "form_options"=>array('1'=>'Standaard','2'=>'MS Direct','3'=>'MS Docu','4'=>'MS Compleet'),
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true"));
  
    $this->addField('callableDatumGebruiken',
                    array("description"=>"Gebruik Callable-date obligaties",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "form_type"=>"checkbox",
                          "form_visible"=>true,
                          "list_visible"=>false,
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true"));
  
    $this->addField('portaalDailyClientSync',
                    array("description"=>"Met client synchronisatie zonder pdf",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "form_type"=>"checkbox",
                          "form_visible"=>true,
                          "list_visible"=>false,
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true"));
  
    $this->addField('CRM_GesprVerslagVerwWijz',
                    array("description"=>"Gespreksverslagen verwijderen/wijz",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "form_type"=>"checkbox",
                          "form_visible"=>true,
                          "list_visible"=>false,
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
    
    $this->addField('documentloosPortaal',
                    array("description"=>"Documentloos Portaal",
                          "default_value"=>"",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "form_type"=>"checkbox",
                          "form_size"=>"4",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
  
  }
}
?>