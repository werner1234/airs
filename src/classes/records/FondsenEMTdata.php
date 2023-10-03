<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 29 april 2018
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/05/24 09:06:00 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: FondsenEMTdata.php,v $
    Revision 1.2  2020/05/24 09:06:00  rvv
    *** empty log message ***

    Revision 1.1  2020/05/23 16:41:57  rvv
    *** empty log message ***

    Revision 1.10  2019/05/25 16:16:48  rvv
    *** empty log message ***

    Revision 1.9  2019/02/27 13:47:06  rvv
    *** empty log message ***

    Revision 1.8  2018/12/05 16:34:28  rvv
    *** empty log message ***

    Revision 1.7  2018/12/01 19:46:07  rvv
    *** empty log message ***

    Revision 1.6  2018/11/28 13:13:56  rvv
    *** empty log message ***

    Revision 1.5  2018/06/13 15:17:25  rvv
    *** empty log message ***

    Revision 1.4  2018/05/19 15:51:40  rvv
    *** empty log message ***

    Revision 1.3  2018/04/30 07:37:11  rvv
    *** empty log message ***

    Revision 1.2  2018/04/30 05:34:43  rvv
    *** empty log message ***

    Revision 1.1  2018/04/29 09:44:35  rvv
    *** empty log message ***

 		
 	
*/

class FondsenEMTdata extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
	var $JaNeeNeutraal = Array(''=>'---',"Ja"=>"Ja","Nee"=>"Nee","Neutraal"=>"Neutraal");
  
  /*
  * Constructor
  */
  function FondsenEMTdata()
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

		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
    return checkAccess();
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "FondsenEMTdata";
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

		$this->addField('Fonds',
										array("description"=>"Fonds",
													"db_size"=>"25",
													"db_type"=>"varchar",
													'autocomplete' => array(
														'query' => "SELECT Fonds,FondsImportCode,Omschrijving FROM Fondsen WHERE Fonds like '%{find}%'",
														'label' => array(
															'Fonds','Fondsen.FondsImportCode'
														),
														'searchable' => array(
															'Fonds', 'Fondsen.FondsImportCode', 'Fondsen.Omschrijving'
														),
														'field_value' => array(
															'Fonds',
														),
														'value'             => 'Fonds',
														'actions' => array(	'select_addon' => ' $("#Fonds").val(ui.item.data.Fonds); console.log(ui.item.data.Fonds);')
													),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Fondsen"));

		$velden=array('ClientTypeRetail'=>'Is Client Type Retail',
									'ClientTypeProfessional'=>'Is Client Type Professional',
									'ClientTypeEligibleCounterparty'=>'Is Client Type Eligible Counterparty',
									'ExpertiseBasic'=>'Investor With Basic Knowledge',
									'ExpertiseInformed'=>'Informed Investor',
									'ExpertiseAdvanced'=>'Advanced Investor',
									'CapitalLossNone'=>'No Ability For Any Capital Loss',
									'CapitalLossLimited'=>'Ability For Limited Capital Losses',
									'CapitalLossTotal'=>'Ability For Total Capital Loss',
									'CapitalLossBeyondInvestment'=>'Ability For Losses Beyond Capital',
									'ProfilePreservation'=>'Return Profile Preservation',
									'ProfileGrowth'=>'Return Profile Growth',
									'ProfileIncome'=>'Return Profile Income',
									'ProfileHedging'=>'Return Profile Hedging',
									'ProfileOptionsLeverage'=>'Return Profile Options Or Leverage',
									'ProfileOther'=>'Return Profile Other');
		foreach($velden as $veld=>$omschrijving)
		{
			$this->addField($veld,
											array("description"=>$omschrijving,
														"default_value"=>"",
														"db_size"=>"10",
														"db_type"=>"varchar",
														"form_type"=>"select",
														"form_options"=>$this->JaNeeNeutraal,
														"form_select_option_notempty" =>true,
														"form_size"=>"10",
														"form_visible"=>true,
														"list_width"=>"150",
														"list_visible"=>true,
														"list_align"=>"right",
														"list_search"=>true,
														"list_order"=>"true",
														"categorie"=>"Algemeen"));
		}
  
    $velden=array('ServiceExecOnly'=>'Eligible For Execution Only Distribution',
                  'ServiceExecOnlyAppTest'=>'Eligible For Execution Only With Appropriateness Test',
                  'ServiceAdvice'=>'Eligible For Advised Retail Distribution',
                  'ServiceManagement'=>'Eligible For Portfolio Management');
    foreach($velden as $veld=>$omschrijving)
    {
      $this->addField($veld,
                      array("description"=>$omschrijving,
                            "default_value"=>"",
                            "db_size"=>"12",
                            "db_type"=>"varchar",
                            "form_type"=>"select",
                            "form_options"=>array('Retail','Professional','Both','Neither'),
                            "form_size"=>"12",
                            "form_visible"=>true,
                            "list_width"=>"150",
                            "list_visible"=>true,
                            "list_align"=>"right",
                            "list_search"=>false,
                            "list_order"=>"true",
                            "categorie"=>"Algemeen"));
    }

		$velden=array('RiskSRRI'=>'SRRI','RiskPRIIPSRI'=>'PRIIP Summary Risk Indicator');
		foreach($velden as $veld=>$omschrijving)
		{
			$this->addField($veld,
											array("description"=>$omschrijving,
														"default_value"=>"",
														"db_size"=>"11",
														"db_type"=>"int",
														"form_type"=>"text",
														"form_size"=>"11",
														"form_visible"=>true,
														"list_width"=>"150",
														"list_visible"=>true,
														"list_align"=>"right",
														"list_search"=>false,
														"list_order"=>"true",
														"categorie"=>"Algemeen"));
		}

		$this->addField('ClientRiskTolerance',
										array("description"=>'Cliënt Risk Tolerance',
													"default_value"=>"",
                          "db_size"=>"10",
                          "db_type"=>"varchar",
                          "form_type"=>"select",
                          "form_options"=>array('Low','Medium','High'),
													"form_size"=>"11",
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Algemeen"));

		$this->addField('ClientHorizon',
										array("description"=>'Cliënt Horizon',
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_options"=>array('Short','Medium','Long','Neutral'),
													"form_size"=>"10",
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>true,
													"list_order"=>"true",
													"categorie"=>"Algemeen"));
  /*
    $this->addField('MSFondswaarde',
                    array("description"  => "MSFondswaarde",
                          "db_size"      => "0",
                          "db_type"      => "int",
                          "form_type"    => "text",
                          "form_visible" => true,
                          "list_width" => "150",
                          "form_format"  => "%01.0f",
                          "list_format"  => "%01.0f",
                          "list_visible" => true,
                          "list_align"   => "right",
                          "list_search"  => false,
                          "list_order"   => "true"));
    $this->addField('MSAantalIntr',
                    array("description"  => "MSAantalIntr",
                          "db_size"      => "0",
                          "db_type"      => "int",
                          "form_type"    => "text",
                          "form_visible" => true,
                          "list_width" => "150",
                          "form_format"  => "%01.0f",
                          "list_format"  => "%01.0f",
                          "list_visible" => true,
                          "list_align"   => "right",
                          "list_search"  => false,
                          "list_order"   => "true"));
    $this->addField('MSManFeeFonds',
                    array("description"  => "MSManFeeFonds",
                          "db_size"      => "0",
                          "db_type"      => "double",
                          "form_type"    => "text",
                          "form_visible" => true,
                          "list_width" => "150",
                          "form_format"  => "%01.3f",
                          "list_format"  => "%01.3f",
                          "list_visible" => true,
                          "list_align"   => "right",
                          "list_search"  => false,
                          "list_order"   => "true"));
*/
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

  }
}
?>