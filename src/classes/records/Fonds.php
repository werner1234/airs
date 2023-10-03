<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/03/07 14:43:19 $
 		File Versie					: $Revision: 1.120 $

        $Log: Fonds.php,v $
        Revision 1.120  2020/03/07 14:43:19  rvv
        *** empty log message ***

        Revision 1.119  2020/03/04 08:47:35  rvv
        *** empty log message ***

        Revision 1.118  2020/01/22 15:58:51  rvv
        *** empty log message ***

        Revision 1.117  2020/01/18 17:54:56  rvv
        *** empty log message ***

        Revision 1.116  2019/08/28 08:20:49  rvv
        *** empty log message ***

        Revision 1.115  2019/07/10 15:35:57  rvv
        *** empty log message ***

        Revision 1.114  2019/05/25 16:16:48  rvv
        *** empty log message ***

        Revision 1.113  2019/04/24 14:12:05  cvs
        call 7630

        Revision 1.112  2019/03/23 17:04:21  rvv
        *** empty log message ***

        Revision 1.111  2019/02/27 13:47:06  rvv
        *** empty log message ***

        Revision 1.110  2019/01/26 19:32:07  rvv
        *** empty log message ***

        Revision 1.109  2019/01/16 16:34:25  rvv
        *** empty log message ***

        Revision 1.108  2018/11/28 13:13:56  rvv
        *** empty log message ***

        Revision 1.107  2018/07/14 13:58:15  rvv
        *** empty log message ***

        Revision 1.106  2018/02/22 07:44:01  rvv
        *** empty log message ***

        Revision 1.105  2018/02/21 16:53:53  rvv
        *** empty log message ***

        Revision 1.104  2018/02/04 15:43:46  rvv
        *** empty log message ***

        Revision 1.103  2017/11/25 20:20:06  rvv
        *** empty log message ***

        Revision 1.102  2017/08/19 18:12:17  rvv
        *** empty log message ***

        Revision 1.101  2017/08/09 16:08:28  rvv
        *** empty log message ***

        Revision 1.100  2017/06/24 16:48:38  rvv
        *** empty log message ***

        Revision 1.99  2017/03/29 16:00:09  rvv
        *** empty log message ***

        Revision 1.98  2017/02/08 16:18:21  rvv
        *** empty log message ***

        Revision 1.97  2017/01/25 15:48:30  rvv
        *** empty log message ***

        Revision 1.96  2017/01/11 16:25:33  rvv
        *** empty log message ***

        Revision 1.95  2016/12/30 20:51:30  rvv
        *** empty log message ***

        Revision 1.94  2016/12/24 16:28:17  rvv
        *** empty log message ***

        Revision 1.93  2016/12/21 16:29:00  rvv
        *** empty log message ***

        Revision 1.92  2016/11/23 16:34:28  rvv
        *** empty log message ***

        Revision 1.91  2016/11/19 18:58:47  rvv
        *** empty log message ***

        Revision 1.90  2016/11/16 16:47:43  rvv
        *** empty log message ***

        Revision 1.89  2016/10/09 14:58:50  rvv
        *** empty log message ***

        Revision 1.88  2016/07/13 08:14:08  rvv
        *** empty log message ***

        Revision 1.87  2016/06/27 06:13:06  rvv
        *** empty log message ***

        Revision 1.86  2016/06/25 16:25:35  rvv
        *** empty log message ***

        Revision 1.85  2016/06/05 12:34:45  rvv
        *** empty log message ***

        Revision 1.84  2016/05/29 14:03:55  rvv
        *** empty log message ***

        Revision 1.83  2016/05/09 05:45:20  rvv
        *** empty log message ***

        Revision 1.82  2016/05/08 19:21:34  rvv
        *** empty log message ***

        Revision 1.81  2016/03/12 17:46:21  rvv
        *** empty log message ***

        Revision 1.80  2015/12/13 08:59:27  rvv
        *** empty log message ***

        Revision 1.79  2015/11/29 13:05:52  rvv
        *** empty log message ***

        Revision 1.78  2015/10/28 16:36:36  rvv
        *** empty log message ***

        Revision 1.77  2015/10/07 19:41:38  rvv
        *** empty log message ***

        Revision 1.76  2015/09/20 17:28:35  rvv
        *** empty log message ***

        Revision 1.75  2015/09/03 07:05:42  rvv
        *** empty log message ***

        Revision 1.74  2015/09/03 03:39:50  rvv
        *** empty log message ***

        Revision 1.73  2015/06/03 14:54:48  rvv
        *** empty log message ***

        Revision 1.72  2015/05/13 14:46:32  rm
        validatie

        Revision 1.71  2015/05/01 14:10:55  rm
        fonds turbo en optie symbolen

        Revision 1.70  2015/04/05 07:30:21  rvv
        *** empty log message ***

        Revision 1.69  2015/03/22 10:34:26  rvv
        *** empty log message ***

        Revision 1.68  2015/02/04 16:11:14  rvv
        *** empty log message ***

        Revision 1.67  2015/01/21 16:51:31  rvv
        *** empty log message ***

        Revision 1.66  2015/01/11 12:34:54  rvv
        *** empty log message ***

        Revision 1.65  2015/01/04 13:32:11  rvv
        *** empty log message ***

        Revision 1.64  2015/01/04 13:23:28  rvv
        *** empty log message ***

        Revision 1.63  2015/01/04 13:14:31  rvv
        *** empty log message ***

        Revision 1.62  2015/01/03 16:07:20  rvv
        *** empty log message ***

        Revision 1.61  2014/12/20 21:55:13  rvv
        *** empty log message ***

        Revision 1.60  2014/12/03 17:09:47  rvv
        *** empty log message ***

        Revision 1.59  2014/11/19 16:43:28  rvv
        *** empty log message ***

        Revision 1.58  2014/10/22 15:46:05  rvv
        *** empty log message ***

        Revision 1.57  2014/07/27 11:25:09  rvv
        *** empty log message ***

        Revision 1.56  2014/07/06 12:30:45  rvv
        *** empty log message ***

        Revision 1.55  2014/06/18 15:45:08  rvv
        *** empty log message ***

        Revision 1.54  2014/06/14 16:37:52  rvv
        *** empty log message ***

        Revision 1.53  2014/05/14 16:03:32  rvv
        *** empty log message ***

        Revision 1.52  2014/05/14 11:26:15  rvv
        *** empty log message ***

        Revision 1.51  2014/05/10 13:51:28  rvv
        *** empty log message ***

        Revision 1.50  2014/04/02 15:50:43  rvv
        *** empty log message ***

        Revision 1.49  2014/03/29 16:24:32  rvv
        *** empty log message ***

        Revision 1.48  2014/03/16 11:15:48  rvv
        *** empty log message ***

        Revision 1.47  2014/02/02 10:44:50  rvv
        *** empty log message ***

        Revision 1.46  2014/01/12 12:53:35  rvv
        *** empty log message ***

        Revision 1.45  2014/01/11 15:56:16  rvv
        *** empty log message ***

        Revision 1.44  2013/12/23 16:35:15  rvv
        *** empty log message ***

        Revision 1.43  2013/12/21 18:26:50  rvv
        *** empty log message ***

        Revision 1.42  2013/12/18 17:01:59  rvv
        *** empty log message ***

        Revision 1.41  2013/10/26 15:36:08  rvv
        *** empty log message ***

        Revision 1.40  2013/10/02 15:46:23  rvv
        *** empty log message ***

        Revision 1.39  2013/09/28 14:40:56  rvv
        *** empty log message ***

        Revision 1.38  2012/09/05 18:20:30  rvv
        *** empty log message ***

        Revision 1.37  2012/02/20 17:28:35  rvv
        *** empty log message ***

        Revision 1.36  2012/01/16 16:00:03  rvv
        *** empty log message ***

        Revision 1.35  2012/01/16 15:54:02  cvs
        ABR veld toegevoegd

        Revision 1.34  2011/12/24 16:31:00  rvv
        *** empty log message ***

        Revision 1.33  2011/11/19 15:36:03  rvv
        *** empty log message ***

        Revision 1.32  2011/08/31 15:18:50  rvv
        *** empty log message ***

        Revision 1.31  2010/12/12 15:24:17  rvv
        *** empty log message ***

        Revision 1.30  2010/07/13 09:02:27  cvs
        *** empty log message ***

        Revision 1.29  2010/06/09 08:13:16  cvs
        *** empty log message ***

*/

class Fonds extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function Fonds()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
    $this->codesEnkel=array(
      'aabbeCode',          'binckCode',
      'bucketCode',         'FVLCode',
      'kasbankCode',        'raboCode',
      'snsSecCode',         'stroeveCode',
			'CSCode',             'PICcode',
      'Lomcode',            'giroCode',
      'UBPcode',            'UBScode',
      'JBcode',             'LYNXcode',
      'BILcode',            'INGCode',
      'HSBCcode',           'KBCcode',
      'IBcode',
      'UBSLcode',
      'BNPBGLcode',         'JBLuxcode',
      'CAWcode',            'optCode',
      'KNOXcode',           'GScode',
      'Sarasincode',        'Dierickscode',
      'VPcode',             'JPMcode',
      'SAXOcode',           'Quintetcode'
    );
    $this->codesDubbel=array('AABCode/ABRCode'=>array('AABCode','ABRCode'));
  }

	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}

	function checkAccess($type)
	{
	  global $__appvar,$USR;
   	if($_SESSION['usersession']['superuser'])
    {
      if(isset($__appvar["homeAdmins"]) && $type=='delete')
      {
        if(in_array($USR,$__appvar["homeAdmins"]))
          return true;
      }
      else
		    return true;
	  }
    return false;
	}

	function getCustomFields()
	{
	  foreach ($this->data['fields'] as $name=>$eigenschappen)
	  {
	    if($eigenschappen['form_visible'] == true)
	      $tmp[$name] = substr($eigenschappen['description'],0,10);
	  }
	  return $tmp;
	}

	function validate()
	{
	  global $__appvar;
    
    /** validate fonds Types **/
    if ( ! empty ($_GET['fondsInputType']) ) {
      switch ($_GET['fondsInputType']) {
        /** validate fonds turbo symbolen **/
        case 'fondsTurbo':
          if ( isset ($_GET['fondsTurboSymbolen']) && ! empty ($_GET['fondsTurboSymbolen']) ) {
            $db = new DB();
            $query = "SELECT * FROM `fondsTurboSymbolen` WHERE `Fonds` = '" . $_GET['fondsTurboSymbolen'] . "';";
            if ( $db->QRecords($query) == 0 ) {
              $this->setError("fondsTurboSymbolen", vt("Symbool is onbekend!"));
            }
          }
          
          (empty($_GET['fondsTurboSymbolen'])) ? $this->setError("fondsTurboSymbolen", vt("Mag niet leeg zijn!")) : true;
          ($_GET["turbo_isinCode"]=="")?$this->setError("turbo_isinCode", vt("Mag niet leeg zijn!")):true;
          ($_GET["turbo_issuer"] == "")?$this->setError("turbo_issuer", vt("Mag niet leeg zijn!")):true;
          ($_GET["turbo_kind"]=="")?$this->setError("turbo_kind", vt("Mag niet leeg zijn!")):true;
          ($_GET["turbo_longShort"]=="")?$this->setError("turbo_longShort", vt("Mag niet leeg zijn!")):true;
          ($_GET["turbo_stopLoss"] == "" || $_GET["turbo_stopLoss"] == '0.00')?$this->setError("turbo_stopLoss", vt("Mag niet leeg zijn!")):true;

        break;
        
        /** validate fonds optie symbolen **/
        case 'fondsOption':
          if ( isset ($_GET['fondsOptieSymbolen']) && ! empty ($_GET['fondsOptieSymbolen']) ) {
            $db = new DB();
            $query = "SELECT * FROM `fondsOptieSymbolen` WHERE `key` = '" . $_GET['fondsOptieSymbolen'] . "';";
            if ( $db->QRecords($query) == 0 ) {
              $this->setError("fondsOptieSymbolen","Symbool is onbekend!");
            }
          }
          
          (empty ($_GET['fondsOptieSymbolen'])) ? $this->setError("fondsOptieSymbolen", vt("Mag niet leeg zijn!")) : true;
          (empty ($_GET['optieOptieType']))?$this->setError("optieOptieType", vt("Mag niet leeg zijn!")):true;
          (empty ($_GET['optieexpiratieMaand']))?$this->setError("optieexpiratieMaand", vt("Mag niet leeg zijn!")):true;
          (empty ($_GET['optieexpiratieJaar']))?$this->setError("optieexpiratieJaar", vt("Mag niet leeg zijn!")):true;
          (empty ($_GET['optieOptieUitoefenPrijs']) || $_GET["optieOptieUitoefenPrijs"] == '0.00')?$this->setError("optieOptieUitoefenPrijs", vt("Mag niet leeg zijn!")):true;
        break;
      }
    }


		($this->get("Fonds")=="")?$this->setError("Fonds", vt("Mag niet leeg zijn!")):true;
    ($this->get("Omschrijving")=="")?$this->setError("Omschrijving", vt("Mag niet leeg zijn!")):true;
    ($this->get("FondsImportCode")=="")?$this->setError("FondsImportCode", vt("Mag niet leeg zijn!")):true;
    ($this->get("Valuta")=="")?$this->setError("Valuta", vt("Mag niet leeg zijn!")):true;
    ($this->get("fondssoort")=="")?$this->setError("fondssoort", vt("Mag niet leeg zijn!")):true;
		(!isNumeric($this->get("Fondseenheid")))?$this->setError("Fondseenheid", vt("Moet een geldig getal zijn!")):true;
    ($this->get("Fondseenheid")==0)?$this->setError("Fondseenheid", vt("Moet een geldig getal zijn!")):true;
    ($this->get("Beurs")=='keuze')?$this->setError("Beurs", vt("Maak een keuze!")):true;

    if($__appvar['master']==true && in_array($this->get("fondssoort"),array('AAND','OPT','TURBO','STOCKDIV')))
    {
      ($this->get("standaardSector")=='')?$this->setError("standaardSector", vt("Mag niet leeg zijn!")):true;
    }
 
    if(($this->get("fondssoort")=='OBL' || $this->get("fondssoort")=='OVERIGE') && $this->get("Renteperiode")=="")
      $this->set("Renteperiode","12");

      
		//(!isNumeric($this->get("Rentepercentage")))?$this->setError("Rentepercentage","Moet een geldig getal zijn!"):true;

		$DB = new DB();

		$query  = "SELECT id FROM Fondsen WHERE Fonds = '".$this->get("Fonds")."' ";
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		if($DB->records() >0 && $this->get("id") <> $data['id'])
			$this->setError("Fonds", vtb("%s bestaat al", array($this->get("Fonds"))));

		$query  = "SELECT id FROM Fondsen WHERE FondsImportCode = '".$this->get("FondsImportCode")."' ";
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		if($DB->records() >0 && $this->get("id") <> $data['id'])
			$this->setError("FondsImportCode", vtb("%s bestaat al", array($this->get("FondsImportCode"))));

		$optieParts=explode(" ",$this->get("Fonds"));
		$symbool=$optieParts[0];

		$query  = "SELECT id FROM Fondsen WHERE 
    Fondseenheid = '".$this->get("Fondseenheid")."' AND
    OptieType = '".$this->get("OptieType")."' AND
    OptieExpDatum = '".$this->get("OptieExpDatum")."' AND
    OptieUitoefenPrijs = '".$this->get("OptieUitoefenPrijs")."' AND
    OptieBovenliggendFonds = '".$this->get("OptieBovenliggendFonds")."' AND 
    Fonds like '$symbool%'";
		$DB->SQL($query); 
		$DB->Query();
		$data = $DB->nextRecord();
  
		if($DB->records() >0 && $this->get("id") <> $data['id'] && $_GET['key_Fonds']==0 && $this->get("fondssoort")=='OPT')//
			$this->setError("Fonds",vtb("Optie parameters bestaan al |%s|%s|%s|%s|%s|%s", array($this->get("OptieType"), $this->get("OptieBovenliggendFonds"), $this->get("OptieExpDatum"), $this->get("OptieUitoefenPrijs"), $this->get("Fondseenheid"), $symbool)));

		if($_GET['key_Fonds']==1 && $this->get("id") >0 && $this->get("id") <1000000)
		{
			$query="SELECT max(koersExport) as koersExport FROM Vermogensbeheerders";
			$DB->SQL($query);
			$DB->Query();
			$data = $DB->nextRecord();
			if($data['koersExport'] > 0 && $__appvar['master']==false)
				$this->setError("Fonds", vtb("%s niet kunnen muteren vanwege Vermogensbeheerders.koersExport instelling.", array($this->get("Fonds"))));
		}

		if ($this->get("HeeftOptie") == '1' && $this->get("OptieBovenliggendFonds") != "")
		{
			$this->setError("HeeftOptie",vt("Optie kan geen opties hebben."));
		}
  
		if($this->get('EindDatum')=='')
    {
      if($this->get("OptieBovenliggendFonds") != "")
      {
        $expDate=$this->get('OptieExpDatum');
        if($expDate <> '')
        {
          $maand=substr($expDate,4,2)+1;
          $jaar=substr($expDate,0,4);
          $unixtime=adodb_mktime(0,0,0,$maand,0,$jaar);
          $einddatum=adodb_date('Y-m-d',$unixtime);
          $this->set('EindDatum',$einddatum);
        }
      }
      $lossingsdatum=$this->get('Lossingsdatum');
      if($lossingsdatum <> '')
      {
        $jaar=substr($lossingsdatum,0,4);
        $maand=substr($lossingsdatum,5,2)+3;
				if($maand>12)
				{
					$maand -= 12;
					$jaar++;
        }
				//$dag=substr($lossingsdatum,8,2);
				$unixtime=adodb_mktime(0,0,0,$maand,0,$jaar);
        $einddatum=adodb_date('Y-m-d',$unixtime);

        $this->set('EindDatum',$einddatum);
      }
    }
    $this->checkBankCodes();

		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	function checkBankCodes()
	{
		/*

AAB-Code 			`AABCode` varchar(26) NOT NULL DEFAULT '',
ABR-Code 			`ABRCode` varchar(26) NOT NULL DEFAULT '',

AABBE-Code 			`aabbeCode` varchar(30) NOT NULL DEFAULT '',
Binck-Code 			`binckCode` varchar(26) NOT NULL DEFAULT '',
Bucket-Code 		`bucketCode` varchar(26) NOT NULL DEFAULT '',
FVL-code 			`FVLCode` varchar(35) NOT NULL DEFAULT '',
Kasbank-Code 		`kasbankCode` varchar(26) NOT NULL,
Rabo-Code 			`raboCode` varchar(35) NOT NULL DEFAULT '',
SNSSEC-Code 		`snsSecCode` varchar(30) NOT NULL DEFAULT '',
Stroeve-Code 		`stroeveCode` varchar(25) NOT NULL DEFAULT '',
Credit Suisse-Code 	`CSCode` varchar(25) NOT NULL,
Pic-Code 			`PICcode` varchar(25) NOT NULL,
Lombard-Code 		`LomCode` varchar(25) NOT NULL,
DeGiro-Code			`giroCode` varchar(50) NOT NULL,
		*/
    $DB=new DB();
    foreach($this->codesEnkel as $code)
    {
			if($code=='bucketCode')
				continue;
      $huidigeWaarde=$this->get($code);
      if($huidigeWaarde <> '')
      {
        $query  = "SELECT id,Fonds FROM Fondsen WHERE $code = '".mysql_real_escape_string($huidigeWaarde)."' AND id <> '".$this->get("id")."'";
        $DB->SQL($query);
        $DB->Query();
        $data = $DB->nextRecord();
       if($DB->records() >0 )
           $this->setError($code,$huidigeWaarde." bestaat al bij '".$data['Fonds']."'");
      }
    }

    foreach($this->codesDubbel as $naam=>$codes)
    {
      $eersteWaarde=$this->get($codes[0]);
      $tweedeWaarde=$this->get($codes[1]);

      if($eersteWaarde <> '')
      {
        $query  = "SELECT id,Fonds FROM Fondsen WHERE (".$codes[0]."='".mysql_real_escape_string($eersteWaarde)."' OR ".$codes[1]."='".mysql_real_escape_string($eersteWaarde)."') AND id <> '".$this->get("id")."' ";
        $DB->SQL($query);
        $DB->Query();
        $data = $DB->nextRecord();
        if($DB->records() >0 )
        {
          $this->setError($codes[0],$eersteWaarde." bestaat al bij ".$data['Fonds']);
        }
      }

      if($tweedeWaarde <> '')
      {
        $query  = "SELECT id,Fonds FROM Fondsen WHERE (".$codes[0]."='".mysql_real_escape_string($tweedeWaarde)."' OR ".$codes[1]."='".mysql_real_escape_string($tweedeWaarde)."') AND id <> '".$this->get("id")."' ";
        $DB->SQL($query);
        $DB->Query();
        $data = $DB->nextRecord();
        if($DB->records() >0 )
        {
          $this->setError($codes[1],$tweedeWaarde." bestaat al bij ".$data['Fonds']);
        }
      }
    }
	}

	/*
  * Table definition
  */
  function defineData()
	{
		global $__appvar;

		$ms = new AE_cls_Morningstar();

		$this->data['table'] = "Fondsen";
		$this->data['identity'] = "id";
		$this->data['logChange'] = true;

		$this->addField('id',
										array("description"  => "id",
													"db_size"      => "11",
													"db_type"      => "int",
													"form_type"    => "text",
													"form_visible" => false,
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"
										));

		$this->addField('Fonds',
										array("description"  => "Fonds",
													"db_size"      => "25",
													"db_type"      => "varchar",
													"form_size"    => "25",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150", "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => true,
													"list_order"   => "true",
													"key_field"    => true));

		$this->addField('Omschrijving',
										array("description"  => "Omschrijving",
													"db_size"      => "50",
													"db_type"      => "varchar",
													"form_size"    => "50",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150", "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => true,
													"list_order"   => "true"));

		$this->addField('FondsImportCode',
										array("description"  => "Fonds importcode",
													"db_size"      => "16",
													"db_type"      => "varchar",
													"form_size"    => "16",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('identifierVWD',
										array("description"  => "Identifier VWD",
													"db_size"      => "80",
													"db_type"      => "varchar",
													"form_size"    => "50",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('identifierFactSet',
										array("description"  => "Identifier FactSet",
													"db_size"      => "30",
													"db_type"      => "varchar",
													"form_size"    => "30",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('koersmethodiek',
										array("description"  => "Koersmethodiek",
													"db_size"      => "3",
													"db_type"      => "tinyint",
													"form_size"    => "3",
													"form_type"    => "selectKeyed",
													"form_options" => array(1 => 'VWD', 2 => 'FactSet', 3 => 'Handmatig', 4 => 'Overige', 5 => 'VBH', 6 => 'Niet bekoersen'),
													"form_visible" => true, "list_width" => "150",
													"form_extra"   => "onchange=\"toonKoersVBH();\"",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('Valuta',
										array("description"  => "Valuta",
													"db_size"      => "4",
													"db_type"      => "char",
													"form_size"    => "4",
													"form_type"    => "select",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true",
													"keyIn"        => "Valutas"));

		$this->addField('fondssoort',
										array("description"  => "Fondssoort",
													"db_size"      => "8",
													"db_type"      => "char",
													"form_size"    => "8",
													"form_type"    => "selectKeyed",
													"form_options" => array('AAND' => 'Aandeel', 'OBL' => 'Obligatie', 'OPT' => 'Optie', 'STOCKDIV' => 'Stockdividend', 'TURBO' => 'Turbo', 'OVERIG' => 'Overig', 'INDEX' => 'Index'),
													"form_visible" => true,
													"list_width"   => "150",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('Fondseenheid',
										array("description"  => "Fondseenheid",
													"db_size"      => "0",
													"db_type"      => "double",
													"form_size"    => "8",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));
		/*
        $this->addField('Garantiepercentage',
                              array("description"=>"Garantiepercentage",
                              "db_size"=>"0",
                              "db_type"=>"double",
                              "form_size"=>"8",
                              "form_type"=>"text",
                              "form_visible"=>true,"list_width"=>"150",
                              "list_visible"=>true,
                              "list_align"=>"right",
                              "list_search"=>false,
                              "list_order"=>"true"));

        $this->addField('Rentepercentage',
                              array("description"=>"Rentepercentage",
                              "db_size"=>"0",
                              "db_type"=>"double",
                              "form_size"=>"8",
                              "form_type"=>"text",
                              "form_visible"=>true,"list_width"=>"150",
                              "list_visible"=>true,
                              "list_align"=>"right",
                              "list_search"=>false,
                              "list_order"=>"true"));
    */
		$this->addField('Rentedatum',
										array("description"  => "Coupondatum",
													"db_size"      => "0",
													"db_type"      => "datetime",
													"form_type"    => "calendar",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));


		$this->addField('Huisfonds',
										array("description"  => "Huisfonds",
													"db_size"      => "4",
													"db_type"      => "tinyint",
													"form_size"    => "4",
													"form_type"    => "checkbox",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));


		$this->addField('Portefeuille',
										array("description"       => "Portefeuille",
													"db_size"           => "24",
													"db_type"           => "varchar",
													"form_size"         => "24",
													"form_type"         => "selectKeyed",
													"select_query"      => "SELECT Portefeuille,Portefeuille FROM Portefeuilles ORDER BY Portefeuille",
													"select_query_ajax" => "SELECT Portefeuille, Portefeuille FROM Portefeuilles WHERE Portefeuille='{value}'",
//													"select_query" =>"(SELECT Portefeuille,Portefeuille as P2 FROM Portefeuilles) UNION (SELECT VirtuelePortefeuille as Portefeuille,VirtuelePortefeuille as P2 FROM GeconsolideerdePortefeuilles) ORDER BY Portefeuille ",
//													"select_query_ajax" =>"(SELECT Portefeuille,Portefeuille FROM Portefeuilles WHERE Portefeuille='{value}') UNION (SELECT VirtuelePortefeuille,VirtuelePortefeuille FROM GeconsolideerdePortefeuilles WHERE VirtuelePortefeuille='{value}')",
													"form_visible"      => true, "list_width" => "150",
													"list_visible"      => true,
													"list_align"        => "left",
													"list_search"       => false,
													"list_order"        => "true",
													"keyIn"             => "Portefeuilles"));

		$this->addField('Renteperiode',
										array("description"  => "Renteperiode",
													"db_size"      => "20",
													"db_type"      => "bigint",
													"form_options" => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12),
													"form_size"    => "8",
													"form_type"    => "select",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('EersteRentedatum',
										array("description"  => "Eerste Coupondatum",
													"db_size"      => "0",
													"db_type"      => "datetime",
													"form_type"    => "calendar",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('AABCode',
										array("description"  => "AAB-Code",
													"db_size"      => "26",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('ABRCode',
										array("description"  => "ABR-Code",
													"db_size"      => "26",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('ISINCode',
										array("description"  => "ISIN-Code",
													"db_size"      => "26",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('bucketCode',
										array("description"  => "Bucket-Code",
													"db_size"      => "26",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('stroeveCode',
										array("description"  => "Stroeve-Code",
													"db_size"      => "25",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('snsSecCode',
										array("description"  => "SNSSEC-Code",
													"db_size"      => "30",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('aabbeCode',
										array("description"  => "AABBE-Code",
													"db_size"      => "50",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('binckCode',
										array("description"  => "Binck-Code",
													"db_size"      => "26",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));
    $this->addField('binckValuta',
                    array("description"  => "Binck Valuta",
                          "db_size"      => "4",
                          "db_type"      => "varchar",
                          "form_size"     => "12",
                          "form_type"=>"selectKeyed",
                          "form_options"=>array('PNC'=>'PNC'),
                       //   "select_query"  => "SELECT Valuta,Valuta as omschrijving FROM Valutas  WHERE Valuta IN('PNC') ORDER BY Valuta", //
                          "form_visible" => true, "list_width" => "10",
                          "list_visible" => true,
                          "list_align"   => "left",
                          "list_search"  => false,
                          "list_order"   => "true",
                          "keyIn"        => "Valutas"));
    $this->addField('binckBeurs',
                    array("description"  => "Binck Beurs",
                          "db_size"      => "4",
                          "db_type"      => "varchar",
                          "form_size"     => "12",
                          "form_type"     => "selectKeyed",
                          "select_query"  => "SELECT Beurs, concat(Omschrijving,' | ',Beurs) FROM Beurzen ORDER BY Omschrijving",
                          "form_visible" => true, "list_width" => "10",
                          "list_visible" => true,
                          "list_align"   => "left",
                          "list_search"  => false,
                          "list_order"   => "true",
                          "keyIn"        => "Beurzen"));

		$this->addField('raboCode',
										array("description"  => "Rabo-Code",
													"db_size"      => "35",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('kasbankCode',
										array("description"  => "Kasbank-Code",
													"db_size"      => "35",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('FVLCode',
										array("description"  => "FVL-code",
													"db_size"      => "35",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('CSCode',
										array("description"  => "Credit Suisse-Code",
													"db_size"      => "25",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('PICcode',
										array("description"  => "Pic-Code",
													"db_size"      => "25",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('Lomcode',
										array("description"  => "Lombard-Code",
													"db_size"      => "25",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('giroCode',
										array("description"  => "DeGiro-Code",
													"db_size"      => "50",
													"db_type"      => "varchar",
													"form_size"    => "25",
													"form_type"    => "text",
													"form_visible" => true,
													"list_width"   => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('UBPcode',
										array("description"  => "UBP-Code",
													"db_size"      => "50",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true,
													"list_width"   => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('UBScode',
										array("description"  => "UBS-Code",
													"db_size"      => "50",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true,
													"list_width"   => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));
		$this->addField('INGCode',
										array("description"  => "ING-Code",
													"db_size"      => "26",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));
		$this->addField('JBcode',
										array("description"  => "JB-Code",
													"db_size"      => "26",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));
		$this->addField('LYNXcode',
										array("description"  => "LYNX-Code",
													"db_size"      => "26",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));
		$this->addField('BILcode',
										array("description"  => "BIL-Code",
													"db_size"      => "26",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));
    $this->addField('HSBCcode',
                    array("description"  => "HSBC-Code",
                          "db_size"      => "50",
                          "db_type"      => "varchar",
                          "form_type"    => "text",
                          "form_visible" => true, "list_width" => "150",
                          "list_visible" => true,
                          "list_align"   => "left",
                          "list_search"  => false,
                          "list_order"   => "true"));
    $this->addField('KBCcode',
                    array("description"  => "KBC-Code",
                          "db_size"      => "26",
                          "db_type"      => "varchar",
                          "form_type"    => "text",
                          "form_visible" => true, "list_width" => "150",
                          "list_visible" => true,
                          "list_align"   => "left",
                          "list_search"  => false,
                          "list_order"   => "true"));

    foreach(array(
                    'UBSLcode',
                    'BNPBGLcode',
                    'JBLuxcode',
                    'IBcode',
                    'CAWcode',
                    'KNOXcode',
                    'GScode',
                    'Sarasincode',
                    'Dierickscode',
                    'VPcode',
                    'JPMcode',
                    'SAXOcode',
                    'Quintetcode'
                  ) as $code)
    {
      $this->addField($code,
                      array("description"  => substr($code,0,-4).'-Code',
                            "db_size"      => "26",
                            "db_type"      => "varchar",
                            "form_type"    => "text",
                            "form_visible" => true, "list_width" => "150",
                            "list_visible" => true,
                            "list_align"   => "left",
                            "list_search"  => false,
                            "list_order"   => "true"));
    }
    $this->addField('optCode',
                    array("description"  => "Optimix-Code",
                          "db_size"      => "26",
                          "db_type"      => "varchar",
                          "form_type"    => "text",
                          "form_visible" => true, "list_width" => "150",
                          "list_visible" => true,
                          "list_align"   => "left",
                          "list_search"  => false,
                          "list_order"   => "true"));
		/*
        $this->addField('FondsOverslaanInValutaRisico',
                              array("description"=>"Fonds Overslaan In ValutaRisico",
                              "db_size"=>"4",
                              "db_type"=>"tinyint",
                              "form_size"=>"25",
                              "form_type"=>"checkbox",
                              "form_visible"=>true,"list_width"=>"150",
                              "list_visible"=>true,
                              "list_align"=>"left",
                              "list_search"=>false,
                              "list_order"=>"true"));
    */

		$this->addField('EindDatum',
										array("description"  => "Eind Datum",
													"db_size"      => "0",
													"db_type"      => "date",
													"form_size"    => "25",
													"form_type"    => "calendar",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));
		$this->addField('Lossingsdatum',
										array("description"  => "Lossingsdatum",
													"db_size"      => "0",
													"db_type"      => "date",
													"form_size"    => "25",
													"form_type"    => "calendar",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));
    
    $this->addField('callabledatum',
                    array("description"  => "Callable datum",
                          "db_size"      => "0",
                          "db_type"      => "date",
                          "form_size"    => "11",
                          "form_type"    => "calendar",
                          "form_visible" => true, "list_width" => "150",
                          "list_visible" => true,
                          "list_align"   => "left",
                          "list_search"  => false,
                          "list_order"   => "true"));
    
		$this->addField('lossingskoers',
										array("description"  => "Lossingskoers",
													"db_size"      => "0",
													"db_type"      => "double",
													"form_size"    => "8",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('add_date',
										array("description"  => "add_date",
													"db_size"      => "0",
													"db_type"      => "datetime",
													"form_type"    => "datum",
													"form_visible" => true,
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('add_user',
										array("description"  => "add_user",
													"db_size"      => "10",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true,
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('change_date',
										array("description"  => "change_date",
													"db_size"      => "0",
													"db_type"      => "datetime",
													"form_type"    => "datum",
													"form_visible" => true,
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('change_user',
										array("description"  => "change_user",
													"db_size"      => "10",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true,
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('HeeftOptie',
										array("description"  => "Heeft optie/index",
													"db_size"      => "4",
													"db_type"      => "tinyint",
													"form_size"    => "25",
													"form_type"    => "checkbox",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('OptieType',
										array("description"  => "[P]ut/[C]all/[F]uture",
													"db_size"      => "1",
													"db_type"      => "varchar",
													"form_options" => array('P', 'C', 'F'),
													"form_type"    => "select",
													"form_size"    => "1",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('OptieExpDatum',
										array("description"  => "Expiratie datum",
													"db_size"      => "6",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('OptieUitoefenPrijs',
										array("description"  => "Uitoefenprijs",
													"db_size"      => "0",
													"db_type"      => "double",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"form_format"  => "%01.2f",
													"list_format"  => "%01.2f",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('OptieBovenliggendFonds',
										array("description"  => "Bovenliggend fonds",
													"db_size"      => "25",
													"db_type"      => "varchar",
													"form_size"    => "16",
													"form_visible" => true, "list_width" => "150",
													"form_type"    => "selectKeyed",
													"select_query" => "SELECT Fonds,Fonds FROM Fondsen WHERE HeeftOptie > 0 order by Fonds",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => true,
													"list_order"   => "true",
													"keyIn"        => "Fondsen"));

		$this->addField('Rente30_360',
										array("description"  => "30/360 renteberekening",
													"db_size"      => "4",
													"db_type"      => "tinyint",
													"form_size"    => "1",
                          "form_type"     => "selectKeyed",
                          "form_select_option_notempty" => true,
                          "form_options"                => array(0 => 'Act/365',1=>'30/360', 2 => 'Act/360',3=>'Act/Act'),
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));
		/*
		                      "form_type"     => "selectKeyed",
                          "form_select_option_notempty" => true,
                          "form_options"                => array(0 => 'Act/365',1=>'30/360', 2 => 'Act/360',3=>'Act/Act'),
		 */

		$this->addField('Beurs',
										array("description"   => "Beurs",
													"db_size"       => "4",
													"db_type"       => "varchar",
													"form_size"     => "12",
													"form_type"     => "selectKeyed",
													"select_query"  => "(SELECT 'keuze','keuze' as omschrijving) UNION (SELECT Beurs, concat(Omschrijving,' | ',Beurs) FROM Beurzen ORDER BY Omschrijving) order by omschrijving",
													"form_visible"  => true, "list_width" => "150",
													"default_value" => 'keuze',
													"list_visible"  => true,
													"list_align"    => "left",
													"list_search"   => false,
													"list_order"    => "true",
													"keyIn"         => "Beurzen"));

		$this->addField('orderinlegInBedrag',
										array("description"                 => "Orderinleg in bedrag",
													"db_size"                     => "3",
													"db_type"                     => "tinyint",
													"form_size"                   => "3",
													"form_type"                   => "selectKeyed",
													"form_select_option_notempty" => true,
													"form_options"                => array(0 => 'Niet toegestaan', 1 => 'Toegestaan'),
													"form_visible"                => true, "list_width" => "150",
													"form_extra"                  => "",
													"list_visible"                => true,
													"list_align"                  => "left",
													"list_search"                 => false,
													"list_order"                  => "true"));

		$this->addField('standaardSector',
										array("description"   => "standaard sector",
													"db_size"       => "15",
													"default_value" => 'keuze',
													"db_type"       => "varchar",
													"form_size"     => "15",
													"form_type"     => "selectKeyed",
													"select_query"  => "SELECT Beleggingssector,Omschrijving FROM Beleggingssectoren WHERE standaard=1 ORDER BY Beleggingssector",
													"form_visible"  => true, "list_width" => "150",
													"list_visible"  => true,
													"list_align"    => "left",
													"list_search"   => false,
													"list_order"    => "true",
													"keyIn"         => "Beurzen"));

		$this->addField('bbLandcode',
										array("description"  => "Landcode",
													"db_size"      => "2",
													"db_type"      => "varchar",
													"form_size"    => "2",
													"form_type"    => "selectKeyed",
													"select_query" => "SELECT bbLandcode, concat(bbLandcode,' - ',Omschrijving) FROM BbLandcodes ORDER BY bbLandcode ",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true",
													"keyIn"        => "BbLandcodes"));

		$this->addField('rating',
										array("description"  => "Rating",
													"db_size"      => "26",
													"db_type"      => "varchar",
													"form_size"    => "2",
													"form_type"    => "selectKeyed",
													"select_query" => "SELECT rating,rating FROM Rating ",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true",
													"keyIn"        => "Rating"));

		$this->addField('optieCode',
										array("description"  => "AIRS-Optiecode",
													"db_size"      => "30",
													"db_type"      => "varchar",
													"form_size"    => "30",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => true,
													"list_order"   => "true"));

		/*
          $this->addField('valutaRisicoPercentage',
                              array("description"=>"risico %",
                              "db_size"=>"3",
                              "db_type"=>"varchar",
                              "form_size"=>"3",
                              "form_type"=>"text",
                              "form_visible"=>true,"list_width"=>"150",
                              "list_visible"=>true,
                              "list_align"=>"left",
                              "list_search"=>false,
                              "list_order"=>"true"));
    */
		$this->addField('variabeleCoupon',
										array("description"  => "Variabele coupon",
													"db_size"      => "1",
													"db_type"      => "tinyint",
													"form_type"    => "checkbox",
													"form_visible" => true,
													"list_width"   => "150",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));


		$this->addField('koersControle',
										array("description"  => "Koers controle overslaan",
													"db_size"      => "1",
													"db_type"      => "tinyint",
													"form_type"    => "checkbox",
													"form_visible" => true,
													"list_width"   => "150",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('forward',
										array("description"  => "Forward",
													"db_size"      => "1",
													"db_type"      => "tinyint",
													"form_type"    => "checkbox",
													"form_visible" => true,
													"list_width"   => "150",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('inflatieGekoppeld',
										array("description"  => "Inflatie gekoppeld",
													"db_size"      => "1",
													"db_type"      => "tinyint",
													"form_type"    => "checkbox",
													"form_visible" => true,
													"list_width"   => "150",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('RevConvertable',
										array("description"  => "(Rev) Convertable",
													"db_size"      => "4",
													"db_type"      => "tinyint",
													"form_size"    => "4",
													"form_type"    => "checkbox",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('forwardAfloopDatum',
										array("description"  => "AfloopDatum",
													"db_size"      => "0",
													"db_type"      => "date",
													"form_type"    => "calendar",
													"form_visible" => true,
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('forwardReferentieKoers',
										array("description"  => "Referentiekoers",
													"db_size"      => "0",
													"db_type"      => "double",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"form_format"  => "%01.8f",
													"list_format"  => "%01.8f",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));
    
    
    $this->addField('OblPerpetual',
                    array("description"=>"Perpetual",
                          "db_size"=>"1",
                          "db_type"=>"tinyint",
                          "form_type"=>"checkbox",
                          "form_visible"=>true,
                          "list_width"=>"150",
                          "list_visible"=>true,
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true"));
    
    $this->addField('OblSoortFloater',
                    array("description"=>"Soort Floater",
                          "db_size"=>"20",
                          "db_type"=>"varchar",
                          "form_size"=>"20",
                          "form_type"=>"selectKeyed",
                          "form_options"=>array("Floater"=>'Floater','FixedToFloater'=>'FixedToFloater','FloaterToFIX'=>'FloaterToFIX'),
                          "form_visible"=>true,
                          "list_width"=>"150",
                          "list_visible"=>true,
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
    
    $jaren=array();
    for($i=2017;$i<2061;$i++)
    {
      $jaren[]=$i;
    }
    $this->addField('OblFloaterJaar',
                    array("description"=>"Floater jaar",
                          "default_value"=>'',
                          "db_size"=>"20",
                          "db_type"=>"int",
                          "form_size"=>"20",
                          "form_type"=>"select",
                          "form_options"=>$jaren,
                          "form_visible"=>true,
                          "list_width"=>"150",
                          "list_visible"=>true,
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
    
		
 		if($__appvar["bedrijf"] == "HOME")
		{
			$this->addField('koersmemo',
											array("description"=>"Koersmemo",
														"db_size"=>"255",
														"db_type"=>"varchar",
														"form_size"=>"80",
														"form_type"=>"textarea",
														"form_visible"=>true,
														"list_width"=>"150",
														"list_visible"=>true,
														"list_align"=>"right",
														"list_search"=>false,
														"list_order"=>"true"));

			$this->addField('OblMemo',
											array("description"=>"Memo Obligaties",
														"db_size"=>"255",
														"db_type"=>"text",
														"form_type"=>"textarea",
														"form_size"=>"30",
														"form_rows"=>"2",
														"form_visible"=>true,"list_width"=>"150",
														"list_visible"=>true,
														"list_align"=>"right",
														"list_search"=>false,
														"list_order"=>"true"));

			$this->addField('OblDirtyPr',
											array("description"=>"Dirty Pr.",
														"db_size"=>"1",
														"db_type"=>"tinyint",
														"form_type"=>"checkbox",
														"form_visible"=>true,
														"list_width"=>"150",
														"list_visible"=>true,
														"list_align"=>"right",
														"list_search"=>false,
														"list_order"=>"true"));

	
      

			$this->addField('datumControleStatics',
											array("description"=>"Datum controle statics",
														"db_size"=>"0",
														"db_type"=>"date",
														"form_size"=>"25",
														"form_type"=>"calendar",
														"form_visible"=>true,"list_width"=>"150",
														"list_visible"=>true,
														"list_align"=>"left",
														"list_search"=>false,
														"list_order"=>"true"));

			$this->addField('koersVBH',
											array("description"=>"Aanleverende VBH",
														"db_size"=>"10",
														"db_type"=>"varchar",
														"form_size"=>"10",
														"form_type"=>"selectKeyed",
														"select_query"=>"SELECT Bedrijf,Bedrijf FROM Bedrijfsgegevens ORDER BY Bedrijf",
														"form_visible"=>true,"list_width"=>"150",
														"list_visible"=>true,
														"list_align"=>"left",
														"list_search"=>false,
														"list_order"=>"true",
														"keyIn"=>"Bedrijfsgegevens"));
		}

    if(checkAccess())
    {
    $this->addField('KoersAltijdAanvragen',
													array("description"=>"Koers altijd aanvragen",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

 		    $this->addField('koersBron',
													array("description"=>"Koersbron",
													"db_size"=>"3",
													"db_type"=>"tinyint",
													"form_size"=>"3",
													"form_type"=>"selectKeyed",
                          "form_options"=>array(1=>'WebSite',2=>'Morningstar',3=>'Doc. Fundmanager',4=>'Depotbank',5=>'Overige'),
													"form_visible"=>true,
                          "list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

       $this->addField('koersbronOpm',
													array("description"=>"OpmKoersbron",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_size"=>"80",
													"form_type"=>"textarea",
													"form_visible"=>true,
                          "list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));  
                          
      $this->addField('koersFrequentie',
													array("description"=>"Frequentie",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"selectKeyed",
                          "form_options"=>array(1=>'Dagelijks',2=>'Wekelijks',3=>'Maandelijks',4=>'Kwartaal',5=>'Half-Jaarlijks',5=>'Jaarlijks'),
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
 		$this->addField('revisieDatum',
													array("description"=>"Revisiedatum",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
			}
		$this->addField('VKM',
												array("description"=>"Indirect instrument",
															"db_size"=>"4",
															"db_type"=>"tinyint",
															"form_size"=>"4",
															"form_type"=>"checkbox",
															"form_visible"=>true,"list_width"=>"150",
															"form_extra"=>"onchange=\"checkPassiefFonds();\"",
															"list_visible"=>true,
															"list_align"=>"left",
															"list_search"=>false,
															"list_order"=>"true"));

  	$this->addField('passiefFonds',
												array("description"=>"passief fonds",
															"db_size"=>"4",
															"db_type"=>"tinyint",
															"form_size"=>"4",
															"form_type"=>"checkbox",
															"form_extra"=>"onchange=\"checkPassiefFonds();\"",
															"form_visible"=>true,"list_width"=>"150",
															"list_visible"=>true,
															"list_align"=>"left",
															"list_search"=>false,
															"list_order"=>"true"));

		$this->addField('minOrdergrootte',
										array("description"  => "Min. ordergrootte",
													"db_size"      => "0",
													"db_type"      => "double",
													"form_size"    => "8",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));
		
		$this->addField('handelseenheid',
										array("description"  => "Handelseenheid",
													"db_size"      => "0",
													"db_type"      => "double",
													"form_size"    => "8",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));
    
    $this->addField('IdentifierMS',
                    array("description"  => "IdentifierMS",
                          "db_size"      => "50",
                          "db_type"      => "varchar",
                          "form_size"    => "30",
                          "form_type"    => "text",
                          "form_visible" => true,
                          "list_width" => "150",
                          "list_visible" => true,
                          "list_align"   => "left",
                          "list_search"  => true,
                          "list_order"   => "true"));
    if ($ms->allowed(3,4))  // call 7630
    {
      $this->addField('KIDformulier',
                      array("description"  => "KIDformulier",
                            "db_size"      => "150",
                            "db_type"      => "varchar",
                            "form_size"    => "30",
                            "form_type"    => "text",
                            "form_visible" => true,
                            "list_width" => "150",
                            "list_visible" => true,
                            "list_align"   => "left",
                            "list_search"  => true,
                            "list_order"   => "true"));
    }

    
  }
}
?>