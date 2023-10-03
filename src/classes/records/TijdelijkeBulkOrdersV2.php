<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 12 augustus 2015
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/11/08 07:56:18 $
    File Versie         : $Revision: 1.26 $
 		
    $Log: TijdelijkeBulkOrdersV2.php,v $
    Revision 1.26  2018/11/08 07:56:18  rvv
    *** empty log message ***

    Revision 1.25  2017/12/03 12:14:19  rvv
    *** empty log message ***

    Revision 1.24  2017/11/12 13:37:16  rvv
    *** empty log message ***

    Revision 1.23  2017/09/06 16:23:28  rvv
    *** empty log message ***

    Revision 1.22  2017/05/11 09:25:34  rvv
    *** empty log message ***

    Revision 1.21  2017/03/18 20:21:11  rvv
    *** empty log message ***

    Revision 1.20  2017/03/05 12:03:24  rvv
    *** empty log message ***

    Revision 1.19  2016/10/26 16:16:34  rvv
    *** empty log message ***

    Revision 1.18  2016/10/14 10:08:22  rm
    bulk fonds validatie

    Revision 1.17  2016/07/20 16:04:32  rvv
    *** empty log message ***

    Revision 1.16  2016/07/13 15:39:31  rvv
    *** empty log message ***

    Revision 1.15  2016/07/03 12:30:28  rvv
    *** empty log message ***

    Revision 1.14  2016/07/03 08:43:20  rvv
    *** empty log message ***

    Revision 1.13  2016/06/22 16:06:30  rvv
    *** empty log message ***

    Revision 1.12  2016/06/05 12:14:30  rvv
    *** empty log message ***

    Revision 1.11  2016/04/17 17:12:16  rvv
    *** empty log message ***

    Revision 1.10  2016/03/13 16:20:54  rvv
    *** empty log message ***

    Revision 1.9  2016/02/14 11:24:55  rvv
    *** empty log message ***

    Revision 1.8  2015/12/07 06:59:28  rvv
    *** empty log message ***

    Revision 1.7  2015/12/06 18:03:19  rvv
    *** empty log message ***

    Revision 1.6  2015/12/02 17:16:10  rvv
    *** empty log message ***

    Revision 1.5  2015/11/06 20:29:14  rvv
    *** empty log message ***

    Revision 1.4  2015/11/01 18:07:43  rvv
    *** empty log message ***

    Revision 1.3  2015/10/18 13:38:35  rvv
    *** empty log message ***

    Revision 1.2  2015/10/11 17:46:46  rvv
    *** empty log message ***

    Revision 1.1  2015/09/30 07:49:23  rvv
    *** empty log message ***

 		
 	
*/

class TijdelijkeBulkOrdersV2 extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function TijdelijkeBulkOrdersV2()
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
	  global $USR;
	  $DB = new DB();
    $regelNew=$this->get('regelNr');
    $query="SELECT id,regelNr FROM TijdelijkeBulkOrdersV2 WHERE pagina='".$this->get('pagina')."' AND 
                   add_user='".mysql_real_escape_string($USR)."' AND bron='bulkInvoer' order by regelNr";
 		$DB->SQL($query);
		$DB->Query();
    $toegevoegd=false;
    while($data=$DB->nextRecord())
    {
      if($toegevoegd==false)
      {
        if($regelNew<=$data['regelNr'])
        {
          if($this->get('id')==0)
            $volgorde['new']=$data['regelNr'];
          else
            $volgorde[$this->get('id')]=$data['regelNr'];
          $toegevoegd=true;
       
        }
      }
      $volgorde[$data['id']]=$data['regelNr'];
    }
    if($toegevoegd==false)
    {
            if($this->get('id')==0)
            $volgorde['new']=$data['regelNr'];
          else
            $volgorde[$this->get('id')]=$data['regelNr'];    
    }
    //if($regelNew <> count($volgorde)) //hernummering
    //{
      $n=1;
      foreach($volgorde as $id=>$regel)
      {
         $query="UPDATE TijdelijkeBulkOrdersV2 SET regelNr='$n' WHERE id='$id'";
         $DB->SQL($query);
         $DB->Query();
         $n++;
      }
    //}
    
    if($this->get('id')<>0)
    {
      unset($this->data['fields']['regelNr']);
    }
    
	 
   	//	($this->get("fonds")=="")?$this->setError("fonds","Mag niet leeg zijn!"):true;
      ($this->get("portefeuille")=="")?$this->setError("portefeuille",vt("Mag niet leeg zijn!")):true;
      ($this->get("transactieSoort")=="")?$this->setError("transactieSoort",vt("Mag niet leeg zijn!")):true;
      ($this->get("aantal")=="")?$this->setError("aantal",vt("Mag niet leeg zijn!")):true;
			($this->get("fondsOmschrijving")=="")?$this->setError("fondsOmschrijving",vt("Mag niet leeg zijn!")):true;



//		(!is_numeric($this->get("Koers")))?$this->setError("Koers","Moet een getal zijn."):true;

    $DB = new DB();
		$query  = "SELECT id FROM Portefeuilles WHERE portefeuille = '".$this->get("portefeuille")."' ";
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->NextRecord();
		if($DB->records() < 1)
			$this->setError("portefeuille",vtb("Portefeuille %s bestaat niet.", array($this->get("portefeuille"))));

		/*
		$query  = "SELECT id FROM Fondsen WHERE fonds = '".$this->get("fonds")."' ";
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->NextRecord();
		if($DB->records() < 1)
			$this->setError("fonds","Fonds ".$this->get("fonds")." bestaat niet.");
    */
    $this->orderregelAanvullen();

		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
    if (checkOrderAcces('handmatigBulk_opslaan') == true)
      return true; 
    else
      return false;
  
    //return  checkAccess($type);
	}
  
  function orderregelAanvullen($force=false)
  {
    if($this->get('rekening')=='' || $this->get('depotbank')=='' || $this->get('accountmanager')=='' || $force==true)
    {
      include_once('../../html/orderControlleBerekeningV2.php');
      $check=new orderControlleBerekeningV2();
    
      $velden=$check->getPortefeuilleOpties($this->get('portefeuille'),$this->get('fonds'));

      if($this->get('rekening')=='' || $force==true)
        $this->set('rekening',$velden['Rekening']);
      if($this->get('depotbank')=='' || $force==true)
        $this->set('depotbank',$velden['Depotbank']);
      if($this->get('accountmanager')=='' || $force==true)
        $this->set('accountmanager',$velden['accountmanager']);        
    }   
 
  }
	/*
  * Table definition
  */
  function defineData()
  {
    global $__ORDERvar;
    $this->data['name']  = "";
    $this->data['table']  = "TijdelijkeBulkOrdersV2";
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

    $checks=array('Aanw','Short','Liqu','Zorg','Risi','Groot','Vbep','Akkam','Optie','Rest');
    foreach($checks as $check)
	  	$this->addField('validatie'.$check,
													array("description"=>$check,
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"text",
													"form_visible"=>true,
                          "list_width"=>"50",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
                          
 		$this->addField('transactieSoort',
													array("description"=>"transactieSoort",
													"default_value"=>"",
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
                                                                                                    
		$this->addField('aantal',
													array("description"=>"aantal",
													"default_value"=>"",
													"db_size"=>"12,6",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"12,6",
													"form_visible"=>true,
													"list_visible"=>true,
												//	"list_format"=>"%01.6f",
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
    $this->addField('bedrag',
                    array("description"=>"bedrag",
                          "default_value"=>"",
                          "db_size"=>"12,6",
                          "db_type"=>"double",
                          "form_type"=>"text",
                          "form_size"=>"12,6",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_format"=>"%01.2f",
                          "list_width"=>"100",
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true"));

		$this->addField('ISINCode',
													array("description"=>"ISINCode",
													"default_value"=>"",
													"db_size"=>"26",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"26",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

/*
		$this->addField('valuta',
													array("description"=>"valuta",
													"default_value"=>"",
													"db_size"=>"6",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"6",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
*/
		$this->addField('fonds',
													array("description"=>"fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
                          "form_extra"=>'READONLY style="background-color:#DDDDDD"',
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('fondsOmschrijving',
													array("description"=>"fondsOmschrijving",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"validate"      => array(
														'required' => true,
														'empty'    => false
													)));

		$this->addField('accountmanager',
													array("description"=>"Accountmanager",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
                          "list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

 		$this->addField('client',
													array("description"=>"client",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
                          "form_extra"=>'onchange="select_client(document.editForm.client.value,600,400)"',
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));  

		$this->addField('portefeuille',
													array("description"=>"portefeuille",
													"default_value"=>"",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"24",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('depotbank',
													array("description"=>"depotbank",
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

		$this->addField('rekening',
													array("description"=>"rekening",
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


		$this->addField('controleRegels',
													array("description"=>"controleRegels",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('controleStatus',
													array("description"=>"controleStatus",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"text",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

                      
 		$this->addField('koersLimiet',
													array("description"=>"Limietkoers",
													"default_value"=>"",
													"db_size"=>"12,4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"12,4",
												  "form_extra"=>'onchange="koersLimietChangeBulk();"',
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true")); 

		$this->addField('pagina',
													array("description"=>"pagina",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('orderbedrag',
													array("description"=>"orderbedrag",
													"default_value"=>"",
													"db_size"=>"16,2",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"16,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('modelPercentage',
													array("description"=>"modelPercentage",
													"default_value"=>"",
													"db_size"=>"8,4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"8,4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('portefeuillePercentage',
													array("description"=>"portefeuillePercentage",
													"default_value"=>"",
													"db_size"=>"8,4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"8,4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('afwijking',
													array("description"=>"afwijking",
													"default_value"=>"",
													"db_size"=>"8,4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"8,4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('modelWaarde',
													array("description"=>"modelWaarde",
													"default_value"=>"",
													"db_size"=>"16,2",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"16,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  
    $this->addField('afwijkingsbedrag',
                    array("description"=>"Afwijkingsbedrag",
                          "default_value"=>"",
                          "db_size"=>"16,2",
                          "db_type"=>"double",
                          "form_type"=>"text",
                          "form_size"=>"16,2",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_format"=>"%01.2f",
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
    
		$this->addField('koers',
													array("description"=>"koers",
													"default_value"=>"",
													"db_size"=>"12,4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"12,4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

 		$this->addField('regelNr',
													array("description"=>"regelNr",
													"default_value"=>"1",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"2",
													"form_visible"=>true,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('add_date',
													array("description"=>"add_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datetime",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
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

		$this->addField('bron',
													array("description"=>"bron",
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
                          
    $this->addField('beurs',	array("description"=>"Beurs",
			"db_size"      => "4",
			"db_type"      => "varchar",
			"form_size"    => "12",
			"form_type"    => "selectKeyed",
			"select_query" => "SELECT Beurs, CONCAT(Omschrijving, ' (', beurs, ')') as Omschrijving FROM Beurzen ",
			"form_visible" => true,
      "list_width"   => "150",
			"list_visible" => true,
			"list_align"   => "left",
			"list_search"  => false,
			"list_order"   => "true",
			"keyIn"        => "Beurzen"));

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
													"list_order"=>"true"));

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
													"list_order"=>"true"));

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
													"keyIn"=>"Valutas"));

		$this->addField('fondsBankcode',
										array("description"=>"fondsBankcode",
													"db_size"=>"30",
													"db_type"=>"varchar",
													"form_size"=>"12",
													"form_type"=>"varchar",
													"form_visible"=>true,"list_width"=>"150",
													"default_value"=>'',
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('optieSymbool',
										array("description"=>"Symbool",
													"db_size"=>"5",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

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
													"list_order"=>"true"));

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
													"list_order"=>"true"));

		$this->addField('optieExpDatum',
										array("description"=>"Expiratie datum",
													"db_size"=>"6",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('afmStdevVoor',
										array("description"=>"AFM Standaarddeviatie voor",
													"default_value"=>"",
													"db_size"=>"12,4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"12,4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('afmStdevNa',
										array("description"=>"AFM Standaarddeviatie na",
													"default_value"=>"",
													"db_size"=>"12,4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"12,4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Beleggingscategorie',
										array("description"=>"Beleggingscategorie",
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
													
																			  
    $this->addField('validatieVast',
                    array("description"=>"Validatie vast",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "form_size"=>"4",
                          "form_type"=>"checkbox",
                          "form_visible"=>false,
						  "list_width"=>"150",
                          "list_visible"=>false,
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
	
    $this->addField('aantalInPositie',
                    array("description"=>"Aantal in positie",
                          "default_value"=>"",
                          "db_size"=>"12,4",
                          "db_type"=>"double",
                          "form_type"=>"text",
                          "form_size"=>"12,4",
                          "form_visible"=>true,
                          "list_visible"=>true,
                      //	"list_format"=>"%01.6f",
                          "list_width"=>"100",
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true"));
    $this->addField('nieuwAantal',
                    array("description"=>"Nieuw aantal",
                          "default_value"=>"",
                          "db_size"=>"12,4",
                          "db_type"=>"double",
                          "form_type"=>"text",
                          "form_size"=>"12,4",
                          "form_visible"=>true,
                          "list_visible"=>true,
                      //	"list_format"=>"%01.6f",
                          "list_width"=>"100",
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true"));
  }
}
?>