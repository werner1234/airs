<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 20 december 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/03/08 07:55:07 $
    File Versie         : $Revision: 1.7 $
 		
    $Log: FondsExtraInformatie.php,v $
    Revision 1.7  2020/03/08 07:55:07  rvv
    *** empty log message ***

    Revision 1.6  2020/03/07 18:02:04  rvv
    *** empty log message ***

    Revision 1.5  2018/07/08 08:19:33  rvv
    *** empty log message ***

    Revision 1.4  2018/01/04 07:38:50  rvv
    *** empty log message ***

    Revision 1.3  2018/01/04 05:55:24  rvv
    *** empty log message ***

    Revision 1.2  2018/01/03 14:17:55  rvv
    *** empty log message ***

    Revision 1.1  2017/12/20 16:57:25  rvv
    *** empty log message ***

 		
 	
*/

class FondsExtraInformatie extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function FondsExtraInformatie()
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
		($this->get("fonds")=="")?$this->setError("fonds",vt("Mag niet leeg zijn!")):true;
    
    $query  = "SELECT id FROM FondsExtraInformatie WHERE ".
      " Fonds = '".mysql_real_escape_string($this->get("fonds"))."' ";
    
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $data = $DB->nextRecord();
    if($DB->records() >0 && $this->get("id") <> $data['id'])
    {
      $this->setError("fonds", vtb('Fonds (%s) al aanwezig met id:%s.', array($this->get("fonds"), $data['id'])));
    }
    
    $query="SELECT Fonds FROM Fondsen WHERE Fonds = '".mysql_real_escape_string($this->get("fonds"))."'";
    if($DB->Qrecords($query) == 0)
    {
      $this->setError("fonds",vtb('Fonds (%s) bestaat niet in de fondsen tabel.', array($this->get("fonds"))));
    }

    $fondsRapportagenaam = $this->get('FondsRapportagenaam');
    if ( ! empty ($fondsRapportagenaam) && count ($fondsRapportagenaam) > 50  ) {
      $this->setError("FondsRapportagenaam",vt('Maximaal 50 Karakters'));
    }
    
    
    
    $valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
		if($_SESSION['usersession']['gebruiker']['fondsmutatiesAanleveren']==1)
			return true;

		return checkAccess($type);
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "FondsExtraInformatie";
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

		$this->addField('fonds',
													array("description"=>"fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
														//		"form_type"=>"selectKeyed",
														//		'select_query' => "SELECT Fonds,Fonds FROM Fondsen ORDER BY Fonds",
														//		'select_query_ajax' => "SELECT Fonds,Fonds FROM Fondsen WHERE Fonds='{value}'",
																'autocomplete' => array(
																	'query' => "SELECT Fonds FROM Fondsen WHERE Fonds like '%{find}%'",
																	'label' => array(
																		'Fonds'
																	),
																	'searchable' => array(
																		'Fonds'
																	),
																	'field_value' => array(
																		'Fonds',
																	),
																	'value'             => 'Fonds',
																	'actions' => array(	'select_addon' => ' $("#Fonds").val(ui.item.data.Fonds); console.log(ui.item.data.Fonds);')
																),

													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Fondsen"));
  
    $this->addField('FondsRapportagenaam',
                    array("description"  => "FondsRapportagenaam",
                          "db_size"      => "50",
                          "db_type"      => "varchar",
                          "form_size"    => "50",
                          "form_type"    => "text",
                          "form_visible" => true,
                          "list_width" => "150",
                          "list_visible" => true,
                          "list_align"   => "left",
                          "list_search"  => true,
                          "list_order"   => "true"));
    
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

		$db=new DB();
		$query="SELECT veldnaam, omschrijving, veldtype, 
                   '30' as weergaveBreedte, '50' as headerBreedte 
                   FROM FondsExtraVelden WHERE inActief=0 ORDER BY veldnaam";
		$db->SQL($query);
		$db->Query();
		$eigenVelden=array();
		while ($data=$db->nextRecord())
			$eigenVelden[$data['veldnaam']]=$data;

		foreach ($eigenVelden as $veldnaam=>$data)
		{
			$dbSize=$veldTypen=array('Tekst'=>'255','Memo'=>'255','Getal'=>'20','Datum'=>'15','Trekveld'=>'200','Checkbox'=>'4','Document'=>'16777216');
			$dbType=array('Tekst'=>'varchar(255)','Memo'=>'text','Getal'=>'double','Datum'=>'date','Trekveld'=>'varchar(200)','Checkbox'=>'tinyint','Document'=>'mediumtext');
			$formType=array('Tekst'=>'text','Memo'=>'textarea','Getal'=>'text','Datum'=>'calendar','Trekveld'=>'select','Checkbox'=>'checkbox','Document'=>'document');

			$optionArray='';
			if($data['veldtype']=='Trekveld')
			{
				$optionArray=array();
				$query="SELECT FondsExtraTrekvelden.waarde FROM FondsExtraTrekvelden WHERE trekveld='$veldnaam' ORDER BY FondsExtraTrekvelden.volgorde";
				$db->SQL($query);
				$db->Query();
				while ($optiedata=$db->nextRecord())
					$optionArray[]=$optiedata['waarde'];
			}
			$tmpArray=array("description"=>$data['omschrijving'],
											"default_value"=>"",
											"db_size"=>$dbSize[$data['veldtype']],
											"db_type"=>$dbType[$data['veldtype']],
											"form_type"=>$formType[$data['veldtype']],
											"form_options"=>$optionArray,
											"form_size"=>$data['weergaveBreedte'],
											"form_rows"=>"5",
											"form_visible"=>true,
											"list_visible"=>true,
											"list_width"=>$data['headerBreedte'],
										//	"keyIn"=>"FondsExtraTrekvelden",
										//	"keyUpdateCondition"=>array('module'=>$module),
											"list_align"=>"left",
											"list_search"=>false,
											"list_order"=>"true",
											"categorie"=>"Algemeen");

			if($data['veldtype']=='Trekveld')
			  $tmpArray['keyIn']="FondsExtraTrekvelden";

			if($data['veldtype']=='Document')
				$tmpArray['downloadLink']="fondsextrainformatieEdit.php?action=download&field=$veldnaam&id={id}";

			$this->addField($veldnaam,$tmpArray);
		}


  }
}
?>