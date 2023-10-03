<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2017/11/29 15:55:37 $
 		File Versie					: $Revision: 1.7 $
*/

class Rekeningafschriften_v2 extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function Rekeningafschriften_v2()
  {
    $this->defineData();
    $this->set($this->data['identity'],0);
    $this->setDefaults();
    $this->error = false;
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
    $DB = new DB();
    $DB->SQL("SELECT id, Datum FROM Rekeningafschriften WHERE Rekening = '".$this->get("Rekening")."' AND Afschriftnummer = '".$this->get("Afschriftnummer")."'");
    $DB->Query();
    $data = $DB->nextRecord();

    if ( (int) $this->get('Verwerkt') === 1) {
      $curBoekDate = strtotime($data['Datum']);
      $newBoekDate = strtotime($this->get('Datum'));

      //Wanneer de boekdatum is aangepast
      if (date('Y-m-d', $curBoekDate) !== date('Y-m-d', $newBoekDate)) {
        //Wanneer het jaar afwijkt, zou niet mogen ivm jquery validatie.
        if (date('Y', $curBoekDate) !== date('Y', $newBoekDate)) {
          $this->setError("Datum", vtb("Het boekjaar mag niet afwijken van %s.", array(date('Y', $curBoekDate))));
        }

        //Boekdatum van rekeningmutaties controleren datum mag niet voor de laatste rekeningmutatie liggen.
        $DB->SQL("SELECT `id`, `Boekdatum` FROM Rekeningmutaties WHERE `Afschriftnummer` = '" . $this->get("Afschriftnummer") . "' AND `Rekening` = '" . $this->get("Rekening") . "' Order by `id` DESC LIMIT 0,1");
        $DB->Query();
        $rekeningMutatie = $DB->nextRecord();

        if ( ! empty ($rekeningMutatie)) {
          $lastMutatieBoekDate = strtotime($rekeningMutatie['Boekdatum']);
          if (date('Y-m-d', $newBoekDate) < date('Y-m-d', $lastMutatieBoekDate)) {
            $this->setError("Datum", vtb("Datum mag niet voor de laatste rekeningmutatie liggen (%s).", array(date('d-m-Y', $lastMutatieBoekDate))));
          }
        }
      }
    }


		($this->get("Rekening")=="")?$this->setError("Rekening",vt("Mag niet leeg zijn!")):true;
		($this->get("Afschriftnummer")=="")?$this->setError("Afschriftnummer",vt("Mag niet leeg zijn!")):true;
    ($this->get("Datum")=="")?$this->setError("Datum",vt("Mag niet leeg zijn!")):true;

		if($DB->records() >0 && $this->get("id") <> $data[id])
		{
			$this->setError("Rekening",vt("combinatie bestaat al"));
			$this->setError("Afschriftnummer",vt("combinatie bestaat al"));
		}

		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	function validateDelete()
	{
		$DB = new DB();
		$SQL = "SELECT * FROM Rekeningmutaties WHERE Afschriftnummer = '". $this->get("Afschriftnummer")."' AND Rekening = '". $this->get("Rekening")."'";
		$recs = $DB->QRecords($SQL);
		if ($recs > 0)
		{
			$this->setError("Algemeen",vtb("Dit record mag niet worden verwijderd. Er zijn nog %s onderliggende mutaties aanwezig.", array($recs)));
		}
		$valid = ($this->error==false)?true:false;
		return $valid;
	}


	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "Rekeningafschriften";
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

		$this->addField('Rekening',
													array("description"=>"Rekening",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Rekeningen"));

		$this->addField('Afschriftnummer',
													array("description"=>"Afschriftnummer",
													"value"=>"test",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"12",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('Datum',
													array("description"=>"Datum",
																"default_value"=>"lastworkday",
																"db_size"=>"0",
																"db_type"=>"date",
																"form_type"=>"calendar",
																"form_class"=> "AIRSdatepicker AIRSdatepickerPreviousMonth",
																"form_extra"=>" onchange=\"date_complete(this);\"",
																"form_size"=>"8",
																"form_visible"=>true,
																"list_visible"=>true,
																"list_align"=>"right",
																"list_search"=>false,
																"list_order"=>"true"));

		$this->addField('Saldo',
													array("description"=>"Saldo",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('NieuwSaldo',
													array("description"=>"NieuwSaldo",
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

		$this->addField('Verwerkt',
													array("description"=>"Verwerkt",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"form_size"=>"10",
													"list_visible"=>true,
													"list_align"=>"right",
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
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

  }
}
?>